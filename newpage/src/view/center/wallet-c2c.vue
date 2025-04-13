<template>
    <div class="recharge" >
        <div class="header" >
            <img src="../../assets/static/image/icon_back.0b3c36a6.ac21430a.svg" class="back" @click="back"
                >
            <span class="titles fs-36 fc-353F52 ff_NunitoSemiBold" >
                <span class="uppercase" ></span>&nbsp;{{ $t('key199') }}
            </span>
            <img src="../../assets/static/image/icon_record.7c965f76.6096f376.svg"
                @click="go('/wallet-status', { coin: coin })" class="record" >
        </div>
        <div class="banner">
            <div class="banner-title">{{ $t('key256') }}</div>
            <div class="banner-img">
                <img src="../../assets/images/a.3e3561c3.png" style="width: 100%;height: 1rem;" alt="">
            </div>
            <div class="banner-word">
                <div>{{ $t('key257') }}</div>
                <div>{{ $t('key258') }}</div>
                <div>{{ $t('key259') }}</div>
            </div>
        </div>
        <div class="c2c-content">
            <div class="assets-tab">
                <div class="assets-tab-item blue" @click="go('/wallet-order')">{{ $t('key260') }}</div>
                <div class="assets-tab-item blue" @click="go('/wallet-status')">{{ $t('key261') }}</div>
            </div>
            <div class="c2c-ours-cont">
                <div class="c2c-ours-item"><img src="../../assets/images/aa.cf1529e0.png" alt="">
                    <div class="c2c-ours-word">
                        <div class="c2c-ours-wordTop">{{ $t('key262') }}</div>
                        <div class="c2c-ours-wordBottom">{{ $t('key263') }}</div>
                    </div>
                </div>
                <div class="c2c-ours-item"><img src="../../assets/images/bb.b0b8eace.png" alt="">
                    <div class="c2c-ours-word">
                        <div class="c2c-ours-wordTop">{{ $t('key264') }}</div>
                        <div class="c2c-ours-wordBottom">{{ $t('key265') }}</div>
                    </div>
                </div>
                <div class="c2c-ours-item"><img src="../../assets/images/cc.fe36d4b1.png" alt="">
                    <div class="c2c-ours-word">
                        <div class="c2c-ours-wordTop">{{ $t('key266') }}</div>
                        <div class="c2c-ours-wordBottom">{{ $t('key267') }}</div>
                    </div>
                </div>
                <br/>
                <div class="c2c-ours-word" style="text-align: center;">
                    <!-- <div >{{ $t('key273') }}</div> -->
                    <div style="font-size: .22rem;  color: #717174;">{{ $t('key274') }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { } from '@/api/user'

export default {
    name: 'wallet-c2c',
    props: {
    },
    data() {
        return {
            form: {
                name: "",
                country: "",
                email: "",
                purchase_coin: "",
                coin: "",
                amount: "",
            },
            value: '',
            columns1: ['USD', 'EUR', 'JPY', 'GBP', 'AUD'],
            columns2: ['USDT', 'XAU', 'AIA', 'BTC', 'AUD'],
            showPicker1: false,
            showPicker2: false,
            coin: ""
        }
    },
    mounted() {
        this.coin = this.$route.query.coin
    },
    methods: {
        onSubmit(values) {
            console.log('submit', values);
        },
        onConfirm1(value) {
            this.form.purchase_coin = value
            this.showPicker1 = false;
        },
        onConfirm2(value) {
            this.form.coin = value
            this.showPicker2 = false;
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

.banner {
    background: url('../../assets/images/bg.967e6a3c.png') no-repeat;
    height: 3.5rem;
    width: 100%;
    background-size: 100% 100%;
    padding: 0.5rem;
}

.banner-img {
    padding: 0.3rem .6rem .1rem;
}

.banner-title {
    color: #fff;
    font-weight: bold;
    font-size: .4rem;
}

.banner-word {
    display: flex;
    justify-content: space-between;
}

.banner-word>div {
    flex: 1;
    color: #fff;
    font-size: .25rem;
    text-align: center;
}

.c2c-content {
    background-color: #fff;
    padding: .5rem;
    margin-top: -.18889rem;
    border-top-left-radius: .3rem;
    border-top-right-radius: .3rem;
}

.assets-tab {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .3rem;
}

.assets-tab .assets-tab-item.blue {
    color: #fff;
    background-color: #0b559e;
    width: 48%;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    border-radius: .3rem;
    padding: .2rem;
}

.c2c-ours-cont .c2c-ours-item {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    background-color: #f7f9fc;
    padding: .2rem;
    margin-bottom: .2rem;
    border-radius: .3rem;
}

.c2c-ours-cont .c2c-ours-item img {
    width: 1rem;
    height: 1rem;
}

.c2c-ours-word {
    margin-left: .2rem;
}

.c2c-ours-wordTop {
    font-weight: bolder;
    color: #000;
    font-size: .3rem;
}

.c2c-ours-wordBottom {
    color: #9096a1;
    font-size: .2rem;
}
</style>
