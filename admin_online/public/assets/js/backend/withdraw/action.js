define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'withdraw/action/index' + location.search,
                    add_url: 'withdraw/action/add',
                    edit_url: 'withdraw/action/edit',
                    del_url: 'withdraw/action/del',
                    multi_url: 'withdraw/action/multi',
                    import_url: 'withdraw/action/import',
                    table: 'withdraw_action',
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
                        {field: 'aid', title: __('Aid')},
                        {field: 'from', title: __('From'), operate: 'LIKE'},
                        {field: 'to', title: __('To'), operate: 'LIKE'},
                        {field: 'type', title: __('Type'), operate: 'LIKE'},
                        {field: 'balance', title: __('提币金额'), operate: 'LIKE'},
                        {field: 'time', title: __('时间'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
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
