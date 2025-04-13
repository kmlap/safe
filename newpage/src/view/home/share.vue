<template>
    <div class="share" >
        <div class="header" >
            <img @click="back" src="../../assets/static/image/icon_close.7ba8065d.svg" >
        </div>
        <div class="share_img_content" >
            <img src="../../assets/static/image/img_share.c6632a1c.png" class="img_share" >
        </div>
        <div class="share_info_content" >
            <div class="share_title fc-0A0B0D fs-40 ff_NunitoBold" >{{$t('key107')}}</div>
            <div class="share_subtitle fc-5B616E ff_NunitoRegular" >{{$t('key108')}}</div>
            <div class="share_subtitle fc-5B616E ff_NunitoRegular" >{{$t('key109')}}!</div>
        </div>
        <div class="address_container" >
            <div class="address_content" >
                <div class="address_value fc-353F52 fs-26 ff_NunitoRegular" >
                    {{url}}</div>
                <span class="copy fc-353F52 ff_NunitoSemiBold copyBtn btn-copy" :data-clipboard-text="url" @click="copy" >{{$t('key110')}}</span>
            </div>
        </div>
        <div class="submit_container" >
            <div class="submit_btn ff_NunitoSemiBold copyBtn btn-copy" :data-clipboard-text="url" @click="copy" >
                {{$t('key111')}}</div>
        </div>
  </div>
</template>

<script>
import Clipboard from 'clipboard'

export default {
    name: 'share',
    props: {
    },
    components: {
    },
    data() {
        return {
            url: '',
            
        }
    },
    computed: {
        userinfo () {
            return this.$store.state.user.userinfo
        }
    },
    mounted() {
        this.setshareUrl()
    },
    methods: {
        setshareUrl () {
            const hostname = document.location.hostname
            const protocol = window.location.protocol
            this.url = `${protocol}//${hostname}?invite=${this.userinfo.invite}`
        },
        copy () {
            var clipboard = new Clipboard('.btn-copy')
            clipboard.on('success', e => {
                this.$toast({
                message: this.$t('key96'),
                icon: 'success',
                })
            // 释放内存
            clipboard.destroy()
            })
            clipboard.on('error', e => {
            // 不支持复制
                this.$toast({
                message: this.$t('key97'),
                icon: 'cross',
                })
            // 释放内存
            clipboard.destroy()
            })
        },
        back () {
            this.$router.back()
        }
    }
}
</script>

<style scoped>
.share {
    padding-bottom: 1.64rem
}

.share .header {
    padding: .32rem .4rem
}

.share .header img {
    width: .4rem
}

.share .share_img_content {
    margin-top: .64rem;
    text-align: center
}

.share .share_img_content .img_share {
    width: 5.3rem;
    height: auto
}

.share .share_info_content {
    margin-top: .56rem;
    padding: 0 .48rem;
    text-align: center
}

.share .share_info_content .share_subtitle {
    margin-top: .32rem
}

.share .address_container {
    margin-top: .56rem;
    padding: 0 .48rem
}

.share .address_container .address_content {
    border-radius: .1rem;
    border: 1px solid rgba(151, 151, 151, .51);
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center;
    padding: .16rem .22rem .16rem .24rem
}

.share .address_container .address_content .address_value {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap
}

.share .address_container .address_content .copy {
    margin-left: .3rem;
    padding: .2rem .3rem;
    border-radius: .1rem;
    word-break: keep-all;
    border: 1px solid rgba(151, 151, 151, .51)
}

.share .submit_container {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1.32rem;
    background: #fff;
    padding: 0 .48rem
}

.share .submit_container .submit_btn {
    margin-top: .24rem;
    height: 1.04rem;
    line-height: 1.04rem;
    background: #1652f0;
    border-radius: .14rem;
    color: #fff;
    font-size: .36rem;
    text-align: center
}
</style>

