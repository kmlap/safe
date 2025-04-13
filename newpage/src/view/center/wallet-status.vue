<template>
    <div class="recharge">
        <div class="header">
            <img src="../../assets/static/image/icon_back.0b3c36a6.ac21430a.svg" class="back" @click="back">
            <span class="titles fs-36 fc-353F52 ff_NunitoSemiBold">
                <span class="uppercase"></span>&nbsp;{{ $t('key199') }}
            </span>
            <span></span>
        </div>
        <div class="switch_container">
            <tab :title="[$t('key235'), $t('key236'), $t('key238'), $t('key237'), $t('key234')]" v-model="tabindex"
                width="6.58rem"></tab>
        </div>
        <div class="list_container">
            <van-pull-refresh v-model="loading" @refresh="getTransferRecordXiala" pulling-text="Pull down to refresh.."
                loosing-text="Release to refresh..." loading-text="loading...">
                <van-list v-model="loading" :finished="finished" loading-text="loading..." @load="getTransferRecord">
                    <div class="list_item" v-for="(item, index) in List" :key="index">
                        <div class="labes">
                            <div>
                                {{ $t('key226') }}
                            </div>
                            <div>
                                {{ item.purchase_coin }}
                            </div>
                        </div>
                        <div class="labes">
                            <div>
                                {{ $t('key228') }}
                            </div>
                            <div>
                                {{ item.coin }}
                            </div>
                        </div>
                        <div class="labes">
                            <div>
                                {{ $t('key230') }}
                            </div>
                            <div>
                                {{ item.amount }}
                            </div>
                        </div>
                        <div class="labes">
                            <div v-if="item.status == 1">
                                {{ $t('key239') }}
                            </div>
                            <div v-else>
                                {{ $t('key261') }}
                            </div>
                            <div>
                                <template v-if="item.status == 1">
                                    <van-button type="primary" size="mini" @click="cancel(item)">{{ $t('key240')
                                        }}</van-button>
                                </template>
                                <template v-else-if="item.status == 2">
                                    <van-button type="primary" size="mini" @click="sss(item)">{{ $t('key241')
                                    }}</van-button>
                                </template>
                                <template v-else-if="item.status == 3">
                                    {{ $t('key242') }}
                                </template>
                                <template v-else-if="item.status == 4">
                                    {{ $t('key243') }}
                                </template>
                                <template v-else-if="item.status == 0">
                                    {{ $t('key184') }}
                                </template>
                            </div>
                        </div>
                    </div>
                </van-list>
                <no-data v-if="List.length <= 0"></no-data>
            </van-pull-refresh>
        </div>

        <van-popup v-model="show" round>
            <div style="width: 6rem;padding: .3rem;">
                <div style="font-size: .34rem;font-weight: bold;text-align: center;">{{ $t('key244') }}</div>
                <div>
                    <div class="labes">
                        <div>
                            {{ $t('key268') }}
                        </div>
                        <div style="display: flex;">
                            {{ info.card }}
                            <div class="copy-btn btn-copy" style="color:#1989fa" @click="copyText">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true" role="img" class="iconify iconify--fluent" width="1em"
                                    height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                    data-icon="fluent:copy-24-regular">
                                    <g fill="none">
                                        <path
                                            d="M5.503 4.627L5.5 6.75v10.504a3.25 3.25 0 0 0 3.25 3.25h8.616a2.251 2.251 0 0 1-2.122 1.5H8.75A4.75 4.75 0 0 1 4 17.254V6.75c0-.98.627-1.815 1.503-2.123zM17.75 2A2.25 2.25 0 0 1 20 4.25v13a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-13A2.25 2.25 0 0 1 8.75 2h9zm0 1.5h-9a.75.75 0 0 0-.75.75v13c0 .414.336.75.75.75h9a.75.75 0 0 0 .75-.75v-13a.75.75 0 0 0-.75-.75z"
                                            fill="currentColor"></path>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="labes">
                        <div>
                            {{ $t('key269') }}
                        </div>
                        <div>
                            {{ info.card_code }}
                        </div>
                    </div>
                    <div class="labes">
                        <div>
                            {{ $t('key246') }}
                        </div>
                        <div>
                            {{ info.transfer_amount }}
                        </div>
                    </div>
                    <div class="labes">
                        <div>
                            {{ $t('key247') }}
                        </div>
                        <div>
                            <van-uploader :max-count="1" v-model="fileList" accept="image/*" @delete="deleteFile"
                                :after-read="afterRead">
                            </van-uploader>
                        </div>
                    </div>
                </div>
                <div style="display: flex;justify-content: center;margin-top: .2rem;">
                    <van-button type="info" @click="su">{{ $t('key221') }}</van-button>
                </div>
            </div>
        </van-popup>
    </div>
