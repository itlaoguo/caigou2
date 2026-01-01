<!-- 合同审批 -->
<template>
  <div>
    <div class="list-common-table" v-if="AddSwtich">

      <div class="NavigationTitle">
        <p />
        合同数据
      </div>

      <t-form ref="form" :data="FormData" :label-width="80" colon @reset="onReset" @submit="onSubmit">
        <t-row>
          <t-col :span="10">
            <t-row :gutter="[24, 24]">
              <t-col :span="1">
                <t-input v-model="FormData.name" class="form-item-content" type="search" placeholder="合同名称"
                  style="width: 100%;" />
              </t-col>
              <t-col :span="1">
                <t-select v-model="FormData.status" class="form-item-content" :options="CONTRACT_STATUS_OPTIONS"
                  @change="onSubmit" placeholder="合同状态" clearable />
              </t-col>
              <t-col :span="1">
                <t-input v-model="FormData.no" class="form-item-content" placeholder="合同编号" style="width: 100%;" />
              </t-col>
              <t-col :span="1">
                <t-select v-model="FormData.type" style="display: inline-block" class="form-item-content"
                  :options="CONTRACT_TYPE_OPTIONS" placeholder="合同类型" clearable />
              </t-col>
              <t-col :span="2">
                <!-- <t-button theme="primary" type="submit" :style="{ marginLeft: 'var(--td-comp-margin-s)' }">
                  {{ t('components.commonTable.query') }}
                </t-button> -->
                <t-button type="reset" variant="base" theme="default"> {{ t('components.commonTable.reset') }}
                </t-button>
              </t-col>
            </t-row>
          </t-col>

          <t-col :span="2" class="operation-container">
            <t-button v-if="checkPermission('button')" theme="success" @click="onAdd">
              <template #icon> <t-icon name="plus" /></template>
              添 加
            </t-button>
            <t-button v-if="checkPermission('shenpi')" theme="primary" @click="onExamine">
              <template #icon> <t-icon name="plus" /></template>
              审 批
            </t-button>
            <t-button theme="warning">
              <template #icon> <t-icon name="download" /></template>
              导 出
            </t-button>
          </t-col>
        </t-row>
      </t-form>
      <!-- :bordered="true" 边框 -->
      <!-- :active-row-type="activeRowType" 选中行 -->
      <!-- :hover="hover"  鼠标效果 -->
      <!-- drag-sort="row" 需要多个api配合使用  -->
      <div class="table-container">
        <t-table :data="Data" :columns="COLUMNS" :row-key="RowKey" :vertical-align="VerticalAlign"
          :pagination="Pagination" :loading="DataLoading" maxHeight="840" :stripe="true"
          @page-change="rehandlePageChange" @change="rehandleChange" @sort-change="SortChange"
          @filter-change="onFilterChange" :column-controllter="columnControllerConfig" @select-change="onSelectChange"
          :selected-row-keys="selectedRowKeys">
          <template #index="{ rowIndex }">
            <span>
              {{ rowIndex + 1 }}
            </span>
          </template>
          <template #status="{ row }">
            <t-tag v-if="row.status === CONTRACT_STATUS.FAIL" theme="danger" variant="light">
              {{ t('components.commonTable.contractStatusEnum.fail') }}
            </t-tag>
            <t-tag v-if="row.status === CONTRACT_STATUS.AUDIT_PENDING" theme="warning" variant="light">
              {{ t('components.commonTable.contractStatusEnum.audit') }}
            </t-tag>
            <t-tag v-if="row.status === CONTRACT_STATUS.EXEC_PENDING" theme="warning" variant="light">
              {{ t('components.commonTable.contractStatusEnum.pending') }}
            </t-tag>
            <t-tag v-if="row.status === CONTRACT_STATUS.EXECUTING" theme="success" variant="light">
              {{ t('components.commonTable.contractStatusEnum.executing') }}
            </t-tag>
            <t-tag v-if="row.status === CONTRACT_STATUS.FINISH" theme="success" variant="light">
              {{ t('components.commonTable.contractStatusEnum.finish') }}
            </t-tag>
          </template>
          <template #contractType="{ row }">
            <p v-if="row.contractType === CONTRACT_TYPES.MAIN">{{ t('pages.listBase.contractStatusEnum.fail') }}</p>
            <p v-if="row.contractType === CONTRACT_TYPES.SUB">{{ t('pages.listBase.contractStatusEnum.audit') }}</p>
            <p v-if="row.contractType === CONTRACT_TYPES.SUPPLEMENT">
              {{ t('pages.listBase.contractStatusEnum.pending') }}
            </p>
          </template>
          <template #paymentType="{ row }">
            <div v-if="row.paymentType === CONTRACT_PAYMENT_TYPES.PAYMENT" class="payment-col">
              {{ t('pages.listBase.pay') }}
              <trend class="dashboard-item-trend" type="up" />
            </div>
            <div v-if="row.paymentType === CONTRACT_PAYMENT_TYPES.RECEIPT" class="payment-col">
              {{ t('pages.listBase.receive') }}
              <trend class="dashboard-item-trend" type="down" />
            </div>
          </template>
          <!-- 操作 -->
          <template #op="slotProps">
            <t-space>
              <!-- 详情 -->
              <t-link theme="primary" @click="handleClickDetail()"> {{ t('pages.listBase.detail') }}</t-link>
              <!-- 删除 -->
              <t-link theme="danger" @click="handleClickDelete(slotProps)"> {{ t('pages.listBase.delete') }}</t-link>
            </t-space>
          </template>
        </t-table>

        <!-- 删除操作 -->
        <t-dialog v-model:visible="confirmVisible" header="确认删除当前所选合同？" :body="confirmBody" :on-cancel="onCancel"
          @confirm="onConfirmDelete" />


        <!-- 审批表单 -->
        <ExamineAdd ref="ExamineaddRef" />
      </div>
    </div>

    <!-- 添加 -->
    <div class="list-common-add" v-else>
      <AddCon ref="AddConitem" :contractId="ContractId" :onaddswtich="onaddswtich" />
    </div>
  </div>



