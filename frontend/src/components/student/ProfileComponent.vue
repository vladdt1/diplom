<template>
  <div>
    <div class="student_profile">
      <div class="student_info">
        <p class="student_info_name">{{ profileData.customer_fio }}</p>
        <p class="student_info_login">Номер электронного студенческого билета: {{ profileData.login }}</p>
        <div class="student_info_date">
          <p class="student_info_date_i">Пол: {{ parseInt(profileData.gender) === 0 ? 'мужской' : 'женский' }}</p>
          <p class="student_info_date_i">Email: {{ profileData.email }}</p>
          <p class="student_info_date_i">Телефон: {{ profileData.phone }}</p>
          <p class="student_info_date_i">Уведомление на почту:</p>
          <ul>
            <li class="student_info_date_i">Бронирование материалов</li>
            <li class="student_info_date_i">Возврат материалов</li>
          </ul>
        </div>
      </div>
      <div class="student_img">
        <img class="student_img_photo" v-if="profileData.path" :src="require(`../../assets/avatar/${profileData.path}`)" alt="avatar" loading="lazy"/>
      </div>
      <div class="student_remove">
        <button class="student_remote_button" @click="openModal">
          <p class="student_remote_button_text">
            Редактировать
          </p>
        </button>
      </div>
      <!-- Модальное окно -->
      <div class="modal" v-if="isModalOpen">
        <div class="modal-content">
          <h2 class="modal-content-title">Редактирование профиля</h2>
          <div class="modal-content-email">
            <label for="editEmail">Новый Email:</label>
            <input class="modal-content-input" type="text" id="editEmail" v-model="editedProfileData.email"/>
          </div>

          <div class="modal-content-phone">
            <label for="editPhone">Новый телефон:</label>
            <input class="modal-content-input" type="text" id="editPhone" v-model="editedProfileData.phone"/>
          </div>

          <div class="modal-content-group-button">
            <button @click="saveChanges">Сохранить</button>
            <button @click="closeModal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
    
<script>
import axios from 'axios';

export default {
  data(){
    return{
      profileData: {}, // Данные профиля
      editedProfileData: {},
      isModalOpen: false,
    }
  },
  mounted() {
    this.loadProfile();
  },
  methods: {
    async loadProfile() {
      try {
        // Получение id из хранилища Vuex
        const sessionId = this.$store.state.userId;

        // Выполнение GET-запроса к серверу
        const response = await axios.get('http://localhost:8000/cv/profile', {
          params: { sessionId },
        });

        // Обработка данных, полученных от сервера
        this.profileData = response.data;

      } catch (error) {
        console.error('Error loading profile:', error);
      }
    },
    openModal() {
      this.isModalOpen = true;
    },

    closeModal() {
      this.isModalOpen = false;
    },
    async saveChanges() {
      try {
        // Отправка изменений на сервер
        const response = await axios.post('http://localhost:8000/cv/profile/update', {
          sessionId: this.$store.state.userId,
          updatedProfileData: this.editedProfileData,
        });
         
        console.log('Сервер ответил:', response.data);

        // Закрываем модальное окно после успешного сохранения
        this.isModalOpen = false;

        this.loadProfile();

      } catch (error) {
        console.error('Error saving changes:', error);
      }
    },
  },
};
</script>
    
<style>
.student_profile {
  display: grid;
  justify-content: space-between;
  grid-template-columns: 5fr 4fr 1fr;
  gap: 20px;
  padding: 20px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  border-radius: 10px;
  background-color: #fff;
}

.student_info {
  padding: 20px;
}

.student_info_name, .student_info_login, .student_info_date_i {
  margin-bottom: 20px;
  font-family: 'Roboto', sans-serif;
  color: #333;
}

.student_info_name {
  font-size: 30px;
  font-weight: 500;
}

.student_info_login, .student_info_date_i {
  font-size: 18px;
}

.student_img_photo {
  width: 250px;
  height: 250px;
  border-radius: 125px;
  object-fit: cover;
  border: 4px solid #eee;
  transition: transform 0.3s ease;
}

.student_img_photo:hover {
  transform: scale(1.05);
}

.student_remote_button {
  background: #007BFF;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}

.student_remote_button_text {
  color: #fff;
  font-family: 'Roboto', sans-serif;
  font-size: 18px;
}

.student_remote_button:hover {
  background-color: #0056b3;
}

.modal-content {
  background-color: #fff;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modal-content-input, .modal-content button {
  font-family: 'Roboto', sans-serif;
}

.modal-content button {
  padding: 10px 15px;
  margin-top: 10px;
  width: 48%;
}

.modal-content-title {
  font-size: 24px;
  margin-bottom: 25px;
}

</style>