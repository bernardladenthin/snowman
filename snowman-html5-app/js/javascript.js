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

var htmlConst = {
	"string" : {
		"empty" : "",
		"true" : "true",
		"false" : "false",
		"zero" : "zero",
		"xml" : "xml",
		"JSON" : "JSON",
		"POST" : "POST",
		"GET" : "GET"
	}
}

//parseURI and absolutizeURI from https://gist.github.com/Yaffle/1088850
function parseURI(url) {
	var m = String(url).replace(/^\s+|\s+$/g, '').match(/^([^:\/?#]+:)?(\/\/(?:[^:@]*(?::[^:@]*)?@)?(([^:\/?#]*)(?::(\d*))?))?([^?#]*)(\?[^#]*)?(#[\s\S]*)?/);
	// authority = '//' + user + ':' + pass '@' + hostname + ':' port
	return (m ? {
		href     : m[0] || '',
		protocol : m[1] || '',
		authority: m[2] || '',
		host     : m[3] || '',
		hostname : m[4] || '',
		port     : m[5] || '',
		pathname : m[6] || '',
		search   : m[7] || '',
		hash     : m[8] || ''
	} : null);
}

function absolutizeURI(base, href) {// RFC 3986

	function removeDotSegments(input) {
		var output = [];
		input.replace(/^(\.\.?(\/|$))+/, '')
			.replace(/\/(\.(\/|$))+/g, '/')
			.replace(/\/\.\.$/, '/../')
			.replace(/\/?[^\/]*/g, function (p) {
			if (p === '/..') {
				output.pop();
			} else {
				output.push(p);
			}
		});
		return output.join('').replace(/^\//, input.charAt(0) === '/' ? '/' : '');
	}

	href = parseURI(href || '');
	base = parseURI(base || '');

	return !href || !base ? null : (href.protocol || base.protocol) +
		(href.protocol || href.authority ? href.authority : base.authority) +
		removeDotSegments(href.protocol || href.authority || href.pathname.charAt(0) === '/' ? href.pathname : (href.pathname ? ((base.authority && !base.pathname ? '/' : '') + base.pathname.slice(0, base.pathname.lastIndexOf('/') + 1) + href.pathname) : base.pathname)) +
		(href.protocol || href.authority || href.pathname ? href.search : (href.search || base.search)) +
		href.hash;
}

function isset(o) {
	if(typeof o != 'undefined')
		return true;
	return false;
}

function toIdSel(string) {
	return "#"+string;
}

function toClSel(string) {
	return "."+string;
}

function adaptUri(href) {

	//TODO: force https option here for https protocol
	appData.forceHttps = false;

	newBaseURI = "";
	if(window.location.protocol != "") {
		if(appData.forceHttps) {
			newBaseURI += "https:";
		} else {
			newBaseURI += window.location.protocol;
		}
		newBaseURI += "//";
	}

	if(window.location.host != "") {
		newBaseURI += window.location.host + "/";
	}

	if(window.location.pathname != "") {
		newBaseURI += window.location.pathname;
	}

	return absolutizeURI(newBaseURI, href);
}

function JSONactionRecursive(jData, i) {
	if(i >= appData.serverinformation.ajaxApiUrl.length) {
		return false;
	}

	jQuery.ajax({
		url: appData.serverinformation.ajaxApiUrl[i],
		data: "callback=?" + "&jsonp=" + JSON.stringify(jData),
		dataType: htmlConst.string.JSON,
		success: cbJSONResponsee,
		error: function () { JSONactionRecursive(jData, i+1) }
	});
	return false;
}

function JSONaction(jData) {
	JSONactionRecursive(jData, 0);
	return false;
}

function JSONactionDownloadArchive(jData) {
	window.open(
		appData.serverinformation.downloadarchiveurl
			+"?json=" + JSON.stringify(jData)
			+"&username=" + appData.username
			+"&password=" + appData.password
		,'download archive'
	);
}

function logoutNode(parent) {
	if(parent.status) {
		$.mobile.changePage(toIdSel(htmlDef.page.login.id), {
			transition: htmlDef.page.stdTransition
		});
		appData.login = false;
	}
}

function loginNode(parent) {
	if(parent.status) {
		appData.password = $(toIdSel(htmlDef.page.login.content.loginPassword.id)).val();
		$(toIdSel(htmlDef.page.login.content.loginUsername.id)).val(htmlConst.string.empty);
		$(toIdSel(htmlDef.page.login.content.loginPassword.id)).val(htmlConst.string.empty);

		appData.login = true;
		appData.sessionid = parent.sessionid;
		appData.username = parent.username;
		appData.groups = parent.groups;

		refreshUserformation();
		actionCameraInformation();

		$.mobile.changePage(toIdSel(htmlDef.page.cameras.id), {
			transition: htmlDef.page.stdTransition
		});
	}
	else {
		showUserHighligth(toIdSel(htmlDef.page.login.content.warningWrongUsernameOrPassword.id),true);
		appData.login = false;
	}
}

function commandNode(parent) {
	var success = false;

	if(isset(parent.createarchive)) {
		if(parent.createarchive.success == true) {
			showUserHighligth(toIdSel(htmlDef.page.cameras.content.showSuccessCommandArchive.id),true);
			success = true;
		}
	}

	if(isset(parent.refreshchmod)) {
		if(parent.refreshchmod.success == true) {
			showUserHighligth(toIdSel(htmlDef.page.cameras.content.showSuccessCommandRefreshChmod.id),true);
			success = true;
		}
	}

	if(success == false) {
		console.log("unknown commandNode:" + parent);
	}
}

function archiveListingNodeSelected(event, data) {
	var path=data.node.original.attr.path;
	if(path!=undefined) {
		jPath = JSON.parse(path);
		actionDownloadArchive(jPath);
	}
}

function transformListingTreeToJstree(listingTree, parentPath) {
	var data = [];

	$.each(listingTree, function(iKey, iValue) {
		if(typeof iValue == 'string') {
			//copy the array
			localParentPath = parentPath.slice();
			localParentPath.push(iValue);
			var element = {
				"text" : iValue,
				"attr" : { "path" : JSON.stringify(localParentPath)}
			}
			data.push(element);
		} else if (typeof iValue == 'object' || typeof iValue == 'array') {
			//copy the array
			localParentPath = parentPath.slice();
			localParentPath.push(iKey);
			var element = {
				"text" : iKey,
				"children" : transformListingTreeToJstree(iValue, localParentPath)
			};
			data.push(element);
		}
	});

	return data;
}

function createArchiveListing(json) {
	var archiveListing = $(toIdSel(htmlDef.page.cameras.content.archiveListing.id));

	archiveListing.empty();
	archiveListing.append(fastFrag.create(createListingTreeRoot()));

	var archiveListingTree = $(toIdSel(htmlDef.page.cameras.content.archiveListingTree.id));

	t = {
		"text" : "archive",
		"children" : transformListingTreeToJstree(json, [])
	};

	archiveListingTree.jstree({
		"core" : {
			"data" : t
		},
		"plugins" : [ "sort" ]
	}).bind("select_node.jstree", archiveListingNodeSelected);

}

function archiveListingNode(parent) {
	createArchiveListing(parent);
}

function disableLogin() {
	$(toIdSel(htmlDef.page.login.content.loginForm.content.loginButton.id)).attr("disabled", "");
}

function enableLogin() {
	$(toIdSel(htmlDef.page.login.content.loginForm.content.loginButton.id)).removeAttr("disabled");
}

function hideConnectionToServer() {
	$(toIdSel(htmlDef.page.login.content.connectionToServer.id)).hide();
}

function serverinformationNode(parent) {
	appData.serverinformation = parent;
	appData.initialServerinformation = true;
	hideConnectionToServer();
	enableLogin();
	refreshServerinformation();
}

function camerainformationNode(parent) {
	appData.cameras = parent.cameras;
	refreshListviewCameras();
}

function actionCameraview(name, url, refresh) {

	appData.liveViewCamname = name;
	appData.liveviewinterval = refresh;

	$.mobile.changePage(toIdSel(htmlDef.page.cameraview.id), {
		transition: htmlDef.page.stdTransition
	});

	$(toIdSel(htmlDef.page.cameraview.content.pageCameraviewHead.id)).text(name);
}

function refreshListviewCameras() {

	$(toIdSel(htmlDef.page.cameras.content.listviewCameras.id)).html(htmlConst.string.empty);

	$.each(appData.cameras, function(index, value) {
		tagA = $("<a>");
		tagA.text(value.name);
		tagA.attr("href","#");

		fString =
				"actionCameraview"
			+	"('"
			+	value.name
			+	"','"
			+	value.url
			+	"','"
			+	value.refresh
			+	"')"
		;

		tagA.attr("onclick",fString);

		tagLi = $("<li>");
		tagLi.html(tagA);

		$(toIdSel(htmlDef.page.cameras.content.listviewCameras.id)).append(tagLi);
	});

	$(toIdSel(htmlDef.page.cameras.content.listviewCameras.id)).listview("refresh");
}

function cbJSONResponsee(data, textStatus, jqXHR) {
	if(isset(data.logout)) {
		logoutNode(data.logout);
	}
	if(isset(data.login)) {
		loginNode(data.login);
	}
	if(isset(data.camerainformation)) {
		camerainformationNode(data.camerainformation);
	}
	if(isset(data.serverinformation)) {
		serverinformationNode(data.serverinformation);
	}
	if(isset(data.command)) {
		commandNode(data.command);
	}
	if(isset(data.archiveListing)) {
		archiveListingNode(data.archiveListing);
	}
}

function hideAllNavbars() {
	$.each(navbarsCls, function(index, value) {
		$(toClSel(value)).hide();
	});
}

function showInitNavbar() {
	$(toClSel(htmlDef.navbar.loginFalseWithActiveLogin.cl)).show();
}

function pageEventHandler(page,show,event,ui) {
	if(show) {
		switch(page) {
			case htmlDef.page.login.id:
				if(appData.login) {
					actionLogout();
					hideAllNavbars();
					$(toClSel(htmlDef.navbar.loginFalseWithActiveLogin.cl)).show();
					archiveListingClearAndCollapse();
					loginInformationClearAndCollapse();
					specialCommandsClearAndCollapse();
				} else {
					hideAllNavbars();
					$(toClSel(htmlDef.navbar.loginFalseWithActiveLogin.cl)).show();
				}
				break;
			case htmlDef.page.settings.id:
			case htmlDef.page.settingsAutodeactivate.id:
			case htmlDef.page.settingsPicturerefresh.id:
			case htmlDef.page.settingsPicturesizeandquality.id:
			case htmlDef.page.settingsServerinformation.id:
				if(appData.login) {
					hideAllNavbars();
					$(toClSel(htmlDef.navbar.loginTrueWithActiveSettings.cl)).show();
				} else {
					hideAllNavbars();
					$(toClSel(htmlDef.navbar.loginFalseWithActiveSettings.cl)).show();
				}
				break;
			case htmlDef.page.cameras.id:
				if(appData.login) {
					hideAllNavbars();
					$(toClSel(htmlDef.navbar.loginTrueWithActiveCameras.cl)).show();
				} else {
					$.mobile.changePage(toIdSel(htmlDef.page.login.id));
					hideAllNavbars();
					$(toClSel(htmlDef.navbar.loginFalseWithActiveLogin.cl)).show();
				}
				break;
			case htmlDef.page.cameraview.id:
				if(appData.login) {
					hideAllNavbars();
					$(toClSel(htmlDef.navbar.loginTrueWithActiveCameras.cl)).show();
					startLiveView();
				} else {
					$.mobile.changePage(htmlDef.page.login.id);
					hideAllNavbars();
					$(toClSel(htmlDef.navbar.loginFalseWithActiveLogin.cl)).show();
				}
				break;
		}
	} else {
		switch(page) {
			case htmlDef.page.login.id:
			case htmlDef.page.settings.id:
			case htmlDef.page.settingsAutodeactivate.id:
			case htmlDef.page.settingsPicturerefresh.id:
			case htmlDef.page.settingsPicturesizeandquality.id:
			case htmlDef.page.settingsServerinformation.id:
			case htmlDef.page.cameras.id:
				break;
			case htmlDef.page.cameraview.id:
				deactivateLiveView();
			break;
		}
	}
}

function initPageHandler() {

	$.each(pagesIds, function(index, value) {

		$(document).on("pageshow", toIdSel(value), function(event, ui) {
			pageEventHandler(value,true,event,ui);
		});

		$(document).on("pagehide", toIdSel(value), function(event, ui) {
			pageEventHandler(value,false,event,ui);
		});
	});
}

function actionServerinformation() {

	disableLogin();
	appData.initialServerinformation = false;

	jData = {
		"serverinformation" : {
		}
	}

	JSONaction(jData);
	return false;
}

function actionCameraInformation() {

	jData = {
		"camerainformation" : {
		}
	}

	JSONaction(jData);
	return false;
}

function actionCommandRefreshChmod() {

	jData = {
		"command" : {
			"refreshchmod" : {
			}
		}
	}

	JSONaction(jData);
	return false;
}

function actionCommandArchive() {

	jData = {
		"command" : {
			"createarchive" : {
			}
		}
	}

	JSONaction(jData);
	return false;
}

function actionLogin() {
	usernameVal = $(toIdSel(htmlDef.page.login.content.loginUsername.id)).val();
	passwordVal = $(toIdSel(htmlDef.page.login.content.loginPassword.id)).val();

	if(
			usernameVal == htmlConst.string.empty
		||	passwordVal == htmlConst.string.empty
	) {
		showUserHighligth(toIdSel(htmlDef.page.login.content.warningInputUsernameAndPassword.id),true);
		return false;
	}

	jData = {
		"login" : {
			"username" : usernameVal,
			"password" : passwordVal,
		}
	}

	JSONaction(jData);
	return false;
}

function actionLogout() {
	appData.login = false;
	appData.sessionid = false;

	jData = {
		"logout" : {
		}
	}

	JSONaction(jData);
	return false;
}

function actionDownloadArchive(path) {
	jData = {
		"downloadArchive" : {
			"path" : path
		}
	}

	JSONactionDownloadArchive(jData);
}

function actionGetArchiveListing() {
	jData = {
		"getArchiveListing" : {
		}
	}

	JSONaction(jData);
}

function showUserHighligth(select,show) {
	if(show) {
		$(select).fadeIn(200);
		setTimeout(function() {
			$(select).fadeOut(200);
		},1000);
	} else {
		$(select).hide();
	}
}

function initHtmlElements() {
	$(toIdSel(htmlDef.page.login.content.loginForm.id)).submit(actionLogin);
	showUserHighligth(toIdSel(htmlDef.page.login.content.warningInputUsernameAndPassword.id),false);
	showUserHighligth(toIdSel(htmlDef.page.login.content.warningWrongUsernameOrPassword.id),false);
	showUserHighligth(toIdSel(htmlDef.page.cameras.content.showSuccessCommandRefreshChmod.id),false);
	showUserHighligth(toIdSel(htmlDef.page.cameras.content.showSuccessCommandArchive.id),false);
}

function refreshUserformation() {
	var username = appData.username;
	var usergroups = "";
	$.each(appData.groups, function(index, value) {
		usergroups += value;
		if(index < appData.groups.length-1) {
			usergroups += " , ";
		}
	});

	$(toIdSel(htmlDef.page.cameras.content.yourUsername.id)).val(username);
	$(toIdSel(htmlDef.page.cameras.content.yourUsergroups.id)).val(usergroups);
}

function refreshServerinformation() {
	$(toIdSel(htmlDef.page.settingsServerinformation.content.ServerinformationServerAjaxApiUrl.id)).val(
		appData.serverinformation.ajaxApiUrl
	);
	$(toIdSel(htmlDef.page.settingsServerinformation.content.ServerinformationServerImprintUrl.id)).val(
		appData.serverinformation.imprintUrl
	);
	$(toIdSel(htmlDef.page.settingsServerinformation.content.ServerinformationServerLiveviewUrl.id)).val(
		appData.serverinformation.liveviewUrl
	);
	$(toIdSel(htmlDef.page.settingsServerinformation.content.ServerinformationServerDownloadarchiveUrl.id)).val(
		appData.serverinformation.downloadarchiveurl
	);
	$(toIdSel(htmlDef.page.settingsServerinformation.content.ServerinformationServerVersion.id)).val(
		appData.serverinformation.version
	);
	$(toIdSel(htmlDef.page.settingsServerinformation.content.ServerinformationServerVersionDate.id)).val(
		appData.serverinformation.versionDate
	);
	$(toIdSel(htmlDef.page.settingsServerinformation.content.ServerinformationServerOwner.id)).val(
		appData.serverinformation.owner
	);
}

function liveViewCb() {
	var additionalData = '&PHPSESSID='+appData.sessionid;
	//TODO: api version 1.1 should provide a information callback
	appData.snowmanHtml5CanvasapiInstance.fetchImage(
		appData.liveViewCamname,
		appData.serverinformation.liveviewUrl,
		additionalData
	);
}

function startLiveView() {
	clearInterval(appData.hLiveViewSetInterval);

	appData.hLiveViewSetInterval = setInterval(
		liveViewCb,
		appData.liveviewinterval
	);

	showLiveViewButtonPlay(false);
}

function deactivateLiveView() {
	clearInterval(appData.hLiveViewSetInterval);
}

function showLiveViewButtonPlay(play) {
	if(play) {
		$(toIdSel(htmlDef.page.cameras.content.liveviewplaybtn.id)).show();
		$(toIdSel(htmlDef.page.cameras.content.liveviewpausebtn.id)).hide();
	} else {
		$(toIdSel(htmlDef.page.cameras.content.liveviewplaybtn.id)).hide();
		$(toIdSel(htmlDef.page.cameras.content.liveviewpausebtn.id)).show();
	}
}

function liveviewplaybtn() {
	showLiveViewButtonPlay(false);
	startLiveView();
}

function liveviewpausebtn() {
	showLiveViewButtonPlay(true);
	deactivateLiveView();
}

function initSnowmanHtml5CanvasapiInstance() {
	var canvas = $(toIdSel(htmlDef.page.cameraview.content.canvasLiveview.id));
	appData.snowmanHtml5CanvasapiInstance = getSnowmanHtml5Canvasapi();
	appData.snowmanHtml5CanvasapiInstance.setCanvas(canvas);
	appData.snowmanHtml5CanvasapiInstance.initCanvas();
	//appData.snowmanHtml5CanvasapiInstance.enableDebugLog();
}

function archiveListingCollapsibleExpand() {
	var archiveListing = $(toIdSel(htmlDef.page.cameras.content.archiveListing.id));
	archiveListing.empty();
	archiveListing.append(fastFrag.create(getAjaxLoaderImg()));
	actionGetArchiveListing();
}

function archiveListingCollapsibleCollapse() {
	archiveListingClear();
}

function archiveListingClearAndCollapse() {
	archiveListingClear();
	var archiveListingCollapsible = $(toIdSel(htmlDef.page.cameras.content.archiveListingCollapsible.id));
	archiveListingCollapsible.collapsible( "collapse" );
}

function archiveListingClear() {
	var archiveListing = $(toIdSel(htmlDef.page.cameras.content.archiveListing.id));
	archiveListing.empty();
}

function initCollapsibleContainer() {
	var archiveListingCollapsible = $(toIdSel(htmlDef.page.cameras.content.archiveListingCollapsible.id));

	archiveListingCollapsible.on(
		'collapsibleexpand', archiveListingCollapsibleExpand
	);
	archiveListingCollapsible.on(
		'collapsiblecollapse', archiveListingCollapsibleCollapse
	);

}

function loginInformationClearAndCollapse() {
	var loginInformationCollapsible = $(toIdSel(htmlDef.page.cameras.content.loginInformationCollapsible.id));
	loginInformationCollapsible.collapsible( "collapse" );
	$(toIdSel(htmlDef.page.cameras.content.yourUsername.id)).val("");
	$(toIdSel(htmlDef.page.cameras.content.yourUsergroups.id)).val("");
}

function specialCommandsClearAndCollapse() {
	var specialCommandsCollapsible = $(toIdSel(htmlDef.page.cameras.content.specialCommandsCollapsible.id));
	specialCommandsCollapsible.collapsible( "collapse" );
	// Nothing to clear.
}

function initNicescroll() {
	$(toIdSel(htmlDef.page.cameras.content.archiveListing.id)).niceScroll();
}


