/******************************************************************************
Name:    Highsglide JS
Version: 4.0.12 (February 4 2009)
Config:  default +slideshow +positioning +transitions
Author:  Torstein H�nsi
Support: http://highslide.com/support

Licence:
Highsglide JS is licensed under a Creative Commons Attribution-NonCommercial 2.5
License (http://creativecommons.org/licenses/by-nc/2.5/).

You are free:
	* to copy, distribute, display, and perform the work
	* to make derivative works

Under the following conditions:
	* Attribution. You must attribute the work in the manner  specified by  the
	  author or licensor.
	* Noncommercial. You may not use this work for commercial purposes.

* For  any  reuse  or  distribution, you  must make clear to others the license
  terms of this work.
* Any  of  these  conditions  can  be  waived  if  you  get permission from the 
  copyright holder.

Your fair use and other rights are in no way affected by the above.
******************************************************************************/

var hsg = {
// Language strings
lang : {
	cssDirection: 'ltr',
	loadingText : 'Загрузка...',
	loadingTitle : 'Клик для отмены',
	focusTitle : 'Клик для фокуса',
	fullExpandTitle : 'Оригинальный размер (f)',
	creditsText : '',
	creditsTitle : '',
	previousText : 'Предыдущий',
	nextText : 'Следующий', 
	moveText : 'Переместить',
	closeText : 'Закрыть', 
	closeTitle : 'Закрыть (esc)', 
	resizeTitle : 'Изменить размер',
	playText : 'Играть',
	playTitle : 'Произгать слайдшоу (пробел)',
	pauseText : 'Пауза',
	pauseTitle : 'Пауза слайдшоу (пробел)',
	previousTitle : 'Предыдущий (стрелка влево)',
	nextTitle : 'Следующий (стралка вправо)',
	moveTitle : 'Переместить',
	fullExpandText : 'Большой размер',
	number: 'Изображение %1 из %2',
	restoreTitle : 'Нажмите кнопку, чтобы закрыть изображение, щелкните мышью и переместить. Используйте клавиши со стрелками на следующий и предыдущий.'
},
// See http://highslide.com/ref for examples of settings  
graphicsDir : '/include/js/highslide/graphics/',
expandCursor : 'zoomin.cur', // null disables
restoreCursor : 'zoomout.cur', // null disables
expandDuration : 250, // milliseconds
restoreDuration : 250,
marginLeft : 15,
marginRight : 15,
marginTop : 15,
marginBottom : 15,
zIndexCounter : 1001, // adjust to other absolutely positioned elements
loadingOpacity : 0.75,
allowMultipleInstances: true,
numberOfImagesToPreload : 5,
outlineWhileAnimating : 2, // 0 = never, 1 = always, 2 = HTML only 
outlineStartOffset : 3, // ends at 10
padToMinWidth : false, // pad the popup width to make room for wide caption
fullExpandPosition : 'bottom right',
fullExpandOpacity : 1,
showCredits : false, // you can set this to false if you want
creditsHref : 'http://highslide.com/',
enableKeyListener : true,
openerTagNames : ['a'], // Add more to allow slideshow indexing
transitions : [],
transitionDuration: 250,
dimmingOpacity: 0, // Lightbox style dimming background
dimmingDuration: 50, // 0 for instant dimming

anchor : 'auto', // where the image expands from
align : 'auto', // position in the client (overrides anchor)
targetX: null, // the id of a target element
targetY: null,
dragByHeading: true,
minWidth: 200,
minHeight: 200,
allowSizeReduction: true, // allow the image to reduce to fit client size. If false, this overrides minWidth and minHeight
outlineType : 'rounded-white', // set null to disable outlines
wrapperClassName : 'highslide-wrapper', // for enhanced css-control
skin : {
	controls:
		'<div class="highslide-controls"><ul>'+
			'<li class="highslide-previous">'+
				'<a href="#" title="{hsg.lang.previousTitle}">'+
				'<span>{hsg.lang.previousText}</span></a>'+
			'</li>'+
			'<li class="highslide-next">'+
				'<a href="#" title="{hsg.lang.nextTitle}">'+
				'<span>{hsg.lang.nextText}</span></a>'+
			'</li>'+
			'<li class="highslide-full-expand">'+
				'<a href="#" title="{hsg.lang.fullExpandTitle}">'+
				'<span>{hsg.lang.fullExpandText}</span></a>'+
			'</li>'+
			'<li class="highslide-close">'+
				'<a href="#" title="{hsg.lang.closeTitle}" >'+
				'<span>{hsg.lang.closeText}</span></a>'+
			'</li>'+
		'</ul></div>'
},
// END OF YOUR SETTINGS


// declare internal properties
preloadTheseImages : [],
continuePreloading: true,
expanders : [],
overrides : [
	'allowSizeReduction',
	'useBox',
	'anchor',
	'align',
	'targetX',
	'targetY',
	'outlineType',
	'outlineWhileAnimating',
	'captionId',
	'captionText',
	'captionEval',
	'captionOverlay',
	'headingId',
	'headingText',
	'headingEval',
	'headingOverlay',
	'dragByHeading',
	'autoplay',
	'numberPosition',
	'transitions',
	'dimmingOpacity',
	
	'width',
	'height',
	
	'wrapperClassName',
	'minWidth',
	'minHeight',
	'maxWidth',
	'maxHeight',
	'slideshowGroup',
	'easing',
	'easingClose',
	'fadeInOut',
	'src'
],
overlays : [],
idCounter : 0,
oPos : {
	x: ['leftpanel', 'left', 'center', 'right', 'rightpanel'],
	y: ['above', 'top', 'middle', 'bottom', 'below']
},
mouse: {},
headingOverlay: {},
captionOverlay: {},
faders : [],

slideshows : [],

pendingOutlines : {},
clones : {},
ie : (document.all && !window.opera),
safari : /Safari/.test(navigator.userAgent),
geckoMac : /Macintosh.+rv:1\.[0-8].+Gecko/.test(navigator.userAgent),

$ : function (id) {
	return document.getElementById(id);
},

push : function (arr, val) {
	arr[arr.length] = val;
},

createElement : function (tag, attribs, styles, parent, nopad) {
	var el = document.createElement(tag);
	if (attribs) hsg.setAttribs(el, attribs);
	if (nopad) hsg.setStyles(el, {padding: 0, border: 'none', margin: 0});
	if (styles) hsg.setStyles(el, styles);
	if (parent) parent.appendChild(el);	
	return el;
},

setAttribs : function (el, attribs) {
	for (var x in attribs) el[x] = attribs[x];
},

setStyles : function (el, styles) {
	for (var x in styles) {
		if (hsg.ie && x == 'opacity') {
			if (styles[x] > 0.99) el.style.removeAttribute('filter');
			else el.style.filter = 'alpha(opacity='+ (styles[x] * 100) +')';
		}
		else el.style[x] = styles[x];
	}
},

ieVersion : function () {
	var arr = navigator.appVersion.split("MSIE");
	return arr[1] ? parseFloat(arr[1]) : null;
},

getPageSize : function () {
	var d = document, w = window, iebody = d.compatMode && d.compatMode != 'BackCompat' 
		? d.documentElement : d.body;	
	
	
	var b = d.body;
	var xScroll = (w.innerWidth && w.scrollMaxX) 
			? w.innerWidth + w.scrollMaxX : Math.max(b.scrollWidth, b.offsetWidth),
		yScroll = (w.innerHeight && window.scrollMaxY) 
			? w.innerHeight + w.scrollMaxY : Math.max(b.scrollHeight, b.offsetHeight),
		pageWidth = hsg.ie ? iebody.scrollWidth :
			(d.documentElement.clientWidth || self.innerWidth),
      	pageHeight = hsg.ie ? Math.max(iebody.scrollHeight, iebody.clientHeight) : 
			(d.documentElement.clientHeight || self.innerHeight);
	
	var width = hsg.ie ? iebody.clientWidth : 
			(d.documentElement.clientWidth || self.innerWidth),
		height = hsg.ie ? iebody.clientHeight : self.innerHeight;
	
	return {
		pageWidth: Math.max(pageWidth, xScroll),
		pageHeight: Math.max(pageHeight, yScroll),
		width: width,
		height: height,		
		scrollLeft: hsg.ie ? iebody.scrollLeft : pageXOffset,
		scrollTop: hsg.ie ? iebody.scrollTop : pageYOffset
	}
},

getPosition : function(el)	{
	var p = { x: el.offsetLeft, y: el.offsetTop };
	while (el.offsetParent)	{
		el = el.offsetParent;
		p.x += el.offsetLeft;
		p.y += el.offsetTop;
		if (el != document.body && el != document.documentElement) {
			p.x -= el.scrollLeft;
			p.y -= el.scrollTop;
		}
	}
	return p;
},

expand : function(a, params, custom, type) {
	if (!a) a = hsg.createElement('a', null, { display: 'none' }, hsg.container);
	if (typeof a.getParams == 'function') return params;	
	try {	
		new hsg.Expander(a, params, custom);
		return false;
	} catch (e) { return true; }
},
getElementByClass : function (el, tagName, className) {
	var els = el.getElementsByTagName(tagName);
	for (var i = 0; i < els.length; i++) {
    	if ((new RegExp(className)).test(els[i].className)) {
			return els[i];
		}
	}
	return null;
},
replaceLang : function(s) {
	s = s.replace(/\s/g, ' ');
	var re = /{hsg\.lang\.([^}]+)\}/g,
		matches = s.match(re),
		lang;
	if (matches) for (var i = 0; i < matches.length; i++) {
		lang = matches[i].replace(re, "$1");
		if (typeof hsg.lang[lang] != 'undefined') s = s.replace(matches[i], hsg.lang[lang]);
	}
	return s;
},


focusTopmost : function() {
	var topZ = 0, topmostKey = -1;
	for (var i = 0; i < hsg.expanders.length; i++) {
		if (hsg.expanders[i]) {
			if (hsg.expanders[i].wrapper.style.zIndex && hsg.expanders[i].wrapper.style.zIndex > topZ) {
				topZ = hsg.expanders[i].wrapper.style.zIndex;
				
				topmostKey = i;
			}
		}
	}
	if (topmostKey == -1) hsg.focusKey = -1;
	else hsg.expanders[topmostKey].focus();
},

getParam : function (a, param) {
	a.getParams = a.onclick;
	var p = a.getParams ? a.getParams() : null;
	a.getParams = null;
	
	return (p && typeof p[param] != 'undefined') ? p[param] : 
		(typeof hsg[param] != 'undefined' ? hsg[param] : null);
},

