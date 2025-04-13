<?php


namespace app\common\controller;
use EthereumRPC\EthereumRPC;
use think\Controller;
use think\Exception;
use xtype\Ethereum\Client as EthereumClient;
use xtype\Ethereum\Utils;
use kornrunner\Keccak;



class Erc20 extends Controller
{

    //ERC20代币合约地址
    const contract_address = '0xdac17f958d2ee523a2206206994597c13d831ec7';

    //Bnb代币合约地址
    const contract_address_bnb = '0x55d398326f99059ff775485246999027b3197955';

    private function getERC20()
    {
        $client = new EthereumClient('https://mainnet.infura.io/v3/523b0c5281d94846b7850cd9fd20f5c0');
        return $client;
    }




    private function getEthRpc()
    {
        $geth = new EthereumRPC('mainnet.infura.io/v3/523b0c5281d94846b7850cd9fd20f5c0');
        $erc20 = new \ERC20\ERC20($geth);
        return $erc20;
    }


    /**
     * @return array|string
     * 获取最新区块ID
     */
    public function get_BlockNumber()
    {
        $blockData = $this->getERC20()->eth_blockNumber();
        $num = gmp_init($blockData, 16);
        $num = gmp_strval($num, 10);//转为十进制
        $ret = array();
        $ret['block_num'] = $num;
        return array('code' => 1, 'data' => $ret);
    }


    /**
     * @param $id
     * @return array
     * @throws \Tron\Exceptions\TransactionException
     * 查询最新2个区块的数据
     *
     */
    public function get_Transactions($start_block)
    {
        //先获取当前区块高度
        $ret = $this->get_BlockNumber();
        if ($ret['code'] == 0) {
            return array('code' => 0, 'data' => "get_BlockNumber fail");
        }

        $end_block = $ret['data']['block_num'] - 2; //减去两个区块 保证已经交易确认
        if (empty($start_block)) {
            $start_block = $end_block - 1;
        }

        $transactions = array();
        $lastblock = $start_block;

        $end_block = min($start_block + 2, $end_block);


        for ($i = $start_block + 1; $i <= $end_block; $i++) {
            $ret = $this->get_Block($i);

            if ($ret['code'] == 1) {
                if (isset($ret['data']['transactions'])) {
                    $transaction_list = $ret['data']['transactions'];
                    foreach ($transaction_list as $key => $value) {
                        array_push($transactions, $value);
                    }
                    $lastblock = $i;
                }
            }
        }
        $result['transactions'] = $transactions;
        $result['lastblock'] = $lastblock;
        return array('code' => 1, 'data' => $result);
    }


    //查询所有地址
    public function get_Block($block_height)
    {
        $block_height_hex = "0x" . dechex($block_height);
        $result = $this->getERC20()->eth_getBlockByNumber($block_height_hex, true);
        $result_arr = json_decode(json_encode($result), true);
        if (!isset($result_arr)) {
            return array('code' => 0, 'data' => $result_arr);
        } else {
            return array('code' => 1, 'data' => $result_arr);
        }
    }


    /**
     * 获取当前代币余额
     * @param $address
     * ERC20地址
     * @return string
     * @throws \EthereumRPC\Exception\ConnectionException
     * @throws \EthereumRPC\Exception\ContractABIException
     * @throws \EthereumRPC\Exception\ContractsException
     * @throws \EthereumRPC\Exception\GethException
     * @throws \HttpClient\Exception\HttpClientException
     */
    public function getErc20Balance($address)
    {

        $token = $this->getEthRpc()->token(self::contract_address);
        $balance = $token->balanceOf($address);
        return $balance;
    }



    /**
     * 获取当前代币余额
     * @param $address
     * ERC20地址
     * @return string
     * @throws \EthereumRPC\Exception\ConnectionException
     * @throws \EthereumRPC\Exception\ContractABIException
     * @throws \EthereumRPC\Exception\ContractsException
     * @throws \EthereumRPC\Exception\GethException
     * @throws \HttpClient\Exception\HttpClientException
     */
    public function getBNBUsdtBalance($address)
    {

        $token = $this->getEthRpc()->token(self::contract_address_bnb);
        $balance = $token->balanceOf($address);
        return $balance;
    }




