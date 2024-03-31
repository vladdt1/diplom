<template>
        <tr>
          <td>{{ data.bookId }}</td>
          <td>
            <router-link :to="'/search-book/' + data.bookId" class="black-text">{{ data.BookName }}</router-link>
          </td>
          <td>{{ getStatusString(data.status) }}</td>
          <td v-if="data.status === '23'">
            <button 
              class="request-student-comp-status-rename" 
                @click.stop="showModalStatus = true"
              >
                  Подтвердить выдачу
            </button>
            <button class="modal-content-text-li" 
              v-if="data.status === '23'"
                @click="changeStatus(1)"
              >
                Отменить
            </button>
            <!-- Модальное окно для смены статуса -->
            <div v-if="showModalStatus" class="modal" @click="closeModalStatus">
              <div class="modal-content" @click.stop>
                <h1>
                  Окно подтверждения выдачи
                </h1>
                <p class="modal-content-text">
                  Выберите инвентарный номер
                </p>
                <select v-model="inventory_number" required class="addbook-card-input addbook-card-title">
                  <option v-for="book in freeBook" :key="book" :value="book">{{ book }}</option>
                </select> 
                <p class="modal-content-text">
                  Выберите дату возврата
                </p>
                <select v-model="date" required class="addbook-card-input addbook-card-title">
                  <option v-for="date in dateReturn" :key="date" :value="date">{{ date }}</option>
                </select>                
                <div class="modal-content-button">
                  <button class="modal-content-text-li" 
                    v-if="data.status === '23'" 
                    @click="changeStatus(2)"
                  >
                    Подтвердить выдачу
                  </button>
                  <button @click="closeModalStatus">Отмена</button>
                </div>
              </div>
            </div>
            <!-- Модальное окно для отображения успешной статуса -->
            <div v-if="showModal" class="modal">
              <div class="modal-content">
                <p class="modal-content-text">Успешное изменение статуса!</p>
                <button @click="closeModal">OK</button>
              </div>
            </div>
          </td>
          <td v-if="data.status === '3'">
            <button 
              @click="changeStatus(3)"
            >
              Подтвердить возврат
            </button>
          </td>
          <td>{{ data.date }}</td>
          <th v-if="data.status === '42'">{{ data.delay }}</th>
          <th v-if="data.status === '32'">0</th>
        </tr>
</template>

<script>
import axios from 'axios';
export default {
  props: {
    data: Object,
    removeFromList: Function
  },
  data() {
    return {
      inventory_number: '',
      showModalStatus: false,
      showModal: false,
      freeBook: [],
      date: '',
      dateReturn: ["Неделя", "2 недели", "Месяц", "6 месяцев"]
    };
  },
  async created() {
    try {
        const response = await axios.post('http://localhost:8000/cv/searchBooks/inventoryNumber', {
          bookid: this.data.bookId
        });
        this.freeBook = response.data;
      } catch (error) {
        console.error("Ошибка изменения статуса книги для студента:", error);
      }
  },
  methods: {
    getStatusStringDate(status) {
      switch (status) {
        case "1":
          return 'Дата заявки';
        case "23":
          return 'Забраниравано на';
        case "3":
          return 'На руках c';
        case "32":
          return 'Дата возврата';
        case "42":
          return 'Дата возврата';
        default:
          return 'Неизвестный статус';
      }
    },
    getStatusString(status) {
      switch (status) {
        case "1":
          return 'Ожидает свободную книгу';
        case "23":
          return 'Ожидает посещение';
        case "3":
          return 'На руках';
        case "32":
          return 'Вернули книгу в срок';
        case "42":
          return 'Вернули книгу не в срок';
        default:
          return 'Неизвестный статус';
      }
    },
    handleClick() {
      // Программный переход по маршруту
      this.$router.push({
        name: 'MyBookView',
        params: { id: this.data.bookId },
        query: { bookData: this.data.date, status: this.data.status },
      });
    },
    closeModalStatus(event) {
      this.showModalStatus = false;
      event.stopPropagation();
    },
    closeModal(event){
      this.showModal = false;
      event.stopPropagation();
    },
    async changeStatus(status){
      try {
        await axios.post('http://localhost:8000/cv/searchStudents/renameStatus', {
          id: this.$route.params.id,
          status: status,
          bookid: this.data.bookId,
          inventory_number: this.inventory_number,
          date_return: this.date
        });
        this.showModalStatus = false;
        this.showModal = true;
        this.removeFromList(this.data, status);
      } catch (error) {
        console.error("Ошибка изменения статуса книги для студента:", error);
      }
    },
  },
};
</script>

<style>
.request-student-comp {
  width: 100%;
}

.black-text {
  color: black;
}

.request-student-comp-info-name,
.request-student-comp-info-status {
  font-size: 18px;
  font-weight: bold;
}

.request-student-comp-info-name {
  border-bottom: 1px solid #ccc;
}

.request-student-comp-status {
  padding: 10px;
}

.request-student-comp-status button {
  margin-right: 10px;
}

.request-student-comp-status p {
  margin: 0;
  font-size: 16px;
}

.modal-content-button{
  margin-top: 30px;
  display: flex;
  justify-content: space-between;
}

</style>