getSrc : function (a) {
	var src = hsg.getParam(a, 'src');
	if (src) return src;
	return a.href;
},

getNode : function (id) {
	var node = hsg.$(id), clone = hsg.clones[id], a = {};
	if (!node && !clone) return null;
	if (!clone) {
		clone = node.cloneNode(true);
		clone.id = '';
		hsg.clones[id] = clone;
		return node;
	} else {
		return clone.cloneNode(true);
	}
},

discardElement : function(d) {
	hsg.garbageBin.appendChild(d);
	hsg.garbageBin.innerHTML = '';
},
dim : function(exp) {
	if (!hsg.dimmer) {
		hsg.dimmer = hsg.createElement ('div', 
			{ 
				className: 'highslide-dimming',
				owner: '',
				onclick: function() {
					 
						hsg.close();
				}
			}, 
			{ position: 'absolute', left: 0 }, hsg.container, true);
		hsg.addEventListener(window, 'resize', hsg.setDimmerSize);
	}
	hsg.dimmer.style.display = '';
	hsg.setDimmerSize();
	hsg.dimmer.owner += '|'+ exp.key;
	if (hsg.geckoMac && hsg.dimmingGeckoFix) 
		hsg.dimmer.style.background = 'url('+ hsg.graphicsDir + 'geckodimmer.png)';		
	else
		hsg.fade(hsg.dimmer, 0, exp.dimmingOpacity, hsg.dimmingDuration); 
},
undim : function(key) {
	if (!hsg.dimmer) return;
	if (typeof key != 'undefined') hsg.dimmer.owner = hsg.dimmer.owner.replace('|'+ key, '');
	
	if (
		(typeof key != 'undefined' && hsg.dimmer.owner != '')
		|| (hsg.upcoming && hsg.getParam(hsg.upcoming, 'dimmingOpacity'))
	) return;
	if (hsg.geckoMac && hsg.dimmingGeckoFix) 
		hsg.setStyles(hsg.dimmer, { background: 'none', width: 0, height: 0 });
	else hsg.fade(hsg.dimmer, hsg.dimmingOpacity, 0, hsg.dimmingDuration, function() {
		hsg.setStyles(hsg.dimmer, { display: 'none', width: 0, height: 0 });
	});
},
setDimmerSize : function(exp) {
	if (!hsg.dimmer) return;
	var page = hsg.getPageSize();
	var h = (hsg.ie && exp && exp.wrapper) ? 
		parseInt(exp.wrapper.style.top) + parseInt(exp.wrapper.style.height)+ (exp.outline ? exp.outline.offset : 0) : 0; 
	hsg.setStyles(hsg.dimmer, { 
		width: page.pageWidth +'px', 
		height: Math.max(page.pageHeight, h) +'px'
	});
},
transit : function (adj, exp) {
	hsg.last = exp = exp || hsg.getExpander();
	try {
		hsg.upcoming = adj;
		adj.onclick(); 		
	} catch (e){
		hsg.last = hsg.upcoming = null;
	}
	try {
		if (!adj || exp.transitions[1] != 'crossfade')
		exp.close();
	} catch (e) {}
	return false;
},

previousOrNext : function (el, op) {
	var exp = hsg.getExpander(el),
		adj = exp.getAdjacentAnchor(op);
	return hsg.transit(adj, exp);
},

previous : function (el) {
	return hsg.previousOrNext(el, -1);
},

next : function (el) {
	return hsg.previousOrNext(el, 1);	
},

keyHandler : function(e) {
	if (!e) e = window.event;
	if (!e.target) e.target = e.srcElement; // ie
	if (typeof e.target.form != 'undefined') return true; // form element has focus
	var exp = hsg.getExpander();
	
	var op = null;
	switch (e.keyCode) {
		case 70: // f
			if (exp) exp.doFullExpand();
			return true;
		case 32: // Space
			op = 2;
			break;
		case 34: // Page Down
		case 39: // Arrow right
		case 40: // Arrow down
			op = 1;
			break;
		case 8:  // Backspace
		case 33: // Page Up
		case 37: // Arrow left
		case 38: // Arrow up
			op = -1;
			break;
		case 27: // Escape
		case 13: // Enter
			op = 0;
	}
	if (op !== null) {if (op != 2)hsg.removeEventListener(document, window.opera ? 'keypress' : 'keydown', hsg.keyHandler);
		if (!hsg.enableKeyListener) return true;
		
		if (e.preventDefault) e.preventDefault();
    	else e.returnValue = false;
    	
    	if (exp) {
			if (op == 0) {
				exp.close();
			} else if (op == 2) {
				if (exp.slideshow) exp.slideshow.hitSpace();
			} else {
				if (exp.slideshow) exp.slideshow.pause();
				hsg.previousOrNext(exp.key, op);
			}
			return false;
		}
	}
	return true;
},


registerOverlay : function (overlay) {
	hsg.push(hsg.overlays, overlay);
},


addSlideshow : function (options) {
	var sg = options.slideshowGroup;
	if (typeof sg == 'object') {
		for (var i = 0; i < sg.length; i++) {
			var o = {};
			for (var x in options) o[x] = options[x];
			o.slideshowGroup = sg[i];
			hsg.push(hsg.slideshows, o);
		}
	} else {
		hsg.push(hsg.slideshows, options);
	}
},

getWrapperKey : function (element, expOnly) {
	var el, re = /^highslide-wrapper-([0-9]+)$/;
	// 1. look in open expanders
	el = element;
	while (el.parentNode)	{
		if (el.id && re.test(el.id)) return el.id.replace(re, "$1");
		el = el.parentNode;
	}
	// 2. look in thumbnail
	if (!expOnly) {
		el = element;
		while (el.parentNode)	{
			if (el.tagName && hsg.isHsAnchor(el)) {
				for (var key = 0; key < hsg.expanders.length; key++) {
					var exp = hsg.expanders[key];
					if (exp && exp.a == el) return key;
				}
			}
			el = el.parentNode;
		}
	}
	return null; 
},

getExpander : function (el, expOnly) {
	if (typeof el == 'undefined') return hsg.expanders[hsg.focusKey] || null;
	if (typeof el == 'number') return hsg.expanders[el] || null;
	if (typeof el == 'string') el = hsg.$(el);
	return hsg.expanders[hsg.getWrapperKey(el, expOnly)] || null;
},

isHsAnchor : function (a) {
	return (a.onclick && a.onclick.toString().replace(/\s/g, ' ').match(/hsg.(htmlE|e)xpand/));
},

reOrder : function () {
	for (var i = 0; i < hsg.expanders.length; i++)
		if (hsg.expanders[i] && hsg.expanders[i].isExpanded) hsg.focusTopmost();
},

mouseClickHandler : function(e) 
{	
	if (!e) e = window.event;
	if (e.button > 1) return true;
	if (!e.target) e.target = e.srcElement;
	
	var el = e.target;
	while (el.parentNode
		&& !(/highslide-(image|move|html|resize)/.test(el.className)))
	{
		el = el.parentNode;
	}
	var exp = hsg.getExpander(el);
	if (exp && (exp.isClosing || !exp.isExpanded)) return true;
		
	if (exp && e.type == 'mousedown') {
		if (e.target.form) return true;
		var match = el.className.match(/highslide-(image|move|resize)/);
		if (match) {
			hsg.dragArgs = { exp: exp , type: match[1], left: exp.x.pos, width: exp.x.size, top: exp.y.pos, 
				height: exp.y.size, clickX: e.clientX, clickY: e.clientY };
			
			
			hsg.addEventListener(document, 'mousemove', hsg.dragHandler);
			if (e.preventDefault) e.preventDefault(); // FF
			
			if (/highslide-(image|html)-blur/.test(exp.content.className)) {
				exp.focus();
				hsg.hasFocused = true;
			}
			return false;
		}
	} else if (e.type == 'mouseup') {
		
		hsg.removeEventListener(document, 'mousemove', hsg.dragHandler);
		
		if (hsg.dragArgs) {
			if (hsg.styleRestoreCursor && hsg.dragArgs.type == 'image') 
				hsg.dragArgs.exp.content.style.cursor = hsg.styleRestoreCursor;
			var hasDragged = hsg.dragArgs.hasDragged;
			
			if (!hasDragged &&!hsg.hasFocused && !/(move|resize)/.test(hsg.dragArgs.type)) {
				exp.close();
			} 
			else if (hasDragged || (!hasDragged && hsg.hasHtmlExpanders)) {
				hsg.dragArgs.exp.doShowHide('hidden');
			}
			if (hasDragged) hsg.setDimmerSize(exp);
			
			hsg.hasFocused = false;
			hsg.dragArgs = null;
		
		} else if (/highslide-image-blur/.test(el.className)) {
			el.style.cursor = hsg.styleRestoreCursor;		
		}
	}
	return false;
},

dragHandler : function(e)
{
	if (!hsg.dragArgs) return true;
	if (!e) e = window.event;
	var a = hsg.dragArgs, exp = a.exp;
	
	a.dX = e.clientX - a.clickX;
	a.dY = e.clientY - a.clickY;	
	
	var distance = Math.sqrt(Math.pow(a.dX, 2) + Math.pow(a.dY, 2));
	if (!a.hasDragged) a.hasDragged = (a.type != 'image' && distance > 0)
		|| (distance > (hsg.dragSensitivity || 5));
	
	if (a.hasDragged && e.clientX > 5 && e.clientY > 5) {
		
		if (a.type == 'resize') exp.resize(a);
		else {
			exp.moveTo(a.left + a.dX, a.top + a.dY);
			if (a.type == 'image') exp.content.style.cursor = 'move';
		}
	}
	return false;
},

wrapperMouseHandler : function (e) {
	try {
		if (!e) e = window.event;
		var over = /mouseover/i.test(e.type); 
		if (!e.target) e.target = e.srcElement; // ie
		if (hsg.ie) e.relatedTarget = 
			over ? e.fromElement : e.toElement; // ie
		var exp = hsg.getExpander(e.target);
		if (!exp.isExpanded) return;
		if (!exp || !e.relatedTarget || hsg.getExpander(e.relatedTarget, true) == exp 
			|| hsg.dragArgs) return;
		for (var i = 0; i < exp.overlays.length; i++) {
			var o = hsg.$('hsgId'+ exp.overlays[i]);
			if (o && o.hideOnMouseOut) {
				var from = over ? 0 : o.opacity,
					to = over ? o.opacity : 0;			
				hsg.fade(o, from, to);
			}
		}	
	} catch (e) {}
},

