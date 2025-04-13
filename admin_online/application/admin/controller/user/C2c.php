<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;
use think\Db;
/**
 * C2C
 *
 * @icon fa fa-circle-o
 */
class C2c extends Backend
{

    /**
     * C2c模型对象
     * @var \app\admin\model\user\C2c
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\user\C2c;
        $this->view->assign("statusList", $this->model->getStatusList());
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
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['user'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','country','name','email','purchase_coin','coin','amount','card','card_code','status','remark','c2c_time','verify_time']);
                $row->visible(['user']);
				$row->getRelation('user')->visible(['nickname']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    
    public function check($ids = "")
    {
         $info = $this->model->get($ids);
        if ($this->request->isAjax()) {
            $transfer_amount = $this->request->param('transfer_amount');
            $card = $this->request->param('card');
            $card_code = $this->request->param('card_code');
            $status = $this->request->param('status');
  
           $info->save([
               'verify_time' => time(),
                'transfer_amount' => $transfer_amount,
                 'card' => $card,
                 'card_code'=>$card_code,
                 'status' => $status
               ]);

            $this->success();
        }
       
        $this->view->assign("info", $info);
        return $this->view->fetch();
    }
    
        public function set($ids = "")
    {
         $info = $this->model->get($ids);
        if ($this->request->isAjax()) {
                 Db::startTrans();
                try {  
                      $info->save([
                         'status' => 4
                       ]);
                        $user = \app\common\model\users\Fish::get($info->uid);
                   
                        //获取用户
                            $userWallet = (new \app\common\services\UserWalletService($user->id, $info->coin, $info->amount))->addBalance();
                            $accountDetailsData = [                                                                             //新增用户充值账户明细
                                'uid'               => $user->id,
                                'coin'              => $info->coin,
                                'type'              => 16,
                                'change_quantity'   => $info->amount,
                                'current_quantity'  => $userWallet->balance,
                                'extend_info' => json_encode(['recharge_record_id' => $info->id])
                            ];
                            $user->accountDetails()->save($accountDetailsData);
        
                    Db::commit();
                } catch (ValidateException|PDOException|Exception $e) {
                    Db::rollback();
                      $this->error($e->getMessage());
                }
         
            $this->success('成功');
        }
       
        $this->view->assign("info", $info);
        return $this->view->fetch();
    }

}
