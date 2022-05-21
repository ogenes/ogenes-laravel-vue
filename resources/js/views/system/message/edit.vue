<template>
  <message-detail :is-edit="true" />
</template>

<script>
import MessageDetail from './components/MessageDetail'

export default {
  name: 'EditMessage',
  components: { MessageDetail }
}
</script>

