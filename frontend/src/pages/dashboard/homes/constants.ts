interface QuickData {
  // 名称
  name: string,
  // 图标
  icon: string,
  // 路由
  url: string
}

// 快捷操作-菜单
export const HOME_QUICK_DATA: Array<QuickData> = [
  {
    name: '添加产品',
    icon: 'add',
    url: '/project/create',
  },
  {
    name: '查看审批',
    icon: 'add',
    url: '/task/create',
  },
  {
    name: '个人信息',
    icon: 'add',
    url: '/approval/create',
  },
  {
    name: '客户信息',
    icon: 'add',
    url: '/project/create',
  },
  {
    name: '页面管理',
    icon: 'add',
    url: '/task/create',
  },
  {
    name: '菜单管理',
    icon: 'add',
    url: '/approval/create',
  },
  {
    name: '角色管理',
    icon: 'add',
    url: '/project/create',
  }
]