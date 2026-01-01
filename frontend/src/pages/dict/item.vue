<!-- 数据字典 -->
<template>
  <div class="list-common-table">

    <div class="NavigationTitle" @click="Back">
      <p />
      字典数据
    </div>

    <t-form ref="form" :data="FormData" :label-width="80" colon @reset="onReset" @submit="onSubmit">
      <t-row>
        <t-col :span="10">
          <t-row :gutter="[24, 24]">
            <t-col :span="1">
              <t-input v-model="FormData.name" class="form-item-content" type="search" placeholder="字典名称"
                style="width: 100%;" />
            </t-col>
            <t-col :span="1">
              <t-input v-model="FormData.no" class="form-item-content" placeholder="字典标签" style="width: 100%;" />
            </t-col>
            <t-col :span="1">

              <t-select v-model="FormData.status" class="form-item-content" :options="CONTRACT_STATUS_OPTIONS"
                @change="onSubmit" placeholder="状态" clearable />

            </t-col>
            <t-col :span="2">
              <t-button theme="primary" type="submit" :style="{ marginLeft: 'var(--td-comp-margin-s)' }">
                {{ t('components.commonTable.query') }}
              </t-button>
              <t-button type="reset" variant="base" theme="default"> {{ t('components.commonTable.reset') }} </t-button>
            </t-col>
          </t-row>
        </t-col>

        <t-col :span="2" class="operation-container">
          <t-button theme="success" @click="onadd">
            <template #icon> <t-icon name="plus" /></template>
            添 加
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
      <t-table :data="Data" :columns="COLUMNS" row-key="id" :pagination="Pagination" :loading="DataLoading"
        maxHeight="900" @page-change="rehandlePageChange" @change="rehandleChange">
        <template #index="{ rowIndex }">
          <span>
            {{ rowIndex + 1 }}
          </span>
        </template>

        <template #status="{ row }">
          <t-tag v-if="row.status === 1" theme="warning" variant="light">
            关闭
          </t-tag>
          <t-tag v-if="row.status === 0" theme="success" variant="light">
            开启
          </t-tag>
        </template>
        <!-- 操作 -->
        <template #op="slotProps">
          <t-space>
            <!-- 详情 -->
            <t-link theme="primary" @click="handleClickDetail()">修改</t-link>
            <!-- 删除 -->
            <t-link theme="danger" @click="handleClickDelete()">删除</t-link>
          </t-space>
        </template>
      </t-table>
    </div>

    <!-- 删除操作 -->
    <t-dialog v-model:visible="confirmVisible" header="确认删除这条数据吗？" :on-cancel="onCancel" @confirm="onConfirmDelete" />

    <!-- 添加操作 -->
    <DictitemAdd ref="DictaddRef" />
  </div>
</template>

<script lang="ts">
export default {
  name: 'settingdictitem',
}
</script>

<script setup lang="ts">
import DictitemAdd from './itemadd.vue'

import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { prefix } from '@/config/global';
import { t } from '@/locales';
import { useSettingStore } from '@/store';
import { PAGEING_ARRAY } from "@/config/public";



const store = useSettingStore();
const router = useRouter();
const route = useRoute();

// 状态-筛选值
const CONTRACT_STATUS_OPTIONS = [
  { value: 0, label: '开启' },
  { value: 1, label: '关闭' },
];

// 表格配置
const COLUMNS: PrimaryTableCol[] = [
  {
    align: 'center',
    colKey: 'index',
    title: '字典编码',
    width: 100,
    fixed: 'left',
  },
  {
    title: '字典标签',
    fixed: 'left',
    width: 280,
    ellipsis: true,
    align: 'center',
    colKey: 'name',
  },
  {
    align: 'center',
    title: '字典键值',
    width: 160,
    ellipsis: true,
    colKey: 'no',
  },
  {
    align: 'center', title: '状态', colKey: 'status', width: 160,
  },
  {
    align: 'center',
    title: '备注',
    width: 160,
    ellipsis: true,
    colKey: 'contractType',
  },
  {
    align: 'center',
    title: '创建时间',
    width: 160,
    ellipsis: true,
    colKey: 'paymentType',
  },
  {
    align: 'center',
    fixed: 'right',
    width: 160,
    colKey: 'op',
    title: t('components.commonTable.operation'),
  },
];

// 分页配置
const Pagination = ref({
  defaultPageSize: 20,
  total: 100,
  defaultCurrent: 1,
  pageSizeOptions: PAGEING_ARRAY,
});

// 搜索表单类型
interface FormData {
  name: string;
  no: string;
  status?: number;
  type: string;
}

// 搜索表单-值
const FormData = ref<FormData>({
  name: '',
  no: '',
  type: '',
});

// 搜索事件
const onSubmit = (val: unknown) => {
  console.log(val);
  console.log(FormData.value);
};

// 返回事件
const Back = () => {
  router.go(-1);
}

// 重置事件
const onReset = (val: unknown) => {
  console.log(val);
};

// 表格数据
const Data = ref();

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
  const id = route.query.id;
  console.log('查询数据 ==>', id);
});

// 分页查询
const fetchData = async () => {
  DataLoading.value = true;
  try {
    Data.value = [
      {
        id: "1",
        name: '合同类型',
        no: '0',
        status: 0,
        contractType: '合同类型-勿删除',
        paymentType: '2025-3-5 10:00:00',
      },
      {
        id: "2",
        name: '合同类型',
        no: '1',
        status: 0,
        contractType: '合同类型-勿删除',
        paymentType: '2025-3-5 10:00:00',
      },
      {
        id: "3",
        name: '合同类型',
        no: '2',
        status: 0,
        contractType: '合同类型-勿删除',
        paymentType: '2025-3-5 10:00:00',
      },
      {
        id: "4",
        name: '合同类型',
        no: '3',
        status: 0,
        contractType: '合同类型-勿删除',
        paymentType: '2025-3-5 10:00:00',
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

// 添加表单组件
const DictaddRef = ref(null)

// 添加事件
const onadd = () => {
  console.log('点击添加', DictaddRef.value.dictaddVisible);
  console.log('点击添加', DictaddRef.value);
  DictaddRef.value.onSwticher();
}

// 修改事件
const handleClickDetail = () => {
  DictaddRef.value.onSwticher();
  DictaddRef.value.getconId('999')
};

// 删除事件
const handleClickDelete = () => {
  confirmVisible.value = true;
};
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

  .navigationtitle {
    display: flex;
    align-items: center;
    font-size: 18px;
    margin: 0 0 40px;
    cursor: pointer;

    &:hover::after {
      // content: "点击可返回";
      // position: absolute;
      // left: 150px; // 根据实际布局调整
    }

    p {
      width: 4px;
      height: 18px;
      background: #409eff;
      margin-right: 10px;
    }
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