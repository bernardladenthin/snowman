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

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 * A singleton for runtime objects.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
 */
public enum RuntimeSingleton {
    RuntimeSingleton;

    public final static Logger LOGGER = LogManager.getLogger(RuntimeSingleton.class.getName());
    private File streamerPath;

    public void initializeStreamerPath(String pathname) {
        streamerPath = new File(pathname);
        if (!streamerPath.exists()) {
            boolean success = streamerPath.mkdirs();
            if (!success) {
                LOGGER.error("Could not create streamerPath: {}", streamerPath);
            }
        }
    }

    public File getStreamerPath() {
        return streamerPath;
    }

    public boolean isStreamerPathValid() {
        if (streamerPath == null) {
            return false;
        }
        if (!streamerPath.canRead()) {
            return false;
        }
        if (!streamerPath.canWrite()) {
            return false;
        }
        if (!streamerPath.isDirectory()) {
            return false;
        }
        return true;
    }

}
