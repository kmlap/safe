<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 充值设置
 *
 * @icon fa fa-circle-o
 */
class RechargeSettings extends Backend
{

    /**
     * RechargeSettings模型对象
     * @var \app\common\model\RechargeSettings
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\RechargeSettings;
        $this->view->assign("commissionCalcModeList", $this->model->getCommissionCalcModeList());
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
        try {
            $row = $this->model->find();
            if ($this->request->isPost()) {
                $params = $this->request->post("");
                if (!$row) {
                    $this->model->allowField(true)->save($params);
                } else {
                    $row->allowField(true)->save($params);
                }
                $this->success("保存成功!");
            }
            $this->view->assign("row", $row);
            return $this->view->fetch();
        } catch (\think\Exception $e) {
            $this->error($e->getMessage());
        }
        
    }

}
