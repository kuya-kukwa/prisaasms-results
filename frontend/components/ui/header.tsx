'use client'

import { useState, useCallback, useRef, useEffect } from "react";
import { motion } from "framer-motion";
import Link from "next/link";
import Image from "next/image";
import clsx from "clsx";
import { useRouter } from 'next/navigation';
import { useActiveSectionContext } from '@/src/contexts/ActiveSection';

export const links = [
  {
    name: "Home",
    path: "/",
  },
  {
    name: "Profiles",
    path: "/profiles",
    subLinks: [
      { name: "Athletes", path: "/profiles#athletes" },
      { name: "Coaches", path: "/profiles#coaches" },
      { name: "Officials", path: "/profiles#officials" },
      { name: "Schools", path: "/profiles#schools" },
    ],
  },
  {
    name: "Schedules",
    path: "/schedules",
    subLinks: [
      { name: "Games", path: "/schedules#games" },
      { name: "Events", path: "/schedules#events" },
    ],
  },
  {
    name: "Results",
    path: "/results",
    subLinks: [
      { name: "Rankings & Leaderboards", path: "/results#rankings" },
      { name: "Medal Tally", path: "/results#medals" },
    ],
  },
];

export default function Header() {
  const { activeSection, setActiveSection, setTimeOfLastClick } = useActiveSectionContext();
  const [openSubMenu, setOpenSubMenu] = useState<string | null>(null);
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [authToken, setAuthToken] = useState<string | null>(null);
  const router = useRouter();
  const menuRef = useRef<HTMLDivElement>(null);

  // Set authToken from localStorage after component mounts to avoid hydration mismatch
  useEffect(() => {
    setAuthToken(localStorage.getItem('prisaa_token'));
  }, []);

  const toggleSubMenu = useCallback((subMenu: string) => {
    setOpenSubMenu(prev => prev === subMenu ? null : subMenu);
  }, []);

  const closeMenu = useCallback(() => {
    setIsMenuOpen(false);
    setOpenSubMenu(null);
  }, []);

  const handleLinkClick = useCallback((linkName: string) => {
    setActiveSection(linkName);
    setTimeOfLastClick(Date.now());
  }, [setActiveSection, setTimeOfLastClick]);

  const handleDashboardRedirect = useCallback(() => {
    setIsLoading(false);
    const dashboardPath = localStorage.getItem('dashboard') || '/login';
    router.push(dashboardPath);
    closeMenu();
    // Set a timeout to hide the loader after a short delay
    setTimeout(() => {
      setIsLoading(false);
    }, 1000); // Hide after 1 second
  }, [router, closeMenu]);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (menuRef.current && !menuRef.current.contains(event.target as Node)) {
        closeMenu();
      }
    };

    if (isMenuOpen) {
      document.addEventListener('mousedown', handleClickOutside);
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = 'unset';
    }

    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
      document.body.style.overflow = 'unset';
    };
  }, [isMenuOpen, closeMenu]);

  useEffect(() => {
    const handleEscape = (event: KeyboardEvent) => {
      if (event.key === 'Escape') {
        closeMenu();
      }
    };

    if (isMenuOpen) {
      document.addEventListener('keydown', handleEscape);
    }

    return () => document.removeEventListener('keydown', handleEscape);
  }, [isMenuOpen, closeMenu]);

  return (
    <header className="z-[999] relative">
      {/* Navigation Bar */}
      <motion.div
        className="fixed top-4 left-1/2 -translate-x-1/2 h-[4.5rem] w-[90%] sm:w-[70rem] border border-white/20 dark:border-gray-700 bg-white/80 dark:bg-gradient-to-br dark:from-gray-800 dark:to-gray-900 shadow-lg shadow-black/[0.15] dark:shadow-white/[0.25] backdrop-blur-md sm:top-4 sm:h-[4rem] rounded-full dark:text-white dark:border-white/30 px-6"
        initial={{ y: -100, x: "-50%", opacity: 0 }}
        animate={{ y: 0, x: "-50%", opacity: 1 }}
      >
        <div className="flex h-full items-center justify-between">
          {/* Logo and Title */}
          <Link href="/" className="flex items-center space-x-2 sm:space-x-4 flex-shrink-0" passHref>
            <Image
              src="/logo.png"
              alt="PRISAA Logo"
              width={48}
              height={48}
              className="border-2 border-gray-300 dark:border-white rounded-full shadow-lg shadow-black/[0.25]"
              priority
            />
            <div>
              <h1 className="text-lg sm:text-2xl font-black bg-gradient-to-r from-blue-900 to-rose-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-rose-400">
                PRISAA
              </h1>
              <p className="text-xs text-gray-700 dark:text-gray-100">
                Sports Management System
              </p>
            </div>
          </Link>

          {/* Desktop Navigation - Centered */}
          <nav className="hidden sm:flex items-center justify-center text-[0.95rem] absolute left-1/2 transform -translate-x-1/2">
            <ul className="flex items-center space-x-1 relative">
              {links.map((link) => (
                <motion.li
                  key={link.path}
                  className="h-3/4 flex items-center justify-center relative"
                  initial={{ y: -100, opacity: 0 }}
                  animate={{ y: 0, opacity: 1 }}
                  onMouseEnter={() => link.subLinks && setOpenSubMenu(link.name)}
                  onMouseLeave={() => setOpenSubMenu(null)}
                >
                  <Link
                    className={clsx(
                      "flex w-full items-center justify-center px-2 py-3 text-gray-700 hover:text-blue-700 dark:text-gray-200 dark:hover:text-blue-300 transition dark:hover:text-white rounded-full slate-400 font-medium",
                      {
                        "text-gray-900 dark:text-white": activeSection === link.name,
                      }
                    )}
                    href={link.path}
                    onClick={() => handleLinkClick(link.name)}
                    role="menuitem"
                  >
                    {link.name}
                    {link.subLinks && (
                      <motion.svg
                      className={clsx(
                        "ml-1 w-4 h-4 transition-colors",
                        openSubMenu === link.name
                        ? "text-blue-600 dark:text-blue-400"
                        : "text-gray-500 dark:text-gray-400"
                      )}
                      animate={{
                        rotate: openSubMenu === link.name ? 180 : 0,
                        scale: openSubMenu === link.name ? 1.15 : 1,
                      }}
                      transition={{
                        type: "spring",
                        stiffness: 300,
                        damping: 20,
                      }}
                      whileHover={{
                        scale: 1.2,
                      }}
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      aria-hidden="true"
                      >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={2}
                        d="M19 9l-7 7-7-7"
                      />
                      </motion.svg>
                    )}
                    {link.name === activeSection && (
                      <motion.span
                        className="absolute inset-0 -z-10 rounded-full bg-gradient-to-tr from-blue-300/40 via-indigo-400/30 to-blue-300/40 dark:from-blue-700/40 dark:via-indigo-700/40 dark:to-blue-700/30 shadow-md dark:shadow-white/20"
                        layoutId="activeSection"
                        transition={{
                          type: "spring",
                          stiffness: 380,
                          damping: 30,
                        }}
                      ></motion.span>
                    )}
                  </Link>

                  {/* Desktop Submenu */}
                  {link.subLinks && openSubMenu === link.name && (
                    <motion.ul
                      className="absolute top-full text-center mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg min-w-[200px] z-50 py-2 border border-gray-200 dark:border-gray-700"
                      initial={{ opacity: 0, y: -10 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, y: -10 }}
                      role="menu"
                    >
                      {link.subLinks.map((sub) => (
                        <li key={sub.path} role="none">
                          <Link
                            href={sub.path}
                            className="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700 transition focus:outline-none focus:bg-blue-100 dark:focus:bg-gray-700 rounded"
                            role="menuitem"
                            onClick={() => {
                              handleLinkClick(link.name);
                              setOpenSubMenu(null);
                            }}
                          >
                            {sub.name}
                          </Link>
                        </li>
                      ))}
                    </motion.ul>
                  )}
                </motion.li>
              ))}
            </ul>
          </nav>

          {/* Desktop Actions (Right side) */}
          <div className="hidden md:flex items-center space-x-3 flex-shrink-0">
            {/* Conditional Login/Dashboard Button */}
            {authToken ? (
              <button
                onClick={handleDashboardRedirect}
                className="p-2 rounded-xl border border-gray-300 hover:bg-blue-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200 shadow-md hover:shadow-lg transition-all duration-200 flex items-center space-x-2"
                aria-label="Dashboard"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l7 7m-2 2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                  />
                </svg>
                <span>Dashboard</span>
              </button>
            ) : (
              <button
                onClick={() => {
                  setActiveSection("");
                  setTimeOfLastClick(Date.now());
                  router.push("/login");
                }}
                className="p-2 rounded-xl border border-gray-300 hover:bg-blue-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200 shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-gray-700 flex items-center space-x-2"
                aria-label="Login"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                  />
                </svg>
                <span>Login</span>
              </button>
            )}
          </div>

          {/* Mobile Actions */}
          <div className="md:hidden flex items-center justify-center">
            {/* Mobile Menu Toggle Button */}
            <button
              className="rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500"
              aria-label={isMenuOpen ? "Close menu" : "Open menu"}
              aria-expanded={isMenuOpen}
              onClick={() => setIsMenuOpen(prev => !prev)}
            >
              {isMenuOpen ? (
                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-gray-700 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              ) : (
                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-gray-700 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              )}
            </button>
          </div>
        </div>
      </motion.div>

      {/* Simple Aesthetic Mobile Navigation Menu */}
      {isMenuOpen && (
        <motion.nav
          ref={menuRef}
          className="md:hidden fixed inset-x-6 top-24 bg-white/80 dark:bg-gray-900/80 backdrop-blur-2xl border border-gray-100 dark:border-gray-800 shadow-lg rounded-2xl z-[1001] overflow-hidden"
          initial={{ opacity: 0, y: -10 }}
          animate={{ opacity: 1, y: 0 }}
          exit={{ opacity: 0, y: -10 }}
          transition={{ duration: 0.2, ease: "easeOut" }}
        >
          <div className="py-4">
            <ul className="space-y-1" role="menu">
              {links.map((link) => (
                <li key={link.path} role="none">
                  <div>
                    <button
                      onClick={() => {
                        handleLinkClick(link.name);
                        if (link.subLinks?.length) {
                          toggleSubMenu(link.name);
                        } else {
                          router.push(link.path);
                          closeMenu();
                        }
                      }}
                      className={clsx(
                        "flex items-center justify-between w-full px-6 py-3 text-left transition-colors duration-150 focus:outline-none",
                        activeSection === link.name
                          ? "text-blue-600 dark:text-blue-400"
                          : "text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400"
                      )}
                      role="menuitem"
                      aria-expanded={openSubMenu === link.name}
                    >
                      <span className="font-medium">{link.name}</span>
                      {link.subLinks?.length && (
                        <motion.svg
                          className="w-4 h-4 text-gray-400"
                          animate={{ rotate: openSubMenu === link.name ? 180 : 0 }}
                          transition={{ duration: 0.15 }}
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 9l-7 7-7-7" />
                        </motion.svg>
                      )}
                    </button>

                    {/* Submenu */}
                    {link.subLinks && openSubMenu === link.name && (
                      <motion.div
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: "auto", opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        transition={{ duration: 0.15 }}
                        className="overflow-hidden"
                      >
                        <ul className="py-2 ml-6 border-l border-gray-100 dark:border-gray-800" role="menu">
                          {link.subLinks.map((sub) => (
                            <li key={sub.path} role="none">
                              <button
                                onClick={() => {
                                  router.push(sub.path);
                                  closeMenu();
                                }}
                                className="block w-full text-left px-6 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors duration-150 focus:outline-none"
                                role="menuitem"
                              >
                                {sub.name}
                              </button>
                            </li>
                          ))}
                        </ul>
                      </motion.div>
                    )}
                  </div>
                </li>
              ))}
            </ul>

            <div className="px-4 pb-4 pt-2 border-t border-gray-200 dark:border-gray-700 mt-2">
            {authToken ? (
              <button
                onClick={handleDashboardRedirect}
                className="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center space-x-2 mt-2"
                aria-label="Dashboard"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l7 7m-2 2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                  />
                </svg>
                <span>Dashboard</span>
              </button>
            ) : (
              <button
                onClick={() => {
                  setActiveSection("");
                  setTimeOfLastClick(Date.now());
                  router.push("/login");
                  closeMenu();
                }}
                className="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center space-x-2 mt-2"
                aria-label="Login"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                  />
                </svg>
                <span>Login</span>
              </button>
            )}
          </div>
          </div>
        </motion.nav>
      )}
      {isLoading}
    </header>
  );
}