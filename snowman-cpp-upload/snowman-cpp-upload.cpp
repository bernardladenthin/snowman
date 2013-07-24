/**
 * snowman-cpp-upload - High performance upload tool to upload images
 * on a snowman-php-server.
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

#include <algorithm>
#include <deque>
#include <dirent.h>
#include <iostream>
#include <signal.h>
#include <stdio.h>
#include <string>
#include <vector>
#include <boost/algorithm/string.hpp>
#include <boost/date_time/posix_time/posix_time.hpp>
#include <boost/program_options.hpp>
#include <boost/thread.hpp>
#include <boost/thread/mutex.hpp>

using namespace std;
using namespace boost;

// e.g. to output a vector<string>
template<class T>
ostream& operator<<(ostream& os, const vector<T>& v) {
	copy(v.begin(), v.end(), ostream_iterator<T>(cout, " "));
	return os;
}

static string requestCameraimage;
static string requestUsername;
static string requestPassword;
static string requestCameraname;
static string responseSuccess;
static string curlPath;
static string curlOpt;
static string curlTimeout;
static string username;
static string password;
static string cameraname;
static string host;
static string path;
static string slash;
static int debug;
static bool run;

void printLicense();
void printVersion();

void getAvailableFileVector(
	vector<string> * fileVector,
	const vector<string> extensionVector,
	const string path,
	unsigned maxFiles
);

void signalCallbackHandler(
	int signum
);

void workerThread(const string filename);

void createWorkerThreadsFromUntreated(
	boost::thread_group * workerThreadsThreadGroup,
	vector<boost::thread *> * workerThreadsVector,
	vector<string> * untreatedFileVector,
	unsigned const maxThreads
);

void uploadSingleFile(const string filename);

void removeJoinableThreads(
	vector<boost::thread *> * workerThreadsVector
);

/* ----- list of current uploading files ----- */
static vector<string> currentUploadingFileVector;
static boost::mutex mutexCurrentUploadingFileVector;


void printLicense() {
cout
<<endl
<<" snowman-cpp-upload - High performance upload tool to upload images"
<<endl
<<" on a snowman-php-server."
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
	cout<<"snowman-cpp-upload for snowman-php-server; Version: 1.0;"
	<<" Date: 2013-05-01"<<endl<<flush;
}

void signalCallbackHandler(
	int signum
) {
	if(debug) {
		cout << "Caught signal: "<<signum<<endl<<flush;
	}
	run = false;
}

void removeJoinableThreads(
	vector<boost::thread *> * workerThreadsVector
) {

	if(debug) {
		cout
		<<"removeJoinableThreads(): "
		<<"start subroutine"
		<<endl
		<<flush;
	}

	if(debug) {
		cout
		<<"removeJoinableThreads(): "
		<<"workerThreadsVector->size()"
		<<endl
		<<flush;
	}

	for(unsigned i=0;i<workerThreadsVector->size();++i) {

	if(debug) {
		cout
		<<"removeJoinableThreads(): "
		<<"for loop: "
		<<"check: "
		<<"["<<i<<"]"
		<<endl
		<<flush;
	}

		boost::thread *t = workerThreadsVector->at(i);
		bool finished = t->timed_join(boost::posix_time::millisec(1));
		if(finished) {

		if(debug) {
			cout
			<<"removeJoinableThreads(): "
			<<"for loop: "
			<<"check: "
			<<"["<<i<<"]: "
			<<"finished"
			<<endl
			<<flush;
		}

			workerThreadsVector->erase(workerThreadsVector->begin()+i);

			if(debug) {
				cout
				<<"removeJoinableThreads(): "
				<<"for loop: "
				<<"workerThreadsVector->erase(workerThreadsVector->begin()+i);"
				<<endl
				<<flush;
			}
			--i;
		}
	}

	if(debug) {
		cout
		<<"removeJoinableThreads(): "
		<<"leave subroutine"
		<<endl
		<<flush;
	}
}

