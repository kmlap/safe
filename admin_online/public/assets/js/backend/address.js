define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'address/index' + location.search,
                    add_url: 'address/add',
                    edit_url: 'address/edit',
                    del_url: 'address/del',
                    multi_url: 'address/multi',
                    import_url: 'address/import',
                    table: 'address',
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
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'pri_key', title: "密钥", operate: false},
                        {field: 'type', title: __('Type'), operate: 'LIKE'},
                        {field: 'enabled', title: __('Enabled'),formatter: Table.api.formatter.label,searchList: {"0":'未激活',"1":"已激活"}},
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
