define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'withdraw_settings/index' + location.search,
                    table: 'withdraw_settings',
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
                        {field: 'low_withdraw_amount', title: __('Low_withdraw_amount'), operate:'BETWEEN'},
                        {field: 'high_withdraw_amount', title: __('High_withdraw_amount'), operate:'BETWEEN'},
                        {field: 'day_start_time', title: __('Day_start_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'day_end_time', title: __('Day_end_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'day_withdraw_num', title: __('Day_withdraw_num')},
                        {field: 'commission_calc_mode', title: __('Commission_calc_mode'), searchList: {"1":__('Commission_calc_mode 1'),"2":__('Commission_calc_mode 2')}, formatter: Table.api.formatter.normal},
                        {field: 'commission_val', title: __('Commission_val'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        index: function () {
            $(document).on('click', '.submit-btn', function () {
                var data = {
                    "low_withdraw_amount": $("#c-low_withdraw_amount").val(),
                    "high_withdraw_amount": $("#c-high_withdraw_amount").val(),
                    "day_start_time": $("#c-day_start_time").val(),
                    "day_end_time": $("#c-day_end_time").val(),
                    "day_withdraw_num": $("#c-day_withdraw_num").val(),
                    "commission_calc_mode": $("#c-commission_calc_mode").val(),
                    "commission_val": $("#c-commission_val").val()
                };
                $.post('', data, function (res)
                {
                    Layer.alert(res.msg);
                })
            });
            Form.api.bindevent($("form[role=form]"));
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
