<template>
    <div>
      <router-link 
        v-for="(item, index) in dataArray" 
        :key="index" 
        :to="{ name: item.route }" 
        class="nav-button"
        :class="{ 'nav-button-active': isActive(item.route)}"
      >
        <img class="nav-button-img" :src="require(`../../assets/${item.photo}`)" alt="img" />
        <p class="nav-button-text">{{ item.name }}</p>
      </router-link>
    </div>
</template>
  
<script>
export default {
  props: {
    dataArray: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    isActive() {
      return (routeName) => this.$route.name === routeName;
    }
  },
  methods: {},
};
</script>
 
<style>
.nav-button {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  width: 100%; /* Полная ширина для адаптивности */
  min-width: 160px; /* Минимальная ширина для удобного нажатия */
  padding: 10px;
  margin: 5px 0; /* Уменьшенный margin для компактности */
  background-color: #D9D9D9;
  border-radius: 5px;
  color: #333;
  text-decoration: none;
  transition: background-color 0.3s ease;
  white-space: nowrap; /* Запрет на перенос текста для сохранения формы кнопки */
}

.nav-button:hover, .nav-button-active {
  background-color: #FFFFFF; /* Цвет при наведении и активном состоянии */
}

.nav-button-active {
  font-weight: bold; /* Выделение активного элемента */
  background-color: #4CAF50; /* Измените цвет фона для активного состояния */
}

.nav-button-img {
  width: 30px;
  height: 30px;
  margin-right: 15px;
}

.nav-button-text {
  color:#333;
  font-size: 16px;
  font-weight: 500;
}

</style>