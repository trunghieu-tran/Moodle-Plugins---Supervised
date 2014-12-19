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

YUI().use('dd-constrain', 'dd-proxy', 'dd-drop', 'dd-plugin', function(Y) {
    var before;
    var addedNode;
    var child;
    var after;
  //Listen for all drop:over events
    Y.DD.DDM.on('drop:over', function(e) {
        //Get a reference to our drag and drop nodes
        var drag = e.drag.get('node'),
            drop = e.drop.get('node');
        var parent = drop.ancestor();
        var first = parent.one('li');
        if (before !== undefined ) {
            before.remove();
        }
        if (addedNode !== undefined ) {
            addedNode.remove();
        }
        if (after !== undefined ) {
            after.remove();
        }
        // Check if node is first in list
        if (first.compareTo(drop)) {
            before = Y.Node.create( '<li class="placeholder"></li>');
            before.setAttribute("id", Y.guid() );
            e.drop.get('node').get('parentNode').insertBefore(before, drop);
        }
        // Check if node doesn't have children
        if (drop.one( "ul" ) == undefined) {
            child = Y.Node.create( '<li class="placeholder"></li>');
            child.setAttribute("id", Y.guid() );
            addedNode = Y.Node.create( "<ul></ul>" );
            addedNode.setAttribute("id", Y.guid() );
            addedNode.append(child);
            drop.append(addedNode);
        }
        after = Y.Node.create( '<li class="placeholder"></li>');
        after.setAttribute("id", Y.guid() );
        e.drop.get('node').get('parentNode').insert(before, drop);
        drop.insert(after, "after");
        //Are we dropping on a li node?
        /*if (drop.get('tagName').toLowerCase() === 'li') {
            //Are we not going up?
            if (!goingUp) {
                drop = drop.get('nextSibling');
            }
            //Add the node to this list
            e.drop.get('node').get('parentNode').insertBefore(drag, drop);
            //Resize this nodes shim, so we can drop on it later.
            e.drop.sizeShim();
        }*/
    });

    //Listen for all drag:drag events
    Y.DD.DDM.on('drag:drag', function(e) {
        //Get the last y point
        var y = e.target.lastXY[1];
        //is it greater than the lastY var?
        if (y < lastY) {
            //We are going up
            goingUp = true;
        } else {
            //We are going down.
            goingUp = false;
        }
        //Cache for next check
        lastY = y;
    });
    //Listen for all drag:start events
    Y.DD.DDM.on('drag:start', function(e) {
        //Get our drag object
        var drag = e.target;
        //Set some styles here
        drag.get('node').setStyle('opacity', '.25');
        drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
        drag.get('dragNode').setStyles({
            opacity: '.5',
            borderColor: drag.get('node').getStyle('borderColor'),
            backgroundColor: drag.get('node').getStyle('backgroundColor')
        });
    });
    //Listen for a drag:end events
    Y.DD.DDM.on('drag:end', function(e) {
        var drag = e.target;
        //Put our styles back
        drag.get('node').setStyles({
            visibility: '',
            opacity: '1'
        });
    });


    //Static Vars
    var goingUp = false, lastY = 0;
var items = Y.Node.all('#ajaxlistitem');
items.each(function(v, k) {


    var dd = new Y.DD.Drag({
        node: v,
        //Make it Drop target and pass this config to the Drop constructor
        target: {
            padding: '0 0 0 0'
        }
    }).plug(Y.Plugin.DDProxy, {
        //Don't move the node at the end of the drag
        moveOnEnd: false
    }).plug(Y.Plugin.DDConstrained, {
        //Keep it inside the #play node
        constrain2node: '#ajaxcategorylist'
    });
   dd.addHandle('.drag-handle');



});

var uls = Y.Node.all('#placeholder');
    uls.each(function(v, k) {
        var tar = new Y.DD.Drop({
            node: v
        });
    });
});
/*YUI().use( 'dd' , 'sortable', 'gallery-treeviewlite', function(Y) {

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

         /* default:
              break;

        }


    });
});*/