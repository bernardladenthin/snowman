/*
 * snowman-java-imager - A tool to upload images on a snowman-php-server.
 * https://github.com/bernardladenthin/snowman
 *
 * Copyright (C) 2014 Bernard Ladenthin <bernard.ladenthin@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
package net.ladenthin.snowman.imager.run.uploader;

import java.io.File;
import java.io.IOException;
import java.net.SocketException;

import net.ladenthin.snowman.imager.configuration.CImager;
import net.ladenthin.snowman.imager.run.ConfigurationSingleton;
import net.ladenthin.snowman.imager.run.Imager;
import net.ladenthin.snowman.imager.run.fileAssignation.FileAssignationSingleton;
import net.ladenthin.snowman.imager.run.watchdog.WatchdogSingleton;

import org.apache.http.HttpEntity;
import org.apache.http.NoHttpResponseException;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.ContentType;
import org.apache.http.entity.mime.HttpMultipartMode;
import org.apache.http.entity.mime.MultipartEntityBuilder;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.util.EntityUtils;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 * No documentation.
 *
 * @author Bernard Ladenthin: bernard.ladenthin@gmail.com
 */
public class Uploader implements Runnable {

    private final static Logger LOGGER = LogManager.getLogger(UploaderSingleton.class.getName());

    private final static String uploadNameCameraname = "cameraname";
    private final static String uploadNameCameraimage = "cameraimage";
    private final static String uploadNameFilename = "filename";
    private final static String uploadNameUsername = "username";
    private final static String uploadNamePassword = "password";
    private final static String responseSuccess = "{\"imageUpload\":{\"success\":true}}";

    private void logIOExceptionAndWait(IOException e) {
        LOGGER.warn("Known IOException: {} (maybe no network, wait a little bit)", e.getClass());
        Imager.waitALittleBit(5000);
    }

    @Override
    public void run() {
        final CImager cs = ConfigurationSingleton.ConfigurationSingleton.getImager();
        final String url = cs.getSnowmanServer().getApiUrl();
        for (;;) {
            File obtainedFile;
            for (;;) {
                if (WatchdogSingleton.WatchdogSingleton.getWatchdog().getKillFlag() == true) {
                    LOGGER.trace("killFlag == true");
                    return;
                }
                obtainedFile = FileAssignationSingleton.FileAssignationSingleton.obtainFile();

                if (obtainedFile == null) {
                    Imager.waitALittleBit(300);
                    continue;
                } else {
                    break;
                }
            }

            boolean doUpload = true;
            while (doUpload) {
                try {
                    try (CloseableHttpClient httpclient = HttpClients.createDefault()) {
                        final HttpPost httppost = new HttpPost(url);
                        final MultipartEntityBuilder builder = MultipartEntityBuilder.create();
                        final FileBody fb =
                            new FileBody(obtainedFile, ContentType.APPLICATION_OCTET_STREAM);

                        builder.setMode(HttpMultipartMode.BROWSER_COMPATIBLE);
                        builder.addPart(uploadNameCameraimage, fb);
                        builder.addTextBody(uploadNameFilename, obtainedFile.getName());
                        builder
                            .addTextBody(uploadNameUsername, cs.getSnowmanServer().getUsername());
                        builder
                            .addTextBody(uploadNamePassword, cs.getSnowmanServer().getPassword());
                        builder.addTextBody(uploadNameCameraname, cs.getSnowmanServer()
                            .getCameraname());

                        final HttpEntity httpEntity = builder.build();
                        httppost.setEntity(httpEntity);

                        if (LOGGER.isTraceEnabled()) {
                            LOGGER.trace("executing request " + httppost.getRequestLine());
                        }

                        try (CloseableHttpResponse response = httpclient.execute(httppost)) {
                            if (LOGGER.isTraceEnabled()) {
                                LOGGER.trace("response.getStatusLine(): "
                                    + response.getStatusLine());
                            }
                            final HttpEntity resEntity = response.getEntity();
                            if (resEntity != null) {
                                if (LOGGER.isTraceEnabled()) {
                                    LOGGER.trace("RresEntity.getContentLength(): "
                                        + resEntity.getContentLength());
                                }
                            }
                            final String resString = EntityUtils.toString(resEntity).trim();
                            EntityUtils.consume(resEntity);
                            if (resString.equals(responseSuccess)) {
                                doUpload = false;
                                LOGGER.trace("true: resString.equals(responseSuccess)");
                                LOGGER.trace("resString: {}", resString);
                            } else {
                                LOGGER.warn("false: resString.equals(responseSuccess)");
                                LOGGER.warn("resString: {}", resString);
                                // do not flood log files if an error occurred
                                Imager.waitALittleBit(2000);
                            }
                        }
                    }
                } catch (NoHttpResponseException | SocketException e) {
                    logIOExceptionAndWait(e);
                } catch (IOException e) {
                    LOGGER.warn("Found unknown IOException", e);
                }
            }

            if (LOGGER.isTraceEnabled()) {
                LOGGER.trace("delete obtainedFile {}", obtainedFile);
            }
            final boolean delete = obtainedFile.delete();
            if (LOGGER.isTraceEnabled()) {
                LOGGER.trace("delete success {}", delete);
            }
            FileAssignationSingleton.FileAssignationSingleton.freeFile(obtainedFile);
        }
    }

}
