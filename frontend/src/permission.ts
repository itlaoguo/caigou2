import 'nprogress/nprogress.css'; // progress bar style

import NProgress from 'nprogress'; // progress bar
import { MessagePlugin } from 'tdesign-vue-next';
import { RouteRecordRaw } from 'vue-router';

import router from '@/router';
import { getPermissionStore, useUserStore } from '@/store';
import { PAGE_NOT_FOUND_ROUTE } from '@/utils/route/constant';

NProgress.configure({ showSpinner: false });

router.beforeEach(async (to, from, next) => {
  NProgress.start();

  // 白名单
  const permissionStore = getPermissionStore();
  const { whiteListRouters } = permissionStore;

  // 获取登录类
  const userStore = useUserStore();

  // 有token
  if (userStore.token) {

    try {
      // 获取用户信息
      // await userStore.getUserInfo();
      const { asyncRoutes } = permissionStore;

      // 调用接口-获取路由数据
      if (asyncRoutes && asyncRoutes.length === 0) {
        // 模拟接口调用数据
        const routeList = await permissionStore.buildAsyncRoutes();
        console.log(routeList,'------routeList-------');
        routeList.forEach((item: RouteRecordRaw) => {
          router.addRoute(item);
        });

        if (to.name === PAGE_NOT_FOUND_ROUTE.name) {
          // 动态添加路由后，此处应当重定向到fullPath，否则会加载404页面内容
          next({ path: to.fullPath, replace: true, query: to.query });
        } else {
          const redirect = decodeURIComponent((from.query.redirect || to.path) as string);
          next(to.path === "/dashboard/homes" ? { ...to, replace: true } : { path: redirect, query: to.query });
          return;
        }
      }
      // 直接放行，让 Vue Router 自己处理路由匹配
      // 如果路由不存在，会自动匹配到404路由（/:w+）
      next();
    } catch (error) {
      MessagePlugin.error(error.message);
      next({
        path: '/login',
        query: { redirect: encodeURIComponent(to.fullPath) },
      });
      NProgress.done();
    }
  } else {
    // 没有token
    // 白名单放行
    // 不是白名单-是否是登录页（防止是token过期回到登录页）
    if (whiteListRouters.indexOf(to.path) !== -1) {
      next();
    } else {
      next({
        path: '/login',
        query: { redirect: encodeURIComponent(to.fullPath) },
      });
    }
    NProgress.done();
  }
});

router.afterEach((to) => {
  if (to.path === '/login') {
    const userStore = useUserStore();
    const permissionStore = getPermissionStore();

    userStore.logout();
    permissionStore.restoreRoutes();
  }
  NProgress.done();
});
