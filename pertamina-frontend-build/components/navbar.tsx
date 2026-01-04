"use client"

import Link from "next/link"
import Image from "next/image"
import { usePathname } from "next/navigation"
import { useState, useEffect, useCallback } from "react"

export default function Navbar() {
  const pathname = usePathname()
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
    <nav className="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200/50 shadow-sm">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16 relative">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-3 active:scale-95 transition-transform" prefetch={true}>
            <Image
              src="/images/logo-pertamina-light.png"
              alt="Pertamina"
              width={160}
              height={140}
              className="h-[85px] md:h-[110px] w-auto object-contain"
              priority
              onError={(e) => {
                console.warn('Failed to load logo image:', e);
              }}
            />
          </Link>

          {/* Desktop Menu - Centered */}
          <div className="hidden md:flex items-center gap-8 absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2">
            {[
              { name: 'Home', path: '/' },
              { name: 'Maps', path: '/maps' },
              { name: 'Playlist', path: '/playlist' },
              { name: 'Contact', path: '/contact' },
            ].map((item) => {
              const isActive = item.path === '/' 
                ? pathname === '/' 
                : pathname?.startsWith(item.path);
              
              return (
                <Link 
                  key={item.path}
                  href={item.path} 
                  className={`relative py-1 text-sm font-semibold transition-colors duration-200
                    ${isActive 
                      ? 'text-black' 
                      : 'text-gray-700 hover:text-black'
                    }
                  `}
                  prefetch={true}
                >
                  {item.name}
                  {isActive && (
                    <span className="absolute -bottom-1 left-0 w-full h-[2px] bg-black rounded-full" />
                  )}
                </Link>
              )
            })}
          </div>

          {/* Mobile Menu Button - Right Aligned */}
          <button 
            className="md:hidden p-2 text-gray-800 hover:bg-gray-100 rounded-lg transition-colors" 
            onClick={toggleMobileMenu}
            aria-label={mobileMenuOpen ? "Close menu" : "Open menu"}
          >
            {mobileMenuOpen ? (
              XIcon ? <XIcon size={28} /> : <div className="w-7 h-7" />
            ) : (
              MenuIcon ? <MenuIcon size={28} /> : <div className="w-7 h-7" />
            )}
          </button>
        </div>
      </div>
      
      {/* Mobile Menu Dropdown */}
      {mobileMenuOpen && (
        <div className="md:hidden absolute top-16 left-0 right-0 bg-white/95 backdrop-blur-xl border-b border-gray-200/50 shadow-lg z-40 animate-in slide-in-from-top-2 duration-200">
          <div className="flex flex-col divide-y divide-gray-100">
            {[
              { name: 'Home', path: '/' },
              { name: 'Maps', path: '/maps' },
              { name: 'Playlist', path: '/playlist' },
              { name: 'Contact', path: '/contact' },
            ].map((item) => {
              const isActive = item.path === '/' 
                ? pathname === '/' 
                : pathname?.startsWith(item.path);

              return (
                <Link 
                  key={item.path}
                  href={item.path} 
                  className={`block px-6 py-3 text-sm font-medium transition-colors ${
                    isActive 
                      ? 'bg-gray-50 text-black' 
                      : 'text-gray-600 hover:bg-gray-50 hover:text-black'
                  }`}
                  onClick={closeMobileMenu}
                  prefetch={true}
                >
                  {item.name}
                </Link>
              )
            })}
          </div>
        </div>
      )}
    </nav>
  )
}