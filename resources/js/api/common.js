import request from '@/utils/request'
import {Message} from 'element-ui'

/**
 * 文件上传
 * @param {object} fileObj
 * @param {object} extraData
 */
export function fileUpload(fileObj, extraData) {
  const params = new FormData();
  params.append('file', fileObj.file);
  Object.keys(extraData).forEach(function (key) {
    params.append(key, extraData[key])
  });

  return request({
    method: 'post',
    headers: {'Content-Type': 'Multipart/form-data'},
    url: '/api/file/upload',
    data: params
  })
}

export async function exportExcel(uri, data) {
  const ret = await request({
    url: uri,
    method: 'get',
    params: {data},
  });
  if (ret.code > 0) {
    Message.error(ret.msg);
  } else {
    const filepath = ret.data?.filepath || '';
    window.open(filepath, '_blank');
  }
}
