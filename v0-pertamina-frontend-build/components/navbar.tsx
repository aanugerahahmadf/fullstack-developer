"use client"

import Link from "next/link"
import Image from "next/image"
import { useState, useEffect, useCallback } from "react"

export default function Navbar() {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const [icons, setIcons] = useState<any>({})

  // Load icons dynamically to avoid HMR issues with Turbopack
  useEffect(() => {
    let isMounted = true;
    
    const loadIcons = async () => {
      try {
        const lucide = await import('lucide-react')
        if (isMounted) {
          setIcons({
            Menu: lucide.Menu,
            X: lucide.X
          })
        }
      } catch (error) {
        console.warn('Failed to load icons:', error)
        if (isMounted) {
          setIcons({})
        }
      }
    }
    
    loadIcons()
    
    // Cleanup function to prevent state updates on unmounted component
    return () => {
      isMounted = false;
    };
  }, [])

  // Memoize the toggle function to prevent unnecessary re-renders
  const toggleMobileMenu = useCallback(() => {
    setMobileMenuOpen(prev => !prev)
  }, [])

  // Memoize the close function
  const closeMobileMenu = useCallback(() => {
    setMobileMenuOpen(false)
  }, [])

  const MenuIcon = icons.Menu
  const XIcon = icons.X

  return (
    <nav className="bg-white border-b border-gray sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-3" prefetch={true}>
            <Image
              src="/images/logo-pertamina.png"
              alt="Pertamina"
              width={136}
              height={120}
              className="h-34 w-38 object-contain"
              priority
              onError={(e) => {
                console.warn('Failed to load logo image:', e);
                // Handle image loading error gracefully
              }}
            />
          </Link>

          {/* Desktop Menu */}
          <div className="hidden md:flex gap-8">
            <Link 
              href="/" 
              className="text-gray-1000 font-semibold block py-3 px-4 rounded-lg hover:text-gray-400"
              prefetch={true}
            >
              Home
            </Link>
            <Link 
              href="/maps" 
              className="text-gray-1000 font-semibold block py-3 px-4 rounded-lg hover:text-gray-400"
              prefetch={true}
            >
              Maps
            </Link>
            <Link 
              href="/playlist" 
              className="text-gray-1000 font-semibold block py-3 px-4 rounded-lg hover:text-gray-400"
              prefetch={true}
            >
              Playlist
            </Link>
            <Link 
              href="/contact" 
              className="text-gray-1000 font-semibold block py-3 px-4 rounded-lg hover:text-gray-400"
              prefetch={true}
            >
              Contact
            </Link>
          </div>

          {/* Mobile Menu Button */}
          <button 
            className="md:hidden text-gray-1000 font-semibold block py-3 px-4 rounded-lg hover:text-gray-400" 
            onClick={toggleMobileMenu}
            aria-label={mobileMenuOpen ? "Close menu" : "Open menu"}
          >
            {mobileMenuOpen ? (
              XIcon ? <XIcon size={24} /> : <div className="w-6 h-6" />
            ) : (
              MenuIcon ? <MenuIcon size={24} /> : <div className="w-6 h-6" />
            )}
          </button>
        </div>
      </div>
      
      
      {/* Mobile menu */}
      {mobileMenuOpen && (
        <div className="md:hidden bg-white/60 backdrop-blur-md border-t border-gray py-4 px-4 absolute top-full left-0 right-0 z-50">
          <div className="flex flex-col gap-3">
            <Link 
              href="/" 
              className="text-gray-1000 font-semibold block py-3 px-4 rounded-lg hover:text-gray-400"
              onClick={closeMobileMenu}
              prefetch={true}
            >
              Home
            </Link>
            <Link 
              href="/maps" 
              className="text-gray-1000 font-semibold block py-3 px-4 rounded-lg hover:text-gray-400"
              onClick={closeMobileMenu}
              prefetch={true}
            >
              Maps
            </Link>
            <Link 
              href="/playlist" 
              className="text-gray-1000 font-semibold block py-3 px-4 rounded-lg hover:text-gray-400"
              onClick={closeMobileMenu}
              prefetch={true}
            >
              Playlist
            </Link>
            <Link 
              href="/contact" 
              className="text-gray-1000 font-semibold block py-3 px-4 rounded-lg hover:text-gray-400"
              onClick={closeMobileMenu}
              prefetch={true}
            >
              Contact
            </Link>
          </div>
        </div>
      )}
    </nav>
  )
}