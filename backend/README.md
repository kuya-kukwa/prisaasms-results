# PRISAA Sports Management System

A comprehensive Laravel-based API system for managing the Private Schools Athletic Association (PRISAA) Games, featuring multi-level tournament management, historical record tracking, and administrative controls.

## Overview

The PRISAA Sports Management System is designed to handle the complex requirements of managing athletic competitions across multiple levels (Provincial, Regional, National) with complete historical tracking capabilities. The system manages participants, schools, sports categories, tournaments, matches, and comprehensive medal tallies.

## Key Features

### Profiling
- **Participant Profiles**: Comprehensive athlete registration and profile management
- **School Profiles**: Complete institutional information and sports programs
- **Coach Management**: Coaching staff profiles and certifications
- **Statistical Profiles**: Performance history and achievement tracking

### Scheduling
- **Event Scheduling**: Comprehensive tournament and match scheduling system
- **Venue Management**: Facility allocation and scheduling coordination
- **Conflict Resolution**: Automated scheduling conflict detection and resolution
- **Calendar Integration**: Full calendar view with event management capabilities

### Results
- **Live Results**: Real-time match results and score updates
- **Medal Tracking**: Instant medal tally updates and rankings
- **Performance Analytics**: Detailed statistical analysis and reporting
- **Historical Results**: Complete archive of past competition results

### Tournament Management
- **Multi-Level System**: Provincial, Regional, and National tournament levels
- **Independent Management**: Each level has dedicated tournament managers
- **Complete Tournament Lifecycle**: From setup to medal ceremonies
- **Real-time Medal Tracking**: Live updates of gold, silver, bronze counts

### Historical Records
- **Year-by-Year Tracking**: Complete PRISAA Games history from 2017-2025
- **Host Information**: Regional hosting details and venue information
- **Participation Statistics**: Total participants and achievements per year
- **Overall Champions**: Multi-level champion tracking and school performance

### Administrative System
- **Role-Based Access Control**: Admin, Manager, and User roles with Spatie Permissions
- **Controlled Registration**: Admin-managed user accounts
- **Comprehensive Auditing**: Complete activity tracking and logging

### Data Management
- **163+ API Endpoints**: Complete CRUD operations for all entities
- **Advanced Filtering**: Search and filter across all data types
- **Statistical Analytics**: Performance metrics and historical analysis
- **Medal Tally System**: Comprehensive scoring and ranking

## Technology Stack

- **Framework**: Laravel 12.27.1
- **Database**: MySQL with 20+ optimized migrations
- **Authentication**: Laravel Sanctum with Bearer tokens
- **Permissions**: Spatie Permission v6.21.0
- **Architecture**: RESTful API with comprehensive error handling

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM (for frontend assets)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/beansxz/prisaasms.git
   cd prisaasms/backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   # Configure your database in .env file
   php artisan migrate:fresh --seed
   ```

5. **Start the application**
   ```bash
   php artisan serve
   ```

## API Documentation

### Authentication Endpoints
- `POST /api/register` - Admin-controlled user registration
- `POST /api/login` - User authentication
- `POST /api/logout` - Session termination

### Tournament Management
- `GET /api/tournaments` - List all tournaments
- `POST /api/tournaments` - Create new tournament
- `PUT /api/tournaments/{id}` - Update tournament
- `DELETE /api/tournaments/{id}` - Delete tournament

### Historical Records
- `GET /api/prisaa-years` - Get yearly PRISAA Games records
- `POST /api/prisaa-years` - Add new year record
- `GET /api/prisaa-years/{id}/statistics` - Get year statistics
- `GET /api/overall-champions` - Multi-level champion records

### Sports & Categories
- `GET /api/sports` - List all sports
- `GET /api/categories` - List all categories
- `GET /api/participants` - Participant management
- `GET /api/schools` - School information

### Medal System
- `GET /api/medal-tallies` - Current medal standings
- `POST /api/medal-tallies` - Update medal counts
- `GET /api/tournaments/{id}/medal-summary` - Tournament medal breakdown

## Historical Data (2017-2025)

The system maintains complete records of PRISAA Games:

### Recent Games
- **2025 PRISAA Games** - Tuguegarao City, Cagayan Valley
- **2024 PRISAA Games** - Dumaguete City, Negros Oriental  
- **2023 PRISAA Games** - Iloilo City, Western Visayas
- **2022 PRISAA Games** - Butuan City, Caraga Region

### Pre-Pandemic Era
- **2019 PRISAA Games** - Puerto Princesa, Palawan
- **2018 PRISAA Games** - Tagbilaran City, Bohol
- **2017 PRISAA Games** - General Santos City, South Cotabato

*Note: 2020-2021 games were cancelled due to COVID-19 pandemic*

## Database Schema

### Core Tables
- `users` - System users with role-based access
- `schools` - Educational institutions and participants
- `sports` - Available sports categories
- `categories` - Competition categories (Elementary, High School, College)
- `tournaments` - Tournament management (Provincial/Regional/National)
- `participants` - Individual athlete records
- `matches` - Competition matches and results
- `medal_tallies` - Real-time medal tracking
- `prisaa_years` - Historical yearly records
- `overall_champions` - Multi-level champion tracking

## Security Features

- Bearer token authentication via Laravel Sanctum
- Role-based permissions with Spatie Permission package
- Admin-controlled user registration
- Comprehensive input validation and sanitization
- Protected routes with middleware authentication

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## Support

For technical support or questions about the PRISAA Management System:
- Create an issue in the GitHub repository
- Contact the development team
- Check the API documentation for endpoint details

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
