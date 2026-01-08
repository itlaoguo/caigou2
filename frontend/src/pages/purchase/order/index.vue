<template>
  <div class="list-common-table" style="min-height: calc(100vh - 130px);">
    <div class="form-item-content">
      <t-form ref="form" :rules="FORM_RULES" :data="formData" :colon="true">
        <t-form-item label="姓名" name="name">
          <t-input v-model="formData.name" placeholder="请输入内容" @enter="onEnter"></t-input>
        </t-form-item>
        <t-form-item label="手机号码" name="tel">
          <t-input v-model="formData.tel" placeholder="请输入内容" @enter="onEnter"></t-input>
        </t-form-item>

        <t-form-item label="接收短信" name="status">
          <t-switch v-model="formData.status"></t-switch>
        </t-form-item>

        <t-form-item label="性别" name="gender">
          <t-radio-group v-model="formData.gender">
            <t-radio value="1">男</t-radio>
            <t-radio value="2">女</t-radio>
          </t-radio-group>
        </t-form-item>
        <t-form-item label="课程" name="course">
          <t-checkbox-group v-model="formData.course" :options="courseOptions"></t-checkbox-group>
        </t-form-item>
        <t-form-item>
          <t-space size="small">
            <t-button theme="primary" type="submit">提交</t-button>
            <t-button theme="default" type="reset">重置</t-button>
          </t-space>
        </t-form-item>
      </t-form>
    </div>

    <div class="">

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
