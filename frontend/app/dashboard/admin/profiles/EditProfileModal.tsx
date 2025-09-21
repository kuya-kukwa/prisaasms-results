'use client'

import React, { useState } from "react";
import { Sheet, SheetContent, SheetHeader, SheetTitle } from "@/components/ui/sheet";
import { AthleteForm } from "./AthleteForm";
import { OfficialForm } from "./OfficialForm";
import { SchoolForm } from "./SchoolForm";
import { adminProfilesService, type Profile, type AthleteProfile, type OfficialProfile, type SchoolProfile } from "@/src/lib/admin-profiles-api";
import toast from 'react-hot-toast';

export function EditProfileModal({
  open,
  onClose,
  onProfileUpdated,
  profile,
  schools,
  sportsList,
}: {
  open: boolean;
  onClose: () => void;
  onProfileUpdated: (profile: Profile) => void;
  profile: Profile | null;
  schools: Array<{ id: number; name: string }>;
  sportsList: Array<{ id: number; name: string }>;
}) {
  const [isLoading, setIsLoading] = useState(false);

  const handleSubmit = async (data: unknown) => {
    if (!profile) return;

    setIsLoading(true);
    try {
      // Connect to backend update
      const updatedProfile = await adminProfilesService.updateProfile(profile.type, profile.id, data as Partial<Profile> & { avatarFile?: File });
      if (updatedProfile) {
        toast.success('Profile updated successfully');
        onProfileUpdated(updatedProfile as Profile);
        onClose();
      } else {
        toast.error('Failed to update profile');
      }
    } catch (err) {
      toast.error(`Error updating profile: ${err}`);
    } finally {
      setIsLoading(false);
    }
  };

  // Transform profile data for form consumption
  const getAthleteFormData = (profile: Profile) => {
    const athleteProfile = profile as AthleteProfile;
    return {
      first_name: athleteProfile.first_name,
      last_name: athleteProfile.last_name,
      email: athleteProfile.email,
      gender: athleteProfile.gender,
      birthdate: athleteProfile.created_at ? new Date(athleteProfile.created_at).toISOString().split('T')[0] : '',
      school_id: athleteProfile.school_id,
      sport_id: athleteProfile.sport?.id || 1,
      status: (athleteProfile.status === 'pending' ? 'active' : athleteProfile.status === 'active' || athleteProfile.status === 'inactive' ? athleteProfile.status : 'active') as 'active' | 'inactive' | 'injured' | 'suspended',
      avatar: athleteProfile.avatar
    };
  };

  const getOfficialFormData = (profile: Profile) => {
    const officialProfile = profile as OfficialProfile;
    return {
      first_name: officialProfile.first_name,
      last_name: officialProfile.last_name,
      gender: 'male' as const,
      birthdate: officialProfile.created_at ? new Date(officialProfile.created_at).toISOString().split('T')[0] : '',
      contact_number: officialProfile.contact_number || '',
      email: officialProfile.email,
      certification_level: officialProfile.certification_level || 'trainee',
      official_type: officialProfile.official_type || 'referee',
      sports_certified: ['Basketball'],
      years_experience: 0,
      status: (officialProfile.status === 'pending' ? 'active' : officialProfile.status === 'active' || officialProfile.status === 'inactive' ? officialProfile.status : 'active') as 'active' | 'inactive' | 'suspended' | 'retired',
      available_for_assignment: true,
      availability_schedule: ['Weekends'],
      avatar: officialProfile.avatar
    };
  };

  const getSchoolFormData = (profile: Profile) => {
    const schoolProfile = profile as SchoolProfile;
    return {
      name: schoolProfile.name,
      short_name: schoolProfile.short_name || '',
      address: schoolProfile.address || '',
      region_id: schoolProfile.region?.id?.toString() || '',
      status: schoolProfile.status,
      avatar: schoolProfile.avatar
    };
  };

  return (
    <Sheet open={open} onOpenChange={onClose}>
      <SheetContent className="w-1/2 min-w-[700px] max-w-5xl p-0 flex flex-col overflow-hidden">
        <SheetHeader className="px-6 pt-6 pb-2 flex-shrink-0">
          <SheetTitle>Edit Profile</SheetTitle>
        </SheetHeader>
        <div className="px-6 pb-4 flex-1 overflow-y-auto">
          <div className="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <p className="text-sm text-blue-900">
              <strong>Note:</strong> All fields marked with <span className="text-red-500">*</span> are required.
            </p>
          </div>

          {profile && (
            <>
              {profile.type === "athlete" && (
                <AthleteForm
                  onSubmit={handleSubmit}
                  onCancel={onClose}
                  schools={schools}
                  sportsList={sportsList}
                  isLoading={isLoading}
                  defaultValues={getAthleteFormData(profile)}
                />
              )}
              {profile.type === "official" && (
                <OfficialForm
                  onSubmit={handleSubmit}
                  onCancel={onClose}
                  isLoading={isLoading}
                  defaultValues={getOfficialFormData(profile)}
                />
              )}
              {profile.type === "school" && (
                <SchoolForm
                  onSubmit={handleSubmit}
                  onCancel={onClose}
                  isLoading={isLoading}
                  defaultValues={getSchoolFormData(profile)}
                />
              )}
              {(profile.type === "coach" || profile.type === "tournament_manager") && (
                <div className="text-center py-8">
                  <p className="text-muted-foreground">
                    Coach and Tournament Manager profiles cannot be edited through this interface.
                  </p>
                  <p className="text-sm text-muted-foreground mt-2">
                    Please use the user management system to update these profiles.
                  </p>
                </div>
              )}
            </>
          )}
        </div>
      </SheetContent>
    </Sheet>
  );
}