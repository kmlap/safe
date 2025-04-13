import Web3 from 'web3';

let web3Instance = null;

export const initWeb3 = async () => {
  if (window.ethereum) {
    try {
      // 请求账户授权
      await window.ethereum.request({ method: 'eth_requestAccounts' });
      
      // 创建 Web3 实例
      web3Instance = new Web3(window.ethereum);
      
      // 检测是否是 Trust Wallet
      if (window.ethereum.isTrust) {
        console.log('Trust Wallet 已连接');
      }
      
      return web3Instance;
    } catch (error) {
      console.error('用户拒绝授权:', error);
      throw new Error('用户拒绝了钱包授权请求');
    }
  } else if (window.web3) {
    // 兼容老版本 MetaMask
    web3Instance = new Web3(window.web3.currentProvider);
    return web3Instance;
  } else {
    // console.log('未检测到以太坊钱包，请安装 Trust Wallet 或 MetaMask');
    // return false;
    throw new Error('未检测到以太坊钱包，请安装 Trust Wallet 或 MetaMask');
  }
};

export const getWeb3 = () => {
  if (!web3Instance) {
    throw new Error('Web3 尚未初始化');
  }
  return web3Instance;
};

export const getCurrentAccount = async () => {
  const web3 = getWeb3();
  const accounts = await web3.eth.getAccounts();
  return accounts[0] || null;
};

export const signMessage = async (message, account) => {
  const web3 = getWeb3();
  return await web3.eth.personal.sign(message, account, '');
};