<template>
  <div>
    <t-dialog header="订单详情" :footer="false" width="60%" v-model:visible="orderDetailVisible" :onClose="onClose">
      <div class="detail">
        <t-card class="t-card" :bordered="false" title="基本信息">

          <t-descriptions :label-style="{ width: '120px', textAlign: 'left' }" :column="3"
            :content-style="{ textAlign: 'left' }">
            <t-descriptions-item label="订单编号">123123123123</t-descriptions-item>
            <t-descriptions-item label="所属主体">翼飞智能</t-descriptions-item>
            <t-descriptions-item label="销售渠道">海外商城-shop.iflight.com</t-descriptions-item>
            <t-descriptions-item label="订单金额">USD 9999</t-descriptions-item>
            <t-descriptions-item label="订单状态">已付款</t-descriptions-item>
            <t-descriptions-item label="下单日期">2025-03-25 12:01</t-descriptions-item>
          </t-descriptions>
        </t-card>
        <t-card class="t-card" :bordered="false" title="商品信息">
          <!-- <t-table :columns="orderProductColumns" :data="orderProductList" :pagination="orderProductPagination" -->
          <t-table :columns="orderProductColumns" :hover="true" :stripe="true" row-key="index" size="large">
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
        </t-card>
      </div>
    </t-dialog>
  </div>
</template>

<script lang="ts">
export default {
  name: 'orderDetail',
}
</script>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { t } from '@/locales';
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';

const orderId = ref<number>(0);
const orderDetailVisible = ref(false);

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


const orderProductColumns: PrimaryTableCol<TableRowData>[] = [
  {
    title: t('pages.orderDetail.productListTable.column.name'),
    align: 'left',
    width: 180,
    colKey: 'name',
    fixed: 'left',
  },
  {
    title: t('pages.orderDetail.productListTable.column.image'),
    align: 'left',
    width: 180,
    colKey: 'image',
    fixed: 'left',
  },
  {
    title: t('pages.orderDetail.productListTable.column.number'),
    width: 220,
    colKey: 'platform_order_id',
  },
  {
    title: t('pages.orderDetail.productListTable.column.price'),
    align: 'left',
    width: 180,
    colKey: 'image',
  },
  {
    title: t('pages.orderDetail.productListTable.column.quantity'),
    width: 160,
    colKey: 'platform',
  },
  {
    title: t('pages.orderDetail.productListTable.column.operate'),
    align: 'left',
    fixed: 'right',
    width: 160,
    colKey: 'op',
  },
];




onMounted(() => {
  console.log("组件挂载");
})

defineExpose({
  orderDetailVisible, orderId
})

</script>

<style lang="less" scoped>
.detail {
  .t-card {
    margin-bottom: 10px;
  }
}
</style>