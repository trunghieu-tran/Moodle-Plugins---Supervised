/**
 * Script for button "Test regex" from edit_ast_preg_form.php
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
YUI().use('node', 'panel', 'node-load', 'get', "io-xdr", "substitute", "json-parse", function(Y) {

    load_content = function(url) {

        var upd_dialog_Success = function(id, o, a) {
            //alert(o.responseText);
            var json_array = Y.JSON.parse(o.responseText);

            //TODO: add errors message
            if(typeof json_array['tree_src'] != 'undefined') {
                Y.one('#id_tree').setAttribute("src", '').setAttribute("src", json_array['tree_src']);
            }
            if(typeof json_array['map'] != 'undefined') {
                Y.one('#tree_map').setHTML(json_array['map']);
                Y.all("#_anonymous_0 > area").on('click', check_tree);
            }
            if(typeof json_array['graph_src'] != 'undefined') {
                Y.one('#id_graph').setAttribute("src", '').setAttribute("src", json_array['graph_src']);
            }
            if(typeof json_array['description'] != 'undefined') {
                Y.one('#description_handler').setHTML(json_array['description']);
            }
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

    var testfoo = function(e) {

        e.preventDefault();

        var page_regex_auth_helper_height = 1000;
        var page_regex_auth_helper_width = 1000;
        current_line_edit = this;
        //regex = encodeURIComponent(this.get('value'));
        var page_regex_auth_helper_adr = preg_www_root + '/question/type/preg/authors_tool/ast_preg_form.php?regex=' + encodeURIComponent(this.get('value')) + '&id=-1' + '&id_line_edit=' + this.getAttribute('id');
        if(typeof dialog == 'undefined') {
            dialog = new Y.Panel({
                contentBox: Y.Node.create('<div id="dialog" />'),
                bodyContent: '<div class="message icon-warn">Loading...</div>',
                width: page_regex_auth_helper_width,
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

            Y.one('#dialog .message').load(preg_www_root + '/question/type/preg/authors_tool/ast_preg_form.php?regex=' + encodeURIComponent(current_line_edit.get('value')) + '&id=-1', function() {
                Y.Get.js(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_script.js', function(err) {
                    if(err) {
                        alert('Error loading JS: ' + err[0].error, 'error');
                        return;
                    }
                })
                //TODO: set empty src in all field
                Y.one('#id_regex_text').set('value', current_line_edit.get('value'));
                Y.one('#id_tree').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
                Y.one('#id_graph').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
                load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex=' + encodeURIComponent(current_line_edit.get('value')) + '&id=-1');
            })

        } else {
            //TODO: set empty src in all field
            Y.one('#id_regex_text').set('value', current_line_edit.get('value'));
            Y.one('#id_tree').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
            Y.one('#id_graph').setAttribute("src", preg_www_root + '/question/type/preg/tmp_img/spacer.gif');
            load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex=' + encodeURIComponent(current_line_edit.get('value')) + '&id=-1');
            dialog.show();
        }
    }

    var i = 0;
    var node = Y.one('#id_regextest_' + i);
    var context = Y.one('#id_answer_' + i);
    while(node != null) {
        node = Y.one('#id_regextest_' + i);
        context = Y.one('#id_answer_' + i);
        if(node != null) {
            node.on("click", testfoo, context);
        }
        ++i;
    }

});
