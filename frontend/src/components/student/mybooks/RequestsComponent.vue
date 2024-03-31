<template>
  <div class="mybooks" @click="handleClick">
    <div class="mybooks-request">
      <div class="mybooks-request-name">
        {{ data.BookName }}
      </div>
      <div class="mybooks-request-status">
        Статус заявки: {{ getStatusString(data.status) }}
      </div>
      <div v-if="data.status === '3'" class="mybooks-request-date">
        <p>{{ getStatusStringDate(data.status) }}: {{ data.date }}</p>
        <p>Вернуть до: {{ data.date_return }}</p>
      </div>
      <div v-if="data.status !== '3'" class="mybooks-request-date">
        {{ getStatusStringDate(data.status) }}: {{ data.date }}
      </div>
      <!-- Добавленная кнопка для status=1-2 -->
      <button v-if="data.status === '21'" @click.stop="handleCancelButton">Отменить бронирование</button>
      <button v-if="data.status === '11'" @click.stop="handleCancelRequest">Отменить заявку</button>
      <div v-if="showModal" class="modal" @click="preventClosing">
        <div class="modal-content">
          <p class="modal-content-text">Успешная отмена</p>
          <button @click="closeModal">OK</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  props: {
    data: Object,
    removeFromList: Function, // Переменная removeFromList
    removeFromList2: Function, // Переменная removeFromList2
  },
  data() {
    return {
      showModal: false,
    };
  },
  methods: {
    getStatusStringDate(status) {
      switch (status) {
        case "11":
          return 'Дата заявки';
        case "21":
          return 'Забраниравано на';
        case "3":
          return 'На руках';
        case "00":
          return 'Дата отмены';
        case "10":
          return 'Дата отмены';
        case "20":
          return 'Дата отмены';
        case "32":
          return 'Дата возврата';
        case "42":
          return 'Дата возврата';
        default:
          return 'Неизвестный статус';
      }
    },
    getStatusString(status) {
      console.log(status);
      switch (status) {
        case "11":
          return 'Ожидает свободную книгу';
        case "21":
          return 'Ожидает посещение';
        case "3":
          return 'На руках';
        case "00":
          return 'Отмена бронирования (Вы не забрали книгу в установленный день)';
        case "10":
          return 'Отмена бронирования (Вы отменили бронирование)';
        case "20":
          return 'Отмена бронирования (Библиотекарь отменил бронирование)';
        case "32":
          return 'Вернули книгу в срок';
        case "42":
          return 'Вернули книгу не в срок';
        default:
          return 'Неизвестный статус';
      }
    },
    async handleCancelButton() {
      try {
        const response = await axios.post('http://localhost:8000/cv/canselBooking', {
          UserId: this.$store.getters.getUserId,
          TitleId: this.data.bookId,
        });
        this.showModal = true;
        this.removeFromList2(this.data, '10');
        console.log(response);
      } catch (error) {
        console.error("Ошибка отмены бронирования:", error);
      }
    },
    handleClick() {
      // Программный переход по маршруту
      this.$router.push({
        name: 'MyBookView',
        params: { id: this.data.bookId },
        query: { bookData: this.data.date, status: this.data.status, return: this.data.date_return },
      });
    },
    closeModal() {
      this.showModal = false;
    },
    async handleCancelRequest() {
      try {
        const response = await axios.post('http://localhost:8000/cv/canselRequest', {
          UserId: this.$store.getters.getUserId,
          TitleId: this.data.bookId,
        });
        this.showModal = true;
        this.removeFromList(this.data); // Использование removeFromList
        console.log(response);
      } catch (error) {
        console.error("Ошибка отмены бронирования:", error);
      }
    },
    preventClosing(event) {
      event.stopPropagation();
    },
  },
};
</script>

<style>
.mybooks{
  text-decoration: none;
}
.mybooks-request{
  height: 119px;
  flex-shrink: 0;
  padding: 15px;

  width: 900px;
  min-width: 200px;
  margin: 10px;

  border-radius: 10px;
  border: 1px solid #000;
  background: #EBEBEB;
}

.mybooks-request:hover{
  background-color: #ffffff;
}

.mybooks-request-name{
  color: #000;
  font-family: Inter;
  font-size: 24px;
  font-style: normal;
  font-weight: 400;
  line-height: normal;
}

.mybooks-request-status{
  margin-top: 14px;
  color: #000;
  font-family: Inter;
  font-size: 19px;
  font-style: normal;
  font-weight: 400;
  line-height: normal;
}

.mybooks-request-date{
  margin-top: 10px;
  color: #000;
  text-align: right;
  font-family: Inter;
  font-size: 16px;
  font-style: normal;
  font-weight: 400;
  line-height: normal;
}
</style>
