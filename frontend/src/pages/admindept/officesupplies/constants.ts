import { t } from '@/locales';
import { PrimaryTableCol } from "tdesign-vue-next";

// 表格配置 - 物料列表
export const COLUMNS: PrimaryTableCol[] = [
  {
    colKey: 'row-select',
    type: 'multiple',
    width: 50,
    checkProps: { allowUncheck: true },
  },
  {
    align: 'center',
    colKey: 'index',
    title: '序号',
    width: 80,
    fixed: 'left',
  },
  {
    title: '办公用品名称',
    fixed: 'left',
    ellipsis: true,
    align: 'left',
    colKey: 'aa',
  },
  {
    align: 'center',
    title: '编码',
    ellipsis: true,
    colKey: 'bb',
  },
  {
    align: 'center',
    title: '物料图片',
    ellipsis: true,
    colKey: 'cc',
  },
  {
    align: 'center',
    title: '单位',
    ellipsis: true,
    colKey: 'dd',
  },
  {
    align: 'center',
    title: '负责人',
    ellipsis: true,
    colKey: 'ee',
  },
  {
    align: 'center',
    title: '库存',
    ellipsis: true,
    colKey: 'll',
  },
  {
    align: 'center',
    title: '更新人',
    ellipsis: true,
    colKey: 'kk',
  },
  {
    align: 'center',
    title: '更新时间',
    ellipsis: true,
    colKey: 'ff',
  },
  {
    align: 'center',
    fixed: 'right',
    width: 160,
    colKey: 'op',
    title: t('components.commonTable.operation'),
  },
];


// 负责人员
export const CONTRACT_TYPE_OPTIONS = [
  { value: 0, label: '秦学海' },
  { value: 1, label: '李四' },
  { value: 2, label: '张三' },
];

// 单位
export const CONTRACT_TYPE_UNIT = [
  { value: 0, label: '个' },
  { value: 1, label: '根' },
  { value: 2, label: '张' },
  { value: 3, label: '件' },
]