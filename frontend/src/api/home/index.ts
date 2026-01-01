import type { LineResult, searchForm } from '@/api/model/homeModel';
import { request } from '@/utils/request';

// 订单
export function DashboardList() {
    return request.get({
        url: '/common/dashboard',
    });
}

// 用户
export function getuserList(searchForm: searchForm) {
    return request.get<LineResult>({
        url: '/customer/customer',
        params: searchForm
    });
}