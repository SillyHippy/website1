"use client";

import React from 'react';
import { CreditCard, DollarSign } from 'lucide-react';
import Link from 'next/link';

export default function PaymentsPage() {
  return (
    <main className="min-h-screen pt-24 pb-16 px-4">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <h1 className="text-4xl font-bold text-center mb-4">Payment Options</h1>
        <p className="text-lg text-center text-gray-700 mb-16">
          Please choose an option below to pay. Payment in advance is required for non-established clients.
        </p>

        {/* Payment Options Grid */}
        <div className="grid md:grid-cols-2 gap-12">
          {/* Cash/Check Option */}
          <div className="text-center" itemScope itemType="https://schema.org/PaymentMethod">
            <div className="mb-6">
              <DollarSign className="w-16 h-16 mx-auto text-gray-700" />
            </div>
            <h2 className="text-2xl font-bold mb-4" itemProp="name">Cash, Check or Money Order</h2>
            <p className="text-gray-700" itemProp="description">
              Checks and money orders can be made out to and mailed to: Joseph Iannazzi,
              <br />
              <span itemProp="address">564 E 138th Pl, Glenpool, OK 74033</span>.
            </p>
          </div>

          {/* Electronic Payment Option */}
          <div className="text-center" itemScope itemType="https://schema.org/PaymentMethod">
            <div className="mb-6">
              <CreditCard className="w-16 h-16 mx-auto text-gray-700" />
            </div>
            <h2 className="text-2xl font-bold mb-4" itemProp="name">Electronic Payment</h2>
            <p className="text-gray-700 mb-2" itemProp="description">
              To pay online{' '}
              <Link href="https://buy.stripe.com/3cs17SbHS6h95nGaEE" className="text-blue-600 hover:text-blue-800">
                Click here
              </Link>
              . Be sure to enter your case number with the payment.
            </p>
            <p className="text-gray-700">
              If you are not sure how much to pay, please contact us first.
            </p>
          </div>
        </div>
      </div>
    </main>
  );
}