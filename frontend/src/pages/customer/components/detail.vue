<template>
  <div>
    <t-dialog header="用户详情" :footer="false" width="60%" v-model:visible="customerDetailVisible" :on-close="closeDialog"
      :top="'10vh'">
      <div class="detail">
        <t-descriptions :label-style="{ width: '120px', textAlign: 'left' }" :column="3"
          :content-style="{ textAlign: 'left' }">
          <t-descriptions-item label="电子邮箱">{{ customer.email }}</t-descriptions-item>
          <t-descriptions-item label="手机号码">{{ customer.telephone }}</t-descriptions-item>
          <t-descriptions-item label="创建人">{{ customer.createBy }}</t-descriptions-item>
          <t-descriptions-item label="注册日期">{{ customer.createdAt }}</t-descriptions-item>
          <t-descriptions-item label="最近活动时间">{{ customer.updatedAt }}</t-descriptions-item>
        </t-descriptions>
        <t-divider />
        <!-- <t-tabs v-model="consumptionRecordTabValue" size="large"> -->
        <t-tabs size="large" v-model="customerTabValue">
          <t-tab-panel :value="1" label="活动平台" :destroy-on-hide="false">
            <t-table :columns="platformColumns" :data="platformList" :pagination="platformPagination" :hover="true"
              height="300" :stripe="true" row-key="index" size="large" @page-change="handlePlatformPageChange">
              <!-- <t-table :columns="orderColumns" :data="orderList" :pagination="orderPagination" :hover="true"
              :stripe="true" row-key="index" size="large" @sort-change="sortChange"> -->
              <template #gender="{ row }">
                <span v-if="row.gender == 0" size="medium" style="margin-left: var(--td-comp-margin-s)">未知</span>
                <span v-if="row.gender == 1" size="medium" style="margin-left: var(--td-comp-margin-s)">男</span>
                <span v-if="row.gender == 2" size="medium" style="margin-left: var(--td-comp-margin-s)">女</span>
              </template>
            </t-table>
          </t-tab-panel>
          <t-tab-panel :value="2" label="订单记录" :destroy-on-hide="false">
            <t-table :columns="orderColumns" :data="orderList" :pagination="orderPagination" :hover="true" height="300"
              :stripe="true" row-key="index" size="large" @page-change="handleOrderPageChange">
              <template #pay_status="{ row }">
                <span v-if="row.pay_status == 0" size="medium" style="margin-left: var(--td-comp-margin-s)">未收款</span>
                <span v-if="row.pay_status == 1" size="medium" style="margin-left: var(--td-comp-margin-s)">已收款</span>
                <span v-if="row.pay_status == 2" size="medium" style="margin-left: var(--td-comp-margin-s)">部分收款</span>
              </template>
            </t-table>
          </t-tab-panel>
          <t-tab-panel :value="3" label="产品购买记录" :destroy-on-hide="false">
            <t-table :columns="productColumns" :data="productList" :pagination="productPagination" :hover="true"
              height="300" :stripe="true" row-key="index" size="large" @page-change="handleProductPageChange">
              <template #product_image="{ row }">
                <img :src="row.product_image" alt="" srcset="" width="50" height="50">
              </template>

              <template #quantity="{ row }">
                {{ row.quantity }} {{ row.unit }}
              </template>
            </t-table>
          </t-tab-panel>
          <t-tab-panel :value="4" label="收货地址" :destroy-on-hide="false">
            <t-table :columns="shippingAddressColumns" :data="shippingAddressList" height="300"
              :pagination="shippingAddressPagination" :hover="true" :stripe="true" row-key="shippingAddressIndex"
              size="large" @page-change="handleShippingAddressPageChange">
              <template #shippingAddress="{ row }">
                <span></span>
              </template>
              <template #op="slotProps">
                <t-space>
                  <!-- <t-link theme="primary" @click="showShippingAddressDetail()">
                    {{ t('pages.customerDetail.shippingAddressTable.operate.detail') }}
                  </t-link> -->
                  <t-link theme="primary">
                    {{ t('pages.customerDetail.shippingAddressTable.operate.detail') }}
                  </t-link>
                </t-space>
              </template>

              <template #op-column>
                <t-icon name="descending-order" />
              </template>
            </t-table>
          </t-tab-panel>
        </t-tabs>

      </div>
    </t-dialog>
  </div>
