<template>
  <div>
    <t-dialog v-model:visible="DictaddVisible" header="权限添加" width="600" @confirm="onConfirmDelete"
      :on-cancel="onCancel">
      <div>
        <t-form ref="form" :rules="rules" :data="FormData" label-align="right" :label-width="100">
          <t-form-item label="岗位名称" name="name">
            <t-input v-model="FormData.name"></t-input>
          </t-form-item>

          <t-form-item label="菜单权限" name="password" style="width: 100%;">
            <div style="display: flex;flex-direction: column;width: 100%;">
              <div style="display: flex;margin-bottom: 10px;margin-left: 5px;">
                <t-checkbox v-model="UnFold" @change="onunfold" style="margin-right: 20px;">展开</t-checkbox> <t-checkbox
                  v-model="AllRbac" @change="onallrbac">全选</t-checkbox>
              </div>

              <div style="border: 1px solid #ccc;width: 100%; border-radius: 5px; padding: 4px;">
                <t-tree ref="Tree" v-model:expanded="ExpandedKeys" :expand-on-click-node="false" :data="Items" activable
                  hover transition v-model="AllChecked" checkable :keys="{
                    value: 'id', label: 'name', children: 'children'
                  }" @change="onChange" />
              </div>
            </div>
          </t-form-item>
          <t-form-item label="岗位状态" name="status">
            <t-radio-group :default-value="FormData.status">
              <t-radio value="0">开启</t-radio>
              <t-radio value="1">关闭</t-radio>
            </t-radio-group>
          </t-form-item>
          <t-form-item label="备注" name="remark">
            <t-textarea v-model="FormData.remark" placeholder="请输入内容" />
          </t-form-item>
        </t-form>
      </div>
    </t-dialog>
  </div>
</template>

<script lang="ts">
export default {
  name: 'Stationrbacpage',
}
</script>

<script setup lang="ts">
import { values } from 'lodash';
import { FormProps } from 'tdesign-vue-next';
import { ref } from 'vue';

// 表单数据
const Form = ref(null);
const FormData = ref({
  name: '',
  password: '',
  status: '0',
  remark: ''
});

// 表单验证
const rules: FormProps['rules'] = {
  name: [{ required: true, message: '必填', type: 'error' }],
  password: [{ required: true, message: '必填', type: 'error' }],
};

const handleChange = (value: any) => {
  console.log(value);
  FormData.value.status = value;
  console.log(FormData.value);

};

// 树形数据
const Tree = ref(null);
const AllChecked = ref([]);

const Items = ref([
  {
    id: "1",
    name: 'DEMO页面',
    no: '1',
    icon: 'setting',
    status: 0,
    contractType: '',
    paymentType: '/demo/one',
    amount: 'name',
    children: [
      {
        id: "11",
        name: '表格页面',
        no: '1',
        icon: 'setting',
        status: 0,
        contractType: '',
        paymentType: '/demo/one/index',
        amount: 'one',
      },
      {
        id: "12",
        name: '数据字典',
        no: '2',
        icon: '',
        status: 0,
        contractType: '',
        paymentType: '/dict/index',
        amount: 'dict',
      },
    ]
  },
  {
    id: "2",
    name: '系统管理',
    no: '1',
    icon: 'setting',
    status: 0,
    contractType: '',
    paymentType: '',
    amount: 'name',
    children: [
      {
        id: "21",
        name: '页面管理',
        no: '1',
        icon: '',
        status: 0,
        contractType: '',
        paymentType: '/system/menu/index.vue',
        amount: 'one',
      },
      {
        id: "22",
        name: '部门管理',
        no: '2',
        icon: '',
        status: 0,
        contractType: '',
        paymentType: '/system/station/index.vue',
        amount: 'dict',
      },
      {
        id: "23",
        name: '账号管理',
        no: '3',
        icon: '',
        status: 0,
        contractType: '',
        paymentType: '/system/account/index.vue',
        amount: 'dict',
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
    ExpandedKeys.value = getAllIds(Items.value); // 展开所有节点
  } else {
    ExpandedKeys.value = []; // 折叠所有节点
  }
}

// 添加按钮
const onConfirm = () => {
  Form.value.validate().then((res: any) => {
    console.log(res);
  });
}

//添加开关
const DictaddVisible = ref(false);

// 重置按钮
const onReset = () => {
  Form.value.reset();
};

// 打开按钮
const onSwticher = () => {
  DictaddVisible.value = !DictaddVisible.value;
}

// 删除-确定
const onConfirmDelete = () => {
  onReset();
  onSwticher();
};

// 删除-取消
const onCancel = () => {
  console.log('表单关闭执行');
};

// 查询单条
const getconId = (id: number) => {
  console.log(id);
}

// 暴露
defineExpose({ onSwticher, getconId });

</script>

<style lang="less" scoped></style>