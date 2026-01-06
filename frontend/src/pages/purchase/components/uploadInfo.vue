<template>
  <div class="main">
    <t-space>
      <t-upload v-model="file" :auto-upload="autoUpload" :theme="file" draggable action="" />
    </t-space>
  </div>

</template>

<script lang="ts" setup>
import { ref } from 'vue';

const file = ref([]);
const autoUpload = ref(true);
const theme = ref('file');

</script>


<style lang="less" scoped>
.main {
  padding: 20px;
  background: #fff;
  min-height: calc(100vh - 130px);
}
</style>