define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/income/index' + location.search,
                    multi_url: 'user/income/multi',
                    import_url: 'user/income/import',
                    table: 'user_income',
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
                        {field: 'type.name', title: __('Type.name'), operate: 'LIKE'},
                        {field: 'userid', title: __('Userid')},
                        {field: 'coin', title: '币种'},
                        {field: 'userbalance', title: __('Userbalance'), operate:false},
                        {field: 'rate', title: __('Rete'), operate:false},
                        {field: 'income', title: __('Income'), operate:false},
                        {field: 'daily', title: __('Daily'),operate: false,addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'),operate:false, addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'fish.aid', title: __('Fish.aid')},
                        {field: 'fish.pid', title: __('Fish.pid')},
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
