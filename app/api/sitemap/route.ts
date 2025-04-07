import { NextResponse } from 'next/server';

export async function GET() {
  const baseUrl = 'https://justlegalsolutions.org';
  const lastModified = new Date().toISOString();

  return NextResponse.json([
    {
      url: baseUrl,
      lastModified,
      changeFrequency: 'weekly',
      priority: 1,
    },
    {
      url: `${baseUrl}/pricing`,
      lastModified,
      changeFrequency: 'monthly',
      priority: 0.8,
    },
    {
      url: `${baseUrl}/payments`,
      lastModified,
      changeFrequency: 'monthly',
      priority: 0.8,
    },
    {
      url: `${baseUrl}/card`,
      lastModified,
      changeFrequency: 'monthly',
      priority: 0.7,
    },
  ]);
}