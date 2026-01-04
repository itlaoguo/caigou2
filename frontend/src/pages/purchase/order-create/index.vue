<template>
  <div class="list-common-table" style="min-height: calc(100vh - 130px);">
    
  </div>
</template>

<script lang="ts">
export default {
  name: 'orderCreate',
}
</script>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { t } from '@/locales';

import { PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';
import { getList as getOrderList } from '@/api/order/index';
import { getAll as getAllPlatForms } from '@/api/platform/index';
import { searchParameter } from "@/api/model/orderModel";


//搜索表单
const PAY_STATUS = [
  { value: '0', label: t('pages.orderList.pay_status.pendding_pay') },
  { value: '1', label: t('pages.orderList.pay_status.paid') },
  { value: '2', label: t('pages.orderList.pay_status.part_paid') }
]

const searchParameter = ref<searchParameter>({
  name: '',
  platform: '',
  payStatus: '',
  page: 1,
  pageSize: 20,
  sortBy: 'created_at',
  descending: true,
})

const onReset = async () => {
  searchParameter.value = {
    name: '',
    platform: '',
    payStatus: '',
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


</style>
