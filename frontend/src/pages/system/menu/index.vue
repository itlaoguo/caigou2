<!-- 合同审批 -->
<template>
  <div class="system-box">
    <div class="list-common-table">
      <div class="NavigationTitle">
        <p />
        页面管理
      </div>

      <t-form :data="formData" :label-width="80" colon @reset="onReset">
        <t-row>
          <t-col :span="10">
            <t-row :gutter="[24, 24]">
              <t-col :span="2">
                <t-input v-model="formData.permission_name" class="form-item-content" type="search" placeholder="菜单名称"
                  style="width:100%" :onEnter="onSearch" />
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
        <t-enhanced-table :data="Data" :columns="COLUMNS" :vertical-align="verticalAlign" :loading="DataLoading"
          maxHeight="900" :stripe="true" @sort-change="SortChange" row-key="id" :tree="treeProp">
          <template #hidden="{ row }">
            <t-tag v-if="row.hidden === 1 && row.type != 3" theme="warning" variant="light">
              关闭
            </t-tag>
            <t-tag v-if="row.hidden === 2 && row.type != 3" theme="success" variant="light">
              开启
            </t-tag>
          </template>
          <template #type="{ row }">

            <t-tag v-if="row.type === 1" theme="default" variant="light">
              目录
            </t-tag>
            <t-tag v-if="row.type === 2" theme="success" variant="light">
              页面
            </t-tag>
            <t-tag v-if="row.type === 3" theme="warning" variant="light">
              按钮
            </t-tag>
          </template>

          <template #icon="{ row }">
            <t-icon :name="row.icon" />
          </template>

          <template #contractType="{ row }">
            <p v-if="row.contractType === CONTRACT_TYPES.MAIN">{{ t('pages.listBase.contractStatusEnum.fail') }}</p>
            <p v-if="row.contractType === CONTRACT_TYPES.SUB">{{ t('pages.listBase.contractStatusEnum.audit') }}</p>
            <p v-if="row.contractType === CONTRACT_TYPES.SUPPLEMENT">
              {{ t('pages.listBase.contractStatusEnum.pending') }}
            </p>
          </template>


          <!-- 操作 -->
          <template #op="slotProps">
            <t-space>
              <!-- 修改 -->
              <t-link theme="primary" @click.stop="handleClickDetail(slotProps)"> 编 辑</t-link>
              <!-- 新增 -->
              <t-link theme="primary" @click.stop="onhandleClickadd(slotProps)"> 新 增</t-link>
              <!-- 删除 -->
              <t-link theme="danger" @click.stop="handleClickDelete(slotProps)"> {{ t('pages.listBase.delete')
                }}</t-link>
            </t-space>
          </template>
        </t-enhanced-table>

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
  name: 'menuPage',
}
</script>
<script setup lang="ts">
import { MessagePlugin, TableProps } from 'tdesign-vue-next';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { CONTRACT_PAYMENT_TYPES, CONTRACT_STATUS, CONTRACT_TYPES } from '@/constants';
import { t } from '@/locales';
import { useSettingStore } from '@/store';
import MenuAdd from "./add.vue";
import { MenuList, MenuListDeleter } from "@/api/system/menu";

import { COLUMNS, SearchFormData } from "./constants"
import { TransformActionsToChildren } from "@/config/public";

const formData = ref<SearchFormData>(
  {
    permission_name: '',
  }
);

// 表格配置
const verticalAlign = 'bottom' as const;
const SortChange: TableProps['onSortChange'] = (val) => {
  console.log('sort-change', val);
};

// 替换树形结构图标
const TreeExpandIcon = (h: any, { type }: { type: string }) => {
  return type === 'expand' ? '>' : '>';
};

// 删除开关
const ConfirmVisible = ref(false);

// 列表数据
const Data = ref();

// 添加
const onadd = () => {
  console.log('点击添加');
  DictaddRef.value.onSwticher();
  DictaddRef.value.FormData.type = 1;
  DictaddRef.value.FormData.parent_id = 0;
}


// 配置树节点展开
const treeProp = ref({
  childrenKey: 'children',
  defaultExpandAll: false,
})

// 分页查询
const DataLoading = ref(false);
const fetchData = async (data?: { permission_name: string; }) => {
  DataLoading.value = true;
  try {
    const params = data || { permission_name: "" }
    const res = await MenuList(params);
    console.log("路由数据==》", res);
    Data.value = TransformActionsToChildren(res.data);
    nextTick(() => {
      treeProp.value.defaultExpandAll = true;
    })
    console.log("Data.value", Data.value);
  } catch (e) {
    console.log(e);
  } finally {
    DataLoading.value = false;
  }
};


// 删除操作
const DeleteIdx = ref(-1);

const resetIdx = () => {
  DeleteIdx.value = -1;
};

const onCancel = () => {
  resetIdx();
};

onMounted(() => {
  fetchData();
});

// 删除事件
const MenuId = ref(null)
const handleClickDelete = (slot: { row: { rowIndex: number, id: number } }) => {
  console.log("这一行==》", slot.row.id);
  MenuId.value = slot.row.id;
  DeleteIdx.value = slot.row.rowIndex;
  ConfirmVisible.value = true;
};


// 删除确定事件
const onConfirmDelete = async () => {
  const res = await MenuListDeleter(MenuId.value);
  console.log("删除成功==》", res);
  if (res.code == 10000) {
    fetchData();
    MessagePlugin.success('删除成功');
    resetIdx();
    ConfirmVisible.value = false;
  }
};

// 搜索
const onSearch = () => {
  console.log(formData.value.permission_name);
  fetchData(formData.value);
}

// 搜索重置
const onReset = () => {
  formData.value.permission_name = '';
  fetchData();
};

// 修改
const DictaddRef = ref(null)
const handleClickDetail = (slot: { row: { id: number, parent_id: number } }) => {
  DictaddRef.value.onSwticher();
  DictaddRef.value.getitemId(slot.row.id);
};

// 新增
const onhandleClickadd = (slot: { row: { id: number } }) => {
  DictaddRef.value.onSwticher();
  console.log(DictaddRef.value);
  console.log(slot.row.id);
  DictaddRef.value.FormData.type = 1;
  DictaddRef.value.FormData.parent_id = slot.row.id;
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
