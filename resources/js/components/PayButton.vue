<template>
  <button @click="modalOpen = true" type="button" class="rounded px-4 py-2 text-lg font-bold text-white bg-indigo-600 hover:bg-indigo-400 transition-all duration-300 ease-in-out focus:outline-none focus:shadow-outline-indigo">
    <slot></slot>
    <complete-purchase-modal :open="modalOpen" @close="modalOpen = false" :amount="this.amount" :selected-slots="selectedSlots"></complete-purchase-modal>
  </button>
</template>

<script>
  import CompletePurchaseModal from "./CompletePurchaseModal"

  export default {
    components: {CompletePurchaseModal},
    props: ['amount', 'selectedSlots'],
    data() {
      return {
        modalOpen: false,
      }
    },
    methods: {

    }
  }
</script>