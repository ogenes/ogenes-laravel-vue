<template>
  <el-row :gutter="40" class="panel-group">
    <el-col :xs="16" :sm="16" :lg="8" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('allUser')">
        <div class="card-panel-icon-wrapper icon-all-user">
          <svg-icon icon-class="all-user" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">
            All Users
          </div>
          <count-to :start-val="0" :end-val="allUser" :duration="100" class="card-panel-num" />
        </div>
      </div>
    </el-col>
    <el-col :xs="16" :sm="16" :lg="8" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('activeUser')">
        <div class="card-panel-icon-wrapper icon-active-user">
          <svg-icon icon-class="active-user" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">
            Active Users
          </div>
          <count-to :start-val="0" :end-val="activeUser" :duration="100" class="card-panel-num" />
        </div>
      </div>
    </el-col>
    <el-col :xs="16" :sm="16" :lg="8" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('purchases')">
        <div class="card-panel-icon-wrapper icon-online-user">
          <svg-icon icon-class="online-user" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">
            Online Users
          </div>
          <count-to :start-val="0" :end-val="onlineUser" :duration="100" class="card-panel-num" />
        </div>
      </div>
    </el-col>
  </el-row>
</template>

<script>
import CountTo from 'vue-count-to'
import { getUserGroup } from "@/api/dashboard";

export default {
  components: {
    CountTo
  },
  data() {
    return {
      allUser: 0,
      activeUser: 0,
      onlineUser: 0,
    }
  },
  async created() {
    const ret = await getUserGroup();
    this.allUser = parseInt(ret?.data?.allUser || 0);
    this.activeUser = parseInt(ret?.data?.activeUser || 0);
    this.onlineUser = parseInt(ret?.data?.onlineUser || 0);
  },
  methods: {
    handleSetLineChartData(type) {
      console.log('type', type);
    }
  }
}
</script>

<style lang="scss" scoped>
.panel-group {
  margin-top: 18px;

  .card-panel-col {
    margin-bottom: 32px;
  }

  .card-panel {
    height: 108px;
    cursor: pointer;
    font-size: 12px;
    position: relative;
    overflow: hidden;
    color: #666;
    background: #fff;
    box-shadow: 4px 4px 40px rgba(0, 0, 0, .05);
    border-color: rgba(0, 0, 0, .05);

    &:hover {
      .card-panel-icon-wrapper {
        color: #fff;
      }

      .icon-all-user {
        background: #40c9c6;
      }

      .icon-active-user {
        background: #36a3f7;
      }

      .icon-online-user {
        background: #f4516c;
      }

    }

    .icon-all-user {
      color: #40c9c6;
    }

    .icon-active-user {
      color: #36a3f7;
    }

    .icon-online-user {
      color: #f4516c;
    }

    .card-panel-icon-wrapper {
      float: left;
      margin: 14px 0 0 14px;
      padding: 16px;
      transition: all 0.38s ease-out;
      border-radius: 6px;
    }

    .card-panel-icon {
      float: left;
      font-size: 48px;
    }

    .card-panel-description {
      float: right;
      font-weight: bold;
      margin: 26px;
      margin-left: 0px;

      .card-panel-text {
        line-height: 18px;
        color: rgba(0, 0, 0, 0.45);
        font-size: 16px;
        margin-bottom: 12px;
      }

      .card-panel-num {
        font-size: 20px;
      }
    }
  }
}

@media (max-width:550px) {
  .card-panel-description {
    display: none;
  }

  .card-panel-icon-wrapper {
    float: none !important;
    width: 100%;
    height: 100%;
    margin: 0 !important;

    .svg-icon {
      display: block;
      margin: 14px auto !important;
      float: none !important;
    }
  }
}
</style>
