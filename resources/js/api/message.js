import request from '@/utils/request'

export function getMessage(data) {
  return request({
    url: '/api/message',
    method: 'get',
    params: {data},
  })
}

export function getOptions() {
  return request({
    url: '/api/message/options',
    method: 'get',
  })
}

export function readMessage(data) {
  return request({
    url: '/api/message/read',
    method: 'get',
    data: {data},
  })
}
