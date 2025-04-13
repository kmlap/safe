import Vue from 'vue'
import Router from 'vue-router'
import routes from './routers'
import store from '@/store'
import { setToken, getToken, canTurnTo } from '@/libs/util'
import config from '@/config'
import { recursiveRouter } from '@/libs/common'
import $ from 'jquery'
const { homeName } = config


// 判断手机还是电脑
// if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
//   window.location.href = "http://jsd.sshyedu.com/"
// }

Vue.use(Router)
const router = new Router({
  routes,
  mode: 'history'
})
const LOGIN_PAGE_NAME = 'login'

let registerRouteFresh = true

const turnTo = (to, access, next) => {
  if (canTurnTo(to.name, access, routes)) {
    next() // 有权限，可访问
  } else {
    next({ replace: true, name: 'error_401' }) // 无权限，重定向到401页面
  }
}

const pushRouter = () => {
  if (registerRouteFresh) {
    let routerData = recursiveRouter(store.state.user.permission)
    for (let i = 0; i < routerData.length; i++) {
      store.commit('setHttpRouters', routerData[i])
      // store.state.app.httpRouters.push(routerData[i])
    }
    router.addRoutes(routerData)
    registerRouteFresh = false
  }
}

const commitUserInfo = (data) => {
  store.commit('setUserMobile', data.content.userInfo.userMobile)
  store.commit('setEmaliAccount', data.content.userInfo.emaliAccount)
  store.commit('setUserName', data.content.userInfo.userName)
  store.commit('setUserId', data.content.userInfo.Id)
  store.commit('permission', data.content.permission)
  store.commit('permissionBtn', data.content.permissionBtn)
  store.commit('setAccess', ['admin'])
  store.commit('setHasGetInfo', true)
}

// if (getToken()) {
//   $.ajax({
//     url: '/api/Login/GetUserInfoApiPermission',
//     type: 'get',
//     async: false,
//     beforeSend: function (xhr) {
//       xhr.setRequestHeader('Authorization', getToken())
//     },
//     success: (res) => {
//       let data = res
//       if (!data.success) alert(data.message)
//       commitUserInfo(data)
//       pushRouter()
//     }
//   })
// }

// router.beforeEach((to, from, next) => {
//   const token = getToken()
//   if (!token && to.name !== LOGIN_PAGE_NAME) {
//     // 未登录且要跳转的页面不是登录页
//     next({
//       name: LOGIN_PAGE_NAME // 跳转到登录页
//     })
//   } else if (!token && to.name === LOGIN_PAGE_NAME) {
//     // 未登陆且要跳转的页面是登录页
//     next() // 跳转
//   } else if (token && to.name === LOGIN_PAGE_NAME) {
//     // 已登录且要跳转的页面是登录页
//     next({
//       name: homeName // 跳转到homeName页
//     })
//   } else {
//     if (store.state.user.hasGetInfo) {
//       turnTo(to, store.state.user.access, next)
//     } else {
//       store.dispatch('getUserInfo').then(user => {
//         if (user.success) {
//           pushRouter()
//           // 拉取用户信息，通过用户权限和跳转的页面的name来判断是否有权限访问;access必须是一个数组，如：['super_admin'] ['super_admin', 'admin']
//           turnTo(to, user.access, next)
//         } else {
//           alert(user.message)
//           setToken('')
//           next({
//             name: 'login'
//           })
//         }
//       })
//     }
//   }
// })

router.afterEach(to => {
  // iView.LoadingBar.finish()
  window.scrollTo(0, 0)
})

export default router
