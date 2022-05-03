import request from '@/utils/request'

export function login(data) {
  return request({
    url: '/api/user/login',
    method: 'post',
    data
  })
}

export function getInfo() {
  return request({
    url: '/api/user/info',
    method: 'get',
  })
}

export function getActionList(data) {
  return request({
    url: '/api/user/getActionList',
    method: 'get',
    params: {data}
  })
}

export function getHasInfo() {
  return request({
    url: '/api/user/hasInfo',
    method: 'get',
  })
}

export function logout() {
  return request({
    url: '/api/user/logout',
    method: 'post'
  })
}

export function getDepartmentList() {
  return request({
    url: '/api/user/departmentList',
    method: 'get',
  })
}

export function getList(data) {
  return request({
    url: '/api/user/list',
    method: 'get',
    params: {data}
  })
}

export function add(data) {
  return request({
    url: '/api/user/add',
    method: 'post',
    data: {data},
  })
}

export function edit(data) {
  return request({
    url: '/api/user/edit',
    method: 'post',
    data: {data},
  })
}

export function switchStatus(data) {
  return request({
    url: '/api/user/switchStatus',
    method: 'post',
    data: {data},
  })
}

export function resetPassByUid(data) {
  return request({
    url: '/api/user/resetPassByUid',
    method: 'post',
    data: {data},
  })
}

export function getRoleTree(data) {
  return request({
    url: '/api/user/roleTree',
    method: 'get',
    params: {data},
  })
}

export function saveUserHasRole(data) {
  return request({
    url: '/api/user/saveUserHasRole',
    method: 'post',
    data: {data},
  })
}

export function updatePass(data) {
  return request({
    url: '/api/user/updatePass',
    method: 'post',
    data: {data},
  })
}

export function updateUsername(data) {
  return request({
    url: '/api/user/updateUsername',
    method: 'post',
    data: {data},
  })
}

export function updateAccount(data) {
  return request({
    url: '/api/user/updateAccount',
    method: 'post',
    data: {data},
  })
}

export function updateMobile(data) {
  return request({
    url: '/api/user/updateMobile',
    method: 'post',
    data: {data},
  })
}

export function updateEmail(data) {
  return request({
    url: '/api/user/updateEmail',
    method: 'post',
    data: {data},
  })
}

export function updateAvatar(params) {
  return request({
    url: '/api/user/updateAvatar',
    method: 'post',
    headers: {'Content-Type': 'Multipart/form-data'},
    data: params,
  })
}

export const USER_STATUS_OPTION = [
  {
    label: '启用',
    value: 1,
  },
  {
    label: '禁用',
    value: 0,
  }
];

export const USER_EXPORT_URL = '/api/user/export';
