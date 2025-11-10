"use client"

import { useState, useEffect } from "react"
import dynamic from 'next/dynamic'
import { getContacts } from '@/lib/api'

// Dynamically import lucide-react icons to avoid HMR issues with Turbopack
const Phone = dynamic(() => import('lucide-react').then((mod) => mod.Phone), { ssr: false })
const Mail = dynamic(() => import('lucide-react').then((mod) => mod.Mail), { ssr: false })
const MapPin = dynamic(() => import('lucide-react').then((mod) => mod.MapPin), { ssr: false })
const Instagram = dynamic(() => import('lucide-react').then((mod) => mod.Instagram), { ssr: false })
const X = dynamic(() => import('lucide-react').then((mod) => mod.X), { ssr: false })
const Building2 = dynamic(() => import('lucide-react').then((mod) => mod.Building2), { ssr: false })
const CheckCircle = dynamic(() => import('lucide-react').then((mod) => mod.CheckCircle), { ssr: false })
const AlertTriangle = dynamic(() => import('lucide-react').then((mod) => mod.AlertTriangle), { ssr: false })
const XCircle = dynamic(() => import('lucide-react').then((mod) => mod.XCircle), { ssr: false })

export default function ContactPage() {
  const [contacts, setContacts] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  const [apiStatus, setApiStatus] = useState('connected')
  const [systemStatus, setSystemStatus] = useState({status: 'active', message: 'All systems operational'})
  
  // State for dynamically imported icons
  const [icons, setIcons] = useState({
    Phone: null as any,
    Mail: null as any,
    MapPin: null as any,
    Instagram: null as any,
    X: null as any,
    Building2: null as any,
    CheckCircle: null as any,
    AlertTriangle: null as any,
    XCircle: null as any,
  });

  // Load icons dynamically
  useEffect(() => {
    const loadIcons = async () => {
      const { Phone, Mail, MapPin, Instagram, X, Building2, CheckCircle, AlertTriangle, XCircle } = await import('lucide-react');
      setIcons({ Phone, Mail, MapPin, Instagram, X, Building2, CheckCircle, AlertTriangle, XCircle });
    };
    
    loadIcons();
  }, []);

  // Load data only once on component mount - no automatic refreshing
  useEffect(() => {
    const fetchContacts = async () => {
      try {
        const contactsData = await getContacts()
        setContacts(contactsData)
      } catch (error) {
        console.error('Failed to fetch contacts:', error)
      } finally {
        setLoading(false)
      }
    }

    // Use a small delay to ensure UI is ready before fetching data
    const timer = setTimeout(() => {
      fetchContacts()
    }, 50);
    
    return () => clearTimeout(timer);
  }, []) // Empty dependency array - only run once on mount

  // Function to get the appropriate icon and color for system status
  const getSystemStatusDisplay = () => {
    switch (systemStatus.status) {
      case 'active':
        return { icon: icons.CheckCircle, color: 'text-emerald-400', bgColor: 'bg-emerald-500/20' };
      case 'warning':
        return { icon: icons.AlertTriangle, color: 'text-yellow-400', bgColor: 'bg-yellow-500/20' };
      case 'error':
        return { icon: icons.XCircle, color: 'text-red-400', bgColor: 'bg-red-500/20' };
      default:
        return { icon: icons.Building2, color: 'text-blue-400', bgColor: 'bg-blue-500/20' };
    }
  };

  const systemStatusDisplay = getSystemStatusDisplay();
  const SystemStatusIcon = systemStatusDisplay.icon;

  return (
    <main className="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 py-8 min-h-[calc(100vh-140px)]">
      {/* Header */}
      <div className="pt-4 pb-6 px-4">
        <div className="flex justify-center items-center gap-4">
          <h1 className="text-3xl md:text-4xl font-semibold text-white text-center">Contact</h1>
        </div>
      </div>

      {/* System Status Card - responsive design */}
      <div className="max-w-7xl mx-auto px-4 pb-6">
        <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 md:p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-white font-semibold text-sm mb-1">System Status</p>
              <p className={`text-lg font-semibold ${systemStatusDisplay.color}`}>
                {loading ? 'Loading...' : systemStatus.message}
              </p>
            </div>
            <div className={`p-2 rounded-full ${systemStatusDisplay.bgColor}`}>
              {SystemStatusIcon && <SystemStatusIcon className={`w-6 h-6 ${systemStatusDisplay.color}`} />}
            </div>
          </div>
        </div>
      </div>

      {/* Contact Cards Grid - responsive layout */}
      <div className="max-w-7xl mx-auto px-4 pb-8">
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mb-3"></div>
            <p className="text-white font-semibold">Loading contact information...</p>
          </div>
        ) : contacts.length === 0 ? (
          <div className="text-center py-12">
            {icons.Building2 && <icons.Building2 className="w-12 h-12 text-white mx-auto mb-4" />}
            <p className="text-white font-semibold">No contact information available</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            {contacts.map((contact) => (
              <div key={contact.id} className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 md:p-6">
                <div className="space-y-4">
                  {/* Email */}
                  {contact.email && (
                    <div className="flex items-center gap-3">
                      {icons.Mail && <icons.Mail className="w-5 h-5 text-blue-400" />}
                      <div>
                        <p className="text-white text-sm font-semibold">Email</p>
                        <p className="text-white text-sm">{contact.email}</p>
                      </div>
                    </div>
                  )}
                  
                  {/* Phone */}
                  {contact.phone && (
                    <div className="flex items-center gap-3">
                      {icons.Phone && <icons.Phone className="w-5 h-5 text-green-400" />}
                      <div>
                        <p className="text-white text-sm font-semibold">Phone</p>
                        <p className="text-white text-sm">{contact.phone}</p>
                      </div>
                    </div>
                  )}
                  
                  {/* Address */}
                  {contact.address && (
                    <div className="flex items-start gap-3">
                      {icons.MapPin && <icons.MapPin className="w-5 h-5 text-red-400 mt-0.5" />}
                      <div>
                        <p className="text-white text-sm font-semibold">Address</p>
                        <p className="text-white text-sm">{contact.address}</p>
                      </div>
                    </div>
                  )}
                  
                  {/* Instagram */}
                  {contact.instagram && (
                    <div className="flex items-center gap-3">
                      {icons.Instagram && <icons.Instagram className="w-5 h-5 text-pink-400" />}
                      <div>
                        <p className="text-white text-sm font-semibold">Instagram</p>
                        <p className="text-white text-sm">{contact.instagram}</p>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </main>
  )
}