import type { ListResult } from '@/api/model/platformModel';
import { request } from '@/utils/request';

export function getAll() {
  return request.get({
    url: '/base/platform/all',
  });
}

export function getList() {
  return request.get<ListResult>({
    url: '/base/platform',
  });
}
