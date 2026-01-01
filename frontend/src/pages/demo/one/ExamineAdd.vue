<template>
  <div>
    <t-dialog v-model:visible="DictaddVisible" header="审批发起" width="900" @confirm="onConfirmDelete"
      :on-cancel="onCancel" top="5%">
      <div>
        <t-form ref="Form" :rules="rules" :data="FormData" label-align="right" :label-width="100">
          <t-form-item label="合同名称" name="name">
            <t-input v-model="FormData.name" disabled></t-input>
          </t-form-item>
          <t-form-item label="合同类型" name="password">
            <t-input v-model="FormData.name" disabled></t-input>
          </t-form-item>
          <t-form-item label="审批流程" name="Examinedata">
            <div class="examineData-box"
              style="display: flex;flex-direction: column;max-height: 650px;overflow-y: auto;width: 100%;">
              <div>
                <t-button theme="success" @click="onAddexamine" style="margin-bottom: 20px;">
                  <template #icon> <t-icon name="plus" size="18px" /></template>
                  添加审批环节
                </t-button>
              </div>
              <t-steps layout="vertical" readonly>
                <!-- 每一项 -->
                <t-step-item :title="'审批第' + (index + 1) + '步'" v-for="(btn, index) in ExamineData" :key="index">
                  <div style="display: flex;align-items: center;width: 100%;">
                    <div
                      style="display: flex;white-space: nowrap;align-items: center;margin-right: 15px;color: black;width: 100%;">
                      审批人员:
                      &nbsp;&nbsp;&nbsp;<t-select v-model="btn.b" style="display: inline-block"
                        class="form-item-content" :options="CONTRACT_TYPE_OPTIONS" placeholder="审批人员" clearable
                        filterable @blur="handleBlur" @focus="handleFocus" @enter="handleEnter" />
                    </div>
                    <t-icon name="delete" size="20px" style="color: red;cursor: pointer;" @click="onDelete(index)" />
                  </div>
                </t-step-item>
              </t-steps>
            </div>
          </t-form-item>


          <t-form-item label="备注" name="remark">
            <t-textarea v-model="FormData.remark" placeholder="请输入内容" />
          </t-form-item>
        </t-form>
      </div>

      <!-- 删除 -->
      <t-dialog v-model:visible="ConfirmVisible" :header="'确认删除第' + (DeleteValue + 1) + '步审批环节吗'"
        @confirm="onitemDelete" />
    </t-dialog>
  </div>
</template>

<script lang="ts">
export default {
  name: 'examineAdd',
}
</script>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps, SelectProps, FormProps } from 'tdesign-vue-next';
import { t } from '@/locales';
import { CONTRACT_PAYMENT_TYPES, CONTRACT_STATUS, CONTRACT_TYPES } from '@/constants';


// 表单数据
const Form = ref(null);
const FormData = ref({
  name: '惠州翼飞科技',
  password: '项目合同',
  status: '0',
  remark: ''
});

// 配置内容
const CONTRACT_TYPE_OPTIONS = [
  { value: CONTRACT_TYPES.MAIN, label: '秦学海' },
  { value: CONTRACT_TYPES.SUB, label: '李四' },
  { value: CONTRACT_TYPES.SUPPLEMENT, label: '张三' },
];

onMounted(() => {
})

// 岗位人员信息筛选
// 岗位数据
const PostData = ref([
  {
    id: "1",
    name: '董事长-总经办',
    no: '1',
    status: '0',
    children: [
      {
        id: "11",
        name: '会计出纳-财务部',
        no: '1',
        status: '0',

      },
      {
        id: "12",
        name: '副总经理(程总) - 运营中心(程总)',
        no: '2',
        status: '0',

        children: [
          {
            id: "22",
            name: '教学 - 教培部(liliting)',
            no: '2',
            status: '0',

          },
          {
            id: "23",
            name: '校研 - 教培部',
            no: '2',
            status: '0',

          },
        ]
      },
      {
        id: "13",
        name: '总经理 - 管理中心(徐总)',
        no: '3',
        status: '0',
        children: [
          {
            id: "24",
            name: '仓库主管 - 仓储部(XiaoMeiJiao)',
            no: '2',

            status: '0',
          },
          {
            id: "25",
            name: '采购主管 - 采购部(DengLiHui)',
            no: '2',
            status: '0',
          },
          {
            id: "26",
            name: '生产部主管 - 生管部',
            no: '2',
            status: '0',
          },
          {
            id: "27",
            name: '生产部主管 - 生产部',
            no: '2',
            status: '0',
          },
        ]
      },
      {
        id: "14",
        name: '财务主管 - 财务部',
        no: '3',
        status: '0',
      },
      {
        id: "15",
        name: '成本会计 - 财务部',
        no: '3',
        status: '0',
      },
      {
        id: "16",
        name: '内控专员 - 财务部',
        no: '3',
        status: '0',
      },
    ]
  }
]);

