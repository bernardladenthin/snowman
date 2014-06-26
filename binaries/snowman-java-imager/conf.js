{
  "snowmanServer" : {
    "apiUrl" : "http://localhost/snowman-php-server/cameraupload.php",
    "cameraName" : "camera0",
    "username" : "admin",
    "password" : "admin"
  },
  "streamer" : {
    "device": "/dev/video0",
    "framesPerSecond" : "8",
    "path" : "pictures",
    "recordTime" : 180,
    "resolutionX" : "640",
    "resolutionY" : "360"
  },
  "upload" : {
    "ignoreLastFiles" : 1,
    "threads" : 10
  },
  "watchdog" : {
    "blockingProcesses" : ["snowman-py-avconv", "snowman-py-ffmpeg"],
    "interval" : 10000, "_comment" : "10000 are 10 seconds",
    "restartCommand" : "init 6",
    "timeWindow" : 250000, "_comment" : "250000 are 250 seconds"
  }
}

