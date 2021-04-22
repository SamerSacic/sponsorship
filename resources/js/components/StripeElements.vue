<template>
  <div></div>
</template>

<script>
  export default {
    props: [],
    mounted() {
      this.stripe = Stripe('pk_test_IDr6dVB8Tcy1LdJa4gmKdyK5')
      this.elements = this.stripe.elements()

      this.card = this.elements.create('card', {
        classes: {
          focus: 'outline-none shadow-outline'
        },
        style: {
          base: {
            fontSize: '16px',
            fontSmoothing: 'antialiased',
            color: 'rgb(70, 85, 104)',
            '::placeholder': {
              color: 'rgba(70, 85, 104, 0.5)',
            }
          }
        }
      })

      this.card.mount(this.$el)
    },
    methods: {
      createToken() {
        return this.stripe.createToken(this.card).then(function (result) {
          if (result.error) {
            console.log(result.error)
            // var errorElement = document.getElementById('card-errors')
            // errorElement.textContent = result.error.message;
          } else {
            return result.token
            // stripeTokenHandler(result.token)
          }
        })
      }
    }
  }
</script>