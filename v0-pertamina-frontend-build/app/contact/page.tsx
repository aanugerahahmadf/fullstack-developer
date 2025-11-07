"use client"

import { useState, useEffect } from "react"
import { Mail, Phone, MapPin, Instagram } from "lucide-react"
import { api } from '@/lib/api'

interface Contact {
  id: string
  email?: string
  phone?: string
  address?: string
  instagram?: string
}

interface ContactMethod {
  id: string
  type: 'email' | 'phone' | 'address' | 'instagram'
  title: string
  value: string
  icon: typeof Mail | typeof Phone | typeof MapPin | typeof Instagram
  color: string
  borderColor: string
  hoverColor: string
  textColor: string
}

export default function ContactPage() {
  const [contacts, setContacts] = useState<Contact[]>([])
  const [loading, setLoading] = useState(true)

  // Load data only once on component mount
  useEffect(() => {
    const fetchContacts = async () => {
      try {
        const data = await api.getContacts()
        // Handle case where data might be null, a single contact, or an array of contacts
        if (Array.isArray(data)) {
          setContacts(data)
        } else if (data && typeof data === 'object' && 'id' in data) {
          // If it's a single contact object, wrap it in an array
          setContacts([data as Contact])
        } else {
          // If no data or null, set empty array
          setContacts([])
        }
      } catch (error) {
        console.error('Failed to fetch contacts:', error)
        // Set empty array on error
        setContacts([])
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

  // Flatten all contact methods into individual boxes
  const getAllContactMethods = (): ContactMethod[] => {
    const methods: ContactMethod[] = [];
    
    contacts.forEach(contact => {
      // Add email box
      if (contact.email) {
        methods.push({
          id: `email-${contact.id}`,
          type: 'email',
          title: 'Gmail',
          value: contact.email,
          icon: Mail,
          color: 'bg-blue-600',
          borderColor: 'border-blue-400',
          hoverColor: 'hover:bg-blue-700',
          textColor: 'text-blue-100'
        });
      }
      
      // Add phone box
      if (contact.phone) {
        methods.push({
          id: `phone-${contact.id}`,
          type: 'phone',
          title: 'Phone',
          value: contact.phone,
          icon: Phone,
          color: 'bg-green-600',
          borderColor: 'border-green-400',
          hoverColor: 'hover:bg-green-700',
          textColor: 'text-green-100'
        });
      }
      
      // Add address box
      if (contact.address) {
        methods.push({
          id: `address-${contact.id}`,
          type: 'address',
          title: 'Address',
          value: contact.address,
          icon: MapPin,
          color: 'bg-purple-600',
          borderColor: 'border-purple-400',
          hoverColor: 'hover:bg-purple-700',
          textColor: 'text-purple-100'
        });
      }
      
      // Add instagram box
      if (contact.instagram) {
        methods.push({
          id: `instagram-${contact.id}`,
          type: 'instagram',
          title: 'Instagram',
          value: `@${contact.instagram}`,
          icon: Instagram,
          color: 'bg-pink-600',
          borderColor: 'border-pink-400',
          hoverColor: 'hover:bg-pink-700',
          textColor: 'text-pink-100'
        });
      }
    });
    
    return methods;
  };

  const contactMethods = getAllContactMethods();

  return (
    <main className="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 py-8 min-h-[calc(100vh-140px)]">
      {/* Header */}
      <div className="pt-4 pb-6 px-4">
        <div className="flex justify-center items-center gap-4">
          <h1 className="text-3xl md:text-4xl font-semibold text-white text-center">Contact Us</h1>
        </div>
      </div>

      {/* All Contact Methods - completely separate boxes */}
      <div className="max-w-7xl mx-auto px-4 pb-8">
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mb-3"></div>
            <p className="text-white font-semibold">Loading contacts...</p>
          </div>
        ) : contactMethods.length === 0 ? (
          <div className="text-center py-12">
            <Mail className="w-12 h-12 text-white mx-auto mb-4" />
            <p className="text-white font-semibold">No contacts available</p>
            <p className="text-white text-sm mt-2">Contacts will appear once added in admin panel</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            {contactMethods.map((method) => {
              const IconComponent = method.icon;
              let href = '#';
              let target = '_self';
              let rel = '';
              
              // Set appropriate links for each type
              switch(method.type) {
                case 'email':
                  href = `mailto:${method.value}`;
                  break;
                case 'phone':
                  href = `tel:${method.value}`;
                  break;
                case 'address':
                  href = `https://maps.google.com/?q=${encodeURIComponent(method.value)}`;
                  target = '_blank';
                  rel = 'noopener noreferrer';
                  break;

                case 'instagram':
                  href = `https://instagram.com/${method.value.substring(1)}`;
                  target = '_blank';
                  rel = 'noopener noreferrer';
                  break;
              }
              
              return (
                <a
                  key={method.id}
                  href={href}
                  target={target}
                  rel={rel}
                  className={`${method.color} ${method.borderColor} border-2 rounded-lg p-6 hover:${method.hoverColor} transition-colors block`}
                >
                  <div className="flex items-center gap-2 mb-2">
                    <IconComponent className="w-5 h-5 text-white" />
                    <span className="font-semibold text-white">{method.title}</span>
                  </div>
                  <p className={`${method.textColor} text-sm`}>{method.value}</p>
                </a>
              );
            })}
          </div>
        )}
      </div>
    </main>
  );
}