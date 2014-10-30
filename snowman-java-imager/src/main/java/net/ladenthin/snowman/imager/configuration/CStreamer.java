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

import com.google.common.base.Objects;

import java.io.Serializable;

import javax.annotation.concurrent.Immutable;

/**
 * Configuration class.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
 */
@Immutable
public class CStreamer implements Serializable {

    private static final long serialVersionUID = -5637123625275071790L;

    private final String device;
    private final int framesPerSecond;
    private final String path;
    private final int recordTime;
    private final int resolutionX;
    private final int resolutionY;

    public CStreamer(final String device, final int framesPerSecond, final String path,
        final int recordTime, final int resolutionX, final int resolutionY) {
        this.device = java.util.Objects.requireNonNull(device);
        this.framesPerSecond = Int.requirePositive(framesPerSecond);
        this.path = java.util.Objects.requireNonNull(path);
        this.recordTime = Int.requirePositive(recordTime);
        this.resolutionX = Int.requirePositive(resolutionX);
        this.resolutionY = Int.requirePositive(resolutionY);
    }

    public String getDevice() {
        return java.util.Objects.requireNonNull(device);
    }

    public int getFramesPerSecond() {
        return Int.requirePositive(framesPerSecond);
    }

    public String getPath() {
        return java.util.Objects.requireNonNull(path);
    }

    public int getRecordTime() {
        return Int.requirePositive(recordTime);
    }

    public int getResolutionX() {
        return Int.requirePositive(resolutionX);
    }

    public int getResolutionY() {
        return Int.requirePositive(resolutionY);
    }

    @Override
    public int hashCode() {
        return Objects.hashCode(device, framesPerSecond, path, recordTime, resolutionX, resolutionY);
    }

    @Override
    public boolean equals(Object obj) {
        if (this == obj) {
            return true;
        }
        if (obj == null || getClass() != obj.getClass()) {
            return false;
        }
        final CStreamer other = (CStreamer) obj;
        return Objects.equal(this.device, other.device)
                && Objects.equal(this.framesPerSecond, other.framesPerSecond)
                && Objects.equal(this.path, other.path)
                && Objects.equal(this.recordTime, other.recordTime)
                && Objects.equal(this.resolutionX, other.resolutionX)
                && Objects.equal(this.resolutionY, other.resolutionY);
    }

    @Override
    public String toString() {
        return Objects.toStringHelper(this)
                .add("device", device)
                .add("framesPerSecond", framesPerSecond)
                .add("path", path)
                .add("recordTime", recordTime)
                .add("resolutionX", resolutionX)
                .add("resolutionY", resolutionY)
                .toString();
    }
}
