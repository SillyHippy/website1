import React from 'react';
import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Service Pricing & Packages',
  description: 'Transparent pricing for process serving, document delivery, and legal support services. Competitive rates with standard, rush, and same-day service options.',
  openGraph: {
    title: 'Service Pricing & Packages | Just Legal Solutions',
    description: 'Transparent pricing for process serving, document delivery, and legal support services. Competitive rates with standard, rush, and same-day service options.'
  }
};

export default function PricingPage() {
  return (
    <main className="min-h-screen bg-white">
      {/* Partner Section */}
<section className="py-16 px-4 bg-gray-100 rounded-lg shadow-md">
  <div className="max-w-7xl mx-auto text-center">
    <h2 className="text-3xl font-bold mb-4">JLS Wants to Partner with You</h2>
    <p className="text-gray-700 text-lg font-medium max-w-4xl mx-auto">
      <strong>If you don't see exactly what you're looking for</strong> in our service offerings, have a particularly complex or unique situation, or <strong>need more competitive pricing</strong>, we encourage you to reach out <strong>before exploring alternatives.</strong> We're happy to discuss your specific needs and create tailored solutions that work for you. <span className="text-blue-600 font-semibold"> Bulk and volume pricing available upon request</span>—contact us directly to learn more!
    </p>
  </div>
</section>

      {/* Service Packages */}
      <section className="py-12 px-4 bg-gray-50">
        <div className="max-w-7xl mx-auto">
          <h2 className="text-2xl font-bold text-center mb-4">Service Packages</h2>
          <p className="text-center text-gray-600 mb-12">
            Explore our competitive pricing plans tailored for your legal needs. 60 Dollars per Service, 30 Miles Included, 70 Cents per Mile Over
          </p>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {/* Standard Service */}
            <div className="bg-white p-6 rounded-lg shadow-md">
              <h3 className="text-xl font-bold mb-4">Standard Service</h3>
              <p className="text-3xl font-bold mb-4">$60</p>
              <p className="text-gray-600 mb-4">Ideal for routine document delivery.</p>
              <p className="text-sm text-gray-500">
                First Service Attempt Within 7 Business Days (usually sooner)
              </p>
            </div>

            {/* Rush Service */}
            <div className="bg-white p-6 rounded-lg shadow-md">
              <h3 className="text-xl font-bold mb-4">Rush Service</h3>
              <p className="text-3xl font-bold mb-4">$100</p>
              <p className="text-gray-600 mb-4">For urgent demands that need immediate attention.</p>
              <p className="text-sm text-gray-500">
                A JLS Agent Will Attempt Service Within 72 Hours or Sooner based on availability
              </p>
            </div>

            {/* Same Day Service */}
            <div className="bg-white p-6 rounded-lg shadow-md">
              <h3 className="text-xl font-bold mb-4">Same Day Service</h3>
              <p className="text-3xl font-bold mb-4">$150</p>
              <p className="text-gray-600 mb-4">For urgent time-critical matters.</p>
              <p className="text-sm text-gray-500">
                A JLS Agent Will Attempt Service Within 24 Hours or Sooner based on availability
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Additional Services */}
      <section className="py-16 px-4">
        <div className="max-w-7xl mx-auto">
          <h2 className="text-2xl font-bold mb-8">Additional and Optional Services</h2>
          <div className="space-y-6">
            <div>
              <h3 className="font-bold mb-2">SAME-DAY/RUSH:</h3>
              <p className="text-gray-600">This specific service, which requires prompt action, requires an additional fee. This charge is strictly applied when you or your client requests that the respondent be served on the same day we receive your court documents or, alternatively, within a specific timeframe of 24 hours (in circumstances where a Special Service is required, we will proceed to serve the respondent within a reasonable period).</p>
            </div>

            <div>
              <h3 className="font-bold mb-2">35 MILES INCLUDED:</h3>
              <p className="text-gray-600">As part of our service fee, the first 35 miles of travel are included. If travel beyond this 35-mile range becomes necessary, it will be charged at the rate of $0.70 per mile for the excess distance.</p>
            </div>

            <div>
              <h3 className="font-bold mb-2">$60 PER SERVICE:</h3>
              <p className="text-gray-600">The foundational fee for a single service starts at $60. This fee covers not only the effective service of documents in a timely manner but also includes the processing and filing of the original Return of Service Affidavit directly to you if you wish to handle the filing yourself. We will file the Return of Service Affidavit with the court for an additional fee of $35.00 plus the court&apos;s filing fee. We fully appreciate that you may have multiple addresses pertaining to a single respondent. In such cases, if those addresses are located within a close proximity (specifically within a 20-mile radius), we will attempt service at both addresses on the same day under the same service fee of $60.00.</p>
            </div>

            <div>
              <h3 className="font-bold mb-2">PRINTING OF DOCUMENTS:</h3>
              <p className="text-gray-600">Should you choose to email us your documents that need serving, we offer to print the first 10 pages at no charge. Any pages exceeding this limit will incur a fee of $0.20 per page.</p>
            </div>

            <div>
              <h3 className="font-bold mb-2">SET TIME OF SERVICE:</h3>
              <p className="text-gray-600">If you require that the service occurs at a predetermined specific time and date, this request can be treated similarly to a RUSH and may incur an additional fee. This extra charge will be implemented only if you or your client specifically mandate that the respondent MUST be served at that designated date and time. If no specific time is requested, we will execute the service within a reasonable period.</p>
            </div>

            <div>
              <h3 className="font-bold mb-2">EVASIVE RESPONDENT:</h3>
              <p className="text-gray-600">Staking out an evasive respondent will incur a $90.00 per hour additional fee with a one hour minimum. Again this is an additional fee and will only be used at your request.</p>
            </div>

            <div>
              <h3 className="font-bold mb-2">MULTIPLE SERVICES AT SAME ADDRESS:</h3>
              <p className="text-gray-600">If more than one person needs to be served at an address (i.e. husband and wife) then it is only $30.00 per extra person served.</p>
            </div>

            <div>
              <h3 className="font-bold mb-2">FILING AFFIDAVITS:</h3>
              <p className="text-gray-600">If court is in Tulsa County, PPS can file at the courthouse for you for additional $35 (other counties will be an additional charge and discussed with client beforehand). If you would like this additional service, please discuss when setting up your process service. Otherwise the original Return of Service Affidavit will be mailed to you with the invoice once completed.</p>
            </div>

            <div>
              <h3 className="font-bold mb-2">POSTING AFFIDAVITS:</h3>
              <p className="text-gray-600">If you require us to post an affidavit and not personally serve the respondent, such as an eviction notice, then this service will cost $50.00.</p>
            </div>

            <div>
              <h3 className="font-bold mb-2">CERTIFIED MAILING OF AFFIDAVITS:</h3>
              <p className="text-gray-600">If you require us to send the affidavit to the respondent via certified mail, then this service incurs a fee of $35.00 plus the price of the certified mail. This fee is additional to any other services we provide to you.</p>
            </div>

            <div>
              <h3 className="font-bold mb-2">SKIP TRACING SERVICES:</h3>
              <p className="text-gray-600">Skip tracing services can be provided at an additional fee discussed beforehand.</p>
            </div>
          </div>
        </div>
      </section>

      {/* Non-Service Policy */}
      <section className="py-16 px-4 bg-gray-50">
        <div className="max-w-7xl mx-auto">
          <h2 className="text-2xl font-bold mb-8">Non-Service Policy</h2>
          <p className="text-gray-600 mb-8">
            While we strive to ensure the completion of our tasks swiftly and effectively, yet there may be instances when serving a respondent proves to be impossible for various reasons beyond our control. These reasons may include incorrect addresses provided to us, the respondent having moved, or instances in which the individual is actively evading service for various reasons, to name just a few. It is important to note that if we dedicate our time and efforts to serving a respondent, but ultimately are unsuccessful, we will still charge the flat fee of $60.00 for the service attempt. We pride ourselves on our high success rates, but we feel it is important to clearly communicate this potential issue to you in advance.
          </p>
          
          <h3 className="text-xl font-bold mb-4">Due Diligence Policy</h3>
          <p className="text-gray-600">
            At Just Legal Solutions, we adhere to Oklahoma's due diligence standards to ensure proper service of process. While Oklahoma law does not specify an exact number of attempts required for due diligence, it emphasizes that service must be carried out with reasonable efforts to serve the defendant personally before considering alternative methods. Our process servers make multiple attempts at serving the individual at different times of the day and on different days of the week to demonstrate a thorough effort to effectuate personal service.
          </p>
        </div>
      </section>
    </main>
  );
}
