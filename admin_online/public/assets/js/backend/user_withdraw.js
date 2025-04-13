define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'editable'], function ($, undefined, Backend, Table, Form, undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user_withdraw/index' + location.search,
                    // add_url: 'user_withdraw/add',
                    // edit_url: 'user_withdraw/edit',
                    // del_url: 'user_withdraw/del',
                    // multi_url: 'user_withdraw/multi',
                    // import_url: 'user_withdraw/import',
                    table: 'user_withdraw',
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
                        {field: 'order_sn', title: __('Order_sn'), operate: 'LIKE'},
                        {field: 'uid', title: __('Uid')},
                        {field: 'coin', title: __('Coin'), operate: 'LIKE'},
                        {field: 'link', title: __('Link'), operate: 'LIKE'},
                        {field: 'bank_code', title: __('bank_code'), operate: 'LIKE'},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'commission', title: __('Commission'), operate:'BETWEEN'},
                        {field: 'real_amount', title: __('Real_amount'), operate:'BETWEEN'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'remark', title: __('Remark'), operate: 'LIKE'},
                        {field: 'withdraw_time', title: __('Withdraw_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'verify_time', title: __('Verify_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'status',
                                    text: '通过',
                                    title:'通过',
                                    confirm:"确定通过么?",
                                    icon: 'fa fa-check',
                                    classname: 'btn btn-info btn-xs btn-success btn-ajax',
                                    url:"user_withdraw/verify?status=1",
                                    success:function(data,ret){
                                        $(".btn-refresh").trigger("click");
                                    },
                                    visible:function(row){
                                        if(row.status == 0){
                                            return true;
                                        }
                                    },
                                },
                                {
                                    name: 'status',
                                    text: '拒绝',
                                    title:'拒绝',
                                    confirm:"确定拒绝么?",
                                    icon: 'fa fa-remove',
                                    classname: 'btn btn-danger btn-xs btn-success btn-ajax',
                                    url:"user_withdraw/verify?status=2",
                                    success:function(data,ret){
                                        $(".btn-refresh").trigger("click");
                                    },
                                    visible:function(row){
                                        if(row.status == 0){
                                            return true;
                                        }
                                    },
                                },
                            ],formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        // add: function () {
        //     Controller.api.bindevent();
        // },
        // edit: function () {
        //     Controller.api.bindevent();
        // },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
