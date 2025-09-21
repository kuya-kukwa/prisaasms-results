'use client';

import React from "react";
import ProtectedRoute from '@/src/components/auth/ProtectedRoute';
import AppSidebar from '@/components/ui/app-sidebar';
import { adminNavItems } from "@/config/nav-items";
import { SidebarProvider, SidebarInset } from '@/components/ui/sidebar';

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <ProtectedRoute allowedRoles={['admin']}>
      <SidebarProvider>
        <AppSidebar navItems={adminNavItems} baseUrl="/" />
        <SidebarInset>
          {children}
        </SidebarInset>
      </SidebarProvider>
    </ProtectedRoute>
  );
}
