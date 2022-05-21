<template>
  <message-detail :is-edit="false" />
</template>

<script>
import MessageDetail from './components/MessageDetail'

export default {
  name: 'CreateMessage',
  components: { MessageDetail }
}
</script>

