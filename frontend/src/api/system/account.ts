import { request } from '@/utils/request';


// 查询 - 列表
export function AccountList(data: any) {
  return request.get({
    url: '/users',
    params: data
  });
}

// 查询 - 添加
export function AccountAdd(data: any) {
  return request.post({
    url: '/users',
    data
  });
}


// 查询 - 查询单条
export function AccountDetail(id: any) {
  return request.get({
    url: `/users/${id}`,
  });
}

// 查询 - 删除
export function AccountDeleter(id: any) {
  return request.delete({
    url: `/users/${id}`,
  });
}

// 查询 - 修改
export function AccountAlter(data: any, id: any) {
  return request.put({
    url: `/users/${id}`,
    data
  });
}