</template>
<script setup lang="ts">
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps, Button } from 'tdesign-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';

import { getList } from '@/api/list';
import Trend from '@/components/trend/index.vue';
import { prefix } from '@/config/global';
import { CONTRACT_PAYMENT_TYPES, CONTRACT_STATUS, CONTRACT_TYPES } from '@/constants';
import { PAGEING_ARRAY, TABLE_SORT_FIGURE, TABLE_SORT_FILTER, checkPermission } from "@/config/public";
import { t } from '@/locales';
import { useSettingStore } from '@/store';

import AddCon from "../add/index.vue";
import ExamineAdd from "./ExamineAdd.vue"

interface FormData {
  name: string;
  no: string;
  status?: number;
  type: string;
}

const store = useSettingStore();
const router = useRouter();

const CONTRACT_STATUS_OPTIONS = [
  { value: CONTRACT_STATUS.FAIL, label: t('components.commonTable.contractStatusEnum.fail') },
  { value: CONTRACT_STATUS.AUDIT_PENDING, label: t('components.commonTable.contractStatusEnum.audit') },
  { value: CONTRACT_STATUS.EXEC_PENDING, label: t('components.commonTable.contractStatusEnum.pending') },
  { value: CONTRACT_STATUS.EXECUTING, label: t('components.commonTable.contractStatusEnum.executing') },
  { value: CONTRACT_STATUS.FINISH, label: t('components.commonTable.contractStatusEnum.finish') },
];

