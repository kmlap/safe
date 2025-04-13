import Vue from 'vue'
import VueI18n from 'vue-i18n'
import { localRead, localSave } from '@/libs/util'
import zhTW from './lang/zhTW'
// import en from './lang/en'
// import cn from './lang/cn'
// import fr from './lang/fr'
// import ja from './lang/ja'
// import it from './lang/it'
// import de from './lang/de'
// import sv from './lang/sv'
Vue.use(VueI18n)

// let lang = localRead('local') || 'zh-TW'
let lang = 'zh-TW'
localSave('local', lang)
Vue.config.lang = lang

// vue-i18n 6.x+写法
// Vue.locale = () => {}
const messages = {
  'zh-TW': zhTW, // 繁体中文
  // 'en': en, // 英文
  // 'cn': cn, // 简体中文
  // 'fr': fr, // 法语
  // 'ja': ja, // 日语
  // 'it': it, // 意大利语
  // 'de': de, // 德语
  // 'sv': sv // 瑞典语
}
const i18n = new VueI18n({
  locale: lang,
  messages
})

export default i18n
