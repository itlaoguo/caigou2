<template>
  <div class="main">
    <t-row>
      <t-col :span="12">
        <t-form ref="form" :data="uploadInfo" :rules="uploadInfoRules" label-align="right" labelWidth="180px"
          @submit="onNext">
          <t-form-item label="上传采购商品excel文件" name="file">
            <t-space>
              <t-upload :files="fileList" autoUpload :theme="display" draggable :multiple="false"
                action="http://caigou2.yuanmapai.net.local/api/upload/file" :format-response="formatResponse"
                :headers="headers" @success="handleUploadSuccess" @fail="handleUploadFail" />
            </t-space>
          </t-form-item>
          <t-form-item label="">
            <t-typography-text theme="secondary">请使用下载的模版填写采购商品excel文件&nbsp;&nbsp;</t-typography-text>
            <t-link theme="primary" underline download
              href="http://caigou2.yuanmapai.net.local/uploads/purchase-template.xlsx" target="_self">
              <template #prefix-icon>
                <icon name="download" size="16" />
              </template>
              下载采购商品excel模版
            </t-link>
          </t-form-item>
          <t-form-item>
            <t-button theme="primary" @click="onPre">上一步</t-button>
            <t-button theme="primary" @click="onNext">下一步</t-button>
          </t-form-item>
        </t-form>
      </t-col>
    </t-row>
  </div>

</template>

<script lang="ts" setup>
import { reactive, ref, watch, onMounted, nextTick, computed } from 'vue';
import { FormProps, UploadProps, Icon, MessagePlugin, FormInstanceFunctions } from 'tdesign-vue-next';
import type { UploadFile, UploadFailContext } from 'tdesign-vue-next';
import type { UploadInfo } from '@/api/model/purchaseModel';
import { usePurchaseStore } from '@/store/modules/purchase';
import { useUserStore } from '@/store';

const emits = defineEmits(['prev', 'next'])

const display = ref<UploadProps['theme']>('file');
const purchaseStore = usePurchaseStore();
const userStore = useUserStore();
const form = ref<FormInstanceFunctions>();

// 获取用户 token 并设置请求头
const headers = computed<UploadProps['headers']>(() => ({
  Authorization: `Bearer ${userStore.token || ''}`,
}));

// t-upload 组件需要的文件列表
const fileList = ref<UploadFile[]>([]);

const uploadInfoRules: FormProps['rules'] = {
  // 文件上传验证在 onNext 中手动处理
};

// 表单数据 - 直接从 store 获取初始值
const uploadInfo: UploadInfo = reactive({
  file: purchaseStore.uploadInfo?.file || '',
  path: purchaseStore.uploadInfo?.path || '',
  url: purchaseStore.uploadInfo?.url || ''
});

const onPre = () => {

  emits('prev');
}

// 格式化上传响应，用于处理错误情况
const formatResponse = (res: any) => {
  // 如果响应包含错误信息，返回错误对象
  if (res && res.code) {
    // code 10000 表示成功
    if (res.code === 10000) {
      return res.data;
    }
    // 其他 code 表示失败，返回错误信息
    return {
      error: res.message || '上传失败',
      code: res.code
    };
  }
  // 如果没有 code 字段，检查是否有 data
  if (res && res.data) {
    return res.data;
  }
  return res;
};

// 处理上传成功
const handleUploadSuccess: UploadProps['onSuccess'] = (context) => {
  // 检查响应是否包含错误
  if (context?.response?.error) {
    const errorMsg = context.response.error || '上传失败';
    MessagePlugin.error(errorMsg);

    // 如果是认证失败（code 10001），系统会自动处理跳转登录
    if (context.response.code === 10001) {
      MessagePlugin.warning('认证失败，请重新登录');
    } else if (context.response.code === 10003) {
      MessagePlugin.warning('没有权限操作');
    }

    // 清除文件列表中的失败文件
    if (context.file) {
      const index = fileList.value.findIndex(f => f.name === context.file?.name);
      if (index > -1) {
        fileList.value.splice(index, 1);
      }
    }
    return;
  }

  // 根据上传接口返回的数据更新 uploadInfo
  if (context?.response?.data || context?.response) {
    const data = context.response.data || context.response;
    const newUploadInfo: UploadInfo = {
      path: data.path || data.filename || '',
      url: data.url || data.path || '',
      file: data.filename || data.path || ''
    };

    // 更新表单数据
    Object.assign(uploadInfo, newUploadInfo);

    // 更新 store（这会触发持久化）
    purchaseStore.setUploadInfo(newUploadInfo);

    // 更新文件列表显示
    if (newUploadInfo.path || newUploadInfo.url) {
      const fileUrl = newUploadInfo.url || newUploadInfo.path;
      fileList.value = [{
        name: fileUrl.split('/').pop() || '已上传文件',
        url: fileUrl,
        status: 'success'
      } as UploadFile];
    }

  }
};

// 处理上传失败
const handleUploadFail: UploadProps['onFail'] = (options: UploadFailContext) => {
  const errorMsg = options.response?.error || options.response?.message || '文件上传失败';
  MessagePlugin.error(`文件 ${options.file.name} 上传失败：${errorMsg}`);

  // 如果是认证失败
  if (options.response?.code === 10001) {
    MessagePlugin.warning('认证失败，请重新登录');
  } else if (options.response?.code === 10003) {
    MessagePlugin.warning('没有权限操作');
  }

  // 从文件列表中移除失败的文件
  const index = fileList.value.findIndex(f => f.name === options.file.name);
  if (index > -1) {
    fileList.value.splice(index, 1);
  }
};

const onNext = async () => {
  // 检查是否已上传文件
  const hasUploadedFile = uploadInfo.path || uploadInfo.url || uploadInfo.file;
  const hasFileInList = fileList.value.length > 0 && fileList.value.some(file => file.status === 'success');

  if (!hasUploadedFile && !hasFileInList) {
    MessagePlugin.warning('请先上传采购商品excel文件');
    return;
  }

  // 如果文件列表中有成功上传的文件，但 uploadInfo 中没有信息，从文件列表中获取
  if (!hasUploadedFile && hasFileInList) {
    const successFile = fileList.value.find(file => file.status === 'success');
    if (successFile?.url) {
      uploadInfo.url = successFile.url;
      uploadInfo.path = successFile.url;
      uploadInfo.file = successFile.name || '';
    }
  }

  // 保存上传文件信息到 store
  if (uploadInfo.path || uploadInfo.url || uploadInfo.file) {
    purchaseStore.setUploadInfo({ ...uploadInfo });
    emits('next');
  } else {
    MessagePlugin.warning('请先上传采购商品excel文件');
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