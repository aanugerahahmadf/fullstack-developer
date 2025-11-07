"use client"

import Link from "next/link"
import Image from "next/image"
import { Menu, X } from "lucide-react"
import { useState } from "react"

export default function Navbar() {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  return (
    <nav className="bg-blue-950/80 backdrop-blur-md border-b border-white sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between">
        {/* Logo */}
        <Link href="/" className="flex items-center gap-3">
          <Image
            src="/images/design-mode/logo-pertamina.png"
            alt="Pertamina"
            width={136}
            height={120}
            className="h-30 w-34"
            priority
          />
        </Link>

        {/* Desktop Menu */}
        <div className="hidden md:flex gap-8">
          <Link href="/" className="text-white font-semibold transition hover:text-white">
            Home
          </Link>
          <Link href="/maps" className="text-white font-semibold transition hover:text-white">
            Maps
          </Link>
          <Link href="/playlist" className="text-white font-semibold transition hover:text-white">
            Playlist
          </Link>
          <Link href="/contact" className="text-white font-semibold transition hover:text-white">
            Contact
          </Link>
        </div>

        {/* Mobile Menu Button */}
        <button 
          className="md:hidden text-white p-2" 
          onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
          aria-label={mobileMenuOpen ? "Close menu" : "Open menu"}
        >
          {mobileMenuOpen ? <X size={24} /> : <Menu size={24} />}
        </button>
      </div>

      {/* Mobile Menu */}
      {mobileMenuOpen && (
        <div className="md:hidden bg-blue-900/95 border-t border-white py-4 px-4 absolute top-full left-0 right-0 z-50">
          <div className="flex flex-col gap-3">
            <Link 
              href="/" 
              className="text-white font-semibold transition block py-3 px-4 rounded-lg hover:bg-white/10"
              onClick={() => setMobileMenuOpen(false)}
            >
              Home
            </Link>
            <Link 
              href="/maps" 
              className="text-white font-semibold transition block py-3 px-4 rounded-lg hover:bg-white/10"
              onClick={() => setMobileMenuOpen(false)}
            >
              Maps
            </Link>
            <Link 
              href="/playlist" 
              className="text-white font-semibold transition block py-3 px-4 rounded-lg hover:bg-white/10"
              onClick={() => setMobileMenuOpen(false)}
            >
              Playlist
            </Link>
            <Link 
              href="/contact" 
              className="text-white font-semibold transition block py-3 px-4 rounded-lg hover:bg-white/10"
              onClick={() => setMobileMenuOpen(false)}
            >
              Contact
            </Link>
          </div>
        </div>
      )}
    </nav>
  )
}