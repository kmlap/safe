<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;

/**
 * 充值记录
 *
 * @icon fa fa-circle-o
 */
class RechargeRecord extends Backend
{

    /**
     * RechargeRecord模型对象
     * @var \app\common\model\RechargeRecord
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\RechargeRecord;
        $this->view->assign("statusList", $this->model->getStatusList());

    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 编辑
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            if ($params['status'] != $row->status) {
                $params['verify_time'] = time();
                $user = $row->user;                                                                                     //获取用户
                if ($params['status'] == 1) {
                    $userWallet = (new \app\common\services\UserWalletService($user->id, $row->coin, $row->amount))->addBalance();
                    $accountDetailsData = [                                                                             //新增用户充值账户明细
                        'uid'               => $user->id,
                        'coin'              => $row->coin,
                        'type'              => 15,
                        'change_quantity'   => $row->amount,
                        'current_quantity'  => $userWallet->balance,
                        'extend_info' => json_encode(['recharge_record_id' => $row->id])
                    ];
                    $user->accountDetails()->save($accountDetailsData);
                }
                if ($row->coin == 'USDT' && $params['status'] == 1) {
                    (new \app\common\model\PlatformReport())->writeReport('recharge_usdt',$row->amount);           //写入平台报表
                    (new \app\common\model\ProxyReport())->writeReport($user->aid,'recharge_usdt',$row->amount);   //写入代理报表
                    (new \app\common\model\UserReport())->writeReport($user->id,'recharge_usdt',$row->amount);     //写入用户报表
                }
            }
            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

}
