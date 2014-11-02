/*jslint devel: true, browser: true */
/*global jQuery*/
// test this file with http://jshint.com/ and http://jshint.com/
// version 1.0.1
/**
 * snowman-html5-canvasapi- HTML5 canvas api for snowman.
 * https://github.com/bernardladenthin/snowman
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
    "use strict";

    var snowmanHtml5Canvasapi = {
        "bDebugLog" : false,
        "data" : {
            "canvas" : false,
            "liveviewLastSeconds" : 0,
            "liveviewLastMillis" : 0
        }
    };

    snowmanHtml5Canvasapi.debugLog = function (o) {
        if (snowmanHtml5Canvasapi.bDebugLog) {
            console.log(o);
        }
    };

    snowmanHtml5Canvasapi.enableDebugLog = function () {
        snowmanHtml5Canvasapi.bDebugLog = true;
    };

    snowmanHtml5Canvasapi.disableDebugLog = function () {
        snowmanHtml5Canvasapi.bDebugLog = false;
    };

    snowmanHtml5Canvasapi.getCanvas = function () {
        return snowmanHtml5Canvasapi.data.canvas;
    };

    snowmanHtml5Canvasapi.setCanvas = function (canvas) {
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
    snowmanHtml5Canvasapi.parseResponseHeaders = function (headerStr) {
        var headers, pairs, headerPair, index, k, v, i;

        headers = {};
        pairs = headerStr.split("\r\n");

        if (!headerStr) {
            return headers;
        }

        for (i = 0; i < pairs.length; i = i + 1) {
            headerPair = pairs[i];
            index = headerPair.indexOf(": ");
            if (index > 0) {
                k = headerPair.substring(0, index);
                v = headerPair.substring(index + 2);
                headers[k] = v;
            }
        }
        return headers;
    };

    snowmanHtml5Canvasapi.initCanvas = function () {
        var canvas = snowmanHtml5Canvasapi.getCanvas(), width, height, ctx;
        if (canvas[0].getContext) {
            width = 250;
            height = 250;

            canvas[0].width = width;
            canvas[0].height = height;
            ctx = canvas[0].getContext("2d");

            ctx.moveTo(0, 0);
            ctx.lineTo(width, height);
            ctx.stroke();

            ctx.fillStyle = "black";
            ctx.strokeRect(0, 0, width, height);
        }
    };

    snowmanHtml5Canvasapi.fetchImage = function (name, url, additionalData) {
        var time, timestamp, data;

        time = new Date();
        timestamp = time.getTime();
        data = 'name=' + name + '&t=' + timestamp + '&base64=true';

        if (additionalData && additionalData.length > 0) {
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

    snowmanHtml5Canvasapi.successCb = function (data, textStatus, jqXHR) {
        var header, seconds, millis, hit, image, ctx, canvas;
        snowmanHtml5Canvasapi.debugLog("call snowmanHtml5Canvasapi.successCb");
        snowmanHtml5Canvasapi.debugLog(textStatus);
        snowmanHtml5Canvasapi.debugLog(jqXHR);

        snowmanHtml5Canvasapi.debugLog(jqXHR.getAllResponseHeaders());
        header = snowmanHtml5Canvasapi.parseResponseHeaders(
            jqXHR.getAllResponseHeaders()
        );
        snowmanHtml5Canvasapi.debugLog(header);

        seconds = parseInt(header["snowman-timeseconds"], 10);
        millis = parseInt(header["snowman-timemillis"], 10);

        snowmanHtml5Canvasapi.debugLog(seconds);

        snowmanHtml5Canvasapi.debugLog(millis);

        snowmanHtml5Canvasapi.debugLog(
            snowmanHtml5Canvasapi.data.liveviewLastSeconds
        );

        snowmanHtml5Canvasapi.debugLog(
            snowmanHtml5Canvasapi.data.liveviewLastMillis
        );

        hit = false;

        if (snowmanHtml5Canvasapi.data.liveviewLastSeconds < seconds) {
            hit = true;
        } else if (
            // use === instead == because seconds are already an int
            snowmanHtml5Canvasapi.data.liveviewLastSeconds === seconds &&
                snowmanHtml5Canvasapi.data.liveviewLastMillis < millis
        ) {
            hit = true;
        } else if (isNaN(seconds) || isNaN(millis)) {
            seconds = millis = 0;
            //no snowman-* information could be found
            hit = true;
        }

        if (hit) {
            snowmanHtml5Canvasapi.data.liveviewLastSeconds = seconds;
            snowmanHtml5Canvasapi.data.liveviewLastMillis = millis;

            snowmanHtml5Canvasapi.debugLog("hit");

            canvas = snowmanHtml5Canvasapi.getCanvas();
            if (canvas[0].getContext) {

                image = new Image();
                image.src = "data:image/jpeg;base64," + data;
                image.onload = function () {

                    canvas[0].width = image.naturalWidth;
                    canvas[0].height = image.naturalHeight;

                    ctx = canvas[0].getContext("2d");
                    ctx.drawImage(image, 0, 0);

                };
            }
        } else {
            snowmanHtml5Canvasapi.debugLog("miss");
        }
    };

    snowmanHtml5Canvasapi.errorCb = function (jqXHR, textStatus, errorThrown) {
        snowmanHtml5Canvasapi.debugLog("call liveviewErrorCb");
        snowmanHtml5Canvasapi.debugLog(jqXHR);
        snowmanHtml5Canvasapi.debugLog(textStatus);
        snowmanHtml5Canvasapi.debugLog(errorThrown);
    };

    snowmanHtml5Canvasapi.completeCb = function (jqXHR, textStatus) {
        snowmanHtml5Canvasapi.debugLog("call liveviewCompleteCb");
        snowmanHtml5Canvasapi.debugLog(jqXHR);
        snowmanHtml5Canvasapi.debugLog(textStatus);
    };

    return snowmanHtml5Canvasapi;
}

