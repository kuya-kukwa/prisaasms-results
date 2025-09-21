import { ReactNode } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";

function Label({ children }: { children: ReactNode }) {
  return <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">{children}</label>;
}

const officialSchema = z.object({
  first_name: z.string().min(1, "First name is required"),
  last_name: z.string().min(1, "Last name is required"),
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
  contact_number: z.string().min(1, "Contact number is required"),
  email: z.string().email("Invalid email").min(1, "Email is required"),
  certification_level: z.string().min(1, "Certification level is required"),
  official_type: z.string().min(1, "Official type is required"),
  sports_certified: z.array(z.string()).min(1, "At least one sport certification is required"),
  years_experience: z.number().min(0, "Years of experience is required"),
  status: z.enum(["active", "inactive", "suspended", "retired"]),
  available_for_assignment: z.boolean(),
  availability_schedule: z.array(z.string()).min(1, "Availability schedule is required"),
  avatar: z.any().optional(),
});

export type OfficialFormValues = z.infer<typeof officialSchema>;

export function OfficialForm({
  onSubmit,
  onCancel,
  defaultValues,
  isLoading,
}: {
  onSubmit: (data: OfficialFormValues) => void;
  onCancel?: () => void;
  defaultValues?: Partial<OfficialFormValues>;
  isLoading?: boolean;
}) {
  const {
    register,
    handleSubmit,
    formState: { errors },
    setValue,
    watch,
  } = useForm<OfficialFormValues>({
    resolver: zodResolver(officialSchema),
    defaultValues: {
      sports_certified: [],
      available_for_assignment: false,
      status: "active",
      availability_schedule: [],
      ...defaultValues,
    },
  });

  const watchedSports = watch("sports_certified") || [];

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
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
            <Label>Gender <span className="text-red-500">*</span></Label>
            <select {...register("gender")} required className="w-full h-10 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
            {errors.gender && <p className="text-xs text-red-500 mt-1">{errors.gender.message}</p>}
          </div>
          <div>
            <Label>Date of Birth <span className="text-red-500">*</span></Label>
            <Input type="date" {...register("birthdate")} required className="h-10" />
            {errors.birthdate && <p className="text-xs text-red-500 mt-1">{errors.birthdate.message}</p>}
          </div>
          <div>
            <Label>Email <span className="text-red-500">*</span></Label>
            <Input type="email" {...register("email")} placeholder="Enter email address" required className="h-10" />
            {errors.email && <p className="text-xs text-red-500 mt-1">{errors.email.message}</p>}
          </div>
          <div>
            <Label>Contact Number <span className="text-red-500">*</span></Label>
            <Input {...register("contact_number")} placeholder="Enter contact number" required className="h-10" />
            {errors.contact_number && <p className="text-xs text-red-500 mt-1">{errors.contact_number.message}</p>}
          </div>
        </div>
      </div>

      {/* Certification Information */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Certification Information</h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <Label>Certification Level <span className="text-red-500">*</span></Label>
            <select {...register("certification_level")} required className="w-full h-10 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Level</option>
              <option value="trainee">Trainee</option>
              <option value="local">Local</option>
              <option value="regional">Regional</option>
              <option value="national">National</option>
            </select>
            {errors.certification_level && <p className="text-xs text-red-500 mt-1">{errors.certification_level.message}</p>}
          </div>
          <div>
            <Label>Official Type <span className="text-red-500">*</span></Label>
            <select {...register("official_type")} required className="w-full h-10 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Type</option>
              <option value="referee">Referee</option>
              <option value="umpire">Umpire</option>
              <option value="judge">Judge</option>
              <option value="timekeeper">Timekeeper</option>
              <option value="scorer">Scorer</option>
              <option value="technical_official">Technical Official</option>
              <option value="line_judge">Line Judge</option>
              <option value="table_official">Table Official</option>
              <option value="starter">Starter</option>
              <option value="field_judge">Field Judge</option>
              <option value="track_judge">Track Judge</option>
              <option value="swimming_judge">Swimming Judge</option>
              <option value="diving_judge">Diving Judge</option>
              <option value="gymnastics_judge">Gymnastics Judge</option>
              <option value="athletics_official">Athletics Official</option>
              <option value="team_manager">Team Manager</option>
              <option value="match_commissioner">Match Commissioner</option>
              <option value="protest_jury_member">Protest Jury Member</option>
            </select>
            {errors.official_type && <p className="text-xs text-red-500 mt-1">{errors.official_type.message}</p>}
          </div>
          <div>
            <Label>Years of Experience <span className="text-red-500">*</span></Label>
            <Input type="number" {...register("years_experience", { valueAsNumber: true })} placeholder="0" required className="h-10" />
            {errors.years_experience && <p className="text-xs text-red-500 mt-1">{errors.years_experience.message}</p>}
          </div>
          <div>
            <Label>Status <span className="text-red-500">*</span></Label>
            <select {...register("status")} required className="w-full h-10 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="suspended">Suspended</option>
              <option value="retired">Retired</option>
            </select>
            {errors.status && <p className="text-xs text-red-500 mt-1">{errors.status.message}</p>}
          </div>
        </div>
      </div>

      {/* Sports Certification */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Sports Certification</h3>
        <div>
          <Label>Sports Certified For <span className="text-red-500">*</span> (Select all that apply)</Label>
          <div className="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-2">
            {["Basketball", "Volleyball", "Soccer", "Tennis", "Badminton", "Swimming", "Athletics", "Table Tennis", "Chess"].map((sport) => (
              <label key={sport} className="flex items-center space-x-2">
                <input
                  type="checkbox"
                  checked={watchedSports.includes(sport)}
                  onChange={(e) => {
                    const currentSports = watchedSports || [];
                    if (e.target.checked) {
                      setValue("sports_certified", [...currentSports, sport]);
                    } else {
                      setValue("sports_certified", currentSports.filter(s => s !== sport));
                    }
                  }}
                  className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span className="text-sm text-gray-700 dark:text-gray-300">{sport}</span>
              </label>
            ))}
          </div>
          {errors.sports_certified && <p className="text-xs text-red-500 mt-1">{errors.sports_certified.message}</p>}
        </div>
      </div>

      {/* Availability */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Availability</h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <Label>Available for Assignment <span className="text-red-500">*</span></Label>
            <select
              {...register("available_for_assignment", {
                setValueAs: (value) => value === "true"
              })}
              required
              className="w-full h-10 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Select Availability</option>
              <option value="true">Yes</option>
              <option value="false">No</option>
            </select>
          </div>
          <div>
            <Label>Availability Schedule <span className="text-red-500">*</span></Label>
            <Input
              {...register("availability_schedule", {
                setValueAs: (value) => value ? [value] : []
              })}
              placeholder="e.g., Weekends, Evenings"
              required
              className="h-10"
            />
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
            onChange={(e: React.ChangeEvent<HTMLInputElement>) => setValue("avatar", e.target.files?.[0])}
            className="h-10"
          />
        </div>
      </div>

      {/* Form Actions */}
      <div className="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
        {onCancel && (
          <Button type="button" variant="outline" onClick={onCancel} className="px-6">
            Cancel
          </Button>
        )}
        <Button type="submit" disabled={isLoading} className="px-6 bg-blue-900 hover:bg-blue-500 text-white">
          {isLoading ? "Saving..." : "Save Official"}
        </Button>
      </div>
    </form>
  );
}
