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
import java.util.List;

import javax.annotation.concurrent.Immutable;
import com.google.common.collect.ImmutableList;
import java.util.Objects;

/**
 * Configuration class.
 *
 * @author Bernard Ladenthin: bernard.ladenthin@gmail.com
 */
@Immutable
public class CWatchdog implements Serializable {

    private static final long serialVersionUID = 7876821946293577214L;

    private final List<String> blockingProcesses;
    private final int interval;
    private final String restartCommand;
    private final int timeWindow;

    public CWatchdog(final List<String> blockingProcesses, final int interval, final String restartCommand,
                     final int timeWindow) {
        if (blockingProcesses == null) {
            this.blockingProcesses = ImmutableList.of();
        } else {
            this.blockingProcesses = ImmutableList.copyOf(blockingProcesses);
        }

        this.interval = Int.requirePositive(interval);
        this.restartCommand = Objects.requireNonNull(restartCommand);
        this.timeWindow = Int.requirePositive(timeWindow);
    }

    @edu.umd.cs.findbugs.annotations.SuppressWarnings(value = "BC_VACUOUS_INSTANCEOF",
        justification = "It is quite possible that by GSON the type is e.g. ArrayList.")
    public ImmutableList<String> getBlockingProcesses() {
        Objects.requireNonNull(blockingProcesses);
        if (blockingProcesses instanceof ImmutableList) {
            return (ImmutableList<String>) blockingProcesses;
        } else {
            return ImmutableList.copyOf(blockingProcesses);
        }
    }

    public List<String> getBlockingProcessesUnsafe() {
        return Objects.requireNonNull(blockingProcesses);
    }

    public int getInterval() {
        return Int.requirePositive(interval);
    }

    public String getRestartCommand() {
        return Objects.requireNonNull(restartCommand);
    }

    public int getTimeWindow() {
        return Int.requirePositive(timeWindow);
    }

    @Override
    public int hashCode() {
        int hash = 5;
        hash = 89 * hash + Objects.hashCode(this.blockingProcesses);
        hash = 89 * hash + this.interval;
        hash = 89 * hash + Objects.hashCode(this.restartCommand);
        hash = 89 * hash + this.timeWindow;
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
        final CWatchdog other = (CWatchdog) obj;
        if (!Objects.equals(this.blockingProcesses, other.blockingProcesses)) {
            return false;
        }
        if (this.interval != other.interval) {
            return false;
        }
        if (!Objects.equals(this.restartCommand, other.restartCommand)) {
            return false;
        }
        if (this.timeWindow != other.timeWindow) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "CWatchdog{" + "blockingProcesses=" + blockingProcesses + ", interval=" + interval + ", restartCommand=" + restartCommand + ", timeWindow=" + timeWindow + '}';
    }
    
}
