/**!
 * jQuery Progress Timer - v1.0.5 - 6/8/2015
 * http://www.thomasnorberg.com
 * Copyright (c) 2015 Thomas Norberg;
 * Licensed MIT
 */

/*
 <div class="progress">
 <div class="progress-bar progress-bar-success progress-bar-striped"
 role="progressbar" aria-valuenow="40" aria-valuemin="0"
 aria-valuemax="100" style="width: 40%">
 <span class="sr-only">40% Complete (success)</span>
 </div>
 </div>
 */
if (typeof jQuery === "undefined") {
    throw new Error("jQuery progress timer requires jQuery");
}
/*!
 * jQuery lightweight plugin boilerplate
 * Original author: @ajpiano
 * Further changes, comments: @addyosmani
 * Licensed under the MIT license
 */

(function ($, window, document, undefined) {
    "use strict";
    // undefined is used here as the undefined global
    // variable in ECMAScript 3 and is mutable (i.e. it can
    // be changed by someone else). undefined isn't really
    // being passed in so we can ensure that its value is
    // truly undefined. In ES5, undefined can no longer be
    // modified.

    // window and document are passed through as local
    // variables rather than as globals, because this (slightly)
    // quickens the resolution process and can be more
    // efficiently minified (especially when both are
    // regularly referenced in your plugin).

    // Create the defaults once
    var pluginName = "progressTimer",
        defaults = {
            //total number of seconds
            timeLimit: 60,
            //seconds remaining triggering switch to warning color
            warningThreshold: 5,
            //invoked once the timer expires
            onFinish: function () {
            },
            //bootstrap progress bar style at the beginning of the timer
            baseStyle: "",
            //bootstrap progress bar style in the warning phase
            warningStyle: "progress-bar-danger",
            //bootstrap progress bar style at completion of timer
            completeStyle: "progress-bar-success",
            //show html on progress bar div area
            showHtmlSpan: true,
            //set the error text when error occurs
            errorText: "ERROR!",
            //set the success text when succes occurs
            successText: "100%"
        };

    // The actual plugin constructor
    var Plugin = function (element, options) {
        this.element = element;
        this.$elem = $(element);
        this.options = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.metadata = this.$elem.data("plugin-options");
        this.init();
    };

    Plugin.prototype.constructor = Plugin;

    Plugin.prototype.init = function () {
        var t = this;
        $(t.element).empty();
        t.span = $("<span/>");
        t.barContainer = $("<div>").addClass("progress");
        t.bar = $("<div>").addClass("progress-bar active progress-bar-striped").addClass(t.options.baseStyle)
            .attr("role", "progressbar")
            .attr("aria-valuenow", "0")
            .attr("aria-valuemin", "0")
            .attr("aria-valuemax", t.options.timeLimit);
        t.span.appendTo(t.bar);
        if (!t.options.showHtmlSpan) {
            t.span.addClass("sr-only");
        }
        t.bar.appendTo(t.barContainer);
        t.barContainer.appendTo(t.element);
        t.start = new Date();
        t.limit = t.options.timeLimit * 1000;
        t.warningThreshold = t.options.warningThreshold * 1000;
        t.interval = window.setInterval(function () {
            t._run.call(t);
        }, 250);
        t.bar.data("progress-interval", t.interval);
        return true;
    };

    Plugin.prototype.destroy = function(){
        this.$elem.removeData();
    };

    Plugin.prototype._run = function () {
        var t = this;
        var elapsed = new Date() - t.start,
            width = ((elapsed / t.limit) * 100);
        t.bar.attr("aria-valuenow", width);
        t.bar.width(width + "%");
        var percentage = width.toFixed(2);
        if (percentage >= 100) {
            percentage = 100;
        }
        if (t.options.showHtmlSpan) {
            t.span.html(percentage + "%");
        }
        if (elapsed >= t.warningThreshold) {
            t.bar.removeClass(this.options.baseStyle)
                .removeClass(this.options.completeStyle)
                .addClass(this.options.warningStyle);
        }
        if (elapsed >= t.limit) {
            t.complete.call(t);
        }
        return true;
    };

    Plugin.prototype.removeInterval = function () {
        var t = this,
            bar = $(".progress-bar", t.element);
        if (typeof bar.data("progress-interval") !== "undefined") {
            var interval = bar.data("progress-interval");
            window.clearInterval(interval);
        }
        return bar;
    };

    Plugin.prototype.complete = function () {
        var t = this,
            bar = t.removeInterval.call(t),
            args = arguments;
        if(args.length !== 0 && typeof args[0] === "object"){
            t.options = $.extend({}, t.options, args[0]);
        }
        bar.removeClass(t.options.baseStyle)
            .removeClass(t.options.warningStyle)
            .addClass(t.options.completeStyle);
        bar.width("100%");
        if (t.options.showHtmlSpan) {
            $("span", bar).html(t.options.successText);
        }
        bar.attr("aria-valuenow", 100);
        setTimeout(function () {
            t.options.onFinish.call(bar);
        }, 500);
        t.destroy.call(t);
    };

    Plugin.prototype.error = function () {
        var t = this,
            bar = t.removeInterval.call(t),
            args = arguments;
        if(args.length !== 0 && typeof args[0] === "object"){
            t.options = $.extend({}, t.options, args[0]);
        }
        bar.removeClass(t.options.baseStyle)
            .addClass(t.options.warningStyle);
        bar.width("100%");
        if (t.options.showHtmlSpan) {
            $("span", bar).html(t.options.errorText);
        }
        bar.attr("aria-valuenow", 100);
        setTimeout(function () {
            t.options.onFinish.call(bar);
        }, 500);
        t.destroy.call(t);
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function (options) {
        var args = arguments;
        if (options === undefined || typeof options === "object") {
            // Creates a new plugin instance
            return this.each(function () {
                if (!$.data(this, "plugin_" + pluginName)) {
                    $.data(this, "plugin_" + pluginName, new Plugin(this, options));
                }
            });
        } else if (typeof options === "string" && options[0] !== "_" && options !== "init") {
            // Call a public plugin method (not starting with an underscore) and different
            // from the "init" one
            if (Array.prototype.slice.call(args, 1).length === 0 && $.inArray(options, $.fn[pluginName].getters) !== -1) {
                // If the user does not pass any arguments and the method allows to
                // work as a getter then break the chainability so we can return a value
                // instead the element reference.
                var instance = $.data(this[0], "plugin_" + pluginName);
                return instance[options].apply(instance, Array.prototype.slice.call(args, 1));
            } else {
                // Invoke the specified method on each selected element
                return this.each(function() {
                    var instance = $.data(this, "plugin_" + pluginName);
                    if (instance instanceof Plugin && typeof instance[options] === "function") {
                        instance[options].apply(instance, Array.prototype.slice.call(args, 1));
                    }
                });
            }
        }
    };

    $.fn[pluginName].getters = ["complete", "error"];

})(jQuery, window, document, undefined);