addEventListener : function (el, event, func) {
	try {
		el.addEventListener(event, func, false);
	} catch (e) {
		try {
			el.detachEvent('on'+ event, func);
			el.attachEvent('on'+ event, func);
		} catch (e) {
			el['on'+ event] = func;
		}
	} 
},

removeEventListener : function (el, event, func) {
	try {
		el.removeEventListener(event, func, false);
	} catch (e) {
		try {
			el.detachEvent('on'+ event, func);
		} catch (e) {
			el['on'+ event] = null;
		}
	}
},

preloadFullImage : function (i) {
	if (hsg.continuePreloading && hsg.preloadTheseImages[i] && hsg.preloadTheseImages[i] != 'undefined') {
		var img = document.createElement('img');
		img.onload = function() { 
			img = null;
			hsg.preloadFullImage(i + 1);
		};
		img.src = hsg.preloadTheseImages[i];
	}
},
preloadImages : function (number) {
	if (number && typeof number != 'object') hsg.numberOfImagesToPreload = number;
	
	var arr = hsg.getAnchors();
	for (var i = 0; i < arr.images.length && i < hsg.numberOfImagesToPreload; i++) {
		hsg.push(hsg.preloadTheseImages, hsg.getSrc(arr.images[i]));
	}
	
	// preload outlines
	if (hsg.outlineType)	new hsg.Outline(hsg.outlineType, function () { hsg.preloadFullImage(0)} );
	else
	
	hsg.preloadFullImage(0);
	
	// preload cursor
	if (hsg.restoreCursor) var cur = hsg.createElement('img', { src: hsg.graphicsDir + hsg.restoreCursor });
},


init : function () {
	if (!hsg.container) {
		hsg.container = hsg.createElement('div', {
				className: 'highslide-container'
			}, {
				position: 'absolute', 
				left: 0, 
				top: 0, 
				width: '100%', 
				zIndex: hsg.zIndexCounter,
				direction: 'ltr'
			}, 
			document.body,
			true
		);
		hsg.loading = hsg.createElement('a', {
				className: 'highslide-loading',
				title: hsg.lang.loadingTitle,
				innerHTML: hsg.lang.loadingText,
				href: 'javascript:;'
			}, {
				position: 'absolute',
				top: '-9999px',
				opacity: hsg.loadingOpacity,
				zIndex: 1
			}, hsg.container
		);
		hsg.garbageBin = hsg.createElement('div', null, { display: 'none' }, hsg.container);
		
		// http://www.robertpenner.com/easing/ 
		Math.linearTween = function (t, b, c, d) {
			return c*t/d + b;
		};
		Math.easeInQuad = function (t, b, c, d) {
			return c*(t/=d)*t + b;
		};
		Math.easeInOutQuad = function (t, b, c, d) {
			if ((t/=d/2) < 1) return c/2*t*t + b;
			return -c/2 * ((--t)*(t-2) - 1) + b;
		};
		for (var x in hsg.langDefaults) {
			if (typeof hsg[x] != 'undefined') hsg.lang[x] = hsg[x];
			else if (typeof hsg.lang[x] == 'undefined' && typeof hsg.langDefaults[x] != 'undefined') 
				hsg.lang[x] = hsg.langDefaults[x];
		}
		
		hsg.hideSelects = (hsg.ie && hsg.ieVersion() < 7);
		hsg.hideIframes = ((window.opera && navigator.appVersion < 9) || navigator.vendor == 'KDE' 
			|| (hsg.ie && hsg.ieVersion() < 5.5));
	}
},
domReady : function() {
	hsg.isDomReady = true;
	if (hsg.onDomReady) hsg.onDomReady();
},

updateAnchors : function() {
	var el, els, all = [], images = [],groups = {}, re;
		
	for (var i = 0; i < hsg.openerTagNames.length; i++) {
		els = document.getElementsByTagName(hsg.openerTagNames[i]);
		for (var j = 0; j < els.length; j++) {
			el = els[j];
			re = hsg.isHsAnchor(el);
			if (re) {
				hsg.push(all, el);
				if (re[0] == 'hsg.expand') hsg.push(images, el);
				var g = hsg.getParam(el, 'slideshowGroup') || 'none';
				if (!groups[g]) groups[g] = [];
				hsg.push(groups[g], el);
			}
		}
	}
	hsg.anchors = { all: all, groups: groups, images: images };
	return hsg.anchors;
	
},

getAnchors : function() {
	return hsg.anchors || hsg.updateAnchors();
},


fade : function (el, o, oFinal, dur, fn, i, dir) {
	if (typeof i == 'undefined') { // new fader
		if (typeof dur != 'number') dur = 250;
		if (dur < 25) { // instant
			hsg.setStyles( el, { opacity: oFinal	});
			if (fn) fn();
			return;
		}
		i = hsg.faders.length;
		dir = oFinal > o ? 1 : -1;
		var step = (25 / (dur - dur % 25)) * Math.abs(o - oFinal);
	}
	o = parseFloat(o);
	var skip = (el.fade === 0 || el.fade === false || (el.fade == 2 && hsg.ie));
	el.style.visibility = ((skip ? oFinal : o) <= 0) ? 'hidden' : 'visible';
	if (skip || o < 0 || (dir == 1 && o > oFinal)) { 
		if (fn) fn();
		return;
	}
	if (el.fading && el.fading.i != i) {
		clearTimeout(hsg.faders[el.fading.i]);
		o = el.fading.o;
	}
	el.fading = {i: i, o: o, step: (step || el.fading.step)};
	el.style.visibility = (o <= 0) ? 'hidden' : 'visible';
	hsg.setStyles(el, { opacity: o });
	hsg.faders[i] = setTimeout(function() {
		hsg.fade(el, o + el.fading.step * dir, oFinal, null, fn, i, dir);
	}, 25);
},

close : function(el) {
	var exp = hsg.getExpander(el);
	if (exp) exp.close();
	return false;
}
}; // end hsg object


hsg.Outline =  function (outlineType, onLoad) {
	this.onLoad = onLoad;
	this.outlineType = outlineType;
	var v = hsg.ieVersion(), tr;
	
	this.hasAlphaImageLoader = hsg.ie && v >= 5.5 && v < 7;
	if (!outlineType) {
		if (onLoad) onLoad();
		return;
	}
	
	hsg.init();
	this.table = hsg.createElement(
		'table', { 
			cellSpacing: 0 
		}, {
			visibility: 'hidden',
			position: 'absolute',
			borderCollapse: 'collapse',
			width: 0
		},
		hsg.container,
		true
	);
	var tbody = hsg.createElement('tbody', null, null, this.table, 1);
	
	this.td = [];
	for (var i = 0; i <= 8; i++) {
		if (i % 3 == 0) tr = hsg.createElement('tr', null, { height: 'auto' }, tbody, true);
		this.td[i] = hsg.createElement('td', null, null, tr, true);
		var style = i != 4 ? { lineHeight: 0, fontSize: 0} : { position : 'relative' };
		hsg.setStyles(this.td[i], style);
	}
	this.td[4].className = outlineType +' highslide-outline';
	
	this.preloadGraphic(); 
};

hsg.Outline.prototype = {
preloadGraphic : function () {
	var src = hsg.graphicsDir + (hsg.outlinesDir || "outlines/")+ this.outlineType +".png";
				
	var appendTo = hsg.safari ? hsg.container : null;
	this.graphic = hsg.createElement('img', null, { position: 'absolute', 
		top: '-9999px' }, appendTo, true); // for onload trigger
	
	var pThis = this;
	this.graphic.onload = function() { pThis.onGraphicLoad(); };
	
	this.graphic.src = src;
},

onGraphicLoad : function () {
	var o = this.offset = this.graphic.width / 4,
		pos = [[0,0],[0,-4],[-2,0],[0,-8],0,[-2,-8],[0,-2],[0,-6],[-2,-2]],
		dim = { height: (2*o) +'px', width: (2*o) +'px' };
	for (var i = 0; i <= 8; i++) {
		if (pos[i]) {
			if (this.hasAlphaImageLoader) {
				var w = (i == 1 || i == 7) ? '100%' : this.graphic.width +'px';
				var div = hsg.createElement('div', null, { width: '100%', height: '100%', position: 'relative', overflow: 'hidden'}, this.td[i], true);
				hsg.createElement ('div', null, { 
						filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale, src='"+ this.graphic.src + "')", 
						position: 'absolute',
						width: w, 
						height: this.graphic.height +'px',
						left: (pos[i][0]*o)+'px',
						top: (pos[i][1]*o)+'px'
					}, 
				div,
				true);
			} else {
				hsg.setStyles(this.td[i], { background: 'url('+ this.graphic.src +') '+ (pos[i][0]*o)+'px '+(pos[i][1]*o)+'px'});
			}
			
			if (window.opera && (i == 3 || i ==5)) 
				hsg.createElement('div', null, dim, this.td[i], true);
			
			hsg.setStyles (this.td[i], dim);
		}
	}
	this.graphic = null;
	if (hsg.pendingOutlines[this.outlineType]) hsg.pendingOutlines[this.outlineType].destroy();
	hsg.pendingOutlines[this.outlineType] = this;
	if (this.onLoad) this.onLoad();
},
	
setPosition : function (exp, pos, vis) {
	pos = pos || {
		x: exp.x.pos,
		y: exp.y.pos,
		w: exp.x.size + exp.x.p1 + exp.x.p2,
		h: exp.y.size + exp.y.p1 + exp.y.p2
	};
	if (vis) this.table.style.visibility = (pos.h >= 4 * this.offset) 
		? 'visible' : 'hidden';
	hsg.setStyles(this.table, {
		left: (pos.x - this.offset) +'px',
		top: (pos.y - this.offset) +'px',
		width: (pos.w + 2 * (exp.x.cb + this.offset)) +'px'
	});
	
	pos.w += 2 * (exp.x.cb - this.offset);
	pos.h += + 2 * (exp.y.cb - this.offset);
	hsg.setStyles (this.td[4], {
		width: pos.w >= 0 ? pos.w +'px' : 0,
		height: pos.h >= 0 ? pos.h +'px' : 0
	});
	if (this.hasAlphaImageLoader) this.td[3].style.height 
		= this.td[5].style.height = this.td[4].style.height;
},
	
destroy : function(hide) {
	if (hide) this.table.style.visibility = 'hidden';
	else hsg.discardElement(this.table);
}
};

