<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use fast\Random;
use think\Db;
/**
 * 新闻管理
 *
 * @icon fa fa-circle-o
 */
class News extends Backend
{

    /**
     * News模型对象
     * @var \app\common\model\News
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\News;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("langList", $this->model->getLangList());
    }

    public function add()
        {
            if (false === $this->request->isPost()) {
                return $this->view->fetch();
            }
            $params = $this->request->post('row/a');
            if (empty($params)) {
                $this->error(__('Parameter %s can not be empty', ''));
            }
            $params = $this->preExcludeFields($params);
            $params['up_info'] = Random::numeric(5);
            $params['down_info'] = Random::numeric(5);
            if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                $params[$this->dataLimitField] = $this->auth->id;
            }
            $result = false;
            Db::startTrans();
            try {
                //是否采用模型验证
                if ($this->modelValidate) {
                    $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                    $this->model->validateFailException()->validate($validate);
                }
                $result = $this->model->allowField(true)->save($params);
                Db::commit();
            } catch (ValidateException|PDOException|Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($result === false) {
                $this->error(__('No rows were inserted'));
            }
            $this->success();
        }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
