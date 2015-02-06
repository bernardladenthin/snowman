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
import java.util.Objects;

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
        this.device = Objects.requireNonNull(device);
        this.framesPerSecond = Int.requirePositive(framesPerSecond);
        this.path = Objects.requireNonNull(path);
        this.recordTime = Int.requirePositive(recordTime);
        this.resolutionX = Int.requirePositive(resolutionX);
        this.resolutionY = Int.requirePositive(resolutionY);
    }

    public String getDevice() {
        return Objects.requireNonNull(device);
    }

    public int getFramesPerSecond() {
        return Int.requirePositive(framesPerSecond);
    }

    public String getPath() {
        return Objects.requireNonNull(path);
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
        int hash = 3;
        hash = 37 * hash + Objects.hashCode(this.device);
        hash = 37 * hash + this.framesPerSecond;
        hash = 37 * hash + Objects.hashCode(this.path);
        hash = 37 * hash + this.recordTime;
        hash = 37 * hash + this.resolutionX;
        hash = 37 * hash + this.resolutionY;
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
        final CStreamer other = (CStreamer) obj;
        if (!Objects.equals(this.device, other.device)) {
            return false;
        }
        if (this.framesPerSecond != other.framesPerSecond) {
            return false;
        }
        if (!Objects.equals(this.path, other.path)) {
            return false;
        }
        if (this.recordTime != other.recordTime) {
            return false;
        }
        if (this.resolutionX != other.resolutionX) {
            return false;
        }
        if (this.resolutionY != other.resolutionY) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "CStreamer{" + "device=" + device + ", framesPerSecond=" + framesPerSecond + ", path=" + path + ", recordTime=" + recordTime + ", resolutionX=" + resolutionX + ", resolutionY=" + resolutionY + '}';
    }
    
}
