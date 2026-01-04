"use client"

import { useState, useEffect, useCallback } from "react"
import { useRouter } from "next/navigation"
import { 
  LineChart, 
  Line, 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  Legend, 
  ResponsiveContainer 
} from 'recharts'
import { 
  getStats, 
  getBuildings, 
  getRooms, 
  getCctvs, 
  getProductionTrends, 
  getUnitPerformance 
} from '@/lib/api'
import { handleApiError, DataCache, formatDateString } from '@/lib/enhanced-utils'

// Define TypeScript interfaces
interface Stats {
  total_buildings: number
  total_rooms: number
  total_cctvs: number
}

interface Building {
  id: string
  name: string
  // Add other building properties as needed
}

interface Room {
  id: string
  name: string
  building_id: string
  // Add other room properties as needed
}

interface Cctv {
  id: string
  name: string
  ip_address: string
  rtsp_url: string
  room_id: string
  // Add other CCTV properties as needed
}

interface ProductionTrend {
  date: string
  production: number
  target: number
  traffic_volume?: number
  average_speed?: number
  incidents?: number
  congestion_index?: number
  signal_changes?: number
  green_wave_efficiency?: number
}

interface UnitPerformance {
  unit: string
  efficiency: number
  capacity: number
  traffic_density?: number
  signal_optimization?: number
  average_delay?: number
  queue_length?: number
  green_wave_ratio?: number
  incident_rate?: number
  adaptive_control?: number
}

interface DateRange {
  start: string
  end: string
}

// Add metadata for the page

