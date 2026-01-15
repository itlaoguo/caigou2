import type { ListResult, PurchaseOrderModel, searchParameter } from '@/api/model/purchaseModel';
import { request } from '@/utils/request';

export function add(data: any) {
  return request.post({
    url: '/purchase/order',
    data
  });
}


export function del(id: any) {
  return request.delete({
    url: `/purchase/order/${id}`,
  });
}


export function update(data: any, id: any) {
  return request.put({
    url: `/purchase/order/${id}`,
    data
  });
}

export function list(parameters: searchParameter) {
  return request.get<ListResult>({
    url: '/purchase/order',
    params: parameters
  });
}

export function info(id: number) {
  return request.get<PurchaseOrderModel>({
    url: `/purchase/order/${id}`,
  });
}




