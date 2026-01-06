<template>
  <div class="list-common-table" style="min-height: calc(100vh - 130px);">
    <div class="form-item-content">

    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: 'puchaseOrderList',
}
</script>

<script setup lang="ts">
import orderDetail from "../components/detail.vue";
import { computed, onMounted, ref } from 'vue';
import { t } from '@/locales';
import { PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';


</script>

<style lang="less" scoped>
.list-common-table {
  // background-color: var(--td-bg-color-container);
  padding: 15px;
  border-radius: var(--td-radius-medium);

  .table-container {
    margin-top: var(--td-comp-margin-xxl);
  }
}

.form-item-content {
  width: 100%;
  background: #fff;
  padding: 20px;
  border-radius: 10px;
}

.operation-container {
  display: flex;
  justify-content: flex-end;
  align-items: center;

  .expand {
    .t-button__text {
      display: flex;
      align-items: center;
    }
  }
}
</style>
