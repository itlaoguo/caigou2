import { PrimaryTableCol } from "tdesign-vue-next";

export const COLUMN: PrimaryTableCol[] = [
    {
        align: 'center',
        colKey: 'index',
        title: '序号',
        width: 80,
        fixed: 'left',
    },
    {
        align: 'center',
        title: "区域",
        colKey: 'name',
    },
    {
        title: "用户数",
        align: 'center',
        colKey: 'value',
    },
    {
        title: "占比",
        align: 'center',
        colKey: 'percent',
    },
];
