import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/api/menu/list',
    method: 'get',
    params: {data},
  })
}

export function getMenuMap(data) {
  return request({
    url: '/api/menu/menuMap',
    method: 'get',
    params: {data},
  })
}

export function getOptions() {
  return request({
    url: '/api/menu/options',
    method: 'get',
  })
}

export function add(data) {
  return request({
    url: '/api/menu/add',
    method: 'post',
    data: {data},
  })
}

export function edit(data) {
  return request({
    url: '/api/menu/edit',
    method: 'post',
    data: {data},
  })
}

export function remove(data) {
  return request({
    url: '/api/menu/remove',
    method: 'post',
    data: {data},
  })
}
