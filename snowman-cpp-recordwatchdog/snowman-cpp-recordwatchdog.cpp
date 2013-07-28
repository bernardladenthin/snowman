/**
 * snowman-cpp-recordwatchdog - High performance watchdog to detect
 * a hanging snowman-py-record script.
 * http://code.google.com/p/snowman/
 *
 * Copyright (C) 2013 Bernard Ladenthin <bernard@ladenthin.net>
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

#include <signal.h>
#include <sys/stat.h>
#include <boost/algorithm/string.hpp>
#include <boost/program_options.hpp>

using namespace std;
using namespace boost;

static int debug;
static bool run;

void printLicense();
void printVersion();
bool isSnomanVideoConvertRunning();

void signalCallbackHandler(
	int signum
);

void printLicense() {
cout
<<endl
<<" snowman-cpp-recordwatchdog - High performance watchdog to detect"
<<endl
<<" a hanging snowman-py-record script."
<<endl
<<" Copyright (C) 2013 Bernard Ladenthin <bernard@ladenthin.net>"
<<endl
<<endl
<<" This program is free software: you can redistribute it and/or modify"
<<endl
<<" it under the terms of the GNU General Public License as published by"
<<endl
<<" the Free Software Foundation, either version 3 of the License, or"
<<endl
<<" (at your option) any later version."
<<endl
<<endl
<<" This program is distributed in the hope that it will be useful,"
<<endl
<<" but WITHOUT ANY WARRANTY; without even the implied warranty of"
<<endl
<<" MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the"
<<endl
<<" GNU General Public License for more details."
<<endl
<<endl
<<" You should have received a copy of the GNU General Public License"
<<endl
<<" along with this program.  If not, see <http://www.gnu.org/licenses/>."
<<endl
<<flush;
}

void printVersion() {
	cout<<"snowman-cpp-recordwatchdog for snowman-py-record; Version: 1.0;"
	<<" Date: 2013-07-13"<<endl<<flush;
}

void signalCallbackHandler(
	int signum
) {
	if(debug) {
		cout << "Caught signal: "<<signum<<endl<<flush;
	}
	run = false;
}

bool isSnomanVideoConvertRunning() {

	string command = "/bin/ps ax";
	string output;
	FILE *in;
	char buff[2048];
	if(!(in = popen(command.c_str(), "r"))) {
		return false;
	}

	while(fgets(buff, sizeof(buff), in)!=NULL) {
		output.append(buff);
	}

	string snowmanPyAvconv("snowman-py-avconv");
	string snowmanPyFfmpeg("snowman-py-ffmpeg");

	if (string::npos != output.find(snowmanPyAvconv)) {
		return true;
	}

	if (string::npos != output.find(snowmanPyFfmpeg)) {
		return true;
	}

	return false;
}

int main(int argc, char * argv[]) {
	string pathToMonitor;
	int timeHanging;

	printVersion();

	//if change something here, do not forget to change the desc options
	unsigned sleepTime = 5000000;
	timeHanging = 300;

	debug = 0;
	bool invalidOption = false;

	program_options::options_description desc("Allowed options");
	desc.add_options()
		("help", "produce help message")
		("version,v", "print version and license message")
		(
			"debug,d",
			program_options::value<int>(),
			"The debug level for information output. "
				"Default is [0]."
		)
		(
			"pathToMonitor",
			program_options::value<string>(),
			"The path to be monitored for changes. "
		)
		(
			"sleep-time,s",
			program_options::value<unsigned>(),
			"Mikro seconds of main loop. "
				"Calculate like (5s)*1000000. Default is 5s (0.2Hz) [5000000]."
		)
		(
			"timeHanging",
			program_options::value<unsigned>(),
			"The timeout option for curl. "
				"Default is [300]. Unit: [s]."
		)
	;

	program_options::variables_map vm;

	try {
			program_options::store(
				program_options::parse_command_line(argc, argv, desc), vm
			);
			program_options::notify(vm);
	} catch (
				boost::exception_detail::clone_impl<
					boost::exception_detail::error_info_injector<
						boost::program_options::unknown_option
					>
				>& c
	) {
		cout << "Some option you gave was unknown. " <<
			"Please give '--help' to get help." << endl;
		return EXIT_FAILURE;
	} catch (

				boost::exception_detail::clone_impl<
					boost::exception_detail::error_info_injector<
						boost::program_options::invalid_command_line_syntax
					>
				>& c
	) {
		cout << "Invalud command line syntax. "<<
			"Please give '--help' to get help." << endl;
		return EXIT_FAILURE;
	} catch (

				boost::exception_detail::clone_impl<
					boost::exception_detail::error_info_injector<
						boost::program_options::invalid_option_value
					>
				>& c
	) {
		cout << "Some option you gave has a wrong value. "<<
			"Please give '--help' to get help." << endl;
		return EXIT_FAILURE;
	}


	if (vm.count("help")) {
		printVersion();
		cout << desc << endl;
		return EXIT_FAILURE;
	}

	if (vm.count("version")) {
		printVersion();
		printLicense();
		return EXIT_FAILURE;
	}

	if (vm.count("pathToMonitor")) {
		pathToMonitor = vm["pathToMonitor"].as<string>();
		cout << "option pathToMonitor overwritten as ["
		<< pathToMonitor << "]"
		<< endl;
	} else {
		cout << "Option pathToMonitor not given. "<<endl;
		invalidOption = true;
	}

	if (vm.count("timeHanging")) {
		timeHanging = vm["timeHanging"].as<unsigned>();
		cout << "option timeHanging overwritten as ["
		<< timeHanging << "]"
		<< endl;
	}

	if (vm.count("debug")) {
		debug = vm["debug"].as<int>();
		cout << "option debug overwritten as ["
		<< debug << "]"
		<< endl;
	}

	if (vm.count("sleep-time")) {
		sleepTime = vm["sleep-time"].as<unsigned>();
		cout << "option sleep-time overwritten as ["
		<< sleepTime << "]"
		<< endl;
	}

	if(invalidOption) {
		cout << "Invalid option given. "
			"Please give '--help' to get help." << endl;
		return EXIT_FAILURE;
	}

	cout << endl << "...running" << endl << flush;

	run = true;

	signal(SIGINT, signalCallbackHandler);

	while(run) {
		usleep(sleepTime);

		struct stat pathStat;
		int statReturn = stat(pathToMonitor.c_str(), &pathStat);
		if(statReturn != 0) {
			if(debug) {
				cout
				<<"main loop: "
				<<"statReturn != 0: "
				<<statReturn
				<<endl
				<<flush;
			}
			continue;
		}

		time_t currentTime;
		time(&currentTime);

		double seconds;
		seconds = difftime(currentTime, pathStat.st_mtime);

			if(debug) {
				cout
				<<"main loop: "
				<<"seconds: "
				<<seconds
				<<endl
				<<flush;
			}

		if(seconds >= timeHanging) {
			if(debug) {
				cout
				<<"main loop: "
				<<"seconds > timeHanging: restart operating system"
				<<endl
				<<flush;
			}

			if(isSnomanVideoConvertRunning()) {
				if(debug) {
					cout
					<<"main loop: "
					<<"isSnomanVideoConvertRunning == true; "
					<<"Continue to wait for the completion"
					<<endl
					<<flush;
				}
				continue;
			}

			if(debug) {
				cout
				<<"main loop: "
				<<"restart operating system"
				<<endl
				<<flush;
			}

			sync();
			system("reboot");
			run = false;
		}

	}

	cout<<endl<<"run terminated..."<<endl<<flush;
	cout << "return EXIT_SUCCESS" << endl << endl << flush;
	return EXIT_SUCCESS;
}

