import type { ListResult, OrderModel, searchParameter } from '@/api/model/orderModel';
import { request } from '@/utils/request';

export function getList(parameters: searchParameter) {
  return request.get<ListResult>({
    url: '/order/order',
    params: parameters
  });
}

export function getInfo(id: number) {
  return request.get({
    url: `/order/order/${id}`,
  });
}

