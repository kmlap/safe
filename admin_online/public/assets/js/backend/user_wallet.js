define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user_wallet/index' + location.search,
                    add_url: 'user_wallet/add',
                    edit_url: 'user_wallet/edit',
                    multi_url: 'user_wallet/multi',
                    import_url: 'user_wallet/import',
                    table: 'user_wallet',
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
                        {field: 'coin.coin', title: __('Wallet_coin_id'), operate: 'LIKE'},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'balance', title: __('Balance'), operate:'BETWEEN'},
                        {field: 'pledge_balance', title: __('Pledge_balance'), operate:'BETWEEN'},
                        {field: 'total_balance', title: __('Total_balance'), operate:'BETWEEN'},
                        // {field: 'is_convert', title: __('Is_convert')},
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
