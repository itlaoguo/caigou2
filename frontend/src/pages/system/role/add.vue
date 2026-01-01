<template>
  <div>
    <t-dialog v-model:visible="DictaddVisible" header="角色添加" width="600" :footer="false" :onClose="onClose">
      <div>
        <t-form ref="Form" :rules="rules" :data="FormData" label-align="right" :label-width="100" @submit="onSubmit"
          @reset="onReset">
          
          <t-form-item v-if="FormData.parent_id == 0" label="父级节点" name="permission_name">
            <t-input v-model="tacitlyapprove"></t-input>
          </t-form-item>

          <t-form-item v-else label="父级节点" name="parent_id">
            <t-tree-select v-model="FormData.parent_id" :data="RoleItems" clearable filterable placeholder="请选择"
              :tree-props="{
                checkStrictly: true,
                keys: { value: 'id', label: 'role_name', children: 'children' }
              }" />
          </t-form-item>

          <t-form-item label="角色名称" name="role_name">
            <t-input v-model="FormData.role_name"></t-input>
          </t-form-item>

          <t-form-item label="角色标识" name="identify">
            <t-input v-model="FormData.identify"></t-input>
          </t-form-item>

          <t-form-item label="角色描述" name="description">
            <t-input v-model="FormData.description"></t-input>
          </t-form-item>

          <t-form-item label="角色权限" style="width: 100%;">
            <div style="display: flex;flex-direction: column;width: 100%;">
              <div style="display: flex;margin-bottom: 10px;margin-left: 5px;">
                <t-checkbox v-model="UnFold" @change="onunfold" style="margin-right: 20px;">展开</t-checkbox> <t-checkbox
                  v-model="AllRbac" @change="onallrbac">全选</t-checkbox>
              </div>

              <div style="border: 1px solid #ccc;width: 100%; border-radius: 5px; padding: 4px;">
                <t-tree ref="Tree" v-model:expanded="ExpandedKeys" :expand-on-click-node="false" :check-strictly="true"
                  :data="Items" activable hover transition v-model="AllChecked" checkable :keys="{
                    value: 'id', label: 'permission_name', children: 'children'
                  }" @change="onChange" />
              </div>
            </div>
          </t-form-item>

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
      </div>
    </t-dialog>
  </div>
</template>

<script lang="ts">
export default {
  name: 'systemaccountAdd',
}
</script>

<script setup lang="ts">
import { t } from '@/locales';
import { FormProps, SubmitContext } from 'tdesign-vue-next';
import { ref, onMounted, defineProps } from 'vue';
import { MenuList } from "@/api/system/menu";
import { RoleList, RoleListDetail, RoleListAlter, RoleListAdd } from "@/api/system/role";
import { FormData } from "./constants"
import { TransformActionsToChildren } from "@/config/public";


const datafun = defineProps({
  fetchData: {
    type: Function,
  }
})

// 默认值
const tacitlyapprove = ref('无父级')

// 表单数据
const Form = ref(null);
const FormData = ref<FormData>({
  role_name: '',
  identify: '',
  description: '',
  parent_id: 0,
  permissions: []
});

// 表单验证
const rules: FormProps['rules'] = {
  parent_id: [{ required: true, message: '必填', type: 'warning' }],
  role_name: [{ required: true, message: '必填', type: 'warning' }],
  identify: [{ required: true, message: '必填', type: 'warning' }],
  // description: [{ required: true, message: '必填', type: 'warning' }],
};

onMounted(() => {
  console.log("执行");
  getTree();
})

// 父级
const RoleItems = ref([]);
const getRoleList = async () => {
  const res = await RoleList({});
  console.log("父级==>", res);
  if (res.code == 10000) {
    RoleItems.value = res.data;
  }
};

// 重置按钮
const form = ref(null);
const onClose = () => {
  Form.value.reset();
  if (FormData.value.id) { 
    FormData.value.id = null;
    delete FormData.value.id;
  }
  AllChecked.value = [];
  AllRbac.value = false;
  UnFold.value = false;
  console.log("清空表单===》",FormData.value);
};

const onReset = () => {
  onClose();
  onSwticher();
};


// 查询单挑
const getitemId = async (id: number) => {
  const res = await RoleListDetail(id);
  console.log("单条==>", res);
  if (res.code == 10000) {
    FormData.value = res.data;

    AllChecked.value = res.data.permissions;
  }
}

// 提交
const onSubmit = async (ctx: SubmitContext) => {
  console.log("执行");

  if (ctx.validateResult === true) {
    console.log(ExpandedKeys.value);

    FormData.value.permissions = AllChecked.value
    console.log(FormData.value);
    let res = null
    if (FormData.value.id) {
      console.log("修改");
      res = await RoleListAlter(FormData.value, FormData.value.id);
      console.log("修改", res);
    } else {

      console.log("添加");
      res = await RoleListAdd(FormData.value);
      console.log("添加", res);
    }

    if (res.code == 10000) {
      onSwticher();
      onReset();
      getTree();
      datafun.fetchData();
    }
  }
};

// 角色权限
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

// 获取树形结构
const getTree = async () => {
  const res = await MenuList({});
  Items.value = TransformActionsToChildren(res.data);
  console.log("item", Items.value);
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

const onChange = (checked: any, context: { node: any; }) => {
  console.info('onChange checked:', checked, 'context:', context);
  const { node } = context;
  console.info(node.value, 'onChange context.node.checked:', node.checked);
};

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

//添加开关
const DictaddVisible = ref(false);

// 打开按钮
const onSwticher = () => {
  DictaddVisible.value = !DictaddVisible.value;
}

// 暴露
defineExpose({ onSwticher, getitemId, getRoleList, FormData });

</script>

<style lang="less" scoped></style>