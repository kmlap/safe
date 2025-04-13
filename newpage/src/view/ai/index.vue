<template>
    <div class="arbitrage" >
        <div class="header" >
            <header-nav :backIconType="1" color="white"></header-nav>
            <div class="title" >
                <div class="fs-40 fc-ffffff ff_NunitoSemiBold" >{{ $t('key2') }}</div>
                <div class="fs-60 fc-ffffff ff_InterSemiBold" > $ {{ info.trusteeship_amount }}</div>
            </div>
            <div class="order" >
                <div class="order_btn" >
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        aria-hidden="true" role="img" class="iconify iconify--feather icon_order" width="1em"
                        height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                        data-icon="feather:file-text" >
                        <g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M16 13H8"></path>
                            <path d="M16 17H8"></path>
                            <path d="M10 9H8"></path>
                        </g>
                    </svg>
                    <span class="fs-32 fc-353F52 ff_NunitoSemiBold" 
                        @click="go('/ai/record')">{{ $t('key3') }}</span>
                </div>
            </div>
        </div>
        <div class="account_info" >
            <div class="info_content" >
                <div class="info_items" >
                    <div class="info_item" >
                        <div class="fs-32 fc-353F52 ff_NunitoSemiBold" >{{ $t('key4') }}</div>
                        <div class="mt-24 fs-28 fc-5B616E ff_InterMedium" >{{ info.total_amount }} ETH                        </div>
                    </div>
                    <div class="info_item" >
                        <div class="fs-32 fc-353F52 ff_NunitoSemiBold" >{{ $t('key5') }}</div>
                        <div class="mt-24 fs-28 fc-5B616E ff_InterMedium" >{{ info.day_amount }} ETH
                        </div>
                    </div>
                </div>
                <div class="intro_content" >
                    <div class="ff_NunitoSemiBold"  @click="go('/ai/intro?type=6')">
                        <div class="fs-32 fc-1652F0" >{{ $t('key179') }}{{ $t('key180') }}</div>
                        <div class="fs-32 fc-050F1A" >{{$t('key178')}}{{ $t('key181') }}</div>
                    </div>
                    <img src="../../assets/static/image/img_intro.e0184ea6.5acfef68.png" class="intro_img"
                        >
                </div>
            </div>
        </div>
        <div class="product_list" >
            <div class="pro_title ff_NunitoBold" >{{ $t('key175') }}{{ $t('key177') }}</div>
            <div class="list_content" >
                <div class="pro_item" v-for="(item, index) in list" :key="index" >
                    <div class="item_name ff_NunitoSemiBold" >
                        <span class="days" >
                            <span >{{ item.cycle }}</span>
                        </span>
                        <span class="name" >{{ item.name }}</span>
                    </div>
                    <div class="item_value" >
                        <div class="value_item" >
                            <div class="fs-32 fc-353F52 ff_NunitoSemiBold" >{{ $t('key9') }}</div>
                            <div class="fc-5B616E mt-24 ff_InterMedium" >
                                ${{ item.low_amount }}&nbsp;-&nbsp;{{ item.high_amount }}
                            </div>
                        </div>
                        <div class="value_item" >
                            <div class="fs-32 fc-353F52 ff_NunitoSemiBold" >{{ $t('key10') }}</div>
                            <div class="fc-5B616E mt-24 ff_InterMedium" >{{ item.day_profit }} </div>
                        </div>
                    </div>
                    <div class="item_types" >
                        <div class="type_title fs-32 fc-353F52 ff_NunitoSemiBold" >{{ $t('key175') }}{{ $t('key176') }}
                        </div>
                        <div class="types_action" >
                            <div class="types" >
                                <img v-for="(item2, index2) in item.coin_icon_list" :key="index2" :src="item2"
                                    class="coin_icon" >
                            </div>
                            <span class="actions" >
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    @click="go('/ai/product', { product_id: item.product_id })" aria-hidden="true"
                                    role="img" class="iconify iconify--feather" width="1em" height="1em"
                                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                    data-icon="feather:arrow-right" >
                                    <g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5l7 7l-7 7"></path>
                                    </g>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="video_container" >
            <div class="video_content" >
                <div class="video_info" >
                    <div class="fs-32 fc-1652F0 ff_NunitoSemiBold" >{{ $t('key179') }}{{ $t('key178') }}</div>
                    <div class="tips fs-32 fc-050F1A" >{{ $t('key13') }}</div>
                </div>
                <div class="video_file" >
                    <iframe src="https://www.youtube-nocookie.com/embed/LLoKFgfeE0o" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" ></iframe>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import headerNav from '@/components/header-nav.vue'
import { findTrusteeshipInfo, getProductList } from '@/api/user'

