<template>
  <t-form ref="form" class="base-form" :data="formData" :rules="FORM_RULES" label-align="left" :label-width="100"
    @reset="onReset" @submit="onSubmit">
    <div class="form-basic-container">
      <div class="form-basic-item">

        <div class="form-basic-container-title">
          <p /> {{ DetailsSwticher ? '添加' : '查看' }} 工资单
        </div>

        <t-divider align="left">1.基本信息</t-divider>
        <t-row class="row-gap" :gutter="[32, 24]">

          <!-- 第二行 -->
          <t-col :span="3">
            <t-form-item label="公司主体" name="name">
              <t-select v-model="formData.type" :style="{ width: '220px' }" class="demo-select-base" clearable>
                <t-option v-for="(item, index) in TYPE_OPTIONS_COMPANY" :key="index" :value="item.value"
                  :label="item.label">
                  {{ item.label }}
                </t-option>
              </t-select>
            </t-form-item>
          </t-col>
          <t-col :span="3">
            <t-form-item label="工资月份" name="type">
              <t-date-picker mode="month" v-model="formData.type" clearable allow-input />
            </t-form-item>
          </t-col>
        </t-row>

        <t-divider align="left">
          2.基本信息&nbsp;&nbsp;&nbsp;
          <t-button theme="success">
            <template #icon> <t-icon name="add" /></template>
            导入
          </t-button>
        </t-divider>

        <!-- 内容 -->
        <t-table :data="RewardsData" :columns="COLUMNS" bordered :column-controllter="columnControllerConfig"
          maxHeight="700">
          <template #index="{ rowIndex }">
            <span>
              {{ rowIndex + 1 }}
            </span>
          </template>

          <!-- 操作 -->
          <template v-if="DetailsSwticher" #op="slotProps">
            <t-space>
              <!-- 详情 -->
              <t-tooltip content="编辑" placement="top" theme="light">
                <t-button variant="text" @click="handleClickDetails">
                  <t-icon name="edit" />
                </t-button>
              </t-tooltip>
              <!-- 删除 -->
              <t-tooltip content="删除" placement="top" theme="light">
                <t-button variant="text" @click="handleClickDelete">
                  <t-icon name="delete" />
                </t-button>
              </t-tooltip>
            </t-space>
          </template>
        </t-table>

      </div>
    </div>


    <div class="form-submit-container">
      <div class="form-submit-sub">
        <div class="form-submit-left">
          <t-button v-if="DetailsSwticher" theme="primary" class="form-submit-confirm" type="submit">
            {{ t('pages.formBase.confirm') }}
          </t-button>
          <t-button type="reset" class="form-submit-cancel" theme="default" variant="base">
            {{ t('pages.formBase.cancel') }}
          </t-button>
        </div>
      </div>
    </div>
  </t-form>
</template>

<script lang="ts">
export default {
  name: 'DashboardBase',
}
</script>

<script setup lang="ts">
import { MessagePlugin, SubmitContext } from 'tdesign-vue-next';
import { ref, defineProps, onMounted, computed } from 'vue';
import { t } from '@/locales';
import { FORM_RULES, INITIAL_DATA } from '../constants';
import { TYPE_OPTIONS_COMPANY, COLUMNS } from "./constants"


const formData = ref({ ...INITIAL_DATA });

const datafun = defineProps({
  contractId: {
    type: String,
    default: ''
  },
  onaddswtich: {
    type: Function,
  },
  DetailsSwticher: {
    type: Boolean,
  }
})

onMounted(() => {
  DetailsSwticher.value = datafun.DetailsSwticher
})

// 查看开关
const DetailsSwticher = ref(true);

// 添加导入表单
const RewardsData = ref([{ a: "22" }, {
  a: "22",
  b: "22",
  c: "22",
  d: "22",
  e: "22",
  f: "22",
  g: "22",
  h: "22",
  i: "22",
  cc: "22",
}
]);

// 查看
const handleClickDetails = () => {

};

const handleClickDelete = () => {

};

// 筛选数组
const columnControllerConfig = computed(() => ({
  // 列配置按钮位置
  placement: 'top-right',
  // 用于设置允许用户对哪些列进行显示或隐藏的控制，默认为全部字段
  fields: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'cc',],

  // 弹框组件属性透传
  dialogProps: { preventScrollThrough: true },
  // 列配置按钮组件属性透传
  buttonProps: false
}));

const onReset = () => {
  datafun.onaddswtich();
  MessagePlugin.warning('取消新建');
};
const onSubmit = (ctx: SubmitContext) => {
  if (ctx.validateResult === true) {
    MessagePlugin.success('新建成功');
  }
};


</script>

<style lang="less" scoped>
@import "@/style/public/add.less";

.form-basic-item {
  width: 100% !important;
}

.form-submit-container {
  border-top: 0 !important;
}

::v-deep .t-divider--with-text::before,
.t-divider--with-text::after {
  border: 2px solid #409eff !important;
}

::v-deep .t-table--bordered th {
  // background-color: #c9e1fb !important;
  // color: black !important;

  background-color: #125be2 !important;
  // background-color: #1968a0 !important;
  color: white !important;
  font-weight: bold !important;
  font-size: 16px !important;
}
</style>