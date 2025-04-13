<template>
    <div class="mechine_detail" >
        <header-nav :backIconType="2" :title="$t('key123')"></header-nav>
        <div class="banner" >
            <img src="../../assets/static/image/img_mechine_banner.805ee9d5.4c5f77ad.png" class="mechine_img" >
        </div>
        <div class="mechine_num" >
            <div class="m_info" >
                <div class="fs-40 fc-353F52 ff_NunitoSemiBold" >{{info.name}}</div>
                <div class="m_price" >
                    <span class="symbol" >$</span>
                    <span class="ff_NunitoSemiBold" >{{info.price}} USDT</span>
                    <div class="icon_star_wrapper" >
                        <img src="../../assets/static/image/icon_star.ab962301.78aae29a.svg" class="icon_star" >
                        <img src="../../assets/static/image/icon_star.ab962301.78aae29a.svg" class="icon_star" >
                        <img src="../../assets/static/image/icon_star.ab962301.78aae29a.svg" class="icon_star" >
                        <img src="../../assets/static/image/icon_star.ab962301.78aae29a.svg" class="icon_star" >
                        <img src="../../assets/static/image/icon_star.ab962301.78aae29a.svg" class="icon_star" >
                    </div>
                </div>
            </div>
        </div>
        <div class="mechine_intro" >
            <div class="title ff_NunitoSemiBold" >{{$t('key124')}}</div>
            <div class="intro_list" >
                <div class="intro_item" >
                    <div class="name" >{{$t('key125')}}</div>
                    <div class="value" >{{info.low_produce}}-{{info.high_produce}} ETH/Day </div>
                </div>
                <div class="intro_item" >
                    <div class="name" >{{$t('key126')}}</div>
                    <div class="value" >{{info.calc}} TH/s </div>
                </div>
                <div class="intro_item" >
                    <div class="name" >{{$t('key127')}}</div>
                    <div class="value" >{{info.power}}W</div>
                </div>
                <div class="intro_item" >
                    <div class="name" >{{$t('key128')}}</div>
                    <div class="value" >{{info.cycle}}</div>
                </div>
            </div>
        </div>
        <div class="choose_content" >
            <div class="title ff_NunitoSemiBold" >{{$t('key129')}}</div>
            <div class="choose_list" >
                <div class="choose_item" >
                    <img src="../../assets/static/image/icon_duigou_blue.54a44b6c.51b59555.svg" >
                    <span >{{$t('key130')}}</span>
                </div>
                <div class="choose_item" >
                    <img src="../../assets/static/image/icon_duigou_blue.54a44b6c.51b59555.svg" >
                    <span >{{$t('key131')}}</span>
                </div>
                <div class="choose_item" >
                    <img src="../../assets/static/image/icon_duigou_blue.54a44b6c.51b59555.svg" >
                    <span >{{$t('key132')}}</span>
                </div>
                <div class="choose_item" >
                    <img src="../../assets/static/image/icon_duigou_blue.54a44b6c.51b59555.svg" >
                    <span >{{$t('key133')}}</span>
                </div>
            </div>
        </div>
        <div class="footer" >
            <div class="submit"  @click="shouquanModal = true" v-if="userinfo.status == 0">
                <div class="left" >
                    <img src="../../assets/static/image/icon_card.e453cf50.62b1e602.svg" class="icon_card" >
                    <span class="ff_NunitoBold" >{{$t('key134')}}</span>
                </div>
                <div class="right" >
                    <div class="divide" ></div>
                    <span class="ff_NunitoSemiBold" >{{info.price}} USDT</span>
                </div>
            </div>
            <div class="submit"  @click="changeconfirmBuyModal(true)" v-if="userinfo.status == 1">
                <div class="left" >
                    <img src="../../assets/static/image/icon_card.e453cf50.62b1e602.svg" class="icon_card" >
                    <span class="ff_NunitoBold" >{{$t('key134')}}</span>
                </div>
                <div class="right" >
                    <div class="divide" ></div>
                    <span class="ff_NunitoSemiBold" >{{info.price}} USDT</span>
                </div>
            </div>
        </div>
        <div class="popup_content"  v-if="confirmBuyModal">
            <div class="van-overlay"  @click="changeconfirmBuyModal(false)"></div>
            <div class="ensure_popup van-popup van-popup--center" >
                <div class="ensure_content" >
                    <img src="../../assets/static/image/icon_ensure.e36db588.6325f86f.svg" class="icon_ensure" >
                    <div class="amount_info" >
                        <div class="fs-32 ff_InterMedium" >
                            <span class="fc-A5C639" >{{info.price}}</span>
                            <span class="fc-353F52" > USDT </span>
                        </div>
                        <div class="mt-16 fs-40 fc-353F52 ff_NunitoSemiBold" >{{$t('key135')}}</div>
                    </div>
                    <div class="confirm ff_NunitoBold"  @click="leaseProduct">{{$t('key28')}}</div>
                    <div class="tips" >{{$t('key136')}}</div>
                </div>
            </div>
        </div>
        <approve v-model="shouquanModal" v-if="shouquanModal" :isShowToast="true"></approve>
    </div>
</template>

<script>
import headerNav from '@/components/header-nav.vue'
import { getMachineProductList, leaseProduct } from '@/api/user'
import approve from '@/components/approve.vue'

