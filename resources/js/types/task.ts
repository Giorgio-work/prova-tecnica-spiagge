export interface TaskEntity {
  id: number
  title: string
  description?: string
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled'
  priority: 'low' | 'medium' | 'high' | 'urgent'
  due_date?: string
  created_at: string
  updated_at: string
  completed_at?: string
  user_id: number
}

export interface TaskWithRelations extends TaskEntity {
  user?: {
    id: number
    name: string
    email: string
  }
}

export interface CreateTaskProps {
  users: UserEntity[]
}

export interface EditTaskProps {
  task: TaskEntity
  users: UserEntity[]
}

export interface TaskCardProps {
  task: TaskWithRelations
}

export interface TaskIndexProps {
  tasks: {
    data: TaskWithRelations[]
    links: PaginationLink[]
    meta: {
      current_page: number
      from: number
      last_page: number
      per_page: number
      to: number
      total: number
    }
  }
  statuses: string[]
  priorities: string[]
  users: UserEntity[]
  filters: {
    status?: string
    priority?: string
    my_tasks?: boolean
    overdue?: boolean
  }
}

export interface UserEntity {
  id: number
  name: string
  email: string
}

export interface PaginationLink {
  url: string | null
  label: string
  active: boolean
}

export interface PaginationProps {
  links: PaginationLink[]
}
