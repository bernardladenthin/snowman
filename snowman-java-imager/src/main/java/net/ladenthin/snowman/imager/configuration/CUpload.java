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
package net.ladenthin.snowman.imager.configuration;

import com.google.common.base.Objects;

import java.io.Serializable;

import javax.annotation.concurrent.Immutable;

/**
 * Configuration class.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
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
        return Objects.hashCode(ignoreLastFiles, threads);
    }

    @Override
    public boolean equals(Object obj) {
        if (this == obj) {
            return true;
        }
        if (obj == null || getClass() != obj.getClass()) {
            return false;
        }
        final CUpload other = (CUpload) obj;
        return Objects.equal(this.ignoreLastFiles, other.ignoreLastFiles)
                && Objects.equal(this.threads, other.threads);
    }

    @Override
    public String toString() {
        return Objects.toStringHelper(this)
                .add("ignoreLastFiles", ignoreLastFiles)
                .add("threads", threads)
                .toString();
    }
}
