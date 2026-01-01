<template>
  <t-form ref="form" class="base-form" :data="formData" :rules="FORM_RULES" label-align="top" :label-width="100"
    @reset="onReset" @submit="onSubmit">
    <div class="form-basic-container">
      <div class="form-basic-item">

        <div class="form-basic-container-title">
          <p /> {{ DetailsSwticher ? '添加/修改员工信息' : '查看员工信息' }}
        </div>
        <!-- 表单内容 -->

        <!-- 合同名称,合同类型 -->
        <t-divider align="left">1.基本信息</t-divider>
        <t-row class="row-gap" :gutter="[32, 24]">
          <!-- 第一行 -->
          <t-col :span="3">
            <t-form-item label="员工姓名" name="name">
              <t-input v-model="formData.name" :style="{ width: '322px' }" placeholder="请输入内容" />
              <!-- 秦学海 -->
            </t-form-item>
          </t-col>
          <t-col :span="3">
            <t-form-item label="员工编号" name="type">
              <t-input v-model="formData.name" :style="{ width: '322px' }" placeholder="请输入内容" />
            </t-form-item>
          </t-col>
          <t-col :span="3">
            <t-form-item label="员工性别" name="type">
              <t-select v-model="formData.type" :style="{ width: '322px' }" class="demo-select-base" clearable>
                <t-option v-for="(item, index) in TYPE_OPTIONS_GENDER" :key="index" :value="item.value"
                  :label="item.label">
                  {{ item.label }}
                </t-option>
              </t-select>
            </t-form-item>
          </t-col>
          <t-col :span="3">
            <t-form-item label="手机号码" name="type">
              <t-input v-model="formData.name" :style="{ width: '322px' }" placeholder="请输入内容" />
            </t-form-item>
          </t-col>

          <!-- 第二行 -->
          <t-col :span="3">
            <t-form-item label="出生日期" name="name">
              <t-date-picker v-model="formData.name" style="width: 100%;" />
            </t-form-item>
          </t-col>
          <t-col :span="3">
            <t-form-item label="学历" name="type">
              <t-select v-model="formData.type" :style="{ width: '322px' }" class="demo-select-base" clearable>
                <t-option v-for="(item, index) in TYPE_OPTIONS_KNOELEDGE" :key="index" :value="item.value"
                  :label="item.label">
                  {{ item.label }}
                </t-option>
              </t-select>
            </t-form-item>
          </t-col>
          <t-col :span="3">
            <t-form-item label="婚姻状况" name="type">
              <t-select v-model="formData.type" :style="{ width: '322px' }" class="demo-select-base" clearable>
                <t-option v-for="(item, index) in TYPE_OPTIONS_MARRIAGE" :key="index" :value="item.value"
                  :label="item.label">
                  {{ item.label }}
                </t-option>
              </t-select>
            </t-form-item>
          </t-col>
          <t-col :span="3">
            <t-form-item label="在职状态" name="type">
              <t-select v-model="formData.type" :style="{ width: '322px' }" class="demo-select-base" clearable>
                <t-option v-for="(item, index) in TYPE_OPTIONS_STATUS" :key="index" :value="item.value"
                  :label="item.label">
                  {{ item.label }}
                </t-option>
              </t-select>
            </t-form-item>
          </t-col>

          <!-- 第三行 -->
          <t-col :span="3">
            <t-form-item label="身份证号码" name="name">
              <t-input v-model="formData.name" :style="{ width: '100%' }" placeholder="请输入内容" />
            </t-form-item>
          </t-col>
          <t-col :span="3">
            <t-form-item label="户籍地址" name="type">
              <t-input v-model="formData.name" :style="{ width: '100%' }" placeholder="请输入内容" />
            </t-form-item>
          </t-col>
          <t-col :span="3">
            <t-form-item label="衣服码数" name="type">
              <t-input v-model="formData.name" :style="{ width: '100%' }" placeholder="请输入内容" />
            </t-form-item>
          </t-col>
          <t-col :span="3">
            <t-form-item label="现在住址" name="type">
              <t-input v-model="formData.name" :style="{ width: '100%' }" placeholder="请输入内容" />
            </t-form-item>
          </t-col>

          <!-- 第四行 -->
          <t-col :span="12">
            <t-form-item v-if="DetailsSwticher" label="在线简历" name="name">
              <t-upload :v-model="files" ref="uploadRef1" action="http://jxj.zhgdyun.com:15551/upload/image"
                placeholder=" 要求文件大小在 10M 以内" :size-limit="{ size: 10, unit: 'MB' }" :max="1"
                :allow-upload-duplicate-file="true" name="files" @select-change="handleSelectChange" @fail="handleFail"
                @success="handleSuccess" @one-file-success="onOneFileSuccess" @validate="onValidate" />
            </t-form-item>
            <t-form-item v-else label="在线简历">
              <a href="http://jxj.zhgdyun.com:15551/image/67d007d201fdb41366cc19f1.pdf" target="_blank"
                rel="noopener noreferrer" style="text-decoration: none; color: blue; font-size: 18px;">
                点击查看
              </a>


            </t-form-item>
          </t-col>
        </t-row>

        <t-divider align="left">2.紧急联系人</t-divider>
        <t-form-item v-if="DetailsSwticher" label="" name="Examinedata">
          <div class="examineData-box"
            style="display: flex;flex-direction: column;max-height: 650px;overflow-y: auto;width: 100%;">
            <div>
              <t-button theme="success" @click="onAddexamine" style="margin-bottom: 20px;">
                <template #icon> <t-icon name="plus" size="18px" /></template>
                添 加
              </t-button>
            </div>
            <t-steps layout="vertical" readonly>
              <!-- 每一项 -->
              <t-step-item :title="'紧急联系人' + (index + 1)" v-for="(btn, index) in ExamineData" :key="index">
                <div style="display: flex;align-items: center;width: 100%;">
                  <div
                    style="display: flex;white-space: nowrap;align-items: center;margin-right: 15px;color: black;width: 100%;">
                    姓名:
                    &nbsp;&nbsp;&nbsp;<t-input v-model="btn.a" />&nbsp;&nbsp;&nbsp;
                    关系:
                    &nbsp;&nbsp;&nbsp;<t-input v-model="btn.b" />&nbsp;&nbsp;&nbsp;
                    电话:
                    &nbsp;&nbsp;&nbsp;<t-input v-model="btn.c" />&nbsp;&nbsp;&nbsp;
                  </div>
                  <t-icon name="delete" size="20px" style="color: red;cursor: pointer;" @click="onDelete(index)" />
                </div>
              </t-step-item>
            </t-steps>
          </div>
        </t-form-item>

        <!-- 回显 -->
        <div v-else>
          <t-table :data="ExamineData" :columns="COLUMNSExamineData" bordered>
            <template #index="{ rowIndex }">
              <span>
                {{ rowIndex + 1 }}
              </span>
            </template>
          </t-table>
        </div>

        <t-divider align="left">3.奖惩记录</t-divider>
        <t-form-item v-if="DetailsSwticher" label="" name="Examinedata">
          <div class="examineData-box"
            style="display: flex;flex-direction: column;max-height: 650px;overflow-y: auto;width: 100%;">
            <div>
              <t-button theme="success" @click="onAddRewards" style="margin-bottom: 20px;">
                <template #icon> <t-icon name="plus" size="18px" /></template>
                添 加
              </t-button>
            </div>
            <t-steps layout="vertical" readonly>
              <!-- 每一项 -->
              <t-step-item :title="'奖惩记录' + (index + 1)" v-for="(btn, index) in RewardsData" :key="index">
                <div style="display: flex;align-items: center;width: 100%;">
                  <div
                    style="display: flex;white-space: nowrap;align-items: center;margin-right: 15px;color: black;width: 100%;">
                    类型:
                    &nbsp;&nbsp;&nbsp;<t-input v-model="btn.a" />&nbsp;&nbsp;&nbsp;
                    说明:
                    &nbsp;&nbsp;&nbsp;<t-input v-model="btn.b" />&nbsp;&nbsp;&nbsp;
                    资料:&nbsp;&nbsp;&nbsp;
                    <t-upload ref="uploadRef1" v-model="file1" :image-viewer-props="{ closeOnEscKeydown: false }"
                      :size-limit="{ size: 1, unit: 'MB' }" action="http://jxj.zhgdyun.com:15551/upload/image"
                      name="files" theme="image" tips="单张图片文件上传" accept="image/*" auto-upload show-image-file-name
                      @fail="handleFail"></t-upload>&nbsp;&nbsp;&nbsp;
                  </div>
                  <t-icon name="delete" size="20px" style="color: red;cursor: pointer;"
                    @click="onRewardsDelete(index)" />
                </div>
              </t-step-item>
            </t-steps>
          </div>
        </t-form-item>
        <!-- 回显 -->
        <div v-else>
          <t-table :data="RewardsData" :columns="COLUMNSRewardsData" bordered>
            <template #index="{ rowIndex }">
              <span>
                {{ rowIndex + 1 }}
              </span>
            </template>

            <template #cc="{ row }">
              <t-image-viewer :images="[row.cc]">
                <template #trigger="{ open }">
                  <div class="tdesign-demo-image-viewer__ui-image">
                    <img alt="test" :src="row.cc" class="tdesign-demo-image-viewer__ui-image--img"
                      style="width: 50px;height: 50px;" />
                    <div class="tdesign-demo-image-viewer__ui-image--hover" @click="open">
                      <span>
                        <BrowseIcon size="1.4em" /> 查看
                      </span>
                    </div>
                  </div>
                </template>
              </t-image-viewer>
            </template>
          </t-table>
        </div>

        <!-- 入离职记录 -->
        <div v-if="!DetailsSwticher">
          <t-divider align="left">4.入/离职记录</t-divider>

          <t-table :data="ExititData" :columns="COLUMNSExititData" bordered>
            <template #index="{ rowIndex }">
              <span>
                {{ rowIndex + 1 }}
              </span>
            </template>

            <template #aa="{ row }">
              <t-tag v-if="row.aa == 1" theme="success" variant="light">
                在职
              </t-tag>
              <t-tag v-if="row.bb == 0" theme="danger" variant="light">
                离职
              </t-tag>
            </template>
          </t-table>
        </div>

      </div>
    </div>

    <div class="form-submit-container">
      <div class="form-submit-sub">
        <div class="form-submit-left">
          <t-button v-if="DetailsSwticher" theme="primary" class="form-submit-confirm" type="submit">
            {{ t('pages.formBase.confirm') }}
          </t-button>
          <t-button type="reset" class="form-submit-cancel" theme="default" variant="base">
            {{ t('pages.formBase.cancel') }}
          </t-button>
        </div>
      </div>
    </div>
  </t-form>
