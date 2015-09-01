M.mod_poasassignment = {};

M.mod_poasassignment.init_tree = function(Y, expand_all, htmlid) {
    Y.use('yui2-treeview', function(Y) {
        var tree;
        if (typeof(YAHOO) != 'undefined')
            tree = new YAHOO.widget.TreeView(htmlid);
        else
            if (typeof(Y.YUI2) != 'undefined')
                tree = new Y.YUI2.widget.TreeView(htmlid);
        
        tree.subscribe("clickEvent", function(node, event) {
            // we want normal clicking which redirects to url
            return false;
        });

        if (expand_all) {
            tree.expandAll();
        }

        tree.render();
    });
};
