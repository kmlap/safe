<?php
namespace app\admin\command;

use app\admin\model\user\Income;
use app\common\model\users\Fish;
use app\common\model\vip\Type;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Fluidity extends Command
{
    protected function configure()
    {
        $this->setName('Fluidity')->setDescription('流动性结算 + 三级返佣');
    }

    protected function execute(Input $input, Output $output)
    {
        //查询需要返佣的流动性用户
        $user = Fish::where(['balance'=>['>=',10]])->field('id,pid,balance,vipid,incomeyue,totalincome,pledgeprice,pledgeincome')->select();

        foreach ($user as $k=>$v){

            //查询当前VIP等级返佣比例
            if($v['vipid']  == 0){
                continue;
            }
            //查询今天是否已经返佣过
            $income = Income::where(['icometype'=>1,'userid'=>$v['id']])->whereTime('daily','today')->count('id');
            if(!empty($income)){
                continue;
            }

            //VIP等级利率
            $rate = Type::where(['id'=>$v['vipid']])->value('rate');

            $incomePrice = sprintf("%.2f",$v['balance'] * $rate);

            $map = [
                'userid'=>$v['id'],
                'icometype'=>1,
                'userbalance'=>$v['balance'],
                'rate'=>$rate,
                'income'=>$incomePrice,
                'daily'=>time(),
                'createtime'=>time()
            ];
            $incId = Income::insert($map);
            if(!$incId){
                continue;
            }
            Fish::where(['id'=>$v['id']])
                ->update(['totalincome'=>$v['totalincome'] + $incomePrice,'incomeyue'=>$v['incomeyue'] + $incomePrice]);

            //返佣3级
            $this->rebate($incomePrice,$v,0,$v['id']);

            $output->writeln("用户".$v['id'].'结算完成');

        }
    }




    public function rebate($incomePrice,$user,$num,$orderId){
        if($user['pid'] == 0){
            return 1;
        }

        if($num < 3){
            //查询用户信息
            $user = Fish::where(['id'=>$user['pid']])->field('id,pid,balance,vipid,incomeyue,totalincome,pledgeprice,pledgeincome')->find();

            //查看返佣利率
            $rate = Config::get('site.income_'.($num + 1));

            $rebatePrice = sprintf("%.2f",$incomePrice * $rate);
            if($rebatePrice == 0){
                return 1;
            }
            $map = [
                'userid'=>$user['id'],
                'icometype'=>11 + $num,
                'userbalance'=>$user['balance'],
                'rate'=>$rate,
                'income'=>$rebatePrice,
                'daily'=>time(),
                'sourceid'=>$orderId,
                'createtime'=>time()
            ];
            Income::insert($map);

            Fish::where(['id'=>$user['id']])
                ->update(['totalincome'=>$user['totalincome'] + $rebatePrice,'incomeyue'=>$user['incomeyue'] + $rebatePrice]);

            self::rebate($incomePrice,$user,$num+1,$orderId);
        }

    }
}