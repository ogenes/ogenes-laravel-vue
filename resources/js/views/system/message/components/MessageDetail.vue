<template>
  <div class="message-container">
    <sticky :z-index="10" :class-name="'sub-navbar '+messageParams.status">
      <el-button v-loading="loading" style="margin-left: 10px;" type="primary" @click="submitForm">
        保存
      </el-button>
    </sticky>
    <el-form ref="messageForm" :model="messageParams" :rules="rules" label-position="left" class="message-form">
      <el-row :gutter="5">
        <el-col :span="4">
          <el-form-item prop="catId">
            <el-select v-model="messageParams.catId" size="medium" placeholder="请选择类型" class="message-header">
              <el-option
                v-for="(value, key) in catMap"
                :key="key"
                :label="value"
                :value="key">
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>
        <el-col :span="20">
          <el-form-item prop="title">
            <el-input type="text" placeholder="请输入标题" size="medium" class="message-header"
                      v-model="messageParams.title"/>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item style="margin-bottom: 40px;" prop="banner">
            <div class="message-img">
              <SingleImage1
                :image-url="messageParams.banner"
                source="message-banner"
                @uploadSuccess="uploadBanner"
              />
            </div>
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <div style="height: 198px;">
            <div style="height: 60px; line-height: 60px;">
              <el-form-item prop="top">
                <el-switch
                  active-text="置顶"
                  v-model="messageParams.top"
                >
                </el-switch>
              </el-form-item>
            </div>
            <div style="height: 60px; line-height: 60px;">
              <el-form-item prop="publisher">
                <el-input
                  type="text"
                  placeholder="公布人"
                  size="medium"
                  class="message-item"
                  v-model="messageParams.publisher"
                />
              </el-form-item>
            </div>
            <div style="height: 60px; line-height: 60px;">
              <el-form-item prop="publishTime" style="margin-top: 20px;">
                <el-date-picker
                  v-model="messageParams.publishTime"
                  type="datetime"
                  placeholder="请选择公布时间（默认实时公布）"
                  align="right"
                  clearable
                  format="yyyy-MM-dd HH:mm:ss"
                  value-format="yyyy-MM-dd HH:mm:ss"
                  class="message-item"
                  style="width: 100%;"
                  :picker-options="pickerOptions">
                </el-date-picker>
              </el-form-item>
            </div>
          </div>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="24">
          <el-form-item style="margin-bottom: 40px;" label=" ">
            <el-input v-model="messageParams.desc" :rows="1" type="textarea" class="article-textarea" autosize
                      placeholder="请输入简述"/>
            <span v-show="descLength" class="word-counter">{{ descLength }}words</span>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="24">
          <el-form-item prop="text" style="margin-bottom: 30px;">
            <Tinymce ref="editor" v-model="messageParams.text" :height="400"/>
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>

    <el-dialog
      title="保存成功"
      :visible.sync="successDialog"
      :close-on-click-modal="false"
      :show-close="false"
      :center="true"
      :destroy-on-close="true"
      width="30%">
      <div style="width: 50%; margin: 20px auto; padding-bottom: 20px; text-align: center">
        <el-button style="float: left" type="primary" @click="jumpView">去查看</el-button>
        <el-button style="float: right" type="info" @click="closeDialog">知道了</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  import Tinymce from '@/components/Tinymce'
  import MDinput from '@/components/MDinput'
  import Sticky from '@/components/Sticky';
  import SingleImage1 from "@/components/Upload/SingleImage1";
  import {getOptions, getDetail, add, edit} from '@/api/system/message'

  const defaultForm = {
    title: '',
    catId: '1',
    banner: '',
    top: 0,
    publisher: '',
    publishTime: '',
    text: '',
    desc: '',
    status: 'draft',
    id: undefined,
  };

  export default {
    name: 'MessageDetail',
    components: {Tinymce, MDinput, Sticky, SingleImage1},
    props: {
      isEdit: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        pickerOptions: {
          shortcuts: [{
            text: '今天',
            onClick(picker) {
              picker.$emit('pick', new Date());
            }
          }, {
            text: '明天',
            onClick(picker) {
              const date = new Date();
              date.setTime(date.getTime() + 3600 * 1000 * 24);
              picker.$emit('pick', date);
            }
          }, {
            text: '一周后',
            onClick(picker) {
              const date = new Date();
              date.setTime(date.getTime() + 3600 * 1000 * 24 * 7);
              picker.$emit('pick', date);
            }
          }]
        },

        catMap: [],
        messageParams: Object.assign({}, defaultForm),
        loading: false,
        rules: {
          title: [{required: true, message: '请输入标题', trigger: 'change'}],
          banner: [{required: true, message: '请上传图片', trigger: 'change'}],
          text: [{required: true, message: '请输入内容', trigger: 'change'}],
          catId: [{required: true, message: '请选择类型', trigger: 'change'}],
          publisher: [{required: true, message: '请输入公布人', trigger: 'change'}],
        },
        tempRoute: {},
        successDialog: false,
        jumperId: 0,
      }
    },
    computed: {
      descLength() {
        return this.messageParams.desc.length
      },
    },
    async created() {
      const ret = await getOptions();
      this.catMap = ret?.data?.catMap || [];
      if (this.isEdit) {
        const id = this.$route.params && this.$route.params.id
        this.fetchData(id)
      }
      this.tempRoute = Object.assign({}, this.$route)
    },
    methods: {
      fetchData(id) {
        getDetail({id: id}).then(res => {
          this.messageParams = res.data
          this.messageParams.status = 'published';
          this.messageParams.catId = this.messageParams.catId.toString();
          this.messageParams.top = this.messageParams.top > 0;
          this.setTagsViewTitle()
          this.setPageTitle()
        }).catch(err => {
          console.log(err)
        })
      },
      setTagsViewTitle() {
        const title = '编辑消息'
        const route = Object.assign({}, this.tempRoute, {title: `${title}-${this.messageParams.id}`})
        this.$store.dispatch('tagsView/updateVisitedView', route)
      },
      setPageTitle() {
        const title = '编辑消息'
        document.title = `${title} - ${this.messageParams.id}`
      },
      submitForm() {
        this.$refs.messageForm.validate(valid => {
          if (valid) {
            this.loading = true
            const func = this.messageParams.id > 0 ? edit : add;
            func(this.messageParams).then((res) => {
              if (res.code > 0) {
                this.$message.error(res.msg)
              } else {
                this.jumperId = res.data.id;
                this.successDialog = true;
              }
            })
            this.loading = false
          } else {
            console.log('error submit!!')
            return false
          }
        })
      },

      uploadBanner(url) {
        this.messageParams.banner = url;
      },
      closeDialog() {
        if (this.messageParams.id > 0) {
          this.fetchData(this.messageParams.id)
        } else {
          this.messageParams = Object.assign({}, defaultForm);
        }
        this.successDialog = false;
      },

      jumpView() {
        this.$router.push(`/system/message/view/${this.jumperId}`);
      }
    }
  }
</script>

<style lang="scss" scoped>

  .message-container {
    width: 80%;
    margin: 20px auto;

    .message-form {
      margin-top: 50px;

      .message-header ::v-deep {
        .el-input__inner {
          resize: none;
          border: none;
          border-radius: 0;
          border-bottom: 1px solid #bfcbd9;
          font-size: 20px;
          font-weight: bold;
        }
      }

      .message-item ::v-deep {
        .el-input__inner {
          resize: none;
          border: none;
          border-radius: 0;
          border-bottom: 1px solid #bfcbd9;
          font-size: 18px;
        }
      }

      .message-img {
        padding: 0 15px;
        height: 198px;
        width: 388px;
        border-radius: 0;
        border-bottom: 1px solid #bfcbd9;
      }
    }
  }

  .article-textarea ::v-deep {
    textarea {
      resize: none;
      border: none;
      border-radius: 0;
      border-bottom: 1px solid #bfcbd9;
      font-size: 18px;

    }
  }

</style>

