<template>
    <div class="recharge">
        <div class="header">
            <img src="../../assets/static/image/icon_back.0b3c36a6.ac21430a.svg" class="back" @click="back">
            <span class="titles fs-36 fc-353F52 ff_NunitoSemiBold">
                <span class="uppercase"></span>&nbsp;{{ $t('key200') }}
            </span>
            <span></span>
        </div>
        <div class="switch_container">
            <tab hidden :title="[$t('key204'), $t('key205')]" v-model="tabindex" width="6.58rem"></tab>
        </div>
        <div style="margin-top: .3rem;">
            <div v-show="show1">
                <van-cell-group inset>
                    <van-cell :title="$t('key204')" />
                    <van-cell>
                        <van-form @submit="onSubmit">
                            <van-field v-model="form.country" type="text" :name="$t('key206')" :label="$t('key206')"
                                :placeholder="$t('key206')" :rules="[{ required: true, message: $t('key207') }]" />
                            <van-field v-model="form.name" type="text" :name="$t('key208')" :label="$t('key208')"
                                :placeholder="$t('key208')" :rules="[{ required: true, message: $t('key209') }]" />
                            <van-field v-model="form.card" type="text" :name="$t('key210')" :label="$t('key210')"
                                :placeholder="$t('key210')" :rules="[{ required: true, message: $t('key211') }]" />
                            <div style="margin: 16px;">
                                <template v-if="form.status1 == 1">
                                    <div
                                        style="display: flex;flex-direction: row;align-items: center;justify-content: center;">
                                        <van-tag type="warning">{{ $t('key217') }}</van-tag>
                                    </div>
                                </template>
                                <template v-else-if="form.status1 == 2">
                                    <div
                                        style="display: flex;flex-direction: row;align-items: center;justify-content: center;">
                                        <van-tag type="success">{{ $t('key218') }}</van-tag>
                                    </div>
                                </template>
                                <template v-else-if="form.status1 == 0">
                                    <div
                                        style="margin-bottom: .3rem; display: flex;flex-direction: row;align-items: center;justify-content: center;">
                                        <van-tag type="danger">{{ $t('key219') }}</van-tag>
                                    </div>
                                    <van-button round block type="info" native-type="submit">{{ $t('key220')
                                        }}</van-button>
                                </template>
                                <template v-else>
                                    <van-button round block type="info" native-type="submit">{{ $t('key221')
                                        }}</van-button>
                                </template>
                            </div>
                        </van-form>
                    </van-cell>
                </van-cell-group>
            </div>
            <div v-show="show2">
                <van-cell-group inset>
                    <van-cell :title="$t('key205')" />
                    <van-cell>
                        <van-form @submit="onSubmit2">
                            <van-field name="uploader" :label="$t('key212')" v-model="form.image1"
                                :rules="[{ required: true, message: $t('key213') }]">
                                <template #input>
                                    <template v-if="form.status2 == 1 || form.status2 == 2">

                                        <van-image width="2rem" height="2rem" fit="contain" :src="form.image1" />
                                        <!-- <img :src="form.image1" style="width: 1rem;height: 1rem;" alt=""> -->
                                    </template>
                                    <template v-else>
                                        <van-uploader :max-count="1" v-model="fileList1" accept="image/*"
                                            @delete="deleteFile1" :after-read="afterRead1" :before-read="beforeRead">
                                        </van-uploader>
                                    </template>
                                </template>
                            </van-field>
                            <van-field name="uploader" :label="$t('key214')" v-model="form.image2"
                                :rules="[{ required: true, message: $t('key215') }]">
                                <template #input>
                                    <template v-if="form.status2 == 1 || form.status2 == 2">
                                        <van-image width="2rem" height="2rem" fit="contain" :src="form.image2" />
                                    </template>
                                    <template v-else>
                                        <van-uploader :max-count="1" v-model="fileList2" accept="image/*"
                                            @delete="deleteFile2" :after-read="afterRead2" :before-read="beforeRead">
                                        </van-uploader>
                                    </template>
                                </template>
                            </van-field>
                            <van-field name="uploader" :label="$t('key216')" v-model="form.image3"
                                :rules="[{ required: true, message: $t('upkey216') }]">
                                <template #input>
                                    <template v-if="form.status2 == 1 || form.status2 == 2">
                                        <van-image width="2rem" height="2rem" fit="contain" :src="form.image3" />
                                    </template>
                                    <template v-else>
                                        <van-uploader :max-count="1" v-model="fileList3" accept="image/*"
                                            @delete="deleteFile3" :after-read="afterRead3" :before-read="beforeRead">
                                        </van-uploader>
                                    </template>
                                </template>
                            </van-field>
                            <div style="margin: 16px;">
                                <template v-if="form.status2 == 1">
                                    <div
                                        style="display: flex;flex-direction: row;align-items: center;justify-content: center;">
                                        <van-tag type="warning">{{ $t('key217') }}</van-tag>
                                    </div>
                                </template>
                                <template v-else-if="form.status2 == 2">
                                    <div
                                        style="display: flex;flex-direction: row;align-items: center;justify-content: center;">
                                        <van-tag type="success">{{ $t('key218') }}</van-tag>
                                    </div>
                                </template>
                                <template v-else-if="form.status2 == 0">
                                    <div
                                        style="margin-bottom: .3rem; display: flex;flex-direction: row;align-items: center;justify-content: center;">
                                        <van-tag type="danger">{{ $t('key219') }}</van-tag>
                                    </div>
                                    <van-button round block type="info" native-type="submit">{{ $t('key220')
                                    }}</van-button>
                                </template>
                                <template v-else>
                                    <van-button round block type="info" native-type="submit">{{ $t('key221')
                                    }}</van-button>
                                </template>
                            </div>
                        </van-form>
                    </van-cell>
                </van-cell-group>

            </div>
        </div>
    </div>