void workerThread(const string filename) {

	if(debug) {
		cout
		<<"workerThread(): "
		<<"start thread"
		<<endl
		<<flush;
	}

	if(debug) {
		cout
		<<"workerThread(): "
		<<"mutexCurrentUploadingFileVector.lock();"
		<<endl
		<<flush;
	}
	mutexCurrentUploadingFileVector.lock();

	if(debug) {
		cout
		<<"workerThread(): "
		<<"uploadingFiles.push_back(filename); "
		<<"filename: "
		<<"["<<filename<<"]"
		<<endl
		<<flush;
	}

	currentUploadingFileVector.push_back(filename);

	if(debug) {
		cout
		<<"workerThread(): "
		<<"mutexCurrentUploadingFileVector.unlock();"
		<<endl
		<<flush;
	}

	mutexCurrentUploadingFileVector.unlock();

	string command;

	command.append(curlPath);
	command.append(" ");
	command.append(curlOpt);
	command.append(" ");
	command.append(" -m ");
	command.append(curlTimeout);
	command.append(" -F ");
	command.append(requestCameraimage);
	command.append("=@");
	command.append("\"");
	command.append(path);
	command.append(slash);
	command.append(filename);
	command.append("\"");

	command.append(" -F ");
	command.append(requestUsername);
	command.append("=\"");
	command.append(username);
	command.append("\"");

	command.append(" -F ");
	command.append(requestPassword);
	command.append("=\"");
	command.append(password);
	command.append("\"");

	command.append(" -F ");
	command.append(requestCameraname);
	command.append("=\"");
	command.append(cameraname);
	command.append("\"");

	command.append(" ");
	command.append(host);

	if(debug) {
		cout
		<<"workerThread(): "
		<<"prepared commands: "
		<<command
		<<endl
		<<flush;
	}

	//command.append(" > /dev/null 2>&1 &");
	string output;
	FILE *in;
	char buff[512];
	if(!(in = popen(command.c_str(), "r"))) {
		return;
	}

	while(fgets(buff, sizeof(buff), in)!=NULL) {
		output.append(buff);
	}

	pclose(in);

	if(debug) {
		cout
		<<"workerThread(): "
		<<"server message: "
		<<output
		<<endl
		<<flush;
	}

	erase_all(output, " ");

	if(!output.empty() && responseSuccess == output) {

		if(debug) {
			cout
			<<"workerThread(): "
			<<"server response: "
			<<"success uploaded"
			<<endl
			<<flush;
		}

		//delete the file from filesystem
		string rmString;
		rmString.append(path);
		rmString.append(slash);
		rmString.append(filename);

		if( remove( rmString.c_str() ) != 0 ) {

			if(debug) {
				cout
				<<"workerThread(): "
				<<"ERROR: "
				<<"delete file: "
				<<"["<<rmString<<"]"
				<<endl
				<<flush;
			}
		}
		else {
			if(debug) {
				cout
				<<"workerThread(): "
				<<"delete file: "
				<<"success: "
				<<"["<<rmString<<"]"
				<<endl
				<<flush;
			}
		}

	} else {

		if(debug) {
			cout
			<<"workerThread(): "
			<<"WARNING: "
			<<"server response: "
			<<"upload invalid"
			<<endl
			<<flush;
		}

	}

	if(debug) {
		cout
		<<"workerThread(): "
		<<"mutexCurrentUploadingFileVector.lock();"
		<<endl
		<<flush;
	}

	mutexCurrentUploadingFileVector.lock();

	if(debug) {
		cout
		<<"workerThread(): "
		<<"erase file from list: "
		<<"search for filename: "
		<<"["<<filename<<"]"
		<<endl
		<<flush;
	}

	vector<string>::iterator pos =
		std::find(
			currentUploadingFileVector.begin(),
			currentUploadingFileVector.end(),
			filename
		);

	if(
		pos != currentUploadingFileVector.end()
	) {

	if(debug) {
		cout
		<<"workerThread(): "
		<<"erase file from list: "
		<<"finish remove file: "
		<<"["<<filename<<"]"
		<<endl
		<<flush;
	}

		currentUploadingFileVector.erase(pos);

	} else {
		//this should never happen
		if(debug) {
			cout
			<<"workerThread(): "
			<<"ERROR: "
			<<"erase file from list, file not found: "
			<<"["<<filename<<"]"
			<<endl
			<<flush;
		}
	}

	if(debug) {
		cout
		<<"workerThread(): "
		<<"mutexCurrentUploadingFileVector.unlock();"
		<<endl
		<<flush;
	}

	mutexCurrentUploadingFileVector.unlock();
}

