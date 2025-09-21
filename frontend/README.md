# PRISAA Sports Management System - Frontend

## Overview
Next.js 15.5.2 frontend application for the Private Schools Athletic Association (PRISAA) Sports Management System.

## Features

### 🏆 **Tournament Management**
- Multi-level competitions (Provincial, Regional, National)
- Tournament bracket generation and management
- Competition rules and format configuration
- Registration and qualification tracking

### � **Profiling System**
- **School Profiles** - Institution details, contact information, facilities
- **Team Profiles** - Squad management, team composition, coaching staff
- **Athlete Profiles** - Personal information, sports history, achievements
- **Coach Profiles** - Credentials, certifications, coaching history
- **Official Profiles** - Referee and judge management
- **Performance Analytics** - Individual and team statistics tracking

### 📅 **Scheduling Management**
- **Match Scheduling** - Automated and manual schedule generation
- **Venue Management** - Facility booking and availability tracking
- **Calendar Integration** - Event synchronization and reminders
- **Conflict Detection** - Automatic scheduling conflict resolution
- **Multi-Sport Coordination** - Cross-sport schedule optimization
- **Time Zone Support** - Regional competition coordination

### 📊 **Results Management**
- **Live Score Entry** - Real-time match result input
- **Automated Standings** - Dynamic leaderboards and rankings
- **Match Statistics** - Detailed performance metrics
- **Historical Results** - Complete competition archives
- **Result Verification** - Official approval workflows
- **Export & Reporting** - PDF reports, CSV exports, print-ready formats

### 📈 **Analytics & Reporting**
- Sports statistics and performance tracking
- Team and individual performance trends
- Competition analysis and insights
- Custom report generation

### 🔐 **Authentication & Access Control**
- Role-based permissions (Admin, Manager, Coach, Athlete)
- Secure login for administrators and users
- Multi-level access control for different user types

## Tech Stack
- **Framework:** Next.js 15.5.2 with App Router
- **Styling:** Tailwind CSS 4 + shadcn/ui (Slate theme)
- **Language:** TypeScript
- **UI Components:** shadcn/ui with Radix primitives
- **API Client:** Axios with SWR for data fetching
- **Forms:** React Hook Form + Zod validation
- **Charts:** Recharts for sports analytics
- **Animation:** Framer Motion

## Getting Started

### Prerequisites
- Node.js 18+ 
- Laravel backend running on http://localhost:8000

### Installation
```bash
npm install
```

### Environment Setup
Copy `.env.local` and configure:
```env
NEXT_PUBLIC_API_BASE_URL=http://localhost:8000/api
FRONTEND_URL=http://localhost:3000
```

### Development
```bash
npm run dev
```
Open [http://localhost:3000](http://localhost:3000)

### Build
```bash
npm run build
npm start
```

## Project Structure
```
├── app/                    # Next.js App Router pages
│   ├── dashboard/         # Main dashboard interface
│   ├── profiles/          # Profile management (schools, teams, athletes)
│   ├── scheduling/        # Match and venue scheduling
│   ├── results/           # Results entry and viewing
│   ├── tournaments/       # Tournament management
│   └── analytics/         # Statistics and reporting
├── components/
│   ├── ui/               # shadcn/ui base components
│   ├── profiles/         # Profile-specific components
│   ├── scheduling/       # Calendar and scheduling components
│   ├── results/          # Results entry and display components
│   └── shared/           # Reusable components
├── src/
│   ├── lib/
│   │   ├── api.ts        # Laravel API client
│   │   ├── types.ts      # TypeScript interfaces
│   │   └── utils.ts      # Utility functions
│   ├── contexts/         # React contexts (Auth, etc.)
│   ├── hooks/            # Custom React hooks
│   └── stores/           # State management
├── lib/utils.ts          # shadcn utility functions
└── public/               # Static assets (logos, icons)
```

## API Integration
- **Backend:** Laravel 12.27.1 (http://localhost:8000)
- **Authentication:** Laravel Sanctum tokens
- **Endpoints:** 225+ API routes for sports data
- **Real-time:** Live data fetching with SWR

## Components Added
- **Base UI:** Button, Card, Table, Badge
- **Overlays:** Dialog, Dropdown Menu, Toast
- **Data Display:** Data Tables with sorting/filtering
- **Forms:** Form components with validation

### Planned Components for Key Features
- **Profiling:** Avatar, Tabs, Accordion, Progress indicators
- **Scheduling:** Calendar, Date/Time pickers, Timeline components
- **Results:** Score input forms, Live scoreboards, Statistics charts
- **Analytics:** Chart components, Dashboard widgets, Export tools

## Contributing
This is the frontend for PRISAA Sports Management System.
Backend repository: [Backend Documentation](../backend/README.md)

## License
Private - PRISAA Sports Management System
