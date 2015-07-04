/**
* DD_belatedPNG: Adds IE6 support: PNG images for CSS background-image and HTML <IMG/>.
* Author: Drew Diller
* Email: drew.diller@gmail.com
* URL: http://www.dillerdesign.com/experiment/DD_belatedPNG/
* Version: 0.0.8a
* Licensed under the MIT License: http://dillerdesign.com/experiment/DD_belatedPNG/#license
*
* Example usage:
* DD_belatedPNG.fix('.png_bg'); // argument is a CSS selector
* DD_belatedPNG.fixPng( someNode ); // argument is an HTMLDomElement
**/

/*
PLEASE READ:
Absolutely everything in this script is SILLY.  I know this.  IE's rendering of certain pixels doesn't make sense, so neither does this code!
*/


if (jQuery.browser.msie && (6 == jQuery.browser.version)) {



	var DD_belatedPNG = {
		ns: 'DD_belatedPNG',
		imgSize: {},
		delay: 10,
		nodesFixed: 0,
		createVmlNameSpace: function () { /* enable VML */
			if (document.namespaces && !document.namespaces[this.ns]) {
				document.namespaces.add(this.ns, 'urn:schemas-microsoft-com:vml');
			}
		},
		createVmlStyleSheet: function () { /* style VML, enable behaviors */
			/*
				Just in case lots of other developers have added
				lots of other stylesheets using document.createStyleSheet
				and hit the 31-limit mark, let's not use that method!
				further reading: http://msdn.microsoft.com/en-us/library/ms531194(VS.85).aspx
			*/
			var screenStyleSheet, printStyleSheet;
			screenStyleSheet = document.createElement('style');
			screenStyleSheet.setAttribute('media', 'screen');
			document.documentElement.firstChild.insertBefore(screenStyleSheet, document.documentElement.firstChild.firstChild);
			if (screenStyleSheet.styleSheet) {
				screenStyleSheet = screenStyleSheet.styleSheet;
				screenStyleSheet.addRule(this.ns + '\\:*', '{behavior:url(#default#VML)}');
				screenStyleSheet.addRule(this.ns + '\\:shape', 'position:absolute;');
				screenStyleSheet.addRule('img.' + this.ns + '_sizeFinder', 'behavior:none; border:none; position:absolute; z-index:-1; top:-10000px; visibility:hidden;'); /* large negative top value for avoiding vertical scrollbars for large images, suggested by James O'Brien, http://www.thanatopsic.org/hendrik/ */
				this.screenStyleSheet = screenStyleSheet;

				/* Add a print-media stylesheet, for preventing VML artifacts from showing up in print (including preview). */
				/* Thanks to R�mi Pr�vost for automating this! */
				printStyleSheet = document.createElement('style');
				printStyleSheet.setAttribute('media', 'print');
				document.documentElement.firstChild.insertBefore(printStyleSheet, document.documentElement.firstChild.firstChild);
				printStyleSheet = printStyleSheet.styleSheet;
				printStyleSheet.addRule(this.ns + '\\:*', '{display: none !important;}');
				printStyleSheet.addRule('img.' + this.ns + '_sizeFinder', '{display: none !important;}');
			}
		},
		readPropertyChange: function () {
			var el, display, v;
			el = event.srcElement;
			if (!el.vmlInitiated) {
				return;
			}
			if (event.propertyName.search('background') != -1 || event.propertyName.search('border') != -1) {
				DD_belatedPNG.applyVML(el);
			}
			if (event.propertyName == 'style.display') {
				display = (el.currentStyle.display == 'none') ? 'none' : 'block';
				for (v in el.vml) {
					if (el.vml.hasOwnProperty(v)) {
						el.vml[v].shape.style.display = display;
					}
				}
			}
			if (event.propertyName.search('filter') != -1) {
				DD_belatedPNG.vmlOpacity(el);
			}
		},
		vmlOpacity: function (el) {
			if (el.currentStyle.filter.search('lpha') != -1) {
				var trans = el.currentStyle.filter;
				trans = parseInt(trans.substring(trans.lastIndexOf('=')+1, trans.lastIndexOf(')')), 10)/100;
				el.vml.color.shape.style.filter = el.currentStyle.filter; /* complete guesswork */
				el.vml.image.fill.opacity = trans; /* complete guesswork */
			}
		},
		handlePseudoHover: function (el) {
			setTimeout(function () { /* wouldn't work as intended without setTimeout */
				DD_belatedPNG.applyVML(el);
			}, 1);
		},
		/**
		* This is the method to use in a document.
		* @param {String} selector - REQUIRED - a CSS selector, such as '#doc .container'
		**/
		fix: function (selector) {
			if (this.screenStyleSheet) {
				var selectors, i;
				selectors = selector.split(','); /* multiple selectors supported, no need for multiple calls to this anymore */
				for (i=0; i<selectors.length; i++) {
					this.screenStyleSheet.addRule(selectors[i], 'behavior:expression(DD_belatedPNG.fixPng(this))'); /* seems to execute the function without adding it to the stylesheet - interesting... */
				}
			}
		},
		applyVML: function (el) {
			el.runtimeStyle.cssText = '';
			this.vmlFill(el);
			this.vmlOffsets(el);
			this.vmlOpacity(el);
			if (el.isImg) {
				this.copyImageBorders(el);
			}
		},
		attachHandlers: function (el) {
			var self, handlers, handler, moreForAs, a, h;
			self = this;
			handlers = {resize: 'vmlOffsets', move: 'vmlOffsets'};
			if (el.nodeName == 'A') {
				moreForAs = {mouseleave: 'handlePseudoHover', mouseenter: 'handlePseudoHover', focus: 'handlePseudoHover', blur: 'handlePseudoHover'};
				for (a in moreForAs) {
					if (moreForAs.hasOwnProperty(a)) {
						handlers[a] = moreForAs[a];
					}
				}
			}
			for (h in handlers) {
				if (handlers.hasOwnProperty(h)) {
					handler = function () {
						self[handlers[h]](el);
					};
					el.attachEvent('on' + h, handler);
				}
			}
			el.attachEvent('onpropertychange', this.readPropertyChange);
		},
		giveLayout: function (el) {
			el.style.zoom = 1;
			if (el.currentStyle.position == 'static') {
				el.style.position = 'relative';
			}
		},
		copyImageBorders: function (el) {
			var styles, s;
			styles = {'borderStyle':true, 'borderWidth':true, 'borderColor':true};
			for (s in styles) {
				if (styles.hasOwnProperty(s)) {
					el.vml.color.shape.style[s] = el.currentStyle[s];
				}
			}
		},
		vmlFill: function (el) {
			if (!el.currentStyle) {
				return;
			} else {
				var elStyle, noImg, lib, v, img, imgLoaded;
				elStyle = el.currentStyle;
			}
			for (v in el.vml) {
				if (el.vml.hasOwnProperty(v)) {
					el.vml[v].shape.style.zIndex = elStyle.zIndex;
				}
			}
			el.runtimeStyle.backgroundColor = '';
			el.runtimeStyle.backgroundImage = '';
			noImg = true;
			if (elStyle.backgroundImage != 'none' || el.isImg) {
				if (!el.isImg) {
					el.vmlBg = elStyle.backgroundImage;
					el.vmlBg = el.vmlBg.substr(5, el.vmlBg.lastIndexOf('")')-5);
				}
				else {
					el.vmlBg = el.src;
				}
				lib = this;
				if (!lib.imgSize[el.vmlBg]) { /* determine size of loaded image */
					img = document.createElement('img');
					lib.imgSize[el.vmlBg] = img;
					img.className = lib.ns + '_sizeFinder';
					img.runtimeStyle.cssText = 'behavior:none; position:absolute; left:-10000px; top:-10000px; border:none; margin:0; padding:0;'; /* make sure to set behavior to none to prevent accidental matching of the helper elements! */
					imgLoaded = function () {
						this.width = this.offsetWidth; /* weird cache-busting requirement! */
						this.height = this.offsetHeight;
						lib.vmlOffsets(el);
					};
					img.attachEvent('onload', imgLoaded);
					img.src = el.vmlBg;
					img.removeAttribute('width');
					img.removeAttribute('height');
					document.body.insertBefore(img, document.body.firstChild);
				}
				el.vml.image.fill.src = el.vmlBg;
				noImg = false;
			}
			el.vml.image.fill.on = !noImg;
			el.vml.image.fill.color = 'none';
			el.vml.color.shape.style.backgroundColor = elStyle.backgroundColor;
			el.runtimeStyle.backgroundImage = 'none';
			el.runtimeStyle.backgroundColor = 'transparent';
		},
		/* IE can't figure out what do when the offsetLeft and the clientLeft add up to 1, and the VML ends up getting fuzzy... so we have to push/enlarge things by 1 pixel and then clip off the excess */
		vmlOffsets: function (el) {
			var thisStyle, size, fudge, makeVisible, bg, bgR, dC, altC, b, c, v;
			thisStyle = el.currentStyle;
			size = {'W':el.clientWidth+1, 'H':el.clientHeight+1, 'w':this.imgSize[el.vmlBg].width, 'h':this.imgSize[el.vmlBg].height, 'L':el.offsetLeft, 'T':el.offsetTop, 'bLW':el.clientLeft, 'bTW':el.clientTop};
			fudge = (size.L + size.bLW == 1) ? 1 : 0;
			/* vml shape, left, top, width, height, origin */
			makeVisible = function (vml, l, t, w, h, o) {
				vml.coordsize = w+','+h;
				vml.coordorigin = o+','+o;
				vml.path = 'm0,0l'+w+',0l'+w+','+h+'l0,'+h+' xe';
				vml.style.width = w + 'px';
				vml.style.height = h + 'px';
				vml.style.left = l + 'px';
				vml.style.top = t + 'px';
			};
			makeVisible(el.vml.color.shape, (size.L + (el.isImg ? 0 : size.bLW)), (size.T + (el.isImg ? 0 : size.bTW)), (size.W-1), (size.H-1), 0);
			makeVisible(el.vml.image.shape, (size.L + size.bLW), (size.T + size.bTW), (size.W), (size.H), 1 );
			bg = {'X':0, 'Y':0};
			if (el.isImg) {
				bg.X = parseInt(thisStyle.paddingLeft, 10) + 1;
				bg.Y = parseInt(thisStyle.paddingTop, 10) + 1;
			}
			else {
				for (b in bg) {
					if (bg.hasOwnProperty(b)) {
						this.figurePercentage(bg, size, b, thisStyle['backgroundPosition'+b]);
					}
				}
			}
			el.vml.image.fill.position = (bg.X/size.W) + ',' + (bg.Y/size.H);
			bgR = thisStyle.backgroundRepeat;
			dC = {'T':1, 'R':size.W+fudge, 'B':size.H, 'L':1+fudge}; /* these are defaults for repeat of any kind */
			altC = { 'X': {'b1': 'L', 'b2': 'R', 'd': 'W'}, 'Y': {'b1': 'T', 'b2': 'B', 'd': 'H'} };
			if (bgR != 'repeat' || el.isImg) {
				c = {'T':(bg.Y), 'R':(bg.X+size.w), 'B':(bg.Y+size.h), 'L':(bg.X)}; /* these are defaults for no-repeat - clips down to the image location */
				if (bgR.search('repeat-') != -1) { /* now let's revert to dC for repeat-x or repeat-y */
					v = bgR.split('repeat-')[1].toUpperCase();
					c[altC[v].b1] = 1;
					c[altC[v].b2] = size[altC[v].d];
				}
				if (c.B > size.H) {
					c.B = size.H;
				}
				el.vml.image.shape.style.clip = 'rect('+c.T+'px '+(c.R+fudge)+'px '+c.B+'px '+(c.L+fudge)+'px)';
			}
			else {
				el.vml.image.shape.style.clip = 'rect('+dC.T+'px '+dC.R+'px '+dC.B+'px '+dC.L+'px)';
			}
		},
		figurePercentage: function (bg, size, axis, position) {
			var horizontal, fraction;
			fraction = true;
			horizontal = (axis == 'X');
			switch(position) {
				case 'left':
				case 'top':
					bg[axis] = 0;
					break;
				case 'center':
					bg[axis] = 0.5;
					break;
				case 'right':
				case 'bottom':
					bg[axis] = 1;
					break;
				default:
					if (position.search('%') != -1) {
						bg[axis] = parseInt(position, 10) / 100;
					}
					else {
						fraction = false;
					}
			}
			bg[axis] = Math.ceil(  fraction ? ( (size[horizontal?'W': 'H'] * bg[axis]) - (size[horizontal?'w': 'h'] * bg[axis]) ) : parseInt(position, 10)  );
			if (bg[axis] % 2 === 0) {
				bg[axis]++;
			}
			return bg[axis];
		},
		fixPng: function (el) {
			el.style.behavior = 'none';
			var lib, els, nodeStr, v, e;
			if (el.nodeName == 'BODY' || el.nodeName == 'TD' || el.nodeName == 'TR') { /* elements not supported yet */
				return;
			}
			el.isImg = false;
			if (el.nodeName == 'IMG') {
				if(el.src.toLowerCase().search(/\.png$/) != -1) {
					el.isImg = true;
					el.style.visibility = 'hidden';
				}
				else {
					return;
				}
			}
			else if (el.currentStyle.backgroundImage.toLowerCase().search('.png') == -1) {
				return;
			}
			lib = DD_belatedPNG;
			el.vml = {color: {}, image: {}};
			els = {shape: {}, fill: {}};
			for (v in el.vml) {
				if (el.vml.hasOwnProperty(v)) {
					for (e in els) {
						if (els.hasOwnProperty(e)) {
							nodeStr = lib.ns + ':' + e;
							el.vml[v][e] = document.createElement(nodeStr);
						}
					}
					el.vml[v].shape.stroked = false;
					el.vml[v].shape.appendChild(el.vml[v].fill);
					el.parentNode.insertBefore(el.vml[v].shape, el);
				}
			}
			el.vml.image.shape.fillcolor = 'none'; /* Don't show blank white shapeangle when waiting for image to load. */
			el.vml.image.fill.type = 'tile'; /* Makes image show up. */
			el.vml.color.fill.on = false; /* Actually going to apply vml element's style.backgroundColor, so hide the whiteness. */
			lib.attachHandlers(el);
			lib.giveLayout(el);
			lib.giveLayout(el.offsetParent);
			el.vmlInitiated = true;
			lib.applyVML(el); /* Render! */
		}
	};
	try {
		document.execCommand("BackgroundImageCache", false, true); /* TredoSoft Multiple IE doesn't like this, so try{} it */
	} catch(r) {}
	DD_belatedPNG.createVmlNameSpace();
	DD_belatedPNG.createVmlStyleSheet();


}
/*
 * Date Format 1.2.3
 * (c) 2007-2009 Steven Levithan <stevenlevithan.com>
 * MIT license
 *
 * Includes enhancements by Scott Trenda <scott.trenda.net>
 * and Kris Kowal <cixar.com/~kris.kowal/>
 *
 * Accepts a date, a mask, or a date and a mask.
 * Returns a formatted version of the given date.
 * The date defaults to the current date/time.
 * The mask defaults to dateFormat.masks.default.
 */

