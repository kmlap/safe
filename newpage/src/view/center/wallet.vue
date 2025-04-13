<template>
     <div class="account" >
      <div class="header" >
          <img src="../../assets/static/image/icon_back_account.bddc267e.286ce987.svg" class="back" @click="back" >
      </div>
      <div class="title_container" >
          <div class="title_info" >
              <div class="title fs-40 ff_NunitoBold" >{{$t('key99')}}</div>
              <div class="subtitle fs-26 fc-5B616E ff_NunitoSemiBold" >{{$t('key100')}}</div>
          </div>
          <img src="../../assets/static/image/img_wallet.e04efaed.062eb968.png" class="title_img" >
      </div>
      <div class="wallte_select" >
          <div class="title" >
              <div class="select_line" ></div>
              <div class="value fs-26 fc-5B616E ff_NunitoSemiBold" >{{$t('key101')}}</div>
          </div>
          <div class="wallet_list" >
              <div class="wallet_item"  v-for="(item, index) in list" :key="index" @click="go('/wallet-info', { coin: item.coin })">
                  <div class="item_info" >
                      <img :src="item.icon" class="icon" >
                      <div class="info" >
                          <div class="title fs-32 fc-353F52 ff_NunitoBold" >
                              <span class="uppercase" >{{item.coin}}</span>
                              <!-- &nbsp;{{$t('key79')}} -->
                          </div>
                          <div class="subtitle fs-26 fc-5B616E ff_NunitoSemiBold" >
                              <span class="uppercase" >{{item.coin}}</span>&nbsp;Coin
                          </div>
                      </div>
                  </div>
                  <div class="item_value" >
                      <div class="title fs-32 fc-353F52 ff_InterSemiBold" > $&nbsp;{{item.balance}}</div>
                      <div class="subtitle fs-26 fc-5B616E ff_InterMedium" >{{item.convert_usdt}}&nbsp;<span class="uppercase" >{{item.coin}}</span>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
import { getWalletList } from '@/api/user'
export default {
    name: 'wallet',
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
        this.getWalletList()
    },
    methods: {
        getWalletList () {
            getWalletList().then(res => {
                let data = res.data
                if (data.code == 1) {
                    this.list = data.data
                }
            })
        },
        back () {
            this.$router.back()
        },
        go (path, query) {
            this.$router.push({ path, query })
        }
    }
}
</script>

<style scoped>
.account {
    font-weight: 500
}

.title {
    line-height: normal !important
}

.account .uppercase {
    text-transform: uppercase
}

.account .header {
    padding: .24rem 0 0 .4rem
}

.account .header .back {
    width: .6rem
}

.account .title_container {
    margin-top: .32rem;
    padding: 0 .4rem;
    display: flex;
    align-content: center;
    align-items: center;
    justify-content: space-between
}

.account .title_container .title_info .title {
    margin-bottom: 0 !important
}

.account .title_container .title_info .subtitle {
    margin-top: .12rem
}

.account .title_container .title_img {
    width: 1.64rem;
    height: auto
}

.account .wallte_select .title {
    margin-top: .24rem;
    position: relative;
    margin-bottom: 0 !important;
    font-weight: 500 !important
}

.account .wallte_select .title .value {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    margin: auto;
    background: #fff;
    height: .36rem;
    padding: 0 .12rem 0 .4rem
}

.account .wallte_select .title .select_line {
    position: relative;
    top: .1rem;
    width: 100%;
    height: .02rem;
    background: rgba(216, 216, 216, .5)
}

.account .wallte_select .wallet_list .wallet_item {
    margin-top: .38rem;
    padding: 0 .4rem .38rem;
    border-bottom: 1px solid rgba(216, 216, 216, .5);
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center
}

.account .wallte_select .wallet_list .wallet_item .title {
    font-weight: 600
}

.account .wallte_select .wallet_list .wallet_item .item_info {
    display: flex;
    align-content: center;
    align-items: center
}

.account .wallte_select .wallet_list .wallet_item .item_info .icon {
    margin-right: .32rem;
    width: .64rem;
    height: .64rem;
    border-radius: 50%
}

.account .wallte_select .wallet_list .wallet_item .item_info .info .subtitle {
    margin-top: .08rem
}

.account .wallte_select .wallet_list .wallet_item .item_value {
    text-align: right
}

.account .wallte_select .wallet_list .wallet_item .item_value .subtitle {
    margin-top: .1rem
}
</style>

