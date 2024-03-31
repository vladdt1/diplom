<template>
  <div>
    <HeaderComponent v-if="!$route.meta.hideHeaderNav"></HeaderComponent>
    <div class="app-flex">
      <NavComponent class="app-nav" v-if="!$route.meta.hideHeaderNav"></NavComponent>
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import HeaderComponent from './components/staticComponent/HeaderComponent.vue';
import NavComponent from './components/staticComponent/NavComponent.vue';

export default {
  name: 'App',
  components: {
      HeaderComponent,
      NavComponent
  },
  mounted() {
    // В этом методе происходит запрос к серверу при монтировании компонента.
    axios.get('http://localhost:8000/cv/checkreserv')
    .then(response => {
        // Обработка успешного ответа от сервера
        console.log(response.data);
    })
    .catch(error => {
        // Обработка ошибок
        console.error(error);
    });
  }
}
</script>

<style scoped>
.app-flex{
  display: flex;
}
</style>
