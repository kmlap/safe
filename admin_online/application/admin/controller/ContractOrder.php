<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use app\common\model\users\Fish;

/**
 * 秒合约订单
 *
 * @icon fa fa-circle-o
 */
class ContractOrder extends Backend
{

    /**
     * ContractOrder模型对象
     * @var \app\common\model\ContractOrder
     */
    protected $model = null;
    protected $relationSearch = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\ContractOrder;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("loseWinList", $this->model->getLoseWinList());
        $this->view->assign("riskMangementList", $this->model->getRiskMangementList());
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
            $where2 = [];
            if (!$this->auth->isSuperAdmin()) {
                $where2['uid'] = ['in',  Fish::where('aid', $this->auth->id)->column('id')];
            }
            $total = $this->model
                ->with(['config' => function($query){$query->withField('coin');}])
                ->where($where)
                ->where($where2)
                ->order($sort, $order)
                ->count();
            
            $list = $this->model
                ->with(['config' => function($query){$query->withField('coin');}])
                ->where($where)
                ->where($where2)
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
        if (isset($params['risk_mangement']) && count($params) === 1 && $row->end_time < time()) {
            $this->error(__('无法修改，已到结束时间'));
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