</template>

<script lang="ts">
export default {
  name: 'StaffAdd',
};
</script>

<script setup lang="ts">
import type { SubmitContext, UploadFailContext, UploadFile, UploadProps, UploadSelectChangeContext } from 'tdesign-vue-next';
import { MessagePlugin } from 'tdesign-vue-next';
import { ref, defineProps, onMounted } from 'vue';

import { t } from '@/locales';

import { FORM_RULES, INITIAL_DATA, TYPE_OPTIONS_KNOELEDGE, TYPE_OPTIONS_MARRIAGE, TYPE_OPTIONS_STATUS, TYPE_OPTIONS_GENDER, COLUMNSExititData, COLUMNSRewardsData, COLUMNSExamineData } from '../constants';
import { status } from 'nprogress';
import { number } from 'echarts';

const formData = ref({ ...INITIAL_DATA });

const datafun = defineProps({
  // contractId: {
  //   type: String,
  //   default: ''
  // },
  onaddswtich: {
    type: Function,
  },
  DetailsSwticher: {
    type: Boolean,
  }
})

onMounted(() => {
  console.log("查看开关==》", datafun.DetailsSwticher);
  DetailsSwticher.value = datafun.DetailsSwticher;
})


// 查看修改控制
const DetailsSwticher = ref(true);

