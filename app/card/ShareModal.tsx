import React, { useEffect, useState, useRef } from 'react';
import Image from 'next/image';

interface ShareModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export default function ShareModal({ isOpen, onClose }: ShareModalProps) {
  const [copySuccess, setCopySuccess] = useState(false);
  const modalRef = useRef<HTMLDivElement>(null);
  const cardUrl = typeof window !== 'undefined' ? window.location.href : '';

  // Handle click outside modal
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (modalRef.current && !modalRef.current.contains(event.target as Node)) {
        onClose();
      }
    };

    if (isOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    }
    
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [isOpen, onClose]);

  // Reset copy success message after 2 seconds
  useEffect(() => {
    let timer: NodeJS.Timeout;
    if (copySuccess) {
      timer = setTimeout(() => {
        setCopySuccess(false);
      }, 2000);
    }
    return () => clearTimeout(timer);
  }, [copySuccess]);

  // Copy URL function
  const handleCopyLink = async (e: React.MouseEvent) => {
    e.preventDefault();
    try {
      await navigator.clipboard.writeText(cardUrl);
      setCopySuccess(true);
      console.log("URL copied to clipboard");
    } catch (err) {
      console.error("Failed to copy text: ", err);
    }
  };

  // Text message sharing
  const handleTextShare = (e: React.MouseEvent) => {
    e.preventDefault();
    const smsUrl = `sms:?&body=Check out Joseph Iannazzi's digital business card: ${cardUrl}`;
    window.location.href = smsUrl;
  };

  // Email sharing - using mailto link
  const handleEmailShare = (e: React.MouseEvent) => {
    e.preventDefault();
    const mailtoUrl = `mailto:?subject=Check out this digital business card&body=Here is Joseph Iannazzi's digital business card: ${cardUrl}`;
    window.location.href = mailtoUrl;
  };
  
  // WhatsApp sharing
  const handleWhatsAppShare = (e: React.MouseEvent) => {
    e.preventDefault();
    const whatsappUrl = `https://wa.me/?text=Check out Joseph Iannazzi's digital business card: ${cardUrl}`;
    window.open(whatsappUrl, '_blank');
  };

  // Removed Facebook sharing function

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-end justify-center z-50">
      <div ref={modalRef} className="bg-white rounded-t-lg p-6 w-full max-w-[400px]">
        <h2 className="text-xl font-bold mb-4">Share Joseph Iannazzi's Digital Business Card</h2>
        <div className="space-y-3">
          {/* Copy Card Link - Full button clickable */}
          <div 
            className="flex items-center justify-between p-3 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer"
            onClick={handleCopyLink}
          >
            <div className="flex items-center">
              <Image src="/favicon1.svg" alt="Link" width={24} height={24} />
              <span className="ml-3">{copySuccess ? "Link Copied!" : "Copy Card Link"}</span>
            </div>
            <div className="flex items-center">
              <Image src="/landingpage/icons/copy.svg" alt="Copy" width={32} height={32} />
            </div>
          </div>
          
          {/* Share via Text - Full button clickable */}
          <div 
            className="flex items-center justify-between p-3 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer"
            onClick={handleTextShare}
          >
            <div className="flex items-center">
              <Image src="/landingpage/icons/imessage.svg" alt="Text" width={24} height={24} />
              <span className="ml-3">Share via Text</span>
            </div>
            <div className="flex items-center">
              <Image src="/landingpage/icons/external link icon.svg" alt="External Link" width={24} height={24} />
            </div>
          </div>
          
          {/* Share via Email - Full button clickable */}
          <div 
            className="flex items-center justify-between p-3 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer"
            onClick={handleEmailShare}
          >
            <div className="flex items-center">
              <Image src="/landingpage/icons/mail.svg" alt="Email" width={24} height={24} />
              <span className="ml-3">Share via Email</span>
            </div>
            <div className="flex items-center">
              <Image src="/landingpage/icons/external link icon.svg" alt="External Link" width={24} height={24} />
            </div>
          </div>
          
          {/* Share via WhatsApp - Full button clickable */}
          <div 
            className="flex items-center justify-between p-3 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer"
            onClick={handleWhatsAppShare}
          >
            <div className="flex items-center">
              <Image src="/landingpage/icons/WhatsApp.svg" alt="WhatsApp" width={30} height={30} />
              <span className="ml-3">Share via WhatsApp</span>
            </div>
            <div className="flex items-center">
              <Image src="/landingpage/icons/external link icon.svg" alt="External Link" width={24} height={24} />
            </div>
          </div>
        </div>
        <button 
          onClick={onClose} 
          className="mt-4 w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition-colors"
        >
          Close
        </button>
      </div>
    </div>
  );
}
