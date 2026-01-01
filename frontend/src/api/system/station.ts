// 部门管理
import { request } from '@/utils/request';

// 查询 - 列表
export function StationList(data: any) {
  return request.get({
    url: '/permissions/departments',
    params: data
  });
}

// 查询 - 添加
export function StationAdd(data: any) {
  return request.post({
    url: '/permissions/departments',
    data
  });
}


// 查询 - 查询单条
export function StationDetail(id: any) {
  return request.get({
    url: `/permissions/departments/${id}`,
  });
}

// 查询 - 删除
export function StationDeleter(id: any) {
  return request.delete({
    url: `/permissions/departments/${id}`,
  });
}

// 查询 - 修改
export function StationAlter(data: any, id: any) {
  return request.put({
    url: `/permissions/departments/${id}`,
    data
  });
}

