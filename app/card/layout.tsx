import React from 'react';
import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Joseph Iannazzi | Process Server - Just Legal Solutions',
  description: 'Professional process server and legal support services by Joseph Iannazzi at Just Legal Solutions.',
  openGraph: {
    title: 'Joseph Iannazzi | Process Server',
    description: 'Professional process server and legal support services by Joseph Iannazzi at Just Legal Solutions.',
    images: [
      {
        url: 'https://justlegalsolutions.org/og-image.png', // Create this image (1200x630px)
        width: 1200,
        height: 630,
        alt: 'Joseph Iannazzi - Just Legal Solutions',
      },
    ],
    locale: 'en_US',
    type: 'website',
    siteName: 'Just Legal Solutions',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Joseph Iannazzi | Process Server',
    description: 'Professional process server and legal support services',
    images: ['https://justlegalsolutions.org/og-image.jpg'],
  }
};

export default function CardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
