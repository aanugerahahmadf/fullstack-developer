/**
 * Comprehensive JavaScript Functionality Summary
 * 
 * This file documents all JavaScript functionalities in the Pertamina Fullstack Application
 */

import { useState, useEffect } from 'react';

// 1. API Functions (lib/api.ts)
// - getStats(): Fetch statistics data
// - getBuildings(): Fetch all buildings
// - getBuilding(id): Fetch specific building
// - getRooms(): Fetch all rooms
// - getRoom(id): Fetch specific room
// - getRoomsByBuilding(buildingId): Fetch rooms for a specific building
// - getCctvs(): Fetch all CCTVs
// - getCctvsByRoom(roomId): Fetch CCTVs for a specific room
// - getCctvStreamUrl(cctvId): Fetch stream URL for a specific CCTV
// - getProductionTrends(startDate, endDate): Fetch production trends
// - getUnitPerformance(): Fetch unit performance data
// - getContacts(): Fetch contact information

// 2. Utility Functions (lib/utils.ts)
// - cn(...inputs): Class name merging utility

// 3. Home Page Functionality (app/page.tsx)
// - State management for stats, charts, loading states
// - Data fetching for all dashboard components
// - Dynamic icon loading
// - Chart rendering with Recharts
// - System status monitoring
// - Date range handling

// 4. Maps Page Functionality (app/maps/page.tsx)
// - Dynamic Leaflet map loading
// - Building marker rendering
// - Room modal display
// - Live stream handling
// - Fullscreen video support
// - Building selection and room viewing

// 5. Playlist Page Functionality (app/playlist/page.tsx)
// - Building listing with search
// - Building grouping by name
// - Room count calculation
// - Navigation to building-specific playlists

// 6. Contact Page Functionality (app/contact/page.tsx)
// - Contact information display
// - Contact method grouping
// - External link handling (email, phone, maps, social media)

// 7. Additional JavaScript Enhancements to Add

// Add these to the existing functionality:

// Enhanced error handling across all pages
export function handleApiError(error: any, context: string) {
  console.error(`API Error in ${context}:`, error);
  // Could add toast notifications or other error UI handling here
}

// Enhanced data validation
export function validateBuildingData(building: any): boolean {
  return building && 
         building.name && 
         building.latitude && 
         building.longitude &&
         !isNaN(parseFloat(building.latitude)) &&
         !isNaN(parseFloat(building.longitude));
}

export function validateRoomData(room: any): boolean {
  return room && 
         room.name && 
         room.building_id;
}

export function validateCctvData(cctv: any): boolean {
  return cctv && 
         cctv.name && 
         cctv.room_id;
}

// Enhanced date formatting utilities
export function formatDateString(dateString: string): string {
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      month: 'long', 
      day: 'numeric',
      year: 'numeric'
    });
  } catch (error) {
    console.error('Error formatting date:', error);
    return dateString;
  }
}

export function formatTimeAgo(dateString: string): string {
  try {
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60));
    
    if (diffInHours < 1) return 'Just now';
    if (diffInHours < 24) return `${diffInHours} hours ago`;
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) return `${diffInDays} days ago`;
    return formatDateString(dateString);
  } catch (error) {
    console.error('Error formatting time ago:', error);
    return 'Unknown time';
  }
}

// Enhanced search functionality
export function fuzzySearch(items: any[], searchTerm: string, keys: string[]): any[] {
  if (!searchTerm) return items;
  
  const term = searchTerm.toLowerCase();
  return items.filter(item => {
    try {
      return keys.some(key => {
        const value = item[key];
        return value && value.toString().toLowerCase().includes(term);
      });
    } catch (error) {
      console.error('Error in fuzzy search:', error);
      return false;
    }
  });
}

// Enhanced data caching
export class DataCache {
  private cache: Map<string, { data: any; timestamp: number }> = new Map();
  private ttl: number; // Time to live in milliseconds

  constructor(ttl: number = 5 * 60 * 1000) { // 5 minutes default
    this.ttl = ttl;
  }

  set(key: string, data: any): void {
    try {
      this.cache.set(key, {
        data,
        timestamp: Date.now()
      });
    } catch (error) {
      console.error('Error setting cache:', error);
    }
  }

  get(key: string): any {
    try {
      const item = this.cache.get(key);
      if (!item) return null;
      
      if (Date.now() - item.timestamp > this.ttl) {
        this.cache.delete(key);
        return null;
      }
      
      return item.data;
    } catch (error) {
      console.error('Error getting cache:', error);
      return null;
    }
  }

  clear(): void {
    try {
      this.cache.clear();
    } catch (error) {
      console.error('Error clearing cache:', error);
    }
  }
}

// Enhanced localStorage wrapper with error handling
export class SafeStorage {
  static setItem(key: string, value: string): boolean {
    try {
      localStorage.setItem(key, value);
      return true;
    } catch (error) {
      console.error('Failed to set item in localStorage:', error);
      return false;
    }
  }

  static getItem(key: string): string | null {
    try {
      return localStorage.getItem(key);
    } catch (error) {
      console.error('Failed to get item from localStorage:', error);
      return null;
    }
  }

  static removeItem(key: string): boolean {
    try {
      localStorage.removeItem(key);
      return true;
    } catch (error) {
      console.error('Failed to remove item from localStorage:', error);
      return false;
    }
  }
}

// Enhanced form validation utilities
export function validateEmail(email: string): boolean {
  try {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  } catch (error) {
    console.error('Error validating email:', error);
    return false;
  }
}

export function validatePhone(phone: string): boolean {
  try {
    const re = /^\+?[\d\s\-\(\)]{10,}$/;
    return re.test(phone);
  } catch (error) {
    console.error('Error validating phone:', error);
    return false;
  }
}

export function validateRequired(value: string): boolean {
  return value !== null && value !== undefined && value.trim().length > 0;
}

// Enhanced accessibility utilities
export function focusFirstFocusableElement(element: HTMLElement): void {
  try {
    const focusableElements = element.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    if (focusableElements.length > 0) {
      (focusableElements[0] as HTMLElement).focus();
    }
  } catch (error) {
    console.error('Error focusing first focusable element:', error);
  }
}

// Enhanced performance monitoring
export function measurePerformance<T>(fn: () => T, label: string): T {
  const start = performance.now();
  try {
    const result = fn();
    const end = performance.now();
    console.log(`${label} took ${end - start} milliseconds`);
    return result;
  } catch (error) {
    const end = performance.now();
    console.log(`${label} failed after ${end - start} milliseconds`);
    throw error;
  }
}

// Enhanced responsive utilities
export function useMediaQuery(query: string): boolean {
  const [matches, setMatches] = useState(false);

  useEffect(() => {
    let isMounted = true;
    
    try {
      const media = window.matchMedia(query);
      if (isMounted && media.matches !== matches) {
        setMatches(media.matches);
      }
      
      const listener = () => {
        if (isMounted) {
          setMatches(media.matches);
        }
      };
      
      media.addEventListener('change', listener);
      
      return () => {
        isMounted = false;
        media.removeEventListener('change', listener);
      };
    } catch (error) {
      console.error('Error in useMediaQuery:', error);
      return () => {};
    }
  }, [matches, query]);

  return matches;
}

export function useIsMobile(): boolean {
  return useMediaQuery('(max-width: 768px)');
}

export function useIsTablet(): boolean {
  return useMediaQuery('(min-width: 769px) and (max-width: 1024px)');
}

export function useIsDesktop(): boolean {
  return useMediaQuery('(min-width: 1025px)');
}