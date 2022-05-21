<template>
  <div style="width: 100%; height: 100%;">
    <el-upload
      class="upload-container"
      action="eric"
      :http-request="(val)=>{doUpload(val)}"
      :show-file-list="false"
      list-type="picture-card"
    >
      <div v-if="url" style="width: 100%; height: 100%;" @click.stop>
        <el-image
          ref="uploadImage"
          fit="fill"
          style="height: 100%;"
          :src="url"
          :preview-src-list="[url]"
        />
        <div class="modal">
          <i class="el-icon-zoom-in" @click="previewImage"/>
          <i class="el-icon-delete" @click="deleteImage"/>
        </div>
      </div>
      <i v-else :class=" loading ? 'el-icon-loading': 'el-icon-plus'"/>
    </el-upload>
  </div>
</template>

<script>
  import {fileUpload} from '@/api/common'

  export default {
    name: "SingleImage1",

    props: {
      imageUrl: {
        type: String,
        default: '',
      },
      source: {
        type: String,
        default: '',
      }
    },

    data() {
      return {
        loading: false,
        url: '',
      }
    },
    watch: {
      imageUrl: function(val) {
        this.url = val;
      }
    },

    methods: {
      previewImage() {
        this.$refs.uploadImage.showViewer = true
      },
      deleteImage() {
        this.url = '';
        this.$emit("uploadSuccess", '')
      },

      async doUpload(file) {
        this.loading = true;
        const ret = await fileUpload(file, {'source': this.source});
        if (ret.code > 0) {
          this.$message.error(ret.msg);
        } else {
          this.$message.success('上传成功！');
          this.url = ret.data.path;
          this.$emit("uploadSuccess", this.url)
        }
        this.loading = false
      },
    }
  }
</script>

<style>
  .el-upload--picture-card {
    height: 198px;
    line-height: 198px;
    border-radius: 5px 5px 0 0;
  }
</style>
<style lang="less">
  .el-upload {
    position: relative !important;
    display: flex;
    align-items: center;
    justify-content: center;

    .modal {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;

      i {
        color: #fff;
        font-size: 16px;
        display: none;
        margin: 0 4px;
      }

      &:hover {
        background: rgba(0, 0, 0, .5);

        i {
          display: inline-block;
        }
      }

    }
  }
</style>
