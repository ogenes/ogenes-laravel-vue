<template>
  <div class="pictures-container">
    <el-card>
      <div class="pictures-form">
        <el-form
          :model="queryForm"
          v-loading.fullscreen.lock="loading"
          :inline="true"
          label-width="120px"
          label-position="right">
          <el-form-item label="Date：">
            <el-date-picker
              v-model="queryForm.uploadDate"
              size="small"
              type="daterange"
              :start-placeholder="$t('queryForm.startTime')"
              :end-placeholder="$t('queryForm.endTime')"
              value-format="yyyy-MM-dd"
              class="form-element-item"
            />
          </el-form-item>
          <el-form-item label="Filename：">
            <el-input
              type="text"
              v-model="queryForm.keyword"
              size="small"
              clearable
              :placeholder="$t('queryForm.pleaseInput')"
              class="form-element-item"
              @keyup.enter.native="queryList"
            />
          </el-form-item>
          <el-form-item label=" ">
            <el-button
              type="primary"
              :loading="loading"
              size="small"
              @click="queryList"
            >{{ $t('queryForm.query') }}
            </el-button>
          </el-form-item>
        </el-form>
      </div>
      <div class="pictures-content">
        <div class="pictures-pagination">
          <el-pagination
            background
            :page-sizes="pageSizes"
            :page-size="pageSize"
            :current-page="currentPage"
            layout="total, sizes, prev, pager, next, jumper"
            :total="total"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
          />
        </div>
        <div class="pictures-table">
          <el-table :data="result" :height="620" stripe border>
            <el-table-column align="center" label="File">
              <template slot-scope="scope">
                <div>
                  <el-image
                    style="width: 100px;height: 100px; overflow: hidden;"
                    :src="scope.row.url"
                    :preview-src-list="[scope.row.url]"
                  />
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="name" label="Name">
              <template slot-scope="scope">
                <a :href="scope.row.url" target="_blank" style="color: #409EFF;">{{ scope.row.name
                  }}</a>
              </template>
            </el-table-column>
            <el-table-column prop="size" label="Size">
              <template slot-scope="scope">
                <span>{{ getFileSize(scope.row.size) }}</span>
                <el-progress :percentage="100" status="success"></el-progress>
              </template>
            </el-table-column>
            <el-table-column prop="created_at" label="Date"/>
            <el-table-column prop="ret" label="Preview" min-width="400">
              <template slot-scope="scope">
                <p>
                  <el-input type="text" v-model="scope.row.link"
                            @focus="$event.currentTarget.select()">
                    <template slot="prepend">Link:</template>
                  </el-input>
                  <el-input type="text" v-model="scope.row.html"
                            @focus="$event.currentTarget.select()">
                    <template slot="prepend">Html:</template>
                  </el-input>
                  <el-input type="text" v-model="scope.row.markdown"
                            @focus="$event.currentTarget.select()">
                    <template slot="prepend">Markdown:</template>
                  </el-input>
                </p>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script>
  import axios from '@/utils/axios';
  import {mapGetters} from 'vuex'
  import {getFileSize} from '@/utils/index'

  export default {
    computed: {
      ...mapGetters([
        'userInfo',
      ]),
    },
    data() {
      return {
        getFileSize,

        loading: false,
        queryForm: {
          keyword: '',
          uploadDate: []
        },
        result: [],
        total: 0,
        currentPage: 1,
        pageSize: 10,
        pageSizes: [10, 30, 50, 100, 200]
      };
    },
    async created() {
      this.queryList()
      try {
        await this.$store.dispatch('user/getInfo', true)
      } catch (err) {
        console.log('error:', err.message)
        await this.$router.push(`/Users/login`)
      }
    },

    methods: {
      queryList() {
        this.currentPage = 1;
        this.getList();
      },
      async getList() {
        this.loading = true;
        const params = JSON.parse(JSON.stringify(this.queryForm));
        params.page = this.currentPage;
        params.pageSize = this.pageSize;
        axios.post("/api/file/getList", params).then((res) => {
          if (res.data.code > 0) {
            return this.$message.error(res.data.msg);
          } else {
            this.result = res.data.data.list;
            this.result.forEach((item) => {
              item.link = item.url;
              item.html = '<a href="' + item.url + '" target="_blank"><img alt="' + item.name + '" src="' + item.url + '" ></a>';
              item.markdown = '![' + item.name + '](' + item.url + ')';
            });
            this.total = res.data.data.total;
            this.currentPage = parseInt(res.data.data.page)
            this.pageSize = parseInt(res.data.data.pageSize)
          }
        }).catch((e) => {
          console.log('error:', e.message)
        });
        this.loading = false
      },
      handleSizeChange: function (val) {
        this.pageSize = val;
        this.currentPage = 1;
        this.getList();
      },
      handleCurrentChange: function (currentPage) {
        this.currentPage = currentPage;
        this.getList();
      },
    },
  };
</script>
