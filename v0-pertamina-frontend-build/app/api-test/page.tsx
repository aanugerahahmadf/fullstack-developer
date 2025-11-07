"use client"

import { useState, useEffect } from "react"
import { api } from '@/lib/api'

export default function ApiTestPage() {
  const [buildings, setBuildings] = useState<any[]>([])
  const [contacts, setContacts] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<boolean>(false)

  useEffect(() => {
    const testConnection = async () => {
      try {
        setLoading(true)
        // Test multiple API endpoints to verify Axios integration
        const buildingsData = await api.getBuildings()
        const contactsData = await api.getContacts()
        
        setBuildings(buildingsData)
        // Handle the case where contactsData might be null or a single object
        if (Array.isArray(contactsData)) {
          setContacts(contactsData)
        } else if (contactsData && typeof contactsData === 'object' && 'id' in contactsData) {
          // If it's a single contact object, wrap it in an array
          setContacts([contactsData])
        } else {
          // If no data or null, set empty array
          setContacts([])
        }
        setSuccess(true)
        setError(null)
      } catch (err) {
        setError('Failed to fetch data: ' + (err as Error).message)
        console.error(err)
      } finally {
        setLoading(false)
      }
    }

    testConnection()
  }, [])

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold mb-4">API Connection Test (Axios)</h1>
      
      {loading && <p className="text-blue-500">Testing connection...</p>}
      
      {error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <p>{error}</p>
        </div>
      )}
      
      {success && (
        <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          <p>✅ Connection successful! Axios is working correctly.</p>
        </div>
      )}
      
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h2 className="text-xl font-semibold mb-2">Buildings ({buildings.length})</h2>
          {buildings.length > 0 ? (
            <ul className="list-disc pl-5">
              {buildings.map((building) => (
                <li key={building.id} className="mb-1">
                  {building.name} - Lat: {building.latitude}, Lng: {building.longitude}
                  {building.rooms && building.rooms.length > 0 && (
                    <ul className="list-circle pl-5 mt-1">
                      {building.rooms.map((room: any) => (
                        <li key={room.id}>
                          {room.name} ({room.cctvs?.length || 0} CCTVs)
                        </li>
                      ))}
                    </ul>
                  )}
                </li>
              ))}
            </ul>
          ) : (
            <p>No buildings found.</p>
          )}
        </div>
        
        <div>
          <h2 className="text-xl font-semibold mb-2">Contacts ({contacts.length})</h2>
          {contacts.length > 0 ? (
            <ul className="space-y-2">
              {contacts.map((contact: any) => (
                <li key={contact.id} className="border p-3 rounded">
                  <p><strong>Email:</strong> {contact.email || 'N/A'}</p>
                  <p><strong>Phone:</strong> {contact.phone || 'N/A'}</p>
                  <p><strong>Address:</strong> {contact.address || 'N/A'}</p>
                </li>
              ))}
            </ul>
          ) : (
            <p>No contacts found.</p>
          )}
        </div>
      </div>
    </div>
  )
}