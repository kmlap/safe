define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'recmmended/grade/index' + location.search,
                    add_url: 'recmmended/grade/add',
                    edit_url: 'recmmended/grade/edit',
                    del_url: 'recmmended/grade/del',
                    multi_url: 'recmmended/grade/multi',
                    import_url: 'recmmended/grade/import',
                    table: 'recmmended_grade',
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
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'level', title: __('Level')},
                        {field: 'minAmount', title: __('Minamount')},
                        {field: 'oneRate', title: __('Onerate')},
                        {field: 'twoRate', title: __('Tworate')},
                        {field: 'threeRate', title: __('Threerate')},
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
