<template>
  <div class="order-box">
    <t-row>
      <t-col :span="12">
        <t-row :gutter="[15, 15]">
          <t-col :span="OrderPrice">
            <div class=" con-box">
              <div class="conbox-title">
                <div class="conbox-left">
                  地理区域统计
                  <t-icon name="chevron-right" size="17px" />
                </div>
                <div class="conbox-right">
                  <t-radio-group :value="AreaData" @change="onArea">
                    <t-radio-button value="1">国内</t-radio-button>
                    <t-radio-button value="2">全球</t-radio-button>
                  </t-radio-group>

                  &nbsp;&nbsp;&nbsp;

                  <t-icon name="code-1" size="17px" @click="onMagnify"
                    style="color: black; transform: rotate(135deg); transform-origin: center;cursor: pointer;" />
                </div>
              </div>
              <div class="conbox-con" :class="OrderSwitch ? 'order-maxheight' : 'order-height'">
                <AreaStatis ref="AreaStatisRef" />
              </div>
            </div>
          </t-col>

          <t-col :span="6">
            <div class="con-box">
              <div class="conbox-title">
                <div class="conbox-left">
                  地理数据分析
                  <t-icon name="chevron-right" size="17px" />
                </div>
                <div class="conbox-right">
                  <!-- <t-date-range-picker allow-input clearable format="YYYY-MM-DD HH:mm:ss"
                    :default-time="['00:00:00', '11:59:59']" />
                  &nbsp;&nbsp;
                  <t-select placeholder="周期" style="width: 200px;">
                    <t-option label="近七天" value="1" />
                    <t-option label="近14天" value="2" />
                    <t-option label="近30天" value="2" />
                  </t-select> -->
                  <t-radio-group :value="AreaData" @change="onArea">
                    <t-radio-button value="1">国内</t-radio-button>
                    <t-radio-button value="2">全球</t-radio-button>
                  </t-radio-group>
                  &nbsp;&nbsp;&nbsp;
                </div>
              </div>
              <div class="conbox-con" style="height: 500px;">
                <t-table :data="Performance" :pagination="Pagination" :columns="COLUMN"
                  @page-change="rehandlePageChange" bordered size="small" max-height="420">
                  <template #index="{ rowIndex }">
                    <span>
                      {{ rowIndex + 1 }}
                    </span>
                  </template>
                </t-table>
              </div>
            </div>
          </t-col>

          <t-col :span="6">
            <div class="one-row con-box">
              <div class="conbox-title">
                <div class="conbox-left">
                  用户性别统计
                  <t-icon name="chevron-right" size="17px" />
                </div>
                <div class="conbox-right">
                </div>
              </div>
              <div class="conbox-con">
                <GenderstatiStics />
              </div>
            </div>
          </t-col>

          <t-col :span="6">
            <div class="one-row con-box">
              <div class="conbox-title">
                <div class="conbox-left">
                  用户趋势图
                  <t-icon name="chevron-right" size="17px" />
                </div>
                <div class="conbox-right">
                  <!-- <t-select placeholder="请选择" style="width: 200px;">
                    <t-option label="近七天" value="1" />
                    <t-option label="近14天" value="2" />
                    <t-option label="近30天" value="2" />
                  </t-select> -->
                  &nbsp;&nbsp;&nbsp;
                </div>
              </div>
              <div class="conbox-con">
                <ChannelLine />
              </div>
            </div>
          </t-col>
        </t-row>
      </t-col>
    </t-row>
  </div>
</template>

<script lang="ts">
export default {
  name: 'CustomerStatics',
}
</script>

<script setup lang="ts">
import { nextTick, ref, onMounted } from 'vue';

import { COLUMN } from "./constants"

import { getProvince, getCountry } from "@/api/customer/index"


// 地理区域
import AreaStatis from "../components/areastatis.vue"

// 性别统计
import GenderstatiStics from "../components/genderstatis.vue"

// 渠道
import ChannelLine from "../components/channelline.vue"
import { PageInfo, TableRowData } from 'tdesign-vue-next';

// 组件
const AreaStatisRef = ref(null);


onMounted(() => {
  onGetData();
})

// 获取数据
const onGetData = async () => {
  let res

  if (AreaData.value == 1) {
    res = await getProvince({
      page: Pagination.value.defaultCurrent,
      pageSize: Pagination.value.defaultPageSize,
    });
  } else {
    res = await getCountry({
      page: Pagination.value.defaultCurrent,
      pageSize: Pagination.value.defaultPageSize,
    });
  }
  Performance.value = [];
  Performance.value = res.data.items;
  Pagination.value = {
    ...Pagination.value,
    total: res.data.total,
  };
}


// 放大操作
const OrderPrice = ref(6);
const OrderSwitch = ref(false);
const onMagnify = () => {
  OrderSwitch.value = !OrderSwitch.value;
  OrderPrice.value = OrderSwitch.value ? 12 : 6;
  AreaStatisRef.value.onResize();
  nextTick(() => {
    if (AreaData.value == 1) {
      console.log('国内');
      AreaStatisRef.value.EchartsChinainit();
    } else {
      console.log('全球');
      AreaStatisRef.value.Echartsinit();
    }
  })
};

// 表格
const Performance = ref([
]);

const Pagination = ref({
  defaultPageSize: 10,
  total: 0,
  defaultCurrent: 1,
  pageSizeOptions: [10],
});


const rehandlePageChange = (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);
  Pagination.value.defaultCurrent = pageInfo.current;
  Pagination.value.defaultPageSize = pageInfo.pageSize;
  onGetData()
};

// 地理区域
const AreaData = ref(1)
const onArea = (value: any) => {
  AreaStatisRef.value.onResize();
  AreaData.value = value;
  console.log(value);
  if (value == 1) {
    AreaStatisRef.value.EchartsChinainit();
  } else {
    AreaStatisRef.value.Echartsinit();
  }
  onGetData();
};

</script>

<style lang="less" scoped>
.order-box {
  width: 100%;
  padding: 20px;

  ::v-deep .t-table--bordered th {
    // background-color: #c9e1fb !important;
    // color: black !important;

    background-color: #e8e8e8 !important;
    // background-color: #1968a0 !important;
    color: rgb(0, 0, 0) !important;
    font-weight: bold !important;
    font-size: 16px !important;
  }

  // 公共样式
  .con-box {
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    // box-shadow: 2px 4px 12px rgba(0, 0, 0, 0.1),
    //     /* 主阴影层（增强立体感） */
    //     0 8px 20px rgba(0, 0, 0, 0.06);
    /* 次阴影层（增加深度扩散） */

    display: flex;
    flex-direction: column;

    // 标题
    .conbox-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 15px;

      .conbox-left,
      .conbox-right {
        display: flex;
        align-items: center;
      }

      .conbox-left {
        font-size: 18px;
        font-weight: 700;
      }

      .conbox-right {
        color: #8993a3;
      }
    }
  }

  .one-row {
    .conbox-con {
      height: 400px;
    }
  }

  .order-maxheight {
    height: 700px !important;
  }

  .order-height {
    height: 500px !important;
  }
}
</style>