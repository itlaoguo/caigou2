<template>
  <t-form ref="form" class="base-form" :data="formData" :rules="FORM_RULES" label-align="top" :label-width="100"
    @reset="onReset" @submit="onSubmit">
    <div class="form-basic-container">
      <div class="form-basic-item">

        <div class="form-basic-container-title">
          <p />{{ '添加公告' }}
        </div>
        <!-- 表单内容 -->

        <!-- 合同名称,合同类型 -->
        <t-row class="row-gap" :gutter="[32, 24]">
          <t-col :span="12">
            <t-form-item label="标题" name="name">
              <t-input v-model="formData.name" :style="{ width: '322px' }" placeholder="请输入内容" />
            </t-form-item>
          </t-col>
          <t-col :span="6">
            <t-form-item label="是否发布" name="type">
              <t-radio-group :default-value="formData.name" @change="onChange">
                <t-radio value="0">显示</t-radio>
                <t-radio value="1">不显示</t-radio>
              </t-radio-group>
            </t-form-item>
          </t-col>
          <t-col :span="6">
            <t-form-item label="首页显示" name="type">
              <t-radio-group :default-value="formData.name">
                <t-radio value="0">显示</t-radio>
                <t-radio value="1">不显示</t-radio>
              </t-radio-group>
            </t-form-item>
          </t-col>
        </t-row>

        <t-form-item label="选择部门" name="comment">
          <div
            style="display: flex;flex-direction: column;width: 100%;border: 1px solid #ccc;border-radius: 5px; padding: 4px;">
            <div style="display: flex;margin-bottom: 10px;margin-left: 5px;">
              <t-checkbox v-model="UnFold" @change="onunfold" style="margin-right: 20px;">展开</t-checkbox> <t-checkbox
                v-model="AllRbac" @change="onallrbac">全选</t-checkbox>
            </div>

            <div style="width: 100%; ">
              <t-tree ref="Tree" v-model:expanded="ExpandedKeys" :expand-on-click-node="false" :data="Items" activable
                hover transition v-model="AllChecked" checkable :keys="{
                  value: 'id', label: 'name', children: 'children'
                }" @change="onTreeChange" />
            </div>
          </div>
        </t-form-item>

        <t-form-item label="公告内容" name="comment">
          <EditorComponent class="WangEditor">
          </EditorComponent>
        </t-form-item>
      </div>
    </div>

    <div class="form-submit-container">
      <div class="form-submit-sub">
        <div class="form-submit-left">
          <t-button theme="primary" class="form-submit-confirm" type="submit">
            {{ t('pages.formBase.confirm') }}
          </t-button>
          <t-button type="reset" class="form-submit-cancel" theme="default" variant="base">
            {{ t('pages.formBase.cancel') }}
          </t-button>
        </div>
      </div>
    </div>
  </t-form>
</template>

<script lang="ts">
export default {
  name: 'NotificAdd',
};
</script>

<script setup lang="ts">
import type { FormRule, SubmitContext, UploadFailContext, UploadFile } from 'tdesign-vue-next';
import { MessagePlugin } from 'tdesign-vue-next';
import { ref, defineProps } from 'vue';

import { t } from '@/locales';

// 富文本
import EditorComponent from "./EditorComponent.vue";

const formData = ref({
  files: [],
  name: '',
  comment: '',
});

const FORM_RULES: Record<string, FormRule[]> = {
  name: [{ required: true, message: '请输入合同名称', type: 'error' }],
  type: [{ required: true, message: '请选择合同类型', type: 'error' }],
  payment: [{ required: true, message: '请选择合同收付类型', type: 'error' }],
  amount: [{ required: true, message: '请输入合同金额', type: 'error' }],
  partyA: [{ required: true, message: '请选择甲方', type: 'error' }],
  partyB: [{ required: true, message: '请选择乙方', type: 'error' }],
  signDate: [{ required: true, message: '请选择日期', type: 'error' }],
  startDate: [{ required: true, message: '请选择日期', type: 'error' }],
  endDate: [{ required: true, message: '请选择日期', type: 'error' }],
};

