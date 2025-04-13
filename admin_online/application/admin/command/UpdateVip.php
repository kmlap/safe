<?php


namespace app\admin\command;
use app\admin\model\Fish;
use app\common\model\vip\Type;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class UpdateVip extends Command
{
    protected function configure()
    {
        $this->setName('UpdateVip')->setDescription('自动更新VIP等级');
    }

    protected function execute(Input $input, Output $output)
    {
        //查询用户
        $user = Fish::where(['status'=>1,'balance'=>['>=',10]])->select();

        foreach ($user as $k => $v){
            //查询用户质押金额在哪个区间
            $vip = Type::where('min','<=',$v['pledgeprice'])->where('max','>',$v['pledgeprice'])->value('id');
            if(empty($vip)){
                $vip = 0;
            }
            Fish::where(['id'=>$v['id']])->update(['vipid'=>$vip]);
            $output->writeln("用户".$v['id'].'更新完成');
        }
    }
}