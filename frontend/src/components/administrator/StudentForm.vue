<template>
    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="form-librarian">
      <h2>Добавление студента</h2>
      <input type="text" v-model="fio" placeholder="ФИО" />
      <input type="text" v-model="email" placeholder="Email" />
      <div class="gender-checkboxes">
        <label for="male">Мужской:</label>
        <input type="checkbox" id="male" v-model="maleChecked" @change="handleCheckboxChange('male')" :disabled="femaleChecked">
        <label for="female">Женский:</label>
        <input type="checkbox" id="female" v-model="femaleChecked" @change="handleCheckboxChange('female')" :disabled="maleChecked">
      </div>
      <input type="tel" v-model="phone" placeholder="Номер телефона" />
      <p v-if="!isValidPhoneNumber">Пожалуйста, введите корректный номер телефона</p>
      <input type="text" v-model="sitizenship" placeholder="Гражданство" />
      <input type="date" v-model="birthdate">
      <input type="text" v-model="password" placeholder="Пароль" />
      <input type="text" v-model="login" placeholder="Логин" />
      <input type="text" v-model="customer_group" placeholder="Группа" />
      <label class="input-file">
        <input type="file" name="file" id="image" @change="handleImageChange" accept="image/*">
         <span class="input-file-btn">Выберите файл</span>           
        <span class="input-file-text">Максимум 10мб</span>
      </label>
      <button type="submit">Добавить</button>
      <div v-if="showModal" class="modal">
        <div class="modal-content">
          <p class="modal-content-text">Студент успешно добавлен</p>
          <button @click="closeModal">OK</button>
        </div>
      </div>
    </form>
</template>
  
<script>
  import axios from 'axios';
  import $ from 'jquery';
  export default {
      data(){
          return{
              fio: '',
              email: '',
              maleChecked: false,
              femaleChecked: false,
              phone: '',
              isValidPhoneNumber: true,
              sitizenship: '',
              birthdate: '',
              password: '',
              login: '',
              customer_group: '',
              image: null,
              showModal: false
          };
        },
        mounted(){
            $('.input-file input[type=file]').on('change', function(){
                let file = this.files[0];
                $(this).closest('.input-file').find('.input-file-text').html(file.name);
            });
        },
        methods: {
            closeModal(){
                this.showModal = false;
            },
            handleImageChange(event) {
                const selectedFile = event.target.files[0];
                this.image = selectedFile;
            },
             async submitForm() {
                if (/^\d{9,11}$/.test(this.phone)) {
                    this.isValidPhoneNumber = true;
                    try {
                        let formData = new FormData();
                        formData.append('email', this.email);
                        formData.append('fio', this.fio);
                        formData.append('phone', this.phone);
                        formData.append('sitizenship', this.sitizenship);
                        formData.append('birthdate', this.birthdate);
                        formData.append('login', this.login);
                        formData.append('gender', this.maleChecked ? '1' : '0');
                        formData.append('password', this.password);
                        formData.append('customer_group', this.customer_group);
                        // Добавляем файл изображения, если он выбран
                        if (this.image !== null) {
                            formData.append('image', this.image);
                        }

                        const response = await axios.post('http://localhost:8000/cv/addstudent', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        if(response.data.success) {
                            this.showModal = true;
                            this.fio = '';
                            this.email = '';
                            this.maleChecked = false;
                            this.femaleChecked = false;
                            this.phone = '';
                            this.isValidPhoneNumber = true;
                            this.sitizenship = '';
                            this.birthdate = '';
                            this.password = '';
                            this.login = '';
                            this.customer_group = '';
                            this.image = null;
                        } else {
                            console.error('Ошибка при добавлении студента');
                        }
    
                    } catch (error) {
                        console.error('Ошибка при добавлении библиотекаря:');
                    }
                } else {
                    this.isValidPhoneNumber = false;
                }
            },
            handleCheckboxChange(changedCheckbox) {
                if (changedCheckbox === 'male' && this.maleChecked) {
                    this.femaleChecked = false; // Сбросить выбор женского пола, если выбран мужской
                } else if (changedCheckbox === 'female' && this.femaleChecked) {
                    this.maleChecked = false; // Сбросить выбор мужского пола, если выбран женский
                }
            }
        }
    }
</script>
    