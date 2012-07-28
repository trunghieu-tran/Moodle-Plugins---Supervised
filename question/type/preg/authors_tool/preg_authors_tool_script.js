/**
 * 
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
YUI().use('node', function (Y) {

    function highlight_description(id){
        const highlighted_class = 'description_highlighted';
        var old_highlighted = Y.one('.'+highlighted_class);
        if(old_highlighted!=null){
            old_highlighted.removeClass(highlighted_class).setStyle('background-color','transparent');
        }
        Y.one('.description_node_'+id).addClass(highlighted_class).setStyle('background-color','yellow');
    }
    
    Y.all("#_anonymous_0 > area").on('click',function( e ) {
        //document.write('sadsadsa');
        id = e.currentTarget.getAttribute ( 'id' );
        highlight_description(id);
        // тут надо вызвать php скрипт чтобы сгенерились новые картинки
        Y.one('#id_of_tree').setAttribute('src','').setAttribute('src','new_url');
        Y.one('#id_of_graph').setAttribute('src','').setAttribute('src','new_url');
    });
});
