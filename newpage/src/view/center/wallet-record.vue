<template>
    <div class="record">
      <header-nav :backIconType="2" :title="$t('key98')"></header-nav>
      <div class="list_container" >
          <van-pull-refresh v-model="loading" @refresh="getTransferRecordXiala" pulling-text="Pull down to refresh.." loosing-text="Release to refresh..." loading-text="loading...">
              <van-list v-model="loading" :finished="finished" loading-text="loading..." @load="getTransferRecord">
                  <div class="list_item"  v-for="(item, index) in List" :key="index">
                      <div class="info" >
                          <div class="titles" >{{item.title}}
                              <!-- &nbsp;<span class="uppercase"
                                  >usdt</span> -->
                          </div>
                          <div class="subtitles" >
                              <!---->
                              <!---->
                          </div>
                      </div>
                      <div class="value" >
                          <div class="amount ygreen" :class="[Number(item.amount) > 0 ? 'green' : 'yred']" >{{item.amount}}</div>
                          <div class="status" >{{item.create_time}}</div>
                      </div>
                  </div>
              </van-list>
              <no-data v-if="List.length <= 0"></no-data>
          </van-pull-refresh>
      </div>
  </div>
</template>
  
<script>
import headerNav from '@/components/header-nav.vue'
import noData from '@/components/no-data.vue'
import { getTransferRecord } from '@/api/user'

export default {
    name: 'airecord',
    props: {
    },
    components: {
        noData,
        headerNav
    },
    data() {
        return {
            page: 1,
            page_size: 20,
            loading: false,
            finished: false,
            List: [],
            coin: ''
        }
    },
    mounted() {
        this.coin = this.$route.query.coin
        this.getTransferRecord()
    },
    methods: {
        reset () {
            this.page = 1
            this.loading = false
            this.finished = false
            this.List = []
        },
        getTransferRecordXiala () {
            this.reset()
            this.getTransferRecord()
        },
        getTransferRecord () {
            this.loading = true
            getTransferRecord({ page: this.page, page_size: this.page_size, coin: this.coin }).then(res => {
                let data = res.data
                this.loading = false
                if (data.code == 1) {
                    this.List.push(...data.data.list)
                    this.page++
                }
                if (this.List.length >= data.data.total) this.finished = true
            })
        },
    }
}
</script>
  
<style scoped>
.right-panel-wrapper.is-news .right-panel-head span {
    font-size: .36rem
}

.right-panel-wrapper.is-news .right-panel-head .head-end {
    display: flex;
    align-items: center;
    width: 90px
}

.right-panel-wrapper.is-news .right-panel-body {
    overflow: hidden;
    padding: 0;
    height: calc(100% - 50px) !important
}

.right-panel-wrapper.is-news .right-panel-body .loading-wrapper {
    text-align: center;
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: absolute;
    background: #00000050;
    left: 0;
    top: 0
}

.right-panel-wrapper.is-news .right-panel-body .loading-wrapper .iconify,
.right-panel-wrapper.is-news .right-panel-body .loading-wrapper .data {
    color: var(--light-text)
}

.right-panel-wrapper.is-news .right-panel-body .news-title {
    padding: 16px 0;
    font-weight: 700;
    color: #000;
    font-size: .3rem
}

.right-panel-wrapper.is-news .right-panel-body .news-info {
    font-size: .28rem
}

.right-panel-wrapper.is-news .right-panel-body .news-content {
    height: 100%;
    width: 100%;
    margin: 0;
    overflow: auto
}

.record {
    padding-bottom: .6rem
}

.record .header {
    position: relative;
    padding: .32rem .4rem;
    text-align: center
}

.record .uppercase {
    text-transform: uppercase
}

.record .header .back {
    /* position: absolute; */
    top: 0;
    bottom: 0;
    left: .4rem;
    margin: auto;
    width: .32rem
}

.record .list_container {
    margin-top: .32rem;
    padding: 0 .32rem
}

.record .list_container .list_item {
    padding: .32rem;
    margin-top: .4rem;
    box-shadow: 0 4px 40px 1px #00000008;
    border-radius: .1rem;
    border: 1px solid #eeeef0;
    display: flex;
    justify-content: space-between
}

.record .list_container .list_item:first-child {
    margin-top: .32rem
}

.record .list_container .list_item .info .titles {
    color: #434343;
    font-size: .32rem
}

.record .list_container .list_item .info .subtitles .button_status {
    margin-right: .2rem;
    font-size: .2rem;
    font-weight: 600;
    border: 1px solid #aaa;
    padding: .05rem;
    border-radius: .1rem;
    width: 1rem;
    display: inline-block;
    text-align: center;
    align-content: center;
    color: #fff
}

.record .list_container .list_item .info .subtitles .button_status.success {
    background-color: var(--rise-color)
}

.record .list_container .list_item .info .subtitles .button_status.pending {
    background-color: var(--primary)
}

.record .list_container .list_item .info .subtitles {
    margin-top: .26rem;
    color: #5b616e
}

.record .list_container .list_item .value {
    text-align: right
}

.record .list_container .list_item .value .amount {
    font-size: .32rem;
    font-weight: 600
}

.record .list_container .list_item .value .amount.receive {
    color: var(--rise-color)
}

.record .list_container .list_item .value .amount.withdraw {
    color: var(--fall-color)
}

.record .list_container .list_item .value .status {
    margin-top: .26rem;
    color: #5b616e;
    display: flex;
    justify-content: flex-end;
    align-content: center;
    align-items: center
}

.record .no_data_content {
    margin-top: .4rem;
    padding-bottom: .4rem;
    text-align: center;
    color: #aaa;
    font-size: .4rem
}

.record .no_data_content .img_no_data {
    margin-bottom: .56rem;
    width: 3.7rem;
    height: auto
}

.record .load-more {
    margin: 0 auto;
    margin-top: .32rem;
    text-align: center;
    color: #1652f0
}

.record .no-more-data {
    margin: 0 auto;
    margin-top: .32rem;
    text-align: center;
    color: #aaa
}
</style>
  
  