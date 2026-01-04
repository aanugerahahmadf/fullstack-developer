"use client"

import React, { useState, useEffect, useRef, useCallback } from "react"
import dynamic from "next/dynamic"
import { 
  getBuildings, 
  getRoomsByBuilding, 
  getCctvStreamUrl, 
  getCctvsByRoom
} from '@/lib/api'
import { handleApiError } from '@/lib/enhanced-utils'
import { ErrorBoundary } from '@/components/error-boundary'

// Dynamically import lucide-react icons to avoid HMR issues with Turbopack
const X = dynamic(() => import('lucide-react').then((mod) => mod.X), { ssr: false })
const Video = dynamic(() => import('lucide-react').then((mod) => mod.Video), { ssr: false })

// Dynamically import leaflet components for better performance
const MapContainer = dynamic(
  () => import("react-leaflet").then((mod) => mod.MapContainer),
  { 
    ssr: false, 
    loading: () => (
      <div className="w-full h-full bg-gradient-to-br from-blue-950/30 to-slate-900/30 flex items-center justify-center">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mb-3"></div>
          <p className="text-white font-semibold">Loading Maps...</p>
        </div>
      </div>
    )
  }
)

const TileLayer = dynamic(
  () => import("react-leaflet").then((mod) => mod.TileLayer),
  { ssr: false }
)

const Marker = dynamic(
  () => import("react-leaflet").then((mod) => mod.Marker),
  { ssr: false }
)

const Popup = dynamic(
  () => import("react-leaflet").then((mod) => mod.Popup),
  { ssr: false }
)

