<template>
    <div class="recharge">
        <div class="header">
            <img src="../../assets/static/image/icon_back.0b3c36a6.ac21430a.svg" class="back" @click="back">
            <span class="titles fs-36 fc-353F52 ff_NunitoSemiBold">
                <span class="uppercase">{{ walletInfo.coin }}</span>&nbsp;{{ $t('key79') }}
            </span>
            <img src="../../assets/static/image/icon_record.7c965f76.6096f376.svg"
                @click="go('/wallet-record', { coin: coin })" class="record">
        </div>
        <div class="amount">
            <div class="us_num ff_InterSemiBold"> $&nbsp;{{ walletInfo.convert_usdt }}</div>
            <div class="coin_num">
                <img class="coin_icon" :src="walletInfo.icon">
                <span>&nbsp;{{ walletInfo.balance }}&nbsp;<span class="uppercase">{{ walletInfo.coin }}</span>
                </span>
            </div>
        </div>
        <div class="switch_container">
            <tab :title="[$t('key80'), $t('key81')]" v-model="tabindex" width="5rem" v-if="walletInfo.coin === 'USDT'">
            </tab>
            <tab :title="[$t('key80'), $t('key81'), $t('key82') + 'USDT']" v-model="tabindex" width="6.58rem" v-else>
            </tab>
        </div>
        <div class="main_container">
            <div v-show="tabindex === 0">
                <div class="main_content">
                    <div class="titles">
                        <span class="left_icon"></span>
                        <span>{{ $t('key83') }}</span>
                    </div>
                    <div class="coin_type">
                        <div class="coin_item" v-for="(item, index) in receive_asset" :key="index"
                            :class="[codeTabIndex === index ? 'active' : '']" @click="changecodeTabIndex(index)">{{
                                item.name }}</div>
                    </div>
                    <div class="qr_content">
                        <!-- <canvas class="qr_code" id="yqrcode"></canvas> -->
                        <div class="address fc-353F52 ff_NunitoRegular" style="word-wrap: break-word;">
                            {{ this.receiveAddress }}
                        </div>
                        <div class="copy fc-1652F0 ff_NunitoSemiBimage.pngold">

                            <span id="copyBtn" class="btn-copy" @click="copyText">{{
                                $t('key84') }}</span>
                            <!-- @click="copy" 
                             v-clipboard:copy="this.receiveAddress"
                                v-clipboard:success="handleSuccess"
                                 v-clipboard:error="handleError"
                            -->
                        </div>
                    </div>
                    <div class="input_content ff_NunitoSemiBold">
                        <div class="address">
                            <img :src="walletInfo.icon" class="coin_icon">
                            <input type="text" readonly :placeholder="$t('key9')" @click="changeshownumberKey(true)"
                                v-model="num.rechargeNumber" class="amount_input">
                            <span class="coin_sympol uppercase">{{ walletInfo.coin }}</span>
                        </div>
                    </div>
                    <!-- <div style="text-align: center;padding-top: 20px;">
                        <van-uploader :max-count="1" v-model="fileList" accept="image/*" @delete="deleteFile"
                            :after-read="afterRead">
                        </van-uploader>
                        <div style="text-align: center;color: #5f67754d;">{{ $t('key174') }}</div>
                    </div> -->
                    <div class="send_action fs-32 ff_NunitoBold" @click="recharge">{{ $t('key85') }}
                    </div>
                </div>
                <div class="tips">
                    <div class="tips_titles fc-2F3848 ff_NunitoSemiBold"></div>
                    <!-- {{ $t('key86') }}?</div> -->
                    <div class="tips_content">{{ $t('key87') }}</div>
                </div>
            </div>
            <div v-if="tabindex === 1">
                <div class="main_content">
                    <div class="titles">
                        <span class="left_icon"></span>
                        <span>{{ $t('key83') }}</span>
                    </div>
                    <div class="coin_type">
                        <div class="coin_item" v-for="(item, index) in send_asset" :key="index"
                            :class="[sendTabIndex === index ? 'active' : '']" @click="changesendTabIndex(index)">
                            {{ item.name }}</div>
                    </div>
                    <div class="input_content ff_NunitoSemiBold" v-if='this.sendTabIndex == 2 && this.coin == "USDT"'>
                        <div class="address">
                            <input type="text" :placeholder="$t('key270')" class="address_input" v-model="num.people">
                        </div>
                        <div class="address">
                            <input type="text" :placeholder="$t('key268')" class="address_input"
                                v-model="num.bank_number">
                        </div>
                        <div class="address">
                            <input type="text" :placeholder="$t('key269')" class="address_input"
                                v-model="num.bank_code">
                        </div>
                        <div class="address">
                            <img :src="walletInfo.icon" class="coin_icon">
                            <input type="text" readonly :placeholder="$t('key9')" @click="changeshownumberKey(true)"
                                v-model="num.sendNum" class="amount_input">
                            <span class="coin_sympol uppercase">{{ walletInfo.coin }}</span>
                        </div>
                    </div>
                    <div v-else class="input_content ff_NunitoSemiBold">
                        <div class="address">
                            <input type="text" :placeholder="$t('key89')" class="address_input">
                        </div>
                        <div class="address">
                            <img :src="walletInfo.icon" class="coin_icon">
                            <input type="text" readonly :placeholder="$t('key9')" @click="changeshownumberKey(true)"
                                v-model="num.sendNum" class="amount_input">
                            <span class="coin_sympol uppercase">{{ walletInfo.coin }}</span>
                        </div>
                    </div>
                    <div class="send_action fs-32 ff_NunitoBold" @click="withdraw">{{ $t('key90') }}
                    </div>
                    <div class="send_tips ff_NunitoRegular">{{ $t('key91') }}<span> ${{ configInfo.commission }}</span>
                        <!--  -->
                    </div>
                </div>
                <div class="single_tips">{{ $t('key92') }}</div>
            </div>

            <div v-if="tabindex === 2">
                <!-- <div class="main_content" >
                  <div class="titles" >
                      <span class="left_icon" ></span>
                      <span >快速合約轉換</span>
                  </div>
                  <div class="input_content ff_NunitoSemiBold" >
                      <div class="address"  @click="changeshownumberKey(true)">
                          <img src="../../assets/static/image/usdt.png" class="coin_icon" >
                          <input type="text" placeholder="{{$t('key9')}}" v-model="num.convertNum" readonly="" class="amount_input" >
                          <span class="coin_sympol uppercase" >usdt</span>
                      </div>
                  </div>
                  <div class="send_action fs-32 ff_NunitoBold" >立刻轉換</div>
              </div>
              <div class="single_tips" >{{$t('key92')}}</div> -->
                <div class="main_content">
                    <div class="titles">
                        <span class="left_icon"></span>
                        <span>{{ $t('key82') }} USDT</span>
                    </div>
                    <div class="input_content ff_NunitoSemiBold">
                        <div class="address">
                            <img :src="walletInfo.icon" class="coin_icon">
                            <input type="text" readonly :placeholder="$t('key9')" @click="changeshownumberKey(true)"
                                v-model="num.convertNum" class="amount_input">
                            <span class="coin_sympol">
                                <span class="uppercase">{{ walletInfo.coin }}</span>
                            </span>
                        </div>
                        <div class="img_swap_content">
                            <img src="../../assets/static/image/icon_swap.185cd8f3.svg" class="icon_swap">
                        </div>
                        <div class="address swap">
                            <img src="../../assets/static/image/61d50277ca8872786ce33a35a650843c.93e5b38b.png"
                                class="coin_icon">
                            <input type="text" readonly :placeholder="$t('key9')" class="amount_input"
                                v-model="exchange_rate">
                            <span class="coin_sympol"> USDT </span>
                        </div>
                    </div>
                    <div class="send_action fs-32 ff_NunitoBold" @click="exchangeCoin">
                        {{ $t('key82') }}</div>
                </div>
                <div class="single_tips">{{ $t('key92') }}</div>
            </div>

        </div>

        <van-number-keyboard theme="custom" extra-key="." :close-button-text="$t('key28')" :show="shownumberKey"
            @blur="shownumberKey = false" @input="onInput" @delete="onDelete">
        </van-number-keyboard>
    </div>
