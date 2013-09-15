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

    /** @var string with moodle root url (smth like 'http://moodle.site.ru/') */
    www_root : null,

    /** @var {string} name of qtype_preg_textbutton parent object */
    textbutton_widget : null,

    /** @var {Object} reference to the regex textarea */
    regex_input : null,

    /** @var {Object} cache of content; dimensions are: orientation, display mode, regex, selection borders */
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

            // Function called on the very first form opening.
            onfirstpresscallback : function () {
                $.ajax({
                    url: self.www_root + '/question/type/preg/authoring_tools/preg_authoring.php',
                    type: "GET",
                    dataType: "text"
                }).done(function( responseText, textStatus, jqXHR ) {
                    $(self.textbutton_widget.dialog).html($.parseHTML(responseText, document, true));

                    // Create a clone of the textarea.
                    var textarea = $('<textarea style="margin:0;padding:0;border:none;resize:none;outline:none;overflow:hidden;width:100%;height:100%"></textarea>');
                    $('#id_regex_text').each(function() {
                        $.each(this.attributes, function() {
                            if (this.specified) {
                                textarea.attr(this.name, this.value);
                            }
                        });
                    });

                    // Replace the textarea with an iframe. TODO: CSS для ресайзового треугольничка
                    var iframeMarkup = '<div id="id_regex_resizable" style="border:1px solid black;padding-right:10px;overflow:hidden;width:50%;height:20px">' +
                                         '<iframe id="id_regex_text_replacement" style="border:none;resize:none;width:100%;height:100%"/>' +
                                       '</div>';

                    $('#id_regex_text').replaceWith(iframeMarkup);
                    $("#id_regex_resizable").resizable();

                    // Deal with iframe.
                    var iframe = $('#id_regex_text_replacement');

                    setTimeout(function() {
                        var innerDoc = iframe[0].contentWindow.document,
                            innerBody = $('body', innerDoc);
                        innerBody.css('margin', '0').css('padding', '0')
                                 .append(textarea);
                    }, 1);

                    // Add handlers for the buttons.
                    $('#id_regex_show').click(self.btn_show_clicked);
                    $('#id_regex_save').click(self.btn_save_clicked);
                    $("#id_regex_cancel").click(self.btn_cancel_clicked);
                    $('#id_regex_check_strings').click(self.btn_check_strings_clicked);

                    // Add handlers for the radiobuttons.
                    $("#fgroup_id_tree_orientation_radioset input").change(self.rbtn_changed);
                    $("#fgroup_id_charset_process_radioset input").change(self.rbtn_changed);

                    // Add handlers for the regex textarea.
                    self.regex_input = textarea;
                    self.regex_input.keyup(self.textbutton_widget.fix_textarea_rows);

                    // Add handlers for the regex testing textarea.
                    $('#id_regex_match_text').keyup(self.textbutton_widget.fix_textarea_rows);

                    options.oneachpresscallback();
                });
            },

            // Function called on non-first form openings.
            oneachpresscallback : function () {
                self.regex_input.val(self.textbutton_widget.data).trigger('keyup');

                // Put the testing data into ui.
                $('#id_regex_match_text').val($('input[name=\'regextests[' + $(self.textbutton_widget.currentlinput).attr('id').split("id_answer_")[1] + ']\']').val())
                                         .trigger('keyup');

                options.display_question_options();
                self.load_content(-1);
                $('#id_regex_check_strings').click();
            },

            onsaveclicked : function () {
                $('#id_regex_save').click();
            },

            oncancelclicked : function () {
                $('#id_regex_cancel').click();
            },

            display_question_options : function () {
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

    btn_show_clicked : function (e) {
        e.preventDefault();
        self.load_content(-1);
        $('#id_regex_check_strings').click();
    },

    btn_save_clicked : function (e) {
        e.preventDefault();
        var new_regex = self.regex_input.val();
        self.textbutton_widget.data = new_regex;
        self.textbutton_widget.close_and_set_new_data();
    },

    btn_cancel_clicked : function (e) {
        e.preventDefault();
        self.textbutton_widget.dialog.dialog("close");
        $('#id_test_regex').html('');
    },

    btn_check_strings_clicked : function (e) {
        e.preventDefault();
        $.ajax({
            type: 'GET',
            url: self.www_root + '/question/type/preg/authoring_tools/preg_regex_testing_tool_loader.php',
            data: {
                regex: self.regex_input.val(),
                strings: $('#id_regex_match_text').val(),
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

    rbtn_changed : function (e) {
        e.preventDefault();
        self.load_content(-1);
    },

    tree_node_clicked : function (e) {
        e.preventDefault();
        self.load_content(e.target.id.split(',')[0]);
    },

    tree_node_misclicked : function (e) {
        e.preventDefault();
        self.load_content(-1);
    },

    upd_tools_success : function (data, textStatus, jqXHR) {
        var json = JSON.parse(data),
            _o = self.get_orientation(),
            _d = self.get_displayas(),
            _r = json['regex'],
            _s = json['newindfirst'] + '' + json['newindlast'],
            t = json[self.TREE_KEY],
            m = json[self.TREE_MAP_KEY],
            g = json[self.GRAPH_KEY],
            d = json[self.DESCRIPTION_KEY];

        // Cache the data.
        if (_o && _d && _r && _s && t && m && g && d) {
            self.cache_data(_o, _d, _r, _s, t, m, g, d);
        }

        // Display the data.
        self.display_data(json['id'], t, m, g, d);

        // Update the regex text selection if needed.
        var newindfirst = json['newindfirst'],
            newindlast = json['newindlast'] - json['newindfirst'] + 1;
        if (newindfirst < 0 || json['newindlast'] < json['newindfirst']) {
            newindfirst = -1;
            newindlast = 0;
        }
        self.regex_input.textrange('set', newindfirst, newindlast);
    },

    upd_check_strings_success : function (data, textStatus, jqXHR) {
        var json = JSON.parse(data);
        $('#id_test_regex').html(json.regex_test);
    },

    upd_failure : function (data, textStatus, jqXHR) {
       //alert('Error\n' + textStatus + '\n' + jqXHR.responseText);
    },

    // Stores images and description for the given regex and node id in the cache
    cache_data : function (_o, _d, _r, _s, t, m, g, d) {
        if (!self.cache[_o][_d][_r]) {
            self.cache[_o][_d][_r] = {};
        }
        if (!self.cache[_o][_d][_r][_s]) {
            self.cache[_o][_d][_r][_s] = {};
        }
        self.cache[_o][_d][_r][_s][self.TREE_KEY] = t;
        self.cache[_o][_d][_r][_s][self.TREE_MAP_KEY] = m;
        self.cache[_o][_d][_r][_s][self.GRAPH_KEY] = g;
        self.cache[_o][_d][_r][_s][self.DESCRIPTION_KEY] = d;
    },

    // Displays given images and description
    display_data : function (id, t, m, g, d) {
        var tree_err = $('#tree_err'),
            tree_img = $('#tree_img'),
            tree_map = $('#tree_map'),
            graph_err = $('#graph_err'),
            graph_img = $('#graph_img'),
            err = null;

        if (t) {
            err = (t.substring(0, 4) != 'data');
            if (err) {
                tree_err.html(t);
                tree_img.removeAttr('src').css('visibility', 'hidden');
            } else {
                tree_err.html('');
                tree_img.attr('src', t).css('visibility', 'visible');
            }
        }
        if (m) {
            tree_map.html(m);
            tree_img.click(self.tree_node_misclicked);
            $(self.TREE_MAP_ID + ' > area').click(self.tree_node_clicked);
        }
        if (g) {
            err = (g.substring(0, 4) != 'data');
            if (err) {
                graph_err.html(g);
                graph_img.removeAttr('src').css('visibility', 'hidden');
            } else {
                graph_err.html('');
                graph_img.attr('src', g).css('visibility', 'visible');
            }
        }
        if (d) {
            $('#description_handler').html(d);
        }

        self.highlight_description(id);
    },

    /** Checks for cached data and if it doesn't exist, sends a request to the server */
    load_content : function (id) {
        var selection = self.regex_input.textrange('get'),
            indfirst = selection.start,
            indlast = selection.end - 1;
        if (indlast < indfirst) {
            indfirst = -1;
            indlast = -1;
        }

        // Update the fields.
        var tree_orientation = self.get_orientation(),
            displayas = self.get_displayas(),
            regex = self.regex_input.val(),
            selection = indfirst + '' + indlast;

        // Unbind tree handlers so nothing is clickable till the response is received.
        $('#tree_img').unbind();
        $(self.TREE_MAP_ID + ' > area').unbind();

        var cachedregex = null,
            cachedsel = null;

        // Check the cache.
        cachedregex = self.cache[tree_orientation][displayas][regex];
        if (cachedregex) {
            cachedsel = cachedregex[selection];
        }
        if (cachedsel) {
            self.display_data(id, cachedsel[self.TREE_KEY], cachedsel[self.TREE_MAP_KEY], cachedsel[self.GRAPH_KEY], cachedsel[self.DESCRIPTION_KEY]);
            return;
        }

        var data = {
            regex: regex,
            indfirst: indfirst,
            indlast: indlast,
            tree_orientation: tree_orientation,
            notation: $('#id_notation :selected').val(),
            engine: $('#id_engine :selected').val(),
            displayas: displayas,
            ajax: true
        };
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
    highlight_description : function (id) {
        var highlightedclass = 'description_highlighted',
            oldhighlighted = $('.' + highlightedclass),
            targetspan = $('.description_node_' + id);
        if (oldhighlighted != null) {
           oldhighlighted.removeClass(highlightedclass).css('background', 'transparent');
        }
        if (targetspan != null) {
            targetspan.addClass(highlightedclass);
            targetspan.css('background', '#FFFF00');
        }
    },

    get_orientation : function () {
        return $("#fgroup_id_tree_orientation_radioset input:checked").val();
    },

    get_displayas : function () {
        return $("#fgroup_id_charset_process_radioset input:checked").val();
    }
};

return self;

})(jQuery);
