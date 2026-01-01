<!-- 合同审批 -->
<template>
  <div class="list-common-table" v-if="AddSwtich">

    <div class="NavigationTitle">
      <p />
      薪资管理
    </div>

    <t-form ref="form" :data="FormData" :label-width="80" colon @reset="onReset" @submit="onSubmit">
      <t-row>
        <t-col :span="10">
          <t-row :gutter="[24, 24]">
            <t-col :span="1">
              <t-input v-model="FormData.name" class="form-item-content" type="search" placeholder="创建人"
                style="width: 100%" />
            </t-col>
            <t-col :span="1">
              <t-select v-model="FormData.status" class="form-item-content" :options="TYPE_OPTIONS_COMPANY"
                @change="onSubmit" placeholder="公司主体" clearable />
            </t-col>
            <t-col :span="1">
              <t-date-picker mode="month" clearable allow-input placeholder="工资月份" style="width: 100%"/>
            </t-col>
            <t-col :span="1">
              <t-select v-model="FormData.type" style="display: inline-block" class="form-item-content"
                :options="TYPE_OPTIONS_EXAMINE" placeholder="审核状态" clearable />
            </t-col>
            <t-col :span="1">
              <t-select v-model="FormData.type" style="display: inline-block" class="form-item-content"
                :options="TYPE_OPTIONS_SALARY" placeholder="支付状态" clearable />
            </t-col>
            <t-col :span="2">
              <!-- <t-button theme="primary" type="submit" :style="{ marginLeft: 'var(--td-comp-margin-s)' }">
                {{ t('components.commonTable.query') }}
              </t-button> -->
              <t-button type="reset" variant="base" theme="default"> {{ t('components.commonTable.reset') }} </t-button>
            </t-col>
          </t-row>
        </t-col>

        <t-col :span="12" class="operation-container">
          <t-button theme="success" @click="onAdd">
            <template #icon> <t-icon name="add" /></template>
            添加工资条
          </t-button>
          <t-button theme="primary" @click="onExamine">
            <template #icon> <t-icon name="institution-checked" /></template>
            提交审核
          </t-button>
          <t-button theme="success" @click="onPushSalary">
            <template #icon> <t-icon name="send" /></template>
            推送工资条
          </t-button>
        </t-col>
      </t-row>
    </t-form>
    <!-- :bordered="true" 边框 -->
    <!-- :active-row-type="activeRowType" 选中行 -->
    <!-- :hover="hover"  鼠标效果 -->
    <!-- drag-sort="row" 需要多个api配合使用  -->
    <!-- :stripe="true" -->
    <!-- :column-controller="columnControllerConfig" 筛选 -->
    <div class="table-container">
      <t-table :data="Data" :columns="COLUMNS" :row-key="RowKey" :vertical-align="VerticalAlign"
        :pagination="Pagination" :loading="DataLoading" maxHeight="840" @page-change="rehandlePageChange"
        @change="rehandleChange" @sort-change="SortChange" @filter-change="onFilterChange"
        @select-change="onSelectChange" :selected-row-keys="selectedRowKeys">
        <template #index="{ rowIndex }">
          <span>
            {{ rowIndex + 1 }}
          </span>
        </template>

        编号
        <template #bb="{ row }">
          NO.{{ row.bb }}
        </template>


        <!-- 审核状态 -->
        <template #b="{ row }">
          <t-tag v-if="row.b == 0" variant="light">
            待提交
          </t-tag>
          <t-tag v-if="row.b == 1" theme="warning" variant="light">
            待审核
          </t-tag>
          <t-tag v-if="row.b == 2" theme="success" variant="light">
            通过
          </t-tag>
        </template>

        <!-- 支付状态 -->
        <template #c="{ row }">
          <t-tag v-if="row.c == 0" variant="light">
            待支付
          </t-tag>
          <t-tag v-if="row.c == 1" theme="success" variant="light">
            已支付
          </t-tag>
        </template>

        <!-- 工资条推送 -->
        <template #cc="{ row }">
          <t-tag v-if="row.cc == 0" variant="light">
            未推送
          </t-tag>
          <t-tag v-if="row.cc == 1" theme="success" variant="light">
            已推送
          </t-tag>
        </template>

        <!-- 付款人 -->
        <template #h="{ row }">
          {{ row.h ? row.h : '--' }}
        </template>

        <!-- 付款时间 -->
        <template #i="{ row }">
          {{ row.i ? row.i : '--' }}
        </template>


        <!-- 操作 -->
        <template #op="slotProps">
          <t-space>
            <!-- 详情 -->
            <t-tooltip content="编辑" placement="top" theme="light">
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
      <ExamineAdd ref="ExamineaddRef" />
    </div>
  </div>

  <div class="list-common-add" v-else>
    <salaryAdd :contractId="ContractId" :onaddswtich="onaddswtich" :DetailsSwticher="DetailsSwticher" />
  </div>

</template>
<script lang="ts">
export default {
  name: 'AdmindeptSalary',
}
</script>
<script setup lang="ts">
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps, Row, TreeSelectProps } from 'tdesign-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { prefix } from '@/config/global';
import { CONTRACT_PAYMENT_TYPES, CONTRACT_STATUS, CONTRACT_TYPES } from '@/constants';
import { PAGEING_ARRAY, TABLE_SORT_FIGURE, TABLE_SORT_FILTER } from "@/config/public";
import { t } from '@/locales';
import { useSettingStore } from '@/store';

