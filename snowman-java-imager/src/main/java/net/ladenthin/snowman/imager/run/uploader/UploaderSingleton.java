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

import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

import net.ladenthin.snowman.imager.run.ConfigurationSingleton;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 * A singleton for the {@link Uploader}.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
 */
public enum UploaderSingleton {
    UploaderSingleton;
    
    private final static Logger LOGGER = LogManager.getLogger(UploaderSingleton.class.getName());

    private ExecutorService executor;

    public void createWorkerThreads() {
        if (executor != null) {
            throw new RuntimeException("executor != null");
        }
        final int threads = ConfigurationSingleton.ConfigurationSingleton.getImager().getUpload().getThreads();
        executor = Executors.newFixedThreadPool(threads);
        LOGGER.debug("Create worker threads: {}", threads);
        for (int i = 0; i < threads; ++i) {
            Runnable worker = new Uploader();
            executor.execute(worker);
        }
    }

    public ExecutorService getExecutor() {
        return executor;
    }

}
