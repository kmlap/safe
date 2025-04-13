import myaxios from '@/libs/my-request'
import config from '@/config'
import { getToken, localRead } from '@/libs/util'
const HOST = config.Host

// 用户邀请码注册或者授权成功注册
export const create = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/create',
    data
  })
}

// 获取用户信息
export const userinfo = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/userinfo',
    data,
    headers: {
      token: getToken()
    }
  })
}

// 获取平台公共数据
export const configapi = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/index/config',
    data,
    headers: {
      token: getToken()
    }
  })
}

// 用户授权
export const authorize = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/authorize',
    data,
    headers: {
      token: getToken()
    }
  })
}

// 获取用户钱包列表
export const getWalletList = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + '/api/user/getWalletList',
    data,
    headers: {
      token: getToken()
    }
  })
}

// 获取用户钱包详情
export const getWalletDetails = (data) => {
  return myaxios({
    method: 'get',
    url: HOST + '/api/user/getWalletDetails?coin=' + data.coin,
    data,
    headers: {
      token: getToken()
    }
  })
}

// (我的钱包)币种兑换
export const exchangeCoin = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/exchangeCoin',
    data,
    headers: {
      token: getToken()
    }
  })
}

// (我的钱包)用户提现
export const withdraw = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/withdraw',
    data,
    headers: {
      token: getToken()
    }
  })
}

// (我的钱包)获取转账记录
export const getTransferRecord = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + `/api/user/getTransferRecord?coin=${data.coin}&page=${data.page}&page_size=${data.page_size}`,
    data,
    headers: {
      token: getToken()
    }
  })
}

// (我的钱包)用户充值
export const recharge = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/recharge',
    data,
    headers: {
      token: getToken()
    }
  })
}

// (AI量化)获取搬砖产品列表
export const getProductList = (data = {}) => {
  let params = ''
  if (data.product_id) params = '?product_id=' + data.product_id
  return myaxios({
    method: 'get',
    url: HOST + '/api/pledge/getProductList' + params,
    data,
    headers: {
      token: getToken()
    }
  })
}

// (AI量化)托管产品
export const trusteeshipProduct = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/pledge/trusteeshipProduct',
    data,
    headers: {
      token: getToken()
    }
  })
}

// (AI量化)获取托管信息
export const findTrusteeshipInfo = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + '/api/pledge/findTrusteeshipInfo',
    data,
    headers: {
      token: getToken()
    }
  })
}

// (AI量化)获取托管订单列表
export const getOrderList = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + `/api/pledge/getOrderList?status=${data.status}&page=${data.page}&page_size=${data.page_size}`,
    data,
    headers: {
      token: getToken()
    }
  })
}

// (矿机租赁)获取矿机产品列表
export const getMachineProductList = (data = {}) => {
  let params = ''
  if (data.product_id) params = '?product_id=' + data.product_id
  return myaxios({
    method: 'get',
    url: HOST + '/api/machine/getProductList' + params,
    data,
    headers: {
      token: getToken()
    }
  })
}

// (矿机租赁)租赁矿机
export const leaseProduct = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/machine/leaseProduct',
    data,
    headers: {
      token: getToken()
    }
  })
}

// (矿机租赁)获取矿机订单列表
export const getMachineOrderList = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + `/api/machine/getOrderList?status=${data.status}&page=${data.page}&page_size=${data.page_size}`,
    data,
    headers: {
      token: getToken()
    }
  })
}

// (矿机租赁)我的矿机
export const myMachine = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + '/api/machine/myMachine',
    data,
    headers: {
      token: getToken()
    }
  })
}

// (记录与查看详细信息)获取账户与汇率
export const findAccountAndRate = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + '/api/record/findAccountAndRate',
    data,
    headers: {
      token: getToken()
    }
  })
}

// (记录)获取记录列表
export const getRecordList = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + `/api/record/getRecordList?type=${data.type}&page=${data.page}&page_size=${data.page_size}`,
    data,
    headers: {
      token: getToken()
    }
  })
}

// 获取秒合约列表
export const getContractList = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + '/api/contract/getContractList?type=' + data.type,
    data,
    headers: {
      token: getToken()
    }
  })
}

// 获取秒合约详细
export const getContractDetails = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + '/api/contract/getContractDetails?contract_id=' + data.contract_id,
    data,
    headers: {
      token: getToken()
    }
  })
}

// 关注秒合约
export const followContract = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/contract/followContract',
    data,
    headers: {
      token: getToken()
    }
  })
}

// 获取K线数据
export const getKlineData = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + `/api/contract/getKlineData?contract_id=${data.contract_id}&interval=${data.interval}`,
    data,
    headers: {
      token: getToken()
    }
  })
}

// 秒合约下单
export const contractPlaceOrder = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/contract/contractPlaceOrder',
    data,
    headers: {
      token: getToken()
    }
  })
}

// 获取秒合约订单列表
export const getHeyueOrderList = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + `/api/contract/getOrderList?contract_id=${data.contract_id}&status=${data.status}&page=${data.page}&page_size=${data.page_size}`,
    data,
    headers: {
      token: getToken()
    }
  })
}

// 获取秒合约订单状态
export const findOrderStatus = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + `/api/contract/findOrderStatus?order_id=${data.order_id}`,
    data,
    headers: {
      token: getToken()
    }
  })
}

// 获取新闻列表
export const getNewsList = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + `/api/index/getNewsList?page=${data.page}&page_size=${data.page_size}`,
    data,
    headers: {
      token: getToken()
    }
  })
}

// 获取公告
export const findNotice = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + `/api/index/findNotice?type=${data.type}`,
    data,
    headers: {
      token: getToken()
    }
  })
}
export const c2clist = (data) => { 
  return myaxios({
    method: 'get',
    url: HOST + `/api/user/c2clist?page=${data.page}&page_size=${data.page_size}&status=${data.status}`,
    data,
    headers: {
      token: getToken()
    }
  })
}

export const suc2c = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/suc2c',
    data,
    headers: {
      token: getToken()
    }
  })
}
export const c2c = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/c2c',
    data,
    headers: {
      token: getToken()
    }
  })
}

export const noc2c = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/noc2c',
    data,
    headers: {
      token: getToken()
    }
  })
}

export const authentication = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/authentication',
    data,
    headers: {
      token: getToken()
    }
  })
}


export const authentication1 = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/authentication1',
    data,
    headers: {
      token: getToken()
    }
  })
}


export const authentication2 = (data) => { 
  return myaxios({
    method: 'post',
    url: HOST + '/api/user/authentication2',
    data,
    headers: {
      token: getToken()
    }
  })
}