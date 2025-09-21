import { ReactNode, useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { apiClient, type Region } from "@/src/lib/api";

// Standardized Label component for consistent form styling
function Label({ children }: { children: ReactNode }) {
  return <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">{children}</label>;
}

const scheduleSchema = z.object({
  title: z.string().min(1, "Title is required"),
  description: z.string().optional(),
  event_date: z.string().min(1, "Event date is required"),
  start_time: z.string().min(1, "Start time is required"),
  end_time: z.string().min(1, "End time is required"),
  sport_id: z.number().min(1, "Sport is required"),
  venue_id: z.number().min(1, "Venue is required"),
  tournament_id: z.number().min(1, "Tournament is required"),
  status: z.enum(["scheduled", "ongoing", "completed", "cancelled"]),
  duration_minutes: z.number().optional(),
  event_type: z.enum(["tournament", "practice", "training", "exhibition", "other"]),
  priority: z.enum(["low", "normal", "high", "urgent"]),
});

export type ScheduleFormValues = z.infer<typeof scheduleSchema>;

export function ScheduleForm({
  onSubmit,
  onCancel,
  defaultValues,
  venues,
  sports,
  tournaments,
  isLoading,
}: {
  onSubmit: (data: ScheduleFormValues) => void;
  onCancel?: () => void;
  defaultValues?: Partial<ScheduleFormValues>;
  venues: Array<{ id: number; name: string; capacity?: number; is_indoor?: boolean; address?: string; region?: string }>;
  sports: Array<{ id: number; name: string; category?: string; gender_category?: string }>;
  tournaments: Array<{ id: number; name: string; sport_id?: number }>;
  isLoading?: boolean;
}) {
  const [regions, setRegions] = useState<Region[]>([]);
  const [loadingRegions, setLoadingRegions] = useState(true);
  const [selectedRegion, setSelectedRegion] = useState<string>("");
  const {
    register,
    handleSubmit,
    formState: { errors },
    setValue,
    watch,
  } = useForm<ScheduleFormValues>({
    resolver: zodResolver(scheduleSchema),
    defaultValues: {
      event_type: "tournament",
      status: "scheduled",
      priority: "normal",
      ...defaultValues,
    },
  });

  const selectedVenueId = watch("venue_id");
  const selectedVenue = venues.find(v => v.id === selectedVenueId);

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

  const filteredVenues = selectedRegion
    ? venues.filter(venue => venue.region === selectedRegion)
    : venues;

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      {/* Event Information */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
          Event Information
        </h3>

        <div className="grid grid-cols-1 gap-4">
          <div>
            <Label>Event Title <span className="text-red-500">*</span></Label>
            <Input
              {...register("title")}
              placeholder="Enter event title (e.g., Basketball Championship Final)"
              required
              className="h-10"
            />
            {errors.title && <p className="text-xs text-red-500 mt-1">{errors.title.message}</p>}
          </div>

          <div>
            <Label>Description</Label>
            <Textarea
              {...register("description")}
              placeholder="Brief description of the event (optional)"
              className="min-h-[80px] resize-none"
            />
            {errors.description && <p className="text-xs text-red-500 mt-1">{errors.description.message}</p>}
          </div>
        </div>
      </div>

      {/* Date & Time */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
          Date & Time
        </h3>

        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <Label>Event Date <span className="text-red-500">*</span></Label>
            <Input
              {...register("event_date")}
              type="date"
              required
              className="h-10"
            />
            {errors.event_date && <p className="text-xs text-red-500 mt-1">{errors.event_date.message}</p>}
          </div>

          <div>
            <Label>Start Time <span className="text-red-500">*</span></Label>
            <Input
              {...register("start_time")}
              type="time"
              required
              className="h-10"
            />
            {errors.start_time && <p className="text-xs text-red-500 mt-1">{errors.start_time.message}</p>}
          </div>

          <div>
            <Label>End Time <span className="text-red-500">*</span></Label>
            <Input
              {...register("end_time")}
              type="time"
              required
              className="h-10"
            />
            {errors.end_time && <p className="text-xs text-red-500 mt-1">{errors.end_time.message}</p>}
          </div>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <Label>Duration (minutes)</Label>
            <Input
              {...register("duration_minutes", { valueAsNumber: true })}
              type="number"
              placeholder="Auto-calculated"
              className="h-10"
            />
            <p className="text-xs text-gray-500 mt-1">
              Leave empty for auto-calculation from start/end time
            </p>
            {errors.duration_minutes && <p className="text-xs text-red-500 mt-1">{errors.duration_minutes.message}</p>}
          </div>

          <div>
            <Label>Priority Level</Label>
            <Select onValueChange={(value) => setValue("priority", value as ScheduleFormValues["priority"])}>
              <SelectTrigger className="h-10">
                <SelectValue placeholder="Select priority" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="low">Low</SelectItem>
                <SelectItem value="normal">Normal</SelectItem>
                <SelectItem value="high">High</SelectItem>
                <SelectItem value="urgent">Urgent</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      </div>

      {/* Location & Venue */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
          Location & Venue
        </h3>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <Label>Region Filter</Label>
            <Select value={selectedRegion} onValueChange={setSelectedRegion}>
              <SelectTrigger className="h-10">
                <SelectValue placeholder={loadingRegions ? "Loading regions..." : "All regions"} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">All regions</SelectItem>
                {regions.map((region) => (
                  <SelectItem key={region.id} value={region.name}>
                    {region.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div>
            <Label>Venue <span className="text-red-500">*</span></Label>
            <Select onValueChange={(value) => setValue("venue_id", parseInt(value))}>
              <SelectTrigger className="h-10">
                <SelectValue placeholder="Select venue" />
              </SelectTrigger>
              <SelectContent>
                {filteredVenues.map((venue) => (
                  <SelectItem key={venue.id} value={venue.id.toString()}>
                    {venue.name}
                    {venue.capacity && ` (${venue.capacity} capacity)`}
                    {venue.is_indoor !== undefined && ` • ${venue.is_indoor ? 'Indoor' : 'Outdoor'}`}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.venue_id && <p className="text-xs text-red-500 mt-1">{errors.venue_id.message}</p>}
          </div>
        </div>

        {selectedVenue && (
          <div className="bg-blue-50 dark:bg-blue-950/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800">
            <p className="text-sm text-blue-800 dark:text-blue-200">
              <strong>Venue Details:</strong> {selectedVenue.name}
              {selectedVenue.capacity && ` • Capacity: ${selectedVenue.capacity}`}
              {selectedVenue.is_indoor !== undefined && ` • ${selectedVenue.is_indoor ? 'Indoor' : 'Outdoor'}`}
            </p>
          </div>
        )}
      </div>

      {/* Sport & Tournament Details */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
          Sport & Tournament Details
        </h3>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <Label>Sport <span className="text-red-500">*</span></Label>
            <Select onValueChange={(value) => setValue("sport_id", parseInt(value))}>
              <SelectTrigger className="h-10">
                <SelectValue placeholder="Select sport" />
              </SelectTrigger>
              <SelectContent>
                {sports.map((sport) => (
                  <SelectItem key={sport.id} value={sport.id.toString()}>
                    {sport.name}
                    {sport.category && ` (${sport.category})`}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.sport_id && <p className="text-xs text-red-500 mt-1">{errors.sport_id.message}</p>}
          </div>

          <div>
            <Label>Tournament <span className="text-red-500">*</span></Label>
            <Select onValueChange={(value) => setValue("tournament_id", parseInt(value))}>
              <SelectTrigger className="h-10">
                <SelectValue placeholder="Select tournament" />
              </SelectTrigger>
              <SelectContent>
                {tournaments.map((tournament) => (
                  <SelectItem key={tournament.id} value={tournament.id.toString()}>
                    {tournament.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.tournament_id && <p className="text-xs text-red-500 mt-1">{errors.tournament_id.message}</p>}
          </div>

          <div>
            <Label>Event Type</Label>
            <Select onValueChange={(value) => setValue("event_type", value as ScheduleFormValues["event_type"])}>
              <SelectTrigger className="h-10">
                <SelectValue placeholder="Select event type" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="tournament">Tournament</SelectItem>
                <SelectItem value="practice">Practice</SelectItem>
                <SelectItem value="training">Training</SelectItem>
                <SelectItem value="exhibition">Exhibition</SelectItem>
                <SelectItem value="other">Other</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div>
            <Label>Status</Label>
            <Select onValueChange={(value) => setValue("status", value as ScheduleFormValues["status"])}>
              <SelectTrigger className="h-10">
                <SelectValue placeholder="Select status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="scheduled">Scheduled</SelectItem>
                <SelectItem value="ongoing">Ongoing</SelectItem>
                <SelectItem value="completed">Completed</SelectItem>
                <SelectItem value="cancelled">Cancelled</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      </div>

      {/* Form Actions */}
      <div className="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
        <Button
          type="submit"
          disabled={isLoading}
          className="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white"
        >
          {isLoading ? "Creating Schedule..." : "Create Schedule"}
        </Button>

        {onCancel && (
          <Button
            type="button"
            variant="outline"
            onClick={onCancel}
            disabled={isLoading}
            className="flex-1 sm:flex-none"
          >
            Cancel
          </Button>
        )}
      </div>
    </form>
  );
}