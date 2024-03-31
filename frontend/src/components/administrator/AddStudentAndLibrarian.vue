<template>
  <div class="admin-panel">
    <h1>Админка</h1>
    <div class="toggle-buttons">
      <button @click="currentView = 'librarian'">Добавить библиотекаря</button>
      <button @click="currentView = 'student'">Добавить студента</button>
    </div>

    <component :is="currentViewComponent"></component>
  </div>
</template>

    
<script>
import LibrarianForm from './LibrarianForm.vue'; // Путь к вашему компоненту формы добавления библиотекаря
import StudentForm from './StudentForm.vue'; // Путь к вашему компоненту формы добавления студента

export default {
  data() {
    return {
      currentView: 'librarian', // Переключатель между 'librarian' и 'student'
    };
  },
  components: {
    LibrarianForm,
    StudentForm,
  },
  computed: {
    currentViewComponent() {
      return this.currentView === 'librarian' ? LibrarianForm : StudentForm;
    },
  },
};
</script>

    
<style>
.admin-panel {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.toggle-buttons {
  margin-bottom: 20px;
}

.toggle-buttons button {
  margin: 0 10px;
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.toggle-buttons button:hover {
  background-color: #45a049;
}

.form-librarian, .form-student {
  padding: 20px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  width: 90%;
  max-width: 500px;
}

.form-librarian h2, .form-student h2 {
  color: #333;
}

.form-librarian input, .form-student input,
.form-librarian button, .form-student button {
  display: block;
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.form-librarian button, .form-student button {
  background-color: #4CAF50;
  color: white;
  border: none;
  cursor: pointer;
}

.form-librarian button:hover, .form-student button:hover {
  background-color: #45a049;
}

input[type="file"] {
  border: none;
}
</style>
