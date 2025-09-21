"use client";

import { ReactNode } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";

// Standardized Label component
function Label({ children }: { children: ReactNode }) {
  return <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">{children}</label>;
}

// Schema
const athleteSchema = z.object({
  first_name: z.string().min(1, "First name is required"),
  last_name: z.string().min(1, "Last name is required"),
  email: z.string().email("Invalid email address").min(1, "Email is required"),
  gender: z.enum(["male", "female"]),
  birthdate: z.string()
    .min(1, "Birthdate is required")
    .refine(
      (val) => {
        if (!val) return false;
        const inputDate = new Date(val);
        const today = new Date();
        today.setHours(0,0,0,0);
        return inputDate < today;
      },
      { message: "Birthdate must be in the past" }
    ),
  school_id: z.number(),
  sport_id: z.number(),
  status: z.enum(["active", "inactive", "injured", "suspended"]),
  avatar: z.any().optional(),
});

export type AthleteFormValues = z.infer<typeof athleteSchema>;

export function AthleteForm({
  onSubmit,
  onCancel,
  defaultValues,
  schools,
  sportsList,
  isLoading,
}: {
  onSubmit: (data: FormData) => void;
  onCancel?: () => void;
  defaultValues?: Partial<AthleteFormValues>;
  schools: Array<{ id: number; name: string }>;
  sportsList: Array<{ id: number; name: string }>;
  isLoading?: boolean;
}) {
  const {
    register,
    handleSubmit,
    formState: { errors },
    setValue,
  } = useForm<AthleteFormValues>({
    resolver: zodResolver(athleteSchema),
    defaultValues,
  });

  const handleFormSubmit = (data: AthleteFormValues) => {
    const formData = new FormData();
    Object.entries(data).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value as any);
      }
    });
    onSubmit(formData);
  };

  return (
    <form onSubmit={handleSubmit(handleFormSubmit)} className="space-y-6">
      {/* Personal Information */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Personal Information</h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <Label>First Name <span className="text-red-500">*</span></Label>
            <Input {...register("first_name")} placeholder="Enter first name" required className="h-10" />
            {errors.first_name && <p className="text-xs text-red-500 mt-1">{errors.first_name.message}</p>}
          </div>

          <div>
            <Label>Last Name <span className="text-red-500">*</span></Label>
            <Input {...register("last_name")} placeholder="Enter last name" required className="h-10" />
            {errors.last_name && <p className="text-xs text-red-500 mt-1">{errors.last_name.message}</p>}
          </div>

          <div>
            <Label>Email <span className="text-red-500">*</span></Label>
            <Input {...register("email")} type="email" placeholder="Enter email address" required className="h-10" />
            {errors.email && <p className="text-xs text-red-500 mt-1">{errors.email.message}</p>}
          </div>

          <div>
            <Label>Gender <span className="text-red-500">*</span></Label>
            <select
              {...register("gender")}
              defaultValue={defaultValues?.gender || "male"}
              required
              className="w-full h-10 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
            {errors.gender && <p className="text-xs text-red-500 mt-1">{errors.gender.message}</p>}
          </div>

          <div>
            <Label>Date of Birth <span className="text-red-500">*</span></Label>
            <Input
              type="date"
              {...register("birthdate")}
              required
              className="h-10"
            />
            {errors.birthdate && <p className="text-xs text-red-500 mt-1">{errors.birthdate.message}</p>}
          </div>
        </div>
      </div>

      {/* Athletic Information */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Athletic Information</h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <Label>School <span className="text-red-500">*</span></Label>
            <select
              {...register("school_id", { valueAsNumber: true })}
              required
              className="w-full h-10 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Select School</option>
              {schools.map((school) => (
                <option key={school.id} value={school.id}>
                  {school.name}
                </option>
              ))}
            </select>
            {errors.school_id && <p className="text-xs text-red-500 mt-1">{errors.school_id.message}</p>}
          </div>

          <div>
            <Label>Sport <span className="text-red-500">*</span></Label>
            <select
              {...register("sport_id", { valueAsNumber: true })}
              required
              className="w-full h-10 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Select Sport</option>
              {sportsList.length > 0 ? (
                sportsList.map((sport) => (
                  <option key={sport.id} value={sport.id}>
                    {sport.name}
                  </option>
                ))
              ) : (
                <>
                  <option value="" disabled>Loading sports...</option>
                </>
              )}
            </select>
            {errors.sport_id && <p className="text-xs text-red-500 mt-1">{errors.sport_id.message}</p>}
          </div>

          <div>
            <Label>Status <span className="text-red-500">*</span></Label>
            <select
              {...register("status")}
              defaultValue={defaultValues?.status || "active"}
              required
              className="w-full h-10 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="injured">Injured</option>
              <option value="suspended">Suspended</option>
            </select>
            {errors.status && <p className="text-xs text-red-500 mt-1">{errors.status.message}</p>}
          </div>
        </div>
      </div>

      {/* Avatar Upload */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Profile Picture</h3>
        <div>
          <Label>Profile Picture (Optional)</Label>
          <Input
            type="file"
            accept="image/*"
            onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
              setValue("avatar", e.target.files?.[0])
            }
            className="h-10"
          />
        </div>
      </div>

      {/* Actions */}
      <div className="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
        {onCancel && (
          <Button type="button" variant="outline" onClick={onCancel} className="px-6">
            Cancel
          </Button>
        )}
        <Button type="submit" disabled={isLoading} className="px-6 bg-blue-900 hover:bg-blue-500 text-white">
          {isLoading ? "Saving..." : "Save Athlete"}
        </Button>
      </div>
    </form>
  );
}
