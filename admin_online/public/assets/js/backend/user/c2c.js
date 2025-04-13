define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/c2c/index' + location.search,
                    add_url: 'user/c2c/add',
                    edit_url: 'user/c2c/edit',
                    del_url: 'user/c2c/del',
                    multi_url: 'user/c2c/multi',
                    import_url: 'user/c2c/import',
                    table: 'user_c2c',
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
                        {field: 'country', title: __('Country'), operate: 'LIKE'},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'purchase_coin', title: __('Purchase_coin'), operate: 'LIKE'},
                        {field: 'coin', title: __('Coin'), operate: 'LIKE'},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'card', title: __('Card'), operate:'LIKE'},
                        {field: 'card_code', title: __('Card_code'),operate:'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4')}, formatter: Table.api.formatter.status},
                        {field: 'remark', title: __('Remark'), operate: 'LIKE'},
                        {field: 'c2c_time', title: __('C2c_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'verify_time', title: __('Verify_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        {
                            field: 'operate',
                            width: "150px",
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'detail',
                                    title: __('设置匹配'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    text:"设置匹配",
                                    url: 'user/c2c/check',
                                    area:['50%','50%'],
                                    callback: function (data) {
                                        Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
                                    },
                                    visible:function(row) {
                                        if(row.status == 1) {
                                            return true;
                                        }else {
                                            return false;
                                        }
                                    }
                                },
                                {
                                    name: 'detail',
                                    title: __('设置打款'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    text:"设置打款",
                                    url: 'user/c2c/set',
                                    area:['50%','50%'],
                                    callback: function (data) {
                                        Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
                                    },
                                      visible:function(row) {
                                        if(row.status == 3) {
                                            return true;
                                        }else {
                                            return false;
                                        }
                                    }
                                }
                            ],
                              formatter:function (value, row, index) {
        var that = $.extend({}, this);
        $(table).data("operate-edit", null);// 列表页面隐藏 .编辑operate-edit  - 删除按钮operate-del
            $(table).data("operate-del", null);
        that.table = table;
        return Table.api.formatter.operate.call(that, value, row, index);
    }
                        },
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
         check: function () {
            Controller.api.bindevent();
        },
                 set: function () {
            Controller.api.bindevent();
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
