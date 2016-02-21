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
package net.ladenthin.snowman.imager.run.streamer;

import java.io.File;
import java.io.IOException;

import net.ladenthin.snowman.imager.configuration.CImager;
import net.ladenthin.snowman.imager.run.ConfigurationSingleton;
import net.ladenthin.snowman.imager.run.Imager;
import net.ladenthin.snowman.imager.run.watchdog.WatchdogSingleton;

import org.apache.commons.exec.CommandLine;
import org.apache.commons.exec.DefaultExecutor;
import org.apache.commons.exec.ExecuteWatchdog;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 * No documentation.
 *
 * @author Bernard Ladenthin: bernard.ladenthin@gmail.com
 */
public class Streamer implements Runnable {

    private final static Logger LOGGER = LogManager.getLogger(Streamer.class.getName());
    private final static String streamer = "streamer";

    @Override
    public void run() {
        final CImager conf = ConfigurationSingleton.ConfigurationSingleton.getImager();

        for (;;) {
            if (WatchdogSingleton.WatchdogSingleton.getWatchdog().getKillFlag() == true) {
                LOGGER.trace("killFlag == true");
                return;
            }

            final CommandLine cmdLine = new CommandLine(streamer);
            // final CommandLine cmdLine = new CommandLine("sleep");
            // cmdLine.addArgument("200");

            cmdLine.addArgument("-c");
            cmdLine.addArgument(conf.getStreamer().getDevice());
            cmdLine.addArgument("-t");
            cmdLine.addArgument(String.valueOf(conf.getStreamer().getFramesPerSecond()
                * conf.getStreamer().getRecordTime()));
            cmdLine.addArgument("-r");
            cmdLine.addArgument(String.valueOf(conf.getStreamer().getFramesPerSecond()));
            cmdLine.addArgument("-s");
            cmdLine.addArgument(conf.getStreamer().getResolutionX() + "x"
                + conf.getStreamer().getResolutionY());
            cmdLine.addArgument("-o");
            cmdLine.addArgument(conf.getStreamer().getPath() + File.separator
                + conf.getSnowmanServer().getCameraname() + "_"
                + (long) (System.currentTimeMillis() / 1000) + "_"
                + conf.getStreamer().getFramesPerSecond() + "_00000000.jpeg");

            LOGGER.trace("cmdLine: {}", cmdLine);

            // 10 seconds should be more than enough
            final long safetyTimeWindow = 10000;

            final DefaultExecutor executor = new DefaultExecutor();
            final long timeout = 1000 * (conf.getStreamer().getRecordTime() + safetyTimeWindow);
            // final long timeout = 5000;
            LOGGER.trace("timeout: {}", timeout);
            final ExecuteWatchdog watchdog = new ExecuteWatchdog(timeout);
            executor.setWatchdog(watchdog);
            try {
                LOGGER.debug("start process");
                final int exitValue = executor.execute(cmdLine);
                LOGGER.debug("process executed");
                LOGGER.trace("exitValue: {}", exitValue);
            } catch (IOException e) {
                if (watchdog.killedProcess()) {
                    LOGGER.warn("Process was killed on purpose by the watchdog ");
                } else {
                    LOGGER.error("Process exited with an error.");
                    Imager.waitALittleBit(5000);
                }
            }
            LOGGER.trace("loop end");

        }
    }

}
