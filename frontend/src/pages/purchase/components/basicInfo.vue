<template>
  <div class="main">
    <t-row>
      <t-col :span="24">
        <t-form ref="form" :data="baseInfo" colon @reset="onReset" @submit="onSubmit">
          <t-form-item label="商品录入方式">
            <t-radio-group default-value="1" @change="onChange">
              <t-radio value="1">excel录入</t-radio>
              <t-radio value="4" disabled>手工录入</t-radio>
            </t-radio-group>
          </t-form-item>
          <t-form-item label="名称">
            <t-input v-model="input" placeholder="请输入名称" />
          </t-form-item>
          <t-form-item label="任务描述">
            <t-textarea v-model="value" placeholder="请输入任务描述" autosize name="description" @change="onChange" />
          </t-form-item>


        </t-form>
      </t-col>
    </t-row>
    <t-row>
      <t-col>

      </t-col>
    </t-row>
  </div>

</template>

<script lang="ts" setup>
import { ref } from 'vue';

const emits = defineEmits(['prev', 'next'])

const file = ref([]);
const autoUpload = ref(true);
const theme = ref('file');


</script>


<style lang="less" scoped>
.main {
  padding: 20px;
  background: #fff;
}
</style>