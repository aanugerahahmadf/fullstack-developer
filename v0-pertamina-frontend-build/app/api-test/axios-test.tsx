"use client"

import { useState, useEffect } from "react"
import axios from 'axios'

export default function AxiosTestPage() {
  const [contacts, setContacts] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<boolean>(false)

  useEffect(() => {
    const testAxiosConnection = async () => {
      try {
        setLoading(true)
        // Fixed the API endpoint URL - removed incorrect /v1/ prefix
        const response = await axios.get('http://127.0.0.1:8000/api/contacts')
        setContacts(response.data.data || [])
        setSuccess(true)
        setError(null)
      } catch (err) {
        setError('Failed to fetch contacts: ' + (err as Error).message)
        console.error(err)
      } finally {
        setLoading(false)
      }
    }

    testAxiosConnection()
  }, [])

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold mb-4">Axios Connection Test</h1>
      
      {loading && <p className="text-blue-500">Testing Axios connection...</p>}
      
      {error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <p>{error}</p>
        </div>
      )}
      
      {success && (
        <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          <p>✅ Axios connection successful!</p>
        </div>
      )}
      
      {contacts.length > 0 && (
        <div>
          <h2 className="text-xl font-semibold mb-2">Contacts ({contacts.length})</h2>
          <ul className="list-disc pl-5">
            {contacts.map((contact: any) => (
              <li key={contact.id} className="mb-2">
                <p>Email: {contact.email}</p>
                <p>Phone: {contact.phone}</p>
                <p>Address: {contact.address}</p>
              </li>
            ))}
          </ul>
        </div>
      )}
      
      {!loading && !error && contacts.length === 0 && (
        <p>No contacts found.</p>
      )}
    </div>
  )
}