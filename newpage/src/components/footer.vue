<template>
<div >
    <div class="van-tabbar__placeholder" style="height: 54.3438px;">
        <div class="van-hairline--top-bottom van-tabbar van-tabbar--fixed">
        <div class="van-tabbar-item" :class="[currPath === '/home' ? 'van-tabbar-item--active' : '']" @click="go('/home')">
            <div class="van-tabbar-item__icon">
            <!---->
            </div>
            <div class="van-tabbar-item__text">
            <div class="iconfont icon-foot1"></div>
            <div class="item-label">{{$t('key1')}}</div>
            </div>
        </div>
        <div class="van-tabbar-item " :class="[currPath === '/robot-run-page' ? 'van-tabbar-item--active' : '']" @click="go('/robot-run-page')">
            <div class="van-tabbar-item__icon">
            <!---->
            </div>
            <div class="van-tabbar-item__text">
            <div class="iconfont icon-foot2"></div>
            <div class="item-label">{{$t('key2')}}</div>
            </div>
        </div>
        <div class="van-tabbar-item" :class="[currPath === '/uc-team' ? 'van-tabbar-item--active' : '']" @click="go('/uc-team')">
            <div class="van-tabbar-item__icon">
            <!---->
            </div>
            <div class="van-tabbar-item__text">
            <div class="iconfont icon-foot3"></div>
            <div class="item-label">{{$t('key3')}}</div>
            </div>
        </div>
        <div class="van-tabbar-item" :class="[currPath === '/ucenter' ? 'van-tabbar-item--active' : '']" @click="go('/ucenter')">
            <div class="van-tabbar-item__icon">
            <!---->
            </div>
            <div class="van-tabbar-item__text">
            <div class="iconfont icon-foot4"></div>
            <div class="item-label">{{$t('key4')}}</div>
            </div>
        </div>
        </div>
    </div>
</div>
</template>

<script>

export default {
  props: {
  },
  components: {
  },
  data () {
    return {
        currPath: '/home'
    }
  },
  watch: {
    '$route' (to) {
      this.currPath = this.$route.path
    },
  },
  mounted () {
    this.currPath = this.$route.path
  },
  methods: {
    go (path) {
      this.$router.push({ path })
    }
  }
}
</script>

<style scoped>
.wallet-img {
  width: 20px;
  height: 20px;
}
</style>

