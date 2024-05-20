<template>
    <div class="book-search">
        <div class="book-search-button">
            <div class="book-search-button-exit">
                <button @click="goBack" class="search-container-button">Вернуться</button>
            </div>
        </div>
        <div class="book-search-text">
            <div class="book-search-card">
                <h2 class="book-search-card-name">{{ book.title }}</h2>
                <div class="book-search-card-flex">
                    <p class="book-search-card-text"><b>Автор:</b> {{ book.autor }}</p>
                    <p class="book-search-card-text underlined-text">
                        <b>Статус:</b> 
                        {{ getStatusString(this.$route.query.status) }}
                    </p>
                </div>
                <div class="book-search-card-flex">
                    <p class="book-search-card-text">
                        <b>Артикул:</b> 
                        {{ this.$route.params.id }}
                    </p>
                    <p class="book-search-card-text underlined-text">
                        <b>
                            {{ getStatusStringDate(this.$route.query.status) }}:
                        </b> 
                        {{ this.$route.query.bookData }}
                    </p>
                    <p class="book-search-card-text underlined-text">
                        <b>
                            {{ getStatusStringDateOn(this.$route.query.status) }}
                        </b>
                    </p>
                </div>
                <p class="book-search-card-text"><b>Издательство:</b> {{ book.publishing }}</p>
                <p class="book-search-card-text"><b>Страниц:</b> {{ book.page }}</p>
                <p class="book-search-card-text"><b>Аннотация:</b> {{ book.annotation }}</p>
                <p class="book-search-card-text"><b>Кому рекомендовано:</b> {{ book.recommended }}</p>
                <p class="book-search-card-text"><b>УДК:</b> {{ book.ydk }}</p>
                <p class="book-search-card-text"><b>ББК:</b> {{ book.bbk }}</p>
            </div>
            <div>
                <img class="book-search-card-img" v-if="book.path" :src="require(`../../../assets/book/${book.path}`)" alt="book" loading="lazy"/>
            </div>
        </div>
    </div>
  </template>
  
<script>
  import axios from 'axios';

  export default {
    data() {
      return {
        book: [],
      };
    },
    async created() {
        try {
            //this.$route.query.bookData
            const idFromRoute = this.$route.params.id;

            const response = await axios.post('http://localhost:8000/cv/searchBooks/bookDetals', {
                idBook: idFromRoute
            });

            this.book = response.data;
        } catch (error) {
            console.error("Error while searching book:", error);
        }
    },
    methods: {
        getStatusString(status) {
            switch (status) {
                case '11':
                    return 'Ожидает свободную книгу';
                case '21':
                    return 'Ожидает посещение';
                case '3':
                    return 'На руках';
                case "00":
                    return 'Отмена бронирования (Вы не забрали книгу в установленный день)';
                case "10":
                    return 'Отмена бронирования (Вы отменили бронирование)';
                case "32":
                    return 'Вернули книгу в срок';
                case "42":
                    return 'Вернули книгу не в срок';
                default:
                    return 'Неизвестный статус';
            }
        },
        getStatusStringDate(status) {
            switch (status) {
                case "11":
                    return 'Дата заявки';
                case "21":
                    return 'Забраниравано на';
                case "3":
                    return 'На руках';
                case "00":
                    return 'Дата отмены';
                case "10":
                    return 'Дата отмены';
                case "32":
                    return 'Дата возврата';
                case "42":
                    return 'Дата возврата';
                default:
                    return 'Неизвестный статус';
            }
        },
        getStatusStringDateOn(status){
            switch (status) {
                case '3': {
                    return 'Вернуть до: ' + this.$route.query.return;
                }
                case '1': {
                    const dateString = this.$route.query.bookData;

                    const parts = dateString.split('.');

                    const dateObject = new Date(parts[2], parts[1] - 1, parts[0]);

                    dateObject.setDate(dateObject.getDate() + 5);

                    const newDateString = `${dateObject.getDate()}.${dateObject.getMonth() + 1}.${dateObject.getFullYear()}`;

                    return 'Забрать можно до: ' + newDateString;
                }
                default:
                    return '';
            }
        },
        goBack() {
            this.$router.go(-1); // Перейти на предыдущую страницу в истории
        },
        async addbooking(){
            try {
                if(this.book.count > 0){
                    const response = await axios.post('http://localhost:8000/cv/addbooking', {
                        UserId: this.$store.getters.getUserId,
                        idBook: this.$route.params.id
                    });

                    if (response.data.success) {
                        this.book.count = parseInt(this.book.count, 10) - 1;
                    }
                }
            } catch (error) {
                console.error("Ошибка бронирования:", error);
            }
        }
    },
  };
</script>
  
<style>
.book-search{
    width: 80%;
    padding: 20px;
}

.book-search-button{
  display: flex;
  justify-content: space-between;
}

.book-search-text{
    display: flex;
    justify-content: space-between;

    margin: 50px;
}

.book-search-card-name{
    color: #000;
    font-family: Inter;
    font-size: 34px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
}

.book-search-card-flex{
    display: flex;
    justify-content: space-between;
}

.book-search-card-text{
    margin-top: 32px;

    max-width: 600px;
    color: #000;
    font-family: Inter;
    font-size: 20px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
}

.book-search-card-img{
    width: 400px;
    border: 2px solid #000;
    border-radius: 5px;
}
.underlined-text{
    text-decoration: underline;
}
</style>
  