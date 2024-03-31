<template>
    <div class="header">
        <div class="head-text">
            <p class="header-text">Цифровой читательский <br>билет</p>
        </div>
        <div class="head-exit">
            <div @click="exit" class="head-exit-flex">
                <p class="header-exit">Выход</p>
                <img class="head-exit-flex-img" src="../../assets/exit.svg" alt="">
            </div>
        </div>
    </div>
</template>
  
  <script>
  export default {
    methods: {
        async exit(){
            this.$router.push('/')
        }
    },
  };
  </script>
  
  <style>
  .header{
    display: flex;
    align-items: center; /* Центрируем по вертикали */
    justify-content: center; /* Центрируем по горизонтали */
    width: 100%;
    height: 90px;
    top: 0;
    z-index: 1000;
    position: sticky;
    background-color: #2C3E50;
    justify-content: space-between;
  }
  .head-text{
    width: 20%;
  }
  .head-exit-flex{
    display: flex;
    align-items: center;
    cursor: pointer;
  }
  .header-text{
    color: #FFF;
    text-align: center;
    font-family: Inter;
    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }
  .head-exit{
    margin-right: 60px;
  }
  .header-exit{
    margin-top: 10px;
    text-decoration: none;
    color: #FFF;
    font-family: Inter;
    font-size: 20px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
  }
  .head-exit-flex-img{
    margin-left: 15px;
  }
  </style>