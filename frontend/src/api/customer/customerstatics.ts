import type { ListResult, CustomerModel, searchParameter } from '@/api/model/customerModel';
import { request } from '@/utils/request';

// 
export function getList(parameters: searchParameter) {
  // 发送get请求，获取列表结果
  return request.get<ListResult>({
    // 请求的url
    url: '/customer/customer',
    // 请求的参数
    params: parameters
  });
}
