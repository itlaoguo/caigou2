import { t } from '@/locales';
import { PrimaryTableCol } from "tdesign-vue-next";

// 列表
export const COLUMNS: PrimaryTableCol[] = [
  {
    title: '用户名',
    fixed: 'left',
    width: 160,
    ellipsis: true,
    align: 'left',
    colKey: 'username',
  },

  {
    align: 'center',
    title: '邮箱号码',
    width: 160,
    ellipsis: true,
    colKey: 'email',
  },
  {
    align: 'center',
    title: '状态',
    width: 160,
    ellipsis: true,
    colKey: 'status',
  },
  {
    align: 'center',
    title: '创建时间',
    width: 160,
    ellipsis: true,
    colKey: 'created_at',
  },
  {
    align: 'center',
    fixed: 'right',
    width: 200,
    colKey: 'op',
    title: t('components.commonTable.operation'),
  },
];

// 添加表单
export interface FormData {
  id?: number,
  username: string,
  email: string,
  password: string,
  status: string | number,
  roles: any
}

// 搜索表单
export interface SearchFormData {
  username: string;
  email: string;
  status: number;
}

// 搜索状态
export const CONTRACT_STATUS_OPTIONS = [
  { value: 1, label: '开启' },
  { value: 2, label: '关闭' },
];
