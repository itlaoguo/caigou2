<!-- 通告管理 -->
<template>
  <div class="list-common-table" v-if="AddSwtich">
    <div class="NavigationTitle">
      <p />
      公告管理
    </div>

    <t-form ref="form" :data="FormData" :label-width="80" colon @reset="onReset" @submit="onSubmit">
      <t-row>
        <t-col :span="10">
          <t-row :gutter="[24, 24]">
            <t-col :span="2">
              <t-form-item label="公告标题" name="name">
                <t-input v-model="FormData.name" class="form-item-content" type="search"
                  :placeholder="t('components.commonTable.contractNamePlaceholder')" :style="{ minWidth: '134px' }" />
              </t-form-item>
            </t-col>
            <t-col :span="2">
              <t-form-item label="创建人员" name="status">
                <t-input v-model="FormData.name" class="form-item-content" type="search"
                  :placeholder="t('components.commonTable.contractNamePlaceholder')" :style="{ minWidth: '134px' }" />
              </t-form-item>
            </t-col>
            <t-col :span="2">
              <t-form-item label="发布时间" name="status">
                <t-date-picker v-model="FormData.name" />
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
          <!-- <t-button theme="warning">
            <template #icon> <t-icon name="download" /></template>
            导 出
          </t-button> -->
        </t-col>
      </t-row>
    </t-form>
    <!-- :bordered="true" 边框 -->
    <!-- :active-row-type="activeRowType" 选中行 -->
    <!-- :hover="hover"  鼠标效果 -->
    <!-- drag-sort="row" 需要多个api配合使用  -->
    <!-- :treeExpandAndFoldIcon="treeExpandIcon" 下拉图标 -->
    <div class="table-container">
      <t-table :data="Data" :columns="COLUMNS" :vertical-align="VerticalAlign" :loading="DataLoading" maxHeight="900"
        :pagination="Pagination" @sort-change="sortChange" @page-change="rehandlePageChange" @change="rehandleChange"
        row-key="id">

        <template #index="{ rowIndex }">
          <span>
            {{ rowIndex + 1 }}
          </span>
        </template>

        <template #status="{ row }">
          <!-- <t-tag v-if="row.status == 1" theme="warning" variant="light">
            关闭
          </t-tag>
          <t-tag v-if="row.status == 0" theme="success" variant="light">
            开启
          </t-tag> -->
          <t-switch v-model="row.status" size="large" :customValue="['0', '1']" @change="onNotification">
          </t-switch> -
          <t-tag v-if="row.status == '0'" theme="success" variant="light">
            显示通告
          </t-tag>
          <t-tag v-if="row.status == '1'" theme="warning" variant="light">
            不显示
          </t-tag>
        </template>


        <!-- 操作 -->
        <template #op="slotProps">
          <t-space>
            <!-- 查 看 -->
            <t-link theme="primary" @click.stop="handleClickDetail()">查 看</t-link>
            <!-- 删除 -->
            <t-link theme="danger" @click.stop="handleClickDelete(slotProps)"> {{ t('pages.listBase.delete') }}</t-link>
          </t-space>
        </template>
      </t-table>

      <!-- 删除操作 -->
      <t-dialog v-model:visible="ConfirmVisible" header="确认删除当前所选数据吗？" :body="confirmBody" :on-cancel="onCancel"
        @confirm="onConfirmDelete" />

      <!-- 通告操作 -->
      <t-dialog v-model:visible="NotificdataVisible" :header="'确认' + (Notificdata == '1' ? '关闭' : '开启') + '这条公告'"
        :body="confirmBody" :on-cancel="onNotificCancel" @confirm="onNotificSave" />
    </div>
  </div>

  <!-- 添加 -->
  <div class="list-common-add" v-else>
    <NotificAdd :contractId="ContractId" :onaddswtich="onaddswtich" />
  </div>
</template>

<script lang="ts">
export default {
  name: 'systemnotification',
}
</script>
<script setup lang="ts">
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';// ‌:ml-citation{ref="1" data="citationList"}
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';


import { prefix } from '@/config/global';
import { CONTRACT_PAYMENT_TYPES, CONTRACT_STATUS, CONTRACT_TYPES } from '@/constants';
import { t } from '@/locales';
import { useSettingStore } from '@/store';

// 公告添加
import NotificAdd from "./NotificAdd.vue"
import { PAGEING_ARRAY } from '@/config/public';


interface FormData {
  name: string;
  no: string;
  status?: number;
  type: string;
}

const store = useSettingStore();
const router = useRouter();

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
    title: '标题',
    fixed: 'left',
    width: 160,
    ellipsis: true,
    align: 'left',
    colKey: 'account',
  },
  {
    align: 'center',
    title: '创建人',
    width: 160,
    ellipsis: true,
    colKey: 'name',
  },
  {
    align: 'center',
    title: '公告状态',
    width: 160,
    ellipsis: true,
    colKey: 'status',
  },
  {
    align: 'center',
    title: '首页状态',
    width: 160,
    ellipsis: true,
    colKey: 'status',
  },
  {
    align: 'center',
    title: '创建时间',
    width: 160,
    ellipsis: true,
    colKey: 'created',
  },
  {
    align: 'center',
    title: '更新时间',
    width: 160,
    ellipsis: true,
    colKey: 'created',
  },
  {
    align: 'center',
    fixed: 'right',
    width: 100,
    colKey: 'op',
    title: t('components.commonTable.operation'),
  },
];



const SearchForm = {
  name: '',
  no: '',
  type: '',
};

const FormData = ref<FormData>({ ...SearchForm });
const VerticalAlign = 'bottom' as const;


const sortChange: TableProps['onSortChange'] = (val) => {
  console.log('sort-change', val);
};

const ConfirmVisible = ref(false);

const Data = ref();

// 添加
const AddSwtich = ref(true)

const ContractId = ref(null)

const onaddswtich = () => {
  AddSwtich.value = !AddSwtich.value
}

const onadd = () => {
  console.log('点击添加');
  onaddswtich();
}

const DataLoading = ref(false);

const Pagination = ref({
  defaultPageSize: 20,
  total: 100,
  defaultCurrent: 1,
  pageSizeOptions: PAGEING_ARRAY,
});


const fetchData = async () => {
  DataLoading.value = true;
  try {

    Data.value = [
      {
        id: '1',
        account: 'ERP1.0-版本',
        name: 'admin',
        status: '0',
        created: '2025-3-6 16:48:00'
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

const rehandlePageChange = (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);
};
const rehandleChange = (changeParams: unknown, triggerAndData: unknown) => {
  console.log('统一Change', changeParams, triggerAndData);
};

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

  ConfirmVisible.value = false;
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
  ConfirmVisible.value = true;
};
const onReset = (val: unknown) => {
  console.log(val);
};

// 通告状态
const Notificdata = ref('1');
const NotificdataVisible = ref(false);

const onNotification = (e: any) => {
  console.log('公告开关');
  Notificdata.value = e;
  NotificdataVisible.value = true;
}


// 公告取消
const onNotificCancel = () => {
  console.log('取消');
  Notificdata.value = '';
}

// 公告确认
const onNotificSave = () => {
  console.log('确认');
  Notificdata.value = '';
  NotificdataVisible.value = false;
}

// 修改
const handleClickDetail = () => {
  onaddswtich();
};

const onSubmit = (val: unknown) => {
  console.log(val);
  console.log(FormData.value);
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