</template>

<script lang="ts">
export default {
  name: 'customerDetail',
}
</script>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { t } from '@/locales';
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';
import { CustomerModel, SearchRelatedParameter } from "@/api/model/customerModel";
import { getInfo as getCustomerInfo, getPlatforms, getOrders, getShippingAddresses, getPurchasedProducts } from '@/api/customer/index'

const customerDetailVisible = ref<boolean>(false);
const customerTabValue = ref<number>(1)
const dataLoading = ref<boolean>(false);

const customerId = ref<number>(0);
const customer = ref<CustomerModel>({})

const searchRelatedParameter = ref<SearchRelatedParameter>({
  customerId: customerId.value,
  page: 1,
  pageSize: 5,
})

/** 活动平台 **/
const platformColumns: PrimaryTableCol<TableRowData>[] = [
  {
    title: t('pages.customerDetail.platformTable.column.platform'),
    width: 160,
    colKey: 'name',
  },
  {
    title: t('pages.customerDetail.platformTable.column.name'),
    width: 220,
    colKey: 'customer_name',
  },
  {
    title: t('pages.customerDetail.platformTable.column.gender'),
    width: 160,
    ellipsis: true,
    colKey: 'gender',
  }
];


const platformList = ref([])
const platformPagination = ref({
  defaultPageSize: 5,
  total: 0,
  defaultCurrent: 1,
});

const handlePlatformPageChange = async (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);

  searchRelatedParameter.value = {
    customerId: customerId.value,
    page: pageInfo.current,
    pageSize: pageInfo.pageSize
  };

  await getCustomerPlatforms(searchRelatedParameter.value);
};

/* 订单记录 */
const orderColumns: PrimaryTableCol<TableRowData>[] = [
  {
    title: t('pages.customerDetail.orderTable.column.number'),
    align: 'left',
    width: 180,
    colKey: 'number',
    fixed: 'left',
  },
  {
    title: t('pages.customerDetail.orderTable.column.platform'),
    width: 160,
    colKey: 'platform_name',
  },
  {
    title: t('pages.customerDetail.orderTable.column.total'),
    width: 160,
    ellipsis: true,
    colKey: 'total',
  },
  {
    title: t('pages.customerDetail.orderTable.column.pay_status'),
    width: 160,
    ellipsis: true,
    colKey: 'pay_status',
  },
  {
    title: t('pages.customerDetail.orderTable.column.creator'),
    width: 160,
    ellipsis: true,
    colKey: 'creator',
  },
  {
    title: t('pages.customerDetail.orderTable.column.created_at'),
    width: 160,
    colKey: 'created_at',
    sorter: true
  },
  {
    title: t('pages.customerDetail.orderTable.column.updated_at'),
    align: 'left',
    width: 160,
    colKey: 'updated_at',
    sorter: true
  },
  // {
  //   title: t('pages.customerDetail.orderTable.column.operate'),
  //   align: 'left',
  //   fixed: 'right',
  //   width: 160,
  //   colKey: 'op',
  // },
];
const orderList = ref([])
const orderPagination = ref({
  defaultPageSize: 5,
  total: 0,
  defaultCurrent: 1,
});

const handleOrderPageChange = async (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);

  searchRelatedParameter.value = {
    customerId: customerId.value,
    page: pageInfo.current,
    pageSize: pageInfo.pageSize
  };

  await getCustomerOrders(searchRelatedParameter.value);
};

/** 收货地址 **/
const shippingAddressColumns: PrimaryTableCol<TableRowData>[] = [
  {
    title: t('pages.customerDetail.shippingAddressTable.column.shippingAddress'),
    align: 'left',
    width: 320,
    fixed: 'left',
  },
  {
    title: t('pages.customerDetail.shippingAddressTable.operate.detail'),
    align: 'left',
    fixed: 'right',
    width: 160,
    colKey: 'op',
  },
];
const shippingAddressList = ref([])
const shippingAddressPagination = ref({
  defaultPageSize: 5,
  total: 0,
  defaultCurrent: 1,
});
const handleShippingAddressPageChange = async (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);

  searchRelatedParameter.value = {
    customerId: customerId.value,
    page: pageInfo.current,
    pageSize: pageInfo.pageSize
  };

  await getCustomerShippingAddresses(searchRelatedParameter.value);
};

