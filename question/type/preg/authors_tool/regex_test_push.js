/**
 * Script for button "Test regex" from edit_ast_preg_form.php
 * 
 * @copyright &copy; 2012  Terechov Grigory, Pahomov Dmitry
 * @author Terechov Grigory, Pahomov Dmitry, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
// TODO - code documentation
 
M.qtype_preg_authors_tool = {};
  
M.qtype_preg_authors_tool.init = function(Y) {

    // variables:
    
    /** @var a reference to 'Check' button */
    var node;

    /** @var a reference to line edit with new regex */
    var context;

    /** @var a reference to 'Back' button */
    var back;

    /** @var a hidden field */
    //var hidden; // unused ?

    /** @var a reference to line edit on a base page from which we are getting a regex */
    var currentlineedit;

    // functions:
    
    /**
     * Loads data about new regex
     *
     * @param url URL address to which a request is made, with the new regular expression
     */
    var load_content = function(url) {

        /**
         * Calls if request for information about new regex is successful
         */
        var upd_dialog_Success = function(id, o, a) {
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
                Y.one('#id_tree').setAttribute("src", '').setAttribute("src", jsonarray['tree_src']);
            }
            if(typeof jsonarray['map'] != 'undefined') {
                Y.one('#tree_map').setHTML(jsonarray['map']);
                Y.all("#_anonymous_0 > area").on('click', check_tree);
            }
            if(typeof jsonarray['graph_src'] != 'undefined') {
                Y.one('#id_graph').setAttribute("src", '').setAttribute("src", jsonarray['graph_src']);
            }
            if(typeof jsonarray['description'] != 'undefined') {
                Y.one('#description_handler').setHTML(jsonarray['description']);
            }

            node = Y.one('#id_regex_check');
            context = Y.one('#id_regex_text');
            back = Y.one('#id_regex_back');
            //hidden = Y.one('#hidden_id');
            back.on("click", back_regex/*, hidden*/);
            node.on("click", check_regex, context);
        }

        /**
         * Calls if request for information about new regex fails
         */
        var upd_dialog_failure = function(id, o, a) {
            alert("ERROR " + id + " " + a);
        }

        /** @var configuration of request */
        var cfg = {
            method: "GET",
            xdr: {
                use: 'native'
            },
            on: {
                success: upd_dialog_Success,    // upd_dialog_Succes(...) will call if request is successful
                failure: upd_dialog_failure     // upd_dialog_failure(...) will call if request fails
            }
        };

        var response = Y.io(url, cfg);
    }

    /**
     * Handler of pressing on 'Test regex' button
     */
    var testregexbtn_pressed = function(e) {

        e.preventDefault();

        /** @var width of dialog */
        var pageregexauthhelperwidth = 1000;

        /** @var a reference to line edit from which we got a regex (this reference is passed as 'this' when we install this handler) */
        currentlineedit = this;

        /** @var regex, which we should analyse */
        var currentregex = encodeURIComponent(this.get('value'));

        // TODO - replace preg_www_root with moodle variable ???
        /** @var full address of request */
        var pageregexauthhelperadr = preg_www_root + '/question/type/preg/authors_tool/ast_preg_form.php?regex=' + currentregex + '&id=-1' /*+ '&id_line_edit=' + this.getAttribute('id')*/;

        if (typeof dialog == 'undefined') {
            // if the 'Test regex' button is first pressed, we should generate a dialog window
            dialog = new Y.Panel({
                contentBox: Y.Node.create('<div id="dialog" />'),
                bodyContent: '<div class="message icon-warn">Loading...</div>',
                width: pageregexauthhelperwidth,
                zIndex: 120,
                centered: true,
                modal: true, // modal behavior
                render: '.example',
                visible: true, // make visible explicitly with .show()
                buttons: {
                    footer: [
                        {
                            name: 'cancel',
                            label: 'Cancel',
                            action: 'onCancel'
                        },

                        {
                            name: 'proceed',
                            label: 'OK',
                            action: 'onOK'
                        }
                    ]
                }
            });

            /**
             * Handler of pressing on 'Cancel' button of dialog
             */
            dialog.onCancel = function(e) {
                e.preventDefault();
                this.hide();
                // the callback is not executed, and is
                // callback reference removed, so it won't persist
                this.callback = false;
                //Y.one('#tree_img').setAttribute('src','');
            }

            /**
             * Handler of pressing on 'Ok' button of dialog
             */
            dialog.onOK = function(e) {
                e.preventDefault();
                //TODO: implement save text widgets "string test" in database
                this.hide();
                // code that executes the user confirmed action goes here
                //if(this.callback) {
                //    this.callback();
                //}
                //  callback reference removed, so it won't persist
                //this.callback = false;
                //Y.one('#tree_img').setAttribute('src','');
            }


            Y.one('#dialog .message').load(preg_www_root + '/question/type/preg/authors_tool/ast_preg_form.php?regex=' + encodeURIComponent(currentlineedit.get('value')) + '&id=-1', function() {
                //TODO: set empty src in all field
                Y.one('#id_regex_text').set('value', currentlineedit.get('value'));
                Y.one('#id_tree').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
                Y.one('#id_graph').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
                load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex=' + encodeURIComponent(currentlineedit.get('value')) + '&id=-1');
            })

        } else {
            // if a dialog window is already generated we should fill it with new data
            //TODO: set empty src in all field
            Y.one('#id_regex_text').set('value', currentlineedit.get('value'));
            Y.one('#id_tree').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
            Y.one('#id_graph').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
            load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex=' + encodeURIComponent(currentlineedit.get('value')) + '&id=-1');
            dialog.show();
        }
    }

    /**
     * Handler of pressing on 'Back' button of dialog
     */
    var back_regex = function( e ) {
        
        e.preventDefault();
       
        var new_regex = Y.one(context).get('value');
        currentlineedit.set('value',new_regex);
        dialog.hide();
    }

    /**
     * Highlights part of text description of regex corresponding to giving id
     *
     * @param id id of node for which we should highlight part of description
     */
    var highlight_description = function(id){
        
        const highlightedclass = 'description_highlighted';
        var oldhighlighted = Y.one('.'+highlightedclass);
        
        if(oldhighlighted!=null){
           oldhighlighted.removeClass(highlightedclass).setStyle('background-color','transparent');
        }
        
        Y.one('.description_node_'+id).addClass(highlightedclass).setStyle('background-color','yellow');
    }

    /**
     * Handler of pressing on 'Check' button of dialog
     */
    var check_regex = function( e ) {
        
        e.preventDefault();
        
        load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex='+encodeURIComponent(Y.one('#id_regex_text').get('value'))+'&id=-1');
    }    

    /**
     * Handler of pressing on area of a map on regex tree image
     */
    var check_tree = function( e ) {

       id = e.currentTarget.getAttribute ( 'id' );
       highlight_description(id);
       load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex='+encodeURIComponent(Y.one('#id_regex_text').get('value')) + '&id=' + id);
    }

    // code:
    // installation click handler:
    var i = 0;
    var testregexbtn = Y.one('#id_answer_' + i + '_test');
    var testregexlineedit = Y.one('#id_answer_' + i);
    while(testregexbtn != null) {
        testregexbtn.on("click", testregexbtn_pressed, testregexlineedit);
        ++i;
        testregexbtn = Y.one('#id_answer_' + i + '_test');
        testregexlineedit = Y.one('#id_answer_' + i);
    }
}


YUI().use('node', 'panel', 'node-load', 'get', "io-xdr", "substitute", "json-parse", function(Y) {
    M.qtype_preg_authors_tool.init(Y);
});
