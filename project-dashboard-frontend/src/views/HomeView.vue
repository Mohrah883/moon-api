<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { projectService } from '../services/projectService'
import ProjectStats from '../components/ProjectStats.vue'

const router = useRouter()
const projects = ref([])
const loading = ref(true)
const error = ref(null)
const filterStatus = ref('all')
const sortBy = ref('createdAt')
const stats = ref({
  total: 0,
  overdue: 0,
  byStatus: {
    planning: 0,
    'in-progress': 0,
    completed: 0,
    'on-hold': 0
  },
  byPriority: {
    urgent: 0,
    high: 0,
    medium: 0,
    low: 0
  }
})

// Fetch projects from API
const fetchProjects = async () => {
  try {
    loading.value = true
    const [projectsData, statsData] = await Promise.all([
      projectService.getAllProjects(),
      projectService.getProjectStats()
    ])
    projects.value = projectsData
    stats.value = statsData
  } catch (err) {
    console.error('Error fetching projects:', err)
    error.value = err.message || 'Failed to load projects'
  } finally {
    loading.value = false
  }
}

// Filtered and sorted projects
const filteredProjects = computed(() => {
  let filtered = projects.value

  // Filter by status
  if (filterStatus.value !== 'all') {
    filtered = filtered.filter(project => project.status === filterStatus.value)
  }

  // Sort projects
  filtered = [...filtered].sort((a, b) => {
    switch (sortBy.value) {
      case 'name':
        return a.name.localeCompare(b.name)
      case 'priority':
        const priorityOrder = { urgent: 4, high: 3, medium: 2, low: 1 }
        return priorityOrder[b.priority] - priorityOrder[a.priority]
      case 'deadline':
        if (!a.deadline) return 1
        if (!b.deadline) return -1
        return new Date(a.deadline) - new Date(b.deadline)
      case 'createdAt':
      default:
        return new Date(b.createdAt) - new Date(a.createdAt)
    }
  })

  return filtered
})

// Get status badge styling
const getStatusBadge = (status) => {
  const statusConfig = {
    'planning': { class: 'status-planning', label: 'Planning' },
    'in-progress': { class: 'status-progress', label: 'In Progress' },
    'completed': { class: 'status-completed', label: 'Completed' },
    'on-hold': { class: 'status-hold', label: 'On Hold' }
  }
  return statusConfig[status] || { class: 'status-default', label: status }
}

// Get priority badge styling
const getPriorityBadge = (priority) => {
  const priorityConfig = {
    'urgent': { class: 'priority-urgent', label: 'Urgent' },
    'high': { class: 'priority-high', label: 'High' },
    'medium': { class: 'priority-medium', label: 'Medium' },
    'low': { class: 'priority-low', label: 'Low' }
  }
  return priorityConfig[priority] || { class: 'priority-default', label: priority }
}

