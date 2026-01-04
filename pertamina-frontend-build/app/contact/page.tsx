"use client"

import React, { useState, useEffect } from "react"
import dynamic from 'next/dynamic'
import { getContacts } from '@/lib/api'

// Dynamically import lucide-react icons to avoid HMR issues with Turbopack
const Phone = dynamic(() => import('lucide-react').then((mod) => mod.Phone), { ssr: false })
const Mail = dynamic(() => import('lucide-react').then((mod) => mod.Mail), { ssr: false })
const MapPin = dynamic(() => import('lucide-react').then((mod) => mod.MapPin), { ssr: false })
const Instagram = dynamic(() => import('lucide-react').then((mod) => mod.Instagram), { ssr: false })
const X = dynamic(() => import('lucide-react').then((mod) => mod.X), { ssr: false })
const Building2 = dynamic(() => import('lucide-react').then((mod) => mod.Building2), { ssr: false })

export default function ContactPage() {
  const [contacts, setContacts] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  
  // State for dynamically imported icons
  const [icons, setIcons] = useState({
    Phone: null as any,
    Mail: null as any,
    MapPin: null as any,
    Instagram: null as any,
    X: null as any,
    Building2: null as any,
  });

  // Load icons dynamically
  useEffect(() => {
    const loadIcons = async () => {
      const { Phone, Mail, MapPin, Instagram, X, Building2 } = await import('lucide-react');
      setIcons({ Phone, Mail, MapPin, Instagram, X, Building2 });
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

  // Function to handle email click - opens Gmail specifically
  const handleEmailClick = (email: string) => {
    window.open(`https://mail.google.com/mail/?view=cm&fs=1&to=${email}`, '_blank');
  };

  // Function to handle phone click - opens phone contacts app or dialer
  const handlePhoneClick = (phone: string) => {
    // Remove any non-digit characters except + for international numbers
    const cleanPhone = phone.replace(/[^\d+]/g, '');
    
    // Try multiple approaches for better compatibility
    const urls = [
      `tel:${cleanPhone}`,           // Standard phone dialer (most widely supported)
      `sms:${cleanPhone}`,           // SMS as fallback
    ];
    
    // Try to open the phone dialer first (most reliable)
    window.open(urls[0], '_blank');
  };

  // Function to handle Instagram click - opens Instagram profile
  const handleInstagramClick = (instagram: string) => {
    // Assuming instagram handle doesn't include @, add it if needed
    const instagramHandle = instagram.startsWith('@') ? instagram.substring(1) : instagram;
    window.open(`https://www.instagram.com/${instagramHandle}`, '_blank');
  };

  // Function to handle address click - opens Google Maps
  const handleAddressClick = (address: string) => {
    // Encode the address for URL
    const encodedAddress = encodeURIComponent(address);
    window.open(`https://www.google.com/maps/search/?api=1&query=${encodedAddress}`, '_blank');
  };

  return (
    // Fixed background gradient to ensure full width and proper height
    <main className="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 py-8 min-h-screen w-full">
      {/* Header */}
      <div className="pt-4 pb-6 px-4">
        <div className="flex justify-center items-center gap-4">
          <h1 className="text-4xl md:text-5xl font-semibold text-white text-center">Contact Us</h1>
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
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mt-4">
            {contacts.map((contact) => (
              <React.Fragment key={contact.id}>
                {/* Email Card */}
                {contact.email && (
                  <div 
                    className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 md:p-8 min-h-[120px] md:min-h-[140px] cursor-pointer hover:bg-white/20 transition-all duration-200 flex items-center justify-center shadow-lg"
                    onClick={() => handleEmailClick(contact.email)}
                  >
                    <div className="flex flex-col items-center justify-center text-center">
                      {icons.Mail && <icons.Mail className="w-10 h-10 text-blue-400 mb-3" />}
                      <div>
                        <p className="text-white text-base md:text-lg font-bold mb-1">Email</p>
                        <p className="text-white text-base">{contact.email}</p>
                      </div>
                    </div>
                  </div>
                )}
                
                {/* Phone Card */}
                {contact.phone && (
                  <div 
                    className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 md:p-8 min-h-[120px] md:min-h-[140px] cursor-pointer hover:bg-white/20 transition-all duration-200 flex items-center justify-center shadow-lg"
                    onClick={() => handlePhoneClick(contact.phone)}
                  >
                    <div className="flex flex-col items-center justify-center text-center">
                      {icons.Phone && <icons.Phone className="w-10 h-10 text-green-400 mb-3" />}
                      <div>
                        <p className="text-white text-base md:text-lg font-bold mb-1">Phone</p>
                        <p className="text-white text-base">{contact.phone}</p>
                      </div>
                    </div>
                  </div>
                )}
                
                {/* Instagram Card */}
                {contact.instagram && (
                  <div 
                    className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 md:p-8 min-h-[120px] md:min-h-[140px] cursor-pointer hover:bg-white/20 transition-all duration-200 flex items-center justify-center shadow-lg"
                    onClick={() => handleInstagramClick(contact.instagram)}
                  >
                    <div className="flex flex-col items-center justify-center text-center">
                      {icons.Instagram && <icons.Instagram className="w-10 h-10 text-pink-400 mb-3" />}
                      <div>
                        <p className="text-white text-base md:text-lg font-bold mb-1">Instagram</p>
                        <p className="text-white text-base">{contact.instagram}</p>
                      </div>
                    </div>
                  </div>
                )}
              </React.Fragment>
            ))}
            
            {/* Address Card - Moved to the bottom and full width */}
            {contacts.map((contact) => (
              contact.address && (
                <div 
                  key={`address-${contact.id}`} 
                  className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 md:p-8 min-h-[200px] md:min-h-[300px] col-span-1 md:col-span-2 lg:col-span-3 cursor-pointer hover:bg-white/20 transition-all duration-200 flex items-center justify-center shadow-lg"
                  onClick={() => handleAddressClick(contact.address)}
                >
                  <div className="flex flex-col items-center justify-center text-center">
                    {icons.MapPin && <icons.MapPin className="w-10 h-10 text-red-400 mb-3" />}
                    <div>
                      <p className="text-white text-base md:text-lg font-bold mb-1">Address</p>
                      <p className="text-white text-base">{contact.address}</p>
                    </div>
                  </div>
                </div>
              )
            ))}
          </div>
        )}
      </div>
    </main>
  )
}