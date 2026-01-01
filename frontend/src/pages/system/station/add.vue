<template>
  <div>
    <t-dialog v-model:visible="dictaddVisible" header="岗位添加" width="600">
      <div>
        <t-form ref="form" :rules="rules" :data="FormData" label-align="right" :label-width="100" @submit="onSubmit"
          @reset="onReset">
          <t-form-item label="父级节点" name="name">
            <t-tree-select v-model="FormData.parent_id" :data="Items" clearable filterable placeholder="请选择"
              :tree-props="{
                checkStrictly: true,
                keys: { value: 'id', label: 'department_name', children: 'children' }
              }" @popup-visible-change="onVisibleChange" />
          </t-form-item>

          <t-form-item label="岗位名称" name="name">
            <t-input v-model="FormData.department_name"></t-input>
          </t-form-item>

          <t-form-item label="显示排序" name="password">
            <t-input-number v-model="FormData.sort" />
          </t-form-item>
          <t-form-item label="岗位状态" name="status">
            <t-radio-group :default-value="FormData.status">
              <t-radio :value="1">开启</t-radio>
              <t-radio :value="2">关闭</t-radio>
            </t-radio-group>
          </t-form-item>
        </t-form>

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
      </div>
    </t-dialog>
  </div>
</template>

<script lang="ts">
export default {
  name: 'systemstationAdd',
}
</script>

<script setup lang="ts">
import { FormProps, SubmitContext, TreeSelectProps } from 'tdesign-vue-next';
import { StationList, StationAlter, StationAdd, StationDetail } from "@/api/system/station";

import { ref, onMounted, defineProps } from 'vue';
import { t } from '@/locales';

const datafun = defineProps({
  fetchData: {
    type: Function,
  }
})


onMounted(() => {
  console.log("执行");
  getTree();
})


// 表单数据
const Form = ref(null);
const FormData = ref({
  department_name: '',
  sort: '',
  status: 1,
  parent_id: 0
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

// 树形下拉框
interface TreeNode {
  id: string | number;
  name: string;
  children?: TreeNode[];
  // 其他可选字段需声明类型
  status?: string;
  no?: string;
}

const Items = ref([
  // {
  //   id: "1",
  //   name: '董事长-总经办',
  //   no: '1',
  //   status: '0',
  //   children: [
  //     {
  //       id: "11",
  //       name: '会计出纳-财务部',
  //       no: '1',
  //       status: '0',

  //     },
  //     {
  //       id: "12",
  //       name: '副总经理(程总) - 运营中心(程总)',
  //       no: '2',
  //       status: '0',

  //       children: [
  //         {
  //           id: "22",
  //           name: '教学 - 教培部(liliting)',
  //           no: '2',
  //           status: '0',

  //         },
  //         {
  //           id: "23",
  //           name: '校研 - 教培部',
  //           no: '2',
  //           status: '0',

  //         },
  //       ]
  //     },
  //     {
  //       id: "13",
  //       name: '总经理 - 管理中心(徐总)',
  //       no: '3',
  //       status: '0',
  //       children: [
  //         {
  //           id: "24",
  //           name: '仓库主管 - 仓储部(XiaoMeiJiao)',
  //           no: '2',

  //           status: '0',
  //         },
  //         {
  //           id: "25",
  //           name: '采购主管 - 采购部(DengLiHui)',
  //           no: '2',
  //           status: '0',
  //         },
  //         {
  //           id: "26",
  //           name: '生产部主管 - 生管部',
  //           no: '2',
  //           status: '0',
  //         },
  //         {
  //           id: "27",
  //           name: '生产部主管 - 生产部',
  //           no: '2',
  //           status: '0',
  //         },
  //       ]
  //     },
  //     {
  //       id: "14",
  //       name: '财务主管 - 财务部',
  //       no: '3',
  //       status: '0',
  //     },
  //     {
  //       id: "15",
  //       name: '成本会计 - 财务部',
  //       no: '3',
  //       status: '0',
  //     },
  //     {
  //       id: "16",
  //       name: '内控专员 - 财务部',
  //       no: '3',
  //       status: '0',
  //     },

  //   ]
  // },
]);

// 获取树形结构
const getTree = async () => {
  const res = await StationList({});
  Items.value = res.data;
  console.log("item", Items.value);
}

const onVisibleChange: TreeSelectProps['onPopupVisibleChange'] = (visible, context) => {
  console.log(visible, context);
};

// 添加按钮
const onConfirm = () => {
  Form.value.validate().then((res: any) => {
    console.log(res);
  });
}

//添加开关
const dictaddVisible = ref(false);

// 重置按钮
const onReset = () => {
  Form.value.reset();
};

// 打开按钮
const onSwticher = () => {
  dictaddVisible.value = !dictaddVisible.value;
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

// 查询单挑
const getitemId = async (id: number) => {
  const res = await StationDetail(id);
  console.log("单条==>", res);
  if (res.code == 10000) {
    FormData.value = res.data;
  }
}

const onSubmit = async (ctx: SubmitContext) => {
  console.log("执行");

  if (ctx.validateResult === true) {
    console.log(FormData.value);
    let res = null
    // if (FormData.value.id) {
    //   console.log("修改");
    //   res = await StationAlter(FormData.value, FormData.value.id);
    //   console.log("添加", res);
    // } else {
    //   console.log("添加");
    //   res = await StationAdd(FormData.value);
    //   console.log("添加", res);
    // }

    // if (res.code == 10000) {
    //   onSwticher();
    //   onReset();
    //   getTree();
    //   datafun.fetchData();
    // }
  }
};

// 暴露
defineExpose({ onSwticher, getitemId });

</script>

<style lang="less" scoped></style>