void createWorkerThreadsFromUntreated(
	boost::thread_group * workerThreadsThreadGroup,
	vector<boost::thread *> * workerThreadsVector,
	vector<string> * untreatedFileVector,
	unsigned const maxThreads
) {
	if(debug) {
		cout
		<<"createWorkerThreadsFromUntreated(): "
		<<"start subroutine"
		<<endl
		<<flush;
	}

	//of course file is removed from list, but thread still
	//exist in workerThreadsVector
	while(
		   untreatedFileVector->size()
		&& workerThreadsVector->size() < maxThreads
	) {

		if(debug) {
			cout
			<<"createWorkerThreadsFromUntreated(): "
			<<"while loop: "
			<<"run"
			<<endl
			<<flush;
		}

		if(debug) {
			cout
			<<"createWorkerThreadsFromUntreated(): "
			<<"untreatedFileVector->size(): "
			<<untreatedFileVector->size()
			<<endl
			<<flush;
		}

		boost::thread *t = new boost::thread(
			workerThread,
			untreatedFileVector->front()
		);

		if(debug) {
			cout
			<<"createWorkerThreadsFromUntreated(): "
			<<"untreatedFileVector->erase(untreatedFileVector->begin());"
			<<endl
			<<flush;
		}

		untreatedFileVector->erase(untreatedFileVector->begin());

		if(debug) {
			cout
			<<"createWorkerThreadsFromUntreated(): "
			<<"workerThreadsVector->push_back(t);"
			<<endl
			<<flush;
		}

		workerThreadsVector->push_back(t);

		if(debug) {
			cout
			<<"workerThreadsVector->size(): "
			<<workerThreadsVector->size()
			<<endl
			<<flush;
		}

		if(debug) {
			cout
			<<"workerThreadsThreadGroup->add_thread(t);"
			<<endl
			<<flush;
		}

		workerThreadsThreadGroup->add_thread(t);
	}

		if(debug) {
			cout
			<<"createWorkerThreadsFromUntreated(): "
			<<"untreatedFileVector->size(): "
			<<untreatedFileVector->size()
			<<endl
			<<flush;
		}

		if(debug) {
			cout
			<<"createWorkerThreadsFromUntreated(): "
			<<"while loop: "
			<<"end"
			<<endl
			<<flush;
		}

}

