import request from '@/utils/request'

export function getList() {
  return request({
    url: '/api/department/list',
    method: 'get',
  })
}

export function getDepartmentHasUser(data) {
  return request({
    url: '/api/department/user',
    method: 'get',
    params: {data},
  })
}

export function add(data) {
  return request({
    url: '/api/department/add',
    method: 'post',
    data: {data},
  })
}

export function edit(data) {
  return request({
    url: '/api/department/edit',
    method: 'post',
    data: {data},
  })
}

export function remove(data) {
  return request({
    url: '/api/department/remove',
    method: 'post',
    data: {data},
  })
}
