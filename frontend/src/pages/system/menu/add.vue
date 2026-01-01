<template>
  <div>
    <t-dialog v-model:visible="DictaddVisible" header="菜单添加" width="600" :footer="false" :onClose="onClose">
      <div>
        <t-form ref="form" :rules="FORM_RULES" :data="FormData" label-align="right" :label-width="100"
          @submit="onSubmit" @reset="onReset">
          <t-form-item v-if="FormData.parent_id == 0" label="父级节点" name="permission_name">
            <t-input v-model="tacitlyapprove"></t-input>
          </t-form-item>
          <t-form-item v-else label="父级节点" name="parent_id">
            <t-tree-select v-model="FormData.parent_id" :data="Items" filterable placeholder="请选择"
              :tree-props="{
                checkStrictly: true,
                keys: { value: 'id', label: 'permission_name', children: 'children' }
              }" @popup-visible-change="onVisibleChange">
              </t-tree-select>
          </t-form-item>
          <t-form-item label="菜单类型" name="type">
            <t-radio-group :value="FormData.type" @change="handleChange">
              <t-radio :value="1">目录</t-radio>
              <t-radio :value="2">页面</t-radio>
              <t-radio :value="3">按钮</t-radio>
            </t-radio-group>
          </t-form-item>
          <t-form-item label="菜单名称" name="permission_name"
            v-if="FormData.type == 3 || FormData.type == 2 || FormData.type == 1">
            <t-input v-model="FormData.permission_name"></t-input>
          </t-form-item>
          <t-form-item label="菜单图标" name="icon" v-if="FormData.type == 1 || FormData.type == 2">
            <t-input v-model="FormData.icon"></t-input>
          </t-form-item>
          <t-form-item label="组件名称" name="route" v-if="FormData.type == 2 || FormData.type == 1">
            <t-input v-model="FormData.route"></t-input>
          </t-form-item>
          <t-form-item label="组件路径" name="component" v-if="FormData.type == 2">
            <t-input v-model="FormData.component"></t-input>
          </t-form-item>
          <t-form-item label="权限字符" name="permission_mark" v-if="FormData.type == 3">
            <t-input v-model="FormData.permission_mark"></t-input>
          </t-form-item>
          <t-form-item label="显示排序" name="sort" v-if="FormData.type == 1 || FormData.type == 3 || FormData.type == 2">
            <t-input-number v-model="FormData.sort" />
          </t-form-item>
          <t-form-item label="页面状态" name="hidden" v-if="FormData.type == 2 || FormData.type == 1">
            <t-radio-group :value="FormData.hidden" @change="onhandleChange">
              <t-radio :value="2">开启</t-radio>
              <t-radio :value="1">关闭</t-radio>
            </t-radio-group>
          </t-form-item>
          <!-- <t-form-item label="备注" name="remark">
            <t-textarea v-model="FormData.remark" placeholder="请输入内容" />
          </t-form-item> -->


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
  name: 'systemAdd',
}
</script>

<script setup lang="ts">
import { t } from '@/locales';
import { FormProps, FormRule, SubmitContext, TreeSelectProps } from 'tdesign-vue-next';
import { ref, onMounted, defineProps } from 'vue';
import { MenuListAdd, MenuListDetail, MenuListAlter, MenuList } from "@/api/system/menu";
import { TransformActionsToChildren } from "@/config/public";
import { FormData } from "./constants"

const datafun = defineProps({
  fetchData: {
    type: Function,
  }
})

// 默认值
const tacitlyapprove = ref('无父级')

// 表单数据
const FormData = ref<FormData>({
  // id: null,
  // 菜单名称
  permission_name: '',
  route: "",
  // 菜单图标
  icon: "",
  // 权限标识
  permission_mark: "",
  // 组件路径
  component: "",
  // 组件名称
  module: "",
  // 排序
  sort: null,
  // 父级id
  parent_id: 0,
  // 菜单类型
  type: 1,
  // 是否显示
  hidden: 2,
});

// 表单验证
const FORM_RULES: Record<string, FormRule[]> = {
  permission_name: [{ required: true, message: '请输入', type: 'warning' }],
  type: [{ required: true, message: '请选择', type: 'warning' }],
  parent_id: [{ required: true, message: '请输入', type: 'warning' }],
  // icon: [{ required: true, message: '请输入', type: 'warning' }],
  route: [{ required: true, message: '请输入', type: 'warning' }],
  component: [{ required: true, message: '请输入', type: 'warning' }],
  permission_mark: [{ required: true, message: '请输入', type: 'warning' }],
  sort: [{ required: true, message: '请输入', type: 'warning' }],
  hidden: [{ required: true, message: '请输入', type: 'warning' }],
};

const handleChange = (value: any) => {
  console.log(value);
  FormData.value.type = value;
  console.log(FormData.value);

  if (value == 3) {
    FormData.value.hidden == 1;
  }
};

const onhandleChange = (value: any) => {
  console.log(value);
  FormData.value.hidden = value;
  console.log(FormData.value);
};


onMounted(() => {
  console.log("执行");
  getTree();
})



const Items = ref([
]);

const onVisibleChange: TreeSelectProps['onPopupVisibleChange'] = (visible, context) => {
  console.log(visible, context);
};

// 获取树形结构
const getTree = async () => {
  const res = await MenuList({});
  Items.value = TransformActionsToChildren(res.data);
  console.log("item", Items.value);
}


//添加开关
const DictaddVisible = ref(false);

// 重置按钮
const form = ref(null);

const onClose = () => {
  form.value.reset();
  if (FormData.value.id) { 
    FormData.value.id = null;
    delete FormData.value.id;
  }
  FormData.value.hidden = 1;
  FormData.value.hidden = 2;
};

const onReset = () => {
  onClose();
  onSwticher();
};



// 查询单挑
const getitemId = async (id: number) => {
  const res = await MenuListDetail(id);
  console.log("单条==>", res);
  if (res.code == 10000) {
    FormData.value = res.data;
  }
}

// 打开按钮
const onSwticher = () => {
  DictaddVisible.value = !DictaddVisible.value;
}


// 确定
const onSubmit = async (ctx: SubmitContext) => {
  console.log("执行");
  console.log(FormData.value);


  if (ctx.validateResult === true) {
    let res = null
    if (FormData.value.id) {
      console.log("修改");
      res = await MenuListAlter(FormData.value, FormData.value.id);
      console.log("添加", res);
    } else {
      console.log("添加");
      res = await MenuListAdd(FormData.value);
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


// 暴露
defineExpose({ onSwticher, getitemId, FormData });

</script>

<style lang="less" scoped></style>