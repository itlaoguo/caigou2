<template>
  <t-row :gutter="[16, 16]">
    <t-col :xs="12">
      <t-card>
        <div class="" id="line-chart">

        </div>
      </t-card>
    </t-col>
  </t-row>
</template>

<script lang="ts">
export default {
  name: 'customerStatisticsLine',
};
</script>

<script setup lang="ts">
import { useWindowSize } from '@vueuse/core';
import { nextTick, onMounted, ref, watch } from 'vue';

import * as echarts from 'echarts/core';
import {
  TitleComponent,
  TitleComponentOption,
  ToolboxComponent,
  ToolboxComponentOption,
  TooltipComponent,
  TooltipComponentOption,
  GridComponent,
  GridComponentOption,
  LegendComponent,
  LegendComponentOption
} from 'echarts/components';
import { LineChart, LineSeriesOption } from 'echarts/charts';
import { UniversalTransition } from 'echarts/features';
import { CanvasRenderer } from 'echarts/renderers';
import { random } from 'lodash';


const resizeTime = ref(1);

const getLast100Days = () => {
  const result = [];
  const today = new Date();

  // 创建起始日期（当前日期的前99天）
  const startDate = new Date(today);
  startDate.setDate(today.getDate() - 99);

  // 循环生成100个递增日期
  for (let i = 0; i < 100; i++) {
    const currentDate = new Date(startDate);
    currentDate.setDate(startDate.getDate() + i);

    // 格式化为YYYYMMDD
    const yyyy = currentDate.getFullYear();
    const mm = String(currentDate.getMonth() + 1).padStart(2, '0');
    const dd = String(currentDate.getDate()).padStart(2, '0');

    result.push(`${yyyy}${mm}${dd}`);
  }

  return result;
}

// 获取100个数
const get100Numbers: (start: number, length: number) => number[] = (
  start,
  length
) => {
  const result = [];

  let preNumber = start;

  for (let i = 0; i < length; i++) {
    preNumber = preNumber + Math.floor(Math.random() * 100 + 1)
    result.push(preNumber)
  }

  return result;
}

// const getNumbers: () => number[] = () => {
//   return [1, 2, 3, 4, 5];
// }

echarts.use([
  TitleComponent,
  ToolboxComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
  LineChart,
  CanvasRenderer,
  UniversalTransition
]);

type EChartsOption = echarts.ComposeOption<
  | TitleComponentOption
  | ToolboxComponentOption
  | TooltipComponentOption
  | GridComponentOption
  | LegendComponentOption
  | LineSeriesOption
>;

var option: EChartsOption;

option = {
  title: {
    text: '销售渠道用户增长趋'
  },
  tooltip: {
    trigger: 'axis'
  },
  legend: {
    data: ['海外独立站-shop.iflight.cn', '国内独立站-www.iflight.cn', '亚马逊-美国站', '亚马逊-英国站', '线下批发渠道']
  },
  grid: {
    left: '3%',
    right: '4%',
    bottom: '3%',
    containLabel: true
  },
  toolbox: {
    feature: {
      saveAsImage: {}
    }
  },
  xAxis: {
    type: 'category',
    boundaryGap: false,
    data: getLast100Days()
  },
  yAxis: {
    type: 'value'
  },

  series: [
    {
      name: '海外独立站-shop.iflight.cn',
      type: 'line',
      stack: 'Total',
      data: get100Numbers(300, 100)
    },
    {
      name: '国内独立站-www.iflight.cn',
      type: 'line',
      stack: 'Total',
      data: get100Numbers(500, 100)
    },
    {
      name: '亚马逊-美国站',
      type: 'line',
      stack: 'Total',
      data: get100Numbers(200, 100)
    },
    {
      name: '亚马逊-英国站',
      type: 'line',
      stack: 'Total',
      data: get100Numbers(100, 100)
    },
    {
      name: '线下批发渠道',
      type: 'line',
      stack: 'Total',
      data: get100Numbers(5, 100)
    }
  ]
};

let lineContainer: HTMLElement;
let lineChart: echarts.ECharts;
const renderLineChart = () => {
  if (!lineContainer) {
    lineContainer = document.getElementById('line-chart');
  }
  lineChart = echarts.init(lineContainer);
  lineChart.setOption(option);
};

// chartSize update
const updateContainer = () => {
  if (document.documentElement.clientWidth >= 1400 && document.documentElement.clientWidth < 1920) {
    resizeTime.value = Number((document.documentElement.clientWidth / 2080).toFixed(2));
  } else if (document.documentElement.clientWidth < 1080) {
    resizeTime.value = Number((document.documentElement.clientWidth / 1080).toFixed(2));
  } else {
    resizeTime.value = 1;
  }

  lineChart.resize({
    width: lineContainer.clientWidth,
    height: resizeTime.value * 326,
  });

};

const renderCharts = () => {
  renderLineChart();
};


onMounted(() => {
  renderCharts();
  nextTick(() => {
    updateContainer();
  });
});

const { width, height } = useWindowSize();
watch([width, height], () => {
  updateContainer();
});



</script>

<style lang="less" scoped>
.dashboard-item {
  padding: var(--td-comp-paddingTB-xl) var(--td-comp-paddingLR-xxl);

  :deep(.t-card__header) {
    padding: 0;
  }

  :deep(.t-card__footer) {
    padding: 0;
  }

  :deep(.t-card__title) {
    font: var(--td-font-body-medium);
    color: var(--td-text-color-secondary);
  }

  :deep(.t-card__body) {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex: 1;
    position: relative;
    padding: 0;
    margin-top: var(--td-comp-margin-s);
    margin-bottom: var(--td-comp-margin-xxl);
  }

  &:hover {
    cursor: pointer;
  }

  &-top {
    display: flex;
    flex-direction: row;
    align-items: flex-start;

    >span {
      display: inline-block;
      color: var(--td-text-color-primary);
      font-size: var(--td-font-size-headline-medium);
      line-height: var(--td-line-height-headline-medium);
    }
  }

  &-bottom {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;

    >.t-icon {
      cursor: pointer;
      font-size: var(--td-comp-size-xxxs);
    }
  }

  &-block {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--td-text-color-placeholder);
  }

  &-trend {
    margin-left: var(--td-comp-margin-s);
  }

  &-left {
    position: absolute;
    top: 0;
    right: 0;

    >span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: var(--td-comp-size-xxxl);
      height: var(--td-comp-size-xxxl);
      background: var(--td-brand-color-light);
      border-radius: 50%;

      .t-icon {
        font-size: 24px;
        color: var(--td-brand-color);
      }
    }
  }

  // 针对第一个卡片需要反色处理
  &--main-color {
    background: var(--td-brand-color);
    color: var(--td-text-color-primary);

    :deep(.t-card__title),
    .dashboard-item-top span,
    .dashboard-item-bottom {
      color: var(--td-text-color-anti);
    }

    .dashboard-item-block {
      color: var(--td-text-color-anti);
      opacity: 0.6;
    }

    .dashboard-item-bottom {
      color: var(--td-text-color-anti);
    }
  }
}
</style>
