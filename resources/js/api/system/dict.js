import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/api/dict/list',
    method: 'get',
    params: {data},
  })
}

export function getDataList(data) {
  return request({
    url: '/api/dict/dataList',
    method: 'get',
    params: {data},
  })
}

export function add(data) {
  return request({
    url: '/api/dict/add',
    method: 'post',
    data: {data},
  })
}

export function edit(data) {
  return request({
    url: '/api/dict/edit',
    method: 'post',
    data: {data},
  })
}

export function addData(data) {
  return request({
    url: '/api/dict/addData',
    method: 'post',
    data: {data},
  })
}

export function editData(data) {
  return request({
    url: '/api/dict/editData',
    method: 'post',
    data: {data},
  })
}

export function remove(data) {
  return request({
    url: '/api/dict/remove',
    method: 'post',
    data: {data},
  })
}
export function removeData(data) {
  return request({
    url: '/api/dict/removeData',
    method: 'post',
    data: {data},
  })
}
