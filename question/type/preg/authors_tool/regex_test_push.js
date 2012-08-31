/**
 * Script for button "Test regex" from edit_ast_preg_form.php
 * 
 * @copyright &copy; 2012  Terechov Grigory, Pahomov Dmitry
 * @author Terechov Grigory, Pahomov Dmitry, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
M.qtype_preg_authors_tool = {};
 
M.qtype_preg_authors_tool.init = function(Y) {

    // variables:
    var node;
    var context;
    var back;
    var hidden;
    var currentlineedit;

    // functions:
    var load_content = function(url) {

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
            hidden = Y.one('#hidden_id');
            back.on("click", back_regex, hidden);
            node.on("click", check_regex, context);
        }

        var upd_dialog_failure = function(id, o, a) {
            alert("ERROR " + id + " " + a);
        }

        var cfg = {
            method: "GET",
            xdr: {
                use: 'native'
            },
            on: {
                success: upd_dialog_Success,
                failure: upd_dialog_failure
            }
        };

        var response = Y.io(url, cfg);
    }

    var testregexbtn_pressed = function(e) {

        e.preventDefault();

        //var page_regex_auth_helper_height = 1000;
        var pageregexauthhelperwidth = 1000;
        currentlineedit = this;
        //regex = encodeURIComponent(this.get('value'));
        // TODO - replace preg_www_root with moodle variable
        var pageregexauthhelperadr = preg_www_root + '/question/type/preg/authors_tool/ast_preg_form.php?regex=' + encodeURIComponent(this.get('value')) + '&id=-1' + '&id_line_edit=' + this.getAttribute('id');
        if (typeof dialog == 'undefined') {
            dialog = new Y.Panel({
                contentBox: Y.Node.create('<div id="dialog" />'),
                bodyContent: '<div class="message icon-warn">Loading...</div>',
                width: pageregexauthhelperwidth,
                //height     : page_regex_auth_helper_height,
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

            dialog.onCancel = function(e) {
                e.preventDefault();
                this.hide();
                // the callback is not executed, and is
                // callback reference removed, so it won't persist
                this.callback = false;
                //Y.one('#tree_img').setAttribute('src','');
            }

            dialog.onOK = function(e) {
                e.preventDefault();
                //TODO: implement save text widgets "string test" in database
                //alert('123');
                this.hide();
                // code that executes the user confirmed action goes here
                if(this.callback) {
                    this.callback();
                }
                //  callback reference removed, so it won't persist
                this.callback = false;
                //Y.one('#tree_img').setAttribute('src','');
            }

            //Y.one('#dialog .message').load('http://localhost/moodle/question/type/preg/ast_preg_form.php?regex='+this.get("value"));

            Y.one('#dialog .message').load(preg_www_root + '/question/type/preg/authors_tool/ast_preg_form.php?regex=' + encodeURIComponent(currentlineedit.get('value')) + '&id=-1', function() {
                /*Y.Get.js(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_script.js', function(err) {
                    if(err) {
                        alert('Error loading JS: ' + err[0].error, 'error');
                        return;
                    }
                })*/
                //TODO: set empty src in all field
                Y.one('#id_regex_text').set('value', currentlineedit.get('value'));
                Y.one('#id_tree').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
                Y.one('#id_graph').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
                load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex=' + encodeURIComponent(currentlineedit.get('value')) + '&id=-1');
            })

        } else {
            //TODO: set empty src in all field
            Y.one('#id_regex_text').set('value', currentlineedit.get('value'));
            Y.one('#id_tree').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
            Y.one('#id_graph').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
            load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex=' + encodeURIComponent(currentlineedit.get('value')) + '&id=-1');
            dialog.show();
        }
    }

    var back_regex = function( e ) {
        
        e.preventDefault();
       
        var new_regex = Y.one(context).get('value');
        currentlineedit.set('value',new_regex);
        dialog.hide();
        
        //TODO: call OK button
        //dialog.onOK();

    }

    var highlight_description = function(id){
        
        const highlightedclass = 'description_highlighted';
        var oldhighlighted = Y.one('.'+highlightedclass);
        
        if(oldhighlighted!=null){
           oldhighlighted.removeClass(highlightedclass).setStyle('background-color','transparent');
        }
        
        Y.one('.description_node_'+id).addClass(highlightedclass).setStyle('background-color','yellow');
    }
    
    var check_regex = function( e ) {
        
        e.preventDefault();
        
        load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex='+encodeURIComponent(Y.one('#id_regex_text').get('value'))+'&id=-1');
    }    
    
    var check_tree = function( e ) {

       id = e.currentTarget.getAttribute ( 'id' );
       //alert(id);
       highlight_description(id);
        
       /*var tmp = encodeURIComponent(context.get("value"));
       
       var url = preg_www_root + '/question/type/preg/authors_tool/ast_preg_form.php?regex=' + tmp + '&id=' + id;
       Y.io(url);
       Y.one('#id_graph').setAttribute('src','');
       setTimeout(function() {
           Y.one('#id_graph').setAttribute('src', preg_www_root + '/question/type/preg/tmp_img/graph.png');
           //TODO: implement for tree and map
           }, 500);*/
       //Y.one('#id_graph').setAttribute('src','').setAttribute('src','http://localhost/moodle/question/type/preg/tmp_img/graph.png');
       
       load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex='+encodeURIComponent(Y.one('#id_regex_text').get('value')) + '&id=' + id);
    }

    // code:
    var i = 0;
    var testregexbtn = Y.one('#id_regextest_' + i);
    var testregexlineedit = Y.one('#id_answer_' + i);
    while(testregexbtn != null) {
        testregexbtn.on("click", testregexbtn_pressed, testregexlineedit);
        ++i;
        testregexbtn = Y.one('#id_regextest_' + i);
        testregexlineedit = Y.one('#id_answer_' + i);
    }
}


YUI().use('node', 'panel', 'node-load', 'get', "io-xdr", "substitute", "json-parse", function(Y) {
    M.qtype_preg_authors_tool.init(Y);
});
