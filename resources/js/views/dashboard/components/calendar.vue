<template>
  <v-calendar
    show-weeknumbers
    is-expanded
    :attributes='attrs'
  >
  </v-calendar>
</template>

<script>
  export default {
    name: "HomeCalendar",
    data() {
      return {
        attrs: [
          {
            key: 'today',
            dates: new Date(),
            highlight: {
              color: 'blue',
              fillMode: 'light',
            },
          },
        ],
      }
    },
  }
</script>
