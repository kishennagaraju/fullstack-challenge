<script>
import { useLoading } from "vue3-loading-overlay";
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

import Modal from "@/components/Modal.vue";

export default {
  data: () => ({
    apiResponse: null,
    fullPage: true,
    isModalOpen: false,
    weatherData: {status: false, data: "Nothing to Show"},
  }),

  created() {
    this.fetchData()
  },

  components: {
    Modal
  },

  methods: {
    async fetchData() {
      let loader = useLoading();
      loader.show({
        // Optional parameters
        container: null,
        canCancel: true,
        color: '#000000',
        loader: 'spinner',
        width: 64,
        height: 64,
        backgroundColor: "#ffffff",
        opacity: 0.5,
        zIndex: 999,
      });
      const url = import.meta.env.VITE_WEATHER_API_URL + '/users'
      this.apiResponse = await (await fetch(url)).json()
      loader.hide()
    },
    async showModal(email){
      let loader = useLoading();
      loader.show({
        // Optional parameters
        container: null,
        canCancel: true,
        color: '#000000',
        loader: 'spinner',
        width: 64,
        height: 64,
        backgroundColor: "#ffffff",
        opacity: 0.5,
        zIndex: 999,
      });
      const url = import.meta.env.VITE_WEATHER_API_URL + '/users/' + email + '/weather';
      let response = await fetch(url);
      const modalData = await response.json();
      console.log("modalData", modalData);
      loader.hide();
      if (modalData?.data) {
        this.weatherData = modalData.data;
        this.isModalOpen = true;
      } else {
        alert("There is something wrong. Please try again later !!!");
      }
    }
  }
}
</script>

<template>
    <div class="container mx-auto bg-gray-200 rounded-xl shadow border p-8 m-10">
      <p class="text-3xl text-gray-700 font-bold mb-5">
        Users with their Weather
      </p>
    </div>

    <div v-if="apiResponse" class="container mx-auto shadow border p-8 m-10">
      <table>
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-2 text-xs text-gray-500">
              ID
            </th>
            <th class="px-6 py-2 text-xs text-gray-500">
              Name
            </th>
            <th class="px-6 py-2 text-xs text-gray-500">
              Email
            </th>
            <th class="px-6 py-2 text-xs text-gray-500">
              Current Weather
            </th>
            <th class="px-6 py-2 text-xs text-gray-500">
              View Detailed Weather
            </th>
          </tr>
        </thead>
        <tbody class="bg-white">
          <tr v-for="(user, index) in apiResponse.data" class="whitespace-nowrap">
            <td class="px-6 py-4 text-sm text-gray-500">
              {{ ++index }}
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-900">
                {{ user.name }}
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-500">{{ user.email }}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
              {{ user.weather.current.temp }}
            </td>
            <td class="px-6 py-4">
              <a href="#" @click="showModal(user.email)" class="px-4 py-1 text-sm text-white bg-red-400 rounded">View</a>
            </td>
          </tr>
        </tbody>
      </table>

      <Modal :open="isModalOpen" @close="isModalOpen = !isModalOpen" :weatherData="weatherData"/>
    </div>

    <div v-if="!apiResponse">
      <div class="container mx-auto shadow border p-8 m-10">
        <table>
          <tbody class="bg-white">
            <td rowspan="5" style="text-align: center"> Pinging the API. </td>
          </tbody>
        </table>
      </div>
    </div>
</template>