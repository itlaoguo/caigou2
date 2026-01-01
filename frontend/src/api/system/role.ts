import { request } from '@/utils/request';


// 查询 - 列表
export function RoleList(data: any) {
  return request.get({
    url: '/permissions/roles',
    params: data
  });
}

// 查询 - 添加
export function RoleListAdd(data: any) {
  return request.post({
    url: '/permissions/roles',
    data
  });
}


// 查询 - 查询单条
export function RoleListDetail(id: any) {
  return request.get({
    url: `/permissions/roles/${id}`,
  });
}

// 查询 - 删除
export function RoleListDeleter(id: any) {
  return request.delete({
    url: `/permissions/roles/${id}`,
  });
}

// 查询 - 修改
export function RoleListAlter(data: any, id: any) {
  return request.put({
    url: `/permissions/roles/${id}`,
    data
  });
}

