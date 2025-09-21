'use client';

import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Shield, Mail, Lock, Eye, EyeOff, Loader, Key } from 'lucide-react';
import { useAuth } from '@/src/contexts/AuthContext';

interface FormData {
  email: string;
  password: string;
}

export default function LoginPage() {
  const { login, isAuthenticated, redirectToDashboard, loading } = useAuth();

  const [formData, setFormData] = useState<FormData>({ email: '', password: '' });
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  // Redirect if already authenticated
  useEffect(() => {
    if (isAuthenticated && !loading) {
      redirectToDashboard();
    }
  }, [isAuthenticated, loading, redirectToDashboard]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const togglePasswordVisibility = () => setShowPassword(!showPassword);

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setIsLoading(true);

    try {
      const success = await login(formData.email, formData.password);
      if (success) {
        redirectToDashboard();
      }
    } catch (error) {
      console.error('Login error:', error);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center relative overflow-hidden">
      <div className="relative z-10 w-full max-w-md p-6">
        {/* Login Card with minimal animation */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5 }}
          className="bg-white/90 dark:bg-gray-800/30 rounded-2xl border border-gray-400 dark:border-gray-400/30 p-8"
        >
          <div className="flex flex-col items-center mb-6">
            <div className="inline-flex items-center justify-center w-16 h-16 bg-blue-900 dark:bg-blue-500 rounded-full mb-4">
              <Shield className="w-8 h-8 text-white" />
            </div>
            <h1 className="text-lg font-bold text-center text-gray-800 dark:text-gray-100">Sports Management Login Portal</h1>
            <p className="text-sm text-gray-700 dark:text-gray-300 mt-1">Access your dashboard</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Email
              </label>
              <div className="relative">
                <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-300" />
                <input
                  autoFocus
                  name="email"
                  id="email"
                  type="email"
                  value={formData.email}
                  onChange={handleInputChange}
                  placeholder="Enter your email"
                  className="w-full pl-10 pr-12 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-400 dark:border-gray-600/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                  required
                  disabled={isLoading}
                  autoComplete="email"
                />
              </div>
            </div>

            <div>
              <label htmlFor="password" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Password
              </label>
              <div className="relative">
                <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-300" />
                <input
                  name="password"
                  id="password"
                  type={showPassword ? 'text' : 'password'}
                  value={formData.password}
                  onChange={handleInputChange}
                  placeholder="Enter your password"
                  className="w-full pl-10 pr-12 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-400 dark:border-gray-600/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                  required
                  disabled={isLoading}
                  autoComplete="current-password"
                />
                <button
                  type="button"
                  onClick={togglePasswordVisibility}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-white transition-colors"
                  disabled={isLoading}
                >
                  {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>

            <button
              type="submit"
              disabled={isLoading}
              className="w-full bg-blue-900 hover:bg-blue-600 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 shadow hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span className="flex items-center justify-center">
                {isLoading ? (
                  <>
                    <Loader className="animate-spin w-4 h-4 mr-2" />
                    Login
                  </>
                ) : (
                  <>
                    <Key className="w-4 h-4 mr-2" />
                    Login
                  </>
                )}
              </span>
            </button>
          </form>

          <div className="text-center mt-6 text-sm text-gray-700 dark:text-gray-400">
            Need help? Contact your system administrator.
          </div>
        </motion.div>
      </div>
    </div>
  );
}