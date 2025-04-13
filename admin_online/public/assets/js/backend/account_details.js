define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'account_details/index' + location.search,
                    table: 'account_details',
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
                        {field: 'coin', title: __('Coin'), operate: 'LIKE'},
                        {field: 'type', title: __('Type'), searchList: {
                                "1":__('Type 1'),
                                "2":__('Type 2'),
                                "3":__('Type 3'),
                                "4":__('Type 4'),
                                "5":__('Type 5'),
                                "6":__('Type 6'),
                                "7":__('Type 7'),
                                "8":__('Type 8'),
                                "9":__('Type 9'),
                                "10":__('Type 10'),
                                "11":__('Type 11'),
                                "12":__('Type 12'),
                                "13":__('Type 13'),
                                "14":__('Type 14'),
                                "15":__('Type 15'),
                                "16":__('Type 16'),
                                "17":__('Type 17'),
                            },
                            formatter: Table.api.formatter.normal
                        },
                        {field: 'change_quantity', title: __('Change_quantity'), operate:'BETWEEN'},
                        {field: 'current_quantity', title: __('Current_quantity'), operate:'BETWEEN'},
                        {field: 'extend_info', title: __('Extend_info'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
