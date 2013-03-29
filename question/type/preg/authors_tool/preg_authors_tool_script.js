/**
 * Script for button "Check", "Back" and push in interactive tree
 *
 * @copyright &copy; 2012  Terechov Grigory, Pahomov Dmitry
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

// requaries: 'node', 'io-base'

M.preg_authors_tool_script = {

    /** @var string with www host of moodle (smth like 'http://moodle.site.ru/') */
    preg_www_root : null,

    /** @var {string} name of qtype_preg_textbutton parent object */
    textbutton_widget : null,

    check_btn : null,
    main_input : null,
    back_btn : null,
    Y : null,

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
        /*this.check_btn = this.Y.one('#id_regex_check');
        this.main_input = this.Y.one('#id_regex_text');
        this.back_btn = this.Y.one('#id_regex_back');
        this.main_input.set('value',this.textbutton_widget.currentlinput.get('value'));*/
        //alert(1);
        this.setup_parent_object();
    },

    /**
     * Sets up options of parent object
     */
    setup_parent_object : function() {
        var self = this;
        var options = {
            onfirstpresscallback : function() {
                this.dialoghtmlnode.load(self.preg_www_root + '/question/type/preg/authors_tool/ast_preg_form.php?regex=' + encodeURIComponent(this.data) + '&id=-1', function() {
                    //TODO: set empty src in all field
                    self.check_btn = self.Y.one('#id_regex_check')
                    self.check_btn.on("click", self.check_regex);
                    self.main_input = self.Y.one('#id_regex_text');
                    self.main_input.on('change',self.regex_change)
                    self.back_btn = self.Y.one('#id_regex_back');
                    self.back_btn.on("click", self.back_regex);
                    self.main_input.set('value',M.poasquestion_text_and_button.data);
                    self.load_content();
                })
            },

            oneachpresscallback : function() {
                M.preg_authors_tool_script.load_content();
            }
        };
        this.textbutton_widget.setup(options);
    },

    /** Calls if request for information about new regex is successful */
    upd_dialog_Success : function(id, o, a) {

        var obj = M.preg_authors_tool_script;
        // this is debug output (should be deleted is release): !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        var indexofbracket = o.responseText.indexOf("{");
        if (indexofbracket != 0) {
            alert(o.responseText.substr(0,indexofbracket));
        }
        // allerting json array:
        // alert(o.responseText.substr(indexofbracket));
        // end of debug output !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        var jsonarray = Y.JSON.parse(o.responseText);

        //TODO: add errors message
        if(typeof jsonarray['tree_src'] != 'undefined') {
            obj.Y.one('#id_tree').setAttribute("src", '').setAttribute("src", jsonarray['tree_src']);
        }
        if(typeof jsonarray['map'] != 'undefined') {
            obj.Y.one('#tree_map').setHTML(jsonarray['map']);
            obj.Y.all("#_anonymous_0 > area").on('click', obj.check_tree);
        }
        if(typeof jsonarray['graph_src'] != 'undefined') {
            obj.Y.one('#id_graph').setAttribute("src", '').setAttribute("src", jsonarray['graph_src']);
        }
        if(typeof jsonarray['description'] != 'undefined') {
            obj.Y.one('#description_handler').setHTML(jsonarray['description']);
        }

        //obj.back_btn.on("click", obj.back_regex, obj);
        //obj.check_btn.on("click", obj.check_regex, obj);
    },

    /** Calls if request for information about new regex fails */
    upd_dialog_failure : function(id, o, a) {
       alert("ERROR " + id + " " + a);
    },

    load_content_by_id : function(id) {

        //var obj = M.preg_authors_tool_script;
        id += '';
        var regex = this.main_input.get('value');
        this.textbutton_widget.data = regex;
        var url = this.preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex='+encodeURIComponent(regex) + '&id=' + id;
        var cfg = {
            method: "GET",
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
        M.preg_authors_tool_script.load_content_by_id(-1);
    },

    /**
     * Highlights part of text description of regex corresponding to giving id
     * @param {int} id id of node for which we should highlight part of description
     */
    highlight_description : function(id){

        var highlightedclass = 'description_highlighted';
        var oldhighlighted = this.Y.one('.'+highlightedclass);

        if(oldhighlighted!=null){
           oldhighlighted.removeClass(highlightedclass).setStyle('background-color','transparent');
        }
        var targetspan = this.Y.one('.description_node_'+id);
        targetspan.addClass(highlightedclass);
        targetspan.setStyle('background','#FFFF00');
    },

    /** Handler of pressing on 'Back' button of dialog */
    back_regex : function( e) {
        e.preventDefault();
        var obj = M.preg_authors_tool_script;
        var new_regex = obj.main_input.get('value');
        obj.textbutton_widget.data = new_regex;
        obj.textbutton_widget.close_and_set_new_data();
    },

    /** Handler of pressing on 'Check' button of dialog */
    check_regex : function( e ) {

        e.preventDefault();
        var obj = M.preg_authors_tool_script;
        obj.load_content();
    },

    /**
     * Handler of pressing on area of a map on regex tree image
     */
    check_tree : function( e ) {
       var obj = M.preg_authors_tool_script;
       var id = e.currentTarget.getAttribute ( 'id' );
       obj.highlight_description(id);
       obj.load_content_by_id(id);
    },

     /**
     * Handler of pressing on area of a map on regex tree image
     */
    regex_change : function( e ) {
       M.preg_authors_tool_script.textbutton_widget.data = M.preg_authors_tool_script.main_input.get('value');
    }
}

/*YUI().use('node', 'io-base', function (Y) {
    M.preg_authors_tool_script.init(Y,'http://localhost/moodle','M.poasquestion_text_and_button');
});*/