hsg.Dimension = function(exp, dim) {
	this.exp = exp;
	this.dim = dim;
	this.ucwh = dim == 'x' ? 'Width' : 'Height';
	this.wh = this.ucwh.toLowerCase();
	this.uclt = dim == 'x' ? 'Left' : 'Top';
	this.lt = this.uclt.toLowerCase();
	this.ucrb = dim == 'x' ? 'Right' : 'Bottom';
	this.rb = this.ucrb.toLowerCase();
	this.p1 = this.p2 = 0;
};
hsg.Dimension.prototype = {
get : function(key) {
	switch (key) {
		case 'loadingPos':
			return this.tpos + this.tb + (this.t - hsg.loading['offset'+ this.ucwh]) / 2;
		case 'loadingPosXfade':
			return this.pos + this.cb+ this.p1 + (this.size - hsg.loading['offset'+ this.ucwh]) / 2;
		case 'wsize':
			return this.size + 2 * this.cb + this.p1 + this.p2;
		case 'fitsize':
			return this.clientSize - this.marginMin - this.marginMax;
		case 'opos':
			return this.pos - (this.exp.outline ? this.exp.outline.offset : 0);
		case 'osize':
			return this.get('wsize') + (this.exp.outline ? 2*this.exp.outline.offset : 0);
		case 'imgPad':
			return this.imgSize ? Math.round((this.size - this.imgSize) / 2) : 0;
		
	}
},
calcBorders: function() {
	// correct for borders
	this.cb = (this.exp.content['offset'+ this.ucwh] - this.t) / 2;
	this.marginMax = hsg['margin'+ this.ucrb] + 2 * this.cb;
},
calcThumb: function() {
	this.t = this.exp.el[this.wh] ? parseInt(this.exp.el[this.wh]) : 
		this.exp.el['offset'+ this.ucwh];
	this.tpos = this.exp.tpos[this.dim];
	this.tb = (this.exp.el['offset'+ this.ucwh] - this.t) / 2;
	if (this.tpos == 0) {
		this.tpos = (hsg.page[this.wh] / 2) + hsg.page['scroll'+ this.uclt];		
	};
},
calcExpanded: function() {
	var exp = this.exp;
	this.justify = 'auto';
	
	// get alignment
	if (exp.align == 'center') this.justify = 'center';
	else if (new RegExp(this.lt).test(exp.anchor)) this.justify = null;
	else if (new RegExp(this.rb).test(exp.anchor)) this.justify = 'max';
	
	
	// size and position
	this.pos = this.tpos - this.cb + this.tb;
	this.size = Math.min(this.full, exp['max'+ this.ucwh] || this.full);
	this.minSize = exp.allowSizeReduction ? 
		Math.min(exp['min'+ this.ucwh], this.full) :this.full;
	if (exp.useBox)	{
		this.size = exp[this.wh];
		this.imgSize = this.full;
	}
	if (this.dim == 'x' && hsg.padToMinWidth) this.minSize = exp.minWidth;
	this.target = exp['target'+ this.dim.toUpperCase()];
	this.marginMin = hsg['margin'+ this.uclt];
	this.scroll = hsg.page['scroll'+ this.uclt];
	this.clientSize = hsg.page[this.wh];
},
setSize: function(i) {
	var exp = this.exp;
	if (exp.isImage && (exp.useBox || hsg.padToMinWidth)) {
		this.imgSize = i;
		this.size = Math.max(this.size, this.imgSize);
		exp.content.style[this.lt] = this.get('imgPad')+'px';
	} else
	this.size = i;

	exp.content.style[this.wh] = i +'px';
	exp.wrapper.style[this.wh] = this.get('wsize') +'px';
	if (exp.outline) exp.outline.setPosition(exp);
	if (this.dim == 'x' && exp.overlayBox) exp.sizeOverlayBox(true);
	if (this.dim == 'x' && exp.slideshow && exp.isImage) {
		if (i == this.full) exp.slideshow.disable('full-expand');
		else exp.slideshow.enable('full-expand');
	}
},
setPos: function(i) {
	this.pos = i;
	this.exp.wrapper.style[this.lt] = i +'px';	
	
	if (this.exp.outline) this.exp.outline.setPosition(this.exp);
	
}
};

hsg.Expander = function(a, params, custom, contentType) {
	if (document.readyState && hsg.ie && !hsg.isDomReady) {
		hsg.onDomReady = function() {
			new hsg.Expander(a, params, custom, contentType);
		};
		return;
	} 
	this.a = a;
	this.custom = custom;
	this.contentType = contentType || 'image';
	this.isImage = !this.isHtml;
	
	hsg.continuePreloading = false;
	this.overlays = [];
	this.last = hsg.last;
	hsg.last = null;
	hsg.init();
	var key = this.key = hsg.expanders.length;
	
	// override inline parameters
	for (var i = 0; i < hsg.overrides.length; i++) {
		var name = hsg.overrides[i];
		this[name] = params && typeof params[name] != 'undefined' ?
			params[name] : hsg[name];
	}
	if (!this.src) this.src = a.href;
	
	// get thumb
	var el = (params && params.thumbnailId) ? hsg.$(params.thumbnailId) : a;
	el = this.thumb = el.getElementsByTagName('img')[0] || el;
	this.thumbsUserSetId = el.id || a.id;
	
	// check if already open
	for (var i = 0; i < hsg.expanders.length; i++) {
		if (hsg.expanders[i] && hsg.expanders[i].a == a 
			&& !(this.last && this.transitions[1] == 'crossfade')) {
			hsg.expanders[i].focus();
			return false;
		}
	}	

	// cancel other
	for (var i = 0; i < hsg.expanders.length; i++) {
		if (hsg.expanders[i] && hsg.expanders[i].thumb != el && !hsg.expanders[i].onLoadStarted) {
			hsg.expanders[i].cancelLoading();
		}
	}
	hsg.expanders[this.key] = this;
	if (!hsg.allowMultipleInstances && !hsg.upcoming) {
		if (hsg.expanders[key-1]) hsg.expanders[key-1].close();
		if (typeof hsg.focusKey != 'undefined' && hsg.expanders[hsg.focusKey])
			hsg.expanders[hsg.focusKey].close();
	}
	
	// initiate metrics
	this.el = el;
	this.tpos = hsg.getPosition(el);
	hsg.page = hsg.getPageSize();
	var x = this.x = new hsg.Dimension(this, 'x');
	x.calcThumb();
	var y = this.y = new hsg.Dimension(this, 'y');
	y.calcThumb();
	
	// instanciate the wrapper
	this.wrapper = hsg.createElement(
		'div', {
			id: 'highslide-wrapper-'+ this.key,
			className: this.wrapperClassName
		}, {
			visibility: 'hidden',
			position: 'absolute',
			zIndex: hsg.zIndexCounter++
		}, null, true );
	
	this.wrapper.onmouseover = this.wrapper.onmouseout = hsg.wrapperMouseHandler;
	if (this.contentType == 'image' && this.outlineWhileAnimating == 2)
		this.outlineWhileAnimating = 0;
	
	// get the outline
	if (!this.outlineType 
		|| (this.last && this.isImage && this.transitions[1] == 'crossfade')) {
		this[this.contentType +'Create']();
	
	} else if (hsg.pendingOutlines[this.outlineType]) {
		this.connectOutline();
		this[this.contentType +'Create']();
	
	} else {
		this.showLoading();
		var exp = this;
		new hsg.Outline(this.outlineType, 
			function () {
				exp.connectOutline();
				exp[exp.contentType +'Create']();
			} 
		);
	}
	return true;
};

