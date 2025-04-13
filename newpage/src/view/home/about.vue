<template>
  <div class="question_answer">
    <header-nav :backIconType="2" :title="$t('key201')"></header-nav>
    <div class="qa-container">
      <van-list @load='findNotice' v-model="loading" :finished="finished" loading-text="loading...">
        <div class="qa-item" v-for="(item, index) in list" :key="index">

          <div class="item-title" @click="changetabindex(index)">
            <div>{{ item.title }}</div>
            <img src="../../assets/static/image/icon_arrow_down.fca20a50.svg"
              :class="[tabindex === index ? 'active' : '']">
          </div>
          <div class="item-content" v-if="tabindex === index" v-html="item.content">
            <!-- <p>{{ item.content }}</p> -->
          </div>
        </div>
      </van-list>
    </div>
  </div>
</template>

<script>
import headerNav from '@/components/header-nav.vue'
import { findNotice } from '@/api/user'
export default {
  name: 'help',
  props: {
  },
  components: {
    headerNav
  },
  data() {
    return {
      tabindex: '',
      list: [],
      loading: false,
      finished: false,
    }
  },
  mounted() {
    this.findNotice()
  },
  methods: {
    findNotice() {
      findNotice({ type: 4 }).then(res => {
        let data = res.data
        this.loading = false
        if (data.code == 1) {
          this.list = data.data
        }

        this.finished = true
      })

    },
    changetabindex(index) {
      if (index === this.tabindex) this.tabindex = ''
      else this.tabindex = index
    }
  }
}
</script>

<style scoped>
.header {
  position: relative;
  padding: .32rem .4rem;
  text-align: center
}

.header .back-btn {
  position: fixed;
  width: .32rem;
  opacity: 0;
  z-index: 99
}

.header .back {
  /* position: absolute; */
  top: 0;
  bottom: 0;
  left: .4rem;
  margin: auto;
  width: .32rem;
  /* rotate: 180deg */
}

.van-loading {
  text-align: center;
  margin-top: .5rem
}

.qa-container {
  margin-top: .4rem;
  padding: 0 .32rem
}

.qa-container .qa-item {
  margin-bottom: .32rem;
  background: #fff;
  box-shadow: 0 .04rem .4rem .01rem #00000008;
  border-radius: .2rem
}

.qa-container .qa-item .item-title {
  position: relative;
  padding: .29rem .32rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: .28rem;
  color: #353f52;
  font-family: NunitoSemiBold;
  word-break: break-all
}

.qa-container .qa-item .item-title img {
  margin-left: .32rem;
  width: .28rem;
  height: .28rem
}

.qa-container .qa-item .item-title img.active {
  rotate: 180deg
}

.qa-container .qa-item .item-title:after {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  margin: auto;
  width: .08rem;
  height: .41rem;
  background: linear-gradient(1turn, rgba(90, 71, 217, .09), #1652f0);
  border-radius: .04rem
}

.qa-container .qa-item .item-content {
  margin-top: .03rem;
  padding: 0 .32rem .32rem;
  color: #333;
  font-size: .24rem;
  font-family: var(--font-alt)
}
</style>
