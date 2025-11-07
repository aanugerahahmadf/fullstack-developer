"use client"

import { useState, useEffect, useRef } from "react"
import { Search, Video, ArrowLeft, Play, ExternalLink } from "lucide-react"
import Link from "next/link"
import { useParams } from "next/navigation"
import { api } from '@/lib/api'

// Add metadata for the page

export default function PlaylistCctvPage() {
  const params = useParams()
  const buildingId = params.buildingId as string
  const roomId = params.roomId as string
  const [searchTerm, setSearchTerm] = useState("")
  const [cctvs, setCctvs] = useState<any[]>([])
  const [room, setRoom] = useState<any>(null)
  const [selectedCctv, setSelectedCctv] = useState<any>(null)
  const [showLiveStream, setShowLiveStream] = useState(false)
  const [loading, setLoading] = useState(true)
  const videoPlayerRef = useRef<HTMLDivElement>(null)

  // Load data only once on component mount - no automatic refreshing
  useEffect(() => {
    const fetchRoomAndCctvs = async () => {
      try {
        // Fetch room details
        const roomData = await api.getRoom(roomId)
        setRoom(roomData)
        
        // Fetch CCTVs for this room
        const cctvsData = await api.getCctvsByRoom(roomId)
        setCctvs(cctvsData)
      } catch (error) {
        console.error('Failed to fetch room or CCTVs:', error)
      } finally {
        setLoading(false)
      }
    }

    if (roomId) {
      // Use a small delay to ensure UI is ready before fetching data
      const timer = setTimeout(() => {
        fetchRoomAndCctvs()
      }, 50);
      
      return () => clearTimeout(timer);
    }
  }, [roomId]) // Only re-fetch when roomId changes (which shouldn't happen)

  const filteredCctvs = cctvs.filter((c) => 
    c.name.toLowerCase().includes(searchTerm.toLowerCase())
  )

  const handleLiveStream = async (cctv: any) => {
    try {
      setSelectedCctv(cctv)
      setShowLiveStream(true)
      // Fetch stream URL
      const streamData = await api.getCctvStreamUrl(cctv.id)
      console.log('Stream URL:', streamData)
      // In a real implementation, you would use this URL to play the stream
    } catch (error) {
      console.error('Failed to fetch stream URL:', error)
    }
  }

  const handleFullscreen = () => {
    if (!videoPlayerRef.current) return;
    
    const element = videoPlayerRef.current;
    
    if (element.requestFullscreen) {
      element.requestFullscreen();
    } else if ((element as any).mozRequestFullScreen) { /* Firefox */
      (element as any).mozRequestFullScreen();
    } else if ((element as any).webkitRequestFullscreen) { /* Chrome, Safari & Opera */
      (element as any).webkitRequestFullscreen();
    } else if ((element as any).msRequestFullscreen) { /* IE/Edge */
      (element as any).msRequestFullscreen();
    }
  };

  // Close modal on Escape key press
  useEffect(() => {
    const handleEsc = (event: KeyboardEvent) => {
      if (event.key === 'Escape') {
        setShowLiveStream(false)
      }
    }
    window.addEventListener('keydown', handleEsc)
    return () => window.removeEventListener('keydown', handleEsc)
  }, [])

  return (
    <main className="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 py-8 min-h-[calc(100vh-140px)]">
      {/* Header - responsive design */}
      <div className="pt-4 pb-6 px-4">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <Link href={`/playlist/${buildingId}`} className="text-blue-300 hover:text-white transition p-2">
            <ArrowLeft size={20} className="md:w-6 md:h-6" />
          </Link>
          <h1 className="text-2xl md:text-3xl font-semibold text-white truncate text-center flex-grow mx-4">
            {room ? room.name : 'Playlist'}
          </h1>
          <div className="w-8 md:w-6"></div> {/* Spacer to balance the layout */}
        </div>
      </div>

      {/* Search - responsive design */}
      <div className="max-w-7xl mx-auto px-4 pb-6">
        <div className="relative">
          <Search className="absolute left-3 top-3 text-white" size={20} />
          <input
            type="text"
            placeholder="Search CCTV..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2 md:py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl text-white placeholder-white/50 font-semibold focus:outline-none focus:border-white/40 transition"
          />
        </div>
      </div>

      {/* CCTV Grid - responsive layout */}
      <div className="max-w-7xl mx-auto px-4 pb-8">
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mb-3"></div>
            <p className="text-white font-semibold">Loading CCTV cameras...</p>
          </div>
        ) : filteredCctvs.length === 0 ? (
          <div className="text-center py-12">
            <Video className="w-12 h-12 text-white mx-auto mb-4" />
            <p className="text-white font-semibold">No CCTV cameras available</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            {filteredCctvs.map((cctv) => (
              <div
                key={cctv.id}
                className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 md:p-6 hover:bg-white/15 transition-all duration-300"
              >
                <div className="flex items-center gap-3 mb-4">
                  <Video className="w-5 h-5 md:w-6 md:h-6 text-red-400" />
                  <h3 className="text-lg md:text-xl font-semibold text-white truncate">{cctv.name}</h3>
                </div>
                
                {/* CCTV Details - responsive design */}
                <div className="space-y-2 mb-4 text-sm">
                  <div className="flex items-center gap-2">
                    <span className="text-white font-mono text-xs md:text-sm truncate">{cctv.ip_address}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <span className="text-white font-mono text-xs md:text-sm truncate">{cctv.username}</span>
                  </div>
                </div>
                
                <div className="flex gap-2">
                  <button
                    onClick={() => handleLiveStream(cctv)}
                    className="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                  >
                    <Play size={16} />
                    <span className="text-sm">LIVE STREAM</span>
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Live Stream Modal - responsive design */}
      {showLiveStream && selectedCctv && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
          <div className="bg-blue-950 border border-white/20 rounded-xl w-full max-w-3xl flex flex-col">
            {/* Header */}
            <div className="flex items-center justify-between p-4 md:p-6 border-b border-white/10">
              <h2 className="text-lg md:text-xl font-semibold text-white flex items-center gap-2">
                <span className="truncate max-w-[150px] sm:max-w-xs md:max-w-md">{selectedCctv.name}</span>
              </h2>
              <button 
                onClick={() => setShowLiveStream(false)} 
                className="text-white hover:text-white transition p-1"
                aria-label="Close"
              >
                <span className="text-2xl font-light">×</span>
              </button>
            </div>

            {/* Video Player - responsive aspect ratio */}
            <div ref={videoPlayerRef} className="aspect-video bg-black/50 flex items-center justify-center flex-grow">
              <div className="text-center p-4">
                <Video className="w-8 h-8 text-white mx-auto mb-3" />
                <p className="text-white font-semibold">Live stream player</p>
                <div className="mt-4 text-xs text-white">
                  <p className="truncate">{selectedCctv.ip_address}</p>
                  <p>{selectedCctv.username}</p>
                </div>
              </div>
            </div>
            
            {/* Stream Controls - responsive design */}
            <div className="p-4 border-t border-white/10">
              <div className="flex gap-2">
                <button 
                  onClick={handleFullscreen}
                  className="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm"
                >
                  Full Screen
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </main>
  )
}