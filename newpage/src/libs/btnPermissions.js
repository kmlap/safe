import Vue from 'vue'
import store from '../store'

// 权限指令
const has = Vue.directive('has', {
  // 之前是bind
  inserted: function (el, binding, vnode) {
    // 获取按钮权限
    if (!Vue.prototype.$_has(binding.value)) {
      el.parentNode.removeChild(el)
    }
  }
})
// 权限检查方法
Vue.prototype.$_has = function (value) {
  let isExist = false
  let buttonperms = store.state.user.permissionBtn // store.state.user.userName
  if (buttonperms === undefined || buttonperms === null) {
    return false
  }

  for (let i = 0; i < buttonperms.length; i++) {
    if (buttonperms[i].indexOf(value) > -1) {
      isExist = true
      break
    }
  }
  return isExist
}

export default { has }
