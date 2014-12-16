/**
 * Script for button "Check", "Back" and push in interactive tree
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
YUI().use('node', 'io-base',function (Y) {

    var node = Y.one('#id_regex_check');
    var context = Y.one('#id_regex_text');
    
    var back = Y.one('#id_regex_back');
    var hidden = Y.one('#hidden_id');

    var back_regex = function( e ) {
        
        e.preventDefault();
       
        var new_regex = Y.one(context).get('value');
        current_line_edit.set('value',new_regex);
        dialog.hide();
        
        //TODO: call OK button
        //dialog.onOK();

    }

    function highlight_description(id){
        
        const highlighted_class = 'description_highlighted';
        var old_highlighted = Y.one('.'+highlighted_class);
        
        if(old_highlighted!=null){
           old_highlighted.removeClass(highlighted_class).setStyle('background-color','transparent');
        }
        
        Y.one('.description_node_'+id).addClass(highlighted_class).setStyle('background-color','yellow');
    }
    
    var check_regex = function( e ) {
        
        e.preventDefault();
        
        load_content(preg_www_root + '/question/type/preg/authors_tool/preg_authors_tool_load.php?regex='+encodeURIComponent(Y.one('#id_regex_text').get('value'))+'&id=-1');
    }    
    
    check_tree = function( e ) {

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
    
    if(node!=null){
       node.on("click", check_regex, context);
    }
    
    if(back!=null){
       back.on("click", back_regex, hidden);
    }
    
    Y.all("#_anonymous_0 > area").on('click', check_tree);
});
