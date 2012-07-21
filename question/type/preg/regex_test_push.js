/**
 * Script for button "Test regex" from edit_ast_preg_form.php
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
YUI().use('node', 'panel', 'node-load', function (Y) {
    
    var testfoo = function( e ) {
        
        e.preventDefault();
        
        var dialog = new Y.Panel({
            contentBox : Y.Node.create('<div id="dialog" />'),
            bodyContent: '<div class="message icon-warn">'+this.get("value")+'</div>',
            width      : 1000,
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
        }
        
        Y.one('#dialog .message').load('http://localhost/moodle/question/type/preg/ast_preg_form.php?regex='+this.get("value"));
    }

    var i=0;
    var node = Y.one('#id_regextest_'+i);
    var context = Y.one('#id_answer_'+i);
    while(node!=null){
        node = Y.one('#id_regextest_'+i);
        context = Y.one('#id_answer_'+i);
        node.on("click", testfoo, context);
        ++i;
    }
    
});

