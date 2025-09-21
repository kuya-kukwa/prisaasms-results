import { ReactNode, useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { apiClient, type Region } from "@/src/lib/api";

function Label({ children }: { children: ReactNode }) {
  return <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">{children}</label>;
}

const schoolSchema = z.object({
  name: z.string().min(1, "School name is required"),
  short_name: z.string().min(1, "Short name is required"),
  address: z.string().min(1, "Address is required"),
  region_id: z.string().min(1, "Region is required"),
  status: z.enum(["active", "inactive"]),
  avatar: z.any().optional(),
});

export type SchoolFormValues = z.infer<typeof schoolSchema>;

export function SchoolForm({
  onSubmit,
  onCancel,
  defaultValues,
  isLoading,
}: {
  onSubmit: (data: SchoolFormValues) => void;
  onCancel?: () => void;
  defaultValues?: Partial<SchoolFormValues>;
  isLoading?: boolean;
}) {
  const [regions, setRegions] = useState<Region[]>([]);
  const [loadingRegions, setLoadingRegions] = useState(true);

  const {
    register,
    handleSubmit,
    formState: { errors },
    setValue,
    watch,
  } = useForm<SchoolFormValues>({
    resolver: zodResolver(schoolSchema),
    defaultValues,
  });

  // Load regions
  useEffect(() => {
    const loadRegions = async () => {
      try {
        const response = await apiClient.admin.regions.list();
        setRegions(response.data || []);
      } catch (error) {
        console.error('Error loading regions:', error);
      } finally {
        setLoadingRegions(false);
      }
    };

    loadRegions();
  }, []);

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      {/* School Information */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">School Information</h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <Label>School Name <span className="text-red-500">*</span></Label>
            <Input {...register("name")} placeholder="Enter school name" required className="h-10" />
            {errors.name && <p className="text-xs text-red-500 mt-1">{errors.name.message}</p>}
          </div>
          <div>
            <Label>Short Name <span className="text-red-500">*</span></Label>
            <Input {...register("short_name")} placeholder="Enter short name" required className="h-10" />
            {errors.short_name && <p className="text-xs text-red-500 mt-1">{errors.short_name.message}</p>}
          </div>
          <div>
            <Label>Region <span className="text-red-500">*</span></Label>
            <Select
              value={watch("region_id")}
              onValueChange={(value) => setValue("region_id", value)}
              disabled={loadingRegions}
            >
              <SelectTrigger className="h-10">
                <SelectValue placeholder={loadingRegions ? "Loading regions..." : "Select a region"} />
              </SelectTrigger>
              <SelectContent>
                {regions.map((region) => (
                  <SelectItem key={region.id} value={region.id.toString()}>
                    {region.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.region_id && <p className="text-xs text-red-500 mt-1">{errors.region_id.message}</p>}
          </div>
          <div>
            <Label>Status <span className="text-red-500">*</span></Label>
            <select {...register("status")} required className="w-full h-10 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            {errors.status && <p className="text-xs text-red-500 mt-1">{errors.status.message}</p>}
          </div>
          <div className="sm:col-span-2">
            <Label>Address <span className="text-red-500">*</span></Label>
            <Input {...register("address")} placeholder="Enter full address" required className="h-10" />
            {errors.address && <p className="text-xs text-red-500 mt-1">{errors.address.message}</p>}
          </div>
        </div>
      </div>

      {/* School Logo */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">School Logo</h3>
        <div>
          <Label>School Logo (Optional)</Label>
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
        <Button type="submit" disabled={isLoading || loadingRegions} className="px-6 bg-blue-900 hover:bg-blue-500 text-white">
          {isLoading ? "Saving..." : "Save School"}
        </Button>
      </div>
    </form>
  );
}
