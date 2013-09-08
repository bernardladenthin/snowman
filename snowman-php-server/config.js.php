<?php exit;/*DO NOT REMOVE THIS LINE IT MUST STAY TO PROTECT ILLEGAL ACCESS*/?>

{
	"snowman": {
		"version": "3.0.0",
		"versiondate": "2013-03-24",

		"owner": "Bernard Ladenthin",
		"imprinturl": "http://localhost/imprint.html",
		"ajaxapiurl": "http://localhost/snowman-php-server/ajaxapi.php",
		"liveviewurl": "http://localhost/snowman-php-server/liveview.php",

		"archiveOnlyFromSecureHost" : false,
		"archiveOnlyAccessableCameras" : false,
		"securehosts": ["localhost"]
	},
	"users" : [
		{
			"name": "admin",
			"password": "admin",
			"passwordHashAlgorithm" : false,
			"loginDisabled" : false,

			"groups": ["admin"]
		},
		{
			"name": "admin2",
			"password": "admin2",
			"passwordHashAlgorithm" : false,
			"loginDisabled" : false,

			"groups": ["admin"]
		},
		{
			"name": "guest",
			"password": "guest",
			"passwordHashAlgorithm" : false,
			"loginDisabled" : false,

			"groups": ["guest"]
		}
	],
	"cameras" : [
		{
			"name": "camera0",
			"toptextbranding" : "code.google.com/p/snowman/ | ",
			"bottomtextbranding": "This is camera zero.",
			"dir": "protected/camera0_pictures",

			"refresh": "700",
			"delay": "15",

			"width": "640",
			"height": "360",

			"userdeny": [],
			"userallow": [],
			"groupdeny": ["guest"],
			"groupallow": ["admin", "user"],

			"tmpPathPrepend": "protected/",
			"archiveDir": "protected/camera0_archive",
			"archiveDirDate": "Y/m/d/H",

			"archiveMaxFiles": 5000,
			"archivePackageFormatZip" : false,
			"archivePackageFormatZipName" : "_Ymd_H_i_s",
			"archivePackageFormatCustomExec" :
"snowman-py-avconv.py -c \"%s\" -d \"%s\" -s \"%s\" -t \"%s\" > /dev/null 2>/dev/null &",
			"archivePackageFormatCustomExecImageFormat" : "%d.jpeg",
			"archivePackageFormatCustomExecDateParameter" : "Ymd_H_i_s",
			"archiveImageUnlink" : true,
			"archiveLogFile" : "protected/log.txt",

			"archiveExtensions": ["mp4"],
			"archiveExtensionsCaseSensitive": false,

			"imageExtensions": ["jpg", "jpeg"],
			"imageExtensionsCaseSensitive": false,

			"camerawriterFormatPosixMillis" : true,
			"maximumFilesystemFileArray" : 9999,
			"logRawCameraUpload" : false
		},
		{
			"name": "camera1",
			"toptextbranding" : "code.google.com/p/snowman/ | ",
			"bottomtextbranding": "This is camera one.",
			"dir": "protected/camera1_pictures",

			"refresh": "700",
			"delay": "3",

			"width": "640",
			"height": "360",

			"userdeny": [],
			"userallow": [],
			"groupdeny": ["guest"],
			"groupallow": ["admin", "user"],

			"tmpPathPrepend": "protected/",
			"archiveDir": "protected/camera1_archive",
			"archiveDirDate": "Y/m/d/H",

			"archiveMaxFiles": 5000,
			"archivePackageFormatZip" : false,
			"archivePackageFormatZipName" : "_Ymd_H_i_s",
			"archivePackageFormatCustomExec" :
"snowman-py-avconv.py -c \"%s\" -d \"%s\" -s \"%s\" -t \"%s\" > /dev/null 2>/dev/null &",
			"archivePackageFormatCustomExecImageFormat" : "%d.jpeg",
			"archivePackageFormatCustomExecDateParameter" : "Ymd_H_i_s",
			"archiveImageUnlink" : true,
			"archiveLogFile" : "protected/log.txt",

			"archiveExtensions": ["mp4"],
			"archiveExtensionsCaseSensitive": false,

			"imageExtensions": ["jpg", "jpeg"],
			"imageExtensionsCaseSensitive": false,

			"camerawriterFormatPosixMillis" : true,
			"maximumFilesystemFileArray" : 9999,
			"logRawCameraUpload" : false
		}
	]
}

