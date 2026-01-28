<template>
  <div class="main">
    <t-row>
      <t-col :span="12">
        <t-form ref="form" :data="baseInfo" :rules="baseInfoRules" label-align="right" labelWidth="120px">
          <t-form-item label="商品录入方式" name="enter_way">
            <t-radio-group v-model="baseInfo.enter_way">
              <t-radio value="excel" :checked="true">excel录入</t-radio>
              <t-radio value="manual" disabled>手工录入</t-radio>
            </t-radio-group>
          </t-form-item>
          <t-form-item label="采购名称" name="name">
            <t-input v-model="baseInfo.name" placeholder="请输入采购名称" />
          </t-form-item>
          <t-form-item label="任务描述" name="description">
            <t-textarea v-model="baseInfo.description" placeholder="请输入任务描述" :autosize="{ minRows: 3, maxRows: 5 }" />
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
import { FormProps, MessagePlugin } from 'tdesign-vue-next';
import type { BaseInfo } from '@/api/model/purchaseModel';
import { usePurchaseStore } from '@/store';

const emits = defineEmits(['prev', 'next'])

const form = ref()
const purchaseStore = usePurchaseStore()

const baseInfo: BaseInfo = reactive({
  enter_way: 'excel',
  name: purchaseStore.baseInfo?.name || '',
  description: purchaseStore.baseInfo?.description || '',
});


const baseInfoRules: FormProps['rules'] = {
  enter_way: [
    {
      required: true,
      message: '请选择商品录入方式',
      type: 'error',
    },
  ],
  name: [
    {
      required: true,
      message: '采购名称必填',
      type: 'error',
    },
  ],
};


const onReset = () => {
  baseInfo.enter_way = 'excel';
  baseInfo.name = '';
  baseInfo.description = '';
};

const onNext = async () => {
  try {
    // 验证表单
    const validateResult = await form.value?.validate();

    // 验证成功，保存到 store
    if (validateResult === true) {
      // 将表单数据保存到 store
      purchaseStore.setBaseInfo({ ...baseInfo });
      console.log('表单数据已保存到 store:', purchaseStore.baseInfo);
      emits('next');
    }
  } catch (error) {
    console.log('表单验证失败:', error);
    MessagePlugin.error('请完善表单信息');
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
