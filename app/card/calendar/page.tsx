"use client";

import React, { useState } from 'react';
import { useRouter } from 'next/navigation';
import Image from 'next/image';

export default function CalendarPage() {
  const router = useRouter();
  const [date, setDate] = useState('');
  const [time, setTime] = useState('');
  
  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    
    if (!date || !time) {
      alert("Please select both date and time");
      return;
    }
    
    // Create date objects for the selected date and time
    const selectedDateTime = new Date(`${date}T${time}`);
    const endDateTime = new Date(selectedDateTime.getTime() + 30 * 60000); // Fixed 30 minutes
    
    // Format dates for ICS file (in UTC format)
    const formatDateForICS = (date: Date) => {
      return date.toISOString().replace(/-|:|\.\d+/g, '');
    };
    
    const startDate = formatDateForICS(selectedDateTime);
    const endDate = formatDateForICS(endDateTime);
    const now = formatDateForICS(new Date());
    
    // Generate ICS content
    const icsContent = `BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Just Legal Solutions//Joseph Iannazzi//EN
CALSCALE:GREGORIAN
METHOD:PUBLISH
BEGIN:VEVENT
SUMMARY:Appointment with Just Legal Solutions
DESCRIPTION:Process Server and Legal Support Services
DTSTART:${startDate}
DTEND:${endDate}
DTSTAMP:${now}
ORGANIZER;CN=Joseph Iannazzi:MAILTO:Joseph@JustLegalSolutions.org
URL:https://justlegalsolutions.org
STATUS:CONFIRMED
END:VEVENT
END:VCALENDAR`;
    
    // Create blob and trigger download
    const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'JustLegalSolutions-appointment.ics');
    document.body.appendChild(link);
    link.click();
    
    // Clean up and redirect back to card page
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    // Redirect after a short delay to ensure download starts
    setTimeout(() => {
      router.push('/card');
    }, 500);
  };
  
  return (
    <div className="min-h-screen bg-gradient-to-br from-[#0A0F1C] via-[#121D34] to-[#0A0F1C] flex items-center justify-center p-4">
      <div className="bg-[#1B2738] rounded-[24px] p-8 max-w-[480px] w-full shadow-[0_20px_50px_rgba(0,0,0,0.8)] relative border border-[#2A3653]">
        <button 
          onClick={() => router.push('/card')}
          className="absolute top-4 left-4 text-white p-2 rounded-full hover:bg-[#2A3B58] transition-all"
          aria-label="Back"
        >
          ← Back
        </button>
        
        <div className="text-center mb-8 pt-8">
          <h1 className="text-[28px] text-white font-bold mb-2">Schedule an Appointment</h1>
          <p className="text-[#CBD5E1]">Select a date and time for your appointment</p>
        </div>
        
        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label htmlFor="date" className="block text-[#CBD5E1] mb-2">Date</label>
            <input 
              id="date"
              type="date" 
              value={date} 
              onChange={(e) => setDate(e.target.value)} 
              required
              className="w-full p-3 bg-[#2A3B58] rounded-[12px] text-white border border-[#3A4B68] focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          
          <div>
            <label htmlFor="time" className="block text-[#CBD5E1] mb-2">Time</label>
            <input 
              id="time"
              type="time" 
              value={time} 
              onChange={(e) => setTime(e.target.value)} 
              required
              className="w-full p-3 bg-[#2A3B58] rounded-[12px] text-white border border-[#3A4B68] focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          
          <button 
            type="submit" 
            className="relative flex items-center justify-center bg-[#1B2738] text-white h-[64px] px-8 rounded-[16px] hover:bg-[#263448] transition-all duration-200 group shadow-[0_6px_20px_rgba(0,0,0,0.4)] w-full mt-8"
          >
            <div
              className="absolute top-1/2 transform -translate-y-1/2 flex items-center justify-center bg-[#2A3B58] rounded-full overflow-hidden"
              style={{
                width: "48px",
                height: "48px",
                left: "16px",
                boxShadow: "0px 6px 12px rgba(0, 0, 0, 0.4)"
              }}
            >
              <Image
                src="/landingpage/icons/calendar-reminder-icon.svg"
                alt="Calendar"
                width={48}
                height={48}
                className="object-cover"
                style={{ transform: "scale(150%)" }}
              />
            </div>
            <span className="text-base font-medium text-white/90 group-hover:text-white pl-[56px]">
              Add to Calendar
            </span>
            <span className="absolute right-4 text-xl font-bold text-white/70 group-hover:text-white transition-transform group-hover:translate-x-1">
              →
            </span>
          </button>
        </form>
      </div>
    </div>
  );
}