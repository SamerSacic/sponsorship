<template>
  <div @click="$emit('input', !selected)" class="flex justify-between bg-white rounded-lg shadow p-4 group border-2 border-transparent hover:border-indigo-400 select-none cursor-pointer">
    <div class="w-3/4 flex items-center justify-between">
      <div class="flex items-center">
        <img class="block w-16 h-16 rounded mr-4" :src="sponsorableSlot.image_url" alt="Radio Logo">
        <div>
          <div class="text-lg font-bold text-gray-700">{{ sponsorableSlot.title }}</div>
          <div class="text-sm text-gray-600">{{ dateForHumans }}</div>
        </div>
      </div>
      <div class="text-right">
        <div class="text-lg font-bold text-gray-700">${{ priceInDollars }}</div>
        <div class="text-sm text-gray-600">USD</div>
      </div>
    </div>
    <div class="w-1/4 flex justify-end items-center">
       <i v-if="selected" class="fas fa-check-circle text-2xl text-green-400"></i>
      <i v-if="!selected" class="fas fa-plus-circle text-2xl text-gray-400 group-hover:text-indigo-600"></i>
    </div>
  </div>
</template>

<script>
  import parseDate from 'date-fns/parseISO'
  import formatDate from 'date-fns/format'
	import formatNumber from 'accounting-js/lib/formatNumber'

  export default {
    props: ['sponsorableSlot', 'selected'],
    computed: {
      dateForHumans() {
        return formatDate(parseDate(this.sponsorableSlot.publish_date), 'MMM d, YYY')
      },
			priceInDollars() {
      	return formatNumber(this.sponsorableSlot.price / 100, { precision: 0 })
			}
    },
    methods: {
    }
  }
</script>