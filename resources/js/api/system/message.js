import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/api/message/list',
    method: 'get',
    params: {data},
  })
}


export function add(data) {
  return request({
    url: '/api/message/add',
    method: 'post',
    data: {data},
  })
}

export function edit(data) {
  return request({
    url: '/api/message/edit',
    method: 'post',
    data: {data},
  })
}

export function switchHidden(data) {
  return request({
    url: '/api/message/switchHidden',
    method: 'post',
    data: {data},
  })
}
