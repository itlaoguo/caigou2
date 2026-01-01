import { PrimaryTableCol } from "tdesign-vue-next";
import { t } from '@/locales';


// 主体
export const TYPE_OPTIONS_COMPANY = [
  { label: '翼飞智能', value: 1 },
  { label: '翼飞教育', value: 2 },
  { label: '翼飞传媒', value: 3 },
];

// 审核状态
export const TYPE_OPTIONS_EXAMINE = [
  { label: '待提交', value: 0 },
  { label: "待审核", value: 1 },
  { label: "待审核", value: 2 },
];

// 工资推送
export const TYPE_OPTIONS_SALARY = [
  { label: "已推送", value: 0 },
  { label: "未推送", value: 1 },
];


// 添加-导入工资表单
export const COLUMNS: PrimaryTableCol[] = [
  {
    align: 'center',
    colKey: 'index',
    title: '序号',
    width: 80,
    fixed: 'left',
  },
  {
    align: 'center',
    title: "部门",
    colKey: 'a',
  },
  {
    align: 'center',
    title: "正常出勤工资",
    width: 130,
    colKey: 'b',
  },
  {
    align: 'center',
    title: "岗位工资",
    colKey: 'c',
  },
  {
    align: 'center',
    title: "工龄奖",
    colKey: 'd',
  },
  {
    align: 'center',
    title: "其他补贴",
    colKey: 'e',
  },
  {
    align: 'center',
    title: "搬迁补贴",
    colKey: 'f',
  },
  {
    align: 'center',
    title: "全勤奖",
    colKey: 'g',
  },
  {
    align: 'center',
    title: "津贴",
    colKey: 'h',
  },
  {
    align: 'center',
    title: "加班补贴",
    colKey: 'i',
  },
  {
    align: 'center',
    title: "合计",
    colKey: 'j',
  },
  {
    align: 'center',
    title: "加班金额",
    colKey: 'k',
  },
  {
    align: 'center',
    title: "单月奖金",
    colKey: 'l',
  },
  {
    align: 'center',
    title: "当月应发工资",
    width: 130,
    colKey: 'm',
  },
  {
    align: 'center',
    title: "扣公积金",
    colKey: 'n',
  },
  {
    align: 'center',
    title: "补交社保",
    colKey: 'o',
  },
  {
    align: 'center',
    title: "扣社保",
    colKey: 'p',
  },
  {
    align: 'center',
    title: "应税工资",
    colKey: 'q',
  },
  {
    align: 'center',
    title: "水电扣除",
    colKey: 'r',
  },
  {
    align: 'center',
    title: "其他扣款",
    colKey: 's',
  },
  {
    align: 'center',
    title: "扣税款",
    colKey: 't',
  },
  {
    align: 'center',
    title: "实发工资",
    colKey: 'u',
  },
  {
    align: 'center',
    title: "备注",
    colKey: 'v',
  },
  {
    align: 'center',
    fixed: 'right',
    width: 150,
    colKey: 'op',
    title: t('components.commonTable.operation'),
  },
];