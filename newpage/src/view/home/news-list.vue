<template>
    <div>
        <header-nav :backIconType="2" :title="$t('key0')"></header-nav>
        <div class="view-wrapper has-top-nav" >
            <div class="page-content-wrapper" >
                <div class="page-content is-relative"  style="padding: 16px;padding-top: 0;">
                    <div class="is-navbar-lg" >
                        <div class="news-container" >
                            <div class="home-news" >
                                <div class="news-content" >

                                    <van-pull-refresh v-model="loading" @refresh="getNewsListXiala"
                                        pulling-text="Pull down to refresh.." loosing-text="Release to refresh..."
                                        loading-text="loading...">
                                        <van-list v-model="loading" :finished="finished" loading-text="loading..."
                                            @load="getNewsList">
                                            <!-- <div class="news-item" 
                                                @click="changenewsPanelModal(true, item.link)"
                                                v-for="(item, index) in List" :key="index">
                                                <div class="news-info" >
                                                    <div class="main-title" >
                                                        <span class="fs-26" > {{ item.title }} </span>
                                                        <span class="date fs-26"
                                                            >{{ item.news_time }}</span>
                                                    </div>
                                                    <div class="subtitle" >{{ item.abstract }}</div>
                                                    <div class="author" >{{ item.author }} </div>
                                                </div>
                                                <div class="news-img" >
                                                    <img :src="item.image" class="img-pic" >
                                                </div>
                                            </div> -->

                                            <div class="news-item"
                                                style="width: 100%;display: flex;flex-direction: column;"
                                                 v-for="(item, index) in List" :key="index">
                                                <!-- @click="changenewsPanelModal(true, item.link)" -->
                                                <div class="news-info" style="width: 100%;" >
                                                    <div class="main-title" 
                                                        style="display: flex;flex-direction: column;">
                                                        <span class="fs-26" 
                                                        style="font-size: .28rem;font-weight: 600;color:#000"> {{ item.title }} </span>
                                                        <span class=" fs-26" style="color:darkgrey">{{ item.news_time
                                                            }}</span>
                                                    </div>
                                                    <div ref='showText' class="subtitle show" 
                                                        style="font-size: .28rem;" v-html="item.abstract"></div>
                                                    <div class="author" ref="textshow"
                                                        style="text-align: center;width: 100%;color:steelblue"
                                                        @click="showNews(index)">
                                                        {{$t('key271')}}
                                                    </div>
                                                    <!-- <div class="author" >{{item.author}} </div> -->
                                                </div>
                                                <div class="emotion"
                                                    style="display: flex;align-items: center;gap: .2rem;">
                                                    <div style="display: flex;align-items: center;gap: .1rem;"
                                                        class="emotion-item"><span>Positive({{item.up_info}})</span><img
                                                            style="width: .3rem;height: .3rem;"
                                                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAGaBAMAAABHwGMuAAAAHlBMVEUAAAAUspgUspgUspgUspgUspgUspgUspgUspgUspg1sh+QAAAACXRSTlMAQMBgoDDw0BA3VwgBAAAEXklEQVR42szQuQ3CUABAsUhMlY4haf62PBBHrhIRewRPv3O5TqL5NoEuY4hd8xhgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1liV1lgV1li1yPL63pmeV1lRet6ZWld7yyr65NldX2zpK5FltS1zHK6VllO1zpL6dpkKV3bLKNrl2V07bOEroMsoeso6/yuw6zzu/6QVReZdWeHjk20isIoiv4IiuVYgiWI4HSgYgmGRsYiaLueXOZtWBcmeif97oXNCi7G+vr86a9ynWP9+fj87dd35DrH+vzqIusncp1jfbjKeotc51iPq6wHcp1jXWch1znWdZZxnWNFlnGdY0UWcZ1jVRZxnWN1lnM5VmYZ1zlWZzmXY3WWczlWZzmXY3UWcp1jdZZzOVZnOZdjdZZzOVZnOZdjdRZynWN1lnM5Vmc5l2N1lnM5Vmc5l2N1FnGdY3WWczlWZzmXY3UWcDkWZBmXY3WWczlWZzmXY3WWczlWZ/3P1XtPWJClXI7VWc7lWJ3lXI7VWc7lWJ3lXI7VWcwFWJCFXI7VWc7lWJ3lXI7VWc7lWJ3lXIAFWcD1BrE6y7n2+fU3xOos53r3eDwZVmc51++9G1djSZZzDWtchNVZF1yN1VzDoqzgCqxxAZZnjauxmmtYlhVchTUuwOKscSVWcw0Ls4IrscYFWJw1rsBqrmFpVnA11rgAi7PGFVjFNSzOIq5hBVdheda4Aiu4hsVZwjWs4AqszgKuYQVXYXUWcA0ruAILspprWMEVWJLVXMMKrsCCrOYaVnI1Vmc1V2ONq7E8q7mGFVyBJVnNNazgCizIaq5hBVdgSVZzDSu4AguymmtYwRVYkBVcgTWuwMKs5hpWcAWWZDXXsK65AkuymmtYwRVYktVcwwquwIKs5hpWcgUWZCXXsJIrsCAruIa1JVdgQVZwDav3dIEFWck1rK25vlxgQVZyDWvLfXq8XNb24+HzrN6ddWfdWXfWs7uz7qw76876x+4cCAAAAAAI8reeYIMiaGlpaWktLS0traWlpaW1tLS0tJaWlpbW0tLS0lpaWlpaS0tLS2tpaWlpLS0tLa2lpaWltbS0tLSWlpaW1tLS0tJaWlpaWktLS0traWlpaS0tLS2tpaWlpbW0tLS0lpaWltbS0tLSWlpaWlpLS0tLa2lpaWktLS0traWlpaW1tLS0tJaWllYbdRZOMOqsUWdhAlZBnECCIoMBCWFo6ZzFVmkAAAAASUVORK5CYII=">
                                                    </div>
                                                    <div style="display: flex;align-items: center;gap: .1rem;"
                                                        class="emotion-item"><span>Negative({{ item.down_info }})</span><img
                                                            style="width: .3rem;height: .3rem;"
                                                            src="data:image/png;base64,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">
                                                    </div>
                                                </div>
                                                <!-- <div class="news-img" >
                            <img :src="item.image" class="img-pic" >
                        </div> -->
                                            </div>
                                        </van-list>
                                        <no-data v-if="List.length <= 0"></no-data>
                                    </van-pull-refresh>
                                </div>
                            </div>
                        </div>
                        <div id="news-panel" class="right-panel-wrapper is-news"  >
                            <div class="panel-overlay" tabindex="0" ></div>
                            <div class="right-panel" >
                                <div class="right-panel-head" >
                                    <div  style="width: 90px;">
                                        <a class="close-panel" tabindex="0" >
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img"
                                                class="iconify iconify--fluent" width="1em" height="1em"
                                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                                data-icon="fluent:arrow-left-24-regular" >
                                                <g fill="none">
                                                    <path
                                                        d="M10.733 19.79a.75.75 0 0 0 1.034-1.086L5.516 12.75H20.25a.75.75 0 0 0 0-1.5H5.516l6.251-5.955a.75.75 0 0 0-1.034-1.086l-7.42 7.067a.995.995 0 0 0-.3.58a.754.754 0 0 0 .001.289a.995.995 0 0 0 .3.579l7.419 7.067z"
                                                        fill="currentColor"></path>
                                                </g>
                                            </svg>
                                        </a>
                                    </div>
                                    <span >{{ $t('key0') }}</span>
                                    <div class="head-end" ></div>
                                </div>
                                <div class="right-panel-body has-slimscroll" >
                                    <!---->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <news-panel v-model="newsPanelModal" :url="link"></news-panel>
    </div>
