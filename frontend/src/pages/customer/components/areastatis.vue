<template>
    <!-- 图表容器 -->
    <div id="maporderChart"></div>
</template>


<script lang="ts">
export default {
    name: 'AreaStatis',
}
</script>

<script setup lang="ts">
// api
import {getAllCountry,getAllProvince} from "@/api/customer/index"
// 导入echarts核心模块
import * as echarts from 'echarts/core';
// 导入echarts组件模块
import {
    TitleComponent,
    ToolboxComponent,
    TooltipComponent,
    VisualMapComponent,
    GeoComponent,
    type TooltipComponentOption,
    type VisualMapComponentOption,
    type ToolboxComponentOption,
    type TitleComponentOption,
    type GeoComponentOption,
} from 'echarts/components';
// 导入地图图表模块
import { MapChart, type MapSeriesOption } from 'echarts/charts';
// 导入Canvas渲染器模块
import { CanvasRenderer } from 'echarts/renderers';

// 导入世界地图JSON数据
import mapJson from '@/config/world.json';
// 中国地图数据
import mapChinaJson from '@/config/china.json';


import { nextTick, onMounted, ref } from 'vue';

// 数据data
const DataItem = ref([])

let myChart: echarts.ECharts | null = null;
let chartDom = null;

// 使用echarts模块
echarts.use([
    TitleComponent,
    ToolboxComponent,
    TooltipComponent,
    VisualMapComponent,
    GeoComponent,
    MapChart,
    CanvasRenderer,
]);

// 定义ECharts配置类型
type EChartsOption = echarts.ComposeOption<
    | TitleComponentOption
    | ToolboxComponentOption
    | TooltipComponentOption
    | VisualMapComponentOption
    | GeoComponentOption
    | MapSeriesOption
>;

// 在组件挂载后执行
onMounted(() => {
    EchartsChinainit()
});

// 世界地图
const Echartsinit = async () => {
    const res = await getAllCountry();
    DataItem.value = res.data
    console.log("===>",DataItem.value)
    // 定义ECharts配置项
    const option: EChartsOption = {
        tooltip: {
            trigger: 'item',
            formatter: '{b}: {c}'
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },

        // 单一地图图层配置
        series: [{
            type: 'map',
            map: 'world',
            roam: true,
            zoom: 1.1,
            center: [28.848234, 32.82333],
            scaleLimit: {
                min: 1.5,
                max: 5
            },

            // 数据标签配置
            // label: {
            //     show: true,
            //     position: 'right',
            //     fontSize: 10,
            //     color: '#FFF',
            //     formatter: (params: any) => {
            //         return `${params.name}\n${params.value}`;
            //     },
            //     textBorderColor: '#000',
            //     textBorderWidth: 1
            // },

            // 区域样式配置
            itemStyle: {
                areaColor: 'rgb(96,126,210)',
                borderColor: '#E1ECFF',
                borderWidth: 0.5
            },

            // 高亮样式
            emphasis: {
                label: {
                    show: true,
                    fontWeight: 'bold',
                    fontSize: 12
                },
                itemStyle: {
                    areaColor: {
                        type: 'linear',
                        x: 0,
                        y: 0,
                        x2: 0,
                        y2: 1,
                        colorStops: [
                            { offset: 0, color: '#073684' },
                            { offset: 1, color: '#2B91B7' }
                        ]
                    }
                }
            },

            // 数据配置
            // data: [
            //     {
            //         name: 'Virginia',
            //         value: 81857,
            //         // 单独设置特殊区域的标签样式
            //         // label: {
            //         //     color: '#FFD700',
            //         //     fontSize: 12
            //         // }
            //     },
            //     { name: 'Brazil', value: 81857 },
            //     { name: 'Zimbabwe', value: 55413 },
            //     { name: 'China', value: 900000 },
            //     { name: 'Papua New Guinea', value: 5762 },
            //     { name: 'Puerto Rico', value: 67084 },
            // ]
            data: DataItem.value
        }],

        // 视觉映射配置
        visualMap: {
            left: '3%',
            bottom: '10%',
            // min: 1000,
            // max: 90000,
            calculable: true,
            inRange: {
                color: [
                    '#e1f1ff',
                    '#c6ddfe',
                    '#a8ccfe',
                    '#8bbdff',
                    '#5998f9',
                    '#397ff2',
                    '#1f5cce',
                    '#0d389c',
                    '#052376',
                    '#02185a',
                    '#03195a',

                ]
            },
            textStyle: {
                color: '#fff'
            }
        }
    };

    // 注册自定义地图
    echarts.registerMap(
        'world',
        mapJson as unknown as Parameters<typeof echarts.registerMap>[1],
    );
    // 获取图表容器
    chartDom = document.getElementById('maporderChart');
    // 初始化图表
    myChart = echarts.init(chartDom);
    // 设置图表配置项
    myChart.setOption(option);
    window.addEventListener("resize", () => {
        myChart.resize();
    });
};

// 中国地图
const EchartsChinainit = async () => {
    const res = await getAllProvince();
    DataItem.value = res.data
    console.log("===>",DataItem.value);
    // 定义ECharts配置项
    const option: EChartsOption = {
        tooltip: {
            trigger: 'item',
            formatter: '{b}: {c}'
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },

        // 单一地图图层配置
        series: [{
            type: 'map',
            map: 'China',
            roam: true,
            zoom: 1.6,
            center: [103.822482,33.830765],
            scaleLimit: {
                min: 1.5,
                max: 5
            },

            // 数据标签配置
            label: {
                show: true,
                position: 'right',
                fontSize: 10,
                color: '#FFF',
                textBorderColor: '#000',
                textBorderWidth: 1
            },

            // 区域样式配置
            itemStyle: {
                areaColor: 'rgb(96,126,210)',
                borderColor: '#E1ECFF',
                borderWidth: 0.5
            },

            // 高亮样式
            emphasis: {
                label: {
                    show: true,
                    fontWeight: 'bold',
                    fontSize: 12
                },
                itemStyle: {
                    areaColor: {
                        type: 'linear',
                        x: 0,
                        y: 0,
                        x2: 0,
                        y2: 1,
                        colorStops: [
                            { offset: 0, color: '#073684' },
                            { offset: 1, color: '#2B91B7' }
                        ]
                    }
                }
            },
            // 数据配置
            data: DataItem.value
        }],

        // 视觉映射配置
        visualMap: {
            left: '3%',
            bottom: '10%',
            // min: 1000,
            // max: 90000,
            calculable: true,
            inRange: {
                color: [
                    '#e1f1ff',
                    '#c6ddfe',
                    '#a8ccfe',
                    '#8bbdff',
                    '#5998f9',
                    '#397ff2',
                    '#1f5cce',
                    '#0d389c',
                    '#052376',
                    '#02185a',
                    '#03195a',

                ]
            },
            textStyle: {
                color: '#fff'
            }
        }
    };

    // 注册自定义地图
    echarts.registerMap(
        'China',
        mapChinaJson as unknown as Parameters<typeof echarts.registerMap>[1],
    );
    // 获取图表容器
    chartDom = document.getElementById('maporderChart');
    // 初始化图表
    myChart = echarts.init(chartDom);
    // 设置图表配置项
    myChart.setOption(option);
    window.addEventListener("resize", () => {
        myChart.resize();
    });
};

const onResize = () => {
    myChart.dispose()

    // nextTick(() => {
    //     Echartsinit()
    // })
};

// 暴露
defineExpose({ Echartsinit, onResize,EchartsChinainit });


</script>

<style scoped lang="less">
#maporderChart {
    width: 100%;
    height: 100%;
}
</style>