const CONTRACT_TYPE_OPTIONS = [
  { value: CONTRACT_TYPES.MAIN, label: t('components.commonTable.contractTypeEnum.main') },
  { value: CONTRACT_TYPES.SUB, label: t('components.commonTable.contractTypeEnum.sub') },
  { value: CONTRACT_TYPES.SUPPLEMENT, label: t('components.commonTable.contractTypeEnum.supplement') },
];
const COLUMNS: PrimaryTableCol[] = [
  {
    colKey: 'row-select',
    type: 'multiple',
    width: 46,
    checkProps: { allowUncheck: true },
  },
  {
    align: 'center',
    colKey: 'index',
    title: '序号',
    width: 50,
    fixed: 'left',
  },
  {
    title: t('components.commonTable.contractName'),
    fixed: 'left',
    width: 280,
    ellipsis: true,
    align: 'center',
    colKey: 'name',
  },
  {
    align: 'center', title: t('components.commonTable.contractStatus'), colKey: 'status', width: 160, filter: {
      type: 'multiple',
      resetValue: [],
      list: [
        { label: 'All', checkAll: true },
        { label: '审核失败', value: 0 },
        { label: '待审核', value: 1 },
        { label: '待履行', value: 2 },
        { label: '履行中', value: 3 },
        { label: '已完成', value: 4 },
      ],
      // 是否显示重置取消按钮，一般情况不需要显示
      showConfirmAndReset: true,
    },
  },
  {
    align: 'center',
    title: t('components.commonTable.contractNum'),
    width: 160,
    ellipsis: true,
    colKey: 'no',
  },
  {
    align: 'center',
    title: t('components.commonTable.contractType'),
    width: 160,
    ellipsis: true,
    colKey: 'contractType',
  },
  {
    align: 'center',
    title: t('components.commonTable.contractPayType'),
    width: 160,
    ellipsis: true,
    colKey: 'paymentType',
  },
  {
    align: 'center',
    title: t('components.commonTable.contractAmount'),
    width: 160,
    ellipsis: true,
    colKey: 'amount',
    sortType: 'all',
    sorter: true,
  },
  {
    align: 'center',
    fixed: 'right',
    width: 160,
    colKey: 'op',
    title: t('components.commonTable.operation'),
  },
];

// 开关
const AddSwtich = ref(true)

const ContractId = ref(null)

const onaddswtich = () => {
  AddSwtich.value = !AddSwtich.value
}

// 子组件
const AddConitem = ref(null);


const SearchForm = {
  name: '',
  no: '',
  type: '',
};

const FormData = ref<FormData>({ ...SearchForm });
const RowKey = 'id';
const VerticalAlign = 'bottom' as const;

// const activeRowType = ref('single');
const hover = ref(true);

// watch([activeRowType], ([activeRowType]) => {
//   if (!activeRowType) {
//     hover.value = true;
//   }
// });


// 排序
const SortChange: TableProps['onSortChange'] = async (sort) => { // 参数应为排序对象
  console.log('sort-change', sort);
  Data.value = await TABLE_SORT_FIGURE(sort, Data);
};

