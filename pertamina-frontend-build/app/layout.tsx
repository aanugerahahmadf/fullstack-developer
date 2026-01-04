import type React from "react"
import type { Metadata } from "next"
import { Geist, Geist_Mono } from "next/font/google"
import "./globals.css"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"

const _geist = Geist({ subsets: ["latin"] })
const _geistMono = Geist_Mono({ subsets: ["latin"] })

export const metadata: Metadata = {
  title: "Kilang Pertamina International",
  description: "Refinery monitoring and management system",
  generator: 'v0.app'
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return (
    <html lang="en">
      <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
      </head>
      <body className={`${_geist.className} antialiased flex flex-col min-h-screen`}>
        <Navbar />
        <main className="flex-grow w-full pt-16 pb-12">
          {children}
        </main>
        <Footer />
      </body>
    </html>
  )
}