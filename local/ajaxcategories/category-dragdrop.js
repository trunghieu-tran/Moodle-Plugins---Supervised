/**
 * Form string with parametrs for GET-request
 *
 * @param object with parametrs
 * @return string with parametrs for GET-request
*/
function get_params(options) {
    var string = '';
    for (var key in options) {
        string += key + '=' + options[key] + '&';
    }
    string = string.slice(0, -1);

    //alert(string);
    return string;
}

YUI().use('dd-constrain', 'dd-proxy', 'dd-drop', 'dd-plugin','io-base', function(Y) {
    var addednode;
    var child;
    var after;
    var change = true;
    var contextid;
    var beforeitemid;
    var afteritemid;
    var level;
    var movingid;
    var addednodewasadd = false;
    var ancestor;
    var cloneancestor;
    var options = {};
    var childadded;

    /**
     * Make ajax request.
     *
     * @param string with GET-request
    */
    function ajax_request(uri) {
        // Make the request
        Y.io(uri, {
            on: {
                success: function (x, o) {
                }
            }
        });
    }

    //Listen for all drop:over events
    Y.DD.DDM.on('drag:over', function(e) {
        var tar;
        var tar1;
        var item;
        //Get a reference to our drag and drop nodes
        var drag = e.drag.get('node');
        var drop = e.drop.get('node');

        // Get parent node of drop node.
        var parent = drop.ancestor();
        // Remove old nodes created for previous drag-and-drop.
        if (child !== undefined && child !== null && !change) {
            child.remove();
        }
        if (addednodewasadd && !childadded) {
            addednode.remove();
        }
        change = false;
        childadded = false;
        // Get contextid of dropped list.
        contextid = drop.getAttribute('data-id');
        if (contextid === null ||  contextid == '') {
            parent = drop.ancestor('ul[data-id]');
            if (parent !== null) {
                contextid = parent.getAttribute('data-id');
            } else {
                contextid = 999999;
            }
        } else {
            contextid = drop.getAttribute('data-id');
        }

        // Create node of placeholders.
        child = Y.Node.create( '<div id = "placeholder"></div>' );
        // Make node be droppable.
        tar = new Y.DD.Drop({
            node: child
        });
        // Add category as a child.
        if (drop.get('tagName').toLowerCase() === 'li') {
            // No nested list.
            if (drop.one("ul") == undefined) {
                addednode = Y.Node.create( '<ul><div id = "placeholder"></div></ul>' );
                addednode.setAttribute("id", Y.guid() );
                tar1 = new Y.DD.Drop({
                    node: addednode.one('#placeholder')
                });
                // Add dragged node to list.
                addednode.append(child);
                addednode.append(drag);
                addednode.append(child);
                drop.append(addednode);
                // Get id of parent node.
                item = drop.one('.ajaxitem[data-id]');
                beforeitemid = -1;
                afteritemid = item.getAttribute('data-id');
                level = 'inner';
                addednodewasadd = true;
            } else {
                // Add to nested list created before/
                addednodewasadd = false;
                addednode = drop.one("ul");
                addednode.append(drag);
                // Get id of parent node.
                if ((addednode.get('children').size() - 3) >= 0)
                {
                    item = addednode.get('children').item(addednode.get('children').size() - 3);
                    item = item.one('.ajaxitem[data-id]');
                    if (item) {
                        beforeitemid = -1;
                        afteritemid = item.getAttribute('data-id');
                        level = 'normal';
                    }
                }
                addednode.append(child);
            }
        } else {
            // Add node at the same level with other nodes in list/
            if (drop.one('#placeholder') !== undefined) {
                beforeitemid = -1;
                afteritemid = -1;
                item = drop.get('nextSibling');
                // Get id of item which should be undo dragged node.
                if (item !== undefined && item !== null) {
                    if (item.get('nextSibling')) {
                        if (item.get('nextSibling').get('tagName').toLowerCase() === 'li') {
                            item = item.get('nextSibling');
                        }
                    }
                    if (item !== null) {
                        item = item.one('.ajaxitem[data-id]');
                        if (item !== undefined && item !== null) {
                            beforeitemid = item.getAttribute('data-id');
                            contextid = item.ancestor('ul').getAttribute('data-id');
                            level = 'normal';
                        }
                    }
                }
                // Get id of item which should be above dragged node.
                item = drop.get('previousSibling');
                if (item !== undefined && item !== null) {
                    item = item.one('.ajaxitem[data-id]');
                    if (item !== undefined && item !== null) {
                        beforeitemid = -1;
                        afteritemid = item.getAttribute('data-id');
                        level = 'inner';
                    }
                }
                // Add dragged node to list.
                drop.insert(child, 'after');
                drop.insert(drag, 'after');
                level = 'normal';
            }
        }
    });

    //Listen for all drag:start events
    Y.DD.DDM.on('drag:start', function(e) {
        //Get our drag object
        var drag = e.target;
        var dragnode = drag.get('node');
        // Get copy of parent.
        cloneancestor = dragnode.get('parentNode').cloneNode(true);
        ancestor = dragnode.get('parentNode');
        //Set new style
        drag.get('node').setStyle('opacity', '.25');
        // Remove bottom placeholder.
        var next = drag.get('node').get('nextSibling');
        if (next !== null) {
            next.remove();
        }
        //Set new style
        drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
        drag.get('dragNode').setStyles({
            opacity: '.5',
            borderColor: drag.get('node').getStyle('borderColor'),
            backgroundColor: drag.get('node').getStyle('backgroundColor')
        });
        var item = drag.get('node').one('.ajaxitem[data-id]');
        // Get id of dragged category.
        movingid = item.getAttribute('data-id');
        // Remove dragged node from old place.
        drag.get('node').remove();
    });

    //Listen for a drag:end events
    Y.DD.DDM.on('drag:end', function(e) {
        var drag = e.target;
        var item = drag.get('node').one("ul");
        // Get ul with context id.
        while (item !== undefined && item !== null) {
            item.setAttribute('data-id', contextid);
            item = item.one("ul[data-id]");
        }
        //Put styles back
        drag.get('node').setStyles({
            visibility: '',
            opacity: '1'
        });
        change = true;
        if (level == 'inner') {
            childadded = true;
        }
        // Check count of top categories in each context.
        // Get top items.
        var topitems = Y.Node.all('ul[data-id]');
        topitems.each(function(value, key) {
            var parentnode = value.get('parentNode');
            if (!parentnode.hasClass('ajaxitem')) {
                var childrennodes = parentnode.one('ul').get("children");
                var childrencount = 0;
                // Count children of node.
                childrennodes.each(function(child, key) {
                    if (child.get('tagName').toLowerCase() === 'li') {
                        childrencount++;
                    }
                });
                // In case if top categories in context more than one add drag-handle.
                if (childrencount > 1) {
                    childrennodes.each(function(child, key) {
                        if (child.get('tagName').toLowerCase() === 'li' && child.one('#ajaxitem') !== null && !child.one('#ajaxitem').get("children").item(0).hasClass('drag-handle')) {
                            child.one('#ajaxitem').prepend(draghandle.cloneNode(true));
                        }
                    });
                } else {
                    // In case of single top category remove drag-handle.
                    childrennodes.each(function(child, key) {
                        if (child.get('tagName').toLowerCase() === 'li' && child.one('#ajaxitem') !== null && child.one('#ajaxitem').get("children").item(0).hasClass('drag-handle')) {
                            child.one('#ajaxitem').get("children").item(0).remove();
                        }
                    });
                }
            }
        });
        // If dropped place is invalid, return category to start position.
        if (beforeitemid == -1 && afteritemid == -1 || beforeitemid === undefined || afteritemid == undefined) {
            ancestor.replace(cloneancestor);
        } else {
            // Fill options.
            options['movingid'] = movingid;
            options['before'] = beforeitemid.toString();
            options['after'] = afteritemid.toString();
            options['level'] = level;
            options['dest'] = contextid;
            // Get uri for GET-request.
            get_params(options);
            var uri = "changer.php" + location.search + '&';
            uri += get_params(options);
            uri = M.cfg.wwwroot + '/local/ajaxcategories/' + uri;
            console.log(uri);
            // Make ajax request.
            ajax_request(uri);
        }
    });

    //Static Vars
    var goingUp = false, lastY = 0;
    // Draggable nodes
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

    // Droppable nodes.
    var uls = Y.Node.all('#placeholder');
        uls.each(function(v, k) {
            var tar = new Y.DD.Drop({
                node: v
            });
        });
    });

    var draghandle = Y.Node.one('.drag-handle').cloneNode(true);
