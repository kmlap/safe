import Axios from 'axios'
import Cookies from 'js-cookie'
import { TOKEN_KEY, localRead } from '@/libs/util'

const service = Axios.create()

//请求拦截器
service.interceptors.request.use((config) => {
  // console.log(config)
  config.headers['lang'] = localRead('local') || 'en'
  return config
})

// 添加响应拦截器
service.interceptors.response.use((response) => {
  // 对响应数据做点什么
  return response
}, (error) => {
  // 对响应错误做点什么
  if (error.response.status === 401) {
    // Cookies.remove(TOKEN_KEY)
    // window.location.href = '/'
    // Message.error('未登录，或登录失效，请登录')
  } if (error.response.status === 500) {
    // Message.error(error.response.data.ErrorInfo)
    // alert('Server error')
  }else {
    return Promise.reject(error)
  }


})

// service.defaults.headers.common['Accept'] = '*/*'

export default service
