<!-- 合同审批 -->
<template>
  <div class="system-box">
    <div class="list-common-table">
      <div class="NavigationTitle">
        <p />
        角色管理
      </div>

      <t-form :data="formData" :label-width="80" colon @reset="onReset">
        <t-row>
          <t-col :span="10">
            <t-row :gutter="[24, 24]">
              <t-col :span="2">
                <t-input v-model="formData.role_name" class="form-item-content" type="search" placeholder="角色名称"
                  style="width: 100%" :onEnter="onSearch" />
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
        <t-enhanced-table :data="Data" :columns="COLUMNS" :vertical-align="VerticalAlign" :loading="DataLoading"
          maxHeight="900" @change="rehandleChange" row-key="id" :tree="treeProp">
          <template #status="{ row }">
            <t-tag v-if="row.status == 1" theme="warning" variant="light">
              关闭
            </t-tag>
            <t-tag v-if="row.status == 0" theme="success" variant="light">
              开启
            </t-tag>
          </template>


          <!-- 操作 -->
          <template #op="slotProps">
            <t-space>
              <!-- 查 看 -->
              <t-link theme="primary" @click.stop="handleClickDetail(slotProps)">编 辑</t-link>
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
  name: 'systemrole',
}
</script>
<script setup lang="ts">
import { MessagePlugin, TableProps } from 'tdesign-vue-next';// ‌:ml-citation{ref="1" data="citationList"}
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';

import { prefix } from '@/config/global';
import { CONTRACT_STATUS, CONTRACT_TYPES } from '@/constants';
import { t } from '@/locales';
import { useSettingStore } from '@/store';
import MenuAdd from "./add.vue";
import { RoleListDeleter, RoleList } from "@/api/system/role";
import { COLUMNS, SearchFormData } from "./constants"

const store = useSettingStore();
const router = useRouter();


// 子组件
const DictaddRef = ref(null);

const formData = ref<SearchFormData>({
  role_name: '',
});

const VerticalAlign = 'bottom' as const;


// 权限管理
const RbacPageref = ref(null)

const sortChange: TableProps['onSortChange'] = (val) => {
  console.log('sort-change', val);
};

const ConfirmVisible = ref(false);

const Data = ref();

// 添加
const onadd = () => {
  console.log('点击添加');
  DictaddRef.value.onSwticher();
  DictaddRef.value.getRoleList();
  DictaddRef.value.FormData.parent_id = 0;
}

const DataLoading = ref(false);

// 配置树节点展开
const treeProp = ref({
  childrenKey: 'children',
  defaultExpandAll: false,
})

// 分页查询
const fetchData = async (data?: { role_name: string; }) => {
  DataLoading.value = true;
  try {
    const params = data || { role_name: "" }
    const res = await RoleList(params);
    Data.value = res.data;

    // Data.value = [
    //   {
    //     id: '1',
    //     account: 'qinxuehai',
    //     name: '秦学海',
    //     statice: '程序开发部',
    //     phone: '15220730439',
    //     sex: '男',
    //     Email: '3026341569@qq.com',
    //     status: '0',
    //     created: '2025-3-6 16:48:00'
    //   },
    // ];

  } catch (e) {
    console.log(e);
  } finally {
    DataLoading.value = false;
  }
};

const rehandleChange = (changeParams: unknown, triggerAndData: unknown) => {
  console.log('统一Change', changeParams, triggerAndData);
};

const DeleteIdx = ref(-1);

const resetIdx = () => {
  DeleteIdx.value = -1;
};

const onConfirmDelete = async () => {
  // 真实业务请发起请求
  const res = await RoleListDeleter(MenuId.value);
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

const MenuId = ref(null)
const handleClickDelete = (slot: { row: { rowIndex: number, id: number } }) => {
  console.log("这一行==》", slot.row.id);
  MenuId.value = slot.row.id;
  DeleteIdx.value = slot.row.rowIndex;
  ConfirmVisible.value = true;
};


// 搜索
const onSearch = () => {
  console.log(formData.value.role_name);
  fetchData(formData.value);
}

// 搜索重置
const onReset = () => {
  formData.value.role_name = '';
  fetchData(formData.value);
};

// 查看
const handleClickDetail = (slot: { row: { rowIndex: number, id: number } }) => {
  DictaddRef.value.onSwticher();
  DictaddRef.value.getRoleList();
  DictaddRef.value.getitemId(slot.row.id);
};

// 新增
const onhandleClickadd = (slot: { row: { id: number } }) => {
  DictaddRef.value.onSwticher();
  DictaddRef.value.getRoleList();
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
