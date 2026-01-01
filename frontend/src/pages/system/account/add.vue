<template>
  <div>
    <t-dialog v-model:visible="DictaddVisible" header="账号添加" width="600" :footer="false" :onClose="onClose">
      <div>
        <t-form ref="form" :rules="rules" :data="FormData" label-align="right" :label-width="100" @submit="onSubmit"
          @reset="onReset">
          <t-form-item label="用户名" name="username">
            <t-input v-model="FormData.username"></t-input>
          </t-form-item>

          <t-form-item label="邮箱号码" name="email">
            <t-input v-model="FormData.email"></t-input>
          </t-form-item>

          <t-form-item v-if="!FormData.id" label="用户密码" name="password">
            <t-input v-model="FormData.password"></t-input>
          </t-form-item>

          <t-form-item label="状态" name="status">
            <t-radio-group :value="FormData.status" @change="onhandleChange">
              <t-radio :value="1">开启</t-radio>
              <t-radio :value="2">关闭</t-radio>
            </t-radio-group>
          </t-form-item>

          <t-form-item label="角色权限" style="width: 100%;">
            <div style="display: flex;flex-direction: column;width: 100%;">
              <div style="display: flex;margin-bottom: 10px;margin-left: 5px;">
                <t-checkbox v-model="UnFold" @change="onunfold" style="margin-right: 20px;">展开</t-checkbox> <t-checkbox
                  v-model="AllRbac" @change="onallrbac">全选</t-checkbox>
              </div>

              <div style="border: 1px solid #ccc;width: 100%; border-radius: 5px; padding: 4px;">
                <t-tree ref="Tree" v-model:expanded="ExpandedKeys" :expand-on-click-node="false" :data="Items" activable
                  hover transition v-model="AllChecked" checkable :keys="{
                    value: 'id', label: 'role_name', children: 'children'
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
import { RoleList } from "@/api/system/role"
import { AccountAdd, AccountDetail, AccountAlter } from "@/api/system/account";
import { FormData } from "./constants"

const datafun = defineProps({
  fetchData: {
    type: Function,
  }
})


// 表单数据
const FormData = ref<FormData>({
  username: '',
  email: '',
  password: "",
  status: 1,
  roles: []
});


// 重置按钮
const form = ref(null);
const onClose = () => {
  form.value.reset();
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

const onhandleChange = (value: any) => {
  console.log(value);
  FormData.value.status = value;
  console.log(FormData.value);
};



// 查询单挑
const getitemId = async (id: number) => {
  const res = await AccountDetail(id);
  console.log("单条==>", res);
  if (res.code == 10000) {
    FormData.value = res.data;
    AllChecked.value = res.data.roles;
  }
}

const onSubmit = async (ctx: SubmitContext) => {
  console.log("执行",FormData.value);

  if (ctx.validateResult === true) {
    console.log(AllChecked.value);

    FormData.value.roles = AllChecked.value
    console.log(FormData.value);
    let res = null
    if (FormData.value.id) {
      console.log("修改");
      res = await AccountAlter(FormData.value, FormData.value.id);
      console.log("修改", res);
    } else {

      console.log("添加");
      res = await AccountAdd(FormData.value);
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

// 角色
const Tree = ref(null);
const AllChecked = ref([]);

// 获取树形结构
const getTree = async () => {
  const res = await RoleList({});
  Items.value = res.data;
  console.log("item", Items.value);
}

onMounted(() => {
  console.log("执行");
  getTree();
})


const Items = ref([

]);

const onChange = (checked: any, context: { node: any; }) => {
  console.info('onChange checked:', checked, 'context:', context);
  const { node } = context;
  console.info(node.value, 'onChange context.node.checked:', node.checked);

  console.log("ExpandedKeys", AllChecked.value);

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

// 表单验证
const rules: FormProps['rules'] = {
  // 注意：原代码中字段名 "usernam" 疑似拼写错误，应改为 "username"
  username: [{ required: true, message: '必填', type: 'error' }],
  email: [
    {
      required: true,
      message: '必填',
      type: 'error'
    },
    {
      validator: (value) => {
        // 邮箱正则表达式：允许字母、数字、点、下划线、百分号、加号、短横线，域名需包含至少两位后缀
        const emailPattern = /^[a-zA-Z0-9_.%+-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,}$/;
        return emailPattern.test(value);
      },
      message: '请输入正确的邮箱格式（如：user@qq.com）',
      type: 'error'
    }
  ],
  status: [{ required: true, message: '必填', type: 'error' }],
  password: [
    {
      required: true,
      message: '必填',
      type: 'error'
    },
    {
      validator: (value) => {
        // 密码正则：至少6位，包含大小写字母和数字
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/;
        return passwordPattern.test(value);
      },
      message: '密码需包含大小写字母和数字，且至少6位',
      type: 'error'
    }
  ],
};


//添加开关
const DictaddVisible = ref(false);


// 打开按钮
const onSwticher = () => {
  DictaddVisible.value = !DictaddVisible.value;
}


// 暴露
defineExpose({ onSwticher, getitemId });

</script>

<style lang="less" scoped></style>