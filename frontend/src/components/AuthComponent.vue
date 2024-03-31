<template>
  <div class="auth">
    <div class="auth_form">
      <div class="auth_text">Веб сервис электронной библиотеки</div>
      <form style="padding: 55px;" @submit.prevent="login">
        <div class="mb-3">
          <label for="login" class="form-label">Ваш логин</label>
          <input v-model="loginData.login" type="text" class="form-control" id="login" aria-describedby="emailHelp" required autocomplete="username">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Пароль</label>
          <input v-model="loginData.password" type="password" class="form-control" id="password" autocomplete="current-password" required>
        </div>
        <div class="auth-flex">
          <button type="submit" class="btn sbut">Войти</button>
          <div v-if="errorMessage" class="error-message">
            {{ errorMessage }}
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      loginData: {
        login: '',
        password: '',
      },
      errorMessage: '', // Переменная для хранения сообщения об ошибке
    };
  },
  methods: {
    async login() {
      try {
        const logData = {
          login: this.loginData.login,
          password: this.loginData.password
        }
        const response = await axios.get('http://localhost:8000/cv/auth', {
          params: logData, // Передача параметров в запросе
        });

        const responseData = response.data;
        console.log(responseData)
        if (responseData && responseData.length > 0 && responseData[0].customer_id !== undefined) {
          const userId = responseData[0].customer_id;
          const userStatus = responseData[0].role;
          this.$store.commit('setUserId', userId);
          this.$store.commit('setUserStatus', userStatus);
          console.log(userStatus)
          if (userStatus == '0') {
            this.$router.push({ name: 'Profile' });
          } else if (userStatus == '1') {
            this.$router.push({ name: 'Students' });
          } else if (userStatus == '2'){
            this.$router.push({ name: 'Admin' });
          }
        } else {
          this.errorMessage = "Неверные учетные данные"; // Установка сообщения об ошибке
          console.error("Ошибка при получении данных: Неверные учетные данные");
        }
      } catch (error) {
        this.errorMessage = "Ошибка при выполнении запроса"; // Установка сообщения об ошибке
        console.error("Ошибка при выполнении запроса:", error);

        // Добавьте вывод текста ошибки для более подробной информации
        if (error.response) {
          console.error("Ответ сервера:", error.response.data);
        }
      }
    },
  },
};
</script>

<style>
.auth {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100vh;
  background: linear-gradient(135deg, #6e8efb, #a777e3);
}

.auth_form {
  max-width: 400px;
  padding: 40px;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  background-color: white;
}

.auth_text {
  margin-bottom: 30px;
  text-align: center;
  font-size: 24px;
  color: #333;
}

.form-label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
  color: #666;
}

.form-control {
  display: block;
  width: 100%;
  padding: 10px;
  margin-bottom: 20px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
}

.sbut {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 4px;
  background-color: #5a67d8;
  color: white;
  cursor: pointer;
  font-size: 16px;
}

.sbut:hover {
  background-color: #4c51bf;
}

.error-message {
  color: #e53e3e;
  text-align: center;
  margin-top: 10px;
}

.auth-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

</style>
