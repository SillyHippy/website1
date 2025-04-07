"use client";

import React, { useState, useEffect } from 'react';
import Image from 'next/image';
import ShareModal from './ShareModal';

export default function CardPage() {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isResumeModalOpen, setIsResumeModalOpen] = useState(false);

  const handleShareClick = () => {
    console.log("Share icon clicked");
    setIsModalOpen(true);
  };

  const handleResumeClick = () => {
    setIsResumeModalOpen(true);
  };

  const handleCloseModal = () => {
    console.log("Modal closed");
    setIsModalOpen(false);
  };

  const handleCloseResumeModal = () => {
    setIsResumeModalOpen(false);
  };

  useEffect(() => {
    console.log("Modal state:", isModalOpen);
  }, [isModalOpen]);

  return (
    <div className="min-h-screen bg-[#0B132B] flex items-center justify-center p-4">
      <div className="bg-[#1C2541] rounded-[15px] p-6 max-w-[420px] w-full shadow-lg relative" itemScope itemType="https://schema.org/Person">
        {/* Share Icon - Added better click area */}
        <button 
          onClick={handleShareClick}
          className="absolute top-4 right-4 w-[30px] h-[30px] z-10 cursor-pointer border-none bg-transparent p-0"
          aria-label="Share"
        >
          <Image
            src="/landingpage/icons/share.svg"
            alt="Share"
            width={30}
            height={30}
            className="w-full h-full"
          />
        </button>
        {/* Top Profile Section */}
        <div className="flex items-center mb-4">
          <div className="w-[90px] h-[90px] bg-white rounded-full flex-shrink-0 mr-4 flex items-center justify-center overflow-hidden">
            <Image
              src="/favicon1.svg"
              alt="Just Legal Solutions Logo"
              width={90}
              height={90}
              className="w-full h-full object-contain"
              priority
            />
          </div>
          <div>
            <h2 className="text-[26px] font-bold text-white -mb-1" itemProp="worksFor">Just Legal Solutions</h2>
          </div>
        </div>

        {/* Info Section */}
        <div className="pl-1 mb-3">
          <h1 className="text-[28px] text-white mb-3" itemProp="name">Joseph Iannazzi</h1>
          <div className="flex flex-wrap gap-2">
            <span className="inline-block px-3 py-1.5 bg-[#374151] text-[#E5E7EB] rounded-full text-sm" itemProp="jobTitle">
              Process Server
            </span>
            <span className="inline-block px-3 py-1.5 bg-[#374151] text-[#E5E7EB] rounded-full text-sm">
              Legal Support
            </span>
          </div>
        </div>

        {/* Button Section */}
        <div className="space-y-3 mt-6">
          {/* Contact Details Button */}
          <a href="/contact-details.vcf" download className="relative flex items-center justify-between bg-[#2D3748] text-white p-5 rounded-lg hover:bg-[#4A5568] transition-colors group">
            <div className="absolute -left-5 top-1/2 -translate-y-1/2 w-[65px] h-[65px]">
              <Image
                src="/landingpage/icons/contact.svg"
                alt="Contact"
                width={65}
                height={65}
                className="w-full h-full"
              />
            </div>
            <span className="pl-12 text-base">Download my contact details</span>
            <span className="text-xl transition-transform group-hover:translate-x-1">→</span>
          </a>

          {/* Email Button */}
          <a href="mailto:Joseph@JustLegalSolutions.org" itemProp="email" className="relative flex items-center justify-between bg-[#2D3748] text-white p-5 rounded-lg hover:bg-[#4A5568] transition-colors group">
            <div className="absolute -left-5 top-1/2 -translate-y-1/2 w-[60px] h-[60px]">
              <Image
                src="/landingpage/icons/mail.svg"
                alt="Email"
                width={60}
                height={60}
                className="w-full h-full"
              />
            </div>
            <span className="pl-12 text-base">Email</span>
            <span className="text-xl transition-transform group-hover:translate-x-1">→</span>
          </a>

          {/* Phone Button */}
          <a href="tel:+15393676832" itemProp="telephone" className="relative flex items-center justify-between bg-[#2D3748] text-white p-5 rounded-lg hover:bg-[#4A5568] transition-colors group">
            <div className="absolute -left-5 top-1/2 -translate-y-1/2 w-[56px] h-[66px]">
              <Image
                src="/landingpage/icons/phone.svg"
                alt="Phone"
                width={56}
                height={66}
                className="w-full h-full"
              />
            </div>
            <span className="pl-12 text-base">Call</span>
            <span className="text-xl transition-transform group-hover:translate-x-1">→</span>
          </a>

          {/* Calendar Button */}
          <a href="/card/calendar" className="relative flex items-center justify-between bg-[#2D3748] text-white p-5 rounded-lg hover:bg-[#4A5568] transition-colors group">
            <div className="absolute -left-5 top-1/2 -translate-y-1/2 w-[62px] h-[62px]">
              <Image
                src="/landingpage/icons/calendar-reminder-icon.svg"
                alt="Calendar"
                width={62}
                height={62}
                className="w-full h-full"
              />
            </div>
            <span className="pl-12 text-base">Add a Reminder</span>
            <span className="text-xl transition-transform group-hover:translate-x-1">→</span>
          </a>

          {/* Website Button */}
          <a href="https://justlegalsolutions.org" itemProp="url" className="relative flex items-center justify-between bg-[#2D3748] text-white p-5 rounded-lg hover:bg-[#4A5568] transition-colors group">
            <div className="absolute -left-5 top-1/2 -translate-y-1/2 w-[65px] h-[65px]">
              <Image
                src="/landingpage/icons/website1.svg"
                alt="Website"
                width={65}
                height={65}
                className="w-full h-full"
              />
            </div>
            <span className="pl-12 text-base">Website</span>
            <span className="text-xl transition-transform group-hover:translate-x-1">→</span>
          </a>

          {/* LinkedIn Button */}
          <a
            href="https://www.linkedin.com/in/joseph-iannazzi"
            target="_blank"
            rel="noopener noreferrer"
            className="relative flex items-center justify-between bg-[#2D3748] text-white p-5 rounded-lg hover:bg-[#4A5568] transition-colors group"
          >
            <div className="absolute -left-5 top-1/2 -translate-y-1/2 w-[60px] h-[60px] flex items-center justify-center">
              <Image
                src="/landingpage/icons/linkedin.svg"
                alt="LinkedIn"
                width={60}
                height={60}
                className="w-full h-full"
              />
            </div>
            <span className="pl-12 text-base">LinkedIn</span>
            <span className="text-xl transition-transform group-hover:translate-x-1">→</span>
          </a>

          {/* Resume Button */}
          <button
            onClick={handleResumeClick}
            className="relative flex items-center justify-between bg-[#2D3748] text-white p-5 rounded-lg hover:bg-[#4A5568] transition-colors group w-full"
          >
            <div className="absolute -left-5 top-1/2 -translate-y-1/2 w-[60px] h-[60px]">
              <Image
                src="/landingpage/icons/document.svg"
                alt="Resume"
                width={60}
                height={60}
                className="w-full h-full"
              />
            </div>
            <span className="pl-12 text-base">View Resumes</span>
            <span className="text-xl transition-transform group-hover:translate-x-1">→</span>
          </button>
        </div>
      </div>
      <ShareModal isOpen={isModalOpen} onClose={handleCloseModal} />

      {/* Resume Modal */}
      {isResumeModalOpen && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 w-full max-w-[400px]">
            <h2 className="text-xl font-bold mb-4">Select a Resume</h2>
            <div className="space-y-3">
              <a
                href="/resumes/Project Manager - Resume.pdf"
                download
                className="block bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Project Manager Resume
              </a>
              <a
                href="/resumes/Process Server - Resume.pdf"
                download
                className="block bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Process Server Resume
              </a>
              <a
                href="/resumes/Payroll - Resume.pdf"
                download
                className="block bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Payroll Resume
              </a>
              <a
                href="/resumes/Office Assistant - Resume.pdf"
                download
                className="block bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Office Assistant Resume
              </a>
              <a
                href="/resumes/Legal Assistant - Resume.pdf"
                download
                className="block bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Legal Assistant Resume
              </a>
              <a
                href="/resumes/Human Resources Assistant - Resume.pdf"
                download
                className="block bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Human Resources Assistant Resume
              </a>
              <a
                href="/resumes/Executive Assistant - Resume.pdf"
                download
                className="block bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Executive Assistant Resume
              </a>
              <a
                href="/resumes/Administrative Assistant - Resume.pdf"
                download
                className="block bg-gray-100 p-3 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Administrative Assistant Resume
              </a>
            </div>
            <button
              onClick={handleCloseResumeModal}
              className="mt-4 w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition-colors"
            >
              Close
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
