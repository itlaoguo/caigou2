<template>
  <div class="main">
    <t-row>
      <t-col :span="12">
        <t-form ref="form" :data="baseInfo" :rules="baseInfoRules" label-align="right" labelWidth="120px">
          <t-form-item label="商品录入方式" name="inputMethod">
            <t-radio-group v-model="baseInfo.inputMethod">
              <t-radio value="excel" :checked="true">excel录入</t-radio>
              <t-radio value="manual" disabled>手工录入</t-radio>
            </t-radio-group>
          </t-form-item>
          <t-form-item label="名称" name="name">
            <t-input v-model="baseInfo.name" placeholder="请输入名称" />
          </t-form-item>
          <t-form-item label="任务描述">
            <t-textarea v-model="baseInfo.description" placeholder="请输入任务描述" :autosize="{ minRows: 3, maxRows: 5 }"
              name="description" />
          </t-form-item>
          <t-form-item>
            <t-button theme="primary" @click="onNext">下一步</t-button>
          </t-form-item>
        </t-form>
      </t-col>
    </t-row>
  </div>
</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue';
import { FormProps } from 'tdesign-vue-next';

const emits = defineEmits(['prev', 'next'])

const form = ref()

const baseInfo: FormProps['data'] = reactive({
  inputMethod: 'excel',
  name: '',
  description: '',
});


const baseInfoRules: FormProps['rules'] = {
  inputMethod: [
    {
      required: true,
      message: '请选择商品录入方式',
      type: 'error',
    },
  ],
  name: [
    {
      required: true,
      message: '名称必填',
      type: 'error',
    },
  ],
};


const onReset = () => {
  baseInfo.value = { inputMethod: 'excel', name: '', description: '' };
};

const onNext = async () => {
  try {
    await form.value?.validate();
    console.log(baseInfo.value);
    emits('next');
  } catch (error) {
    console.log('表单验证失败:', error);
  }
}
</script>


<style lang="less" scoped>
.main {
  padding: 20px;
  background: #fff;
  width: 100%;
}
</style>
