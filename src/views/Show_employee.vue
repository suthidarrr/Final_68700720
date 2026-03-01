<template>
  <div class="container my-5">

    <!-- ✅ Loading -->
    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary"></div>
      <p class="mt-3">กำลังโหลดข้อมูล...</p>
    </div>

    <!-- ✅ Error -->
    <div v-else-if="error" class="alert alert-danger text-center">
      {{ error }}
    </div>

    <!-- ✅ Detail -->
    <div v-else-if="employee" class="row">

      <!-- ✅ รูปสินค้า -->
      <div class="col-md-5 text-center">
        <img
           :src="'http://localhost/final-68700720/php_api/uploads/' + employee.image"
          class="img-fluid rounded shadow-sm"
        />
      </div>

      <!-- ✅ รายละเอียด -->
      <div class="col-md-7">
        <h2 class="fw-bold">{{ employee.first_name }}</h2>

        <h4 class="text-primary my-3">
          Phone : {{ employee.phone }}
        </h4>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useRoute } from "vue-router"

const route = useRoute()
const employee = ref(null)

const fetchEmployee = async () => {
  try {

    const id = route.params.id || route.query.id

    const response = await fetch(
      `http://localhost/final-68700720/php_api/api_employee.php?id=${id}`
    )

    const result = await response.json()

    console.log("API RESULT:", result)   // 🔥 Debug สำคัญมาก

    if (result.success) {
      employee.value = result.data
    } else {
      console.error(result.message)
    }

  } catch (error) {
    console.error("ERROR:", error)
  }
}

onMounted(() => {
  fetchEmployee()
})
</script>