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

import java.io.File;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.atomic.AtomicBoolean;

import net.ladenthin.snowman.imager.configuration.CImager;
import net.ladenthin.snowman.imager.run.fileAssignation.FileAssignationSingleton;
import net.ladenthin.snowman.imager.run.streamer.StreamerSingleton;
import net.ladenthin.snowman.imager.run.uploader.UploaderSingleton;
import net.ladenthin.snowman.imager.run.watchdog.WatchdogSingleton;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import de.fraunhofer.fokus.eject.ObjectInstantiation;

/**
 * No documentation.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
 */
public class Imager {

    public final static String jarFilename = "imager.jar";
    public final static String version = "1.0.0";

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

    private void waitForAllThreads() throws InterruptedException {
        Thread t;
        LOGGER.debug("Await termination for watchdog thread (no time limit).");
        t = WatchdogSingleton.getSingleton().getThread();
        t.join();
        if (t.isAlive()) {
            LOGGER.warn("Watchdog thread still alive, skip.");
        }

        LOGGER.debug("Shutdown all uploading threads.");
        UploaderSingleton.getSingleton().getExecutor().shutdown();

        LOGGER.debug("Await termination for all uploading threads (2s time limit).");
        final boolean terminated =
            UploaderSingleton.getSingleton().getExecutor().awaitTermination(2, TimeUnit.SECONDS);
        if (terminated) {
            LOGGER.debug("All uploading threads killed successfully.");
        } else {
            LOGGER.warn("Uploading threads still alive, skip.");
        }

        LOGGER.debug("Await termination for streamer thread (2s time limit).");
        t = StreamerSingleton.getSingleton().getThread();
        t.join(2000);
        if (t.isAlive()) {
            LOGGER.warn("Streamer thread still alive, skip.");
        }
    }

    @edu.umd.cs.findbugs.annotations.SuppressWarnings(
        value = "DM_EXIT",
        justification = "It is intended that the program is fully completed. It could stay at worst unattainable threads in the background.")
    public Imager(final String configurationPath) throws Exception {
        LOGGER.trace("Try to read the configuration file.");
        LOGGER.trace("configurationPath: " + configurationPath);

        final File configurationFile = new File(configurationPath);
        final ObjectInstantiation<CImager> oi = new ObjectInstantiation<>(CImager.class, false, false, false);
        final CImager configuration = oi.readFile(configurationFile);

        if (LOGGER.isTraceEnabled()) {
            LOGGER.trace("ObjectInstantiation log: {}", oi.getLogMessage());
        }

        LOGGER.debug("configuration: {}", configuration);

        ConfigurationSingleton.setSingleton(configuration);

        final RuntimeSingleton rs = RuntimeSingleton.getSingleton();

        LOGGER.debug("initialize streamer path");
        rs.initializeStreamerPath(configuration.getStreamer().getPath());

        if (!rs.isStreamerPathValid()) {
            throw new RuntimeException("!rs.isStreamerPathValid()");
        }

        WatchdogSingleton.setSingleton();
        UploaderSingleton.setSingleton();
        StreamerSingleton.setSingleton();
        LOGGER.info("Imager successfully initialized.");

        waitForAllThreads();
        if (restartFlag.get() == true) {
            Runtime.getRuntime().exec(configuration.getWatchdog().getRestartCommand());
        }
        System.exit(0);
    }
}
