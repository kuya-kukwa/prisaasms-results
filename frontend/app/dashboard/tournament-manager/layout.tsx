'use client';

import React from "react";
import ProtectedRoute from '@/src/components/auth/ProtectedRoute';
import AppSidebar from '@/components/ui/app-sidebar';
import { tmNavItems } from "@/config/nav-items";
import { SidebarProvider, SidebarInset } from '@/components/ui/sidebar';

export default function TournamentLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <ProtectedRoute allowedRoles={['tournament_manager']}>
      <SidebarProvider>
        <AppSidebar navItems={tmNavItems} baseUrl="/" />
        <SidebarInset>
          {children}
        </SidebarInset>
      </SidebarProvider>
    </ProtectedRoute>
  );
}