hsg.Expander.prototype = {

connectOutline : function() {
	var o = this.outline = hsg.pendingOutlines[this.outlineType];
	o.table.style.zIndex = this.wrapper.style.zIndex;
	hsg.pendingOutlines[this.outlineType] = null;
},

showLoading : function() {
	if (this.onLoadStarted || this.loading) return;
	
	this.loading = hsg.loading;
	var exp = this;
	this.loading.onclick = function() {
		exp.cancelLoading();
	};
	var exp = this, 
		l = this.x.get('loadingPos') +'px',
		t = this.y.get('loadingPos') +'px';
	if (!tgt && this.last && this.transitions[1] == 'crossfade') 
		var tgt = this.last; 
	if (tgt) {
		l = tgt.x.get('loadingPosXfade') +'px';
		t = tgt.y.get('loadingPosXfade') +'px';
		this.loading.style.zIndex = hsg.zIndexCounter++;
	}
	setTimeout(function () { 
		if (exp.loading) hsg.setStyles(exp.loading, { left: l, top: t, zIndex: hsg.zIndexCounter++ })}
	, 100);
},

imageCreate : function() {
	var exp = this;
	
	var img = document.createElement('img');
    this.content = img;
    img.onload = function () {
    	if (hsg.expanders[exp.key]) exp.contentLoaded(); 
	};
    if (hsg.blockRightClick) img.oncontextmenu = function() { return false; };
    img.className = 'highslide-image';
    hsg.setStyles(img, {
    	visibility: 'hidden',
    	display: 'block',
    	position: 'absolute',
		maxWidth: '9999px',
		zIndex: 3
	});
    img.title = hsg.lang.restoreTitle;
    if (hsg.safari) hsg.container.appendChild(img);
    if (hsg.ie && hsg.flushImgSize) img.src = null;
	img.src = this.src;
	
	this.showLoading();
},

contentLoaded : function() {
	try {	
		if (!this.content) return;
		this.content.onload = null;
		if (this.onLoadStarted) return;
		else this.onLoadStarted = true;
		
		var x = this.x, y = this.y;
		
		if (this.loading) {
			hsg.setStyles(this.loading, { top: '-9999px' });
			this.loading = null;
		}
		
		hsg.setStyles (this.wrapper, {
			left: x.tpos +'px',
			top: y.tpos +'px'
		});	
			x.full = this.content.width;
			y.full = this.content.height;
			
			hsg.setStyles(this.content, {
				width: this.x.t +'px',
				height: this.y.t +'px'
			});
			this.wrapper.appendChild(this.content);
			hsg.container.appendChild(this.wrapper);
		
		x.calcBorders();
		y.calcBorders();
		
		
		this.initSlideshow();
		this.getOverlays();
		
		var ratio = x.full / y.full;
		
		x.calcExpanded();
		this.justify(x);
		
		y.calcExpanded();
		this.justify(y);
		if (this.overlayBox) this.sizeOverlayBox(0, 1);
		
		if (this.allowSizeReduction) {
				this.correctRatio(ratio);
			var ss = this.slideshow;			
			if (ss && this.last && ss.controls && ss.fixedControls) {
				var pos = ss.overlayOptions.position || '', p;
				for (var dim in hsg.oPos) for (var i = 0; i < 5; i++) {
					p = this[dim];
					if (pos.match(hsg.oPos[dim][i])) {
						p.pos = this.last[dim].pos 
							+ (this.last[dim].p1 - p.p1)
							+ (this.last[dim].size - p.size) * [0, 0, .5, 1, 1][i];
						if (ss.fixedControls == 'fit') {
							if (p.pos + p.size + p.p1 + p.p2 > p.scroll + p.clientSize - p.marginMax)
								p.pos = p.scroll + p.clientSize - p.size - p.marginMin - p.marginMax - p.p1 - p.p2;
							if (p.pos < p.scroll + p.marginMin) p.pos = p.scroll + p.marginMin; 
						} 
					}
				}
			}
			if (this.isImage && this.x.full > (this.x.imgSize || this.x.size)) {
				this.createFullExpand();
				if (this.overlays.length == 1) this.sizeOverlayBox();
			}
		}
		this.show();
		
	} catch (e) {
		window.location.href = this.src;
	}
},

justify : function (p, moveOnly) {
	var tgtArr, tgt = p.target, dim = p == this.x ? 'x' : 'y';
	
	if (tgt && tgt.match(/ /)) {
		tgtArr = tgt.split(' ');
		tgt = tgtArr[0];
	}
	if (tgt && hsg.$(tgt)) {
		p.pos = hsg.getPosition(hsg.$(tgt))[dim];
		if (tgtArr && tgtArr[1] && tgtArr[1].match(/^[-]?[0-9]+px$/)) 
			p.pos += parseInt(tgtArr[1]);
		
	} else if (p.justify == 'auto' || p.justify == 'center') {
	
		var hasMovedMin = false;
		
		var allowReduce = p.exp.allowSizeReduction;
		if (p.justify == 'center')
			p.pos = Math.round(p.scroll + (p.clientSize - p.get('wsize')) / 2);
		else
			p.pos = Math.round(p.pos - ((p.get('wsize') - p.t) / 2));
		if (p.pos < p.scroll + p.marginMin) {
			p.pos = p.scroll + p.marginMin;
			hasMovedMin = true;		
		}
		if (!moveOnly && p.size < p.minSize) {
			p.size = p.minSize;
			allowReduce = false;
		}
		if (p.pos + p.get('wsize') > p.scroll + p.clientSize - p.marginMax) {
			if (!moveOnly && hasMovedMin && allowReduce) {
				p.size = p.get('fitsize')- 2 * p.cb - p.p1 - p.p2; // can't expand more
			} else if (p.get('wsize') < p.get('fitsize')) {
				p.pos = p.scroll + p.clientSize - p.marginMax - p.get('wsize');
			} else { // image larger than viewport
				p.pos = p.scroll + p.marginMin;
				if (!moveOnly && allowReduce) p.size = p.get('fitsize')- 2 * p.cb - p.p1 - p.p2;
			}			
		}
		
		if (!moveOnly && p.size < p.minSize) {
			p.size = p.minSize;
			allowReduce = false;
		}
		
	
	} else if (p.justify == 'max') {
		p.pos = Math.floor(p.pos - p.size + p.t);
	}
	
		
	if (p.pos < p.marginMin) {
		var tmpMin = p.pos;
		p.pos = p.marginMin; 
		
		if (allowReduce && !moveOnly) p.size = p.size - (p.pos - tmpMin);
		
	}
},

correctRatio : function(ratio) {
	var x = this.x, 
		y = this.y,
		changed = false,
		xSize = Math.min(x.full, x.size),
		ySize = Math.min(y.full, y.size),
		useBox = (this.useBox || hsg.padToMinWidth);
	
	if (xSize / ySize > ratio) { // width greater
		xSize = ySize * ratio;
		if (xSize < x.minSize) { // below minWidth
			xSize = x.minSize;
			ySize = xSize / ratio;
		}
		changed = true;
	
	} else if (xSize / ySize < ratio) { // height greater
		ySize = xSize / ratio;
		changed = true;
	}
	
	if (hsg.padToMinWidth && x.full < x.minSize) {
		x.imgSize = x.full;
		y.size = y.imgSize = y.full;
	} else if (this.useBox) {
		x.imgSize = xSize;
		y.imgSize = ySize;
	} else {
		x.size = xSize;
		y.size = ySize;
	}
	this.fitOverlayBox(useBox ? null : ratio);
	if (useBox && y.size < y.imgSize) {
		y.imgSize = y.size;
		x.imgSize = y.size * ratio;
	}
	if (changed || useBox) {
		x.pos = x.tpos - x.cb + x.tb;
		x.minSize = x.size;
		this.justify(x, true);
	
		y.pos = y.tpos - y.cb + y.tb;
		y.minSize = y.size;
		this.justify(y, true);
		if (this.overlayBox) this.sizeOverlayBox();
	}
},
fitOverlayBox : function(ratio) {
	var x = this.x, y = this.y;
	if (this.overlayBox) {
		while (y.size > this.minHeight && x.size > this.minWidth 
				&&  y.get('wsize') > y.get('fitsize')) {
			y.size -= 10;
			if (ratio) x.size = y.size * ratio;
			this.sizeOverlayBox(0, 1);
		}
	}
},

show : function () {
	this.doShowHide('hidden');
	// Apply size change
	this.changeSize(
		1,
		{ 
			xpos: this.x.tpos + this.x.tb - this.x.cb,
			ypos: this.y.tpos + this.y.tb - this.y.cb,
			xsize: this.x.t,
			ysize: this.y.t,
			xp1: 0,
			xp2: 0,
			yp1: 0,
			yp2: 0,
			ximgSize: this.x.t,
			ximgPad: 0,
			yimgSize: this.y.t,
			yimgPad: 0,
			o: hsg.outlineStartOffset
		},
		{
			xpos: this.x.pos,
			ypos: this.y.pos,
			xsize: this.x.size,
			ysize: this.y.size,
			xp1: this.x.p1,
			yp1: this.y.p1,
			xp2: this.x.p2,
			yp2: this.y.p2,
			ximgSize: this.x.imgSize,
			ximgPad: this.x.get('imgPad'),
			yimgSize: this.y.imgSize,
			yimgPad: this.y.get('imgPad'),
			o: this.outline ? this.outline.offset : 0
		},
		hsg.expandDuration
	);
},

changeSize : function(up, from, to, dur) {
	// transition
	var trans = this.transitions,
	other = up ? (this.last ? this.last.a : null) : hsg.upcoming,
	t = (trans[1] && other 
			&& hsg.getParam(other, 'transitions')[1] == trans[1]) ?
		trans[1] : trans[0];
		
	if (this[t] && t != 'expand') {
		this[t](up, from, to);
		return;
	}
	if (up) hsg.setStyles(this.wrapper, { opacity: 1 });
	
	if (this.outline && !this.outlineWhileAnimating) {
		if (up) this.outline.setPosition(this);
		else this.outline.destroy();
	}
	
	
	if (!up && this.overlayBox) {
		if (this.slideshow) {
			var c = this.slideshow.controls;
			if (c && hsg.getExpander(c) == this) c.parentNode.removeChild(c);
		}
		hsg.discardElement(this.overlayBox);
	}
	if (this.fadeInOut) {
		from.op = up ? 0 : 1;
		to.op = up;
	}
	var t,
		exp = this,
		easing = Math[this.easing] || Math.easeInQuad,
		steps = (up ? hsg.expandSteps : hsg.restoreSteps) || parseInt(dur / 25) || 1;
	if (!up) easing = Math[this.easingClose] || easing;
	for (var i = 1; i <= steps ; i++) {
		t = Math.round(i * (dur / steps));
		
		(function(){
			var pI = i, size = {};
			
			for (var x in from) {
				size[x] = easing(t, from[x], to[x] - from[x], dur);
				if (isNaN(size[x])) size[x] = to[x];
				if (!/^op$/.test(x)) size[x] = Math.round(size[x]);
			}
			setTimeout ( function() {
				if (up && pI == 1) {
					exp.content.style.visibility = 'visible';
					exp.a.className += ' highslide-active-anchor';
				}
				exp.setSize(size);
			}, t);				
		})();
	}
	
	if (up) { 
			
		setTimeout(function() {
			if (exp.outline) exp.outline.table.style.visibility = "visible";
		}, t);
		setTimeout(function() {
			exp.afterExpand();
		}, t + 50);
	}
	else setTimeout(function() { exp.afterClose(); }, t);
},

setSize : function (to) {
	try {
		if (to.op) hsg.setStyles(this.wrapper, { opacity: to.op });
		hsg.setStyles ( this.wrapper, {
			width : (to.xsize +to.xp1 + to.xp2 +
				2 * this.x.cb) +'px',
			height : (to.ysize +to.yp1 + to.yp2 +
				2 * this.y.cb) +'px',
			left: to.xpos +'px',
			top: to.ypos +'px'
		});
		hsg.setStyles(this.content, {
			left: (to.xp1 + to.ximgPad) +'px',
			top: (to.yp1 + to.yimgPad) +'px',
			width: (to.ximgSize ||to.xsize) +'px',
			height: (to.yimgSize ||to.ysize) +'px'
		});
		
		if (this.outline && this.outlineWhileAnimating) {
			var o = this.outline.offset - to.o;
			this.outline.setPosition(this, {
				x: to.xpos + o, 
				y: to.ypos + o, 
				w: to.xsize + to.xp1 + to.xp2 + - 2 * o, 
				h: to.ysize + to.yp1 + to.yp2 + - 2 * o
			}, 1);
		}
			
		this.wrapper.style.visibility = 'visible';
		
	} catch (e) {
		window.location.href = this.src;	
	}
},

fade : function(up, from, to) {
	this.outlineWhileAnimating = false;
	var exp = this,	t = up ? 250 : 0;
	
	if (up) {
		hsg.setStyles(this.wrapper, { opacity: 0 });
		this.setSize(to);
		this.content.style.visibility = 'visible';

		hsg.fade (this.wrapper, 0, 1);
	}
	
	if (this.outline) {
		this.outline.table.style.zIndex = this.wrapper.style.zIndex;
		var dir = up || -1;
		for (var i = from.o; dir * i <= dir * to.o; i += dir, t += 25) {
			(function() {
				var o = up ? to.o - i : from.o - i;
				setTimeout(function() {
					exp.outline.setPosition(exp, {
						x: (exp.x.pos + o), 
						y: (exp.y.pos + o),
						w: (exp.x.size - 2 * o + exp.x.p1 + exp.x.p2), 
						h: (exp.y.size - 2 * o + exp.y.p1 + exp.y.p2)
					}, 1);
				}, t);
			})();
		}
	}
	
	
	if (up) setTimeout(function() { exp.afterExpand(); }, t+50);
	else {
		setTimeout( function() {
			if (exp.outline) exp.outline.destroy(exp.preserveContent);
			hsg.fade (exp.wrapper, 1, 0);
			setTimeout( function() {
				exp.afterClose();
			}, 250);
		}, t);		
	}
},

crossfade : function (up, from, to) {
	
	if (!up) return;
	var exp = this, steps = parseInt(hsg.transitionDuration / 25) || 1, last = this.last;
	hsg.removeEventListener(document, 'mousemove', hsg.dragHandler);
	
	hsg.setStyles(this.content, { 
		width: (to.ximgSize ||to.xsize) +'px', 
		height: (to.yimgSize ||to.ysize) +'px'		
	});
	this.outline = this.last.outline;
	this.last.outline = null;
	this.fadeBox = hsg.createElement('div', {
		className: 'highslide-image'
	}, { 
		position: 'absolute', 
		zIndex: 4,
		overflow: 'hidden',
		display: 'none'
	});
	var names = { oldImg: last, newImg: this };
	for (var x in names) { 	
		this[x] = names[x].content.cloneNode(1);
		hsg.setStyles(this[x], {
			position: 'absolute',
			border: 0,
			visibility: 'visible'
		});
		this.fadeBox.appendChild(this[x]);
	}
	this.wrapper.appendChild(this.fadeBox);
	from = {
		xpos: last.x.pos,
		xsize: last.x.size,
		xp1: last.x.p1,
		xp2: last.x.p2,
		ximgSize: last.x.imgSize || last.x.size,
		ximgPad: last.x.get('imgPad'),
		yimgSize: last.y.imgSize || last.y.size,
		yimgPad: last.y.get('imgPad'),
		ypos: last.y.pos,
		ysize: last.y.size,
		yp1: last.y.p1,
		yp2: last.y.p2,
		o: 1 / steps
	};
	to.ysize = this.y.size;
	to.o = 1;
	if (!to.ximgSize) to.ximgSize = to.xsize;
	if (!to.yimgSize) to.yimgSize = to.ysize;
	
	var t, easing = Math.easeInOutQuad;
	
	if (steps > 1) this.crossfadeStep(from);
	function prep() {
		if (exp.overlayBox) {
			exp.overlayBox.className = '';
			exp.overlayBox.style.overflow = 'visible';
			exp.wrapper.appendChild(exp.overlayBox);
				
			for (var i = 0; i < exp.last.overlays.length; i++) {
				var oDiv = hsg.$('hsgId'+ exp.last.overlays[i]);
				if (oDiv.reuse === exp.key) exp.overlayBox.appendChild(oDiv);
				else hsg.fade(oDiv, oDiv.opacity, 0);
			}
		}
		exp.fadeBox.style.display = '';
		exp.last.content.style.display = 'none';
	};
	if (/rv:1\.[0-8].+Gecko/.test(navigator.userAgent)) setTimeout(prep, 0);
	else prep();
	if (hsg.safari) {
		var match = navigator.userAgent.match(/Safari\/([0-9]{3})/);
		if (match && parseInt(match[1]) < 525) this.wrapper.style.visibility = 'visible';
	}  
	
	for (var i = 1; i <= steps; i++) {
		t = Math.round(i * (hsg.transitionDuration / steps));
		
		(function(){
			var size = {}, pI = i;
			for (var x in from)	{
				var val = easing(t, from[x], to[x] - from[x], hsg.transitionDuration);
				if (isNaN(val)) val = to[x];
				size[x] = (x != 'o') ? Math.round(val) : val;
			}
			
			setTimeout ( function() {
				exp.crossfadeStep(size);
			}, t);				
		})();
	}
	setTimeout ( function () {
		exp.crossfadeEnd();
	}, t + 100);

},

crossfadeStep : function (size) {
	try {
		if (this.outline) this.outline.setPosition(this, { 
			x: size.xpos, 
			y: size.ypos, 
			w: size.xsize + size.xp1 + size.xp2, 
			h: size.ysize + size.yp1 + size.yp2
		}, 1);
		this.last.wrapper.style.clip = 'rect('
			+ (size.ypos - this.last.y.pos)+'px, '
			+ (size.xsize + size.xp1 + size.xp2 + size.xpos + 2 * this.last.x.cb - this.last.x.pos) +'px, '
			+ (size.ysize + size.yp1 + size.yp2 + size.ypos + 2 * this.last.y.cb - this.last.y.pos) +'px, '
			+ (size.xpos - this.last.x.pos)+'px)';
			
			
		hsg.setStyles(this.content, {
			top: (size.yp1 + this.y.get('imgPad')) +'px',
			left: (size.xp1 + this.x.get('imgPad')) +'px',
			marginTop: (this.y.pos - size.ypos) +'px',
			marginLeft: (this.x.pos - size.xpos) +'px'
		});
		
		hsg.setStyles(this.wrapper, {
			top: size.ypos +'px',
			left: size.xpos +'px',
			width: (size.xp1 + size.xp2 + size.xsize + 2 * this.x.cb)+ 'px',
			height: (size.yp1 + size.yp2 + size.ysize + 2 * this.y.cb) + 'px'
		});
		hsg.setStyles(this.fadeBox, {
			width: (size.ximgSize || size.xsize) + 'px',
			height: (size.yimgSize || size.ysize) +'px',
			left: (size.xp1 + size.ximgPad)  +'px',
			top: (size.yp1 + size.yimgPad) +'px',
			visibility: 'visible'
		});
		
		hsg.setStyles(this.oldImg, {
			top: (this.last.y.pos - size.ypos + this.last.y.p1 - size.yp1 +
				this.last.y.get('imgPad') - size.yimgPad)+'px',
			left: (this.last.x.pos - size.xpos + this.last.x.p1 - size.xp1 + 
				this.last.x.get('imgPad') - size.ximgPad)+'px'
		});		
		
		hsg.setStyles(this.newImg, {
			opacity: size.o,
			top: (this.y.pos - size.ypos + this.y.p1 - size.yp1 + this.y.get('imgPad') - size.yimgPad) +'px',
			left: (this.x.pos - size.xpos + this.x.p1 - size.xp1 + this.x.get('imgPad') - size.ximgPad) +'px'
		});
		hsg.setStyles(this.overlayBox, {
			width: size.xsize + 'px',
			height: size.ysize +'px',
			left: (size.xp1 + this.x.cb)  +'px',
			top: (size.yp1 + this.y.cb) +'px'
		});
	} catch (e) {}
},
crossfadeEnd : function() {
	this.wrapper.style.background = this.wrapperBG || '';
	
	this.wrapper.style.visibility = this.content.style.visibility = 'visible';
	this.fadeBox.style.display = 'none';
	this.a.className += ' highslide-active-anchor';
	this.afterExpand();
	this.last.afterClose();
},
reuseOverlay : function(o, el) {
	if (!this.last) return false;
	for (var i = 0; i < this.last.overlays.length; i++) {
		var oDiv = hsg.$('hsgId'+ this.last.overlays[i]);
		if (oDiv && oDiv.hsgId == o.hsgId) {
			this.genOverlayBox();
			oDiv.reuse = this.key;
			hsg.push(this.overlays, this.last.overlays[i]);
			return true;
		}
	}
	return false;
},


afterExpand : function() {
	this.isExpanded = true;	
	this.focus();
	
	if (this.dimmingOpacity) hsg.dim(this);
	if (hsg.upcoming && hsg.upcoming == this.a) hsg.upcoming = null;
	this.prepareNextOutline();
	
	
	var p = hsg.page, mX = hsg.mouse.x + p.scrollLeft, mY = hsg.mouse.y + p.scrollTop;
	this.mouseIsOver = this.x.pos < mX && mX < this.x.pos + this.x.get('wsize')
		&& this.y.pos < mY && mY < this.y.pos + this.y.get('wsize');
	
	if (this.overlayBox) this.showOverlays();
	
},


prepareNextOutline : function() {
	var key = this.key;
	var outlineType = this.outlineType;
	new hsg.Outline(outlineType, 
		function () { try { hsg.expanders[key].preloadNext(); } catch (e) {} });
},


preloadNext : function() {
	var next = this.getAdjacentAnchor(1);
	if (next && next.onclick.toString().match(/hsg\.expand/)) 
		var img = hsg.createElement('img', { src: hsg.getSrc(next) });
},


getAdjacentAnchor : function(op) {
	var current = this.getAnchorIndex(), as = hsg.anchors.groups[this.slideshowGroup || 'none'];
	
	/*< ? if ($cfg->slideshow) : ?>s*/
	if (!as[current + op] && this.slideshow && this.slideshow.repeat) {
		if (op == 1) return as[0];
		else if (op == -1) return as[as.length-1];
	}
	/*< ? endif ?>s*/
	return as[current + op] || null;
},

getAnchorIndex : function() {
	var arr = hsg.anchors.groups[this.slideshowGroup || 'none'];
	for (var i = 0; i < arr.length; i++) {
		if (arr[i] == this.a) return i; 
	}
	return null;
},


getNumber : function() {
	if (this[this.numberPosition]) {
		var arr = hsg.anchors.groups[this.slideshowGroup || 'none'];
		var s = hsg.lang.number.replace('%1', this.getAnchorIndex() + 1).replace('%2', arr.length);
		this[this.numberPosition].innerHTML = 
			'<div class="highslide-number">'+ s +'</div>'+ this[this.numberPosition].innerHTML;
	}
},
initSlideshow : function() {
	if (!this.last) {
		for (var i = 0; i < hsg.slideshows.length; i++) {
			var ss = hsg.slideshows[i], sg = ss.slideshowGroup;
			if (typeof sg == 'undefined' || sg === null || sg === this.slideshowGroup) 
				this.slideshow = new hsg.Slideshow(ss);
		} 
	} else {
		this.slideshow = this.last.slideshow;
	}
	var ss = this.slideshow;
	if (!ss) return;
	var exp = ss.exp = this;
	
	ss.checkFirstAndLast();
	ss.disable('full-expand');
	if (ss.controls) {
		var o = ss.overlayOptions || {};
		o.overlayId = ss.controls;
		o.hsgId = 'controls';		
		this.createOverlay(o);
	}
	if (!this.last && this.autoplay) ss.play(true);
	if (ss.autoplay) {
		ss.autoplay = setTimeout(function() {
			hsg.next(exp.key);
		}, (ss.interval || 500));
	}
},

cancelLoading : function() {	
	hsg.expanders[this.key] = null;
	if (hsg.upcoming == this.a) hsg.upcoming = null;
	hsg.undim(this.key);
	if (this.loading) hsg.loading.style.left = '-9999px';
},

writeCredits : function () {
	if (this.credits) return;
	this.credits = hsg.createElement('a', {
		href: hsg.creditsHref,
		className: 'highslide-credits',
		innerHTML: hsg.lang.creditsText,
		title: hsg.lang.creditsTitle
	});
	this.createOverlay({ 
		overlayId: this.credits, 
		position: 'top left', 
		hsgId: 'credits' 
	});
},

getInline : function(types, addOverlay) {
	for (var i = 0; i < types.length; i++) {
		var type = types[i], s = null;
		if (!this[type +'Id'] && this.thumbsUserSetId)  
			this[type +'Id'] = type +'-for-'+ this.thumbsUserSetId;
		if (this[type +'Id']) this[type] = hsg.getNode(this[type +'Id']);
		if (!this[type] && !this[type +'Text'] && this[type +'Eval']) try {
			s = eval(this[type +'Eval']);
		} catch (e) {}
		if (!this[type] && this[type +'Text']) {
			s = this[type +'Text'];
		}
		if (!this[type] && !s) {
			var next = this.a.nextSibling;
			while (next && !hsg.isHsAnchor(next)) {
				if ((new RegExp('highslide-'+ type)).test(next.className || null)) {
					this[type] = next.cloneNode(1);
					break;
				}
				next = next.nextSibling;
			}
		}
		if (!this[type] && !s && this.numberPosition == type) s = '\n';
		
		if (!this[type] && s) this[type] = hsg.createElement('div', 
				{ className: 'highslide-'+ type, innerHTML: s } );
		
		if (addOverlay && this[type]) {
			var o = { position: (type == 'heading') ? 'above' : 'below' };
			for (var x in this[type+'Overlay']) o[x] = this[type+'Overlay'][x];
			o.overlayId = this[type];
			this.createOverlay(o);
		}
	}
},


// on end move and resize
doShowHide : function(visibility) {
	if (hsg.hideSelects) this.showHideElements('SELECT', visibility);
	if (hsg.hideIframes) this.showHideElements('IFRAME', visibility);
	if (hsg.geckoMac) this.showHideElements('*', visibility);
},
showHideElements : function (tagName, visibility) {
	var els = document.getElementsByTagName(tagName);
	var prop = tagName == '*' ? 'overflow' : 'visibility';
	for (var i = 0; i < els.length; i++) {
		if (prop == 'visibility' || (document.defaultView.getComputedStyle(
				els[i], "").getPropertyValue('overflow') == 'auto'
				|| els[i].getAttribute('hidden-by') != null)) {
			var hiddenBy = els[i].getAttribute('hidden-by');
			if (visibility == 'visible' && hiddenBy) {
				hiddenBy = hiddenBy.replace('['+ this.key +']', '');
				els[i].setAttribute('hidden-by', hiddenBy);
				if (!hiddenBy) els[i].style[prop] = els[i].origProp;
			} else if (visibility == 'hidden') { // hide if behind
				var elPos = hsg.getPosition(els[i]);
				elPos.w = els[i].offsetWidth;
				elPos.h = els[i].offsetHeight;
				if (!this.dimmingOpacity) { // hide all if dimming
				
					var clearsX = (elPos.x + elPos.w < this.x.get('opos') 
						|| elPos.x > this.x.get('opos') + this.x.get('osize'));
					var clearsY = (elPos.y + elPos.h < this.y.get('opos') 
						|| elPos.y > this.y.get('opos') + this.y.get('osize'));
				}
				var wrapperKey = hsg.getWrapperKey(els[i]);
				if (!clearsX && !clearsY && wrapperKey != this.key) { // element falls behind image
					if (!hiddenBy) {
						els[i].setAttribute('hidden-by', '['+ this.key +']');
						els[i].origProp = els[i].style[prop];
						els[i].style[prop] = 'hidden';
						
					} else if (hiddenBy.indexOf('['+ this.key +']') == -1) {
						els[i].setAttribute('hidden-by', hiddenBy + '['+ this.key +']');
					}
				} else if ((hiddenBy == '['+ this.key +']' || hsg.focusKey == wrapperKey)
						&& wrapperKey != this.key) { // on move
					els[i].setAttribute('hidden-by', '');
					els[i].style[prop] = els[i].origProp || '';
				} else if (hiddenBy && hiddenBy.indexOf('['+ this.key +']') > -1) {
					els[i].setAttribute('hidden-by', hiddenBy.replace('['+ this.key +']', ''));
				}
						
			}
		}
	}
},

focus : function() {
	this.wrapper.style.zIndex = hsg.zIndexCounter++;
	// blur others
	for (var i = 0; i < hsg.expanders.length; i++) {
		if (hsg.expanders[i] && i == hsg.focusKey) {
			var blurExp = hsg.expanders[i];
			blurExp.content.className += ' highslide-'+ blurExp.contentType +'-blur';
				blurExp.content.style.cursor = hsg.ie ? 'hand' : 'pointer';
				blurExp.content.title = hsg.lang.focusTitle;
		}
	}
	
	// focus this
	if (this.outline) this.outline.table.style.zIndex 
		= this.wrapper.style.zIndex;
	this.content.className = 'highslide-'+ this.contentType;
		this.content.title = hsg.lang.restoreTitle;
		
		if (hsg.restoreCursor) {
			hsg.styleRestoreCursor = window.opera ? 'pointer' : 'url('+ hsg.graphicsDir + hsg.restoreCursor +'), pointer';
			if (hsg.ie && hsg.ieVersion() < 6) hsg.styleRestoreCursor = 'hand';
			this.content.style.cursor = hsg.styleRestoreCursor;
		}
		
	hsg.focusKey = this.key;	
	hsg.addEventListener(document, window.opera ? 'keypress' : 'keydown', hsg.keyHandler);	
},
moveTo: function(x, y) {
	this.x.setPos(x);
	this.y.setPos(y);
},
resize : function (e) {
	var w, h, r = e.width / e.height;
	w = Math.max(e.width + e.dX, Math.min(this.minWidth, this.x.full));
	if (this.isImage && Math.abs(w - this.x.full) < 12) w = this.x.full;
	h = w / r;
	if (h < Math.min(this.minHeight, this.y.full)) {
		h = Math.min(this.minHeight, this.y.full);
		if (this.isImage) w = h * r;
	}
	this.resizeTo(w, h);
},
resizeTo: function(w, h) {
	this.y.setSize(h);
	this.x.setSize(w);
},

close : function() {
	if (this.isClosing || !this.isExpanded) return;
	if (this.transitions[1] == 'crossfade' && hsg.upcoming) {
		hsg.getExpander(hsg.upcoming).cancelLoading();
		hsg.upcoming = null;
	}
	this.isClosing = true;
	if (this.slideshow && !hsg.upcoming) this.slideshow.pause();
	
	hsg.removeEventListener(document, window.opera ? 'keypress' : 'keydown', hsg.keyHandler);
	
	try {
		this.content.style.cursor = 'default';
		this.changeSize(
			0, {
				xpos: this.x.pos,
				ypos: this.y.pos,
				xsize: this.x.size,
				ysize: this.y.size,
				xp1: this.x.p1,
				yp1: this.y.p1,
				xp2: this.x.p2,
				yp2: this.y.p2,
				ximgSize: this.x.imgSize,
				ximgPad: this.x.get('imgPad'),
				yimgSize: this.y.imgSize,
				yimgPad: this.y.get('imgPad'),
				o: this.outline ? this.outline.offset : 0
			}, {
				xpos: this.x.tpos - this.x.cb + this.x.tb,
				ypos: this.y.tpos - this.y.cb + this.y.tb,
				xsize: this.x.t,
				ysize: this.y.t,
				xp1: 0,
				yp1: 0,
				xp2: 0,
				yp2: 0,
				ximgSize: this.x.imgSize ? this.x.t : null,
				ximgPad: 0,
				yimgSize: this.y.imgSize ? this.y.t : null,
				yimgPad: 0,
				o: hsg.outlineStartOffset
			},
			hsg.restoreDuration
		);
		
	} catch (e) { this.afterClose(); } 
},

createOverlay : function (o) {
	var el = o.overlayId;
	if (typeof el == 'string') el = hsg.getNode(el);
	if (!el || typeof el == 'string') return;
	el.style.display = 'block';
	o.hsgId = o.hsgId || o.overlayId; 
	if (this.transitions[1] == 'crossfade' && this.reuseOverlay(o, el)) return;
	this.genOverlayBox();
	var width = o.width && /^[0-9]+(px|%)$/.test(o.width) ? o.width : 'auto';
	if (/^(left|right)panel$/.test(o.position) && !/^[0-9]+px$/.test(o.width)) width = '200px';
	
	var overlay = hsg.createElement(
		'div', { 
			id: 'hsgId'+ hsg.idCounter++, hsgId: o.hsgId
		}, {
			position: 'absolute',
			visibility: 'hidden',
			width: width,
			direction: hsg.lang.cssDirection || ''
		},
		this.overlayBox,
		true
	);
	
	overlay.appendChild(el);
	hsg.setAttribs(overlay, {
		hideOnMouseOut: o.hideOnMouseOut,
		opacity: o.opacity || 1,
		hsgPos: o.position,
		fade: o.fade
	});
	
	if (this.gotOverlays) {
		this.positionOverlay(overlay);
		if (!overlay.hideOnMouseOut || this.mouseIsOver) hsg.fade(overlay, 0, overlay.opacity);
	}
	hsg.push(this.overlays, hsg.idCounter - 1);
},
positionOverlay : function(overlay) {
	var p = overlay.hsgPos || 'middle center';
	if (/left$/.test(p)) overlay.style.left = 0; 
	if (/center$/.test(p))	hsg.setStyles (overlay, { 
		left: '50%',
		marginLeft: '-'+ Math.round(overlay.offsetWidth / 2) +'px'
	});	
	if (/right$/.test(p))	overlay.style.right = 0;
	
	if (/^leftpanel$/.test(p)) { 
		hsg.setStyles(overlay, {
			right: '100%',
			marginRight: this.x.cb +'px',
			top: - this.y.cb +'px',
			bottom: - this.y.cb +'px',
			overflow: 'auto'
		});		 
		this.x.p1 = overlay.offsetWidth;
	
	} else if (/^rightpanel$/.test(p)) {
		hsg.setStyles(overlay, {
			left: '100%',
			marginLeft: this.x.cb +'px',
			top: - this.y.cb +'px',
			bottom: - this.y.cb +'px',
			overflow: 'auto'
		});
		this.x.p2 = overlay.offsetWidth;
	}
	if (/^top/.test(p)) overlay.style.top = 0; 
	if (/^middle/.test(p))	hsg.setStyles (overlay, { 
		top: '50%', 
		marginTop: '-'+ Math.round(overlay.offsetHeight / 2) +'px'
	});	
	if (/^bottom/.test(p)) overlay.style.bottom = 0;
	if (/^above$/.test(p)) {
		hsg.setStyles(overlay, {
			left: (- this.x.p1 - this.x.cb) +'px',
			right: (- this.x.p2 - this.x.cb) +'px',
			bottom: '100%',
			marginBottom: this.y.cb +'px',
			width: 'auto'
		});
		this.y.p1 = overlay.offsetHeight;
	
	} else if (/^below$/.test(p)) {
		hsg.setStyles(overlay, {
			position: 'relative',
			left: (- this.x.p1 - this.x.cb) +'px',
			right: (- this.x.p2 - this.x.cb) +'px',
			top: '100%',
			marginTop: this.y.cb +'px',
			width: 'auto'
		});
		this.y.p2 = overlay.offsetHeight;
		overlay.style.position = 'absolute';
	}
},

getOverlays : function() {	
	this.getInline(['heading', 'caption'], true);
	this.getNumber();
	if (this.heading && this.dragByHeading) this.heading.className += ' highslide-move';
	if (hsg.showCredits) this.writeCredits();
	for (var i = 0; i < hsg.overlays.length; i++) {
		var o = hsg.overlays[i], tId = o.thumbnailId, sg = o.slideshowGroup;
		if ((!tId && !sg) || (tId && tId == this.thumbsUserSetId)
				|| (sg && sg === this.slideshowGroup)) {
			this.createOverlay(o);
		}
	}
	var os = [];
	for (var i = 0; i < this.overlays.length; i++) {
		var o = hsg.$('hsgId'+ this.overlays[i]);
		if (/panel$/.test(o.hsgPos)) this.positionOverlay(o);
		else hsg.push(os, o);
	}
	for (var i = 0; i < os.length; i++) this.positionOverlay(os[i]);
	this.gotOverlays = true;
},
genOverlayBox : function() {
	if (!this.overlayBox) this.overlayBox = hsg.createElement (
		'div', {
			className: this.wrapperClassName
		}, {
			position : 'absolute',
			width: this.x.size ? this.x.size +'px' : this.x.full +'px',
			height: 0,
			visibility : 'hidden',
			overflow : 'hidden',
			zIndex : hsg.ie ? 4 : null
		},
		hsg.container,
		true
	);
},
sizeOverlayBox : function(doWrapper, doPanels) {
	hsg.setStyles( this.overlayBox, {
		width: this.x.size +'px', 
		height: this.y.size +'px'
	});
	if (doWrapper || doPanels) {
		for (var i = 0; i < this.overlays.length; i++) {
			var o = hsg.$('hsgId'+ this.overlays[i]);
			var ie6 = (hsg.ie && (hsg.ieVersion() <= 6 || document.compatMode == 'BackCompat'));
			if (o && /^(above|below)$/.test(o.hsgPos)) {
				if (ie6) {
					o.style.width = (this.overlayBox.offsetWidth + 2 * this.x.cb
						+ this.x.p1 + this.x.p2) +'px';
				}
				this.y[o.hsgPos == 'above' ? 'p1' : 'p2'] = o.offsetHeight;
			}
			if (o && ie6 && /^(left|right)panel$/.test(o.hsgPos)) {
				o.style.height = (this.overlayBox.offsetHeight + 2 * this.y.cb
						+ this.y.p1 + this.y.p2) +'px';
			}
		}
	}
	if (doWrapper) {
		hsg.setStyles(this.content, {
			top: this.y.p1 +'px'
		});
		hsg.setStyles(this.overlayBox, {
			top: (this.y.p1 + this.y.cb) +'px'
		});
	}
},

showOverlays : function() {
	var b = this.overlayBox;
	b.className = '';
	hsg.setStyles(b, {
		top: (this.y.p1 + this.y.cb) +'px',
		left: (this.x.p1 + this.x.cb) +'px',
		overflow : 'visible'
	});
	if (hsg.safari) b.style.visibility = 'visible';
	this.wrapper.appendChild (b);
	for (var i = 0; i < this.overlays.length; i++) {
		var o = hsg.$('hsgId'+ this.overlays[i]);
		o.style.zIndex = o.hsgId == 'controls' ? 5 : 4;
		if (!o.hideOnMouseOut || this.mouseIsOver) hsg.fade(o, 0, o.opacity);
	}
},



createFullExpand : function () {
	if (this.slideshow && this.slideshow.controls) {
		this.slideshow.enable('full-expand');
		return;
	}
	this.fullExpandLabel = hsg.createElement(
		'a', {
			href: 'javascript:hsg.expanders['+ this.key +'].doFullExpand();',
			title: hsg.lang.fullExpandTitle,
			className: 'highslide-full-expand'
		}
	);
	
	this.createOverlay({ 
		overlayId: this.fullExpandLabel, 
		position: hsg.fullExpandPosition, 
		hideOnMouseOut: true, 
		opacity: hsg.fullExpandOpacity
	});
},

doFullExpand : function () {
	try {
		if (this.fullExpandLabel) hsg.discardElement(this.fullExpandLabel);
		
		this.focus();
		var xSize = this.x.size;
		this.resizeTo(this.x.full, this.y.full);
		
		var xpos = this.x.pos - (this.x.size - xSize) / 2;
		if (xpos < hsg.marginLeft) xpos = hsg.marginLeft;
		
		this.moveTo(xpos, this.y.pos);
		this.doShowHide('hidden');
		hsg.setDimmerSize(this);
	
	} catch (e) {
		window.location.href = this.content.src;
	}
},


afterClose : function () {
	this.a.className = this.a.className.replace('highslide-active-anchor', '');
	
	this.doShowHide('visible');
		if (this.outline && this.outlineWhileAnimating) this.outline.destroy();
	
		hsg.discardElement(this.wrapper);
	if (this.dimmingOpacity) hsg.undim(this.key);
	hsg.expanders[this.key] = null;		
	hsg.reOrder();
}

};


