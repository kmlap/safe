<template>
    <div class="arbitrage_record">
      <header-nav :backIconType="2" :title="$t('key57')"></header-nav>
      <tab v-model="tabindex" :title="[$t('key58'), $t('key31')]"></tab>
      <div >
          <div class="record_list" >
              <template v-if="tabindex === 0">
                  <van-pull-refresh v-model="loading" @refresh="getHeyueOrderListXiala" pulling-text="Pull down to refresh.." loosing-text="Release to refresh..." loading-text="loading...">
                      <van-list v-model="loading" :finished="finished" loading-text="loading..." @load="getHeyueOrderList">
                          <div class="record_item"  v-for="(item, index) in List" :key="index">
                              <div class="pro_info" >
                                  <div class="pro_cycle ff_NunitoSemiBold" >
                                      <img :src="item.icon" class="icon_save" >
                                      <span class="uppercase sell-coin" >{{item.coin}}</span>
                                  </div>
                                  <div class="fs-32 ff_NunitoBold" >
                                      <span class="rise-fall"  style="color: var(--rise-color);" v-if="Number(item.increase) > 0">
                                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--fluent" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="fluent:arrow-trending-24-regular" >
                                              <g fill="none">
                                                  <path d="M13.748 5.5h7.554l.1.014l.099.028l.06.026a.72.72 0 0 1 .219.15l.04.044l.044.057l.054.09l.039.09l.019.064l.014.064l.009.095v7.532a.75.75 0 0 1-1.493.102l-.006-.102l-.001-5.695l-7.719 7.72a.75.75 0 0 1-.976.073l-.085-.073l-2.97-2.97l-5.47 5.47a.75.75 0 0 1-1.133-.977l.073-.084l6-6a.75.75 0 0 1 .976-.073l.084.073l2.97 2.97L19.438 7h-5.69a.75.75 0 0 1-.742-.648l-.007-.102a.75.75 0 0 1 .648-.743l.102-.007z" fill="currentColor"></path>
                                              </g>
                                          </svg> {{$t('key45')}}{{$t('key46')}}
                                      </span>
                                      <span class="rise-fall"  style="color: var(--fall-color);" v-if="Number(item.increase) < 0">
                                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--fluent" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="fluent:arrow-trending-24-regular" >
                                              <g fill="none">
                                                  <path d="M13.748 5.5h7.554l.1.014l.099.028l.06.026a.72.72 0 0 1 .219.15l.04.044l.044.057l.054.09l.039.09l.019.064l.014.064l.009.095v7.532a.75.75 0 0 1-1.493.102l-.006-.102l-.001-5.695l-7.719 7.72a.75.75 0 0 1-.976.073l-.085-.073l-2.97-2.97l-5.47 5.47a.75.75 0 0 1-1.133-.977l.073-.084l6-6a.75.75 0 0 1 .976-.073l.084.073l2.97 2.97L19.438 7h-5.69a.75.75 0 0 1-.742-.648l-.007-.102a.75.75 0 0 1 .648-.743l.102-.007z" fill="currentColor"></path>
                                              </g>
                                          </svg> {{$t('key45')}}{{$t('key47')}}
                                      </span>
                                  </div>
                              </div>
                              <div class="record_info" >
                                  <div class="section" >
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key50')}}</div>
                                          <div class="item_value" >{{item.amount}}
                                              <!-- &nbsp;<span class="uppercase" >usdt</span> -->
                                          </div>
                                      </div>
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key61')}}</div>
                                          <div class="item_value" >{{item.buy_price}}</div>
                                      </div>
                                      <!---->
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key62')}}</div>
                                          <div class="item_value" >{{item.second}} S </div>
                                      </div>
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key63')}}</div>
                                          <div class="item_value" >{{item.increase}}%</div>
                                      </div>
                                      <!---->
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key53')}}</div>
                                          <div class="item_value" >{{item.odds}}% </div>
                                      </div>
                                      <!---->
                                  </div>
                              </div>
                              <div class="order-info" >
                                  <div class="current-data" >
                                      <div class="buy-time" >
                                          <span >{{$t('key64')}}</span>
                                          <span class="fs-26" >{{item.buy_time}}</span>
                                      </div>
                                      <div class="count-down" >
                                          <div class="timer" >{{item.countdown}}</div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </van-list>
                      <no-data v-if="List.length <= 0"></no-data>
                  </van-pull-refresh>
              </template>
              <template v-if="tabindex === 1">
                  <van-pull-refresh v-model="loading" @refresh="getHeyueOrderListXiala" pulling-text="Pull down to refresh.." loosing-text="Release to refresh..." loading-text="loading...">
                      <van-list v-model="loading" :finished="finished" loading-text="loading..." @load="getHeyueOrderList">
                          <div class="record_item"  v-for="(item, index) in List" :key="index">
                              <div class="pro_info" >
                                  <div class="pro_cycle ff_NunitoSemiBold" >
                                      <img :src="item.icon" class="icon_save" >
                                      <span class="uppercase sell-coin" >{{item.coin}}</span>
                                  </div>
                                  <div class="fs-32 ff_NunitoBold" >
                                      <span class="rise-fall"  style="color: var(--rise-color);" v-if="Number(item.increase) > 0">
                                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--fluent" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="fluent:arrow-trending-24-regular" >
                                              <g fill="none">
                                                  <path d="M13.748 5.5h7.554l.1.014l.099.028l.06.026a.72.72 0 0 1 .219.15l.04.044l.044.057l.054.09l.039.09l.019.064l.014.064l.009.095v7.532a.75.75 0 0 1-1.493.102l-.006-.102l-.001-5.695l-7.719 7.72a.75.75 0 0 1-.976.073l-.085-.073l-2.97-2.97l-5.47 5.47a.75.75 0 0 1-1.133-.977l.073-.084l6-6a.75.75 0 0 1 .976-.073l.084.073l2.97 2.97L19.438 7h-5.69a.75.75 0 0 1-.742-.648l-.007-.102a.75.75 0 0 1 .648-.743l.102-.007z" fill="currentColor"></path>
                                              </g>
                                          </svg> {{$t('key45')}}{{$t('key46')}}
                                      </span>
                                      <span class="rise-fall"  style="color: var(--fall-color);" v-if="Number(item.increase) < 0">
                                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--fluent" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="fluent:arrow-trending-24-regular" >
                                              <g fill="none">
                                                  <path d="M13.748 5.5h7.554l.1.014l.099.028l.06.026a.72.72 0 0 1 .219.15l.04.044l.044.057l.054.09l.039.09l.019.064l.014.064l.009.095v7.532a.75.75 0 0 1-1.493.102l-.006-.102l-.001-5.695l-7.719 7.72a.75.75 0 0 1-.976.073l-.085-.073l-2.97-2.97l-5.47 5.47a.75.75 0 0 1-1.133-.977l.073-.084l6-6a.75.75 0 0 1 .976-.073l.084.073l2.97 2.97L19.438 7h-5.69a.75.75 0 0 1-.742-.648l-.007-.102a.75.75 0 0 1 .648-.743l.102-.007z" fill="currentColor"></path>
                                              </g>
                                          </svg> {{$t('key45')}}{{$t('key47')}}
                                      </span>
                                  </div>
                              </div>
                              <div class="record_info" >
                                  <div class="section" >
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key50')}}</div>
                                          <div class="item_value" >{{item.amount}}
                                              <!-- &nbsp;<span class="uppercase" >usdt</span> -->
                                          </div>
                                      </div>
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key61')}}</div>
                                          <div class="item_value" >{{item.buy_price}}</div>
                                      </div>
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key65')}}</div>
                                          <div class="item_value" >{{item.sell_price}}</div>
                                      </div>
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key62')}}</div>
                                          <div class="item_value" >{{item.second}} S </div>
                                      </div>
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key63')}}</div>
                                          <div class="item_value" >{{item.increase}}%</div>
                                      </div>
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key66')}}</div>
                                          <div class="item_value" >{{item.incr}}% </div>
                                      </div>
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key53')}}</div>
                                          <div class="item_value" >{{item.odds}}% </div>
                                      </div>
                                      <div class="section_item" >
                                          <div class="item_name" >{{$t('key67')}}</div>
                                          <div class="item_value"  style="color: var(--rise-color);" v-if="item.profit_loss.indexOf('-') == -1">
                                              {{item.profit_loss}}
                                              <!-- <span class="uppercase" >&nbsp;usdt</span> -->
                                          </div>
                                          <div class="item_value"  style="color: var(--fall-color);" v-else>
                                              {{item.profit_loss}}
                                              <!-- <span class="uppercase" >&nbsp;usdt</span> -->
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="order-info" >
                                  <div class="order-data" >
                                      <span >{{$t('key64')}}</span>
                                      <span >{{$t('key68')}}</span>
                                  </div>
                                  <div class="order-data fs-26" >
                                      <span >{{item.buy_time}}</span>
                                      <span >{{item.sell_time}}</span>
                                  </div>
                              </div>
                          </div>
                      </van-list>
                      <no-data v-if="List.length <= 0"></no-data>
                  </van-pull-refresh>
              </template>
          </div>
      </div>
  </div>
