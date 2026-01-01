<template>
  <div class="examine-page">
    <!-- 标题 -->
    <div class="NavigationTitle">
      <p />
      审批处理
    </div>

    <!-- 类型 -->
    <t-tabs v-model="Tab" :list="tabList" @change="onTab" />

    <!-- 内容 -->
    <t-form ref="form" :data="FormData" :label-width="80" colon @reset="onReset" @submit="onSubmit"
      style="margin: 20px 0;">
      <t-row>
        <t-col :span="12">
          <t-row :gutter="[24, 24]">
            <t-col :span="2">
              <t-form-item label="发起人" name="name">
                <t-input v-model="FormData.name" class="form-item-content" type="search"
                  :placeholder="t('components.commonTable.contractNamePlaceholder')" :style="{ minWidth: '134px' }" />
              </t-form-item>
            </t-col>
            <t-col :span="2">
              <t-form-item label="合同类型" name="no">
                <t-input v-model="FormData.no" class="form-item-content"
                  :placeholder="t('components.commonTable.contractNumPlaceholder')" :style="{ minWidth: '134px' }" />
              </t-form-item>
            </t-col>
            <t-col :span="2">
              <t-form-item label="发起时间" name="status">
                <t-date-range-picker v-model="FormData.name" :presets="presets" />
              </t-form-item>
            </t-col>
            <t-col :span="2">
              <t-button theme="primary" type="submit" :style="{ marginLeft: 'var(--td-comp-margin-s)' }">
                {{ t('components.commonTable.query') }}
              </t-button>
              <t-button type="reset" variant="base" theme="default"> {{ t('components.commonTable.reset') }} </t-button>
            </t-col>
          </t-row>
        </t-col>

        <!-- <t-col :span="1" class="operation-container">
          <t-button theme="success" @click="onadd">
            <template #icon> <t-icon name="plus" /></template>
添 加
</t-button>
<t-button theme="warning">
  <template #icon> <t-icon name="download" /></template>
  导 出
</t-button>
</t-col> -->
      </t-row>
    </t-form>
    <!-- :bordered="true" 边框 -->
    <!-- :active-row-type="activeRowType" 选中行 -->
    <!-- :hover="hover"  鼠标效果 -->
    <!-- drag-sort="row" 需要多个api配合使用  -->
    <t-table :data="Data" :columns="COLUMNS" row-key="id" :pagination="Pagination" :loading="DataLoading"
      maxHeight="900" @page-change="rehandlePageChange" @change="rehandleChange" @filter-change="onFilterChange">
      <template #index="{ rowIndex }">
        <span>
          {{ rowIndex + 1 }}
        </span>
      </template>
      <!-- 待审批 -->
      <template #status="{ row }">
        <t-tag v-if="row.status === 0" theme="success" variant="light">
          审批发起
        </t-tag>
        <t-tag v-if="row.status === 1" theme="warning" variant="light">
          待审批
        </t-tag>
        <t-tag v-if="row.status === 3" theme="danger" variant="light">
          驳 回
        </t-tag>
        <t-tag v-if="row.status === 4" theme="success" variant="light">
          审批完成
        </t-tag>
      </template>

      <!-- 操作 -->
      <template #op="{ row }">
        <t-space>
          <!-- 详情 -->
          <t-tooltip v-if="SaveTab === 1 || SaveTab === 3" content="编辑" placement="top" theme="light">
            <t-button variant="text" @click="onExamine()">
              <t-icon name="view-list" />
            </t-button>
          </t-tooltip>

          <!-- 处理审批 -->
          <t-tooltip v-if="SaveTab === 2" content="审批" placement="top" theme="light">
            <t-button variant="text" @click="onsaveExamine()">
              <t-icon name="edit" />
            </t-button>
          </t-tooltip>

          <!-- 重新审批 -->
          <t-tooltip v-if="row.status === 2" content="重新审批" placement="top" theme="light">
            <t-button variant="text" @click="onRefresh()">
              <t-icon name="refresh" />
            </t-button>
          </t-tooltip>

          <!-- 删除 -->
          <t-tooltip content="删除" placement="top" theme="light">
            <t-button variant="text" @click="handleClickDelete()">
              <t-icon name="delete" />
            </t-button>
          </t-tooltip>
        </t-space>
      </template>
      <template #empty>
        <span
          style="display: flex; align-items: center; justify-content: center; height: 100px; color: rgba(0, 0, 0, 0.26)">
          暂无数据-请刷新
        </span>
      </template>
    </t-table>


    <!-- 删除操作 -->
    <t-dialog v-model:visible="confirmVisible" header="确认删除这条数据吗？" :on-cancel="onCancel" @confirm="onConfirmDelete" />

    <!-- 审批表单 -->
    <ExamineAdd ref="ExamineAddRef" />

  </div>

