<template>
  <header class="login-header">
    <img src="http://jxj.zhgdyun.com:15551/image/67f712d501fdb41366cc1a23.png" alt="" width="180">
    <div class="operations-container">
      <t-tooltip placement="bottom" content="官方网站">
        <t-button theme="default" shape="square" variant="text" @click="navToHelper">
          <t-icon name="logo-twitter" />
        </t-button>
      </t-tooltip>
      <t-button theme="default" shape="square" variant="text" @click="toggleSettingPanel">
        <t-icon name="setting" class="icon" />
      </t-button>
    </div>
  </header>
</template>

<script setup lang="ts">
import LogoFullIcon from '@/assets/assets-logo-full.svg?component';
import { useSettingStore } from '@/store';

const settingStore = useSettingStore();
const toggleSettingPanel = () => {
  settingStore.updateConfig({
    showSettingPanel: true,
  });
};

const navToGitHub = () => {
  window.open('https://www.zanboob.com');
};

const navToHelper = () => {
  window.open('https://www..com/');
};
</script>

<style lang="less" scoped>
.login-header {
  padding: 0 var(--td-comp-paddingLR-xl);
  display: flex;
  justify-content: space-between;
  align-items: center;
  backdrop-filter: blur(10px);
  color: var(--td-text-color-primary);
  height: var(--td-comp-size-xxxl);

  .logo {
    width: 178px;
    height: var(--td-comp-size-s);
  }

  .operations-container {
    display: flex;
    align-items: center;

    .t-button {
      margin-left: var(--td-comp-margin-l);
    }
  }
}
</style>