export default {
    name: 'aiindex',
    props: {
    },
    components: {
        headerNav
    },
    data() {
        return {
            info: {},
            list: []
        }
    },
    mounted() {
        this.findTrusteeshipInfo()
        this.getProductList()
    },
    methods: {
        findTrusteeshipInfo() {
            findTrusteeshipInfo().then(res => {
                let data = res.data
                if (data.code === 1) {
                    this.info = data.data
                }
            })
        },
        getProductList() {
            getProductList().then(res => {
                let data = res.data
                if (data.code === 1) {
                    this.list = data.data
                }
            })
        },
        go(path, query) {
            this.$router.push({ path, query })
        }
    }
}
</script>

<style scoped>
.arbitrage {
    padding-bottom: .6rem;
    font-weight: 500;
    line-height: normal
}

.arbitrage .header {
    padding-bottom: 2.02rem;
    background: #1652f0
}

.arbitrage .header .back {
    padding: .32rem .4rem
}

.arbitrage .header .back .icon_back {
    width: .4rem
}

.arbitrage .header .title {
    margin-top: .16rem;
    text-align: center;
    margin-bottom: 0 !important
}

.arbitrage .header .title .ff_InterSemiBold {
    margin-top: .06rem
}

.arbitrage .header .order {
    text-align: center
}

.arbitrage .header .order .order_btn {
    margin-top: .48rem;
    padding: 0 .56rem;
    display: inline-block;
    height: .88rem;
    line-height: .88rem;
    background: #f5f6f8;
    border-radius: .2rem
}

.arbitrage .header .order .order_btn .icon_order {
    margin-right: .2rem;
    font-size: .32rem
}

.arbitrage .account_info {
    position: relative;
    padding: 0 .32rem
}

.arbitrage .account_info .info_content {
    padding: .4rem .32rem;
    position: absolute;
    top: -1.38rem;
    left: .32rem;
    right: .32rem;
    background: #fff;
    box-shadow: 6px 12px 20px #0000001a;
    border-radius: .15rem
}

.arbitrage .account_info .info_content .info_items {
    padding-bottom: .44rem;
    border-bottom: 1px solid rgba(216, 216, 216, .5);
    display: flex;
    text-align: center
}

.arbitrage .account_info .info_content .info_items .info_item {
    width: 50%
}

.arbitrage .account_info .info_content .intro_content {
    margin-top: .42rem;
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center
}

.arbitrage .account_info .info_content .intro_content .intro_img {
    width: 2.3rem
}

.arbitrage .product_list {
    margin-top: 3.8rem;
    padding: 0 .32rem
}

.arbitrage .product_list .pro_title {
    font-size: .4rem;
    color: #000
}

.arbitrage .product_list .list_content .pro_item {
    margin-top: .4rem;
    background: #fff;
    box-shadow: 6px 12px 20px #0000001a;
    border-radius: .15rem
}

.arbitrage .product_list .list_content .pro_item .item_name {
    padding: .32rem .4rem;
    border-bottom: 1px solid rgba(216, 216, 216, .5)
}

.arbitrage .product_list .list_content .pro_item .item_name .days {
    display: inline-block;
    padding: 0 .24rem;
    height: .56rem;
    line-height: .56rem;
    background: linear-gradient(90deg, #318af9, #1652f0);
    box-shadow: 2px 2px 10px 4px #1652f040;
    border-radius: .1rem;
    color: #fff
}

.arbitrage .product_list .list_content .pro_item .item_name .name {
    margin-left: .4rem;
    font-size: .4rem;
    color: #5f6775
}

.arbitrage .product_list .list_content .pro_item .item_value {
    padding: .32rem 0;
    display: flex;
    text-align: center
}

.arbitrage .product_list .list_content .pro_item .item_value .value_item {
    width: 50%
}

.arbitrage .product_list .list_content .pro_item .item_types {
    margin-top: .16rem;
    padding: 0 .4rem .42rem .42rem
}

.arbitrage .product_list .list_content .pro_item .item_types .types_action {
    margin-top: .1rem;
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center
}

.arbitrage .product_list .list_content .pro_item .item_types .types_action .types {
    display: flex;
    align-content: center;
    align-items: center
}

.arbitrage .product_list .list_content .pro_item .item_types .types_action .types .coin_icon {
    margin-right: .24rem;
    width: .4rem;
    border-radius: 50%
}

.arbitrage .product_list .list_content .pro_item .item_types .types_action .actions {
    color: #fff;
    background-color: #1652f0;
    font-size: .35rem;
    border-radius: 100%;
    padding: .1rem;
    width: .55rem;
    height: .55rem;
    display: flex
}

.arbitrage .video_container {
    margin-top: .8rem;
    padding: 0 .32rem
}

.arbitrage .video_container .video_content {
    padding: .32rem;
    border-radius: .2rem;
    border: 1px solid #d8d8d8
}

.arbitrage .video_container .video_content .video_info {
    padding: .1rem .08 0 .08rem
}

.arbitrage .video_container .video_content .video_info .tips {
    font-weight: 700;
    margin-top: .04rem
}

.arbitrage .video_container .video_content .video_file {
    margin-top: .4rem
}

.arbitrage .video_container .video_content .video_file iframe {
    border-radius: .1rem;
    width: 100%
}
</style>

