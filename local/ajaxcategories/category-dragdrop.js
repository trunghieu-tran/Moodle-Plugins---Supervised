function dump(obj) {
    var out = "";
    if(obj && typeof(obj) == "object"){
        for (var i in obj) {
            out += i + ": " + obj[i] + "\n";
        }
    } else {
        out = obj;
    }
    alert(out);
}
YUI().use( 'dd' , 'sortable', 'gallery-treeviewlite', function(Y) {

 var list1 = new Y.Sortable({
        container: '#ajaxcategorylist',
        nodes: 'li',
        opacity: '.5',

    });

    Y.one( "#ajaxcategorylist" ).plug( Y.Plugin.TreeviewLite );
    // nodes we're moving around
    var addedNode, newNode;

var recNode;

    // make a new node that can be inserted as a new tree sometimes
    Y.DD.DDM.on("drag:start", function(ev){

            newNode = Y.Node.create( "<ul></ul>" );
            newNode.setAttribute("id", Y.guid() );
            recNode = Y.Node.create( '<li class="placeholder"></li>');
            recNode.setAttribute("id", Y.guid() );
    });


    // insert the nodes where needed
    Y.DD.DDM.on("drag:over", function(ev){

                  // remove it from where it was
                  if( addedNode !== undefined ) {
                      addedNode.remove();
                  }


        var t = ev.drop.get("node"),
            // tOl is looking for a child ol below the li
            tOl = t.one( "ul" );



        // if we've over an li, add the new ol child block
        switch( t.get("nodeName").toLowerCase() ) {

           case "li":

                // try and append it to existing ol on the target
                if( tOl ) {
                  try {
                    //addedNode = placeholder;
                    tOl.append( ev.drag.get("node") );
                  } catch(e){ }
                }

                // else add a new ol to the target
                else {

                  // try adding newNode
                  try{
                    t.append( newNode );
                    newNode.append( ev.drag.get( "node" ) );

                    addedNode = newNode;


                  } catch(e){ }
                }
                break;


        // if we're over an ul, just add this as a new li child
         /* case "ul":
              try{
                t.prepend( ev.drag.get("node" ) );
                dump(t);
              }
              catch(e){}
              break;*/

          default:
              break;

        }


    });
});