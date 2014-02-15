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

    /**
     * Sets up options of M.poasquestion_text_and_button object
     * This method defines onfirstpresscallback method, that calls on very first
     * press on button, right afted dialog generation
     * oneachpresscallback calls on second and following pressings on button
     */
    setup_parent_object : function () {
        var options = {

            onfirstpresscallback : function () {
                var content_url = self.www_root + '/question/type/preg/authoring_tools/preg_authoring.php';
                var scripts = [
                        self.www_root+'/question/type/poasquestion/jquerypanzoommin.js',
                        self.www_root+'/question/type/poasquestion/jquery-textrange.js',
                        self.www_root+'/question/type/poasquestion/interface.js',
                        self.www_root+'/question/type/poasquestion/jquery.mousewheel.js'
                        ];
                self.textbutton_widget.loadDialogContent(content_url, scripts, function () {

                    // Remove the "skip to main content" link.
                    $(self.textbutton_widget.dialog).find('.skiplinks').remove();

                    // Create a clone of the textarea.
                    var textarea = $('<textarea style="margin:0;padding:0;border:none;resize:both;outline:none;overflow:hidden;width:100%;height:100%"></textarea>');
                    $('#id_regex_text').each(function () {
                        $.each(this.attributes, function () {
                            if (this.specified) {
                                textarea.attr(this.name, this.value);
                            }
                        });
                    });

                    // Replace the textarea with an iframe.
                    var iframeMarkup = '<div id="id_regex_resizable" style="border:1px solid black;padding:0;overflow:hidden;width:50%;height:20px">' +
                                         '<iframe id="id_regex_text_replacement" style="border:none;resize:none;width:100%;height:100%"></iframe>' +
                                       '</div>';

                    $('#id_regex_text').replaceWith(iframeMarkup);
                    $('#id_regex_resizable').resizable();

                    // Deal with iframe.
                    var iframe = $('#id_regex_text_replacement');

                    setTimeout(function () {
                        var innerDoc = iframe[0].contentWindow.document,
                            innerBody = $('body', innerDoc);
                        innerBody.css('margin', '0').css('padding', '0')
                                 .append(textarea);
                    }, 1);

                    // Add handlers for the buttons.
                    $('#id_regex_show').click(self.btn_show_clicked);
                    if (!self.textbutton_widget.is_stand_alone()) {
                        $('#id_regex_save').click(self.btn_save_clicked);
                    } else {
                        $('#id_regex_save').hide();
                    }
                    $('#id_regex_cancel').click(self.btn_cancel_clicked);
                    $('#id_regex_check_strings').click(self.btn_check_strings_clicked);

                    $("#id_selection_mode").change(self.btn_selection_mode_rectangle_selection_click);
                    $('#id_send_select').click(self.btn_select_rectangle_selection_click);

                    // Add handlers for the radiobuttons.
                    $('#fgroup_id_tree_orientation_radioset input').change(self.rbtn_changed);
                    $('#fgroup_id_charset_process_radioset input').change(self.rbtn_changed);

                    // Add handlers for the regex textarea.
                    self.regex_input = textarea;
                    self.regex_input.keyup(self.textbutton_widget.fix_textarea_rows);

                    // Add handlers for the regex testing textarea.
                    $('#id_regex_match_text').keyup(self.textbutton_widget.fix_textarea_rows);

                    // Hide the non-working "displayas".
                    $('#fgroup_id_charset_process_radioset').hide();

                    $('#id_send_select').attr('disabled',true);
                    
                    self.panzooms.init();
                    options.oneachpresscallback();
                });
            },

            oneachpresscallback : function () {
                self.regex_input.val(self.textbutton_widget.data).trigger('keyup');
                self.invalidate_content();

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
        var sel = self.get_selection();
        self.load_content(sel.indfirst, sel.indlast);
        self.load_strings(sel.indfirst, sel.indlast);
        self.panzooms.reset_all();
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
        var sel = self.get_selection();
        self.load_content(sel.indfirst, sel.indlast);
        self.panzooms.reset_tree();
    },

    tree_node_clicked : function (e) {
        e.preventDefault();
        if (!self.is_tree_selection_rectangle_visible()) {
            var tmp = e.target.id.split(','),
                indfirst = tmp[1],
                indlast = tmp[2];
            self.load_content(indfirst, indlast);
            self.load_strings(indfirst, indlast);
        }
    },

    tree_node_misclicked : function (e) {
        e.preventDefault();
        if (!self.is_tree_selection_rectangle_visible()) {
            self.load_content();
            self.load_strings();
        }
    },

    is_tree_selection_rectangle_visible : function () {
        return $("#id_selection_mode").is(':checked');
    },
    
    cache_key_for_explaining_tools : function (indfirst, indlast) {
        return '' +
               self.regex_input.val() +
               $('#id_notation_auth').val() +
               $('#id_exactmatch_auth').val() +
               $('#id_usecase_auth').val() +
               self.get_orientation() +
               self.get_displayas() +
               indfirst + ',' + indlast;
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
        if (typeof data == "object") {
            new M.core.ajaxException(data);
            return;
        }
        var json = JSON.parse(data),
            regex = json['regex'],
            //engine = json['engine'],
            notation = json['notation'],
            exactmatch = json['exactmatch'],
            usecase = json['usecase'],
            treeorientation = json['treeorientation'],
            displayas = json['displayas'],
            indfirst = json['indfirst'],
            indlast = json['indlast'],
            t = json[self.TREE_KEY],
            g = json[self.GRAPH_KEY],
            d = json[self.DESCRIPTION_KEY],
            k = '' + regex + notation + exactmatch + usecase + treeorientation + displayas + indfirst + ',' + indlast;

        // Cache the content.
        self.cache[self.TREE_KEY][k] = t;
        self.cache[self.GRAPH_KEY][k] = g;
        self.cache[self.DESCRIPTION_KEY][k] = d;

        // Display the content.
        self.display_content(t, g, d, indfirst, indlast);
    },

    upd_strings_success : function (data, textStatus, jqXHR) {
        if (typeof data == "object") {
            new M.core.ajaxException(data);
            return;
        }
        var json = JSON.parse(data),
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
        var tree_err = $('#tree_err'),
            tree_img = $('#tree_img'),
            tree_map = $('#tree_map'),
            graph_err = $('#graph_err'),
            graph_img = $('#graph_img'),
            desc_hnd = $('#description_handler');

        tree_err.html('');
        tree_img.removeAttr('src').css('visibility', 'hidden');
        tree_map.html('');

        graph_err.html('');
        graph_img.removeAttr('src').css('visibility', 'hidden');

        desc_hnd.html('');
    },

    // Displays given images and description
    display_content : function (t, g, d, indfirst, indlast) {
        var scroll = $(window).scrollTop(),
            tree_err = $('#tree_err'),
            tree_img = $('#tree_img'),
            tree_map = $('#tree_map'),
            graph_err = $('#graph_err'),
            graph_img = $('#graph_img'),
            graph_map = $('#graph_map'),
            desc_hnd = $('#description_handler');

        self.invalidate_content();


        if (typeof t != 'undefined' && t.img && t.map) {
            tree_img.attr('src', t.img).css('visibility', 'visible');
            tree_map.html(t.map);

            $('#tree_img').mousedown(function(e) {
                e.preventDefault();
                //check is checked check box
                if (self.is_tree_selection_rectangle_visible()) {

                    self.CALC_COORD = true;
                    var br = document.getElementById('tree_img').getBoundingClientRect();
                    $('#resizeMe').Resizable(
                        {
                            minWidth: 20,
                            minHeight: 20,
                            maxWidth: (br.right - br.left),
                            maxHeight: (br.bottom - br.top),
                            minTop: $('#tree_hnd').prop('offsetTop'),
                            minLeft: 220,
                            maxRight: br.right - br.left + 220,
                            maxBottom: br.bottom - br.top + $('#tree_hnd').prop('offsetTop'),
                            dragHandle: true,
                            onDrag: function(x, y)
                            {
                                this.style.backgroundPosition = '-' + (x - 50) + 'px -' + (y - 50) + 'px';
                            },
                            handlers: {
                                se: '#resizeSE',
                                e: '#resizeE',
                                ne: '#resizeNE',
                                n: '#resizeN',
                                nw: '#resizeNW',
                                w: '#resizeW',
                                sw: '#resizeSW',
                                s: '#resizeS'
                            },
                            onResize : function(size, position) {
                                this.style.backgroundPosition = '-' + (position.left - 50) + 'px -' + (position.top - 50) + 'px';
                            }
                        }
                    );

                    //self.RECTANGLE_WIDTH = e.pageX - $(window).prop('scrollX') - br.left;
                    //self.RECTANGLE_HEIGHT = e.pageY - $(window).prop('scrollY') - br.top;

                    self.RECTANGLE_WIDTH = self.get_current_x(e);
                    self.RECTANGLE_HEIGHT = self.get_current_y(e);

                    $('#resizeMe').css({
                        width : 20,
                        height : 20,
                        left : self.RECTANGLE_WIDTH,
                        top : self.RECTANGLE_HEIGHT,
                    });
                }
            });

            $('#tree_img').mousemove(function(e) {
                e.preventDefault();
                if (self.CALC_COORD) {
                    var br = document.getElementById('tree_img').getBoundingClientRect();
                    var new_pageX = self.get_current_x(e);//e.pageX - $(window).prop('scrollX') - br.left;
                    var new_pageY = self.get_current_y(e);//e.pageY - $(window).prop('scrollY') - br.top;

                    if(self.RECTANGLE_WIDTH < new_pageX && self.RECTANGLE_HEIGHT < new_pageY) {
                        $('#resizeMe').css({
                            width : (new_pageX - self.RECTANGLE_WIDTH)-10,
                            height : (new_pageY - self.RECTANGLE_HEIGHT)-10,
                        });
                    } else if(self.RECTANGLE_WIDTH < new_pageX && self.RECTANGLE_HEIGHT > new_pageY) {
                        $('#resizeMe').css({
                            width : (new_pageX - self.RECTANGLE_WIDTH)-10,
                            height : (self.RECTANGLE_HEIGHT - new_pageY)-10,
                            top : new_pageY,
                        });
                    } else if(self.RECTANGLE_WIDTH > new_pageX && self.RECTANGLE_HEIGHT > new_pageY) {
                        $('#resizeMe').css({
                            width : (self.RECTANGLE_WIDTH - new_pageX)-10,
                            height : (self.RECTANGLE_HEIGHT - new_pageY)-10,
                            top : new_pageY,
                            left : new_pageX,
                        });
                    } else if(self.RECTANGLE_WIDTH > new_pageX && self.RECTANGLE_HEIGHT < new_pageY) {
                        $('#resizeMe').css({
                            width : (self.RECTANGLE_WIDTH - new_pageX)-10,
                            height : (new_pageY - self.RECTANGLE_HEIGHT)-10,
                            left : new_pageX,
                        });
                    }
                }
            });

            end_rectangle_selection : $(window).mouseup(function(e){
                e.preventDefault();
                self.CALC_COORD = false;
            });

            tree_img.click(self.tree_node_misclicked);
            $(self.TREE_MAP_ID + ' > area').click(self.tree_node_clicked);
        } else if (typeof t != 'undefined') {
            tree_err.html(t);
        }

        if (typeof g != 'undefined' && g.img && g.map) {
            graph_img.attr('src', g.img).css('visibility', 'visible');
            graph_map.html(g.map);
            graph_map.click(self.tree_node_misclicked);
            $(self.GRAPH_MAP_ID + ' > area').click(self.tree_node_clicked);
        } else if (typeof g != 'undefined') {
            graph_err.html(g);
        }

        if (typeof d != 'undefined') {
            desc_hnd.html(d);
        }

        var length =  indlast - indfirst + 1;
        if (indfirst < 0) {
            indfirst = 0;
        }
        if (indlast < 0) {
            length = 0;
        }
        $(self.regex_input).textrange('set', indfirst, length);
        $(window).scrollTop(scroll);
    },

    display_strings : function (s) {
        $('#id_test_regex').html(s);
    },

    btn_select_rectangle_selection_click : function (e) {
        e.preventDefault();

        var sel = self.get_rect_selection();
        self.load_content(sel.indfirst, sel.indlast);
        self.load_strings(sel.indfirst, sel.indlast);

        $('#resizeMe').css({
            width : 0,
            height : 0,
            left : -10,
            top : -10,
        });
    },

    btn_selection_mode_rectangle_selection_click : function (e) {
        e.preventDefault();
        if (self.is_tree_selection_rectangle_visible()) {
            $('#id_send_select').attr('disabled',false);
            $('#tree_img').attr("usemap", "");
            self.panzooms.reset_tree();
            self.panzooms.disable_tree();
        } else {
            $('#id_send_select').attr('disabled',true);
            $('#tree_img').attr("usemap", "#qtype_preg_tree");
            self.panzooms.enable_tree();
            $('#resizeMe').css({
                width : 0,
                height : 0,
                left : -10,
                top : -10,
            });
        }
    },

    get_current_x : function(e) {
        var br = document.getElementById('tree_img').getBoundingClientRect();
        var local_x = e.pageX - $(window).prop('scrollX') - br.left;
        return local_x + 220;
    },

    get_current_y : function(e) {
        var br = document.getElementById('tree_img').getBoundingClientRect();
        var local_y = e.pageY - $(window).prop('scrollY') - br.top;
        return local_y + $('#tree_hnd').prop('offsetTop');
    },

    get_rect_selection : function (e) {
        // check ids selected nodes
        var br = document.getElementById('tree_img').getBoundingClientRect();
        rect_left_bot_x = $('#resizeMe').prop('offsetLeft') - 210;
        rect_left_bot_y = $('#resizeMe').prop('offsetTop') + $('#resizeMe').prop('offsetHeight') + 17 - $('#tree_hnd').prop('offsetTop');
        rect_right_top_x = $('#resizeMe').prop('offsetLeft') + $('#resizeMe').prop('offsetWidth') - 210;
        rect_right_top_y = $('#resizeMe').prop('offsetTop') + 17 - $('#tree_hnd').prop('offsetTop');
        var areas = $('#qtype_preg_tree').children();
        var indfirst = 999;
        var indlast = -999;
        // check all areas and select indfirst and indlast
        var i = 0;
        while (areas[i]) {
            var nodeId = areas[i].id.split(',');
            var nodeCoords = areas[i].coords.split(/[, ]/);
            if (areas[i].shape == "rect") {
                nodeCoords = [
                    nodeCoords[0], nodeCoords[1],
                    nodeCoords[2], nodeCoords[1],
                    nodeCoords[0], nodeCoords[3],
                    nodeCoords[2], nodeCoords[3]
                ];
            }
            var coords = [];
            for (var j = 0; j < nodeCoords.length; j += 2) {
                coords[coords.length] = [nodeCoords[j], nodeCoords[j + 1]];
            }
            // check selected coords
            for (var j = 0; j < coords.length; ++j) {
                if (rect_left_bot_x < coords[j][0]
                    && rect_right_top_x > coords[j][0]
                    && rect_left_bot_y > coords[j][1]
                    && rect_right_top_y < coords[j][1]) {
                        if(nodeId[1] < indfirst) {
                            indfirst = nodeId[1];
                        }
                        if(nodeId[2] > indlast) {
                            indlast = nodeId[2];
                        }
                }
            }
            ++i;
        }

        if (indfirst > indlast) {
            indfirst = indlast = -2;
        }
        return {
            indfirst : indfirst,
            indlast : indlast
        };
    },

    /** Checks for cached data and if it doesn't exist, sends a request to the server */
    load_content : function (indfirst, indlast) {
        if (typeof indfirst == "undefined" || typeof indlast == "undefined") {
            indfirst = indlast = -2;
        }

        // Unbind tree handlers so nothing is clickable till the response is received.
        $('#tree_img').unbind('click', self.tree_node_misclicked);
        $(self.TREE_MAP_ID + ' > area').unbind('click', self.tree_node_clicked);

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
            selection = $(self.regex_input).textrange('get'),
            indfirst = selection.start,
            indlast = selection.end - 1;
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

    panzooms : {
        reset_tree : function() {
            var tree_img = $('#tree_img');
            tree_img.panzoom("reset");
        },

        disable_tree : function() {
            var tree_img = $('#tree_img');
            tree_img.panzoom("disable");
        },
        
        enable_tree : function() {
            var tree_img = $('#tree_img');
            tree_img.panzoom("enable");
        },
        
        reset_graph : function() {
            var graph_img = $('#graph_img');
            graph_img.panzoom("reset");
        },

        reset_all : function() {
            self.panzooms.reset_tree();
            self.panzooms.reset_graph();
            self.panzooms.reset_tree_dimensions();
            self.panzooms.reset_graph_dimensions();
        },

        reset_tree_dimensions : function() {
            var tree_img = $('#tree_img');
            tree_img.panzoom("resetDimensions");
        },

        reset_graph_dimensions : function() {
            var graph_img = $('#graph_img');
            graph_img.panzoom("resetDimensions");
        },

        init_tree : function() {
            var tree_img = $('#tree_img');
            var tree_panzoom_obj = $(tree_img).panzoom();
            $(tree_img).on('mousewheel.focal', this._zoom);
        },

        init_graph : function() {
            var graph_img = $('#graph_img');
            var graph_panzoom_obj = $(graph_img).panzoom();
            $(graph_img).on('mousewheel.focal', this._zoom);
        },

        init : function() {
            self.panzooms.init_graph();
            self.panzooms.init_tree();
        },

        _zoom : function( e ) {
            e.preventDefault();
            var delta = e.delta || e.originalEvent.wheelDelta;
            var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
            $(e.target).panzoom().panzoom('zoom', zoomOut, { // TODO - panzoom() may reset options but jquery.mousewheel.js doesnt support passing data throught event O_O
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