</template>

<script lang="ts">
export default {
  name: 'examinedispose',
}
</script>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import dayjs from 'dayjs';
import { t } from '@/locales';
import { useRouter } from 'vue-router';
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps, DateRangePickerProps } from 'tdesign-vue-next';

// 审批表单
import ExamineAdd from "./ExamineAdd.vue"
import { TABLE_SORT_FILTER } from '@/config/public';

const router = useRouter();

// 状态-筛选值
const CONTRACT_STATUS_OPTIONS = [
  { value: 0, label: '开启' },
  { value: 1, label: '关闭' },
];

// tabs栏
const tabList = [
  { label: '我的审批', value: 1, },
  { label: '待审批', value: 2, },
  { label: '已审批', value: 3, },
];

// tab栏值
const SaveTab = ref(1);

const onTab = (e: any) => {
  console.log("tab栏", e);
  SaveTab.value = e;
  if (e === 1) {
    Data.value = [
      {
        id: "1",
        aa: "进货单审批",
        bb: "张三",
        cc: "2025-3-7 16:00:00",
        dd: "财务部",
        vv: "秦学海",
        status: 0,
        ee: "",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "2",
        aa: "进货单审批",
        bb: "张三",
        cc: "2025-3-7 16:00:00",
        dd: "财务部",
        vv: "秦学海",
        status: 1,
        ee: "",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "4",
        aa: "进货单审批",
        bb: "张三",
        cc: "2025-3-7 16:00:00",
        dd: "财务部",
        vv: "秦学海",
        status: 3,
        ee: "缺少发票",
        ff: "2025-3-10 16:00:00",
      },
    ]
  } else if (e === 2) {
    Data.value = [

      {
        id: "2",
        aa: "进货单审批",
        bb: "张三",
        cc: "2025-3-7 16:00:00",
        dd: "财务部",
        vv: "秦学海",
        status: 1,
        ee: "",
        ff: "2025-3-10 16:00:00",
      },
    ]

  } else {
    Data.value = [
      {
        id: "4",
        aa: "进货单审批",
        bb: "张三",
        cc: "2025-3-7 16:00:00",
        dd: "财务部",
        vv: "秦学海",
        status: 4,
        ee: "",
        ff: "2025-3-10 16:00:00",
      }
    ]
  }
}

const Tab = ref(1);

// 表格配置
const COLUMNS: PrimaryTableCol[] = [
  {
    align: 'center',
    colKey: 'index',
    title: '序号',
    width: 50,
    fixed: 'left',
  },
  {
    title: '审批类型',
    fixed: 'left',
    width: 280,
    ellipsis: true,
    align: 'center',
    colKey: 'aa',
  },
  {
    align: 'center',
    title: '发起人',
    width: 160,
    ellipsis: true,
    colKey: 'bb',
  },
  {
    align: 'center',
    title: '发起时间',
    width: 160,
    ellipsis: true,
    colKey: 'cc',
  },
  {
    align: 'center',
    title: '当前环节',
    width: 160,
    ellipsis: true,
    colKey: 'dd',
  },
  {
    align: 'center',
    title: '审批人',
    width: 160,
    ellipsis: true,
    colKey: 'vv',
  },
  {
    align: 'center', title: '审批状态', colKey: 'status', width: 160, filter: {
      type: 'multiple',
      resetValue: [],
      list: [
        { label: 'All', checkAll: true },
        { label: '审批发起', value: 0 },
        { label: '待审批', value: 1 },
        // { label: '超时', value: 2 },
        { label: '驳回', value: 3 },
        { label: '审批完成', value: 4 },
      ],
      // 是否显示重置取消按钮，一般情况不需要显示
      showConfirmAndReset: true,
    },
  },

  {
    align: 'center',
    title: '备注',
    width: 160,
    ellipsis: true,
    colKey: 'ee',
  },
  {
    align: 'center',
    fixed: 'right',
    width: 160,
    colKey: 'op',
    title: t('components.commonTable.operation'),
  },
];
// 发起时间配置
const presets = ref<DateRangePickerProps['presets']>({
  最近7天: [dayjs().subtract(6, 'day').toDate(), dayjs().toDate()],
  最近3天: [dayjs().subtract(2, 'day').toDate(), dayjs().toDate()],
  今天: [dayjs().toDate(), dayjs().toDate()],
});


// 分页配置
const Pagination = ref({
  defaultPageSize: 20,
  total: 100,
  defaultCurrent: 1,
  pageSizeOptions: [10, 20, 50, 100],
});

// 搜索表单
interface FormData {
  name: any;
  no: string;
  status?: number;
  type: string;
}

// 搜索表单-值
const FormData = ref<FormData>({
  name: [],
  no: '',
  type: '',
});

