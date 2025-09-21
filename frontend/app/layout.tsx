import type { Metadata } from "next";
import { Poppins, Fira_Code } from "next/font/google";
import { Toaster } from "react-hot-toast";
import { AuthProvider } from "../src/contexts/AuthContext";
import ActiveSectionContextProvider from "@/src/contexts/ActiveSection";
import LayoutShell from "@/components/ui/layoutshell";
import "./globals.css";

const poppins = Poppins({
  variable: "--font-poppins",
  subsets: ["latin"],
  weight: ["100", "200", "300", "400", "500", "600", "700", "800", "900"],
});

const firaCode = Fira_Code({
  variable: "--font-fira-code",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  metadataBase: new URL(process.env.NEXT_PUBLIC_APP_URL || 'https://prisaasms.com'),
  title: {
    default: "PRISAA Sports Management System",
    template: "%s | PRISAA SMS"
  },
  description: "The Official Sports Management System of PRISAA Sports Foundation, Inc. Track leaderboards, manage athletes, events, scores, and records efficiently across regional and national competitions.",
  keywords: [
    "PRISAA Sports Management System",
    "Sports Management System", 
    "PRISAA",
    "PRISAA Sorsogon",
    "PRISAA Regional",
    "PRISAA National",
    "Athletes Management",
    "Athletics Philippines",
    "Philippine Sports",
    "Collegiate Sports",
    "Sports Leaderboards",
    "Athletic Events",
    "Sports Records",
    "Competition Management",
    "Student Athletes",
    "Sports Foundation"
  ],
  authors: [{ name: "PRISAA Sports Foundation, Inc." }],
  creator: "PRISAA Sports Foundation, Inc.",
  publisher: "PRISAA Sports Foundation, Inc.",
  applicationName: "PRISAA SMS",
  category: "Sports Management",
  classification: "Sports Management Software",
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
  openGraph: {
    type: "website",
    locale: "en_PH",
    url: "https://prisaasms.com",
    siteName: "PRISAA Sports Management System",
    title: "PRISAA Sports Management System",
    description: "The Official Sports Management System of PRISAA Sports Foundation, Inc. Track leaderboards, manage athletes, events, scores, and records efficiently.",
    images: [
      {
        url: "/og-image.png",
        width: 1200,
        height: 630,
        alt: "PRISAA Sports Management System",
        type: "image/png",
      }
    ],
  },
  twitter: {
    card: "summary_large_image",
    site: "@PRISAA_Official",
    creator: "@PRISAA_Official",
    title: "PRISAA Sports Management System",
    description: "The Official Sports Management System of PRISAA Sports Foundation, Inc. Track leaderboards, manage athletes, events, scores, and records efficiently.",
    images: ["/twitter-image.png"],
  },
  icons: {
    icon: [
      { url: "/logo.png", sizes: "any", type: "image/png" }
    ]
  },
  manifest: "/site.webmanifest",
  alternates: {
    canonical: "https://prisaa-sms.com",
  },
  other: {
    "mobile-web-app-capable": "yes",
    "apple-mobile-web-app-capable": "yes",
    "apple-mobile-web-app-status-bar-style": "default",
    "apple-mobile-web-app-title": "PRISAA SMS",
    "application-name": "PRISAA SMS",
    "msapplication-TileColor": "#1e40af",
    "theme-color": "#ffffff",
  },
};

export const viewport = {
  width: "device-width",
  initialScale: 1,
  maximumScale: 1,
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body
        className={`${poppins.variable} ${firaCode.variable} font-sans antialiased`}
      >
        <AuthProvider>
          <ActiveSectionContextProvider>
            <LayoutShell>
              {children}
            </LayoutShell>
          </ActiveSectionContextProvider>
        </AuthProvider>
        <Toaster
          position="top-right"
          toastOptions={{
            duration: 4000,
            style: {
              background: '#363636',
              color: '#fff',
              fontFamily: 'var(--font-poppins)',
              fontSize: '14px',
              fontWeight: '500',
              borderRadius: '8px',
              boxShadow: '0 10px 25px rgba(0, 0, 0, 0.1)',
            },
            success: {
              iconTheme: {
                primary: '#10b981',
                secondary: '#fff',
              },
            },
            error: {
              iconTheme: {
                primary: '#ef4444',
                secondary: '#fff',
              },
            },
          }}
        />
      </body>
    </html>
  );
}