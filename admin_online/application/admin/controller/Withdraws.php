<?php

namespace app\admin\controller;

use app\admin\model\Fish;
use app\common\controller\Backend;
use app\admin\model\Wallet;
use think\Db;

/**
 * 用户提现记录
 *
 * @icon fa fa-circle-o
 */
class Withdraws extends Backend
{

    /**
     * Withdraw模型对象
     * @var \app\common\model\Withdraw
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Withdraw;

    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','userid','createtime','bpprice','realprice','remarks','status','updatetime','bpbalance','balance_sn']);
                
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }



    /**
     * 提现处理
     */
    public function dorecharge($ids,$status){

        //获取提现订单信息
        $balance = $this->model->where(['id'=>$ids,'status'=>0])->find();
        if($balance){
            $result = false;
            $error = '';
            // 启动事务
            Db::startTrans();
            try {
                $_data['status'] = $status;
                $_data['updatetime'] = time();
                $result = $this->model->where(['id'=>$ids])->update($_data);

                //获取个人信息
                $userinfo = Fish::field('id,balance')->where('id',$balance['userid'])->find();
                if(empty($userinfo)){
                    Db::commit();
                    $this->error("用户不存在");
                }

                if($status == 2){
                    //加余额
                    Wallet::where('id', $userinfo['id'])->where('coin','USDT')->setInc('balance',$balance['bpprice']);
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                $error = $e->getMessage();
                // 回滚事务
                Db::rollback();
            }

            if ($result !== false) {
                $this->success("修改成功");
            } else {
                $this->error($error);
            }
        }else{
            $this->error("修改失败");
        }
    }
}
