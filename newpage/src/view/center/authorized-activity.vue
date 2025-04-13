<template>
     <div class="arbitrage_record" >
      <header-nav :backIconType="2" :title="$t('key79') + $t('key175')"></header-nav>
      <div class="exchange-container" >
          <div class="base-content" >
              <div class="base-title" >
                  <div class="left-line" ></div>
              </div>
              <div class="base-value"  style="justify-content: center; margin-top: 0rem;">
                  <div class="value-item" >
                      <div class="value-title" >{{$t('key70')}}</div>
                      <div class="value ff_InterRegular" >{{info.usdt_balance}} USDT </div>
                  </div>
              </div>
              <div class="base-value" >
                  <div class="value-item" >
                      <div class="value-title" >{{$t('key71')}}</div>
                      <div class="value ff_InterRegular" >{{info.total_produce}} ETH </div>
                  </div>
                  <div class="value-item" >
                      <div class="value-title" >{{$t('key72')}}</div>
                      <div class="value ff_InterRegular" >{{info.day_produce}} ETH </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="list-container" >
          <div class="list-title" >{{$t('key73')}}</div>
          <van-pull-refresh v-model="loading" @refresh="getRecordListXiala" pulling-text="Pull down to refresh.." loosing-text="Release to refresh..." loading-text="loading...">
              <van-list v-model="loading" :finished="finished" loading-text="loading..." @load="getRecordList">
              <div class="record_list"  style="padding: 0px;">
                  <div class="record_item"  v-for="(item, index) in List" :key="index">
                      <div class="pro_info" >
                          <div class="pro_cycle ff_NunitoSemiBold" >
                              <img :src="item.icon" class="icon_save" >
                              <span class="fs-32" >
                                  <span class="" :class="[Number(item.amount) > 0 ? 'ygreen' : 'yred']" >{{item.amount}}</span> {{item.coin}}
                              </span>
                          </div>
                      </div>
                      <div class="order_info" >
                          <div >{{item.create_time}}</div>
                          <!-- <div class="order_status" >
                              <button class="button_status"  v-if="item.type === 7">{{$t('key74')}}</button>
                              <button class="button_status"  v-if="item.type === 8">{{$t('key75')}}</button>
                              <button class="button_status"  v-if="item.type === 10">{{$t('key76')}}</button>
                              <button class="button_status"  v-if="item.type === 11">{{$t('key77')}}</button>
                              <button class="button_status"  v-if="item.type === 12">{{$t('key78')}}</button>
                          </div> -->
                      </div>
                  </div>
                  <!---->
              </div>
              </van-list>
              <no-data v-if="List.length <= 0"></no-data>
          </van-pull-refresh>
      </div>
  </div>
</template>

<script>
import headerNav from '@/components/header-nav.vue'
import NoData from '@/components/no-data.vue'
import { getRecordList, findAccountAndRate } from '@/api/user'

export default {
    name: 'authorized-activity',
    props: {
    },
    components: {
        headerNav,
        NoData
    },
    data() {
        return {
            page: 1,
            page_size: 20,
            loading: false,
            finished: false,
            List: [],
            info: {}
        }
    },
    mounted() {
        this.findAccountAndRate()
        this.getRecordList()
    },
    methods: {
        findAccountAndRate () {
            findAccountAndRate().then(res => {
                let data = res.data
                if (data.code === 1) {
                    this.info = data.data
                }
            })
        },
        reset () {
            this.page = 1
            this.loading = false
            this.finished = false
            this.List = []
        },
        getRecordListXiala () {
            this.reset()
            this.getRecordList()
        },
        getRecordList () {
            this.loading = true
            getRecordList({ page: this.page, page_size: this.page_size, type: 2 }).then(res => {
                let data = res.data
                this.loading = false
                if (data.code == 1) {
                    this.List.push(...data.data.list)
                    this.page++
                }
                if (this.List.length >= data.data.count) this.finished = true
            })
        },
    }
}
</script>

<style scoped>
.arbitrage_record {
    padding-bottom: .6rem;
    font-weight: 500
}

.arbitrage_record .header {
    position: relative;
    padding: .27rem .44rem;
    text-align: center
}

.arbitrage_record .header .back {
    float: left;
    font-size: .4rem
}

.arbitrage_record .switch_container {
    margin-top: .32rem
}

.arbitrage_record .switch_container .switch_content {
    margin: 0 auto;
    padding: .08rem;
    width: 4.44rem;
    background: #f5f6f8;
    border-radius: .2rem;
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center
}

.arbitrage_record .switch_container .switch_content .switch_item {
    width: 2.14rem;
    height: .72rem;
    line-height: .72rem;
    text-align: center;
    font-size: .26rem;
    color: #353f52;
    font-weight: 600
}

.arbitrage_record .switch_container .switch_content .switch_item.active {
    background: #fff;
    border-radius: .2rem
}

.arbitrage_record .record_list {
    padding: 0 .36rem
}

.arbitrage_record .record_list .record_item {
    margin-top: .56rem;
    background: #fff;
    box-shadow: .06rem .12rem .2rem #0000000d;
    border-radius: .3rem
}

