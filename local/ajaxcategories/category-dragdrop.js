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
    var block;
    var change = true;
    var blockX;
    var contextid;
    var beforeitemid;
    var afteritemid;
    var level;
    var movingid;
  //Listen for all drop:over events
    Y.DD.DDM.on('drag:over', function(e) {
        //Get a reference to our drag and drop nodes
        var drag = e.drag.get('node'),
            drop = e.drop.get('node');
        var parent = drop.ancestor();
        var first = parent.one('li');
        if (child !== undefined) {
            child.remove();
        }
        contextid = drop.getAttribute('data-id');
        if (contextid  === null ||  contextid == '') {
            //alert('aaaaa');
            parent = drop.ancestor('li[data-id]');
            contextid = parent.getAttribute('data-id');
        } else {
            contextid = drop.getAttribute('data-id');
        }
        //alert(contextid);
        child = Y.Node.create( '<div id = "placeholder"></div>' );
        if (drop.get('tagName').toLowerCase() === 'li') {
            if (drop.one("ul") == undefined) {
                addedNode = Y.Node.create( "<ul></ul>" );
                addedNode.setAttribute("id", Y.guid() );
                addedNode.append(child);
                addedNode.append(drag);
                addedNode.append(child);
                drop.append(addedNode);
                var item = drop.one('.ajaxitem[data-id]');
                beforeitemid = -1;
                afteritemid = item.getAttribute('data-id');
                level = 'inner';
            } else {
                addedNode = drop.one("ul");
                addedNode.append(drag);
                if ((addedNode.get('children').size()-3) >= 0)
                {
                    var item = addedNode.get('children').item(addedNode.get('children').size()-3);
                    beforeitemid = -1;
                    afteritemid = item.one('.ajaxitem[data-id]').getAttribute('data-id');
                    level = 'normal';
                }

                addedNode.append(child);
            }
        } else {
            if (drop.one('#placeholder') !== undefined) {
                beforeitemid = -1;
                afteritemid = -1;
                var item = drop.get('nextSibling');
                if (item !== undefined) {
                    item = item.one('.ajaxitem[data-id]');
                    if (item !== undefined && item !== null) {
                        beforeitemid = item.getAttribute('data-id');
                    }
                }
                item = drop.get('previousSibling');
                if (item !== undefined) {
                    item = item.one('.ajaxitem[data-id]');
                    if (item !== undefined && item !== null) {
                        afteritemid = item.getAttribute('data-id');
                    }
                }
                drop.insert(child, 'after');
                drop.insert(drag, 'after');
                level = 'normal';
            }
        }

        //alert(drop.getAttribute('data-id'));

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
        //var y = e.target.lastXY[0];
        //is it greater than the lastY var?
        /*if (y < lastY) {
            //We are going up
            goingUp = true;
        } else {
            //We are going down.
            goingUp = false;
        }*/
        //Cache for next check
        //lastY = y;

        if ((blockX - e.pageX) > 10 || (blockX - e.pageX) < -10) {
            //alert('sdssdsd');
            change = true;
        }
    });
    //Listen for all drag:start events
    Y.DD.DDM.on('drag:start', function(e) {
        //Get our drag object
        var drag = e.target;
        //Set some styles here
        drag.get('node').setStyle('opacity', '.25');
        //drag.get('node').remove();
        var next = drag.get('node').get('nextSibling');
        next.remove();
        drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
        drag.get('dragNode').setStyles({
            opacity: '.5',
            borderColor: drag.get('node').getStyle('borderColor'),
            backgroundColor: drag.get('node').getStyle('backgroundColor')
        });
        var item = drag.get('node').one('.ajaxitem[data-id]');
        movingid = item.getAttribute('data-id');
    });
    //Listen for a drag:end events
    Y.DD.DDM.on('drag:end', function(e) {
        var drag = e.target;
        //Put our styles back
        drag.get('node').setStyles({
            visibility: '',
            opacity: '1'
        });
        alert(afteritemid);
        alert(contextid);
    });


    //Static Vars
    var goingUp = false, lastY = 0;
var items = Y.Node.all('#ajaxcategorylist li');
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
        constrain2node: '#main'
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