// 查询单条
const getsomeId = (id: string) => {
  console.log(`查询单条==>${id}`);

}

// 紧急联系人
const ExamineData = ref([
  {
    a: "张三",
    b: "未知",
    c: "15220730439",
  }
])
const onAddexamine = () => {
  ExamineData.value.push({
    a: null,
    b: null,
    c: null,
  })
  console.log(formData.value);

};

const onDelete = (index: number) => {
  ExamineData.value.splice(index, 1);
}

// 奖惩记录
const RewardsData = ref([
  {
    a: "奖励",
    b: "在2025-3-10  提出.....",
    cc: "https://img2.baidu.com/it/u=2048686680,1627900849&fm=253&fmt=auto&app=138&f=JPEG?w=749&h=500",
  }
])
const onAddRewards = () => {
  console.log(file1.value);
  RewardsData.value.push({
    a: null,
    b: null,
    cc: null,
  })
};

const onRewardsDelete = (index: number) => {
  RewardsData.value.splice(index, 1);
}

// 离职记录
const ExititData = ref([
  {
    aa: 1,
    bb: "2025-3-3",
    cc: "王兰",
    dd: "2025-3-10"
  }
])

// 上传
// 添加表单类型
interface files {
  name: string,
  status: string,
  url: string,
  size: number
}
const files = ref<files>()


const handleFail: UploadProps['onFail'] = ({ file }) => {
  MessagePlugin.error(`文件 ${file.name} 上传失败`);
};
function handleSelectChange(files: File[], context: UploadSelectChangeContext) {
  console.log('onSelectChange', files, context);
}
const handleSuccess: UploadProps['onSuccess'] = (params) => {
  console.log('success', params);
  console.log(formData.value);
  // let data = {
  //   name: '',
  //   status: 'success',
  //   url: '',
  //   size: 0
  // }
  // data.name = params.file.name;
  // data.size = params.file.size;
  // data.url = `http://jxj.zhgdyun.com:15551/image/${params.fileList[0].response.data[0].imageUrl}`

  // formData.value.files = [];
  // formData.value.files.push(data);


  // MessagePlugin.success('上传成功');
};


const onValidate: UploadProps['onValidate'] = (params) => {
  console.log("超出文件");
  const { files, type } = params;
  console.log('onValidate', type, files);
};

const onOneFileSuccess: UploadProps['onOneFileSuccess'] = (params) => {
  console.log('onOneFileSuccess', params);
};


// 上传图片
const file1 = ref([])

const onReset = () => {
  datafun.onaddswtich();
  MessagePlugin.warning('取消新建');
};
const onSubmit = (ctx: SubmitContext) => {
  if (ctx.validateResult === true) {
    MessagePlugin.success('新建成功');
  }
};
</script>

<style lang="less" scoped>
@import "@/style/public/add.less";

.form-basic-item {
  width: 1100px !important;
}

.form-submit-container {
  border-top: 0 !important;

}

::v-deep .t-table--bordered th {
  // background-color: #c9e1fb !important;
  // color: black !important;

  background-color: #125be2 !important;
  // background-color: #1968a0 !important;
  color: white !important;
  font-weight: bold !important;
  font-size: 16px !important;
}
</style>
