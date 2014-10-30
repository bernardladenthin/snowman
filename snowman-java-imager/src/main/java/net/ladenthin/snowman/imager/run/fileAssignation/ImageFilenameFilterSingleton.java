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
package net.ladenthin.snowman.imager.run.fileAssignation;

import java.io.File;
import java.io.FilenameFilter;

/**
 * A singleton to implement the {@link FilenameFilter}.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
 */
public class ImageFilenameFilterSingleton implements FilenameFilter {

    private static ImageFilenameFilterSingleton singleton = new ImageFilenameFilterSingleton();

    private ImageFilenameFilterSingleton() {
    }

    public static synchronized ImageFilenameFilterSingleton getSingleton() {
        return singleton;
    }

    @Override
    public boolean accept(final File dir, final String name) {
        final String lName = name.toLowerCase();

        int dot = lName.lastIndexOf(".");
        String fExtension = lName.substring(dot + 1);

        switch (fExtension) {
        case "jpeg":
        case "jpg":
            return true;
        }

        return false;
    }

}
