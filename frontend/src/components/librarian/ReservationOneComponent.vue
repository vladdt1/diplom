<template>
  <table class="reservation-table">
    <tr>
      <th>Название</th>
      <th>Артикул</th>
      <th>Автор</th>
      <th>Издательство</th>
    </tr>
    <tr>
      <td>{{ data.title }}</td>
      <td>{{ data.title_id }}</td>
      <td>{{ data.autor }}</td>
      <td>{{ data.publishing }}</td>
    </tr>
  </table>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style scoped>
.reservation-table {
  width: 100%;
  border-collapse: collapse;
}

.reservation-table th,
.reservation-table td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
}

.reservation-table th {
  background-color: #f2f2f2;
}
</style>
