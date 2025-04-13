<template>
    <div class="arbitrage_intro" >
      <div class="header" >
          <span >
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" @click="back" role="img" class="iconify iconify--feather back" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="feather:x" >
                  <g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M18 6L6 18"></path>
                      <path d="M6 6l12 12"></path>
                  </g>
              </svg>
          </span>
          <span class="fs-32 ff_NunitoSemiBold" >{{ $t('key179') }}{{ $t('key180') }}</span>
      </div>
      <div class="intro_content">
          <div class="intro_img" >
              <img src="../../assets/static/image/img_invest.49cd0918.9336986f.png" >
          </div>
          <div class="intro_detail ff_NunitoRegular" >
            <div v-html="html"></div>
              <!-- <p>
                  <strong>
                      <span style="color:#0066cc;">{{$t('key14')}}</span>
                  </strong>
              </p>

              <p>{{$t('key15')}}</p>

              <p>{{$t('key16')}}</p>

              <p>
                  <span style="color:#0066cc;">
                      <strong>{{$t('key17')}}</strong>
                  </span>
              </p>

              <p>{{$t('key18')}}</p> -->
          </div>
      </div>
  </div>
</template>

<script>
import { findNotice } from '@/api/user'
export default {
    name: 'intro',
    props: {
    },
    components: {
        
    },
    data() {
        return {
            html:''
        }
    },
    mounted() {
        let type = this.$route.query.type;
        this.type = type;
        findNotice({ type }).then(res => {
            let data = res.data
            // console.log(data.data[0].content)
            // this.title = data.data.title
            this.html = data.data[0].content
        })
    },
    methods: {
        back() {
            this.$router.back()
        }
    }
}
</script>

<style scoped>
.arbitrage_intro {
    padding-bottom: .5rem;
    font-weight: 500
}

.arbitrage_intro .header {
    position: relative;
    padding: .27rem .44rem;
    text-align: center
}

.arbitrage_intro .header .back {
    position: absolute;
    top: 0;
    left: .44rem;
    bottom: 0;
    margin: auto;
    font-size: .5rem
}

.arbitrage_intro .intro_content {
    padding: .32rem
}

.arbitrage_intro .intro_content .intro_img {
    text-align: center
}

.arbitrage_intro .intro_content .intro_img img {
    width: 1.94rem
}

.arbitrage_intro .intro_content .intro_detail,
.arbitrage_intro .intro_content .intro_item {
    margin-top: .64rem
}

.arbitrage_intro .intro_content .intro_item .intro_title {
    font-size: .4rem;
    color: #1652f0
}

.arbitrage_intro .intro_content .intro_item .intro_text {
    font-size: .32rem;
    color: #333
}

.arbitrage_intro .intro_content .intro_item .intro_text .text {
    margin-top: .4rem
}

.arbitrage_intro p {
    color: #000;
    margin: 0
}

 .intro_detail p {
    margin: 10px 0
}

 .intro_detail strong {
    color: inherit
}
</style>

