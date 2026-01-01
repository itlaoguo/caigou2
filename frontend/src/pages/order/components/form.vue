<template>
  <div>
    <t-dialog :header="title" :footer="false" width="60%" v-model:visible="marketFormVisible" :onClose="onClose">
      <div class="marketing-form">
        <t-form ref="form" :data="formData" :colon="true">
          <t-form-item label="营销名称" name="name">
            <t-input v-model="formData.name" placeholder="请输入营销名称"></t-input>
          </t-form-item>

          <t-form-item label="营销方式" name="methods">
            <t-checkbox-group v-model="formData.marketingMethods">
              <t-checkbox key="1" label="短信" value="1" />
              <t-checkbox key="2" label="邮件" value="2" />
            </t-checkbox-group>
          </t-form-item>

          <t-form-item label="营销渠道" name="platform">
            <t-checkbox-group v-model="formData.platformId" :options="platforms" />
          </t-form-item>

          <t-form-item label="短信模板" name="smsTemplates">
            <t-select v-model="formData.smsTemplate" :options="smsTemplates" placeholder="请选择短信模板" clearable></t-select>
          </t-form-item>
          <t-form-item label="邮件模板" name="emailTemplate">
            <t-upload v-model="formData.emailTemplate"
              action="https://service-bv448zsw-1257786608.gz.apigw.tencentcs.com/api/upload-demo" :abridge-name="[8, 6]"
              theme="file-input" placeholder="未选择邮件模板"></t-upload>
          </t-form-item>

          <t-form-item label="状态" name="status">
            <t-switch v-model="formData.status"></t-switch>
          </t-form-item>

          <t-form-item>
            <t-space size="small">
              <t-button theme="primary" type="submit">提交</t-button>
              <t-button theme="default" variant="base" type="reset">重置</t-button>
            </t-space>
          </t-form-item>
        </t-form>

        <t-space direction="vertical">

        </t-space>
      </div>
    </t-dialog>
  </div>
</template>

<script lang="ts">
export default {
  name: 'marketingForm',
}
</script>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { t } from '@/locales';
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps, CheckboxGroupProps, CheckboxProps, SelectProps, FormProps } from 'tdesign-vue-next';

const marketFormVisible = ref(false);
let marketingId = ref(0);

let title = ref('编辑营销活动')

if (marketingId.value === 0) {

  title.value = '新增营销活动';
}

const formData: FormProps['data'] = reactive({
  name: '',
  marketingMethod: [],
  platformId: [],
  smsTemplate: '',
  emailTemplate: '',
  status: false,
});


const platforms: CheckboxGroupProps['options'] = [
  {
    value: '1',
    label: '海外独立站-shop.iflight.com',
  },
  {
    value: '2',
    label: '国内独立站-www.iflight.cn',
  },
  {
    value: '3',
    label: 'ebay美国',
  },
  {
    value: '4',
    label: '亚马逊美国站',
  },
  {
    value: '5',
    label: '淘宝旗舰店',
  },
];
const smsTemplates: SelectProps['options'] = [
  {
    value: 'sms_111431',
    label: 'sms_111431',
    title: 'sms_111431',
  },
  {
    value: 'sms_124915',
    label: 'sms_124915',
    title: 'sms_124915',
  },
]

const onClose = () => {

};

const getMarketing = async () => {
  formData.value = {
    name: '1111',
    marketingMethod: [1, 2],
    platformId: [1, 2, 3],
    smsTemplate: 'sms_111431',
    emailTemplate: '1111.html',
    status: true,
  }
}

watch(marketingId, () => {

  if (marketingId.value === 0) {
    formData.value = {
      name: '',
      marketingMethod: [],
      platformId: [],
      status: true,
    }
    return
  }

  return getMarketing();
});


onMounted(() => {
  console.log("组件挂载");
})

defineExpose({
  marketFormVisible, marketingId
})

</script>

<style lang="less" scoped>
.detail {
  .t-card {
    margin-bottom: 10px;
  }
}
</style>