    /**
     * @param $address
     * 获取当前账户以太坊钱包余额
     */
    public function getEthBalance($address)
    {
        $tag = "latest";
        try {
            $data = $this->getERC20()->eth_getBalance($address, $tag);
        } catch (\Exception $e) {
            return 0;
        }
        $num = gmp_init($data, 16);
        $num = round(gmp_strval($num, 10) / pow(10, 18), 18);//转为十进制
        $balance = $num;
        return $balance;
    }


    /**
     * @param $coin_symbol
     * @param $txid
     * @return array
     * 返回指定交易的收据，使用哈希指定交易。
     */
    public function rpc_get_transaction_receipt($coin_symbol, $txid)
    {

        $result = $this->getERC20()->eth_getTransactionReceipt($txid);
        $result_arr = json_decode(json_encode($result), true);

        if (!isset($result_arr)) {
            return array('code' => 0, 'msg' => "$coin_symbol get_TransactionReceipt error:" . $result_arr, 'data' => array());
        } else {
            return array('code' => 1, 'msg' => "$coin_symbol method: get_TransactionReceipt ok", 'data' => $result_arr);
        }

    }


    /**
     * 提现接口
     */
    public function erc_withdraw($from_address,$to_address,$address,$privateKey,$amount){
        //ERC20合约
        $contract_address = self::contract_address;

        $client = new EthereumClient('https://mainnet.infura.io/v3/523b0c5281d94846b7850cd9fd20f5c0');

        //查询ETH钱包是否有燃料
        $fuel = $this->getEthBalance($address);
        // if($fuel <= 0){
        //     return array('code' => 0, 'data' => '钱包无燃料');
        // }

        $nvs_we = hexdec($fuel);



        $gas = Utils::ethToWei($nvs_we,false);

        if($gas <= 0){
            return array('code' => 0, 'data' => '燃料不足');
        }


        $client->addPrivateKeys([$privateKey]);



        $param =[

            'from' =>$from_address,//查询地址

            'to' => $contract_address,    //usdt 智能合约地址

            'data' => '0x70a08231000000000000000000000000' . substr($from_address, 2),
        ];


        $nv = $client->eth_call($param,'latest');

        $nv_we = hexdec($nv);

        $num = $nv_we/1000000 ; //转账数量

        if($num <= 0.0001){
            return array('code' => 0, 'data' => '当前余额为0,不可以归拢');
        }

        if($amount > $num){
            return array('code' => 0, 'data' => '当前余额不足');
        }


        $num= $amount;

        // 2. 建立您的交易
        $trans = [
            "from" => $address,
            "to" => $contract_address,
            "value" => '0x0',
            "data" => '0x',
        ];
        $hash = Keccak::hash("transferFrom(address,address,uint256)",256);
        $hash_sub = mb_substr($hash,0,8,'utf-8');

        //转账地址
        $fill_from = $this->fill0(Utils::remove0x($from_address));

        //接收地址
        $fill_to= $this->fill0(Utils::remove0x($to_address));


        $num10=$num*1000000;

        $num16 = Utils::decToHex($num10);


        $fill_num16 = $this->fill0(Utils::remove0x($num16));
        $trans['data'] = "0x" . $hash_sub . $fill_from.$fill_to. $fill_num16;

        $trans['gas'] = dechex(hexdec($client->eth_estimateGas($trans)) * 1.5);

        $trans['gasPrice'] = $client->eth_gasPrice();

        $trans['nonce'] = $client->eth_getTransactionCount($address, 'pending');

        $txid = $client->sendTransaction($trans);

        $twohash=substr($txid,0,2);

        if($twohash=="0x"){
            return array('code' => 1, 'data' => '提现成功','tx_id'=>$txid);
        }else{
            return array('code' => 1, 'data' => '提现失败');
        }
    }





    /**
     * 字符串长度 ‘0’左补齐
     * @param string $str   原始字符串
     * @param int $bit      字符串总长度
     * @return string       真实字符串
     */
    public function fill0($str, $bit=64){
        if(!strlen($str)) return "";
        $str_len = strlen($str);
        $zero = '';
        for($i=$str_len; $i<$bit; $i++){
            $zero .= "0";
        }
        $real_str = $zero . $str;
        return $real_str;
    }
}