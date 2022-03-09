import Layout from '@/layout'

const systemRouter = {
  path: '/system',
  component: Layout,
  redirect: '/system/department',
  alwaysShow: true, // will always show the root menu
  name: 'System',
  meta: {
    title: '系统管理',
    icon: 'system',
    roles: ['system'] // you can set roles in root nav
  },
  children: [
    {
      path: 'department',
      component: () => import('@/views/system/department'),
      name: 'DepartmentManage',
      meta: {
        title: '部门管理',
        icon: 'department',
        roles: ['system-department'] // or you can only set roles in sub nav
      }
    },
    {
      path: 'user',
      component: () => import('@/views/system/user'),
      name: 'UserManage',
      meta: {
        title: '用户管理',
        icon: 'user',
        roles: ['system-user'] // or you can only set roles in sub nav
      }
    },
    {
      path: 'menu',
      component: () => import('@/views/system/menu'),
      name: 'MenuManage',
      meta: {
        title: '权限管理',
        icon: 'menu',
        roles: ['system-menu'] // or you can only set roles in sub nav
      }
    },
  ]
};

export default systemRouter
