<template>
  <div>
    <t-dialog v-model:visible="dictaddVisible" header="库存表单" width="600" @confirm="onConfirmDelete"
      :on-cancel="onCancel">
      <div>
        <t-form ref="form" :rules="rules" :data="FormData" label-align="right" :label-width="100">
          <t-form-item label="办公产品" name="name">
            <t-input v-model="FormData.a" disabled></t-input>
          </t-form-item>

          <t-form-item label="库存" name="password">
            <t-input-number v-model="FormData.b" />
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
  name: 'repertoryAdd',
}
</script>

<script setup lang="ts">
import { FormProps } from 'tdesign-vue-next';
import { ref } from 'vue';

// 表单数据
const Form = ref(null);
const FormData = ref({
  a: '脑WIN10 机箱',
  b: '',
  remark: ''
});

// 表单验证
const rules: FormProps['rules'] = {
  name: [{ required: true, message: '必填', type: 'error' }],
  password: [{ required: true, message: '必填', type: 'error' }],
};

const handleChange = (value: any) => {
  console.log(value);
  // FormData.value.status = value;
  console.log(FormData.value);

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

// 查询单条
const getconId = (id: number) => {
  console.log(id);
}

// 暴露
defineExpose({ onSwticher, getconId });

</script>

<style lang="less" scoped></style>