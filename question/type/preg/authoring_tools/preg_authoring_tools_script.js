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
M.preg_authoring_tools_script = (function() {

    var self = {

    /** @var string with www host of moodle (smth like 'http://moodle.site.ru/') */
    preg_www_root : null,

    /** @var {string} name of qtype_preg_textbutton parent object */
    textbutton_widget : null,

    /** @var {Object} reference for 'Check' button */
    check_btn : null,

    /** @var {Object} reference for 'input' on top of dialog */
    main_input : null,

    /** @var {Object} reference for 'Back' button */
    back_btn : null,

    /** @var {Integer} id of current node */
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

    /** @var {string} previously selected tree orientation */
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
     * @param {Object} Y NOT USED! It need because moodle passes this object as first param anyway...
     * @param {string} _preg_www_root string with www host of moodle
     * (smth like 'http://moodle.site.ru/')
     * @param {string} poasquestion_text_and_button_objname name of
     *  qtype_preg_textbutton parent object
     */
    init : function(Y, _preg_www_root, poasquestion_text_and_button_objname) {
        this.preg_www_root = _preg_www_root;
        this.textbutton_widget = M.poasquestion_text_and_button;
        this.setup_parent_object();
    },

    radio_changed : function() {
        self.load_content_by_id(self.node_id);
    },

    upd_answer_success : function(data, textStatus, jqXHR) {
        // TODO: delete on release
        var indexofbracket = data.indexOf('{');
        if (indexofbracket != 0) {
            alert(data.substr(0, indexofbracket));
            data = data.substr(indexofbracket);
        }
        // End of TODO

        var jsonarray = JSON.parse(data);
        $('#test_regex').html(jsonarray.regex_test);
    },

    regex_check_string : function(e) {
        $.ajax({
            type: 'GET',
            url: self.preg_www_root + '/question/type/preg/authoring_tools/preg_regex_testing_tool_loader.php',
            data: {
                regex: self.main_input.val(),
                answer: $('#id_regex_match_text').val(),
                ajax: true
            },
            success: self.upd_answer_success,    // upd_dialog_Succes(...) will call if request is successful
            error: self.upd_dialog_failure      // upd_dialog_failure(...) will call if request fails
        });
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
                $(self.textbutton_widget.dialog).load(self.preg_www_root + '/question/type/preg/authoring_tools/ast_preg_form.php', function() {
                    //TODO: set empty src in all field
                    self.check_btn = $('#id_regex_check').click(self.check_regex_clicked);
                    self.main_input =    $('#id_regex_text').change(self.regex_change)
                                                            //.change(self.textbutton_widget.fix_textarea_rows)
                                                            .keyup(self.textbutton_widget.fix_textarea_rows);
                    self.back_btn = $('#id_regex_back').click(self.back_regex_clicked);
                    $(self.main_input).val(self.textbutton_widget.data).trigger('keyup');
                    $("#tree_orientation_radioset input, #charset_process_radioset input").change(self.radio_changed);
                    // TODO - FIND GOOD WAY TO HIDE "EXPAND ALL" BUTTON!
                    $(".collapsible-actions").hide();
                    $('#id_regex_check_string').click(self.regex_check_string);
                    self.load_content_by_id('-1');
                });
            },

            // Function called on non-first form openings.
            oneachpresscallback : function() {
                self.main_input.val(self.textbutton_widget.data);
                self.load_content_by_id('-1');
            }
        };

        self.textbutton_widget.setup(options);
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
            $(self.TREE_MAP_ID + ' > area').on('click', self.tree_node_clicked);
        }
        if (graph) {
            $('#id_graph').attr('src', graph);
        }
        if (description) {
            $('#description_handler').html(description);
        }

        self.highlight_description(id);
    },

    // Calls if request for information about new regex is successful
    upd_dialog_success : function(data, textStatus, jqXHR) {

        // TODO: delete on release
        var indexofbracket = data.indexOf('{');
        if (indexofbracket != 0) {
            alert(data.substr(0, indexofbracket));
            data = data.substr(indexofbracket);
        }
        // End of TODO

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

    // Calls if request for information about new regex fails
    upd_dialog_failure : function(data, textStatus, jqXHR) {
       alert('ERROR\n'+textStatus+'\n'+jqXHR.responseText);
    },

    // Checks for cached data and if it doesn't exist, sends a request to the server
    load_content_by_id : function(id) {
        var currenttreeorientation = self.get_orientation();
        var currentdisplayas = self.get_displayas();
        var needdeselect = self.node_id == id
                        && self.tree_orientation===currenttreeorientation
                        && self.displayas===currentdisplayas;
        if (needdeselect) {
            id = '-1';  // Deselect the node when clicked for the second time.
        }
        self.tree_orientation = currenttreeorientation;
        self.displayas = currentdisplayas;
        self.node_id = id;
        var regex = self.main_input.val();

        // Check the cache.
        var cachedregex = self.cache[self.tree_orientation][self.displayas][regex];
        var cachedid = null;
        if (cachedregex) {
            cachedid = cachedregex[id];
        }
        if (cachedid) {
            self.display_data(id, cachedid[self.TREE_KEY], cachedid[self.TREE_MAP_KEY], cachedid[self.GRAPH_KEY], cachedid[self.DESCRIPTION_KEY]);
            return;
        }

        $.ajax({
            type: 'GET',
            url: self.preg_www_root + '/question/type/preg/authoring_tools/preg_authoring_tools_loader.php',
            data: {
                regex: regex,
                id: id,
                tree_orientation: self.tree_orientation,
                displayas: self.displayas,
                ajax: true
            },
            success: self.upd_dialog_success,    // upd_dialog_Succes(...) will call if request is successful
            error: self.upd_dialog_failure      // upd_dialog_failure(...) will call if request fails
        });
    },

    /**
     * Highlights part of text description of regex corresponding to given id.
     * Highlights nothing if '-1' is passed.
     */
    highlight_description : function(id) {
        var highlightedclass = 'description_highlighted';
        var oldhighlighted = $('.' + highlightedclass);

        if(oldhighlighted != null) {
           oldhighlighted.removeClass(highlightedclass).css('background', 'transparent');
        }
        var targetspan = $('.description_node_' + id);
        if (targetspan != null) {
            targetspan.addClass(highlightedclass);
            targetspan.css('background', '#FFFF00');
        }
    },

    /** Handler of pressing on 'Back' button of dialog */
    back_regex_clicked : function(e) {
        e.preventDefault();
        var new_regex = self.main_input.val();
        self.textbutton_widget.data = new_regex;
        self.textbutton_widget.close_and_set_new_data();
    },

    /** Handler of pressing on 'Check' button of dialog */
    check_regex_clicked : function(e) {
        e.preventDefault();
        self.load_content_by_id('-1');
    },

    /**
     * Handler of clicking on a node (map area, in fact)
     */
    tree_node_clicked : function(e) {
       var id = $(e.target).attr('id') + '';
       self.load_content_by_id(id);
    },

    /**
     * Handler of clicking on area outside all nodes
     */
    tree_node_misclicked : function(e) {
       self.load_content_by_id('-1');
    },

    /**
     * Handler of pressing on area of a map on regex tree image
     */
    regex_change : function(e) {
       self.textbutton_widget.data = self.main_input.val();
    },

    get_orientation : function() {
        return $("#tree_orientation_radioset input:checked").val();
    },

    get_displayas : function () {
        return $("#charset_process_radioset input:checked").val();
    }
};

return self;

})();