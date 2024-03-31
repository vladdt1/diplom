<template>
  <div class="student-component">
    <div class="student-component-card">
        <div class="student-component-card-info">
            <div class="student-component-card-info-avatar">
                <img class="student-component-card-info-img" 
                    v-if="student_info[0] && student_info[0].path" 
                    :src="require(`../../../assets/avatar/${student_info[0].path}`)" 
                    alt="avatar" 
                    loading="lazy"
                />
            </div>
            <div class="student-component-card-info-component" v-if="student_info.length > 0">
                <h3>Карточка студента</h3>
                <p>ФИО: {{ student_info[0].customer_fio }}</p>
                <p>Номер электронного студенческого билета: {{ student_info[0].login }}</p>
                <p>Группа: {{ student_info[0].customer_group }}</p>
                <div class="student-component-card-info-component-button">
                  <div><button @click="openModal">Вся информация о студенте</button></div>
                  <div><button @click="openModalBooking">Добавить новое бронирование</button></div>
                </div>
            </div>
        </div>
        <div class="student-component-card-detale">
            <div>
                <button
                    :class="{ 'active-button': activeButton === 'requests' }"
                    @click="setActiveButton('requests')"
                    class="my-books-header-input-button"
                >
                    Заявки
                </button>
                <button
                    :class="{ 'active-button': activeButton === 'onHands' }"
                    @click="setActiveButton('onHands')"
                    class="my-books-header-input-button"
                >
                    На руках
                </button>
                <button
                    :class="{ 'active-button': activeButton === 'returned' }"
                    @click="setActiveButton('returned')"
                    class="my-books-header-input-button"
                >
                    Вернул
                </button>
            </div>
            <table class="reservation-table">
              <thead v-if="activeButton === 'requests'">
                <tr>
                  <th>Артикул</th>
                  <th>Название</th>
                  <th>Статус</th>
                  <th>Изменить статус</th>
                  <th>Дата получения</th>
                </tr>
              </thead>
              <thead v-if="activeButton === 'onHands'">
                <tr>
                  <th>Артикул</th>
                  <th>Название</th>
                  <th>Статус</th>
                  <th>Изменить статус</th>
                  <th>Дата выдачи</th>
                </tr>
              </thead>
              <thead  v-if="activeButton === 'returned'">
                <tr>
                  <th>Артикул</th>
                  <th>Название</th>
                  <th>Статус</th>
                  <th>Дата возврата</th>
                  <th>Количество дней просрочки</th>
                </tr>
              </thead>
              <tbody v-if="activeButton === 'requests'">
                <RequestsComponent
                  v-for="item in request"
                  :key="item.id"
                  :data="item"
                  :removeFromList="removeFromList"
                />
              </tbody>
              
              <tbody v-if="activeButton === 'onHands'">
                <RequestsComponent
                  v-for="item in onHands"
                  :key="item.id"
                  :data="item"
                  :removeFromList="removeFromList"
                />
              </tbody>
              
              <tbody v-if="activeButton === 'returned'">
                <RequestsComponent
                  v-for="item in returnedBooks"
                  :key="item.id"
                  :data="item"
                />
                <div class="search-container-else" v-if="returned.length > returnedBooks.length">
                  <button @click="loadMoreBooks" class="search-container-button">Показать ещё</button>
                </div>
              </tbody>              
            </table>
        </div>
    </div>
    <!-- Модальное окно для информации о студенте -->
    <div v-if="modalOpen" class="modal">
        <div class="modal-content">
            <p>{{ student_info[0].customer_fio }}</p>
            <p>Номер электронного студенческого билета: {{ student_info[0].login }}</p>
            <p>Группа: {{ student_info[0].customer_group }}</p>
            <p>Email: {{ student_info[0].email }}</p>
            <p>Телефон: {{ student_info[0].phone }}</p>
            <p v-if="student_info[0].gender == 1">Пол: Мужской</p>
            <p v-else>Пол: Женский</p>
            <p>Дата рождения: {{ student_info[0].birth_date }}</p>
            <!-- Кнопка для закрытия модального окна -->
            <button @click="closeModal">Закрыть</button>
        </div>
    </div>
    <!-- Модальное окно для оповещания при бронировании -->
    <div v-if="modalOpenResult" class="modal">
      <div class="modal-content">
          <p v-if="status === 1">Нет свободного материала</p>
          <p v-else-if="status === 0">Успешное бронирование материала</p>
          <p v-else-if="status === 2">Ошибка бронирования</p>
          <!-- Кнопка для закрытия модального окна -->
          <button @click="closeModalResult">Закрыть</button>
      </div>
    </div>
    <!-- Модальное окно для нового бронирования -->
    <div v-if="modalOpenBooking" class="modal">
      <div class="modal-content">
          <p class="modal-content-text">{{ student_info[0].customer_fio }}</p>
          <p class="modal-content-text">Номер электронного студенческого билета: {{ student_info[0].login }}</p>
          <form @submit.prevent="submitForm" enctype="multipart/form-data">
            <label for="title"  class="modal-content-text">Инвентарный номер материала</label>
            <br>
            <br>
            <input class="modal-content-text"
              type="text"
              v-model="id_book"
              @input="searchBooks"
            />
            <ul class="modat_hint" v-if="suggestions.length && id_book.length > 3">
              <li v-for="suggestion in suggestions" :key="suggestion">
                {{ suggestion }}
              </li>
            </ul>
            <p class="modal-content-text">
              Выберите дату возврата
            </p>
            <select v-model="date" required class="addbook-card-input addbook-card-title">
              <option v-for="date in dateReturn" :key="date" :value="date">{{ date }}</option>
            </select>  
            <br>
            <br>
            <div class="button-flex">
                <!-- Кнопка для отправки формы -->
                <button type="submit" :disabled="id_book.trim() === '' || !suggestions.includes(id_book.trim())">Забронировать</button>
                <!-- Кнопка для закрытия модального окна -->
                <button @click="closeModalBooking">Закрыть</button>
            </div>
        </form>        
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import RequestsComponent from './RequestStudentComponent';

