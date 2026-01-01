<!-- 数据字典 -->
<template>
  <div class="list-common-table">
    <div class="  NavigationTitle">
      <p />
      公司主体
    </div>

    <t-form ref="form" :data="FormData" :label-width="80" colon @reset="onReset" @submit="onSubmit">
      <t-row>
        <t-col :span="10">
          <t-row :gutter="[24, 24]">
            <t-col :span="2">
              <t-form-item label="公司编码" name="name">
                <t-input v-model="FormData.name" class="form-item-content" type="search" placeholder="请输入"
                  :style="{ minWidth: '134px' }" />
              </t-form-item>
            </t-col>
            <t-col :span="2">
              <t-form-item label="公司名称" name="no">
                <t-input v-model="FormData.no" class="form-item-content" placeholder="请输入"
                  :style="{ minWidth: '134px' }" />
              </t-form-item>
            </t-col>
            <t-col :span="2">
              <t-form-item label="公司简称" name="no">
                <t-input v-model="FormData.no" class="form-item-content" placeholder="请输入"
                  :style="{ minWidth: '134px' }" />
              </t-form-item>
            </t-col>
            <t-col :span="2">
              <t-form-item label="公司状态" name="status">
                <t-select v-model="FormData.status" class="form-item-content" :options="CONTRACT_STATUS_OPTIONS"
                  @change="onSubmit" placeholder="请选择" clearable />
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
        <template #no="{ row }">
          <t-space>
            <t-link theme="primary" @click="dictAdd(row)">{{ row.no }}</t-link>
          </t-space>
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
        <template #empty>
          <span
            style="display: flex; align-items: center; justify-content: center; height: 100px; color: rgba(0, 0, 0, 0.26)">
            暂无数据-请刷新
          </span>
        </template>
      </t-table>
    </div>

    <!-- 删除操作 -->
    <t-dialog v-model:visible="confirmVisible" header="确认删除这条数据吗？" :on-cancel="onCancel" @confirm="onConfirmDelete" />

    <!-- 添加操作 -->
    <CompanyAdd ref="CompanyAddRef" />
  </div>
</template>

<script lang="ts">
export default {
  name: 'SystemCompany',
}
</script>

<script setup lang="ts">
import CompanyAdd from './companyAdd.vue'

import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { prefix } from '@/config/global';
import { t } from '@/locales';
import { useSettingStore } from '@/store';
import { PAGEING_ARRAY } from "@/config/public";


const store = useSettingStore();
const router = useRouter();

// 状态-筛选值
const CONTRACT_STATUS_OPTIONS = [
  { value: 0, label: '正常' },
  { value: 1, label: '异常' },
];

// 表格配置
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
    colKey: 'aa',
    title: '公司名称',
    fixed: 'left',
  },
  {
    title: '公司编码',
    ellipsis: true,
    align: 'center',
    colKey: 'bb',
  },
  {
    align: 'center',
    title: '简称',
    ellipsis: true,
    colKey: 'cc',
  },
  {
    align: 'center', title: '公司状态', colKey: 'status', width: 160,
  },

  {
    align: 'center',
    title: '创建人',
    ellipsis: true,
    colKey: 'dd',
  },
  {
    align: 'center',
    title: '创建时间',
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
});

// 分页查询
const fetchData = async () => {
  DataLoading.value = true;
  try {

    Data.value = [
      {
        aa: "惠州市翼飞智能科技有限公司",
        bb: "YFZN001",
        cc: "翼飞智能",
        dd: "admin",
        ee: "2025-3-12 11:00:00",
        status: 0,
      },
      {
        aa: "惠州市翼飞教育科技有限公司",
        bb: "YFJZ002",
        cc: "翼飞教育",
        dd: "admin",
        ee: "2025-3-12 11:00:00",
        status: 0,
      },
      {
        aa: "惠州翼飞传媒有限公司",
        bb: "YFCM003",
        cc: "翼飞传媒",
        dd: "admin",
        ee: "2025-3-12 11:00:00",
        status: 0,
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

// 字典子选项添加
const dictAdd = (row: any) => {
  console.log('字典子选项添加', row);
  router.push({ path: '/public/dictitem', query: { id: '999' } });
}

// 添加表单组件
const CompanyAddRef = ref(null)

// 添加事件
const onadd = () => {
  console.log('点击添加', CompanyAddRef.value.dictaddVisible);
  console.log('点击添加', CompanyAddRef.value);
  CompanyAddRef.value.onSwticher();
}

// 修改事件
const handleClickDetail = () => {
  CompanyAddRef.value.onSwticher();
  CompanyAddRef.value.getconId('999')
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