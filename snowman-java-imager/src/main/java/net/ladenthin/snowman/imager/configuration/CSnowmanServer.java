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
 * @author Bernard Ladenthin: bernard.ladenthin@gmail.com
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
        this.apiUrl = Objects.requireNonNull(apiUrl);
        this.cameraName = Objects.requireNonNull(cameraName);
        this.password = Objects.requireNonNull(password);
        this.username = Objects.requireNonNull(username);
    }

    public String getApiUrl() {
        return Objects.requireNonNull(apiUrl);
    }

    public String getCameraname() {
        return Objects.requireNonNull(cameraName);
    }

    public String getPassword() {
        return Objects.requireNonNull(password);
    }

    public String getUsername() {
        return Objects.requireNonNull(username);
    }

    @Override
    public int hashCode() {
        int hash = 3;
        hash = 37 * hash + Objects.hashCode(this.apiUrl);
        hash = 37 * hash + Objects.hashCode(this.cameraName);
        hash = 37 * hash + Objects.hashCode(this.password);
        hash = 37 * hash + Objects.hashCode(this.username);
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
        final CSnowmanServer other = (CSnowmanServer) obj;
        if (!Objects.equals(this.apiUrl, other.apiUrl)) {
            return false;
        }
        if (!Objects.equals(this.cameraName, other.cameraName)) {
            return false;
        }
        if (!Objects.equals(this.password, other.password)) {
            return false;
        }
        if (!Objects.equals(this.username, other.username)) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "CSnowmanServer{" + "apiUrl=" + apiUrl + ", cameraName=" + cameraName + ", password=" + password + ", username=" + username + '}';
    }
    
}
