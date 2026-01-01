<template>
  <div class="examine-page">
    <!-- 标题 -->
    <div class="NavigationTitle">
      <p />
      办公用品
    </div>

    <!-- 类型 -->
    <!-- <t-tabs v-model="Tab" :list="tabList" @change="onTab" /> -->

    <!-- 内容 -->
    <t-form ref="form" :data="FormData" :label-width="100" colon @reset="onReset" @submit="onSubmit"
      style="margin: 20px 0;">
      <t-row>
        <t-col :span="12">
          <t-row :gutter="[24, 24]">
            <t-col :span="1">
              <t-input v-model="FormData.name" class="form-item-content" type="search" placeholder="物料名称"
                style="width: 100%;" />
            </t-col>
            <t-col :span="1">
              <t-input v-model="FormData.no" class="form-item-content" placeholder="物料编码" style="width: 100%;" />
            </t-col>
            <t-col :span="1">
              <t-input v-model="FormData.no" class="form-item-content" placeholder="负责人" style="width: 100%;" />
            </t-col>
            <t-col :span="1">
              <t-input v-model="FormData.no" class="form-item-content" placeholder="采购负责人" style="width: 100%;" />
            </t-col>
            <t-col :span="1">
              <t-input v-model="FormData.no" class="form-item-content" placeholder="创建人" style="width: 100%;" />
            </t-col>
            <t-col :span="2">
              <t-date-range-picker style="width: '100%';" v-model="FormData.name" :presets="presets"
                placeholder="创建时间" />
            </t-col>
            <t-col :span="3">
              <!-- <t-button theme="primary" type="submit" :style="{ marginLeft: 'var(--td-comp-margin-s)' }">
                {{ t('components.commonTable.query') }}
              </t-button> -->
              <t-button type="reset" variant="base" theme="default"> {{ t('components.commonTable.reset') }} </t-button>
            </t-col>
          </t-row>
        </t-col>

        <t-col style="margin-top: 17px;" :span="5" class="operation-container">
          <!-- 分类 -->
          <t-button theme="success" @click="onAddClassify">
            <template #icon> <t-icon name="plus" /></template>
            添 加
          </t-button>
          <!-- 物料 - 负责人 -->
          <t-button theme="primary" @click="onAddPrincipalAddRef">
            <template #icon> <t-icon name="user-marked" /></template>
            指定负责人
          </t-button>
          <!-- 物料 - 库存 -->
          <t-button theme="success" @click="onRepertoryAddRef">
            <template #icon> <t-icon name="task-add" /></template>
            录入库存
          </t-button>

        </t-col>
      </t-row>
    </t-form>
    <!-- :bordered="true" 边框 -->
    <!-- :active-row-type="activeRowType" 选中行 -->
    <!-- :hover="hover"  鼠标效果 -->
    <!-- drag-sort="row" 需要多个api配合使用  -->
    <t-table :data="Data" :columns="COLUMNS" row-key="id" :pagination="Pagination" :loading="DataLoading"
      maxHeight="900" @page-change="rehandlePageChange" @change="rehandleChange" @filter-change="onFilterChange"
      @select-change="onSelectChange">
      <template #index="{ rowIndex }">
        <span>
          {{ rowIndex + 1 }}
        </span>
      </template>

      <!-- 物料图片 -->
      <template #cc="{ row }">
        <t-image-viewer :images="[row.cc]">
          <template #trigger="{ open }">
            <div class="tdesign-demo-image-viewer__ui-image">
              <img alt="test" :src="row.cc" class="tdesign-demo-image-viewer__ui-image--img"
                style="width: 50px;height: 50px;" @click="open" />
              <div class="tdesign-demo-image-viewer__ui-image--hover" @click="open">
                <span>
                  <BrowseIcon size="1.4em" /> 查看
                </span>
              </div>
            </div>
          </template>
        </t-image-viewer>
      </template>

      <!-- 操作 -->
      <template #op="{ row }">
        <t-space>
          <!-- 详情 -->
          <t-tooltip content="查看" placement="top" theme="light">
            <t-button variant="text" @click="onsaveExamine()">
              <t-icon name="edit" />
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

    <!-- 添加 -->
    <ClassifyAdd ref="ClassifyAddRef" />

    <!-- 负责人 -->
    <PrincipalAdd ref="PrincipalAddRef" />

    <!-- 库存 -->
    <RepertoryAdd ref="RepertoryAddRef" />

  </div>