var dateFormat = function () {
	var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
		timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
		timezoneClip = /[^-+\dA-Z]/g,
		pad = function (val, len) {
			val = String(val);
			len = len || 2;
			while (val.length < len) val = "0" + val;
			return val;
		};

	// Regexes and supporting functions are cached through closure
	return function (date, mask, utc) {
		var dF = dateFormat;

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   pad(H % 12 || 12),
				H:    H,
				HH:   pad(H),
				M:    M,
				MM:   pad(M),
				s:    s,
				ss:   pad(s),
				l:    pad(L, 3),
				L:    pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
			};

		return mask.replace(token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();

// Some common format strings
dateFormat.masks = {
	"default":      "ddd mmm dd yyyy HH:MM:ss",
	shortDate:      "m/d/yy",
	mediumDate:     "mmm d, yyyy",
	longDate:       "mmmm d, yyyy",
	fullDate:       "dddd, mmmm d, yyyy",
	shortTime:      "h:MM TT",
	mediumTime:     "h:MM:ss TT",
	longTime:       "h:MM:ss TT Z",
	isoDate:        "yyyy-mm-dd",
	isoTime:        "HH:MM:ss",
	isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
	dayNames: [
		"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
		"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
	],
	monthNames: [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
		"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
	return dateFormat(this, mask, utc);
};

/**
 * @link https://github.com/alexei/sprintf.js
 */
(function(ctx) {
	var sprintf = function() {
		if (!sprintf.cache.hasOwnProperty(arguments[0])) {
			sprintf.cache[arguments[0]] = sprintf.parse(arguments[0]);
		}
		return sprintf.format.call(null, sprintf.cache[arguments[0]], arguments);
	};

	sprintf.format = function(parse_tree, argv) {
		var cursor = 1, tree_length = parse_tree.length, node_type = '', arg, output = [], i, k, match, pad, pad_character, pad_length;
		for (i = 0; i < tree_length; i++) {
			node_type = get_type(parse_tree[i]);
			if (node_type === 'string') {
				output.push(parse_tree[i]);
			}
			else if (node_type === 'array') {
				match = parse_tree[i]; // convenience purposes only
				if (match[2]) { // keyword argument
					arg = argv[cursor];
					for (k = 0; k < match[2].length; k++) {
						if (!arg.hasOwnProperty(match[2][k])) {
							throw(sprintf('[sprintf] property "%s" does not exist', match[2][k]));
						}
						arg = arg[match[2][k]];
					}
				}
				else if (match[1]) { // positional argument (explicit)
					arg = argv[match[1]];
				}
				else { // positional argument (implicit)
					arg = argv[cursor++];
				}

				if (/[^s]/.test(match[8]) && (get_type(arg) != 'number')) {
					throw(sprintf('[sprintf] expecting number but found %s', get_type(arg)));
				}
				switch (match[8]) {
					case 'b': arg = arg.toString(2); break;
					case 'c': arg = String.fromCharCode(arg); break;
					case 'd': arg = parseInt(arg, 10); break;
					case 'e': arg = match[7] ? arg.toExponential(match[7]) : arg.toExponential(); break;
					case 'f': arg = match[7] ? parseFloat(arg).toFixed(match[7]) : parseFloat(arg); break;
					case 'o': arg = arg.toString(8); break;
					case 's': arg = ((arg = String(arg)) && match[7] ? arg.substring(0, match[7]) : arg); break;
					case 'u': arg = arg >>> 0; break;
					case 'x': arg = arg.toString(16); break;
					case 'X': arg = arg.toString(16).toUpperCase(); break;
				}
				arg = (/[def]/.test(match[8]) && match[3] && arg >= 0 ? '+'+ arg : arg);
				pad_character = match[4] ? match[4] == '0' ? '0' : match[4].charAt(1) : ' ';
				pad_length = match[6] - String(arg).length;
				pad = match[6] ? str_repeat(pad_character, pad_length) : '';
				output.push(match[5] ? arg + pad : pad + arg);
			}
		}
		return output.join('');
	};

	sprintf.cache = {};

	sprintf.parse = function(fmt) {
		var _fmt = fmt, match = [], parse_tree = [], arg_names = 0;
		while (_fmt) {
			if ((match = /^[^\x25]+/.exec(_fmt)) !== null) {
				parse_tree.push(match[0]);
			}
			else if ((match = /^\x25{2}/.exec(_fmt)) !== null) {
				parse_tree.push('%');
			}
			else if ((match = /^\x25(?:([1-9]\d*)\$|\(([^\)]+)\))?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(_fmt)) !== null) {
				if (match[2]) {
					arg_names |= 1;
					var field_list = [], replacement_field = match[2], field_match = [];
					if ((field_match = /^([a-z_][a-z_\d]*)/i.exec(replacement_field)) !== null) {
						field_list.push(field_match[1]);
						while ((replacement_field = replacement_field.substring(field_match[0].length)) !== '') {
							if ((field_match = /^\.([a-z_][a-z_\d]*)/i.exec(replacement_field)) !== null) {
								field_list.push(field_match[1]);
							}
							else if ((field_match = /^\[(\d+)\]/.exec(replacement_field)) !== null) {
								field_list.push(field_match[1]);
							}
							else {
								throw('[sprintf] huh?');
							}
						}
					}
					else {
						throw('[sprintf] huh?');
					}
					match[2] = field_list;
				}
				else {
					arg_names |= 2;
				}
				if (arg_names === 3) {
					throw('[sprintf] mixing positional and named placeholders is not (yet) supported');
				}
				parse_tree.push(match);
			}
			else {
				throw('[sprintf] huh?');
			}
			_fmt = _fmt.substring(match[0].length);
		}
		return parse_tree;
	};

	var vsprintf = function(fmt, argv, _argv) {
		_argv = argv.slice(0);
		_argv.splice(0, 0, fmt);
		return sprintf.apply(null, _argv);
	};

	/**
	 * helpers
	 */
	function get_type(variable) {
		return Object.prototype.toString.call(variable).slice(8, -1).toLowerCase();
	}

	function str_repeat(input, multiplier) {
		for (var output = []; multiplier > 0; output[--multiplier] = input) {/* do nothing */}
		return output.join('');
	}

	/**
	 * export to either browser or node.js
	 */
	ctx.sprintf = sprintf;
	ctx.vsprintf = vsprintf;
})(typeof exports != "undefined" ? exports : window);
if (!jQuery.browser.msie) {
	// Domain Public by Eric Wendelin http://eriwen.com/ (2008)
	//                  Luke Smith http://lucassmith.name/ (2008)
	//                  Loic Dachary <loic@dachary.org> (2008)
	//                  Johan Euphrosine <proppy@aminche.com> (2008)
	//                  Øyvind Sean Kinsey http://kinsey.no/blog (2010)
	//
	// Information and discussions
	// http://jspoker.pokersource.info/skin/test-printstacktrace.html
	// http://eriwen.com/javascript/js-stack-trace/
	// http://eriwen.com/javascript/stacktrace-update/
	// http://pastie.org/253058
	//
	// guessFunctionNameFromLines comes from firebug
	//
	// Software License Agreement (BSD License)
	//
	// Copyright (c) 2007, Parakey Inc.
	// All rights reserved.
	//
	// Redistribution and use of this software in source and binary forms, with or without modification,
	// are permitted provided that the following conditions are met:
	//
	// * Redistributions of source code must retain the above
	//   copyright notice, this list of conditions and the
	//   following disclaimer.
	//
	// * Redistributions in binary form must reproduce the above
	//   copyright notice, this list of conditions and the
	//   following disclaimer in the documentation and/or other
	//   materials provided with the distribution.
	//
	// * Neither the name of Parakey Inc. nor the names of its
	//   contributors may be used to endorse or promote products
	//   derived from this software without specific prior
	//   written permission of Parakey Inc.
	//
	// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR
	// IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
	// FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
	// CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
	// DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
	// DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER
	// IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT
	// OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	/**
	 * Main function giving a function stack trace with a forced or passed in Error
	 *
	 * @cfg {Error} e The error to create a stacktrace from (optional)
	 * @cfg {Boolean} guess If we should try to resolve the names of anonymous functions
	 * @return {Array} of Strings with functions, lines, files, and arguments where possible
	 */
	function printStackTrace(options) {
		var ex = (options && options.e) ? options.e : null;
		var guess = options ? !!options.guess : true;

		var p = new printStackTrace.implementation();
		var result = p.run(ex);
		return (guess) ? p.guessFunctions(result) : result;
	}

	printStackTrace.implementation = function() {};

	printStackTrace.implementation.prototype = {
		run: function(ex) {
			ex = ex ||
				(function() {
					try {
						var _err = __undef__ << 1;
					} catch (e) {
						return e;
					}
				})();
			// Use either the stored mode, or resolve it
			var mode = this._mode || this.mode(ex);
			if (mode === 'other') {
				return this.other(arguments.callee);
			} else {
				return this[mode](ex);
			}
		},

		/**
		 * @return {String} mode of operation for the environment in question.
		 */
		mode: function(e) {
			if (e['arguments']) {
				return (this._mode = 'chrome');
			} else if (window.opera && e.stacktrace) {
				return (this._mode = 'opera10');
			} else if (e.stack) {
				return (this._mode = 'firefox');
			} else if (window.opera && !('stacktrace' in e)) { //Opera 9-
				return (this._mode = 'opera');
			}
			return (this._mode = 'other');
		},

		/**
		 * Given a context, function name, and callback function, overwrite it so that it calls
		 * printStackTrace() first with a callback and then runs the rest of the body.
		 *
		 * @param {Object} context of execution (e.g. window)
		 * @param {String} functionName to instrument
		 * @param {Function} function to call with a stack trace on invocation
		 */
		instrumentFunction: function(context, functionName, callback) {
			context = context || window;
			context['_old' + functionName] = context[functionName];
			context[functionName] = function() {
				callback.call(this, printStackTrace());
				return context['_old' + functionName].apply(this, arguments);
			};
			context[functionName]._instrumented = true;
		},

		/**
		 * Given a context and function name of a function that has been
		 * instrumented, revert the function to it's original (non-instrumented)
		 * state.
		 *
		 * @param {Object} context of execution (e.g. window)
		 * @param {String} functionName to de-instrument
		 */
		deinstrumentFunction: function(context, functionName) {
			if (context[functionName].constructor === Function &&
					context[functionName]._instrumented &&
					context['_old' + functionName].constructor === Function) {
				context[functionName] = context['_old' + functionName];
			}
		},

		/**
		 * Given an Error object, return a formatted Array based on Chrome's stack string.
		 *
		 * @param e - Error object to inspect
		 * @return Array<String> of function calls, files and line numbers
		 */
		chrome: function(e) {
			return e.stack.replace(/^[^\(]+?[\n$]/gm, '').replace(/^\s+at\s+/gm, '').replace(/^Object.<anonymous>\s*\(/gm, '{anonymous}()@').split('\n');
		},

		/**
		 * Given an Error object, return a formatted Array based on Firefox's stack string.
		 *
		 * @param e - Error object to inspect
		 * @return Array<String> of function calls, files and line numbers
		 */
		firefox: function(e) {
			return e.stack.replace(/(?:\n@:0)?\s+$/m, '').replace(/^\(/gm, '{anonymous}(').split('\n');
		},

		/**
		 * Given an Error object, return a formatted Array based on Opera 10's stacktrace string.
		 *
		 * @param e - Error object to inspect
		 * @return Array<String> of function calls, files and line numbers
		 */
		opera10: function(e) {
			var stack = e.stacktrace;
			var lines = stack.split('\n'), ANON = '{anonymous}',
				lineRE = /.*line (\d+), column (\d+) in ((<anonymous function\:?\s*(\S+))|([^\(]+)\([^\)]*\))(?: in )?(.*)\s*$/i, i, j, len;
			for (i = 2, j = 0, len = lines.length; i < len - 2; i++) {
				if (lineRE.test(lines[i])) {
					var location = RegExp.$6 + ':' + RegExp.$1 + ':' + RegExp.$2;
					var fnName = RegExp.$3;
					fnName = fnName.replace(/<anonymous function\:?\s?(\S+)?>/g, ANON);
					lines[j++] = fnName + '@' + location;
				}
			}

			lines.splice(j, lines.length - j);
			return lines;
		},

		// Opera 7.x-9.x only!
		opera: function(e) {
			var lines = e.message.split('\n'), ANON = '{anonymous}',
				lineRE = /Line\s+(\d+).*script\s+(http\S+)(?:.*in\s+function\s+(\S+))?/i,
				i, j, len;

			for (i = 4, j = 0, len = lines.length; i < len; i += 2) {
				//TODO: RegExp.exec() would probably be cleaner here
				if (lineRE.test(lines[i])) {
					lines[j++] = (RegExp.$3 ? RegExp.$3 + '()@' + RegExp.$2 + RegExp.$1 : ANON + '()@' + RegExp.$2 + ':' + RegExp.$1) + ' -- ' + lines[i + 1].replace(/^\s+/, '');
				}
			}

			lines.splice(j, lines.length - j);
			return lines;
		},

		// Safari, IE, and others
		other: function(curr) {
			var ANON = '{anonymous}', fnRE = /function\s*([\w\-$]+)?\s*\(/i,
				stack = [], j = 0, fn, args;

			var maxStackSize = 10;
			while (curr && stack.length < maxStackSize) {
				fn = fnRE.test(curr.toString()) ? RegExp.$1 || ANON : ANON;
				args = Array.prototype.slice.call(curr['arguments']);
				stack[j++] = fn + '(' + this.stringifyArguments(args) + ')';
				curr = curr.caller;
			}
			return stack;
		},

		/**
		 * Given arguments array as a String, subsituting type names for non-string types.
		 *
		 * @param {Arguments} object
		 * @return {Array} of Strings with stringified arguments
		 */
		stringifyArguments: function(args) {
			for (var i = 0; i < args.length; ++i) {
				var arg = args[i];
				if (arg === undefined) {
					args[i] = 'undefined';
				} else if (arg === null) {
					args[i] = 'null';
				} else if (arg.constructor) {
					if (arg.constructor === Array) {
						if (arg.length < 3) {
							args[i] = '[' + this.stringifyArguments(arg) + ']';
						} else {
							args[i] = '[' + this.stringifyArguments(Array.prototype.slice.call(arg, 0, 1)) + '...' + this.stringifyArguments(Array.prototype.slice.call(arg, -1)) + ']';
						}
					} else if (arg.constructor === Object) {
						args[i] = '#object';
					} else if (arg.constructor === Function) {
						args[i] = '#function';
					} else if (arg.constructor === String) {
						args[i] = '"' + arg + '"';
					}
				}
			}
			return args.join(',');
		},

		sourceCache: {},

		/**
		 * @return the text from a given URL.
		 */
		ajax: function(url) {
			var req = this.createXMLHTTPObject();
			if (!req) {
				return;
			}
			req.open('GET', url, false);
			req.setRequestHeader('User-Agent', 'XMLHTTP/1.0');
			req.send('');
			return req.responseText;
		},

		/**
		 * Try XHR methods in order and store XHR factory.
		 *
		 * @return <Function> XHR function or equivalent
		 */
		createXMLHTTPObject: function() {
			var xmlhttp, XMLHttpFactories = [
				function() {
					return new XMLHttpRequest();
				}, function() {
					return new ActiveXObject('Msxml2.XMLHTTP');
				}, function() {
					return new ActiveXObject('Msxml3.XMLHTTP');
				}, function() {
					return new ActiveXObject('Microsoft.XMLHTTP');
				}
			];
			for (var i = 0; i < XMLHttpFactories.length; i++) {
				try {
					xmlhttp = XMLHttpFactories[i]();
					// Use memoization to cache the factory
					this.createXMLHTTPObject = XMLHttpFactories[i];
					return xmlhttp;
				} catch (e) {}
			}
		},

		/**
		 * Given a URL, check if it is in the same domain (so we can get the source
		 * via Ajax).
		 *
		 * @param url <String> source url
		 * @return False if we need a cross-domain request
		 */
		isSameDomain: function(url) {
			return url.indexOf(location.hostname) !== -1;
		},

		/**
		 * Get source code from given URL if in the same domain.
		 *
		 * @param url <String> JS source URL
		 * @return <String> Source code
		 */
		getSource: function(url) {
			if (!(url in this.sourceCache)) {
				this.sourceCache[url] = this.ajax(url).split('\n');
			}
			return this.sourceCache[url];
		},

		guessFunctions: function(stack) {
			for (var i = 0; i < stack.length; ++i) {
				var reStack = /\{anonymous\}\(.*\)@(\w+:\/\/([\-\w\.]+)+(:\d+)?[^:]+):(\d+):?(\d+)?/;
				var frame = stack[i], m = reStack.exec(frame);
				if (m) {
					var file = m[1], lineno = m[4]; //m[7] is character position in Chrome
					if (file && this.isSameDomain(file) && lineno) {
						var functionName = this.guessFunctionName(file, lineno);
						stack[i] = frame.replace('{anonymous}', functionName);
					}
				}
			}
			return stack;
		},

		guessFunctionName: function(url, lineNo) {
			try {
				return this.guessFunctionNameFromLines(lineNo, this.getSource(url));
			} catch (e) {
				return 'getSource failed with url: ' + url + ', exception: ' + e.toString();
			}
		},

		guessFunctionNameFromLines: function(lineNo, source) {
			var reFunctionArgNames = /function ([^(]*)\(([^)]*)\)/;
			var reGuessFunction = /['"]?([0-9A-Za-z_]+)['"]?\s*[:=]\s*(function|eval|new Function)/;
			// Walk backwards from the first line in the function until we find the line which
			// matches the pattern above, which is the function definition
			var line = "", maxLines = 10;
			for (var i = 0; i < maxLines; ++i) {
				line = source[lineNo - i] + line;
				if (line !== undefined) {
					var m = reGuessFunction.exec(line);
					if (m && m[1]) {
						return m[1];
					} else {
						m = reFunctionArgNames.exec(line);
						if (m && m[1]) {
							return m[1];
						}
					}
				}
			}
			return '(?)';
		}
	};


}


;(function($){
    $.fn.alignBottom = function() 
    {

        var defaults = {
            outerHight: 0,
            elementHeight: 0
        };

        var options = $.extend(defaults, options);
        var bpHeight = 0; // Border + padding

        return this.each(function() 
        {
            options.outerHight = $(this).parent().outerHeight();
            bpHeight = options.outerHight - $(this).parent().height();
            options.elementHeight = $(this).outerHeight(true) + bpHeight;


            $(this).css({'position':'relative','top':options.outerHight-(options.elementHeight)+'px'});
        });
    };
})(jQuery);/*!
 * jQuery Cookie Plugin v1.3.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
;(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals.
		factory(jQuery);
	}
}(function ($) {

	var pluses = /\+/g;

	function raw(s) {
		return s;
	}

	function decoded(s) {
		return decodeURIComponent(s.replace(pluses, ' '));
	}

	function converted(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}
		try {
			return config.json ? JSON.parse(s) : s;
		} catch(er) {}
	}

	var config = $.cookie = function (key, value, options) {

		// write
		if (value !== undefined) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = config.json ? JSON.stringify(value) : String(value);

			return (document.cookie = [
				config.raw ? key : encodeURIComponent(key),
				'=',
				config.raw ? value : encodeURIComponent(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// read
		var decode = config.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		var result = key ? undefined : {};
		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = decode(parts.join('='));

			if (key && key === name) {
				result = converted(cookie);
				break;
			}

			if (!key) {
				result[name] = converted(cookie);
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) !== undefined) {
			// Must not alter options, thus extending a fresh object...
			$.cookie(key, '', $.extend({}, options, { expires: -1 }));
			return true;
		}
		return false;
	};

}));
/*!
 * fancyBox - jQuery Plugin
 * version: 2.1.4 (Thu, 10 Jan 2013)
 * @requires jQuery v1.6 or later
 *
 * Examples at http://fancyapps.com/fancybox/
 * License: www.fancyapps.com/fancybox/#license
 *
 * Copyright 2012 Janis Skarnelis - janis@fancyapps.com
 *
 */

;(function (window, document, $, undefined) {
	"use strict";

	var W = $(window),
		D = $(document),
		F = $.fancybox = function () {
			F.open.apply( this, arguments );
		},
		IE =  navigator.userAgent.match(/msie/),
		didUpdate = null,
		isTouch	  = document.createTouch !== undefined,

		isQuery	= function(obj) {
			return obj && obj.hasOwnProperty && obj instanceof $;
		},
		isString = function(str) {
			return str && $.type(str) === "string";
		},
		isPercentage = function(str) {
			return isString(str) && str.indexOf('%') > 0;
		},
		isScrollable = function(el) {
			return (el && !(el.style.overflow && el.style.overflow === 'hidden') && ((el.clientWidth && el.scrollWidth > el.clientWidth) || (el.clientHeight && el.scrollHeight > el.clientHeight)));
		},
		getScalar = function(orig, dim) {
			var value = parseInt(orig, 10) || 0;

			if (dim && isPercentage(orig)) {
				value = F.getViewport()[ dim ] / 100 * value;
			}

			return Math.ceil(value);
		},
		getValue = function(value, dim) {
			return getScalar(value, dim) + 'px';
		};

	$.extend(F, {
		// The current version of fancyBox
		version: '2.1.4',

		defaults: {
			padding : 15,
			margin  : 20,

			width     : 800,
			height    : 600,
			minWidth  : 100,
			minHeight : 100,
			maxWidth  : 9999,
			maxHeight : 9999,

			autoSize   : true,
			autoHeight : false,
			autoWidth  : false,

			autoResize  : true,
			autoCenter  : !isTouch,
			fitToView   : true,
			aspectRatio : false,
			topRatio    : 0.5,
			leftRatio   : 0.5,

			scrolling : 'auto', // 'auto', 'yes' or 'no'
			wrapCSS   : '',

			arrows     : true,
			closeBtn   : true,
			closeClick : false,
			nextClick  : false,
			mouseWheel : true,
			autoPlay   : false,
			playSpeed  : 3000,
			preload    : 3,
			modal      : false,
			loop       : true,

			ajax  : {
				dataType : 'html',
				headers  : { 'X-fancyBox': true }
			},
			iframe : {
				scrolling : 'auto',
				preload   : true
			},
			swf : {
				wmode: 'transparent',
				allowfullscreen   : 'true',
				allowscriptaccess : 'always'
			},

			keys  : {
				next : {
					13 : 'left', // enter
					34 : 'up',   // page down
					39 : 'left', // right arrow
					40 : 'up'    // down arrow
				},
				prev : {
					8  : 'right',  // backspace
					33 : 'down',   // page up
					37 : 'right',  // left arrow
					38 : 'down'    // up arrow
				},
				close  : [27], // escape key
				play   : [32], // space - start/stop slideshow
				toggle : [70]  // letter "f" - toggle fullscreen
			},

			direction : {
				next : 'left',
				prev : 'right'
			},

			scrollOutside  : true,

			// Override some properties
			index   : 0,
			type    : null,
			href    : null,
			content : null,
			title   : null,

			// HTML templates
			tpl: {
				wrap     : '<div class="fancybox-wrap" tabIndex="-1"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>',
				image    : '<img class="fancybox-image" src="{href}" alt="" />',
				iframe   : '<iframe id="fancybox-frame{rnd}" name="fancybox-frame{rnd}" class="fancybox-iframe" frameborder="0" vspace="0" hspace="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen' + (IE ? ' allowtransparency="true"' : '') + '></iframe>',
				error    : '<p class="fancybox-error">The requested content cannot be loaded.<br/>Please try again later.</p>',
				closeBtn : '<a title="Close" class="fancybox-item fancybox-close" href="javascript:;"></a>',
				next     : '<a title="Next" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
				prev     : '<a title="Previous" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
			},

			// Properties for each animation type
			// Opening fancyBox
			openEffect  : 'fade', // 'elastic', 'fade' or 'none'
			openSpeed   : 250,
			openEasing  : 'swing',
			openOpacity : true,
			openMethod  : 'zoomIn',

			// Closing fancyBox
			closeEffect  : 'fade', // 'elastic', 'fade' or 'none'
			closeSpeed   : 250,
			closeEasing  : 'swing',
			closeOpacity : true,
			closeMethod  : 'zoomOut',

			// Changing next gallery item
			nextEffect : 'elastic', // 'elastic', 'fade' or 'none'
			nextSpeed  : 250,
			nextEasing : 'swing',
			nextMethod : 'changeIn',

			// Changing previous gallery item
			prevEffect : 'elastic', // 'elastic', 'fade' or 'none'
			prevSpeed  : 250,
			prevEasing : 'swing',
			prevMethod : 'changeOut',

			// Enable default helpers
			helpers : {
				overlay : true,
				title   : true
			},

			// Callbacks
			onCancel     : $.noop, // If canceling
			beforeLoad   : $.noop, // Before loading
			afterLoad    : $.noop, // After loading
			beforeShow   : $.noop, // Before changing in current item
			afterShow    : $.noop, // After opening
			beforeChange : $.noop, // Before changing gallery item
			beforeClose  : $.noop, // Before closing
			afterClose   : $.noop  // After closing
		},

		//Current state
		group    : {}, // Selected group
		opts     : {}, // Group options
		previous : null,  // Previous element
		coming   : null,  // Element being loaded
		current  : null,  // Currently loaded element
		isActive : false, // Is activated
		isOpen   : false, // Is currently open
		isOpened : false, // Have been fully opened at least once

		wrap  : null,
		skin  : null,
		outer : null,
		inner : null,

		player : {
			timer    : null,
			isActive : false
		},

		// Loaders
		ajaxLoad   : null,
		imgPreload : null,

		// Some collections
		transitions : {},
		helpers     : {},

		/*
		 *	Static methods
		 */

		open: function (group, opts) {
			if (!group) {
				return;
			}

			if (!$.isPlainObject(opts)) {
				opts = {};
			}

			// Close if already active
			if (false === F.close(true)) {
				return;
			}

			// Normalize group
			if (!$.isArray(group)) {
				group = isQuery(group) ? $(group).get() : [group];
			}

			// Recheck if the type of each element is `object` and set content type (image, ajax, etc)
			$.each(group, function(i, element) {
				var obj = {},
					href,
					title,
					content,
					type,
					rez,
					hrefParts,
					selector;

				if ($.type(element) === "object") {
					// Check if is DOM element
					if (element.nodeType) {
						element = $(element);
					}

					if (isQuery(element)) {
						obj = {
							href    : element.data('fancybox-href') || element.attr('href'),
							title   : element.data('fancybox-title') || element.attr('title'),
							isDom   : true,
							element : element
						};

						if ($.metadata) {
							$.extend(true, obj, element.metadata());
						}

					} else {
						obj = element;
					}
				}

				href  = opts.href  || obj.href || (isString(element) ? element : null);
				title = opts.title !== undefined ? opts.title : obj.title || '';

				content = opts.content || obj.content;
				type    = content ? 'html' : (opts.type  || obj.type);

				if (!type && obj.isDom) {
					type = element.data('fancybox-type');

					if (!type) {
						rez  = element.prop('class').match(/fancybox\.(\w+)/);
						type = rez ? rez[1] : null;
					}
				}

				if (isString(href)) {
					// Try to guess the content type
					if (!type) {
						if (F.isImage(href)) {
							type = 'image';

						} else if (F.isSWF(href)) {
							type = 'swf';

						} else if (href.charAt(0) === '#') {
							type = 'inline';

						} else if (isString(element)) {
							type    = 'html';
							content = element;
						}
					}

					// Split url into two pieces with source url and content selector, e.g,
					// "/mypage.html #my_id" will load "/mypage.html" and display element having id "my_id"
					if (type === 'ajax') {
						hrefParts = href.split(/\s+/, 2);
						href      = hrefParts.shift();
						selector  = hrefParts.shift();
					}
				}

				if (!content) {
					if (type === 'inline') {
						if (href) {
							content = $( isString(href) ? href.replace(/.*(?=#[^\s]+$)/, '') : href ); //strip for ie7

						} else if (obj.isDom) {
							content = element;
						}

					} else if (type === 'html') {
						content = href;

					} else if (!type && !href && obj.isDom) {
						type    = 'inline';
						content = element;
					}
				}

				$.extend(obj, {
					href     : href,
					type     : type,
					content  : content,
					title    : title,
					selector : selector
				});

				group[ i ] = obj;
			});

			// Extend the defaults
			F.opts = $.extend(true, {}, F.defaults, opts);

			// All options are merged recursive except keys
			if (opts.keys !== undefined) {
				F.opts.keys = opts.keys ? $.extend({}, F.defaults.keys, opts.keys) : false;
			}

			F.group = group;

			return F._start(F.opts.index);
		},

		// Cancel image loading or abort ajax request
		cancel: function () {
			var coming = F.coming;

			if (!coming || false === F.trigger('onCancel')) {
				return;
			}

			F.hideLoading();

			if (F.ajaxLoad) {
				F.ajaxLoad.abort();
			}

			F.ajaxLoad = null;

			if (F.imgPreload) {
				F.imgPreload.onload = F.imgPreload.onerror = null;
			}

			if (coming.wrap) {
				coming.wrap.stop(true, true).trigger('onReset').remove();
			}

			F.coming = null;

			// If the first item has been canceled, then clear everything
			if (!F.current) {
				F._afterZoomOut( coming );
			}
		},

		// Start closing animation if is open; remove immediately if opening/closing
		close: function (event) {
			F.cancel();

			if (false === F.trigger('beforeClose')) {
				return;
			}

			F.unbindEvents();

			if (!F.isActive) {
				return;
			}

			if (!F.isOpen || event === true) {
				$('.fancybox-wrap').stop(true).trigger('onReset').remove();

				F._afterZoomOut();

			} else {
				F.isOpen = F.isOpened = false;
				F.isClosing = true;

				$('.fancybox-item, .fancybox-nav').remove();

				F.wrap.stop(true, true).removeClass('fancybox-opened');

				F.transitions[ F.current.closeMethod ]();
			}
		},

		// Manage slideshow:
		//   $.fancybox.play(); - toggle slideshow
		//   $.fancybox.play( true ); - start
		//   $.fancybox.play( false ); - stop
		play: function ( action ) {
			var clear = function () {
					clearTimeout(F.player.timer);
				},
				set = function () {
					clear();

					if (F.current && F.player.isActive) {
						F.player.timer = setTimeout(F.next, F.current.playSpeed);
					}
				},
				stop = function () {
					clear();

					$('body').unbind('.player');

					F.player.isActive = false;

					F.trigger('onPlayEnd');
				},
				start = function () {
					if (F.current && (F.current.loop || F.current.index < F.group.length - 1)) {
						F.player.isActive = true;

						$('body').bind({
							'afterShow.player onUpdate.player'   : set,
							'onCancel.player beforeClose.player' : stop,
							'beforeLoad.player' : clear
						});

						set();

						F.trigger('onPlayStart');
					}
				};

			if (action === true || (!F.player.isActive && action !== false)) {
				start();
			} else {
				stop();
			}
		},

		// Navigate to next gallery item
		next: function ( direction ) {
			var current = F.current;

			if (current) {
				if (!isString(direction)) {
					direction = current.direction.next;
				}

				F.jumpto(current.index + 1, direction, 'next');
			}
		},

		// Navigate to previous gallery item
		prev: function ( direction ) {
			var current = F.current;

			if (current) {
				if (!isString(direction)) {
					direction = current.direction.prev;
				}

				F.jumpto(current.index - 1, direction, 'prev');
			}
		},

		// Navigate to gallery item by index
		jumpto: function ( index, direction, router ) {
			var current = F.current;

			if (!current) {
				return;
			}

			index = getScalar(index);

			F.direction = direction || current.direction[ (index >= current.index ? 'next' : 'prev') ];
			F.router    = router || 'jumpto';

			if (current.loop) {
				if (index < 0) {
					index = current.group.length + (index % current.group.length);
				}

				index = index % current.group.length;
			}

			if (current.group[ index ] !== undefined) {
				F.cancel();

				F._start(index);
			}
		},

		// Center inside viewport and toggle position type to fixed or absolute if needed
		reposition: function (e, onlyAbsolute) {
			var current = F.current,
				wrap    = current ? current.wrap : null,
				pos;

			if (wrap) {
				pos = F._getPosition(onlyAbsolute);

				if (e && e.type === 'scroll') {
					delete pos.position;

					wrap.stop(true, true).animate(pos, 200);

				} else {
					wrap.css(pos);

					current.pos = $.extend({}, current.dim, pos);
				}
			}
		},

		update: function (e) {
			var type = (e && e.type),
				anyway = !type || type === 'orientationchange';

			if (anyway) {
				clearTimeout(didUpdate);

				didUpdate = null;
			}

			if (!F.isOpen || didUpdate) {
				return;
			}

			didUpdate = setTimeout(function() {
				var current = F.current;

				if (!current || F.isClosing) {
					return;
				}

				F.wrap.removeClass('fancybox-tmp');

				if (anyway || type === 'load' || (type === 'resize' && current.autoResize)) {
					F._setDimension();
				}

				if (!(type === 'scroll' && current.canShrink)) {
					F.reposition(e);
				}

				F.trigger('onUpdate');

				didUpdate = null;

			}, (anyway && !isTouch ? 0 : 300));
		},

		// Shrink content to fit inside viewport or restore if resized
		toggle: function ( action ) {
			if (F.isOpen) {
				F.current.fitToView = $.type(action) === "boolean" ? action : !F.current.fitToView;

				// Help browser to restore document dimensions
				if (isTouch) {
					F.wrap.removeAttr('style').addClass('fancybox-tmp');

					F.trigger('onUpdate');
				}

				F.update();
			}
		},

		hideLoading: function () {
			D.unbind('.loading');

			$('#fancybox-loading').remove();
		},

		showLoading: function () {
			var el, viewport;

			F.hideLoading();

			el = $('<div id="fancybox-loading"><div></div></div>').click(F.cancel).appendTo('body');

			// If user will press the escape-button, the request will be canceled
			D.bind('keydown.loading', function(e) {
				if ((e.which || e.keyCode) === 27) {
					e.preventDefault();

					F.cancel();
				}
			});

			if (!F.defaults.fixed) {
				viewport = F.getViewport();

				el.css({
					position : 'absolute',
					top  : (viewport.h * 0.5) + viewport.y,
					left : (viewport.w * 0.5) + viewport.x
				});
			}
		},

		getViewport: function () {
			var locked = (F.current && F.current.locked) || false,
				rez    = {
					x: W.scrollLeft(),
					y: W.scrollTop()
				};

			if (locked) {
				rez.w = locked[0].clientWidth;
				rez.h = locked[0].clientHeight;

			} else {
				// See http://bugs.jquery.com/ticket/6724
				rez.w = isTouch && window.innerWidth  ? window.innerWidth  : W.width();
				rez.h = isTouch && window.innerHeight ? window.innerHeight : W.height();
			}

			return rez;
		},

		// Unbind the keyboard / clicking actions
		unbindEvents: function () {
			if (F.wrap && isQuery(F.wrap)) {
				F.wrap.unbind('.fb');
			}

			D.unbind('.fb');
			W.unbind('.fb');
		},

		bindEvents: function () {
			var current = F.current,
				keys;

			if (!current) {
				return;
			}

			// Changing document height on iOS devices triggers a 'resize' event,
			// that can change document height... repeating infinitely
			W.bind('orientationchange.fb' + (isTouch ? '' : ' resize.fb') + (current.autoCenter && !current.locked ? ' scroll.fb' : ''), F.update);

			keys = current.keys;

			if (keys) {
				D.bind('keydown.fb', function (e) {
					var code   = e.which || e.keyCode,
						target = e.target || e.srcElement;

					// Skip esc key if loading, because showLoading will cancel preloading
					if (code === 27 && F.coming) {
						return false;
					}

					// Ignore key combinations and key events within form elements
					if (!e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey && !(target && (target.type || $(target).is('[contenteditable]')))) {
						$.each(keys, function(i, val) {
							if (current.group.length > 1 && val[ code ] !== undefined) {
								F[ i ]( val[ code ] );

								e.preventDefault();
								return false;
							}

							if ($.inArray(code, val) > -1) {
								F[ i ] ();

								e.preventDefault();
								return false;
							}
						});
					}
				});
			}

			if ($.fn.mousewheel && current.mouseWheel) {
				F.wrap.bind('mousewheel.fb', function (e, delta, deltaX, deltaY) {
					var target = e.target || null,
						parent = $(target),
						canScroll = false;

					while (parent.length) {
						if (canScroll || parent.is('.fancybox-skin') || parent.is('.fancybox-wrap')) {
							break;
						}

						canScroll = isScrollable( parent[0] );
						parent    = $(parent).parent();
					}

					if (delta !== 0 && !canScroll) {
						if (F.group.length > 1 && !current.canShrink) {
							if (deltaY > 0 || deltaX > 0) {
								F.prev( deltaY > 0 ? 'down' : 'left' );

							} else if (deltaY < 0 || deltaX < 0) {
								F.next( deltaY < 0 ? 'up' : 'right' );
							}

							e.preventDefault();
						}
					}
				});
			}
		},

		trigger: function (event, o) {
			var ret, obj = o || F.coming || F.current;

			if (!obj) {
				return;
			}

			if ($.isFunction( obj[event] )) {
				ret = obj[event].apply(obj, Array.prototype.slice.call(arguments, 1));
			}

			if (ret === false) {
				return false;
			}

			if (obj.helpers) {
				$.each(obj.helpers, function (helper, opts) {
					if (opts && F.helpers[helper] && $.isFunction(F.helpers[helper][event])) {
						opts = $.extend(true, {}, F.helpers[helper].defaults, opts);

						F.helpers[helper][event](opts, obj);
					}
				});
			}

			$.event.trigger(event + '.fb');
		},

		isImage: function (str) {
			return isString(str) && str.match(/(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp)((\?|#).*)?$)/i);
		},

		isSWF: function (str) {
			return isString(str) && str.match(/\.(swf)((\?|#).*)?$/i);
		},

		_start: function (index) {
			var coming = {},
				obj,
				href,
				type,
				margin,
				padding;

			index = getScalar( index );
			obj   = F.group[ index ] || null;

			if (!obj) {
				return false;
			}

			coming = $.extend(true, {}, F.opts, obj);

			// Convert margin and padding properties to array - top, right, bottom, left
			margin  = coming.margin;
			padding = coming.padding;

			if ($.type(margin) === 'number') {
				coming.margin = [margin, margin, margin, margin];
			}

			if ($.type(padding) === 'number') {
				coming.padding = [padding, padding, padding, padding];
			}

			// 'modal' propery is just a shortcut
			if (coming.modal) {
				$.extend(true, coming, {
					closeBtn   : false,
					closeClick : false,
					nextClick  : false,
					arrows     : false,
					mouseWheel : false,
					keys       : null,
					helpers: {
						overlay : {
							closeClick : false
						}
					}
				});
			}

			// 'autoSize' property is a shortcut, too
			if (coming.autoSize) {
				coming.autoWidth = coming.autoHeight = true;
			}

			if (coming.width === 'auto') {
				coming.autoWidth = true;
			}

			if (coming.height === 'auto') {
				coming.autoHeight = true;
			}

			/*
			 * Add reference to the group, so it`s possible to access from callbacks, example:
			 * afterLoad : function() {
			 *     this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
			 * }
			 */

			coming.group  = F.group;
			coming.index  = index;

			// Give a chance for callback or helpers to update coming item (type, title, etc)
			F.coming = coming;

			if (false === F.trigger('beforeLoad')) {
				F.coming = null;

				return;
			}

			type = coming.type;
			href = coming.href;

			if (!type) {
				F.coming = null;

				//If we can not determine content type then drop silently or display next/prev item if looping through gallery
				if (F.current && F.router && F.router !== 'jumpto') {
					F.current.index = index;

					return F[ F.router ]( F.direction );
				}

				return false;
			}

			F.isActive = true;

			if (type === 'image' || type === 'swf') {
				coming.autoHeight = coming.autoWidth = false;
				coming.scrolling  = 'visible';
			}

			if (type === 'image') {
				coming.aspectRatio = true;
			}

			if (type === 'iframe' && isTouch) {
				coming.scrolling = 'scroll';
			}

			// Build the neccessary markup
			coming.wrap = $(coming.tpl.wrap).addClass('fancybox-' + (isTouch ? 'mobile' : 'desktop') + ' fancybox-type-' + type + ' fancybox-tmp ' + coming.wrapCSS).appendTo( coming.parent || 'body' );

			$.extend(coming, {
				skin  : $('.fancybox-skin',  coming.wrap),
				outer : $('.fancybox-outer', coming.wrap),
				inner : $('.fancybox-inner', coming.wrap)
			});

			$.each(["Top", "Right", "Bottom", "Left"], function(i, v) {
				coming.skin.css('padding' + v, getValue(coming.padding[ i ]));
			});

			F.trigger('onReady');

			// Check before try to load; 'inline' and 'html' types need content, others - href
			if (type === 'inline' || type === 'html') {
				if (!coming.content || !coming.content.length) {
					return F._error( 'content' );
				}

			} else if (!href) {
				return F._error( 'href' );
			}

			if (type === 'image') {
				F._loadImage();

			} else if (type === 'ajax') {
				F._loadAjax();

			} else if (type === 'iframe') {
				F._loadIframe();

			} else {
				F._afterLoad();
			}
		},

		_error: function ( type ) {
			$.extend(F.coming, {
				type       : 'html',
				autoWidth  : true,
				autoHeight : true,
				minWidth   : 0,
				minHeight  : 0,
				scrolling  : 'no',
				hasError   : type,
				content    : F.coming.tpl.error
			});

			F._afterLoad();
		},

		_loadImage: function () {
			// Reset preload image so it is later possible to check "complete" property
			var img = F.imgPreload = new Image();

			img.onload = function () {
				this.onload = this.onerror = null;

				F.coming.width  = this.width;
				F.coming.height = this.height;

				F._afterLoad();
			};

			img.onerror = function () {
				this.onload = this.onerror = null;

				F._error( 'image' );
			};

			img.src = F.coming.href;

			if (img.complete !== true) {
				F.showLoading();
			}
		},

		_loadAjax: function () {
			var coming = F.coming;

			F.showLoading();

			F.ajaxLoad = $.ajax($.extend({}, coming.ajax, {
				url: coming.href,
				error: function (jqXHR, textStatus) {
					if (F.coming && textStatus !== 'abort') {
						F._error( 'ajax', jqXHR );

					} else {
						F.hideLoading();
					}
				},
				success: function (data, textStatus) {
					if (textStatus === 'success') {
						coming.content = data;

						F._afterLoad();
					}
				}
			}));
		},

		_loadIframe: function() {
			var coming = F.coming,
				iframe = $(coming.tpl.iframe.replace(/\{rnd\}/g, new Date().getTime()))
					.attr('scrolling', isTouch ? 'auto' : coming.iframe.scrolling)
					.attr('src', coming.href);

			// This helps IE
			$(coming.wrap).bind('onReset', function () {
				try {
					$(this).find('iframe').hide().attr('src', '//about:blank').end().empty();
				} catch (e) {}
			});

			if (coming.iframe.preload) {
				F.showLoading();

				iframe.one('load', function() {
					$(this).data('ready', 1);

					// iOS will lose scrolling if we resize
					if (!isTouch) {
						$(this).bind('load.fb', F.update);
					}

					// Without this trick:
					//   - iframe won't scroll on iOS devices
					//   - IE7 sometimes displays empty iframe
					$(this).parents('.fancybox-wrap').width('100%').removeClass('fancybox-tmp').show();

					F._afterLoad();
				});
			}

			coming.content = iframe.appendTo( coming.inner );

			if (!coming.iframe.preload) {
				F._afterLoad();
			}
		},

		_preloadImages: function() {
			var group   = F.group,
				current = F.current,
				len     = group.length,
				cnt     = current.preload ? Math.min(current.preload, len - 1) : 0,
				item,
				i;

			for (i = 1; i <= cnt; i += 1) {
				item = group[ (current.index + i ) % len ];

				if (item.type === 'image' && item.href) {
					new Image().src = item.href;
				}
			}
		},

		_afterLoad: function () {
			var coming   = F.coming,
				previous = F.current,
				placeholder = 'fancybox-placeholder',
				current,
				content,
				type,
				scrolling,
				href,
				embed;

			F.hideLoading();

			if (!coming || F.isActive === false) {
				return;
			}

			if (false === F.trigger('afterLoad', coming, previous)) {
				coming.wrap.stop(true).trigger('onReset').remove();

				F.coming = null;

				return;
			}

			if (previous) {
				F.trigger('beforeChange', previous);

				previous.wrap.stop(true).removeClass('fancybox-opened')
					.find('.fancybox-item, .fancybox-nav')
					.remove();
			}

			F.unbindEvents();

			current   = coming;
			content   = coming.content;
			type      = coming.type;
			scrolling = coming.scrolling;

			$.extend(F, {
				wrap  : current.wrap,
				skin  : current.skin,
				outer : current.outer,
				inner : current.inner,
				current  : current,
				previous : previous
			});

			href = current.href;

			switch (type) {
				case 'inline':
				case 'ajax':
				case 'html':
					if (current.selector) {
						content = $('<div>').html(content).find(current.selector);

					} else if (isQuery(content)) {
						if (!content.data(placeholder)) {
							content.data(placeholder, $('<div class="' + placeholder + '"></div>').insertAfter( content ).hide() );
						}

						content = content.show().detach();

						current.wrap.bind('onReset', function () {
							if ($(this).find(content).length) {
								content.hide().replaceAll( content.data(placeholder) ).data(placeholder, false);
							}
						});
					}
				break;

				case 'image':
					content = current.tpl.image.replace('{href}', href);
				break;

				case 'swf':
					content = '<object id="fancybox-swf" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="movie" value="' + href + '"></param>';
					embed   = '';

					$.each(current.swf, function(name, val) {
						content += '<param name="' + name + '" value="' + val + '"></param>';
						embed   += ' ' + name + '="' + val + '"';
					});

					content += '<embed src="' + href + '" type="application/x-shockwave-flash" width="100%" height="100%"' + embed + '></embed></object>';
				break;
			}

			if (!(isQuery(content) && content.parent().is(current.inner))) {
				current.inner.append( content );
			}

			// Give a chance for helpers or callbacks to update elements
			F.trigger('beforeShow');

			// Set scrolling before calculating dimensions
			current.inner.css('overflow', scrolling === 'yes' ? 'scroll' : (scrolling === 'no' ? 'hidden' : scrolling));

			// Set initial dimensions and start position
			F._setDimension();

			F.reposition();

			F.isOpen = false;
			F.coming = null;

			F.bindEvents();

			if (!F.isOpened) {
				$('.fancybox-wrap').not( current.wrap ).stop(true).trigger('onReset').remove();

			} else if (previous.prevMethod) {
				F.transitions[ previous.prevMethod ]();
			}

			F.transitions[ F.isOpened ? current.nextMethod : current.openMethod ]();

			F._preloadImages();
		},

		_setDimension: function () {
			var viewport   = F.getViewport(),
				steps      = 0,
				canShrink  = false,
				canExpand  = false,
				wrap       = F.wrap,
				skin       = F.skin,
				inner      = F.inner,
				current    = F.current,
				width      = current.width,
				height     = current.height,
				minWidth   = current.minWidth,
				minHeight  = current.minHeight,
				maxWidth   = current.maxWidth,
				maxHeight  = current.maxHeight,
				scrolling  = current.scrolling,
				scrollOut  = current.scrollOutside ? current.scrollbarWidth : 0,
				margin     = current.margin,
				wMargin    = getScalar(margin[1] + margin[3]),
				hMargin    = getScalar(margin[0] + margin[2]),
				wPadding,
				hPadding,
				wSpace,
				hSpace,
				origWidth,
				origHeight,
				origMaxWidth,
				origMaxHeight,
				ratio,
				width_,
				height_,
				maxWidth_,
				maxHeight_,
				iframe,
				body;

			// Reset dimensions so we could re-check actual size
			wrap.add(skin).add(inner).width('auto').height('auto').removeClass('fancybox-tmp');

			wPadding = getScalar(skin.outerWidth(true)  - skin.width());
			hPadding = getScalar(skin.outerHeight(true) - skin.height());

			// Any space between content and viewport (margin, padding, border, title)
			wSpace = wMargin + wPadding;
			hSpace = hMargin + hPadding;

			origWidth  = isPercentage(width)  ? (viewport.w - wSpace) * getScalar(width)  / 100 : width;
			origHeight = isPercentage(height) ? (viewport.h - hSpace) * getScalar(height) / 100 : height;

			if (current.type === 'iframe') {
				iframe = current.content;

				if (current.autoHeight && iframe.data('ready') === 1) {
					try {
						if (iframe[0].contentWindow.document.location) {
							inner.width( origWidth ).height(9999);

							body = iframe.contents().find('body');

							if (scrollOut) {
								body.css('overflow-x', 'hidden');
							}

							origHeight = body.height();
						}

					} catch (e) {}
				}

			} else if (current.autoWidth || current.autoHeight) {
				inner.addClass( 'fancybox-tmp' );

				// Set width or height in case we need to calculate only one dimension
				if (!current.autoWidth) {
					inner.width( origWidth );
				}

				if (!current.autoHeight) {
					inner.height( origHeight );
				}

				if (current.autoWidth) {
					origWidth = inner.width();
				}

				if (current.autoHeight) {
					origHeight = inner.height();
				}

				inner.removeClass( 'fancybox-tmp' );
			}

			width  = getScalar( origWidth );
			height = getScalar( origHeight );

			ratio  = origWidth / origHeight;

			// Calculations for the content
			minWidth  = getScalar(isPercentage(minWidth) ? getScalar(minWidth, 'w') - wSpace : minWidth);
			maxWidth  = getScalar(isPercentage(maxWidth) ? getScalar(maxWidth, 'w') - wSpace : maxWidth);

			minHeight = getScalar(isPercentage(minHeight) ? getScalar(minHeight, 'h') - hSpace : minHeight);
			maxHeight = getScalar(isPercentage(maxHeight) ? getScalar(maxHeight, 'h') - hSpace : maxHeight);

			// These will be used to determine if wrap can fit in the viewport
			origMaxWidth  = maxWidth;
			origMaxHeight = maxHeight;

			if (current.fitToView) {
				maxWidth  = Math.min(viewport.w - wSpace, maxWidth);
				maxHeight = Math.min(viewport.h - hSpace, maxHeight);
			}

			maxWidth_  = viewport.w - wMargin;
			maxHeight_ = viewport.h - hMargin;

			if (current.aspectRatio) {
				if (width > maxWidth) {
					width  = maxWidth;
					height = getScalar(width / ratio);
				}

				if (height > maxHeight) {
					height = maxHeight;
					width  = getScalar(height * ratio);
				}

				if (width < minWidth) {
					width  = minWidth;
					height = getScalar(width / ratio);
				}

				if (height < minHeight) {
					height = minHeight;
					width  = getScalar(height * ratio);
				}

			} else {
				width = Math.max(minWidth, Math.min(width, maxWidth));

				if (current.autoHeight && current.type !== 'iframe') {
					inner.width( width );

					height = inner.height();
				}

				height = Math.max(minHeight, Math.min(height, maxHeight));
			}

			// Try to fit inside viewport (including the title)
			if (current.fitToView) {
				inner.width( width ).height( height );

				wrap.width( width + wPadding );

				// Real wrap dimensions
				width_  = wrap.width();
				height_ = wrap.height();

				if (current.aspectRatio) {
					while ((width_ > maxWidth_ || height_ > maxHeight_) && width > minWidth && height > minHeight) {
						if (steps++ > 19) {
							break;
						}

						height = Math.max(minHeight, Math.min(maxHeight, height - 10));
						width  = getScalar(height * ratio);

						if (width < minWidth) {
							width  = minWidth;
							height = getScalar(width / ratio);
						}

						if (width > maxWidth) {
							width  = maxWidth;
							height = getScalar(width / ratio);
						}

						inner.width( width ).height( height );

						wrap.width( width + wPadding );

						width_  = wrap.width();
						height_ = wrap.height();
					}

				} else {
					width  = Math.max(minWidth,  Math.min(width,  width  - (width_  - maxWidth_)));
					height = Math.max(minHeight, Math.min(height, height - (height_ - maxHeight_)));
				}
			}

			if (scrollOut && scrolling === 'auto' && height < origHeight && (width + wPadding + scrollOut) < maxWidth_) {
				width += scrollOut;
			}

			inner.width( width ).height( height );

			wrap.width( width + wPadding );

			width_  = wrap.width();
			height_ = wrap.height();

			canShrink = (width_ > maxWidth_ || height_ > maxHeight_) && width > minWidth && height > minHeight;
			canExpand = current.aspectRatio ? (width < origMaxWidth && height < origMaxHeight && width < origWidth && height < origHeight) : ((width < origMaxWidth || height < origMaxHeight) && (width < origWidth || height < origHeight));

			$.extend(current, {
				dim : {
					width	: getValue( width_ ),
					height	: getValue( height_ )
				},
				origWidth  : origWidth,
				origHeight : origHeight,
				canShrink  : canShrink,
				canExpand  : canExpand,
				wPadding   : wPadding,
				hPadding   : hPadding,
				wrapSpace  : height_ - skin.outerHeight(true),
				skinSpace  : skin.height() - height
			});

			if (!iframe && current.autoHeight && height > minHeight && height < maxHeight && !canExpand) {
				inner.height('auto');
			}
		},

		_getPosition: function (onlyAbsolute) {
			var current  = F.current,
				viewport = F.getViewport(),
				margin   = current.margin,
				width    = F.wrap.width()  + margin[1] + margin[3],
				height   = F.wrap.height() + margin[0] + margin[2],
				rez      = {
					position: 'absolute',
					top  : margin[0],
					left : margin[3]
				};

			if (current.autoCenter && current.fixed && !onlyAbsolute && height <= viewport.h && width <= viewport.w) {
				rez.position = 'fixed';

			} else if (!current.locked) {
				rez.top  += viewport.y;
				rez.left += viewport.x;
			}

			rez.top  = getValue(Math.max(rez.top,  rez.top  + ((viewport.h - height) * current.topRatio)));
			rez.left = getValue(Math.max(rez.left, rez.left + ((viewport.w - width)  * current.leftRatio)));

			return rez;
		},

		_afterZoomIn: function () {
			var current = F.current;

			if (!current) {
				return;
			}

			F.isOpen = F.isOpened = true;

			F.wrap.css('overflow', 'visible').addClass('fancybox-opened');

			F.update();

			// Assign a click event
			if ( current.closeClick || (current.nextClick && F.group.length > 1) ) {
				F.inner.css('cursor', 'pointer').bind('click.fb', function(e) {
					if (!$(e.target).is('a') && !$(e.target).parent().is('a')) {
						e.preventDefault();

						F[ current.closeClick ? 'close' : 'next' ]();
					}
				});
			}

			// Create a close button
			if (current.closeBtn) {
				$(current.tpl.closeBtn).appendTo(F.skin).bind('click.fb', function(e) {
					e.preventDefault();

					F.close();
				});
			}

			// Create navigation arrows
			if (current.arrows && F.group.length > 1) {
				if (current.loop || current.index > 0) {
					$(current.tpl.prev).appendTo(F.outer).bind('click.fb', F.prev);
				}

				if (current.loop || current.index < F.group.length - 1) {
					$(current.tpl.next).appendTo(F.outer).bind('click.fb', F.next);
				}
			}

			F.trigger('afterShow');

			// Stop the slideshow if this is the last item
			if (!current.loop && current.index === current.group.length - 1) {
				F.play( false );

			} else if (F.opts.autoPlay && !F.player.isActive) {
				F.opts.autoPlay = false;

				F.play();
			}
		},

		_afterZoomOut: function ( obj ) {
			obj = obj || F.current;

			$('.fancybox-wrap').trigger('onReset').remove();

			$.extend(F, {
				group  : {},
				opts   : {},
				router : false,
				current   : null,
				isActive  : false,
				isOpened  : false,
				isOpen    : false,
				isClosing : false,
				wrap   : null,
				skin   : null,
				outer  : null,
				inner  : null
			});

			F.trigger('afterClose', obj);
		}
	});

	/*
	 *	Default transitions
	 */

	F.transitions = {
		getOrigPosition: function () {
			var current  = F.current,
				element  = current.element,
				orig     = current.orig,
				pos      = {},
				width    = 50,
				height   = 50,
				hPadding = current.hPadding,
				wPadding = current.wPadding,
				viewport = F.getViewport();

			if (!orig && current.isDom && element.is(':visible')) {
				orig = element.find('img:first');

				if (!orig.length) {
					orig = element;
				}
			}

			if (isQuery(orig)) {
				pos = orig.offset();

				if (orig.is('img')) {
					width  = orig.outerWidth();
					height = orig.outerHeight();
				}

			} else {
				pos.top  = viewport.y + (viewport.h - height) * current.topRatio;
				pos.left = viewport.x + (viewport.w - width)  * current.leftRatio;
			}

			if (F.wrap.css('position') === 'fixed' || current.locked) {
				pos.top  -= viewport.y;
				pos.left -= viewport.x;
			}

			pos = {
				top     : getValue(pos.top  - hPadding * current.topRatio),
				left    : getValue(pos.left - wPadding * current.leftRatio),
				width   : getValue(width  + wPadding),
				height  : getValue(height + hPadding)
			};

			return pos;
		},

		step: function (now, fx) {
			var ratio,
				padding,
				value,
				prop       = fx.prop,
				current    = F.current,
				wrapSpace  = current.wrapSpace,
				skinSpace  = current.skinSpace;

			if (prop === 'width' || prop === 'height') {
				ratio = fx.end === fx.start ? 1 : (now - fx.start) / (fx.end - fx.start);

				if (F.isClosing) {
					ratio = 1 - ratio;
				}

				padding = prop === 'width' ? current.wPadding : current.hPadding;
				value   = now - padding;

				F.skin[ prop ](  getScalar( prop === 'width' ?  value : value - (wrapSpace * ratio) ) );
				F.inner[ prop ]( getScalar( prop === 'width' ?  value : value - (wrapSpace * ratio) - (skinSpace * ratio) ) );
			}
		},

		zoomIn: function () {
			var current  = F.current,
				startPos = current.pos,
				effect   = current.openEffect,
				elastic  = effect === 'elastic',
				endPos   = $.extend({opacity : 1}, startPos);

			// Remove "position" property that breaks older IE
			delete endPos.position;

			if (elastic) {
				startPos = this.getOrigPosition();

				if (current.openOpacity) {
					startPos.opacity = 0.1;
				}

			} else if (effect === 'fade') {
				startPos.opacity = 0.1;
			}

			F.wrap.css(startPos).animate(endPos, {
				duration : effect === 'none' ? 0 : current.openSpeed,
				easing   : current.openEasing,
				step     : elastic ? this.step : null,
				complete : F._afterZoomIn
			});
		},

		zoomOut: function () {
			var current  = F.current,
				effect   = current.closeEffect,
				elastic  = effect === 'elastic',
				endPos   = {opacity : 0.1};

			if (elastic) {
				endPos = this.getOrigPosition();

				if (current.closeOpacity) {
					endPos.opacity = 0.1;
				}
			}

			F.wrap.animate(endPos, {
				duration : effect === 'none' ? 0 : current.closeSpeed,
				easing   : current.closeEasing,
				step     : elastic ? this.step : null,
				complete : F._afterZoomOut
			});
		},

		changeIn: function () {
			var current   = F.current,
				effect    = current.nextEffect,
				startPos  = current.pos,
				endPos    = { opacity : 1 },
				direction = F.direction,
				distance  = 200,
				field;

			startPos.opacity = 0.1;

			if (effect === 'elastic') {
				field = direction === 'down' || direction === 'up' ? 'top' : 'left';

				if (direction === 'down' || direction === 'right') {
					startPos[ field ] = getValue(getScalar(startPos[ field ]) - distance);
					endPos[ field ]   = '+=' + distance + 'px';

				} else {
					startPos[ field ] = getValue(getScalar(startPos[ field ]) + distance);
					endPos[ field ]   = '-=' + distance + 'px';
				}
			}

			// Workaround for http://bugs.jquery.com/ticket/12273
			if (effect === 'none') {
				F._afterZoomIn();

			} else {
				F.wrap.css(startPos).animate(endPos, {
					duration : current.nextSpeed,
					easing   : current.nextEasing,
					complete : F._afterZoomIn
				});
			}
		},

		changeOut: function () {
			var previous  = F.previous,
				effect    = previous.prevEffect,
				endPos    = { opacity : 0.1 },
				direction = F.direction,
				distance  = 200;

			if (effect === 'elastic') {
				endPos[ direction === 'down' || direction === 'up' ? 'top' : 'left' ] = ( direction === 'up' || direction === 'left' ? '-' : '+' ) + '=' + distance + 'px';
			}

			previous.wrap.animate(endPos, {
				duration : effect === 'none' ? 0 : previous.prevSpeed,
				easing   : previous.prevEasing,
				complete : function () {
					$(this).trigger('onReset').remove();
				}
			});
		}
	};

	/*
	 *	Overlay helper
	 */

	F.helpers.overlay = {
		defaults : {
			closeClick : true,  // if true, fancyBox will be closed when user clicks on the overlay
			speedOut   : 200,   // duration of fadeOut animation
			showEarly  : true,  // indicates if should be opened immediately or wait until the content is ready
			css        : {},    // custom CSS properties
			locked     : !isTouch,  // if true, the content will be locked into overlay
			fixed      : true   // if false, the overlay CSS position property will not be set to "fixed"
		},

		overlay : null,   // current handle
		fixed   : false,  // indicates if the overlay has position "fixed"

		// Public methods
		create : function(opts) {
			opts = $.extend({}, this.defaults, opts);

			if (this.overlay) {
				this.close();
			}

			this.overlay = $('<div class="fancybox-overlay"></div>').appendTo( 'body' );
			this.fixed   = false;

			if (opts.fixed && F.defaults.fixed) {
				this.overlay.addClass('fancybox-overlay-fixed');

				this.fixed = true;
			}
		},

		open : function(opts) {
			var that = this;

			opts = $.extend({}, this.defaults, opts);

			if (this.overlay) {
				this.overlay.unbind('.overlay').width('auto').height('auto');

			} else {
				this.create(opts);
			}

			if (!this.fixed) {
				W.bind('resize.overlay', $.proxy( this.update, this) );

				this.update();
			}

			if (opts.closeClick) {
				this.overlay.bind('click.overlay', function(e) {
					if ($(e.target).hasClass('fancybox-overlay')) {
						if (F.isActive) {
							F.close();
						} else {
							that.close();
						}
					}
				});
			}

			this.overlay.css( opts.css ).show();
		},

		close : function() {
			$('.fancybox-overlay').remove();

			W.unbind('resize.overlay');

			this.overlay = null;

			if (this.margin !== false) {
				$('body').css('margin-right', this.margin);

				this.margin = false;
			}

			if (this.el) {
				this.el.removeClass('fancybox-lock');
			}
		},

		// Private, callbacks

		update : function () {
			var width = '100%', offsetWidth;

			// Reset width/height so it will not mess
			this.overlay.width(width).height('100%');

			// jQuery does not return reliable result for IE
			if (IE) {
				offsetWidth = Math.max(document.documentElement.offsetWidth, document.body.offsetWidth);

				if (D.width() > offsetWidth) {
					width = D.width();
				}

			} else if (D.width() > W.width()) {
				width = D.width();
			}

			this.overlay.width(width).height(D.height());
		},

		// This is where we can manipulate DOM, because later it would cause iframes to reload
		onReady : function (opts, obj) {
			$('.fancybox-overlay').stop(true, true);

			if (!this.overlay) {
				this.margin = D.height() > W.height() || $('body').css('overflow-y') === 'scroll' ? $('body').css('margin-right') : false;
				this.el     = document.all && !document.querySelector ? $('html') : $('body');

				this.create(opts);
			}

			if (opts.locked && this.fixed) {
				obj.locked = this.overlay.append( obj.wrap );
				obj.fixed  = false;
			}

			if (opts.showEarly === true) {
				this.beforeShow.apply(this, arguments);
			}
		},

		beforeShow : function(opts, obj) {
			if (obj.locked) {
				this.el.addClass('fancybox-lock');

				if (this.margin !== false) {
					$('body').css('margin-right', getScalar( this.margin ) + obj.scrollbarWidth);
				}
			}

			this.open(opts);
		},

		onUpdate : function() {
			if (!this.fixed) {
				this.update();
			}
		},

		afterClose: function (opts) {
			// Remove overlay if exists and fancyBox is not opening
			// (e.g., it is not being open using afterClose callback)
			if (this.overlay && !F.isActive) {
				this.overlay.fadeOut(opts.speedOut, $.proxy( this.close, this ));
			}
		}
	};

	/*
	 *	Title helper
	 */

	F.helpers.title = {
		defaults : {
			type     : 'float', // 'float', 'inside', 'outside' or 'over',
			position : 'bottom' // 'top' or 'bottom'
		},

		beforeShow: function (opts) {
			var current = F.current,
				text    = current.title,
				type    = opts.type,
				title,
				target;

			if ($.isFunction(text)) {
				text = text.call(current.element, current);
			}

			if (!isString(text) || $.trim(text) === '') {
				return;
			}

			title = $('<div class="fancybox-title fancybox-title-' + type + '-wrap">' + text + '</div>');

			switch (type) {
				case 'inside':
					target = F.skin;
				break;

				case 'outside':
					target = F.wrap;
				break;

				case 'over':
					target = F.inner;
				break;

				default: // 'float'
					target = F.skin;

					title.appendTo('body');

					if (IE) {
						title.width( title.width() );
					}

					title.wrapInner('<span class="child"></span>');

					//Increase bottom margin so this title will also fit into viewport
					F.current.margin[2] += Math.abs( getScalar(title.css('margin-bottom')) );
				break;
			}

			title[ (opts.position === 'top' ? 'prependTo'  : 'appendTo') ](target);
		}
	};

	// jQuery plugin initialization
	$.fn.fancybox = function (options) {
		var index,
			that     = $(this),
			selector = this.selector || '',
			run      = function(e) {
				var what = $(this).blur(), idx = index, relType, relVal;

				if (!(e.ctrlKey || e.altKey || e.shiftKey || e.metaKey) && !what.is('.fancybox-wrap')) {
					relType = options.groupAttr || 'data-fancybox-group';
					relVal  = what.attr(relType);

					if (!relVal) {
						relType = 'rel';
						relVal  = what.get(0)[ relType ];
					}

					if (relVal && relVal !== '' && relVal !== 'nofollow') {
						what = selector.length ? $(selector) : that;
						what = what.filter('[' + relType + '="' + relVal + '"]');
						idx  = what.index(this);
					}

					options.index = idx;

					// Stop an event from bubbling if everything is fine
					if (F.open(what, options) !== false) {
						e.preventDefault();
					}
				}
			};

		options = options || {};
		index   = options.index || 0;

		if (!selector || options.live === false) {
			that.unbind('click.fb-start').bind('click.fb-start', run);

		} else {
			D.undelegate(selector, 'click.fb-start').delegate(selector + ":not('.fancybox-item, .fancybox-nav')", 'click.fb-start', run);
		}

		this.filter('[data-fancybox-start=1]').trigger('click');

		return this;
	};

	// Tests that need a body at doc ready
	D.ready(function() {
		if ( $.scrollbarWidth === undefined ) {
			// http://benalman.com/projects/jquery-misc-plugins/#scrollbarwidth
			$.scrollbarWidth = function() {
				var parent = $('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo('body'),
					child  = parent.children(),
					width  = child.innerWidth() - child.height( 99 ).innerWidth();

				parent.remove();

				return width;
			};
		}

		if ( $.support.fixedPosition === undefined ) {
			$.support.fixedPosition = (function() {
				var elem  = $('<div style="position:fixed;top:20px;"></div>').appendTo('body'),
					fixed = ( elem[0].offsetTop === 20 || elem[0].offsetTop === 15 );

				elem.remove();

				return fixed;
			}());
		}

		$.extend(F.defaults, {
			scrollbarWidth : $.scrollbarWidth(),
			fixed  : $.support.fixedPosition,
			parent : $('body')
		});
	});

}(window, document, jQuery));/*!
 * jQuery blockUI plugin
 * Version 2.53 (01-NOV-2012)
 * @requires jQuery v1.3 or later
 *
 * Examples at: http://malsup.com/jquery/block/
 * Copyright (c) 2007-2012 M. Alsup
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Thanks to Amir-Hossein Sobhi for some excellent contributions!
 */

;(function() {
"use strict";

	function setup($) {
		if (/^1\.(0|1|2)/.test($.fn.jquery)) {
			/*global alert:true */
			alert('blockUI requires jQuery v1.3 or later!  You are using v' + $.fn.jquery);
			return;
		}

		$.fn._fadeIn = $.fn.fadeIn;

		var noOp = $.noop || function() {};

		// this bit is to ensure we don't call setExpression when we shouldn't (with extra muscle to handle
		// retarded userAgent strings on Vista)
		var msie = /MSIE/.test(navigator.userAgent);
		var ie6  = /MSIE 6.0/.test(navigator.userAgent);
		var mode = document.documentMode || 0;
		// var setExpr = msie && (($.browser.version < 8 && !mode) || mode < 8);
		var setExpr = $.isFunction( document.createElement('div').style.setExpression );

		// global $ methods for blocking/unblocking the entire page
		$.blockUI   = function(opts) { install(window, opts); };
		$.unblockUI = function(opts) { remove(window, opts); };

		// convenience method for quick growl-like notifications  (http://www.google.com/search?q=growl)
		$.growlUI = function(title, message, timeout, onClose) {
			var $m = $('<div class="growlUI"></div>');
			if (title) $m.append('<h1>'+title+'</h1>');
			if (message) $m.append('<h2>'+message+'</h2>');
			if (timeout === undefined) timeout = 3000;
			$.blockUI({
				message: $m, fadeIn: 700, fadeOut: 1000, centerY: false,
				timeout: timeout, showOverlay: false,
				onUnblock: onClose,
				css: $.blockUI.defaults.growlCSS
			});
		};

		// plugin method for blocking element content
		$.fn.block = function(opts) {
			var fullOpts = $.extend({}, $.blockUI.defaults, opts || {});
			this.each(function() {
				var $el = $(this);
				if (fullOpts.ignoreIfBlocked && $el.data('blockUI.isBlocked'))
					return;
				$el.unblock({ fadeOut: 0 });
			});

			return this.each(function() {
				if ($.css(this,'position') == 'static')
					this.style.position = 'relative';
				this.style.zoom = 1; // force 'hasLayout' in ie
				install(this, opts);
			});
		};

		// plugin method for unblocking element content
		$.fn.unblock = function(opts) {
			return this.each(function() {
				remove(this, opts);
			});
		};

		$.blockUI.version = 2.53; // 2nd generation blocking at no extra cost!

		// override these in your code to change the default behavior and style
		$.blockUI.defaults = {
			// message displayed when blocking (use null for no message)
			message:  '<h1>Please wait...</h1>',

			title: null,		// title string; only used when theme == true
			draggable: true,	// only used when theme == true (requires jquery-ui.js to be loaded)

			theme: false, // set to true to use with jQuery UI themes

			// styles for the message when blocking; if you wish to disable
			// these and use an external stylesheet then do this in your code:
			// $.blockUI.defaults.css = {};
			css: {
				padding:	0,
				margin:		0,
				width:		'30%',
				top:		'40%',
				left:		'35%',
				textAlign:	'center',
				color:		'#000',
				border:		'3px solid #aaa',
				backgroundColor:'#fff',
				cursor:		'wait'
			},

			// minimal style set used when themes are used
			themedCSS: {
				width:	'30%',
				top:	'40%',
				left:	'35%'
			},

			// styles for the overlay
			overlayCSS:  {
				backgroundColor:	'#000',
				opacity:				0.6,
				cursor:				'wait'
			},

			// style to replace wait cursor before unblocking to correct issue
			// of lingering wait cursor
			cursorReset: 'default',

			// styles applied when using $.growlUI
			growlCSS: {
				width:		'350px',
				top:		'10px',
				left:		'',
				right:		'10px',
				border:		'none',
				padding:	'5px',
				opacity:	0.6,
				cursor:		'default',
				color:		'#fff',
				backgroundColor: '#000',
				'-webkit-border-radius':'10px',
				'-moz-border-radius':	'10px',
				'border-radius':		'10px'
			},

			// IE issues: 'about:blank' fails on HTTPS and javascript:false is s-l-o-w
			// (hat tip to Jorge H. N. de Vasconcelos)
			/*jshint scripturl:true */
			iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank',

			// force usage of iframe in non-IE browsers (handy for blocking applets)
			forceIframe: false,

			// z-index for the blocking overlay
			baseZ: 1000,

			// set these to true to have the message automatically centered
			centerX: true, // <-- only effects element blocking (page block controlled via css above)
			centerY: true,

			// allow body element to be stetched in ie6; this makes blocking look better
			// on "short" pages.  disable if you wish to prevent changes to the body height
			allowBodyStretch: true,

			// enable if you want key and mouse events to be disabled for content that is blocked
			bindEvents: true,

			// be default blockUI will supress tab navigation from leaving blocking content
			// (if bindEvents is true)
			constrainTabKey: true,

			// fadeIn time in millis; set to 0 to disable fadeIn on block
			fadeIn:  200,

			// fadeOut time in millis; set to 0 to disable fadeOut on unblock
			fadeOut:  400,

			// time in millis to wait before auto-unblocking; set to 0 to disable auto-unblock
			timeout: 0,

			// disable if you don't want to show the overlay
			showOverlay: true,

			// if true, focus will be placed in the first available input field when
			// page blocking
			focusInput: true,

			// suppresses the use of overlay styles on FF/Linux (due to performance issues with opacity)
			// no longer needed in 2012
			// applyPlatformOpacityRules: true,

			// callback method invoked when fadeIn has completed and blocking message is visible
			onBlock: null,

			// callback method invoked when unblocking has completed; the callback is
			// passed the element that has been unblocked (which is the window object for page
			// blocks) and the options that were passed to the unblock call:
			//	onUnblock(element, options)
			onUnblock: null,

			// callback method invoked when the overlay area is clicked.
			// setting this will turn the cursor to a pointer, otherwise cursor defined in overlayCss will be used.
			onOverlayClick: null,

			// don't ask; if you really must know: http://groups.google.com/group/jquery-en/browse_thread/thread/36640a8730503595/2f6a79a77a78e493#2f6a79a77a78e493
			quirksmodeOffsetHack: 4,

			// class name of the message block
			blockMsgClass: 'blockMsg',

			// if it is already blocked, then ignore it (don't unblock and reblock)
			ignoreIfBlocked: false
		};

		// private data and functions follow...

		var pageBlock = null;
		var pageBlockEls = [];

		function install(el, opts) {
			var css, themedCSS;
			var full = (el == window);
			var msg = (opts && opts.message !== undefined ? opts.message : undefined);
			opts = $.extend({}, $.blockUI.defaults, opts || {});

			if (opts.ignoreIfBlocked && $(el).data('blockUI.isBlocked'))
				return;

			opts.overlayCSS = $.extend({}, $.blockUI.defaults.overlayCSS, opts.overlayCSS || {});
			css = $.extend({}, $.blockUI.defaults.css, opts.css || {});
			if (opts.onOverlayClick)
				opts.overlayCSS.cursor = 'pointer';

			themedCSS = $.extend({}, $.blockUI.defaults.themedCSS, opts.themedCSS || {});
			msg = msg === undefined ? opts.message : msg;

			// remove the current block (if there is one)
			if (full && pageBlock)
				remove(window, {fadeOut:0});

			// if an existing element is being used as the blocking content then we capture
			// its current place in the DOM (and current display style) so we can restore
			// it when we unblock
			if (msg && (typeof msg != 'string') && (msg.parentNode || msg.jquery)) {
				var node = msg.jquery ? msg[0] : msg;

				if ('undefined' !== typeof node) {
					var data = {};
					$(el).data('blockUI.history', data);
					data.el = node;
					data.parent = node.parentNode;
					data.display = node.style.display;
					data.position = node.style.position;
					if (data.parent)
						data.parent.removeChild(node);
				}
			}

			$(el).data('blockUI.onUnblock', opts.onUnblock);
			var z = opts.baseZ;

			// blockUI uses 3 layers for blocking, for simplicity they are all used on every platform;
			// layer1 is the iframe layer which is used to supress bleed through of underlying content
			// layer2 is the overlay layer which has opacity and a wait cursor (by default)
			// layer3 is the message content that is displayed while blocking
			var lyr1, lyr2, lyr3, s;
			if (msie || opts.forceIframe)
				lyr1 = $('<iframe class="blockUI" style="z-index:'+ (z++) +';display:none;border:none;margin:0;padding:0;position:absolute;width:100%;height:100%;top:0;left:0" src="'+opts.iframeSrc+'"></iframe>');
			else
				lyr1 = $('<div class="blockUI" style="display:none"></div>');

			if (opts.theme)
				lyr2 = $('<div class="blockUI blockOverlay ui-widget-overlay" style="z-index:'+ (z++) +';display:none"></div>');
			else
				lyr2 = $('<div class="blockUI blockOverlay" style="z-index:'+ (z++) +';display:none;border:none;margin:0;padding:0;width:100%;height:100%;top:0;left:0"></div>');

			if (opts.theme && full) {
				s = '<div class="blockUI ' + opts.blockMsgClass + ' blockPage ui-dialog ui-widget ui-corner-all" style="z-index:'+(z+10)+';display:none;position:fixed">';
				if ( opts.title ) {
					s += '<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(opts.title || '&nbsp;')+'</div>';
				}
				s += '<div class="ui-widget-content ui-dialog-content"></div>';
				s += '</div>';
			}
			else if (opts.theme) {
				s = '<div class="blockUI ' + opts.blockMsgClass + ' blockElement ui-dialog ui-widget ui-corner-all" style="z-index:'+(z+10)+';display:none;position:absolute">';
				if ( opts.title ) {
					s += '<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(opts.title || '&nbsp;')+'</div>';
				}  
				s += '<div class="ui-widget-content ui-dialog-content"></div>';
				s += '</div>';
			}
			else if (full) {
				s = '<div class="blockUI ' + opts.blockMsgClass + ' blockPage" style="z-index:'+(z+10)+';display:none;position:fixed"></div>';
			}
			else {
				s = '<div class="blockUI ' + opts.blockMsgClass + ' blockElement" style="z-index:'+(z+10)+';display:none;position:absolute"></div>';
			}
			lyr3 = $(s);

			// if we have a message, style it
			if (msg) {
				if (opts.theme) {
					lyr3.css(themedCSS);
					lyr3.addClass('ui-widget-content');
				}
				else
					lyr3.css(css);
			}

			// style the overlay
			if (!opts.theme /*&& (!opts.applyPlatformOpacityRules)*/)
				lyr2.css(opts.overlayCSS);
			lyr2.css('position', full ? 'fixed' : 'absolute');

			// make iframe layer transparent in IE
			if (msie || opts.forceIframe)
				lyr1.css('opacity',0.0);

			//$([lyr1[0],lyr2[0],lyr3[0]]).appendTo(full ? 'body' : el);
			var layers = [lyr1,lyr2,lyr3], $par = full ? $('body') : $(el);
			$.each(layers, function() {
				this.appendTo($par);
			});

			if (opts.theme && opts.draggable && $.fn.draggable) {
				lyr3.draggable({
					handle: '.ui-dialog-titlebar',
					cancel: 'li'
				});
			}

			// ie7 must use absolute positioning in quirks mode and to account for activex issues (when scrolling)
			var expr = setExpr && (!$.support.boxModel || $('object,embed', full ? null : el).length > 0);
			if (ie6 || expr) {
				// give body 100% height
				if (full && opts.allowBodyStretch && $.support.boxModel)
					$('html,body').css('height','100%');

				// fix ie6 issue when blocked element has a border width
				if ((ie6 || !$.support.boxModel) && !full) {
					var t = sz(el,'borderTopWidth'), l = sz(el,'borderLeftWidth');
					var fixT = t ? '(0 - '+t+')' : 0;
					var fixL = l ? '(0 - '+l+')' : 0;
				}

				// simulate fixed position
				$.each(layers, function(i,o) {
					var s = o[0].style;
					s.position = 'absolute';
					if (i < 2) {
						if (full)
							s.setExpression('height','Math.max(document.body.scrollHeight, document.body.offsetHeight) - (jQuery.support.boxModel?0:'+opts.quirksmodeOffsetHack+') + "px"');
						else
							s.setExpression('height','this.parentNode.offsetHeight + "px"');
						if (full)
							s.setExpression('width','jQuery.support.boxModel && document.documentElement.clientWidth || document.body.clientWidth + "px"');
						else
							s.setExpression('width','this.parentNode.offsetWidth + "px"');
						if (fixL) s.setExpression('left', fixL);
						if (fixT) s.setExpression('top', fixT);
					}
					else if (opts.centerY) {
						if (full) s.setExpression('top','(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (blah = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"');
						s.marginTop = 0;
					}
					else if (!opts.centerY && full) {
						var top = (opts.css && opts.css.top) ? parseInt(opts.css.top, 10) : 0;
						var expression = '((document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + '+top+') + "px"';
						s.setExpression('top',expression);
					}
				});
			}

			// show the message
			if (msg) {
				if (opts.theme)
					lyr3.find('.ui-widget-content').append(msg);
				else
					lyr3.append(msg);
				if (msg.jquery || msg.nodeType)
					$(msg).show();
			}

			if ((msie || opts.forceIframe) && opts.showOverlay)
				lyr1.show(); // opacity is zero
			if (opts.fadeIn) {
				var cb = opts.onBlock ? opts.onBlock : noOp;
				var cb1 = (opts.showOverlay && !msg) ? cb : noOp;
				var cb2 = msg ? cb : noOp;
				if (opts.showOverlay)
					lyr2._fadeIn(opts.fadeIn, cb1);
				if (msg)
					lyr3._fadeIn(opts.fadeIn, cb2);
			}
			else {
				if (opts.showOverlay)
					lyr2.show();
				if (msg)
					lyr3.show();
				if (opts.onBlock)
					opts.onBlock();
			}

			// bind key and mouse events
			bind(1, el, opts);

			if (full) {
				pageBlock = lyr3[0];
				pageBlockEls = $(':input:enabled:visible',pageBlock);
				if (opts.focusInput)
					setTimeout(focus, 20);
			}
			else
				center(lyr3[0], opts.centerX, opts.centerY);

			if (opts.timeout) {
				// auto-unblock
				var to = setTimeout(function() {
					if (full)
						$.unblockUI(opts);
					else
						$(el).unblock(opts);
				}, opts.timeout);
				$(el).data('blockUI.timeout', to);
			}
		}

		// remove the block
		function remove(el, opts) {
			var full = (el == window);
			var $el = $(el);
			var data = $el.data('blockUI.history');
			var to = $el.data('blockUI.timeout');
			if (to) {
				clearTimeout(to);
				$el.removeData('blockUI.timeout');
			}
			opts = $.extend({}, $.blockUI.defaults, opts || {});
			bind(0, el, opts); // unbind events

			if (opts.onUnblock === null) {
				opts.onUnblock = $el.data('blockUI.onUnblock');
				$el.removeData('blockUI.onUnblock');
			}

			var els;
			if (full) // crazy selector to handle odd field errors in ie6/7
				els = $('body').children().filter('.blockUI').add('body > .blockUI');
			else
				els = $el.find('>.blockUI');

			// fix cursor issue
			if ( opts.cursorReset ) {
				if ( els.length > 1 )
					els[1].style.cursor = opts.cursorReset;
				if ( els.length > 2 )
					els[2].style.cursor = opts.cursorReset;
			}

			if (full)
				pageBlock = pageBlockEls = null;

			if (opts.fadeOut) {
				els.fadeOut(opts.fadeOut);
				setTimeout(function() { reset(els,data,opts,el); }, opts.fadeOut);
			}
			else
				reset(els, data, opts, el);
		}

		// move blocking element back into the DOM where it started
		function reset(els,data,opts,el) {
			els.each(function(i,o) {
				// remove via DOM calls so we don't lose event handlers
				if (this.parentNode)
					this.parentNode.removeChild(this);
			});

			if (data && data.el) {
				data.el.style.display = data.display;
				data.el.style.position = data.position;
				if (data.parent)
					data.parent.appendChild(data.el);
				$(el).removeData('blockUI.history');
			}

			if (typeof opts.onUnblock == 'function')
				opts.onUnblock(el,opts);

			// fix issue in Safari 6 where block artifacts remain until reflow
			var body = $(document.body), w = body.width(), cssW = body[0].style.width;
			body.width(w-1).width(w);
			body[0].style.width = cssW;
		}

		// bind/unbind the handler
		function bind(b, el, opts) {
			var full = el == window, $el = $(el);

			// don't bother unbinding if there is nothing to unbind
			if (!b && (full && !pageBlock || !full && !$el.data('blockUI.isBlocked')))
				return;

			$el.data('blockUI.isBlocked', b);

			// don't bind events when overlay is not in use or if bindEvents is false
			if (!opts.bindEvents || (b && !opts.showOverlay))
				return;

			// bind anchors and inputs for mouse and key events
			var events = 'mousedown mouseup keydown keypress touchstart touchend touchmove';
			if (b)
				$(document).bind(events, opts, handler);
			else
				$(document).unbind(events, handler);

		// former impl...
		//		var $e = $('a,:input');
		//		b ? $e.bind(events, opts, handler) : $e.unbind(events, handler);
		}

		// event handler to suppress keyboard/mouse events when blocking
		function handler(e) {
			// allow tab navigation (conditionally)
			if (e.keyCode && e.keyCode == 9) {
				if (pageBlock && e.data.constrainTabKey) {
					var els = pageBlockEls;
					var fwd = !e.shiftKey && e.target === els[els.length-1];
					var back = e.shiftKey && e.target === els[0];
					if (fwd || back) {
						setTimeout(function(){focus(back);},10);
						return false;
					}
				}
			}
			var opts = e.data;
			var target = $(e.target);
			if (target.hasClass('blockOverlay') && opts.onOverlayClick)
				opts.onOverlayClick();

			// allow events within the message content
			if (target.parents('div.' + opts.blockMsgClass).length > 0)
				return true;

			// allow events for content that is not being blocked
			return target.parents().children().filter('div.blockUI').length === 0;
		}

		function focus(back) {
			if (!pageBlockEls)
				return;
			var e = pageBlockEls[back===true ? pageBlockEls.length-1 : 0];
			if (e)
				e.focus();
		}

		function center(el, x, y) {
			var p = el.parentNode, s = el.style;
			var l = ((p.offsetWidth - el.offsetWidth)/2) - sz(p,'borderLeftWidth');
			var t = ((p.offsetHeight - el.offsetHeight)/2) - sz(p,'borderTopWidth');
			if (x) s.left = l > 0 ? (l+'px') : '0';
			if (y) s.top  = t > 0 ? (t+'px') : '0';
		}

		function sz(el, p) {
			return parseInt($.css(el,p),10)||0;
		}

	}


	/*global define:true */
	if (typeof define === 'function' && define.amd && define.amd.jQuery) {
		define(['jquery'], setup);
	} else {
		setup(jQuery);
	}

})();
/*
 * jQuery history plugin
 * 
 * sample page: http://www.mikage.to/jquery/jquery_history.html
 *
 * Copyright (c) 2006-2009 Taku Sano (Mikage Sawatari)
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Modified by Lincoln Cooper to add Safari support and only call the callback once during initialization
 * for msie when no initial hash supplied.
 */


jQuery.extend({
	historyCurrentHash: undefined,
	historyCallback: undefined,
	historyIframeSrc: undefined,
	historyNeedIframe: jQuery.browser.msie && (jQuery.browser.version < 8 || document.documentMode < 8),
	
	historyInit: function(callback, src){
		jQuery.historyCallback = callback;
		if (src) jQuery.historyIframeSrc = src;
		var current_hash = location.hash.replace(/\?.*$/, '');
		
		jQuery.historyCurrentHash = current_hash;
		if (jQuery.historyNeedIframe) {
			// To stop the callback firing twice during initilization if no hash present
			if (jQuery.historyCurrentHash == '') {
				jQuery.historyCurrentHash = '#';
			}
		
			// add hidden iframe for IE
			jQuery("body").prepend('<iframe id="jQuery_history" style="display: none;"'+
				' src="javascript:false;"></iframe>'
			);
			var ihistory = jQuery("#jQuery_history")[0];
			var iframe = ihistory.contentWindow.document;
			iframe.open();
			iframe.close();
			iframe.location.hash = current_hash;
		}
		else if (jQuery.browser.safari) {
			// etablish back/forward stacks
			jQuery.historyBackStack = [];
			jQuery.historyBackStack.length = history.length;
			jQuery.historyForwardStack = [];
			jQuery.lastHistoryLength = history.length;
			
			jQuery.isFirst = true;
		}
		if(current_hash)
			jQuery.historyCallback(current_hash.replace(/^#/, ''));
		setInterval(jQuery.historyCheck, 100);
	},
	
	historyAddHistory: function(hash) {
		// This makes the looping function do something
		jQuery.historyBackStack.push(hash);
		
		jQuery.historyForwardStack.length = 0; // clear forwardStack (true click occured)
		this.isFirst = true;
	},
	
	historyCheck: function(){
		if (jQuery.historyNeedIframe) {
			// On IE, check for location.hash of iframe
			var ihistory = jQuery("#jQuery_history")[0];
			var iframe = ihistory.contentDocument || ihistory.contentWindow.document;
			var current_hash = iframe.location.hash.replace(/\?.*$/, '');
			if(current_hash != jQuery.historyCurrentHash) {
			
				location.hash = current_hash;
				jQuery.historyCurrentHash = current_hash;
				jQuery.historyCallback(current_hash.replace(/^#/, ''));
				
			}
		} else if (jQuery.browser.safari) {
			if(jQuery.lastHistoryLength == history.length && jQuery.historyBackStack.length > jQuery.lastHistoryLength) {
				jQuery.historyBackStack.shift();
			}
			if (!jQuery.dontCheck) {
				var historyDelta = history.length - jQuery.historyBackStack.length;
				jQuery.lastHistoryLength = history.length;
				
				if (historyDelta) { // back or forward button has been pushed
					jQuery.isFirst = false;
					if (historyDelta < 0) { // back button has been pushed
						// move items to forward stack
						for (var i = 0; i < Math.abs(historyDelta); i++) jQuery.historyForwardStack.unshift(jQuery.historyBackStack.pop());
					} else { // forward button has been pushed
						// move items to back stack
						for (var i = 0; i < historyDelta; i++) jQuery.historyBackStack.push(jQuery.historyForwardStack.shift());
					}
					var cachedHash = jQuery.historyBackStack[jQuery.historyBackStack.length - 1];
					if (cachedHash != undefined) {
						jQuery.historyCurrentHash = location.hash.replace(/\?.*$/, '');
						jQuery.historyCallback(cachedHash);
					}
				} else if (jQuery.historyBackStack[jQuery.historyBackStack.length - 1] == undefined && !jQuery.isFirst) {
					// back button has been pushed to beginning and URL already pointed to hash (e.g. a bookmark)
					// document.URL doesn't change in Safari
					if (location.hash) {
						var current_hash = location.hash;
						jQuery.historyCallback(location.hash.replace(/^#/, ''));
					} else {
						var current_hash = '';
						jQuery.historyCallback('');
					}
					jQuery.isFirst = true;
				}
			}
		} else {
			// otherwise, check for location.hash
			var current_hash = location.hash.replace(/\?.*$/, '');
			if(current_hash != jQuery.historyCurrentHash) {
				jQuery.historyCurrentHash = current_hash;
				jQuery.historyCallback(current_hash.replace(/^#/, ''));
			}
		}
	},
	historyLoad: function(hash){
		var newhash;
		hash = decodeURIComponent(hash.replace(/\?.*$/, ''));
		
		if (jQuery.browser.safari) {
			newhash = hash;
		}
		else {
			newhash = '#' + hash;
			location.hash = newhash;
		}
		jQuery.historyCurrentHash = newhash;
		
		if (jQuery.historyNeedIframe) {
			var ihistory = jQuery("#jQuery_history")[0];
			var iframe = ihistory.contentWindow.document;
			iframe.open();
			iframe.close();
			iframe.location.hash = newhash;
			jQuery.lastHistoryLength = history.length;
			jQuery.historyCallback(hash);
		}
		else if (jQuery.browser.safari) {
			jQuery.dontCheck = true;
			// Manually keep track of the history values for Safari
			this.historyAddHistory(hash);
			
			// Wait a while before allowing checking so that Safari has time to update the "history" object
			// correctly (otherwise the check loop would detect a false change in hash).
			var fn = function() {jQuery.dontCheck = false;};
			window.setTimeout(fn, 200);
			jQuery.historyCallback(hash);
			// N.B. "location.hash=" must be the last line of code for Safari as execution stops afterwards.
			//      By explicitly using the "location.hash" command (instead of using a variable set to "location.hash") the
			//      URL in the browser and the "history" object are both updated correctly.
			location.hash = newhash;
		}
		else {
		  jQuery.historyCallback(hash);
		}
	}
});


/**
 * Обратите внимание,
 * что имя файла намеренно начинается с символа подчёркивания.
 * Благодаря этому, сборщик (компилятор) помещает функции этого файла до других
 * (он размещает их в алфавитном порядке).
 */
/**
 * Обратите внимание, что без начального «;»
 * стандартное слияние файлов JavaScript в Magento создаёт сбойный файл
 */
;(function($) {
	$.extend(true, window,{
		rm: {
			/**
			 * @param value
			 * @returns {Boolean}
			 */
			defined: function(value) {
				return ('undefined' !== typeof value);
			}
			/**
			 * @param {*} value
			 * @returns {Boolean}
			 */
			,empty: function(value) {
				/**
				 * @link http://stackoverflow.com/a/154068/254475
				 */
				return !value;
			}
			/**
			 * @function
			 * @throws {Error}
			 */
			,error: function() {
				/** @type {String} */
				var message = '';
				if (0 < arguments.length) {
					message =
						(1 === arguments.length)
						? arguments[0]
						: sprintf.apply(arguments)
					;
				}
				console.trace();
				throw new Error(message);
			}
			,namespace:
				/**
				 * Создаёт иерархическое объектное пространство имён.
				 * Пример применения:
				 * rm.namespace('rm.catalog.showcase');
				 * rm.catalog.showcase.product = {
				 * 		<...>
				 * };
				 *
				 */
				function() {
					var a=arguments, o=null, i, j, d;
					for(i=0; i<a.length; i+=1) {
						d=a[i].split(".");
						o=window;
						for(j=0; j<d.length; j+=1) {
							o[d[j]]=o[d[j]] || {};
							o=o[d[j]];
						}
					}
					return o;
				}
			,reduce: function(array, fnReduce, valueInitial) {
				$.each(array, function(index, value) {
					valueInitial = fnReduce.call(value, valueInitial, value, index, array);
				});
				return valueInitial;
			}
			/**
			 * @param value
			 * @returns {Boolean}
			 */
			,undefined: function(value) {
				return !rm.defined(value);
			}
		}
	});
})(jQuery);
;(function($) {
	rm.assert = {
		/**
		 * @public
		 * @param {Boolean} condition
		 * @param {?String} message
		 * @throws {Error}
		 */
		_: function(condition, message) {
			if (!condition) {
				rm.error(message);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,array: function(value) {
			rm.assert._generic(rm.check.object, 'массив', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @param {Number} lowBound
		 * @param {Number} highBound
		 * @throws {Error}
		 */
		,between: function(value, lowBound, highBound) {
			rm.assert.number(highBound);
			rm.assert.number(lowBound);
			if (!rm.check.between(value, lowBound, highBound)) {
				rm.error('Требуется число между %s и %s, однако получено «%s».', lowBound, highBound, value);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,boolean: function(value) {
			rm.assert._generic(rm.check.object, 'логическое значение', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,defined: function(value) {
			if (rm.undefined(value)) {
				rm.error('Переменная должна быть инициализирована.');
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,function: function(value) {
			rm.assert._generic(rm.check.object, 'функция', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,integer: function(value) {
			rm.assert._generic(rm.check.object, 'целое число', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @param {Number} lowBound
		 * @returns {Boolean}
		 */
		,ge: function(value, lowBound) {
			rm.assert.number(value);
			rm.assert.number(lowBound);
			if (!rm.check.gt(value)) {
				rm.error('Требуется число не меньше %s, однако получено «%s».', lowBound, value);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @param {Number} lowBound
		 * @throws {Error}
		 */
		,gt: function(value, lowBound) {
			rm.assert.number(value);
			rm.assert.number(lowBound);
			if (!rm.check.gt(value)) {
				rm.error('Требуется число больше %s, однако получено «%s».', lowBound, value);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @param {Number} highBound
		 * @throws {Error}
		 */
		,le: function(value, highBound) {
			rm.assert.number(value);
			rm.assert.number(highBound);
			if (!rm.check.le(value)) {
				rm.error('Требуется число не больше %s, однако получено «%s».', highBound, value);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @param {Number} highBound
		 * @throws {Error}
		 */
		,lt: function(value, highBound) {
			rm.assert.number(value);
			rm.assert.number(highBound);
			if (!rm.check.lt(value)) {
				rm.error('Требуется число меньше %s, однако получено «%s».', highBound, value);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,numeric: function(value) {
			rm.assert._generic(rm.check.object, 'число', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,object: function(value) {
			rm.assert._generic(rm.check.object, 'объект', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,string: function(value) {
			rm.assert._generic(rm.check.string, 'строка', value);
		}
		/**
		 * @private
		 * @param {Function} validator
		 * @param {String} expectedTypeName
		 * @param {*} value
		 * @throws {Error}
		 */
		,_generic: function(validator, expectedTypeName, value) {
			if (!validator.apply(value)) {
				rm.error(
					'Требуется %s, однако получена переменная типа «%s».'
					,expectedTypeName
					,$.getType(value)
				);
			}
		}
	};
})(jQuery);;(function($) {
	rm.check = {
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		array: function(value) {
			return $.isArray(value);
		}
		/**
		 * @function
		 * @param {*} value
		 * @param {Number} lowBound
		 * @param {Number} highBound
		 * @returns {Boolean}
		 */
		,between: function(value, lowBound, highBound) {
			return rm.check.numeric(value) && (value >= lowBound) && (value <= highBound);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,boolean: function(value) {
			return (true === value) || (false === value);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,function: function(value) {
			return $.isFunction(value);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,integer: function(value) {
			return rm.check.numeric(value) && (value === Math.floor(value));
		}
		/**
		 * @function
		 * @param {*} value
		 * @param {Number} lowBound
		 * @returns {Boolean}
		 */
		,ge: function(value, lowBound) {
			return rm.check.numeric(value) && (value >= lowBound);
		}
		/**
		 * @function
		 * @param {*} value
		 * @param {Number} lowBound
		 * @returns {Boolean}
		 */
		,gt: function(value, lowBound) {
			return rm.check.numeric(value) && (value > lowBound);
		}
		/**
		 * @function
		 * @param {*} value
		 * @param {Number} lowBound
		 * @returns {Boolean}
		 */
		,le: function(value, lowBound) {
			return rm.check.numeric(value) && (value <= lowBound);
		}
		/**
		 * @function
		 * @param {*} value
		 * @param {Number} lowBound
		 * @returns {Boolean}
		 */
		,lt: function(value, lowBound) {
			return rm.check.numeric(value) && (value < lowBound);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,numeric: function(value) {
			return $.isNumeric(value);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,object: function(value) {
			return $.isPlainObject(value);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,string: function(value) {
			return('string' === typeof(value));
		}
	};
})(jQuery);;(function($) {
	rm.dom = {
		/**
		 * @public
		 * @param {HTMLElement} node
		 * @param {Boolean} includeWhitespaceNodes
		 * @returns {HTMLElement}[]
		 */
		getChildrenTextNodes: function(node, includeWhitespaceNodes) {
			var textNodes = [], whitespace = /^\s*$/;
			function getTextNodes(node) {
				if (3 == node.nodeType) {
					if (includeWhitespaceNodes || !whitespace.test(node.nodeValue)) {
						textNodes.push(node);
					}
				}
				else {
					for(var i = 0, len = node.childNodes.length; i < len; ++i) {
						getTextNodes(node.childNodes[i]);
					}
				}
			}
			getTextNodes(node);
			return textNodes;
		}

		/**
		 * @public
		 * @returns {rm.text}
		 */
		,replaceHtmlPartial: function($element, originalText, translatedText) {
			/**
			 * Этот цикл обязателен, потому что .text()
			 * правильно работает только с единичным элементом.
			 */
			$element.each(function() {
				/** @type {jQuery} HTMLElement */
				var $element = $(this);
				$element.html($element.html().replace(originalText, translatedText));
			});
			return this;
		}

		/**
		 * @public
		 * @returns {rm.text}
		 */
		,replaceText: function($element, originalText, translatedText) {
			/**
			 * Этот цикл обязателен, потому что .text()
			 * правильно работает только с единичным элементом.
			 */
			$element.each(function() {
				/** @type {jQuery} HTMLElement */
				var $element = $(this);
				if (originalText === $element.text()) {
					$element.text(translatedText);
				}
			});
			return this;
		}
	};
})(jQuery);;(function($) {
	rm.format = {
		date: {
			russianLong:
				/**
				 * Преобразовывает объект-дату
				 * в строку-дату в российском формате (дд.мм.ГГГГ)
				 * @param {Date} date
				 * @returns {String}
				 */
				function(date) {
					/** @type {String} */
					var result =
						[
							rm.format.pad(date.getDate(), 2)
							,rm.format.pad(1 + date.getMonth(), 2)
							,date.getFullYear()

						].join('.')
					;
					return result;
				}
		}
		/**
		 * Форматирует число number как строку из length цифр,
		 * добавляя, при необходимости, нули в начале строки
		 * @param {Number} number
		 * @param {Number} length
		 * @returns {String}
		 */
		,pad: function(number, length) {
			var result = number.toString();
			if (result.length < length) {
				result =
					('0000000000' + result)
						.slice(-length)
				;
			}

			return result;
		}
	};
})(jQuery);(function() {
	/**
	 * Мы начали разрабатывать прикладные решения,
	 * которые не включают библиотеку Prototype и стандартные скрипты Magento.
	 * Поэтому учитываем ситуацию, когда класс Validation остутствует
	 * Обратите внимание, что нужно писать именно rm.defined(window.Validation),
	 * а не rm.defined(Validation),
	 * потому что второй вариант приводит к сбою в Firefox:
	 * «ReferenceError: Validation is not defined».
	 */
	if (rm.defined(window.Validation)) {
		Object.extend(Validation, {
			/**
			 * @used-by Validation.prototype.dfValidateFilledFieldsOnly()
			 * @param {Element} elm
			 * @return {Boolean}
			 */
			dfIsVisibleAndNotEmpty : function(elm) {
				/** @type {Boolean} */
				var result =
						Validation.rm.parent.isVisible(elm)
					&&
						/**
						 * Временно считаем пустые поля "невидимыми",
						 * чтобы стандарный класс не считал их неправильно заполненными
						 */
						('' !== $F(elm))
				;
				return result;
			}

			,/**
			 * Данный метод проверяет корректность заполнения формы
			 * так же, как и стандартный метод test(),
			 * Это используется при Быстром оформлении заказа
			 * @param {String} name
			 * @param {Element} elm
			 * @param {Boolean} useTitle
			 * @return {Boolean}
			 */
			dfTestSilent: function(name, elm, useTitle) {
				/** @type {Boolean} */
				var result = false;
				/** @type {Validator} */
				var validator = Validation.get(name);
				try {
					result = (!Validation.isVisible(elm) || validator.test($F(elm), elm));
				}
				catch(e) {
					alert("exception: " + e.message);
					alert(e.stack.toString());
					console.log(e.message);
					console.log(e.stack.toString());
					throw(e);
				}
				return result;
			}
		});
		Object.extend(Validation.prototype, {
			/**
			 * Это используется при Быстром оформлении заказа
			 * @return {Boolean}
			 */
			dfValidateFilledFieldsOnly: function() {
				/** @type {Boolean} */
				var result = false;
				rm.namespace('Validation.rm.parent');
				Validation.rm.parent.isVisible = Validation.isVisible;
				try {
					Validation.isVisible = Validation.dfIsVisibleAndNotEmpty;
					result = this.validate();
				}
				finally {
					Validation.isVisible = Validation.rm.parent.isVisible;
				}
				return result;
			}


			,/**
			 * Данный метод проверяет корректность заполнения формы
			 * так же, как и стандартный метод validate(),
			 * но не выводит диагностических сообщений.
			 * Это используется при Быстром оформлении заказа
			 * @function
			 * @return {Boolean}
			 */
			dfValidateSilent: function() {
				/** @type {Boolean} */
				var result = false;
				/** @function */
				var standardMethod = Validation.test;
				try {
					Validation.test = Validation.dfTestSilent;
					result = this.validate();
				}
				finally {
					Validation.test = standardMethod;
				}
				return result;
			}
		});
	}
})();(function($) {$(function() {
	/**
	 * Мы начали разрабатывать прикладные решения,
	 * которые не включают библиотеку Prototype и стандартные скрипты Magento.
	 * Поэтому учитываем ситуацию, когда класс Validation остутствует
	 * Обратите внимание, что нужно писать именно rm.defined(window.Validation),
	 * а не rm.defined(Validation),
	 * потому что второй вариант приводит к сбою в Firefox:
	 * «ReferenceError: Validation is not defined».
	 */
	if (rm.defined(window.Validation)) {
		rm.namespace('rm.checkout');
		/** @type {RegExp} */
		var alphabetRu = /^[a-zA-Zа-яА-ЯёЁ]*$/;
		/** @type {RegExp} */
		var alphabetRuExtended = /^[a-zA-Zа-яА-ЯёЁ\-\s]*$/;
		// Обратите внимание, что украинские буквы «Іі» внешне неотличимы от латинских,
		// однако имеют другой код в Unicode,
		// поэтому не покрываются регулярным выражением [a-zA-Z]
		/** @type {RegExp} */
		var alphabetRuUa = /^[a-zA-Zа-яА-ЯёЁҐґЄєЇїІі]*$/;
		/** @type {RegExp} */
		var alphabetRuUaExtended = /^[a-zA-Zа-яА-ЯёЁҐґЄєЇїІі\-\s]*$/;
		// Обратите внимание, что казахские буквы «Іі» внешне неотличимы от латинских,
		// однако имеют другой код в Unicode,
		// поэтому не покрываются регулярным выражением [a-zA-Z]
		/** @type {RegExp} */
		var alphabetRuKz = /^[a-zA-Zа-яА-ЯёӘәҒғҚқҢңӨөҰұҺһІі]*$/;
		/** @type {RegExp} */
		var alphabetRuKzExtended = /^[a-zA-Zа-яА-ЯёӘәҒғҚқҢңӨөҰұҺһІі\-\s]*$/;
		/** @type {RegExp} */
		var alphabet = alphabetRu;
		/** @type {RegExp} */
		var alphabetExtended = alphabetRuExtended;
		switch(rm.checkout.alphabet) {
			case 'ua':
				alphabet = alphabetRuUa;
				alphabetExtended = alphabetRuUaExtended;
				break;
			case 'kz':
				alphabet = alphabetRuKz;
				alphabetExtended = alphabetRuKzExtended;
				break;
			default:
				break;
		}
		Validation
			.addAllThese(
				[
					[
						'rm.validate.firstName'
						,'Пожалуйста, исправьте написание Вашего имени.'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							return alphabet.test(value);
						}
					]
					,[
						'rm.validate.lastName'
						,'Фамилия должна состоять только из букв, дефиса(«-») и пробелов'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							return alphabetExtended.test(value);
						}
					]
					,[
						'rm.validate.patronymic'
						,'Отчество должно состоять только из букв, дефиса(«-») и пробелов'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							return alphabetExtended.test(value);
						}
					]
					,[
						'rm.validate.postalCode'
						,'Данное поле должно содержать 6 цифр.'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							/** @type {Boolean} */
							var result =
									Validation.get('IsEmpty').test(value)
								||
									/^[\d]{6}$/.test(value)
							;
							return result;
						}
					]
					,[
						'rm.validate.phone'
						,'Укажите действующий телефонный номер'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							/** @type {Boolean} */
							var result =
									Validation.get('IsEmpty').test(value)
								||
									/^[\d\-\(\)\+\s]{5,20}$/.test(value)
							;
							return result;
						}
					]
					,[
						'rm.validate.city'
						,'Название города должно состоять только из букв, дефиса(«-») и пробелов'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							/** @type {Boolean} */
							var result =
									Validation.get('IsEmpty').test(value)
								||
									alphabetExtended.test(value)
							;
							return result;
						}
					]
					,[
						'rm.validate.region.text'
						,'Название области должно состоять только из букв, дефиса(«-») и пробелов'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							/** @type {Boolean} */
							var result =
									Validation.get('IsEmpty').test(value)
								||
									alphabetExtended.test(value)
							;
							return result;
						}
					]
					,[
						'rm.validate.urlKey'
						,'Уникальная часть веб-адреса должна начинаться с буквы или цифры, '
						+ 'затем допустимы буквы, цифры, символ пути(«/»), дефис(«-»), символ подчёркивания(«_»). '
						+ 'В конце допустимо расширение, как у имён файлов: '
						+ 'точка(«.») и после неё: буквы, цифры, дефис(«-») и символ подчёркивания(«_») .'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							/** @type {Boolean} */
							var result =
									Validation.get('IsEmpty').test(value)
								||
									/^[a-zа-яА-ЯёЁ0-9][a-zа-яА-ЯёЁ0-9_\/-]+(\.[a-zа-яА-ЯёЁ0-9_-]+)?$/.test(value)
							;
							return result;
						}
					]
				]
			)
		;
	}
});})(jQuery);