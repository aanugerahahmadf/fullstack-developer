"use client"

import { useState, useEffect } from "react"
import dynamic from 'next/dynamic'
import Link from "next/link"
import { useParams } from "next/navigation" // Changed to useParams for App Router
import { getBuilding, getRoomsByBuilding } from '@/lib/api'

// Dynamically import lucide-react icons to avoid HMR issues with Turbopack
const Search = dynamic(() => import('lucide-react').then((mod) => mod.Search), { ssr: false })
const Building2 = dynamic(() => import('lucide-react').then((mod) => mod.Building2), { ssr: false })
const DoorOpen = dynamic(() => import('lucide-react').then((mod) => mod.DoorOpen), { ssr: false })
const ArrowLeft = dynamic(() => import('lucide-react').then((mod) => mod.ArrowLeft), { ssr: false })

export default function BuildingRoomsPage() {
  const params = useParams(); // Using useParams instead of useRouter for App Router
  const { buildingId } = params;
  const [building, setBuilding] = useState<any>(null);
  const [rooms, setRooms] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [apiStatus, setApiStatus] = useState('connected');
  const [systemStatus, setSystemStatus] = useState({status: 'active', message: 'All systems operational'});
  const [searchTerm, setSearchTerm] = useState("");
  
  // State for dynamically imported icons
  const [icons, setIcons] = useState({
    Search: null as any,
    Building2: null as any,
    Monitor: null as any,
    CheckCircle: null as any,
    AlertTriangle: null as any,
    XCircle: null as any,
    ArrowLeft: null as any,
    DoorOpen: null as any,
  });

  // Load icons dynamically
  useEffect(() => {
    const loadIcons = async () => {
      const { Search, Building2, Monitor, CheckCircle, AlertTriangle, XCircle, ArrowLeft, DoorOpen } = await import('lucide-react');
      setIcons({ Search, Building2, Monitor, CheckCircle, AlertTriangle, XCircle, ArrowLeft, DoorOpen });
    };
    
    loadIcons();
  }, []);

  // Load data only once on component mount - no automatic refreshing
  useEffect(() => {
    const fetchBuildingAndRooms = async () => {
      try {
        // Fetch building details
        if (typeof buildingId === 'string') {
          const buildingData = await getBuilding(buildingId)
          setBuilding(buildingData)
          
          // Fetch rooms for this building
          const roomsData = await getRoomsByBuilding(buildingId)
          setRooms(roomsData)
        }
      } catch (error) {
        console.error('Failed to fetch building or rooms:', error)
      } finally {
        setLoading(false)
      }
    }

    if (buildingId) {
      // Use a small delay to ensure UI is ready before fetching data
      const timer = setTimeout(() => {
        fetchBuildingAndRooms()
      }, 50);
      
      return () => clearTimeout(timer);
    }
  }, [buildingId]) // Only re-fetch when buildingId changes (which shouldn't happen)

  const filteredRooms = rooms.filter((r: any) => 
    r.name && r.name.toLowerCase().includes(searchTerm.toLowerCase())
  )

  return (
    // Fixed background gradient to ensure full width and proper height
    <main className="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 py-8 min-h-screen w-full">
      {/* Header - responsive design */}
      <div className="pt-4 pb-6 px-4">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <Link href="/playlist" className="text-blue-300 hover:text-white transition p-2">
            {icons.ArrowLeft && <icons.ArrowLeft size={20} className="md:w-6 md:h-6" />}
          </Link>
          <h1 className="text-2xl md:text-3xl font-semibold text-white truncate text-center flex-grow mx-4">
            {building ? building.name : 'Building Rooms'}
          </h1>
          <div className="w-8 md:w-6"></div> {/* Spacer to balance the layout */}
        </div>
      </div>

      {/* Search - responsive design */}
      <div className="max-w-7xl mx-auto px-4 pb-6">
        <div className="relative">
          {icons.Search && <icons.Search className="absolute left-3 top-3 text-white" size={20} />}
          <input
            type="text"
            placeholder="Search room..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2 md:py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl text-white placeholder-white/50 font-semibold focus:outline-none focus:border-white/40 transition"
          />
        </div>
      </div>

      {/* Rooms Grid - responsive layout */}
      <div className="max-w-7xl mx-auto px-4 pb-8">
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mb-3"></div>
            <p className="text-white font-semibold">Loading rooms...</p>
          </div>
        ) : filteredRooms.length === 0 ? (
          <div className="text-center py-12">
            {icons.DoorOpen && <icons.DoorOpen className="w-12 h-12 text-white mx-auto mb-4" />}
            <p className="text-white font-semibold">No rooms available</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            {filteredRooms.map((room) => (
              <div
                key={room.id}
                className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 md:p-6 hover:bg-white/15 transition-all duration-300"
              >
                <div className="flex items-center gap-3 mb-4">
                  {icons.DoorOpen && <icons.DoorOpen className="w-5 h-5 md:w-6 md:h-6 text-green-400" />}
                  <h3 className="text-lg md:text-xl font-semibold text-white truncate">{room.name}</h3>
                </div>
                
                <div className="flex items-center justify-between mt-4 pt-3 border-t border-white/10">
                  <span className="text-sm text-white">
                    {room.cctvs ? room.cctvs.length : 0} CCTV
                  </span>
                  <Link href={`/playlist/${buildingId}/${room.id}`}>
                    <button className="text-xs md:text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition">
                      View CCTV
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