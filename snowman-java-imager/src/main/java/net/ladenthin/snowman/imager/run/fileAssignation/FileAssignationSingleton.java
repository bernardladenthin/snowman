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
package net.ladenthin.snowman.imager.run.fileAssignation;

import java.io.File;
import java.util.*;

import net.ladenthin.snowman.imager.run.ConfigurationSingleton;
import net.ladenthin.snowman.imager.run.RuntimeSingleton;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 * A singleton to assign files.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
 */
public class FileAssignationSingleton {

    private final static Logger LOGGER = LogManager.getLogger(FileAssignationSingleton.class.getName());

    private static FileAssignationSingleton singleton = new FileAssignationSingleton();

    public static synchronized FileAssignationSingleton getSingleton() {
        return singleton;
    }

    private FileAssignationSingleton() {
    }

    private final NavigableSet<File> idleFiles = new TreeSet<>();
    private final Set<File> obtainedFiles = new HashSet<>();

    private synchronized void fetchFiles() {
        LOGGER.debug("fetchFiles");
        final File fetchPath = RuntimeSingleton.getSingleton().getStreamerPath();

        final File[] files = fetchPath.listFiles(ImageFilenameFilterSingleton.getSingleton());
        if (files.length == 0) {
            return;
        }
        Collections.addAll(idleFiles, files);
        idleFiles.removeAll(obtainedFiles);
        LOGGER.trace("fetched idleFiles.size(): {}", idleFiles.size());
        final int ignoreLastFiles =
            ConfigurationSingleton.getSingleton().getImager().getUpload().getIgnoreLastFiles();
        LOGGER.trace("rignoreLastFiles: {}", ignoreLastFiles);
        for (int i = 0; i < ignoreLastFiles; ++i) {
            idleFiles.pollLast();
        }
        LOGGER.trace("fetched idleFiles.size(): {}", idleFiles.size());
    }

    public synchronized File obtainFile() {
        if (idleFiles.size() == 0) {
            LOGGER.trace("outer if idleFiles.size() == 0");
            fetchFiles();
            if (idleFiles.size() == 0) {
                LOGGER.trace("inner if idleFiles.size() == 0");
                return null;
            }
        }

        File idleFile = idleFiles.pollFirst();
        if (idleFile.exists()) {
            obtainedFiles.add(idleFile);
            return idleFile;
        } else {
            LOGGER.warn("file does not exist: " + idleFile);
            return null;
        }
    }

    public synchronized void freeFile(File obtainedFile) {
        obtainedFiles.remove(obtainedFile);
    }

}
