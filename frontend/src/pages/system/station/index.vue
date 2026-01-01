<!-- 岗位管理 -->
<template>
  <div class="system-box">
    <div class="list-common-table">
      <div class="NavigationTitle">
        <p />
        部门管理
      </div>

      <t-form ref="form" :data="formData" :label-width="80" colon @reset="onReset" @submit="onSubmit">
        <t-row>
          <t-col :span="10">
            <t-row :gutter="[24, 24]">
              <t-col :span="2">
                <t-form-item label="岗位名称" name="name">
                  <t-input v-model="formData.name" class="form-item-content" type="search"
                    :placeholder="t('components.commonTable.contractNamePlaceholder')" :style="{ minWidth: '134px' }" />
                </t-form-item>
              </t-col>
              <t-col :span="2">
                <t-form-item label="岗位状态" name="status">
                  <t-select v-model="formData.status" class="form-item-content" :options="CONTRACT_STATUS_OPTIONS"
                    @change="onSubmit" :placeholder="t('components.commonTable.contractStatusPlaceholder')" clearable />
                </t-form-item>
              </t-col>
              <t-col :span="2">
                <t-button theme="primary" type="submit" :style="{ marginLeft: 'var(--td-comp-margin-s)' }">
                  {{ t('components.commonTable.query') }}
                </t-button>
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
          maxHeight="900" @sort-change="sortChange" row-key="id" :tree="{
            childrenKey: 'children',
            expandTreeNodeOnClick: true,
          }" v-model:expandedTreeNodes="ExpandedIds">

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
              <!-- 修改 -->
              <t-link theme="primary" @click.stop="handleClickDetail(slotProps)"> 修 改</t-link>
              <!-- 新增 -->
              <t-link theme="primary" @click.stop="onadd()"> 新 增</t-link>
              <!-- 权限管理 -->
              <t-link theme="primary" @click.stop="handlejurisdiction()"> 权限管理</t-link>
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

        <!-- 权限管理 -->
        <RbacPage ref="RbacPageref" />

      </div>
    </div>
  </div>



</template>

<script lang="ts">
export default {
  name: 'systemstation',
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
import RbacPage from "./rbacpage.vue"

import { StationList, StationDeleter } from "@/api/system/station";

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
    title: '岗位名称',
    fixed: 'left',
    width: 280,
    ellipsis: true,
    align: 'left',
    colKey: 'department_name',
  },
  {
    align: 'center',
    title: '排序',
    width: 160,
    ellipsis: true,
    colKey: 'sort',
  },
  {
    align: 'center',
    title: '状态',
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
    fixed: 'right',
    width: 160,
    colKey: 'op',
    title: t('components.commonTable.operation'),
  },
];


// 子组件
const DictaddRef = ref(null)


const SearchForm = {
  name: '',
  no: '',
  type: '',
};

const formData = ref<FormData>({ ...SearchForm });
const verticalAlign = 'bottom' as const;



const sortChange: TableProps['onSortChange'] = (val) => {
  console.log('sort-change', val);
};

const ConfirmVisible = ref(false);

const Data = ref();

// 添加
const onadd = () => {
  console.log('点击添加');
  DictaddRef.value.onSwticher();
}

const DataLoading = ref(false);

// 示例方法：递归收集节点ID
const generateExpandedIds = (data: any) => {
  const ids: any = [];
  const traverse = (nodes: any) => {
    nodes.forEach((node: { id: any; children: string | any[]; }) => {
      ids.push(node.id);
      if (node.children?.length) traverse(node.children);
    });
  };
  traverse(data);
  return ids;
};

const ExpandedIds = ref([]);


// 权限管理
const RbacPageref = ref(null)
const handlejurisdiction = () => {
  RbacPageref.value.onSwticher();
}

const fetchData = async () => {
  DataLoading.value = true;
  try {
    const res = await StationList({});
    console.log("路由数据==》", res);
    Data.value = res.data;
    console.log("Data.value", Data.value);

    // Data.value = [
    //   {
    //     id: "1",
    //     name: '董事长-总经办',
    //     no: '1',
    //     status: '0',
    //     children: [
    //       {
    //         id: "11",
    //         name: '会计出纳-财务部',
    //         no: '1',
    //         status: '0',

    //       },
    //       {
    //         id: "12",
    //         name: '副总经理(程总) - 运营中心(程总)',
    //         no: '2',
    //         status: '0',

    //         children: [
    //           {
    //             id: "22",
    //             name: '教学 - 教培部(liliting)',
    //             no: '2',
    //             status: '0',

    //           },
    //           {
    //             id: "23",
    //             name: '校研 - 教培部',
    //             no: '2',
    //             status: '0',

    //           },
    //         ]
    //       },
    //       {
    //         id: "13",
    //         name: '总经理 - 管理中心(徐总)',
    //         no: '3',
    //         status: '0',
    //         children: [
    //           {
    //             id: "24",
    //             name: '仓库主管 - 仓储部(XiaoMeiJiao)',
    //             no: '2',

    //             status: '0',
    //           },
    //           {
    //             id: "25",
    //             name: '采购主管 - 采购部(DengLiHui)',
    //             no: '2',
    //             status: '0',
    //           },
    //           {
    //             id: "26",
    //             name: '生产部主管 - 生管部',
    //             no: '2',
    //             status: '0',
    //           },
    //           {
    //             id: "27",
    //             name: '生产部主管 - 生产部',
    //             no: '2',
    //             status: '0',
    //           },
    //         ]
    //       },
    //       {
    //         id: "14",
    //         name: '财务主管 - 财务部',
    //         no: '3',
    //         status: '0',
    //       },
    //       {
    //         id: "15",
    //         name: '成本会计 - 财务部',
    //         no: '3',
    //         status: '0',
    //       },
    //       {
    //         id: "16",
    //         name: '内控专员 - 财务部',
    //         no: '3',
    //         status: '0',
    //       },

    //     ]
    //   },
    // ];

    ExpandedIds.value = generateExpandedIds(Data.value);
    console.log(ExpandedIds.value);


  } catch (e) {
    console.log(e);
  } finally {
    DataLoading.value = false;
  }
};



const deleteIdx = ref(-1);

const resetIdx = () => {
  deleteIdx.value = -1;
};

const onConfirmDelete = async () => {
  // 真实业务请发起请求
  const res = await StationDeleter(StationId.value);
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

const StationId = ref(null);
const handleClickDelete = (slot: { row: { rowIndex: number, id: number } }) => {
  console.log("这一行==》", slot.row.id);
  StationId.value = slot.row.id;
  deleteIdx.value = slot.row.rowIndex;
  ConfirmVisible.value = true;
};
const onReset = (val: unknown) => {
  console.log(val);
};

// 修改
const handleClickDetail = (slot: { row: { id: number } }) => {
  DictaddRef.value.onSwticher();
  DictaddRef.value.getitemId(slot.row.id);
};

const onSubmit = (val: unknown) => {
  console.log(val);
  console.log(formData.value);
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
