#!/usr/bin/env python
# snowman-py-ffmpeg - Python script to archive images using ffmpeg.
# http://code.google.com/p/snowman/
#
# Copyright 2013 Bernard Ladenthin <bernard@ladenthin.net>
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
# http://www.apache.org/licenses/LICENSE-2.0
#

#python -m compileall .
import os
from optparse import OptionParser
import shutil
#from shutil import rmtree
import subprocess
from subprocess import call

def removeDir(path):
    if os.path.isdir(path):
        shutil.rmtree(path)

parser = OptionParser("snowman-py-ffmpeg.py -c -d -s -t")

parser.add_option(
    "-c",
    "--camera-name",
    dest="cameraName",
    help="camera name"
)
parser.add_option(
    "-d",
    "--date",
    dest="date",
    help="prefered date"
)
parser.add_option(
    "-s",
    "--src-path",
    dest="srcPath",
    help="source path"
)
parser.add_option(
    "-t",
    "--target-path",
    dest="targetPath",
    help="target path"
)

(options, args) = parser.parse_args()

if not options.cameraName:
    parser.error("cameraName required.")

if not options.date:
    parser.error("date required.")

if not options.srcPath:
    parser.error("srcPath required.")

if not options.targetPath:
    parser.error("targetPath required.")

srcFile=options.srcPath+"/"+"%d.jpeg"
targetFile=options.targetPath+"/"+options.date+".mp4"

ffmpegexec=[
    "ffmpeg",
    "-y", #overwrite if file exist
    "-i",
    srcFile,
    "-c:v",
    "libx264",
    "-preset",
    "veryfast",
    #"slow",
    targetFile
]

call(ffmpegexec)
removeDir(options.srcPath)

