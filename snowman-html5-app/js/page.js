/**
 * snowman-html5-app - HTML5 application to connect to the snowman-php-server.
 * https://github.com/bernardladenthin/snowman
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

function getListElementsFromArray(elements) {
	o = [
	];
	for (var i = 0; i < elements.length; i++ ) {
		o.push(
			{
				"content" : elements[i],
				"type" : "li"
			}
		);
	}
	return o;
}

function getAjaxLoaderImg() {
	o =
	{
		"attrs": {
			"alt": "ajaxloader",
			"src": "./img/ajaxloader.gif",
		},
		"type": "img",
		"css" : htmlDef.img.miniajaxloader.cl
	}
	return o;
}

function createListingTreeRoot() {
	o =
	{
		"id": htmlDef.page.cameras.content.archiveListingTree.id,
		"type": "div"
	}
	return o;
}

function getNavbar() {
	o =
	{
		"attrs": {
			"data-id": "datafooter",
			"data-position": "fixed",
			"data-role": "footer",
			"data-tap-toggle": "false"
		},
		"content": [
			getNavbarLFLogin(),
			getNavbarLFSettings(),
			getNavbarLTCameras(),
			getNavbarLTSettings()
		],
		"css": "ui-state-persist"
	}
	return o;
}

function getNavbarLFLogin() {
	o =
	{
		"attrs": {
			"data-role": "navbar"
		},
		"content": {
			"content": [
				{
					"content": {
						"attrs": {
							"data-icon": "check",
							"data-iconpos": "top",
							"href": ""
						},
						"content": {
							"text": "Login"
						},
						"css": "ui-btn-active ui-state-persist",
						"type": "a"
					},
					"type": "li"
				},
				{
					"content": {
						"attrs": {
							"data-icon": "home",
							"data-iconpos": "top",
							"data-transition": "slide",
							"href": toIdSel(htmlDef.page.cameras.id)
						},
						"content": {
							"text": "Cameras"
						},
						"css": "ui-disabled",
						"type": "a"
					},
					"type": "li"
				},
				{
					"content": {
						"attrs": {
							"data-icon": "gear",
							"data-iconpos": "top",
							"data-transition": "slide",
							"href": toIdSel(htmlDef.page.settings.id)
						},
						"content": {
							"text": "Settings"
						},
						"type": "a"
					},
					"type": "li"
				}
			],
			"type": "ul"
		},
		"css": "navbarLFLogin"
	}
	return o;
}

function getNavbarLFSettings() {
	o =
	{
		"attrs": {
			"data-role": "navbar"
		},
		"content": {
			"content": [
				{
					"content": {
						"attrs": {
							"data-direction": "reverse",
							"data-icon": "check",
							"data-iconpos": "top",
							"data-transition": "slide",
							"href": toIdSel(htmlDef.page.login.id)
						},
						"content": {
							"text": "Login"
						},
						"type": "a"
					},
					"type": "li"
				},
				{
					"content": {
						"attrs": {
							"data-icon": "home",
							"data-iconpos": "top",
							"href": toIdSel(htmlDef.page.cameras.id)
						},
						"content": {
							"text": "Cameras"
						},
						"css": "ui-disabled",
						"type": "a"
					},
					"type": "li"
				},
				{
					"content": {
						"attrs": {
							"data-icon": "gear",
							"data-iconpos": "top",
							"href": ""
						},
						"content": {
							"text": "Settings"
						},
						"css": "ui-btn-active ui-state-persist",
						"type": "a"
					},
					"type": "li"
				}
			],
			"type": "ul"
		},
		"css": "navbarLFSettings"
	}
	return o;
}

function getNavbarLTCameras() {
	o =
	{
		"attrs": {
			"data-role": "navbar"
		},
		"content": {
			"content": [
				{
					"content": {
						"attrs": {
							"data-direction": "reverse",
							"data-icon": "delete",
							"data-iconpos": "top",
							"data-transition": "slide",
							"href": toIdSel(htmlDef.page.login.id)
						},
						"content": {
							"text": "Logout"
						},
						"type": "a"
					},
					"type": "li"
				},
				{
					"content": {
						"attrs": {
							"data-icon": "home",
							"data-iconpos": "top",
							"href": ""
						},
						"content": {
							"text": "Cameras"
						},
						"css": "ui-btn-active ui-state-persist",
						"type": "a"
					},
					"type": "li"
				},
				{
					"content": {
						"attrs": {
							"data-icon": "gear",
							"data-iconpos": "top",
							"data-transition": "slide",
							"href": toIdSel(htmlDef.page.settings.id)
						},
						"content": {
							"text": "Settings"
						},
						"type": "a"
					},
					"type": "li"
				}
			],
			"type": "ul"
		},
		"css": "navbarLTCameras"
	}
	return o;
}

function getNavbarLTSettings() {
	o =
	{
		"attrs": {
			"data-role": "navbar"
		},
		"content": {
			"content": [
				{
					"content": {
						"attrs": {
							"data-direction": "reverse",
							"data-icon": "delete",
							"data-iconpos": "top",
							"data-transition": "slide",
							"href": toIdSel(htmlDef.page.login.id)
						},
						"content": {
							"text": "Logout"
						},
						"type": "a"
					},
					"type": "li"
				},
				{
					"content": {
						"attrs": {
							"data-icon": "home",
							"data-iconpos": "top",
							"data-transition": "slide",
							"href": toIdSel(htmlDef.page.cameras.id)
						},
						"content": {
							"text": "Cameras"
						},
						"type": "a"
					},
					"type": "li"
				},
				{
					"content": {
						"attrs": {
							"data-icon": "gear",
							"data-iconpos": "top",
							"data-transition": "slide",
							"href": ""
						},
						"content": {
							"text": "Settings"
						},
						"css": "ui-btn-active ui-state-persist",
						"type": "a"
					},
					"type": "li"
				}
			],
			"type": "ul"
		},
		"css": "navbarLTSettings"
	}
	return o;
}

function getPageCameraview() {
	o =
	{
		"attrs": {
			"data-role": "page"
		},
		"content": [
			{
				"attrs": {
					"data-role": "header"
				},
				"content": [
					{
						"id": htmlDef.page.cameraview.content.pageCameraviewHead.id,
						"type": "h1"
					},
					{
						"attrs": {
							"data-icon": "gear",
							"href": "#",
							"onclick": "liveviewplaybtn();"
						},
						"content": {
							"text": "Play"
						},
						"css": "ui-btn-right",
						"id": htmlDef.page.cameras.content.liveviewplaybtn.id,
						"type": "a"
					},
					{
						"attrs": {
							"data-icon": "gear",
							"href": "#",
							"onclick": "liveviewpausebtn();"
						},
						"content": {
							"text": "Pause"
						},
						"css": "ui-btn-right",
						"id": htmlDef.page.cameras.content.liveviewpausebtn.id,
						"type": "a"
					}
				]
			},
			{
				"attrs": {
					"data-role": "content"
				},
				"content": {
					"content": [
						{
							"id": "canvasLiveview",
							"type": "canvas"
						}
					],
					"id": htmlDef.page.cameraview.content.camerapicture.id
				}
			},
			getNavbar()
		],
		"id": "pageCameraview"
	}
	return o;
}

function getPageCameras(){
	o =
	{
		"attrs": {
			"data-role": "page"
		},
		"content": [
			{
				"attrs": {
					"data-role": "header"
				},
				"content": {
					"content": {
						"text": "Cameras"
					},
					"type": "h1"
				}
			},
			{
				"attrs": {
					"data-role": "content"
				},
				"content": [
					{
						"attrs": {
							"data-role": "listview",
							"data-theme": "b"
						},
						"id": htmlDef.page.cameras.content.listviewCameras.id,
						"type": "ul"
					},
					{
						"type": "br"
					},
					{
						"type": "br"
					},
					{
						"id" : htmlDef.page.cameras.content.archiveListingCollapsible.id,
						"attrs": {
							"data-content-theme": "a",
							"data-role": "collapsible",
							"data-theme": "a"
						},
						"content": [
							{
								"content": {
									"text": "Archive listing"
								},
								"type": "h3"
							},
							{
								"attrs": {
									"data-role": "fieldcontain"
								},
								"content": [
									{
										"id" : htmlDef.page.cameras.content.archiveListing.id,
										"type": "div"
									}
								]
							}
						]
					},
					{
						"id" : htmlDef.page.cameras.content.loginInformationCollapsible.id,
						"attrs": {
							"data-content-theme": "a",
							"data-role": "collapsible",
							"data-theme": "a"
						},
						"content": [
							{
								"content": {
									"text": "Login information"
								},
								"type": "h3"
							},
							{
								"attrs": {
									"data-role": "fieldcontain"
								},
								"content": [
									{
										"attrs": {
											"for": htmlDef.page.cameras.content.yourUsername.id
										},
										"content": {
											"text": "Your username:"
										},
										"type": "label"
									},
									{
										"attrs": {
											"name": htmlDef.page.cameras.content.yourUsername.id,
											"readonly": "readonly",
											"type": "text",
											"value": ""
										},
										"id": htmlDef.page.cameras.content.yourUsername.id,
										"type": "input"
									},
									{
										"attrs": {
											"for": htmlDef.page.cameras.content.yourUsergroups.id
										},
										"content": {
											"text": "your usergroups"
										},
										"type": "label"
									},
									{
										"attrs": {
											"name": htmlDef.page.cameras.content.yourUsergroups.id,
											"readonly": "readonly",
											"type": "text",
											"value": ""
										},
										"id": htmlDef.page.cameras.content.yourUsergroups.id,
										"type": "input"
									}
								]
							}
						]
					},
					{
						"id" : htmlDef.page.cameras.content.specialCommandsCollapsible.id,
						"attrs": {
							"data-content-theme": "a",
							"data-role": "collapsible",
							"data-theme": "a"
						},
						"content": [
							{
								"content": {
									"text": "Special commands"
								},
								"type": "h3"
							},
							{
								"content": {
									"attrs": {
										"data-rel": "dialog",
										"data-role": "button",
										"data-transition": "pop",
										"href": "#",
										"onclick": "actionCommandRefreshChmod();"
									},
									"content": {
										"text": "refresh chmod"
									},
									"type": "a"
								},
								"type": "p"
							},
							{
								"content": {
									"attrs": {
										"data-rel": "page",
										"data-role": "button",
										"data-transition": "slide",
										"href": "#",
										"onclick": "actionCommandArchive();"
									},
									"content": {
										"text": "archive images"
									},
									"type": "a"
								},
								"type": "p"
							},
							{
								"content": {
									"text": "command chmod executed."
								},
								"css": "ui-body ui-body-e",
								"id": htmlDef.page.cameras.content.showSuccessCommandRefreshChmod.id
							},
							{
								"content": {
									"text": "command archive  executed."
								},
								"css": "ui-body ui-body-e",
								"id": htmlDef.page.cameras.content.showSuccessCommandArchive.id
							}
						]
					}
				]
			},
			getNavbar()
		],
		"id": "pageCameras"
	}
	return o;
}

function getPageSettingsPicturesizeandquality() {
	o =
	{
		"attrs": {
			"data-role": "page"
		},
		"content": [
			{
				"attrs": {
					"data-role": "header"
				},
				"content": {
					"content": {
						"text": "Picture size and quality"
					},
					"type": "h1"
				}
			},
			{
				"attrs": {
					"data-role": "content"
				},
				"content": {
					"text": "coming up soon.."
				}
			},
			getNavbar()
		],
		"id": "pageSettingsPicturesizeandquality"
	}
	return o;
}

function getLabelForFieldset(text,id) {
	o =
	{
		"attrs": {
			"for": id
		},
		"content": {
			"text": text
		},
		"type": "label"
	}
	return o;
}

function getInputForFieldset(id) {
	o =
	{
		"attrs": {
			"name": id,
			"readonly": "readonly",
			"type": "text",
			"value": ""
		},
		"id": id,
		"type": "input"
	}
	return o;
}

function getPageSettingsServerinformation() {
	o =
	{
		"attrs": {
			"data-role": "page"
		},
		"content": [
			{
				"attrs": {
					"data-role": "header"
				},
				"content": {
					"content": {
						"text": "Server Info"
					},
					"type": "h1"
				}
			},
			{
				"attrs": {
					"data-role": "content"
				},
				"content": {
					"attrs": {
						"data-role": "fieldcontain"
					},
					"content": [
						getLabelForFieldset("Owner:",htmlDef.page.settingsServerinformation.content.ServerinformationServerOwner.id),
						getInputForFieldset(htmlDef.page.settingsServerinformation.content.ServerinformationServerOwner.id),
						{
							"type": "br"
						},
						{
							"type": "br"
						},
						getLabelForFieldset("Imprint-URL:",htmlDef.page.settingsServerinformation.content.ServerinformationServerImprintUrl.id),
						getInputForFieldset(htmlDef.page.settingsServerinformation.content.ServerinformationServerImprintUrl.id),
						{
							"type": "br"
						},
						{
							"type": "br"
						},
						getLabelForFieldset("Server-Version:",htmlDef.page.settingsServerinformation.content.ServerinformationServerVersion.id),
						getInputForFieldset(htmlDef.page.settingsServerinformation.content.ServerinformationServerVersion.id),
						{
							"type": "br"
						},
						{
							"type": "br"
						},
						getLabelForFieldset("Version-Date:",htmlDef.page.settingsServerinformation.content.ServerinformationServerVersionDate.id),
						getInputForFieldset(htmlDef.page.settingsServerinformation.content.ServerinformationServerVersionDate.id),
						{
							"type": "br"
						},
						{
							"type": "br"
						},
						getLabelForFieldset("Ajax-API-URL:",htmlDef.page.settingsServerinformation.content.ServerinformationServerAjaxApiUrl.id),
						getInputForFieldset(htmlDef.page.settingsServerinformation.content.ServerinformationServerAjaxApiUrl.id),
						{
							"type": "br"
						},
						{
							"type": "br"
						},
						getLabelForFieldset("Liveview-URL:",htmlDef.page.settingsServerinformation.content.ServerinformationServerLiveviewUrl.id),
						getInputForFieldset(htmlDef.page.settingsServerinformation.content.ServerinformationServerLiveviewUrl.id),
						{
							"type": "br"
						},
						{
							"type": "br"
						},
						getLabelForFieldset("DownloadArchive-URL:",htmlDef.page.settingsServerinformation.content.ServerinformationServerDownloadarchiveUrl.id),
						getInputForFieldset(htmlDef.page.settingsServerinformation.content.ServerinformationServerDownloadarchiveUrl.id)
					]
				}
			},
			getNavbar()
		],
		"id": "pageSettingsServerinformation"
	}
	return o;
}

function getPageSettingsAutodeactivate() {
	o =
	{
		"attrs": {
			"data-role": "page"
		},
		"content": [
			{
				"attrs": {
					"data-role": "header"
				},
				"content": {
					"content": {
						"text": "Auto deactivate"
					},
					"type": "h1"
				}
			},
			{
				"attrs": {
					"data-role": "content"
				},
				"content": [
					{
						"attrs": {
							"data-role": "fieldcontain"
						},
						"content": [
							{
								"attrs": {
									"data-role": "slider",
									"name": "sliderAutoDeactivate"
								},
								"content": [
									{
										"attrs": {
											"value": "off"
										},
										"content": {
											"text": "Off"
										},
										"type": "option"
									},
									{
										"attrs": {
											"value": "on"
										},
										"content": [
											{
												"text": "On"
											},
											{
												"text": "If you forget to deactivate the live view,it prevents high traffic."
											}
										],
										"type": "option"
									}
								],
								"id": "sliderAutoDeactivate",
								"type": "select"
							},
							{
								"type": "br"
							},
							{
								"type": "br"
							}
						]
					},
					{
						"attrs": {
							"data-role": "fieldcontain"
						},
						"content": [
							{
								"attrs": {
									"for": "sliderMinutes"
								},
								"content": {
									"text": "Minutes:"
								},
								"type": "label"
							},
							{
								"attrs": {
									"data-highlight": "true",
									"max": "10",
									"min": "1",
									"name": "sliderMinutes",
									"step": "1",
									"type": "range",
									"value": "5"
								},
								"id": "sliderMinutes",
								"type": "input"
							}
						]
					}
				]
			},
			getNavbar()
		],
		"id": "pageSettingsAutodeactivate"
	}
	return o;
}

function getPageLogin() {
	o =
	{
		"attrs": {
			"data-role": "page"
		},
		"content": [
			{
				"attrs": {
					"data-role": "header"
				},
				"content": {
					"content": {
						"text": "Login"
					},
					"type": "h1"
				}
			},
			{
				"attrs": {
					"data-inset": "true",
					"data-role": "content"
				},
				"content": [
					{
						"attrs": {
							"action": "#",
							"method": "POST"
						},
						"content": [
							{
								"content": {
									"attrs": {
										"data-role": "fieldcontain"
									},
									"content": [
										{
											"attrs": {
												"for": htmlDef.page.login.content.loginUsername.id
											},
											"content": {
												"text": "Username:"
											},
											"type": "label"
										},
										{
											"attrs": {
												"name": "username",
												"type": "text",
												"value": ""
											},
											"id": htmlDef.page.login.content.loginUsername.id,
											"type": "input"
										},
										{
											"attrs": {
												"for": htmlDef.page.login.content.loginPassword.id
											},
											"content": {
												"text": "Password:"
											},
											"type": "label"
										},
										{
											"attrs": {
												"name": "password",
												"type": "password",
												"value": ""
											},
											"id": htmlDef.page.login.content.loginPassword.id,
											"type": "input"
										}
									]
								},
								"type": "fieldset"
							},
							{
								"attrs": {
									"data-theme": "b",
									"name": "submit",
									"type": "submit"
								},
								"content": {
									"text": "Login"
								},
								"type": "button",
								"id" : htmlDef.page.login.content.loginForm.content.loginButton.id
							}
						],
						"css": "ui-body ui-body-a ui-corner-all",
						"id": htmlDef.page.login.content.loginForm.id,
						"type": "form"
					},
					{
						"type": "br"
					},
					{
						"content": {
							"text": "Please input username and password."
						},
						"css": "ui-body ui-body-e",
						"id": htmlDef.page.login.content.warningInputUsernameAndPassword.id
					},
					{
						"content": {
							"text": "Wrong login data."
						},
						"css": "ui-body ui-body-e",
						"id": htmlDef.page.login.content.warningWrongUsernameOrPassword.id
					},
					{
						"content": [
							getAjaxLoaderImg(),
							{
								"text": "Try to connect to given server(s): "
							},
							{
								"content" : getListElementsFromArray(appData.serverinformation.ajaxApiUrl),
								"type" : "ul"
							}
						],
						"css": "ui-body ui-body-e",
						"id": htmlDef.page.login.content.connectionToServer.id
					}
				]
			},
			getNavbar()
		],
		"id": "pageLogin"
	}
	return o;
}

function getPageSettingsPicturerefresh() {
	o =
	{
		"attrs": {
			"data-role": "page"
		},
		"content": [
			{
				"attrs": {
					"data-role": "header"
				},
				"content": {
					"content": {
						"text": "Picture refresh"
					},
					"type": "h1"
				}
			},
			{
				"attrs": {
					"data-role": "content"
				},
				"content": [
					{
						"attrs": {
							"data-role": "fieldcontain"
						},
						"content": [
							{
								"attrs": {
									"for": "sliderrefresh1"
								},
								"content": {
									"text": "Picture-refresh"
								},
								"type": "label"
							},
							{
								"attrs": {
									"data-role": "slider",
									"name": "sliderrefresh1"
								},
								"content": [
									{
										"attrs": {
											"value": "off"
										},
										"content": {
											"text": "Auto"
										},
										"type": "option"
									},
									{
										"attrs": {
											"value": "on"
										},
										"content": {
											"text": "Manuall"
										},
										"type": "option"
									}
								],
								"id": "sliderrefresh1",
								"type": "select"
							}
						]
					},
					{
						"attrs": {
							"data-role": "fieldcontain"
						},
						"content": [
							{
								"attrs": {
									"for": "sliderrefresh2"
								},
								"content": {
									"text": "Seconds/Milliseconds:"
								},
								"type": "label"
							},
							{
								"attrs": {
									"data-highlight": "true",
									"max": "1000",
									"min": "1",
									"name": "sliderrefresh2",
									"step": "1",
									"type": "range",
									"value": "100"
								},
								"id": "sliderrefresh2",
								"type": "input"
							}
						]
					},
					{
						"attrs": {
							"data-role": "fieldcontain"
						},
						"content": {
							"attrs": {
								"data-role": "controlgroup",
								"data-type": "horizontal"
							},
							"content": [
								{
									"content": {
										"text": "Choose base unit:"
									},
									"type": "legend"
								},
								{
									"attrs": {
										"checked": "checked",
										"name": "radio-choice-2",
										"type": "radio",
										"value": "choice-1"
									},
									"id": "radio-choice-21",
									"type": "input"
								},
								{
									"attrs": {
										"for": "radio-choice-21"
									},
									"content": {
										"text": "Seconds"
									},
									"type": "label"
								},
								{
									"attrs": {
										"name": "radio-choice-2",
										"type": "radio",
										"value": "choice-2"
									},
									"id": "radio-choice-22",
									"type": "input"
								},
								{
									"attrs": {
										"for": "radio-choice-22"
									},
									"content": {
										"text": "Milliseconds"
									},
									"type": "label"
								}
							],
							"type": "fieldset"
						}
					}
				]
			},
			getNavbar()
		],
		"id": "pageSettingsPicturerefresh"
	}
	return o;
}

function getPageSettings() {
	o =
	{
		"attrs": {
			"data-role": "page"
		},
		"content": [
			{
				"attrs": {
					"data-role": "header"
				},
				"content": {
					"content": {
						"text": "Settings"
					},
					"type": "h1"
				}
			},
			{
				"attrs": {
					"data-inset": "true",
					"data-role": "content"
				},
				"content": [
					{
						"attrs": {
							"data-role": "listview",
							"data-theme": "b"
						},
						"content": [
							{
								"content": {
									"attrs": {
										"href": toIdSel(htmlDef.page.settingsAutodeactivate.id)
									},
									"content": {
										"text": "Auto deactivate"
									},
									"type": "a"
								},
								"type": "li"
							},
							{
								"content": {
									"attrs": {
										"href": toIdSel(htmlDef.page.settingsPicturerefresh.id)
									},
									"content": {
										"text": "Picture refresh"
									},
									"type": "a"
								},
								"type": "li"
							},
							{
								"content": {
									"attrs": {
										"href": toIdSel(htmlDef.page.settingsPicturesizeandquality.id)
									},
									"content": {
										"text": "Picture size and quality"
									},
									"type": "a"
								},
								"type": "li"
							},
							{
								"content": {
									"attrs": {
										"href": toIdSel(htmlDef.page.settingsServerinformation.id)
									},
									"content": {
										"text": "Server Information"
									},
									"type": "a"
								},
								"type": "li"
							}
						],
						"type": "ul"
					},
					{
						"type": "br"
					},
					{
						"type": "br"
					},
					{
						"content": [
							{
								"content": {
									"text": "About"
								},
								"type": "h1"
							},
							{
								"content": {
									"text": "snowman-html5-app - HTML5 application to connect to the snowman-php-server."
								},
								"type": "b"
							},
							{
								"type": "br"
							},
							{
								"content" : {
									"text" : "Copyright (C) 2013 Bernard Ladenthin <bernard.ladenthin@gmail.com>"
								}
							},
							{
								"type": "br"
							},
							{
								"attrs": {
									"href": "http://bernard.ladenthin.net"
								},
								"content": [
									{
										"text": "http://bernard.ladenthin.net"
									}
								],
								"type": "a"
							},
							{
								"type": "br"
							},
							{
								"content": [
									{
										"attrs": {
											"alt": "snowman-460x689-transparent.png",
											"src": "img/snowman-460x689-transparent.png",
											"style": "max-width:100%;"
										},
										"type": "img"
									},
									{
										"type": "br"
									},
									{
										"attrs": {
											"alt": "html5-badge-h-css3-graphics-semantics.png",
											"src": "img/html5-badge-h-css3-graphics-semantics.png",
											"style": "max-width:100%;"
										},
										"type": "img"
									}
								]
							}
						],
						"css": "ui-body ui-body-c"
					},
					{
						"type": "br"
					},
					{
						"type": "br"
					},
					{
						"content": [
							{
								"content": {
									"text": "License"
								},
								"type": "h1"
							},
							{
								"attrs": {
									"alt": "gplv3-127x51.png",
									"src": "img/gplv3-127x51.png",
									"style": "max-width:100%;"
								},
								"type": "img"
							},
							{
								"attrs": {
									"data-collapsed": "false",
									"data-role": "collapsible",
									"data-theme": "c"
								},
								"content": [
									{
										"content": {
											"text": "License"
										},
										"type": "h3"
									},
									{
										"content": [
											{
												"text": "snowman-html5-app - HTML5 application to connect to the snowman-php-server"
											},
											{
												"type": "br"
											},
											{
												"text": "Copyright (C) 2013 Bernard Ladenthin <bernard.ladenthin@gmail.com>"
											},
											{
												"type": "br"
											},
											{
												"type": "br"
											},
											{
												"text": "This program is free software: you can redistribute it and/or modify"+
														" it under the terms of the GNU General Public License as published by "+
														"the Free Software Foundation, either version 3 of the License, or "+
														"(at your option) any later version."
											},
											{
												"type": "br"
											},
											{
												"type": "br"
											},
											{
												"text": "This program is distributed in the hope that it will be useful, "+
														"but WITHOUT ANY WARRANTY; without even the implied warranty of "+
														"MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the "+
														"GNU General Public License for more details."
											},
											{
												"type": "br"
											},
											{
												"type": "br"
											},
											{
												"text": "You should have received a copy of the GNU General Public License "+
														"along with this program. If not, see <http://www.gnu.org/licenses/>."
											}
										],
										"type": "p"
									}
								]
							}
						],
						"css": "ui-body ui-body-c"
					},
					{
						"type": "br"
					},
					{
						"type": "br"
					}
				]
			},
			getNavbar()
		],
		"id": "pageSettings"
	}
	return o;
}

