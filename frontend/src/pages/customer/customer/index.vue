<template>
  <div class="list-common-table">
    <div class="form-item-content">
      <t-form ref="form" :data="searchParameter" :label-width="80" colon @reset="onReset" @submit="onSubmit">
        <t-row>
          <t-col :span="12">
            <t-row :gutter="[24, 24]">
              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.customerList.filterForm.email')" name="email">
                  <t-input v-model="searchParameter.email" type="search"
                    :placeholder="t('pages.customerList.filterForm.emailPlaceholder')" :style="{ minWidth: '134px' }" />
                </t-form-item>
              </t-col>
              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.customerList.filterForm.telephone')" name="telephone">
                  <t-input v-model="searchParameter.telephone" type="search"
                    :placeholder="t('pages.customerList.filterForm.telephonePlaceholder')"
                    :style="{ minWidth: '134px' }" />
                </t-form-item>
              </t-col>
              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.customerList.filterForm.platform')" name="platform">
                  <t-select v-model="searchParameter.platform" style="display: inline-block" :options="platforms"
                    :placeholder="t('pages.customerList.filterForm.platformPlaceholder')" clearable />
                </t-form-item>
              </t-col>

              <t-col :xxl="4" :xl="1" :xs="0" />

              <t-col :xxl="2" :xl="2" :xs="6" class="operation-container">
                <t-button theme="primary" type="submit" :style="{ marginLeft: 'var(--td-comp-margin-s)' }">
                  {{ t('components.commonTable.query') }}
                </t-button>
                <t-button type="reset" variant="base" theme="default"> {{ t('components.commonTable.reset') }}
                </t-button>
              </t-col>

            </t-row>
          </t-col>


        </t-row>
      </t-form>

      <div class="table-container">
        <t-table :data="data" :hide-sort-tips="true" :columns="COLUMNS" :row-keys="rowKey" :pagination="pagination"
          @sort-change="sortChange" :selected-row-keys="selectedRowKeys" :hover="true" :loading="dataLoading"
          :header-affixed-top="headerAffixedTop" @page-change="rehandlePageChange">

          <template #account="{ row }">
            <div v-if="row.email.length > 0 && row.telephone.length > 0">
              {{ row.email }} | {{ row.telephone }}
            </div>
            <div v-else="row.email.length >0">
              {{ row.email }}
            </div>
            <div v-else="row.telephone.length > 0">
              {{ row.telephone }}
            </div>
          </template>

          <template #op="slotProps">
            <t-space>
              <t-link theme="primary" @click="handleClickDetail(slotProps)"> {{ t('pages.listBase.detail') }}</t-link>
            </t-space>
          </template>
        </t-table>
      </div>

      <!--  详情 -->
      <CustomerDetail ref="customerDetailRef" />
    </div>

  </div>
</template>

<script lang="ts">
export default {
  name: 'customerList',
}
</script>

