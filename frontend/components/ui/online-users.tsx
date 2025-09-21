"use client";

import * as React from "react";
import {
  Avatar,
  AvatarImage,
  AvatarFallback,
} from "@/components/ui/avatar";
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import {
  ScrollArea,
} from "@/components/ui/scroll-area";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { dashboardService } from "@/src/lib/admin-dashboard-api";
import { OnlineUser } from "@/src/lib/api";
import { Users, ChevronDown, RefreshCw } from "lucide-react";

function cn(...classes: (string | undefined | false | null)[]) {
  return classes.filter(Boolean).join(" ");
}

type HybridOnlineUsersProps = {
  maxDisplayAvatars?: number;
  showStatus?: boolean;
  delayDuration?: number;
  className?: string;
  refreshInterval?: number;
  compactMode?: boolean;
};

const statusColors = {
  online: "bg-green-500",
  busy: "bg-red-500",
  away: "bg-amber-500",
  offline: "bg-gray-400",
};

const statusConfig = {
  online: { color: "bg-green-500", label: "Online" },
  away: { color: "bg-amber-500", label: "Away" },
  busy: { color: "bg-red-500", label: "Busy" },
  offline: { color: "bg-gray-400", label: "Offline" },
};

const getInitials = (name: string): string => {
  return name
    .split(" ")
    .map((part) => part[0])
    .join("")
    .toUpperCase();
};

const parseMinutes = (m?: number | string | null) => {
  const n = Number(m ?? NaN);
  return Number.isFinite(n) ? Math.abs(n) : null;
};

const getEffectiveStatus = (user: OnlineUser): keyof typeof statusConfig => {
  // Prefer explicit has_active_session + minutes_ago if available
  const minutes = parseMinutes(user.minutes_ago);

  if (user.has_active_session) {
    if (minutes !== null) {
      if (minutes <= 30) return 'online';
      if (minutes <= 45) return 'away';
      if (minutes <= 60) return 'busy';
      return 'offline';
    }
    // If has_active_session but no minutes provided, trust server status or assume online
    if (user.status && ['online','away','busy','offline'].includes(user.status)) {
      return user.status as keyof typeof statusConfig;
    }
    return 'online';
  }

  // Fallback: prefer server-provided status if valid
  if (user.status && ['online','away','busy','offline'].includes(user.status)) {
    return user.status as keyof typeof statusConfig;
  }

  // Final fallback: infer from minutes_ago if available
  if (minutes !== null) {
    if (minutes <= 5) return 'online';
    if (minutes <= 15) return 'away';
    if (minutes <= 30) return 'busy';
  }

  return 'offline';
};

