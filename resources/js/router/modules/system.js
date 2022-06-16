import Layout from '@/layout'

const systemRouter = {
  path: '/system',
  component: Layout,
  redirect: '/system/department',
  alwaysShow: true, // will always show the root menu
  name: 'System',
  meta: {title: '系统管理', icon: 'system'},
  children: [
    {
      path: 'department',
      component: () => import('@/views/system/department'),
      name: 'DepartmentManage',
      meta: {
        title: '部门管理',
        icon: 'department',
      }
    },
    {
      path: 'user',
      component: () => import('@/views/system/user'),
      name: 'UserManage',
      meta: {
        title: '用户管理',
        icon: 'user',
      }
    },
    {
      path: 'permission',
      component: () => import('@/views/system/permission/index'),
      name: 'PermissionManage',
      alwaysShow: true,
      meta: {
        title: '权限管理',
        icon: 'permission',
      },
      children: [
        {
          path: 'menu',
          component: () => import('@/views/system/permission/menu'),
          name: 'PermissionMenuManage',
          meta: {
            title: '菜单权限',
            icon: 'menu',
          }
        },
        {
          path: 'data',
          hidden: true,
          component: () => import('@/views/system/permission/data'),
          name: 'PermissionDataManage',
          meta: {
            title: '数据权限',
            icon: 'data',
          }
        },
      ]
    },
    {
      path: 'role',
      component: () => import('@/views/system/role'),
      name: 'RoleManage',
      meta: {
        title: '角色管理',
        icon: 'role',
      }
    },
    {
      path: 'dict',
      component: () => import('@/views/system/dict'),
      name: 'DictManage',
      meta: {
        title: '字典管理',
        icon: 'dict',
      },
    },
    {
      path: 'actionLog',
      component: () => import('@/views/system/action-log'),
      name: 'ActionLogManage',
      meta: {
        title: '日志管理',
        icon: 'log',
      },
    },
    {
      path: 'message',
      component: () => import('@/views/system/message/index'),
      redirect: '/system/message/list',
      name: 'MessageManage',
      meta: {
        title: '消息通知',
        icon: 'notify'
      },
      children: [
        {
          path: 'create',
          component: () => import('@/views/system/message/create'),
          name: 'MessageManageAdd',
          meta: { title: '新增消息', icon: 'edit' }
        },
        {
          path: 'edit/:id(\\d+)',
          component: () => import('@/views/system/message/edit'),
          name: 'MessageManageEdit',
          meta: { title: '编辑消息', noCache: true, activeMenu: '/system/message/list' },
          hidden: true
        },
        {
          path: 'view/:id(\\d+)',
          component: () => import('@/views/system/message/view'),
          name: 'ViewMessage',
          meta: { title: '查看消息', noCache: true, activeMenu: '/system/message/list' },
          hidden: true
        },
        {
          path: 'list',
          component: () => import('@/views/system/message/list'),
          name: 'MessageManageList',
          meta: { title: '消息列表', icon: 'list' }
        }
      ]
    },
    {
      path: 'setting',
      component: () => import('@/views/system/setting'),
      name: 'Setting',
      meta: {
        title: '系统设置',
        icon: 'setting',
      },
    },
  ]
};

export default systemRouter
