"use client"

import { useState, useEffect, useRef } from "react"
import dynamic from 'next/dynamic'
import Link from "next/link"
import { useParams } from "next/navigation"
import { getRoom, getCctvsByRoom, getCctvStreamUrl } from '@/lib/api'

// Dynamically import lucide-react icons to avoid HMR issues with Turbopack
const Search = dynamic(() => import('lucide-react').then((mod) => mod.Search), { ssr: false })
const Video = dynamic(() => import('lucide-react').then((mod) => mod.Video), { ssr: false })
const ArrowLeft = dynamic(() => import('lucide-react').then((mod) => mod.ArrowLeft), { ssr: false })
const Play = dynamic(() => import('lucide-react').then((mod) => mod.Play), { ssr: false })
const ExternalLink = dynamic(() => import('lucide-react').then((mod) => mod.ExternalLink), { ssr: false })

// Add metadata for the page

export default function RoomCctvsPage() {
  const params = useParams();
  const { buildingId, roomId } = params as { buildingId?: string; roomId?: string };
  const [room, setRoom] = useState<any>(null);
  const [cctvs, setCctvs] = useState<any[]>([]);
  const [selectedCctv, setSelectedCctv] = useState<any>(null);
  const [streamData, setStreamData] = useState<any>(null);
  const [showLiveStream, setShowLiveStream] = useState(false);
  const [loading, setLoading] = useState(true);
  const [apiStatus, setApiStatus] = useState('connected');
  const [systemStatus, setSystemStatus] = useState({status: 'active', message: 'All systems operational'});
  const [searchTerm, setSearchTerm] = useState("");
  const videoPlayerRef = useRef<HTMLDivElement>(null);
  const videoRef = useRef<HTMLVideoElement | null>(null);
  
  // State for dynamically imported icons
  const [icons, setIcons] = useState({
    ArrowLeft: null as any,
    Search: null as any,
    Monitor: null as any,
    Play: null as any,
    CheckCircle: null as any,
    AlertTriangle: null as any,
    XCircle: null as any,
    Video: null as any,
  });

  // Load icons dynamically
  useEffect(() => {
    const loadIcons = async () => {
      const { ArrowLeft, Search, Monitor, Play, CheckCircle, AlertTriangle, XCircle, Video } = await import('lucide-react');
      setIcons({ ArrowLeft, Search, Monitor, Play, CheckCircle, AlertTriangle, XCircle, Video });
    };
    
    loadIcons();
  }, []);

  // Load data only once on component mount - no automatic refreshing
  useEffect(() => {
    const fetchRoomAndCctvs = async () => {
      try {
        // Fetch room details
        if (roomId && typeof roomId === 'string') {
          const roomData = await getRoom(roomId)
          setRoom(roomData)
        }
        
        // Fetch CCTVs for this room
        if (roomId && typeof roomId === 'string') {
          const cctvsData = await getCctvsByRoom(roomId)
          setCctvs(cctvsData)
        }
      } catch (error) {
        console.error('Failed to fetch room or CCTVs:', error)
      } finally {
        setLoading(false)
      }
    }

    // Only fetch if we have a valid roomId
    if (roomId && typeof roomId === 'string') {
      // Use a small delay to ensure UI is ready before fetching data
      const timer = setTimeout(() => {
        fetchRoomAndCctvs()
      }, 50);
      
      return () => clearTimeout(timer);
    } else {
      // If no roomId, stop loading
      setLoading(false)
    }
  }, [roomId]) // Only re-fetch when roomId changes (which shouldn't happen)

  const filteredCctvs = cctvs.filter((c) => 
    c.name.toLowerCase().includes(searchTerm.toLowerCase())
  )

  const handleLiveStream = async (cctv: any) => {
    try {
      setSelectedCctv(cctv)
      setShowLiveStream(true)
      setStreamData(null) // Reset stream data before fetching new one
      
      // Fetch stream URL
      const streamData = await getCctvStreamUrl(cctv.id)
      console.log('Stream URL fetched:', streamData)
      
      // Validate stream data
      if (streamData && streamData.stream_url) {
        setStreamData(streamData)
      } else {
        console.error('Invalid stream data received:', streamData)
        setStreamData({ stream_url: null }) // Set to null object to show error state
      }
    } catch (error) {
      console.error('Failed to fetch stream URL:', error)
      setStreamData({ stream_url: null }) // Set to null object to show error state
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
        setStreamData(null) // Reset stream data when closing modal
      }
    }
    window.addEventListener('keydown', handleEsc)
    return () => window.removeEventListener('keydown', handleEsc)
  }, [])

  // Initialize HLS playback when streamData changes
  useEffect(() => {
    if (!showLiveStream || !streamData?.stream_url) return
    const url = streamData.stream_url as string
    const video = videoRef.current
    if (!video) return

    if (video.canPlayType('application/vnd.apple.mpegurl')) {
      video.src = url
      video.play().catch(() => {})
      return
    }

    let hls: any
    ;(async () => {
      const mod = await import('hls.js')
      const Hls = mod.default
      if (Hls.isSupported()) {
        hls = new Hls({ 
          maxBufferLength: 15, // Balanced connection between latency and stability
          maxMaxBufferLength: 30,
          enableWorker: true,
          liveSyncDurationCount: 3, // Start 3 segments behind live edge (approx 6s latency)
          manifestLoadingTimeOut: 15000,
        })
        
        // Add error recovery
        hls.on(Hls.Events.ERROR, function(event: any, data: any) {
          if (data.fatal) {
            switch (data.type) {
              case Hls.ErrorTypes.NETWORK_ERROR:
                console.log('fatal network error encountered, try to recover');
                hls.startLoad();
                break;
              case Hls.ErrorTypes.MEDIA_ERROR:
                console.log('fatal media error encountered, try to recover');
                hls.recoverMediaError();
                break;
              default:
                hls.destroy();
                break;
            }
          }
        });

        hls.loadSource(url)
        hls.attachMedia(video)
      } else {
        video.src = url
      }
    })()

    return () => {
      if (hls) {
        try { hls.destroy() } catch {}
      }
    }
  }, [showLiveStream, streamData])

  return (
    // Fixed background gradient to ensure full width and proper height
    <main className="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 py-8 min-h-screen w-full">
      {/* Header - responsive design */}
      <div className="pt-4 pb-6 px-4">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <Link href={`/playlist/${buildingId}`} className="text-blue-300 hover:text-white transition p-2">
            {icons.ArrowLeft && <icons.ArrowLeft size={20} className="md:w-6 md:h-6" />}
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
          {icons.Search && <icons.Search className="absolute left-3 top-3 text-white" size={20} />}
          <input
            key="search-input"
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
                  {icons.Video && <icons.Video className="w-5 h-5 md:w-6 md:h-6 text-red-400" />}
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
                    {icons.Play && <icons.Play size={16} />}
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
                className="text-white/70 hover:text-white transition p-1"
                aria-label="Close"
              >
                <span className="text-2xl font-light">×</span>
              </button>
            </div>

            {/* Video Player - responsive aspect ratio */}
            <div ref={videoPlayerRef} className="aspect-video bg-black/50 flex items-center justify-center flex-grow relative">
              {streamData && streamData.stream_url ? (
                <video 
                  ref={videoRef}
                  autoPlay 
                  controls 
                  className="w-full h-full object-contain"
                  onError={() => {
                    console.warn('Video playback error')
                  }}
                >
                  Your browser does not support the video tag.
                </video>
              ) : (
                <div className="text-center p-4">
                  {streamData === null ? (
                    <>
                      {icons.Video && <icons.Video className="w-8 h-8 text-white/30 mx-auto mb-3" />}
                      <p className="text-white/50 font-semibold">Live stream player</p>
                      <div className="mt-4 text-xs text-white/40">
                        <p className="truncate">{selectedCctv.ip_address}</p>
                        <p>{selectedCctv.username}</p>
                      </div>
                    </>
                  ) : streamData && streamData.stream_url === null ? (
                    <>
                      {icons.Video && <icons.Video className="w-8 h-8 text-white/30 mx-auto mb-3" />}
                      <p className="text-white font-semibold">Stream unavailable</p>
                      <p className="text-white/50 text-sm mt-2">Please check the camera connection</p>
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
    </main>
  )
}