//产品购买记录
const productColumns: PrimaryTableCol<TableRowData>[] = [
  {
    title: t('pages.customerDetail.productTable.column.image'),
    align: 'left',
    width: 180,
    colKey: 'product_image',
    fixed: 'left',
  },
  {
    title: t('pages.customerDetail.productTable.column.name'),
    width: 160,
    colKey: 'product_name',
  },
  {
    title: t('pages.customerDetail.productTable.column.number'),
    width: 220,
    colKey: 'product_sku',
  },
  {
    title: t('pages.customerDetail.productTable.column.platform'),
    width: 160,
    ellipsis: true,
    colKey: 'platform_name',
  },
  {
    title: t('pages.customerDetail.productTable.column.number'),
    width: 160,
    ellipsis: true,
    colKey: 'number',
  },
  {
    title: t('pages.customerDetail.productTable.column.quantity'),
    width: 160,
    ellipsis: true,
    colKey: 'quantity',
  },
  {
    title: t('pages.customerDetail.productTable.column.buy_datetime'),
    width: 160,
    colKey: 'created_at',
    sorter: true
  },
]
const productList = ref([])
const productPagination = ref({
  defaultPageSize: 5,
  total: 0,
  defaultCurrent: 1,
});
const handleProductPageChange = async (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);

  searchRelatedParameter.value = {
    customerId: customerId.value,
    page: pageInfo.current,
    pageSize: pageInfo.pageSize
  };

  await getCustomerPurchasedProducts(searchRelatedParameter.value);
};

onMounted(() => {
  console.log("客户详情组件挂载");
})

const getCustomer = async (customerId: number) => {
  const customerInfo = await getCustomerInfo(customerId);
  console.log(customerInfo, '---------获取用户详情信息----------')
  customer.value = {
    name: customerInfo.data.name,
    email: customerInfo.data.email,
    telephone: customerInfo.data.telephone,
    createBy: customerInfo.data.creator_name,
    createdAt: customerInfo.data.created_at,
    updatedAt: customerInfo.data.updated_at
  }
}

const getCustomerPlatforms = async (params: SearchRelatedParameter) => {
  console.log(params, '----------- getCustomerPlatforms params-----------');
  const result = await getPlatforms(params);
  console.log(result, '---------获取用户活动平台列表----------')
  platformList.value = result.data.items
  platformPagination.value = {
    defaultPageSize: result.data.pageSize,
    total: result.data.total,
    defaultCurrent: result.data.currentPage,
  };

}

const getCustomerOrders = async (params: SearchRelatedParameter) => {

  console.log(params, '----------- getCustomerOrders params-----------');
  const result = await getOrders(params);
  console.log(result, '---------获取用户历史订单列表----------')
  orderList.value = result.data.items
  orderPagination.value = {
    defaultPageSize: result.data.pageSize,
    total: result.data.total,
    defaultCurrent: result.data.currentPage,
  };
}

const getCustomerShippingAddresses = async (params: SearchRelatedParameter) => {
  console.log(params, '----------- getCustomerShippingAddresses params-----------');
  const result = await getShippingAddresses(params);
  console.log(result, '---------获取用户已有收货地址----------')
  shippingAddressList.value = result.data.items
  productPagination.value = {
    defaultPageSize: result.data.pageSize,
    total: result.data.total,
    defaultCurrent: result.data.currentPage,
  };
}

const getCustomerPurchasedProducts = async (params: SearchRelatedParameter) => {
  console.log(params, '----------- getCustomerPurchasedProducts params-----------');
  const result = await getPurchasedProducts(params);
  console.log(result, '---------获取用户历史购买产品记录----------')
  productList.value = result.data.items
  productPagination.value = {
    defaultPageSize: result.data.pageSize,
    total: result.data.total,
    defaultCurrent: result.data.currentPage,
  };
}

const closeDialog = async () => {
  customerTabValue.value = 1
}


defineExpose({
  customerDetailVisible, customerId, getCustomer, getCustomerPlatforms, getCustomerOrders, getCustomerShippingAddresses, getCustomerPurchasedProducts
})

</script>

<style lang="less" scoped>
.detail {

  .t-card {
    margin-bottom: 10px;
  }
}
</style>