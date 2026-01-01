import { t } from '@/locales';
import { PrimaryTableCol } from "tdesign-vue-next";

// 列表
export const COLUMNS: PrimaryTableCol[] = [
  {
    title: '菜单名称',
    fixed: 'left',
    width: 280,
    ellipsis: true,
    align: 'left',
    colKey: 'permission_name',
  },
  {
    align: 'center', title: '图标', colKey: 'icon', width: 160,
  },
  {
    align: 'center',
    title: '排序',
    width: 160,
    ellipsis: true,
    colKey: 'sort',
  },
  {
    align: 'center',
    title: '权限标识',
    width: 160,
    ellipsis: true,
    colKey: 'permission_mark',
  },
  {
    align: 'center',
    title: '组件路径',
    width: 160,
    ellipsis: true,
    colKey: 'component',
  },
  {
    align: 'center',
    title: '组件名称',
    width: 160,
    ellipsis: true,
    colKey: 'route',
  },
  {
    align: 'center',
    title: '状态',
    width: 160,
    ellipsis: true,
    colKey: 'hidden',
  },
  {
    align: 'center',
    title: '类型',
    width: 160,
    ellipsis: true,
    colKey: 'type',
  },
  {
    align: 'center',
    fixed: 'right',
    width: 160,
    colKey: 'op',
    title: t('components.commonTable.operation'),
  },
];

// 添加表单类型
export interface FormData {
  id?: number,
  permission_name: string,
  route: string,
  // 菜单图标
  icon: string,
  // 权限标识
  permission_mark: string,
  // 组件路径
  component: string,
  // 组件名称
  module: string,
  // 排序
  sort: number | null,
  // 父级id
  parent_id: number,
  // 菜单类型
  type: number,
  // 是否显示
  hidden: number,
}

// 搜索表单
export interface SearchFormData {
  permission_name: string;
}


// 树形下拉框
export interface TreeNode {
  id: string | number;
  name: string;
  children?: TreeNode[];
  // 其他可选字段需声明类型
  status?: number;
  no?: string;
  icon?: string;
  contractType?: string;
  paymentType?: string;
  amount?: string;
}