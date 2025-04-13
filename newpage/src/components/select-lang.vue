<template>
    <div class="select-lang" style="display: flex;align-items: center;" v-if="lang">
        <div class="currlang" @click="isshowlang = !isshowlang">
            <span class="currtext">{{ languages[lang].name }}</span>
            <img :src="languages[lang].img" class="currimg" />
        </div>
        <div class="langlist" style="right: 0;top:60upx" v-show="isshowlang">
            <div class="item" v-for="(item, i) in languages" :key="i" @click="changeLang(i)">
                <span class="text">{{ item.name }}</span>
                <img class="img" :src="item.img" />
            </div>
        </div>
    </div>
  </template>
  
<script>
    import { localRead, localSave } from '@/libs/util'
    import hkimg from '@/assets/static/image/lang/hk.png'
    import Arabicimg from '@/assets/static/image/lang/Arabic.png'
    import enimg from '@/assets/static/image/lang/en.png'
    import farsiimg from '@/assets/static/image/lang/farsi.png'
    import Frenchimg from '@/assets/static/image/lang/French.png'
    import Japaneseimg from '@/assets/static/image/lang/Japanese.png'
    import Koreanimg from '@/assets/static/image/lang/Korean.png'

  export default {
      props: {
      },
      components: {
      },
      data() {
          return {
            isshowlang:false,
            languages: {
                // hk: { name: '繁體中文', img: hkimg },
                en: { name: 'English', img: enimg },
                Arabic: { name: 'عربي', img: Arabicimg },
                farsi: { name: 'فارسی', img: farsiimg },
                French: { name: 'Français', img: Frenchimg },
                Japanese: { name: '日本', img: Japaneseimg },
                Korean: { name: '한국인', img: Koreanimg },
            },
            lang: '',
          }
      },
      mounted() {
        this.lang = localRead('local')
        if (this.lang === 'hk') {
            this.lang = 'en'
            this.changeLang(this.lang)
        }
        console.log(localRead('local'))
      },
      methods: {
			// 语言切换
			changeLang(lang) {
                this.lang = lang;
                localSave('local', lang)
                this.$i18n.locale = lang;
                this.isshowlang = false;
			},
      }
  }
  </script>
  
  <style>
    .select-lang {
        position: relative;
    }
    .select-lang .currlang {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
    .select-lang .currlang .currtext {
        color: #fff;
        margin-right: 5px;
        font-size: 14px;
    }
    .select-lang .currlang .currimg {
        width: 15px;
        height: 12px;
    }
    .select-lang .langlist {
        background: #1f2525;
        padding-left: 4px;
        padding-right: 4px;
        position: absolute;
        border-radius: 4px;
        right: 0px;
        top: 30px;
        z-index: 9999;
        width: 90px;
    }
    .select-lang .langlist .item {
        display: flex;
        flex-direction: row;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding-bottom: 10px;
        padding-top: 10px;
        justify-content: space-between;
    }
    .select-lang .langlist .text {
        color: #fff;
        font-size: 14px;
    }
    .select-lang .langlist .img {
        width: 15px;
        height: 12px;
    }
  </style>
  
  