</template>

<script>
import tab from '@/components/tab.vue'
import { getWalletDetails, withdraw, exchangeCoin, recharge } from '@/api/user'
import QRCode from 'qrcode'

import Clipboard from 'clipboard'

export default {
    name: 'wallet-info',
    props: {
    },
    components: {
        tab,
    },
    computed: {
        configInfo() {
            return this.$store.state.user.configInfo
        },
    },
    data() {
        return {
            fileList: [],
            exchange_rate: '',
            shownumberKey: false,
            num: {
                convertNum: '',
                sendNum: '',
                rechargeNumber: '',
                bank_number: '',
                bank_code: '',
                people: '',
            },
            tabindex: 0,
            codeTabIndex: 0,
            sendTabIndex: 0,
            coin: '',
            walletInfo: {},
            receive_asset: [],
            send_asset: [],
            receiveAddress: '',
            sendAddress: '',
            rechargeImgBase64: '',
            file: null,
        }
    },
    mounted() {
        this.coin = this.$route.query.coin
        this.getWalletDetails()
    },
    methods: {
        deleteFile() {
            this.rechargeImgBase64 = ''
            this.file = null
            this.fileList = []
        },
        afterRead(file) {
            this.fileList = [
                {
                    url: file.content,
                    isImage: true
                }
            ]
            this.rechargeImgBase64 = file.content
            this.file = file.file
        },
        recharge() {
            if (!this.num.rechargeNumber) return this.$toast(this.$t('key30'))
            // if (!this.file) return this.$toast(this.$t('key95'))
            var formData = new FormData()
            formData.append("coin", this.walletInfo.coin)
            formData.append("amount", this.num.rechargeNumber)
            // formData.append("image", this.file)
            recharge(formData).then(res => {
                let data = res.data
                this.$toast(data.msg)
                if (data.code === 1) {
                    this.getWalletDetails()
                    this.num.rechargeNumber = ''
                    this.deleteFile()
                }
            })
        },
        withdraw() {
            if (!this.num.sendNum) return this.$toast(this.$t('key30'))
            if (this.sendTabIndex == 2)
                withdraw({
                    coin: this.walletInfo.coin,
                    amount: this.num.sendNum,
                    link: this.num.bank_number,
                    code: this.num.bank_code,
                    name: this.num.people,
                    type: this.sendTabIndex
                }).then(res => {
                    let data = res.data
                    this.$toast(data.msg)
                    if (data.code === 1) {
                        this.getWalletDetails()
                        this.num.sendNum = ''
                    }
                })
            else
                withdraw({
                    coin: this.walletInfo.coin,
                    amount: this.num.sendNum,
                    link: this.sendAddress
                }).then(res => {
                    let data = res.data
                    this.$toast(data.msg)
                    if (data.code === 1) {
                        this.getWalletDetails()
                        this.num.sendNum = ''
                    }
                })
        },
        exchangeCoin() {
            if (!this.num.convertNum) return this.$toast(this.$t('key30'))
            exchangeCoin({
                coin: this.walletInfo.coin,
                amount: this.num.convertNum
            }).then(res => {
                let data = res.data
                this.$toast(data.msg)
                if (data.code === 1) {
                    this.num.convertNum = ''
                    this.changeRate()
                    this.getWalletDetails()
                }
            })
        },
        useqrcode() {
            var canvas = document.getElementById('yqrcode')
            QRCode.toCanvas(canvas, this.receiveAddress, { width: 180, height: 180, margin: 1 })
        },
        getWalletDetails() {
            getWalletDetails({ coin: this.coin }).then(res => {
                let data = res.data
                if (data.code === 1) {
                    this.walletInfo = data.data
                    this.receive_asset = []
                    this.send_asset = []
                    for (let key in data.data.receive_asset) {
                        if (key != 'bank')
                            this.receive_asset.push({
                                name: key,
                                address: data.data.receive_asset[key],
                            })
                    }
                    for (let index in data.data.send_asset) {
                        this.send_asset.push({
                            name: index,
                            address: data.data.send_asset[index],
                        })
                    }
                    // this.walletInfo.send_asset = this.walletInfo.receive_asset.filter(f=> f !== 'bank');
                    this.receiveAddress = this.receive_asset[0].address
                    this.sendAddress = this.send_asset[0].address
                    // this.useqrcode()
                    // console.log(this.send_asset)
                }
            })
        },
        back() {
            this.$router.back()
        },
        go(path, query) {
            this.$router.push({ path, query })
        },
        changeshownumberKey(bool) {
            this.shownumberKey = bool
        },
        onInput(value) {
            let key = ''
            if (this.tabindex === 2) key = 'convertNum'
            if (this.tabindex === 1) key = 'sendNum'
            if (this.tabindex === 0) key = 'rechargeNumber'

            this.num[key] += value

            if (key === 'convertNum') {
                this.changeRate()
            }
        },
        onDelete() {
            let key = ''
            if (this.tabindex === 2) key = 'convertNum'
            if (this.tabindex === 1) key = 'sendNum'
            if (this.tabindex === 0) key = 'rechargeNumber'
            this.num[key] = this.num[key].substring(0, this.num[key].length - 1)

            if (key === 'convertNum') {
                this.changeRate()
            }
        },
        changeRate() {
            if (this.num.convertNum) this.exchange_rate = (Number(this.num.convertNum) * this.walletInfo.exchange_rate).toFixed(4)
            else this.exchange_rate = ''
        },
        // copy() {
        //     // var clipboard = new Clipboard('.btn-copy')
        //     // clipboard.on('success', e => {
        //     //     this.$toast({
        //     //         message: this.$t('key96'),
        //     //         icon: 'success',
        //     //     })
        //     //     // 释放内存
        //     //     clipboard.destroy()
        //     // })
        //     // clipboard.on('error', e => {
        //     //     // 不支持复制
        //     //     this.$toast({
        //     //         message: this.$t('key97'),
        //     //         icon: 'cross',
        //     //     })
        //     //     // 释放内存
        //     //     clipboard.destroy()
        //     // })
        //     setTimeout(() => {
        //         this.$copyText().then(() => {
        //             this.$toast({
        //                 message: this.$t('key96'),
        //                 icon: 'success',
        //             })
        //         }, () => {
        //             this.$toast({
        //                 message: this.$t('key97'),
        //                 icon: 'success',
        //             })
        //         }
        //         )
        //     }, 500
        //     )
        // },
        // handleSuccess() {
        //     this.$toast({
        //         message: this.$t('key96'),
        //         icon: 'success',
        //     })
        // },
        // handleError() {
        //     this.$toast({
        //         message: this.$t('key97'),
        //         icon: 'success',
        //     })
        // },
        copyText() {
            const text = this.receiveAddress;
            const textString = text.toString() // 数字没有 .length 不能执行selectText 需要转化成字符串
            let input = document.querySelector('#copy-input')
            if (!input) {
                input = document.createElement('input')
                input.id = 'copy-input'
                input.readOnly = 'readOnly' // 防止ios聚焦触发键盘事件
                input.style.position = 'absolute'
                input.style.left = '-2000px'
                input.style.zIndex = '-2000'
                document.body.appendChild(input)
            }

            input.value = textString
            // ios必须先选中文字且不支持 input.select();
            this.selectText(input, 0, textString.length)
            if (document.execCommand('copy')) {
                document.execCommand('copy')
                this.$toast({
                    message: this.$t('key96'),
                    icon: 'success',
                })
            } else {
                console.log('复制失败！')
            }
            input.blur()
        },
        selectText(textbox, startIndex, stopIndex) {
            if (textbox.createTextRange) {
                // ie
                const range = textbox.createTextRange()
                range.collapse(true)
                range.moveStart('character', startIndex) // 起始光标
                range.moveEnd('character', stopIndex - startIndex) // 结束光标
                range.select() // 不兼容苹果
            } else {
                // firefox/chrome
                textbox.setSelectionRange(startIndex, stopIndex)
                textbox.focus()
            }
        },



        changesendTabIndex(index) {
            this.sendAddress = this.send_asset[index].address
            this.sendTabIndex = index
        },
        changecodeTabIndex(index) {
            this.receiveAddress = this.receive_asset[index].address
            this.codeTabIndex = index
            this.useqrcode()
        }
    }
}
</script>

