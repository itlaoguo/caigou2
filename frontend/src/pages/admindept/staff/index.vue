<!-- 合同审批 -->
<template>
  <div>
    <div class="list-common-table" v-if="AddSwtich">

      <div class="NavigationTitle">
        <p />
        人员管理
      </div>

      <t-form ref="form" :data="FormData" :label-width="80" colon @reset="onReset" @submit="onSubmit">
        <t-row>
          <t-col :span="10">
            <t-row :gutter="[24, 24]">
              <t-col :span="1">
                <t-input v-model="FormData.name" class="form-item-content" type="search" placeholder="员工姓名"
                  style="width: 100%" />
              </t-col>
              <t-col :span="1">
                <t-input v-model="FormData.no" class="form-item-content" placeholder="员工编号" style="width: 100%" />
              </t-col>
              <t-col :span="1">
                <t-select v-model="FormData.status" class="form-item-content" :options="TYPE_OPTIONS_STATUS"
                  @change="onSubmit" placeholder="在职状态" clearable />
              </t-col>
              <t-col :span="1">
                <t-select v-model="FormData.type" style="display: inline-block" class="form-item-content"
                  :options="TYPE_OPTIONS_MARRIAGE" placeholder="婚姻状况" clearable />
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
            <t-button theme="success" @click="onAdd">
              <template #icon> <t-icon name="plus" /></template>
              添 加
            </t-button>
            <!-- <t-button theme="primary" @click="onExamine">
        <template #icon> <t-icon name="plus" /></template>
        审 批
      </t-button> -->
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
          @filter-change="onFilterChange" :column-controllter="columnControllerConfig">
          <template #index="{ rowIndex }">
            <span>
              {{ rowIndex + 1 }}
            </span>
          </template>

          <!-- 编号 -->
          <template #bb="{ row }">
            NO.{{ row.bb }}
          </template>

          <!-- 性别 -->
          <template #cc="{ row }">
            <t-tag theme="success" variant="light">
              {{ row.cc == 0 ? '女' : '男' }}
            </t-tag>
          </template>

          <!-- 在职状态 -->
          <template #ff="{ row }">
            <t-tag v-if="row.ff == 1" theme="success" variant="light">
              在职
            </t-tag>
            <t-tag v-if="row.ff == 0" theme="danger" variant="light">
              离职
            </t-tag>
          </template>


          <!-- 操作 -->
          <template #op="slotProps">
            <t-space>
              <!-- 详情 -->
              <t-tooltip content="查看" placement="top" theme="light">
                <t-button variant="text" @click="handleClickDetail()">
                  <t-icon name="edit" />
                </t-button>
              </t-tooltip>
              <!-- 删除 -->
              <t-tooltip content="删除" placement="top" theme="light">
                <t-button variant="text" @click="handleClickDelete(slotProps)">
                  <t-icon name="delete" />
                </t-button>
              </t-tooltip>
            </t-space>
          </template>
        </t-table>

        <!-- 删除操作 -->
        <t-dialog v-model:visible="confirmVisible" header="确认删除当前所选合同？" :body="confirmBody" :on-cancel="onCancel"
          @confirm="onConfirmDelete" />


        <!-- 审批表单 -->
        <!-- <ExamineAdd ref="ExamineaddRef" /> -->
      </div>
    </div>

    <!-- 添加 -->
    <div class="list-common-add" v-else>
      <AddCon ref="AddConitem" :contractId="ContractId" :onaddswtich="onaddswtich" :DetailsSwticher="DetailsSwticher" />
    </div>
  </div>



</template>
<script lang="ts">
export default {
  name: 'AdmindeptStaff',
}
</script>
<script setup lang="ts">
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps, Row } from 'tdesign-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';

import { getList } from '@/api/list';
import Trend from '@/components/trend/index.vue';
import { prefix } from '@/config/global';
import { CONTRACT_PAYMENT_TYPES, CONTRACT_STATUS, CONTRACT_TYPES } from '@/constants';
import { PAGEING_ARRAY, TABLE_SORT_FIGURE, TABLE_SORT_FILTER } from "@/config/public";
import { TABLE_SORT_FILTERFF, TYPE_OPTIONS_MARRIAGE, TYPE_OPTIONS_STATUS } from "../constants";
import { t } from '@/locales';
import { useSettingStore } from '@/store';

import AddCon from "./add.vue";
// import ExamineAdd from "./ExamineAdd.vue"

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
    align: 'center',
    colKey: 'index',
    title: '序号',
    width: 80,
    fixed: 'left',
  },
  {
    align: 'center',
    title: "姓名",
    width: 160,
    ellipsis: true,
    colKey: 'aa',
  },
  {
    title: "编号",
    width: 280,
    ellipsis: true,
    align: 'center',
    colKey: 'bb',
    sortType: 'all',
    sorter: true,
  },
  {
    title: "性别",
    width: 280,
    ellipsis: true,
    align: 'center',
    colKey: 'cc',
  },
  {
    title: "年纪",
    width: 280,
    ellipsis: true,
    align: 'center',
    colKey: 'dd',
    sortType: 'all',
    sorter: true,
  },
  {
    align: 'center',
    title: "手机号",
    width: 160,
    ellipsis: true,
    colKey: 'ee',
  },
  {
    align: 'center',
    title: "在职状态",
    width: 160,
    ellipsis: true,
    colKey: 'ff',
    filter: {
      type: 'multiple',
      resetValue: [],
      list: [
        { label: 'All', checkAll: true },
        { label: '离职', value: 0 },
        { label: '在职', value: 1 },
      ],
      // 是否显示重置取消按钮，一般情况不需要显示
      showConfirmAndReset: true,
    },
  },
  {
    title: "入职时间",
    width: 280,
    ellipsis: true,
    align: 'center',
    colKey: 'gg',
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
  Data.value = [

  ];
  Data.value = [
    {
      aa: "秦学海",
      bb: "423",
      cc: 1,
      dd: "23",
      ee: "15220730439",
      ff: 1,
      gg: "2025-3-3",
    },
    {
      aa: "张三",
      bb: "400",
      cc: 1,
      dd: "25",
      ee: "15256230439",
      ff: 0,
      gg: "2024-3-3",
    }
  ];
  Data.value = TABLE_SORT_FILTERFF(Data.value, filters);
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
  DetailsSwticher.value = true;
  ContractId.value = 999;
  onaddswtich();
}

// 筛选数组
const columnControllerConfig = computed(() => ({
  // 列配置按钮位置
  placement: 'top-right',
  // 用于设置允许用户对哪些列进行显示或隐藏的控制，默认为全部字段
  fields: ['aa', 'bb', 'cc', 'dd', 'ee', 'ff', 'gg'],

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
        aa: "秦学海",
        bb: "423",
        cc: 1,
        dd: "23",
        ee: "15220730439",
        ff: 1,
        gg: "2025-3-3",
      },
      {
        aa: "张三",
        bb: "400",
        cc: 1,
        dd: "25",
        ee: "15256230439",
        ff: 0,
        gg: "2024-3-3",
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

// 审批组件
const ExamineaddRef = ref(null);

// 审批按钮
const onExamine = () => {
  console.log('审批');
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

// 查看详情
const DetailsSwticher = ref(true);
const handleClickDetail = () => {
  DetailsSwticher.value = false;
  onaddswtich();
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
