<template>
  <div>
    <t-dialog header="订单详情" :footer="false" width="60%" v-model:visible="orderDetailVisible" :onClose="onClose" :top="'8vh'">
      <div class="detail">
        <t-card class="t-card" :bordered="false" title="基本信息">

          <t-descriptions :label-style="{ width: '120px', textAlign: 'left' }" :column="3"
            :content-style="{ textAlign: 'left' }">
            <t-descriptions-item label="订单编号">{{ orderInfo.number }}</t-descriptions-item>
            <t-descriptions-item label="所属主体">{{ orderInfo.companyName }}</t-descriptions-item>
            <t-descriptions-item label="销售渠道">{{ orderInfo.platform }}</t-descriptions-item>
            <t-descriptions-item label="订单金额">{{ orderInfo.total }} RMB</t-descriptions-item>
            <t-descriptions-item label="订单状态">
              <span v-if="orderInfo.payStatus == '0'">未付款</span>
              <span v-if="orderInfo.payStatus == '1'">已付款</span>
              <span v-if="orderInfo.payStatus == '2'">部分付款</span>
            </t-descriptions-item>
            <t-descriptions-item label="下单日期">{{ orderInfo.createAt }}</t-descriptions-item>
          </t-descriptions>
        </t-card>
        <t-card class="t-card" :bordered="false" title="收货地址">
          <t-descriptions :label-style="{ width: '120px', textAlign: 'left' }" :column="3"
            :content-style="{ textAlign: 'left' }" v-if="orderInfo.shippingAddress.otherAddress.length > 0">
            <t-descriptions-item label="收货信息">{{ orderInfo.shippingAddress.otherAddress }}</t-descriptions-item>
          </t-descriptions>

          <t-descriptions v-else :label-style="{ width: '120px', textAlign: 'left' }" :column="3"
            :content-style="{ textAlign: 'left' }">
            <t-descriptions-item label="收货地址">
              {{ orderInfo.shippingAddress.country }}
              {{ orderInfo.shippingAddress.province }}
              {{ orderInfo.shippingAddress.city }}
              {{ orderInfo.shippingAddress.address }}
              {{ orderInfo.shippingAddress.zipcode }}
            </t-descriptions-item>
            <t-descriptions-item label="收货联系人">{{ orderInfo.shippingAddress.contactName }}</t-descriptions-item>
            <t-descriptions-item label="收货联系电话">{{ orderInfo.shippingAddress.contactTelephone }}</t-descriptions-item>
            <t-descriptions-item label="备注">{{ orderInfo.shippingAddress.note }}</t-descriptions-item>
          </t-descriptions>


        </t-card>
        <t-card class="t-card" :bordered="false" title="商品信息">
          <!-- <t-table :columns="orderProductColumns" :data="orderProductList" :pagination="orderProductPagination"
            :hover="true" :stripe="true" row-key="index" size="large"> -->
          <t-table :data="orderInfo.productList" :columns="orderProductColumns" :hover="true" :stripe="true"
            row-key="index" size="large" height="300">
            <template #product_image="{ row }">
              <img :src="row.product_image" alt="" width="50" height="50">
            </template>
            <template #quantity="{ row }">
              <span>
                {{ row.quantity }} {{ row.unit }}
              </span>
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
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps, Row } from 'tdesign-vue-next';
import { OrderModel } from "@/api/model/orderModel";
import { getInfo } from '@/api/order/index'

const orderId = ref<number>(0);
const orderDetailVisible = ref<boolean>(false);
const orderInfo = ref<OrderModel>({
  number: "",
  customerName: "",
  companyId: 0,
  companyName: "",
  platformCode: "",
  platform: "",
  subject: "",
  total: "",
  payStatus: "",
  note: "",
  createdBy: "",
  createAt: "",
  updatedAt: "",
  shippingAddress: {
    contactName: "",
    contactTelephone: "",
    country: "",
    countryCode: "",
    province: "",
    city: "",
    address: "",
    otherAddress: "",
    zipcode: "",
    note: "",
  },
});



const onClose = () => {

};


const orderProductColumns: PrimaryTableCol<TableRowData>[] = [
  {
    title: t('pages.orderDetail.productListTable.column.name'),
    align: 'left',
    width: 180,
    colKey: 'product_name',
    fixed: 'left',
  },
  {
    title: t('pages.orderDetail.productListTable.column.image'),
    align: 'left',
    width: 180,
    colKey: 'product_image',
    fixed: 'left',
  },
  {
    title: t('pages.orderDetail.productListTable.column.number'),
    width: 220,
    colKey: 'product_sku',
  },
  {
    title: t('pages.orderDetail.productListTable.column.price'),
    align: 'left',
    width: 180,
    colKey: 'price',
  },
  {
    title: t('pages.orderDetail.productListTable.column.quantity'),
    width: 160,
    colKey: 'quantity',
  }
];

const getOrderInfo = async (orderId: number) => {
  const result = await getInfo(orderId)
  console.log(result, '=================getOrderInfo===================')
  const _orderInfo = result.data;
  orderInfo.value = {
    number: _orderInfo.number,
    customerName: _orderInfo.customer_name,
    companyId: _orderInfo.company_id,
    companyName: _orderInfo.company_name,
    platformCode: _orderInfo.platform_code,
    platform: _orderInfo.platform_name,
    subject: _orderInfo.subject,
    total: _orderInfo.total,
    payStatus: _orderInfo.pay_status,
    note: _orderInfo.note,
    createdBy: _orderInfo.creator_name,
    createAt: _orderInfo.created_at,
    updatedAt: _orderInfo.updated_at,
    shippingAddress: {
      contactName: _orderInfo.shipping_address.contact_name,
      contactTelephone: _orderInfo.shipping_address.contact_telephone,
      country: _orderInfo.shipping_address.country,
      countryCode: _orderInfo.shipping_address.country_code,
      province: _orderInfo.shipping_address.province,
      city: _orderInfo.shipping_address.city,
      address: _orderInfo.shipping_address.address,
      otherAddress: _orderInfo.shipping_address.other_address,
      zipcode: _orderInfo.shipping_address.zipcode,
      note: _orderInfo.shipping_address.note,
    },
    productList: _orderInfo.products,
  }

  console.log('----------orderInfo.value---------', orderInfo.value)
}


onMounted(() => {
  console.log("组件挂载");
})

defineExpose({
  orderDetailVisible, orderId, getOrderInfo
})

</script>

<style lang="less" scoped>
.detail {
  .t-card {
    margin-bottom: 10px;
  }
}
</style>