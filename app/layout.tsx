import './globals.css';
import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import Script from 'next/script';
import { organizationSchema } from '@/components/ui/schema';

const inter = Inter({ subsets: ['latin'] });

export const metadata: Metadata = {
  metadataBase: new URL('https://justlegalsolutions.org'),
  title: {
    template: '%s | Just Legal Solutions',
    default: 'Just Legal Solutions - Professional Process Serving in Oklahoma'
  },
  description: 'Expert process serving, secure document delivery, court transfers, and skip tracing services in Oklahoma. Fast, reliable, and professional legal support services.',
  keywords: ['process server', 'legal services', 'document delivery', 'court transfers', 'skip tracing', 'Oklahoma process server', 'legal support', 'same day service'],
  authors: [{ name: 'Joseph Iannazzi' }],
  creator: 'Just Legal Solutions',
  publisher: 'Just Legal Solutions',
  formatDetection: {
    email: false,
    address: false,
    telephone: false,
  },
  icons: {
    icon: [
      { url: '/Favicon/favicon.ico' },
      { url: '/Favicon/favicon-96x96.png', sizes: '96x96', type: 'image/png' }
    ],
    apple: [
      { url: '/Favicon/apple-touch-icon.png' }
    ],
    other: [
      {
        rel: 'mask-icon',
        url: '/Favicon/favicon.svg',
        color: '#0B132B'
      }
    ]
  },
  manifest: '/Favicon/site.webmanifest',
  openGraph: {
    type: 'website',
    locale: 'en_US',
    url: 'https://justlegalsolutions.org',
    title: 'Just Legal Solutions - Professional Process Serving in Oklahoma',
    description: 'Expert process serving, secure document delivery, court transfers, and skip tracing services in Oklahoma. Fast, reliable, and professional legal support services.',
    siteName: 'Just Legal Solutions',
    images: [
      {
        url: '/images/jls-logo.webp',
        width: 1200,
        height: 630,
        alt: 'Just Legal Solutions Logo'
      }
    ]
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Just Legal Solutions - Professional Process Serving in Oklahoma',
    description: 'Expert process serving, secure document delivery, court transfers, and skip tracing services in Oklahoma.',
    images: ['/images/jls-logo.webp']
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      'max-video-preview': -1,
      'max-image-preview': 'large',
      'max-snippet': -1,
    },
  },
  verification: {
    google: 'your-google-verification-code', // Replace with actual code
  }
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en" suppressHydrationWarning>
      <head>
        <link rel="canonical" href="https://justlegalsolutions.org" />
        <link rel="preload" href="/images/hero.webp" as="image" />
        <Script
          id="schema-org"
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(organizationSchema) }}
        />
      </head>
      <body className={inter.className} suppressHydrationWarning>
        {children}
      </body>
    </html>
  );
}