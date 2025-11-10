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
    <nav className="bg-blue-950/80 backdrop-blur-md border-b border-white sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between">
        {/* Logo */}
        <Link href="/" className="flex items-center gap-3" prefetch={true}>
          <Image
            src="/images/logo-pertamina.png"
            alt="Pertamina"
            width={136}
            height={120}
            className="h-30 w-34 object-contain"
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
            className="text-white font-semibold block py-3 px-4 rounded-lg hover:text-white"
            prefetch={true}
          >
            Home
          </Link>
          <Link 
            href="/maps" 
            className="text-white font-semibold block py-3 px-4 rounded-lg hover:text-white"
            prefetch={true}
          >
            Maps
          </Link>
          <Link 
            href="/playlist" 
            className="text-white font-semibold block py-3 px-4 rounded-lg hover:text-white"
            prefetch={true}
          >
            Playlist
          </Link>
          <Link 
            href="/contact" 
            className="text-white font-semibold block py-3 px-4 rounded-lg hover:text-white"
            prefetch={true}
          >
            Contact
          </Link>
        </div>

        {/* Mobile Menu Button */}
        <button 
          className="md:hidden text-white font-semibold block py-3 px-4 rounded-lg hover:text-white" 
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

      {/* Mobile Menu */}
      {mobileMenuOpen && (
        <div className="md:hidden bg-blue-900/95 border-t border-white py-4 px-4 absolute top-full left-0 right-0 z-50">
          <div className="flex flex-col gap-3">
            <Link 
              href="/" 
              className="text-white font-semibold block py-3 px-4 rounded-lg hover:text-white"
              onClick={closeMobileMenu}
              prefetch={true}
            >
              Home
            </Link>
            <Link 
              href="/maps" 
              className="text-white font-semibold block py-3 px-4 rounded-lg hover:text-white "
              onClick={closeMobileMenu}
              prefetch={true}
            >
              Maps
            </Link>
            <Link 
              href="/playlist" 
              className="text-white font-semibold block py-3 px-4 rounded-lg hover:text-white "
              onClick={closeMobileMenu}
              prefetch={true}
            >
              Playlist
            </Link>
            <Link 
              href="/contact" 
              className="text-white font-semibold block py-3 px-4 rounded-lg hover:text-white"
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