// API configuration for the frontend to communicate directly with the backend
const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://127.0.0.1:8000/api';

// PERSISTENT CACHE (LocalStorage + Memory)
// Cache survives page reloads!
const CACHE_KEY_PREFIX = 'pertamina_cache_v2_';
const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes cache

// Helper to get from storage safely
const getFromCache = (key: string) => {
  if (typeof window === 'undefined') return null;
  try {
    const item = localStorage.getItem(CACHE_KEY_PREFIX + key);
    if (!item) return null;
    
    const parsed = JSON.parse(item);
    // Return data regardless of expiry first (Stale-While-Revalidate)
    // We can check expiry to decide if we need to refresh in background
    return parsed;
  } catch {
    return null;
  }
};

// Helper to save to storage safely
const saveToCache = (key: string, data: any) => {
  if (typeof window === 'undefined') return;
  try {
    localStorage.setItem(CACHE_KEY_PREFIX + key, JSON.stringify({
      data,
      timestamp: Date.now()
    }));
  } catch (e) {
    console.warn('Cache quota exceeded likely', e);
  }
};

// Define response wrapper interface
interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

// Simple, fast API helper with PERSISTENT CACHING
export async function api<T>(endpoint: string, options: RequestInit = {}, timeoutMs: number = 10000): Promise<T> {
  const url = `${API_BASE_URL}${endpoint}`;
  
  // Create simple cache key based on URL
  const isGetRequest = !options.method || options.method === 'GET';
  const cacheKey = endpoint;

  // 1. CEK CACHE (LOCAL STORAGE) - INSTANT LOAD!
  if (isGetRequest) {
    const cached = getFromCache(cacheKey);
    if (cached) {
      // If cache exists, return it IMMEDIATELY! 
      // Don't wait for network.
      // We can fetch in background if it's too old (> 5 mins) but return cached data first.
      
      const age = Date.now() - cached.timestamp;
      if (age < CACHE_DURATION) {
        // Data masih fresh, pakai ini saja. No loading at all.
        return cached.data as T;
      }
      
      // Data agak lama, tapi kembalikan saja dulu biar TIDAK ADA LOADING SCREEEN
      // Nanti kita bisa refresh di background (bukan di sini biar ga block)
       console.log(`Serving stale cache for ${endpoint} (age: ${age}ms)`);
       // Trigger background refresh (optional, but good practice)
       fetchWithErrorHandling<T>(url, options, timeoutMs, cacheKey).catch(() => {});
       
       return cached.data as T;
    }
  }
  
  // Kalau tidak ada cache sama sekali, baru fetch
  return fetchWithErrorHandling<T>(url, options, timeoutMs, isGetRequest ? cacheKey : undefined);
}

// Separated fetch logic
async function fetchWithErrorHandling<T>(url: string, options: RequestInit, timeoutMs: number, cacheKey?: string): Promise<T> {
  const config: RequestInit = {
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json',
      ...options.headers,
    },
    signal: options.signal ?? AbortSignal.timeout(timeoutMs),
    ...options,
  };

  try {
    const response = await fetch(url, config);
    
    if (!response.ok) {
      throw new Error(`API request failed: ${response.status} ${response.statusText}`);
    }
    
    const result: ApiResponse<T> = await response.json();
    
    // Simpan ke Cache
    if (cacheKey) {
      saveToCache(cacheKey, result.data);
    }

    return result.data;
  } catch (error) {
    console.error(`API request error for ${url}:`, error);
    
    if (error instanceof Error) {
      if (error.name === 'AbortError') {
         // Silently fail if just timeout and allow component to handle or show stale data
        throw new Error('Timeout'); 
      }
      throw error;
    }
    throw new Error('Unknown API request error');
  }
}

// Define TypeScript interfaces for API responses
interface Stats {
  total_buildings: number;
  total_rooms: number;
  total_cctvs: number;
}

interface Building {
  id: string;
  name: string;
  latitude?: string;
  longitude?: string;
  // Add other building properties as needed
}

interface Room {
  id: string;
  name: string;
  building_id: string;
  // Add other room properties as needed
}

