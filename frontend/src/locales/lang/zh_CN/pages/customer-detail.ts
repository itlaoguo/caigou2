export default {
  platformTable: {
    column: {
      platform: '平台',
      name: '名称',
      gender: '性别',
      operate: '操作'
    },
    operate: {
      detail: '详情',
    }
  },
  shippingAddressTable: {
    column: {
      platform: '来源平台',
      shippingAddress: '收货地址',
      operate: '操作'
    },
    operate: {
      detail: '详情',
    }
  },
  orderTable: {
    column: {
      number: '订单编号',
      platform: '销售平台',
      platform_order_id: '销售平台订单号',
      type: '订单类型',
      total: '订单总金额',
      currency: '币种',
      order_status: '订单状态',
      pay_status: '支付状态',
      pay_method: '支付方式',
      warehouse_status: '出库状态',
      shipping_status: '物流状态',
      shipping_method: '物流/快递方式',
      shipping_number: '物流/快递单号',
      need_customs_declaration: '是否需要报关',
      need_fapiao: '是否需要开发票',
      note: '备注',
      creator: '制单人',
      created_at: '创建时间',
      updated_at: '最后更新时间',
      operate: '操作',
    },
    operate: {
      detail: '详情',
    }
  },
  productTable: {
    column: {
      image: '产品图片',
      name: '产品名称',
      number: '产品编号',
      platform: '购买渠道',
      platform_order_id: '订单号',
      quantity: '购买数量',
      buy_datetime: '购买时间',
      operate: '操作',
    }
  }
}