const datafun = defineProps({
  contractId: {
    type: String,
    default: ''
  },
  onaddswtich: {
    type: Function,
  }
})

// 查询单条
const getsomeId = (id: string) => {
  console.log(`查询单条==>${id}`);

}

// 通告发布
const onTreeChange = (e: any) => {
  formData.value.name = e;
  console.log(`通告发布==>${formData.value}`);
}

// 树形数据
// 树形数据
const Tree = ref(null);
const AllChecked = ref([]);

interface TreeNode {
  id: string | number;
  name: string;
  children?: TreeNode[];
  // 其他可选字段需声明类型
  status?: string;
  no?: string;
}

const Items = ref<TreeNode[]>([
  {
    id: "1",
    name: '董事长-总经办',
    no: '1',
    status: '0',
    children: [
      {
        id: "11",
        name: '会计出纳-财务部',
        no: '1',
        status: '0',

      },
      {
        id: "12",
        name: '副总经理(程总) - 运营中心(程总)',
        no: '2',
        status: '0',
        children: [
          {
            id: "22",
            name: '教学 - 教培部(liliting)',
            no: '2',
            status: '0',

          },
          {
            id: "23",
            name: '校研 - 教培部',
            no: '2',
            status: '0',
          },
        ]
      },
      {
        id: "13",
        name: '总经理 - 管理中心(徐总)',
        no: '3',
        status: '0',
        children: [
          {
            id: "24",
            name: '仓库主管 - 仓储部(XiaoMeiJiao)',
            no: '2',

            status: '0',
          },
          {
            id: "25",
            name: '采购主管 - 采购部(DengLiHui)',
            no: '2',
            status: '0',
          },
          {
            id: "26",
            name: '生产部主管 - 生管部',
            no: '2',
            status: '0',
          },
          {
            id: "27",
            name: '生产部主管 - 生产部',
            no: '2',
            status: '0',
          },
        ]
      },
      {
        id: "14",
        name: '财务主管 - 财务部',
        no: '3',
        status: '0',
      },
      {
        id: "15",
        name: '成本会计 - 财务部',
        no: '3',
        status: '0',
      },
      {
        id: "16",
        name: '内控专员 - 财务部',
        no: '3',
        status: '0',
      },

    ]
  },
]);

// const onChange = (checked: any, context: { node: any; }) => {
//   console.info('onChange checked:', checked, 'context:', context);
//   const { node } = context;
//   console.info(node.value, 'onChange context.node.checked:', node.checked);
// };
const onChange = () => {
  console.log("onChange");
}

// 全选
const AllRbac = ref(false);
const onallrbac = () => {
  console.log("全选", AllRbac.value);
  AllChecked.value = AllRbac.value ? getAllIds(Items.value) : [];
}

const getAllIds = (nodes: any[]) => {
  let ids: any[] = [];
  nodes.forEach(node => {
    ids.push(node.id);
    if (node.children?.length) ids.push(...getAllIds(node.children));
  });
  return ids;
};

// 展开
const UnFold = ref(false);
const ExpandedKeys = ref([]);
const onunfold = () => {
  console.log("全选", UnFold.value);
  if (UnFold.value) {
    // 展开所有节点
    ExpandedKeys.value = getAllIds(Items.value);
  } else {
    // 折叠所有节点
    ExpandedKeys.value = [];
  }
}

// 富文本
const editValue = ref("")

//当编辑器的内容更新时，获取该值
const getRich = function (value: any) {
  console.log(value);
}


const onReset = () => {
  datafun.onaddswtich();
  MessagePlugin.warning('取消新建');
};

const onSubmit = (ctx: SubmitContext) => {
  if (ctx.validateResult === true) {
    MessagePlugin.success('新建成功');
  }
};



</script>

<style lang="less" scoped>
@import "@/style/public/add.less";
</style>
