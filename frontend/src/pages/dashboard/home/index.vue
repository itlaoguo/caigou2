<template>
  <div class="lessee">
    <button v-for="(btn, index) in Data" :key="btn.id" @click="CurrentIndex = index"
      :class="{ active: CurrentIndex === index }">{{ btn.name }}</button>
  </div>
</template>

<script lang="ts">
export default {
  name: 'Dashboardhome',
}
</script>

<script setup lang="ts">
import { ref } from 'vue';
const CurrentIndex = ref(-1)
const Data = ref([
  { id: 1, name: '翼飞智能' },
  { id: 2, name: '翼飞教育' },
  { id: 3, name: '翼飞传媒' },
])

</script>

<style lang="less" scoped>
.lessee {
  // 水平居中布局
  display: flex;
  justify-content: center;
  gap: 20px;
  padding: 20px;

  button {
    // 基础按钮样式
    padding: 16px 34px;
    border: none; // 清除默认边框
    // cursor: pointer; // 手型指针
    // border-radius: 4px;
    background: #f0f0f0;
    color: #333;
    border: 1px solid black;
    font-size: 16px;

    // 选中状态
    &.active {
      background: #d9001b;
      color: white;
      box-shadow: 4px 4px 8px rgba(162, 162, 162, 0.9); // 右下方阴影‌:ml-citation{ref="1,2" data="citationList"}
    }


    // 悬停交互
    &:hover {
      opacity: 0.9;
      background: #d9001b;
      color: white;
      transform: translateY(-2px);
    }

    // 过渡动画
    transition: all 0.3s ease; // 平滑过渡效果 ‌:ml-citation{ref="3" data="citationList"}
  }
}
</style>
