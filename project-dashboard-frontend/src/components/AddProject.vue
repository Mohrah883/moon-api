<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const name = ref('')
const description = ref('')
const router = useRouter()

const addProject = async () => {
  if (!name.value || !description.value) return
  await axios.post('http://localhost:3001/projects', {
    name: name.value,
    description: description.value
  })
  name.value = ''
  description.value = ''
  router.push('/')
}
</script>

<template>
  <div class="add-project">
    <h2>Add New Project</h2>
    <input v-model="name" placeholder="Project Name" />
    <textarea v-model="description" placeholder="Project Description" />
    <button @click="addProject">Add Project</button>
  </div>
</template>

<style scoped>
.add-project {
  max-width: 500px;
  margin: 3rem auto;
  padding: 2rem;
  background-color: #fff0f5;
  border-radius: 12px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.add-project h2 {
  color: #d63384;
  margin-bottom: 1rem;
}
input, textarea {
  display: block;
  width: 100%;
  margin-bottom: 1rem;
  padding: 0.75rem;
  border: 1px solid #f5c2da;
  border-radius: 6px;
  font-size: 1rem;
  color: #c2185b;
}
button {
  background-color: #d63384;
  color: white;
  border: none;
  padding: 0.7rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
}
button:hover {
  background-color: #c2185b;
}
</style>
