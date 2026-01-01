<template>
    <div id="GenderstatiStics" style="width: 100%;height: 100%;"></div>
</template>


<script lang="ts">
export default {
    name: 'GenderstatiStics',
}
</script>
<script setup lang="ts">
import { getAllGender, getPlatform, getAllCountry } from "@/api/customer/index"
import * as echarts from "echarts";
import { onMounted, ref } from "vue";

const simdata = ref({
    a: '0',
    b: '0',
    c: '0'
})

const DataTotal = ref('0')
const DataMan = ref('0')
const DataItem = ref([])

const percent = ref(null);

onMounted(() => {
    ongetExceedSpeedTrend();
});

// 初始化
const ongetExceedSpeedTrend = async () => {
    const res = await getAllGender()
    console.log(res)
    DataItem.value = res.data.items.map((item: any[]) => {
        return {
            name: item[0],
            value: item[1]

        }
    }
    )
    DataTotal.value = res.data.totalCustomers
    DataMan.value = res.data.items[0][1]

    const ratio = parseFloat(DataMan.value) / parseFloat(DataTotal.value);
    percent.value = Number(ratio.toFixed(2)) * 100 + '%';


    // 实例不要使用ref 会导致第三方库二次渲染有问题  
    const myChart = echarts.init(document.
        getElementById("GenderstatiStics"));
    const option = {
        // 鼠标移入上面时候-提示框组件
        tooltip: {
            trigger: 'item'
        },
        // 实例title
        legend: {
            top: '5%',
            left: 'left',
            // 纵向布局
            orient: 'vertical'
            // 图例高度
            // itemHeight: 6,
            // show: false
        },
        // 工具箱-下载
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        // 饼图中间-数据
        title: {
            text: '性别比例-个\n\n' + percent.value,
            top: 'middle',
            left: 'center',
            width: 110,
            textStyle: {
                rich: {
                    a: { // 第一行样式
                        color: '#333',
                        fontSize: 22,
                    },
                    b: { // 第二行样式
                        color: '#333',
                        fontSize: 30,
                        fontWeight: 'bold',
                        lineHeight: 100
                    }
                }
            },
            formatter: (params: any) => '{a|男性比例}\n{b|' + percent.value + '}'
        },

        series: [
            {
                name: '性别统计',
                type: 'pie',
                // 饼图半径
                radius: ['50%', '70%'],
                // 相对于父容器比例
                center: ['50%', '50%'],
                // 防止标签间的重叠-默认开启true
                avoidLabelOverlap: false,
                // 数据名称-静态
                label: {
                    formatter: '{name|{c}人\n\n{b}}',
                    padding: [0, -70],
                    width: 100,
                    rich: {
                        name: {
                            fontSize: 14,
                            color: '#333',
                            fontWeight: 'bold'
                        },
                        num: {
                            fontSize: 12,
                            color: '#999'
                            //lineHeight:15
                        }
                    }
                },
                // 选中图标-标题样式-静态
                emphasis: {},
                // 引导线
                labelLine: {
                    length: 15,
                    length2: 80
                },
                // data: [
                //     {
                //         value: simdata.value.b,
                //         name: '男',
                //         itemStyle: { color: '#21DA7C' }
                //     },
                //     {
                //         value: simdata.value.c,
                //         name: '女',
                //         itemStyle: { color: '#3586FF' }
                //     }
                // ]
                data: DataItem.value
            }
        ]
    }
    myChart.setOption(option);
    window.addEventListener("resize", () => {
        myChart.resize();
    });
};
</script>