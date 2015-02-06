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

import javax.annotation.concurrent.Immutable;
import java.io.Serializable;
import java.util.Objects;

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
        this.snowmanServer = Objects.requireNonNull(snowmanServer);
        this.streamer = Objects.requireNonNull(streamer);
        this.upload = Objects.requireNonNull(upload);
        this.watchdog = Objects.requireNonNull(watchdog);
    }

    public CSnowmanServer getSnowmanServer() {
        return Objects.requireNonNull(snowmanServer);
    }

    public CStreamer getStreamer() {
        return Objects.requireNonNull(streamer);
    }

    public CUpload getUpload() {
        return Objects.requireNonNull(upload);
    }

    public CWatchdog getWatchdog() {
        return Objects.requireNonNull(watchdog);
    }

    @Override
    public int hashCode() {
        int hash = 3;
        hash = 67 * hash + Objects.hashCode(this.snowmanServer);
        hash = 67 * hash + Objects.hashCode(this.streamer);
        hash = 67 * hash + Objects.hashCode(this.upload);
        hash = 67 * hash + Objects.hashCode(this.watchdog);
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
        final CImager other = (CImager) obj;
        if (!Objects.equals(this.snowmanServer, other.snowmanServer)) {
            return false;
        }
        if (!Objects.equals(this.streamer, other.streamer)) {
            return false;
        }
        if (!Objects.equals(this.upload, other.upload)) {
            return false;
        }
        if (!Objects.equals(this.watchdog, other.watchdog)) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "CImager{" + "snowmanServer=" + snowmanServer + ", streamer=" + streamer + ", upload=" + upload + ", watchdog=" + watchdog + '}';
    }
    
}
