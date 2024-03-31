<template>
  <div class="reservation">
    <div class="reservation-header-input">
      <button 
        class="my-books-header-input-button" 
        @click="getBookList('today')"
        :class="{ 'active-button': selectedDate === 'today' }"
      >
        Бронирование на сегодня
      </button>
      <button 
        class="my-books-header-input-button" 
        @click="getBookList('tomorrow')"
        :class="{ 'active-button': selectedDate === 'tomorrow' }"
      >
        Бронирование на завтра
      </button>
      <button 
        class="my-books-header-input-button" 
        @click="getBookList('all')"
        :class="{ 'active-button': selectedDate === 'all' }"
      >
        Все бронирования
      </button>
    </div>
    <table class="reservation-table">
      <thead>
        <tr>
          <th>Артикул</th>
          <th>Название</th>
          <th>Автор</th>
          <th>Кол-во свободных экземплятров</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in bookList" :key="item.title_id">
          <td>{{ item.title_id }}</td>
          <td><router-link :to="'/search-book/' + item.title_id">{{ item.title }}</router-link></td>
          <td>{{ item.autor }}</td>
          <td>{{ item.status_0_count }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      bookList: [],
      selectedDate: 'today',
    };
  },
  methods: {
    async getBookList(dateType) {
      try {
        const response = await axios.post('http://localhost:8000/cv/checkCard', {
          dateType: dateType,
        });

        this.bookList = response.data;
        this.selectedDate = dateType;
        console.log(this.bookList)
      } catch (error) {
        console.error("Error while searching book:", error);
      }
    },
  },
  created() {
    this.getBookList('today');
  },
};
</script>

<style>.reservation {
  max-width: 100%;
  margin: 0 auto;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Добавляем немного тени для глубины */
  border-radius: 8px; /* Слегка закругляем углы */
  background-color: #fff; /* Чистый белый фон для контента */
}

.my-books-header-input-button {
  background-color: #d0d0d0; /* Светлый серый цвет для кнопок по умолчанию */
  border: none;
  padding: 10px 20px;
  margin-right: 10px; /* Добавляем пространство между кнопками */
  border-radius: 20px; /* Закругляем углы кнопок */
  cursor: pointer;
  transition: background-color 0.3s, color 0.3s; /* Плавное изменение цветов */
}

.my-books-header-input-button:hover {
  background-color: #d1d1d1; /* Темнее при наведении */
}

.active-button {
  background-color: #4CAF50; /* Зеленый цвет для активной кнопки */
  color: #fff; /* Белый текст для лучшей читаемости */
}

.reservation-table {
  margin-top: 40px;
  width: 100%;
  border-collapse: collapse;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); /* Мягкая тень под таблицей */
}

.reservation-table th,
.reservation-table td {
  border: 1px solid #ddd; /* Светлее для границ */
  padding: 12px; /* Больше пространства для содержимого */
  text-align: left;
}

.reservation-table th {
  background-color: #f9f9f9; /* Светлее для заголовков */
  color: #333; /* Темный серый для контраста текста */
}

/* Добавляем стили для улучшения взаимодействия с элементами */
.reservation-table tr:hover {
  background-color: #f1f1f1; /* Слегка подсвечиваем строки при наведении */
}

/* Стиль для ссылок в таблице для улучшения взаимодействия */
.router-link-active, .router-link-exact-active {
  color: #4CAF50; /* Зеленый цвет для подчеркивания активности */
  text-decoration: underline; /* Подчеркивание для выделения */
}

</style>
