define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'pledge_activity/index' + location.search,
                    add_url: 'pledge_activity/add',
                    edit_url: 'pledge_activity/edit',
                    del_url: 'pledge_activity/del',
                    multi_url: 'pledge_activity/multi',
                    import_url: 'pledge_activity/import',
                    table: 'pledge_activity',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name_cn', title: __('名称'), operate: 'LIKE'},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'cycle_val', title: __('Cycle_val')},
                        {field: 'cycle_unit', title: __('Cycle_unit'), searchList: {"1":__('Cycle_unit 1'),"2":__('Cycle_unit 2')}, formatter: Table.api.formatter.normal},
                        {field: 'day_rate', title: __('Day_rate'), operate:'BETWEEN'},
                        {field: 'buy_num', title: __('Buy_num'), operate:'BETWEEN'},
                        {field: 'low_amount', title: __('Low_amount'), operate:'BETWEEN'},
                        {field: 'high_amount', title: __('High_amount'), operate:'BETWEEN'},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"0":__('Status 0')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
