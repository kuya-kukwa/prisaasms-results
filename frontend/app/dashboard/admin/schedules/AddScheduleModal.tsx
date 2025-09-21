'use client'

import React from "react";
import { Sheet, SheetContent, SheetHeader, SheetTitle } from "@/components/ui/sheet";
import { ScheduleForm, type ScheduleFormValues } from "./ScheduleForm";

export function AddScheduleModal({
  open,
  onClose,
  onScheduleCreated,
  venues,
  sports,
  tournaments,
}: {
  open: boolean;
  onClose: () => void;
  onScheduleCreated: (schedule: ScheduleFormValues) => void;
  venues: Array<{ id: number; name: string; capacity?: number; is_indoor?: boolean; address?: string; region?: string }>;
  sports: Array<{ id: number; name: string; category?: string; gender_category?: string }>;
  tournaments: Array<{ id: number; name: string; sport_id?: number }>;
}) {
  const handleSubmit = async (data: ScheduleFormValues) => {
    onScheduleCreated(data);
  };

  return (
    <Sheet open={open} onOpenChange={onClose}>
      <SheetContent className="w-1/2 min-w-[600px] max-w-4xl p-0 flex flex-col">
        <SheetHeader className="px-6 pt-6 pb-2 flex-shrink-0">
          <SheetTitle>Add New Schedule</SheetTitle>
        </SheetHeader>
        <div className="px-6 pb-4 flex-1 overflow-y-auto">
          <div className="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <p className="text-sm text-blue-900">
              <strong>Note:</strong> All fields marked with <span className="text-red-500">*</span> are required.
            </p>
          </div>
          <ScheduleForm
            onSubmit={handleSubmit}
            onCancel={onClose}
            venues={venues}
            sports={sports}
            tournaments={tournaments}
          />
        </div>
      </SheetContent>
    </Sheet>
  );
}