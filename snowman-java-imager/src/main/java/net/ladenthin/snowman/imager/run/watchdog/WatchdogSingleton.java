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

import java.util.Objects;

/**
 * A singleton for the {@link Watchdog}.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
 */
public class WatchdogSingleton {

    private static WatchdogSingleton singleton;
    private final Thread thread;
    private final Watchdog watchdog;

    private WatchdogSingleton() {
        watchdog = new Watchdog();
        thread = new Thread(watchdog);
        thread.setName(Watchdog.class.getName());
        thread.start();
    }

    public static synchronized WatchdogSingleton getSingleton() {
        return Objects.requireNonNull(singleton);
    }

    public static synchronized void setSingleton() {
        if (WatchdogSingleton.singleton != null) {
            throw new RuntimeException("singleton != null");
        }
        WatchdogSingleton.singleton = new WatchdogSingleton();
    }

    public Thread getThread() {
        return thread;
    }

    public Watchdog getWatchdog() {
        return watchdog;
    }

}