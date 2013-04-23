/**
 * Script for button "Check", "Back" and push in interactive tree
 *
 * @copyright &copy; 2012  Terechov Grigory, Pahomov Dmitry
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

// requaries: 'node', 'io-base'

/**
 * This object extends M.poasquestion_text_and_button with onfirstpresscallback()
 * function and oneachpresscallback()
 */
M.preg_authoring_tools_script = (function(){

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
    node_id : -1,

    /** @var {Object} reference for YUI object, extended with requarued modules */
    Y : null,

    TREE_KEY : 'tree_src',

    TREE_MAP_KEY : 'map',

    TREE_MAP_ID : '#qtype_preg_graph',

    GRAPH_KEY: 'graph_src',

    DESCRIPTION_KEY : 'description',

    HEIGHT : '_height',

    WIDTH : '_width',

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
                    self.main_input.on('change',self.regex_change)
                    self.back_btn = self.Y.one('#id_regex_back');
                    self.back_btn.on('click', self.back_regex);
                    self.main_input.set('value',self.textbutton_widget.data);
                    self.load_content();
                })
            },

            // Function called on non-first form openings.
            oneachpresscallback : function() {
                self.main_input.set('value',self.textbutton_widget.data);
                self.load_content();
            }
        };

        this.textbutton_widget.setup(options);
    },

    /** Calls if request for information about new regex is successful */
    upd_dialog_Success : function(id, o, a) {

        // this is debug output (should be deleted is release): !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        var indexofbracket = o.responseText.indexOf('{');
        if (indexofbracket != 0) {
            alert(o.responseText.substr(0,indexofbracket));
        }
        // allerting json array:
        // alert(o.responseText.substr(indexofbracket));
        // end of debug output !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        var jsonarray = Y.JSON.parse(o.responseText);

        //TODO: add errors message
        if (typeof jsonarray[self.TREE_KEY] != 'undefined') {
            self.Y.one('#id_tree')
                .setAttribute('src', '')
                .setAttribute('src', jsonarray[self.TREE_KEY])
                .setAttribute('width', jsonarray[self.TREE_KEY+self.WIDTH])
                .setAttribute('height', jsonarray[self.TREE_KEY+self.HEIGHT]);
        }

        if (typeof jsonarray[self.TREE_MAP_KEY] != 'undefined') {
            self.Y.one('#tree_map').setHTML(jsonarray[self.TREE_MAP_KEY]);
            self.Y.all(self.TREE_MAP_ID + ' > area').on('click', self.check_tree);
        }

        if (typeof jsonarray[self.GRAPH_KEY] != 'undefined') {
            self.Y.one('#id_graph')
                .setAttribute('src', '')
                .setAttribute('src', jsonarray[self.GRAPH_KEY])
                .setAttribute('width', jsonarray[self.GRAPH_KEY+self.WIDTH])
                .setAttribute('height', jsonarray[self.GRAPH_KEY+self.HEIGHT]);
        }

        if (typeof jsonarray[self.DESCRIPTION_KEY] != 'undefined' && self.node_id < 0) {
            self.Y.one('#description_handler').setHTML(jsonarray[self.DESCRIPTION_KEY]);
        }
    },

    /** Calls if request for information about new regex fails */
    upd_dialog_failure : function(id, o, a) {
       alert('ERROR ' + id + ' ' + a);
    },

    load_content_by_id : function(id) {

        this.node_id = id+0;
        id += '';
        var regex = this.main_input.get('value');
        this.textbutton_widget.data = regex;
        var url = this.preg_www_root + '/question/type/preg/authoring_tools/preg_authoring_tools_loader.php?regex='+encodeURIComponent(regex) + '&id=' + id;
        var cfg = {
            method: 'GET',
            xdr: {
                use: 'native'
            },
            on: {
                success: this.upd_dialog_Success,    // upd_dialog_Succes(...) will call if request is successful
                failure: this.upd_dialog_failure     // upd_dialog_failure(...) will call if request fails
            }
        };

        var response = this.Y.io(url, cfg);
    },

    load_content : function() {
        self.load_content_by_id(-1);
    },

    /**
     * Highlights part of text description of regex corresponding to giving id
     * @param {int} id id of node for which we should highlight part of description
     */
    highlight_description : function(id){

        var highlightedclass = 'description_highlighted';
        var oldhighlighted = this.Y.one('.'+highlightedclass);

        if(oldhighlighted!=null){
           oldhighlighted.removeClass(highlightedclass).setStyle('background','transparent');
        }
        var targetspan = this.Y.one('.description_node_'+id);
        targetspan.addClass(highlightedclass);
        targetspan.setStyle('background','#FFFF00');
    },

    /** Handler of pressing on 'Back' button of dialog */
    back_regex : function( e) {
        e.preventDefault();
        var new_regex = self.main_input.get('value');
        self.textbutton_widget.data = new_regex;
        self.textbutton_widget.close_and_set_new_data();
    },

    /** Handler of pressing on 'Check' button of dialog */
    check_regex : function( e ) {

        e.preventDefault();
        self.load_content();
    },

    /**
     * Handler of pressing on area of a map on regex tree image
     */
    check_tree : function( e ) {
       var id = e.currentTarget.getAttribute ( 'id' );
       self.highlight_description(id);
       self.load_content_by_id(id);
    },

     /**
     * Handler of pressing on area of a map on regex tree image
     */
    regex_change : function( e ) {
       self.textbutton_widget.data = self.main_input.get('value');
    }
};

return self;

})();
/*YUI().use('node', 'io-base', function (Y) {
    M.preg_authoring_tools_script.init(Y,'http://localhost/moodle','M.poasquestion_text_and_button');
});*/