export default {
    components: {
        RequestsComponent,
  },
  data() {
    return {
      id_student: 0,
      student_info: [],
      modalOpen: false,
      modalOpenBooking: false,
      modalOpenResult: false,
      activeButton: 'requests',
      request: [],
      onHands: [],
      returned: [],
      bookData: [],
      id_book: '',
      status: '',
      returnedBooks: [],
      returnedBooksCount: 5,
      suggestions: [],
      date: '',
      dateReturn: ["Неделя", "2 недели", "Месяц", "6 месяцев"]
    };
  },
  async created() {
    this.id_student = this.$route.params.id;
    await this.getStudentInfo();
    this.setActiveButton('requests');
    this.bookingBook();
    this.requestBook();
    this.returnedBook();
  },
  methods: {
    searchBooks() {
      if (this.id_book.trim() === '') {
        this.suggestions = [];
        return;
      }

      this.suggestions = this.bookData.filter(item =>
        item.includes(this.id_book.trim())
      );
    },
    removeFromList(itemToRemove, newStatus) {
      if(newStatus == 1){
        const index = this.request.findIndex(item => item === itemToRemove);
        if (index !== -1) {
          this.request.splice(index, 1);
        }
      }else if(newStatus == 2){
        const index = this.request.findIndex(item => item === itemToRemove);
        if (index !== -1) {
          this.request[index].status = '3';
          const elementToMove = this.request.splice(index, 1)[0];
          this.onHands.push(elementToMove);
        }
      }else if(newStatus == 3){
        const index = this.onHands.findIndex(item => item === itemToRemove);
        if (index !== -1) {
          this.onHands.splice(index, 1);
          this.returnedBook();
        }
      }
    },
    async getStudentInfo() {
      try {
        const response = await axios.post('http://localhost:8000/cv/searchStudents/getInfoStudent', {
          id: this.id_student
        });

        this.student_info = response.data;
      } catch (error) {
        console.error("Ошибка получения информации о студенте:", error);
      }
    },
    openModal() {
      this.modalOpen = true;
    },
    closeModal() {
      this.modalOpen = false;
    },
    setActiveButton(buttonType) {
      this.activeButton = buttonType;
    },
    async openModalBooking(){
      this.modalOpenBooking = true;
      try {
        const response = await axios.post('http://localhost:8000/cv/searchBooks/titleBook');
        this.bookData = response.data;

      } catch (error) {
        console.error("Ошибка получения информации:", error);
      }
    },
    closeModalBooking(){
      this.modalOpenBooking = false;
    },
    closeModalResult(){
      this.modalOpenResult = false;
    },
    async requestBook(){
      try {
        const response = await axios.post('http://localhost:8000/cv/requestBook/request', {
          UserId: this.id_student,
        });
        for (var i = 0; i < response.data.length; i += 1) {
          var card_data = {
            bookId: response.data[i].title_id,
            status: response.data[i].status+'3',
            date: response.data[i].request_date,
            BookName: response.data[i].title
          }
          if (response.data[i].status == 2) {
            this.request.push(card_data)
          }
        }
      } catch (error) {
        console.error("Ошибка получения книг студента:", error);
      }
    },
    async bookingBook() {
      try {
        const response = await axios.post('http://localhost:8000/cv/requestBook', {
          UserId: this.id_student,
        });
        for (var i = 0; i < response.data.length; i += 1) {
          var card_data = {
            bookId: response.data[i].title_id,
            status: response.data[i].status,
            date: response.data[i].date_booking,
            BookName: response.data[i].title
          }
          if (response.data[i].status == 3) {
            this.onHands.push(card_data)
          }
        }
      } catch (error) {
        console.error("Ошибка получения книг студента:", error);
      }
    },
    async returnedBook(){
      try {
        const response = await axios.post('http://localhost:8000/cv/returnedBook', {
          UserId: this.id_student,
        });
        for (var i = 0; i < response.data.length; i += 1) {
          var card_data = {
            bookId: response.data[i].title_id,
            status: response.data[i].status+'2',
            date: response.data[i].returned_date,
            BookName: response.data[i].title,
            delay: response.data[i].delay
          }
          this.returned.push(card_data)
        }
        this.returnedBooksCount = 5;
        this.returnedBooks = this.returned.slice(0, this.returnedBooksCount)
      } catch (error) {
        console.error("Ошибка получения книг студента:", error);
      }
    },
    async submitForm() {
      try {
        // Получение текущей даты
        const today = new Date();

        // Извлечение года
        const year = today.getFullYear();

        // Извлечение месяца (добавляем 1, так как месяцы в JavaScript нумеруются с 0)
        let month = today.getMonth() + 1;
        // Если месяц состоит из одной цифры, добавляем перед ней 0
        month = month < 10 ? '0' + month : month;

        // Извлечение дня
        let day = today.getDate();
        // Если день состоит из одной цифры, добавляем перед ней 0
        day = day < 10 ? '0' + day : day;

        // Формирование строки в формате YYYY-MM-DD
        const formattedDate = `${year}-${month}-${day}`;
        const response = await axios.post('http://localhost:8000/cv/addbooking/libr', {
          idBook: this.id_book,
          UserId: this.id_student,
          bookingDate: formattedDate,
          date_return: this.date
        });
        var newMaterial = {
          bookId: this.id_book,
          status: '3',
          date: formattedDate,
          BookName: response.data.title,
        }
        this.onHands.push(newMaterial);
        this.closeModalBooking();
        this.modalOpenResult = true;
        this.status = response.data.status;
      } catch (error) {
        console.error('Ошибка при добавлении книги:', error);
      }
    },
    loadMoreBooks() {
      this.returnedBooksCount += 5;
      this.returnedBooks = this.returned.slice(0, this.returnedBooksCount);
    },
  },
};
</script>

