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
package net.ladenthin.snowman.imager.run;

import com.google.gson.Gson;
import net.ladenthin.javacommons.StreamHelper;
import net.ladenthin.snowman.imager.configuration.CImager;
import net.ladenthin.snowman.imager.run.streamer.StreamerSingleton;
import net.ladenthin.snowman.imager.run.uploader.UploaderSingleton;
import net.ladenthin.snowman.imager.run.watchdog.WatchdogSingleton;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import java.io.File;
import java.io.IOException;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.atomic.AtomicBoolean;

/**
 * No documentation.
 *
 * @author Bernard Ladenthin: bernard.ladenthin@gmail.com
 */
public class Imager {

    public final static String jarFilename = "imager.jar";
    public final static String version = "1.4.0";

    public final static AtomicBoolean restartFlag = new AtomicBoolean();

    public final static Logger LOGGER = LogManager.getLogger(Imager.class.getName());

    /**
     * Wait a little bit.
     * As example do not flood log files if an error occurred.
     * @param millis
     */
    public final static void waitALittleBit(long millis) {
        try {
            Thread.sleep(millis);
        } catch (InterruptedException e) {
            LOGGER.error("InterruptedException: ", e);
            return;
        }
    }

    public void waitForAllThreads() throws InterruptedException {
        Thread t;
        LOGGER.debug("Await termination for watchdog thread (no time limit).");
        t = WatchdogSingleton.WatchdogSingleton.getThread();
        t.join();

        LOGGER.debug("Shutdown all uploading threads.");
        UploaderSingleton.UploaderSingleton.getExecutor().shutdown();

        LOGGER.debug("Await termination for all uploading threads (2s time limit).");
        final boolean terminated =
            UploaderSingleton.UploaderSingleton.getExecutor().awaitTermination(2, TimeUnit.SECONDS);
        if (terminated) {
            LOGGER.debug("All uploading threads killed successfully.");
        } else {
            LOGGER.warn("Uploading threads still alive, skip.");
        }

        LOGGER.debug("Await termination for streamer thread (2s time limit).");
        t = StreamerSingleton.StreamerSingleton.getThread();
        t.join(2000);
        if (t.isAlive()) {
            LOGGER.warn("Streamer thread still alive, skip.");
        }
    }
    
    public void restartAndExit() throws IOException {
        if (restartFlag.get() == true) {
            Runtime.getRuntime().exec(ConfigurationSingleton.ConfigurationSingleton.getImager().getWatchdog().getRestartCommand());
        }
        System.exit(0);
    }

    @edu.umd.cs.findbugs.annotations.SuppressWarnings(
        value = "DM_EXIT",
        justification = "It is intended that the program is fully completed. It could stay at worst unattainable threads in the background.")
    public Imager(final String configurationPath) throws IOException, InstantiationException {
        LOGGER.trace("Try to read the configuration file.");
        LOGGER.trace("configurationPath: " + configurationPath);

        final File configurationFile = new File(configurationPath);
        if (!configurationFile.exists()) {
            LOGGER.error("configurationFile does not exist: " + configurationFile.getAbsolutePath());
        }
        String configurationString = new StreamHelper().readFullyAsUTF8String(configurationFile);
        LOGGER.trace("configurationString:\n" + configurationString);
        final CImager configuration = new Gson().fromJson(configurationString, CImager.class);

        LOGGER.debug("configuration: {}", configuration);

        ConfigurationSingleton.ConfigurationSingleton.setImager(configuration);

        LOGGER.debug("initialize streamer path");
        RuntimeSingleton.RuntimeSingleton.initializeStreamerPath(configuration.getStreamer().getPath());

        if (!RuntimeSingleton.RuntimeSingleton.isStreamerPathValid()) {
            throw new RuntimeException("!rs.isStreamerPathValid()");
        }

        WatchdogSingleton.WatchdogSingleton.startWatchdog();
        UploaderSingleton.UploaderSingleton.createWorkerThreads();
        StreamerSingleton.StreamerSingleton.startStreamer();
        LOGGER.info("Imager successfully initialized. Version: " + Imager.version);
    }
}
