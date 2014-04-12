/**
 * jquery.textareaHighlighter
 * This plugin allow you to select text in textarea with custom color and keep this selection until text is changed.
 * See the readme at [git](https://github.com/pahomovda/jquery.textareaHighlighter).
 *
 * Copyright (c) 2014 Pahomov Dmitry
 * Released under the MIT license
 * https://github.com/pahomovda/jquery.textareaHighlighter/blob/master/LICENSE
 */

/*var get_selection = function (id) {
    var scroll = $(window).scrollTop(),
        selection = $(id).textrange('get'),
        indfirst = selection.start,
        indlast = selection.end - 1;
    if (indfirst > indlast) {
        indfirst = indlast = -2;
    }
    //$(window).scrollTop(scroll);
    return {
        indfirst: indfirst,
        indlast: indlast
    };
};*/

(function( $ ) {
    var PLUGINNAME = 'textareaHighlighter';
    var PLUGINDATAKEY = PLUGINNAME+'data';
    var EVENTNAMESPACE = '.'+PLUGINNAME+'_evt';
    $.fn[PLUGINNAME] = function( method ) {
        var argsclosure = arguments;
        $this = $(this);
        var data = $this.data(PLUGINDATAKEY);
        var methods = {
            init: function (options) {
                var settings = $.extend( {
                    fontsize: 14,
                    cols: 65,
                    rows: 3
                }, options);
                data = {
                    _helperel: null,
                    _textareael: null,
                    _fontsize: settings.fontsize,
                    _linesize: settings.fontsize+2,
                    _ishelperhidden: false
                };
                var content = this.val() || '';
                var id = this.attr('id') || PLUGINNAME+Math.random().toString(36).substr(2, 5);
                var bgcolor = this.css('backgroundColor') || 'yellow';
                var fontstyle = 'normal '+data._fontsize+'px/'+data._linesize +'px "Courier New",Courier,monospace';
                var cssfortextareael = {
                    'margin': '0',
                    'padding': '0',
                    'position': 'relative',
                    'border': '1px solid #999999',
                    'outline': 'none',
                    'overflow': 'scroll',
                    'width': '100%',
                    'background': 'transparent',
                    'resize': 'none',
                    'font': fontstyle
                };
                var cssforhelperel = {
                    'position': 'absolute',
                    'background': bgcolor,
                    'white-space': 'pre',
                    'border': '1px solid transparent',
                    'color': 'transparent',
                    'font': fontstyle,
                    'overflow': 'scroll'
                };
                data._textareael = $('<textarea cols="'+settings.cols+'" rows="'+settings.rows+'" wrap="off">' + content + '</textarea>')
                    .attr('id', id)
                    .bind('keyup'+EVENTNAMESPACE, methods._textareaonchange)
                    .bind('paste'+EVENTNAMESPACE, function(e) {setTimeout(function () {methods._textareaonchange(e);}, 1);});

                this.replaceWith(data._textareael);
                // this is not valid!
                data._textareael.resizable({resize: methods._textarearesize})
                    .bind('scroll'+EVENTNAMESPACE, methods._textareascroll)
                    .css(cssfortextareael); // TODO - remove chain?
                data._helperel = $('<span></span>')
                    .css(cssforhelperel)
                    .insertBefore(data._textareael);
                data._textareael.data(PLUGINDATAKEY, data);
                data._textareael.keyup();
                return data._textareael[0];
            },
            destroy: function() {
                $(window).undind(EVENTNAMESPACE);
                data._helperel.remove();
                data._textareael.resizable('destroy');
                data._textareael.remove();
                $this.removeData(PLUGINDATAKEY);
            },
            _textareascroll: function(e) {
                if (!data._ishelperhidden) {
                    data._helperel.scrollTop($this.scrollTop());
                    data._helperel.scrollLeft($this.scrollLeft());
                }
            },
            _textarearesize: function(e) {
                if (!data._ishelperhidden) {
                    data._helperel.height($this.height());
                    data._helperel.width($this.width());
                }
            },
            _textareaonchange: function (e) {
                methods._hidehelper.apply(this);
            },
            _hidehelper: function () {
                if (!data._ishelperhidden) {
                    data._helperel.hide();
                    data._ishelperhidden = true;
                }
            },
            _showhelper: function (target) {
                if (data._ishelperhidden) {
                    data._helperel.show();
                    data._ishelperhidden = false;
                    $this.resizable('option', 'resize').apply($this.parent()); // magic....
                }
            },
            highlight: function (start, end, color) {
                console.log('highlight '+start+' '+end);
                if ( (end-start+1)<=0 ) {
                    //console.log('end-start+1<=0 @ jQuery.'+PLUGINNAME+'.highlight');
                    methods._hidehelper.apply(this);
                    return;
                }
                var srctext = $this.val();
                var p = [];
                p[0] = srctext.substring(0, start);
                p[1] = srctext.substring(start, end+1);
                p[2] = srctext.substring(end+1, srctext.length);
                data._helperel.html(p[0]+'<span style="background: '+color+'">'+p[1]+'</span>'+p[2]);
                methods._showhelper.apply(this);
                data._textareael.scroll();
                $this.blur();
            },
            highlight2areas: function (starto, endo, coloro, starti, endi, colori) {
                console.log('333');
                if ( (endo-starto+1)<=0 || (endi-starti+1)<=0 ) {
                    //console.log('end-start+1<=0 @ jQuery.'+PLUGINNAME+'.highlight');
                    methods._hidehelper.apply(this);
                    return;
                }
                var srctext = $this.val();
                var p = [];
                p[0] = srctext.substring(0, starto);
                p[1] = srctext.substring(starto, starti);
                p[2] = srctext.substring(starti, endi+1);
                p[3] = srctext.substring(endi+1, endo+1);
                p[4] = srctext.substring(endo+1, srctext.length);
                data._helperel.html(p[0]+'<span style="background: '+coloro+'">'+p[1]+'<span style="background: '+colori+'">'+p[2]+'</span>'+p[3]+'</span>'+p[4]);
                methods._showhelper.apply(this);
                data._textareael.scroll();
                $this.blur();
            }
        };

        /*
         * Code starts here! *o*
         */
        if ( methods[method] ) {
            return this.each(function() {
                methods[ method ].apply( $(this), Array.prototype.slice.call( argsclosure, 1 ));
            });
        } else if ( typeof method === 'object' || ! method ) {
            var newjq = [];
            this.each(function() {
                newjq.push(methods.init.apply( $(this), arguments ));
            });
            return $(newjq);
        } else {
            $.error( 'No method with name ' +  method + ' for jQuery.textareaHighlighter' );
        }
    };

})( jQuery );


/*$('#test1').textareaHighlighter( {fontsize: 14} );
$('#test3').click(function(e) {
    var sel = get_selection('#test1');
    var color = 'orange';
    if (sel.indlast !== -2) {
        $('#test1').textareaHighlighter('highlight', sel.indfirst, sel.indlast, color);
        console.log(sel.indfirst +' '+sel.indlast+' @ $("#test3").click');
    } else {
        console.log('-2 @ get_selection');
    }
});

$('#test11').textareaHighlighter( {fontsize: 14} );
$('#test33').click(function(e) {
    var sel = get_selection('#test11');
    var color = 'orange';
    if (sel.indlast !== -2) {
        $('#test11').textareaHighlighter('highlight', sel.indfirst, sel.indlast, color);
        console.log(sel.indfirst +' '+sel.indlast+' @ $("#test3").click');
    } else {
        console.log('-2 @ get_selection');
    }
});*/