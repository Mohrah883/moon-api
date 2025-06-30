<template>
  <div class="stats-container">
    <h3 class="stats-title">Project Overview</h3>
    
    <div class="stats-grid">
      <!-- Total Projects -->
      <div class="stat-card total">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
          <div class="stat-number">{{ stats.total }}</div>
          <div class="stat-label">Total Projects</div>
        </div>
      </div>

      <!-- Overdue Projects -->
      <div class="stat-card overdue" :class="{ 'has-overdue': stats.overdue > 0 }">
        <div class="stat-icon">⚠️</div>
        <div class="stat-content">
          <div class="stat-number">{{ stats.overdue }}</div>
          <div class="stat-label">Overdue</div>
        </div>
      </div>

      <!-- In Progress -->
      <div class="stat-card progress">
        <div class="stat-icon">🚀</div>
        <div class="stat-content">
          <div class="stat-number">{{ stats.byStatus['in-progress'] }}</div>
          <div class="stat-label">In Progress</div>
        </div>
      </div>

      <!-- Completed -->
      <div class="stat-card completed">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
          <div class="stat-number">{{ stats.byStatus.completed }}</div>
          <div class="stat-label">Completed</div>
        </div>
      </div>
    </div>

    <!-- Status Breakdown -->
    <div class="status-breakdown">
      <h4>Status Breakdown</h4>
      <div class="status-bars">
        <div 
          v-for="(count, status) in stats.byStatus" 
          :key="status"
          class="status-bar"
          v-show="count > 0"
        >
          <div class="status-info">
            <span class="status-name">{{ getStatusLabel(status) }}</span>
            <span class="status-count">{{ count }}</span>
          </div>
          <div class="progress-bar">
            <div 
              class="progress-fill"
              :class="`status-${status}`"
              :style="{ width: `${getPercentage(count)}%` }"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Priority Breakdown -->
    <div class="priority-breakdown">
      <h4>Priority Breakdown</h4>
      <div class="priority-list">
        <div 
          v-for="(count, priority) in stats.byPriority" 
          :key="priority"
          class="priority-item"
          v-show="count > 0"
        >
          <span class="priority-badge" :class="`priority-${priority}`">
            {{ getPriorityLabel(priority) }}
          </span>
          <span class="priority-count">{{ count }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  stats: {
    type: Object,
    required: true,
    default: () => ({
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
  }
})

// Get percentage for progress bars
const getPercentage = (count) => {
  if (props.stats.total === 0) return 0
  return Math.round((count / props.stats.total) * 100)
}

// Get status labels
const getStatusLabel = (status) => {
  const labels = {
    'planning': 'Planning',
    'in-progress': 'In Progress',
    'completed': 'Completed',
    'on-hold': 'On Hold'
  }
  return labels[status] || status
}

// Get priority labels
const getPriorityLabel = (priority) => {
  const labels = {
    'urgent': 'Urgent',
    'high': 'High',
    'medium': 'Medium',
    'low': 'Low'
  }
  return labels[priority] || priority
}
</script>

<style scoped>
.stats-container {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
  margin-bottom: 2rem;
}

.stats-title {
  color: #2d3748;
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  text-align: center;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  display: flex;
  align-items: center;
  padding: 1rem;
  border-radius: 8px;
  background: #f7fafc;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.stat-card.total {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.stat-card.overdue {
  background: #fed7d7;
  color: #c53030;
  border-color: #feb2b2;
}

.stat-card.overdue.has-overdue {
  background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
  color: white;
}

.stat-card.progress {
  background: #fef5e7;
  color: #d69e2e;
  border-color: #fbd38d;
}

.stat-card.completed {
  background: #c6f6d5;
  color: #2f855a;
  border-color: #9ae6b4;
}

.stat-icon {
  font-size: 2rem;
  margin-right: 1rem;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.9rem;
  font-weight: 500;
  opacity: 0.9;
}

.status-breakdown,
.priority-breakdown {
  margin-bottom: 1.5rem;
}

.status-breakdown h4,
.priority-breakdown h4 {
  color: #4a5568;
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.status-bars {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.status-bar {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.status-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.9rem;
}

.status-name {
  color: #4a5568;
  font-weight: 500;
}

.status-count {
  color: #718096;
  font-weight: 600;
}

.progress-bar {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.progress-fill.status-planning {
  background: linear-gradient(90deg, #bee3f8 0%, #63b3ed 100%);
}

.progress-fill.status-progress {
  background: linear-gradient(90deg, #fef5e7 0%, #f6ad55 100%);
}

.progress-fill.status-completed {
  background: linear-gradient(90deg, #c6f6d5 0%, #68d391 100%);
}

.progress-fill.status-hold {
  background: linear-gradient(90deg, #fed7d7 0%, #fc8181 100%);
}

.priority-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.priority-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  background: #f7fafc;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

.priority-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.priority-badge.priority-urgent {
  background: #fed7d7;
  color: #c53030;
}

.priority-badge.priority-high {
  background: #fef5e7;
  color: #d69e2e;
}

.priority-badge.priority-medium {
  background: #bee3f8;
  color: #2b6cb0;
}

.priority-badge.priority-low {
  background: #e6fffa;
  color: #319795;
}

.priority-count {
  font-weight: 600;
  color: #4a5568;
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .stat-card {
    padding: 0.75rem;
  }
  
  .stat-icon {
    font-size: 1.5rem;
    margin-right: 0.75rem;
  }
  
  .stat-number {
    font-size: 1.5rem;
  }
  
  .priority-list {
    flex-direction: column;
  }
  
  .priority-item {
    justify-content: space-between;
  }
}
</style> 