// 下拉框筛选
const handleBlur: SelectProps['onBlur'] = ({ value, e }) => {
  console.log('handleBlur: ', value, e);
};
const handleFocus: SelectProps['onFocus'] = ({ value, e }) => {
  console.log('handleFocus: ', value, e);
};
const handleEnter: SelectProps['onEnter'] = ({ value, e, inputValue }) => {
  console.log('handleEnter: ', value, e, inputValue);
};

const dg = (list: any[]): any[] => {
  if (!list) return [];

  return list.map(v => {
    // 创建新对象并拷贝原始属性 ‌:ml-citation{ref="1,2" data="citationList"}
    const newNode = {
      ...v,
      label: v.name,
      children: v.children ? dg(v.children) : null
    };

    // 避免保留原始引用 ‌:ml-citation{ref="2" data="citationList"}
    delete newNode.children; // 移除旧引用
    newNode.children = v.children ? dg(v.children) : null;

    return newNode;
  });
};


// 添加审批环节
const ExamineData = ref([
  {
    a: null,
    b: null
  }
])
const onAddexamine = () => {
  console.log('添加审批环节');
  ExamineData.value.push({
    a: null,
    b: null
  })
};

const DeleteValue = ref(null)

const onDelete = (index: number) => {
  DeleteValue.value = index;
  ConfirmVisible.value = true;
}



// 弹出框
const ConfirmVisible = ref(false);
const onitemDelete = () => {
  console.log('删除');
  ExamineData.value.splice(DeleteValue.value, 1);
  ConfirmVisible.value = false
}


// 表单验证
const rules: FormProps['rules'] = {
  name: [{ required: true, message: '必填', type: 'error' }],
  password: [{ required: true, message: '必填', type: 'error' }],
};

//添加开关
const DictaddVisible = ref(false);

// 重置按钮
const onReset = () => {
  Form.value.reset();
};

const cesgu = ref(1)

// 打开按钮
const onSwticher = () => {
  DictaddVisible.value = !DictaddVisible.value;
}

// 删除-确定
const onConfirmDelete = () => {
  onReset();
  console.log('审批流', ExamineData.value);

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

<style lang="less" scoped>
// .examineData-box::-webkit-scrollbar {
//   width: 10px;
//   height: 10px;
// }

// .examineData-box::-webkit-scrollbar-track {
//   background: #f1f1f1;
//   border-radius: 10px;
// }

// .examineData-box::-webkit-scrollbar-thumb {
//   background: #1f5ef1;
//   border-radius: 10px;
// }

// .examineData-box::-webkit-scrollbar-thumb:hover {
//   background: #1f5ef1;
// }


::v-deep .t-steps--vertical {
  z-index: 99 !important;
  width: 100% !important;

  .t-steps-item {
    // min-height: 200px !important;
    // overflow: auto !important;

    &:first-child {
      .t-icon {
        // background: skyblue !important;
        display: none;
      }
    }
  }

  // .t-steps-item::-webkit-scrollbar {
  //   width: 10px;
  //   height: 10px;
  // }

  // .t-steps-item::-webkit-scrollbar-track {
  //   background: #f1f1f1;
  //   border-radius: 10px;
  // }

  // .t-steps-item::-webkit-scrollbar-thumb {
  //   background: #39ad7a;
  //   border-radius: 10px;
  // }

  // .t-steps-item::-webkit-scrollbar-thumb:hover {
  //   background: #39ad7a;
  // }

  // .t-steps-item:not(:last-child)::before {
  //   background: #1f5ef1 !important;
  //   border-right-width: 3px !important;
  // }

  // background: skyblue !important;
  .t-steps-item__title {
    color: black !important;

  }

  .t-steps-item__icon {
    // background: skyblue !important;

    .t-steps-item__icon--number {
      color: black !important;


    }

  }
}
</style>