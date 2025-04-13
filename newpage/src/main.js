// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store'
// import iView from 'view-design'
import i18n from '@/locale'
import config from '@/config'
import importDirective from '@/directive'
import installPlugin from '@/plugin'
import VueJsonp from 'vue-jsonp'
import VueClipboard from 'vue-clipboard2'

import Vant from 'vant'
import 'vant/lib/index.css'


import 'swiper/dist/css/swiper.css'

Vue.use(Vant)
Vue.use(VueClipboard)
import './libs/btnPermissions.js'

// import 'iview/dist/styles/iview.css'
// import './index.less'
// import '@/assets/icons/iconfont.css'

// 实际打包时应该不引入mock
/* eslint-disable */
// if (process.env.NODE_ENV !== 'production') require('@/mock')
Vue.prototype.$echarts = window.echarts

Vue.use(VueJsonp)

// Vue.use(iView, {
//   i18n: (key, value) => i18n.t(key, value)
// })
/**
 * @description 注册admin内置插件
 */
installPlugin(Vue)
/**
 * @description 生产环境关掉提示
 */
Vue.config.productionTip = false
/**
 * @description 全局注册应用配置
 */
Vue.prototype.$config = config
/**
 * 注册指令
 */
importDirective(Vue)

Vue.directive('drag', {
  // 之前是bind
  inserted: function (obj, binding, vnode) {
    const dragNode = obj;
    const distanceTop = obj.distanceTop || 0;
    const distanceLeft = obj.distanceLeft || 0;
    const distanceRight = obj.distanceRight || 0;
    const distanceBottom = obj.distanceBottom || 0;
    //限制最大宽高，不让滑块出去
    let maxW = document.body.clientWidth - dragNode.offsetWidth;
    let maxH = document.body.clientHeight - dragNode.offsetHeight;
    if (distanceRight) {
      maxW = maxW - distanceRight
    }
    if (distanceBottom) {
      maxH = maxH - distanceBottom
    }

    let oL,oT;
    //手指触摸开始，记录div的初始位置
    dragNode.addEventListener('touchstart', function(e) {
      var ev = e || window.event;
      var touch = ev.targetTouches[0];
      oL = touch.clientX - dragNode.offsetLeft;
      oT = touch.clientY - dragNode.offsetTop;
      document.addEventListener("touchmove", defaultEvent, false);
    });
    //触摸中的，位置记录
    dragNode.addEventListener('touchmove', function(e) {
      const ev = e || window.event;
      const touch = ev.targetTouches[0];
      let oLeft = touch.clientX - oL;
      let oTop = touch.clientY - oT;
      if(oLeft < distanceLeft) {
        oLeft = distanceLeft;
      } else if(oLeft >= maxW) {
        oLeft = maxW;
      }
      if(oTop < distanceTop) {
        oTop = distanceTop;
      } else if(oTop >= maxH) {
        oTop = maxH;
      }
      dragNode.style.left = oLeft + 'px';
      dragNode.style.top = oTop + 'px';
    });
    //触摸结束时的处理
    dragNode.addEventListener('touchend', function() {
      document.removeEventListener("touchmove", defaultEvent);
    });
    //阻止默认事件
    function defaultEvent(e) {
      e.preventDefault();
    }


    // pc的拖动
    // let dragBox = el;
    // dragBox.onmousedown = e => {
    //   //算出鼠标相对元素的位置
    //   let disX = e.clientX - dragBox.offsetLeft;
    //   let disY = e.clientY - dragBox.offsetTop;
    //   document.onmousemove = e => {
    //     //用鼠标的位置减去鼠标相对元素的位置，得到元素的位置
    //     //这个 170 是我元素宽的一半
    //     let left = e.clientX - disX + 170;
    //     let top = e.clientY - disY;
    //     //移动当前元素
    //     dragBox.style.left = left + "px";
    //     dragBox.style.top = top + "px";
    //   };
    //   document.onmouseup = e => {
    //     //鼠标弹起来的时候不再移动
    //     document.onmousemove = null;
    //     //预防鼠标弹起来后还会循环（即预防鼠标放上去的时候还会移动）
    //     document.onmouseup = null;
    //   };
    // };
  }
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  i18n,
  store,
  render: h => h(App)
})
