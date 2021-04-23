<script>
  export default {
    props: ['do'],
    mounted() {
      const handleClick = (e) => {
        if (this.$el.contains(e.target) || this.$el === e.target ) {
          return
        }

        this.do()
      }
      document.addEventListener('click', handleClick)
      this.$once('hook:beforeDestroy', () => {
        document.removeEventListener('click', handleClick)
      })
    },
    render() {
      return this.$slots.default[0]
    }
  }
</script>