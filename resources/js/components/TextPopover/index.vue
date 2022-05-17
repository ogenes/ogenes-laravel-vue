<template>
  <el-popover trigger="hover">
    <div>
      <el-button style="float: right" type="text" class="el-icon-document-copy" @click="handleClipboard(showText, $event)">
        复制
      </el-button>
    </div>
    <div style="width: 600px; overflow-x: scroll;">
      <pre v-if="type==='html'" v-html="text"/>
      <pre v-else v-text="showText"/>
    </div>
    <div slot="reference">
      <pre v-if="type==='html'" v-html="text.length > 100 ? text.substring(0, 100) + '...' : text"/>
      <pre v-else v-text="text.length > 100 ? text.substring(0, 100) + '...' : text"/>
    </div>
  </el-popover>
</template>

<script>
  import {handleJsonFormat} from '@/utils/index';
  import clipboard from '@/utils/clipboard'

  export default {
    name: "TextPopover",

    props: {
      text: {
        type: String,
        default: ""
      },
      type: {
        type: String,
        default: "text"
      },
    },

    computed: {
      showText() {
        if (this.type === 'json') {
          return handleJsonFormat(this.text);
        } else {
          return this.text;
        }
      }
    },

    methods: {
      handleClipboard(text, event) {
        clipboard(text, event)
      }
    }
  }
</script>
