<template>
  <div class="index-container" v-on:paste="handlePaste">
    <el-card>
      <div class="title">
        <h1>Image Upload</h1>
        <div class="note">5 MB max per file. 10 files max per request.</div>
      </div>
      <div class="uploader">
        <el-upload
          ref="upload"
          v-loading="loading"
          class="upload-demo"
          action="eric"
          drag
          :on-change="onChange"
          :file-list="files"
          :auto-upload="false"
          :multiple="true"
          :show-file-list="false"
          list-type="picture">
          <div class="upload-demo-title">
            Click & select files here ...
            <br/>
            or
            <br/>
            Drag &amp; drop files here ...
            <br/>
            or
            <br/>
            Copy &amp; paste screenshots here ...
          </div>
        </el-upload>
      </div>
      <div class="upload-content">
        <div class="upload-content-btn">
          <el-button size="small" type="danger" @click="clearFiles">{{ $t('queryForm.clear') }}</el-button>
          <el-button size="small" type="success" @click="submitUpload" v-no-more-click>{{ $t('queryForm.upload') }}
          </el-button>
        </div>
        <div class="upload-list" v-if="fileList.length > 0">
          <el-table :data="fileData" :height="400" stripe>
            <el-table-column align="center" label="File">
              <template slot-scope="scope">
                <div>
                  <el-image
                    style="width: 100px;height: auto; overflow: hidden;"
                    :src="scope.row.url"
                    :preview-src-list="[scope.row.url]"
                  />
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="name" label="Name">
              <template slot-scope="scope">
                <div style="display: inline-flex;">
                  <div>
                    <el-popconfirm
                      confirm-button-text='Yes'
                      cancel-button-text='No'
                      icon="el-icon-info"
                      icon-color="red"
                      title="remove ?"
                      @confirm="onRemove(scope.row)"
                    >
                      <i slot="reference" class="el-icon-circle-close"
                         style="color: #F56C6C; cursor: pointer;">
                        <span style="color: #409EFF;">{{ scope.row.name }}</span>
                      </i>
                    </el-popconfirm>
                  </div>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="size" label="Size">
              <template slot-scope="scope">
                <span>{{ getFileSize(scope.row.size) }}</span>
                <el-progress v-if="scope.row.hasUpload > 0" :percentage="100"
                             status="success"></el-progress>
                <el-progress v-else :percentage="0"></el-progress>
              </template>
            </el-table-column>
            <el-table-column prop="ret" label="Preview" min-width="400">
              <template slot-scope="scope">
                <p v-if="scope.row.hasUpload > 0">
                  <el-input type="text" v-model="scope.row.ret.link"
                            @focus="$event.currentTarget.select()">
                    <template slot="prepend">Link:</template>
                  </el-input>
                  <el-input type="text" v-model="scope.row.ret.html"
                            @focus="$event.currentTarget.select()">
                    <template slot="prepend">Html:</template>
                  </el-input>
                  <el-input type="text" v-model="scope.row.ret.markdown"
                            @focus="$event.currentTarget.select()">
                    <template slot="prepend">Markdown:</template>
                  </el-input>
                </p>
                <p v-else v-loading="scope.row.uploadLoading"></p>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script>
  import {getFileSize} from '@/utils/index';
  import axios from '@/utils/axios';
  import {mapGetters} from 'vuex';
  import {pushOne, removeAll} from '@/utils/sync';

  export default {
    data() {
      return {
        getFileSize,

        loading: false,
        files: [],
        fileList: []
      };
    },
    computed: {
      ...mapGetters([
        'userInfo',
      ]),
      fileData() {
        const fileData = JSON.parse(JSON.stringify(this.fileList))
        fileData.reverse()
        return fileData
      }
    },
    created() {
    },

    methods: {
      onRemove(row) {
        for (let i = 0; i < this.fileList.length; i++) {
          if (this.fileList[i].uid === row.uid) {
            this.fileList.splice(i, 1);
          }
        }
      },
      onChange(response, fileList) {
        this.fileList = fileList;
        if (this.fileList.length > 10) {
          this.$message.error('10 files max per request.');
          this.fileList.splice(10, 1);
          return;
        }
        this.fileList.forEach((item, index) => {
          if (item.size / 1024 / 1024 > 5) {
            this.$message.error('5 MB max per file.');
            this.fileList.splice(index, 1);
          } else {
            item.hasUpload = item.hasUpload ? item.hasUpload : 0
          }
        })
      },
      submitUpload() {
        let num = 0;
        for (let i = 0; i < this.fileList.length; i++) {
          if (this.fileList[i].hasUpload !== 1) {
            num += 1;
            const params = new FormData();
            params.append('file', this.fileList[i].raw);
            params.append('bucket', 'factory-feedback');
            let config = {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            };
            this.fileList[i].uploadLoading = true;
            this.$set(this.fileList, i, this.fileList[i]);
            axios.post('/api/file/upload', params, config).then((res) => {
              if (res.data.code > 0) {
                this.fileList[i].uploadLoading = false;
                this.$set(this.fileList, i, this.fileList[i]);
                return this.$message.error(res.data.msg);
              } else {
                const id = res.data.data.id;
                const url = res.data.data.path;
                const name = res.data.data.original_name;
                const objectId = res.data.data.object_id;
                this.fileList[i].ret = {
                  link: url,
                  html: '<a href="' + url + '" target="_blank"><img alt="' + name + '" src="' + url + '" ></a>',
                  markdown: '![' + name + '](' + url + ')',
                };
                this.fileList[i].hasUpload = 1;
                this.fileList[i].uploadLoading = false;
                this.$set(this.fileList, i, this.fileList[i]);
                if (!Boolean(this.userInfo.email)) {
                  pushOne(id, objectId);
                }
              }
            }).catch((e) => {
              this.fileList[i].uploadLoading = false;
              this.$set(this.fileList, i, this.fileList[i]);
              return this.$message.error(e.message)
            })
          }
        }
        if (num === 0) {
          return this.$message.warning('没有待上传的文件！');
        }
      },
      clearFiles() {
        this.$confirm('是否清空本次上传记录？', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.$refs.upload.clearFiles();
          this.fileList = [];
          removeAll();
        })
      },
      getInputFocus(event) {
        event.currentTarget.select();
      },
      handlePaste(event) {
        if (this.fileList.length > 10) {
          this.$message.error('10 files max per request.');
          return;
        }
        const items = (event.clipboardData || window.clipboardData).items;
        let file = null;

        if (!items || items.length === 0) {
          this.$message.error("当前浏览器不支持本地");
          return;
        }
        // 搜索剪切板items
        for (let i = 0; i < items.length; i++) {
          if (items[i].type.indexOf("image") !== -1) {
            file = items[i].getAsFile();
            break;
          }
        }
        if (!file) {
          this.$message.error("粘贴内容非图片");
          return;
        }
        // 此时file就是我们的剪切板中的图片对象
        // 如果需要预览，可以执行下面代码
        const reader = new FileReader();
        reader.readAsDataURL(file);

        reader.onload = event => {
          let fileObj = {}
          fileObj.hasUpload = 0;
          fileObj.name = file.name;
          fileObj.percentage = 0;
          fileObj.raw = file;
          fileObj.size = file.size;
          fileObj.status = "ready";
          fileObj.uid = file.uid;
          fileObj.url = `${event.target.result}`;
          this.fileList.push(fileObj)
        };
      },
    },
  };
</script>

