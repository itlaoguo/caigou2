<template>
  <div class="list-common-table" style="min-height: calc(100vh - 130px);">
    <div class="form-item-content">

    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: 'marketingList',
}
</script>

<script setup lang="ts">
import MarketingDetail from "../components/detail.vue";
import MarketingForm from "../components/form.vue";
import { computed, onMounted, ref } from 'vue';
import { useSettingStore } from '@/store';
import { prefix } from '@/config/global';
import { t } from '@/locales';
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';

</script>

<style lang="less" scoped>
.action-section {
  margin-bottom: var(--td-comp-margin-xxl);
  margin-left: 15px !important;
}

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
