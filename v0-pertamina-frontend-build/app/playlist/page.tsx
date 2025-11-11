"use client"

import { useState, useEffect } from "react"
import dynamic from 'next/dynamic'
import Link from "next/link"
import { getBuildings } from '@/lib/api'

// Dynamically import lucide-react icons to avoid HMR issues with Turbopack
const Search = dynamic(() => import('lucide-react').then((mod) => mod.Search), { ssr: false })
const Building2 = dynamic(() => import('lucide-react').then((mod) => mod.Building2), { ssr: false })

export default function PlaylistPage() {
  const [searchTerm, setSearchTerm] = useState("")
  const [buildings, setBuildings] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  // State for dynamically imported icons
  const [icons, setIcons] = useState({
    Search: null as any,
    Building2: null as any,
  });

  // Load icons dynamically with error handling
  useEffect(() => {
    let isMounted = true;
    
    const loadIcons = async () => {
      try {
        const { Search, Building2 } = await import('lucide-react');
        if (isMounted) {
          setIcons({ Search, Building2 });
        }
      } catch (error) {
        console.warn('Failed to load icons:', error);
        if (isMounted) {
          setIcons({
            Search: null,
            Building2: null,
          });
        }
      }
    };
    
    loadIcons();
    
    // Cleanup function to prevent state updates on unmounted component
    return () => {
      isMounted = false;
    };
  }, []);

  // Load data only once on component mount
  useEffect(() => {
    let isMounted = true;
    
    const fetchBuildings = async () => {
      try {
        const data = await getBuildings()
        // Ensure data is an array
        if (Array.isArray(data)) {
          if (isMounted) {
            setBuildings(data)
          }
        } else {
          if (isMounted) {
            setBuildings([])
          }
        }
      } catch (error) {
        console.error('Failed to fetch buildings:', error)
        if (isMounted) {
          setBuildings([])
        }
      } finally {
        if (isMounted) {
          setLoading(false)
        }
      }
    }

    // Use a small delay to ensure UI is ready before fetching data
    const timer = setTimeout(() => {
      fetchBuildings()
    }, 50);
    
    return () => {
      isMounted = false;
      clearTimeout(timer);
    };
  }, [])

  const filteredBuildings = buildings
    .filter((b: any) => b.name && b.name.toLowerCase().includes(searchTerm.toLowerCase()))
    .reduce((acc: any[], building: any) => {
      const existingBuilding = acc.find((b: any) => b.name === building.name);
      if (existingBuilding) {
        // If building already exists, merge rooms
        if (building.rooms && Array.isArray(building.rooms)) {
          existingBuilding.rooms = existingBuilding.rooms || [];
          existingBuilding.rooms = [...existingBuilding.rooms, ...building.rooms];
        }
        // Update room count with unique rooms only
        if (existingBuilding.rooms && Array.isArray(existingBuilding.rooms)) {
          const uniqueRooms = Array.from(
            new Map(existingBuilding.rooms.map((room: any) => [room.name, room])).values()
          );
          existingBuilding.roomCount = uniqueRooms.length;
          existingBuilding.rooms = uniqueRooms;
        }
        // Keep track of all building IDs with the same name
        existingBuilding.ids = [...existingBuilding.ids, building.id];
      } else {
        // If new building, add it with room count
        let uniqueRooms = building.rooms;
        if (building.rooms && Array.isArray(building.rooms)) {
          uniqueRooms = Array.from(
            new Map(building.rooms.map((room: any) => [room.name, room])).values()
          );
        }
        acc.push({
          ...building,
          rooms: uniqueRooms,
          roomCount: (uniqueRooms && uniqueRooms.length) || 0,
          ids: [building.id]
        });
      }
      return acc;
    }, [])

  return (
    // Fixed background gradient to match other pages and ensure full width
    <main className="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 py-8 min-h-screen w-full">
      {/* Header */}
      <div className="pt-4 pb-6 px-4">
        <div className="flex justify-center items-center gap-4">
          <h1 className="text-3xl md:text-4xl font-semibold text-white text-center">Playlist Building</h1>
        </div>
      </div>

      {/* Search - responsive design */}
      <div className="max-w-7xl mx-auto px-4 pb-6 w-full">
        <div className="relative">
          {icons.Search ? <icons.Search className="absolute left-3 top-3 text-white" size={20} /> : <div className="absolute left-3 top-3 text-white" style={{width: 20, height: 20}}></div>}
          <input
            key="search-input"
            type="text"
            placeholder="Search building..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2 md:py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl text-white placeholder-white/50 font-semibold focus:outline-none focus:border-white/40 transition"
            suppressHydrationWarning
          />
        </div>
      </div>

      {/* Buildings Grid - responsive layout */}
      <div className="max-w-7xl mx-auto px-4 pb-8 w-full">
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mb-3"></div>
            <p className="text-white font-semibold">Loading building...</p>
          </div>
        ) : filteredBuildings.length === 0 ? (
          <div className="text-center py-12">
            {icons.Building2 ? <icons.Building2 className="w-12 h-12 text-white mx-auto mb-4" /> : <div className="w-12 h-12 text-white mx-auto mb-4"></div>}
            <p className="text-white font-semibold">No building available</p>
            <p className="text-white text-sm mt-2">Building will appear once added in admin panel</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            {filteredBuildings.map((building) => (
              <div key={building.id} className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 md:p-6 hover:bg-white/15 transition-all duration-300">
                <div className="flex items-center gap-3 mb-4">
                  {icons.Building2 ? <icons.Building2 className="w-5 h-5 md:w-6 md:h-6 text-blue-400" /> : <div className="w-5 h-5 md:w-6 md:h-6 text-blue-400"></div>}
                  <h3 className="text-lg md:text-xl font-semibold text-white truncate">
                    {building.name || 'Unnamed Building'}
                  </h3>
                </div>
                
                <div className="flex items-center justify-between mt-4 pt-3 border-t border-white/10">
                  <span className="text-sm text-white">
                    {building.roomCount} Room
                  </span>
                  <Link href={`/playlist/${building.ids[0]}`}>
                    <button className="text-xs md:text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition">
                      View Room
                    </button>
                  </Link>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </main>
  )
}