</template>

<script>
import { c2clist, suc2c, noc2c } from '@/api/user'
import tab from '@/components/tab.vue'
import noData from '@/components/no-data.vue'
import Clipboard from 'clipboard'
export default {
    name: 'wallet-c2c',
    props: {
    },
    components: {
        tab,
        noData
    },
    data() {
        return {
            fileList: [],
            file: null,
            rechargeImgBase64: "",
            show: false,
            tabindex: 0,
            page: 1,
            page_size: 20,
            loading: false,
            finished: false,
            List: [],
            coin: '',
            info: {}
        }
    },
    watch: {
        tabindex() {
            this.getTransferRecordXiala()
        }
    },
    mounted() {
        this.coin = this.$route.query.coin
        this.getTransferRecord()
    },
    methods: {
        deleteFile() {
            this.rechargeImgBase64 = ''
            this.file = null
            this.fileList = []
        },
        afterRead(file) {
            this.fileList = [
                {
                    url: file.content,
                    isImage: true
                }
            ]
            this.rechargeImgBase64 = file.content
            this.file = file.file
        },
        sss(item) {
            this.info = item;
            this.show = true
        },
        su() {
            if (this.rechargeImgBase64 == '') {
                this.$toast(this.$t('key232'))
                return;
            }
            suc2c({ image: this.rechargeImgBase64, id: this.info.id }).then(res => {
                // this.$toast(res.data)
                if (res.data.code == 1) {
                    this.$toast(this.$t('key233'));
                    this.show = false;
                    this.getTransferRecordXiala()
                }
            })
        },
        cancel(item) {
            noc2c({ id: item.id }).then(res => {
                // console.log(res.data)
                if (res.data.code == 1) {
                    this.$toast(this.$t('key185'));
                    this.show = false;
                    this.getTransferRecordXiala()
                }
            })
        },
        reset() {
            this.page = 1
            this.loading = false
            this.finished = false
            this.List = []
        },
        getTransferRecordXiala() {
            this.reset()
            this.getTransferRecord()
        },
        getTransferRecord() {
            this.loading = true
            let status = 0
            switch (this.tabindex) {
                case 0:
                    status = 1
                    break;
                case 1:
                    status = 2
                    break;
                case 2:
                    status = 3
                    break;
                case 3:
                    status = 4
                    break;
                case 4:
                    status = 0
                    break;
                default:
                    break;
            }
            c2clist({ page: this.page, page_size: this.page_size, status: status }).then(res => {
                let data = res.data
                console.log('data', data);
                this.loading = false
                if (data.code == 1) {
                    this.List.push(...data.data.list)
                    this.page++
                }
                if (this.List.length >= data.data.total) this.finished = true
            })
        },
        back() {
            this.$router.back()
        },
        go(path, query) {
            this.$router.push({ path, query })
        },
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
        // },
        copyText() {
            const text = this.info.card;
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

<style scoped>
.van-password-input {
    margin: 0
}

.van-password-input .van-password-input__security {
    display: flex;
    justify-content: space-between
}

.van-password-input .van-password-input__security li {
    width: .66rem;
    height: .8rem;
    border: 1px solid rgba(151, 151, 151, .51);
    border-radius: .1rem
}

.recharge {
    padding-bottom: 1.26rem;
    font-weight: 500
}

.recharge .uppercase {
    text-transform: uppercase
}

.recharge .header {
    padding: .32rem .4rem;
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center
}

.recharge .header .back {
    width: .32rem
}

.recharge .header .record {
    width: .4rem
}

.list_item {
    padding: .3rem;
    margin-bottom: .3rem;
    display: flex;
    flex-direction: column;
    gap: .1rem;
}

.labes {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
}
</style>
