<template>
  <div class="list-common-table">
    <div class="form-item-content">
      <t-form ref="form" :data="searchParameter" label-width="120px">
        <t-row>
          <t-col :span="12">
            <t-row :gutter="[24, 24]">
              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.purchaseOrder.filterForm.purchase_number')" name="purchaseNumber">
                  <t-input v-model="searchParameter.purchaseNumber" type="search"
                    :placeholder="t('pages.purchaseOrder.filterForm.purchase_number_placeholder')"
                    :style="{ minWidth: '134px' }" />
                </t-form-item>
              </t-col>
              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.purchaseOrder.filterForm.purchase_name')" name="purchaseName">
                  <t-input v-model="searchParameter.purchaseName" type="search"
                    :placeholder="t('pages.purchaseOrder.filterForm.purchase_number_placeholder')"
                    :style="{ minWidth: '134px' }" />
                </t-form-item>
              </t-col>

              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.purchaseOrder.filterForm.purchase_name')" name="purchaseName">
                  <t-input v-model="searchParameter.purchaseName" type="search"
                    :placeholder="t('pages.purchaseOrder.filterForm.purchase_number_placeholder')"
                    :style="{ minWidth: '134px' }" />
                </t-form-item>
              </t-col>

              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.purchaseOrder.filterForm.purchase_name')" name="purchaseName">
                  <t-input v-model="searchParameter.purchaseName" type="search"
                    :placeholder="t('pages.purchaseOrder.filterForm.purchase_number_placeholder')"
                    :style="{ minWidth: '134px' }" />
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
    </div>

  </div>
</template>

<script lang="ts">
export default {
  name: 'purchaseOrderList',
}
</script>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue';
import { useSettingStore } from '@/store';
import { prefix } from '@/config/global';
import { t } from '@/locales';
import type { SortInfo, TableSort } from 'tdesign-vue-next';
import { PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';
import { SearchParameter, ListResult } from "@/api/model/purchaseModel";

const store = useSettingStore();

const searchParameter: SearchParameter = reactive({
  purchaseNumber: '',
  purchaseName: '',
  purchaseStatus: undefined,
  purchaseStartedAt: '',
  purchaseEndedAt: '',
  page: 1,
  pageSize: 10,
  sortBy: '',
  descending: false,
});


/** 表格 **/
const COLUMNS: PrimaryTableCol<TableRowData>[] = [
  { colKey: 'row-select', type: 'multiple', width: 64, fixed: 'left' },
  // {
  //   colKey: 'serial-number', width: 64, fixed: 'left'
  // },
  {
    title: t('pages.purchaseOrder.table.purchase_name'),
    align: 'left',
    width: 320,
    colKey: 'purchase_name',
    fixed: 'left',
  },
   {
    title: t('pages.purchaseOrder.table.created_at'),
    width: 160,
    ellipsis: true,
    colKey: 'created_at',
    sorter: true
  },
  {
    title: t('pages.purchaseOrder.table.updated_at'),
    align: 'left',
    fixed: 'right',
    width: 160,
    colKey: 'updated_at',
    sorter: true
  },
  {
    title: t('pages.purchaseOrder.table.status'),
    align: 'left',
    width: 320,
    colKey: 'status',
    fixed: 'left',
  },
  {
    title: t('pages.purchaseOrder.table.detail_status'),
    align: 'left',
    width: 320,
    colKey: 'status',
    fixed: 'left',
  },
  {
    title: t('pages.purchaseOrder.table.operate'),
    align: 'left',
    fixed: 'right',
    width: 160,
    colKey: 'op',
  },
];
const rowKey = 'index';

const headerAffixedTop = computed(
  () =>
    ({
      offsetTop: store.isUseTabsRouter ? 48 : 0,
      container: `.${prefix}-layout`,
    }) as any, // TO BE FIXED
);

const dataLoading = ref(false);
const selectedRowKeys = ref([]);
const data = ref([]);

/** 排序 */
const sortChange: TableProps['onSortChange'] = async (sort) => {
  console.log(sort, '----------')
  if (sort && !Array.isArray(sort)) {
    searchParameter.sortBy = sort.sortBy;
    searchParameter.descending = sort.descending;
  }
};

/** 分页 **/
const pagination = ref({
  defaultPageSize: 20,
  total: 0,
  defaultCurrent: 1,
});
const rehandlePageChange = async (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);

  searchParameter.page = pageInfo.current;
  searchParameter.pageSize = pageInfo.pageSize;

};

const onSubmit = () => {
  console.log('搜索参数：', searchParameter);
  // TODO: 执行搜索逻辑
};

/* 生命周期 */
onMounted(() => {

});

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
