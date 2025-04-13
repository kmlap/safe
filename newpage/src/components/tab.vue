<template>
    <div class="switch_container" >
        <div class="switch_content"  :style="`width: ${width}`">
            <div class="switch_item" v-for="(item, index) in title" :key="index" :class="[value === index ? 'active' : '']"  @click="changeTabindex(index)">{{item}}</div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'tab',
    props: {
        title: {
            default: []
        },
        width: {
            default: '4.44rem'
        },
        value: {
            default: 0
        }
    },
    components: {
    },
    data() {
        return {
        }
    },
    mounted() {
    },
    methods: {
        changeTabindex (value) {
            this.$emit('input', value)
        }
    }
}
</script>

<style scoped>
.switch_container {
    margin-top: 0.32rem;
}
.switch_container .switch_content {
    margin: 0 auto;
    padding: 0.08rem;
    width: 4.44rem;
    background: #f5f6f8;
    border-radius: 0.2rem;
    display: flex;
    justify-content: space-between;
    align-content: center;
    align-items: center;
}
.switch_container .switch_content .switch_item {
    width: 2.14rem;
    height: 0.72rem;
    line-height: .72rem;
    text-align: center;
    font-size: .26rem;
    color: #353f52;
    font-weight: 600;
}
.switch_content .switch_item.active {
    background: #fff;
    border-radius: 0.2rem;
}
.switch_container .switch_content .switch_item {
    width: 2.14rem;
    height: 0.72rem;
    line-height: .72rem;
    text-align: center;
    font-size: .26rem;
    color: #353f52;
    font-weight: 600;
}
</style>

