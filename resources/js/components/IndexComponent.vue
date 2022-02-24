<template>
  <div class="main-container">
    <div class="main-header">
      <div class="contents">
        <el-menu
          class="el-menu-demo"
          mode="horizontal"
          background-color="#545c64"
          text-color="#fff"
          active-text-color="#ffd04b"
          :router="true"
          :default-active="$route.path">
          <el-menu-item index="/">
            {{ $t("home.name") }}
          </el-menu-item>
          <el-menu-item index="/About">
            {{ $t("about.name") }}
          </el-menu-item>
          <el-menu-item index="/Pictures">
            {{ $t("pictures.name") }}
          </el-menu-item>
          <el-submenu v-if="userInfo.id > 0" index="/Users" style="float:right;">
            <template slot="title">{{ userInfo.username}}</template>
            <el-menu-item index="/Users/info">{{ $t('user.profile.name') }}</el-menu-item>
            <el-menu-item divided>
              <span style="display:block;" @click="logout">{{ $t('user.logout.name') }}</span>
            </el-menu-item>
            <el-menu-item divided>
              <span style="display:block;" @click="switchLanguage">{{ $t('language') }}</span>
            </el-menu-item>
          </el-submenu>
          <el-submenu v-else index="/Users" style="float:right;">
            <template slot="title">{{ $t('user.name') }}</template>
            <el-menu-item index="/Users/login">{{ $t('user.login.name') }}</el-menu-item>
            <el-menu-item index="/Users/register">{{ $t('user.register.name') }}</el-menu-item>
            <el-menu-item divided>
              <span style="display:block;" @click="switchLanguage">{{ $t('language') }}</span>
            </el-menu-item>
          </el-submenu>
        </el-menu>
      </div>
    </div>
    <div class="main-content">
      <div class="contents">
        <router-view></router-view>
      </div>
    </div>
    <div class="main-footer">
      <div class="contents">
        <p>
          请勿上传违反中华人民共和国法律的图片，违者后果自负。<br/>
          Copyright © 2021 Ogenes All Rights Reserved 备案号：<a href="https://beian.miit.gov.cn" target="_blank">豫ICP备16028696号-3</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
  import {mapGetters} from 'vuex'
  import axios from "@/utils/axios";

  export default {
    name: "App",
    computed: {
      ...mapGetters([
        'userInfo',
      ]),
    },
    async created() {
      this.init();
    },
    methods: {
      async init () {
        try {
          await this.$store.dispatch('user/getInfo')
        } catch (err) {
          console.log(err.message)
        }

        const res = await axios.post('/api/system/init');
        if (res.data.code > 0) {
          console.log(res.data.msg)
        } else {
          const conf = res.data.data;
          this.$i18n.locale = conf.lang === 'zh' ? 'cn' : 'en';
        }
      },
      async logout() {
        await this.$store.dispatch('user/logout');
        await this.$router.push(`/Users/login`)
      },
      async switchLanguage() {
        if (this.$i18n.locale === 'en') {
          this.$i18n.locale = 'cn'
        } else {
          this.$i18n.locale = 'en'
        }
        await axios.post('/api/system/setLang', {lang: this.$i18n.locale});
      }
    }
  }
</script>

<style lang="less">
  * {
    padding: 0;
    margin: 0;
  }

  .main-container {
    height: 100%;
    display: flex;
    flex-direction: column;

    .main-header {
      background-color: #545c64;
      height: 62px;

      .contents {
        height: 100%;
        width: 60%;
        margin: 0 auto;
      }
    }

    .main-content {
      height: 100%;

      .contents {
        height: 100%;
        width: 60%;
        margin: 0 auto;
      }
    }

    .main-footer {
      background-color: #545c64;
      height: 60px;

      .contents {
        height: 100%;
        width: 60%;
        margin: 0 auto;
        text-align: center;

        p {
          color: #AAAAAD;

          a {
            color: #AAAAAD;
          }
        }
      }
    }
  }
</style>
