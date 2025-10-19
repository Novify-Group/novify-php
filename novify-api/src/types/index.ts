// User and Authentication Types
export interface User {
  id: number
  name: string
  email: string
  role: 'super_admin' | 'admin' | 'staff'
  permissions: string[]
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  isLoading: boolean
  error: string | null
}

// API Response Types
export interface ApiResponse<T = any> {
  success: boolean
  message: string
  data: T
}

export interface PaginatedResponse<T> {
  data: T[]
  pagination: {
    current_page: number
    per_page: number
    total: number
    last_page: number
    from: number
    to: number
  }
}

// Dashboard Types
export interface DashboardStats {
  total_merchants: number
  active_merchants: number
  pending_merchants: number
  total_products: number
  total_orders: number
  total_revenue: number
  system_health: 'healthy' | 'warning' | 'critical'
  recent_activities: Activity[]
}

export interface Activity {
  id: number
  type: string
  description: string
  user: string
  timestamp: string
}

// Merchant Types
export interface Merchant {
  id: number
  business_name: string
  owner_name: string
  email: string
  phone: string
  status: 'active' | 'pending' | 'suspended'
  verification_status: 'verified' | 'pending' | 'rejected'
  total_products: number
  total_orders: number
  total_revenue: number
  created_at: string
  updated_at: string
}

export interface MerchantFilters {
  search?: string
  status?: string
  verification_status?: string
  date_from?: string
  date_to?: string
  page?: number
  per_page?: number
}

// Admin User Types
export interface AdminUser {
  id: number
  name: string
  email: string
  role: 'super_admin' | 'admin' | 'staff'
  permissions: string[]
  is_active: boolean
  last_login: string | null
  created_at: string
  updated_at: string
}

export interface CreateAdminUserRequest {
  name: string
  email: string
  password: string
  role: 'admin' | 'staff'
  permissions: string[]
}

export interface UpdateAdminUserRequest {
  name?: string
  email?: string
  role?: 'admin' | 'staff'
  permissions?: string[]
  is_active?: boolean
}

// Temporary Records Types
export interface TempCategory {
  id: number
  name: string
  description: string
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface TempMeasureUnit {
  id: number
  name: string
  symbol: string
  is_active: boolean
  created_at: string
  updated_at: string
}

// Bill Configuration Types
export interface BillCategory {
  id: number
  name: string
  description: string
  icon: string | null
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface Biller {
  id: number
  name: string
  code: string
  logo: string | null
  description: string
  is_active: boolean
  bill_category_id: number
  created_at: string
  updated_at: string
}

// Lookup Data Types
export interface Country {
  id: number
  name: string
  code: string
  phone_code: string
  currency_code: string
  currency_symbol: string
  is_active: boolean
}

export interface Currency {
  id: number
  name: string
  code: string
  symbol: string
  is_active: boolean
}

// UI State Types
export interface UIState {
  sidebarOpen: boolean
  theme: 'light' | 'dark'
  notifications: Notification[]
  loading: boolean
  error: string | null
}

export interface Notification {
  id: string
  type: 'success' | 'error' | 'warning' | 'info'
  title: string
  message: string
  timestamp: string
  read: boolean
}

// Form Types
export interface LoginForm {
  email: string
  password: string
  remember: boolean
}

export interface FilterForm {
  search: string
  status: string
  date_from: string
  date_to: string
}

// Table Types
export interface TableColumn<T> {
  key: keyof T
  label: string
  sortable?: boolean
  render?: (value: any, row: T) => React.ReactNode
}

export interface TableProps<T> {
  data: T[]
  columns: TableColumn<T>[]
  loading?: boolean
  pagination?: {
    current: number
    total: number
    pageSize: number
    onChange: (page: number, pageSize: number) => void
  }
  onSort?: (key: keyof T, direction: 'asc' | 'desc') => void
  onRowClick?: (row: T) => void
}

// Modal Types
export interface ModalProps {
  isOpen: boolean
  onClose: () => void
  title: string
  children: React.ReactNode
  size?: 'sm' | 'md' | 'lg' | 'xl'
  showCloseButton?: boolean
}

// API Error Types
export interface ApiError {
  message: string
  errors?: Record<string, string[]>
  status: number
}
