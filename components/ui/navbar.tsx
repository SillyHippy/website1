"use client";

import { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { Button } from '@/components/ui/button';
import { X } from 'lucide-react';
import { useRouter, usePathname } from 'next/navigation';

export function Navbar() {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const router = useRouter();
  const pathname = usePathname();

  const scrollToFooter = () => {
    const footer = document.querySelector('footer');
    if (footer) {
      footer.scrollIntoView({ behavior: 'smooth' });
      setIsMobileMenuOpen(false);
    }
  };

  const handleCourierServicesClick = (e: React.MouseEvent) => {
    e.preventDefault();
    if (pathname !== '/') {
      router.push('/#courier-services');
    } else {
      document.getElementById('courier-services')?.scrollIntoView({ behavior: 'smooth' });
    }
    setIsMobileMenuOpen(false);
  };

  const toggleMobileMenu = () => {
    setIsMobileMenuOpen(!isMobileMenuOpen);
  };

  return (
    <nav className="fixed top-0 left-0 right-0 z-40 bg-white shadow-sm">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link href="/" className="flex items-center">
            <Image 
              src="/favicon1.svg" 
              alt="JLS Logo" 
              width={60} 
              height={60}
              className="object-contain"
              priority
            />
          </Link>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center space-x-8">
            <Link href="/" className="text-gray-700 hover:text-gray-900">
              Home
            </Link>
            <Link href="/pricing" className="text-gray-700 hover:text-gray-900">
              Pricing
            </Link>
            <a 
              href="/#courier-services" 
              className="text-gray-700 hover:text-gray-900 cursor-pointer"
              onClick={handleCourierServicesClick}
            >
              Courier Services
            </a>
            <Link href="/services" className="text-gray-700 hover:text-gray-900">
              Business Solutions
            </Link>
            <Link href="/payments" className="text-gray-700 hover:text-gray-900">
              Payments
            </Link>
          </div>

          {/* Contact Us Button - Desktop */}
          <div className="hidden md:block">
            <Button
              onClick={scrollToFooter}
              className="bg-black text-white hover:bg-gray-800"
            >
              Contact Us
            </Button>
          </div>

          {/* Mobile Menu Button */}
          <div className="md:hidden">
            <button
              type="button"
              onClick={toggleMobileMenu}
              className="text-gray-500 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
            >
              <span className="sr-only">Open main menu</span>
              <svg
                className="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                strokeWidth="1.5"
                stroke="currentColor"
                aria-hidden="true"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                />
              </svg>
            </button>
          </div>
        </div>

        {/* Mobile Menu */}
        <div 
          className={`fixed top-0 right-0 bottom-0 w-[250px] bg-white shadow-lg transform transition-transform duration-300 ease-in-out z-50 ${
            isMobileMenuOpen ? 'translate-x-0' : 'translate-x-full'
          } md:hidden`}
        >
          <div className="flex justify-between items-center p-4 border-b">
            <h2 className="text-xl font-semibold">Menu</h2>
            <button
              onClick={toggleMobileMenu}
              className="p-2 hover:bg-gray-100 rounded-full"
            >
              <X className="h-6 w-6" />
            </button>
          </div>
          <div className="flex flex-col py-4">
            <Link
              href="/"
              className="px-6 py-3 text-gray-700 hover:bg-gray-100"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Home
            </Link>
            <Link
              href="/pricing"
              className="px-6 py-3 text-gray-700 hover:bg-gray-100"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Pricing
            </Link>
            <a
              href="/#courier-services"
              className="px-6 py-3 text-gray-700 hover:bg-gray-100 cursor-pointer"
              onClick={handleCourierServicesClick}
            >
              Courier Services
            </a>
            <Link
              href="/services"
              className="px-6 py-3 text-gray-700 hover:bg-gray-100"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Business Solutions
            </Link>
            <Link
              href="/payments"
              className="px-6 py-3 text-gray-700 hover:bg-gray-100"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Payments
            </Link>
            <button
              onClick={scrollToFooter}
              className="px-6 py-3 text-gray-700 hover:bg-gray-100 text-left"
            >
              Contact Us
            </button>
          </div>
        </div>

        {/* Overlay */}
        {isMobileMenuOpen && (
          <div
            className="fixed inset-0 bg-black bg-opacity-50 z-45 md:hidden"
            onClick={toggleMobileMenu}
          />
        )}
      </div>
    </nav>
  );
}
