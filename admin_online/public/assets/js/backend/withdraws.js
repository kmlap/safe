define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'withdraws/index' + location.search,
                    // add_url: 'withdraw/add',
                    // edit_url: 'withdraw/edit',
                    // del_url: 'withdraw/del',
                    multi_url: 'withdraws/multi',
                    import_url: 'withdraws/import',
                    table: 'withdraws',
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
                searchFormVisible:true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'userid', title: __('Userid')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'bpprice', title: __('Bpprice'), operate:'BETWEEN'},
                        {field: 'realprice', title: __('Realprice'), operate:'BETWEEN'},
                        {field: 'remarks', title: __('Remarks'), operate: 'LIKE'},
                        {field: 'status', title: __('状态'),searchList: {0:'待处理',1:'通过', 2:'拒绝'},formatter: Table.api.formatter.normal},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'bpbalance', title: __('Bpbalance'), operate:'BETWEEN'},
                        {field: 'balance_sn', title: __('Balance_sn'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'status',
                                    text: '通过',
                                    title:'通过',
                                    confirm:"确定通过么?",
                                    icon: 'fa fa-check',
                                    classname: 'btn btn-info btn-xs btn-success btn-ajax',
                                    url:"withdraws/dorecharge?status=1",
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
                                    url:"withdraws/dorecharge?status=2",
                                    success:function(data,ret){
                                        $(".btn-refresh").trigger("click");
                                    },
                                    visible:function(row){
                                        if(row.status == 0){
                                            return true;
                                        }
                                    },
                                },
                            ],formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.off('dbl-click-row.bs.table');
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
