<template>
  <div class="main">
    <t-row>
      <t-col :span="12">
        <t-form ref="form" :data="uploadInfo" colon @submit="onSubmit">
          <t-space>
            <t-upload v-model="file" :auto-upload="autoUpload" :theme="file" draggable action="" />
          </t-space>
          <t-form-item>
            <t-button theme="primary" @click="onPre">上一步</t-button>
            <t-button theme="primary" type="submit">提交</t-button>
          </t-form-item>
        </t-form>
      </t-col>
    </t-row>
  </div>

</template>

<script lang="ts" setup>
import { ref } from 'vue';

const file = ref([]);
const autoUpload = ref(true);
const theme = ref('file');

const emits = defineEmits(['prev', 'next'])

const onPre = () => {
  emits('prev');
}

const onSubmit = () => {
 console.log('提交了');
}

</script>


<style lang="less" scoped>
.main {
  padding: 20px;
  background: #fff;
  width: 100%;
}
</style>