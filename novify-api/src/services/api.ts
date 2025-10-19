import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios'
import { ApiResponse, ApiError } from '@/types'

// API Configuration
const API_BASE_URL = process.env.NODE_ENV === 'production' 
  ? 'https://novify.solvertech.co/api/v1' 
  : 'http://localhost:8000/api/v1'

// Create axios instance
const api: AxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // Important for HTTP-only cookies
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Log request in development
    if (process.env.NODE_ENV === 'development') {
      console.log(`🚀 API Request: ${config.method?.toUpperCase()} ${config.url}`)
    }
    
    return config
  },
  (error) => {
    console.error('❌ Request Error:', error)
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response: AxiosResponse<ApiResponse>) => {
    // Log response in development
    if (process.env.NODE_ENV === 'development') {
      console.log(`✅ API Response: ${response.status} ${response.config.url}`)
    }
    
    return response
  },
  (error) => {
    // Handle different error types
    if (error.response) {
      const { status, data } = error.response
      
      // Log error in development
      if (process.env.NODE_ENV === 'development') {
        console.error(`❌ API Error: ${status} ${error.config?.url}`, data)
      }
      
      // Handle specific status codes
      switch (status) {
        case 401:
          // Unauthorized - redirect to login
          if (typeof window !== 'undefined') {
            window.location.href = '/login'
          }
          break
        case 403:
          // Forbidden - show access denied message
          console.error('Access denied')
          break
        case 422:
          // Validation errors
          console.error('Validation errors:', data.errors)
          break
        case 500:
          // Server error
          console.error('Server error')
          break
        default:
          console.error('API Error:', data.message || 'Unknown error')
      }
      
      // Transform error to consistent format
      const apiError: ApiError = {
        message: data.message || 'An error occurred',
        errors: data.errors,
        status,
      }
      
      return Promise.reject(apiError)
    } else if (error.request) {
      // Network error
      console.error('❌ Network Error:', error.message)
      const networkError: ApiError = {
        message: 'Network error. Please check your connection.',
        status: 0,
      }
      return Promise.reject(networkError)
    } else {
      // Other error
      console.error('❌ Error:', error.message)
      const unknownError: ApiError = {
        message: error.message || 'An unknown error occurred',
        status: 0,
      }
      return Promise.reject(unknownError)
    }
  }
)

// API Methods
export const apiClient = {
  // Generic methods
  get: <T = any>(url: string, config?: AxiosRequestConfig) =>
    api.get<ApiResponse<T>>(url, config).then(res => res.data),
  
  post: <T = any>(url: string, data?: any, config?: AxiosRequestConfig) =>
    api.post<ApiResponse<T>>(url, data, config).then(res => res.data),
  
  put: <T = any>(url: string, data?: any, config?: AxiosRequestConfig) =>
    api.put<ApiResponse<T>>(url, data, config).then(res => res.data),
  
  patch: <T = any>(url: string, data?: any, config?: AxiosRequestConfig) =>
    api.patch<ApiResponse<T>>(url, data, config).then(res => res.data),
  
  delete: <T = any>(url: string, config?: AxiosRequestConfig) =>
    api.delete<ApiResponse<T>>(url, config).then(res => res.data),
}

// Specific API endpoints
export const authApi = {
  login: (credentials: { email: string; password: string }) =>
    apiClient.post('/auth/login', credentials),
  
  logout: () =>
    apiClient.post('/auth/logout'),
  
  refresh: () =>
    apiClient.post('/auth/refresh'),
  
  me: () =>
    apiClient.get('/auth/me'),
}

export const dashboardApi = {
  getStats: () =>
    apiClient.get('/admin/dashboard'),
  
  getHealth: () =>
    apiClient.get('/admin/health'),
}

export const merchantsApi = {
  getMerchants: (params?: any) =>
    apiClient.get('/admin/merchants', { params }),
  
  getMerchant: (id: number) =>
    apiClient.get(`/admin/merchants/${id}`),
  
  updateMerchantStatus: (id: number, status: string) =>
    apiClient.put(`/admin/merchants/${id}/status`, { status }),
  
  getMerchantAnalytics: (id: number) =>
    apiClient.get(`/admin/merchants/${id}/analytics`),
  
  notifyMerchant: (id: number, message: string) =>
    apiClient.post(`/admin/merchants/${id}/notify`, { message }),
}

export const adminUsersApi = {
  getUsers: (params?: any) =>
    apiClient.get('/admin/users', { params }),
  
  createUser: (data: any) =>
    apiClient.post('/admin/users', data),
  
  updateUser: (id: number, data: any) =>
    apiClient.put(`/admin/users/${id}`, data),
  
  deleteUser: (id: number) =>
    apiClient.delete(`/admin/users/${id}`),
  
  resetPassword: (id: number) =>
    apiClient.post(`/admin/users/${id}/reset-password`),
}

export const tempRecordsApi = {
  getCategories: (params?: any) =>
    apiClient.get('/admin/temp/categories', { params }),
  
  createCategory: (data: any) =>
    apiClient.post('/admin/temp/categories', data),
  
  updateCategory: (id: number, data: any) =>
    apiClient.put(`/admin/temp/categories/${id}`, data),
  
  deleteCategory: (id: number) =>
    apiClient.delete(`/admin/temp/categories/${id}`),
  
  getMeasureUnits: (params?: any) =>
    apiClient.get('/admin/temp/measure-units', { params }),
  
  createMeasureUnit: (data: any) =>
    apiClient.post('/admin/temp/measure-units', data),
  
  updateMeasureUnit: (id: number, data: any) =>
    apiClient.put(`/admin/temp/measure-units/${id}`, data),
  
  deleteMeasureUnit: (id: number) =>
    apiClient.delete(`/admin/temp/measure-units/${id}`),
}

export const billConfigsApi = {
  getCategories: (params?: any) =>
    apiClient.get('/admin/bill-categories', { params }),
  
  createCategory: (data: any) =>
    apiClient.post('/admin/bill-categories', data),
  
  updateCategory: (id: number, data: any) =>
    apiClient.put(`/admin/bill-categories/${id}`, data),
  
  deleteCategory: (id: number) =>
    apiClient.delete(`/admin/bill-categories/${id}`),
  
  getBillers: (params?: any) =>
    apiClient.get('/admin/billers', { params }),
  
  createBiller: (data: any) =>
    apiClient.post('/admin/billers', data),
  
  updateBiller: (id: number, data: any) =>
    apiClient.put(`/admin/billers/${id}`, data),
  
  deleteBiller: (id: number) =>
    apiClient.delete(`/admin/billers/${id}`),
}

export const lookupApi = {
  getCountries: (params?: any) =>
    apiClient.get('/admin/lookup/countries', { params }),
  
  updateCountry: (id: number, data: any) =>
    apiClient.put(`/admin/lookup/countries/${id}`, data),
  
  getCurrencies: (params?: any) =>
    apiClient.get('/admin/lookup/currencies', { params }),
  
  createCurrency: (data: any) =>
    apiClient.post('/admin/lookup/currencies', data),
  
  updateCurrency: (id: number, data: any) =>
    apiClient.put(`/admin/lookup/currencies/${id}`, data),
  
  deleteCurrency: (id: number) =>
    apiClient.delete(`/admin/lookup/currencies/${id}`),
  
  getSettings: () =>
    apiClient.get('/admin/settings'),
  
  updateSettings: (data: any) =>
    apiClient.put('/admin/settings', data),
}

export default api
