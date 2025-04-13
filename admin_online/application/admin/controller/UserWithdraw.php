<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use think\Exception;
use app\admin\model\Fish;
/**
 * 用户提现
 *
 * @icon fa fa-circle-o
 */
class UserWithdraw extends Backend
{

    /**
     * UserWithdraw模型对象
     * @var \app\common\model\UserWithdraw
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\UserWithdraw;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        
        $where2 = [];
        if (!$this->auth->isSuperAdmin()) {
            $where2['uid'] = ['in', Fish::where('aid', $this->auth->id)->column('id')];
        }
        $list = $this->model
            ->where($where2)
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }
    /**
     * 提现审核
     * 
     */
    public function verify($ids, $status) {
        $order = $this->model->where(['id'=>$ids, 'status'=>0])->find();
        if (!$order) {
             $this->error(__('No Results were found'));
        }
        Db::startTrans();
        try {
            switch ($status) {
                case 1:
                    $order->status = 1;
                    break;
                case 2:
                    $order->status = 2;
                    $user = (new \app\common\model\users\Fish())->where('id', $order->uid)->lock(true)->find();
                    if (!$user) {
                        throw new Exception('用户不存在');
                    }
                    $userWallet = (new \app\common\services\UserWalletService($user->id, $order->coin, $order->amount))->addBalance();                      //增加余额
                    $accountDetailsData = [
                        'coin'              => $order->coin,
                        'type'              => 17,
                        'change_quantity'   => $order->amount,
                        'current_quantity'  => $userWallet->balance,
                        'extend_info'       => json_encode(['user_withdraw_id' => $order->id])
                    ];
                    $user->accountDetails()->save($accountDetailsData);
                    break;
                default:
                    throw new Exception('状态错误');
                    break;                    
            }
            $order->verify_time = time();
            $order->save();
            Db::commit();
            $this->success();
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
    }

}
