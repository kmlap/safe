import { userinfo } from '@/api/user'
import { setToken, getToken } from '@/libs/util'

export default {
  state: {
    userInfo: {},
    showNotice: false,
    currency: 'USDT',
    Hdtoken: '',
    walletlistsHome: [],
    walletTotol_price: 0,
    walletObj: {
      web3: null,
      provider: null,
      userAddress: '',
      connected: false,
      chainId: 1,
      networkId: 1
    },
    walletassets: 0,
    configInfo: {
      home: { pool: {} } 
    },
    userinfo: {}
  },
  mutations: {
    setUserInfo (state, userInfo) {
      state.userInfo = userInfo
    },
    setShowNotice (state, showNotice) {
      state.showNotice = showNotice
    },
    setCurrency (state, showNotice) {
      state.currency = showNotice
    },
    setHdToken (state, Hdtoken) {
      state.Hdtoken = Hdtoken
    },
    setHomeWallet (state, data) {
      state.walletlistsHome = data.list
      state.walletTotol_price = data.totol_price
    },
    setWalletObj (state, walletObj) {
      state.walletObj = walletObj
    },
    setWalletassets (state, walletassets) {
      state.walletassets = walletassets
    },
    setConfigInfo (state, configInfo) {
      state.configInfo = configInfo
    },
    setUserInfo (state, userinfo) {
      state.userinfo = userinfo
    },
  },
  actions: {
    setHdTokenA({ commit }, Hdtoken) {
      commit('setHdToken', Hdtoken)
    },
    setWalletObj({ commit }, walletObj) {
      commit('setWalletObj', walletObj)
    },
    setWalletassets({ commit }, walletassets) {
      commit('setWalletassets', walletassets)
    },
    setConfigInfo({ commit }, configInfo) {
      commit('setConfigInfo', configInfo)
    },
    setUserInfo({ commit }, userinfo) {
      commit('setUserInfo', userinfo)
    },
    // 获取用户相关信息
    getuserinfo ({ state, commit }) {
      return new Promise((resolve, reject) => {
        userinfo().then(res => {
          const data = res.data
          if (data.code == 1) {
            commit('setUserInfo', data.data)
          }
          resolve(data)
        })
      })
    },
    // // 获取用户相关信息
    // getCurrency ({ state, commit }) {
    //   return new Promise((resolve, reject) => {
    //     getCurrency(state.token).then(res => {
    //       const data = res.data
    //       if (data.code == 1) {
    //         commit('setCurrency', data.data)
    //       }
    //       resolve(data)
    //     })
    //   })
    // },
    // // 获取钱包页面数据
    // walletlists ({ state, commit }) {
    //   return new Promise((resolve, reject) => {
    //     walletlists().then(res => {
    //       let data = res.data
    //       if (data.code === 1) {
    //         commit('setHomeWallet', data.data)
    //       }
    //       resolve(data)
    //     })
    //   })
    // }
  }
}
