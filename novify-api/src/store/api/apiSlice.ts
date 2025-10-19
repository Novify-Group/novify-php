import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'
import { RootState } from '../index'

// API Configuration
const API_BASE_URL = process.env.NODE_ENV === 'production' 
  ? 'https://novify.solvertech.co/api/v1' 
  : 'http://localhost:8000/api/v1'

export const api = createApi({
  reducerPath: 'api',
  baseQuery: fetchBaseQuery({
    baseUrl: API_BASE_URL,
    credentials: 'include', // Important for HTTP-only cookies
    prepareHeaders: (headers, { getState }) => {
      // Get token from auth state if available
      const token = (getState() as RootState).auth.token
      if (token) {
        headers.set('authorization', `Bearer ${token}`)
      }
      return headers
    },
  }),
  tagTypes: [
    'User',
    'Dashboard',
    'Merchant',
    'AdminUser',
    'TempCategory',
    'TempMeasureUnit',
    'BillCategory',
    'Biller',
    'Country',
    'Currency',
    'Settings',
  ],
  endpoints: (builder) => ({
    // Auth endpoints
    login: builder.mutation({
      query: (credentials) => ({
        url: '/auth/login',
        method: 'POST',
        body: credentials,
      }),
      invalidatesTags: ['User'],
    }),
    logout: builder.mutation({
      query: () => ({
        url: '/auth/logout',
        method: 'POST',
      }),
      invalidatesTags: ['User'],
    }),
    getMe: builder.query({
      query: () => '/auth/me',
      providesTags: ['User'],
    }),
    
    // Dashboard endpoints
    getDashboardStats: builder.query({
      query: () => '/admin/dashboard',
      providesTags: ['Dashboard'],
    }),
    getSystemHealth: builder.query({
      query: () => '/admin/health',
      providesTags: ['Dashboard'],
    }),
    
    // Merchant endpoints
    getMerchants: builder.query({
      query: (params) => ({
        url: '/admin/merchants',
        params,
      }),
      providesTags: ['Merchant'],
    }),
    getMerchant: builder.query({
      query: (id) => `/admin/merchants/${id}`,
      providesTags: (result, error, id) => [{ type: 'Merchant', id }],
    }),
    updateMerchantStatus: builder.mutation({
      query: ({ id, status }) => ({
        url: `/admin/merchants/${id}/status`,
        method: 'PUT',
        body: { status },
      }),
      invalidatesTags: ['Merchant'],
    }),
    getMerchantAnalytics: builder.query({
      query: (id) => `/admin/merchants/${id}/analytics`,
      providesTags: (result, error, id) => [{ type: 'Merchant', id }],
    }),
    notifyMerchant: builder.mutation({
      query: ({ id, message }) => ({
        url: `/admin/merchants/${id}/notify`,
        method: 'POST',
        body: { message },
      }),
    }),
    
    // Admin User endpoints
    getAdminUsers: builder.query({
      query: (params) => ({
        url: '/admin/users',
        params,
      }),
      providesTags: ['AdminUser'],
    }),
    createAdminUser: builder.mutation({
      query: (data) => ({
        url: '/admin/users',
        method: 'POST',
        body: data,
      }),
      invalidatesTags: ['AdminUser'],
    }),
    updateAdminUser: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/users/${id}`,
        method: 'PUT',
        body: data,
      }),
      invalidatesTags: ['AdminUser'],
    }),
    deleteAdminUser: builder.mutation({
      query: (id) => ({
        url: `/admin/users/${id}`,
        method: 'DELETE',
      }),
      invalidatesTags: ['AdminUser'],
    }),
    resetAdminUserPassword: builder.mutation({
      query: (id) => ({
        url: `/admin/users/${id}/reset-password`,
        method: 'POST',
      }),
    }),
    
    // Temporary Records endpoints
    getTempCategories: builder.query({
      query: (params) => ({
        url: '/admin/temp/categories',
        params,
      }),
      providesTags: ['TempCategory'],
    }),
    createTempCategory: builder.mutation({
      query: (data) => ({
        url: '/admin/temp/categories',
        method: 'POST',
        body: data,
      }),
      invalidatesTags: ['TempCategory'],
    }),
    updateTempCategory: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/temp/categories/${id}`,
        method: 'PUT',
        body: data,
      }),
      invalidatesTags: ['TempCategory'],
    }),
    deleteTempCategory: builder.mutation({
      query: (id) => ({
        url: `/admin/temp/categories/${id}`,
        method: 'DELETE',
      }),
      invalidatesTags: ['TempCategory'],
    }),
    
    getTempMeasureUnits: builder.query({
      query: (params) => ({
        url: '/admin/temp/measure-units',
        params,
      }),
      providesTags: ['TempMeasureUnit'],
    }),
    createTempMeasureUnit: builder.mutation({
      query: (data) => ({
        url: '/admin/temp/measure-units',
        method: 'POST',
        body: data,
      }),
      invalidatesTags: ['TempMeasureUnit'],
    }),
    updateTempMeasureUnit: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/temp/measure-units/${id}`,
        method: 'PUT',
        body: data,
      }),
      invalidatesTags: ['TempMeasureUnit'],
    }),
    deleteTempMeasureUnit: builder.mutation({
      query: (id) => ({
        url: `/admin/temp/measure-units/${id}`,
        method: 'DELETE',
      }),
      invalidatesTags: ['TempMeasureUnit'],
    }),
    
    // Bill Configuration endpoints
    getBillCategories: builder.query({
      query: (params) => ({
        url: '/admin/bill-categories',
        params,
      }),
      providesTags: ['BillCategory'],
    }),
    createBillCategory: builder.mutation({
      query: (data) => ({
        url: '/admin/bill-categories',
        method: 'POST',
        body: data,
      }),
      invalidatesTags: ['BillCategory'],
    }),
    updateBillCategory: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/bill-categories/${id}`,
        method: 'PUT',
        body: data,
      }),
      invalidatesTags: ['BillCategory'],
    }),
    deleteBillCategory: builder.mutation({
      query: (id) => ({
        url: `/admin/bill-categories/${id}`,
        method: 'DELETE',
      }),
      invalidatesTags: ['BillCategory'],
    }),
    
    getBillers: builder.query({
      query: (params) => ({
        url: '/admin/billers',
        params,
      }),
      providesTags: ['Biller'],
    }),
    createBiller: builder.mutation({
      query: (data) => ({
        url: '/admin/billers',
        method: 'POST',
        body: data,
      }),
      invalidatesTags: ['Biller'],
    }),
    updateBiller: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/billers/${id}`,
        method: 'PUT',
        body: data,
      }),
      invalidatesTags: ['Biller'],
    }),
    deleteBiller: builder.mutation({
      query: (id) => ({
        url: `/admin/billers/${id}`,
        method: 'DELETE',
      }),
      invalidatesTags: ['Biller'],
    }),
    
    // Lookup Data endpoints
    getCountries: builder.query({
      query: (params) => ({
        url: '/admin/lookup/countries',
        params,
      }),
      providesTags: ['Country'],
    }),
    updateCountry: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/lookup/countries/${id}`,
        method: 'PUT',
        body: data,
      }),
      invalidatesTags: ['Country'],
    }),
    
    getCurrencies: builder.query({
      query: (params) => ({
        url: '/admin/lookup/currencies',
        params,
      }),
      providesTags: ['Currency'],
    }),
    createCurrency: builder.mutation({
      query: (data) => ({
        url: '/admin/lookup/currencies',
        method: 'POST',
        body: data,
      }),
      invalidatesTags: ['Currency'],
    }),
    updateCurrency: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/lookup/currencies/${id}`,
        method: 'PUT',
        body: data,
      }),
      invalidatesTags: ['Currency'],
    }),
    deleteCurrency: builder.mutation({
      query: (id) => ({
        url: `/admin/lookup/currencies/${id}`,
        method: 'DELETE',
      }),
      invalidatesTags: ['Currency'],
    }),
    
    getSettings: builder.query({
      query: () => '/admin/settings',
      providesTags: ['Settings'],
    }),
    updateSettings: builder.mutation({
      query: (data) => ({
        url: '/admin/settings',
        method: 'PUT',
        body: data,
      }),
      invalidatesTags: ['Settings'],
    }),
  }),
})

// Export hooks for usage in functional components
export const {
  // Auth
  useLoginMutation,
  useLogoutMutation,
  useGetMeQuery,
  
  // Dashboard
  useGetDashboardStatsQuery,
  useGetSystemHealthQuery,
  
  // Merchants
  useGetMerchantsQuery,
  useGetMerchantQuery,
  useUpdateMerchantStatusMutation,
  useGetMerchantAnalyticsQuery,
  useNotifyMerchantMutation,
  
  // Admin Users
  useGetAdminUsersQuery,
  useCreateAdminUserMutation,
  useUpdateAdminUserMutation,
  useDeleteAdminUserMutation,
  useResetAdminUserPasswordMutation,
  
  // Temp Records
  useGetTempCategoriesQuery,
  useCreateTempCategoryMutation,
  useUpdateTempCategoryMutation,
  useDeleteTempCategoryMutation,
  useGetTempMeasureUnitsQuery,
  useCreateTempMeasureUnitMutation,
  useUpdateTempMeasureUnitMutation,
  useDeleteTempMeasureUnitMutation,
  
  // Bill Configs
  useGetBillCategoriesQuery,
  useCreateBillCategoryMutation,
  useUpdateBillCategoryMutation,
  useDeleteBillCategoryMutation,
  useGetBillersQuery,
  useCreateBillerMutation,
  useUpdateBillerMutation,
  useDeleteBillerMutation,
  
  // Lookup Data
  useGetCountriesQuery,
  useUpdateCountryMutation,
  useGetCurrenciesQuery,
  useCreateCurrencyMutation,
  useUpdateCurrencyMutation,
  useDeleteCurrencyMutation,
  useGetSettingsQuery,
  useUpdateSettingsMutation,
} = api
