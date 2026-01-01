import { request } from '@/utils/request';


// 查询 - 列表
export function MenuList(data: any) {
  return request.get({
    url: '/permissions/permissions',
    params: data
  });
}

// 查询 - 添加
export function MenuListAdd(data: any) {
  return request.post({
    url: '/permissions/permissions',
    data
  });
}


// 查询 - 查询单条
export function MenuListDetail(id: any) {
  return request.get({
    url: `/permissions/permissions/${id}`,
  });
}

// 查询 - 删除
export function MenuListDeleter(id: any) {
  return request.delete({
    url: `/permissions/permissions/${id}`,
  });
}

// 查询 - 修改
export function MenuListAlter(data: any, id: any) {
  return request.put({
    url: `/permissions/permissions/${id}`,
    data
  });
}

