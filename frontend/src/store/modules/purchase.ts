import { defineStore } from 'pinia';

export const usePurchaseStore = defineStore('purchase', {
  state: () => ({
    // token: 'main_token', // 默认token不走权限
    token: '', // 默认token不走权限
    userInfo: { ...InitUserInfo },
  }),
  getters: {
    roles: (state) => {
      return state.userInfo?.roles;
    },
  },
  actions: {
    async login(userInfo: Record<string, unknown>) {
      console.log("登录信息=>", userInfo);
      const res = await getlogin(userInfo);
      console.log("res===>", res.data.token);
      this.token = res.data.token;
      MessagePlugin.success('登录成功');
    },
    async getUserInfo() {
      const mockRemoteUserInfo = async (token: string) => {
        if (token === 'main_token') {
          return {
            name: 'Tencent',
            roles: ['all'], // 前端权限模型使用 如果使用请配置modules/permission-fe.ts使用
          };
        }
        return {
          name: 'td_dev',
          roles: ['UserIndex', 'DashboardBase', 'login'], // 前端权限模型使用 如果使用请配置modules/permission-fe.ts使用
        };
      };

      const res = await mockRemoteUserInfo(this.token);
      console.log("用户信息=>", res);
      this.userInfo = res;
    },
    async logout() {
      this.token = '';
      this.userInfo = { ...InitUserInfo };
    },
  },
  persist: {
    afterRestore: () => {
      const permissionStore = usePermissionStore();
      permissionStore.initRoutes();
    },
    key: 'user',
    paths: ['token'],
  },
});