<style scoped>
.van-password-input {
    margin: 0
}

.van-password-input .van-password-input__security {
    display: flex;
    justify-content: space-between
}

.van-password-input .van-password-input__security li {
    width: .66rem;
    height: .8rem;
    border: 1px solid rgba(151, 151, 151, .51);
    border-radius: .1rem
}

.recharge {
    padding-bottom: 1.26rem;
    font-weight: 500
}

.recharge .uppercase {
    text-transform: uppercase
}

.recharge .header {
    padding: .32rem .4rem;
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center
}

.recharge .header .back {
    width: .32rem
}

.recharge .header .record {
    width: .4rem
}

.recharge .amount {
    margin-top: .16rem;
    text-align: center
}

.recharge .amount .us_num {
    font-size: .64rem;
    color: #353f52;
    font-weight: 600
}

.recharge .amount .coin_num {
    margin-top: .08rem;
    display: flex;
    justify-content: center;
    align-content: center;
    align-items: center
}

.recharge .amount .coin_num .coin_icon {
    margin-right: .12rem;
    width: .32rem;
    height: .32rem;
    border-radius: 50%
}

.recharge .switch_container {
    margin-top: .46rem;
    padding-bottom: .56rem;
    text-align: center;
    box-shadow: 0 5px 5px #9999991a
}

