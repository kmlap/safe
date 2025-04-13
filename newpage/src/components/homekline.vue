<template>
    <div class="kline-container"  ref="kline"></div>
</template>

<script>
import { createChart } from "lightweight-charts"

export default {
    props: {
        kData: {
            default: []
        },
        change: {
            default: 0
        }
    },
    components: {
    },
    data() {
        return {
            chart: null,
            lineSeries: null,
        }
    },
    watch: {
        change () {
            this.syncToInterval()
        }
    },
    mounted() {
        this.init()
    },
    methods: {
        syncToInterval() {
            if (this.lineSeries) {
                this.chart.removeSeries(this.lineSeries);
                this.lineSeries = null;
            }
            this.lineSeries = this.chart.addAreaSeries({
                topColor: "#fff",
                bottomColor: "#fff",
                lineColor:  Number(this.change) >= 0 ? "#13b26f" : '#dd4b4b', // #13b26f 绿色 #dd4b4b 红
                lineWidth: 2,
                priceFormat: {
                    type: "price",
                },
                priceLineVisible: !1,
            });
            let kData = JSON.parse(JSON.stringify(this.kData))
            this.lineSeries.setData(kData)
        },
        init() {
            this.chart = createChart(this.$refs.kline, {
                width: 60,
                height: 36,
                watermark: {
                    visible: !1
                },
                grid: {
                    vertLines: {
                        visible: !1
                    },
                    horzLines: {
                        visible: !1
                    }
                },
                layout: {
                    background: {
                        color: "#FFFFFF"
                    },
                    textColor: "rgba(255, 255, 255, 0.9)"
                },
                crosshair: {
                    mode: 'Magnet',
                    vertLine: {
                        visible: !1,
                        labelVisible: !1,
                        labelBackgroundColor: "#fff"
                    },
                    horzLine: {
                        visible: !1,
                        labelVisible: !1,
                        labelBackgroundColor: "#fff"
                    }
                },
                timeScale: {
                    visible: !1
                },
                rightPriceScale: {
                    visible: !1
                },
                handleScale: {
                    mouseWheel: !1
                },
                handleScroll: {
                    mouseWheel: !1,
                    pressedMouseMove: !1,
                    horzTouchDrag: !1,
                    vertTouchDrag: !1
                }
            });
            this.syncToInterval();
        },
    }
}
</script>

<style>

</style>
    
    