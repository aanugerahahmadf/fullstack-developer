// API configuration for the frontend to communicate directly with the backend
const API_BASE_URL = 'http://127.0.0.1:8000/api';

// Define response wrapper interface
interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

// Helper function to make API requests with improved error handling
export async function api<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
  const url = `${API_BASE_URL}${endpoint}`;
  
  const config: RequestInit = {
    credentials: 'include', // Include credentials for CORS requests
    headers: {
      'Content-Type': 'application/json',
      ...options.headers,
    },
    // Set timeout to 10 seconds as per project requirements
    signal: AbortSignal.timeout(10000),
    ...options,
  };

  try {
    const response = await fetch(url, config);
    
    if (!response.ok) {
      throw new Error(`API request failed: ${response.status} ${response.statusText}`);
    }
    
    const result: ApiResponse<T> = await response.json();
    return result.data;
  } catch (error) {
    console.error('API request error:', error);
    // Provide more detailed error information
    if (error instanceof Error) {
      if (error.name === 'AbortError') {
        throw new Error('API request timeout: The request took too long to complete');
      }
      // Re-throw the error to be handled by the calling function
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
  try {
    const response = await api<StreamData>(`/cctvs/stream/${cctvId}`);
    return response;
  } catch (error) {
    console.error(`Error fetching stream URL for CCTV ${cctvId}:`, error);
    // Return empty object to prevent app crash
    return { stream_url: '' };
  }
};

export const getProductionTrends = async (startDate: string, endDate: string): Promise<ProductionTrend[]> => {
  try {
    const response = await api<ProductionTrend[]>(`/chart/production-trends?start_date=${startDate}&end_date=${endDate}`);
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
    const response = await api<Contact[]>('/contact');
    return response;
  } catch (error) {
    console.error('Error fetching contacts:', error);
    // Return empty array to prevent app crash
    return [];
  }
};