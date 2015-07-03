/**
 * Script for button "Check", "Back" and push in interactive tree
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Pahomov Dmitry, Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

/**
 * This object extends M.poasquestion_text_and_button with onfirstpresscallback()
 * function and oneachpresscallback()
 */
M.preg_authoring_tools_script = (function ($) {

    var self = {

    TREE_KEY : 'tree',

    TREE_MAP_KEY : 'map',

    GRAPH_KEY : 'graph',

    DESCRIPTION_KEY : 'description',

    STRINGS_KEY : 'regex_test',

    TREE_MAP_ID : '#qtype_preg_tree',

    GRAPH_MAP_ID : '#qtype_preg_graph',

    /** @var string with moodle root url (smth like 'http://moodle.site.ru/') */
    www_root : null,

    /** @var {string} name of qtype_preg_textbutton parent object */
    textbutton_widget : null,

    /** @var {Object} reference to the regex textarea */
    regex_input : null,

    matching_options : ['engine', 'notation', 'exactmatch', 'usecase'],

    prevdata : null,

    data : null,

    /** @var {Object} cache of content; dimensions are: 1) tool name, 2) concatenated options, selection borders, etc. */
    cache : {
        tree : {},
        graph : {},
        description : {},
        regex_test : {}
    },

    /**
     * setups module
     * @param {Object} Y NOT USED! It's needed because moodle passes this object anyway
     * @param {string} _www_root string with www host of moodle
     * (smth like 'http://moodle.site.ru/')
     * @param {string} poasquestion_text_and_button_objname name of qtype_preg_textbutton parent object
     */
    init : function (Y, _www_root, poasquestion_text_and_button_objname) {
        this.www_root = _www_root;
        this.textbutton_widget = M.poasquestion_text_and_button;
        this.setup_parent_object();
    },

    tree_err : function () { return $('#tree_err'); },
    tree_img: function () { return $('#tree_img'); },
    graph_err: function () { return $('#graph_err'); },
    graph_img: function () { return $('#graph_img'); },
    desc_hnd: function () { return $('#description_handler'); },

    /**
     * Sets up options of M.poasquestion_text_and_button object
     * This method defines onfirstpresscallback method, that calls on very first
     * press on button, right afted dialog generation
     * oneachpresscallback calls on second and following pressings on button
     */
    setup_parent_object : function () {
        var options = {

            onfirstpresscallback : function () {
                $.ajax({
                    url: self.www_root + '/question/type/preg/authoring_tools/preg_authoring.php',
                    type: "GET",
                    dataType: "text"
                }).done(function (responseText, textStatus, jqXHR) {
                    var tmpM = M;
                    $(self.textbutton_widget.dialog).html($.parseHTML(responseText, document, false));
                    M = $.extend(M, tmpM);



                    // init moodle form js
                    if (M.form && M.form.shortforms) {
                        M.form.shortforms({"formid":"mformauthoring"}); // TODO - find native way to init headers collapce functionatily
                    }

                    // Remove the "skip to main content" link.
                    $(self.textbutton_widget.dialog).find('.skiplinks').remove();

                    // Add handlers for the buttons.
                    $('#id_regex_show').click(self.btn_show_clicked);
                    if (!self.textbutton_widget.is_stand_alone()) {
                        $('#id_regex_save').click(self.btn_save_clicked);
                    } else {
                        $('#id_regex_save').hide();
                    }
                    $('#id_regex_cancel').click(self.btn_cancel_clicked);
                    $('#id_regex_check_strings').click(self.btn_check_strings_clicked);

                    $("#id_graph_selection_mode").change(self.btn_graph_selection_mode_rectangle_selection_click);

                    // Add handlers for the radiobuttons.
                    $('#fgroup_id_tree_orientation_radioset input').change(self.rbtn_changed);
                    $('#fgroup_id_charset_process_radioset input').change(self.rbtn_changed);

                    // Add handlers for the regex textarea.
                    self.regex_input = $('.qtype-preg-highlighted-regex-text');//$('#id_regex_text');
                    self.regex_input.textareaHighlighter({matches: []});
                    //self.regex_input.textareaHighlighter('debugModeOn');
                    self.regex_input.bind('input propertychange', self.regex_text_changed);

                    //remove left margin
                    $(self.textbutton_widget.dialog).find('#region-main').css('margin-left',0);

                    // Add handlers for the regex testing textarea.
                    $('#id_regex_match_text').keyup(self.textbutton_widget.fix_textarea_rows);

                    // Hide the non-working "displayas".
                    $('#fgroup_id_charset_process_radioset').hide();

                    // resize magic (alter for html-voodoo-bug-positioning-development)
                    $( window ).resize(self.resize_handler);
                    self.resize_handler();

                    self.panzooms.init();
                    options.oneachpresscallback();
                });
            },

            oneachpresscallback : function () {
                self.regex_input.val(self.textbutton_widget.data).trigger('keyup');
                self.invalidate_content();

                self.data = self.regex_input.val();

                // Put the testing data into ui.
                if (!self.textbutton_widget.is_stand_alone()) {
                    $('#id_regex_match_text').val($('input[name=\'regextests[' + $(self.textbutton_widget.current_input).attr('id').split("id_answer_")[1] + ']\']').val())
                        .trigger('keyup');

                    $.each(self.matching_options, function (i, option) {
                        var preg_id = '#id_' + option,
                            this_id = preg_id + '_auth';
                        $(this_id).val($(preg_id).val());
                    });
                }
                $('#id_regex_show').click();
            },

            onclosecallback : function () {
                self.save_sections_state();
            },

            onsaveclicked : function () {
                $('#id_regex_save').click();
            },

            oncancelclicked : function () {
                $('#id_regex_cancel').click();
            }
        };

        self.textbutton_widget.setup(options);
    },

    is_changed : function() {
        return self.data !== self.prevdata;
    },

    save_sections_state : function () {
        var sections = ['regex_input',
            'regex_matching_options',
            'regex_tree',
            'regex_graph',
            'regex_description',
            'regex_testing'
        ];
        $.each(sections, function (i, section) {
            var val = $("[name='mform_isexpanded_id_" + section + "_header']").val();
            M.util.set_user_preference('qtype_preg_' + section + '_expanded', val);
        });
    },

    btn_show_clicked : function (e) {
        e.preventDefault();

        self.data = self.regex_input.val();
        // If regex is changed
        if (self.is_changed()) {
            $('input[name=\'tree_fold_node_points\']').val('');
            self.prevdata = self.data;
            self.panzooms.reset_all();
        }

        selection = $(self.regex_input).textrange('get'),
            indfirst = selection.start,
            indlast = selection.end - 1;
        if (indfirst > indlast) {
            indfirst = indlast = -2;
        }
        $('input[name=\'tree_selected_node_points\']').val(indfirst + ',' + indlast);
        self.load_content(indfirst, indlast);
        self.load_strings(indfirst, indlast);

        /*$('input[name=\'tree_selected_node_points\']').val('');
        var sel = self.get_selection();
        self.load_content(sel.indfirst, sel.indlast);
        self.load_strings(sel.indfirst, sel.indlast);*/
    },

    btn_save_clicked : function (e) {
        e.preventDefault();
        self.textbutton_widget.data = self.regex_input.val();
        $.each(self.matching_options, function (i, option) {
            var preg_id = '#id_' + option,
                this_id = preg_id + '_auth';
            $(preg_id).val($(this_id).val());
        });
        self.textbutton_widget.close_and_set_new_data(self.textbutton_widget.data);
        $('input[name=\'regextests[' + $(self.textbutton_widget.current_input).attr('id').split("id_answer_")[1] + ']\']').val($('#id_regex_match_text').val());
        $('#id_test_regex').html('');
        M.form.updateFormState("mform1");
    },

    btn_cancel_clicked : function (e) {
        e.preventDefault();
        self.textbutton_widget.dialog.dialog("close");
        $('#id_test_regex').html('');
    },

    btn_check_strings_clicked : function (e) {
        e.preventDefault();
        var sel = self.get_selection();
        self.load_strings(sel.indfirst, sel.indlast);
    },

    rbtn_changed : function (e) {
        e.preventDefault();
        if (e.currentTarget.id != "id_tree_folding_mode") {
            var sel = self.get_selection();
            self.load_content(sel.indfirst, sel.indlast);
            self.panzooms.reset_tree();
        }
    },

    regex_text_changed : function (e) {
        e.preventDefault();
        self.regex_input.textareaHighlighter('updateMatches', []);
    },

    tree_node_clicked : function (e) {
        e.preventDefault();

        var tmp = $($(e.target).parents(".node")[0]).attr('id').split(/_/), // TODO -omg make beauty
            indfirst = tmp[2],
            indlast = tmp[3];

        if (self.is_tree_foldind_mode()) {
            var points = $('input[name=\'tree_fold_node_points\']').val();
            // if new point not contained
            if (points.indexOf(indfirst + ',' + indlast) == -1) {
                // add new point
                if (points != '') {
                    points += ';';
                }
                points += indfirst + ',' + indlast;
            } else { // if new point already contained
                // remove this point
                if (points.indexOf(';' + indfirst + ',' + indlast) != -1) {
                    points = points.replace(';' + indfirst + ',' + indlast, '');
                } else if (points.indexOf(indfirst + ',' + indlast + ';') != -1) {
                    points = points.replace(indfirst + ',' + indlast + ';', '');
                } else {
                    points = points.replace(indfirst + ',' + indlast, '');
                }
            }
            $('input[name=\'tree_fold_node_points\']').val(points);

            if (typeof $('input[name=\'tree_selected_node_points\']').val() != 'undefined') {
                var tmpcoords = $('input[name=\'tree_selected_node_points\']').val().split(',');
                indfirst = tmpcoords[0];
                indlast = tmpcoords[1];

                self.load_content(indfirst, indlast);
                self.load_strings(indfirst, indlast);
            } else {
                self.load_content();
                self.load_strings();
            }
        } else {
            $('input[name=\'tree_selected_node_points\']').val(indfirst + ',' + indlast);
            self.load_content(indfirst, indlast);
            self.load_strings(indfirst, indlast);
        }
    },

    tree_node_misclicked : function (e) {
        e.preventDefault(); // TODO - joining many times when panning
        if (!self.is_tree_foldind_mode()) {
            $('input[name=\'tree_selected_node_points\']').val('');
            self.load_content();
            self.load_strings();
        }
    },

    graph_node_clicked : function (e) {
        e.preventDefault();
        if (!self.is_graph_selection_rectangle_visible()) {
            var tmp = $($(e.target).parents(".node")[0]).attr('id').split('_'), // TODO -omg make beauty
                indfirst = tmp[2],
                indlast = tmp[3];

            $('input[name=\'tree_selected_node_points\']').val(indfirst + ',' + indlast);

            self.load_content(indfirst, indlast);
            self.load_strings(indfirst, indlast);
        }
    },

    graph_node_misclicked : function (e) {
        e.preventDefault();
        if (!self.is_graph_selection_rectangle_visible()) {

            $('input[name=\'tree_selected_node_points\']').val('');

            self.load_content();
            self.load_strings();
        }
    },

    is_tree_foldind_mode : function () {
        return $("#id_tree_folding_mode").is(':checked');
    },

    is_graph_selection_rectangle_visible : function () {
        return $("#id_graph_selection_mode").is(':checked');
    },

    cache_key_for_explaining_tools : function (indfirst, indlast) {
        return '' /*+
         self.regex_input.val() +
         $('#id_notation_auth').val() +
         $('#id_exactmatch_auth').val() +
         $('#id_usecase_auth').val() +
         self.get_orientation() +
         self.get_displayas() +
         indfirst + ',' + indlast*/;
    },

    cache_key_for_testing_tool : function (indfirst, indlast) {
        return '' +
            self.regex_input.val() +
            $('#id_engine_auth').val() +
            $('#id_notation_auth').val() +
            $('#id_exactmatch_auth').val() +
            $('#id_usecase_auth').val() +
            $('#id_regex_match_text').val() +
            indfirst + ',' + indlast;
    },

    upd_content_success : function (data, textStatus, jqXHR) {
        var json = (typeof data == "object") ? data : JSON.parse(data),
            regex = json['regex'],
            //engine = json['engine'],
            notation = json['notation'],
            exactmatch = json['exactmatch'],
            usecase = json['usecase'],
            treeorientation = json['treeorientation'],
            displayas = json['displayas'],
            indfirst = parseInt(json['indfirst']),
            indlast = parseInt(json['indlast']),
            indfirstorig = parseInt(json['indfirstorig']),
            indlastorig = parseInt(json['indlastorig']),
            t = json[self.TREE_KEY],
            g = json[self.GRAPH_KEY],
            d = json[self.DESCRIPTION_KEY],
            k = '' + regex + notation + exactmatch + usecase + treeorientation + displayas + indfirst + ',' + indlast;

        // Cache the content.
        self.cache[self.TREE_KEY][k] = t;
        self.cache[self.GRAPH_KEY][k] = g;
        self.cache[self.DESCRIPTION_KEY][k] = d;

        // Display the content.
        self.display_content(t, g, d, indfirst, indlast, indfirstorig, indlastorig);
    },

    upd_strings_success : function (data, textStatus, jqXHR) {
        var json = (typeof data == "object") ? data : JSON.parse(data),
            regex = json['regex'],
            engine = json['engine'],
            notation = json['notation'],
            exactmatch = json['exactmatch'],
            usecase = json['usecase'],
            treeorientation = json['treeorientation'],
            displayas = json['displayas'],
            indfirst = json['indfirst'],
            indlast = json['indlast'],
            strings = json['strings'],
            s = json[self.STRINGS_KEY],
            k = '' + regex + engine + notation + exactmatch + usecase + strings + indfirst + ',' + indlast;

        // Cache the strings.
        self.cache[self.STRINGS_KEY][k] = s;

        // Display the strings.
        self.display_strings(s);
    },

    invalidate_content : function () {

        self.tree_err().html('');
        self.tree_img().css('visibility', 'hidden');

        self.graph_err().html('');
        self.graph_img().css('visibility', 'hidden');

        self.desc_hnd().html('');
    },

    // Displays given images and description
    display_content : function (t, g, d, indfirst, indlast, indfirstorig, indlastorig) {
        var scroll = $(window).scrollTop();

        self.invalidate_content();

        if (typeof t != 'undefined' && t.img) {
            self.tree_img().css('visibility', 'visible').html(t.img);

            self.tree_img().click(self.tree_node_misclicked);
            $("svg .node", self.tree_img()).click(self.tree_node_clicked);

            var tmpH = $("#tree_img svg").attr('height');
            var tmpW = $("#tree_img svg").attr('width');

            $("#tree_img svg").attr('height', tmpH.replace('pt', 'px'));
            $("#tree_img svg").attr('width', tmpW.replace('pt', 'px'));

            $("#tree_img")[0].title = '';
            // Clear all tooltip for arrows
            var nodes = $("#tree_img svg")[0].children[0].children;
            for(var i = 0; i < nodes.length; ++i) {
                if (nodes[i].id.indexOf('edge') > -1) {
                    nodes[i].children[0].innerHTML = '';
                } else if (i == 0) {
                    nodes[i].innerHTML = '';
                } else if (nodes[i].id.indexOf('graph2') > -1) {
                    nodes[i].children[0].innerHTML = '';
                }
            }
        } else if (typeof t != 'undefined') {
            self.tree_err().html(t);
        }

        if (typeof g != 'undefined' && g.img) {
            self.graph_img().css('visibility', 'visible').html(g.img)

            self.graph_img().click(self.graph_node_misclicked);
            $("svg .node", self.graph_img()).click(self.graph_node_clicked);

            var tmpH = $("#graph_img svg").attr('height');
            var tmpW = $("#graph_img svg").attr('width');

            $("#graph_img svg").attr('height', tmpH.replace('pt', 'px'));
            $("#graph_img svg").attr('width', tmpW.replace('pt', 'px'));

            $('#graph_img').mousedown(function(e) {
                e.preventDefault();
                //check is checked check box
                if (self.is_graph_selection_rectangle_visible()) {
                    self.init_rectangle_selection(e, 'graph_img','resizeGraph', 'graph_hnd');
                }
            });

            $('#graph_img').mousemove(function(e) {
                e.preventDefault();
                self.resize_rectangle_selection(e, 'graph_img','resizeGraph', 'graph_hnd');
            });

            $(window).mouseup(function(e){
                e.preventDefault();
                if (self.CALC_COORD == true) {
                    self.CALC_COORD = false;

                    var transformattr = $('#explaining_graph').attr('transform');
                    var ta = /.*translate\(\s*(\d+)\s+(\d+).*/g.exec(transformattr);
                    var translate_x = ta[1];
                    var translate_y = ta[2];
                    var sel = self.get_rect_selection(e, 'resizeGraph', 'graph_img',
                        (document.getElementById('graph_hnd').getBoundingClientRect().left - document.getElementById('graph_img').getBoundingClientRect().left
                            + parseInt(translate_x) - $('#graph_hnd').prop('scrollLeft')),
                        (document.getElementById('graph_hnd').getBoundingClientRect().top - document.getElementById('graph_img').getBoundingClientRect().top
                            + parseInt(translate_y) + $('#graph_hnd').prop('scrollTop')));

                    $('input[name=\'tree_selected_node_points\']').val(sel.indfirst + ',' + sel.indlast);

                    self.load_content(sel.indfirst, sel.indlast);
                    self.load_strings(sel.indfirst, sel.indlast);

                    $('#resizeGraph').css({
                        width : 0,
                        height : 0,
                        left : -10,
                        top : -10
                    });
                }
            });
        } else if (typeof g != 'undefined') {
            self.graph_err().html(g);
        }

        if (typeof d != 'undefined') {
            self.desc_hnd().html(d);
        }

        var length =  indlast - indfirst + 1;
        if (indfirst < 0) {
            indfirst = 0;
        }
        if (indlast < 0) {
            length = 0;
        }
        /*if ((indfirstorig !== indfirst || indlastorig !== indlast) && indfirst <= indfirstorig && indlast >= indlastorig) {
            self.regex_input.textareaHighlighter('updateMatches',
              [
                {'type': 'qtype-preg-yellow', start: indfirst, end: indlast},
                {'type': 'qtype-preg-orange', start: indfirstorig, end: indlastorig}
              ]
            );
        } else {*/
            self.regex_input.textrange('set', 0, 0);
            self.regex_input.textareaHighlighter('updateMatches',
              [
                {'type': 'qtype-preg-orange', start: indfirst, end: indlast}
              ]
            );
        //}
    },


    resize_rectangle_selection : function(e, img, rectangle, hnd) {
        if (self.CALC_COORD) {
            var br = document.getElementById(img).getBoundingClientRect();
            var new_pageX = self.get_current_x(e, img, hnd);
            var new_pageY = self.get_current_y(e, img, hnd);

            if (self.RECTANGLE_WIDTH < new_pageX && self.RECTANGLE_HEIGHT < new_pageY) {
                $('#' + rectangle).css({
                    width : (new_pageX - self.RECTANGLE_WIDTH)-10,
                    height : (new_pageY - self.RECTANGLE_HEIGHT)-10
                });
            } else if (self.RECTANGLE_WIDTH < new_pageX && self.RECTANGLE_HEIGHT > new_pageY) {
                $('#' + rectangle).css({
                    width : (new_pageX - self.RECTANGLE_WIDTH)-10,
                    height : (self.RECTANGLE_HEIGHT - new_pageY)-10,
                    top : new_pageY
                });
            } else if (self.RECTANGLE_WIDTH > new_pageX && self.RECTANGLE_HEIGHT > new_pageY) {
                $('#' + rectangle).css({
                    width : (self.RECTANGLE_WIDTH - new_pageX)-10,
                    height : (self.RECTANGLE_HEIGHT - new_pageY)-10,
                    top : new_pageY,
                    left : new_pageX
                });
            } else if (self.RECTANGLE_WIDTH > new_pageX && self.RECTANGLE_HEIGHT < new_pageY) {
                $('#' + rectangle).css({
                    width : (self.RECTANGLE_WIDTH - new_pageX)-10,
                    height : (new_pageY - self.RECTANGLE_HEIGHT)-10,
                    left : new_pageX
                });
            }

            // draw selected items in image
            var transformattr = $('#explaining_graph').attr('transform');
            var ta = /.*translate\(\s*(\d+)\s+(\d+).*/g.exec(transformattr);
            var translate_x = ta[1];
            var translate_y = ta[2];
            var tdx = (document.getElementById('graph_hnd').getBoundingClientRect().left - document.getElementById('graph_img').getBoundingClientRect().left
                + parseInt(translate_x) - $('#graph_hnd').prop('scrollLeft'));
            var tdy = (document.getElementById('graph_hnd').getBoundingClientRect().top - document.getElementById('graph_img').getBoundingClientRect().top
                + parseInt(translate_y) + $('#graph_hnd').prop('scrollTop'));
            var items = self.get_figures_in_rect('resizeGraph', 'graph_img', tdx, tdy);

            var areas = $("ellipse, polygon", "#" + img + " > svg > g");
            // check all sgv elements and set opasity 100%
            for (var i = 0; i < areas.length; ++i) {
                $(areas[i]).attr('opacity' , '1.0');
            }

            // check selected svg elements and set opasity 50%
            for (var i = 0; i < items.length; ++i) {
                $(items[i]).attr('opacity' , '0.5');
            }

        }
    },

    init_rectangle_selection : function(e, img, rectangle, hnd) {
        self.CALC_COORD = true;
        //var br = $("#"+img+" > svg > g")[0].getBoundingClientRect(); // TODO - use pure jquery analog
        $('#' + rectangle).Resizable({
                minWidth: 20,
                minHeight: 20,
                /*maxWidth: (br.right - br.left),
                 maxHeight: (br.bottom - br.top),
                 minTop: 1,
                 minLeft: 1,
                 maxRight: br.right - br.left,
                 maxBottom: br.bottom - br.top,*/
                maxWidth: 9999,
                maxHeight: 9999,
                minTop: 1,
                minLeft: 1,
                maxRight: 9999,
                maxBottom: 9999,
                dragHandle: true,
                onDrag: function(x, y) {
                    this.style.backgroundPosition = '-' + (x - 50) + 'px -' + (y - 50) + 'px';
                },
                /*handlers: {
                 se: '#resizeSE',
                 e: '#resizeE',
                 ne: '#resizeNE',
                 n: '#resizeN',
                 nw: '#resizeNW',
                 w: '#resizeW',
                 sw: '#resizeSW',
                 s: '#resizeS'
                 },*/
                onResize : function(size, position) {
                    this.style.backgroundPosition = '-' + (position.left - 50) + 'px -' + (position.top - 50) + 'px';
                }
            }
        );

        self.RECTANGLE_WIDTH = self.get_current_x(e, img, hnd);
        self.RECTANGLE_HEIGHT = self.get_current_y(e, img, hnd);

        $('#' + rectangle).css({
            width : 20,
            height : 20,
            left : self.RECTANGLE_WIDTH,
            top : self.RECTANGLE_HEIGHT,
            visibility : 'visible'
        });
    },

    get_current_x : function(e, img, hnd) {
        var x = (window.pageXOffset !== undefined) ? window.pageXOffset : (document.documentElement || document.body.parentNode || document.body).scrollLeft;
        return e.pageX - x - document.getElementById(img).getBoundingClientRect().left
            - (document.getElementById(hnd).getBoundingClientRect().left - document.getElementById(img).getBoundingClientRect().left)
            + $('#' + hnd).prop('scrollLeft');
    },

    get_current_y : function(e, img, hnd) {
        var y = (window.pageYOffset !== undefined) ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
        return e.pageY - y - document.getElementById(img).getBoundingClientRect().top
            - (document.getElementById(hnd).getBoundingClientRect().top - document.getElementById(img).getBoundingClientRect().top)
            + $('#' + hnd).prop('scrollTop');
    },

    /**
     * Detects a rectangle within polygon and adds a center point to it.
     * @param area Area of map tag.
     * @returns {boolean} Is polygon a rectangle?
     */
    detect_rect : function(area) {
        // Get list of coordinates as integers.
        var nodeCoords = area.coords.split(/[, ]/).map(function(item){return parseInt(item);});
        // If it looks like a rectangle...
        if (nodeCoords.length == 8) {
            // Build a list of points for convenience.
            var points = [];
            for (var j = 0; j < nodeCoords.length; j += 2) {
                points[points.length] = {x: nodeCoords[j], y: nodeCoords[j+1]};
            }

            // Calculate a center point of rectangle.
            var center = {
                x: Math.floor(points[0].x + (points[2].x - points[0].x)/2),
                y: Math.floor(points[0].y + (points[2].y - points[0].y)/2)
            };

            // Add a center point to coordinates of area.
            area.coords += ',' + center.x + ',' + center.y;

            return true;
        } else {
            return false;
        }
    },

    /**
     * Finds figures at image inside rectangle.
     * @param rectangle selection area.
     * @param img image to search.
     * @param deltaX coordinate's shift by X axis.
     * @param deltaY coordinate's shift by Y axis.
     * @returns {Array} figures inside rectangle.
     */
    get_figures_in_rect : function (rectangle, img, deltaX, deltaY) {
        rect_left_bot_x = $('#' + rectangle).prop('offsetLeft') + deltaX;
        rect_left_bot_y = $('#' + rectangle).prop('offsetTop') + $('#' + rectangle).prop('offsetHeight') - deltaY;
        rect_right_top_x = $('#' + rectangle).prop('offsetLeft') + $('#'  + rectangle).prop('offsetWidth') + deltaX;
        rect_right_top_y = $('#' + rectangle).prop('offsetTop') - deltaY;

        var areas = $(".edge, .node, .cluster", "#"+img+" > svg > g");
        var figures = [];
        for (var i = 0; i < areas.length; ++i) {
            var nodeId = areas[i].id.split('_');
            if (nodeId.length != 4) continue;

            var figure = null;
            switch (areas[i].getAttribute('class')) {
                case 'node':
                    figure = $("ellipse, polygon", areas[i])[0];
                    if (figure.length == 0) {
                        figure = $("polygon", "#"+areas[i].id+" > g > a")[0];
                    }
                    break;
                case 'edge':
                    figure = $("path", areas[i])[0];
                    var additionalFigure = $("polygon", areas[i])[0];
                    break;
                case 'cluster':
                    figure = $("polygon", "#"+areas[i].id+" > g > a")[0];
                    if (figure === undefined) {
                        figure = $("polygon", "#"+areas[i].id+" > a")[0];
                    }
                    break;
                default:
                    continue;
            }

            if ($(figure).is("ellipse")) {
                var nodeCoords = [
                    { x: figure.cx.baseVal.value, y : figure.cy.baseVal.value }
                ];
            } else if ($(figure).is("polygon")) {
                var nodeCoords = [];
                for (var j = 0; j < figure.points.numberOfItems; ++j) {
                    nodeCoords.push({
                        x : figure.points.getItem(j).x,
                        y : figure.points.getItem(j).y
                    });
                }
            } else if ($(figure).is("path")) {
                var nodeCoords = [];
                var pathInfo = figure.getAttribute('d');
                var delimIndex = pathInfo.indexOf('C');
                var biginCoordsStr = pathInfo.substring(1, delimIndex).split(',');
                var biginCoords = {x: parseFloat(biginCoordsStr[0]), y: parseFloat(biginCoordsStr[1])};
                var pathCoordsStr = pathInfo.substring(delimIndex+1).split(' ');
                for (var j = 0; j < pathCoordsStr.length; ++j) {
                    var pairStr = pathCoordsStr[j].split(',');
                    nodeCoords.push({
                        x : parseFloat(pairStr[0]),
                        y : parseFloat(pairStr[1])
                    });
                }

                nodeCoords = nodeCoords.filter(function (item) {
                    var maxX = additionalFigure.points.getItem(1).x;
                    var pathLength = maxX - biginCoords.x;
                    return item.x < (maxX - pathLength*0.15) && item.x > (biginCoords.x + pathLength*0.15);
                });
            } else {
                continue;
            }
            // check selected coords
            for (var j = 0; j < nodeCoords.length; ++j) {
                if (rect_left_bot_x < nodeCoords[j].x
                    && rect_right_top_x > nodeCoords[j].x
                    && rect_left_bot_y + 2*(document.getElementById("graph_hnd").getBoundingClientRect().top - document.getElementById("graph_img").getBoundingClientRect().top) > nodeCoords[j].y
                    && rect_right_top_y + 2*(document.getElementById("graph_hnd").getBoundingClientRect().top - document.getElementById("graph_img").getBoundingClientRect().top) < nodeCoords[j].y) {

                    figures.push(figure);
                }
            }
        }

        return figures;
    },

    get_rect_selection : function (e, rectangle, img, deltaX, deltaY) {
        // Check ids selected nodes
        rect_left_bot_x = $('#' + rectangle).prop('offsetLeft') + deltaX;
        rect_left_bot_y = $('#' + rectangle).prop('offsetTop') + $('#' + rectangle).prop('offsetHeight') - deltaY;
        rect_right_top_x = $('#' + rectangle).prop('offsetLeft') + $('#'  + rectangle).prop('offsetWidth') + deltaX;
        rect_right_top_y = $('#' + rectangle).prop('offsetTop') - deltaY;
        var areas = $(".edge, .node, .cluster", "#"+img+" > svg > g");
        var indfirst = 999;
        var indlast = -999;
        // check all areas and select indfirst and indlast
        for (var i = 0; i < areas.length; ++i) {
            var nodeId = areas[i].id.split('_');
            if (nodeId.length != 4) continue;

            var figure = null;
            switch (areas[i].getAttribute('class')) {
                case 'node':
                    figure = $("ellipse, polygon", areas[i])[0];
                    if (figure.length == 0) {
                        figure = $("polygon", "#"+areas[i].id+" > g > a")[0];
                    }
                    break;
                case 'edge':
                    figure = $("path", areas[i])[0];
                    var additionalFigure = $("polygon", areas[i])[0];
                    break;
                case 'cluster':
                    figure = $("polygon", "#"+areas[i].id+" > g > a")[0];
                    if (figure === undefined) {
                        figure = $("polygon", "#"+areas[i].id+" > a")[0];
                    }
                    break;
                default:
                    continue;
            }

            if ($(figure).is("ellipse")) {
                var nodeCoords = [
                    { x: figure.cx.baseVal.value, y : figure.cy.baseVal.value }
                ];
            } else if ($(figure).is("polygon")) {
                var nodeCoords = [];
                for (var j = 0; j < figure.points.numberOfItems; ++j) {
                    nodeCoords.push({
                        x : figure.points.getItem(j).x,
                        y : figure.points.getItem(j).y
                    });
                }
            } else if ($(figure).is("path")) {
                var nodeCoords = [];
                var pathInfo = figure.getAttribute('d');
                var delimIndex = pathInfo.indexOf('C');
                var biginCoordsStr = pathInfo.substring(1, delimIndex).split(',');
                var biginCoords = {x: parseFloat(biginCoordsStr[0]), y: parseFloat(biginCoordsStr[1])};
                var pathCoordsStr = pathInfo.substring(delimIndex+1).split(' ');
                for (var j = 0; j < pathCoordsStr.length; ++j) {
                    var pairStr = pathCoordsStr[j].split(',');
                    nodeCoords.push({
                        x : parseFloat(pairStr[0]),
                        y : parseFloat(pairStr[1])
                    });
                }

                nodeCoords = nodeCoords.filter(function (item) {
                    var maxX = additionalFigure.points.getItem(1).x;
                    var pathLength = maxX - biginCoords.x;
                    return item.x < (maxX - pathLength*0.15) && item.x > (biginCoords.x + pathLength*0.15);
                });
            } else {
                continue;
            }
            // check selected coords
            for (var j = 0; j < nodeCoords.length; ++j) {
                if (rect_left_bot_x < nodeCoords[j].x
                    && rect_right_top_x > nodeCoords[j].x
                    && rect_left_bot_y + 2*(document.getElementById("graph_hnd").getBoundingClientRect().top - document.getElementById("graph_img").getBoundingClientRect().top) > nodeCoords[j].y
                    && rect_right_top_y + 2*(document.getElementById("graph_hnd").getBoundingClientRect().top - document.getElementById("graph_img").getBoundingClientRect().top) < nodeCoords[j].y) {
                    if (parseInt(nodeId[2]) < parseInt(indfirst)) {
                        indfirst = nodeId[2];
                    }
                    if (parseInt(nodeId[3]) > parseInt(indlast)) {
                        indlast = nodeId[3];
                    }
                }
            }
        }

        if (parseInt(indfirst) == 999 || parseInt(indlast) == -999) {
            indfirst = indlast = -2;
        }
        return {
            indfirst : indfirst,
            indlast : indlast
        };
    },

    display_strings : function (s) {
        $('#id_test_regex').html(s);
    },

    btn_graph_selection_mode_rectangle_selection_click : function (e) {
        e.preventDefault();
        if (self.is_graph_selection_rectangle_visible()) {
            self.panzooms.disable_graph();
        } else {
            self.panzooms.enable_graph();
            $('#resizeGraph').css({
                width : 0,
                height : 0,
                left : -10,
                top : -10
            });
        }
    },

    /** Checks for cached data and if it doesn't exist, sends a request to the server */
    load_content : function (indfirst, indlast) {
        if (typeof indfirst == "undefined" || typeof indlast == "undefined") {
            indfirst = indlast = -2;
        }

        // Unbind tree handlers so nothing is clickable till the response is received.
        self.tree_img().unbind('click', self.tree_node_misclicked);
        $("svg .node", self.tree_img()).unbind('click', self.tree_node_clicked);
        self.graph_img().unbind('click', self.graph_node_misclicked);
        $("svg .node", self.graph_img()).unbind('click', self.graph_node_clicked); // TODO - idea says that this is bad :c

        // Check the cache.
        var k = self.cache_key_for_explaining_tools(indfirst, indlast);
        var cached = self.cache[self.TREE_KEY][k];
        if (cached) {
            self.display_content(self.cache[self.TREE_KEY][k], self.cache[self.GRAPH_KEY][k], self.cache[self.DESCRIPTION_KEY][k], indfirst, indlast);
            return;
        }

        $.ajax({
            type: 'GET',
            url: self.www_root + '/question/type/preg/authoring_tools/preg_authoring_tools_loader.php',
            data: {
                regex: self.regex_input.val(),
                engine: $('#id_engine_auth :selected').val(),
                notation: $('#id_notation_auth :selected').val(),
                exactmatch: $('#id_exactmatch_auth :selected').val(),
                usecase: $('#id_usecase_auth :selected').val(),
                indfirst: indfirst,
                indlast: indlast,
                treeorientation: self.get_orientation(),
                displayas: self.get_displayas(),
                foldcoords: $('input[name=\'tree_fold_node_points\']').val(),
                treeisfold: $("#id_tree_folding_mode").is(':checked') ? 1 : 0,
                ajax: true
            },
            success: self.upd_content_success
        });
    },

    load_strings : function (indfirst, indlast) {
        if (typeof indfirst == "undefined" || typeof indlast == "undefined") {
            indfirst = indlast = -2;
        }

        // Check the cache.
        var k = self.cache_key_for_testing_tool(indfirst, indlast);
        var cached = self.cache[self.STRINGS_KEY][k];
        if (cached) {
            self.display_strings(cached);
            return;
        }

        $.ajax({
            type: 'GET',
            url: self.www_root + '/question/type/preg/authoring_tools/preg_regex_testing_tool_loader.php',
            data: {
                regex: self.regex_input.val(),
                engine: $('#id_engine_auth :selected').val(),
                notation: $('#id_notation_auth :selected').val(),
                exactmatch: $('#id_exactmatch_auth :selected').val(),
                usecase: $('#id_usecase_auth :selected').val(),
                indfirst: indfirst,
                indlast: indlast,
                strings: $('#id_regex_match_text').val(),
                ajax: true
            },
            success: self.upd_strings_success
        });
    },

    get_selection : function () {
        var scroll = $(window).scrollTop(),
            /*selection = $(self.regex_input).textrange('get'),
            indfirst = selection.start,
            indlast = selection.end - 1;*/
            indfirst = indlast = -2;
        if (typeof $('input[name=\'tree_selected_node_points\']').val() != 'undefined') {
            var tmpcoords = $('input[name=\'tree_selected_node_points\']').val().split(',');
            indfirst = tmpcoords[0];
            indlast = tmpcoords[1];
        }
        if (indfirst > indlast) {
            indfirst = indlast = -2;
        }
        $(window).scrollTop(scroll);
        return {
            indfirst : indfirst,
            indlast : indlast
        };
    },

    get_orientation : function () {
        return $('#fgroup_id_tree_orientation_radioset input:checked').val();
    },

    get_displayas : function () {
        return $('#fgroup_id_charset_process_radioset input:checked').val();
    },

    resize_handler : function() {
        $('#tree_hnd').css('width', $('#mformauthoring').prop('offsetWidth') - 37);
        $('#graph_hnd').css('width', $('#mformauthoring').prop('offsetWidth') - 37);
    },

    panzooms : {
        reset_tree : function() {
            self.tree_img().panzoom("reset");
        },

        reset_graph : function() {
            self.graph_img().panzoom("reset");
        },

        disable_tree : function() {
            self.tree_img().panzoom("disable");
        },

        disable_graph : function() {
            self.graph_img().panzoom("instance")._unbind();
            self.graph_img().off('mousewheel.focal', this._zoom);
        },

        enable_tree : function() {
            self.tree_img().panzoom("enable");
        },

        enable_graph : function() {
            self.graph_img().panzoom("instance")._bind();
            self.graph_img().on('mousewheel.focal', this._zoom);
        },

        reset_all : function() {
            self.panzooms.reset_tree();
            self.panzooms.reset_graph();
            self.panzooms.reset_tree_dimensions();
            self.panzooms.reset_graph_dimensions();
        },

        reset_tree_dimensions : function() {
            self.tree_img().panzoom("resetDimensions");
        },

        reset_graph_dimensions : function() {
            self.graph_img().panzoom("resetDimensions");
        },

        init_tree : function() {
            var tree_panzoom_obj = self.tree_img().panzoom();
            self.tree_img().on('mousewheel.focal', this._zoom);
            self.tree_img().panzoom("option", "pan", false);
        },

        init_graph : function() {
            var graph_panzoom_obj = self.graph_img().panzoom();
            self.graph_img().on('mousewheel.focal', this._zoom);
        },

        init : function() {
            self.panzooms.init_graph();
            self.panzooms.init_tree();
        },

        _zoom : function( e ) {
            e.preventDefault();
            var delta = e.delta || e.originalEvent.wheelDelta;
            var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
            var panzoomholder= $(e.target).parents(".preg_img_panzoom")[0];
            $(panzoomholder).panzoom('zoom', zoomOut, {
                increment: 0.1,
                focal: e
            });
        }
    },

    //RECTANGLE SELECTION CODE
    CALC_COORD : false,
    RECTANGLE_WIDTH: 0,
    RECTANGLE_HEIGHT : 0
};

return self;

})(jQuery);