export default function Home() {
  const router = useRouter()
  const [stats, setStats] = useState({
    total_buildings: 0,
    total_rooms: 0,
    total_cctvs: 0
  });
  const [productionTrends, setProductionTrends] = useState<ProductionTrend[]>([]);
  const [unitPerformance, setUnitPerformance] = useState<UnitPerformance[]>([]);
  const [loading, setLoading] = useState(true);
  const [chartLoading, setChartLoading] = useState(true);
  const [dateRange, setDateRange] = useState<{start: string, end: string}>(() => {
    const now = new Date();
    const start = new Date(now.getFullYear(), now.getMonth(), 1); // First day of current month
    const end = new Date(now.getFullYear(), now.getMonth() + 1, 0); // Last day of current month
    return {
      start: start.toISOString().split('T')[0],
      end: end.toISOString().split('T')[0]
    };
  });
  
  // State to track if we've initialized the date range based on actual data
  const [isDateRangeInitialized, setIsDateRangeInitialized] = useState(false);
  
  // State for dynamically imported icons
  const [icons, setIcons] = useState<any>({});
  
  // Load icons dynamically to avoid HMR issues with Turbopack
  useEffect(() => {
    let isMounted = true;
    
    const loadIcons = async () => {
      try {
        const lucide = await import('lucide-react');
        // Only update state if component is still mounted
        if (isMounted) {
          setIcons({
            Zap: lucide.Zap,
            BarChart3: lucide.BarChart3,
            Activity: lucide.Activity,
          });
        }
      } catch (error) {
        console.warn('Failed to load icons:', error);
        // Set empty icons object to prevent errors
        if (isMounted) {
          setIcons({});
        }
      }
    };
    
    loadIcons();
    
    // Cleanup function to prevent state updates on unmounted component
    return () => {
      isMounted = false;
    };
  }, []);

  // Helper function to format dates
  const formatDate = (dateString: string) => {
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric' });
    } catch (error) {
      console.error('Error formatting date:', error);
      return dateString;
    }
  };

  // Function to get date range for current month
  const getCurrentMonthDateRange = useCallback(() => {
    const now = new Date();
    const start = new Date(now.getFullYear(), now.getMonth(), 1);
    const end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    return {
      start: start.toISOString().split('T')[0],
      end: end.toISOString().split('T')[0]
    };
  }, []);

  // Enhanced load data function with system status checking
  const loadData = useCallback(async (overrideDateRange?: {start: string, end: string}) => {
    try {
      setLoading(true);
      setChartLoading(true);

      // Use provided date range or default to current state
      const effectiveDateRange = overrideDateRange || dateRange;

      // Fetch all data in parallel for maximum performance
      console.log('Fetching stats...');
      const statsData = await getStats();
      console.log('Stats data:', statsData);
      
      console.log('Fetching buildings...');
      const buildings: Building[] = await getBuildings();
      console.log('Buildings data:', buildings);
      
      console.log('Fetching rooms...');
      const rooms: Room[] = await getRooms();
      console.log('Rooms data:', rooms);
      
      console.log('Fetching CCTVs...');
      const cctvs: Cctv[] = await getCctvs();
      console.log('CCTVs data:', cctvs);
      
      console.log('Fetching production trends...');
      let productionData: ProductionTrend[] = [];
      try {
        productionData = await getProductionTrends(effectiveDateRange.start, effectiveDateRange.end);
        console.log('Production trends data:', productionData);
        
        // If we haven't initialized the date range yet, try to set it based on actual data
        if (!isDateRangeInitialized) {
          try {
            // Fetch all production trends to determine the appropriate date range
            const allProductionData = await getProductionTrends();
            if (allProductionData && allProductionData.length > 0) {
              // Find the earliest and latest dates in the data
              const dates = allProductionData.map(item => new Date(item.date));
              const minDate = new Date(Math.min(...dates as any));
              const maxDate = new Date(Math.max(...dates as any));
              
              // Set date range to cover the month of the most recent data
              const displayDate = maxDate;
              const newStart = new Date(displayDate.getFullYear(), displayDate.getMonth(), 1);
              const newEnd = new Date(displayDate.getFullYear(), displayDate.getMonth() + 1, 0);
              
              const newDateRange = {
                start: newStart.toISOString().split('T')[0],
                end: newEnd.toISOString().split('T')[0]
              };
              
              setDateRange(newDateRange);
              setIsDateRangeInitialized(true);
              
              // Re-fetch data with the new date range
              productionData = await getProductionTrends(newDateRange.start, newDateRange.end);
            } else {
              // If no data, mark as initialized to prevent infinite loop
              setIsDateRangeInitialized(true);
            }
          } catch (rangeError) {
            console.warn('Could not initialize date range from data:', rangeError);
            setIsDateRangeInitialized(true);
          }
        }
      } catch (error) {
        handleApiError(error, 'Production Trends');
        console.error('Failed to fetch production trends:', error);
        // Return empty array when API fails
        productionData = [];
      }
      
      console.log('Fetching unit performance...');
      let unitPerformanceData: UnitPerformance[] = [];
      try {
        unitPerformanceData = await getUnitPerformance();
        console.log('Unit performance data:', unitPerformanceData);
      } catch (error) {
        handleApiError(error, 'Unit Performance');
        console.error('Failed to fetch unit performance:', error);
        // Return empty array when API fails
        unitPerformanceData = [];
      }

      // Update stats from backend so it matches Filament v4 exactly
      setStats({
        total_buildings: statsData.total_buildings ?? buildings.length,
        total_rooms: statsData.total_rooms ?? rooms.length,
        total_cctvs: statsData.total_cctvs ?? cctvs.length,
      });
      setProductionTrends(productionData);
      setUnitPerformance(unitPerformanceData);
    } catch (error) {
      console.error('Failed to load dashboard data:', error);
      handleApiError(error, 'Dashboard');
      
      // Set default values on error
      setStats({
        total_buildings: 0,
        total_rooms: 0,
        total_cctvs: 0
      });
      
      setProductionTrends([]);
      setUnitPerformance([]);
    } finally {
      setLoading(false);
      // Use setTimeout to ensure state updates are processed
      setTimeout(() => {
        setChartLoading(false);
      }, 0);
    }
  }, [dateRange, isDateRangeInitialized]);

  // Load data immediately on component mount
  useEffect(() => {
    let isMounted = true;
    
    // Load data immediately without delay
    const loadDataWrapper = async () => {
      if (isMounted) {
        await loadData();
      }
    };
    
    loadDataWrapper();
    
    // Set up periodic refresh every 30 seconds
    const intervalId = setInterval(() => {
      if (isMounted) {
        loadData();
      }
    }, 30000);
    
    // Cleanup function to prevent state updates on unmounted component
    return () => {
      isMounted = false;
      clearInterval(intervalId);
    };
  }, []);
  
  // Trigger date range initialization on mount
  useEffect(() => {
    // This will trigger the date range initialization logic in loadData
    if (!isDateRangeInitialized) {
      loadData();
    }
  }, [isDateRangeInitialized, loadData]);

  // Handle date range changes (but not during initialization)
  useEffect(() => {
    // Only load data when date range changes after initialization
    if (isDateRangeInitialized) {
      let isMounted = true;
      
      const loadDataWrapper = async () => {
        if (isMounted) {
          await loadData();
        }
      };
      
      loadDataWrapper();
      
      // Cleanup function to prevent state updates on unmounted component
      return () => {
        isMounted = false;
      };
    }
  }, [dateRange, loadData, isDateRangeInitialized]);

  // Manual refresh function
  const handleRefresh = () => {
    loadData();
  };

  return (
    // Fixed background gradient to ensure full width and proper height
    <main className="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 py-12 min-h-screen w-full">
      {/* Header */}
      <div className="w-full pt-4 pb-8 px-4">
        <div className="flex justify-center items-center gap-4">
          <h1 className="text-4xl font-semibold text-white">Home</h1>
        </div>
      </div>

      {/* Stat Cards */}
      <div className="max-w-7xl mx-auto px-4 pb-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {/* Production Rate - Total Buildings */}
          <div 
            className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 flex flex-col items-center justify-center text-center"
          >
            <div className="flex flex-col items-center justify-center text-center">
              <div className="mb-3 flex items-center justify-center">
                {icons.Zap && <icons.Zap className="w-10 h-10 text-yellow-400" />}
              </div>
              <p className="text-white font-bold text-base md:text-lg mb-2">Total Building</p>
              <p className="text-2xl md:text-3xl font-bold text-white">
                {loading ? '-' : stats.total_buildings}
              </p>
            </div>
          </div>

          {/* Efficiency - Total Rooms */}
          <div 
            className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 hover:bg-white/15 transition-all duration-300 flex flex-col items-center justify-center text-center"
          >
            <div className="flex flex-col items-center justify-center text-center">
              <div className="mb-3 flex items-center justify-center">
                {icons.BarChart3 && <icons.BarChart3 className="w-10 h-10 text-blue-400" />}
              </div>
              <p className="text-white font-bold text-base md:text-lg mb-2">Total Room</p>
              <p className="text-2xl md:text-3xl font-bold text-white">
                {loading ? '-' : stats.total_rooms}
              </p>
            </div>
          </div>

          {/* Units Active - Total CCTVs */}
          <div 
            className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 hover:bg-white/15 transition-all duration-300 flex flex-col items-center justify-center text-center"
          >
            <div className="flex flex-col items-center justify-center text-center">
              <div className="mb-3 flex items-center justify-center">
                {icons.Activity && <icons.Activity className="w-10 h-10 text-green-400" />}
              </div>
              <p className="text-white font-bold text-base md:text-lg mb-2">Total CCTV</p>
              <p className="text-2xl md:text-3xl font-bold text-white">
                {loading ? '-' : stats.total_cctvs}
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* Charts Section */}
      <div className="max-w-7xl mx-auto px-4">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Production Trends Chart */}
          <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6">
            <div className="flex justify-between items-center mb-6">
              <h3 className="text-lg font-semibold text-white text-center w-full">
                Production Trend - {(() => {
                  try {
                    const displayDate = new Date(dateRange.start);
                    return displayDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                  } catch (error) {
                    console.error('Error formatting date:', error);
                    return 'Unknown Date';
                  }
                })()}
              </h3>
            </div>
            <div className="h-64 md:h-80">
              {chartLoading ? (
                <div className="w-full h-full flex items-center justify-center">
                  <p className="text-white font-semibold">Loading chart data...</p>
                </div>
              ) : productionTrends && productionTrends.length > 0 ? (
                <ResponsiveContainer width="100%" height="100%">
                  <LineChart
                    data={productionTrends}
                    margin={{
                      top: 10,
                      right: 10,
                      left: 0,
                      bottom: 0,
                    }}
                  >
                    <CartesianGrid strokeDasharray="3 3" stroke="#ffffff20" />
                    <XAxis 
                      dataKey="date" 
                      stroke="#ffffff80" 
                      tick={{ fill: '#ffffff80' }}
                      tickFormatter={(value) => {
                        const date = new Date(value);
                        return `${date.getDate()}/${date.getMonth() + 1}`;
                      }}
                    />
                    {/* Left Axis for Volume (Thousands) */}
                    <YAxis 
                      yAxisId="left"
                      stroke="#10b981" 
                      tick={{ fill: '#10b981' }}
                      label={{ value: 'Volume', angle: -90, position: 'insideLeft', fill: '#10b981' }}
                    />
                    {/* Right Axis for Percentages/Speed (0-100) */}
                    <YAxis 
                      yAxisId="right"
                      orientation="right"
                      stroke="#3b82f6" 
                      tick={{ fill: '#3b82f6' }}
                      domain={[0, 100]}
                      label={{ value: 'Rate / Speed', angle: 90, position: 'insideRight', fill: '#3b82f6' }}
                    />
                    <Tooltip 
                      contentStyle={{ 
                        backgroundColor: '#1e293b', 
                        borderColor: '#ffffff20', 
                        borderRadius: '0.5rem',
                        color: 'white'
                      }} 
                      formatter={(value, name) => {
                        const nameStr = String(name);
                        if (nameStr === 'Traffic Volume') {
                          return [value.toLocaleString(), 'Vehicles'];
                        } else if (nameStr.includes('Speed')) {
                          return [`${value} km/h`, 'Avg Speed'];
                        } else {
                          return [value, nameStr];
                        }
                      }}
                    />
                    <Legend />
                    <Line
                      yAxisId="left"
                      type="monotone"
                      dataKey="traffic_volume"
                      stroke="#10b981"
                      strokeWidth={3}
                      name="Traffic Volume"
                      dot={false}
                    />
                    <Line
                      yAxisId="right"
                      type="monotone"
                      dataKey="average_speed"
                      stroke="#3b82f6"
                      strokeWidth={2}
                      name="Avg Speed (km/h)"
                      dot={false}
                    />
                    <Line
                      yAxisId="right"
                      type="monotone"
                      dataKey="congestion_index"
                      stroke="#f59e0b"
                      strokeWidth={2}
                      name="Congestion Index"
                      dot={false}
                    />
                    {/* Hide less important lines to reduce clutter, or keep on right axis */}
                    <Line
                      yAxisId="right"
                      type="monotone"
                      dataKey="green_wave_efficiency"
                      stroke="#ec4899"
                      strokeWidth={2}
                      name="Green Wave Eff."
                      dot={false}
                    />
                  </LineChart>
                </ResponsiveContainer>
              ) : (
                <div className="w-full h-full flex items-center justify-center">
                  <p className="text-white font-semibold">No production data available</p>
                </div>
              )}
            </div>
          </div>

          {/* Unit Performance Chart */}
          <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6">
            <h3 className="text-lg font-semibold text-white mb-6 text-center">Unit Performance</h3>
            <div className="h-64 md:h-80">
              {chartLoading ? (
                <div className="w-full h-full flex items-center justify-center">
                  <p className="text-white font-semibold">Loading chart data...</p>
                </div>
              ) : unitPerformance && unitPerformance.length > 0 ? (
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart
                    data={unitPerformance}
                    margin={{
                      top: 20,
                      right: 30,
                      left: 20,
                      bottom: 20,
                    }}
                    barGap={0}
                    barCategoryGap="20%"
                  >
                    <CartesianGrid strokeDasharray="3 3" stroke="#ffffff20" vertical={false} />
                    <XAxis 
                      dataKey="unit" 
                      stroke="#ffffff80" 
                      tick={{ fill: '#ffffff80' }}
                      // Removed angle rotation for better readability if labels fit
                      height={30}
                    />
                    <YAxis 
                      stroke="#ffffff80" 
                      tick={{ fill: '#ffffff80' }}
                      domain={[0, 100]}
                      tickFormatter={(value) => `${value}%`}
                    />
                    <Tooltip 
                      cursor={{ fill: 'transparent' }}
                      contentStyle={{ 
                        backgroundColor: '#1e293b', 
                        borderColor: '#ffffff20', 
                        color: 'white' 
                      }}
                    />
                    <Legend verticalAlign="top" height={36}/>
                    <Bar
                      dataKey="efficiency"
                      name="System Efficiency"
                      fill="#3b82f6"
                      radius={[4, 4, 0, 0]}
                      label={{ position: 'top', fill: 'white', formatter: (val: any) => `${val}%` }}
                    />
                    <Bar
                      dataKey="traffic_density"
                      name="Traffic Density"
                      fill="#10b981"
                      radius={[4, 4, 0, 0]}
                      label={{ position: 'top', fill: 'white', formatter: (val: any) => `${val}%` }}
                    />
                    <Bar
                      dataKey="signal_optimization"
                      name="Signal Optimization"
                      fill="#f59e0b"
                      radius={[4, 4, 0, 0]}
                      label={{ position: 'top', fill: 'white', formatter: (val: any) => `${val}%` }}
                    />
                  </BarChart>
                </ResponsiveContainer>
              ) : (
                <div className="w-full h-full flex items-center justify-center">
                  <p className="text-white font-semibold">No performance data available</p>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </main>
  )
}