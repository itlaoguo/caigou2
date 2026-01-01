import { t } from '@/locales';
import { PrimaryTableCol } from 'tdesign-vue-next';

// 列表
export const COLUMNS: PrimaryTableCol[] = [
  {
    title: '用户角色',
    fixed: 'left',
    width: 160,
    ellipsis: true,
    align: 'left',
    colKey: 'role_name',
  },
  {
    align: 'center',
    title: '用户标识',
    width: 160,
    ellipsis: true,
    colKey: 'identify',
  },
  {
    align: 'center',
    title: '角色描述',
    width: 160,
    ellipsis: true,
    colKey: 'description',
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
  parent_id: number
  description: string,
  identify: string,
  role_name: string,
  permissions?: any
}

// 搜索表单
export interface SearchFormData {
  role_name: string;
}