<script setup lang="ts">
import CustomerDetail from "../components/detail.vue";
import { computed, onMounted, ref } from 'vue';
import { useSettingStore } from '@/store';
import { prefix } from '@/config/global';
import { t } from '@/locales';
import type { SortInfo, TableSort } from 'tdesign-vue-next';
import { PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';
import { getList as getCustomerList } from '@/api/customer/index'
import { getAll as getAllPlatforms } from '@/api/platform/index';
import { searchParameter } from "@/api/model/customerModel";


const store = useSettingStore();

/**搜索表单*/
const searchParameter = ref<searchParameter>({
  name: '',
  platform: '',
  page: 1,
  pageSize: 20,
  sortBy: 'created_at',
  descending: true,
})

const onReset = async () => {

  searchParameter.value = {
    name: '',
    email: '',
    telephone: '',
    platform: '',
    page: 1,
    pageSize: 20,
    sortBy: 'created_at',
    descending: true,
  };

  await fetchData(searchParameter.value)

};

const onSubmit = async () => {
  dataLoading.value = true;
  const result = await fetchData(searchParameter.value)
  dataLoading.value = false;
};

/** 排序 */
const sortChange: TableProps['onSortChange'] = async (sort) => {
  console.log(sort, '----------')
  if (sort && !Array.isArray(sort)) {
    searchParameter.value = {
      ...searchParameter.value,
      sortBy: sort.sortBy,
      descending: sort.descending
    };
    await fetchData(searchParameter.value);
  }
};

//所有销售渠道
const platforms = ref([]);
const fetchPlatFormData = async () => {

  dataLoading.value = true;
  try {
    const result = await getAllPlatforms();
    console.log(result, '--------fetchPlatFormData---------')

    console.log("=======数据========>", result)
    if (result.code === 10000 && result.data && result.data.length > 0) {

      result.data.forEach((item: any) => {
        platforms.value.push({
          label: item.name,
          value: item.code
        })
      });
    }

  } catch (e) {
    console.log(e);
  } finally {
    dataLoading.value = false;
  }
};


/** 表格 **/
const COLUMNS: PrimaryTableCol<TableRowData>[] = [
  // { colKey: 'row-select', type: 'multiple', width: 64, fixed: 'left' },
  {
    colKey: 'serial-number', width: 64, fixed: 'left'
  },
  {
    title: t('pages.customerList.table.email'),
    align: 'left',
    width: 320,
    colKey: 'email',
    fixed: 'left',
  },
  {
    title: t('pages.customerList.table.telephone'),
    align: 'left',
    width: 320,
    colKey: 'telephone',
    fixed: 'left',
  },
  // {
  //   title: t('pages.customerList.table.creator'),
  //   width: 160,
  //   ellipsis: true,
  //   colKey: 'creator_name',
  // },
  {
    title: t('pages.customerList.table.createdAt'),
    width: 160,
    ellipsis: true,
    colKey: 'created_at',
    sorter: true
  },
  {
    title: t('pages.customerList.table.updatedAt'),
    align: 'left',
    fixed: 'right',
    width: 160,
    colKey: 'updated_at',
    sorter: true
  },
  {
    title: t('pages.customerList.table.operate'),
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


/** 分页 **/
const pagination = ref({
  defaultPageSize: 20,
  total: 0,
  defaultCurrent: 1,
});
const rehandlePageChange = async (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);

  searchParameter.value = {
    ...searchParameter.value,
    page: pageInfo.current,
    pageSize: pageInfo.pageSize
  };

  await fetchData(searchParameter.value);
};


/** 弹窗 **/
const customerDetailRef = ref(null)
const handleClickDetail = async (slot: { row: { id: number } }) => {
  customerDetailRef.value.customerDetailVisible = true;
  customerDetailRef.value.customerId = slot.row.id;
  customerDetailRef.value.getCustomer(slot.row.id);
  customerDetailRef.value.getCustomerPlatforms({
    customerId: slot.row.id,
    page: 1,
    pageSize: 5
  });
  customerDetailRef.value.getCustomerOrders({
    customerId: slot.row.id,
    page: 1,
    pageSize: 5
  });
  customerDetailRef.value.getCustomerShippingAddresses({
    customerId: slot.row.id,
    page: 1,
    pageSize: 5
  });
  customerDetailRef.value.getCustomerPurchasedProducts({
    customerId: slot.row.id,
    page: 1,
    pageSize: 5
  });
}

const fetchData = async (parameters: searchParameter) => {
  dataLoading.value = true;
  try {
    const result = await getCustomerList(parameters);
    console.log(result)
    data.value = result.data.items;
    pagination.value = {
      defaultPageSize: result.data.pageSize,
      total: result.data.total,
      defaultCurrent: result.data.currentPage,
    }
  } catch (e) {
    console.log(e);
  } finally {
    dataLoading.value = false;
  }
};

/* 生命周期 */
onMounted(() => {
  dataLoading.value = true;
  Promise.all([fetchPlatFormData(), fetchData(searchParameter.value)]).then(() => {
    dataLoading.value = false;
  })

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
