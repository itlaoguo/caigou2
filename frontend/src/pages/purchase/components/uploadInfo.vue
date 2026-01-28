<template>
  <div class="main">
    <t-row>
      <t-col :span="12">
        <t-form ref="form" :data="uploadInfo" :rules="uploadInfoRules" label-align="right" labelWidth="180px"
          @submit="onNext">
          <t-form-item label="上传采购商品excel文件" name="file">
            <t-space>
              <t-upload v-model="files" name="file" autoUpload :theme="display" draggable :multiple="false"
                :action="uploadURL" :allowUploadDuplicateFile="false" :headers="headers" accept=".xlsx,.xls"
                @success="handleUploadSuccess" @fail="handleUploadFail" @remove="handleRemove" />
            </t-space>
          </t-form-item>
          <t-form-item label="">
            <t-typography-text theme="secondary">请使用下载的模版填写采购商品excel文件&nbsp;&nbsp;</t-typography-text>
            <t-link theme="primary" underline download :href="downloadTemplateURL" target="_self">
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
import type { UploadFile, UploadFailContext, UploadSelectChangeContext, UploadRemoveContext } from 'tdesign-vue-next';
import type { UploadInfo } from '@/api/model/purchaseModel';
import { usePurchaseStore } from '@/store/modules/purchase';
import { useUserStore } from '@/store';

const emits = defineEmits(['prev', 'next'])

const purchaseStore = usePurchaseStore();

const userStore = useUserStore();
const headers = computed<UploadProps['headers']>(() => ({
  'Authorization': `Bearer ${userStore.token || ''}`,
  'Request-from': 'dashboard'
}));

const display = ref<UploadProps['theme']>('file');
const files = ref<UploadProps['value']>([]);

let baseURL = import.meta.env.VITE_BASE_URL.endsWith('/') ? import.meta.env.VITE_BASE_URL.substr(0, import.meta.env.VITE_BASE_URL.length - 1) : import.meta.env.VITE_BASE_URL;
let uploadURL = baseURL + '/api/upload/file';
let downloadTemplateURL = baseURL + '/uploads/purchase-template.xlsx';

const form = ref<FormInstanceFunctions>();

const uploadInfo: UploadInfo = reactive({
  name: '',
  path: '',
  url: '',
  size: 0,
  uploadTime: '',
});

const uploadInfoRules: FormProps['rules'] = {
  // 文件上传验证在 onNext 中手动处理
};

// 同步 store 数据到表单的函数
const syncStoreToForm = () => {
  const storeUploadInfo = purchaseStore.uploadInfo;
  if (storeUploadInfo && (storeUploadInfo.path || storeUploadInfo.url || storeUploadInfo.name || storeUploadInfo.uploadTime)) {
    uploadInfo.name = storeUploadInfo.name || '';
    uploadInfo.path = storeUploadInfo.path || '';
    uploadInfo.url = storeUploadInfo.url || '';
    uploadInfo.uploadTime = storeUploadInfo.uploadTime || '';

    // 如果存在文件路径或URL，构建 UploadFile 对象用于显示
    if (storeUploadInfo.path || storeUploadInfo.url) {
      const fileUrl = storeUploadInfo.url || storeUploadInfo.path;
      files.value = [{
        name: storeUploadInfo.name || fileUrl.split('/').pop() || '已上传文件',
        url: fileUrl,
        status: 'success',
        size: storeUploadInfo.size || 0,
        uploadTime: storeUploadInfo.uploadTime || '',
      } as UploadFile];
    }
  }
};

// 监听 store 的 uploadInfo 变化
watch(
  () => purchaseStore.uploadInfo,
  () => {
    syncStoreToForm();
  },
  { deep: true, immediate: true }
);

// 组件挂载时确保数据同步（处理持久化数据恢复的延迟）
onMounted(() => {
  nextTick(() => {
    syncStoreToForm();
  });
});

const onPre = () => {
  emits('prev');
}

