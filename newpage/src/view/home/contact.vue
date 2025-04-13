<template>
    <div class="question_answer" >
        <header-nav :backIconType="2" :title="title"></header-nav>
        <div style="padding: .3rem;">
            <div v-html="html"></div>
        </div>
        <div v-if='type == 3' style="padding: .3rem;">
            <div style="display: flex;justify-content: space-between; align-items: center;">
                <div>{{$t('key254')}}</div>
                <a :href="configInfo.chatLink" >
                    <li class="drawer-item" >
                        <img src="../../assets/static/image/icon_menu_cs.05117d1f.png" >
                        <!-- <span >{{ $t('key168') }}</span> -->
                    </li>
                </a>
            </div>
            <br />
            <div style="display: flex;justify-content: space-between; align-items: center;"><div>{{$t('key255')}}</div> 
            <div style="display: flex;">{{ configInfo.email }}
                <div class="copy-btn btn-copy" :data-clipboard-text="configInfo.email" @click="copyText"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        aria-hidden="true" role="img" class="iconify iconify--fluent" width="1em" height="1em"
                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="fluent:copy-24-regular"
                        >
                        <g fill="none">
                            <path
                                d="M5.503 4.627L5.5 6.75v10.504a3.25 3.25 0 0 0 3.25 3.25h8.616a2.251 2.251 0 0 1-2.122 1.5H8.75A4.75 4.75 0 0 1 4 17.254V6.75c0-.98.627-1.815 1.503-2.123zM17.75 2A2.25 2.25 0 0 1 20 4.25v13a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-13A2.25 2.25 0 0 1 8.75 2h9zm0 1.5h-9a.75.75 0 0 0-.75.75v13c0 .414.336.75.75.75h9a.75.75 0 0 0 .75-.75v-13a.75.75 0 0 0-.75-.75z"
                                fill="currentColor"></path>
                        </g>
                    </svg>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>

<script>
import { findNotice } from '@/api/user'
import headerNav from '@/components/header-nav.vue'
import Clipboard from 'clipboard'
export default {
    name: 'about',
    props: {
    },
    components: {
        headerNav
    },
    computed: {
        configInfo() {
            return this.$store.state.user.configInfo
        },
    },
    data() {
        return {
            title: "",
            html: '',
            type: '',
        }
    },
    mounted() {
        let type = this.$route.query.type;
        this.type = type;
        switch (type) {
            case '2':
                this.title = 'Q&A'
                break;
            case '3':
                this.title = this.$t('key202')
                break;
            case '4':
                this.title = this.$t('key201')
                break;
        }
        findNotice({ type }).then(res => {
            let data = res.data
            // this.title = data.data.title
            if (type == 3) {
                this.html = data.data[0].content
            }
            else {
                this.html = data.data.content
            }
        })
    },
    methods: {
        // copy() {
        //     var clipboard = new Clipboard('.btn-copy')
        //     clipboard.on('success', e => {
        //         this.$toast({
        //             message: this.$t('key96'),
        //             icon: 'success',
        //         })
        //         // 释放内存
        //         clipboard.destroy()
        //     })
        //     clipboard.on('error', e => {
        //         // 不支持复制
        //         this.$toast({
        //             message: this.$t('key97'),
        //             icon: 'cross',
        //         })
        //         // 释放内存
        //         clipboard.destroy()
        //     })
        // }
        copyText() {
            const text = this.configInfo.email;
            const textString = text.toString() // 数字没有 .length 不能执行selectText 需要转化成字符串
            let input = document.querySelector('#copy-input')
            if (!input) {
                input = document.createElement('input')
                input.id = 'copy-input'
                input.readOnly = 'readOnly' // 防止ios聚焦触发键盘事件
                input.style.position = 'absolute'
                input.style.left = '-2000px'
                input.style.zIndex = '-2000'
                document.body.appendChild(input)
            }

            input.value = textString
            // ios必须先选中文字且不支持 input.select();
            this.selectText(input, 0, textString.length)
            if (document.execCommand('copy')) {
                document.execCommand('copy')
                this.$toast({
                    message: this.$t('key96'),
                    icon: 'success',
                })
            } else {
                console.log('复制失败！')
            }
            input.blur()
        },
        selectText(textbox, startIndex, stopIndex) {
            if (textbox.createTextRange) {
                // ie
                const range = textbox.createTextRange()
                range.collapse(true)
                range.moveStart('character', startIndex) // 起始光标
                range.moveEnd('character', stopIndex - startIndex) // 结束光标
                range.select() // 不兼容苹果
            } else {
                // firefox/chrome
                textbox.setSelectionRange(startIndex, stopIndex)
                textbox.focus()
            }
        },
    }
}
</script>
