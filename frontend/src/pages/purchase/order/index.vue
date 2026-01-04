<template>
  <div class="list-common-table" style="min-height: calc(100vh - 130px);">
    <div class="form-item-content">
      <t-form ref="form" :data="searchParameter" :label-width="80" colon @reset="onReset" @submit="onSubmit"
        class="form-item-content">
        <t-row>
          <t-col :span="12">
            <t-row :gutter="[24, 24]">
              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.purchaseOrder.filterForm.purchase_number')" name="customer">
                  <t-input v-model="searchParameter.purchaseNumber" type="search"
                    :placeholder="t('pages.purchaseOrder.filterForm.purchase_number_placeholder')"
                    :style="{ minWidth: '134px' }" />
                </t-form-item>
              </t-col>
              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.purchaseOrder.filterForm.purchase_number')" name="platform">
                  <t-select v-model="searchParameter.platform" style="display: inline-block" :options="platforms"
                    :placeholder="t('pages.orderList.filterForm.platformPlaceholder')" clearable />
                </t-form-item>
              </t-col>
              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.purchaseOrder.filterForm.purchase_number')" name="pay_status">
                  <t-select v-model="searchParameter.payStatus" style="display: inline-block" :options="PAY_STATUS"
                    :placeholder="t('pages.orderList.filterForm.payStatusPlaceholder')" clearable />
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
        <t-table :data="data" :columns="COLUMNS" :pagination="pagination" :selected-row-keys="selectedRowKeys"
          row-key="order_id" :loading="dataLoading" @page-change="rehandlePageChange">

          <template #total="{ row }">
            {{ row.currency }}&nbsp;{{ row.total }}
          </template>

          <template #op="slotProps">
            <t-space>
              <t-link theme="primary" @click="handleClickDetail(slotProps)"> {{ t('pages.listBase.detail') }}</t-link>
            </t-space>
          </template>
        </t-table>

        <!--  详情 -->
        <orderDetail ref="orderDetailRef" />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: 'customerList',
}
</script>

<script setup lang="ts">
import orderDetail from "../components/detail.vue";
import { computed, onMounted, ref } from 'vue';
import { t } from '@/locales';
import { PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';
import { getList as getOrderList } from '@/api/order/index';
import { getAll as getAllPlatForms } from '@/api/platform/index';
import { searchParameter } from "@/api/model/purchaseOrderModel";


//搜索表单
const PAY_STATUS = [
  { value: '0', label: t('pages.orderList.pay_status.pendding_pay') },
  { value: '1', label: t('pages.orderList.pay_status.paid') },
  { value: '2', label: t('pages.orderList.pay_status.part_paid') }
]

const searchParameter = ref<searchParameter>({
  purchaseNumber: '',
  purchaseName: '',
  purchaseStatus: 1,
  purchaseStartedAt: '',
  purchaseEndedAt: '',
  page: 1,
  pageSize: 20,
  sortBy: 'created_at',
  descending: true,
})

const onReset = async () => {
  searchParameter.value = {
    purchaseNumber: '',
    purchaseName: '',
    purchaseStatus: 1,
    purchaseStartedAt: '',
    purchaseEndedAt: '',
    page: 1,
    pageSize: 20,
    sortBy: 'created_at',
    descending: true,
  };

  await fetchData(searchParameter.value)

};

const onSubmit = async () => {
  await fetchData(searchParameter.value)
};

//所有销售渠道
//所有销售渠道
const platforms = ref([]);
const fetchPlatFormData = async () => {

  dataLoading.value = true;
  try {
    const result = await getAllPlatForms();
    console.log(result, '--------fetchPlatFormData---------')

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


//表格
const COLUMNS: PrimaryTableCol<TableRowData>[] = [
  { colKey: 'row-select', type: 'multiple', width: 64, fixed: 'left' },
  {
    title: t('pages.orderList.table.number'),
    align: 'left',
    width: 180,
    colKey: 'number',
    fixed: 'left',
  },
  {
    title: t('pages.orderList.table.email'),
    align: 'left',
    width: 220,
    colKey: 'customer_email',
    fixed: 'left',
  },
  {
    title: t('pages.orderList.table.telephone'),
    align: 'left',
    width: 220,
    colKey: 'customer_telephone',
    fixed: 'left',
  },
  {
    title: t('pages.orderList.table.company'),
    align: 'left',
    width: 200,
    colKey: 'company_name',
  },
  {
    title: t('pages.orderList.table.platform'),
    width: 160,
    colKey: 'platform_name',
  },
  // {
  //   title: t('pages.orderList.table.platform_order_id'),
  //   width: 220,
  //   colKey: 'platform_order_id',
  //   sorter: true
  // },
  // {
  //   title: t('pages.orderList.table.type'),
  //   width: 160,
  //   ellipsis: true,
  //   colKey: 'type',
  // },
  {
    title: t('pages.orderList.table.total'),
    width: 160,
    ellipsis: true,
    colKey: 'total',
  },
  {
    title: t('pages.orderList.table.creator'),
    width: 160,
    ellipsis: true,
    colKey: 'creator_name',
  },
  {
    title: t('pages.orderList.table.created_at'),
    width: 160,
    colKey: 'created_at',
    sorter: true
  },
  {
    title: t('pages.orderList.table.updated_at'),
    align: 'left',
    width: 160,
    colKey: 'updated_at',
    sorter: true
  },
  {
    title: t('pages.orderList.table.operate'),
    align: 'left',
    fixed: 'right',
    width: 160,
    colKey: 'op',
  },
];

const dataLoading = ref(false);
const selectedRowKeys = ref([]);
const data = ref([]);

//分页
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

  const result = await fetchData(searchParameter.value);
};


/** 弹窗 **/
const orderDetailRef = ref(null)
const handleClickDetail = async (slot: { row: { order_id: number } }) => {
  console.log('============orderId=============', slot.row.order_id);
  orderDetailRef.value.orderDetailVisible = true;
  orderDetailRef.value.orderId = slot.row.order_id;
  orderDetailRef.value.getOrderInfo(slot.row.order_id);
}

//
onMounted(() => {
  dataLoading.value = true;
  Promise.all([fetchPlatFormData(), fetchData(searchParameter.value)]).then(() => {
    dataLoading.value = false;
  })
});

const fetchData = async (parameters: searchParameter) => {
  dataLoading.value = true;
  try {
    const result = await getOrderList(parameters);
    console.log('===============fetchData获取订单列表=================', result)
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