// 文件上传前的验证，只允许 excel 文件
const beforeUpload = (file: UploadFile): boolean => {
  const fileName = file.name || '';
  const validExtensions = ['.xlsx', '.xls'];
  const fileExtension = fileName.substring(fileName.lastIndexOf('.')).toLowerCase();

  if (!validExtensions.includes(fileExtension)) {
    MessagePlugin.error('只能上传 Excel 文件（.xlsx 或 .xls）');
    return false;
  }

  return true;
};

// 处理文件选择变化，确保文件大小被保存
const handleSelectChange = (files: File[], context: UploadSelectChangeContext) => {
  // 当文件被选择时，将 File 对象转换为 UploadFile 对象并保存文件大小
  if (files && files.length > 0) {
    // 验证文件类型
    const file = files[0];
    const fileName = file.name || '';
    const validExtensions = ['.xlsx', '.xls'];
    const fileExtension = fileName.substring(fileName.lastIndexOf('.')).toLowerCase();

    if (!validExtensions.includes(fileExtension)) {
      MessagePlugin.error('只能上传 Excel 文件（.xlsx 或 .xls）');
      files.value = [];
      return;
    }

    files.value = [{
      name: file.name,
      size: file.size, // 文件大小（字节）
      raw: file,
      uploadTime: new Date().toISOString(),
      status: 'progress' as const, // 上传中状态
    } as UploadFile];
  }
}


// 处理上传成功
const handleUploadSuccess: UploadProps['onSuccess'] = (context) => {
  console.log(context, '---------handleUploadSuccess-------------');

  if (context.response && context.response.code === 10000) {
    const responseData = context.response.data;

    // 更新 uploadInfo 数据
    uploadInfo.name = responseData.originalName || '';
    uploadInfo.path = responseData.path || '';
    uploadInfo.url = responseData.url || '';
    uploadInfo.size = responseData.size || 0;
    uploadInfo.uploadTime = responseData.uploadTime || new Date().toISOString();

    // 更新文件列表中的文件信息
    const index = files.value.findIndex(f => f.name === context.file.name);
    if (index > -1) {
      files.value[index].url = responseData.url || '';
      files.value[index].status = 'success';
      files.value[index].size = context.file.size || 0;
      files.value[index].uploadTime = uploadInfo.uploadTime;
    }

    MessagePlugin.success(`文件 ${context.file.name} 上传成功`);
  } else {
    // 上传失败，调用失败处理函数


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
  const index = files.value.findIndex(f => f.name === options.file.name);
  if (index > -1) {
    files.value.splice(index, 1);
  }
};

// 处理文件删除
const handleRemove: UploadProps['onRemove'] = (context: UploadRemoveContext) => {
  // 使用 v-model 时，组件会自动从 files 中移除文件，这里只需要清空相关数据

  // 清空表单数据
  uploadInfo.name = '';
  uploadInfo.path = '';
  uploadInfo.url = '';
  uploadInfo.uploadTime = '';
  uploadInfo.size = undefined;

  // 清空 store
  purchaseStore.setUploadInfo({
    name: '',
    path: '',
    url: '',
    size: 0,
    uploadTime: '',
  });

  MessagePlugin.success('文件已删除');
};

const onNext = async () => {
  // 检查是否已上传文件
  const hasUploadedFile = uploadInfo.path || uploadInfo.url || uploadInfo.name;
  const hasFileInList = files.value.length > 0 && files.value.some(file => file.status === 'success');

  if (!hasUploadedFile && !hasFileInList) {
    MessagePlugin.warning('请先上传采购商品excel文件');
    return;
  }

  // 如果文件列表中有成功上传的文件，但 uploadInfo 中没有信息，从文件列表中获取
  if (!hasUploadedFile && hasFileInList) {
    const successFile = files.value.find(file => file.status === 'success');
    if (successFile?.url) {
      uploadInfo.url = successFile.url;
      uploadInfo.path = successFile.url;
      uploadInfo.name = successFile.name || '';
    }
  }

  // 保存上传文件信息到 store
  if (uploadInfo.path || uploadInfo.url || uploadInfo.name) {
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