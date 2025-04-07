"use client";

import { useState } from "react";

export function Footer() {
  const [showForm, setShowForm] = useState(false);

  const toggleForm = () => {
    setShowForm((prev) => !prev);
  };

  return (
    <footer className="bg-white" id="contact">
      <div className="max-w-7xl mx-auto px-4 py-12">
        <h2 className="text-3xl font-bold mb-6">GET IN TOUCH</h2>
        <p className="text-gray-600 mb-6">
          For any inquiries, please reach out via phone, text, or email. We&apos;re here to assist you.
        </p>

        {/* Button to toggle the form */}
        <div className="mb-8">
          <button
            onClick={toggleForm}
            className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {showForm ? "Close Form" : "Service Request Form"}
          </button>
        </div>

        {/* Dropdown Form */}
        {showForm && (
          <div className="bg-gray-100 p-6 rounded-lg mb-8">
            <h3 className="text-xl font-bold mb-4">Service Request Form</h3>
            <p className="text-red-600 font-bold mb-4">
              PLEASE SUBMIT ANY DOCUMENTS VIA EMAIL TO{" "}
              <a
                href="mailto:info@JustLegalSolutions.org"
                className="text-blue-600 hover:text-blue-800"
              >
                info@JustLegalSolutions.org
              </a>
            </p>
            <form
              action="https://formsubmit.co/info@justlegalsolutions.org"
              method="POST"
              className="space-y-4"
            >
              {/* Updated FormSubmit configuration */}
              <input type="hidden" name="_subject" value="New Service Request" />
              <input type="hidden" name="_captcha" value="false" />
              <input type="hidden" name="_template" value="table" />
              <input type="hidden" name="_next" value="https://justlegalsolutions.org" />
              <input type="hidden" name="_replyto" value="info@JustLegalSolutions.org" />

              {/* Firm Information */}
              <div>
                <label className="block text-sm font-medium text-gray-700">
                  Firm Name
                </label>
                <input
                  type="text"
                  name="firm_name"
                  className="w-full px-4 py-2 border border-gray-300 rounded-md"
                />
              </div>

              {/* Contact Information */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Contact First Name *
                  </label>
                  <input
                    type="text"
                    name="contact_first_name"
                    required
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Contact Last Name *
                  </label>
                  <input
                    type="text"
                    name="contact_last_name"
                    required
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Contact Email *
                  </label>
                  <input
                    type="email"
                    name="contact_email"
                    required
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Contact Phone *
                  </label>
                  <input
                    type="tel"
                    name="contact_phone"
                    required
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
              </div>

              {/* Plaintiff Information */}
              <div>
                <label className="block text-sm font-medium text-gray-700">
                  Plaintiff Name *
                </label>
                <input
                  type="text"
                  name="plaintiff_name"
                  required
                  className="w-full px-4 py-2 border border-gray-300 rounded-md"
                />
              </div>

              {/* Recipient/Defendant Information */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Recipient/Defendant Name *
                  </label>
                  <input
                    type="text"
                    name="recipient_name"
                    required
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Recipient Phone Number
                  </label>
                  <input
                    type="tel"
                    name="recipient_phone"
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700">
                  Recipient Email
                </label>
                <input
                  type="email"
                  name="recipient_email"
                  className="w-full px-4 py-2 border border-gray-300 rounded-md"
                />
              </div>

              {/* Recipient Address */}
              <div>
                <label className="block text-sm font-medium text-gray-700">
                  Recipient Street Address *
                </label>
                <input
                  type="text"
                  name="recipient_address"
                  required
                  className="w-full px-4 py-2 border border-gray-300 rounded-md"
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Recipient City *
                  </label>
                  <input
                    type="text"
                    name="recipient_city"
                    required
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Recipient State *
                  </label>
                  <input
                    type="text"
                    name="recipient_state"
                    required
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Recipient Zip Code *
                  </label>
                  <input
                    type="text"
                    name="recipient_zip"
                    required
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
              </div>

              {/* Court Information */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="md:col-span-1">
                  <label className="block text-sm font-medium text-gray-700">
                    Court Date *
                  </label>
                  <input
                    type="date"
                    name="court_date"
                    required
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
                <div className="md:col-span-2">
                  <label className="block text-sm font-medium text-gray-700">
                    Case Number *
                  </label>
                  <input
                    type="text"
                    name="case_number"
                    required
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Court Name
                  </label>
                  <input
                    type="text"
                    name="court_name"
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Court State
                  </label>
                  <input
                    type="text"
                    name="court_state"
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Court County
                  </label>
                  <input
                    type="text"
                    name="court_county"
                    className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
              </div>

              {/* Service Instructions */}
              <div>
                <label className="block text-sm font-medium text-gray-700">
                  Service Instructions
                </label>
                <textarea
                  name="service_instructions"
                  rows={4}
                  className="w-full px-4 py-2 border border-gray-300 rounded-md"
                  placeholder="Please provide any specific instructions for service..."
                ></textarea>
              </div>

              {/* Service Type Selection */}
              <div className="space-y-4">
                <label className="block text-sm font-medium text-gray-700">
                  Service Type
                </label>
                <div className="space-y-4">
                  <div className="flex items-start">
                    <div className="flex items-center h-5">
                      <input
                        type="radio"
                        name="service_type"
                        value="standard"
                        className="h-4 w-4 text-blue-600 border-gray-300"
                      />
                    </div>
                    <div className="ml-3">
                      <label className="font-medium text-gray-700">
                        Standard Service - $60
                      </label>
                      <p className="text-gray-500 text-sm">
                        Ideal for routine document delivery. First Service Attempt Within 7 Business Days (usually sooner)
                      </p>
                    </div>
                  </div>

                  <div className="flex items-start">
                    <div className="flex items-center h-5">
                      <input
                        type="radio"
                        name="service_type"
                        value="rush"
                        className="h-4 w-4 text-blue-600 border-gray-300"
                      />
                    </div>
                    <div className="ml-3">
                      <label className="font-medium text-gray-700">
                        Rush Service - $100
                      </label>
                      <p className="text-gray-500 text-sm">
                        For urgent demands that need immediate attention. A JLS Agent Will Attempt Service Within 72 Hours or Sooner based on availability
                      </p>
                    </div>
                  </div>

                  <div className="flex items-start">
                    <div className="flex items-center h-5">
                      <input
                        type="radio"
                        name="service_type"
                        value="same_day"
                        className="h-4 w-4 text-blue-600 border-gray-300"
                      />
                    </div>
                    <div className="ml-3">
                      <label className="font-medium text-gray-700">
                        Same Day Service - $150
                      </label>
                      <p className="text-gray-500 text-sm">
                        For urgent time-critical matters. A JLS Agent Will Attempt Service Within 24 Hours or Sooner based on availability
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              {/* Submit Button */}
              <div>
                <button
                  type="submit"
                  className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded"
                >
                  Submit Request
                </button>
              </div>
            </form>
          </div>
        )}

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* Email Section */}
          <div>
            <h3 className="text-xl font-bold mb-4">Email Us</h3>
            <p className="text-gray-600">
              You can reach us at{" "}
              <a
                href="mailto:info@JustLegalSolutions.org"
                className="text-blue-600 hover:text-blue-800"
              >
                info@JustLegalSolutions.org
              </a>
              . We respond promptly to all inquiries.
            </p>
          </div>

          {/* Call Us Section */}
          <div>
            <h3 className="text-xl font-bold mb-4">Call or Text Us</h3>
            <p className="text-gray-600">
              Contact us anytime Call or Text at{" "}
              <a
                href="tel:539-367-6832"
                className="text-blue-600 hover:text-blue-800"
              >
                539-367-6832
              </a>
              . We&apos;re here and ready to assist you with all your needs.
            </p>
          </div>

          {/* Payments Section */}
          <div>
            <h3 className="text-xl font-bold mb-4">Payments</h3>
            <p className="text-gray-600">
              At this time, we accept{" "}
              <a
                href="https://buy.stripe.com/3cs17SbHS6h95nGaEE"
                className="text-blue-600 hover:text-blue-800"
              >
                electronic payments
              </a>
              , cash, checks, or money orders. Payments should be made payable
              to Joseph Iannazzi and mailed to: 564 E 138th Pl, Glenpool, OK
              74033. Please ensure that payment is made in full within 10 days
              of the invoice date.
            </p>
          </div>
        </div>
      </div>
    </footer>
  );
}