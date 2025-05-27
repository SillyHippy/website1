import Image from 'next/image';
import Link from 'next/link';
import Script from 'next/script';
import { ChevronDown } from 'lucide-react';

export const metadata = {
  title: 'Business Solutions – Joseph Iannazzi | Executive Assistant',
  description: 'Professional solutions including executive assistance, event coordination, HR & payroll, and travel management.',
  keywords: ['executive assistant','event planning','HR payroll','travel management'],
  openGraph: { 
    title: 'Business Solutions – Joseph Iannazzi', 
    description: 'Professional solutions to meet your business needs',
    images: [{
      url: 'https://images.unsplash.com/photo-1558478551-1a378f63328e?q=80&w=1469&auto=format&fit=crop&ixlib=rb-4.1.0',
      width: 1200,
      height: 630,
      alt: 'Joseph Iannazzi Executive Assistant Services',
    }],
  },
  twitter: { 
    card: 'summary_large_image', 
    title: 'Business Solutions – Joseph Iannazzi', 
    description: 'Professional solutions to meet your business needs',
    images: ['https://images.unsplash.com/photo-1558478551-1a378f63328e?q=80&w=1469&auto=format&fit=crop&ixlib=rb-4.1.0'],
  }
};

export default function ServicesPage() {
  const services = [
    {
      title: 'Executive Support',
      description: 'Professional administrative assistance for executives, helping manage schedules, communications, and day-to-day operations.',
      image: 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=400&q=80',
    },
    {
      title: 'Event Coordination',
      description: 'End-to-end event planning and management services for corporate events, team building, and special occasions.',
      image: 'https://images.unsplash.com/photo-1529070538774-1843cb3265df?auto=format&fit=crop&w=400&q=80',
    },
    {
      title: 'HR & Payroll',
      description: 'Comprehensive human resources support including recruitment, employee management, and payroll processing.',
      image: 'https://images.unsplash.com/photo-1554224154-22dec7ec8818?auto=format&fit=crop&w=400&q=80',
    },
    {
      title: 'Travel Management',
      description: 'Business travel planning, booking, and expense management to optimize your corporate travel experience.',
      image: 'https://images.unsplash.com/photo-1507149833265-60c372daea22?auto=format&fit=crop&w=400&q=80',
    },
  ];

  return (
    <>
      <Script id="services-schema" type="application/ld+json">
        {`
          {
            "@context": "https://schema.org",
            "@type": "ItemList",
            "itemListElement": [
              {
                "@type": "Service",
                "position": 1,
                "name": "Executive Support",
                "description": "Professional administrative assistance for executives, helping manage schedules, communications, and day-to-day operations."
              },
              {
                "@type": "Service",
                "position": 2,
                "name": "Event Coordination",
                "description": "End-to-end event planning and management services for corporate events, team building, and special occasions."
              },
              {
                "@type": "Service",
                "position": 3,
                "name": "HR & Payroll",
                "description": "Comprehensive human resources support including recruitment, employee management, and payroll processing."
              },
              {
                "@type": "Service",
                "position": 4,
                "name": "Travel Management",
                "description": "Business travel planning, booking, and expense management to optimize your corporate travel experience."
              }
            ]
          }
        `}
      </Script>
      <main>
        {/* Hero Section - Updated */}
        <section className="relative w-full h-[80vh] flex items-center justify-center">
          <div 
            className="absolute inset-0 z-0 bg-cover bg-center" 
            style={{
              backgroundImage: 'url(https://images.unsplash.com/photo-1558478551-1a378f63328e?q=80&w=1469&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D)',
            }}
          >
            <div className="absolute inset-0 bg-black/40"></div>
          </div>
          
          <div className="relative z-10 text-center text-white px-4 max-w-4xl">
            <h1 className="text-4xl md:text-5xl font-bold mb-4">Business Solutions</h1>
            <p className="text-xl md:text-2xl mb-4">Professional solutions to meet your business needs</p>
            <p className="font-medium text-lg mb-4">10+ years supporting C-suite executives with precision & discretion.</p>
            <p className="text-yellow-300 font-semibold mb-8">Prices starting at $15/hr — custom quotes available</p>
            <Link 
              href="/resume.pdf" 
              className="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              target="_blank"
              aria-label="Download Resume"
            >
              Download Resume
            </Link>
          </div>
          
          <div className="absolute bottom-8 animate-bounce">
            <ChevronDown className="h-8 w-8 text-white" />
          </div>
        </section>

        {/* Pricing Banner */}
        <div className="text-center py-6 bg-gray-50 text-gray-700 font-medium">
          Prices starting at $15/hr — custom quotes available
        </div>

        {/* Why Work With Me Section */}
        <section className="container mx-auto py-12 px-4">
          <h2 className="text-3xl font-bold mb-8 text-center">Why Work With Me?</h2>
          <div className="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="flex items-start gap-3">
              <div className="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <span className="text-blue-600 font-bold">•</span>
              </div>
              <div>
                <h3 className="font-semibold text-lg">Strategic calendar & travel management</h3>
                <p className="text-gray-600">Optimizing executive time and ensuring seamless travel experiences.</p>
              </div>
            </div>
            <div className="flex items-start gap-3">
              <div className="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <span className="text-blue-600 font-bold">•</span>
              </div>
              <div>
                <h3 className="font-semibold text-lg">HR systems & payroll implementation</h3>
                <p className="text-gray-600">Setting up efficient systems for human resources and payment processing.</p>
              </div>
            </div>
            <div className="flex items-start gap-3">
              <div className="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <span className="text-blue-600 font-bold">•</span>
              </div>
              <div>
                <h3 className="font-semibold text-lg">End-to-end event planning</h3>
                <p className="text-gray-600">Comprehensive event coordination from concept to execution.</p>
              </div>
            </div>
            <div className="flex items-start gap-3">
              <div className="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <span className="text-blue-600 font-bold">•</span>
              </div>
              <div>
                <h3 className="font-semibold text-lg">Confidential communications & discretion</h3>
                <p className="text-gray-600">Handling sensitive information with the highest level of professionalism.</p>
              </div>
            </div>
          </div>
        </section>

        {/* Executive Support Philosophy */}
        <section className="bg-white py-12">
          <div className="max-w-3xl mx-auto text-center space-y-4">
            <h2 className="text-2xl font-bold">My Executive Support Philosophy</h2>
            <p className="text-gray-700">
              I believe that true partnership is built on anticipation, discretion, and proactive communication. By immersing myself in your priorities—whether it's complex calendar orchestration, confidential project management, or last-minute travel pivots—I deliver the seamless support every C-suite leader deserves.
            </p>
            <ul className="flex flex-col sm:flex-row justify-center gap-6 mt-6 text-gray-600">
              <li className="flex items-start space-x-2">
                <span className="h-2 w-2 bg-blue-600 rounded-full mt-2" />
                <span>Anticipate needs before they arise</span>
              </li>
              <li className="flex items-start space-x-2">
                <span className="h-2 w-2 bg-blue-600 rounded-full mt-2" />
                <span>Maintain strict confidentiality</span>
              </li>
              <li className="flex items-start space-x-2">
                <span className="h-2 w-2 bg-blue-600 rounded-full mt-2" />
                <span>Communicate clearly & proactively</span>
              </li>
              <li className="flex items-start space-x-2">
                <span className="h-2 w-2 bg-blue-600 rounded-full mt-2" />
                <span>Adapt quickly to changing priorities</span>
              </li>
            </ul>
          </div>
        </section>

        {/* Services Grid - Kept Unchanged */}
        <section className="container mx-auto py-16 px-4">
          <h2 className="text-3xl font-bold mb-12 text-center">Our Professional Services</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {services.map((service, index) => (
              <article key={index} className="bg-white rounded-lg shadow-lg overflow-hidden">
                <div className="h-48 relative">
                  <Image 
                    src={service.image} 
                    alt={`${service.title} service illustration`}
                    fill
                    sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 25vw"
                    style={{ objectFit: 'cover' }}
                    priority={index < 2}
                  />
                </div>
                <div className="p-6">
                  <h3 className="text-xl font-bold mb-2">{service.title}</h3>
                  <p className="text-gray-600">{service.description}</p>
                </div>
              </article>
            ))}
          </div>
        </section>
      </main>
    </>
  );
}
