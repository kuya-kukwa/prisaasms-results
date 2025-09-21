"use client";

import { useAuth } from "@/src/contexts/AuthContext";
import { useSidebar, SidebarMenu, SidebarMenuItem, SidebarMenuButton } from "@/components/ui/sidebar";
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuItem } from "@/components/ui/dropdown-menu";
import { Avatar, AvatarImage, AvatarFallback } from "@/components/ui/avatar";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { ChevronsUpDown, LogOut, Home } from "lucide-react";
import { useEffect, useState } from "react";
import axios from "axios";
import Link from "next/link";

const API_URL = process.env.NEXT_PUBLIC_API_BASE_URL || "http://localhost:8000/api";
const BASE_URL = process.env.NEXT_PUBLIC_BACKEND_URL || "http://localhost:8000";

export function NavUser() {
  const { isMobile } = useSidebar();
  const { logout, user: authUser, isAuthenticated } = useAuth();

  const [user, setUser] = useState<{ name: string; email: string; avatar: string } | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    // If user is already available from AuthContext, use it
    if (authUser && isAuthenticated) {
      setUser({
        name: authUser.first_name && authUser.last_name ? 
              `${authUser.first_name} ${authUser.last_name}` : 
              authUser.first_name || "User",
        email: authUser.email,
        avatar: "/logo.png", // Default avatar since User interface doesn't have avatar field
      });
      setLoading(false);
      setError(null);
      return;
    }

    // Fallback to API call if AuthContext doesn't have user data
    const fetchUser = async () => {
      if (!isAuthenticated) {
        setUser({
          name: "Guest User",
          email: "guest@prisaa.com", 
          avatar: "/logo.png",
        });
        setLoading(false);
        return;
      }

      setLoading(true);
      try {
        await axios.get(`${BASE_URL}/sanctum/csrf-cookie`, { withCredentials: true });
        const res = await axios.get(`${API_URL}/user`, {
          withCredentials: true,
          headers: { Accept: "application/json" }
        });
        const data = res.data;
        setUser({
          name: data.full_name || (data.first_name && data.last_name ? `${data.first_name} ${data.last_name}` : data.name) || "Admin User",
          email: data.email || "admin@prisaa.com",
          avatar: data.avatar || "/avatar.png",
        });
        setError(null);
      } catch (err) {
        console.error("User API Error:", err);
        setError("Unable to load user info");
        // Provide fallback user data
        setUser({
          name: "Admin User",
          email: "admin@prisaa.com",
          avatar: "/logo.png",
        });
      } finally {
        setLoading(false);
      }
    };
    fetchUser();
  }, [authUser, isAuthenticated]);

  if (loading) {
    return (
      <div className="flex items-center justify-center p-2 sm:p-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
        Loading user...
      </div>
    );
  }

  if (error || !user) {
    return (
      <div className="flex items-center justify-center p-2 sm:p-4 text-xs sm:text-sm text-red-500 dark:text-red-400">
        {error || "No user found"}
      </div>
    );
  }

  return (
    <SidebarMenu>
      <SidebarMenuItem>
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <SidebarMenuButton
              size="lg"
              className="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
            >
              <Avatar className="h-6 w-6 sm:h-8 sm:w-8 rounded-lg">
                <AvatarImage src={user.avatar} alt={user.name} />
                <AvatarFallback className="rounded-lg text-xs sm:text-sm">{user.name[0]}</AvatarFallback>
              </Avatar>
              <div className="grid flex-1 text-left text-xs sm:text-sm leading-tight">
                <span className="truncate font-medium">{user.name}</span>
                <span className="truncate text-xs">{user.email}</span>
              </div>
              <ChevronsUpDown className="ml-auto size-4" />
            </SidebarMenuButton>
          </DropdownMenuTrigger>

          <DropdownMenuContent
            className="min-w-56 rounded-lg"
            side={isMobile ? "bottom" : "right"}
            align="end"
            sideOffset={4}
          >
            <DropdownMenuLabel className="p-0 font-normal">
              <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                <Avatar className="h-6 w-6 sm:h-8 sm:w-8 rounded-lg">
                  <AvatarImage src={user.avatar} alt={user.name} />
                  <AvatarFallback className="rounded-lg text-xs sm:text-sm">{user.name[0]}</AvatarFallback>
                </Avatar>
                <div className="grid flex-1 text-left text-xs sm:text-sm leading-tight">
                  <span className="truncate font-medium">{user.name}</span>
                  <span className="truncate text-xs">{user.email}</span>
                </div>
              </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem asChild>
              <Link href="/" className="flex items-center">
                <Home className="mr-2 h-4 w-4" />
                Back to Home
              </Link>
            </DropdownMenuItem>
            <AlertDialog>
              <AlertDialogTrigger asChild>
                <DropdownMenuItem onSelect={(e) => e.preventDefault()}>
                  <LogOut className="mr-2 h-4 w-4" />
                  Log out
                </DropdownMenuItem>
              </AlertDialogTrigger>
              <AlertDialogContent className="py-4 px-8">
                <AlertDialogHeader>
                  <AlertDialogTitle>Are you sure you want to log out?</AlertDialogTitle>
                  <AlertDialogDescription>
                    You will be redirected to the login page.
                  </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                  <AlertDialogCancel>Cancel</AlertDialogCancel>
                  <AlertDialogAction onClick={logout} className="bg-blue-900 dark:bg-blue-500">
                    Log out
                  </AlertDialogAction>
                </AlertDialogFooter>
              </AlertDialogContent>
            </AlertDialog>
          </DropdownMenuContent>
        </DropdownMenu>
      </SidebarMenuItem>
    </SidebarMenu>
  );
}