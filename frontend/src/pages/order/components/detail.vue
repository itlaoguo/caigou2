<template>
  <div>
    <t-dialog header="用户详情" :footer="false" width="60%" v-model:visible="customerDetailVisible" :onClose="onClose">
      <div class="detail">
        <t-descriptions :label-style="{ width: '120px', textAlign: 'left' }" :column="3"
          :content-style="{ textAlign: 'left' }">
          <t-descriptions-item label="姓名">张三</t-descriptions-item>
          <t-descriptions-item label="手机号码">139****0609</t-descriptions-item>
          <t-descriptions-item label="电子邮箱">itlaoguo@qq.com</t-descriptions-item>
          <t-descriptions-item label="注册日期">2024-12-08 09:30</t-descriptions-item>
          <t-descriptions-item label="最近活跃时间">2025-03-25 12:01</t-descriptions-item>
        </t-descriptions>
        <t-divider />
        <t-tabs v-model="consumptionRecordTabValue" size="large">
          <t-tab-panel :value="1" label="订单记录" :destroy-on-hide="false">
            <t-table :columns="orderColumns" :data="orderList" :hover="true" :stripe="true"
              row-key="index" size="large">
              <template #pdName="{ row }">
                <span>
                  {{ row.pdName }}
                  <t-tag v-if="row.pdType" size="medium" style="margin-left: var(--td-comp-margin-s)">{{
                    row.pdType
                    }}</t-tag>
                </span>
              </template>

              <template #purchaseNum="{ row }">
                <span>
                  {{ row.purchaseNum }}
                  <t-tag v-if="row.purchaseNum > 50" theme="danger" variant="light" size="medium"
                    style="margin-left: var(--td-comp-margin-s)">超预算</t-tag>
                </span>
              </template>

              <template #op="slotProps">
                <t-space>
                  <t-link theme="primary">{{ t('pages.detailCard.detail.form.manage')
                    }}</t-link>
                  <t-link theme="danger">{{
                    t('pages.detailCard.detail.form.delete')
                    }}</t-link>
                </t-space>
              </template>

              <template #op-column>
                <t-icon name="descending-order" />
              </template>
            </t-table>
          </t-tab-panel>
          <t-tab-panel :value="2" label="产品购买记录" :destroy-on-hide="false">

            <t-table :columns="productColumns" :data="productList" :pagination="productPagination" :hover="true"
              :stripe="true" row-key="index" size="large">
              <template #pdName="{ row }">
                <span>
                  {{ row.pdName }}
                  <t-tag v-if="row.pdType" size="medium" style="margin-left: var(--td-comp-margin-s)">{{
                    row.pdType
                    }}</t-tag>
                </span>
              </template>

              <template #purchaseNum="{ row }">
                <span>
                  {{ row.purchaseNum }}
                  <t-tag v-if="row.purchaseNum > 50" theme="danger" variant="light" size="medium"
                    style="margin-left: var(--td-comp-margin-s)">超预算</t-tag>
                </span>
              </template>

              <template #op="slotProps">
                <t-space>
                  <t-link theme="primary">{{ t('pages.detailCard.detail.form.manage')
                    }}</t-link>
                  <t-link theme="danger">{{
                    t('pages.detailCard.detail.form.delete')
                    }}</t-link>
                </t-space>
              </template>

              <template #op-column>
                <t-icon name="descending-order" />
              </template>
            </t-table>
          </t-tab-panel>
          <t-tab-panel :value="3" label="收货地址" :destroy-on-hide="false">
            <t-table :columns="shippingAddressColumns" :data="shippingAddressList"
              :pagination="shippingAddressPagination" :hover="true" :stripe="true" row-key="shippingAddressIndex"
              size="large">
              <template #shippingAddress="{ row }">
                <span></span>
              </template>
              <template #op="slotProps">
                <t-space>
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
const customerId = ref<number>(0);
const customerDetailVisible = ref(false);

const customerInfo = [
  {
    name: 'id',
    value: 1123
  }, {
    name: '姓名',
    value: '张三'
  }
]


const onClose = () => {

};

const customerTabValue = ref<number>(1)

/** 收货地址 **/
const shippingAddressColumns: PrimaryTableCol<TableRowData>[] = [
  { colKey: 'row-select', type: 'multiple', width: 64, fixed: 'left' },
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
const rowKey = 'index';
const shippingAddressList = ref([])
const shippingAddressPagination = ref({
  defaultPageSize: 20,
  total: 0,
  defaultCurrent: 1,
});

/* 订单记录 */
const consumptionRecordTabValue = ref<number>(1)

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
    colKey: 'platform',
  },
  {
    title: t('pages.customerDetail.orderTable.column.platform_order_id'),
    width: 220,
    colKey: 'platform_order_id',
  },
  {
    title: t('pages.customerDetail.orderTable.column.type'),
    width: 160,
    ellipsis: true,
    colKey: 'type',
  },
  {
    title: t('pages.customerDetail.orderTable.column.total'),
    width: 160,
    ellipsis: true,
    colKey: 'total',
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
  {
    title: t('pages.customerDetail.orderTable.column.operate'),
    align: 'left',
    fixed: 'right',
    width: 160,
    colKey: 'op',
  },
];
const orderList = ref([])
const orderPagination = ref({
  defaultPageSize: 20,
  total: 0,
  defaultCurrent: 1,
});

//产品购买记录
const productColumns: PrimaryTableCol<TableRowData>[] = [
  {
    title: t('pages.customerDetail.productTable.column.image'),
    align: 'left',
    width: 180,
    colKey: 'image',
    fixed: 'left',
  },
  {
    title: t('pages.customerDetail.productTable.column.name'),
    width: 160,
    colKey: 'name',
  },
  {
    title: t('pages.customerDetail.productTable.column.number'),
    width: 220,
    colKey: 'number',
  },
  {
    title: t('pages.customerDetail.productTable.column.platform'),
    width: 160,
    ellipsis: true,
    colKey: 'type',
  },
  {
    title: t('pages.customerDetail.productTable.column.platform_order_id'),
    width: 160,
    ellipsis: true,
    colKey: 'platform_order_id',
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
    colKey: 'buy_datetime',
    sorter: true
  },
  {
    title: t('pages.customerDetail.productTable.column.operate'),
    align: 'left',
    fixed: 'right',
    width: 160,
    colKey: 'op',
  },
]
const productList = ref([])
const productPagination = ref({
  defaultPageSize: 20,
  total: 0,
  defaultCurrent: 1,
});


onMounted(() => {
  console.log("组件挂载");
})

defineExpose({
  customerDetailVisible, customerId
})

</script>

<style lang="less" scoped>
.detail {

  .t-card {
    margin-bottom: 10px;
  }
}
</style>