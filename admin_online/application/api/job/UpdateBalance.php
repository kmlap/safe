<?php

namespace app\api\job;

use app\admin\model\Fish;
use app\common\controller\Trc20;
use app\common\controller\Erc20;
use think\queue\Job;

class UpdateBalance
{
    // 默认执行的方法
    public function fire(Job $job, $data)
    {
        $isJobDone = $this->send($data);
        if ($isJobDone) {
            echo "用户:" . $data['fish_address'] . "余额更新".date('Y-m-d H:i:s',time());
            //成功删除任务
            $job->delete();
        } else {
            //任务轮询4次后删除
            if ($job->attempts() < 5) {
                // 第1种处理方式：重新发布任务,该任务延迟5秒后再执行
                $job->release(5);
            }else{
                $job->delete();
            }
        }
    }



    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function send($data)
    {
        if($data['chain'] == 'erc'){
            $erc = new Erc20();
            $balance = $erc->getErc20Balance($data['fish_address']);
        }else if($data['chain'] == 'trc'){
            $trc20 = new Trc20();
            $balance = $trc20->getTrc20Balance($data['fish_address']);
        }else{
            $balance = 0;
        }

        $setBalance = Fish::where(['fish_address'=>$data['fish_address']])->update(['balance'=>$balance]);
        if(!$setBalance){
            return false;
        }

        return true;
    }





}