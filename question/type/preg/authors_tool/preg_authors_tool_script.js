/**
 * 
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
YUI().use('node', 'io-base',function (Y) {

    //Y.one('body').setStyle('width','1000px').setStyle('max-width','1000px').setStyle('overflow-x','hidden');
    //Y.one('#tree_handler').setStyle('overflow','auto');
    //Y.one('#graph_handler').setStyle('overflow','auto');


    //TODO: test this function
    var back_regex = function( e ) {
        
       e.preventDefault();
       
       var tmp = encodeURIComponent(context.get("value"));
       //alert(tmp);
       id_edit = Y.one('#hidden_id').get("value");
       //window.parent.document.getElementsById(id_edit).value = tmp;
       alert(id_edit);
       Y.one(window.parent.getElemetById(id_edit).set('value',tmp));
       
       dialog.onOK();

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
       
       //alert(encodeURIComponent(this.get("value")));
       var tmp = encodeURIComponent(this.get("value"));
       //alert(tmp);
       //document.write(this.get("value"));
        
       //Y.one('html').load('http://localhost/moodle/question/type/preg/authors_loot/ast_preg_form.php?regex='+tmp+'&id=-1');
       
       var url = 'http://localhost/moodle/question/type/preg/authors_tool/ast_preg_form.php?regex='+tmp+'&id=-1';
       Y.one('#id_tree').setAttribute('src','');
       Y.one('#id_graph').setAttribute('src','');
       window.location.assign(url);

       /*var cfg = {
           method: 'POST',
       };
       Y.io(uri, cfg);*/

    }
    
    var node = Y.one('#id_regex_check');
    var context = Y.one('#id_regex_text');
    if(node!=null){
       node.on("click", check_regex, context);
    }
    
    var back = Y.one('#id_regex_back');
    var hidden = Y.one('#hidden_id');
    if(back!=null){
       back.on("click", back_regex, hidden);
    }
    
    Y.all("#_anonymous_0 > area").on('click',function( e ) {
       //document.write('sadsadsa');
       id = e.currentTarget.getAttribute ( 'id' );
       highlight_description(id);
       // тут надо вызвать php скрипт чтобы сгенерились новые картинки
       //Y.one('#id_of_tree').setAttribute('src','').setAttribute('src','new_url');
       //Y.one('#id_of_graph').setAttribute('src','').setAttribute('src','new_url');
        
       var tmp = encodeURIComponent(context.get("value"));
       //alert(id);
       
       var url = 'http://localhost/moodle/question/type/preg/authors_tool/ast_preg_form.php?regex='+tmp+'&id='+id;
       Y.io(url);
       Y.one('#id_graph').setAttribute('src','');
       setTimeout(function() { 
           Y.one('#id_graph').setAttribute('src','http://localhost/moodle/question/type/preg/tmp_img/graph.png'); 
           }, 500);
       //Y.one('#id_graph').setAttribute('src','').setAttribute('src','http://localhost/moodle/question/type/preg/tmp_img/graph.png');
    });
});
