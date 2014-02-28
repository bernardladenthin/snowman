/*
 * snowman-java-imager - A tool to upload images on a snowman-php-server.
 * http://code.google.com/p/snowman/
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
package net.ladenthin.snowman.imager.run.watchdog;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.concurrent.atomic.AtomicBoolean;

import net.ladenthin.snowman.imager.configuration.CImager;
import net.ladenthin.snowman.imager.run.ConfigurationSingleton;
import net.ladenthin.snowman.imager.run.Imager;
import net.ladenthin.snowman.imager.run.RuntimeSingleton;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 * The {@link Watchdog}.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
 */
public class Watchdog implements Runnable {

    private final static Logger LOGGER = LogManager.getLogger(Watchdog.class.getName());
    private final static int noPIDFound = -1;
    private final static AtomicBoolean killFlag = new AtomicBoolean();

    public final boolean getKillFlag() {
        return killFlag.get();
    }

    @Override
    public void run() {
        final CImager conf = ConfigurationSingleton.getSingleton().getImager();
        final RuntimeSingleton rs = RuntimeSingleton.getSingleton();

        try {
            // create a temporary file to set lastModified
            boolean delete = File.createTempFile("watchdog", "", rs.getStreamerPath()).delete();
            if (!delete) {
                LOGGER.error("Failed to remove temp watchdog file.");
            }

            outer: for (;;) {
                if (killFlag.get() == true) {
                    LOGGER.trace("killFlag == true");
                    return;
                }
                Thread.sleep(conf.getWatchdog().getInterval());
                final long notModified =
                    System.currentTimeMillis() - rs.getStreamerPath().lastModified();

                LOGGER.trace("notModified: {}", notModified);

                if (notModified > conf.getWatchdog().getTimeWindow()) {
                    LOGGER.warn("notModified > timeWindow");
                    if (LOGGER.isTraceEnabled()) {
                        LOGGER.trace("timeWindow: {}", conf.getWatchdog().getTimeWindow());
                    }
                    for (String processName : conf.getWatchdog().getBlockingProcesses()) {
                        final int pid = getPidof(processName);
                        if (pid != noPIDFound) {
                            LOGGER.warn("Found blocking process. pid: {},  processName: {}", pid,
                                processName);
                            continue outer;
                        }
                    }
                    LOGGER.info("No blocking process found, set the restartFlag and killFlag.");
                    Imager.restartFlag.set(true);
                    killFlag.set(true);
                    break;
                } else {
                    LOGGER.debug("modification in timeWindow");
                }
            }
        } catch (Exception e) {
            LOGGER.error("Exception during run: ", e);
        }
    }

    /**
     * Get the pid of a process.
     *
     * @param processName
     * @return -1 if the process is not found, else the pid of the process.
     * @throws IOException
     */
    @edu.umd.cs.findbugs.annotations.SuppressWarnings(value = "DM_DEFAULT_ENCODING",
        justification = "It is important that the default encoding of the system will be used.")
    private int getPidof(String processName) {
        String line;
        try {
            Process p = Runtime.getRuntime().exec("pidof " + processName);
            try (
                InputStreamReader inputSReader = new InputStreamReader(p.getInputStream());
                BufferedReader input = new BufferedReader(inputSReader)
            ) {
                while ((line = input.readLine()) != null) {
                    return Integer.parseInt(line);
                }
            }
        } catch (NumberFormatException e) {
            LOGGER.error("NumberFormatException: ", e);
        } catch (IOException e) {
            LOGGER.error("IOException: ", e);
        }
        return noPIDFound;
    }

}
