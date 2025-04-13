<template>
  <div>
    <button @click="openDApp">打开 DApp</button>
    <button @click="transferToken">转账 EOS</button>
  </div>
</template>

<script>
export default {
  methods: {
    openDApp() {
      console.log('aa');
      if (!window.ethereum) {
        const deepLink = 'tpoutside://open?param=' + encodeURIComponent(
          JSON.stringify({
            action: 'openDApp',
            url: window.location.href,
          })
        );
        window.location.href = deepLink;

        // 如果未安装，跳转下载页
        setTimeout(() => {
          if (!document.hidden) {
            window.open('https://www.tokenpocket.pro/en/download/app', '_blank');
          }
        }, 2000);
      }
    },

    transferToken() {
      const params = {
        action: "transfer",
        symbol: "EOS",
        to: "recipient_account",
        amount: "1.0000",
        memo: "test",
      }; a
      this.jumpToTokenPocket(params);
    },

    jumpToTokenPocket(params) {
      const encodedParams = encodeURIComponent(JSON.stringify(params));
      const deepLink = `tpoutside://open?param=${encodedParams}`;
      window.location.href = deepLink;

      // 如果未安装，跳转下载页
      setTimeout(() => {
        if (!document.hidden) {
          window.location.href = "https://www.tokenpocket.pro/en/download/app";
        }
      }, 2000);
    },
  },
};
</script>