.arbitrage_record .record_list .record_item .pro_info {
    padding: 0 .32rem;
    height: 1.14rem;
    border-bottom: 1px solid rgba(216, 216, 216, .5);
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center
}

.arbitrage_record .record_list .record_item .pro_info .pro_cycle {
    display: flex;
    align-content: center;
    align-items: center;
    font-size: .36rem;
    color: #353f52
}

.arbitrage_record .record_list .record_item .pro_info .pro_cycle .icon_save {
    margin-right: .2rem;
    width: .48rem
}

.arbitrage_record .record_list .record_item .record_info .section {
    padding: 0 !important;
    margin-top: .32rem
}

.arbitrage_record .record_list .record_item .record_info .section .section_item {
    display: flex;
    justify-content: space-between;
    padding: 0 .32rem;
    align-content: center;
    align-items: center;
    margin-bottom: .32rem
}

.arbitrage_record .record_list .record_item .record_info .section .section_item .item_name {
    color: #353f52
}

.arbitrage_record .record_list .record_item .record_info .section .section_item .item_value {
    color: #5b616e
}

.arbitrage_record .record_list .record_item .order_info {
    padding: 0 .4rem;
    height: 1.04rem;
    background: #f5f6f8;
    box-shadow: 6px 12px 20px #0000000d;
    border-radius: 0 0 .3rem .3rem;
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center
}

.arbitrage_record .record_list .record_item .order_info .order_status {
    display: flex;
    align-content: center;
    align-items: center
}

.arbitrage_record .record_list .record_item .order_info .order_status .button_status {
    padding: 0 .4rem;
    display: inline-block;
    height: .65rem;
    line-height: .65rem;
    background: #1652f0;
    border-radius: .2rem;
    border: none;
    color: #fff
}

.arbitrage_record .no_data_content {
    margin-top: .4rem;
    padding-bottom: .4rem;
    text-align: center
}

.arbitrage_record .no_data_content .title {
    color: #aaa;
    font-size: .3rem
}

.arbitrage_record .no_data_content .img_no_data {
    margin-bottom: .56rem;
    width: 3.7rem;
    height: auto
}

.arbitrage_record .load-more {
    margin: 0 auto;
    margin-top: .32rem;
    text-align: center;
    color: #1652f0
}

.arbitrage_record .no-more-data {
    margin: 0 auto;
    margin-top: .32rem;
    text-align: center
}

/*! _variables.scss | Vuero | Css ninja 2020-2021 */
 .van-loading {
    text-align: center;
    margin-top: .5rem
}

.exchange-container {
    margin-top: .4rem;
    padding: 0 .32rem;
    line-height: normal
}

.exchange-container .base-content {
    padding: .24rem .32rem .46rem;
    background: #fff;
    box-shadow: 0 4px 40px 1px #00000008;
    border-radius: 10px;
    border: 1px solid #eeeef0
}

.exchange-container .base-content .base-title {
    position: relative;
    font-size: .28rem;
    color: #353f52;
    font-weight: 500
}

.exchange-container .base-content .base-title .left-line {
    position: absolute;
    top: 0;
    left: -.32rem;
    width: .08rem;
    height: .4rem;
    background: linear-gradient(1turn, rgba(90, 71, 217, .09), #1652f0);
    border-radius: .04rem
}

.exchange-container .base-content .base-value {
    margin-top: .44rem;
    display: flex;
    justify-content: space-between;
    text-align: center
}

.exchange-container .base-content .base-value .value-item {
    width: 50%
}

.exchange-container .base-content .base-value .value-item .value-title {
    margin-bottom: .16rem;
    font-size: .28rem;
    color: #353f52;
    font-weight: 500
}

.exchange-container .base-content .base-value .value-item .value {
    color: #5b616e;
    font-weight: 400
}

.list-container {
    margin-top: .56rem;
    padding: 0 .32rem
}

.list-container .list-title {
    margin-bottom: .24rem;
    color: #353f52;
    font-size: .32rem;
    font-weight: 600
}

.arbitrage_record .popup_content .ensure_popup {
    width: 6.42rem;
    background: #fff;
    border-radius: .2rem;
    z-index: 2
}

.arbitrage_record .popup_content .ensure_popup .ensure_content {
    padding: .44rem;
    text-align: center
}

.arbitrage_record .popup_content .ensure_popup .ensure_content .icon_ensure {
    width: 1.26rem
}

.arbitrage_record .popup_content .ensure_popup .ensure_content .amount_info {
    margin-top: .56rem
}

.arbitrage_record .popup_content .ensure_popup .ensure_content .confirm {
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

.arbitrage_record .popup_content .ensure_popup .ensure_content .tips {
    margin-top: .4rem;
    font-size: .24rem;
    color: #5b616e80
}

.load-more-btn {
    margin-top: .6rem;
    text-align: center
}

.nomore-tips {
    color: #353f5280;
    margin-top: .6rem;
    text-align: center
}

.fc-13B26F {
    color: #13b26f
}

.fc-CF202F {
    color: #cf202f
}
</style>

