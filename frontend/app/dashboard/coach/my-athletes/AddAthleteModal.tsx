"use client";

import * as React from "react";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
  DialogFooter,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { AthleteForm } from "@/app/dashboard/admin/profiles/AthleteForm";

interface AddAthleteModalProps {
  schools: Array<{ id: number; name: string }>;
  sportsList: Array<{ id: number; name: string }>;
  onAthleteAdded: () => void;
}

export default function AddAthleteModal({
  schools,
  sportsList,
  onAthleteAdded,
}: AddAthleteModalProps) {
  const [open, setOpen] = React.useState(false);
  const [isSaving, setIsSaving] = React.useState(false);

  const handleSubmit = async (formData: FormData) => {
    try {
      setIsSaving(true);

      const res = await fetch("http://localhost:8000/api/athletes", {
        method: "POST",
        body: formData,
        credentials: "include",
      });

      if (!res.ok) throw new Error("Failed to create athlete");

      setOpen(false);
      onAthleteAdded();
    } catch (err) {
      console.error("Error saving athlete:", err);
    } finally {
      setIsSaving(false);
    }
  };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button className="bg-blue-900 hover:bg-blue-600 text-white">
          + Add Athlete
        </Button>
      </DialogTrigger>

      <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>Add New Athlete</DialogTitle>
          <DialogDescription>
            Fill in the details to register a new athlete.
          </DialogDescription>
        </DialogHeader>

        <AthleteForm
          onSubmit={handleSubmit}
          onCancel={() => setOpen(false)}
          schools={schools}
          sportsList={sportsList}
          isLoading={isSaving}
        />

        <DialogFooter />
      </DialogContent>
    </Dialog>
  );
}
