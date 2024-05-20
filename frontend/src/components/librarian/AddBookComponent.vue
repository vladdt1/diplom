<template>
  <div class="addbook">
    <p class="addbook-card">Добавление новой книги</p>
    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="addbook-card-form">
      <div>
        <label for="title" class="addbook-card-text">Название книги</label>
        <br>
        <input type="text" id="title" v-model="bookData.title" required class="addbook-card-input addbook-card-title">
        <br>
        <label for="author" class="addbook-card-text">Автор</label>
        <br>
        <input type="text" id="author" v-model="bookData.author" required class="addbook-card-input addbook-card-len">
        <br>
        <label for="article" class="addbook-card-text">Артикул</label>
        <br>
        <input type="text" id="article" v-model="bookData.article" required class="addbook-card-input">
        <br>
        <label for="ydk" class="addbook-card-text">УДК</label>
        <br>
        <input type="text" id="ydk" v-model="bookData.ydk" required class="addbook-card-input">
        <br>
        <label for="bbk" class="addbook-card-text">ББК</label>
        <br>
        <input type="text" id="bbk" v-model="bookData.bbk" required class="addbook-card-input">
        <br>
        <label for="description" class="addbook-card-text">Описание</label>
        <br>
        <textarea id="description" v-model="bookData.description" required class="addbook-card-input addbook-card-textarea"></textarea>
      </div>
      <div>
        <label for="publisher" class="addbook-card-text">Издательство</label>
        <br>
        <input type="text" id="publisher" v-model="bookData.publisher" required class="addbook-card-input addbook-card-len">
        <br>
        <label for="recommended" class="addbook-card-text">Кому рекомендовано</label>
        <br>
        <input type="text" id="recommended" v-model="bookData.recommended" required class="addbook-card-input addbook-card-len">
        <br>
        <label for="counter" class="addbook-card-text">Количество книг</label>
        <br>
        <input type="text" id="counter" v-model="bookData.counter" required class="addbook-card-input">
        <br>
        <label for="page" class="addbook-card-text">Количество страниц</label>
        <br>
        <input type="text" id="page" v-model="bookData.page" required class="addbook-card-input">
        <br>
        <label for="image" class="addbook-card-text">Фотография обложки</label>
        <br>
        <label class="input-file">
          <input type="file" name="file" id="image" @change="handleImageChange" accept="image/*">
           <span class="input-file-btn">Выберите файл</span>           
          <span class="input-file-text">Максимум 10мб</span>
        </label>
        <div class="addbook-btn">
          <button class="btn" type="submit">Добавить книгу</button>
        </div>
      </div>
    </form>
    <div v-if="showModal" class="modal">
      <div class="modal-content">
        <p class="modal-content-text">Книга успешно добавлена</p>
        <button @click="closeModal">OK</button>
      </div>
    </div>
  </div>
</template>

    
<script>
import axios from 'axios';
import $ from 'jquery';

export default {
  data() {
    return {
      bookData: {
        title: '',
        author: '',
        publisher: '',
        description: '',
        article: '',
        counter: '',
        ydk: '',
        recommended: '',
        bbk: '',
        page: '',
        image: null,
      },
      showModal: false,
    };
  },
  mounted(){
    $('.input-file input[type=file]').on('change', function(){
      let file = this.files[0];
      $(this).closest('.input-file').find('.input-file-text').html(file.name);
    });
  },
  methods: {
    handleImageChange(event) {
      const selectedFile = event.target.files[0];
      this.bookData.image = selectedFile;
    },

    async submitForm() {
      try {
        const formData = new FormData();
        formData.append('title', this.bookData.title);
        formData.append('author', this.bookData.author);
        formData.append('publisher', this.bookData.publisher);
        formData.append('description', this.bookData.description);
        formData.append('article', this.bookData.article);
        formData.append('counter', this.bookData.counter);
        formData.append('image', this.bookData.image);
        formData.append('page', this.bookData.page);
        formData.append('ydk', this.bookData.ydk);
        formData.append('bbk', this.bookData.bbk);
        formData.append('recommended', this.bookData.recommended);

        await axios.post('http://localhost:8000/cv/addbook', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

        this.showModal = true;

        this.bookData = {
          title: '',
          author: '',
          publisher: '',
          description: '',
          counter: '',
          article: '',
          ydk: '',
          recommended: '',
          bbk: '',
          page: '',
          image: null,
        };

      } catch (error) {
        console.error('Ошибка при добавлении книги:', error);
      }
    },
    closeModal() {
      this.showModal = false;
    },
  },
};
</script>

<style>
.addbook {
  width: 80%;
  max-width: 1000px;
  margin: 20px auto;
  padding: 20px;
  background: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.addbook-card {
  text-align: center;
  color: #333;
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 20px;
}

.addbook-card-form {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}

.addbook-card-text {
  display: block;
  margin-top: 15px;
  color: #666;
  font-size: 18px;
  font-weight: 500;
}

.addbook-card-input,
.addbook-card-textarea {
  width: 400px; /* Поля ввода будут занимать всю доступную ширину */
  padding: 10px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
  background: #fff;
}

.addbook-card-textarea {
  height: 120px;
}

.input-file {
  position: relative;
  display: inline-block;
  width: calc(50% - 20px);
  text-align: left;
}

.input-file-btn,
.btn {
  display: inline-block;
  padding: 10px 20px;
  margin-top: 10px;
  border: none;
  color: #fff;
  background-image: linear-gradient(135deg, #667eea, #764ba2);
  cursor: pointer;
  border-radius: 5px;
  transition: background 0.3s ease;
}

.input-file-btn:hover,
.btn:hover {
  background-image: linear-gradient(135deg, #764ba2, #667eea);
}

.input-file input[type="file"] {
  opacity: 0;
  width: 0;
  height: 0;
}

.input-file-text {
  margin-left: 10px;
  font-size: 14px;
  vertical-align: middle;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
}

.modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.modal-content-text {
  margin-bottom: 20px;
}

@media (max-width: 768px) {
  .addbook-card-input,
  .addbook-card-textarea,
  .input-file {
    width: 100%; /* Полная ширина на мобильных устройствах */
  }
}

</style>