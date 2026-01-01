import type { ListResult, CustomerModel, searchParameter, SearchRelatedParameter } from '@/api/model/customerModel';
import { request } from '@/utils/request';

export function getList(parameters: searchParameter) {
  return request.get<ListResult>({
    url: '/customer/customer',
    params: parameters
  });
}

export function getInfo(id: number) {
  return request.get({
    url: `/customer/customer/${id}`,
  });
}


export function getPlatforms(params: SearchRelatedParameter) {
  return request.get({
    url: `/customer/customer/platform`,
    params
  });
}
export function getOrders(params: SearchRelatedParameter) {
  return request.get({
    url: `/customer/customer/order`,
    params
  });
}

export function getPurchasedProducts(params: SearchRelatedParameter) {
  return request.get({
    url: `/customer/customer/purchased`,
    params
  });
}

export function getShippingAddresses(params: SearchRelatedParameter) {
  return request.get({
    url: `/customer/customer/shipping_address`,
    params
  });
}

// 获取用户基本统计信息
export function getBaseStatistic() {
  return request.get({
    url: `/customer/statistic`,
  });
}

// 国外统计-分页
export function getCountry(data: any) {
  return request.get({
    url: `/customer/statistic/country`,
    params: data
  });
}

// 国内统计-分页
export function getProvince(data: any) {
  return request.get({
    url: `/customer/statistic/province`,
    params: data
  });
}

// 按国家统计-饼图
export function getAllCountry() {
  return request.get({
    url: `/customer/statistic/all_country`,
  });
}

// 国内省份统计-饼图
export function getAllProvince() {
  return request.get({
    url: `/customer/statistic/all_province`,
  });
}

// 平台统计-趋势图
export function getPlatform() {
  return request.get({
    url: `/customer/statistic/platform`,
  });
}

// 性别-趋势图
export function getGender() {
  return request.get({
    url: `/customer/statistic/gender`,
  });
}

// 性别统计-饼图
export function getAllGender() {
  return request.get({
    url: `/customer/statistic/all_gender`,
  });
}




