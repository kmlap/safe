<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
/**
 * 用户 钱包
 *
 * @icon fa fa-circle-o
 */
class UserWallet extends Backend
{

    /**
     * Wallet模型对象
     * @var \app\common\model\UserWallet
     */
    protected $model = null;
    protected $relationSearch = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\UserWallet;

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
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['coin' => function($query){$query->withField('coin');}])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['coin' => function($query){$query->withField('coin');}])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    
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
            if ($row->balance != $params['balance']) {
                $balance = $row->balance > $params['balance'] ? -($row->balance - $params['balance']) : $params['balance'] - $row->balance;
                //新增质押锁仓账户明细
                $accountDetailsData = [
                    'uid'               => $row->uid,
                    'coin'              => (new \app\common\model\WalletCoin())->where('id', $row->wallet_coin_id)->value('coin'),
                    'type'              => 16,
                    'change_quantity'   => $balance,
                    'current_quantity'  => bcadd($row->balance, $balance, 6),
                    'extend_info'       => json_encode(['user_wallet_id' => $row->id])
                ];
                (new \app\common\model\AccountDetails())->save($accountDetailsData);
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
