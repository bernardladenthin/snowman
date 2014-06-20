/**
 * snowman-html5-canvasapi- HTML5 canvas api for snowman.
 * http://code.google.com/p/snowman/
 *
 * Copyright (C) 2013 Bernard Ladenthin <bernard.ladenthin@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 */

function getSnowmanHtml5Canvasapi() {

	snowmanHtml5Canvasapi = {
		"bDebugLog" : false,
		"data" : {
			"canvas" : false,
			"liveviewLastSeconds" : 0,
			"liveviewLastMillis" : 0
		}
	};

	snowmanHtml5Canvasapi.debugLog = function(o) {
		if(snowmanHtml5Canvasapi.bDebugLog) {
			console.log(o);
		}
	};

	snowmanHtml5Canvasapi.enableDebugLog = function() {
		snowmanHtml5Canvasapi.bDebugLog = true;
	};

	snowmanHtml5Canvasapi.disableDebugLog = function() {
		snowmanHtml5Canvasapi.bDebugLog = false;
	};

	snowmanHtml5Canvasapi.getCanvas = function() {
		return snowmanHtml5Canvasapi.data.canvas;
	};

	snowmanHtml5Canvasapi.setCanvas = function(canvas) {
		snowmanHtml5Canvasapi.data.canvas = canvas;
	};

	/**
	 * XmlHttpRequest's getAllResponseHeaders() method returns a
	 * string of response headers according to the format described here:
	 * http://www.w3.org/TR/XMLHttpRequest/#the-getallresponseheaders-method
	 * This method parses that string into
	 * a user-friendly key/value pair object.
	 * from https://gist.github.com/monsur/706839
	 */
	snowmanHtml5Canvasapi.parseResponseHeaders = function(headerStr) {
		var headers = {};
			if (!headerStr) {
			return headers;
		}

		var pairs = headerStr.split('\u000d\u000a');
		for (var i = 0; i < pairs.length; i++) {
			var headerPair = pairs[i];
			var index = headerPair.indexOf('\u003a\u0020');
			if (index > 0) {
				var k = headerPair.substring(0, index);
				var v = headerPair.substring(index + 2);
				headers[k] = v;
			}
		}
		return headers;
	};

	snowmanHtml5Canvasapi.initCanvas = function() {
		canvas = snowmanHtml5Canvasapi.getCanvas();
		if (canvas[0].getContext) {
			width=250;
			height=250;

			canvas[0].width=width;
			canvas[0].height=height;
			var ctx = canvas[0].getContext("2d");

			ctx.moveTo(0,0);
			ctx.lineTo(width,height);
			ctx.stroke();

			ctx.fillStyle = "black";
			ctx.strokeRect(0,0,width,height);
		}
	};

	snowmanHtml5Canvasapi.fetchImage = function(name, url, additionalData) {
		time = new Date();
		timestamp = time.getTime();
		data =
			'name='+name
		+	'&t='+timestamp
		+	'&base64=true';

		if(additionalData && additionalData.length > 0) {
			data += additionalData;
		}

		jQuery.ajax({
			type: "GET",
			url: url,
			data: data,
			dataType: "text",
			success: snowmanHtml5Canvasapi.successCb,
			error: snowmanHtml5Canvasapi.errorCb,
			complete: snowmanHtml5Canvasapi.completeCb
		});
	};

	//TODO: add here an information callback
/*
function connect() {
	hostname = arguments[0] || "localhost";
	port = port arguments[1] || 80;
	method = arguments[2] || "GET";
}
*/
	snowmanHtml5Canvasapi.successCb = function(data, textStatus, jqXHR) {
		snowmanHtml5Canvasapi.debugLog("call snowmanHtml5Canvasapi.successCb");
		snowmanHtml5Canvasapi.debugLog(textStatus);
		snowmanHtml5Canvasapi.debugLog(jqXHR);

		snowmanHtml5Canvasapi.debugLog(jqXHR.getAllResponseHeaders());
		header = snowmanHtml5Canvasapi.parseResponseHeaders(
			jqXHR.getAllResponseHeaders()
		);
		snowmanHtml5Canvasapi.debugLog(header);

		seconds = parseInt(header["snowman-timeseconds"]);
		millis = parseInt(header["snowman-timemillis"]);

		snowmanHtml5Canvasapi.debugLog(seconds);

		snowmanHtml5Canvasapi.debugLog(millis);

		snowmanHtml5Canvasapi.debugLog(
			snowmanHtml5Canvasapi.data.liveviewLastSeconds
		);

		snowmanHtml5Canvasapi.debugLog(
			snowmanHtml5Canvasapi.data.liveviewLastMillis
		);

		hit = false;

		if(snowmanHtml5Canvasapi.data.liveviewLastSeconds < seconds) {
			hit = true;
		} else if(
				snowmanHtml5Canvasapi.data.liveviewLastSeconds == seconds
			&&	snowmanHtml5Canvasapi.data.liveviewLastMillis < millis
		) {
			hit = true;
		} else if(isNaN(seconds) || isNaN(millis)) {
			seconds = millis = 0;
			//no snowman-* information could be found
			hit = true;
		}

		if(hit) {
			snowmanHtml5Canvasapi.data.liveviewLastSeconds = seconds;
			snowmanHtml5Canvasapi.data.liveviewLastMillis = millis;

			snowmanHtml5Canvasapi.debugLog("hit");

			var canvas = snowmanHtml5Canvasapi.getCanvas();
			if (canvas[0].getContext) {

				var image = new Image();
				image.src = "data:image/jpeg;base64,"+data;
				image.onload = function() {

					canvas[0].width=image.naturalWidth;
					canvas[0].height=image.naturalHeight;

					var ctx = canvas[0].getContext("2d");
					ctx.drawImage(image,0,0);

				};
			}
		} else {
			snowmanHtml5Canvasapi.debugLog("miss");
		}
	};

	snowmanHtml5Canvasapi.errorCb = function(jqXHR, textStatus, errorThrown) {
		snowmanHtml5Canvasapi.debugLog("call liveviewErrorCb");
		snowmanHtml5Canvasapi.debugLog(jqXHR);
		snowmanHtml5Canvasapi.debugLog(textStatus);
		snowmanHtml5Canvasapi.debugLog(errorThrown);
	};

	snowmanHtml5Canvasapi.completeCb = function(jqXHR, textStatus) {
		snowmanHtml5Canvasapi.debugLog("call liveviewCompleteCb");
		snowmanHtml5Canvasapi.debugLog(jqXHR);
		snowmanHtml5Canvasapi.debugLog(textStatus);
	};

	return snowmanHtml5Canvasapi;
}

