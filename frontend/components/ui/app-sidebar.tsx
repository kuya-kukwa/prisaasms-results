"use client";

import * as React from "react";
import Link from "next/link";
import Image from "next/image";
import NavMain from "@/components/ui/nav-main";
import { NavUser } from "@/components/ui/nav-user";
import { Tooltip, TooltipTrigger } from "@/components/ui/tooltip"
import { Sidebar, useSidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarRail } from "@/components/ui/sidebar";
import type { LucideIcon } from "lucide-react";

interface AppSidebarProps {
  navItems: {
    title: string;
    url: string;
    icon?: LucideIcon;
    items?: {
      title: string;
      url: string;
    }[];
  }[];
  baseUrl: string;
}

export default function AppSidebar({ navItems, baseUrl, ...props }: AppSidebarProps) {
  const { state } = useSidebar();
  const isCollapsed = state === 'collapsed';
  return (
    <Sidebar collapsible="icon" {...props}>
      <SidebarHeader>
        <div className={`flex items-center transition-all duration-300 ${isCollapsed ? 'justify-center' : ''}`}>
          {isCollapsed ? (
            <Tooltip>
              <TooltipTrigger asChild>
                <Link
                  href={baseUrl}
                  passHref
                  className="flex items-center space-x-2 flex-shrink-0 p-1 sm:p-2"
                >
                  <Image
                  src="/logo.png"
                  alt="PRISAA Logo"
                  width={28}
                  height={28}
                  className="rounded-full shadow-lg shadow-black/[0.25] transition-all duration-300 sm:w-8 sm:h-8"
                  priority
                />
                </Link>
              </TooltipTrigger>
            </Tooltip>
          ) : (
            <Link
              href={baseUrl}
              passHref
              className="flex items-center space-x-2 flex-shrink-0 px-2 py-3 sm:px-2 sm:py-4"
            >
              <Image
                src="/logo.png"
                alt="PRISAA Logo"
                width={40}
                height={40}
                className="rounded-full shadow-lg shadow-black/[0.25] transition-all duration-300 sm:w-12 sm:h-12"
                priority
              />
              <div className="min-w-0">
                <h1 className="text-base sm:text-lg lg:text-2xl font-black bg-gradient-to-r from-blue-900 to-rose-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-rose-400">
                  PRISAA
                </h1>
                <p className="text-xs text-gray-700 dark:text-white whitespace-nowrap hidden sm:block">
                  Sports Management System
                </p>
              </div>
            </Link>
          )}
        </div>
      </SidebarHeader>
      <SidebarContent>
        <NavMain items={navItems} />
      </SidebarContent>
      <SidebarFooter>
        <NavUser />
      </SidebarFooter>
      <SidebarRail />
    </Sidebar>
  );
}