import SalaryAdd from "./salaryAdd.vue";
import ExamineAdd from "./ExamineAdd.vue";

import { TYPE_OPTIONS_COMPANY, TYPE_OPTIONS_EXAMINE, TYPE_OPTIONS_SALARY } from "./constants"
import { log } from 'node:console';

interface FormData {
  name: string;
  no: string;
  status?: number;
  type: string;
}

const store = useSettingStore();
const router = useRouter();
const COLUMNS: PrimaryTableCol[] = [
  {
    colKey: 'row-select',
    type: 'multiple',
    width: 50,
    checkProps: { allowUncheck: true },
  },
  {
    align: 'center',
    colKey: 'index',
    title: '序号',
    width: 80,
    fixed: 'left',
  },
  {
    align: 'center',
    title: "公司主体",
    colKey: 'a',
    fixed: 'left',
  },
  {
    align: 'center',
    title: "状态",
    colKey: 'b',
  },
  {
    align: 'center',
    title: "支付状态",
    colKey: 'c',
  },
  {
    align: 'center',
    title: "工资条推送",
    colKey: 'cc',
  },
  {
    align: 'center',
    title: "月份",
    colKey: 'd',
  },
  {
    align: 'center',
    title: "工资发放单",
    colKey: 'e',
  },
  {
    align: 'center',
    title: "创建人",
    colKey: 'f',
  },
  {
    align: 'center',
    title: "创建时间",
    colKey: 'g',
  },
  {
    align: 'center',
    title: "付款人",
    colKey: 'h',
  },
  {
    align: 'center',
    title: "付款时间",
    colKey: 'i',
  },
  // {
  //   align: 'center',
  //   title: "备注",
  //   colKey: 'v',
  // },
  {
    align: 'center',
    fixed: 'right',
    width: 130,
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

// 多选
// 多选
const onSelectChange = (value: any[], params: any) => {
  selectedRowKeys.value = value;
  console.log(value, params);
};

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
      a: '惠州市翼飞智能科技有限公司',
      // 0 - 待提交 1 - 待审核  2 - 通过
      b: 0,
      // 0 - 待支付 1 - 已支付
      c: 0,
      // 0 - 未推送  1 - 已推送
      cc: 0,
      d: '2024-11',
      e: 'GZFFD202411',
      f: '马艳红',
      g: '2025-12-10 12:00:00',
      h: '刘小艳',
      i: '2025-12-15 18:00:00',
    },
  ];
  Pagination.value.total = Data.value.length;
};

// const onFilterReset: TableProps['onFilterReset'] = (ctx) => {
//   console.log('filter-reset', ctx);
//   Data.value = [
//     {
//       a:'',
//     },
//   ];
//   Data.value = TABLE_SORT_FILTERFF(Data.value, filters);
//   Pagination.value.total = Data.value.length;
// };

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
  fields: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'cc',],

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
        id: 1,
        a: '惠州市翼飞智能科技有限公司',
        // 0 - 待提交 1 - 待审核  2 - 通过
        b: 0,
        // 0 - 待支付 1 - 已支付
        c: 0,
        // 0 - 未推送  1 - 已推送
        cc: 0,
        d: '2024-02',
        e: 'GZFFD202411',
        f: '马艳红',
        g: '2025-03-10 12:00:00',
        h: '',
        i: '',
      },
      {
        id: 2,
        a: '惠州市翼飞智能科技有限公司',
        // 0 - 待提交 1 - 待审核  2 - 通过
        b: 1,
        // 0 - 待支付 1 - 已支付
        c: 0,
        // 0 - 未推送  1 - 已推送
        cc: 0,
        d: '2024-01',
        e: 'GZFFD202411',
        f: '马艳红',
        g: '2025-02-10 12:00:00',
        h: '',
        i: '',
      },
      {
        id: 3,
        a: '惠州市翼飞智能科技有限公司',
        // 0 - 待提交 1 - 待审核  2 - 通过
        b: 2,
        // 0 - 待支付 1 - 已支付
        c: 1,
        // 0 - 未推送  1 - 已推送
        cc: 1,
        d: '2024-12',
        e: 'GZFFD202411',
        f: '马艳红',
        g: '2025-01-10 12:00:00',
        h: '刘小艳',
        i: '2025-01-15 18:00:00',
      },
      {
        id: 4,
        a: '惠州市翼飞智能科技有限公司',
        // 0 - 待提交 1 - 待审核  2 - 通过
        b: 2,
        // 0 - 待支付 1 - 已支付
        c: 1,
        // 0 - 未推送  1 - 已推送
        cc: 1,
        d: '2024-11',
        e: 'GZFFD202411',
        f: '马艳红',
        g: '2025-12-10 12:00:00',
        h: '刘小艳',
        i: '2025-12-15 18:00:00',
      },



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

// 推送工资条
const onPushSalary = () => {


};

// 审批组件
const ExamineaddRef = ref(null);

// 审批按钮
const selectedRowKeys = ref([]);
const onExamine = () => {
  if (selectedRowKeys.value.length == 0) return MessagePlugin.warning('请先选择需要审批的工单')
  console.log('审批', selectedRowKeys.value);
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
    // margin-top: var(--td-comp-margin-xxl);
  }
}

.form-item-content {
  width: 100%;
}

.operation-container {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  margin: 20px 0;

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
