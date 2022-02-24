import axios from "@/utils/axios";

const FilesKey = 'cynic_img_files';
const EXP = 30;

export function pushOne(id, objectId) {
  const curTime = new Date().getTime();
  let fileIds = [];
  let exist = getAll();
  if (exist !== null) {
    fileIds = exist
  }
  fileIds.push({
    id,
    objectId,
    curTime
  });
  localStorage.setItem(FilesKey, JSON.stringify({fileIds: fileIds, time: curTime}))
}

export function getAll() {
  const data = localStorage.getItem(FilesKey);
  const dataObj = JSON.parse(data);
  if (dataObj === null || new Date().getTime() - dataObj.time > EXP * 864e+5) {
    return null
  } else {
    return dataObj.fileIds
  }
}

export function removeAll() {
  localStorage.removeItem(FilesKey)
}

export function syncFiles() {
  const fileIds = getAll();
  if (fileIds !== null) {
    return new Promise((resolve) => {
      axios.post("/api/file/sync", {files: fileIds});
      removeAll();
      resolve()
    })
  }
}
