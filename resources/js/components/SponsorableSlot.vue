<template>
  <div @click="$emit('input', !selected)" class="bg-white rounded-lg shadow p-4 pr-8 flex group border-2 border-transparent hover:border-indigo-400 select-none cursor-pointer transition-all duration-300 ease-in-out">
    <div class="w-3/4 flex justify-between items-center">
      <div class="flex items-center">
        <img class="block w-16 h-16 mr-4 rounded" :src="sponsorableSlot.image_url" alt="Logo">
        <div>
          <div class="text-lg font-bold text-gray-600">{{ sponsorableSlot.title }}</div>
          <div class="text-sm text-gray-500">{{ dateForHumans }}</div>
        </div>
      </div>
      <div class="text-right">
        <div class="text-lg font-bold text-gray-600">${{ priceInDollars }}</div>
        <div class="text-sm text-gray-500">USD</div>
      </div>
    </div>
    <div class="w-1/4 flex items-center justify-end">
      <svg v-if="selected" class='h-8 w-8 text-green-500 fill-current' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><title>Checkmark Circle</title><path d='M256 48C141.31 48 48 141.31 48 256s93.31 208 208 208 208-93.31 208-208S370.69 48 256 48zm108.25 138.29l-134.4 160a16 16 0 01-12 5.71h-.27a16 16 0 01-11.89-5.3l-57.6-64a16 16 0 1123.78-21.4l45.29 50.32 122.59-145.91a16 16 0 0124.5 20.58z'/></svg>
      <svg v-if="!selected" class='group-hover:hidden h-8 w-8 text-gray-400 fill-current group-hover:text-indigo-500 transition-all duration-300 ease-in-out' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><title>Add Circle</title><path d='M256 48C141.31 48 48 141.31 48 256s93.31 208 208 208 208-93.31 208-208S370.69 48 256 48zm80 224h-64v64a16 16 0 01-32 0v-64h-64a16 16 0 010-32h64v-64a16 16 0 0132 0v64h64a16 16 0 010 32z'/></svg>
    </div>
  </div>
</template>

<script>
  import formatDate from 'date-fns/format'
  import formatNumber from 'accounting-js/lib/formatNumber'

  export default {
    props: ['sponsorableSlot', 'selected'],
    computed: {
      dateForHumans() {
        return formatDate(new Date(this.sponsorableSlot.publish_date), 'MMM d, yyyy');
      },
      priceInDollars() {
        return formatNumber(this.sponsorableSlot.price / 100, { precision: 0 });
      }
    },
    methods: {
    }
  }
</script>