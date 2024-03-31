<template>
    <div class="book-search-tex">
      <div class="book-inventory">
        <p class="section-title">Экземпляры</p>
        <ul class="inventory-list">
          <li v-for="one in info.inventoryNumbers" :key="one">
            {{ one }}
            <button class="inventory-button" @click="infoInstance(one)">Информация</button>
        </li>
        </ul>
      </div>
      <div class="book-requests">
        <p class="section-title">Даты заявок</p>
        <ul class="request-dates">
          <li v-for="one in info.requests" :key="one.request_date">{{ one.request_date }}</li>
        </ul>
      </div>
      <div class="modal" v-if="ModalOpen">
        <div class="modal-content">
          <table>
            <thead>
                <tr>
                    <th>Логин студента</th>
                    <th>Дата возврата</th>
                </tr>
            </thead>
            <tbody v-for="one in limitedDetalInfo" :key="one">
                <tr>
                    <td>
                        <router-link :to="'/search-student/' + one.customer_id" class="black-text">
                            {{one.login}}
                        </router-link>
                    </td>
                    <td>{{one.returned_date}}</td>
                </tr>
            </tbody>
            <div v-if="detalInfo.length > itemsToShow" class="modal-content-button">
                <button @click="showMore">Посмотреть ещё</button>
            </div>
          </table>
          <div class="modal-content-button">
            <button @click="closeModal">Отмена</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  export default {
    data() {
      return {
        info: [],
        ModalOpen: false,
        detalInfo: [],
        itemsToShow: 0,
      };
    },
    computed: {
        limitedDetalInfo() {
            return this.detalInfo.slice(0, this.itemsToShow);
        }
    },
    created() {
      this.instance();
    },
    methods: {
        showMore() {
            this.itemsToShow += 5;
        },
      async instance() {
        try {
          const response = await axios.post('http://localhost:8000/cv/searchBooks/instanceBook', {
            titleId: this.$route.params.id,
          });
          this.info = response.data;
        } catch(error) {
          console.error('Ошибка поиска экземпляров книги:', error);
        }
      },
      closeModal(){
        this.ModalOpen = false;
      },
      async infoInstance(one){
        this.itemsToShow = 4;
        this.ModalOpen = true;
        try {
          const response = await axios.post('http://localhost:8000/cv/searchBooks/searchInstanceOneBook', {
            titleId: one,
          });
          this.detalInfo = response.data;
          console.log(this.detalInfo)
        } catch(error) {
          console.error('Ошибка поиска экземпляров книги:', error);
        }
      }
    },
  };
  </script>
  
  <style>
  .book-search-tex {
    display: flex;
    justify-content: space-between;
  }
  
  .section-title {
    font-size: 1.2rem;
    font-weight: bold;
    text-align: center;
    margin-bottom: 0.5rem;
  }
  
  .inventory-list,
  .request-dates {
    text-align: center;
    list-style-type: none;
    padding: 0;
  }
  
  .inventory-list li,
  .request-dates li {
    margin-bottom: 0.5rem;
  }
  
  .book-inventory,
  .book-requests {
    flex-basis: 45%;
  }
  
  .book-inventory {
    background-color: #f9f9f9;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  }
  
  .book-requests {
    background-color: #fff;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  }
  
  /* Custom styles for table */
  table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    font-size: 0.9rem;
  }
  
  th {
    background-color: #009879;
    color: white;
    text-align: left;
    padding: 8px;
  }
  
  td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
  
  tr:hover {
    background-color: #f2f2f2;
  }
  
  /* Modal and button styling for consistency */
  .modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
  }
  
  .modal-content-button button {
    background-color: #009879;
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
  }
  
  .modal-content-button button:hover {
    background-color: #007961;
  }
  
  /* Responsive table design */
  @media screen and (max-width: 600px) {
    .book-search-tex {
      flex-direction: column;
    }
  
    .book-inventory, .book-requests {
      flex-basis: 100%;
    }
  }
  </style>