<template>
  <div class="nav" v-if="userStatus !== '2'">
    <NavButton class="nav_b" :data-array="userStatus === '0' ? data.array0 : data.array1"></NavButton>
  </div>
</template>
  
<script>
import NavButton from './NavButton.vue';
import data from './data.json';
import { mapState } from 'vuex';

export default {
  components: {
    NavButton,
  },
  data() {
    return {
      data: data,
    };
  },
  computed: {
    ...mapState(['userStatus']), // Используем mapState для получения значения userStatus из хранилища
    status() {
      return this.userStatus
    },
  },
};
</script>
 
<style>
.nav {
  position: sticky;
  top: 90px;
  width: 100%; /* Адаптивная ширина */
  max-width: 250px; /* Максимальная ширина для больших экранов */
  height: calc(100vh - 90px);
  background-color: #34495E;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px 10px; /* Увеличенный padding сверху и снизу для лучшей адаптивности */
  box-sizing: border-box;
}

@media (max-width: 768px) {
  .nav {
    width: 100%; /* Полная ширина на маленьких экранах */
    position: relative;
    top: 0;
    height: auto;
    padding: 10px 0;
    flex-direction: row;
    overflow-x: auto; /* Добавляем прокрутку для горизонтального отображения */
  }
}

</style>
