<template>
  <div class="main">
    <t-form ref="form" :data="searchParameter" label-width="120px">
      <t-row>
        <t-col :span="12">
          <t-row :gutter="[24, 24]">
            <t-col :xxl="2" :xl="3" :xs="6">
              <t-form-item label="收货人" name="id">
                <t-input v-model="searchParameter.purchaseNumber" type="search"
                  :placeholder="t('pages.purchaseOrder.filterForm.purchase_number_placeholder')"
                  :style="{ minWidth: '134px' }" />
              </t-form-item>
            </t-col>
            <t-col :xxl="2" :xl="3" :xs="6">
              <t-form-item label="采购名称" name="name">
                <t-input v-model="searchParameter.purchaseName" type="search"
                  :placeholder="t('pages.purchaseOrder.filterForm.purchase_number_placeholder')"
                  :style="{ minWidth: '134px' }" />
              </t-form-item>
            </t-col>

            <t-col :xxl="2" :xl="3" :xs="6">
              <t-form-item label="采购状态" name="status">
                <t-select v-model="searchParameter.purchaseStatus" :options="statusList" placeholder="请选择采购状态"
                  clearable></t-select>
              </t-form-item>
            </t-col>

            <t-col :xxl="2" :xl="3" :xs="6">
              <t-form-item label="创建时间" name="created_at">
                <t-date-range-picker allow-input clearable :style="{ minWidth: '300px' }" />
              </t-form-item>
            </t-col>

          </t-row>
        </t-col>
      </t-row>
    </t-form>

    <div class="table-container">
      <t-table :data="data" :hide-sort-tips="true" :columns="COLUMNS" :row-keys="rowKey" :pagination="pagination"
        @sort-change="sortChange" :selected-row-keys="selectedRowKeys" :hover="true" :loading="dataLoading"
        :header-affixed-top="headerAffixedTop" @page-change="rehandlePageChange">
      </t-table>
    </div>
    <t-row>
      <t-col :span="12">
        <t-form ref="form" :data="uploadInfo" colon @submit="onSubmit">
          <t-space>
            <t-upload v-model="file" :auto-upload="autoUpload" theme="file" draggable action="" />
          </t-space>
          <t-form-item>
            <t-button theme="primary" @click="onPre">上一步</t-button>
            <t-button theme="primary" type="submit">提交</t-button>
          </t-form-item>
        </t-form>
      </t-col>
    </t-row>
  </div>

</template>

<script lang="ts" setup>
import { ref, reactive } from 'vue';
import { FormProps, UploadProps } from 'tdesign-vue-next';

const file = ref([]);
const autoUpload = ref(true);
const theme = ref('file');

const emits = defineEmits(['prev', 'next'])


const uploadInfo: FormProps['data'] = reactive({
  file: '',
});

const uploadInfoRules: FormProps['rules'] = {
  file: [
    {
      required: true,
      message: '请上传采购商品excel文件',
      type: 'error',
    },
  ],
};

const onPre = () => {
  emits('prev');
}

const onSubmit = () => {
  console.log('提交了');
}

</script>


<style lang="less" scoped>
.main {
  padding: 20px;
  background: #fff;
  width: 100%;
}
</style>