const onFilterChange: TableProps['onFilterChange'] = (filters, ctx) => {
  console.log('filter-change', filters, ctx);
  Data.value = [];
  Data.value = [
    {
      id: "1",
      name: '合同速度建行发的尽快恢复健康',
      no: '123456789',
      status: 0,
      contractType: CONTRACT_TYPES.MAIN,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '1000',
    },
    {
      id: "2",
      name: '合同速度快技术不到家',
      no: '987654321',
      status: 1,
      contractType: CONTRACT_TYPES.SUB,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '2000',
    },
    {
      id: "3",
      name: '合同速度建行发的尽快恢复健康',
      no: '123456789',
      status: 2,
      contractType: CONTRACT_TYPES.MAIN,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '1000',
    },
    {
      id: "4",
      name: '合同速度快技术不到家',
      no: '987654321',
      status: 3,
      contractType: CONTRACT_TYPES.SUB,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '2000',
    },
    {
      id: "5",
      name: '合同速度建行发的尽快恢复健康',
      no: '123456789',
      status: 4,
      contractType: CONTRACT_TYPES.MAIN,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '1000',
    },
    {
      id: "6",
      name: '合同速度快技术不到家',
      no: '987654321',
      status: 1,
      contractType: CONTRACT_TYPES.SUB,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '2000',
    },
    {
      id: "7",
      name: '合同速度建行发的尽快恢复健康',
      no: '123456789',
      status: 3,
      contractType: CONTRACT_TYPES.MAIN,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '1000',
    },
    {
      id: "8",
      name: '合同速度快技术不到家',
      no: '987654321',
      status: 2,
      contractType: CONTRACT_TYPES.SUB,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '2000',
    },
    {
      id: "9",
      name: '合同速度建行发的尽快恢复健康',
      no: '123456789',
      status: 1,
      contractType: CONTRACT_TYPES.MAIN,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '1000',
    },
    {
      id: "10",
      name: '合同速度快技术不到家',
      no: '987654321',
      status: 0,
      contractType: CONTRACT_TYPES.SUB,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '2000',
    },
    {
      id: "11",
      name: '合同速度建行发的尽快恢复健康',
      no: '123456789',
      status: CONTRACT_STATUS.EXECUTING,
      contractType: CONTRACT_TYPES.MAIN,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '1000',
    },
    {
      id: "12",
      name: '合同速度快技术不到家',
      no: '987654321',
      status: 0,
      contractType: CONTRACT_TYPES.SUB,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '2000',
    },
    {
      id: "13",
      name: '合同速度建行发的尽快恢复健康',
      no: '123456789',
      status: 0,
      contractType: CONTRACT_TYPES.MAIN,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '1000',
    },
    {
      id: "14",
      name: '合同速度快技术不到家',
      no: '987654321',
      status: 0,
      contractType: CONTRACT_TYPES.SUB,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '2000',
    },
    {
      id: "15",
      name: '合同速度建行发的尽快恢复健康',
      no: '123456789',
      status: 0,
      contractType: CONTRACT_TYPES.MAIN,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '1000',
    },
    {
      id: "16",
      name: '合同速度快技术不到家',
      no: '987654321',
      status: 0,
      contractType: CONTRACT_TYPES.SUB,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '2000',
    },
    {
      id: "17",
      name: '合同速度建行发的尽快恢复健康',
      no: '123456789',
      status: CONTRACT_STATUS.EXECUTING,
      contractType: CONTRACT_TYPES.MAIN,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '1000',
    },
    {
      id: "18",
      name: '合同速度快技术不到家',
      no: '987654321',
      status: CONTRACT_STATUS.EXEC_PENDING,
      contractType: CONTRACT_TYPES.SUB,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '2000',
    },
    {
      id: "19",
      name: '合同速度建行发的尽快恢复健康',
      no: '123456789',
      status: CONTRACT_STATUS.EXECUTING,
      contractType: CONTRACT_TYPES.MAIN,
      paymentType: 'PAYMENT_TYPE.ANNUAL',
      amount: '1000',
    },
  ];
  Data.value = TABLE_SORT_FILTER(Data.value, filters);
  Pagination.value.total = Data.value.length;
};

// 分页配置
const Pagination = ref({
  defaultPageSize: 20,
  total: 100,
  defaultCurrent: 1,
  pageSizeOptions: PAGEING_ARRAY,
});
const confirmVisible = ref(false);

const Data = ref();

// 添加
const onAdd = () => {
  console.log('点击添加');
  ContractId.value = 999;
  onaddswtich();
}

// 筛选数组
const columnControllerConfig = computed(() => ({
  // 列配置按钮位置
  placement: 'top-right',
  // 用于设置允许用户对哪些列进行显示或隐藏的控制，默认为全部字段
  fields: ['name', 'no', 'status', 'contractType', 'paymentType', 'amount'],

  // 弹框组件属性透传
  dialogProps: { preventScrollThrough: true },
  // 列配置按钮组件属性透传
  buttonProps: false
}));

