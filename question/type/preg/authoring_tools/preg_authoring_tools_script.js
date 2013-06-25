/**
 * Script for button "Check", "Back" and push in interactive tree
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

// requires: 'node', 'io-base'

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

    /** @var {Object} cache of content; first dimension is regex, second is node id */
    cache : {
        vertical:{},
        horizontal:{}
    },

    /** @var {string} previously selected tree orientation */
    tree_orientation : null,

    /** @var {Object} reference for YUI object, extended with requarued modules */
    Y : null,

    REGEX_KEY : 'regex',

    ID_KEY : 'id',

    TREE_KEY : 'tree_src',

    TREE_MAP_KEY : 'map',

    TREE_MAP_ID : '#qtype_preg_graph',

    GRAPH_KEY: 'graph_src',

    DESCRIPTION_KEY : 'description',

    /**
     * setups module
     * @param {object} Y yui object
     * @param {string} _preg_www_root string with www host of moodle
     * (smth like 'http://moodle.site.ru/')
     * @param {string} poasquestion_text_and_button_objname name of
     * qtype_preg_textbutton parent object
     */
    init : function(Y, _preg_www_root, poasquestion_text_and_button_objname) {
        this.Y = Y;
        this.preg_www_root = _preg_www_root;
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
                this.dialoghtmlnode.load(self.preg_www_root + '/question/type/preg/authoring_tools/ast_preg_form.php?regex=' + encodeURIComponent(this.data) + '&id=-1', function() {
                    //TODO: set empty src in all field
                    self.check_btn = self.Y.one('#id_regex_check')
                    self.check_btn.on('click', self.check_regex);
                    self.main_input = self.Y.one('#id_regex_text');
                    self.main_input.on('change', self.regex_change)
                    self.back_btn = self.Y.one('#id_regex_back');
                    self.back_btn.on('click', self.back_regex);
                    self.main_input.set('value', self.textbutton_widget.data);
                    self.Y.one('#id_tree').on('click', self.tree_node_misclicked);
                    self.load_content_by_id('-1');
                })
            },

            // Function called on non-first form openings.
            oneachpresscallback : function() {
                self.main_input.set('value', self.textbutton_widget.data);
                self.load_content_by_id('-1');
            }
        };

        this.textbutton_widget.setup(options);
    },

    // Stores images and description for the given regex and node id in the cache
    cache_data : function(orientation, regex, id, t, m, g, d) {
        if (!self.cache[orientation][regex]) {
            self.cache[orientation][regex] = {};
        }
        if (!self.cache[orientation][regex][id]) {
            self.cache[orientation][regex][id] = {};
        }

        self.cache[orientation][regex][id][self.TREE_KEY] = t;
        self.cache[orientation][regex][id][self.TREE_MAP_KEY] = m;
        self.cache[orientation][regex][id][self.GRAPH_KEY] = g;
        self.cache[orientation][regex][id][self.DESCRIPTION_KEY] = d;
    },

    // Displays given images and description
    display_data : function(i, t, m, g, d) {
        if (t) self.Y.one('#id_tree').setAttribute('src', t);
        if (m) {
            self.Y.one('#tree_map').setHTML(m);
            self.Y.all(self.TREE_MAP_ID + ' > area').on('click', self.tree_node_clicked);
        }
        if (g) self.Y.one('#id_graph').setAttribute('src', g);
        if (d) self.Y.one('#description_handler').setHTML(d);

        self.highlight_description(i);
    },

    // Calls if request for information about new regex is successful
    upd_dialog_success : function(id, o, a) {

        // TODO: delete on release
        var indexofbracket = o.responseText.indexOf('{');
        if (indexofbracket != 0) {
            alert(o.responseText.substr(0, indexofbracket));
        }
        // End of TODO

        var jsonarray = Y.JSON.parse(o.responseText);

        var orientation = self.get_orientation();
        var r = jsonarray[self.REGEX_KEY];
        var i = jsonarray[self.ID_KEY] + '';
        var t = jsonarray[self.TREE_KEY];
        var m = jsonarray[self.TREE_MAP_KEY];
        var g = jsonarray[self.GRAPH_KEY];
        var d = jsonarray[self.DESCRIPTION_KEY];

        // Cache the data.
        if (orientation && r && i && t && m && g && d) {
            self.cache_data(orientation, r, i, t, m, g, d);
        }

        // Display the data.
        self.display_data(i, t, m, g, d);
    },

    // Calls if request for information about new regex fails
    upd_dialog_failure : function(id, o, a) {
       alert('ERROR ' + id + ' ' + a);
    },

    // Checks for cached data and if it doesn't exist, sends a request to the server
    load_content_by_id : function(id) {
        if (self.node_id == id) {
            id = '-1';  // Deselect the node when clicked for the second time.
        }
        self.node_id = id;
        var regex = self.main_input.get('value');

        // Check the cache.
        var cachedregex = self.cache[self.get_orientation()][regex];
        var cachedid = null;
        if (cachedregex) {
            cachedid = cachedregex[id];
        }
        if (cachedid) {
            self.display_data(id, cachedid[self.TREE_KEY], cachedid[self.TREE_MAP_ID], cachedid[self.GRAPH_KEY], cachedid[self.DESCRIPTION_KEY]);
            return;
        }

        var url = self.preg_www_root + '/question/type/preg/authoring_tools/preg_authoring_tools_loader.php'
                +'?regex='
                + encodeURIComponent(regex)
                + '&id='
                + id
                + '&tree_orientation='
                + this.get_orientation();
        var cfg = {
            method: 'GET',
            xdr: {
                use: 'native'
            },
            on: {
                success: self.upd_dialog_success,    // upd_dialog_Succes(...) will call if request is successful
                failure: self.upd_dialog_failure     // upd_dialog_failure(...) will call if request fails
            }
        };

        var response = self.Y.io(url, cfg);
    },

    /**
     * Highlights part of text description of regex corresponding to given id.
     * Highlights nothing if '-1' is passed.
     */
    highlight_description : function(id) {
        var highlightedclass = 'description_highlighted';
        var oldhighlighted = this.Y.one('.' + highlightedclass);

        if(oldhighlighted != null) {
           oldhighlighted.removeClass(highlightedclass).setStyle('background', 'transparent');
        }
        var targetspan = this.Y.one('.description_node_' + id);
        if (targetspan != null) {
            targetspan.addClass(highlightedclass);
            targetspan.setStyle('background', '#FFFF00');
        }
    },

    /** Handler of pressing on 'Back' button of dialog */
    back_regex : function(e) {
        e.preventDefault();
        var new_regex = self.main_input.get('value');
        self.textbutton_widget.data = new_regex;
        self.textbutton_widget.close_and_set_new_data();
    },

    /** Handler of pressing on 'Check' button of dialog */
    check_regex : function(e) {
        e.preventDefault();
        self.load_content_by_id('-1');
    },

    /**
     * Handler of clicking on a node (map area, in fact)
     */
    tree_node_clicked : function(e) {
       var id = e.currentTarget.getAttribute('id') + '';
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
       self.textbutton_widget.data = self.main_input.get('value');
    },

    get_orientation : function() {
        return this.Y.one("#tree_orientation_radioset input:checked").get("value");
    }
};

return self;

})();
/*YUI().use('node', 'io-base', function (Y) {
    M.preg_authoring_tools_script.init(Y,'http://localhost/moodle','M.poasquestion_text_and_button');
});*/
