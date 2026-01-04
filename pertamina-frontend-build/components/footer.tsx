export default function Footer() {
  return (
    <footer className="fixed bottom-0 left-0 right-0 z-40 bg-white/80 backdrop-blur-md border-t border-gray-200/50 py-3">
      <div className="max-w-7xl mx-auto text-center px-4">
        <p className="text-gray-600 text-[10px] md:text-xs font-semibold">
          &copy; {new Date().getFullYear()} PT Kilang Pertamina Internasional – Refinery Unit VI Balongan. All rights reserved.
        </p>
      </div>
    </footer>
  )
}