const DataLoading = ref(false);
const fetchData = async () => {
  DataLoading.value = true;
  try {

    Data.value = [
      {
        id: "1",
        name: '合同速度建行发的尽快恢复健康',
        no: '123456789',
        status: 0,
        contractType: CONTRACT_TYPES.MAIN,
        paymentType: 'PAYMENT_TYPE.ANNUAL',
        amount: '1000',
      },
      {
        id: "2",
        name: '合同速度快技术不到家',
        no: '987654321',
        status: 1,
        contractType: CONTRACT_TYPES.SUB,
        paymentType: 'PAYMENT_TYPE.ANNUAL',
        amount: '2000',
      },
      {
        id: "3",
        name: '合同速度建行发的尽快恢复健康',
        no: '123456789',
        status: 2,
        contractType: CONTRACT_TYPES.MAIN,
        paymentType: 'PAYMENT_TYPE.ANNUAL',
        amount: '1000',
      },
      {
        id: "4",
        name: '合同速度快技术不到家',
        no: '987654321',
        status: 3,
        contractType: CONTRACT_TYPES.SUB,
        paymentType: 'PAYMENT_TYPE.ANNUAL',
        amount: '2000',
      },
      {
        id: "5",
        name: '合同速度建行发的尽快恢复健康',
        no: '123456789',
        status: 4,
        contractType: CONTRACT_TYPES.MAIN,
        paymentType: 'PAYMENT_TYPE.ANNUAL',
        amount: '1000',
      },
      {
        id: "6",
        name: '合同速度快技术不到家',
        no: '987654321',
        status: 1,
        contractType: CONTRACT_TYPES.SUB,
        paymentType: 'PAYMENT_TYPE.ANNUAL',
        amount: '2000',
      },
      {
        id: "7",
        name: '合同速度建行发的尽快恢复健康',
        no: '123456789',
        status: 3,
        contractType: CONTRACT_TYPES.MAIN,
        paymentType: 'PAYMENT_TYPE.ANNUAL',
        amount: '1000',
      },
      {
        id: "8",
        name: '合同速度快技术不到家',
        no: '987654321',
        status: 2,
        contractType: CONTRACT_TYPES.SUB,
        paymentType: 'PAYMENT_TYPE.ANNUAL',
        amount: '2000',
      },
      {
        id: "9",
        name: '合同速度建行发的尽快恢复健康',
        no: '123456789',
        status: 1,
        contractType: CONTRACT_TYPES.MAIN,
        paymentType: 'PAYMENT_TYPE.ANNUAL',
        amount: '1000',
      }
    ];
    Pagination.value = {
      ...Pagination.value,
      total: Data.value.length,
    };
  } catch (e) {
    console.log(e);
  } finally {
    DataLoading.value = false;
  }
};

// 表格单选
const selectedRowKeys = ref([]);
const onSelectChange = (value: any[], params: any) => {
  // if (selectedRowKeys.value.length > 0) return;
  if (value.length > 0) {
    selectedRowKeys.value = [];
    selectedRowKeys.value = value[value.length - 1];
  } else {
    selectedRowKeys.value = value;
  }

  console.log(value, params);
};

// 审批组件
const ExamineaddRef = ref(null);

// 审批按钮
const onExamine = () => {
  console.log('审批');
  if (selectedRowKeys.value.length == 0) return MessagePlugin.warning('请先选择需要审批的工单')
  ExamineaddRef.value.onSwticher();
}

const DeleteIdx = ref(-1);
const confirmBody = computed(() => {
  if (DeleteIdx.value > -1) {
    const { name } = Data.value[DeleteIdx.value];
    return `删除后，${name}的所有合同信息将被清空，且无法恢复`;
  }
  return '';
});

const resetIdx = () => {
  DeleteIdx.value = -1;
};

const onConfirmDelete = () => {
  // 真实业务请发起请求
  Data.value.splice(DeleteIdx.value, 1);
  Pagination.value.total = Data.value.length;
  confirmVisible.value = false;
  MessagePlugin.success('删除成功');
  resetIdx();
};

const onCancel = () => {
  resetIdx();
};

onMounted(() => {
  fetchData();
});

const handleClickDelete = (slot: { row: { rowIndex: number } }) => {
  DeleteIdx.value = slot.row.rowIndex;
  confirmVisible.value = true;
};

const onReset = (val: unknown) => {
  console.log(val);
};

const handleClickDetail = () => {
  // AddConitem.value.getsomeId('9999');
  router.push('/detail/base');
};

const onSubmit = (val: unknown) => {
  console.log(val);
  console.log(FormData.value);
};

const rehandlePageChange = (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);
};

const rehandleChange = (changeParams: unknown, triggerAndData: unknown) => {
  console.log('统一Change', changeParams, triggerAndData);
};

const headerAffixedTop = computed(
  () =>
    ({
      offsetTop: store.isUseTabsRouter ? 48 : 0,
      container: `.${prefix}-layout`,
    }) as any, // TO BE FIXED
);
</script>

<style lang="less" scoped>
.list-common-table {
  height: 100%;
  background-color: var(--td-bg-color-container);
  padding: var(--td-comp-paddingTB-xxl) var(--td-comp-paddingLR-xxl);
  border-radius: var(--td-radius-medium);

  .table-container {
    margin-top: var(--td-comp-margin-xxl);
  }
}

.form-item-content {
  width: 100%;
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

.payment-col {
  display: flex;

  .trend-container {
    display: flex;
    align-items: center;
    margin-left: var(--td-comp-margin-s);
  }
}
</style>
