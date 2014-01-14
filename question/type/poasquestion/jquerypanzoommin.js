/**
 * @license jquery.panzoom.js v1.8.1
 * Updated: Mon Nov 11 2013
 * Add pan and zoom functionality to any element
 * Copyright (c) 2013 timmy willison
 * Released under the MIT license
 * https://github.com/timmywil/jquery.panzoom/blob/master/MIT-License.txt
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],b):"object"==typeof exports?b(require("jquery")):b(a.jQuery)}(this,function(a){"use strict";function b(b){var c={range:!0,animate:!0};return"boolean"==typeof b?c.animate=b:a.extend(c,b),c}function c(b,c,d,e,f,g,h,i,j){this.elements="array"===a.type(b)?[+b[0],+b[2],+b[4],+b[1],+b[3],+b[5],0,0,1]:[b,c,d,e,f,g,h||0,i||0,j||1]}function d(a,b,c){this.elements=[a,b,c]}function e(b,c){1!==b.nodeType&&a.error("Panzoom called on non-Element node"),a.contains(document,b)||a.error("Panzoom element must be attached to the document");var d=a.data(b,g);if(d)return d;if(!(this instanceof e))return new e(b,c);this.options=c=a.extend({},e.defaults,c),this.elem=b;var f=this.$elem=a(b);this.$doc=a(b.ownerDocument||document),this.$parent=f.parent(),this.isSVG=j.test(b.namespaceURI)&&"svg"!==b.nodeName.toLowerCase(),this.panning=!1,this._buildTransform(),this.isSVG||(this._transform=a.cssProps.transform.replace(i,"-$1").toLowerCase()),this._buildTransition(),this.resetDimensions();var h=a(),k=this;a.each(["$zoomIn","$zoomOut","$zoomRange","$reset"],function(a,b){k[b]=c[b]||h}),this.enable(),a.data(b,g,this)}var f={props:["touches","pageX","pageY"],filter:function(a,b){var c;return!b.pageX&&b.touches&&(c=b.touches[0])&&(a.pageX=c.pageX,a.pageY=c.pageY),a}};a.each(["touchstart","touchmove","touchend"],function(b,c){a.event.fixHooks[c]=f});var g="__pz__",h=Array.prototype.slice,i=/([A-Z])/g,j=/^http:[\w\.\/]+svg$/,k=/^inline/,l="(\\-?[\\d\\.e]+)",m="\\,?\\s*",n=new RegExp("^matrix\\("+l+m+l+m+l+m+l+m+l+m+l+"\\)$");return c.prototype={x:function(a){var b=a instanceof d,e=this.elements,f=a.elements;return b&&3===f.length?new d(e[0]*f[0]+e[1]*f[1]+e[2]*f[2],e[3]*f[0]+e[4]*f[1]+e[5]*f[2],e[6]*f[0]+e[7]*f[1]+e[8]*f[2]):f.length===e.length?new c(e[0]*f[0]+e[1]*f[3]+e[2]*f[6],e[0]*f[1]+e[1]*f[4]+e[2]*f[7],e[0]*f[2]+e[1]*f[5]+e[2]*f[8],e[3]*f[0]+e[4]*f[3]+e[5]*f[6],e[3]*f[1]+e[4]*f[4]+e[5]*f[7],e[3]*f[2]+e[4]*f[5]+e[5]*f[8],e[6]*f[0]+e[7]*f[3]+e[8]*f[6],e[6]*f[1]+e[7]*f[4]+e[8]*f[7],e[6]*f[2]+e[7]*f[5]+e[8]*f[8]):!1},inverse:function(){var a=1/this.determinant(),b=this.elements;return new c(a*(b[8]*b[4]-b[7]*b[5]),a*-(b[8]*b[1]-b[7]*b[2]),a*(b[5]*b[1]-b[4]*b[2]),a*-(b[8]*b[3]-b[6]*b[5]),a*(b[8]*b[0]-b[6]*b[2]),a*-(b[5]*b[0]-b[3]*b[2]),a*(b[7]*b[3]-b[6]*b[4]),a*-(b[7]*b[0]-b[6]*b[1]),a*(b[4]*b[0]-b[3]*b[1]))},determinant:function(){var a=this.elements;return a[0]*(a[8]*a[4]-a[7]*a[5])-a[3]*(a[8]*a[1]-a[7]*a[2])+a[6]*(a[5]*a[1]-a[4]*a[2])}},d.prototype.e=c.prototype.e=function(a){return this.elements[a]},e.rmatrix=n,e.defaults={eventNamespace:".panzoom",transition:!0,cursor:"move",disablePan:!1,disableZoom:!1,increment:.3,minScale:.4,maxScale:5,rangeStep:.05,duration:200,easing:"ease-in-out",contain:!1},e.prototype={constructor:e,instance:function(){return this},enable:function(){this._initStyle(),this._bind(),this.disabled=!1},disable:function(){this.disabled=!0,this._resetStyle(),this._unbind()},isDisabled:function(){return this.disabled},destroy:function(){this.disable(),a.removeData(this.elem,g)},resetDimensions:function(){var b=this.$parent;this.container={width:b.width(),height:b.height()};var c=this.elem,d=this.$elem,e=this.dimensions=this.isSVG?{left:c.getAttribute("x")||0,top:c.getAttribute("y")||0,width:c.getAttribute("width")||d.outerWidth(),height:c.getAttribute("height")||d.outerHeight()}:{left:a.css(c,"left",!0)||0,top:a.css(c,"top",!0)||0,width:d.outerWidth(),height:d.outerHeight()};e.widthBorder=a.css(c,"borderLeftWidth",!0)+a.css(c,"borderRightWidth",!0)||0,e.heightBorder=a.css(c,"borderTopWidth",!0)+a.css(c,"borderBottomWidth",!0)||0},reset:function(a){a=b(a);var c=this.setMatrix(this._origTransform,a);a.silent||this._trigger("reset",c)},resetZoom:function(a){a=b(a);var c=this.getMatrix(this._origTransform);a.dValue=c[3],this.zoom(c[0],a)},resetPan:function(a){var c=this.getMatrix(this._origTransform);this.pan(c[4],c[5],b(a))},getTransform:function(b){var c=this.elem,d=this.isSVG?"attr":"style";return b?a[d](c,"transform",b):b=a[d](c,"transform"),"none"===b||n.test(b)||this.isSVG||(b=a.css(c,"transform"),a.style(c,"transform",b)),b||"none"},getMatrix:function(a){var b=n.exec(a||this.getTransform());return b&&b.shift(),b||[1,0,0,1,0,0]},setMatrix:function(b,c){if(!this.disabled){c||(c={}),"string"==typeof b&&(b=this.getMatrix(b));var d,e,f,g,h,i,j=+b[0],l=this.$parent,m=this.elem,n="undefined"!=typeof c.contain?c.contain:this.options.contain;return n&&(d=this._checkDims(),e=this.container,f=(d.width*j-e.width)/2,g=(d.height*j-e.height)/2,"invert"===n?(h=d.width>e.width?d.width-e.width:0,i=d.height>e.height?d.height-e.height:0,f+=(e.width-d.width)/2,g+=(e.height-d.height)/2,b[4]=Math.max(Math.min(b[4],f-d.left),-f-d.left-h),b[5]=Math.max(Math.min(b[5],g-d.top),-g-d.top-i+d.heightBorder)):(i=e.height>d.height?e.height-d.height:0,"center"===l.css("textAlign")&&k.test(a.css(m,"display"))?h=0:(h=e.width>d.width?e.width-d.width:0,f=g=0),b[4]=Math.min(Math.max(b[4],f-d.left),-f-d.left+h),b[5]=Math.min(Math.max(b[5],g-d.top),-g-d.top+i))),"skip"!==c.animate&&this.transition(!c.animate),c.range&&this.$zoomRange.val(j),a[this.isSVG?"attr":"style"](m,"transform","matrix("+b.join(",")+")"),c.silent||this._trigger("change",b),b}},isPanning:function(){return this.panning},transition:function(b){var c=b||!this.options.transition?"none":this._transition;a.style(this.elem,"transition")!==c&&a.style(this.elem,"transition",c)},pan:function(a,b,c){if(!this.options.disablePan){c||(c={});var d=c.matrix;d||(d=this.getMatrix()),c.relative?(d[4]=+d[4]+a,d[5]=+d[5]+b):(d[4]=a,d[5]=b),this.setMatrix(d,c),c.silent||this._trigger("pan",a,b)}},zoom:function(b,e){"object"==typeof b?(e=b,b=null):e||(e={});var f=a.extend({},this.options,e);if(!f.disableZoom){var g=!1,h=f.matrix||this.getMatrix();"number"!=typeof b&&(b=+h[0]+f.increment*(b?-1:1),g=!0),b>f.maxScale?b=f.maxScale:b<f.minScale&&(b=f.minScale);var i=f.focal;if(i&&!f.disablePan){g=!1;var j=this.container,k=i.clientX-j.width/2,l=i.clientY-j.height/2,m=new d(k,l,1),n=new c(h),o=this.$parent.offset(),p=new c(1,0,o.left-this.$doc.scrollLeft(),0,1,o.top-this.$doc.scrollTop()),q=n.inverse().x(p.inverse().x(m)),r=b/h[0];n=n.x(new c([r,0,0,r,0,0])),m=p.x(n.x(q)),h[4]=+h[4]+(k-m.e(0)),h[5]=+h[5]+(l-m.e(1))}h[0]=b,h[3]="number"==typeof f.dValue?f.dValue:b,this.setMatrix(h,{animate:"boolean"==typeof f.animate?f.animate:g,range:!f.noSetRange}),f.silent||this._trigger("zoom",b,f)}},option:function(b,c){var d;if(!b)return a.extend({},this.options);if("string"==typeof b){if(1===arguments.length)return void 0!==this.options[b]?this.options[b]:null;d={},d[b]=c}else d=b;this._setOptions(d)},_setOptions:function(b){var c=this;a.each(b,function(b,d){switch(b){case"disablePan":c._resetStyle();case"disableZoom":case"$zoomIn":case"$zoomOut":case"$zoomRange":case"$reset":case"onStart":case"onChange":case"onZoom":case"onPan":case"onEnd":case"onReset":case"eventNamespace":c._unbind()}switch(c.options[b]=d,b){case"disablePan":c._initStyle();case"disableZoom":case"$zoomIn":case"$zoomOut":case"$zoomRange":case"$reset":case"onStart":case"onChange":case"onZoom":case"onPan":case"onEnd":case"onReset":case"eventNamespace":c._bind();break;case"cursor":a.style(c.elem,"cursor",d);break;case"minScale":c.$zoomRange.attr("min",d);break;case"maxScale":c.$zoomRange.attr("max",d);break;case"rangeStep":c.$zoomRange.attr("step",d);break;case"startTransform":c._buildTransform();break;case"duration":case"easing":c._buildTransition();case"transition":c.transition()}})},_initStyle:function(){this.options.disablePan||this.$elem.css("cursor",this.options.cursor);var b=this.$parent;if(b.length&&!a.nodeName(b[0],"body")){var c={overflow:"hidden"};"static"===b.css("position")&&(c.position="relative"),b.css(c)}},_resetStyle:function(){this.$elem.css({cursor:"",transition:""}),this.$parent.css({overflow:"",position:""})},_bind:function(){var b=this,c=this.options,d=c.eventNamespace,f="touchstart"+d+" mousedown"+d,g="touchend"+d+" click"+d,h={},i=this.$reset,j=this.$zoomRange;if(a.each(["Start","Change","Zoom","Pan","End","Reset"],function(){var b=c["on"+this];a.isFunction(b)&&(h["panzoom"+this.toLowerCase()+d]=b)}),c.disablePan&&c.disableZoom||(h[f]=function(a){var d;("mousedown"===a.type?c.disablePan||1!==a.which:!(d=a.touches)||(1!==d.length||c.disablePan)&&2!==d.length)||(a.preventDefault(),a.stopPropagation(),b._startMove(a,d))}),this.$elem.on(h),i.length&&i.on(g,function(a){a.preventDefault(),b.reset()}),j.length&&j.attr({step:c.rangeStep===e.defaults.rangeStep&&j.attr("step")||c.rangeStep,min:c.minScale,max:c.maxScale}).prop({value:this.getMatrix()[0]}),!c.disableZoom){var k=this.$zoomIn,l=this.$zoomOut;k.length&&l.length&&(k.on(g,function(a){a.preventDefault(),b.zoom()}),l.on(g,function(a){a.preventDefault(),b.zoom(!0)})),j.length&&(h={},h["mousedown"+d]=function(){b.transition(!0)},h["change"+d]=function(){b.zoom(+this.value,{noSetRange:!0})},j.on(h))}},_unbind:function(){this.$elem.add(this.$zoomIn).add(this.$zoomOut).add(this.$reset).off(this.options.eventNamespace)},_buildTransform:function(){return this._origTransform=this.getTransform(this.options.startTransform)},_buildTransition:function(){var a=this.options;this._transform&&(this._transition=this._transform+" "+a.duration+"ms "+a.easing)},_checkDims:function(){var a=this.dimensions;return(a.width===a.widthBorder||a.height===a.heightBorder)&&this.resetDimensions(),this.dimensions},_getDistance:function(a){var b=a[0],c=a[1];return Math.sqrt(Math.pow(Math.abs(c.clientX-b.clientX),2)+Math.pow(Math.abs(c.clientY-b.clientY),2))},_getMiddle:function(a){var b=a[0],c=a[1];return{clientX:(c.clientX-b.clientX)/2+b.clientX,clientY:(c.clientY-b.clientY)/2+b.clientY}},_trigger:function(a){"string"==typeof a&&(a="panzoom"+a),this.$elem.triggerHandler(a,[this].concat(h.call(arguments,1)))},_startMove:function(b,c){var d,e,f,g,h,i,j=this,k=this.options,l="touchstart"===b.type,m=k.eventNamespace,n=(l?"touchmove":"mousemove")+m,o=(l?"touchend":"mouseup")+m,p=this.getMatrix(),q=p.slice(0),r=+q[4],s=+q[5],t={matrix:p,animate:"skip"};this.transition(!0),this.panning=!0,this._trigger("start",b,c),c&&2===c.length?(e=this._getDistance(c),f=+p[0],g=this._getMiddle(c),d=function(a){a.preventDefault();var b=j._getMiddle(c=a.touches),d=j._getDistance(c)-e;j.zoom(d*(k.increment/100)+f,{focal:b,matrix:p}),j.pan(+p[4]+b.clientX-g.clientX,+p[5]+b.clientY-g.clientY,t),g=b}):(h=b.pageX,i=b.pageY,d=function(a){a.preventDefault(),j.pan(r+a.pageX-h,s+a.pageY-i,t)}),a(document).off(m).on(n,d).on(o,function(b){b.preventDefault(),a(this).off(m),j.panning=!1,b.type="panzoomend",j._trigger(b,p,!!a(q).not(p).length)})}},a.fn.panzoom=function(b){var c,d,f,i;return"string"==typeof b?(i=[],d=h.call(arguments,1),this.each(function(){c=a.data(this,g),c?"_"!==b.charAt(0)&&"function"==typeof(f=c[b])&&void 0!==(f=f.apply(c,d))&&i.push(f):i.push(void 0)}),i.length?1===i.length?i[0]:i:this):this.each(function(){new e(this,b)})},e});