<template>
    <div >
        <div class="empty-state"  >
            <img class="img-empty-state" src="../assets/static/image/img_nodata.f22e393b.bf2e71bc.png"
                >
            <div class="no-data" >{{$t('key1')}}</div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'no-data',
    props: {
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
    }
}
</script>

<style scoped>

.empty-state {
    margin-top: .4rem;
    padding-bottom: .4rem;
    text-align: center;
    font-size: .4rem
}

.empty-state .img-empty-state {
    margin-bottom: .56rem;
    width: 3.7rem;
    height: auto
}

.empty-state .no-data {
    font-weight: 500;
    color: #aaa
}
</style>

