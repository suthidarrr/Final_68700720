<template>
  <div class="container mt-4">
    <h2 class="mb-3">Employee List</h2>

    <div class="text-start mb-3">
      <button class="btn btn-primary"  @click="openAddModal">Add+</button>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>รูปภาพ</th>
          <th>รหัส</th>
          <th>ชื่อ</th>
          <th>นามสกุล</th>
          <th>ที่อยู่</th>
          <th>เบอร์โทรศัพท์</th>
          <th>แก้ไข/ลบ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="employee in employees" :key="employee.emp_id">
            <td>
            <img
              v-if="employee.image"
              :src="'http://localhost/final-68700720/php_api/uploads/' + employee.image"
              width="100"
            />
          </td>
          <td>{{ employee.emp_id }}</td>
          <td>{{ employee.first_name }}</td>
          <td>{{ employee.last_name }}</td>
          <td>{{ employee.address }}</td>
          <td>{{ employee.phone }}</td>
          
          <td>
            <button class="btn btn-warning btn-sm me-2" @click="openEditModal(employee)">
              แก้ไข
            </button>
            <button class="btn btn-danger btn-sm" @click="deleteEmployee(employee.emp_id)">
              ลบ
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="loading" class="text-center"><p>กำลังโหลดข้อมูล...</p></div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Modal ใช้ทั้งเพิ่ม / แก้ไข -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditMode ? "แก้ไขสินค้า" : "เพิ่มสินค้าใหม่" }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveEmployee">
              <div class="mb-3">
                <label class="form-label">ชื่อ</label>
                <input v-model="editForm.first_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">นามสกุล</label>
                <textarea v-model="editForm.last_name" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">ที่อยู่</label>
                <input v-model="editForm.address" type="text" step="0.01" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input v-model="editForm.phone" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
  <label class="form-label">รูปภาพ</label>
  <!-- ✅ required เฉพาะตอนเพิ่มสินค้า -->
  <input
    type="file"
    @change="handleFileUpload"
    class="form-control"
    :required="!isEditMode"
  />

  <!-- แสดงรูปเดิมเฉพาะตอนแก้ไข -->
  <div v-if="isEditMode && editForm.image">
    <p class="mt-2">รูปเดิม:</p>
    <img
      :src="'http://localhost/final-68700720/php_api/uploads/' + editForm.image"
      width="100"
    />
  </div>
</div>




              <button type="submit" class="btn btn-success">
                {{ isEditMode ? "บันทึกการแก้ไข" : "บันทึกพนักงานใหม่" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  name: "EmployeeList",
  setup() {
    const employees = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const isEditMode = ref(false); // ✅ เช็คโหมด
    const editForm = ref({
      emp_id: null,
      first_name: "",
      last_name: "",
      address: "",
      phone: "",
      image: ""
    });
    const newImageFile = ref(null);
    let modalInstance = null;

    // โหลดข้อมูลสินค้า
    const fetchEmployees = async () => {
      try {
        const res = await fetch("http://localhost/final-68700720/php_api/api_employee.php");
        const data = await res.json();
        employees.value = data.success ? data.data : [];
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

// เปิด Modal สำหรับเพิ่มสินค้า
const openAddModal = () => {
  isEditMode.value = false;
  editForm.value = {
    emp_id: null,
    first_name: "",
    last_name: "",
    address: "",
    phone: "",
    image: ""
  };
  newImageFile.value = null;
      
  const modalEl = document.getElementById("editModal");
  modalInstance = new window.bootstrap.Modal(modalEl);
  modalInstance.show();

  // ✅ รีเซ็ตค่า input file ให้ไม่แสดงชื่อไฟล์ค้าง
  const fileInput = modalEl.querySelector('input[type="file"]');
  if (fileInput) fileInput.value = "";
 };

// เปิด Modal สำหรับแก้ไขสินค้า
    const openEditModal = (employee) => {
      isEditMode.value = true;
      editForm.value = { ...employee };
      newImageFile.value = null;
      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();
    };

    const handleFileUpload = (event) => {
      newImageFile.value = event.target.files[0];
    };

// ✅ ใช้ฟังก์ชันเดียวในการเพิ่ม / แก้ไข
    const saveEmployee = async () => {
      const formData = new FormData();
      formData.append("action", isEditMode.value ? "update" : "add");
      if (isEditMode.value) formData.append("emp_id", editForm.value.emp_id);
      formData.append("first_name", editForm.value.first_name);
      formData.append("last_name", editForm.value.last_name);
      formData.append("address", editForm.value.address);
      formData.append("phone", editForm.value.phone);
      if (newImageFile.value) formData.append("image", newImageFile.value);

      try {
        const res = await fetch("http://localhost/final-68700720/php_api/api_employee.php", {
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);
          fetchEmployees();
          modalInstance.hide();
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };

    // ลบสินค้า
    const deleteEmployee = async (id) => {
      if (!confirm("คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?")) return;

      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("emp_id", id);

      try {
        const res = await fetch("http://localhost/final-68700720/php_api/api_employee.php", {
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);
          employees.value = employees.value.filter((p) => p.emp_id !== id);
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };

    onMounted(fetchEmployees);

    return {
      employees,
      loading,
      error,
      editForm,
      isEditMode,
      openAddModal,
      openEditModal,
      handleFileUpload,
      saveEmployee,
      deleteEmployee
    };
  }
};
</script>