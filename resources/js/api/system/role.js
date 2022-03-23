import request from '@/utils/request'

export function getOptions(data) {
  return request({
    url: '/api/role/options',
    method: 'get',
    params: {data},
  })
}

export function getRoleTree(data) {
  return request({
    url: '/api/role/roleTree',
    method: 'get',
    params: {data},
  })
}

export function getMenuTree(data) {
  return request({
    url: '/api/role/menuTree',
    method: 'get',
    params: {data},
  })
}

export function getDataTree(data) {
  return request({
    url: '/api/role/dataTree',
    method: 'get',
    params: {data},
  })
}

export function getList(data) {
  return request({
    url: '/api/role/list',
    method: 'get',
    params: {data},
  })
}

export function save(data) {
  return request({
    url: '/api/role/save',
    method: 'post',
    data: {data},
  })
}

export function saveRoleHasData(data) {
  return request({
    url: '/api/role/saveRoleHasData',
    method: 'post',
    data: {data},
  })
}

export function saveRoleHasMenu(data) {
  return request({
    url: '/api/role/saveRoleHasMenu',
    method: 'post',
    data: {data},
  })
}

export function switchStatus(data) {
  return request({
    url: '/api/role/switchStatus',
    method: 'post',
    data: {data},
  })
}

export const ROLE_STATUS_OPTION = [
  {
    label: '启用',
    value: 1,
  },
  {
    label: '禁用',
    value: 0,
  }
];
