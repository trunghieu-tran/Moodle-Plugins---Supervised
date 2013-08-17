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
M.preg_authoring_tools_script = (function($) {

    var self = {

    /** @var string with moodle root url (smth like 'http://moodle.site.ru/') */
    www_root : null,

    /** @var {string} name of qtype_preg_textbutton parent object */
    textbutton_widget : null,

    /** @var {Object} reference for 'input' on top of dialog */
    main_input : null,

    /** @var {Integer} id of the currently selected node */
    node_id : '-1',

    /** @var {Object} cache of content; first dimension is orientation, second id regex, third is node id */
    cache : {
        vertical: {
            userinscription: {},
            flags: {}
        },
        horizontal: {
            userinscription: {},
            flags: {}
        }
    },

    tree_orientation : null,

    displayas : null,

    REGEX_KEY : 'regex',

    ID_KEY : 'id',

    TREE_KEY : 'tree_src',

    TREE_MAP_KEY : 'map',

    TREE_MAP_ID : '#qtype_preg_graph',

    GRAPH_KEY : 'graph_src',

    DESCRIPTION_KEY : 'description',

    /**
     * setups module
     * @param {Object} Y NOT USED! It's needed because moodle passes this object anyway
     * @param {string} _www_root string with www host of moodle
     * (smth like 'http://moodle.site.ru/')
     * @param {string} poasquestion_text_and_button_objname name of qtype_preg_textbutton parent object
     */
    init : function(Y, _www_root, poasquestion_text_and_button_objname) {
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
    setup_parent_object : function() {
        var options = {

            // Function called on the very first form opening.
            onfirstpresscallback : function() {
                $(self.textbutton_widget.dialog).load(self.www_root + '/question/type/preg/authoring_tools/ast_preg_form.php', function() {
                    //TODO: set empty src in all field

                    // Add handlers for the buttons.
                    $('#id_regex_update').click(self.btn_update_clicked);
                    $('#id_regex_save').click(self.btn_save_clicked);
                    $("#id_regex_cancel").click(self.btn_cancel_clicked);
                    $('#id_regex_check_strings').click(self.btn_check_strings_clicked);
                    $('#id_regex_show_selection').click(self.btn_show_selection_clicked);

                    // Add handlers for the radiobuttons.
                    $("#fgroup_id_tree_orientation_radioset input").change(self.rbtn_changed);
                    $("#fgroup_id_charset_process_radioset input").change(self.rbtn_changed);

                    self.main_input = $('#id_regex_text').change(self.regex_change)
                                                         //.change(self.textbutton_widget.fix_textarea_rows)
                                                         .keyup(self.textbutton_widget.fix_textarea_rows);

                    $(self.main_input).val(self.textbutton_widget.data).trigger('keyup');

                    // TODO - FIND A GOOD WAY TO HIDE THE "EXPAND ALL" BUTTON!
                    $(".collapsible-actions").hide();
                    $("#fgroup_id_charset_process_radioset").hide(); // TODO - hidden for beta



                    // get testing data from hidden field and put it into ui
                    $('#id_regex_match_text').val( $('input[name=\'regextests[' + $(self.textbutton_widget.currentlinput).attr('id').split("id_answer_")[1] + ']\']').val())
                                             .keyup(self.textbutton_widget.fix_textarea_rows)
                                             .trigger('keyup');
                    $("#id_regex_input_header").after('<div id="form_properties"></div>');

                    options.display_question_options();
                    self.regex_selection_widget._init();
                    self.load_content('-1');
                });
            },

            // Function called on non-first form openings.
            oneachpresscallback : function() {
                self.main_input.val(self.textbutton_widget.data).trigger('keyup');
                // get testing data from hidden field and put it into ui
                $('#id_regex_match_text').val($('input[name=\'regextests[' + $(self.textbutton_widget.currentlinput).attr('id').split("id_answer_")[1] + ']\']').val());

                options.display_question_options();
                self.load_content('-1');
            },

            onsaveclicked : function() {
                $('#id_regex_save').click();
            },

            oncancelclicked : function() {
                $('#id_regex_cancel').click();
            },

            display_question_options : function() {
                $('#form_properties').html('<div>' +
                                           'engine: '     + $('#id_engine :selected').text() + '<br/>' +
                                           'usecase: '    + $('#id_usecase :selected').text() + '<br/>' +
                                           'exactmatch: ' + $('#id_exactmatch :selected').text() + '<br/>' +
                                           'notation: '   + $('#id_notation :selected').text() +
                                           '</div>');
            }
        };

        self.textbutton_widget.setup(options);
    },

    btn_update_clicked : function(e) {
        e.preventDefault();
        self.load_content('-1');
        self.btn_check_strings_clicked();
    },

    btn_save_clicked : function(e) {
        e.preventDefault();
        var new_regex = self.main_input.val();
        self.textbutton_widget.data = new_regex;
        self.textbutton_widget.close_and_set_new_data();
    },

    btn_cancel_clicked : function(e) {
        self.textbutton_widget.dialog.dialog("close");
        $('#id_test_regex').html('');
    },

    btn_check_strings_clicked : function(e) {
        $.ajax({
            type: 'GET',
            url: self.www_root + '/question/type/preg/authoring_tools/preg_regex_testing_tool_loader.php',
            data: {
                regex: self.main_input.val(),
                answer: $('#id_regex_match_text').val(),
                usecase: $('#id_usecase :selected').val(),
                exactmatch: $('#id_exactmatch :selected').val(),
                engine: $('#id_engine :selected').val(),
                notation: $('#id_notation :selected').val(),
                ajax: true
            },
            success: self.upd_check_strings_success,
            error: self.upd_failure
        });
    },

    btn_show_selection_clicked : function(e) {
        var range = self.regex_selection_widget.get_selected_text_range(self.main_input[0]);
        self.load_content_by_range(range.start, range.end);
    },

    rbtn_changed : function(e) {
        self.load_content(self.node_id);
    },

    upd_tools_success : function(data, textStatus, jqXHR) {
        var jsonarray = JSON.parse(data);

        var orientation = self.get_orientation();
        var displayas = self.get_displayas();
        var r = jsonarray[self.REGEX_KEY];
        var i = jsonarray[self.ID_KEY] + '';
        var t = jsonarray[self.TREE_KEY];
        var m = jsonarray[self.TREE_MAP_KEY];
        var g = jsonarray[self.GRAPH_KEY];
        var d = jsonarray[self.DESCRIPTION_KEY];

        // Cache the data.
        if (orientation && displayas && r && i && t && m && g && d) {
            self.cache_data(orientation, displayas, r, i, t, m, g, d);
        }

        // Display the data.
        self.display_data(i, t, m, g, d);
    },

    upd_check_strings_success : function(data, textStatus, jqXHR) {
        var jsonarray = JSON.parse(data);
        $('#id_test_regex').html(jsonarray.regex_test);
    },

    upd_failure : function(data, textStatus, jqXHR) {
       alert('Error\n' + textStatus + '\n' + jqXHR.responseText);
    },

    // Stores images and description for the given regex and node id in the cache
    cache_data : function(orientation, displayas, regex, id, t, m, g, d) {
        if (!self.cache[orientation][displayas][regex]) {
            self.cache[orientation][displayas][regex] = {};
        }
        if (!self.cache[orientation][displayas][regex][id]) {
            self.cache[orientation][displayas][regex][id] = {};
        }
        self.cache[orientation][displayas][regex][id][self.TREE_KEY] = t;
        self.cache[orientation][displayas][regex][id][self.TREE_MAP_KEY] = m;
        self.cache[orientation][displayas][regex][id][self.GRAPH_KEY] = g;
        self.cache[orientation][displayas][regex][id][self.DESCRIPTION_KEY] = d;
    },

    // Displays given images and description
    display_data : function(id, tree, tree_map, graph, description) {
        if (tree) {
            $('#id_tree').attr('src', tree);
        }
        if (tree_map) {
            $('#tree_map').html(tree_map);
            $('#id_tree').click(self.tree_node_misclicked);
            $(self.TREE_MAP_ID + ' > area').click(self.tree_node_clicked);
        }
        if (graph) {
            $('#id_graph').attr('src', graph);
        }
        if (description) {
            $('#description_handler').html(description);
        }

        self.highlight_description(id);
    },

    load_content_by_range : function(start, end) {
        var text = self.main_input.val();
        var firstline = 0;
        var lastline = 0;
        var lastpos = 0;
        var pos = text.indexOf("\n");
        while (pos != -1 && pos < start) {
            ++firstline;
            lastpos = pos;
            pos = text.indexOf("\n", pos + 1);
        }
        if (firstline > 0) {
            start -= lastpos;
        }
        while (pos != -1 && pos < end) {
            ++lastline;
            lastpos = pos;
            pos = text.indexOf("\n", pos + 1);
        }
        if (lastline > 0) {
            end -= lastpos;
        }
        --end;
        self.load_content('-1', {linefirst: firstline, linelast: lastline, indfirst: start, indlast: end}, true);
    },

    /** Checks for cached data and if it doesn't exist, sends a request to the server */
    load_content : function(id, coordinates, no_cache) {
        // Deselect the node when clicked for the second time.
        if (self.node_id == id && self.tree_orientation == self.get_orientation() && self.displayas == self.get_displayas()) {
            id = '-1';
        }

        // Update the fields.
        self.node_id = id;
        self.tree_orientation = self.get_orientation();
        self.displayas = self.get_displayas();

        // Unbind tree handlers so nothing is clickable till the response is received.
        $('#id_tree').unbind();
        $(self.TREE_MAP_ID + ' > area').unbind();

        var regex = self.main_input.val();

        // Check the cache.
        if(!no_cache) {
            var cachedregex = self.cache[self.tree_orientation][self.displayas][regex];
            var cachedid = null;
            if (cachedregex) {
                cachedid = cachedregex[id];
            }
            if (cachedid) {
                self.display_data(id, cachedid[self.TREE_KEY], cachedid[self.TREE_MAP_KEY], cachedid[self.GRAPH_KEY], cachedid[self.DESCRIPTION_KEY]);
                return;
            }
        }
        var data = {
            regex: regex,
            id: id,
            tree_orientation: self.tree_orientation,
            notation: $('#id_notation :selected').val(),
            engine: $('#id_engine :selected').val(),
            displayas: self.displayas,
            ajax: true
        };
        if (coordinates) {
            $.extend(data,coordinates);
            data.rangeselection = true;
        }
        $.ajax({
            type: 'GET',
            url: self.www_root + '/question/type/preg/authoring_tools/preg_authoring_tools_loader.php',
            data: data,
            success: self.upd_tools_success,
            error: self.upd_failure
        });
    },

    /**
     * Highlights part of text description of regex corresponding to given id.
     * Highlights nothing if '-1' is passed.
     */
    highlight_description : function(id) {
        var highlightedclass = 'description_highlighted';
        var oldhighlighted = $('.' + highlightedclass);

        if (oldhighlighted != null) {
           oldhighlighted.removeClass(highlightedclass).css('background', 'transparent');
        }
        var targetspan = $('.description_node_' + id);
        if (targetspan != null) {
            targetspan.addClass(highlightedclass);
            targetspan.css('background', '#FFFF00');
        }
    },

    /**
     * Handler of clicking on a node (map area, in fact)
     */
    tree_node_clicked : function(e) {
       var id = $(e.target).attr('id') + '';
       self.load_content(id);
    },

    /**
     * Handler of clicking on area outside all nodes
     */
    tree_node_misclicked : function(e) {
        self.load_content('-1');
    },

    /**
     * Handler of pressing on area of a map on regex tree image
     */
    regex_change : function(e) {
       self.textbutton_widget.data = self.main_input.val();
    },

    get_orientation : function() {
        return $("#fgroup_id_tree_orientation_radioset input:checked").val();
    },

    get_displayas : function () {
        return $("#fgroup_id_charset_process_radioset input:checked").val();
    },

    regex_selection_widget : {

        _fake_selection_el : null,

        _init : function () {
            this._fake_selection_el = document.createElement("div");
            $(this._fake_selection_el)  .css('border','1px dashed red')
                                        .css('position','absolute')
                                        .css('z-index','1000')
                                        .hide();
            $('#preg_authoring_tools_dialog').append(this._fake_selection_el);
        },

        _get_selection_position : function () {
            //TODO
        },

        get_selected_text_range : function(el) {
            var start = 0, end = 0, normalizedValue, range,
                textInputRange, len, endRange;

            if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
                start = el.selectionStart;
                end = el.selectionEnd;
            } else {
                range = document.selection.createRange();

                if (range && range.parentElement() == el) {
                    len = el.value.length;
                    normalizedValue = el.value.replace(/\r\n/g, "\n");

                    // Create a working TextRange that lives only in the input
                    textInputRange = el.createTextRange();
                    textInputRange.moveToBookmark(range.getBookmark());

                    // Check if the start and end of the selection are at the very end
                    // of the input, since moveStart/moveEnd doesn't return what we want
                    // in those cases
                    endRange = el.createTextRange();
                    endRange.collapse(false);

                    if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                        start = end = len;
                    } else {
                        start = -textInputRange.moveStart("character", -len);
                        start += normalizedValue.slice(0, start).split("\n").length - 1;

                        if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                            end = len;
                        } else {
                            end = -textInputRange.moveEnd("character", -len);
                            end += normalizedValue.slice(0, end).split("\n").length - 1;
                        }
                    }
                }
            }
            return { start: start, end: end };
        },

        /**
         * @param object coords coordinates of fake div: {top, bottom, left, right, height, width}
         */
        draw_fake_selection : function(coords) {
            $(this._fake_selection_el)  .css('top', coords.top)
                                        .css('bottom', coords.bottom)
                                        .css('left', coords.left)
                                        .css('right', coords.right)
                                        .css('height', coords.height)
                                        .css('width', coords.width)
                                        .show();
            return true;
        },

        hide_fake_selection : function() {
            $(this._fake_selection_el).hide();
        }
    }
};

return self;

})(jQuery);
