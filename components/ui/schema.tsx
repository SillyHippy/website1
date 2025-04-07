import { Organization, WithContext } from 'schema-dts';

export const organizationSchema: WithContext<Organization> = {
  "@context": "https://schema.org",
  "@type": "LegalService",
  "name": "Just Legal Solutions",
  "legalName": "Just Legal Solutions",
  "url": "https://justlegalsolutions.org",
  "logo": "https://justlegalsolutions.org/images/jls-logo.webp",
  "image": "https://justlegalsolutions.org/images/jls-logo.webp",
  "description": "Expert process serving, secure document delivery, court transfers, and skip tracing services throughout Oklahoma, with specialized focus on the Tulsa Metropolitan Area. Fast, reliable, and professional legal support services.",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "564 E 138th Pl",
    "addressLocality": "Glenpool",
    "addressRegion": "OK",
    "postalCode": "74033",
    "addressCountry": "US"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": "35.9473",
    "longitude": "-96.0006"
  },
  "areaServed": [
    {
      "@type": "State",
      "name": "Oklahoma"
    },
    {
      "@type": "City",
      "name": "Tulsa",
      "containedInPlace": {
        "@type": "State",
        "name": "Oklahoma"
      }
    },
    {
      "@type": "City",
      "name": "Oklahoma City",
      "containedInPlace": {
        "@type": "State",
        "name": "Oklahoma"
      }
    },
    {
      "@type": "City",
      "name": "Broken Arrow",
      "containedInPlace": {
        "@type": "State",
        "name": "Oklahoma"
      }
    },
    {
      "@type": "City",
      "name": "Norman",
      "containedInPlace": {
        "@type": "State",
        "name": "Oklahoma"
      }
    },
    {
      "@type": "City",
      "name": "Lawton",
      "containedInPlace": {
        "@type": "State",
        "name": "Oklahoma"
      }
    },
    {
      "@type": "City",
      "name": "Edmond",
      "containedInPlace": {
        "@type": "State",
        "name": "Oklahoma"
      }
    },
    {
      "@type": "City",
      "name": "Stillwater",
      "containedInPlace": {
        "@type": "State",
        "name": "Oklahoma"
      }
    },
    {
      "@type": "City",
      "name": "Muskogee",
      "containedInPlace": {
        "@type": "State",
        "name": "Oklahoma"
      }
    }
  ],
  "telephone": "+15393676832",
  "email": "Info@JustLegalSolutions.org",
  "founder": {
    "@type": "Person",
    "name": "Joseph Iannazzi",
    "jobTitle": "Process Server"
  },
  "sameAs": [
    "https://justlegalsolutions.org"
  ],
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday"
    ],
    "opens": "09:00",
    "closes": "17:00"
  },
  "priceRange": "$$",
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Legal Support Services",
    "itemListElement": [
      {
        "@type": "Service",
        "name": "Process Serving",
        "description": "Professional process serving with standard, rush, and same-day options",
        "areaServed": {
          "@type": "State",
          "name": "Oklahoma",
          "description": "Serving all of Oklahoma with priority service in the Tulsa Metropolitan Area"
        }
      },
      {
        "@type": "Service",
        "name": "Document Delivery",
        "description": "Secure and timely document delivery services",
        "areaServed": {
          "@type": "State",
          "name": "Oklahoma",
          "description": "Statewide service with expedited options in the Tulsa Metropolitan Area"
        }
      },
      {
        "@type": "Service",
        "name": "Court Transfers",
        "description": "Reliable court document transfer services",
        "areaServed": {
          "@type": "State",
          "name": "Oklahoma",
          "description": "Available at all Oklahoma courts with specialized service in Tulsa County courts"
        }
      },
      {
        "@type": "Service",
        "name": "Skip Tracing",
        "description": "Professional skip tracing services to locate individuals",
        "areaServed": {
          "@type": "State",
          "name": "Oklahoma",
          "description": "Comprehensive skip tracing throughout Oklahoma"
        }
      }
    ]
  }
};