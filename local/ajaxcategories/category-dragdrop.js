YUI().use('sortable', function(Y){
    var list1 = new Y.Sortable({
            container: '#list1',
        nodes: 'li:not(.empty)',
            opacity: '.1'
    });

    var list2 = new Y.Sortable({
            container: '#list2',
            nodes: 'li',
            opacity: '.1'
    });

    list1.join(list2);

    list1.drop.on('drop:enter', function(){
        Y.log('drop:enter');
        Y.one('#list1 .empty').setStyle('display', 'none');
    });

    list1.drop.after('drop:exit', function () {
        Y.log('drop:exit');

        Y.log('number of items in #list1: ' + Y.all('#list1 li:not(.empty)').size());
        Y.log(Y.all('#list1 li:not(.empty)'));
        if(Y.all('#list1 li:not(.empty)').size() <= 0){
            Y.one('#list1 .empty').setStyle('display',null);
        }

    });

    list1.delegate.dd.on('drag:start', function () {
        Y.log('drag:start');
        Y.log('number of items in #list1: ' + Y.all('#list1 li:not(.empty)').size());
        if(Y.all('#list1 li:not(.empty)').size() <= 0){
            Y.one('#list1 .empty').setStyle('display',null);
        }
    });
});
