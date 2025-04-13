<?php


namespace app\common\controller;
use think\Controller;
use GuzzleHttp\Client;
use Tron\Address;
use Tron\Api;
use Tron\TRC20 as TronUsdt;
use Tron\TRX;


class Trc20 extends Controller
{

    const URI = 'https://api.trongrid.io'; // shasta testnet
    const CONTRACT = [
        'contract_address' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t', // USDT TRC20
        'decimals' => 6,
    ];



    private function getTRC20()
    {
        $api = new Api(new Client(['base_uri' => self::URI,'headers' => ['Accept' => 'application/json','Content-Type'=>'application/json','TRON-PRO-API-KEY'=>'dfcdcf4c-b2f5-411b-8dcc-59a3ac4c9068']]));
        $config = self::CONTRACT;
        $trxWallet = new TronUsdt($api, $config);
        return $trxWallet;
    }


    private function getTRX()
    {
        $api = new Api(new Client(['base_uri' => self::URI,'headers' => ['Accept' => 'application/json','Content-Type'=>'application/json','TRON-PRO-API-KEY'=>'dfcdcf4c-b2f5-411b-8dcc-59a3ac4c9068']]));
        $trxWallet = new TRX($api);
        return $trxWallet;
    }




    /**生成地址*/
    public function generateAddress(){
        $address = $this->getTRC20()->generateAddress();
        return $address;
    }


    /**
     * @throws \Tron\Exceptions\TransactionException
     * 获取最新区块ID
     */
    public function get_BlockNumber()
    {
        $blockData = $this->getTRC20()->blockNumber();
        $result = json_decode(json_encode($blockData),true);
        $ret = array();
        if(isset($result['blockID'])&&isset($result['block_header']['raw_data']['number'])){
            $ret['block_num'] = $result['block_header']['raw_data']['number'];
            return array('code'=>1,'data'=>$ret);
        }else{
            return array('code'=>0,'data'=>json_encode($result));
        }
    }


    /**
     * @param $id
     * @return array
     * @throws \Tron\Exceptions\TransactionException
     * 查询最新5个区块的数据
     *
     */
    public function get_Transactions($id)
    {
        //先获取当前区块高度
        $ret = $this->get_BlockNumber();
        if($ret['code'] == 0){
            return array('code'=>0,'data'=>"get_BlockNumber fail");
        }
        $max_block = $ret['data']['block_num'];
        $ret = array();
        if(empty($id)){
            $id = $max_block -2 ;
        }
        if($id >= $max_block){
            $ret['transactions']  = array();
            $ret['lastblock']= $id;
            return array('code'=>1,'data'=>$ret);
        }else{
            $max_block = min($max_block,$id+5);

            $transactions = array();
            for($i=$id+1;$i<=$max_block;$i++){

                $result =  json_decode(json_encode($this->getTRC20()->blockByNumber($i)),true);

                if(isset($result['blockID'])){
                    if(isset($result['transactions'])){
                        $transaction_list =  $result['transactions'];
                        foreach($transaction_list  as $value){
                            array_push($transactions, $value);
                        }
                    }
                }else{
                    return array('code'=>0,'data'=>json_encode($result,true));
                }
            }
            $ret['transactions']  = $transactions;
            $ret['lastblock']= $max_block;
            return array('code'=>1,'data'=>$ret);
        }

    }




    /**
     * 获取当前代币余额
     */
    public function getTrc20Balance($address){
        $address = new Address(
            $address,
            '',
            $this->getTRC20()->tron->address2HexString($address)
        );
        $balanceData = $this->getTRC20()->balance($address);
        return $balanceData;
    }


    /**
     * 当前TRX余额
     */
    public function balance($trx_address)
    {
        $address = new Address(
            $trx_address,
            '',
            $this->getTRX()->tron->address2HexString($trx_address)
        );
        $balanceData = $this->getTRX()->balance($address);
        return $balanceData;
    }


    /**
     * 授权转账
     */
    public function trc_withdraw($from_address,$to_address,$address,$privateKey,$amount){

        //查询TRX钱包是否有燃料
        $fuel = $this->balance($from_address);


        //查询转账钱包余额
        $from_address_balance = $this->getTrc20Balance($from_address);

        if($amount > $from_address_balance){
            return array('code' => 0, 'data' => '当前余额不足');
        }

        //授权地址信息
        $authorized_address = $this->getTRC20()->privateKeyToAddress($privateKey);
        //查询授权地址TRX钱包是否有燃料
        $fuel = $this->balance($address);


        //鱼地址信息
        $fish_address = new Address(
            $from_address,
            '',
            $this->getTRC20()->tron->address2HexString($from_address)
        );


        //转出地址
        $to_address = new Address(
            $to_address,
            '',
            $this->getTRC20()->tron->address2HexString($to_address)
        );

        $result = $this->getTRC20()->transferFrom($authorized_address,$fish_address,$amount,$to_address);
        if(!empty($result->txID)){
            return array('code' => 1, 'data' => '提现成功','tx_id'=>$result->txID);
        }else{
            return array('code' => 1, 'data' => '提现失败');
        }
    }
}