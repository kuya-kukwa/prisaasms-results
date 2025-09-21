'use client'

import React from "react";
import { Sheet, SheetContent, SheetTitle } from "@/components/ui/sheet";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  User,
  Mail,
  Phone,
  MapPin,
  Calendar,
  Award,
  Building,
  Users,
  Trophy,
  Hash,
  Clock,
  UserCheck,
  Info,
  GraduationCap,
  Briefcase,
  Home,
  FileText,
  Activity,
  ChevronRight,
  Zap,
  LucideIcon
} from "lucide-react";
import { type Profile, type AthleteProfile, type OfficialProfile, type SchoolProfile } from "@/src/lib/admin-profiles-api";

export function ViewProfileModal({
  open,
  onClose,
  profile,
}: {
  open: boolean;
  onClose: () => void;
  profile: Profile | null;
}) {
  if (!profile) return null;

  const getInitials = (firstName?: string, lastName?: string, name?: string) => {
    if (name) return name.charAt(0).toUpperCase();
    if (firstName && lastName) return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase();
    return 'U';
  };

  const formatDate = (dateString?: string) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const formatDateTime = (dateString?: string) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const getStatusVariant = (status: string): "default" | "secondary" | "destructive" | "outline" => {
    const variants: Record<string, "default" | "secondary" | "destructive" | "outline"> = {
      active: "default",
      inactive: "secondary",
      pending: "outline",
      injured: "destructive",
      suspended: "destructive",
      retired: "secondary",
    };
    return variants[status] || "default";
  };

  const InfoRow = ({ icon: Icon, label, value, className = "" }: { 
    icon: LucideIcon, 
    label: string, 
    value: string | React.ReactNode,
    className?: string 
  }) => (
    <div className={`flex items-center justify-between py-3 px-1 group hover:bg-muted/50 rounded-lg transition-colors ${className}`}>
      <div className="flex items-center gap-3 text-muted-foreground">
        <Icon className="h-4 w-4" />
        <span className="text-sm font-medium">{label}</span>
      </div>
      <span className="text-sm text-right max-w-[60%] text-foreground font-medium">{value}</span>
    </div>
  );

  const DetailCard = ({ icon: Icon, title, children, description }: { 
    icon: LucideIcon, 
    title: string,
    children: React.ReactNode,
    description?: string
  }) => (
    <Card className="border-muted/50 shadow-sm hover:shadow-md transition-shadow">
      <CardHeader className="pb-3">
        <div className="flex items-center gap-2">
          <div className="p-1.5 bg-primary/10 rounded-md">
            <Icon className="h-4 w-4 text-primary" />
          </div>
          <div>
            <CardTitle className="text-base">{title}</CardTitle>
            {description && (
              <CardDescription className="text-xs mt-0.5">{description}</CardDescription>
            )}
          </div>
        </div>
      </CardHeader>
      <CardContent>
        {children}
      </CardContent>
    </Card>
  );

  const StatCard = ({ label, value, icon: Icon, trend }: {
    label: string,
    value: string | number,
    icon: LucideIcon,
    trend?: string
  }) => (
    <div className="flex items-center gap-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors group cursor-default">
      <div className="p-2 bg-background rounded-md border border-border/50 group-hover:border-border transition-colors">
        <Icon className="h-4 w-4 text-muted-foreground" />
      </div>
      <div className="flex-1">
        <p className="text-xs text-muted-foreground font-medium">{label}</p>
        <p className="text-lg font-semibold">{value}</p>
        {trend && (
          <p className="text-xs text-muted-foreground mt-0.5">{trend}</p>
        )}
      </div>
    </div>
  );

  // Get profile name
  const profileName = profile.type === 'school'
    ? (profile as SchoolProfile).name
    : `${(profile as AthleteProfile | OfficialProfile).first_name} ${(profile as AthleteProfile | OfficialProfile).last_name}`;

  const profileSubtitle = profile.type === 'school'
    ? (profile as SchoolProfile).short_name
    : profile.type === 'athlete'
    ? `${(profile as AthleteProfile).school?.name || 'Independent'} • ${(profile as AthleteProfile).sport?.name || 'Multi-sport'}`
    : profile.type === 'official'
    ? (profile as OfficialProfile).official_type || 'Official'
    : profile.type.replace('_', ' ');

  return (
    <Sheet open={open} onOpenChange={onClose}>
      <SheetContent className="w-1/2 min-w-[700px] max-w-5xl p-0 flex flex-col overflow-hidden">
        <SheetTitle className="sr-only">View Profile Details</SheetTitle>
        <div className="px-6 py-6 border-b bg-muted/30">
          <div className="flex items-start gap-4">
            <Avatar className="h-16 w-16 border-2 border-background shadow-md">
              <AvatarImage src={profile.avatar} alt={profileName} />
              <AvatarFallback className="text-lg bg-primary/10 text-primary font-semibold">
                {profile.type === 'school'
                  ? getInitials(undefined, undefined, (profile as SchoolProfile).name)
                  : getInitials((profile as AthleteProfile | OfficialProfile).first_name, (profile as AthleteProfile | OfficialProfile).last_name)
                }
              </AvatarFallback>
            </Avatar>
            
            <div className="flex-1 min-w-0">
              <div className="flex items-start justify-between gap-2">
                <div className="min-w-0">
                  <h2 className="text-xl font-semibold truncate">{profileName}</h2>
                  <p className="text-sm text-muted-foreground mt-0.5">{profileSubtitle}</p>
                </div>
                <Badge variant={getStatusVariant(profile.status || 'active')} className="shrink-0">
                  {profile.status || 'Active'}
                </Badge>
              </div>
              
              {/* Quick Info Pills */}
              <div className="flex flex-wrap gap-2 mt-3">
                <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-background rounded-md border border-border/50 text-xs">
                  <UserCheck className="h-3 w-3 text-muted-foreground" />
                  <span className="font-medium capitalize">{profile.type.replace('_', ' ')}</span>
                </div>
                
                {profile.type === 'athlete' && (profile as AthleteProfile).athlete_number && (
                  <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-background rounded-md border border-border/50 text-xs">
                    <Hash className="h-3 w-3 text-muted-foreground" />
                    <span className="font-medium">#{(profile as AthleteProfile).athlete_number}</span>
                  </div>
                )}
                
                <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-background rounded-md border border-border/50 text-xs">
                  <Calendar className="h-3 w-3 text-muted-foreground" />
                  <span className="font-medium">Since {new Date(profile.created_at || '').getFullYear()}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Content Section */}
        <div className="flex-1 overflow-y-auto">
          {profile.type === 'athlete' && (
            <Tabs defaultValue="overview" className="w-full">
              <div className="px-6 pt-4 pb-2 bg-background sticky top-0 z-10 border-b">
                <TabsList className="grid w-full grid-cols-3">
                  <TabsTrigger value="overview" className="text-xs">Overview</TabsTrigger>
                  <TabsTrigger value="details" className="text-xs">Details</TabsTrigger>
                  <TabsTrigger value="activity" className="text-xs">Activity</TabsTrigger>
                </TabsList>
              </div>
              
              <TabsContent value="overview" className="px-6 py-4 space-y-4 mt-0">
                {/* Stats Grid */}
                <div className="grid grid-cols-2 gap-3">
                  <StatCard 
                    label="School" 
                    value={(profile as AthleteProfile).school?.name || 'Not Assigned'} 
                    icon={Building}
                  />
                  <StatCard 
                    label="Sport" 
                    value={(profile as AthleteProfile).sport?.name || 'Multi-sport'} 
                    icon={Trophy}
                  />
                  <StatCard 
                    label="Jersey Number" 
                    value={(profile as AthleteProfile).athlete_number || 'N/A'} 
                    icon={Hash}
                  />
                  <StatCard 
                    label="Gender" 
                    value={(profile as AthleteProfile).gender === 'male' ? 'Male' : (profile as AthleteProfile).gender === 'female' ? 'Female' : 'N/A'} 
                    icon={User}
                  />
                </div>

                {/* Contact Information */}
                <DetailCard icon={Mail} title="Contact Information" description="Primary contact details">
                  <div className="space-y-2">
                    <InfoRow 
                      icon={Mail} 
                      label="Email Address" 
                      value={(profile as AthleteProfile).email || 'Not provided'}
                    />
                    <InfoRow 
                      icon={User} 
                      label="Full Name" 
                      value={`${(profile as AthleteProfile).first_name} ${(profile as AthleteProfile).last_name}`}
                    />
                  </div>
                </DetailCard>
              </TabsContent>

              <TabsContent value="details" className="px-6 py-4 space-y-4 mt-0">
                <DetailCard icon={User} title="Personal Information">
                  <div className="space-y-2">
                    <InfoRow icon={User} label="First Name" value={(profile as AthleteProfile).first_name || 'N/A'} />
                    <InfoRow icon={User} label="Last Name" value={(profile as AthleteProfile).last_name || 'N/A'} />
                    <InfoRow icon={Mail} label="Email" value={(profile as AthleteProfile).email || 'N/A'} />
                    <InfoRow 
                      icon={User} 
                      label="Gender" 
                      value={(profile as AthleteProfile).gender === 'male' ? 'Male' : (profile as AthleteProfile).gender === 'female' ? 'Female' : 'N/A'}
                    />
                  </div>
                </DetailCard>

                <DetailCard icon={Trophy} title="Athletic Details">
                  <div className="space-y-2">
                    <InfoRow icon={Building} label="School" value={(profile as AthleteProfile).school?.name || 'Not assigned'} />
                    <InfoRow icon={Trophy} label="Sport" value={(profile as AthleteProfile).sport?.name || 'Not assigned'} />
                    <InfoRow icon={Hash} label="Jersey Number" value={(profile as AthleteProfile).athlete_number || 'Not assigned'} />
                    <InfoRow 
                      icon={Activity} 
                      label="Status" 
                      value={
                        <Badge variant={getStatusVariant(profile.status || 'active')} className="text-xs">
                          {profile.status || 'Active'}
                        </Badge>
                      }
                    />
                  </div>
                </DetailCard>
              </TabsContent>

              <TabsContent value="activity" className="px-6 py-4 space-y-4 mt-0">
                <DetailCard icon={Clock} title="Activity Timeline">
                  <div className="space-y-3">
                    <div className="flex items-start gap-3 p-3 rounded-lg bg-muted/30">
                      <div className="p-1.5 bg-primary/10 rounded-full mt-0.5">
                        <Zap className="h-3 w-3 text-primary" />
                      </div>
                      <div className="flex-1">
                        <p className="text-sm font-medium">Profile Created</p>
                        <p className="text-xs text-muted-foreground mt-0.5">
                          {formatDateTime((profile as AthleteProfile).created_at)}
                        </p>
                      </div>
                    </div>
                    
                    <div className="flex items-start gap-3 p-3 rounded-lg bg-muted/30">
                      <div className="p-1.5 bg-blue-500/10 rounded-full mt-0.5">
                        <Clock className="h-3 w-3 text-blue-500" />
                      </div>
                      <div className="flex-1">
                        <p className="text-sm font-medium">Last Updated</p>
                        <p className="text-xs text-muted-foreground mt-0.5">
                          {formatDateTime((profile as AthleteProfile).updated_at)}
                        </p>
                      </div>
                    </div>
                  </div>
                </DetailCard>

                <Card className="border-dashed">
                  <CardContent className="flex flex-col items-center justify-center py-8 text-center">
                    <div className="p-3 bg-muted rounded-full mb-3">
                      <FileText className="h-6 w-6 text-muted-foreground" />
                    </div>
                    <p className="text-sm font-medium text-muted-foreground">No recent activity</p>
                    <p className="text-xs text-muted-foreground mt-1">
                      Activity logs will appear here
                    </p>
                  </CardContent>
                </Card>
              </TabsContent>
            </Tabs>
          )}

          {profile.type === 'official' && (
            <div className="px-6 py-6 space-y-4">
              {/* Official Stats */}
              <div className="grid grid-cols-2 gap-3">
                <StatCard 
                  label="Official Type" 
                  value={(profile as OfficialProfile).official_type || 'General'} 
                  icon={Briefcase}
                />
                <StatCard 
                  label="Certification" 
                  value={(profile as OfficialProfile).certification_level || 'Standard'} 
                  icon={Award}
                />
              </div>

              {/* Personal Information */}
              <DetailCard icon={User} title="Personal Information">
                <div className="space-y-2">
                  <InfoRow icon={User} label="First Name" value={(profile as OfficialProfile).first_name || 'N/A'} />
                  <InfoRow icon={User} label="Last Name" value={(profile as OfficialProfile).last_name || 'N/A'} />
                  <InfoRow icon={Mail} label="Email" value={(profile as OfficialProfile).email || 'N/A'} />
                  <InfoRow icon={Phone} label="Contact" value={(profile as OfficialProfile).contact_number || 'N/A'} />
                </div>
              </DetailCard>

              {/* Credentials */}
              <DetailCard icon={Award} title="Official Credentials">
                <div className="space-y-2">
                  <InfoRow icon={Briefcase} label="Official Type" value={(profile as OfficialProfile).official_type || 'N/A'} />
                  <InfoRow icon={Award} label="Certification Level" value={(profile as OfficialProfile).certification_level || 'N/A'} />
                  <InfoRow 
                    icon={Activity} 
                    label="Status" 
                    value={
                      <Badge variant={getStatusVariant(profile.status || 'active')}>
                        {profile.status || 'Active'}
                      </Badge>
                    }
                  />
                </div>
              </DetailCard>

              {/* Timeline */}
              <DetailCard icon={Clock} title="Timeline">
                <div className="space-y-2">
                  <InfoRow icon={Calendar} label="Joined" value={formatDate((profile as OfficialProfile).created_at)} />
                  <InfoRow icon={Clock} label="Last Updated" value={formatDate((profile as OfficialProfile).updated_at)} />
                </div>
              </DetailCard>
            </div>
          )}

          {profile.type === 'school' && (
            <div className="px-6 py-6 space-y-4">
              {/* School Overview */}
              <div className="grid grid-cols-2 gap-3">
                <StatCard 
                  label="Short Name" 
                  value={(profile as SchoolProfile).short_name || 'N/A'} 
                  icon={GraduationCap}
                />
                <StatCard 
                  label="Region" 
                  value={(profile as SchoolProfile).region?.name || 'N/A'} 
                  icon={MapPin}
                />
              </div>

              {/* School Details */}
              <DetailCard icon={Building} title="Institution Details">
                <div className="space-y-2">
                  <InfoRow icon={Building} label="Full Name" value={(profile as SchoolProfile).name || 'N/A'} />
                  <InfoRow icon={GraduationCap} label="Short Name" value={(profile as SchoolProfile).short_name || 'N/A'} />
                  <InfoRow 
                    icon={Activity} 
                    label="Status" 
                    value={
                      <Badge variant={getStatusVariant(profile.status || 'active')}>
                        {profile.status || 'Active'}
                      </Badge>
                    }
                  />
                </div>
              </DetailCard>

              {/* Location */}
              <DetailCard icon={MapPin} title="Location Information">
                <div className="space-y-2">
                  <InfoRow icon={Home} label="Address" value={(profile as SchoolProfile).address || 'N/A'} />
                  <InfoRow icon={MapPin} label="Region" value={(profile as SchoolProfile).region?.name || 'N/A'} />
                </div>
              </DetailCard>

              {/* Timeline */}
              <DetailCard icon={Clock} title="Timeline">
                <div className="space-y-2">
                  <InfoRow icon={Calendar} label="Established" value={formatDate((profile as SchoolProfile).created_at)} />
                  <InfoRow icon={Clock} label="Last Updated" value={formatDate((profile as SchoolProfile).updated_at)} />
                </div>
              </DetailCard>
            </div>
          )}

          {(profile.type === 'coach' || profile.type === 'tournament_manager') && (
            <div className="px-6 py-6">
              <Card className="border-dashed">
                <CardContent className="py-12">
                  <div className="text-center">
                    <div className="mx-auto w-12 h-12 bg-muted rounded-full flex items-center justify-center mb-4">
                      <Users className="h-6 w-6 text-muted-foreground" />
                    </div>
                    <h3 className="font-semibold mb-2">Limited Access</h3>
                    <p className="text-sm text-muted-foreground mb-4 max-w-sm mx-auto">
                      Detailed information for {profile.type === 'coach' ? 'coaches' : 'tournament managers'} 
                      is managed through the user management system.
                    </p>
                    <div className="inline-flex items-center gap-1.5 text-xs text-muted-foreground">
                      <Info className="h-3 w-3" />
                      <span>Contact your administrator for more details</span>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          )}
        </div>

        {/* Footer */}
        <div className="px-6 py-3 border-t bg-muted/30">
          <div className="flex items-center justify-between text-xs text-muted-foreground">
            <div className="flex items-center gap-1">
              <Clock className="h-3 w-3" />
              <span>Last updated {formatDate(profile.updated_at)}</span>
            </div>
            <button 
              onClick={onClose}
              className="text-primary hover:text-primary/80 font-medium transition-colors flex items-center gap-1"
            >
              Close
              <ChevronRight className="h-3 w-3" />
            </button>
          </div>
        </div>
      </SheetContent>
    </Sheet>
  );
}