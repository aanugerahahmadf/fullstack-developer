// API configuration for the frontend to communicate directly with the backend
const API_BASE_URL = 'http://127.0.0.1:8000/api';

// In-memory cache for ultra-fast responses (no buffering)
const cache = new Map<string, { data: any; timestamp: number; ttl: number }>();
const CACHE_TTL = 500; // 500ms cache for maximum speed

// Request deduplication to prevent duplicate API calls
const pendingRequests = new Map<string, Promise<any>>();

// Define response wrapper interface
interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

// Helper function to make API requests with improved error handling and caching
export async function api<T>(endpoint: string, options: RequestInit = {}, timeoutMs: number = 5000): Promise<T> {
  const url = `${API_BASE_URL}${endpoint}`;
  const cacheKey = `${url}${JSON.stringify(options)}`;
  
  // Check cache first (ultra-fast response)
  const cached = cache.get(cacheKey);
  if (cached && Date.now() - cached.timestamp < cached.ttl) {
    return cached.data as T;
  }
  
  // Check if request is already pending (deduplication)
  if (pendingRequests.has(cacheKey)) {
    return pendingRequests.get(cacheKey) as Promise<T>;
  }
  
  const config: RequestInit = {
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json',
      'Cache-Control': 'no-cache', // Disable browser caching, use our cache
      'X-Requested-With': 'XMLHttpRequest',
      ...options.headers,
    },
    // Shorter timeout for faster failure
    signal: options.signal ?? AbortSignal.timeout(timeoutMs),
    ...options,
  };
  
  // Create request promise
  const requestPromise = (async () => {

    try {
      const response = await fetch(url, config);
      
      if (!response.ok) {
        let errorMessage = `API request failed: ${response.status} ${response.statusText}`;
        try {
          const errorData = await response.text();
          if (errorData) {
            const parsed = JSON.parse(errorData);
            errorMessage = parsed.message || parsed.error || errorMessage;
          }
        } catch (e) {
          // If parsing fails, use default message
        }
        throw new Error(errorMessage);
      }
      
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        throw new Error('API response is not JSON');
      }
      
      const result: ApiResponse<T> = await response.json();
      
      if (!result || typeof result !== 'object') {
        throw new Error('Invalid API response format');
      }
      
      if (result.success === false) {
        throw new Error(result.message || 'API request failed');
      }
      
      // Cache the result for ultra-fast subsequent requests (no buffering)
      cache.set(cacheKey, {
        data: result.data,
        timestamp: Date.now(),
        ttl: CACHE_TTL
      });
      
      return result.data;
    } catch (error) {
      // Provide more detailed error information
      if (error instanceof Error) {
        if (error.name === 'AbortError' || error.message.includes('timeout')) {
          throw new Error('API request timeout: The request took too long to complete. Please check if the backend server is running on http://127.0.0.1:8000');
        }
        if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError') || error.message.includes('ERR_CONNECTION_REFUSED')) {
          throw new Error('Cannot connect to backend server. Please ensure the Laravel backend is running on http://127.0.0.1:8000');
        }
        throw error;
      }
      throw new Error('Unknown API request error');
    } finally {
      // Remove from pending requests
      pendingRequests.delete(cacheKey);
    }
  })();
  
  // Store pending request for deduplication
  pendingRequests.set(cacheKey, requestPromise);
  
  return requestPromise;
}

// Clear cache function (useful for manual cache invalidation)
export function clearApiCache() {
  cache.clear();
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
    let url = '/chart/production-trends';
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
    const response = await api<UnitPerformance[]>('/chart/unit-performance');
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