'use client';

import React from "react";
import ProtectedRoute from '@/src/components/auth/ProtectedRoute';
import AppSidebar from '@/components/ui/app-sidebar';
import { coachNavItems } from "@/config/nav-items";
import { SidebarProvider, SidebarInset } from '@/components/ui/sidebar';

export default function CoachLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <ProtectedRoute allowedRoles={['coach']}>
      <SidebarProvider>
        <AppSidebar navItems={coachNavItems} baseUrl="/" />
        <SidebarInset>
          {children}
        </SidebarInset>
      </SidebarProvider>
    </ProtectedRoute>
  );
}
