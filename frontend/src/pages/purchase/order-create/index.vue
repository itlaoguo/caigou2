<template>
  <div class="list-common-table" style="min-height: calc(100vh - 130px);">
    <div class="form-item-content">
      <t-steps :current="currentStep" readonly>
        <t-step-item title="创建采购单" content="选择导入方式和基本配置" />
        <t-step-item title="导入excel" content="按照模板导入表单" />
        <t-step-item title="确认采购详情" content="确认下单" />
      </t-steps>
      <t-row>
        <BasicInfo v-if="currentStep === 0" @next="next" />
        <UploadInfo v-if="currentStep === 1" @next="next" @prev="prev" />
        <ProductList v-if="currentStep === 2" @prev="prev" @reset="reset" />
      </t-row>
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: 'purchaseOrderCreate',
}
</script>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { t } from '@/locales';

import BasicInfo from '../components/basicInfo.vue'
import UploadInfo from '../components/uploadInfo.vue'
import ProductList from '../components/productList.vue'

import { PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';

let currentStep = ref(0);

const next = () => {
  if (currentStep.value < 2) {
    currentStep.value++
  }
}
const prev = () => {
  if (currentStep.value > 0) {
    currentStep.value--
  }
}

const reset = () => {
  currentStep.value = 0;
}


//
onMounted(() => {

});



</script>

<style lang="less" scoped>
#next-step-btn,
#pre-step-btn {
  width: 120px;
  margin: 0 50px;
}

.list-common-table {
  // background-color: var(--td-bg-color-container);
  padding: 15px;
  border-radius: var(--td-radius-medium);

  .form-item-content {
    width: 100%;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
  }

  .table-container {
    margin-top: var(--td-comp-margin-xxl);
  }
}
</style>
