// This file is part of ajaxcategories plugin - https://code.google.com/p/oasychev-moodle-plugins/
//
// Ajaxcategories plugin is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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
    var clonedrag;
    var lefthtml;
    var realhtml;

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

    function register_nested_lists() {
        items = clonedrag.all('#ajaxitem');

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
            uls = clonedrag.all('#placeholder');
                uls.each(function(v, k) {
                    var tar = new Y.DD.Drop({
                        node: v
                    });
                });


            draghandle = Y.Node.one('.drag-handle').cloneNode(true);
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
        var parent = drop.get('parentNode');

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
        if (parent !== null && parent.get('tagName').toLowerCase() === 'li') {
            drop = parent;
        }
        // Add category as a child.
        if (drop.get('tagName').toLowerCase() === 'li') {
            // No nested list.
            if (drop.one("ul") == undefined) {
                addednode = Y.Node.create( '<ul><div id = "placeholder"></div></ul>' );
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
            // Add node at the same level with other nodes in list.
            if (drop.get('id') === 'placeholder') {
                beforeitemid = -1;
                afteritemid = -1;
                item = drop.get('nextSibling');
                // Get id of item which should be undo dragged node.
                if (item !== undefined && item !== null) {
                    if (item.get('nextSibling')!== undefined && item.get('nextSibling') !== null) {
                        if (item.get('nextSibling').get('tagName') !== undefined && item.get('nextSibling').get('tagName').toLowerCase() === 'li') {
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
                if (drop.get('parentNode') !== null) {
                    drop.insert(child, 'after');
                    drop.insert(drag, 'after');
                }
                level = 'normal';
            }
        }
    });

    Y.DD.DDM.on('drag:mouseDown', function(e) {
        var drag = e.target;
        var wasname = false;
        var nestedlist = drag.get('node').get('parentNode').one('ul');
        if (nestedlist !== null && nestedlist !== undefined) {
            clonedrag = drag.get('node').get('parentNode').one('ul').cloneNode(true);
            drag.get('node').get('parentNode').one('ul').remove();
        }
        var html = drag.get('node').get('innerHTML');
        var index = html.indexOf('</b>');
        lefthtml = '</a></b>' + html.substring(index + 4);
        var childrennodes = drag.get('node').get('children');
        childrennodes.each(function(child, key) {
            if (wasname) {
                child.remove();
            }
            if (child.get('tagName').toLowerCase() === 'b') {
                wasname = true;
            }
        });
    });

    //Listen for all drag:start events
    Y.DD.DDM.on('drag:start', function(e) {
        //Get our drag object
        var drag = e.target;
        var dragnode = drag.get('node');
        var clone;
        beforeitemid = -1;
        afteritemid = -1;
        // Get copy of parent.
        cloneancestor = dragnode.get('parentNode').get('parentNode').get('parentNode').cloneNode(true);
        ancestor = dragnode.get('parentNode').get('parentNode').get('parentNode');
        var html = drag.get('node').get('innerHTML');
        //console.log(html);
        var index = html.indexOf('</b>');
        //lefthtml = '</a></b>' + html.substring(index + 4);
        html = html.substring(0, index - 4);
        html = '<li>' + html + '...' + '</a></b></li>';
        drag.get('node').set('innerHTML', html);
        //console.log(html);
        //Set new style
        drag.get('node').setStyle('opacity', '.25');
        // Remove bottom placeholder.
        var next = drag.get('node').get('parentNode').get('previousSibling');
        if (next !== null) {
            next.remove();
        }

        //console.log(drag.get('node').get('innerHTML'));
        //Set new style
        drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
        drag.get('dragNode').setStyles({
            opacity: '.5',
            borderColor: drag.get('node').getStyle('borderColor'),
            backgroundColor: drag.get('node').getStyle('backgroundColor'),
            height: 21
        });
        var item = drag.get('node');
        // Get id of dragged category.
        movingid = item.getAttribute('data-id');
        // Remove dragged node from old place.
        var oldparent = dragnode.get('parentNode').get('parentNode');
        drag.get('node').get('parentNode').remove();
        if (oldparent.get('children').size() == 1) {
            oldparent.remove();
        }
    });

    //Listen for a drag:end events
    Y.DD.DDM.on('drag:end', function(e) {
        console.log('aaaaaaaaaaa');
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

        // If dropped place is invalid, return category to start position.
        if (beforeitemid == -1 && afteritemid == -1 || beforeitemid === undefined || afteritemid === undefined) {
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
            //console.log(uri);
            // Make ajax request.
            ajax_request(uri);
        }
        /*if (clonedrag.hasClass('yui3-dd-drop yui3-dd-draggable yui3-dd-dragging')) {
            console.log('aaaaaaaaaaaaaa');
            clonedrag.removeClass('yui3-dd-dragging');
        }*/
        var html = drag.get('node').get('innerHTML');
        html = html.substring(4, html.lastIndexOf('...</a></b>'));
        //console.log(html);
        drag.get('node').set('innerHTML', html + lefthtml);
        drag.get('node').wrap('<li id = "ajaxlistitem"></li>');
        if (clonedrag !== null && clonedrag !== undefined) {
            drag.get('node').get('parentNode').append(clonedrag);
            register_nested_lists();
        }
        // Check count of top categories in each context.
        // Get top items.
        var topitems = Y.Node.all('ul[data-id]');
        topitems.each(function(value, key) {
            var parentnode = value.ancestor('div');
            if (parentnode !== null && parentnode !== undefined) {
                var childrennodes = parentnode.one('ul').get('children');
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
        clonedrag = null;
    });

    Y.DD.DDM.on('drag:mouseup', function(e) {
        var drag = e.target;
        var html = drag.get('node').get('innerHTML');
        html = html.substring(0,  html.indexOf('&nbsp;'));
        html += lefthtml;
        drag.get('node').set('innerHTML', html);
        if (clonedrag !== null && clonedrag !== undefined) {
            drag.get('node').get('parentNode').append(clonedrag);
            register_nested_lists();
        }
        clonedrag = null;
    });
    //Static Vars
    var goingUp = false, lastY = 0;
    // Draggable nodes
    var items = Y.Node.all('#ajaxitem');
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
