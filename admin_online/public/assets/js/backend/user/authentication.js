define(['jquery', 'bootstrap', 'backend', 'table', 'form','editable'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/Authentication/index' + location.search,
                    add_url: 'user/Authentication/add',
                    edit_url: 'user/Authentication/edit',
                    del_url: 'user/Authentication/del',
                    multi_url: 'user/Authentication/multi',
                    import_url: 'user/Authentication/import',
                    table: 'Authentication',
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
                        {field: 'uid', title: __('Uid')},
                        {field: 'Country', title: __('Country'), operate: 'LIKE'},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'card', title: __('Card'), operate: 'LIKE'},
                        // {field: 'contract_address', title: __('Contract_address'), operate: 'LIKE'},
                        {field: 'image1', title: __('Image1'),events: Table.api.events.image, formatter: function (value, row, index) {
                            return '<img src="' + value + '" style="width:auto;height:50px" />';
                        }, operate: false},
                        {field: 'image2', title: __('Image2'),events: Table.api.events.image, formatter: function (value, row, index) {
                            return '<img src="' + value + '" style="width:auto;height:50px" />';
                        }, operate: false},
                        {field: 'image3', title: __('Image3'),events: Table.api.events.image, formatter: function (value, row, index) {
                            return '<img src="' + value + '" style="width:auto;height:50px" />';
                        }, operate: false},
                        {field: 'status1', title: __('Status1'),editable: {
                                type: 'select',
                                pk: 1,
                                source: [
                                    {value: '0', text: '审核失败'},
                                    {value: '1', text: '待审核'},
                                    {value: '2', text: '已审核'},
                                ]
                            },searchList: {"0":'审核失败',"1":"待审核",'2':'已审核'}},
                        {field: 'status2', title: __('Status2'),editable: {
                                type: 'select',
                                pk: 1,
                                source: [
                                    {value: '0', text: '审核失败'},
                                    {value: '1', text: '待审核'},
                                    {value: '2', text: '已审核'},
                                ]
                            },searchList: {"0":'审核失败',"1":"待审核",'2':'已审核'}},
                        // {field: 'createtime', title: __('Createtime'), operate:false, addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},    
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 启动和暂停按钮
            $(document).on("click", ".btn-start", function () {
                //在table外不可以使用添加.btn-change的方法
                //只能自己调用Table.api.multi实现
                //如果操作全部则ids可以置为空
                var ids = Table.api.selectedids(table);
                Table.api.multi("changestatus", ids.join(","), table, this);
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
        tixian:function(){
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                checkimage: function (value, row, index) {
                    //这里我们直接使用row的数据

                    return '<img src="' + value +'" target="_blank" style="width:auto;height:100px"/>';
                    


                },
            },
        }
    };
    return Controller;
});