</template>

<script>
import tab from '@/components/tab.vue'
import { authentication1, authentication2, authentication } from '@/api/user'

export default {
    name: 'wallet-c2c',
    props: {
    },
    components: {
        tab
    },
    data() {
        return {
            show1: true,
            show2: false,
            file: null,
            fileList1: [],
            rechargeImgBase641: "",
            fileList2: [],
            rechargeImgBase642: "",
            fileList3: [],
            rechargeImgBase643: "",

            tabindex: 0,
            form: {
                country: "",
                name: "",
                card: "",
                image1: "",
                image2: "",
                image3: "",
            },
        }
    },
    watch: {
        tabindex(e) {
            if (e == 0) {
                this.show1 = true
                this.show2 = false
            } else {
                this.show1 = false
                this.show2 = true
            }
        }
    },
    mounted() {
        // console.log(this.form);
        // this.tabindex = this.$route.query.type ? this.$route.query.type : 0;
        this.tabindex = this.$route.query.type ? parseInt(this.$route.query.type) : 0;
        this.coin = this.$route.query.coin
        authentication().then(res => {
            // console.log('res', res);
            if (res.data.data) {
                this.form = res.data.data;
            }
        })
    },
    methods: {
        beforeRead(file) {
            // console.log(file.type)
            if (file.type !== 'image/jpeg') {
                if (file.type !== 'image/png') {
                    this.$toast(this.$t('key278'));
                    return false;
                }
            }
            return true;
        },
        deleteFile1() {
            this.rechargeImgBase641 = ''
            this.file = null
            this.fileList1 = []
        },
        afterRead1(file) {
            this.fileList1 = [
                {
                    url: file.content,
                    isImage: true
                }
            ]
            this.form.image1 = file.content
            this.file = file.file
        },
        deleteFile2() {
            this.rechargeImgBase642 = ''
            this.file = null
            this.fileList2 = []
        },
        afterRead2(file) {
            this.fileList2 = [
                {
                    url: file.content,
                    isImage: true
                }
            ]
            this.form.image2 = file.content
            this.file = file.file
        },
        deleteFile3() {
            this.rechargeImgBase643 = ''
            this.file = null
            this.fileList3 = []
        },
        afterRead3(file) {
            this.fileList3 = [
                {
                    url: file.content,
                    isImage: true
                }
            ]
            this.form.image3 = file.content
            this.file = file.file
        },
        onSubmit(values) {
            // console.log('submit', values);
            authentication1(this.form).then(res => {
                let data = res.data
                if (data.code == 1) {
                    this.$toast(this.$t('key203'));
                    this.go('/authentication_home');
                }

            })
        },
        onSubmit2() {
            authentication2(this.form).then(res => {
                let data = res.data
                if (data.code == 1) {
                    this.$toast(this.$t('key203'));
                    this.go('/authentication_home');
                }
            })
        },
        back() {
            this.$router.back()
        },
        go(path, query) {
            this.$router.push({ path, query })
        }
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
    position: relative;
    padding: .32rem .4rem;
    text-align: center;
    display: flex;
    justify-content: space-between;
}

.recharge .header .back {
    top: 0;
    bottom: 0;
    left: .4rem;
    margin: 0;
    width: .32rem;
}

.recharge .header .record {
    width: .4rem
}
</style>
