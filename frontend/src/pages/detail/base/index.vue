<template>
  <div class="detail-base">
    <t-card :bordered="false" class="container-base-margin-top">
      <t-descriptions :title="t('pages.detailBase.baseInfo.title')">
        <t-descriptions-item v-for="(item, index) in BASE_INFO_DATA" :key="index" :label="item.name">
          <span :class="{
            ['inProgress']: item.type && item.type.value === 'inProgress',
            ['pdf']: item.type && item.type.value === 'pdf',
          }">
            <i v-if="item.type && item.type.key === 'contractStatus'" />
            {{ item.value }}
          </span>
        </t-descriptions-item>
      </t-descriptions>

      <div class="titlenav" />
    </t-card>


    <div class="card-bottom">
      <t-card title="审批流程-合同审批" class="container-base-margin-top " :bordered="false">
        <t-steps class="detail-base-info-steps" layout="vertical" theme="dot" :current="2">
          <t-step-item :title="'审批第' + (index + 1) + '步'" v-for="(btn, index) in ExamineData" :key="index">

            <div class="examine-box">
              <!-- 顶部 -->
              <div class="examine-top">
                {{ btn.a }}: {{ btn.b }} <t-tag v-if="btn.status == '0'" theme="success" variant="light">
                  审批发起
                </t-tag>
                <t-tag v-if="btn.status == '1'" theme="warning" variant="light">
                  待审批
                </t-tag>
                <t-tag v-if="btn.status == '2'" theme="danger" variant="light">
                  超 时
                </t-tag>
                <t-tag v-if="btn.status == '3'" theme="danger" variant="light">
                  驳 回
                </t-tag>
                <t-tag v-if="btn.status == '4'" theme="success" variant="light">
                  审批完成
                </t-tag>
              </div>
              <!-- 详细信息 -->
              <div class="examine-bottom">
                <t-tag v-if="btn.code == '0'" theme="success" variant="light">
                  同意
                </t-tag>
                <t-tag v-if="btn.code == '1'" theme="danger" variant="light">
                  不同意
                </t-tag>

                {{ btn.d }}
              </div>

              <div v-if="btn.status == '4'">
                备注: {{ btn.e }}
              </div>
            </div>


          </t-step-item>

        </t-steps>

        <div class="titlenav" />
      </t-card>
    </div>


  </div>
</template>

<script lang="ts">
export default {
  name: 'DetailBase',
};
</script>

<script setup lang="ts">
import { t } from '@/locales';
import { status } from 'nprogress';
import { ref } from 'vue';

const BASE_INFO_DATA = [
  {
    name: t('constants.contract.name'),
    value: '总部办公用品采购项目',
    type: null,
  },
  {
    name: t('constants.contract.status'),
    value: '履行中',
    type: {
      key: 'contractStatus',
      value: 'inProgress',
    },
  },
  {
    name: t('constants.contract.num'),
    value: 'BH00010',
    type: null,
  },
  {
    name: t('constants.contract.type'),
    value: t('constants.contract.typeOptions.main'),
    type: null,
  },
  {
    name: t('constants.contract.payType'),
    value: t('constants.contract.pay'),
    type: null,
  },
  {
    name: t('constants.contract.amount'),
    value: '¥ 5,000,000',
    type: null,
  },
  {
    name: t('constants.contract.company'),
    value: '腾讯科技（深圳）有限公司',
    type: null,
  },
  {
    name: t('constants.contract.employee'),
    value: '欧尚',
    type: null,
  },
  {
    name: t('constants.contract.signDate'),
    value: '2020-12-20',
    type: null,
  },
  {
    name: t('constants.contract.effectiveDate'),
    value: '2021-01-20',
    type: null,
  },
  {
    name: t('constants.contract.endDate'),
    value: '2022-12-20',
    type: null,
  },
  {
    name: t('constants.contract.attachment'),
    value: '总部办公用品采购项目合同.pdf',
    type: {
      key: 'contractAnnex',
      value: 'pdf',
    },
  },
  {
    name: t('constants.contract.createDate'),
    value: '2020-12-22 10:00:00',
    type: null,
  },
];


// 审批数据
const ExamineData = ref([
  {
    a: '采购部',
    b: '张三',
    d: '2025-3-7 8:30:00',
    e: '下次注意全部发票',
    status: '4',
    // 0-同意 / 1-不同意
    code: '0',
  },
  {
    a: '生产部',
    b: '张三',
    d: '2025-3-7 10:30:00',
    status: '4',
    // 0-同意 / 1-不同意
    code: '0',
  },
  {
    a: '财务部',
    b: '张三',
    c: '',
    d: '',
    status: '1',
    // 0-同意 / 1-不同意
    code: '',
  },
  {
    a: '总经理',
    b: '张三',
    c: '',
    d: '',
    status: '1',
    // 0-同意 / 1-不同意
    code: '',
  },
  {
    a: '采购部',
    b: '张三',
    d: '2025-3-7 8:30:00',
    e: '下次注意全部发票',
    status: '4',
    // 0-同意 / 1-不同意
    code: '0',
  },
  {
    a: '生产部',
    b: '张三',
    d: '2025-3-7 10:30:00',
    status: '4',
    // 0-同意 / 1-不同意
    code: '0',
  },
  {
    a: '财务部',
    b: '张三',
    c: '',
    d: '',
    status: '1',
    // 0-同意 / 1-不同意
    code: '',
  },
  {
    a: '采购部',
    b: '张三',
    d: '2025-3-7 8:30:00',
    e: '下次注意全部发票',
    status: '4',
    // 0-同意 / 1-不同意
    code: '0',
  },
  {
    a: '生产部',
    b: '张三',
    d: '2025-3-7 10:30:00',
    status: '4',
    // 0-同意 / 1-不同意
    code: '0',
  },
  {
    a: '财务部',
    b: '张三',
    c: '',
    d: '',
    status: '1',
    // 0-同意 / 1-不同意
    code: '',
  },
]);


</script>

<style lang="less" scoped>
@import './index.less';

.detail-base {
  display: flex;
  height: 100%;

  .container-base-margin-top {
    margin-right: 20px;
    height: 100%;
    // overflow: auto;

    &:last-child {
      margin-right: 0px;


    }
  }

  .card-bottom {
    .container-base-margin-top {

      ::v-deep .t-card__body {
        box-sizing: border-box !important;
        height: 1000px !important;
        overflow: auto !important;
      }
    }

  }


}


.flex {
  display: flex;
}

.container-base-margin-top {
  position: relative;

  .examine-box {
    width: 400px;

    .examine-top,
    .examine-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .examine-bottom {
      margin: 20px 0;
    }
  }

  .titlenav {
    width: 4px;
    height: 18px;
    background: #409eff;
    margin-right: 10px;
    position: absolute;
    top: 18px;
    left: 12px;
  }



}
</style>
