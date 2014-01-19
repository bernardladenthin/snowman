/**
 * snowman-html5-app - HTML5 application to connect to the snowman-php-server.
 * http://code.google.com/p/snowman/
 *
 * Copyright (C) 2013 Bernard Ladenthin <bernard.ladenthin@gmail.com>
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

var htmlDef = {
	"page": {
		"stdTransition": "slide",
		"login" : {
			"id" : "pageLogin",
			"content" : {
				"loginUsername" : {
					"id" : "loginUsername"
				},
				"loginPassword" : {
					"id" : "loginPassword"
				},
				"warningInputUsernameAndPassword" : {
					"id" : "warningInputUsernameAndPassword"
				},
				"warningWrongUsernameOrPassword" : {
					"id" : "warningWrongUsernameOrPassword"
				},
				"connectionToServer" : {
					"id" : "connectionToServer"
				},
				"loginForm" : {
					"id" : "loginForm",
					"content" : {
						"loginButton" : {
							"id" : "loginButton"
						}
					} 
				}
			}
		},
		"cameraview" : {
			"id" : "pageCameraview",
			"content" : {
				"pageCameraviewHead" : {
					"id" : "pageCameraviewHead"
				},
				"canvasLiveview" : {
					"id" : "canvasLiveview",
				},
				"camerapicture" : {
					"id" : "camerapicture"
				}
			}
		},
		"cameras" : {
			"id" : "pageCameras",
			"content" : {
				"archiveListingCollapsible" : {
					"id" : "archiveListingCollapsible"
				},
				"archiveListing" : {
					"id" : "archiveListing"
				},
				"archiveListingTree" : {
					"id" : "archiveListingTree"
				},
				"showSuccessCommandArchive" : {
					"id" : "showSuccessCommandArchive"
				},
				"showSuccessCommandArchive" : {
					"id" : "showSuccessCommandArchive"
				},
				"showSuccessCommandRefreshChmod" : {
					"id" : "showSuccessCommandRefreshChmod"
				},
				"listviewCameras" : {
					"id" : "listviewCameras"
				},
				"yourUsername" : {
					"id" : "yourUsername"
				},
				"yourUsergroups" : {
					"id" : "yourUsergroups"
				},
				"liveviewplaybtn" : {
					"id" : "liveviewplaybtn"
				},
				"liveviewpausebtn" : {
					"id" : "liveviewpausebtn"
				}
			}
		},
		"settings" : {
			"id" : "pageSettings"
		},
		"settingsPicturesizeandquality" : {
			"id" : "pageSettingsPicturesizeandquality"
		},
		"settingsServerinformation" : {
			"id" : "pageSettingsServerinformation",
			"content" : {
				"ServerinformationServerAjaxApiUrl" : {
					"id" : "ServerinformationServerAjaxApiUrl"
				},
				"ServerinformationServerVersion" : {
					"id" : "ServerinformationServerVersion"
				},
				"ServerinformationServerVersionDate" : {
					"id" : "ServerinformationServerVersionDate"
				},
				"ServerinformationServerOwner" : {
					"id" : "ServerinformationServerOwner"
				},
				"ServerinformationServerImprintUrl" : {
					"id" : "ServerinformationServerImprintUrl"
				},
				"ServerinformationServerLiveviewUrl" : {
					"id" : "ServerinformationServerLiveviewUrl"
				},
				"ServerinformationServerDownloadarchiveUrl" : {
					"id" : "ServerinformationServerDownloadarchiveUrl"
				}
			}
		},
		"settingsAutodeactivate" : {
			"id" : "pageSettingsAutodeactivate"
		},
		"settingsPicturerefresh" : {
			"id" : "pageSettingsPicturerefresh"
		}
	},
	"navbar": {
		"loginFalseWithActiveLogin" : {
			"cl" : "navbarLFLogin"
		},
		"loginFalseWithActiveSettings" : {
			"cl" : "navbarLFSettings"
		},
		"loginTrueWithActiveCameras" : {
			"cl" : "navbarLTCameras"
		},
		"loginTrueWithActiveSettings" : {
			"cl" : "navbarLTSettings"
		}
	},
	"img": {
		"miniajaxloader" : {
			"cl" : "miniajaxloader"
		}
	}
}

var pagesIds = [
	htmlDef.page.login.id,
	htmlDef.page.settings.id,
	htmlDef.page.settingsAutodeactivate.id,
	htmlDef.page.settingsPicturerefresh.id,
	htmlDef.page.settingsPicturesizeandquality.id,
	htmlDef.page.settingsServerinformation.id,
	htmlDef.page.cameras.id,
	htmlDef.page.cameraview.id
];

var navbarsCls = [
	htmlDef.navbar.loginFalseWithActiveLogin.cl,
	htmlDef.navbar.loginFalseWithActiveSettings.cl,
	htmlDef.navbar.loginTrueWithActiveCameras.cl,
	htmlDef.navbar.loginTrueWithActiveSettings.cl
];