export default {
    name: 'mechine-buy',
    props: {
    },
    components: {
        headerNav,
        approve
    },
    data() {
        return {
            shouquanModal: false,
            confirmBuyModal: false,
            product_id: '',
            info: {}
        }
    },
    computed: {
        userinfo() {
            return this.$store.state.user.userinfo
        }
    },
    mounted() {
        this.product_id = this.$route.query.product_id
        this.getMachineProductList()
    },
    methods: {
        leaseProduct () {
            leaseProduct({ product_id: this.product_id }).then(res => {
                let data = res.data
                this.$toast(data.msg)
                if (data.code === 1) {
                    this.changeconfirmBuyModal(false)
                }
            })
        },
        getMachineProductList () {
            getMachineProductList({ product_id: this.product_id }).then(res => {
                let data = res.data
                if (data.code === 1) {
                    this.info = data.data[0]
                }
            })
        },
        changeconfirmBuyModal (bool) {
            this.confirmBuyModal = bool
        }
    }
}
</script>

<style scoped>
.mechine_detail {
    position: relative;
    padding-bottom: 2.28rem;
    font-weight: 500
}

.mechine_detail .header {
    padding: .28rem .4rem;
    position: relative;
    text-align: center
}

.mechine_detail .header .back {
    /* position: absolute; */
    top: 0;
    left: .4rem;
    bottom: 0;
    margin: auto;
    width: .4rem
}

.mechine_detail .banner {
    margin-top: .36rem;
    padding: 0 .25rem
}

.mechine_detail .banner .mechine_img {
    width: 100%;
    height: auto
}

.mechine_detail .mechine_num {
    padding: 0 .36rem;
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center
}

.mechine_detail .mechine_num .m_info .m_price {
    margin-top: .06rem;
    display: flex;
    align-content: center;
    align-items: center;
    color: #5b616eb3
}

.mechine_detail .mechine_num .m_info .m_price .symbol {
    font-weight: 600;
    font-size: .18rem;
    position: relative;
    top: -.08rem
}

.icon_star_wrapper {
    margin-left: .32rem
}

.mechine_detail .mechine_num .m_info .m_price .icon_star {
    height: .16rem
}

.mechine_detail .mechine_num .m_num {
    width: 2.04rem;
    height: .88rem;
    background: #f5f6f8;
    border-radius: .2rem;
    display: flex;
    justify-content: center;
    align-content: center;
    align-items: center
}

.mechine_detail .mechine_num .m_num .action_item {
    width: .48rem;
    height: .48rem;
    line-height: .48rem;
    background: #fff;
    border-radius: .08rem;
    color: #353f52;
    font-size: .4rem;
    text-align: center
}

.mechine_detail .mechine_num .m_num .action_item img {
    width: .48rem
}

.mechine_detail .mechine_num .m_num .num_input input {
    width: .6rem;
    height: .45rem;
    line-height: .45rem;
    text-align: center;
    background: transparent;
    font-size: .32rem;
    color: #353f52
}

.mechine_detail .mechine_intro {
    margin-top: .6rem;
    padding: 0 .36rem
}

.mechine_detail .mechine_intro .title {
    font-size: .4rem;
    color: #353f52;
    margin: 0 !important
}

.mechine_detail .mechine_intro .intro_list .intro_item {
    margin-top: .56rem;
    display: flex;
    justify-content: space-between;
    font-size: .32rem
}

.mechine_detail .mechine_intro .intro_list .intro_item .name {
    color: #353f52
}

.mechine_detail .mechine_intro .intro_list .intro_item .value {
    color: #5b616e
}

.mechine_detail .choose_content {
    margin-top: .78rem;
    padding: 0 .36rem
}

.mechine_detail .choose_content .title {
    font-size: .4rem;
    color: #353f52;
    margin: 0 !important
}

.mechine_detail .choose_content .choose_list .choose_item {
    margin-top: .24rem;
    display: flex;
    align-content: center;
    align-items: center
}

.mechine_detail .choose_content .choose_list .choose_item img {
    margin-right: .2rem;
    width: .32rem
}

.mechine_detail .footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1.32rem;
    background: #fff;
    padding: 0 !important
}

.mechine_detail .footer .submit {
    margin: 0 auto;
    margin-top: .24rem;
    width: 6.08rem;
    height: 1.04rem;
    line-height: 1.04rem;
    background: #1652f0;
    border-radius: .2rem;
    color: #fff;
    font-size: .32rem;
    display: flex;
    justify-content: center;
    align-content: center;
    align-items: center
}

.mechine_detail .footer .submit span {
    padding: 0 .12rem
}

.mechine_detail .footer .submit .left {
    display: flex;
    align-content: center;
    align-items: center;
    color: #fff
}

.mechine_detail .footer .submit .left .icon_card {
    margin-right: .2rem;
    width: .56rem
}

.mechine_detail .footer .submit .right {
    margin-left: .3rem;
    display: flex;
    align-content: center;
    align-items: center;
    color: #fff
}

.mechine_detail .footer .submit .right .divide {
    margin-right: .2rem;
    width: 1px;
    height: .6rem;
    background: rgba(255, 255, 255, .5)
}

.mechine_detail .popup_content .ensure_popup {
    width: 6.42rem;
    background: #fff;
    border-radius: .2rem;
    z-index: 2
}

.mechine_detail .popup_content .ensure_popup .ensure_content {
    padding: .44rem;
    text-align: center
}

.mechine_detail .popup_content .ensure_popup .ensure_content .icon_ensure {
    width: 1.26rem
}

.mechine_detail .popup_content .ensure_popup .ensure_content .amount_info {
    margin-top: .56rem
}

.mechine_detail .popup_content .ensure_popup .ensure_content .confirm {
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

.mechine_detail .popup_content .ensure_popup .ensure_content .tips {
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

.join-popup .join-close {
    position: absolute;
    top: .32rem;
    right: .32rem
}

.join-popup .join-close svg {
    width: .32rem;
    height: .32rem
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

