<template>
  <portal to="modals">
    <div v-show="open" class="fixed top-0 right-0 bottom-0 left-0 bg-black bg-opacity-20 flex justify-center items-center p-6">
      <on-click-outside :do="handleClickOutside">
        <div ref="modal" class="max-w-md w-full bg-white px-8 py-6 rounded-lg shadow-lg">
          <div v-if="!complete">
            <h2 class="font-semibold text-xl text-back text-center mb-4 text-gray-800">Complete your purchase</h2>
            <form action="#" method="POST" @submit.prevent="handleSubmit">
              <label class="block mb-4">
                <span class="block text-gray-800 text-sm font-bold mb-2">Company</span>
                <input class="block bg-gray-200 rounded px-4 py-2 w-full leading-normal focus:outline-none focus:shadow-outline"
                       type="text"
                       v-model="form.companyName"
                       ref="companyInput"
                       placeholder="DigiTechnoSoft Inc."
                       @keydown.shift.tab.prevent>
              </label>
              <label class="block mb-4">
                <span class="block text-gray-800 text-sm font-bold mb-2">Email</span>
                <input class="block bg-gray-200 rounded px-4 py-2 w-full leading-normal focus:outline-none focus:shadow-outline"
                       type="text"
                       v-model="form.email"
                       placeholder="mail@example.com">
              </label>
              <label class="block mb-6">
                <span class="block text-gray-800 text-sm font-bold mb-2">Credit Card</span>
                <stripe-elements ref="cardInput" class="block bg-gray-200 rounded px-4 py-2 w-full leading-normal"></stripe-elements>
              </label>
              <div>
                <button class="block w-full rounded px-4 py-2 text-lg font-bold text-white bg-indigo-600 transition-all duration-300 ease-in-out mb-4 focus:outline-none focus:shadow-outline-indigo"
                        type="submit"
                        :class="{ 'opacity-50': working, 'hover:bg-indigo-400': !working, 'cursor-not-allowed': working }"
                        :disabled="working"
                        @keydown.tab.exact.prevent>
                  <span v-if="!working">Pay ${{ amountInDollars }} now</span>
                  <span v-if="working">Processing...</span>
                </button>
                <p class="text-center text-gray-400 leading-normal">We'll reach out for your sponsorship information after you've confirmed your purchase.</p>
              </div>
            </form>
          </div>
          <div v-if="complete">
            <h2 class="font-semibold text-xl text-back text-center mb-4 text-gray-800">Thanks</h2>
            <p class="text-center text-gray-400 leading-normal">We'll be reaching out for your sponsorship information via email soon.</p>
          </div>
        </div>
      </on-click-outside>
    </div>
  </portal>
</template>

<script>
  import formatNumber from 'accounting-js/lib/formatNumber'
  import StripeElements from "./StripeElements"
  import OnClickOutside from "./OnClickOutside"

  export default {
    components: { StripeElements, OnClickOutside },
    props: ['open', 'amount', 'selectedSlots'],
    data() {
      return {
        form: {
          companyName: '',
          email: ''
        },
        working: false,
        complete: false
      }
    },
    computed: {
      amountInDollars() {
        return formatNumber(this.amount / 100, { precision: 0 })
      },
    },
    watch: {
      open(newValue) {
        if(newValue) {
          setTimeout(() => {
            this.$refs.companyInput.focus()
          })
        }
      }
    },
    created() {
      const escapeListener = (e) => {
        if(e.key === 'Escape') {
          this.$emit('close')
        }
      }

      document.addEventListener('keydown', escapeListener)

      this.$on('hook:beforeDestroy', () => {
        document.removeEventListener('keydown', escapeListener)
      })
    },
    methods: {
      handleClickOutside(e) {
        if (!this.open) {
          return
        }
        // TODO: Emit not work when click outside of modal
        // this.$emit('close')
      },
      handleSubmit() {
        this.working = true
        this.$refs.cardInput.createToken().then(token => {
          return axios.post('/test', {
            company_name: this.form.companyName,
            email: this.form.email,
            sponsorable_slots: this.selectedSlots,
            payment_token: token.id
          })
        }).then(response => {
          this.working = false
          this.complete = true
          console.log(response)
        })
      }
    }
  }
</script>