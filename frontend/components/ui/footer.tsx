import Link from 'next/link';
import Image from 'next/image';
import { Button } from "@/components/ui/button";
import { Mail, Github, Facebook, Users } from "lucide-react";

const Footer = () => {
  return (
    <footer className="bg-gray-900 dark:bg-gray-900 text-white">
      <div className="max-w-7xl mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
          {/* Brand & Description */}
          <div className="flex flex-col justify-center sm:justify-start text-center md:text-left">
            <div className="flex flex-col md:flex-row items-center justify-center md:justify-start px-4 space-y-2 md:space-y-0 md:space-x-2 mb-2 sm:mb-4">
              <Image src="/logo.png" alt="PRISAA" width={48} height={48} className="border-2 border-gray/90 dark:border-white rounded-full shadow-lg shadow-black/[0.25]" />
              <span className="text-base sm:text-[0.96rem] font-bold text-white dark:text-white">PRISAA Sports Management System</span>
            </div>
            <p className="text-indigo-100 dark:text-indigo-200 px-4 text-sm">
              A modern sports management web platform exclusively for PRISAA Sports Foundation, Inc.
            </p>
          </div>

          {/* Navigation Links */}
          <div className="flex flex-col items-center px-4">
            <h3 className="font-semibold mb-4 text-white dark:text-indigo-200 text-lg text-center">Quick Links</h3>
            <div className="grid grid-cols-2 gap-2 text-sm">
              <Link href="/profiles" className="block text-indigo-200 hover:text-indigo-100 transition-colors duration-200">Athletes</Link>
              <Link href="/profiles" className="block text-indigo-200 hover:text-indigo-100 transition-colors duration-200">Schools</Link>
              <Link href="/schedules" className="block text-indigo-200 hover:text-indigo-100 transition-colors duration-200">Sports</Link>
              <Link href="/schedules" className="block text-indigo-200 hover:text-indigo-100 transition-colors duration-200">Events</Link>
              <Link href="/rankings" className="block text-indigo-200 hover:text-indigo-100 transition-colors duration-200">Results</Link>
              <Link href="/rankings" className="block text-indigo-200 hover:text-indigo-100 transition-colors duration-200">Reports</Link>
            </div>
          </div>

          {/* Developer Info */}
          <div className="flex flex-col items-center px-4">
            <h3 className="font-semibold mb-4 text-white dark:text-indigo-200 text-lg">Developer</h3>
            <div className="space-y-3 text-sm">
              <div className="flex items-center space-x-2">
                <Users className="w-4 h-4 text-indigo-300" />
                <span className="font-medium">Vince Ian Escopete & Company</span>
              </div>
              <div className="flex flex-row items-center justify-center space-x-4">
                <Button variant="link" asChild>
                  <Link href="mailto:vinceianescopete07@gmail.com" className="text-indigo-200 hover:text-indigo-100 hover:scale-105 transition-colors duration-200 underline decoration-transparent hover:decoration-current">
                    <Mail className="w-6 h-6 text-indigo-300" />
                  </Link>
                </Button>
                <Button variant="link" asChild>
                  <Link href="https://github.com/beansxz" target="_blank" rel="noopener noreferrer" className="text-indigo-200 hover:text-indigo-100 transition-colors duration-200 underline decoration-transparent hover:decoration-current hover:scale-105">
                    <Github className="w-6 h-6 text-indigo-300" />
                  </Link>
                </Button>
                <Button variant="link" asChild>
                  <Link href="https://www.facebook.com/beansxz" target="_blank" rel="noopener noreferrer" className="text-indigo-200 hover:text-indigo-100 transition-colors duration-200 underline decoration-transparent hover:decoration-current hover:scale-105">
                    <Facebook className="w-6 h-6 text-indigo-300" />
                  </Link>
                </Button>
              </div>
            </div>
          </div>
        </div>
        <div className="mt-8 border-t border-indigo-600 dark:border-gray-700 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs sm:text-[0.875rem] text-indigo-200 dark:text-indigo-300 text-center">
          <span>© 2025 PRISAA Sports Management System. All rights reserved.</span>
          <div className="flex space-x-2">
            <span>Powered by Computer Communication Development Institute</span>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;