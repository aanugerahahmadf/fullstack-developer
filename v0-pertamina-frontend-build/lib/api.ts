import axios, { AxiosError } from 'axios';

// Define TypeScript interfaces for our data models
interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

interface Building {
  id: string;
  name: string;
  latitude: string;
  longitude: string;
  marker_icon_url?: string;
  rooms?: Room[];
  created_at: string;
  updated_at: string;
}

interface Room {
  id: string;
  name: string;
  building_id: string;
  latitude?: string;
  longitude?: string;
  marker_icon_url?: string;
  cctvs?: Cctv[];
  created_at: string;
  updated_at: string;
}

interface Cctv {
  id: string;
  name: string;
  ip_rtsp_url?: string;
  room_id: string;
  location?: string;
  status?: string;
  ip_address: string;
  created_at: string;
  updated_at: string;
}

interface Contact {
  id: string;
  email?: string;
  phone?: string;
  whatsapp?: string;
  instagram?: string;
  address?: string;
  created_at: string;
  updated_at: string;
}

interface Stats {
  total_buildings: number;
  total_rooms: number;
  total_cctvs: number;
}

interface ProductionTrend {
  date: string;
  production: number;
  target: number;
}

interface UnitPerformance {
  unit: string;
  efficiency: number;
  capacity: number;
}

const API_BASE_URL = 'http://127.0.0.1:8000/api';

// Create axios instance with optimized config for faster response
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept-Encoding': 'gzip, deflate, br'
  },
  timeout: 3000, // Reduced timeout to 3 seconds for faster response
  // Disable request retries for faster failure
  transitional: {
    clarifyTimeoutError: true,
  },
  // Handle compression properly
  decompress: true,
});

// Simple in-memory cache for API responses
const apiCache = new Map<string, { data: any; timestamp: number }>();
const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes cache

// Axios response interceptor for error handling
apiClient.interceptors.response.use(
  (response) => response,
  (error: AxiosError) => {
    console.error('API Error:', error.response?.data || error.message);
    return Promise.reject(error);
  }
);

// Add request interceptor for caching
apiClient.interceptors.request.use(
  (config) => {
    // Create cache key
    const cacheKey = `${config.method?.toUpperCase()}:${config.url}`;
    
    // Check if we have a cached response that's still valid
    if (config.method === 'get' && apiCache.has(cacheKey)) {
      const cached = apiCache.get(cacheKey)!;
      if (Date.now() - cached.timestamp < CACHE_DURATION) {
        // Return cached response
        return Promise.reject({ cached: cached.data });
      }
    }
    
    return config;
  },
  (error) => {
    console.error('API Request Error:', error);
    return Promise.reject(error);
  }
);

// Add response interceptor for caching
apiClient.interceptors.response.use(
  (response) => {
    // Cache GET responses
    if (response.config.method === 'get') {
      const cacheKey = `${response.config.method?.toUpperCase()}:${response.config.url}`;
      apiCache.set(cacheKey, {
        data: response.data,
        timestamp: Date.now()
      });
    }
    return response;
  },
  (error) => {
    // Handle cached response
    if (error.cached) {
      return Promise.resolve({ data: error.cached });
    }
    return Promise.reject(error);
  }
);

export const api = {
  // Stats
  getStats: async (): Promise<Stats> => {
    try {
      const response = await apiClient.get<ApiResponse<Stats>>('/stats');
      return response.data.data;
    } catch (error) {
      console.error('Failed to fetch stats:', error);
      throw error;
    }
  },

  // Chart Data
  getProductionTrends: async (startDate?: string, endDate?: string): Promise<ProductionTrend[]> => {
    try {
      const params = new URLSearchParams();
      if (startDate) params.append('start_date', startDate);
      if (endDate) params.append('end_date', endDate);
      
      const url = `/chart/production-trends${params.toString() ? '?' + params.toString() : ''}`;
      const response = await apiClient.get<ApiResponse<ProductionTrend[]>>(url);
      return response.data.data;
    } catch (error) {
      console.error('Failed to fetch production trends:', error);
      throw error;
    }
  },

  getUnitPerformance: async (): Promise<UnitPerformance[]> => {
    try {
      const response = await apiClient.get<ApiResponse<UnitPerformance[]>>('/chart/unit-performance');
      return response.data.data;
    } catch (error) {
      console.error('Failed to fetch unit performance:', error);
      throw error;
    }
  },

  // Buildings
  getBuildings: async (): Promise<Building[]> => {
    try {
      const response = await apiClient.get<ApiResponse<Building[]>>('/buildings');
      return response.data.data;
    } catch (error) {
      console.error('Failed to fetch buildings:', error);
      throw error;
    }
  },

  getBuilding: async (id: string): Promise<Building> => {
    try {
      const response = await apiClient.get<ApiResponse<Building>>(`/buildings/${id}`);
      return response.data.data;
    } catch (error) {
      console.error(`Failed to fetch building ${id}:`, error);
      throw error;
    }
  },

  // Rooms
  getRooms: async (): Promise<Room[]> => {
    try {
      const response = await apiClient.get<ApiResponse<Room[]>>('/rooms');
      return response.data.data;
    } catch (error) {
      console.error('Failed to fetch rooms:', error);
      throw error;
    }
  },

  getRoomsByBuilding: async (buildingId: string): Promise<Room[]> => {
    try {
      const response = await apiClient.get<ApiResponse<Room[]>>(`/rooms/building/${buildingId}`);
      return response.data.data;
    } catch (error) {
      console.error(`Failed to fetch rooms for building ${buildingId}:`, error);
      throw error;
    }
  },

  getRoom: async (id: string): Promise<Room> => {
    try {
      const response = await apiClient.get<ApiResponse<Room>>(`/rooms/${id}`);
      return response.data.data;
    } catch (error) {
      console.error(`Failed to fetch room ${id}:`, error);
      throw error;
    }
  },

  // CCTVs
  getCctvs: async (): Promise<Cctv[]> => {
    try {
      const response = await apiClient.get<ApiResponse<Cctv[]>>('/cctvs');
      return response.data.data;
    } catch (error) {
      console.error('Failed to fetch CCTVs:', error);
      throw error;
    }
  },

  getCctvsByRoom: async (roomId: string): Promise<Cctv[]> => {
    try {
      const response = await apiClient.get<ApiResponse<Cctv[]>>(`/cctvs/room/${roomId}`);
      return response.data.data;
    } catch (error) {
      console.error(`Failed to fetch CCTVs for room ${roomId}:`, error);
      throw error;
    }
  },

  getCctv: async (id: string): Promise<Cctv> => {
    try {
      const response = await apiClient.get<ApiResponse<Cctv>>(`/cctvs/${id}`);
      return response.data.data;
    } catch (error) {
      console.error(`Failed to fetch CCTV ${id}:`, error);
      throw error;
    }
  },

  getCctvStreamUrl: async (id: string): Promise<{ stream_url: string }> => {
    try {
      const response = await apiClient.get<ApiResponse<{ stream_url: string }>>(`/cctvs/${id}/stream-url`);
      return response.data.data;
    } catch (error) {
      console.error(`Failed to fetch stream URL for CCTV ${id}:`, error);
      throw error;
    }
  },

  // Contacts
  getContacts: async (): Promise<Contact[] | Contact | null> => {
    try {
      const response = await apiClient.get<ApiResponse<Contact[] | Contact | null>>('/contacts');
      return response.data.data;
    } catch (error) {
      console.error('Failed to fetch contacts:', error);
      throw error;
    }
  },

  // Cache management
  clearCache: (): void => {
    apiCache.clear();
  }
};