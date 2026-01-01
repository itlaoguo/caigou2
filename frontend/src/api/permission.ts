import type { MenuListResult } from '@/api/model/permissionModel';
import { request } from '@/utils/request';

const Api = {
  MenuList: '/get-menu-list-i18n',
};

export function getMenuList() {
  return request.get<MenuListResult>({
    url: Api.MenuList,
  });
}

export function getlogin(data: any) {
  return request.post({
    // url: '/login',
    // data: {
    //   "captchaCode": "999",
    //   "captchaUuid": "999",
    //   "loginDevice": 3,
    //   "loginName": "15218430349",
    //   "password": "PT8CoO5RMNNH1QHxOGYs7w=="
    // }

    url: '/login',
    data: {
      "username": data.account,
      "password": data.password,
    }
  });
}

// 获取权限
export function getonline() {
  return request.get({
    url: '/user/online',
  });
}

