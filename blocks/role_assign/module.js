M.block_role_assign = M.block_role_assign || {};
M.block_role_assign.del_rule = function(Y,rowid,ruleid,courseid,del,message) {

    var button = Y.one("#cur_cell" + rowid);
    button.on("click", function (e) {
        if(confirm(message)){
            var params = {
                id : ruleid,
                courseid : courseid,
                del: del
            };
            Y.io(M.cfg.wwwroot + '/blocks/role_assign/delete_rule.php', {
                method: 'POST',
                data: build_querystring(params),
                on: {
                    success: function (id, result) {
                        var row = Y.one("#cur_table" + rowid);
                        row.setStyle('display', 'none');
                    }
                }
            });
        }
    });
}

M.block_role_assign.check_role = function(Y,element,courseid,userid) {
    var handle = Y.later( 1000 * 2, window, function(){
        var params = {
            courseid : courseid,
            userid : userid
        };
        Y.io(M.cfg.wwwroot + '/blocks/role_assign/check_role.php', {
            method: 'GET',
            data: build_querystring(params),
            on: {
                success: function (id, result) {
                    var elem = Y.one("#" + element);
                    elem.set("innerHTML", result.responseText);
                }
            }
        });
        M.block_role_assign.check_role(Y,element,courseid,userid);
    }, [],false);
}
