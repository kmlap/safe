define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'editable'], function ($, undefined, Backend, Table, Form, undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'recharge_record/index' + location.search,
                    add_url: 'recharge_record/add',
                    edit_url: 'recharge_record/edit',
                    del_url: 'recharge_record/del',
                    multi_url: 'recharge_record/multi',
                    import_url: 'recharge_record/import',
                    table: 'recharge_record',
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
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'coin', title: __('Coin'), operate: 'LIKE'},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'status', title: __('Status'), editable: {
                                type: 'select',
                                pk: 1,
                                source: [
                                    {value: '0', text: '待审核'},
                                    {value: '1', text: '同意'},
                                    {value: '2', text: '拒绝'},
                                ]
                            }},
                        {field: 'recharge_time', title: __('Recharge_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'verify_time', title: __('Verify_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
