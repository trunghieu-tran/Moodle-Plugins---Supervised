/**
 * Script for button "Test regex" from edit_ast_preg_form.php
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
YUI().use('node', 'panel', 'node-load', 'get', function (Y) {
    
    var testfoo = function( e ) {
        
       e.preventDefault();
       
        var page_regex_auth_helper_height = 1000;
        var page_regex_auth_helper_width = 1000;
        var page_regex_auth_helper_adr = 'http://localhost/moodle/question/type/preg/authors_tool/ast_preg_form.php?regex='+encodeURIComponent(this.get('value'))+'&id=-1'+'&id_line_edit='+this.getAttribute('id');
        dialog = new Y.Panel({
            contentBox : Y.Node.create('<div id="dialog" />'),
            bodyContent: '<div class="message icon-warn">Loading...</div>',
            width      : page_regex_auth_helper_width,
            //height     : page_regex_auth_helper_height,
            zIndex     : 120,
            centered   : true,
            modal      : true, // modal behavior
            render     : '.example',
            visible    : true, // make visible explicitly with .show()
            buttons    : {
                footer: [
                    {
                        name  : 'cancel',
                        label : 'Cancel',
                        action: 'onCancel'
                    },

                    {
                        name  : 'proceed',
                        label : 'OK',
                        action: 'onOK'
                    }
                ]
            }
        });
    
        dialog.onCancel = function (e) {
            e.preventDefault();
            this.hide();
            // the callback is not executed, and is
            // callback reference removed, so it won't persist
            this.callback = false;
            //Y.one('#tree_img').setAttribute('src','');
        }

        dialog.onOK = function (e) {
            e.preventDefault();
            this.hide();
            // code that executes the user confirmed action goes here
            if(this.callback){
                this.callback();
            }  
            //  callback reference removed, so it won't persist
            this.callback = false;
            //Y.one('#tree_img').setAttribute('src','');
        }
        
        //var iframeheight = document.getElementById('page_regex_auth_helper_iframe_holder').contentWindow.document.body.scrollHeight;
        //Y.one('#page_regex_auth_helper_iframe_holder').setStyle ( 'height',  iframeheight+'' );
        
        //Y.one('#dialog .message').load('http://localhost/moodle/question/type/preg/ast_preg_form.php?regex='+this.get("value"));
        
        Y.one('#dialog .message').load(page_regex_auth_helper_adr,function(){
            Y.Get.js('http://localhost/moodle/question/type/preg/authors_tool/preg_authors_tool_script.js', function (err) {
                if (err) {
                    alert('Error loading JS: ' + err[0].error, 'error');
                    return;
                }
            })
        })
    }

    var i=0;
    var node = Y.one('#id_regextest_'+i);
    var context = Y.one('#id_answer_'+i);
    while(node!=null){
        node = Y.one('#id_regextest_'+i);
        context = Y.one('#id_answer_'+i);
        if(node!=null){
            node.on("click", testfoo, context);
        }
        ++i;
    }
    
});
