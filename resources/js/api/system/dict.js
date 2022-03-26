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

export function save(data) {
  return request({
    url: '/api/dict/save',
    method: 'post',
    data: {data},
  })
}

export function saveData(data) {
  return request({
    url: '/api/dict/saveData',
    method: 'post',
    data: {data},
  })
}

export function switchDataStatus(data) {
  return request({
    url: '/api/dict/switchDataStatus',
    method: 'post',
    data: {data},
  })
}
