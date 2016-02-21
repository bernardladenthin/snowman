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
package net.ladenthin.snowman.imager.configuration;

import java.io.Serializable;

import javax.annotation.concurrent.Immutable;

/**
 * Configuration class.
 *
 * @author Bernard Ladenthin: bernard.ladenthin@gmail.com
 */
@Immutable
public class CUpload implements Serializable {

    private static final long serialVersionUID = 773799314188888515L;

    private final int ignoreLastFiles;
    private final int threads;

    public CUpload(final int ignoreLastFiles, final int threads) {
        this.ignoreLastFiles = Int.requirePositive(ignoreLastFiles);
        this.threads = Int.requirePositive(threads);
    }

    public int getIgnoreLastFiles() {
        return Int.requirePositive(ignoreLastFiles);
    }

    public int getThreads() {
        return Int.requirePositive(threads);
    }

    @Override
    public int hashCode() {
        int hash = 7;
        hash = 23 * hash + this.ignoreLastFiles;
        hash = 23 * hash + this.threads;
        return hash;
    }

    @Override
    public boolean equals(Object obj) {
        if (obj == null) {
            return false;
        }
        if (getClass() != obj.getClass()) {
            return false;
        }
        final CUpload other = (CUpload) obj;
        if (this.ignoreLastFiles != other.ignoreLastFiles) {
            return false;
        }
        if (this.threads != other.threads) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "CUpload{" + "ignoreLastFiles=" + ignoreLastFiles + ", threads=" + threads + '}';
    }
    
}
