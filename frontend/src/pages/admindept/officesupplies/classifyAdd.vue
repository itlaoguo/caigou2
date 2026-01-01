<template>
  <div>
    <t-dialog v-model:visible="dictaddVisible" header="物料类别表单" width="600" @confirm="onConfirmDelete"
      :on-cancel="onCancel">
      <div>
        <t-form ref="form" :rules="rules" :data="FormData" label-align="right" :label-width="100">
          <t-form-item label="产品名称" name="name">
            <t-input v-model="FormData.a"></t-input>
          </t-form-item>

          <t-form-item label="产品编码" name="password">
            <t-input v-model="FormData.b"></t-input>
          </t-form-item>

          <t-form-item label="物料图片" name="password">
            <t-upload ref="uploadRef1" v-model="file1" :image-viewer-props="{ closeOnEscKeydown: false }"
              :size-limit="{ size: 1, unit: 'MB' }" action="http://jxj.zhgdyun.com:15551/upload/image" name="files"
              theme="image" tips="单张图片文件上传" accept="image/*" auto-upload show-image-file-name
              @fail="handleFail"></t-upload>

          </t-form-item>
          <t-form-item label="产品单位" name="status">
            <t-select v-model="FormData.b" style="display: inline-block" class="form-item-content"
              :options="CONTRACT_TYPE_UNIT" placeholder="请选择" clearable filterable />
          </t-form-item>
          <t-form-item label="负责人" name="password">
            <t-select v-model="FormData.b" style="display: inline-block" class="form-item-content"
              :options="CONTRACT_TYPE_OPTIONS" placeholder="请选择" clearable filterable />
          </t-form-item>
          <t-form-item label="备注" name="remark">
            <t-textarea v-model="FormData.remark" placeholder="请输入内容" />
          </t-form-item>
        </t-form>
      </div>
    </t-dialog>
  </div>
</template>

<script lang="ts">
export default {
  name: 'officeAdd',
}
</script>

<script setup lang="ts">
import { FormProps, MessagePlugin, UploadProps } from 'tdesign-vue-next';
import { ref } from 'vue';
import { CONTRACT_TYPE_OPTIONS, CONTRACT_TYPE_UNIT } from "./constants"

// 表单数据
const Form = ref(null);
const FormData = ref({
  a: '电脑WIN10 机箱',
  b: 'BG17796',
  c: '2025-3-7 16:00:00',
  status: '0',
  remark: ''
});

// 表单验证
const rules: FormProps['rules'] = {
  name: [{ required: true, message: '必填', type: 'error' }],
  password: [{ required: true, message: '必填', type: 'error' }],
};

const handleChange = (value: any) => {
  console.log(value);
  FormData.value.status = value;
  console.log(FormData.value);

};

// 上传图片
const file1 = ref([])

// 上传
const handleFail: UploadProps['onFail'] = ({ file }) => {
  MessagePlugin.error(`文件 ${file.name} 上传失败`);
};


// 添加按钮
const onConfirm = () => {
  Form.value.validate().then((res: any) => {
    console.log(res);
  });
}

//添加开关
const dictaddVisible = ref(false);

// 重置按钮
const onReset = () => {
  Form.value.reset();
};

// 打开按钮
const onSwticher = () => {
  dictaddVisible.value = !dictaddVisible.value;
}

// 删除-确定
const onConfirmDelete = () => {
  onReset();
  onSwticher();
};

// 删除-取消
const onCancel = () => {
  console.log('表单关闭执行');
};

// 查询单条
const getconId = (id: number) => {
  console.log(id);
}

// 暴露
defineExpose({ onSwticher, getconId });

</script>

<style lang="less" scoped></style>