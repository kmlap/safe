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

        <van-form @submit="onSubmit">
            <van-field v-model="form.country" type="text" name="country" :label="$t('key206')" :placeholder="$t('key206')"
                :rules="[{ required: true, message: $t('key207') }]" />
            <van-field v-model="form.name" type="text" name="name" :label="$t('key222')" :placeholder="$t('key222')"
                :rules="[{ required: true, message: $t('key223') }]" />
            <van-field v-model="form.email" type="text" name="email" :label="$t('key224')" :placeholder="$t('key224')"
                :rules="[{ required: true, message: $t('key225') }]" />
            <van-field readonly clickable name="purchase_coin" :value="form.purchase_coin" :label="$t('key226')"
                :placeholder="$t('key227')" @click="showPicker1 = true" />
            <van-popup v-model="showPicker1" position="bottom">
                <van-picker :confirm-button-text="$t('key183')" show-toolbar :columns="columns1" @confirm="onConfirm1" @cancel="showPicker1 = false" />
            </van-popup>
            <van-field readonly clickable name="coin" :value="form.coin" :label="$t('key228')"
                :placeholder="$t('key229')" @click="showPicker2 = true" />
            <van-popup v-model="showPicker2" position="bottom">
                <van-picker :confirm-button-text="$t('key183')" show-toolbar :columns="columns2" @confirm="onConfirm2" @cancel="showPicker2 = false" />
            </van-popup>
            <van-field v-model="form.amount" name="amount" :label="$t('key230')" :placeholder="$t('key230')"
                :rules="[{ required: true, message: $t('key231') }]" />
            <div style="margin: 16px;">
                <van-button round block type="info" native-type="submit">{{ $t('key221') }}</van-button>
            </div>

        </van-form>
    </div>
</template>

<script>
import { c2c } from '@/api/user'

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
            columns1: ['美元(USD)', '歐元(EUR)','新臺幣(TWD)', '日元(JPY)', '英鎊(GBP)', '澳幣(AUD)'],
            columns2: ['USDT', 'BTC', 'ETH', 'AIA', 'AUD'],
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
            c2c(values).then(res => {
                let data = res.data
                if (data.code == 1) {
                    this.$toast(this.$t('key203'));
                    this.go('/wallet-status');
                }
            })
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
</style>
