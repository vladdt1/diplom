<template>
    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="form-librarian">
      <h2>Добавление библиотекаря</h2>
      <input type="email" v-model="email" placeholder="Email" />
      <input type="text" v-model="login" placeholder="Login" />
      <input type="text" v-model="password" placeholder="Password" />
      
      <button type="submit">Добавить</button>
      <div v-if="showModal" class="modal">
        <div class="modal-content">
          <p class="modal-content-text">Библиотекарь успешно добавлен</p>
          <button @click="closeModal">OK</button>
        </div>
      </div>
    </form>
</template>
  
<script>
import axios from 'axios';
export default {
    data(){
        return{
            email: '',
            login: '',
            password: '',
            showModal: false
        };
    },
    methods: {
        closeModal(){
            this.showModal = false;
        },
        async submitForm() {
            try {
                const response = await axios.post('http://localhost:8000/cv/addlibrarian', {
                    email: this.email,
                    login: this.login,
                    password: this.password
                });

                if(response.data.success) {
                    this.showModal = true;
                    this.email = '';
                    this.login = '';
                    this.password = '';
                } else {
                    console.error('Ошибка при добавлении библиотекаря');
                }
            } catch (error) {
                console.error('Ошибка при добавлении библиотекаря:');
            }
        }
    }
}
</script>
  