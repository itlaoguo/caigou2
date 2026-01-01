<template>
  <div>
    <t-dialog v-model:visible="DictaddVisible" header="字典添加" width="600" @confirm="onConfirmDelete"
      :on-cancel="onCancel">
      <div>
        <t-form ref="form" :rules="rules" :data="FormData" label-align="right" :label-width="100">
          <t-form-item label="字典名称" name="name">
            <t-input v-model="FormData.name"></t-input>
          </t-form-item>
          <t-form-item label="字典类型" name="password">
            <t-input v-model="FormData.password"></t-input>
          </t-form-item>
          <t-form-item label="状态" name="status">
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
  name: 'dictAdd',
}
</script>

<script setup lang="ts">
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