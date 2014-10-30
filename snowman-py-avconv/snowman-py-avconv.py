#!/usr/bin/env python
# snowman-py-avconv - Python script to archive images using avconv.
# https://github.com/bernardladenthin/snowman
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
import os
from optparse import OptionParser
import shutil
#from shutil import rmtree
import subprocess
from subprocess import call

def removeDir(path):
    if os.path.isdir(path):
        shutil.rmtree(path)

parser = OptionParser("snowman-py-avconv.py -c -d -s -t")

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
targetFileHighRes=options.targetPath+"/"+options.date+"_HD"+".mp4"
targetFileLowRes=options.targetPath+"/"+options.date+".mp4"

# http://dev.beandog.org/x264_preset_reference.html
# http://mewiki.project357.com/wiki/X264_Settings

#avconv [input options] -i [input filename] -codec:v [video options] -codec:a [audio options] [output file options] [output filename]
avconvexecHighRes=[
    "avconv",
#overwrite if file exist
    "-y",
#input file name
    "-i",
    srcFile,
#encode video to H.264 using libx264 library
    "-codec:v",
    "libx264",
#crf Constant Rate Factor http://slhck.info/articles/crf
    "-crf",
    "26",
#sets encoding preset for x264
#ultrafast, superfast, veryfast, faster, fast, medium, slow, slower, veryslow
    "-preset",
    "slow",
#sets video bitrate in bits/s (not used, we use crf)
#    "-b:v",
#    "500k",
#maxrate and -bufsize forces libx264 to build video in a way,
#that it could be streamed over 500kbit/s line considering device buffer of 1000kbits.
#educes quality, only use this if you're encoding for a playback scenario that requires it.
#    "-maxrate",
#    "500k",
#    "-bufsize",
#    "1000k",
#scale filter
#-1 means: resize so the aspect ratio is same.
#    "-vf",
#    "scale=-1:360",
#use all threads
    "-threads",
    "0",
#disable audio
    "-an",
    targetFileHighRes
]

avconvexecLowRes=[
    "avconv",
    "-y",
    "-i",
    srcFile,
    "-codec:v",
    "libx264",
    "-crf",
    "35",
    "-preset",
    "medium",
    "-threads",
    "0",
    "-an",
    targetFileLowRes
]

#first create the low resolution video
call(avconvexecLowRes)
call(avconvexecHighRes)

removeDir(options.srcPath)