function MapsPage() {
  const [buildings, setBuildings] = useState<any[]>([])
  const [rooms, setRooms] = useState<any[]>([])
  const [cctvs, setCctvs] = useState<any[]>([])
  const [selectedBuilding, setSelectedBuilding] = useState<any>(null)
  const [selectedCctv, setSelectedCctv] = useState<any>(null)
  const [streamData, setStreamData] = useState<any>(null)
  const [showLiveStream, setShowLiveStream] = useState(false)
  const [showRoomsModal, setShowRoomsModal] = useState(false)
  const [loading, setLoading] = useState(true)
  const [mounted, setMounted] = useState(false)
  const [leafletReady, setLeafletReady] = useState(false)
  
  const mapRef = useRef<any>(null)
  const leafletRef = useRef<any>(null)
  const videoRef = useRef<HTMLVideoElement | null>(null)
  
  // Load leaflet dynamically only on client side
  useEffect(() => {
    setMounted(true)
    
    const loadLeaflet = async () => {
      try {
        const leafletModule = await import('leaflet')
        leafletRef.current = leafletModule
        
        // Fix for default marker icons in Leaflet
        // @ts-ignore
        delete leafletModule.Icon.Default.prototype._getIconUrl
        // @ts-ignore
        leafletModule.Icon.Default.mergeOptions({
          iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
          iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
          shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
        })
        
        setLeafletReady(true)
      } catch (error) {
        console.error('Failed to load Leaflet:', error)
      }
    }
    
    loadLeaflet()
  }, [])
  
  // Initialize HLS playback for stream URLs
  useEffect(() => {
    if (!showLiveStream || !streamData?.stream_url) return
    const url = streamData.stream_url as string
    const video = videoRef.current
    if (!video) return

    // If native HLS is supported (Safari)
    if (video.canPlayType('application/vnd.apple.mpegurl')) {
      video.src = url
      video.play().catch(() => { /* no-op */ })
      return
    }

    // Use HLS.js for other browsers
    let hls: any
    ;(async () => {
      const mod = await import('hls.js')
      const Hls = mod.default
      if (Hls.isSupported()) {
        hls = new Hls({ maxBufferLength: 10 })
        hls.loadSource(url)
        hls.attachMedia(video)
      } else {
        // Fallback: try direct src (may fail)
        video.src = url
      }
    })()

    return () => {
      if (hls) {
        try { hls.destroy() } catch { /* ignore */ }
      }
    }
  }, [showLiveStream, streamData])

  // Fetch buildings data
  useEffect(() => {
    if (!mounted) return
    
    const fetchBuildings = async () => {
      try {
        setLoading(true)
        const data = await getBuildings()
        
        // Ensure data is an array
        if (Array.isArray(data)) {
          // Filter out buildings without names or coordinates
          const validBuildings = data.filter((building: any) => 
            building.name && building.latitude && building.longitude
          )
          
          setBuildings(validBuildings)
        } else {
          setBuildings([])
        }
      } catch (error) {
        console.error('Failed to fetch buildings:', error)
        setBuildings([])
      } finally {
        setLoading(false)
      }
    }
    
    fetchBuildings()
  }, [mounted])
  
  const handleBuildingClick = async (building: any) => {
    try {
      // Fetch rooms for this building
      const buildingRooms = await getRoomsByBuilding(building.id)
 
      // Fetch CCTVs per room from backend so names/data match Filament
      const buildingRoomsWithCctvs = await Promise.all(
        buildingRooms.map(async (room: any) => {
          try {
            const roomCctvs = await getCctvsByRoom(String(room.id))
            return {
              ...room,
              cctvs: Array.isArray(roomCctvs) ? roomCctvs : []
            }
          } catch {
            return { ...room, cctvs: [] }
          }
        })
      )
      // Optional: keep flat cache
      setCctvs(buildingRoomsWithCctvs.flatMap((r: any) => r.cctvs || []))
      
      // Add rooms to the building object
      const buildingWithRooms = { 
        ...building, 
        rooms: buildingRoomsWithCctvs 
      };
      
      // Set selected building and show rooms modal
      setSelectedBuilding(buildingWithRooms);
      setShowRoomsModal(true);
    } catch (error) {
      console.error('Failed to process building data:', error);
      // Still set the building as selected even if room processing fails
      setSelectedBuilding({ ...building, rooms: [] });
      setShowRoomsModal(true);
    }
  };
  
  const handleLiveStream = useCallback(async (cctv: any) => {
    try {
      setSelectedCctv(cctv)
      setShowLiveStream(true)
      // Fetch stream URL
      const streamData = await getCctvStreamUrl(cctv.id)
      setStreamData(streamData)
    } catch (error) {
      console.error('Failed to fetch stream URL:', error)
    }
  }, [])
  
  // Close modal on Escape key press
  useEffect(() => {
    const handleEsc = (event: KeyboardEvent) => {
      if (event.key === 'Escape') {
        setShowLiveStream(false)
        setStreamData(null)
      }
    }
    window.addEventListener('keydown', handleEsc)
    return () => window.removeEventListener('keydown', handleEsc)
  }, [])
  
  // Memoized icon creation function - only for building markers now
  const createCustomIcon = useCallback(() => {
    if (!leafletRef.current || !leafletRef.current.Icon) {
      console.warn('Leaflet not loaded yet');
      return undefined;
    }
    
    try {
      // Simpler blue circle marker similar to reference
      const svgIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
          <circle cx="12" cy="12" r="10" fill="#3b82f6" stroke="#1d4ed8" stroke-width="2"/>
          <circle cx="12" cy="12" r="4" fill="#ffffff"/>
        </svg>
      `;
      
      return new leafletRef.current.Icon({
        iconUrl: `data:image/svg+xml;base64,${btoa(svgIcon)}`,
        iconSize: [24, 24],
        iconAnchor: [12, 12],
        popupAnchor: [0, -12]
      });
    } catch (error) {
      console.error('Error creating custom icon:', error);
      return undefined;
    }
  }, [leafletReady]);
  
  const handleFullscreen = () => {
    const element = document.querySelector('.live-stream-video-container')
    if (!element) return
    
    // Check if already in fullscreen
    if (document.fullscreenElement || (document as any).webkitFullscreenElement || (document as any).mozFullScreenElement || (document as any).msFullscreenElement) {
      // Exit fullscreen
      if (document.exitFullscreen) {
        document.exitFullscreen()
      } else if ((document as any).webkitExitFullscreen) { /* Safari */
        (document as any).webkitExitFullscreen()
      } else if ((document as any).msExitFullscreen) { /* IE11 */
        (document as any).msExitFullscreen()
      } else if ((document as any).mozCancelFullScreen) { /* Firefox */
        (document as any).mozCancelFullScreen()
      }
    } else {
      // Enter fullscreen
      if (element.requestFullscreen) {
        element.requestFullscreen()
      } else if ((element as any).webkitRequestFullscreen) { /* Safari */
        (element as any).webkitRequestFullscreen()
      } else if ((element as any).msRequestFullscreen) { /* IE11 */
        (element as any).msRequestFullscreen()
      } else if ((element as any).mozRequestFullScreen) { /* Firefox */
        (element as any).mozRequestFullScreen()
      }
    }
  }
  
  if (!mounted) {
    return (
      // Fixed background gradient to ensure full width and proper height
      <main className="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 py-8 min-h-screen w-full flex flex-col">
        <div className="pt-4 pb-6 px-4">
          <div className="flex justify-center items-center gap-4">
            <h1 className="text-3xl md:text-4xl font-semibold text-white text-center">Maps</h1>
          </div>
        </div>
        <div className="px-4 pb-6 flex-grow flex justify-center">
          <div className="h-[50vh] sm:h-[60vh] md:h-[70vh] w-full max-w-4xl rounded-2xl overflow-hidden shadow-2xl z-0">
            <div className="w-full h-full bg-gradient-to-br from-blue-950/30 to-slate-900/30 flex items-center justify-center">
              <div className="text-center">
                <div className="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mb-3"></div>
                <p className="text-white font-semibold">Loading Maps...</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    )
  }
  
  return (
    // Fixed background gradient to ensure full width and proper height
    <main className="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 py-8 min-h-screen w-full flex flex-col">
      {/* Header */}
      <div className="pt-4 pb-6 px-4">
        <div className="flex justify-center items-center gap-4">
          <h1 className="text-3xl md:text-4xl font-semibold text-white text-center">Maps</h1>
        </div>
      </div>

      {/* Back Button - only shown when rooms are displayed */}
      
      {/* Map Container - reduced width */}
      <div className="px-4 pb-6 flex-grow flex justify-center">
        <div className="h-[50vh] sm:h-[60vh] md:h-[70vh] w-full max-w-4xl rounded-2xl overflow-hidden shadow-2xl z-0">
          {!loading ? (
            <MapContainer 
              center={[-6.1751, 108.2146]} 
              zoom={13} 
              style={{ height: "100%", width: "100%" }}
              className="z-0"
              ref={mapRef}
            >
              <TileLayer
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
              />
              {/* Building Markers (Blue Icons) - Always shown */}
              {buildings.filter((b: any) => b.latitude && b.longitude).map((building: any) => {
                const customIcon = createCustomIcon();
                if (!customIcon) return null; // Skip if icon not ready
                
                return (
                <Marker 
                  key={`building-${building.id}`} 
                  position={[parseFloat(building.latitude), parseFloat(building.longitude)]}
                  icon={customIcon}
                  eventHandlers={{
                    click: () => handleBuildingClick(building)
                  }}
                >
                  <Popup className="!rounded-xl !border !border-white/30 !shadow-2xl !bg-blue-950/70 !backdrop-blur-md !text-white !p-0 !min-w-[200px]">
                    <div className="p-3">
                      <h3 className="font-black text-xl text-black mb-2 drop-shadow-md text-center">{building.name || 'Unnamed Building'}</h3>
                      <button
                        onClick={() => handleBuildingClick(building)}
                        className="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-semibold py-2 px-3 rounded-lg shadow-md transition-all duration-200 transform hover:scale-[1.02]"
                      >
                        View Room
                      </button>
                    </div>
                  </Popup>
                </Marker>
                );
              })}
              {/* Room Markers - Removed as per user request */}
            </MapContainer>
          ) : (
            <div className="w-full h-full bg-gradient-to-br from-blue-950/30 to-slate-900/30 flex items-center justify-center">
              <div className="text-center">
                <div className="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mb-3"></div>
                <p className="text-white font-semibold">Loading Maps...</p>
              </div>
            </div>
          )}
        </div>
      </div>
      
      {/* Live Stream Modal - responsive design */}
      {showLiveStream && selectedCctv && (
        <div className="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50 p-4">
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
            <div className="aspect-video bg-black/50 flex items-center justify-center flex-grow relative live-stream-video-container">
              {streamData && streamData.stream_url ? (
                <video
                  ref={videoRef}
                  autoPlay
                  controls
                  className="w-full h-full object-contain"
                  onError={() => {
                    // Hindari logging objek event untuk mencegah warning Next.js (sync dynamic APIs)
                    console.warn('Video playback error')
                  }}
                >
                  Your browser does not support the video tag.
                </video>
              ) : (
                <div className="text-center p-4">
                  {streamData === null ? (
                    <>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-8 h-8 text-white mx-auto mb-3">
                        <path strokeLinecap="round" strokeLinejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                      </svg>
                      <p className="text-white font-semibold">Live stream player</p>
                      <div className="mt-4 text-xs text-white">
                        <p className="truncate">{selectedCctv.ip_address}</p>
                        <p>{selectedCctv.username}</p>
                      </div>
                    </>
                  ) : (
                    <>
                      <div className="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mb-3"></div>
                      <p className="text-white font-semibold">Loading stream...</p>
                    </>
                  )}
                </div>
              )}
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
      
      {/* Rooms Modal */}
      {showRoomsModal && selectedBuilding && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
          <div className="bg-blue-950 border border-white/20 rounded-2xl w-full max-w-6xl max-h-[80vh] flex flex-col">
            {/* Header */}
            <div className="flex items-center justify-between p-4 md:p-6 border-b border-white/10">
              <h2 className="text-lg md:text-xl font-semibold text-white">
                Rooms in {selectedBuilding.name || 'Unnamed Building'}
              </h2>
              <button 
                onClick={() => {
                  setShowRoomsModal(false)
                  setSelectedBuilding(null)
                }} 
                className="text-white hover:text-white transition p-1"
                aria-label="Close"
              >
                <X className="w-5 h-5" />
              </button>
            </div>
            
            {/* Rooms List */}
            <div className="p-4 md:p-6 overflow-y-auto flex-grow">
              {selectedBuilding.rooms && Array.isArray(selectedBuilding.rooms) ? (
                selectedBuilding.rooms.length > 0 ? (
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {selectedBuilding.rooms.map((room: any) => (
                      <div key={`room-${room.id}`} className="bg-white/5 border border-white/10 rounded-lg p-4">
                        <h3 className="font-bold text-white mb-2">{room.name || 'Unnamed Room'}</h3>
                        <p className="text-sm text-white mb-3">
                          {room.cctvs && Array.isArray(room.cctvs) ? room.cctvs.length : 0} CCTV Camera
                        </p>
                        <div className="space-y-2">
                          {room.cctvs && Array.isArray(room.cctvs) && room.cctvs.length > 0 ? (
                            room.cctvs.map((cctv: any) => (
                              <button
                                key={`cctv-${cctv.id}`}
                                onClick={() => {
                                  handleLiveStream(cctv)
                                  setShowRoomsModal(false)
                                }}
                                className="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold py-2 px-3 rounded-lg flex items-center justify-center gap-2 shadow-md transition-all duration-200 transform hover:scale-[1.02]"
                              >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-4 h-4">
                                  <path strokeLinecap="round" strokeLinejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                <span className="truncate">{cctv.name || 'Unnamed CCTV'}</span>
                              </button>
                            ))
                          ) : (
                            <div className="w-full text-center text-white/80 text-sm py-2">
                              No CCTV found in this room
                            </div>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                ) : (
                  <div className="text-center py-8">
                    <p className="text-white">No rooms found for this building</p>
                  </div>
                )
              ) : (
                <div className="text-center py-8">
                  <p className="text-white">Error loading room data</p>
                </div>
              )}
            </div>
            
            {/* Footer with actions - Removed as per user request */}
          </div>
        </div>
      )}
    </main>
  )
}

export default MapsPage