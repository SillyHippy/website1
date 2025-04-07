import { NextResponse } from 'next/server';

export async function GET() {
  return NextResponse.json({
    rules: {
      userAgent: '*',
      allow: '/',
      disallow: ['/security-policy'],
    },
    sitemap: 'https://justlegalsolutions.org/sitemap.xml',
  });
}