</template>

<script lang="ts">
export default {
  name: 'AdmindeptOffice',
}
</script>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import dayjs from 'dayjs';
import { t } from '@/locales';
import { useRouter } from 'vue-router';
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps, DateRangePickerProps } from 'tdesign-vue-next';


// 物料列表
import ClassifyAdd from "./classifyAdd.vue"

// 负责人
import PrincipalAdd from "./principalAdd.vue"

// 库存
import RepertoryAdd from "./repertoryAdd.vue"

import { TABLE_SORT_FILTER } from '@/config/public';
import { COLUMNS } from "./constants"
const router = useRouter();

// tabs栏
const tabList = [
  { label: '物料列表', value: 1, },
  { label: '分类列表', value: 2, },
];

// 产品类别
const TYPE_OPTIONS_COMPANY = [
  { label: '电脑WIN10 机箱', value: 1 },
  { label: '打印机', value: 2 },
  { label: '笔记本', value: 3 },
  { label: '翼飞衣服', value: 4 },
];

// 多选
const onSelectChange = (value: any[], params: any) => {
  console.log(value, params);
};

// tab栏值
const SaveTab = ref(1);

const onTab = (e: any) => {
  console.log("tab栏", e);
  SaveTab.value = e;
  if (e === 1) {
    Data.value = [
      {
        id: "1",
        aa: "电脑WIN10 机箱",
        bb: "BG17796",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "王兰",
        ll: "5",
        kk: "张三",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "1",
        aa: "打印机",
        bb: "BG17797",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "王兰",
        ll: "5",
        kk: "张三",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "1",
        aa: "笔记本",
        bb: "BG17798",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "王兰",
        ll: "5",
        kk: "张三",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "1",
        aa: "翼飞衣服",
        bb: "BG17799",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "王兰",
        ll: "5",
        kk: "张三",
        ff: "2025-3-10 16:00:00",
      },
    ]
  } else if (e === 2) {
    Data.value = [
      {
        id: "2",
        aa: "电脑WIN10 机箱",
        bb: "BG17796",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "秦学海",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "2",
        aa: "打印机",
        bb: "BG17798",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "秦学海",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "2",
        aa: "笔记本",
        bb: "BG17798",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "秦学海",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "2",
        aa: "翼飞衣服",
        bb: "BG17798",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "件",
        ee: "秦学海",
        ff: "2025-3-10 16:00:00",
      },
    ]

  }
}

const Tab = ref(1);

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
        aa: "电脑WIN10 机箱",
        bb: "BG17796",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "王兰",
        ll: "5",
        kk: "张三",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "1",
        aa: "打印机",
        bb: "BG17797",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "王兰",
        ll: "5",
        kk: "张三",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "1",
        aa: "笔记本",
        bb: "BG17798",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "王兰",
        ll: "5",
        kk: "张三",
        ff: "2025-3-10 16:00:00",
      },
      {
        id: "1",
        aa: "翼飞衣服",
        bb: "BG17799",
        cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
        dd: "个",
        ee: "王兰",
        ll: "5",
        kk: "张三",
        ff: "2025-3-10 16:00:00",
      },
    ]
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


// 添加事件 - 类别
const ClassifyAddRef = ref(null);
const onAddClassify = () => {
  ClassifyAddRef.value.onSwticher();
};


// 负责人
const PrincipalAddRef = ref(null);
const onAddPrincipalAddRef = () => {
  PrincipalAddRef.value.onSwticher();
};

// 库存
const RepertoryAddRef = ref(null);
const onRepertoryAddRef = () => {
  RepertoryAddRef.value.onSwticher();
}

// 详情
const onsaveExamine = () => {
  ClassifyAddRef.value.onSwticher();
}

// 删除事件
const handleClickDelete = () => {
  confirmVisible.value = true;
};


</script>

<style lang="less" scoped>
.examine-page {
  height: 100%;
  background: #fff;
  background-color: var(--td-bg-color-container);
  padding: var(--td-comp-paddingTB-xxl) var(--td-comp-paddingLR-xxl);
  border-radius: var(--td-radius-medium);
}

::v-deep .t-date-picker,
.t-date-range-picker {
  width: 100%;
}
</style>