// Format date
const formatDate = (dateString) => {
  if (!dateString) return 'No deadline'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Check if deadline is overdue
const isOverdue = (deadline) => {
  if (!deadline) return false
  return new Date(deadline) < new Date()
}

// Delete project
const deleteProject = async (projectId) => {
  if (!confirm('Are you sure you want to delete this project?')) {
    return
  }

  try {
    await projectService.deleteProject(projectId)
    await fetchProjects() // Refresh data
  } catch (error) {
    console.error('Error deleting project:', error)
    alert('Failed to delete project. Please try again.')
  }
}

onMounted(fetchProjects)
</script>

<template>
  <div class="dashboard-container">
    <div class="dashboard-header">
      <h1>Project Dashboard</h1>
      <router-link to="/add" class="btn-add-project">
        <span>+</span>
        Add New Project
      </router-link>
    </div>

    <!-- Filters and Controls -->
    <div class="dashboard-controls">
      <div class="filter-group">
        <label for="statusFilter">Filter by Status:</label>
        <select id="statusFilter" v-model="filterStatus" class="filter-select">
          <option value="all">All Statuses</option>
          <option value="planning">Planning</option>
          <option value="in-progress">In Progress</option>
          <option value="completed">Completed</option>
          <option value="on-hold">On Hold</option>
        </select>
      </div>

      <div class="filter-group">
        <label for="sortBy">Sort by:</label>
        <select id="sortBy" v-model="sortBy" class="filter-select">
          <option value="createdAt">Date Created</option>
          <option value="name">Name</option>
          <option value="priority">Priority</option>
          <option value="deadline">Deadline</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Loading projects...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="fetchProjects" class="btn-retry">Retry</button>
    </div>

    <!-- Projects List -->
    <div v-else class="projects-grid">
      <div v-if="filteredProjects.length === 0" class="empty-state">
        <div class="empty-icon">📋</div>
        <h3>No projects found</h3>
        <p v-if="filterStatus !== 'all'">
          No projects match the selected status filter.
        </p>
        <p v-else>
          Get started by adding your first project!
        </p>
        <router-link to="/add" class="btn-primary">Add Project</router-link>
      </div>

      <div 
        v-for="project in filteredProjects" 
        :key="project.id" 
        class="project-card"
        :class="{ 'overdue': isOverdue(project.deadline) }"
      >
        <div class="project-header">
          <h3 class="project-title">{{ project.name }}</h3>
          <div class="project-badges">
            <span :class="['status-badge', getStatusBadge(project.status).class]">
              {{ getStatusBadge(project.status).label }}
            </span>
            <span :class="['priority-badge', getPriorityBadge(project.priority).class]">
              {{ getPriorityBadge(project.priority).label }}
            </span>
          </div>
        </div>

        <p class="project-description">{{ project.description }}</p>

        <div class="project-meta">
          <div class="meta-item">
            <span class="meta-label">Deadline:</span>
            <span 
              :class="['deadline', { 'overdue': isOverdue(project.deadline) }]"
            >
              {{ formatDate(project.deadline) }}
            </span>
          </div>
          
          <div class="meta-item">
            <span class="meta-label">Created:</span>
            <span>{{ formatDate(project.createdAt) }}</span>
          </div>
        </div>

        <div class="project-actions">
          <button class="btn-edit">Edit</button>
          <button class="btn-delete" @click="deleteProject(project.id)">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e2e8f0;
}

.dashboard-header h1 {
  color: #2d3748;
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
}

.btn-add-project {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.btn-add-project:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-add-project span {
  font-size: 1.2rem;
  font-weight: bold;
}

.dashboard-controls {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  padding: 1rem;
  background: #f7fafc;
  border-radius: 8px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-weight: 600;
  color: #4a5568;
  font-size: 0.9rem;
}

.filter-select {
  padding: 0.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background: white;
  font-size: 0.9rem;
}

.loading-state,
.error-state,
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.project-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  position: relative;
}

.project-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.project-card.overdue {
  border-left: 4px solid #e53e3e;
}

.project-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.project-title {
  color: #2d3748;
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
  flex: 1;
  margin-right: 1rem;
}

.project-badges {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.status-badge,
.priority-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-planning {
  background: #bee3f8;
  color: #2b6cb0;
}

.status-progress {
  background: #fef5e7;
  color: #d69e2e;
}

.status-completed {
  background: #c6f6d5;
  color: #2f855a;
}

.status-hold {
  background: #fed7d7;
  color: #c53030;
}

.priority-urgent {
  background: #fed7d7;
  color: #c53030;
}

.priority-high {
  background: #fef5e7;
  color: #d69e2e;
}

.priority-medium {
  background: #bee3f8;
  color: #2b6cb0;
}

.priority-low {
  background: #e6fffa;
  color: #319795;
}

.project-description {
  color: #4a5568;
  line-height: 1.6;
  margin-bottom: 1rem;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.project-meta {
  margin-bottom: 1rem;
}

.meta-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.meta-label {
  color: #718096;
  font-weight: 500;
}

.deadline {
  font-weight: 600;
}

.deadline.overdue {
  color: #e53e3e;
}

.project-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-edit,
.btn-delete {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-edit {
  background: #e6fffa;
  color: #319795;
}

.btn-edit:hover {
  background: #b2f5ea;
}

.btn-delete {
  background: #fed7d7;
  color: #c53030;
}

.btn-delete:hover {
  background: #feb2b2;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  display: inline-block;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-retry {
  background: #667eea;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-retry:hover {
  background: #5a67d8;
}

@media (max-width: 768px) {
  .dashboard-container {
    padding: 1rem;
  }
  
  .dashboard-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .dashboard-controls {
    flex-direction: column;
    gap: 1rem;
  }
  
  .projects-grid {
    grid-template-columns: 1fr;
  }
  
  .project-header {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .project-badges {
    align-self: flex-start;
  }
}
</style>
