<template>
  <div class="ser">
    <div class="search-container">
      <input type="text" id="searchInput" class="search-container-input" v-model="searchTerm" />
      <button @click="searchStudent" class="search-container-button">Поиск студентов</button>
    </div>

    <div v-if="students.length > 0" class="student-results-container">
      <router-link
        v-for="student in students"
        :key="student.customer_id"
        :to="{ name: 'studentSearch', params: { id: student.customer_id }}"
        class="student-results-container-router"
      >
        <div class="results-container-student-card">
          <div>
            <img class="results-container-student-card-img" v-if="student.path" :src="require(`../../../assets/avatar/${student.path}`)" alt="avatar" loading="lazy"/>
          </div>
          <div>
            <p class="results-container-student-card-name">{{ student.customer_fio }}</p>
            <p class="results-container-student-card-login">Логин: {{ student.login }}</p>
          </div>
          <div>
            <p class="results-container-student-card-name">{{ student.customer_group }}</p>
          </div>
        </div>
      </router-link>
    </div>
    <div v-else class="student-no-results">
      Данный студент отсутствует
    </div>
    <div class="student-search-container-else" v-if="searchTerm == '' && students.length % 4 !== 0">
      <button @click="loadMoreStudent" class="student-search-container-button">Показать ещё</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      searchTerm: '',
      students: [],
      StudentCount: 4,
    };
  },
  async created() {
    const searchTermFromUrl = this.$route.query.q;
    if (searchTermFromUrl) {
      this.searchTerm = searchTermFromUrl;
      await this.searchStudent();
    } else {
      await this.loadInitialStudent();
    }
  },
  methods: {
    async loadInitialStudent() {
      try {
        const response = await axios.post('http://localhost:8000/cv/searchStudents/all', {
          StudentCount: this.StudentCount
        });

        this.students = response.data;
      } catch (error) {
        console.error("Ошибка получения списка всех студентов:", error);
      }
    },
    async searchStudent() {
      try {
        const trimmedSearchTerm = this.searchTerm.trim();

        const lowerCaseSearchTerm = trimmedSearchTerm.toLowerCase();
        const response = await axios.post('http://localhost:8000/cv/searchStudents/search', {
          searchTerm: lowerCaseSearchTerm
        });

        this.students = response.data;
      } catch (error) {
        console.error("Ошибка поиска студента:", error);
      }
    },
    async loadMoreStudent() {
      this.StudentCount += 4;
      await this.loadInitialStudent();
    },
  },
};
</script>

<style>
.student-results-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 20px;
  padding: 20px;
}

.results-container-student-card {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 20px;
  background: linear-gradient(145deg, #ffffff, #f0f0f0);
  border-radius: 15px;
  box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
}

.results-container-student-card:hover {
  transform: translateY(-5px);
  box-shadow: 5px 5px 20px rgba(0, 0, 0, 0.15);
}

.results-container-student-card-img {
  border-radius: 50%;
  width: 100px;
  height: 100px;
  object-fit: cover;
}

.results-container-student-card-name,
.results-container-student-card-login {
  margin: 0;
}

.results-container-student-card-name {
  font-size: 20px;
  font-weight: bold;
}

.results-container-student-card-login {
  font-size: 16px;
}

.student-no-results, .student-search-container-else {
  text-align: center;
  margin-top: 20px;
}

.student-results-container-router{
  text-decoration: none;
  color: inherit;
}

.student-search-container-button {
  padding: 10px 20px;
  background-color: #007bff;
  color: #ffffff;
  border: none;
  border-radius: 20px;
  cursor: pointer;
}
</style>
