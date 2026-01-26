<template>
  <div class="main">
    <t-row>
      <t-col :span="12">
        <t-form ref="form" :data="baseInfo" colon @reset="onReset" @submit="onSubmit">
          <t-form-item label="商品录入方式">
            <t-radio-group default-value="excel">
              <t-radio value="excel" :checked="true">excel录入</t-radio>
              <t-radio value="manual" disabled>手工录入</t-radio>
            </t-radio-group>
          </t-form-item>
          <t-form-item label="名称">
            <t-input v-model="baseInfo.name" placeholder="请输入名称" @change="onChangeName" />
          </t-form-item>
          <t-form-item label="任务描述">
            <t-textarea v-model="baseInfo.description" placeholder="请输入任务描述" :autosize="{ minRows: 3, maxRows: 5 }"
              name="description" @change="onChangeDescription" />
          </t-form-item>
          <t-form-item>
            <t-button theme="primary" type="submit">下一步</t-button>
            <t-button theme="default" type="reset">重置</t-button>
          </t-form-item>
        </t-form>
      </t-col>
    </t-row>
  </div>
</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue';

const emits = defineEmits(['prev', 'next'])

const baseInfo = reactive({
  name: '',
  description: '',
});

const onChangeName = (value: any) => {
  baseInfo.name = value;
};

const onChangeDescription = (value: string) => {
  baseInfo.description = value;
};

const onReset = () => {
  baseInfo = { name: '', description: '' };
};

const onSubmit = () => {
  console.log(baseInfo);
}
</script>


<style lang="less" scoped>
.main {
  padding: 20px;
  background: #fff;
  width: 100%;
}
</style>
