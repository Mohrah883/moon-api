<template>
  <div class="add-project-container">
    <div class="add-project-card">
      <h2>Submit New Project</h2>
      
      <!-- Success Message -->
      <div v-if="isSubmitted" class="success-message">
        <div class="success-icon">✓</div>
        <h3>Project Submitted Successfully!</h3>
        <p>Your project has been added to the dashboard.</p>
        <button @click="resetForm" class="btn-secondary">Add Another Project</button>
        <router-link to="/" class="btn-secondary">View All Projects</router-link>
      </div>

      <!-- Project Submission Form -->
      <form v-else @submit.prevent="submitProject" class="project-form">
        <div class="form-group">
          <label for="projectName">Project Name *</label>
          <input 
            id="projectName"
            v-model="formData.name" 
            type="text" 
            placeholder="Enter project name"
            :class="{ 'error': errors.name }"
            @blur="validateField('name')"
            required 
          />
          <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
        </div>

        <div class="form-group">
          <label for="projectDescription">Description *</label>
          <textarea 
            id="projectDescription"
            v-model="formData.description" 
            placeholder="Describe your project"
            :class="{ 'error': errors.description }"
            @blur="validateField('description')"
            rows="4"
            required
          ></textarea>
          <span v-if="errors.description" class="error-message">{{ errors.description }}</span>
        </div>

        <div class="form-group">
          <label for="projectStatus">Status</label>
          <select 
            id="projectStatus"
            v-model="formData.status"
            class="form-select"
          >
            <option value="planning">Planning</option>
            <option value="in-progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="on-hold">On Hold</option>
          </select>
        </div>

        <div class="form-group">
          <label for="projectPriority">Priority</label>
          <select 
            id="projectPriority"
            v-model="formData.priority"
            class="form-select"
          >
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>

        <div class="form-group">
          <label for="projectDeadline">Deadline</label>
          <input 
            id="projectDeadline"
            v-model="formData.deadline" 
            type="date"
            class="form-input"
          />
        </div>

        <div class="form-actions">
          <button 
            type="button" 
            @click="resetForm" 
            class="btn-secondary"
            :disabled="isSubmitting"
          >
            Reset
          </button>
          <button 
            type="submit" 
            class="btn-primary"
            :disabled="isSubmitting || !isFormValid"
          >
            <span v-if="isSubmitting" class="loading-spinner"></span>
            {{ isSubmitting ? 'Submitting...' : 'Submit Project' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

// Form state
const formData = reactive({
  name: '',
  description: '',
  status: 'planning',
  priority: 'medium',
  deadline: ''
})

const errors = reactive({
  name: '',
  description: ''
})

const isSubmitting = ref(false)
const isSubmitted = ref(false)

// Computed properties
const isFormValid = computed(() => {
  return formData.name.trim() && 
         formData.description.trim() && 
         !errors.name && 
         !errors.description
})

// Validation functions
const validateField = (field) => {
  errors[field] = ''
  
  if (field === 'name') {
    if (!formData.name.trim()) {
      errors.name = 'Project name is required'
    } else if (formData.name.trim().length < 3) {
      errors.name = 'Project name must be at least 3 characters'
    } else if (formData.name.trim().length > 100) {
      errors.name = 'Project name must be less than 100 characters'
    }
  }
  
  if (field === 'description') {
    if (!formData.description.trim()) {
      errors.description = 'Description is required'
    } else if (formData.description.trim().length < 10) {
      errors.description = 'Description must be at least 10 characters'
    } else if (formData.description.trim().length > 500) {
      errors.description = 'Description must be less than 500 characters'
    }
  }
}

const validateForm = () => {
  validateField('name')
  validateField('description')
  return isFormValid.value
}

// Form submission
const submitProject = async () => {
  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    const projectData = {
      name: formData.name.trim(),
      description: formData.description.trim(),
      status: formData.status,
      priority: formData.priority,
      deadline: formData.deadline || null,
      createdAt: new Date().toISOString()
    }

    const response = await axios.post('http://localhost:3001/projects', projectData)
    
    if (response.status === 201) {
      isSubmitted.value = true
    }
  } catch (error) {
    console.error('Error submitting project:', error)
    alert('Failed to submit project. Please try again.')
  } finally {
    isSubmitting.value = false
  }
}

// Reset form
const resetForm = () => {
  Object.assign(formData, {
    name: '',
    description: '',
    status: 'planning',
    priority: 'medium',
    deadline: ''
  })
  
  Object.assign(errors, {
    name: '',
    description: ''
  })
  
  isSubmitted.value = false
}
</script>

<style scoped>
.add-project-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.add-project-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  padding: 2.5rem;
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
}

.add-project-card h2 {
  color: #2d3748;
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 2rem;
  text-align: center;
}

.project-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: #4a5568;
  font-size: 0.9rem;
}

.form-group input,
.form-group textarea,
.form-group select {
  padding: 0.75rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group input.error,
.form-group textarea.error {
  border-color: #e53e3e;
}

.error-message {
  color: #e53e3e;
  font-size: 0.8rem;
  margin-top: 0.25rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.btn-primary,
.btn-secondary {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 120px;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: #f7fafc;
  color: #4a5568;
  border: 2px solid #e2e8f0;
}

.btn-secondary:hover:not(:disabled) {
  background: #edf2f7;
  border-color: #cbd5e0;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 0.5rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.success-message {
  text-align: center;
  padding: 2rem;
}

.success-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  margin: 0 auto 1rem;
}

.success-message h3 {
  color: #2d3748;
  margin-bottom: 0.5rem;
}

.success-message p {
  color: #718096;
  margin-bottom: 2rem;
}

.success-message .btn-secondary {
  margin: 0 0.5rem;
}

@media (max-width: 768px) {
  .add-project-container {
    padding: 1rem;
  }
  
  .add-project-card {
    padding: 1.5rem;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
  }
}
</style>