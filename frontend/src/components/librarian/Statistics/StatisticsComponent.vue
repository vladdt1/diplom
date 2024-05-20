<template>
    <div>
      <div class="myChart-container">
        <h2 class="myChart-container-title">{{ chartTitle }}</h2>
        <div class="centered-content">
            <p>Вы можете поменять период: </p>
            <select v-model="selectedPeriod" @change="fetchData">
              <option value="1">1 месяц</option>
              <option value="3">3 месяца</option>
              <option value="6">Полгода</option>
              <option value="12">Год</option>
            </select>
          </div>
        <div>
          <p>Общее количество возвратов: {{ totalReturned }}</p>
          <p>Общее количество выдач: {{ totalBooked }}</p>
        </div>
        <canvas :key="canvasKey" ref="myChart" width="1200" height="450"></canvas>
      </div>
    </div>
</template>
  
<script>
import { Chart, registerables } from 'chart.js';
import axios from 'axios';

Chart.register(...registerables);

export default {
  data() {
    return {
      selectedPeriod: '1', // по умолчанию 1 месяц
      chartTitle: 'Возврат и выдачи за 1 месяц',
      myChart: null,
      canvasKey: 0, // Уникальный ключ для перерисовки элемента
      totalReturned: 0,
      totalBooked: 0
    };
  },
  mounted() {
    this.fetchData();
  },
  watch: {
    selectedPeriod() {
      this.updateChartTitle();
      this.fetchData();
    }
  },
  methods: {
    updateChartTitle() {
      const periodMapping = {
        '1': '1 месяц',
        '3': '3 месяца',
        '6': 'Полгода',
        '12': 'Год'
      };
      this.chartTitle = `Возврат и выдачи за ${periodMapping[this.selectedPeriod]}`;
    },
    async fetchData() {
      try {
        const response = await axios.get(`http://localhost:8000/cv/statistics?period=${this.selectedPeriod}`);
        this.totalReturned = response.data.totalReturned;
        this.totalBooked = response.data.totalBooked;
        this.$nextTick(() => {
          this.canvasKey++; // Обновляем ключ для перерисовки элемента
          this.$nextTick(() => {
            this.drawChart(response.data);
          });
        });
      } catch (error) {
        console.error("Ошибка при запросе к API:", error);
      }
    },
    drawChart(data) {
      const canvas = this.$refs.myChart;
      if (canvas) {
        const ctx = canvas.getContext('2d');
        if (this.myChart) {
          this.myChart.destroy(); // Уничтожаем старый график перед созданием нового
        }
        this.myChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: data.dates, // Массив дат
            datasets: [{
              label: 'Возвращено',
              data: data.returned, // Массив значений возвращенных книг
              borderColor: 'rgb(75, 192, 192)',
              tension: 0.1
            }, {
              label: 'Выдано',
              data: data.booked, // Массив значений забронированных книг
              borderColor: 'rgb(255, 99, 132)',
              tension: 0.1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  // Устанавливаем шаг оси Y равным 1, чтобы отображать только целые числа
                  stepSize: 1,
                  // Функция для форматирования меток (убираем дробную часть)
                  callback: function (value) {
                    if (value % 1 === 0) {
                      return value;
                    }
                  }
                }
              }
            }
          }
        });
      } else {
        console.error('Canvas element not found');
      }
    }
  }
};
</script>
  
  <style>
  .container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Высота контейнера - 100% от высоты экрана */
  }
  
  .myChart-container {
    text-align: center;
  }
  
  .myChart-container-title {
    width: 800px;
    margin: 0 auto;
  }
  
  .centered-content {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px; /* Расстояние между элементами */
  }
  
  select {
    display: block;
    padding: 5px;
    font-size: 16px;
    margin-left: 10px; /* Добавлено для дополнительного отступа */
  }
  
  </style>
  