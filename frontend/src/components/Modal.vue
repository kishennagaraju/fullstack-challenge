<template>
  <transition name="fade">
    <div class="vue-modal" v-show="open">
      <transition name="drop-in">
        <div class="vue-modal-inner" v-show="open">
          <div class="vue-modal-content">
            <h1><b>Weather Data by Hour</b></h1>
              <table>
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-2 text-xs text-gray-500">
                      Date/Time
                    </th>
                    <th class="px-6 py-2 text-xs text-gray-500">
                      Weather
                    </th>
                    <th class="px-6 py-2 text-xs text-gray-500">
                      Clouds
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white weather-table-body">
                  <tr v-for="weather in weatherData.hourly" class="whitespace-nowrap">
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900">
                        {{ filterDate(weather.dt * 1000) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ weather.temp }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ weather.clouds }}
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <button type="button" @click="close">Close</button>
                </tfoot>
              </table>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<script>
import { onMounted, onUnmounted } from "vue";
import moment from 'moment';

export default {
  name: "Modal",

  props: {
    open: {
      type: Boolean,
      required: true,
    },
    weatherData: {
      type: Object,
      required: true,
    }
  },
  setup(_, { emit }) {
    const close = () => {
      emit("close");
    };

    const handleKeyup = (event) => {
      if (event.keyCode === 27) {
        close();
      }
    };

    onMounted(() => document.addEventListener("keyup", handleKeyup));
    onUnmounted(() => document.removeEventListener("keyup", handleKeyup));

    return { close };
  },
  methods: {
    filterDate(timestamp) {
      if (!timestamp) {
        return '';
      }
      return moment(timestamp).format('LLL');
    }
  }
}
</script>

<style scoped>
*,
::before,
::after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.vue-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow-x: hidden;
  overflow-y: auto;
  background-color: rgba(0, 0, 0, 0.4);
  z-index: 1;
}

.vue-modal-inner {
  max-width: 500px;
  max-height: 60%;
  margin: 2rem auto;
}

.vue-modal-content {
  position: relative;
  background-color: #fff;
  border: 1px solid rgba(0, 0, 0, 0.3);
  background-clip: padding-box;
  border-radius: 0.3rem;
  padding: 1rem;
}

.weather-table-body {
  max-height: 50% !important;
  overflow: auto;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.drop-in-enter-active,
.drop-in-leave-active {
  transition: all 0.3s ease-out;
}

.drop-in-enter-from,
.drop-in-leave-to {
  opacity: 0;
  transform: translateY(-50px);
}
</style>
