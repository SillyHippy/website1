"use client";

import React, { useState } from "react";
import Image from "next/image";
import ShareModal from "./ShareModal";

export default function CardPage() {
  const [isModalOpen, setIsModalOpen] = useState(false);

  const handleShareClick = () => setIsModalOpen(true);
  const handleCloseModal = () => setIsModalOpen(false);

  return (
    <div className="min-h-screen bg-gradient-to-br from-[#0A0F1C] via-[#121D34] to-[#0A0F1C] flex items-center justify-center p-4 relative">
      {/* Light effect */}
      <div className="absolute top-[-100px] left-[-100px] w-[500px] h-[500px] bg-gradient-radial from-white/20 to-transparent rounded-full blur-[200px] opacity-50"></div>

      <div className="bg-[#1B2738] rounded-[24px] p-8 max-w-[420px] w-full shadow-[0_20px_50px_rgba(0,0,0,0.8)] backdrop-blur-md relative border border-[#2A3653]">
        {/* Share Icon */}
        <button
          onClick={handleShareClick}
          className="absolute top-4 right-4 flex items-center justify-center bg-primary text-primary-foreground hover:bg-primary/80 rounded-md px-3 py-2 text-sm font-medium transition-colors z-10 cursor-pointer shadow-sm"
          aria-label="Share"
        >
          <Image
            src="/landingpage/icons/share.svg"
            alt="Share"
            width={20}
            height={20}
            className="mr-1"
          />
          <span>Share</span>
        </button>

        {/* Top Profile Section */}
        <div className="flex items-center mb-6">
          <div className="w-[90px] h-[90px] bg-white rounded-full flex-shrink-0 mr-4 flex items-center justify-center overflow-hidden border-[1px] border-gray-100">
            <Image
              src="/favicon1.svg"
              alt="Just Legal Solutions Logo"
              width={90}
              height={90}
              className="w-full h-full object-cover"
              priority
            />
          </div>
          <div>
            <h2 className="text-[22px] font-semibold text-white leading-tight">
              Just Legal Solutions
            </h2>
          </div>
        </div>

        {/* Info Section */}
        <div className="pl-1 mb-6">
          <h1 className="text-[28px] text-white font-bold mb-4">Joseph Iannazzi</h1>
          <div className="flex flex-wrap gap-3">
  <span className="inline-block px-4 py-2 bg-[#2A3B58] text-[#CBD5E1] rounded-full text-sm shadow-sm">
    Process Server
  </span>
  <span className="inline-block px-4 py-2 bg-[#2A3B58] text-[#CBD5E1] rounded-full text-sm shadow-sm">
    Executive Support
  </span>
  <span className="inline-block px-4 py-2 bg-[#2A3B58] text-[#CBD5E1] rounded-full text-sm shadow-sm">
    Business Solutions
  </span>
  <span className="inline-block px-4 py-2 bg-[#2A3B58] text-[#CBD5E1] rounded-full text-sm shadow-sm">
    Legal Support
  </span>
</div>
        </div>

        {/* Button Section */}
        <div className="space-y-4">
          {[
            {
              href: "/contact-details.vcf",
              icon: "/landingpage/icons/contact.svg",
              label: "Download my contact details",
              download: true,
              customSize: "120%" // Custom size for this icon
            },
            {
              href: "mailto:Joseph@JustLegalSolutions.org",
              icon: "/landingpage/icons/mail.svg",
              label: "Email",
              customSize: "110%" // Custom size for this icon
            },
            {
              href: "tel:+15393676832",
              icon: "/landingpage/icons/phone.svg",
              label: "Call",
              customSize: "120%" // Custom size for this icon
            },
            {
              href: "/card/calendar", // Changed from /reminder.ics to /card/calendar
              icon: "/landingpage/icons/calendar-reminder-icon.svg",
              label: "Add a Reminder",
              download: false, // Changed from true to false to navigate instead of download
              customSize: "150%" // Custom size for this icon
            },
            {
              href: "https://justlegalsolutions.org",
              icon: "/landingpage/icons/website1.svg",
              label: "Website",
              customSize: "140%" // Custom size for this icon
            }
          ].map(({ href, icon, label, download, customSize }) => (
            <a
              key={label}
              href={href}
              {...(download ? { download: true } : {})}
              className="relative flex items-center bg-[#1B2738] text-white h-[64px] px-8 rounded-[16px] hover:bg-[#263448] transition-all duration-200 group shadow-[0_6px_20px_rgba(0,0,0,0.4)]"
            >
              <div
                className="absolute top-1/2 transform -translate-y-1/2 flex items-center justify-center bg-[#2A3B58] rounded-full overflow-hidden"
                style={{
                  width: "48px",
                  height: "48px",
                  left: "-16px",
                  boxShadow: "0px 6px 12px rgba(0, 0, 0, 0.4)" // Add shadow for floating effect
                }}
              >
                <Image
                  src={icon}
                  alt={label}
                  width={48}
                  height={48}
                  className="object-cover"
                  style={{
                    transform: `scale(${customSize})` // Keep the custom size
                  }}
                />
              </div>
              <span className="text-base font-medium text-white/90 group-hover:text-white pl-[56px]">
                {label}
              </span>
              <span className="absolute right-4 text-xl font-bold text-white/70 group-hover:text-white transition-transform group-hover:translate-x-1">
                →
              </span>
            </a>
          ))}
        </div>
      </div>
      <ShareModal isOpen={isModalOpen} onClose={handleCloseModal} />
    </div>
  );
}
