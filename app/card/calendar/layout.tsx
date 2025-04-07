"use client";

import React from 'react';
import { Toaster } from '@/components/ui/toaster';

export default function CalendarLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <>
      {children}
      <Toaster />
    </>
  );
}