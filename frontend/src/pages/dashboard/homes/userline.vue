<template>
    <div id="UserLine" style="width: 100%;height: 100%;"></div>
</template>


<script lang="ts">
export default {
    name: 'UserLine',
}
</script>
<script setup lang="ts">
import * as echarts from "echarts";
import { onMounted, ref } from "vue";

const lineX = ref(['2025-1', '2025-2', '2025-3', '2025-4', '2025-5', '2025-6', '2025-7', '2025-8', '2025-9', '2025-10', '2025-11', '2025-12']);
const dataone = ref([200, 150, 146, 120, 140, 160, 50, 146, 120, 140, 160, 50]);
const datatwo = ref([150, 140, 130, 110, 150, 160, 20, 146, 120, 140, 160, 50]);

const LineaData = ref([])


onMounted(() => {
    // ongetExceedSpeedTrend();
});

// 初始化
const ongetExceedSpeedTrend = async (item: any) => {
    // 调用api
    console.log("订单组件==>", item);

    LineaData.value = item

    // 实例不要使用ref 会导致第三方库二次渲染有问题  
    const myChart = echarts.init(document.getElementById("UserLine"));
    const option = {
        title: {
            text: ''
        },
        // 提示框
        tooltip: {
            //触发类型
            trigger: 'axis',
            // 提示框的背景颜色
            backgroundColor: '#2c3e50',
            // 提示框边框厚度
            borderWidth: 0,
            // 提示款边框颜色
            borderColor: '#ccc',
            // 提示框字体颜色
            textStyle: {
                color: '#fff'
            },
            // 选中数据时x-y轴样式
            axisPointer: {
                type: 'cross',
                // 小方块
                label: {
                    // 小方块背景颜色
                    backgroundColor: '#2c3e50'
                    // backgroundColor: '#e10602'
                    // 小方块字体颜色
                    // color: '#e10602'
                }
            },
            formatter: function (params: any[]) {
                let res = params[0].name + '<br>';  // X 轴类目名:ml-citation{ref="1,3" data="citationList"}
                params.forEach(item => {
                    res += `${item.marker} ${item.seriesName}: ${item.value} 个<br>`; // 数据值后追加单位:ml-citation{ref="4,6" data="citationList"}
                });
                return res;
            }
        },
        legend: {
            // data: ['Email', 'Union Ads', 'Video Ads', 'Direct', 'Search Engine']
            //字体样式
            textStyle: {
                //   color: '#E0E0E0',
                color: '#1e1e1e',
                fontSize: 13
            },
            //图标形状
            icon: 'roundRect',
            orient: 'horizontal',
            itemWidth: 13,
            itemHeight: 3,
            right: '5%',
            top: 0
        },
        // 工具箱-下载
        toolbox: {
            feature: {
                saveAsImage: {
                    title: '下载用户趋势图',
                    type: 'png',
                    pixelRatio: 2
                }
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: [
            {
                // 单位
                name: '',
                type: 'category',
                boundaryGap: false,
                data: LineaData.value[0].statistics.keys,
                axisTick: {
                    show: false
                    // length:7
                },
                // axisLine: {
                //     lineStyle: {
                //         color: '#1f1f1f',
                //     }
                // },
                axisLabel: {
                    color: '#1f1f1f',
                    margin: 10
                },
                nameTextStyle: {
                    color: '#1f1f1f',
                    fontSize: 8
                },
                // splitLine: {
                //     lineStyle: {
                //         color: 'rgba(255,255,255,0.08)'
                //     }
                // }
            }
        ],
        yAxis: [
            {
                type: 'value',
                name: '',
                splitNumber: 3,
                // axisLine: {
                //     show: true,
                //     lineStyle: {
                //         color: 'rgba(32, 121, 160, 0.3)'
                //     }
                // },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    color: '#1f1f1f',
                    margin: 10
                },
                // splitLine: {
                //     lineStyle: {
                //         color: 'rgba(32, 121, 160, 0.3)'
                //     }
                // },
                // splitArea: {
                //     areaStyle: {
                //         color: 'rgba(2, 29, 159, 0.7)'
                //     }
                // }
            }
        ],
        series: [
            {
                name: LineaData.value[0].platform.name,
                type: 'line',
                symbol: 'circle',
                smooth: true,
                // stack: 'Total',
                // 鼠标在图例上的效果
                emphasis: {
                    // 选中这条数据，隐藏其他数据
                    // focus: 'series'
                },
                data: LineaData.value[0].statistics.values,
                // 折线变得圆滑
                // smooth: true,
                // 把点去掉
                // showSymbol: false,
                // 折线颜色
                lineStyle: {
                    width: 3,
                    color: '#32C5FF'
                    // color: '#1e1e1e'
                },
                // 标头-颜色
                itemStyle: {
                    color: '#32C5FF'
                    // color: '#1e1e1e'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {
                            offset: 0,
                            color: 'rgba(3, 114, 255, 0.1)'
                        },
                        {
                            offset: 1,
                            color: 'rgba(3, 114, 255, 0.1)'
                        }
                    ])
                }

            },
            {
                name: LineaData.value[1].platform.name,
                type: 'line',
                symbol: 'circle',
                smooth: true,
                // stack: 'Total',
                emphasis: {
                    // focus: 'series'
                },
                data: LineaData.value[1].statistics.values,
                // 折线变得圆滑
                // smooth: true,
                // 把点去掉
                // showSymbol: false,
                lineStyle: {
                    width: 3,
                    color: '#eb693f'
                },
                itemStyle: {
                    color: '#eb693f'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {
                            offset: 0,
                            color: 'rgba(3, 114, 255, 0.1)'
                        },
                        {
                            offset: 1,
                            color: 'rgba(3, 114, 255, 0.1)'
                        }
                    ])
                }
            },
            {
                name: LineaData.value[2].platform.name,
                symbol: 'circle',
                type: 'line',
                smooth: true,
                // stack: 'Total',
                emphasis: {
                    // focus: 'series'
                },
                data: LineaData.value[2].statistics.values,
                // 折线变得圆滑
                // smooth: true,
                // 把点去掉
                // showSymbol: false,
                lineStyle: {
                    width: 3,
                    color: '#2fbda1'
                },
                itemStyle: {
                    color: '#2fbda1'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {
                            offset: 0,
                            color: 'rgba(3, 114, 255, 0.1)'
                        },
                        {
                            offset: 1,
                            color: 'rgba(3, 114, 255, 0.1)'
                        }
                    ])
                }
            },
            {
                name: LineaData.value[3].platform.name,
                symbol: 'circle',
                type: 'line',
                smooth: true,
                // stack: 'Total',
                emphasis: {
                    // focus: 'series'
                },
                data: LineaData.value[3].statistics.values,
                // 折线变得圆滑
                // smooth: true,
                // 把点去掉
                // showSymbol: false,
                lineStyle: {
                    width: 3,
                    color: '#0368FF'
                },
                itemStyle: {
                    color: '#0368FF'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {
                            offset: 0,
                            color: 'rgba(3, 114, 255, 0.1)'
                        },
                        {
                            offset: 1,
                            color: 'rgba(3, 114, 255, 0.1)'
                        }
                    ])
                }
            }
        ]
    };
    myChart.setOption(option);
    window.addEventListener("resize", () => {
        myChart.resize();
    });
};

// 暴露
defineExpose({ ongetExceedSpeedTrend });
</script>