void getAvailableFileVector(
	vector<string> * fileVector,
	const vector<string> extensionVector,
	const string path,
	unsigned maxFiles
) {

	if(debug) {
		cout
		<<"getAvailableFileVector(): "
		<<"start subroutine"
		<<endl
		<<flush;
	}

	if(debug) {
		cout
		<<"getAvailableFileVector(): "
		<<"fileVector->clear();"
		<<endl
		<<flush;
	}
	
	fileVector->clear();

	DIR *d;
	struct dirent *dir;

	if(debug) {
		cout
		<<"getAvailableFileVector(): "
		<<"path.c_str(): "
		<< path.c_str()
		<<endl
		<<flush;
	}

	d = opendir(path.c_str());

	if(d) {

		if(debug) {
			cout
			<<"getAvailableFileVector(): "
			<<"opendir: "
			<<"success"
			<<endl
			<<flush;
		}

		while ((dir = readdir(d)) != NULL) {

			string filename(dir->d_name);

			if(debug) {
				cout
				<<"getAvailableFileVector(): "
				<<"special check for dot files"
				<<endl
				<<flush;
			}

			if(filename == string(".")) {

				if(debug) {
					cout
					<<"getAvailableFileVector(): "
					<<"special check for dot files: "
					<<"filename equals [.]: "
					<<"continue"
					<<endl
					<<flush;
				}

				continue;

			} else if(filename == string("..")) {

					if(debug) {
					cout
					<<"getAvailableFileVector(): "
					<<"special check for dot files: "
					<<"filename equals [..]: "
					<<"continue"
					<<endl
					<<flush;
				}

				continue;
			}

			if(debug) {
				cout
				<<"getAvailableFileVector(): "
				<<"check for maximum files: "
				<<"maxFiles: "
				<<maxFiles
				<<endl
				<<flush;
			}

			if(maxFiles-- < 1) {
				if(debug) {
					cout
					<<"getAvailableFileVector(): "
					<<"maximum files reached: "
					<<"break"
					<<endl
					<<flush;
				}
				break;
			}

			if(debug) {
				cout
				<<"getAvailableFileVector(): "
				<<"check extension: "
				<<"entry"
				<<endl
				<<flush;
			}

			for(unsigned i=0;i<extensionVector.size();++i) {

				if(debug) {
					cout
					<<"getAvailableFileVector(): "
					<<"check extension: "
					<<"check for: "
					<<extensionVector.at(i)
					<<endl
					<<flush;
				}

				size_t found = filename.rfind(extensionVector[i]);

				if(found!=string::npos) {

					if(debug) {
						cout
						<<"getAvailableFileVector(): "
						<<"check extension: "
						<<"positive: "
						<<"found: "
						<<extensionVector.at(i)
						<<endl
						<<flush;
					}

					if(
						   (filename.length() -found)
						== extensionVector.at(i).length()
					) {

						if(debug) {
							cout
							<<"getAvailableFileVector(): "
							<<"check extension length: "
							<<"positive "
							<<endl
							<<flush;
						}

						//no double files
						if(
							   std::find(
								fileVector->begin(),
								fileVector->end(),
								filename
								)
							== fileVector->end()
						) {

							if(debug) {
								cout
								<<"getAvailableFileVector(): "
								<<"check for double files: "
								<<"positive "
								<<endl
								<<flush;
							}

							if(debug) {
								cout
								<<"getAvailableFileVector(): "
								<<"all checks positive: "
								<<"push_back file: "
								<<"["<<filename<<"]"
								<<endl
								<<flush;
							}

							fileVector->push_back(filename);

						} else {
							//This should never happen
								if(debug) {
								cout
								<<"getAvailableFileVector(): "
								<<"ERROR: "
								<<"double file check: "
								<<"double file: "
								<<"["<<filename<<"]"
								<<endl
								<<flush;
							}

						}

					} else {

						if(debug) {
							cout
							<<"getAvailableFileVector(): "
							<<"extension length check: "
							<<"negative: "
							<<"["<<filename<<"]"
							<<endl
							<<flush;
						}

					}

				} else {

					if(debug) {
						cout
						<<"getAvailableFileVector(): "
						<<"extension check: "
						<<"negative: "
						<<"["<<filename<<"]"
						<<endl
						<<flush;
					}

				}

				if(debug) {
					cout
					<<"getAvailableFileVector(): "
					<<"extension check: "
					<<"next one"
					<<endl
					<<flush;
				}

			}

			if(debug) {
				cout
				<<"getAvailableFileVector(): "
				<<"extension check: "
				<<"all extensions checked"
				<<endl
				<<flush;
			}

		}

		if(debug) {
			cout
			<<"getAvailableFileVector(): "
			<<"closedir(d)"
			<<endl
			<<flush;
		}

		closedir(d);
	} else {

		if(debug) {
			cout
			<<"getAvailableFileVector(): "
			<<"ERROR: "
			<<" at opendir, could not be opened"
			<<endl
			<<flush;

			perror("opendir() error");
		}

	}

	if(debug) {
		cout
		<<"getAvailableFileVector(): "
		<<"end subroutine"
		<<endl
		<<flush;
	}

}

