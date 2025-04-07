import { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Security Policy',
  description: 'Security policy and guidelines for Just Legal Solutions website and services.',
  robots: {
    index: false,
    follow: false
  }
};

export default function SecurityPolicyPage() {
  return (
    <main className="min-h-screen py-16 px-4">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-4xl font-bold mb-8">Security Policy</h1>
        <div className="prose max-w-none">
          <p className="mb-4">
            At Just Legal Solutions, we take security seriously. This policy outlines our commitment to protecting our systems and your data.
          </p>
          <h2 className="text-2xl font-semibold mt-8 mb-4">Reporting Security Issues</h2>
          <p className="mb-4">
            If you discover a security vulnerability, please report it to Info@JustLegalSolutions.org. We will investigate all legitimate reports and do our best to quickly fix the problem.
          </p>
          <h2 className="text-2xl font-semibold mt-8 mb-4">Security Measures</h2>
          <ul className="list-disc pl-6 mb-4">
            <li>Regular security audits</li>
            <li>Secure data transmission</li>
            <li>Regular backups</li>
            <li>Access control and authentication</li>
          </ul>
        </div>
      </div>
    </main>
  );
}