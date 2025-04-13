
import main from '@/components/main'

/**
 * iview-admin中meta除了原生参数外可配置的参数:
 * meta: {
 *  hideInMenu: (false) 设为true后在左侧菜单不会显示该页面选项
 *  notCache: (false) 设为true后页面不会缓存
 *  access: (null) 可访问该页面的权限数组，当前路由设置的权限会影响子路由
 *  icon: (-) 该页面在左侧菜单、面包屑和标签导航处显示的图标，如果是自定义图标，需要在图标名称前加下划线'_'
 *  beforeCloseName: (-) 设置该字段，则在关闭当前tab页时会去'@/router/before-close.js'里寻找该字段名对应的方法，作为关闭前的钩子函数
 * }
 */

export default [
  {
    path: '/',
    redirect: '/home',
    component: main,
    meta: {
      hideInMenu: true,
      notCache: true
    },
    children: [
      {
        path: '/home',
        name: 'home',
        meta: {
          hideInMenu: true,
          notCache: true,
          icon: 'md-home'
        },
        component: () => import('@/view/home')
      },
    ]
  },
  {
    path: '/business',
    name: 'business',
    meta: {
    },
    component: () => import('@/view/business/index')
  },
  {
    path: '/business/orders',
    name: 'business-orders',
    meta: {
    },
    component: () => import('@/view/business/orders')
  },
  {
    path: '/ai',
    name: 'ai',
    meta: {
    },
    component: () => import('@/view/ai/index')
  },
  {
    path: '/ai/record',
    name: 'record',
    meta: {
    },
    component: () => import('@/view/ai/record')
  },
  {
    path: '/ai/intro',
    name: 'intro',
    meta: {
    },
    component: () => import('@/view/ai/intro')
  },
  {
    path: '/ai/product',
    name: 'product',
    meta: {
    },
    component: () => import('@/view/ai/product')
  },
  {
    path: '/mining',
    name: 'mining',
    meta: {
    },
    component: () => import('@/view/mining/index')
  },
  {
    path: '/mining/intro',
    name: 'mining-intro',
    meta: {
    },
    component: () => import('@/view/mining/intro')
  },
  {
    path: '/mining/record',
    name: 'mining-record',
    meta: {
    },
    component: () => import('@/view/mining/record')
  },
  {
    path: '/mining/mechine-buy',
    name: 'mechine-buy',
    meta: {
    },
    component: () => import('@/view/mining/mechine-buy')
  },
  {
    path: '/share',
    name: 'share',
    meta: {
    },
    component: () => import('@/view/home/share')
  },
  {
    path: '/news-list',
    name: 'news-list',
    meta: {
    },
    component: () => import('@/view/home/news-list')
  },
  {
    path: '/authorized-activity',
    name: 'authorized-activity',
    meta: {
    },
    component: () => import('@/view/center/authorized-activity')
  },
  {
    path: '/wallet',
    name: 'wallet',
    meta: {
    },
    component: () => import('@/view/center/wallet')
  },
  {
    path: '/wallet-info',
    name: 'wallet-info',
    meta: {
    },
    component: () => import('@/view/center/wallet-info')
  },
  {
    path: '/wallet-c2c',
    name: 'wallet-c2c',
    meta: {
    },
    component: () => import('@/view/center/wallet-c2c')
  },
  {
    path: '/wallet-order',
    name: 'wallet-order',
    meta: {
    },
    component: () => import('@/view/center/wallet-order')
  },
  {
    path: '/wallet-status',
    name: 'wallet-status',
    meta: {
    },
    component: () => import('@/view/center/wallet-status')
  },
  {
    path: '/wallet-record',
    name: 'wallet-record',
    meta: {
    },
    component: () => import('@/view/center/wallet-record')
  },
  {
    path: '/miningrecord',
    name: 'miningrecord',
    meta: {
    },
    component: () => import('@/view/mining/miningrecord')
  },
  {
    path: '/help',
    name: 'help',
    meta: {
    },
    component: () => import('@/view/home/help')
  },
  {
    path: '/contact',
    name: 'contact',
    meta: {
    },
    component: () => import('@/view/home/contact')
  },
  {
    path: '/about',
    name: 'about',
    meta: {
    },
    component: () => import('@/view/home/about')
  },
  {
    path: '/language',
    name: 'language',
    meta: {
    },
    component: () => import('@/view/home/language')
  },
  {
    path: '/authentication',
    name: 'authentication',
    meta: {
    },
    component: () => import('@/view/center/authentication')
  },
  {
    path: '/authentication_home',
    name: 'authentication_home',
    meta: {
    },
    component: () => import('@/view/center/authentication_home')
  },
  // {
  //   path: '/aaa',
  //   name: 'aaa',
  //   meta: {
  //   },
  //   component: () => import('@/view/center/index')
  // },
  // {
  //   path: '/bb',
  //   name: 'bb',
  //   meta: {
  //   },
  //   component: () => import('@/view/center/bb')
  // }
]
