<template>
  <div class="my-books">
    <div class="my-books-header">
      <div class="my-books-header-input">
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
    </div>
    <div class="my-books-card">
      <template v-if="activeButton === 'requests'">
        <div v-for="item in request" :key="item.id">
          <RequestsComponent :data="item" :removeFromList="removeFromList" :removeFromList2="removeFromList2" />
        </div>
      </template>
      <template v-else-if="activeButton === 'onHands'">
        <div v-for="item in onHands" :key="item.id">
          <RequestsComponent :data="item" />
        </div>
      </template>
      <template v-else-if="activeButton === 'returned'">
          <RequestsComponent
            v-for="item in returnedBooks"
            :key="item.id"
            :data="item"
          />
          <div class="search-container-else" v-if="returned.length > returnedBooks.length">
            <button @click="loadMoreBooks" class="search-container-button">Показать ещё</button>
          </div>
      </template>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import RequestsComponent from './RequestsComponent.vue';

export default {
  components: {
    RequestsComponent,
  },
  data() {
    return {
      activeButton: 'requests',
      request: [],
      onHands: [],
      returned: [],
      returnedBooks: [],
      returnedBooksCount: 5,
    };
  },
  methods: {
    setActiveButton(buttonType) {
      this.activeButton = buttonType;
    },
    removeFromList(itemToRemove) {
      const index = this.request.findIndex(item => item === itemToRemove);
      if (index !== -1) {
        this.request.splice(index, 1);
      }
    },
    removeFromList2(itemToRemove, newStatus) {
      const index = this.request.findIndex(item => item === itemToRemove);
      if (index !== -1) {
        this.request[index].status = newStatus;
      }
    },
    async bookingBook() {
      try {
        const response = await axios.post('http://localhost:8000/cv/requestBook', {
          UserId: this.$store.getters.getUserId,
        });
        for (var i = 0; i < response.data.length; i += 1) {
          var card_data = {
            bookId: response.data[i].title_id,
            status: response.data[i].status,
            date: response.data[i].date_booking,
            BookName: response.data[i].title,
            date_return: response.data[i].date_return
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
          UserId: this.$store.getters.getUserId,
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
    async canselRequestBook(){
      try {
        const response = await axios.post('http://localhost:8000/cv/requestBook/canselRequest', {
          UserId: this.$store.getters.getUserId,
        });
        console.log(response.data)
        for (var i = 0; i < response.data.length; i += 1) {
          var card_data = {
            bookId: response.data[i].title_id,
            status: response.data[i].status+"0",
            date: response.data[i].returned_date,
            BookName: response.data[i].title
          }
          this.request.push(card_data)
        }
      } catch (error) {
        console.error("Error while searching book:", error);
      }
    },
    async requestBook(){
      try {
        const response = await axios.post('http://localhost:8000/cv/requestBook/request', {
          UserId: this.$store.getters.getUserId,
        });

        for (var i = 0; i < response.data.length; i += 1) {
          var card_data = {
            bookId: response.data[i].title_id,
            status: response.data[i].status+"1",
            date: response.data[i].request_date,
            BookName: response.data[i].title
          }
          this.request.push(card_data)
        }
      } catch (error) {
        console.error("Error while searching book:", error);
      }
    },
    loadMoreBooks() {
      this.returnedBooksCount += 5;
      this.returnedBooks = this.returned.slice(0, this.returnedBooksCount);
    },
  },
  created() {
    this.setActiveButton('requests');
    this.bookingBook();
    this.requestBook();
    this.canselRequestBook();
    this.returnedBook();
  },
};
</script>

<style>
.my-books {
  width: 80%;
  padding: 20px;
}

.my-books-header {
  display: flex;
  justify-content: space-between;
}

.my-books-header-input-button {
  width: 30vh;
  height: 37px;
  margin-left: 10px;
  border-radius: 20px;
  background: #000;
  color: #fff;
  transition: background-color 0.3s ease;
}

.my-books-header-input-button:hover {
  background-color: rgb(117, 117, 117);
}

.my-books-header-input-button:active {
  background-color: rgb(117, 117, 117);
}

.active-button {
  background-color: #ff0000;
  color: #fff;
  font-weight: bold;
}

.my-books-header-search-input {
  width: 300px;
  height: 35px;

  border-radius: 7px;
}

.my-books-header-search-button {
  width: 12vh;
  height: 37px;
  color: #fff;
  background-color: black;
  border-radius: 7px;

  margin-left: 10px;
}

.my-books-header-search-button:hover {
  background-color: rgb(63, 63, 63);
}

.my-books-card {
  margin-top: 10px;
  padding: 20px;
}
</style>
