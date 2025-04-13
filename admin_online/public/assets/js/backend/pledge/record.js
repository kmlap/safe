define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'pledge/record/index' + location.search,
                    add_url: 'pledge/record/add',
                    edit_url: 'pledge/record/edit',
                    del_url: 'pledge/record/del',
                    multi_url: 'pledge/record/multi',
                    import_url: 'pledge/record/import',
                    table: 'pledge_record',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'uid', title: __('Uid')},
                        {field: 'end', title: __('End'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'pledgeprice', title: __('Pledgeprice'), operate:'BETWEEN'},
                        {field: 'profit', title: '盈利', operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('状态'),searchList: {0:'未付款',1:'已付款', 2:'付款失败',3:'质押完成'},formatter: Table.api.formatter.normal},
                        {field: 'symbol', title: __('Symbol'), operate: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.off('dbl-click-row.bs.table');
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
