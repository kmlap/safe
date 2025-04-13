define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'editable'], function ($, undefined, Backend, Table, Form, undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'contract_order/index' + location.search,
                    add_url: 'contract_order/add',
                    edit_url: 'contract_order/edit',
                    del_url: 'contract_order/del',
                    multi_url: 'contract_order/multi',
                    import_url: 'contract_order/import',
                    table: 'contract_order',
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
                        {field: 'config.coin', title: __('交易币种')},
                        {field: 'start_time', title: __('Start_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'increase', title: __('Increase'), operate:'BETWEEN'},
                        {field: 'odds', title: __('Odds'), operate:'BETWEEN'},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'amount_unit', title: __('Amount_unit'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"0":__('Status 0')}, formatter: Table.api.formatter.status},
                        {field: 'start_price', title: __('Start_price'), operate:'BETWEEN'},
                        {field: 'end_price', title: __('End_price'), operate:'BETWEEN'},
                        {field: 'lose_win', title: __('Lose_win'), searchList: {"0":__('Lose_win 0'),"1":__('Lose_win 1'),"2":__('Lose_win 2')}, formatter: Table.api.formatter.normal},
                        {field: 'profit_loss', title: __('Profit_loss'), operate:'BETWEEN'},
                        //{field: 'risk_mangement', title: __('Risk_mangement'), searchList: {"1":__('Risk_mangement 1'),"2":__('Risk_mangement 2'),"0":__('Risk_mangement 0')}, formatter: Table.api.formatter.normal},
                        {field: 'risk_mangement', title: __('Risk_mangement'),
                            editable: {
                                type: 'select',
                                pk: 1,
                                source: [
                                    {value: '0', text: '不控制'},
                                    {value: '1', text: '赢'},
                                    {value: '2', text: '输'},
                                ]
                            }
                        },
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
