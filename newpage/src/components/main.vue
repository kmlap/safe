<template>
    <div>
        <router-view />
        <!-- <yfooter></yfooter> -->
    </div>
</template>

<script>
import yfooter from './footer.vue'

export default {
  name: 'Main',
  components: {
    yfooter
  },
  data () {
    return {
    }
  },
  computed: {
  },
  methods: {
  },
  mounted () {
  }
}
</script>

<style>
</style>