const OnlineUsers = React.forwardRef<HTMLDivElement, HybridOnlineUsersProps>(
  ({ 
    maxDisplayAvatars = 5,
    showStatus = true,
    delayDuration = 300,
    className,
    refreshInterval = 30000,
    compactMode = true
  }, ref) => {
    const [onlineUsers, setOnlineUsers] = React.useState<OnlineUser[]>([]);
    const [isLoading, setIsLoading] = React.useState(true);
    const [isRefreshing, setIsRefreshing] = React.useState(false);
    const [lastUpdated, setLastUpdated] = React.useState<Date | null>(null);
    const [isPopoverOpen, setIsPopoverOpen] = React.useState(false);

    // Fetch online users
    const fetchOnlineUsers = React.useCallback(async (isManualRefresh = false) => {
      try {
        if (isManualRefresh) {
          setIsRefreshing(true);
        } else if (onlineUsers.length === 0) {
          setIsLoading(true);
        }
        
        const users = await dashboardService.getOnlineUsers(50);
        setOnlineUsers(users);
        setLastUpdated(new Date());
      } catch (err) {
        console.error('Failed to fetch online users:', err);
      } finally {
        setIsLoading(false);
        setIsRefreshing(false);
      }
    }, [onlineUsers.length]);

    // Initial load
    React.useEffect(() => {
      fetchOnlineUsers();
    }, [fetchOnlineUsers]);

    // Auto-refresh
    React.useEffect(() => {
      if (!refreshInterval) return;

      const interval = setInterval(() => fetchOnlineUsers(), refreshInterval);
      return () => clearInterval(interval);
    }, [fetchOnlineUsers, refreshInterval]);

    // Group users by status for detailed view
    const usersByStatus = React.useMemo(() => {
      const grouped = onlineUsers.reduce((acc, user) => {
        const status = getEffectiveStatus(user) || 'offline';
        if (!acc[status]) acc[status] = [];
        acc[status].push(user);
        return acc;
      }, {} as Record<string, OnlineUser[]>);

  const sortOrder = ['online', 'away', 'busy', 'offline'];
      return sortOrder.reduce((acc, status) => {
        if (grouped[status]) {
          acc[status] = grouped[status].sort((a, b) => 
            a.full_name.localeCompare(b.full_name)
          );
        }
        return acc;
      }, {} as Record<string, OnlineUser[]>);
    }, [onlineUsers]);

    const displayUsers = React.useMemo(
      () => onlineUsers.slice(0, maxDisplayAvatars),
      [onlineUsers, maxDisplayAvatars]
    );

  const remainingCount = onlineUsers.length - maxDisplayAvatars;
  const actuallyOnline = onlineUsers.filter(u => getEffectiveStatus(u) === 'online').length;

    const handleManualRefresh = () => {
      fetchOnlineUsers(true);
    };

    // Compact Avatar Group Component
    const AvatarGroup = () => {
      if (isLoading) {
        return (
          <div className="flex items-center relative">
            {[...Array(3)].map((_, index) => (
              <div key={index} className={cn("relative", index > 0 && "-ml-2")}>
                <Avatar className="border-2 border-background animate-pulse">
                  <AvatarFallback className="bg-muted"></AvatarFallback>
                </Avatar>
              </div>
            ))}
          </div>
        );
      }

      if (onlineUsers.length === 0) {
        return (
          <div className="flex items-center relative">
            <Avatar className="border-2 border-background">
              <AvatarFallback className="text-xs text-muted-foreground">
                0
              </AvatarFallback>
            </Avatar>
          </div>
        );
      }

      return (
        <TooltipProvider delayDuration={delayDuration}>
          <div className="flex items-center relative">
            {displayUsers.map((user, index) => (
              <Tooltip key={user.id}>
                <TooltipTrigger asChild>
                  <div
                    className={cn("relative hover:z-10", index > 0 && "-ml-2")}
                  >
                    <Avatar className="transition-all duration-300 hover:scale-105 hover:-translate-y-1 hover:shadow-lg border-2 border-background">
                      <AvatarImage src={user.avatar} alt={user.full_name} />
                      <AvatarFallback>{getInitials(user.full_name)}</AvatarFallback>
                    </Avatar>
                    {showStatus && (
                      (() => {
                        const eff = getEffectiveStatus(user);
                        return (
                          <span
                            className={cn(
                              "absolute bottom-0 right-0 block h-2 w-2 rounded-full ring-2 ring-background",
                              statusColors[eff]
                            )}
                          />
                        );
                      })()
                    )}
                  </div>
                </TooltipTrigger>
                <TooltipContent side="bottom" className="font-medium">
                  <div>
                    <div className="font-medium">{user.full_name}</div>
                    <div className="text-xs text-muted-foreground capitalize">
                      {user.role.replace('_', ' ')}
                    </div>
                    {user.school && (
                      <div className="text-xs text-muted-foreground">
                        {user.school.short_name || user.school.name}
                      </div>
                    )}
                    {showStatus && user.status && (
                      <div className="text-xs capitalize text-muted-foreground">
                        {user.status}
                      </div>
                    )}
                  </div>
                </TooltipContent>
              </Tooltip>
            ))}

            {remainingCount > 0 && (
              <Tooltip>
                <TooltipTrigger asChild>
                  <div className={cn("relative hover:z-10", "-ml-2")}>
                    <Avatar className="transition-all duration-300 hover:scale-105 hover:-translate-y-1 hover:shadow-lg border-2 border-background bg-muted cursor-pointer">
                      <AvatarFallback>+{remainingCount}</AvatarFallback>
                    </Avatar>
                  </div>
                </TooltipTrigger>
                <TooltipContent side="bottom" className="font-medium">
                  {remainingCount} more {remainingCount === 1 ? "user" : "users"} online
                  <div className="text-xs text-muted-foreground">Click to see all</div>
                </TooltipContent>
              </Tooltip>
            )}
          </div>
        </TooltipProvider>
      );
    };

    // Detailed Panel Component (for popover)
    const DetailedPanel = () => (
      <Card className="w-72 sm:w-80 border-0 shadow-lg">
        <CardHeader className="pb-3">
          <div className="flex items-center justify-between">
            <CardTitle className="text-sm font-medium flex items-center gap-2">
              <Users className="h-4 w-4" />
              Online Users
              {actuallyOnline > 0 && (
                <Badge variant="secondary" className="text-xs">
                  {actuallyOnline} active
                </Badge>
              )}
            </CardTitle>
            
            <Button
              variant="ghost"
              size="sm"
              onClick={handleManualRefresh}
              disabled={isRefreshing}
              className="h-8 w-8 p-0"
            >
              <RefreshCw className={cn(
                "h-3 w-3",
                isRefreshing && "animate-spin"
              )} />
            </Button>
          </div>
          
          {lastUpdated && (
            <p className="text-xs text-muted-foreground">
              Updated {lastUpdated.toLocaleTimeString()}
            </p>
          )}
        </CardHeader>

        <CardContent className="pt-0">
          {onlineUsers.length === 0 ? (
            <div className="text-center py-6">
              <Users className="h-8 w-8 text-muted-foreground mx-auto mb-2" />
              <p className="text-sm text-muted-foreground">No users online</p>
            </div>
          ) : (
                        <ScrollArea className="h-[200px] sm:h-[250px] pr-3">
              <div className="space-y-3">
                {Object.entries(usersByStatus).map(([status, users]) => {
                  if (users.length === 0) return null;
                  
                  const statusInfo = statusConfig[status as keyof typeof statusConfig];
                  
                  return (
                    <div key={status}>
                      <div className="flex items-center gap-2 mb-2">
                        <div className={cn(
                          "w-2 h-2 rounded-full",
                          statusInfo.color
                        )} />
                        <h4 className="text-xs font-medium text-muted-foreground uppercase tracking-wide">
                          {statusInfo.label} ({users.length})
                        </h4>
                      </div>
                      
                      <div className="space-y-2 ml-4">
                        {users.map((user) => (
                          <div
                            key={user.id}
                            className="flex items-center gap-3 p-2 rounded-md hover:bg-muted/50 transition-colors"
                          >
                            <div className="relative">
                              <Avatar className="w-6 h-6 sm:w-8 sm:h-8">
                                <AvatarImage src={user.avatar} alt={user.full_name} />
                                <AvatarFallback className="text-xs">
                                  {getInitials(user.full_name)}
                                </AvatarFallback>
                              </Avatar>
                              {
                                (() => {
                                  const eff = getEffectiveStatus(user);
                                  const color = statusConfig[eff].color;
                                  return (
                                    <div className={cn(
                                      "absolute -bottom-0.5 -right-0.5 w-2 h-2 sm:w-3 sm:h-3 rounded-full border-2 border-background",
                                      color
                                    )} />
                                  );
                                })()
                              }
                            </div>
                            
                            <div className="flex-1 min-w-0">
                              <p className="text-sm font-medium truncate">
                                {user.full_name}
                              </p>
                              <div className="flex items-center gap-2">
                                <p className="text-xs text-muted-foreground capitalize">
                                  {user.role.replace('_', ' ')}
                                </p>
                                {user.school && (
                                  <>
                                    <span className="text-xs text-muted-foreground">•</span>
                                    <p className="text-xs text-muted-foreground truncate">
                                      {user.school.short_name || user.school.name}
                                    </p>
                                  </>
                                )}
                              </div>
                            </div>
                          </div>
                        ))}
                      </div>
                    </div>
                  );
                })}
              </div>
            </ScrollArea>
          )}
        </CardContent>
      </Card>
    );

    if (compactMode) {
      return (
        <Popover open={isPopoverOpen} onOpenChange={setIsPopoverOpen}>
          <PopoverTrigger asChild>
            <div
              ref={ref}
              className={cn(
                "bg-background flex items-center justify-center rounded-full border p-0 cursor-pointer hover:bg-muted/50 transition-colors",
                className
              )}
            >
              <AvatarGroup />
              {onlineUsers.length > 0 && (
                <ChevronDown className="h-3 w-3 ml-2 text-muted-foreground" />
              )}
            </div>
          </PopoverTrigger>
          <PopoverContent 
            side="bottom" 
            align="end" 
            className="p-0 w-auto"
            sideOffset={8}
          >
            <DetailedPanel />
          </PopoverContent>
        </Popover>
      );
    }

    // Full panel mode (for sidebar)
    return (
      <div ref={ref} className={className}>
        <DetailedPanel />
      </div>
    );
  }
);

OnlineUsers.displayName = "OnlineUsers";

export default OnlineUsers;