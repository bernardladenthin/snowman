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
public class CSnowmanServer implements Serializable {

    private static final long serialVersionUID = 3714099543664885363L;

    private final String apiUrl;
    private final String cameraName;
    private final String password;
    private final String username;

    public CSnowmanServer(final String apiUrl, final String cameraName, final String username,
        final String password) {
        this.apiUrl = java.util.Objects.requireNonNull(apiUrl);
        this.cameraName = java.util.Objects.requireNonNull(cameraName);
        this.password = java.util.Objects.requireNonNull(password);
        this.username = java.util.Objects.requireNonNull(username);
    }

    public String getApiUrl() {
        return java.util.Objects.requireNonNull(apiUrl);
    }

    public String getCameraname() {
        return java.util.Objects.requireNonNull(cameraName);
    }

    public String getPassword() {
        return java.util.Objects.requireNonNull(password);
    }

    public String getUsername() {
        return java.util.Objects.requireNonNull(username);
    }

    @Override
    public int hashCode() {
        return Objects.hashCode(apiUrl, cameraName, password, username);
    }

    @Override
    public boolean equals(Object obj) {
        if (this == obj) {
            return true;
        }
        if (obj == null || getClass() != obj.getClass()) {
            return false;
        }
        final CSnowmanServer other = (CSnowmanServer) obj;
        return Objects.equal(this.apiUrl, other.apiUrl)
                && Objects.equal(this.cameraName, other.cameraName)
                && Objects.equal(this.password, other.password)
                && Objects.equal(this.username, other.username);
    }

    @Override
    public String toString() {
        return Objects.toStringHelper(this)
                .add("apiUrl", apiUrl)
                .add("cameraName", cameraName)
                .add("password", password)
                .add("username", username)
                .toString();
    }
}
