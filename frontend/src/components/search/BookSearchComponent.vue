<template>
    <div class="book-search">
        <div class="book-search-button">
            <div class="book-search-button-booking">
                <button 
                  v-if="userStatus == 0 && this.statusBook == '0' && book.bookCount > 0 && this.statusRequest == '0'" 
                  @click="openBookingModal"
                  class="search-container-button"
                >
                    Забронировать
                </button>
                <button 
                  v-if="userStatus == 0 && this.statusBook == '0' && book.bookCount == 0 && this.statusRequest == '0'" 
                  @click="openRequestModal"
                  class="search-container-button"
                >
                    Оповестить, когда появится
                </button>
                <button 
                  v-if="userStatus == 1" 
                  @click="openModalEdit" 
                  class="search-container-button">
                    Редактировать
                </button>
            </div>
            <div>
              <p class="book-search-card-text"><b>Кол-во книг в библиотеке:</b> {{ book.bookCount }}</p>
            </div>
            <div class="book-search-button-exit">
                <button @click="goBack" class="search-container-button">Вернуться</button>
            </div>
        </div>
        <div class="book-search-text">
            <div class="book-search-card">
                <h2 class="book-search-card-name">{{ book.title }}</h2>
                <p class="book-search-card-text"><b>Автор:</b> {{ book.autor }}</p>
                <p class="book-search-card-text"><b>Артикул:</b> {{ book.title_id }}</p>
                <p class="book-search-card-text"><b>Издательство:</b> {{ book.publishing }}</p>
                <p class="book-search-card-text"><b>Страниц:</b> {{ book.page }}</p>
                <p class="book-search-card-text"><b>Аннотация:</b> {{ book.annotation }}</p>
                <p v-if="book.recommended !== '-'" class="book-search-card-text"><b>Кому рекомендовано:</b> {{ book.recommended }}</p>
                <p v-if="book.ydk !== '-'" class="book-search-card-text"><b>УДК:</b> {{ book.ydk }}</p>
                <p v-if="book.bbk !== '-'" class="book-search-card-text"><b>ББК:</b> {{ book.bbk }}</p>
            </div>
            <div>
                <img class="book-search-card-img" v-if="book.path" :src="require(`../../assets/book/${book.path}`)" alt="book" loading="lazy"/>
            </div>
        </div>
        <instanceComponent v-if="userStatus == 1" />
        <!-- Модальное окно для оповещения -->
        <div class="modal" v-if="isRequestModalOpen">
          <div class="modal-content">
            <h2>Когда книга появится Вам прийдет сообщение на почту</h2>
            <div class="checkbox-container">
              <input type="checkbox" v-model="isChecked" />
              <p>Я подтверждаю отправку сообщения на почту</p>
            </div>
            <div class="modal-content-button">
              <button @click="confirmRequest">Подтвердить</button>
              <button @click="closeRequestModal">Отмена</button>
            </div>
          </div>
        </div>
        <!-- Модальное окно для бронирования -->
        <div class="modal" v-if="isBookingModalOpen">
          <div class="modal-content">
            <h2>Выберите дату бронирования</h2>
            <input type="date" v-model="bookingDate" :min="$options.methods.getMinDate()"/>
            <div class="modal-content-button">
              <button @click="confirmBooking">Забронировать</button>
              <button @click="closeBookingModal">Отмена</button>
            </div>
          </div>
        </div>
        <!-- Модальное окно для редактирования -->
        <div class="modall" v-if="isModalOpen">
            <div class="modall-content">
              <h2 class="modall-content-title">Редактирование книги</h2>
              <div class="modall-content-email">
                <label for="editPhone">Название</label>
                <input type="text" id="editPhone" v-model="editedBookData.title"/>
              </div>
    
              <div class="modall-content-phone">
                <label for="editPhone">Автор</label>
                <input type="text" id="editPhone" v-model="editedBookData.autor"/>
              </div>

              <div class="modall-content-phone">
                <label for="editPhone">Артикул</label>
                <input type="text" id="editPhone" v-model="editedBookData.title_id"/>
              </div>

              <div class="modall-content-phone">
                <label for="editPhone">Издательство</label>
                <input type="text" id="editPhone" v-model="editedBookData.publishing"/>
              </div>

              <div class="modall-content-phone">
                <label for="editPhone">Аннотация</label>
                <input type="text" id="editPhone" v-model="editedBookData.annotation"/>
              </div>
    
              <div class="modall-content-group-button">
                <button @click="saveChanges">Сохранить</button>
                <button @click="closeModal">Закрыть</button>
              </div>
            </div>
          </div>
    </div>
  </template>
  