int main(int argc, char * argv[]) {
	printVersion();

	//if change something here, do not forget to change the desc options
	requestCameraimage = "cameraimage";
	requestUsername = "username";
	requestPassword = "password";
	requestCameraname = "cameraname";
	//TODO: replace with boost_property_tree read_json
	responseSuccess = "{\"imageUpload\":{\"success\":true}}";
	curlPath = "curl";
	curlOpt = "-s";
	curlTimeout = "10";
	unsigned maxThreads = 10;
	unsigned maxFiles = 100;
	debug = 0;
	unsigned filesIgnore = 1;
	unsigned sleepTime = 500000;
	bool invalidOption = false;

	//boost::filesystem::path slash("/");
	//slash = slash.make_preferred().native();
	slash = "/";

	vector<string> fileExtensionVector;

	program_options::options_description desc("Allowed options");
	desc.add_options()
		("help", "produce help message")
		("version,v", "print version and license message")
		(
			"request-cameraimage",
			program_options::value<string>(),
			"The REQUEST id for the filename. "
				"Default is [camimg]."
		)
		(
			"request-username",
			program_options::value<string>(),
			"The REQUEST id for the username. "
				"Default is [username]."
		)
		(
			"request-password",
			program_options::value<string>(),
			"The REQUEST id for the password. "
				"Default is [password]."
		)
		(
			"request-cameraname",
			program_options::value<string>(),
			"The REQUEST id for the cameraname. "
				"Default is [cameraname]."
		)
		(
			"response-success",
			program_options::value<string>(),
			"The response value for successfully upload. "
				"Default is [upload=true;]."
		)
		(
			"curl-path",
			program_options::value<string>(),
			"The path for curl. "
				"Default is [curl]."
		)
		(
			"curl-opt",
			program_options::value<string>(),
			"Optional options for curl. "
				"Default is [-s]."
		)
		(
			"curl-timeout",
			program_options::value<string>(),
			"The timeout option for curl. "
				"Default is [10]."
		)
		(
			"max-threads,t",
			program_options::value<unsigned>(),
			"The maximum threads for parallel uploading. "
				"Default is [10]."
		)
		(
			"debug,d",
			program_options::value<int>(),
			"The debug level for information output. "
				"Default is [0]."
		)
		(
			"files-ignore,i",
			program_options::value<unsigned>(),
			"The number of ignored latest files from filesystem. "
				"Should be allways higher zero. Default is [1]."
		)
		(
			"sleep-time,s",
			program_options::value<unsigned>(),
			"Mikro seconds of main loop. "
				"Calculate like (0.5s)*1000000. Default is 0.5s (2Hz) [500000]."
		)
		(
			"username,u",
			program_options::value<string>(),
			"The username for authentication."
		)
		(
			"password,p",
			program_options::value<string>(),
			"The password for authentication."
		)
		(
			"cameraname,c",
			program_options::value<string>(),
			"The cameraname for uploading."
		)
		(
			"host,h",
			program_options::value<string>(),
			"The host for uploading."
		)
		(
			"path",
			program_options::value<string>(),
			"The path where images stored."
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

	if (vm.count("request-cameraimage")) {
		requestCameraimage = vm["request-cameraimage"].as<string>();
		cout << "option request-cameraimage overwritten as ["
		<< requestCameraimage << "]"
		<< endl;
	}

	if (vm.count("request-username")) {
		requestUsername = vm["request-username"].as<string>();
		cout << "option request-username overwritten as ["
		<< requestUsername << "]"
		<< endl;
	}

	if (vm.count("request-password")) {
		requestPassword = vm["request-password"].as<string>();
		cout << "option request-password overwritten as ["
		<< requestPassword << "]"
		<< endl;
	}

	if (vm.count("request-cameraname")) {
		requestCameraname = vm["request-cameraname"].as<string>();
		cout << "option request-cameraname overwritten as ["
		<< requestCameraname << "]"
		<< endl;
	}

	if (vm.count("response-success")) {
		responseSuccess = vm["response-success"].as<string>();
		cout << "option response-success overwritten as ["
		<< responseSuccess << "]"
		<< endl;
	}

	if (vm.count("curl-path")) {
		curlPath = vm["curl-path"].as<string>();
		cout << "option curl-path overwritten as ["
		<< curlPath << "]"
		<< endl;
	}

	if (vm.count("curl-opt")) {
		curlOpt = vm["curl-opt"].as<string>();
		cout << "option curl-opt overwritten as ["
		<< curlOpt << "]"
		<< endl;
	}

	if (vm.count("curl-timeout")) {
		curlTimeout = vm["curl-timeout"].as<unsigned>();
		cout << "option curl-timeout overwritten as ["
		<< curlTimeout << "]"
		<< endl;
	}

	if (vm.count("max-threads")) {
		maxThreads = vm["max-threads"].as<unsigned>();
		cout << "option max-threads overwritten as ["
		<< maxThreads << "]"
		<< endl;
	}

	if (vm.count("max-files")) {
		maxFiles = vm["max-files"].as<unsigned>();
		cout << "option max-files overwritten as ["
		<< maxFiles << "]"
		<< endl;
	}

	if (vm.count("debug")) {
		debug = vm["debug"].as<int>();
		cout << "option debug overwritten as ["
		<< debug << "]"
		<< endl;
	}

	if (vm.count("files-ignore")) {
		filesIgnore = vm["files-ignore"].as<unsigned>();
		cout << "option files-ignore overwritten as ["
		<< filesIgnore << "]"
		<< endl;
	}

	if (vm.count("sleep-time")) {
		sleepTime = vm["sleep-time"].as<unsigned>();
		cout << "option sleep-time overwritten as ["
		<< sleepTime << "]"
		<< endl;
	}


	if (vm.count("username")) {
		username = vm["username"].as<string>();
		cout << "option username set as ["
		<< username << "]"
		<< endl;
	} else {
		cout << "Option username not given. "<<endl;
		invalidOption = true;
	}

	if (vm.count("password")) {
		password = vm["password"].as<string>();
		cout << "option password set as ["
		<< password << "]"
		<< endl;
	} else {
		cout << "Option password not given. "<<endl;
		invalidOption = true;
	}

	if (vm.count("cameraname")) {
		cameraname = vm["cameraname"].as<string>();
		cout << "option cameraname set as ["
		<< cameraname << "]"
		<< endl;
	} else {
		cout << "Option cameranmae not given. "<<endl;
		invalidOption = true;
	}

	if (vm.count("host")) {
		host = vm["host"].as<string>();
		cout << "option host set as ["
		<< host << "]"
		<< endl;
	} else {
		cout << "Option host not given. "<<endl;
		invalidOption = true;
	}

	if (vm.count("path")) {
		path = vm["path"].as<string>();
		cout << "option path set as ["
		<< path << "]"
		<< endl;
	} else {
		cout << "Option path not given. "<<endl;
		invalidOption = true;
	}

	if(invalidOption) {
		cout << "Invalid option given. "
			"Please give '--help' to get help." << endl;
		return EXIT_FAILURE;
	}


	cout << endl << "...running" << endl << flush;
	/* ----- list of available files in given path ----- */
	vector<string> availableFileVector;

	/* ----- list of untreated files ----- */
	vector<string> untreatedFileVector;



	/* ----- thread_group of current running workers threads ----- */
	//static boost::mutex mutexWorkerThreadsThreadGroup;
	boost::thread_group workerThreadsThreadGroup;

	/* ----- list of workers ----- */
	//static boost::mutex mutexWorkerThreads;
	vector<boost::thread *> workerThreadsVector;

	run = true;

	signal(SIGINT, signalCallbackHandler);

	//never give one extension twice!
	fileExtensionVector.push_back(string(".jpg"));
	fileExtensionVector.push_back(string(".jpeg"));

	while(run) {
		usleep(sleepTime);
		removeJoinableThreads(&workerThreadsVector);

		//this should be euqual or less
		//a litle performance boost
		//only if worker threads free, check the file system
		if(workerThreadsVector.size() == maxThreads) {
			continue;
		}

		getAvailableFileVector(&availableFileVector,fileExtensionVector,path,maxFiles);


		if(debug) {
			cout
			<<"main loop: "
			<<"mutexCurrentUploadingFileVector.lock();"
			<<endl
			<<flush;
		}

		mutexCurrentUploadingFileVector.lock();

		if(debug) {
			cout
			<<"main loop: "
			<<"sort the availableFileVector"
			<<endl
			<<flush;
		}

		sort(availableFileVector.begin(), availableFileVector.end());


		if(debug) {
			cout
			<<"main loop: "
			<<"filesIgnore = "
			<<filesIgnore
			<<endl
			<<flush;
		}

		for(int i=0; i<filesIgnore; ++i) {
			if(availableFileVector.size()) {
				availableFileVector.pop_back();
			}
		}

		if(debug) {
			cout
			<<"main loop: "
			<<"sort the currentUploadingFileVector"
			<<endl
			<<flush;
		}

		sort(currentUploadingFileVector.begin(), currentUploadingFileVector.end());

		if(debug) {
			cout
			<<"main loop: "
			<<"output the availableFileVector"
			<<endl
			<<flush;
			for(unsigned i=0; i<availableFileVector.size(); ++i) {
				cout<<"["<<i<<"]: "<<availableFileVector.at(i)<<endl<<flush;
			}
		}

		if(debug) {
			cout
			<<"main loop: "
			<<"output the currentUploadingFileVector"
			<<endl
			<<flush;
			for(unsigned i=0; i<currentUploadingFileVector.size(); ++i) {
				cout<<"["<<i<<"]: "<<currentUploadingFileVector.at(i)<<endl<<flush;
			}
		}

		untreatedFileVector.clear();

		set_difference(
			availableFileVector.begin(),
			availableFileVector.end(),
			currentUploadingFileVector.begin(),
			currentUploadingFileVector.end(),
			back_inserter(untreatedFileVector)
		);

		if(debug) {
			cout
			<<"main loop: "
			<<"output the untreatedFileVector"
			<<endl
			<<flush;
			for(unsigned i=0; i<untreatedFileVector.size(); ++i) {
				cout<<"["<<i<<"]: "<<untreatedFileVector.at(i)<<endl<<flush;
			}
		}

		//the currentUploadingFileVector must locked before
		createWorkerThreadsFromUntreated(
			&workerThreadsThreadGroup,
			&workerThreadsVector,
			&untreatedFileVector,
			maxThreads
		);

		if(debug) {
			cout
			<<"main loop: "
			<<"mutexCurrentUploadingFileVector.unlock();"
			<<endl
			<<flush;
		}

		mutexCurrentUploadingFileVector.unlock();
	}

	cout<<endl<<"run terminated, join_all, please wait a moment..."<<endl<<flush;
	workerThreadsThreadGroup.join_all();

	cout << "return EXIT_SUCCESS" << endl << endl << flush;
	return EXIT_SUCCESS;
}

