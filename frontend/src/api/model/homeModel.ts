// 返回
export interface LineResult {
    code: number;
    data: Array<LineItemResult>;
    message: string;
}

export interface LineItemResult {
    id: number;
    name: string;
    price: number;
    image: string;
    description: string;
    category: string;
    createdAt: string;
    updatedAt: string;
}

// 查询参数
export interface searchForm {
    data?: string;
    value?: string;
}