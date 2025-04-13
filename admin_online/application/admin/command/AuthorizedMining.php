<?php
namespace app\admin\command;

use app\common\model\users\Fish;
use think\Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use app\common\services\UserWalletService;

class AuthorizedMining extends Command
{
    const USDT = 'USDT';
    const ETH = 'ETH';
    
    
    protected function configure()
    {
        $this->setName('AuthorizedMining')->setDescription('授权挖矿');
    }
    
    protected function execute(Input $input, Output $output) {
        $rate = get_usdt_rate(self::ETH);
        $userList = Fish::where('status', 1)->where('pid', '<>', 0)->field('id')->select();
        foreach ($userList as $v) {
            Db::startTrans();
            try {
                $usdtUserWallet = (new UserWalletService($v->id, self::USDT))->getUserWallet();
                if ($usdtUserWallet->balance <= 0) {
                    Db::rollback();
                    continue;
                }
                $random = round(0.0035 + mt_rand() / mt_getrandmax() * (0.004 - 0.0035), 6);
                $giveUsdtAmount = bcmul($usdtUserWallet->balance, $random, 6);
                $giveEthAmount = bcdiv($giveUsdtAmount, $rate, 6);
                $ethUserWallet = (new UserWalletService($v->id, self::ETH, $giveEthAmount))->addBalance();          //增加eth
                $accountDetailsData = [                                                                                  //新增质押分佣账户明细
                    'uid'               => $v->id,
                    'coin'              => self::ETH,
                    'type'              => 10,
                    'change_quantity'   => $giveEthAmount,
                    'current_quantity'  => $ethUserWallet->balance,
                    //'extend_info' => json_encode(['pledge_order_id' => $orderId])
                ];
                $v->accountDetails()->save($accountDetailsData);
                //新增收益明细
                $incomeDetailsData = [
                    'type'        => 1,
                    'income_usdt' => $giveUsdtAmount,
                    'income_coin' => $giveEthAmount,
                ];
                $v->incomeDetails()->save($incomeDetailsData);
                Db::commit();
                $output->writeln(sprintf("%s 授权挖矿用户id: %s 执行成功", date('Y-m-d H:i:s'), $v->id));
            } catch (Exception $e) {
                Db::rollback();
                $output->writeln(sprintf("%s 授权挖矿用户id: %s error: %s", date('Y-m-d H:i:s'), $v->id, $e->getMessage()));
            }
        }
    }
    
}