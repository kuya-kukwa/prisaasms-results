'use client'

import React from 'react';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import toast from 'react-hot-toast';
import { adminProfilesService, type Profile } from '@/src/lib/admin-profiles-api';

export function RegisterAuthorizedUserModal({ open, onClose, onRegistered, schools }:
  { open: boolean; onClose: () => void; onRegistered: (profile: Profile | null) => void; schools: Array<{ id: number; name: string }> }) {
  const [values, setValues] = React.useState({ first_name: '', last_name: '', email: '', contact_number: '', school_id: '' });
  const [password, setPassword] = React.useState('');
  const [avatarFile, setAvatarFile] = React.useState<File | undefined>();
  const [isLoading, setIsLoading] = React.useState(false);
  const [role, setRole] = React.useState<'coach' | 'tournament_manager'>('coach');
  const [showPassword, setShowPassword] = React.useState(false);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setValues(v => ({ ...v, [name]: value }));
  };

  const isFormValid = () => {
    return values.first_name.trim() &&
           values.last_name.trim() &&
           values.email.trim() &&
           values.contact_number.trim() &&
           password.trim();
  };

  const submit = async () => {
    if (!isFormValid()) {
      toast.error('Please fill in all required fields');
      return;
    }
    setIsLoading(true);
    try {
      type RegisterPayload = {
        first_name: string;
        last_name: string;
        email: string;
        contact_number?: string;
        school_id?: number;
        avatarFile?: File;
        password?: string;
        password_confirmation?: string;
      };

      const payload: RegisterPayload = {
        first_name: values.first_name,
        last_name: values.last_name,
        email: values.email,
        contact_number: values.contact_number || undefined,
      };
      if (values.school_id) payload.school_id = Number(values.school_id);
      if (avatarFile) payload.avatarFile = avatarFile;
      if (password) {
        payload.password = password;
        payload.password_confirmation = password;
      }

      const created = await adminProfilesService.createProfile(role, payload);
      if (created) {
        toast.success(`${role === 'coach' ? 'Coach' : 'Tournament Manager'} registered successfully!`);
      } else {
        toast.error('Registration failed');
      }
      onRegistered(created as Profile | null);
  // Clear sensitive fields from local state immediately
  setPassword('');
  setAvatarFile(undefined);
  setValues({ first_name: '', last_name: '', email: '', contact_number: '', school_id: '' });
  onClose();
    } catch (err) {
      console.error('Registration error', err);
      toast.error('Registration error');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <Sheet open={open} onOpenChange={onClose}>
      <SheetContent className="w-1/2 min-w-[600px] max-w-4xl p-0 flex flex-col">
        <SheetHeader className="px-6 pt-6 pb-2 flex-shrink-0">
          <SheetTitle>Register Authorized User</SheetTitle>
        </SheetHeader>
        <div className="px-6 pb-6 flex-1 overflow-y-auto">
          <div className="space-y-4">
            <div className="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
              <p className="text-sm text-blue-900">
                <strong>Note:</strong> All fields marked with <span className="text-red-500">*</span> are required.
              </p>
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Role</label>
              <select value={role} onChange={e => setRole(e.target.value as 'coach' | 'tournament_manager')} className="w-[42%] h-10 px-3 border rounded-lg">
                <option value="coach">Coach</option>
                <option value="tournament_manager">Tournament Manager</option>
              </select>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium mb-1">First Name <span className="text-red-500">*</span></label>
                <input name="first_name" value={values.first_name} onChange={handleChange} required className="w-full h-10 px-3 border rounded-lg" />
              </div>
              <div>
                <label className="block text-sm font-medium mb-1">Last Name <span className="text-red-500">*</span></label>
                <input name="last_name" value={values.last_name} onChange={handleChange} required className="w-full h-10 px-3 border rounded-lg" />
              </div>
              <div>
                <label className="block text-sm font-medium mb-1">Email <span className="text-red-500">*</span></label>
                <input name="email" type="email" value={values.email} onChange={handleChange} required className="w-full h-10 px-3 border rounded-lg" />
              </div>
              <div>
                <label className="block text-sm font-medium mb-1">Contact Number <span className="text-red-500">*</span></label>
                <input name="contact_number" value={values.contact_number} onChange={handleChange} required className="w-full h-10 px-3 border rounded-lg" />
              </div>

              <div>
                <label className="block text-sm font-medium mb-1">Password <span className="text-red-500">*</span></label>
                <div className="relative">
                  <input
                    name="password"
                    type={showPassword ? "text" : "password"}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required
                    placeholder="Enter password"
                    className="w-full h-10 px-3 pr-10 border rounded-lg"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                  >
                    {showPassword ? (
                      <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                      </svg>
                    ) : (
                      <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    )}
                  </button>
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium mb-1">School (optional)</label>
                <select name="school_id" value={values.school_id} onChange={handleChange} className="w-full h-10 px-3 border rounded-lg">
                  <option value="">Select school</option>
                  {schools.map(s => (<option key={s.id} value={s.id}>{s.name}</option>))}
                </select>
              </div>

              <div className="sm:col-span-2">
                <label className="block text-sm font-medium mb-1">Profile Photo (optional)</label>
                <input type="file" accept="image/*" onChange={(e: React.ChangeEvent<HTMLInputElement>) => setAvatarFile(e.target.files?.[0])} />
              </div>
            </div>

            <div className="flex justify-end gap-2 mt-4">
              <Button variant="outline" onClick={onClose}>Cancel</Button>
              <Button onClick={submit} disabled={isLoading || !isFormValid()} className="bg-blue-900 text-white">{isLoading ? 'Registering...' : 'Register'}</Button>
            </div>
          </div>
        </div>
      </SheetContent>
    </Sheet>
  );
}
