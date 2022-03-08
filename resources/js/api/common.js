import request from '@/utils/request'

/**
 * 文件上传
 * @param {object} fileObj
 * @param {object} extraData
 */
export function fileUpload(fileObj, extraData) {
  const params = new FormData();
  params.append('file', fileObj.file);
  Object.keys(extraData).forEach(function(key) {
    params.append(key, extraData[key])
  });

  return request({
    method: 'post',
    headers: { 'Content-Type': 'Multipart/form-data' },
    url: '/api/file/upload',
    data: params
  })
}
