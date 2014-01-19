#!/usr/bin/env python
# snowman-py-record - Python script to record images and load them
# afterwards on a snowman-php-server via snowman-cpp-upload.
# http://code.google.com/p/snowman/
#
# Copyright 2013 Bernard Ladenthin <bernard.ladenthin@gmail.com>
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
# http://www.apache.org/licenses/LICENSE-2.0
#

#python -m compileall .
import datetime
import logging
import os
import time
import subprocess
from subprocess import call
import time
import threading
import sys
import signal
import thread

recordthread=False
waitNextRecord=False
timer=False
logger=False

waitAfterHang=False

recordTime="180"
username="admin"
password="admin"
cameraname="camera0"
host="http://localhost/snowman-php-server/cameraupload.php"
#never put a "/" on the path end
path="/var/snowman/"+cameraname+"/pics"
logFile="/var/snowman/"+cameraname+"/log.txt"
logLevel=logging.INFO

cameradevice="/dev/video0"

#Logitech C525 and zotac ID13/ID17
fps="8"
resolution="640x360"

#Logitech C525 optimal
#fps="5"
#resolution="1280x720"

#Logitech C525 average
#fps="2"
#resolution="640x360"

#Logitech C525 minimal 0
#fps="1"
#resolution="640x360"

#Logitech C525 minimal 1
#fps="1"
#resolution="320x180"

#standard webcam format
#fps="1"
#resolution="640x480"

mkdirexec=['mkdir', '-p', path]

def getScreenCreateExecByName(screenname, execArray):
    arguments=["screen", "-S", screenname, "-p", "0", "-X", "exec"]
    arguments.extend(execArray)
    return arguments

def getScreenKillExecByName(screenname):
    return ["screen", "-S", screenname, "-X", "quit"]

snowmanCppUploadArg=["snowman-cpp-upload", "-u", username, "-p", password, "-c",
    cameraname, "-h", host, "--path", path, "-t", "12", "-d", "0"]

screenname="screen-snowman-cpp-upload"
screeenexec=getScreenCreateExecByName(screenname, snowmanCppUploadArg)

killscreen=getScreenKillExecByName(screenname)

ntpdateExec=["sntp", "pool.ntp.org"]

def createAndDeleteFileForWatchdog(path):
    fooFilePath=path+os.sep+'foo';
    open(fooFilePath, 'w').close();
    os.remove(fooFilePath);

def createWatchdog(pathToMonitor):
    watchdogScreenname="\"screen-snowman-cpp-recordwatchdog_"+cameraname+"\""
    watchdogArg=["snowman-cpp-recordwatchdog", "--pathToMonitor" , pathToMonitor]
    watchdogScreeenexec=getScreenCreateExecByName(watchdogScreenname, watchdogArg)
    watchdogScreeenexec.extend("&");
    parameterString=""
    for parameter in watchdogScreeenexec:
        parameterString += parameter + " "
    os.system(parameterString);

def killProgram():
    global recordthread
    global killscreen
    logger.debug('killProgram')
    recordthread.terminate()
    logger.debug('sleep 2')
    time.sleep(2)
    recordthread.kill()
    logger.debug('killscreen')
    call(killscreen)
    logger.debug('os._exit')
    os._exit(0)

def cbKillProcess(param=[]):
    global waitAfterHang
    global recordthread
    global logger
    logger.warn('record hang, kill process')
    recordthread.kill();
    waitAfterHang=True

def clearOutOfTimeCallback():
    global timer
    if(timer != False):
        timer.cancel()
        timer=False

def registerOutOfTimeCallback(time):
    global timer
    timer = threading.Timer(time, cbKillProcess)
    timer.start()

def record(fps, cameraname, path, recordTime, resolution, cameradevice):
    global recordthread
    currenttime=str(int(time.time()))
    #never change this format, it is server dependent
    filename=path+"/"+cameraname+"_"+currenttime+"_"+fps+"_00000000.jpeg"

    streamerarg=["-c", cameradevice, "-t", recordTime, "-r", fps, "-s",
        resolution, "-o", filename]

    fswebcamarg=["--no-banner", "--no-subtitle", "-s", "-D", "0","-S","10",
        "-r",resolution,"-d",cameradevice,"-q","--jpeg","-1",filename]

    streamercamcall=["streamer"]
    streamercamcall.extend(streamerarg)
    fswebcamcall=["fswebcam"]
    fswebcamcall.extend(fswebcamarg)
    recordthread = subprocess.Popen(streamercamcall)
    #recordthread = subprocess.Popen(fswebcamcall)
    recordthread.communicate()
    return

try:
    call(mkdirexec)
    createAndDeleteFileForWatchdog(path)
    createWatchdog(path)
    logger = logging.getLogger('myapp')
    hdlr = logging.FileHandler(logFile)
    formatter = logging.Formatter('%(asctime)s %(levelname)s %(message)s')
    hdlr.setFormatter(formatter)
    logger.addHandler(hdlr)
    logger.setLevel(logLevel)
    logger.info('main loop started')
    call(["screen", "-dmS", screenname])
    #only as root
    #call(ntpdateExec)
    logger.debug('wait screen creation')
    time.sleep(2)
    call(screeenexec)
    while True:
        if(waitAfterHang):
            try:
                logger.debug('now sleep ..')
                time.sleep(20)
                waitAfterHang=False
                logger.debug('... sleep over')
            except KeyboardInterrupt:
                logger.debug('KeyboardInterrupt')
                killProgram()
        else:
            clearOutOfTimeCallback();
            registerOutOfTimeCallback(int(recordTime)+30);
            record(fps, cameraname, path, recordTime, resolution, cameradevice)

    time.sleep(2)
except KeyboardInterrupt:
    logger.debug('KeyboardInterrupt')
    print "KeyboardInterrupt"
    killProgram()


