<template>
    <div class="mining" >
        <div class="header_content" >
            <img src="../../assets/static/image/img_mining_top.26710d07.8d50d263.png" class="bg_cover" >
            <div class="header_title" >
                <span >
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" @click="back" aria-hidden="true" role="img" class="iconify iconify--feather back" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="feather:arrow-left" >
                        <g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5"></path>
                            <path d="M12 19l-7-7l7-7"></path>
                        </g>
                    </svg>
                </span>
                <span class="fs-36 ff_NunitoSemiBold" >{{$t('key112')}}</span>
            </div>
            <div class="header_actions" >
                <img src="../../assets/static/image/icon_mining_tip.ae051b2f.9b8a39b7.svg"  @click="go('/mining/intro?type=5')">
                <img src="../../assets/static/image/icon_mining_list.37a747d9.1c5f0090.svg"  @click="go('/mining/record')">
            </div>
        </div>
        <div class="mechine_list" >
            <div class="list_title ff_NunitoSemiBold fs-40" >{{$t('key113')}}</div>
            <div class="list_content" >
                <div class="list_wrapper"  :style="`width: ${4 * list.length}rem;`">
                    
                    <div class="list_item"  v-for="(item, index) in list" :key="index">
                        <img src="../../assets/static/image/img_mechine.5ccc7427.f6157f9a.png" class="mechine_icon" >
                        <!-- <img :src="item.image" class="mechine_icon"
                        > -->
                        <div class="time_days ff_NunitoSemiBold" >{{item.cycle}}</div>
                        <div class="mechine_name fc-353F52 ff_NunitoSemiBold" >{{item.name}}
                            <br >
                            <img src="../../assets/static/image/icon_star.ab962301.78aae29a.svg" class="icon_star" >
                            <img src="../../assets/static/image/icon_star.ab962301.78aae29a.svg" class="icon_star" >
                            <img src="../../assets/static/image/icon_star.ab962301.78aae29a.svg" class="icon_star" >
                            <img src="../../assets/static/image/icon_star.ab962301.78aae29a.svg" class="icon_star" >
                            <img src="../../assets/static/image/icon_star.ab962301.78aae29a.svg" class="icon_star" >
                        </div>
                        <div class="rent_content" >
                            <div class="rent_info ff_NunitoSemiBold" >
                                <div class="fc-353F52" >{{$t('key114')}}</div>
                                <div class="rent_num" >
                                    <span class="symbol" >$</span>{{item.price}} USDT
                                </div>
                            </div>
                            <div class="rent_go" >
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" @click="go('/mining/mechine-buy', { product_id: item.product_id })" aria-hidden="true" role="img" class="iconify iconify--feather" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="feather:arrow-right" >
                                    <g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5l7 7l-7 7"></path>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="video_container" >
            <div class="video_content" >
                <div class="video_info" >
                    <div class="fs-32 fc-1652F0 ff_NunitoSemiBold" >{{$t('key115')}}</div>
                    <div class="tips fs-32 fc-050F1A" >{{$t('key116')}}</div>
                </div>
                <div class="video_file" >
                    <iframe src="https://www.youtube.com/embed/ZOff0wQ_Vz8?si=0oHhnW1fy7aZe39B" frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" ></iframe>
                </div>
            </div>
        </div>
  </div>
</template>

<script>
import { getMachineProductList } from '@/api/user'

export default {
    name: 'miningindex',
    props: {
    },
    components: {
    },
    data() {
        return {
            list: []
        }
    },
    mounted() {
        this.getMachineProductList()
    },
    methods: {
        getMachineProductList () {
            getMachineProductList().then(res => {
                let data = res.data
                if (data.code === 1) {
                    this.list = data.data
                }
            })
        },
        go (path, query) {
            this.$router.push({ path, query })
        },
        back () {
            this.$router.back()
        }
    }
}
</script>

<style scoped>
.mining {
    padding-bottom: .8rem;
    font-weight: 500
}

.mining .header_content {
    position: relative
}

.mining .header_content .bg_cover {
    position: absolute;
    top: 0;
    left: 0;
    height: 3.1rem;
    width: auto
}

.mining .header_content .header_title {
    padding-top: .28rem;
    position: relative;
    text-align: center;
    color: #fff
}

.mining .header_content .header_title .back {
    font-size: .4rem;
    position: absolute;
    left: .4rem;
    top: .28rem;
    bottom: 0;
    margin: auto
}

.mining .header_content .header_actions {
    position: absolute;
    right: .32rem;
    top: .48rem;
    display: flex;
    flex-direction: column
}

.mining .header_content .header_actions img {
    width: .9rem
}

.mining .header_content .header_actions img:last-child {
    margin-top: .24rem
}

.mining .mechine_list {
    margin-top: 3rem
}

.mining .mechine_list .list_title {
    padding: 0 .32rem;
    color: #000
}

.mining .mechine_list .list_content {
    margin-top: .56rem;
    padding: 0 .32rem .4rem;
    overflow: auto
}

.mining .mechine_list .list_content::-webkit-scrollbar {
    display: none
}

.mining .mechine_list .list_content .list_wrapper {
    display: flex;
    flex-wrap: nowrap
}

.mining .mechine_list .list_content .list_item {
    margin-top: .68rem;
    margin-right: .24rem;
    width: 3.76rem;
    background: #fff;
    box-shadow: .06rem .12rem .2rem #0000001a;
    border-radius: .15rem
}

.mining .mechine_list .list_content .list_item .mechine_icon {
    position: relative;
    top: -.68rem;
    width: 3.76rem
}

.mining .mechine_list .list_content .list_item .time_days {
    position: relative;
    top: -.52rem;
    left: .32rem;
    display: inline-block;
    height: .56rem;
    line-height: .56rem;
    padding: 0 .24rem;
    background: linear-gradient(90deg, #318af9, #1652f0);
    box-shadow: 2px 2px 10px 4px #1652f040;
    border-radius: .1rem;
    color: #fff
}

.mining .mechine_list .list_content .list_item .mechine_name {
    position: relative;
    top: -.24rem;
    padding: 0 .32rem
}

.mining .mechine_list .list_content .list_item .mechine_name .icon_star {
    margin-top: .08rem;
    height: .16rem
}

.mining .mechine_list .list_content .list_item .rent_content {
    margin-top: .24rem;
    padding: 0 .32rem .5rem;
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center
}

.mining .mechine_list .list_content .list_item .rent_content .rent_info .rent_num {
    margin-top: .12rem;
    color: #5b616eb3
}

.mining .mechine_list .list_content .list_item .rent_content .rent_info .rent_num .symbol {
    font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Open Sans, Helvetica Neue, sans-serif;
    font-weight: 600;
    position: relative;
    top: -.08rem;
    font-size: .18rem
}

.mining .mechine_list .list_content .list_item .rent_content .rent_go {
    color: #fff;
    background-color: #1652f0;
    font-size: .35rem;
    border-radius: 100%;
    padding: .1rem;
    width: .55rem;
    height: .55rem;
    display: flex
}

.mining .video_container {
    margin-top: .4rem;
    padding: 0 .32rem
}

.mining .video_container .video_content {
    padding: .32rem;
    border-radius: .2rem;
    border: 1px solid #d8d8d8
}

.mining .video_container .video_content .video_info {
    padding: .1rem .08 0 .08rem
}

.mining .video_container .video_content .video_info .tips {
    font-weight: 700;
    margin-top: .04rem
}

.mining .video_container .video_content .video_file {
    margin-top: .4rem
}

.mining .video_container .video_content .video_file iframe {
    border-radius: .1rem;
    width: 100%
}
</style>

