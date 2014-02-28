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
package net.ladenthin.snowman.imager.run;

import org.apache.commons.cli.CommandLine;
import org.apache.commons.cli.CommandLineParser;
import org.apache.commons.cli.HelpFormatter;
import org.apache.commons.cli.OptionBuilder;
import org.apache.commons.cli.Options;
import org.apache.commons.cli.PosixParser;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 * This Class provide a command line interface for imager.
 *
 * @author Bernard Ladenthin <bernard.ladenthin@gmail.com>
 */
public class CLI {

    public final static String cmdHelp = "help";
    public final static String cmdHelpS = "h";
    public final static String cmdHelpD = "Print this message.";

    public final static String cmdVersion = "version";
    public final static String cmdVersionS = "v";
    public final static String cmdVersionD = "Print the version string.";

    public final static String cmdConfiguration = "configuration";
    public final static String cmdConfigurationS = "c";
    public final static String cmdConfigurationD = "Configuration path.";
    public final static String cmdConfigurationA = "PATH";

    private final static Logger LOGGER = LogManager.getLogger(CLI.class.getName());

    @SuppressWarnings("static-access")
    public static void main(String[] args) throws Exception {
        try {
            CommandLineParser parser = new PosixParser();
            Options options = new Options();

            options.addOption(cmdHelpS, cmdHelp, false, cmdHelpD);
            options.addOption(cmdVersionS, cmdVersion, false, cmdVersionD);

            options.addOption(OptionBuilder.withDescription(cmdConfigurationD)
                .withLongOpt(cmdConfiguration).hasArg().withArgName(cmdConfigurationA)
                .create(cmdConfigurationS));

            // parse the command line arguments
            CommandLine line = parser.parse(options, args);

            final String cmdLineSyntax = "java -jar " + Imager.jarFilename;
            // automatically generate the help statement
            HelpFormatter formatter = new HelpFormatter();

            final String configurationPath;
            if (line.hasOption(cmdConfiguration)) {
                configurationPath = line.getOptionValue(cmdConfiguration);
            } else {
                System.out.println("Need configuration value.");
                formatter.printHelp(cmdLineSyntax, options);
                return;
            }

            // check parameter
            if (args.length == 0 || line.hasOption(cmdHelp)) {
                formatter.printHelp(cmdLineSyntax, options);
                return;
            }

            if (line.hasOption(cmdVersion)) {
                System.out.println(Imager.version);
                return;
            }

            new Imager(configurationPath);

        } catch (Exception e) {
            LOGGER.error("Critical exception.", e);
            System.exit(-1);
        }
    }
}
