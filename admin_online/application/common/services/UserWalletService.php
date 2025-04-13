<?php

namespace app\common\services;

use app\common\model\WalletCoin;

class UserWalletService
{
    protected $uid;
    protected $coin;
    protected $amount;
    protected $walletCoin;
    protected $userWallet;
    
    
    public function __construct(int $uid, string $coin, $amount = '0'){
        $this->uid = $uid;
        $this->coin = $coin;
        $this->amount = $amount;
        $this->setCoin();
        $this->setUserWallet();
    }
    
    /**
     * 设置当前币种
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function setCoin() {
        $walletCoin = WalletCoin::where('coin', $this->coin)->find();                           //获取币种信息
        if (!$walletCoin) {
            exception(__('Coin does not exist'));
        }
        $this->walletCoin = $walletCoin;
    }
    
    /**
     * 设置当前用户钱包
     * @return void
     * @throws \Exception
     */
    protected function setUserWallet() {
        $userWallet = $this->walletCoin->userWallet()->where('uid', $this->uid)->lock(true)->find();
        if (!$userWallet) {
            exception(__('Wallet does not exist'));
        }
        $this->userWallet = $userWallet;
    }
    
    /**
     * 增加余额
     * @return mixed
     */
    public function addBalance() {
        $this->userWallet->balance = bcadd($this->userWallet->balance, $this->amount, 6);
        $this->userWallet->save();
        return $this->userWallet;
    }
    
    /**
     * 减少余额
     * @return mixed
     * @throws \Exception
     */
    public function reduceUBalance() {
        if ($this->userWallet->balance < $this->amount) {
            exception(__('Insufficient amount'));
        }
        $this->userWallet->balance = bcsub($this->userWallet->balance, $this->amount, 6);
        $this->userWallet->save();
        return $this->userWallet;
    }
    
    
    /**
     * 获取用户钱包
     * @return WalletCoin
     */
    public function getUserWallet() {
        return $this->userWallet;
    }
    
    
}