</template>
  
<script>
import headerNav from '@/components/header-nav.vue'
import tab from '@/components/tab.vue'
import noData from '@/components/no-data.vue'
import { getHeyueOrderList, findOrderStatus } from '@/api/user'

export default {
    name: 'orders',
    props: {
    },
    components: {
        tab,
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
            contract_id: '',
            tabindex: 0,
            time: []
        }
    },
    watch: {
        tabindex () {
            this.getHeyueOrderListXiala()
            this.chageTime()
        }
    },
    beforeDestroy () {
        this.List.forEach((item, index) => {
            clearInterval(this.time[index])
        })
    },
    mounted() {
        this.contract_id = this.$route.query.contract_id
        this.getHeyueOrderList()
    },
    methods: {
        timeStr (sell_time, index) {
            var date1=new Date(sell_time.replace(/-/g,'/'))    //开始时间
            var date2= this.getLocalTime(-4)    //结束时间
            var date3=date1.getTime()-date2.getTime() //时间差秒
            //计算出相差天数
            var days=Math.floor(date3/(24*3600*1000))

            //计算出小时数
            var leave1=date3%(24*3600*1000)  //计算天数后剩余的毫秒数
            var hours=Math.floor(leave1/(3600*1000))

            //计算相差分钟数
            var leave2=leave1%(3600*1000)        //计算小时数后剩余的毫秒数
            var minutes=Math.floor(leave2/(60*1000))

            //计算相差秒数
            var leave3=leave2%(60*1000)     //计算分钟数后剩余的毫秒数
            var seconds=Math.round(leave3/1000)
            console.log("时间差" + days + "天" + hours + "时" + minutes + "分" + seconds + "秒")
            let countdown = `${minutes >= 10 ? minutes : '0' + minutes} : ${seconds >= 10 ? seconds : '0' + seconds}`
            if (seconds < 0 || minutes == -1) {
                this.$set(this.List[index], "countdown", this.$t('key69'))
                this.findOrderStatus(index)
            } else {
                this.$set(this.List[index], "countdown", countdown)
            }
            
        },
        findOrderStatus (index) {
            findOrderStatus({ order_id: this.List[index].order_id }).then(res => {
                let data = res.data
                if (data.code === 1) {
                    if (data.data.status === 0) {
                        this.List.splice(index, 1)
                        clearInterval(this.time[index])
                    }
                }
            })
        },
        getLocalTime(i) {
            //参数i为时区值数字，比如北京为东八区则输进8,纽约为西5区输入-5
            if (typeof i !== 'number') return;
            var d = new Date();
            //得到1970年一月一日到现在的秒数
            var len = d.getTime();
            //本地时间与GMT时间的时间偏移差
            var offset = d.getTimezoneOffset() * 60000;
            //得到现在的格林尼治时间
            var utcTime = len + offset;
            return new Date(utcTime + 3600000 * i);
        },
        chageTime () {
            this.List.forEach((item, index) => {
                clearInterval(this.time[index])
            })
            if (this.tabindex !== 0) return
            this.List.forEach((item, index) => {
                this.time[index] = setInterval(() => {
                    this.timeStr(item.sell_time, index)
                }, 1000)
            })
        },
        reset () {
            this.page = 1
            this.loading = false
            this.finished = false
            this.List = []
        },
        getHeyueOrderListXiala () {
            this.reset()
            this.getHeyueOrderList()
        },
        getHeyueOrderList () {
            this.loading = true
            getHeyueOrderList({ page: this.page, page_size: this.page_size, contract_id: this.contract_id, status: this.tabindex === 0 ? 1 : 0 }).then(res => {
                let data = res.data
                this.loading = false
                if (data.code == 1) {
                    data.data.list.forEach((item, index) => {
                        data.data.list[index].odds = ((Number(item.odds) - 1) * 100).toFixed(2)
                    })
                    this.List.push(...data.data.list)
                    this.page++
                }
                if (this.List.length >= data.data.count) this.finished = true

                
                this.chageTime()
            })
        },
    }
}
</script>
  
<style>

</style>
  
  