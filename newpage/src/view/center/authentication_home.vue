<template>
    <div class="recharge">
        <div class="header">
            <img src="../../assets/static/image/icon_back.0b3c36a6.ac21430a.svg" class="back" @click="back">
            <span class="titles fs-36 fc-353F52 ff_NunitoSemiBold">
                <span class="uppercase"></span>&nbsp;{{ $t('key200') }}
            </span>
            <span></span>
        </div>
        <div class="card1">
            <div class="card1-item" @click="go('/authentication?type=0')">
                <div class="labels">
                    <div>
                        Lv1
                    </div>
                    <div>{{ status1_text }}</div>
                </div>
                <div class="labels">
                    <div>
                        {{ $t('key248') }}
                    </div>
                </div>
                <div class="labels">
                    <div>
                        C2C
                    </div>
                    <div>{{ $t('lv1v1') }}</div>
                </div>
                <div class="labels">
                    <div>
                        {{ $t('key249') }}
                    </div>
                    <div>{{ $t('lv1v2') }}</div>
                </div>
            </div>
            <div class="card1-item" @click="goLevel2('/authentication?type=1')">
                <div class="labels">
                    <div>
                        Lv2
                    </div>
                    <div>
                        {{ status2_text }}
                    </div>
                </div>
                <div class="labels">
                    <div>
                        {{ $t('key248') }}
                    </div>
                </div>
                <div class="labels">
                    <div>
                        C2C
                    </div>
                    <div>{{ $t('lv2v1') }}</div>
                </div>
                <div class="labels">
                    <div>
                        {{ $t('key249') }}
                    </div>
                    <div>{{ $t('lv2v2') }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { authentication } from '@/api/user'

export default {
    name: 'wallet-c2c',
    props: {
    },
    data() {
        return {
            info: null,
            status1_text: this.$t('key250'),
            status2_text: this.$t('key250'),
        }
    },
    mounted() {
        this.coin = this.$route.query.coin
        authentication().then(res => {
            if (res.data.data) {
                this.info = res.data.data;
                if (this.info) {
                    if (this.info.name) {
                        if (this.info.status1 == 1) {
                            this.status1_text = this.$t('key251')
                        } else if (this.info.status1 == 2) {
                            this.status1_text = this.$t('key252')
                        } else if (this.info.status1 == 0) {
                            this.status1_text = this.$t('key253')
                        }
                        if (this.info.status2 == 1) {
                            this.status2_text = this.$t('key251')
                        } else if (this.info.status2 == 2) {
                            this.status2_text = this.$t('key252')
                        } else if (this.info.status2 == 0) {
                            this.status2_text = this.$t('key253')
                        }
                    }
                }
            }
        })
    },
    methods: {
        back() {
            this.$router.back()
        },
        go(path, query) {
            if (this.info) {
                if (this.info.status1 == 2) {
                    this.$toast(this.$t('key275'))
                } else {
                    this.$router.push({ path, query })
                }
            }
            else {
                this.$router.push({ path, query })
            }

        },
        goLevel2(path, query) {
            if (this.info) {
                if (this.info.status1 == 2) {
                    this.$router.push({ path, query })
                } else {
                    if (this.info.status1 != 2)
                        this.$toast(this.$t('key276'))
                    if (this.info.status2 == 2)
                        this.$toast(this.$t('key277'))
                }
            }else {
                this.$toast(this.$t('key276'))
            }



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

.card1 {
    padding: .3rem;
}

.card1-item {
    background-color: #f5f5f5;
    padding: .3rem;
    margin-bottom: .3rem;
}

.labels {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    color: #999;
}

.labels:first-child {
    color: #000;
}
</style>
