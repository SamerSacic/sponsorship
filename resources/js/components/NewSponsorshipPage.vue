<template>
  <div>
    <div class="bg-gray-50 min-h-screen">
      <div class="absolute top left px-3 opacity-50">
        <div class="flex items-center">
          <svg class="w-8 h-8 text-gray-400 inline-block m-2 fill-current" xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><title>Boat</title><path d='M461.93 261.05c-2-4.76-6.71-7.83-11.67-9.49l-187.18-74.48a23.78 23.78 0 00-14.17 0l-187 74.52c-5 1.56-9.83 4.77-11.81 9.53s-2.94 9.37-1 15.08l46.53 119.15a7.46 7.46 0 007.47 4.64c26.69-1.68 50.31-15.23 68.38-32.5a7.66 7.66 0 0110.49 0C201.29 386 227 400 256 400s54.56-14 73.88-32.54a7.67 7.67 0 0110.5 0c18.07 17.28 41.69 30.86 68.38 32.54a7.45 7.45 0 007.46-4.61l46.7-119.16c1.98-4.78.99-10.41-.99-15.18z' fill='none' stroke='currentColor' stroke-miterlimit='10' stroke-width='32'/><path d='M416 473.14a6.84 6.84 0 00-3.56-6c-27.08-14.55-51.77-36.82-62.63-48a10.05 10.05 0 00-12.72-1.51c-50.33 32.42-111.61 32.44-161.95.05a10.09 10.09 0 00-12.82 1.56c-10.77 11.28-35.19 33.3-62.43 47.75a7.15 7.15 0 00-3.89 5.73 6.73 6.73 0 007.92 7.15c20.85-4.18 41-13.68 60.2-23.83a8.71 8.71 0 018-.06A185.14 185.14 0 00340 456a8.82 8.82 0 018.09.06c19.1 10 39.22 19.59 60 23.8a6.72 6.72 0 007.95-6.71z'/><path d='M320 96V72a24.07 24.07 0 00-24-24h-80a24.07 24.07 0 00-24 24v24M416 233v-89a48.14 48.14 0 00-48-48H144a48.14 48.14 0 00-48 48v92M256 183.6v212.85' fill='none' stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='32'/></svg>
          <span class="text-xl text-gray-400 font-semibold">SponsorShip</span>
        </div>
      </div>
      <div class="max-w-2xl mx-auto px-6 pt-16 pb-32">
        <h1 class="text-center text-3xl font-semibold text-gray-600 mb-8">Which episodes would you want to sponsor?</h1>
        <ul>
          <li class="mb-6" v-for="sponsorableSlot in sponsorableSlots" :key="sponsorableSlot.id">
            <sponsorable-slot
              :sponsorable-slot="sponsorableSlot"
              :selected="selectedSlots.includes(sponsorableSlot.id)"
              @input="handleSponsorableSlotInput(sponsorableSlot, $event)"
            >
            </sponsorable-slot>
          </li>
        </ul>
      </div>
      <div class="bg-white border-t-2 fixed bottom-0 w-full">
        <div class="max-w-2xl mx-auto px-6 py-4">
          <div class="flex justify-between items-center">
            <div class="w-3/4 pr-6 pl-4 flex justify-between items-center">
              <div class="text-lg text-gray-600">{{ selectedSlots.length }} {{ selectedSlots.length === 1 ? 'episode' : 'episodes' }} selected</div>
              <div class="text-right">
                <div class="text-lg font-bold text-gray-600">${{ totalInDollars }}</div>
                <div class="text-sm text-gray-500">USD</div>
              </div>
            </div>
            <div class="w-1/4 flex justify-end items-center">
              <pay-button :amount="total" :selected-slots="selectedSlots">Pay now</pay-button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import _ from 'lodash'
  import SponsorableSlot from "./SponsorableSlot"
  import PayButton from "./PayButton"
  import formatNumber from 'accounting-js/lib/formatNumber'

  export default {
    components: {SponsorableSlot, PayButton},
    data() {
      return {
        selectedSlots: [],
        sponsorableSlots: [
          {
            id: 1,
            title: 'Full Stack Radio: Episode 51',
            publish_date: '2021-06-14',
            price: 50000,
            image_url: 'https://fullstackradio.com/podcast-cover.jpg',
          },
          {
            id: 2,
            title: 'Full Stack Radio: Episode 52',
            publish_date: '2021-07-15',
            price: 40000,
            image_url: 'https://fullstackradio.com/podcast-cover.jpg',
          },
          {
            id: 3,
            title: 'Full Stack Radio: Episode 53',
            publish_date: '2021-08-25',
            price: 55000,
            image_url: 'https://fullstackradio.com/podcast-cover.jpg',
          },
          {
            id: 4,
            title: 'Full Stack Radio: Episode 54',
            publish_date: '2021-09-30',
            price: 60000,
            image_url: 'https://fullstackradio.com/podcast-cover.jpg',
          },
        ],
      }
    },
    computed: {
      totalInDollars() {
        return formatNumber(this.total / 100, { precision: 0 })
      },
      total() {
        return this.sponsorableSlots.filter((slot) => this.selectedSlots.includes(slot.id)).reduce((total, slot) => {
          return total + slot.price
        }, 0)
      }
    },
    methods: {
      handleSponsorableSlotInput(sponsorableSlot, newValue) {
        if(newValue) {
          this.selectedSlots = [...this.selectedSlots, sponsorableSlot.id]
        } else {
          this.selectedSlots = _.without(this.selectedSlots, sponsorableSlot.id)
        }
      }
    }
  }
</script>
