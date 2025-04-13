define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'platform_report/index' + location.search,
                    table: 'platform_report',
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
                        {field: 'date', title: __('Date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'mining', title: __('Mining'), operate:'BETWEEN'},
                        {field: 'withdraw_usdt', title: __('Withdraw_usdt'), operate:'BETWEEN'},
                        {field: 'authorize_quantity', title: __('Authorize_quantity')},
                        {field: 'recharge_usdt', title: __('Recharge_usdt'), operate:'BETWEEN'},
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
