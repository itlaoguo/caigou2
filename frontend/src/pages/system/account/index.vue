<!-- 合同审批 -->
<template>
  <div class="system-box">
    <div class="list-common-table">
      <div class="NavigationTitle">
        <p />
        账号管理
      </div>

      <t-form :label-width="80" colon @reset="onReset">
        <t-row>
          <t-col :span="10">
            <t-row :gutter="[24, 24]">
              <t-col :span="2">
                <t-input v-model="formData.username" class="form-item-content" type="search" placeholder="用户名称"
                  style="width: 100%" :onEnter="onSearch" />
              </t-col>
              <t-col :span="2">
                <t-input v-model="formData.email" class="form-item-content" type="search" placeholder="邮箱号码"
                  style="width: 100%" :onEnter="onSearch" />
              </t-col>
              <t-col :span="2">
                <t-select v-model="formData.status" class="form-item-content" :options="CONTRACT_STATUS_OPTIONS"
                  @change="onSearch" placeholder="用户状态" />
              </t-col>
              <t-col :span="2">
                <t-button type="reset" variant="base" theme="default"> {{ t('components.commonTable.reset') }}
                </t-button>
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
      <!-- :treeExpandAndFoldIcon="treeExpandIcon" 下拉图标 -->
      <div class="table-container">
        <t-table :data="Data" :columns="COLUMNS" :loading="DataLoading" maxHeight="900" :pagination="Pagination"
          @sort-change="sortChange" @page-change="rehandlePageChange" @change="rehandleChange" row-key="id">

          <template #status="{ row }">
            <t-tag v-if="row.status == 1" theme="success" variant="light">
              开启
            </t-tag>
            <t-tag v-if="row.status == 2" theme="warning" variant="light">
              关闭
            </t-tag>
          </template>


          <!-- 操作 -->
          <template #op="slotProps">
            <t-space>
              <!-- 查 看 -->
              <t-link theme="primary" @click.stop="handleClickDetail(slotProps)">编 辑</t-link>
              <!-- 快捷登录 -->
              <!-- <t-link theme="primary" @click.stop="handleClickDetail()">快捷登录</t-link> -->
              <!-- 岗位权限 -->
              <!-- <t-link theme="primary" @click.stop="handlejurisdiction()">岗位权限</t-link> -->
              <!-- 删除 -->
              <t-link theme="danger" @click.stop="handleClickDelete(slotProps)"> {{ t('pages.listBase.delete')
                }}</t-link>
            </t-space>
          </template>
        </t-table>

        <!-- 删除操作 -->
        <t-dialog v-model:visible="ConfirmVisible" header="确认删除当前所选数据吗？" :on-cancel="onCancel"
          @confirm="onConfirmDelete" />

        <!-- 添加操作 -->
        <MenuAdd ref="DictaddRef" :fetchData="fetchData" />
      </div>
    </div>
  </div>

</template>

<script lang="ts">
export default {
  name: 'systemAccount',
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

import MenuAdd from "./add.vue";
import { PAGEING_ARRAY } from "@/config/public";

import { AccountList, AccountDeleter } from "@/api/system/account";
import { COLUMNS, SearchFormData, CONTRACT_STATUS_OPTIONS } from "./constants"


const store = useSettingStore();
const router = useRouter();




// 子组件
const DictaddRef = ref(null)

// 搜索表单
const formData = ref<SearchFormData>({
  username: '',
  email: '',
  status: null,
});



// 权限管理
const RbacPageref = ref(null)
const handlejurisdiction = () => {
  RbacPageref.value.onSwticher();
}

// 筛选
const sortChange: TableProps['onSortChange'] = (val) => {
  console.log('sort-change', val);
};

const Data = ref();

// 添加
const onadd = () => {
  console.log('点击添加');
  DictaddRef.value.onSwticher();
}

const DataLoading = ref(false);

const Pagination = ref({
  defaultPageSize: 20,
  total: 100,
  defaultCurrent: 1,
  pageSizeOptions: PAGEING_ARRAY,
});


const fetchData = async (data?: {
  username: string;
  email: string;
  status: number;
}) => {
  DataLoading.value = true;
  try {
    const res = await AccountList({
      username: data?.username || '',
      email: data?.email || '',
      status: data?.status || '',
      page: Pagination.value.defaultCurrent,
      limit: Pagination.value.defaultPageSize,
    });
    if (res.code === 10000) {
      Data.value = res.data;
    }

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


// 删除
const DeleteIdx = ref(-1);
const ConfirmVisible = ref(false);

const resetIdx = () => {
  DeleteIdx.value = -1;
};

// 删除确定事件
const onConfirmDelete = async () => {
  const res = await AccountDeleter(MenuId.value);
  console.log("删除成功==》", res);
  if (res.code == 10000) {
    fetchData();
    MessagePlugin.success('删除成功');
    resetIdx();
    ConfirmVisible.value = false;
  }
};

const onCancel = () => {
  resetIdx();
};

onMounted(() => {
  fetchData();
});

// 删除
const MenuId = ref(null)
const handleClickDelete = (slot: { row: { rowIndex: number, id: number } }) => {
  MenuId.value = slot.row.id;
  DeleteIdx.value = slot.row.rowIndex;
  ConfirmVisible.value = true;
};


// 搜索
const onSearch = () => {
  fetchData(formData.value);
}

// 搜索重置
const onReset = () => {
  formData.value.username = '';
  formData.value.email = '';
  formData.value.status = null;
  fetchData();
};

// 修改
const handleClickDetail = (slot: { row: { rowIndex: number, id: number } }) => {
  DictaddRef.value.onSwticher();
  DictaddRef.value.getitemId(slot.row.id);
};


</script>

<style lang="less" scoped>
.system-box {
  padding: 20px;
}

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
