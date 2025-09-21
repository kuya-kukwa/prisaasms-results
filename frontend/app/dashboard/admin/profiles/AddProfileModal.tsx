'use client'

import React, { useState } from "react";
import { Sheet, SheetContent, SheetHeader, SheetTitle } from "@/components/ui/sheet";
import { AthleteForm } from "./AthleteForm";
import { OfficialForm } from "./OfficialForm";
import { SchoolForm } from "./SchoolForm";
import { adminProfilesService, type Profile } from "@/src/lib/admin-profiles-api";
import toast from 'react-hot-toast';

const PROFILE_TYPES = [
  { value: "athlete", label: "Athlete" },
  { value: "official", label: "Official" },
  { value: "school", label: "School" },
];

export function AddProfileModal({
  open,
  onClose,
  onProfileCreated,
  schools,
  sportsList,
}: {
  open: boolean;
  onClose: () => void;
  onProfileCreated: (profile: Profile) => void;
  schools: Array<{ id: number; name: string }>;
  sportsList: Array<{ id: number; name: string }>;
}) {
  const [profileType, setProfileType] = useState<string>("athlete");
  const [isLoading, setIsLoading] = useState(false);
  const handleSubmit = async (data: unknown) => {
    setIsLoading(true);
    try {
      // Connect to backend
      const newProfile = await adminProfilesService.createProfile(profileType, data as Partial<Profile> & { avatarFile?: File });
      if (newProfile) {
        toast.success('Profile created');
        onProfileCreated(newProfile as Profile);
        onClose();
      } else {
        toast.error('Failed to create profile');
      }
    } catch (err) {
      toast.error(`Error creating profile: ${err}`);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <Sheet open={open} onOpenChange={onClose}>
      <SheetContent className="w-1/2 min-w-[700px] max-w-5xl p-0 flex flex-col overflow-hidden">
        <SheetHeader className="px-6 pt-6 pb-2 flex-shrink-0">
          <SheetTitle>Add Profile</SheetTitle>
        </SheetHeader>
        <div className="px-6 pb-4 flex-1 overflow-y-auto">
          <div className="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <p className="text-sm text-blue-900">
              <strong>Note:</strong> All fields marked with <span className="text-red-500">*</span> are required.
            </p>
          </div>
          <div className="mb-4">
            <label className="block text-sm font-medium mb-1">Profile Type</label>
            <select
              value={profileType}
              onChange={e => setProfileType(e.target.value)}
              className="w-full h-10 px-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none"
            >
              {PROFILE_TYPES.map(t => (
                <option key={t.value} value={t.value}>{t.label}</option>
              ))}
            </select>
          </div>
          {profileType === "athlete" && (
            <AthleteForm
              onSubmit={handleSubmit}
              onCancel={onClose}
              schools={schools}
              sportsList={sportsList}
              isLoading={isLoading}
            />
          )}
          {profileType === "official" && (
            <OfficialForm
              onSubmit={handleSubmit}
              onCancel={onClose}
              isLoading={isLoading}
            />
          )}
          {profileType === "school" && (
            <SchoolForm
              onSubmit={handleSubmit}
              onCancel={onClose}
              isLoading={isLoading}
            />
          )}
        </div>
      </SheetContent>
    </Sheet>
  );
}
