<template>
    <div class="wallet-auth">
      <button
        v-if="!isConnected"
        @click="connectWallet"
        :disabled="isLoading"
      >
        {{ isLoading ? '连接中...' : '连接 Trust Wallet' }}
      </button>
      
      <div v-else class="wallet-info">
        <span>{{ truncatedAddress }}</span>
        <button @click="disconnect">断开连接</button>
      </div>
      
      <p v-if="error" class="error-message">{{ error }}</p>
    </div>
  </template>
  
  <script>
  import { initWeb3, getCurrentAccount } from '@/libs/web3';
  
  export default {
    name: 'WalletAuth',
    data() {
      return {
        isLoading: false,
        isConnected: false,
        currentAccount: '',
        error: ''
      };
    },
    computed: {
      truncatedAddress() {
        if (!this.currentAccount) return '';
        const start = this.currentAccount.substring(0, 6);
        const end = this.currentAccount.substring(38);
        return `${start}...${end}`;
      }
    },
    methods: {
      async connectWallet() {
        this.isLoading = true;
        this.error = '';
        
        try {
          await initWeb3();
          const account = await getCurrentAccount();
          
          if (!account) {
            throw new Error('未获取到账户');
          }
          
          this.currentAccount = account;
          this.isConnected = true;
          
          // 设置事件监听
          this.setupEventListeners();
          
          // 通知父组件
          this.$emit('connected', account);
        } catch (err) {
          console.error('钱包连接失败:', err);
          this.error = err.message;
          this.$emit('error', err);
        } finally {
          this.isLoading = false;
        }
      },
      
      setupEventListeners() {
        if (!window.ethereum) return;
        
        // 账户变更事件
        window.ethereum.on('accountsChanged', (accounts) => {
          if (accounts.length === 0) {
            this.disconnect();
          } else {
            this.currentAccount = accounts[0];
            this.$emit('accountChanged', this.currentAccount);
          }
        });
        
        // 网络变更事件
        window.ethereum.on('chainChanged', (chainId) => {
          this.$emit('chainChanged', chainId);
          window.location.reload();
        });
      },
      
      disconnect() {
        this.isConnected = false;
        this.currentAccount = '';
        
        if (window.ethereum && window.ethereum.removeListener) {
          window.ethereum.removeListener('accountsChanged', this.handleAccountsChanged);
          window.ethereum.removeListener('chainChanged', this.handleChainChanged);
        }
        
        this.$emit('disconnected');
      },
      
      async signMessage(message) {
        try {
          const signature = await signMessage(message, this.currentAccount);
          this.$emit('signed', { message, signature });
          return signature;
        } catch (err) {
          console.error('签名失败:', err);
          this.$emit('signError', err);
          throw err;
        }
      }
    },
    
    async created() {
      // 检查是否已连接
      if (window.ethereum && window.ethereum.selectedAddress) {
        try {
          await this.connectWallet();
        } catch (err) {
          console.log('自动连接失败:', err);
        }
      }
    },
    
    beforeDestroy() {
      // 清理事件监听
      if (window.ethereum && window.ethereum.removeListener) {
        window.ethereum.removeListener('accountsChanged', this.handleAccountsChanged);
        window.ethereum.removeListener('chainChanged', this.handleChainChanged);
      }
    }
  };
  </script>
  
  <style scoped>
  .wallet-auth {
    margin: 1rem 0;
  }
  
  button {
    padding: 0.5rem 1rem;
    background-color: #3182ce;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
  }
  
  button:disabled {
    background-color: #a0aec0;
    cursor: not-allowed;
  }
  
  .wallet-info {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .error-message {
    color: #e53e3e;
    margin-top: 0.5rem;
  }
  </style>