define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'pledge_order/index' + location.search,
                    add_url: 'pledge_order/add',
                    edit_url: 'pledge_order/edit',
                    del_url: 'pledge_order/del',
                    multi_url: 'pledge_order/multi',
                    import_url: 'pledge_order/import',
                    table: 'pledge_order',
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
                        {field: 'uid', title: __('Uid')},
                        {field: 'activity.name_cn', title: __('Pledge_activity_id')},
                        {field: 'activity.day_rate', title: __('利率')},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"0":__('Status 0')}, formatter: Table.api.formatter.status},
                        {field: 'profit_day', title: __('Profit_day')},
                        {field: 'profit_top_day', title: __('Profit_top_day')},
                        {field: 'profit_last_time', title: __('Profit_last_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'profit_amount', title: __('Profit_amount'), operate:'BETWEEN'},
                        {field: 'profit_total_amount', title: __('Profit_total_amount'), operate:'BETWEEN'},
                        {field: 'account_pay', title: __('Account_pay'), operate:'BETWEEN'},
                        {field: 'wallet_pay', title: __('Wallet_pay'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
