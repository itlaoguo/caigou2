import { DashboardIcon } from 'tdesign-icons-vue-next';
import { shallowRef } from 'vue';

import Layout from '@/layouts/index.vue';

export default [
  {
    path: '/dashboard',
    component: Layout,
    redirect: '/dashboard/homes',
    name: 'dashboard',
    meta: {
      title: {
        zh_CN: '首页',
        en_US: 'Dashboard',
      },
      icon: shallowRef(DashboardIcon),
      // orderNo: 0,
    },
    children: [
      // 新增首页
      {
        path: 'homes',
        name: 'Dashboardhomes',
        component: () => import('@/pages/dashboard/homes/index.vue'),
        meta: {
          hidden: true,
          title: {
            zh_CN: '导航页',
            en_US: 'changehome',
          },
        },
      },
      {
        path: 'home',
        name: 'Dashboardhome',
        component: () => import('@/pages/dashboard/home/index.vue'),
        meta: {
          hidden: true,
          title: {
            zh_CN: '中间页',
            en_US: 'changehome',
          },
        },
      },
      {
        path: 'base',
        name: 'DashboardBase',
        component: () => import('@/pages/dashboard/base/index.vue'),
        meta: {
          hidden: true,
          title: {
            zh_CN: '概览仪表盘',
            en_US: 'Overview',
          },
        },
      },
      {
        path: 'detail',
        name: 'DashboardDetail',
        component: () => import('@/pages/dashboard/detail/index.vue'),
        meta: {
          hidden: true,
          title: {
            zh_CN: '统计报表',
            en_US: 'Dashboard Detail',
          },
        },
      },
    ],
  },
];
