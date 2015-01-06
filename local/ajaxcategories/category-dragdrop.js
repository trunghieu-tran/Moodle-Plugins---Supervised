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

var options = {};

function get_params(options) {
    var string = '';
    for (var key in options) {
        string += key + '=' + options[key] + '&';
    }
    string = string.slice(0, -1);

    //alert(string);
    return string;
}

/*YUI().use("io-base", function(Y) {
    var uri = "./changer.php?";
    uri += get_params(options);
    // Define a function to handle the response data.
    function complete(id, o, args) {
        var id = id; // Transaction ID.
        var data = o.responseText; // Response data.
        var args = args[1]; // 'ipsum'.
    };

    // Subscribe to event "io:complete", and pass an array
    // as an argument to the event handler "complete", since
    // "complete" is global.   At this point in the transaction
    // lifecycle, success or failure is not yet known.
    Y.on('io:complete', complete, Y, ['lorem', 'ipsum']);

    // Make an HTTP request to 'get.php'.
    // NOTE: This transaction does not use a configuration object.
    var request = Y.io(uri);
    alert(request);
});*/

YUI().use('dd-constrain', 'dd-proxy', 'dd-drop', 'dd-plugin','io-base', function(Y) {
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
    var addedNodewasadd = false;
    var ancestor;
    var cloneancestor;

function ajaxRequest(uri) {

    // Make the request. It has to be on your domain, not cross domain.
    Y.io(uri, {
        on: {
            success: function (x, o) {
                console.log("!!!!!!!!!!!!!!!!!");

            }
        }
    });
}
  //Listen for all drop:over events
    Y.DD.DDM.on('drag:over', function(e) {
        //Get a reference to our drag and drop nodes
        var drag = e.drag.get('node'),
            drop = e.drop.get('node');
        var parent = drop.ancestor();
        if (child !== undefined && child !== null && !change) {
            child.remove();
        }
        if (addedNodewasadd) {
            addedNode.remove();
        }
        change = false;
        contextid = drop.getAttribute('data-id');
        if (contextid  === null ||  contextid == '') {
            //alert('aaaaa');
            parent = drop.ancestor('ul[data-id]');
            if (parent !== null) {
                contextid = parent.getAttribute('data-id');
            } else {
                contextid = 999999;
            }
        } else {
            contextid = drop.getAttribute('data-id');
        }
        //alert(contextid);
        child = Y.Node.create( '<div id = "placeholder"></div>' );
        var tar = new Y.DD.Drop({
            node: child
        });
        if (drop.get('tagName').toLowerCase() === 'li') {
            if (drop.one("ul") == undefined) {
                addedNode = Y.Node.create( '<ul><div id = "placeholder"></div></ul>' );
                addedNode.setAttribute("id", Y.guid() );
                var tar1 = new Y.DD.Drop({
            node: addedNode.one('#placeholder')
        });

                addedNode.append(child);
                addedNode.append(drag);
                addedNode.append(child);
                drop.append(addedNode);
                var item = drop.one('.ajaxitem[data-id]');
                beforeitemid = -1;
                afteritemid = item.getAttribute('data-id');
                level = 'inner';
                addedNodewasadd = true;
            } else {
                addedNodewasadd = false;
                addedNode = drop.one("ul");
                addedNode.append(drag);
                if ((addedNode.get('children').size()-3) >= 0)
                {
                    var item = addedNode.get('children').item(addedNode.get('children').size()-3);
                    item = item.one('.ajaxitem[data-id]');
                    if (item) {
                        beforeitemid = -1;
                        afteritemid = item.getAttribute('data-id');
                        level = 'inner';
                    }
                }
                addedNode.append(child);
            }
        } else {
            console.log('sssssss');
            if (drop.one('#placeholder') !== undefined) {
                beforeitemid = -1;
                afteritemid = -1;
                var item = drop.get('nextSibling');
                if (item !== undefined && item !== null) {
                    //if (item.one('.ajaxitem[data-id]') === undefined || item.one('.ajaxitem[data-id]') === null) {
                        console.log(item);
                        if (item.get('nextSibling')) {
                            if (item.get('nextSibling').get('tagName').toLowerCase() === 'li') {
                                item = item.get('nextSibling');
                            }
                        }

                        console.log(item);
                    //}
                    if (item !== null) {
                    item = item.one('.ajaxitem[data-id]');
                    if (item !== undefined && item !== null) {
                        beforeitemid = item.getAttribute('data-id');
                        contextid = item.ancestor('ul').getAttribute('data-id');
                        level = 'normal';

                    }
                }
                }
                item = drop.get('previousSibling');
                if (item !== undefined && item !== null) {
                    item = item.one('.ajaxitem[data-id]');
                    if (item !== undefined && item !== null) {
                        beforeitemid = -1;
                        afteritemid = item.getAttribute('data-id');
                        level = 'normal';
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

    //Listen for all drag:start events
    Y.DD.DDM.on('drag:start', function(e) {
        //Get our drag object
        var drag = e.target;
        var dragnode = drag.get('node');
        cloneancestor = dragnode.get('parentNode').cloneNode(true);
        console.log(cloneancestor);
        ancestor = dragnode.get('parentNode');
        //Set some styles here
        drag.get('node').setStyle('opacity', '.25');
        var next = drag.get('node').get('nextSibling');
        if (next !== null) {
            next.remove();
        }
        drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
        drag.get('dragNode').setStyles({
            opacity: '.5',
            borderColor: drag.get('node').getStyle('borderColor'),
            backgroundColor: drag.get('node').getStyle('backgroundColor')
        });
        var item = drag.get('node').one('.ajaxitem[data-id]');
        movingid = item.getAttribute('data-id');
        drag.get('node').remove();


    });


    //Listen for a drag:end events
    Y.DD.DDM.on('drag:end', function(e) {
        var drag = e.target;
        var item = drag.get('node').one("ul");
        while (item !== undefined && item !== null) {
            item.setAttribute('data-id', contextid);
            item = item.one("ul[data-id]");
        }
        //Put our styles back
        drag.get('node').setStyles({
            visibility: '',
            opacity: '1'
        });
        change = true;
        var topitems = Y.Node.all('ul[data-id]');
        topitems.each(function(value, key) {
            var parentnode = value.get('parentNode');
            if (!parentnode.hasClass('ajaxitem')) {
                var childrennodes = parentnode.one('ul').get("children");
                var childrencount = 0;
                childrennodes.each(function(child, key) {
                    if (child.get('tagName').toLowerCase() === 'li') {
                        childrencount++;
                    }
                });
                if (childrencount > 1) {
                    childrennodes.each(function(child, key) {
                        if (child.get('tagName').toLowerCase() === 'li' && child.one('#ajaxitem') !== null && !child.one('#ajaxitem').get("children").item(0).hasClass('drag-handle')) {
                            child.one('#ajaxitem').prepend(draghandle.cloneNode(true));
                        }
                    });
                } else {
                    childrennodes.each(function(child, key) {
                        if (child.get('tagName').toLowerCase() === 'li' && child.one('#ajaxitem') !== null && child.one('#ajaxitem').get("children").item(0).hasClass('drag-handle')) {
                            child.one('#ajaxitem').get("children").item(0).remove();
                        }
                    });
                }
            }
        });
if (beforeitemid == -1 && afteritemid == -1 || beforeitemid === undefined || afteritemid == undefined) {
    console.log(cloneancestor);
ancestor.replace(cloneancestor);

} else {
        options['movingid'] = movingid;
        options['before'] = beforeitemid.toString();
        options['after'] = afteritemid.toString();
        options['level'] = level;
        options['dest'] = contextid;
        get_params(options);
        var uri = "changer.php" + location.search + '&';
        uri += get_params(options);
        uri = M.cfg.wwwroot + '/local/ajaxcategories/' + uri;
        console.log(uri);
        ajaxRequest(uri);
    }
        //var request = new Y.io(uri);
        //request.send();
        //alert(request);
        //alert(movingid);
        //alert(beforeitemid);
        //alert(afteritemid);
        //alert(contextid);
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

var draghandle = Y.Node.one('.drag-handle').cloneNode(true);


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