</template>

<script>
import headerNav from '@/components/header-nav.vue'
import newsPanel from '@/components/news-panel.vue'
import { getNewsList } from '@/api/user'
import noData from '@/components/no-data.vue'

export default {
    name: 'news-list',
    props: {
    },
    components: {
        headerNav,
        newsPanel,
        noData
    },
    data() {
        return {
            link: '',
            page: 1,
            page_size: 20,
            loading: false,
            finished: false,
            List: [],
            newsPanelModal: false,
        }
    },
    mounted() {
        this.getNewsList()
    },
    methods: {
        showNews(index) {
            this.$refs.textshow[index].innerText = this.List[index].show ? this.$t('key271') : this.$t('key272')
            this.$refs.showText[index].className = this.List[index].show ? 'subtitle show' : 'subtitle'
            this.List[index].show = !this.List[index].show;
        },
        reset() {
            this.page = 1
            this.loading = false
            this.finished = false
            this.List = []
        },
        getNewsListXiala() {
            this.reset()
            this.getNewsList()
        },
        getNewsList() {
            this.loading = true
            getNewsList({ page: this.page, page_size: this.page_size }).then(res => {
                let data = res.data
                this.loading = false
                if (data.code == 1) {
                    data.data.list.forEach(element => {
                        element['show'] = false
                    });
                    this.List.push(...data.data.list)
                    this.page++
                }
                if (this.List.length >= data.data.count) this.finished = true
            })
            
        },
        changenewsPanelModal(bool, link) {
            this.link = link
            this.newsPanelModal = bool
        },
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
/*! _variables.scss | Vuero | Css ninja 2020-2021 */
.navbar {
    padding: 0px .34rem;
    min-height: 1.25rem;
    height: 0
}

.navbar .container {
    min-height: 1.25rem
}

.navbar-brand {
    min-height: 1.25rem;
    display: flex
}

.news-container{
    line-height: normal
}

.news-container .news-title{
    font-size: .4rem;
    font-weight: 500;
    display: flex;
    justify-content: space-between;
    align-items: center
}

.news-container .news-title .more{
    color: #1652f0;
    font-weight: 700;
    font-size: .32rem
}

.news-container .news-content{
    margin-top: .4rem
}

.news-container .news-content .news-item{
    margin-bottom: .76rem;
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: flex-start
}

.news-container .news-content .news-item .news-info{
    width: 72%
}

.news-container .news-content .news-item .fs-26{
    font-size: .26rem;
    color: #5f6775
}

.news-container .news-content .news-item .date{
    color: #f68d29;
    margin-left: .16rem
}

.news-container .news-content .news-item .subtitle{
    margin: .08rem 0;
    font-weight: 600;
    font-size: .28rem;
    font-family: NunitoBold
}

.news-container .news-content .news-img{
    margin-left: .62rem
}

.news-container .news-content .news-img img{
    width: 1.32rem;
    height: 1.32rem;
    border-radius: .1rem
}

.v-button{
    width: 2.66rem;
    height: .84rem;
    font-size: .28rem;
    border-radius: 14px
}

.load-more-btn{
    margin-top: .6rem;
    text-align: center
}

.nomore-tips{
    color: #353f5280;
    margin-top: .6rem;
    text-align: center
}
</style>