// 搜索事件
const onSubmit = (val: unknown) => {
  console.log(val);
  console.log(FormData.value);
};

// 重置事件
const onReset = (val: unknown) => {
  console.log(val);
};

// 表格筛选
const onFilterChange: TableProps['onFilterChange'] = (filters, ctx) => {
  console.log('filter-change', filters, ctx);
  Data.value = [];
  Data.value = [
    {
      id: "1",
      aa: "进货单审批",
      bb: "张三",
      cc: "2025-3-7 16:00:00",
      dd: "财务部",
      vv: "秦学海",
      status: 0,
      ee: "",
      ff: "2025-3-10 16:00:00",
    },
    {
      id: "2",
      aa: "进货单审批",
      bb: "张三",
      cc: "2025-3-7 16:00:00",
      dd: "财务部",
      vv: "秦学海",
      status: 1,
      ee: "",
      ff: "2025-3-10 16:00:00",
    },
    {
      id: "3",
      aa: "进货单审批",
      bb: "张三",
      cc: "2025-3-1 16:00:00",
      dd: "财务部",
      vv: "秦学海",
      status: 2,
      ee: "超过审批时间",
      ff: "2025-3-7 14:00:00",
    },
    {
      id: "4",
      aa: "进货单审批",
      bb: "张三",
      cc: "2025-3-7 16:00:00",
      dd: "财务部",
      vv: "秦学海",
      status: 3,
      ee: "缺少发票",
      ff: "2025-3-10 16:00:00",
    },
    {
      id: "4",
      aa: "进货单审批",
      bb: "张三",
      cc: "2025-3-7 16:00:00",
      dd: "财务部",
      vv: "秦学海",
      status: 4,
      ee: "",
      ff: "2025-3-10 16:00:00",
    },

  ];
  Data.value = TABLE_SORT_FILTER(Data.value, filters);
  Pagination.value.total = Data.value.length;
};

// 表格
const Data = ref([]);
// 表格加载框
const DataLoading = ref(false);

// 分页-统一变化
const rehandlePageChange = (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);
};

// 分页-调用数据
const rehandleChange = (changeParams: unknown, triggerAndData: unknown) => {
  console.log('统一Change', changeParams, triggerAndData);
};


// 初始化
onMounted(() => {
  fetchData();
});

// 分页查询
const fetchData = async () => {
  DataLoading.value = true;
  try {

    Data.value = [
      {
        id: "1",
        aa: "进货单审批",
        bb: "张三",
        cc: "2025-3-7 16:00:00",
        dd: "财务部",
        vv: "秦学海",
        status: 0,
        ee: "",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "2",
        aa: "进货单审批",
        bb: "张三",
        cc: "2025-3-7 16:00:00",
        dd: "财务部",
        vv: "秦学海",
        status: 1,
        ee: "",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "3",
        aa: "进货单审批",
        bb: "张三",
        cc: "2025-3-1 16:00:00",
        dd: "财务部",
        vv: "秦学海",
        status: 2,
        ee: "超过审批时间",
        ff: "2025-3-7 14:00:00",
      },
      {
        id: "4",
        aa: "进货单审批",
        bb: "张三",
        cc: "2025-3-7 16:00:00",
        dd: "财务部",
        vv: "秦学海",
        status: 3,
        ee: "缺少发票",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "4",
        aa: "进货单审批",
        bb: "张三",
        cc: "2025-3-7 16:00:00",
        dd: "财务部",
        vv: "秦学海",
        status: 4,
        ee: "",
        ff: "2025-3-10 16:00:00",
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


// 删除表单
// 删除开关
const confirmVisible = ref(false);

// 删除-确定
const onConfirmDelete = () => {
  // 真实业务请发起请求
  Pagination.value.total = Data.value.length;
  confirmVisible.value = false;
  MessagePlugin.success('删除成功');
};

// 删除-取消
const onCancel = () => {
};


// 添加事件
const onadd = () => {
  // console.log('点击添加', DictaddRef.value.dictaddVisible);
  // console.log('点击添加', DictaddRef.value);
  // DictaddRef.value.onSwticher();
}


// 删除事件
const handleClickDelete = () => {
  confirmVisible.value = true;
};

// 重新审批
const onRefresh = () => {
  MessagePlugin.success('重新审批成功');
}

// 查看事件
const onExamine = () => {
  router.push('/detail/base');
}

// 处理审批
const ExamineAddRef = ref(null);
const onsaveExamine = () => {
  ExamineAddRef.value.onSwticher();
}

</script>

<style lang="less" scoped>
.examine-page {
  height: 100%;
  background: #fff;
  background-color: var(--td-bg-color-container);
  padding: var(--td-comp-paddingTB-xxl) var(--td-comp-paddingLR-xxl);
  border-radius: var(--td-radius-medium);


}
</style>