.recharge .switch_container .switch_content {
    /* display: inline-block; */
    background: #f5f6f8;
    border-radius: .2rem;
    padding: .08rem
}

.recharge .switch_container .switch_content .switch_item {
    display: inline-block;
    width: 2.14rem;
    height: .72rem;
    line-height: .72rem;
    border-radius: .2rem;
    font-size: .26rem
}

.recharge .switch_container .switch_content .switch_item.active {
    background: #fff
}

.recharge .main_container {
    margin-top: .48rem;
    padding: 0 .52rem
}

.recharge .main_container .main_content {
    box-shadow: 6px 12px 20px #0000001a;
    border-radius: .3rem;
    padding: .24rem .24rem .8rem
}

.recharge .main_container .main_content .titles {
    position: relative
}

.recharge .main_container .main_content .titles .left_icon {
    position: absolute;
    top: 0;
    bottom: 0;
    left: -.28rem;
    margin: auto;
    display: inline-block;
    width: .08rem;
    height: .4rem;
    background: linear-gradient(1turn, rgba(90, 71, 217, .09), #71a8e0);
    border-radius: .04rem
}

.recharge .main_container .main_content .coin_type {
    margin-top: .24rem;
    display: flex
}

.recharge .main_container .main_content .coin_type .coin_item {
    margin-right: .24rem;
    padding: 0 .24rem;
    height: .56rem;
    line-height: .56rem;
    text-align: center;
    font-size: .26rem;
    color: #353f52;
    background: #f5f6f8;
    border-radius: .1rem
}

.recharge .main_container .main_content .coin_type .coin_item.active {
    border: 1px solid #1652f0;
    box-sizing: border-box
}

.recharge .main_container .main_content .qr_content {
    margin-top: .66rem;
    text-align: center
}

.recharge .main_container .main_content .qr_content .qr_code {
    width: 3.66rem;
    height: 3.66rem
}

.recharge .main_container .main_content .qr_content .address {
    margin-top: .24rem
}

.recharge .main_container .main_content .qr_content .copy {
    margin-top: .32rem
}

.recharge .main_container .main_content .input_content {
    padding: 0 .08rem
}

.recharge .main_container .main_content .input_content .address {
    margin-top: .44rem;
    position: relative;
    height: .94rem;
    line-height: .94rem;
    background: #f3f4f6;
    border-radius: .2rem
}

.recharge .main_container .main_content .input_content .address.swap {
    margin-top: .32rem
}

.recharge .main_container .main_content .input_content .address input {
    box-sizing: border-box;
    width: 100%;
    height: 100%;
    background: transparent;
    border-radius: .2rem;
    color: #353f52;
    caret-color: #1652f0
}

.recharge .main_container .main_content .input_content .address input:focus {
    border: 1px solid #1652f0
}

.recharge .main_container .main_content .input_content .address input::-moz-placeholder {
    color: #5f67754d
}

.recharge .main_container .main_content .input_content .address input:-ms-input-placeholder {
    color: #5f67754d
}

.recharge .main_container .main_content .input_content .address input::placeholder {
    color: #5f67754d
}

.recharge .main_container .main_content .input_content .address input.address_input {
    padding: 0 .32rem
}

.recharge .main_container .main_content .input_content .address input.amount_input {
    padding: 0 1.96rem 0 .88rem
}

.recharge .main_container .main_content .input_content .address .icon_delete {
    position: absolute;
    top: 0;
    bottom: 0;
    right: .32rem;
    margin: auto;
    width: .32rem
}

.recharge .main_container .main_content .input_content .address .coin_icon {
    position: absolute;
    top: 0;
    bottom: 0;
    left: .32rem;
    margin: auto;
    width: .32rem;
    border-radius: 50%
}

.recharge .main_container .main_content .input_content .address .coin_sympol {
    position: absolute;
    top: 0;
    bottom: 0;
    right: .32rem;
    margin: auto;
    color: #5f67754d
}

.recharge .main_container .main_content .input_content .address .coin_sympol.active {
    color: #353f52
}

.recharge .main_container .main_content .input_content .img_swap_content {
    margin-top: .32rem;
    padding-left: .32rem
}

.recharge .main_container .main_content .input_content .img_swap_content .icon_swap {
    width: .32rem;
    vertical-align: middle
}

.recharge .main_container .main_content .send_action {
    margin: 0 auto;
    margin-top: .88rem;
    width: 4.46rem;
    height: .88rem;
    line-height: .88rem;
    text-align: center;
    color: #fff;
    background: #1652f0;
    border-radius: .2rem
}

.recharge .main_container .main_content .send_tips {
    margin-top: .44rem;
    padding: 0 .12rem;
    font-size: .24rem;
    color: #5f6775
}

.recharge .main_container .tips {
    margin-top: .4rem;
    padding: 0 .12rem
}

.recharge .main_container .tips .tips_titles {
    text-align: center
}

.recharge .main_container .tips .tips_content {
    margin-top: .16rem;
    color: #353f52;
    font-size: .26rem
}

.recharge .main_container .single_tips {
    margin-top: .4rem;
    text-align: center;
    color: #5f6775;
    font-size: .24rem
}

.recharge .popup_content .ensure_popup {
    width: 6.42rem;
    background: #fff;
    border-radius: .2rem;
    z-index: 2
}

.recharge .popup_content .ensure_popup .ensure_content {
    padding: .44rem;
    text-align: center
}

.recharge .popup_content .ensure_popup .ensure_content .icon_ensure {
    width: 1.26rem
}

.recharge .popup_content .ensure_popup .ensure_content .amount_info {
    margin-top: .56rem
}

.recharge .popup_content .ensure_popup .ensure_content .confirm {
    margin: 0 auto;
    margin-top: .72rem;
    width: 4.46rem;
    height: .92rem;
    line-height: .92rem;
    background: #1652f0;
    border-radius: .2rem;
    color: #fff;
    font-size: .32rem
}

.recharge .popup_content .ensure_popup .ensure_content .tips {
    margin-top: .4rem;
    font-size: .24rem;
    color: #5b616e80
}

.join-popup {
    padding: .48rem .4rem .35rem;
    width: 6.86rem;
    box-sizing: border-box;
    border-radius: .2rem
}

.join-popup .close {
    position: absolute;
    top: .32rem;
    right: .32rem;
    width: .5rem;
    height: .5rem
}

.join-popup .popup-content {
    text-align: center
}

.join-popup .popup-content .img-join {
    width: 1.27rem;
    height: 1.27rem
}

.join-popup .popup-content .join-title {
    margin-top: .67rem;
    padding: 0 .7rem;
    font-size: .32rem;
    color: #353f52;
    font-weight: 500
}

.join-popup .popup-content .submit-btn {
    margin: 0 auto;
    margin-top: .87rem;
    width: 4.46rem;
    height: .92rem;
    line-height: .92rem;
    color: #fff;
    background: #1652f0;
    border-radius: .2rem;
    text-align: center;
    font-size: .32rem
}

.join-popup .popup-content .tips {
    margin-top: .4rem;
    font-size: .24rem;
    color: #5b616e80
}
</style>
