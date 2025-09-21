"use client"

import { ChevronRight, type LucideIcon } from "lucide-react"
import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from "@/components/ui/collapsible"
import {
  SidebarGroup,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
} from "@/components/ui/sidebar"
import { useSidebar } from "@/components/ui/sidebar";

interface NavMainItem {
  title: string;
  url: string;
  icon?: LucideIcon;
  items?: { title: string; url: string }[];
}

interface NavMainProps {
  items: NavMainItem[];
}

export default function NavMain({ items }: NavMainProps) {
  const pathname = usePathname();
  const { state } = useSidebar();
  const isCollapsed = state === 'collapsed';

  return (
    <SidebarGroup>
      <SidebarMenu>
        {items.map((item, idx) => {
          const isParentActive = pathname === item.url || item.items?.some(sub => pathname === sub.url);

          return item.items?.length ? (
            <Collapsible
              key={idx}
              asChild
              defaultOpen={isParentActive}
              className={`group/collapsible ${isCollapsed ? 'px-0' : 'px-4'}`}
            >
              <SidebarMenuItem>
                <CollapsibleTrigger asChild>
                  <SidebarMenuButton
                    tooltip={item.title}
                    className={`relative overflow-hidden transition-all duration-300 ${
                      isCollapsed ? 'justify-center px-0' : ''
                    } ${
                      isParentActive
                        ? 'bg-gray-300 dark:bg-gray-900/30 text-gray-900 dark:text-gray-400 shadow-sm'
                        : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'
                    }`}
                  >
                      {item.icon && (
                        <item.icon className={`transition-colors duration-200 ${
                          isParentActive ? 'text-gray-900 dark:text-gray-400' : 'text-gray-600 dark:text-gray-300'
                        } ${isCollapsed ? '' : 'mr-2 sm:mr-3'} w-4 h-4 sm:w-5 sm:h-5`} />
                      )}
                    {!isCollapsed && (
                      <>
                        <span className={`font-medium transition-colors duration-300 text-sm sm:text-base ${
                          isParentActive ? 'text-gray-900 dark:text-gray-400' : 'text-gray-600 dark:text-gray-300'
                        }`}>
                          {item.title}
                        </span>
                        <ChevronRight className={`ml-auto transition-all duration-200 ${
                          isParentActive ? 'text-gray-900 dark:text-gray-400' : 'text-gray-600'
                        } group-data-[state=open]/collapsible:rotate-90`} />
                      </>
                    )}
                  </SidebarMenuButton>
                </CollapsibleTrigger>
                <CollapsibleContent>
                  <SidebarMenuSub className="ml-4 border-l-2 border-gray-300 dark:border-gray-700 pl-4">
                    {item.items.map((subItem) => {
                      const isSubActive = pathname === subItem.url;

                      return (
                        <SidebarMenuSubItem key={subItem.url}>
                          <SidebarMenuSubButton asChild>
                            <Link
                              href={subItem.url}
                              className={`group relative flex items-center px-3 py-2 rounded-lg transition-colors duration-150 ${
                                isSubActive
                                  ? 'bg-gray-300 dark:bg-gray-900/30 text-gray-900 dark:text-gray-400 font-medium shadow-sm'
                                  : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-medium'
                              }`}
                            >
                              <span className="font-medium">{subItem.title}</span>
                            </Link>
                          </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                      );
                    })}
                  </SidebarMenuSub>
                </CollapsibleContent>
              </SidebarMenuItem>
            </Collapsible>
          ) : (
            <SidebarMenuItem key={idx}>
              <SidebarMenuButton
                asChild
                tooltip={item.title}
                className={`relative overflow-hidden transition-colors duration-200 ${
                  isCollapsed ? 'justify-center px-0' : ''
                } ${
                  pathname === item.url
                    ? 'bg-gray-300 dark:bg-gray-900/20 text-gray-900 dark:text-gray-400 shadow-sm'
                    : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'
                }`}
              >
                <Link
                  href={item.url}
                  className={`flex items-center ${isCollapsed ? 'justify-center px-0' : 'px-6'} py-3 rounded-lg transition-all duration-300`}
                >
                  {item.icon && (
                    <item.icon className={`transition-colors duration-300 ${
                      pathname === item.url ? 'text-gray-900 dark:text-gray-400' : 'text-gray-600 dark:text-gray-300'
                    } ${isCollapsed ? '' : 'mr-2 sm:mr-3'} w-4 h-4 sm:w-5 sm:h-5`} />
                  )}
                  {!isCollapsed && (
                    <span className={`font-medium transition-colors duration-300 text-sm sm:text-base ${
                      pathname === item.url ? 'text-gray-900 dark:text-gray-400' : 'text-gray-600 dark:text-gray-300'
                    }`}>
                      {item.title}
                    </span>
                  )}
                </Link>
              </SidebarMenuButton>
            </SidebarMenuItem>
          );
        })}
      </SidebarMenu>
    </SidebarGroup>
  );
}