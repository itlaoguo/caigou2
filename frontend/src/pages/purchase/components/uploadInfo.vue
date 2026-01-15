<template>
  <div class="main">
    <t-row>
      <t-col :span="12">
        <t-form ref="form" :data="uploadInfo" :rules="uploadInfoRules" label-align="right" labelWidth="160px">
          <t-form-item label="上传采购商品excel文件" name="file">
            <t-space>
              <t-upload
                v-model="uploadInfo.file"
                :auto-upload="autoUpload"
                :theme="display"
                :data="{ extra_data: 123, file_name: 'certificate' }"
                :abridge-name="[10, 8]"
                draggable
                action="https://service-bv448zsw-1257786608.gz.apigw.tencentcs.com/api/upload-demo"
              />
            </t-space>  
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
import { reactive,ref } from 'vue';
import { FormProps, UploadProps } from 'tdesign-vue-next';

const emits = defineEmits(['prev', 'next'])

const display = ref<UploadProps['theme']>('file');

const file = ref([]);
const autoUpload = ref(true);
const theme = ref('file');


const uploadInfo: FormProps['data'] = reactive({
  file: '',
});

const uploadInfoRules: FormProps['rules'] = {
  file: [
    {
      required: true,
      message: '请上传采购商品excel文件',
      type: 'error',
    },
  ],
};


const onPre = () => {
  emits('prev');
}

const onNext = () => {
  emits('next');
}

</script>


<style lang="less" scoped>
.main {
  padding: 20px;
  background: #fff;
  width: 100%;
}
</style>