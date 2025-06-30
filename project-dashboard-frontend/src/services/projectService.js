import axios from 'axios'

const API_BASE_URL = 'http://localhost:3001'

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  timeout: 10000,
})

// Request interceptor for logging
apiClient.interceptors.request.use(
  (config) => {
    console.log(`Making ${config.method?.toUpperCase()} request to ${config.url}`)
    return config
  },
  (error) => {
    console.error('Request error:', error)
    return Promise.reject(error)
  }
)

// Response interceptor for error handling
apiClient.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    console.error('Response error:', error)
    if (error.response?.status === 404) {
      throw new Error('Resource not found')
    } else if (error.response?.status >= 500) {
      throw new Error('Server error. Please try again later.')
    } else if (error.code === 'ECONNABORTED') {
      throw new Error('Request timeout. Please check your connection.')
    } else {
      throw new Error(error.response?.data?.message || 'An unexpected error occurred')
    }
  }
)

export const projectService = {
  // Get all projects
  async getAllProjects() {
    try {
      const response = await apiClient.get('/projects')
      return response.data
    } catch (error) {
      console.error('Error fetching projects:', error)
      throw error
    }
  },

  // Get single project by ID
  async getProjectById(id) {
    try {
      const response = await apiClient.get(`/projects/${id}`)
      return response.data
    } catch (error) {
      console.error(`Error fetching project ${id}:`, error)
      throw error
    }
  },

  // Create new project
  async createProject(projectData) {
    try {
      const response = await apiClient.post('/projects', {
        ...projectData,
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      })
      return response.data
    } catch (error) {
      console.error('Error creating project:', error)
      throw error
    }
  },

  // Update project
  async updateProject(id, projectData) {
    try {
      const response = await apiClient.put(`/projects/${id}`, {
        ...projectData,
        updatedAt: new Date().toISOString()
      })
      return response.data
    } catch (error) {
      console.error(`Error updating project ${id}:`, error)
      throw error
    }
  },

  // Delete project
  async deleteProject(id) {
    try {
      await apiClient.delete(`/projects/${id}`)
      return true
    } catch (error) {
      console.error(`Error deleting project ${id}:`, error)
      throw error
    }
  },

  // Get projects by status
  async getProjectsByStatus(status) {
    try {
      const allProjects = await this.getAllProjects()
      return allProjects.filter(project => project.status === status)
    } catch (error) {
      console.error(`Error fetching projects by status ${status}:`, error)
      throw error
    }
  },

  // Get projects by priority
  async getProjectsByPriority(priority) {
    try {
      const allProjects = await this.getAllProjects()
      return allProjects.filter(project => project.priority === priority)
    } catch (error) {
      console.error(`Error fetching projects by priority ${priority}:`, error)
      throw error
    }
  },

  // Get overdue projects
  async getOverdueProjects() {
    try {
      const allProjects = await this.getAllProjects()
      const now = new Date()
      return allProjects.filter(project => {
        if (!project.deadline) return false
        return new Date(project.deadline) < now
      })
    } catch (error) {
      console.error('Error fetching overdue projects:', error)
      throw error
    }
  },

  // Get project statistics
  async getProjectStats() {
    try {
      const allProjects = await this.getAllProjects()
      const stats = {
        total: allProjects.length,
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
        },
        overdue: 0
      }

      allProjects.forEach(project => {
        // Count by status
        if (stats.byStatus[project.status] !== undefined) {
          stats.byStatus[project.status]++
        }

        // Count by priority
        if (stats.byPriority[project.priority] !== undefined) {
          stats.byPriority[project.priority]++
        }

        // Count overdue
        if (project.deadline && new Date(project.deadline) < new Date()) {
          stats.overdue++
        }
      })

      return stats
    } catch (error) {
      console.error('Error fetching project stats:', error)
      throw error
    }
  }
}

export default projectService 