hsg.Slideshow = function (options) {
	if (hsg.dynamicallyUpdateAnchors !== false) hsg.updateAnchors();
	for (var x in options) this[x] = options[x];
	if (this.useControls) this.getControls();
};
hsg.Slideshow.prototype = {
getControls: function() {
	this.controls = hsg.createElement('div', { innerHTML: hsg.replaceLang(hsg.skin.controls) }, 
		null, hsg.container);
	
	var buttons = ['previous', 'next', 'full-expand', 'close']; //'play', 'pause', 'move', 
	this.btn = {};
	var pThis = this;
	for (var i = 0; i < buttons.length; i++) {
		this.btn[buttons[i]] = hsg.getElementByClass(this.controls, 'li', 'highslide-'+ buttons[i]);
		this.enable(buttons[i]);
	}
	//this.btn.pause.style.display = 'none';
	//this.disable('full-expand');
},
checkFirstAndLast: function() {
	if (this.repeat || !this.controls) return;
	var cur = this.exp.getAnchorIndex(), re = /disabled$/;
	if (cur == 0) 
		this.disable('previous');
	else if (re.test(this.btn.previous.getElementsByTagName('a')[0].className))
		this.enable('previous');
	if (cur + 1 == hsg.anchors.groups[this.exp.slideshowGroup || 'none'].length) {
		this.disable('next');
		//this.disable('play');
	} else if (re.test(this.btn.next.getElementsByTagName('a')[0].className)) {
		this.enable('next');
		//this.enable('play');
	}
},
enable: function(btn) {
	if (!this.btn) return;
	var sls = this, a = this.btn[btn].getElementsByTagName('a')[0], re = /disabled$/;
	a.onclick = function() {
		sls[btn]();
		return false;
	};
	if (re.test(a.className)) a.className = a.className.replace(re, '');
},
disable: function(btn) {
	if (!this.btn) return;
	var a = this.btn[btn].getElementsByTagName('a')[0];
	a.onclick = function() { return false; };
	if (!/disabled$/.test(a.className)) a.className += ' disabled';
},
hitSpace: function() {
	if (this.autoplay) this.pause();
	else this.play();
},
play: function(wait) {
	if (this.btn) {
		//this.btn.play.style.display = 'none';
		//this.btn.pause.style.display = '';
	}
	
	this.autoplay = true;	
	if (!wait) hsg.next(this.exp.key);
},
pause: function() {
	if (this.btn) {
		//this.btn.pause.style.display = 'none';
		//this.btn.play.style.display = '';
	}
	
	clearTimeout(this.autoplay);
	this.autoplay = null;
},
previous: function() {
	this.pause();
	hsg.previous(this.btn.previous);
},
next: function() {
	this.pause();
	hsg.next(this.btn.next);
},
move: function() {},
'full-expand': function() {
	hsg.getExpander().doFullExpand();
},
close: function() {
	hsg.close(this.btn.close);
}

};
if (document.readyState && hsg.ie) {
	(function () {
		try {
			document.documentElement.doScroll('left');
		} catch (e) {
			setTimeout(arguments.callee, 50);
			return;
		}
		hsg.domReady();
	})();
}
hsg.langDefaults = hsg.lang;
// history
var HsExpander = hsg.Expander;

// set handlers
hsg.addEventListener(window, 'load', function() {
	if (hsg.expandCursor) {
		var sel = '.highslide img', 
			dec = 'cursor: url('+ hsg.graphicsDir + hsg.expandCursor +'), pointer !important;';
			
		var style = hsg.createElement('style', { type: 'text/css' }, null, 
			document.getElementsByTagName('HEAD')[0]);
	
		if (!hsg.ie) {
			style.appendChild(document.createTextNode(sel + " {" + dec + "}"));
		} else {
			var last = document.styleSheets[document.styleSheets.length - 1];
			if (typeof(last.addRule) == "object") last.addRule(sel, dec);
		}
	}
});
hsg.addEventListener(document, 'mousemove', function(e) {
	hsg.mouse = { x: e.clientX, y: e.clientY	};
});
hsg.addEventListener(document, 'mousedown', hsg.mouseClickHandler);
hsg.addEventListener(document, 'mouseup', hsg.mouseClickHandler);
hsg.addEventListener(window, 'load', hsg.preloadImages);