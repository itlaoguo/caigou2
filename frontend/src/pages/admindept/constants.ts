import type { FormRule, PrimaryTableCol, UploadFile, } from 'tdesign-vue-next';
import { t } from '@/locales';


export const FORM_RULES: Record<string, FormRule[]> = {
  name: [{ required: true, message: '请输入合同名称', type: 'error' }],
  type: [{ required: true, message: '请选择合同类型', type: 'error' }],
  payment: [{ required: true, message: '请选择合同收付类型', type: 'error' }],
  amount: [{ required: true, message: '请输入合同金额', type: 'error' }],
  partyA: [{ required: true, message: '请选择甲方', type: 'error' }],
  partyB: [{ required: true, message: '请选择乙方', type: 'error' }],
  signDate: [{ required: true, message: '请选择日期', type: 'error' }],
  startDate: [{ required: true, message: '请选择日期', type: 'error' }],
  endDate: [{ required: true, message: '请选择日期', type: 'error' }],
};

export const INITIAL_DATA = {
  name: '',
  type: '',
  partyA: '',
  partyB: '',
  signDate: '',
  startDate: '',
  endDate: '',
  payment: '1',
  amount: 0,
  comment: '',
  files: [
  ] as Array<UploadFile>,
};

export const TYPE_OPTIONS_GENDER = [
  { label: '男', value: 0 },
  { label: '女', value: 1 },
];

export const TYPE_OPTIONS_KNOELEDGE = [
  { label: '高中', value: 0 },
  { label: '大专', value: 1 },
  { label: '本科', value: 2 },
  { label: '研究生', value: 3 },
];

export const TYPE_OPTIONS_MARRIAGE = [
  { label: '未婚', value: 0 },
  { label: '已婚', value: 1 },
];

export const TYPE_OPTIONS_STATUS = [
  { label: '在职', value: 0 },
  { label: '离职', value: 1 },
];

export const PARTY_B_OPTIONS = [
  { label: 'Company A', value: '1' },
  { label: 'Company B', value: '2' },
  { label: 'Company C', value: '3' },
];

// 导出一个函数，用于对数据进行筛选和排序
type FilterConfig<T> = {
  status?: (string | number)[]; // 支持多状态筛选
  [key: string]: any; // 允许扩展其他筛选条件
};

export function TABLE_SORT_FILTERFF<T extends {
  [x: string]: any; status: any
}>(
  data: T[],
  filters: FilterConfig<T>
): T[] {
  // 条件筛选
  const filtered = data.filter((item) => {

    console.log(item);

    if (!filters.ff?.length) return true;
    return filters.ff.includes(item.ff);
  });
  console.log(filtered);


  // 按筛选顺序排序
  if (filters.status?.length) {
    filtered.sort((a, b) => {
      const indexA = filters.status.indexOf(a.status);
      const indexB = filters.status.indexOf(b.status);
      return indexA - indexB;
    });
  }

  return filtered;
}


// 紧急联系人
export const COLUMNSExamineData: PrimaryTableCol[] = [
  {
    align: 'center',
    colKey: 'index',
    title: '序号',
    width: 50,
    fixed: 'left',
  },
  {
    align: 'center',
    title: "姓名",
    width: 160,
    colKey: 'a',
  },
  {
    title: "关系",
    width: 280,
    align: 'center',
    colKey: 'b',
  },
  {
    title: "电话",
    width: 280,
    align: 'center',
    colKey: 'c',
  },
];

// 奖惩记录
export const COLUMNSRewardsData: PrimaryTableCol[] = [
  {
    align: 'center',
    colKey: 'index',
    title: '序号',
    width: 50,
    fixed: 'left',
  },
  {
    align: 'center',
    title: "类型",
    width: 160,
    colKey: 'a',
  },
  {
    title: "说明",
    width: 280,
    align: 'center',
    colKey: 'b',
  },
  {
    title: "资料",
    width: 280,
    align: 'center',
    colKey: 'cc',
  },
];

// 入/离职记录
// 奖惩记录
export const COLUMNSExititData: PrimaryTableCol[] = [
  {
    align: 'center',
    colKey: 'index',
    title: '序号',
    width: 80,
    fixed: 'left',
  },
  {
    align: 'center',
    title: "入/离职状态",
    colKey: 'aa',
  },
  {
    title: "入职时间",
    align: 'center',
    colKey: 'bb',
  },
  {
    title: "操作人",
    align: 'center',
    colKey: 'cc',
  },
  {
    title: "操作时间",
    align: 'center',
    colKey: 'dd',
  },
];