interface Cctv {
  id: string;
  name: string;
  ip_address: string;
  rtsp_url: string;
  room_id: string;
  username?: string;
  // Add other CCTV properties as needed
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

interface StreamData {
  stream_url: string;
  // Add other stream properties as needed
}

interface Contact {
  id: string;
  email?: string;
  phone?: string;
  address?: string;
  instagram?: string;
}

// Specific API methods with proper typing and error handling
export const getStats = async (): Promise<Stats> => {
  try {
    const response = await api<Stats>('/stats');
    return response;
  } catch (error) {
    console.error('Error fetching stats:', error);
    // Return default values to prevent app crash
    return {
      total_buildings: 0,
      total_rooms: 0,
      total_cctvs: 0
    };
  }
};

export const getBuildings = async (): Promise<Building[]> => {
  try {
    const response = await api<Building[]>('/buildings');
    return response;
  } catch (error) {
    console.error('Error fetching buildings:', error);
    // Return empty array to prevent app crash
    return [];
  }
};

export const getBuilding = async (id: string): Promise<Building> => {
  try {
    const response = await api<Building>(`/buildings/${id}`);
    return response;
  } catch (error) {
    console.error(`Error fetching building ${id}:`, error);
    // Return empty object to prevent app crash
    return {} as Building;
  }
};

export const getRooms = async (): Promise<Room[]> => {
  try {
    const response = await api<Room[]>('/rooms');
    return response;
  } catch (error) {
    console.error('Error fetching rooms:', error);
    // Return empty array to prevent app crash
    return [];
  }
};

export const getRoom = async (id: string): Promise<Room> => {
  try {
    const response = await api<Room>(`/rooms/${id}`);
    return response;
  } catch (error) {
    console.error(`Error fetching room ${id}:`, error);
    // Return empty object to prevent app crash
    return {} as Room;
  }
};

export const getRoomsByBuilding = async (buildingId: string): Promise<Room[]> => {
  try {
    const response = await api<Room[]>(`/rooms/building/${buildingId}`);
    return response;
  } catch (error) {
    console.error(`Error fetching rooms for building ${buildingId}:`, error);
    // Return empty array to prevent app crash
    return [];
  }
};

export const getCctvs = async (): Promise<Cctv[]> => {
  try {
    const response = await api<Cctv[]>('/cctvs');
    return response;
  } catch (error) {
    console.error('Error fetching CCTVs:', error);
    // Return empty array to prevent app crash
    return [];
  }
};

export const getCctvsByRoom = async (roomId: string): Promise<Cctv[]> => {
  try {
    const response = await api<Cctv[]>(`/cctvs/room/${roomId}`);
    return response;
  } catch (error) {
    console.error(`Error fetching CCTVs for room ${roomId}:`, error);
    // Return empty array to prevent app crash
    return [];
  }
};

export const getCctvStreamUrl = async (cctvId: string): Promise<StreamData> => {
  // Retry a few times because the streaming server may take a moment to warm up
  const attempts = [12000, 15000, 20000]; // timeouts per attempt
  for (let i = 0; i < attempts.length; i++) {
    try {
      const response = await api<StreamData>(`/cctvs/stream/${cctvId}`, {}, attempts[i]);
      return response;
    } catch (error) {
      if (i === attempts.length - 1) {
        console.error(`Error fetching stream URL for CCTV ${cctvId}:`, error);
      } else {
        // Small delay before retry
        await new Promise((res) => setTimeout(res, 750));
      }
    }
  }
  // Return empty object to prevent app crash
  return { stream_url: '' };
};

export const getProductionTrends = async (startDate?: string, endDate?: string): Promise<ProductionTrend[]> => {
  try {
    // Build query string with optional parameters
    let url = '/production-trends';
    const queryParams = new URLSearchParams();
    
    if (startDate) {
      queryParams.append('start_date', startDate);
    }
    
    if (endDate) {
      queryParams.append('end_date', endDate);
    }
    
    if (queryParams.toString()) {
      url += `?${queryParams.toString()}`;
    }
    
    const response = await api<ProductionTrend[]>(url);
    return response;
  } catch (error) {
    console.error('Error fetching production trends:', error);
    // Return empty array to prevent app crash
    return [];
  }
};

export const getUnitPerformance = async (): Promise<UnitPerformance[]> => {
  try {
    const response = await api<UnitPerformance[]>('/unit-performance');
    return response;
  } catch (error) {
    console.error('Error fetching unit performance:', error);
    // Return empty array to prevent app crash
    return [];
  }
};

export const getContacts = async (): Promise<Contact[]> => {
  try {
    const response = await api<Contact | null>('/contact');
    // Convert single contact object to array
    return response ? [response] : [];
  } catch (error) {
    console.error('Error fetching contacts:', error);
    // Return empty array to prevent app crash
    return [];
  }
};