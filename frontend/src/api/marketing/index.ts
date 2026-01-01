import type { ListResult, marketingModel, searchParameter } from '@/api/model/marketingModel';
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

// 国外统计-分页
export function getCountry(data: any) {
  return request.get({
    url: `/order/statistic/country`,
    params: data
  });
}

// 国内统计-分页
export function getProvince(data: any) {
  return request.get({
    url: `/order/statistic/province`,
    params: data
  });
}

// 按国家统计
export function getAllCountry() {
  return request.get({
    url: `/order/statistic/all_country`,
  });
}

// 国内统计
export function getAllProvince() {
  return request.get({
    url: `/order/statistic/all_province`,
  });
}

// 平台统计-折线图
export function getPlatform() {
  return request.get({
    url: `/order/statistic/platform`,
  });
}

// 头部统计
export function getBaseStatistic() {
  return request.get({
    url: `/order/statistic`,
  });
}
