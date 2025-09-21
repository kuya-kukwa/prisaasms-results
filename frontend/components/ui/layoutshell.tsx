"use client";

import { usePathname } from "next/navigation";
import Header from "./header";
import Footer from "./footer";

export default function LayoutShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const isDashboard =
    pathname.startsWith("/dashboard/admin") ||
    pathname.startsWith("/dashboard/coach") ||
    pathname.startsWith("/dashboard/tournament-manager");

  return (
    <>
      {!isDashboard && <Header />}
      {children}
      {!isDashboard && <Footer />}
    </>
  );
}