<script>
  import axios from 'axios';
  import instanceComponent from './instanceComponent.vue';
  export default {
    components: {
      instanceComponent
    },
    data() {
      return {
        book: [],
        sessionStatus: this.$store.getters.getuserStatus,
        editedBookData: {},
        isModalOpen: false,
        isBookingModalOpen: false,
        isRequestModalOpen: false,
        isChecked: false,
        statusRequest: '',
        bookingDate: '',
        statusBook: '',
        props_instance: []
      };
    },
    async created() {
      try {
        const dataCreate = {
          UserId: this.$store.getters.getUserId,
          idBook: this.$route.params.id
        };

        const response = await axios.post('http://localhost:8000/cv/searchBooks/bookDetals', dataCreate);
        this.book = response.data;

        const responseStatus = await axios.post('http://localhost:8000/cv/searchBooks/checkBookUserStatus', dataCreate);
        this.statusBook = responseStatus.data.result1
        this.statusRequest = responseStatus.data.result2
      } catch (error) {
        console.error('Error while adding booking:', error);
      }
    },
    computed: {
      userStatus() {
        return this.$store.getters.getUserStatus;
      },
    },
    methods: {
        goBack() {
            this.$router.go(-1); // Перейти на предыдущую страницу в истории
        },
        openBookingModal() {
          this.isBookingModalOpen = true;
        },
        closeBookingModal() {
          this.isBookingModalOpen = false;
        },

        openRequestModal() {
          this.isRequestModalOpen = true;
        },
        closeRequestModal() {
          this.isRequestModalOpen = false;
        },

        openModalEdit() {
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },

        confirmRequest(){
          if(this.isChecked == true){
            // Отправка данных на сервер
            const data = {
              UserId: this.$store.getters.getUserId,
              idBook: this.$route.params.id
            };

            axios.post('http://localhost:8000/cv/addrequest', data)
              .then(response => {
                if (response.data.success) {
                  this.closeRequestModal();
                  this.statusRequest = '1';
                }
              })
              .catch(error => {
                console.error('Error while adding booking:', error);
              });
          }else{
            alert('Необходимо подтвердить согласие')
          }
        },
        confirmBooking() {
          // Отправка данных на сервер
          const data = {
            UserId: this.$store.getters.getUserId,
            idBook: this.$route.params.id,
            bookingDate: this.bookingDate,
          };

          axios.post('http://localhost:8000/cv/addbooking', data)
            .then(response => {
              if (response.data.success) {
                this.closeBookingModal();
                this.statusBook = '1';
              }
            })
            .catch(error => {
              console.error('Error while adding booking:', error);
            });
        },
        async saveChanges() {
            try {
                // Отправка изменений на сервер
                const response = await axios.post('http://localhost:8000/cv/book/update', {
                    IdBook: this.book.title_id,
                    updatedBookData: this.editedBookData,
                });
                
                this.book = { ...this.book, ...this.editedBookData };

                console.log('Сервер ответил:', response.data);

                // Закрываем модальное окно после успешного сохранения
                this.isModalOpen = false;
            } catch (error) {
                console.error('Error saving changes:', error);
            }
        },
        getMinDate() {
          const today = new Date();
          const year = today.getFullYear();
          const month = today.getMonth() + 1; // Месяцы в JavaScript начинаются с 0
          const day = today.getDate();

          // Форматируем дату в строку "YYYY-MM-DD"
          const formattedDate = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;

          return formattedDate;
        },
    },
  };
</script>
  
<style>
.book-search {
  max-width: 100%;
  margin: 0 auto;
  padding: 40px;
  background-color: #fff;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  font-family: 'Poppins', sans-serif;
}

.book-search-button, .book-search-text {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.book-search-button-booking, .book-search-button-exit, .modal-content-button, .modall-content-group-button {
  display: flex;
  gap: 20px;
}

.search-container-button {
  background-color: #4CAF50;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.search-container-button:hover {
  background-color: #43A047;
}
.book-search-text {
  border: 1px solid #ccc; /* Легкая серая рамка вокруг блока */
  padding: 20px; /* Добавляем немного внутреннего отступа для пространства вокруг текста */
  margin-bottom: 20px; /* Отступ снизу для разделения от других элементов, если они есть */
  border-radius: 5px; /* Слегка закругляем углы рамки */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Мягкая тень для создания эффекта "плавающего" блока */
  background-color: #fff; /* Белый фон для контраста с тенью и обеспечения читаемости */
}
.book-search-card-text, .book-search-card-name {
  margin: 10px 0;
}

.book-search-card-name {
  color: #333;
  margin-top: 0;
  margin-bottom: 16px; /* Больше пространства между заголовком и текстом */
  font-weight: bold;
}

.book-search-card-text {
  color: #666; /* Слегка затемненный цвет текста для комфортного чтения */
  line-height: 1.6; /* Улучшаем прочитаемость за счет увеличения межстрочного интервала */
}

.book-search-card-img {
  max-width: 300px;
  height: auto;
  border: none;
  border-radius: 5px;
}

/* Модальные окна */
.modal, .modall {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal-content, .modall-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 100%;
}

.modall-content-title {
  color: #333;
  font-size: 24px;
}

label {
  display: block;
  margin-bottom: 5px;
  color: #333;
  font-size: 16px;
}

input[type="text"], input[type="date"] {
  width: calc(100% - 20px);
  padding: 10px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

input[type="checkbox"] {
  margin-right: 10px;
}

.checkbox-container p {
  margin: 0;
}

/* Подчеркнем доступность кнопок с помощью изменения стилей при наведении и фокусе */
button:focus, button:hover, input[type="text"]:focus, input[type="date"]:focus {
  outline: none;
  border-color: #4CAF50;
}

</style>
  