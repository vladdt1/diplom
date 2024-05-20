<template>
  <div class="ser">
    <div class="search-container">
      <input type="text" id="searchInput" class="search-container-input" v-model="searchTerm" placeholder="Фамилия автора или название книги"/>
      <button @click="searchBooks" class="search-container-button">Поиск</button>
      <button @click="showModal = true" class="search-container-button">Расширенный поиск</button>
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
          <p class="results-container-book-card-aftor">Издательство: {{ book.publishing  }}</p>
        </div>
      </router-link>
    </div>
    <div v-else class="no-results">
      Данная литература отсутствует
    </div>
    <div class="search-container-else" v-if="books.length > displayedBooks.length">
      <button @click="loadMoreBooks" class="search-container-button">Показать ещё</button>
    </div>

    <!-- Модальное окно для расширенного поиска -->
    <div v-if="showModal" class="modal fade show" tabindex="-1" role="dialog" style="display: block;">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div style="display: flex; justify-content: space-between; margin-bottom: 20px" class="modal-header">
            <h2 class="modal-title">Расширенный поиск</h2>
            <a style="cursor: pointer; width: 40px;" data-dismiss="modal" aria-label="Close" @click="showModal = false">
              <span style=" width: 40px;font-size: 34px;" aria-hidden="true">&times;</span>
            </a>
          </div>
          <div class="modal-body">
            <select class="form-control mb-3 select-class" v-model="searchOption">
              <option disabled value="">Выберите параметр поиска</option>
              <option value="autor">Автор</option>
              <option value="title">Название</option>
              <option value="annotation">Описание</option>
              <option value="publishing">Издательство</option>
              <option value="recommended">Рекомендации</option>
            </select>
            <input type="text" class="form-control" v-model="searchTerm" placeholder="Введите поисковый запрос">
            <div style="display: flex; align-items: center;">
              <p>Год издания с </p> 
              <input type="text" style="width: 50px; margin: 0 10px;"/> 
              <p> по </p> 
              <input type="text" style="width: 50px; margin: 0 10px;"/>
            </div>
          </div>
          <div style="display: flex; justify-content: space-between;" class="modal-footer">
            <button type="button" style="width: 150px;" class="btn btn-secondary" @click="showModal = false">Закрыть</button>
            <button type="button" style="width: 150px;" class="btn btn-primary" @click="searchBooksAdvanced">Поиск</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      searchTerm: '',
      searchOption: '',
      books: [],
      displayedBooks: [],
      displayedBookCount: 5,
      showModal: false,
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
    async searchBooksAdvanced() {
      this.showModal = false; // Закрываем модальное окно
      const response = await axios.post('http://localhost:8000/cv/searchBooks/option', {
        searchTerm: this.searchTerm,
        searchOption: this.searchOption
      });
      this.books = response.data;
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

.select-class {
  height: auto; /* или укажите минимальную высоту, например, 38px */
  padding: 0.375rem 1.75rem 0.375rem 0.75rem; /* Bootstrap стили для padding */
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