<style>
.student-component {
  padding: 20px;
  font-family: 'Roboto', sans-serif; /* Использование более чистого и профессионального шрифта */
}

.student-component-card {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Добавление мягкой тени для "поднятого" эффекта */
  border-radius: 8px; /* Закругленные углы */
  overflow: hidden; /* Убедитесь, что все внутренние элементы также уважают закругленные углы */
}

.student-component-card-info, .student-component-card-detale {
  background: #FFFFFF; /* Чистый белый фон */
}

.student-component-card-info {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 20px;
  border-bottom: 1px solid #EEEEEE; /* Мягкий разделитель */
}

.student-component-card-info-avatar {
  flex-shrink: 0; /* Убедитесь, что аватар не сжимается */
}

.student-component-card-info-img {
  width: 100px;
  height: 100px; /* Устанавливаем фиксированный размер для изображения */
  border-radius: 50%; /* Круглый аватар */
  object-fit: cover; /* Убедитесь, что изображение красиво заполняет пространство */
}

.student-component-card-info-component h3 {
  margin-top: 0;
  color: #333333;
  font-size: 24px;
}

.student-component-card-info-component p {
  color: #666666; /* Слегка затемненный текст для лучшего чтения */
}

.student-component-card-info-component-button {
  display: flex;
  gap: 10px; /* Добавление пространства между кнопками */
}

button {
  border: none;
  padding: 10px 20px;
  border-radius: 20px; /* Мягкие закругленные кнопки */
  background-color: #4CAF50; /* Приятный зеленый цвет */
  color: white;
  cursor: pointer;
  transition: background-color 0.3s; /* Плавное изменение фона при наведении */
}

button:hover {
  background-color: #45a049;
}

.modal {
  /* Стили для затемненного фона */
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  border-radius: 8px; /* Добавление закругленных углов для модальных окон */
}

/* Дополнительные стили для активных кнопок и таблиц */
.active-button {
  background-color: #345BEB; /* Изменение цвета для активной кнопки */
}

.my-books-header-input-button {
  background: transparent;
  color: #4CAF50;
  padding: 5px 10px;
  margin: 0 5px;
  font-size: 16px;
}

.reservation-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.reservation-table th, .reservation-table td {
  text-align: left;
  padding: 8px;
  border-bottom: 1px solid #EEEEEE;
}
.button-flex{
  display: flex;
  justify-content: space-between;
}
</style>
