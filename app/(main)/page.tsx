import { Button } from "@/components/ui/button";
import { Mail, Phone, MapPin, Truck, FileText, Building2, Clock } from "lucide-react";
import Link from "next/link";
import Image from "next/image";
import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Process Server Tulsa | Legal Support Services | Just Legal Solutions',
  description: 'Need a reliable process server in Tulsa, OK? Just Legal Solutions offers fast, efficient process serving statewide, including rush & same-day service.',
  openGraph: {
    // 👇 Updated to match main title
    title: 'Process Server Tulsa | Legal Support Services | Just Legal Solutions',
    // 👇 Updated to match main description
    description: 'Need a reliable process server in Tulsa, OK? Just Legal Solutions offers fast, efficient process serving statewide, including rush & same-day service.'
  }
};

export default function Home() {
  return (
    <main className="min-h-screen pt-16">
      {/* Hero Section */}
      <section className="relative h-[calc(100vh-4rem)]" aria-label="Hero section">
        <div className="absolute inset-0">
          <Image
            src="/images/hero.webp"
            alt="Professional legal services office with modern workspace and legal documents"
            className="object-cover"
            fill
            priority
            sizes="100vw"
            quality={90}
          />
          <div className="absolute inset-0 bg-black/50" />
        </div>
        <div className="relative h-full flex flex-col items-center justify-center text-white text-center px-4">
          <h1 className="text-4xl md:text-5xl font-bold mb-6">Efficient Process Serving Solutions</h1>
          <p className="text-lg md:text-xl max-w-2xl">
            At Just Legal Solutions, we offer comprehensive process serving services
            tailored to your needs. Whether it's routine, rush, or same-day service, we
            ensure Statewide coverage at reasonable rates.
          </p>
        </div>
      </section>

      {/* Services Grid */}
      <section className="bg-black text-white py-16" aria-label="Our services">
        <div className="max-w-7xl mx-auto px-4">
       
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div className="text-center">
              <div className="aspect-w-16 aspect-h-9 mb-4 relative h-64">
                <Image
                  src="/images/secure-delivery.webp"
                  alt="Professional courier delivering legal documents securely"
                   className="rounded object-cover shadow-[0_0_10px_rgba(255,255,255,0.5)]"
                  fill
                  sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 25vw"
                  loading="lazy"
                  style={{ objectFit: 'cover', objectPosition: '50% 30%' }} // Adjust the position of the image // Adjust the fit and position of the image
                />
              </div>
              <h3 className="text-xl font-bold mb-2">Secure Document Delivery</h3>
              <p className="text-gray-300">
                Our secure document delivery ensures your documents arrive safely and on time, professionally and confidentially.
              </p>
            </div>
            <div className="text-center">
              <div className="aspect-w-16 aspect-h-9 mb-4 relative h-64">
                <Image
                  src="/images/court-transfer.webp"
                  alt="Court document transfer service"
                  className="rounded object-cover shadow-[0_0_10px_rgba(255,255,255,0.5)]"
                  fill
                  sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 25vw"
                  loading="lazy"
                />
              </div>
              <h3 className="text-xl font-bold mb-2">Court-Run Transfers</h3>
              <p className="text-gray-300">
                We specialize in court-run document transfers, ensuring your files reach their destination without delay.
              </p>
            </div>
            <div className="text-center">
              <div className="aspect-w-16 aspect-h-9 mb-4 relative h-64">
                <Image
                  src="/images/skip-trace.webp"
                  alt="Skip tracing and investigation services"
                  className="rounded object-cover shadow-[0_0_10px_rgba(255,255,255,0.5)]"
                  fill
                  sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 25vw"
                  loading="lazy"
                />
              </div>
              <h3 className="text-xl font-bold mb-2">Skip Tracing</h3>
              <p className="text-gray-300">
                Our skip trace service helps locate hard-to-find individuals efficiently and cost-effectively.
              </p>
            </div>
            <div className="text-center">
              <div className="aspect-w-16 aspect-h-9 mb-4 relative h-64">
                <Image
                  src="/images/same-day.webp"
                  alt="Same day delivery service"
                  className="rounded object-cover shadow-[0_0_10px_rgba(255,255,255,0.5)]"
                  fill
                  sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 25vw"
                  loading="lazy"
                />
              </div>
              <h3 className="text-xl font-bold mb-2">Same-Day Delivery</h3>
              <p className="text-gray-300">
                Get your time-sensitive documents delivered quickly with our reliable same-day service.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Secure Courier Services */}
      <section id="courier-services" className="relative py-24">
        <div className="absolute inset-0">
          <Image
            src="/images/courier-bg.webp"
            alt="Legal office environment"
            className="object-cover"
            fill
            sizes="100vw"
            loading="lazy"
          />
          <div className="absolute inset-0 bg-black/60" />
        </div>
        <div className="relative max-w-7xl mx-auto px-4 text-white">
          <h2 className="text-4xl font-bold text-center mb-16">Secure Courier Services</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="text-center">
              <Clock className="w-12 h-12 mx-auto mb-4" />
              <h3 className="text-xl font-bold mb-2">Same-Day Delivery</h3>
              <p>When time is critical, our same-day delivery service ensures your documents reach their destination quickly.</p>
            </div>
            <div className="text-center">
              <FileText className="w-12 h-12 mx-auto mb-4" />
              <h3 className="text-xl font-bold mb-2">Secure Handling</h3>
              <p>Our secure handling procedures ensure your sensitive materials are protected throughout the delivery process.</p>
            </div>
            <div className="text-center">
              <Building2 className="w-12 h-12 mx-auto mb-4" />
              <h3 className="text-xl font-bold mb-2">Electronic Filing Assistance</h3>
              <p>Let our experienced staff handle your electronic court filings efficiently and accurately.</p>
            </div>
          </div>
        </div>
      </section>
    </main>
  );
}
