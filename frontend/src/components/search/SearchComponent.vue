<template>
  <div class="ser">
    <div class="search-container">
      <input type="text" id="searchInput" class="search-container-input" v-model="searchTerm" />
      <button @click="searchBooks" class="search-container-button">Поиск книг</button>
    </div>

    <div v-if="books.length > 0" class="results-container">
      <router-link
        v-for="book in displayedBooks"
        :key="book.title_id"
        :to="{ name: 'bookSearch', params: { id: book.title_id }}"
        class="results-container-router"
      >
        <div class="results-container-book-card">
          <p class="results-container-book-card-name">{{ book.title }}</p>
          <p class="results-container-book-card-aftor">Автор: {{ book.autor }}</p>
        </div>
      </router-link>
    </div>
    <div v-else class="no-results">
      Данная литература отсутствует
    </div>
    <div class="search-container-else" v-if="books.length > displayedBooks.length">
      <button @click="loadMoreBooks" class="search-container-button">Показать ещё</button>
    </div>
    
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      searchTerm: '',
      books: [],
      displayedBooks: [],
      displayedBookCount: 5,
    };
  },
  async created() {
    const searchTermFromUrl = this.$route.query.q;
    if (searchTermFromUrl) {
      this.searchTerm = searchTermFromUrl;
      await this.searchBooks();
    } else {
      await this.loadInitialBooks();
    }
    this.displayedBooks = this.books.slice(0, this.displayedBookCount)
  },
  methods: {
    async loadInitialBooks() {
      try {
        const response = await axios.get('http://localhost:8000/cv/searchBooks/initial');

        this.books = response.data;
      } catch (error) {
        console.error("Error loading initial books:", error);
      }
    },
    async searchBooks() {
      try {
        const trimmedSearchTerm = this.searchTerm.trim();

        const lowerCaseSearchTerm = trimmedSearchTerm.toLowerCase();

        // Используем объект router для изменения URL
        this.$router.push({ name: 'Search', query: { q: lowerCaseSearchTerm } });

        // Выполняем запрос к API
        const response = await axios.post('http://localhost:8000/cv/searchBooks', {
          searchTerm: lowerCaseSearchTerm
        });
        this.displayedBooks = [];
        this.displayedBookCount = 5;
        this.books = response.data;
        this.displayedBooks = this.books.slice(0, this.displayedBookCount)
      } catch (error) {
        console.error("Error while searching books:", error);
      }
    },
    loadMoreBooks() {
      this.displayedBookCount += 5;
      this.displayedBooks = this.books.slice(0, this.displayedBookCount);
    },
  },
};
</script>

<style>
.ser{
  width:100%
}
.search-container {
  display: flex;
  justify-content: center; /* Центрируем поисковую строку и кнопку */
  padding: 20px;
}

.search-container-input {
  width: 60%; /* Настройте по предпочтению */
  padding: 10px; /* Увеличенный отступ для лучшей видимости текста */
  border: 2px solid #ddd; /* Субтильный цвет границы */
  border-radius: 20px; /* Скругленные углы для поля ввода */
  font-size: 16px; /* Увеличенный размер шрифта для лучшей читаемости */
}

.search-container-button {
  padding: 10px 20px; /* Корректируем отступы, чтобы лучше вписать текст кнопки */
  margin-left: 10px;
  background-color: #0056b3; /* Более яркий цвет для кнопки */
  color: #ffffff;
  border: none; /* Убираем стандартную границу */
  border-radius: 20px; /* Совпадение с закругленными углами поля ввода */
  cursor: pointer; /* Изменяем курсор на указатель, чтобы указать на кликабельность */
  transition: background-color 0.3s; /* Плавный переход для эффекта наведения */
}

.search-container-button:hover {
  background-color: #003d7a; /* Затемняем цвет кнопки при наведении для обратной связи */
}

.results-container {
  display: grid; /* Используем сетку для лучшего выравнивания и адаптивности */
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Адаптивные колонки */
  gap: 20px; /* Пространство между карточками */
  padding: 20px;
}

.results-container-book-card {
  display: flex; /* Используем flexbox для внутренней компоновки карточки */
  flex-direction: column; /* Элементы располагаем вертикально */
  justify-content: space-between; /* Распределяем пространство равномерно */
  padding: 20px;
  border-radius: 15px; /* Скругленные углы для карточек */
  border: 1px solid #ddd; /* Субтильный цвет границы */
  background: linear-gradient(145deg, #f5f5f5, #ffffff); /* Градиентный фон для современного вида */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Мягкая тень для глубины */
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out; /* Плавные переходы для эффектов наведения */
}

.results-container-book-card:hover {
  transform: translateY(-5px); /* Поднимаем карточку немного при наведении */
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Увеличиваем тень для эффекта поднятия */
}

.results-container-book-card-name, .results-container-book-card-aftor {
  margin: 5px 0; /* Настройка интервалов */
  color: #333; /* Более темный текст для лучшей читаемости */
}

.results-container-router {
  text-decoration: none; /* Убираем подчеркивание */
  color: inherit; /* Наследуем цвет текста от родительского элемента */
}

.results-container-book-card-name {
  font-weight: 600; /* Делаем заголовок более выделяющимся */
}

.no-results {
  text-align: center; /* Центрирование текста */
  color: #d9534f; /* Используем цвет danger из bootstrap для согласованности */
  font-size: 18px; /* Увеличиваем размер шрифта для видимости */
}

.search-container-else {
  display: flex;
  justify-content: center; /* Центрируем кнопку */
  padding: 20px;
}
</style>
