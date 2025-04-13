define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'machine_config/index' + location.search,
                    add_url: 'machine_config/add',
                    edit_url: 'machine_config/edit',
                    del_url: 'machine_config/del',
                    multi_url: 'machine_config/multi',
                    import_url: 'machine_config/import',
                    table: 'machine_config',
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
                        {field: 'type', title: __('Type'), searchList: {"1":__('Type 1'),"2":__('Type 2')}, formatter: Table.api.formatter.normal},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'low_produce', title: __('Low_produce'), operate:'BETWEEN'},
                        {field: 'high_produce', title: __('High_produce'), operate:'BETWEEN'},
                        {field: 'expire_val', title: __('Expire_val')},
                        {field: 'expire_unit', title: __('Expire_unit'), searchList: {"1":__('Expire_unit 1'),"2":__('Expire_unit 2')}, formatter: Table.api.formatter.normal},
                        {field: 'buy_num_mode', title: __('Buy_num_mode'), searchList: {"1":__('Buy_num_mode 1'),"2":__('Buy_num_mode 2')}, formatter: Table.api.formatter.normal},
                        {field: 'allow_buy_num', title: __('Allow_buy_num')},
                        {field: 'power', title: __('Power')},
                        {field: 'calc', title: __('Calc')},
                        {field: 'stars', title: __('Stars')},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
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
