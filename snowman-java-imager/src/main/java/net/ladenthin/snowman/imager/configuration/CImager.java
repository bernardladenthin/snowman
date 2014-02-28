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

import javax.annotation.concurrent.Immutable;
import java.io.Serializable;

/**
 * Configuration class.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
 */
@Immutable
public class CImager implements Serializable {

    private static final long serialVersionUID = -1716062324458588805L;

    private final CSnowmanServer snowmanServer;
    private final CStreamer streamer;
    private final CUpload upload;
    private final CWatchdog watchdog;

    public CImager(final CSnowmanServer snowmanServer, final CStreamer streamer,
                   final CUpload upload, final CWatchdog watchdog) {
        this.snowmanServer = java.util.Objects.requireNonNull(snowmanServer);
        this.streamer = java.util.Objects.requireNonNull(streamer);
        this.upload = java.util.Objects.requireNonNull(upload);
        this.watchdog = java.util.Objects.requireNonNull(watchdog);
    }

    public CSnowmanServer getSnowmanServer() {
        return java.util.Objects.requireNonNull(snowmanServer);
    }

    public CStreamer getStreamer() {
        return java.util.Objects.requireNonNull(streamer);
    }

    public CUpload getUpload() {
        return java.util.Objects.requireNonNull(upload);
    }

    public CWatchdog getWatchdog() {
        return java.util.Objects.requireNonNull(watchdog);
    }

    @Override
    public int hashCode() {
        return Objects.hashCode(snowmanServer, streamer, upload, watchdog);
    }

    @Override
    public boolean equals(Object obj) {
        if (this == obj) {
            return true;
        }
        if (obj == null || getClass() != obj.getClass()) {
            return false;
        }
        final CImager other = (CImager) obj;
        return Objects.equal(this.snowmanServer, other.snowmanServer)
                && Objects.equal(this.streamer, other.streamer)
                && Objects.equal(this.upload, other.upload)
                && Objects.equal(this.watchdog, other.watchdog);
    }

    @Override
    public String toString() {
        return Objects.toStringHelper(this)
                .add("snowmanServer", snowmanServer)
                .add("streamer", streamer)
                .add("upload", upload)
                .add("watchdog", watchdog)
                .toString();
    }
}
