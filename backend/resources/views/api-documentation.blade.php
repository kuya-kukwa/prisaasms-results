<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISAA Sports Management System - API Documentation v1.0</title>
    <style>
        @page { margin: 15mm; size: A4; }
        body { font-family: 'DejaVu Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.5; color: #2c3e50; margin: 0; padding: 0; font-size: 10px; }
        .header { text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; margin-bottom: 25px; border-radius: 8px; }
        .header h1 { font-size: 24px; margin: 0 0 8px 0; font-weight: 700; letter-spacing: -0.5px; }
        .header .subtitle { font-size: 14px; opacity: 0.9; margin: 0; font-weight: 300; }
        .header .version { background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 15px; font-size: 12px; margin-top: 10px; display: inline-block; }
        .toc { background-color: #f8f9fa; border-radius: 6px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #667eea; }
        .toc h2 { color: #2c3e50; font-size: 16px; margin: 0 0 15px 0; font-weight: 600; }
        .toc ul { list-style: none; padding: 0; margin: 0; columns: 2; column-gap: 30px; }
        .toc li { margin: 6px 0; padding: 3px 0; }
        .toc a { color: #5a67d8; text-decoration: none; font-weight: 500; }
        .section { margin-bottom: 30px; page-break-inside: avoid; }
        .section-title { color: #2c3e50; font-size: 20px; font-weight: 700; border-bottom: 3px solid #667eea; padding-bottom: 8px; margin-bottom: 20px; background: linear-gradient(90deg, #667eea, #764ba2); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
        .subsection { margin-bottom: 18px; }
        .subsection-title { color: #4a5568; font-size: 15px; font-weight: 600; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .info-box { background: linear-gradient(135deg, #e6f3ff 0%, #f0f8ff 100%); border-left: 4px solid #3182ce; padding: 15px; margin: 10px 0; border-radius: 0 6px 6px 0; }
        .warning-box { background: linear-gradient(135deg, #fff5e6 0%, #fef5e7 100%); border-left: 4px solid #dd6b20; padding: 15px; margin: 10px 0; border-radius: 0 6px 6px 0; }
        .endpoint-group { background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; margin: 12px 0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .endpoint-header { background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #2d3748; }
        .endpoint { padding: 10px 16px; border-bottom: 1px solid #f1f5f9; font-family: 'Courier New', 'Monaco', monospace; font-size: 10px; background-color: #fafafa; }
        .endpoint:last-child { border-bottom: none; }
        .method { font-weight: bold; padding: 2px 6px; border-radius: 3px; margin-right: 8px; color: white; font-size: 9px; display: inline-block; width: 45px; text-align: center; }
        .method.get { background-color: #48bb78; }
        .method.post { background-color: #4299e1; }
        .method.put { background-color: #ed8936; }
        .method.patch { background-color: #9f7aea; }
        .method.delete { background-color: #f56565; }
        .endpoint-url { color: #2d3748; font-weight: 500; }
        .endpoint-desc { color: #718096; font-size: 9px; margin-top: 4px; font-family: 'DejaVu Sans', sans-serif; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 10px; background-color: white; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 10px; text-align: left; font-weight: 600; font-size: 10px; }
        td { padding: 10px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .data-type { background-color: #e6fffa; color: #234e52; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; font-size: 9px; font-weight: 500; }
        .role-badge { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4px 8px; border-radius: 12px; font-size: 9px; font-weight: 600; margin: 2px; display: inline-block; }
        .footer { margin-top: 40px; padding: 20px; background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); border-radius: 8px; text-align: center; border-top: 3px solid #667eea; }
        .footer p { margin: 5px 0; color: #718096; font-size: 10px; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PRISAA Sports Management System</h1>
        <p class="subtitle">Complete API Documentation for Frontend Developers</p>
        <span class="version">Version 1.0.0</span>
    </div>

    <div class="toc">
        <h2>Table of Contents</h2>
        <ul>
            <li><a href="#overview">1. API Overview</a></li>
            <li><a href="#authentication">2. Authentication</a></li>
            <li><a href="#public-api">3. Public API</a></li>
            <li><a href="#users">4. User Management</a></li>
            <li><a href="#schools">5. School Management</a></li>
            <li><a href="#participants">6. Participant Management</a></li>
            <li><a href="#categories">7. Category Management</a></li>
            <li><a href="#sports">8. Sport Management</a></li>
            <li><a href="#teams">9. Team Management</a></li>
            <li><a href="#venues">10. Venue Management</a></li>
            <li><a href="#tournaments">11. Tournament Management</a></li>
            <li><a href="#schedules">12. Schedule Management</a></li>
            <li><a href="#matches">13. Match Management</a></li>
            <li><a href="#officials">14. Official Management</a></li>
            <li><a href="#results">15. Result Management</a></li>
            <li><a href="#rankings">16. Ranking System</a></li>
            <li><a href="#medals">17. Medal Tally System</a></li>
            <li><a href="#prisaa-years">18. PRISAA Year Management</a></li>
            <li><a href="#overall-champions">19. Overall Champion Tracking</a></li>
            <li><a href="#data-structures">20. Data Structures</a></li>
            <li><a href="#frontend-guide">21. Frontend Integration Guide</a></li>
        </ul>
    </div>

    <div class="section" id="overview">
        <h2 class="section-title">1. API Overview</h2>
        <div class="info-box">
            <strong>Base URL:</strong> <code>{{ url('/api') }}</code><br>
            <strong>Content-Type:</strong> <code>application/json</code><br>
            <strong>Authentication:</strong> Laravel Sanctum (Bearer Token)<br>
            <strong>API Version:</strong> 1.0.0
        </div>
        <p>This REST API provides comprehensive backend functionality for managing the Private Schools Athletic Association (PRISAA) Games, including multi-level tournaments, historical record tracking, participant profiling, scheduling, and results management. The system implements role-based access control (RBAC) with three distinct user roles: <strong>admin</strong>, <strong>coach</strong>, and <strong>tournament manager</strong>.</p>

        <div class="info-box">
            <strong>Key Features:</strong><br>
            • <strong>Profiling:</strong> Comprehensive participant and school management<br>
            • <strong>Scheduling:</strong> Advanced event and venue scheduling system<br>
            • <strong>Results:</strong> Live results tracking and historical records<br>
            • <strong>Multi-Level Management:</strong> Provincial, Regional, and National tournaments<br>
            • <strong>Historical Tracking:</strong> Year-by-year PRISAA Games records (2017-current)
        </div>
    </div>

    <div class="section" id="authentication">
        <h2 class="section-title">2. Authentication</h2>
        <div class="warning-box"><strong>Important:</strong> All protected API endpoints require a valid Bearer token in the <code>Authorization</code> header.</div>
        <div class="endpoint-group">
            <div class="endpoint-header">Authentication Endpoints</div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/auth/register</span><div class="endpoint-desc">Register a new user account.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/auth/login</span><div class="endpoint-desc">Login and receive authentication token.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/auth/logout</span><div class="endpoint-desc">Logout and revoke current token (requires auth).</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/auth/logout-all</span><div class="endpoint-desc">Logout from all devices (requires auth).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/auth/me</span><div class="endpoint-desc">Get authenticated user information (requires auth).</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/auth/refresh</span><div class="endpoint-desc">Get a new token (requires auth).</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/auth/forgot-password</span><div class="endpoint-desc">Request a password reset token.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/auth/reset-password</span><div class="endpoint-desc">Reset password using a token.</div></div>
        </div>
    </div>

    <div class="section page-break" id="public-api">
        <h2 class="section-title">3. Public API Endpoints</h2>
        <div class="info-box">The following endpoints are publicly accessible and do not require authentication. They provide read-only access to general information.</div>
        
        <div class="subsection">
            <h3 class="subsection-title">Public Data Access</h3>
            <div class="endpoint-group">
                <div class="endpoint-header">Public Information</div>
                <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/public/schools</span><div class="endpoint-desc">List all schools.</div></div>
                <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/public/schools/{school}</span><div class="endpoint-desc">Get a specific school.</div></div>
                <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/public/sports</span><div class="endpoint-desc">List all sports.</div></div>
                <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/public/sports/{sport}</span><div class="endpoint-desc">Get a specific sport.</div></div>
                <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/public/schedules</span><div class="endpoint-desc">List all schedules.</div></div>
                <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/public/matches</span><div class="endpoint-desc">List all matches.</div></div>
                <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/public/results</span><div class="endpoint-desc">List all results.</div></div>
                <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/public/rankings</span><div class="endpoint-desc">List all rankings.</div></div>
                <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/public/medals</span><div class="endpoint-desc">List all medal tallies.</div></div>
                <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/public/tournaments</span><div class="endpoint-desc">List all tournaments.</div></div>
            </div>
        </div>
    </div>

    <div class="section page-break" id="users">
        <h2 class="section-title">4. User Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">User Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/users</span><div class="endpoint-desc">List all users (Admin).</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/users</span><div class="endpoint-desc">Create a new user (Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/users/role/{role}</span><div class="endpoint-desc">Get users by role (Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/users/profile</span><div class="endpoint-desc">Get own user profile.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/users/profile</span><div class="endpoint-desc">Update own user profile.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/users/change-password</span><div class="endpoint-desc">Change own password.</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/users/{user}</span><div class="endpoint-desc">Get a specific user (Admin).</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/users/{user}</span><div class="endpoint-desc">Update a specific user (Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/users/{user}</span><div class="endpoint-desc">Delete a user (Admin).</div></div>
        </div>
    </div>

    <div class="section" id="schools">
        <h2 class="section-title">5. School Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">School Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/schools</span><div class="endpoint-desc">List all schools.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/schools</span><div class="endpoint-desc">Create a new school (Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/schools/{school}</span><div class="endpoint-desc">Get a specific school.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/schools/{school}</span><div class="endpoint-desc">Update a school (Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/schools/{school}</span><div class="endpoint-desc">Delete a school (Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/schools/{school}/statistics</span><div class="endpoint-desc">Get statistics for a school.</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/schools/region/{region}</span><div class="endpoint-desc">Get schools by region.</div></div>
        </div>
    </div>

    <div class="section" id="participants">
        <h2 class="section-title">6. Participant Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Participant Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/participants</span><div class="endpoint-desc">List all participants with filtering options.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/participants</span><div class="endpoint-desc">Create a new participant (Admin/Manager).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/participants/{participant}</span><div class="endpoint-desc">Get a specific participant's details.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/participants/{participant}</span><div class="endpoint-desc">Update participant information (Admin/Manager).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/participants/{participant}</span><div class="endpoint-desc">Delete a participant (Admin only).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/participants/school/{schoolId}</span><div class="endpoint-desc">Get participants by school.</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/participants/category/{categoryId}</span><div class="endpoint-desc">Get participants by category.</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/participants/{participant}/performance</span><div class="endpoint-desc">Get performance history for a participant.</div></div>
        </div>
    </div>

    <div class="section" id="categories">
        <h2 class="section-title">7. Category Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Category Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/categories</span><div class="endpoint-desc">List all categories (Elementary, High School, College).</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/categories</span><div class="endpoint-desc">Create a new category (Admin only).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/categories/{category}</span><div class="endpoint-desc">Get a specific category.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/categories/{category}</span><div class="endpoint-desc">Update a category (Admin only).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/categories/{category}</span><div class="endpoint-desc">Delete a category (Admin only).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/categories/{category}/statistics</span><div class="endpoint-desc">Get statistics for a category.</div></div>
        </div>
    </div>

    <div class="section page-break" id="sports">
        <h2 class="section-title">8. Sport Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Sport Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/sports</span><div class="endpoint-desc">List all sports.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/sports</span><div class="endpoint-desc">Create a new sport (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/sports/{sport}</span><div class="endpoint-desc">Get a specific sport.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/sports/{sport}</span><div class="endpoint-desc">Update a sport (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/sports/{sport}</span><div class="endpoint-desc">Delete a sport (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/sports/category/{category}</span><div class="endpoint-desc">Get sports by category.</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/sports/{sport}/statistics</span><div class="endpoint-desc">Get statistics for a sport.</div></div>
        </div>
    </div>

    <div class="section" id="teams">
        <h2 class="section-title">8. Team Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Team Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/teams</span><div class="endpoint-desc">List all teams.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/teams</span><div class="endpoint-desc">Create a new team (Coach/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/teams/{team}</span><div class="endpoint-desc">Get a specific team.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/teams/{team}</span><div class="endpoint-desc">Update a team (Coach/Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/teams/{team}</span><div class="endpoint-desc">Delete a team (Coach/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/teams/school/{schoolId}</span><div class="endpoint-desc">Get teams by school.</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/teams/sport/{sportId}</span><div class="endpoint-desc">Get teams by sport.</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/teams/{team}/statistics</span><div class="endpoint-desc">Get statistics for a team.</div></div>
            <div class="endpoint"><span class="method patch">PATCH</span> <span class="endpoint-url">/teams/{team}/performance</span><div class="endpoint-desc">Update team performance data (Coach/Admin).</div></div>
        </div>
    </div>

    <div class="section" id="venues">
        <h2 class="section-title">9. Venue Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Venue Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/venues</span><div class="endpoint-desc">List all venues.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/venues</span><div class="endpoint-desc">Create a new venue (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/venues/{venue}</span><div class="endpoint-desc">Get a specific venue.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/venues/{venue}</span><div class="endpoint-desc">Update a venue (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/venues/{venue}</span><div class="endpoint-desc">Delete a venue (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/venues/{venue}/availability</span><div class="endpoint-desc">Get availability for a venue.</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/venues/{venue}/statistics</span><div class="endpoint-desc">Get statistics for a venue.</div></div>
        </div>
    </div>

    <div class="section page-break" id="tournaments">
        <h2 class="section-title">10. Tournament Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Tournament Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/tournaments</span><div class="endpoint-desc">List all tournaments.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/tournaments</span><div class="endpoint-desc">Create a new tournament (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/tournaments/{tournament}</span><div class="endpoint-desc">Get a specific tournament.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/tournaments/{tournament}</span><div class="endpoint-desc">Update a tournament (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/tournaments/{tournament}</span><div class="endpoint-desc">Delete a tournament (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/tournaments/{tournament}/register</span><div class="endpoint-desc">Register a participant for a tournament.</div></div>
            <div class="endpoint"><span class="method patch">PATCH</span> <span class="endpoint-url">/tournaments/{tournament}/status</span><div class="endpoint-desc">Update the status of a tournament.</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/tournaments/{tournament}/statistics</span><div class="endpoint-desc">Get statistics for a tournament.</div></div>
        </div>
    </div>

    <div class="section" id="schedules">
        <h2 class="section-title">11. Schedule Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Schedule Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/schedules</span><div class="endpoint-desc">List all schedules.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/schedules</span><div class="endpoint-desc">Create a new schedule (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/schedules/{schedule}</span><div class="endpoint-desc">Get a specific schedule.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/schedules/{schedule}</span><div class="endpoint-desc">Update a schedule (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/schedules/{schedule}</span><div class="endpoint-desc">Delete a schedule (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method patch">PATCH</span> <span class="endpoint-url">/schedules/{schedule}/status</span><div class="endpoint-desc">Update the status of a schedule.</div></div>
        </div>
    </div>

    <div class="section" id="matches">
        <h2 class="section-title">12. Match Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Match Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/matches</span><div class="endpoint-desc">List all matches.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/matches</span><div class="endpoint-desc">Create a new match (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/matches/{gameMatch}</span><div class="endpoint-desc">Get a specific match.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/matches/{gameMatch}</span><div class="endpoint-desc">Update a match (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/matches/{gameMatch}</span><div class="endpoint-desc">Delete a match (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method patch">PATCH</span> <span class="endpoint-url">/matches/{gameMatch}/score</span><div class="endpoint-desc">Update the score of a match.</div></div>
        </div>
    </div>

    <div class="section page-break" id="officials">
        <h2 class="section-title">13. Official Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Official Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/officials</span><div class="endpoint-desc">List all officials.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/officials</span><div class="endpoint-desc">Create a new official (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/officials/{official}</span><div class="endpoint-desc">Get a specific official.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/officials/{official}</span><div class="endpoint-desc">Update an official (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/officials/{official}</span><div class="endpoint-desc">Delete an official (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method patch">PATCH</span> <span class="endpoint-url">/officials/{official}/rating</span><div class="endpoint-desc">Update the rating of an official.</div></div>
        </div>
    </div>

    <div class="section" id="results">
        <h2 class="section-title">14. Result Management</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Result Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/results</span><div class="endpoint-desc">List all results.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/results</span><div class="endpoint-desc">Create a new result (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/results/{result}</span><div class="endpoint-desc">Get a specific result.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/results/{result}</span><div class="endpoint-desc">Update a result (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/results/{result}</span><div class="endpoint-desc">Delete a result (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method patch">PATCH</span> <span class="endpoint-url">/results/{result}/verify</span><div class="endpoint-desc">Verify a result.</div></div>
        </div>
    </div>

    <div class="section" id="rankings">
        <h2 class="section-title">15. Ranking System</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Ranking Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/rankings</span><div class="endpoint-desc">List all rankings.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/rankings</span><div class="endpoint-desc">Create a new ranking (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/rankings/{ranking}</span><div class="endpoint-desc">Get a specific ranking.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/rankings/{ranking}</span><div class="endpoint-desc">Update a ranking (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/rankings/{ranking}</span><div class="endpoint-desc">Delete a ranking (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/rankings/update</span><div class="endpoint-desc">Trigger a recalculation of rankings.</div></div>
        </div>
    </div>

    <div class="section" id="medals">
        <h2 class="section-title">16. Medal Tally System</h2>
        <div class="endpoint-group">
            <div class="endpoint-header">Medal Tally Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/medals</span><div class="endpoint-desc">List all medal tallies.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/medals</span><div class="endpoint-desc">Create a new medal tally entry (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/medals/{medalTally}</span><div class="endpoint-desc">Get a specific medal tally.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/medals/{medalTally}</span><div class="endpoint-desc">Update a medal tally (Manager/Admin).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/medals/{medalTally}</span><div class="endpoint-desc">Delete a medal tally (Manager/Admin).</div></div>
        </div>
    </div>

    <div class="section" id="prisaa-years">
        <h2 class="section-title">18. PRISAA Year Management</h2>
        <div class="info-box">Historical tracking system for PRISAA Games from 2017-2025, including host information, participation statistics, and yearly achievements.</div>
        <div class="endpoint-group">
            <div class="endpoint-header">PRISAA Year Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/prisaa-years</span><div class="endpoint-desc">List all PRISAA Games years with pagination.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/prisaa-years</span><div class="endpoint-desc">Create a new PRISAA year record (Admin only).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/prisaa-years/{prisaaYear}</span><div class="endpoint-desc">Get specific year details.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/prisaa-years/{prisaaYear}</span><div class="endpoint-desc">Update year information (Admin only).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/prisaa-years/{prisaaYear}</span><div class="endpoint-desc">Delete a year record (Admin only).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/prisaa-years/{prisaaYear}/statistics</span><div class="endpoint-desc">Get comprehensive statistics for a specific year.</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/prisaa-years/{prisaaYear}/multi-level-breakdown</span><div class="endpoint-desc">Get multi-level tournament breakdown for a year.</div></div>
        </div>
    </div>

    <div class="section" id="overall-champions">
        <h2 class="section-title">19. Overall Champion Tracking</h2>
        <div class="info-box">Multi-level champion tracking system for Provincial, Regional, and National tournaments with comprehensive school performance metrics.</div>
        <div class="endpoint-group">
            <div class="endpoint-header">Overall Champion Operations</div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/overall-champions</span><div class="endpoint-desc">List all overall champions with filtering.</div></div>
            <div class="endpoint"><span class="method post">POST</span> <span class="endpoint-url">/overall-champions</span><div class="endpoint-desc">Create new champion record (Admin/Manager).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/overall-champions/{overallChampion}</span><div class="endpoint-desc">Get specific champion details.</div></div>
            <div class="endpoint"><span class="method put">PUT</span> <span class="endpoint-url">/overall-champions/{overallChampion}</span><div class="endpoint-desc">Update champion record (Admin/Manager).</div></div>
            <div class="endpoint"><span class="method delete">DELETE</span> <span class="endpoint-url">/overall-champions/{overallChampion}</span><div class="endpoint-desc">Delete champion record (Admin only).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/overall-champions/level/{level}</span><div class="endpoint-desc">Get champions by tournament level (Provincial/Regional/National).</div></div>
            <div class="endpoint"><span class="method get">GET</span> <span class="endpoint-url">/overall-champions/school/{schoolId}</span><div class="endpoint-desc">Get all championships won by a school.</div></div>
        </div>
    </div>

    <div class="section page-break" id="frontend-guide">
        <h2 class="section-title">21. Frontend Integration Guide</h2>
        
        <div class="subsection">
            <h3 class="subsection-title">Authentication Flow</h3>
            <div class="info-box">
                <strong>Step 1:</strong> Login using <code>POST /api/login</code><br>
                <strong>Step 2:</strong> Store the received token<br>
                <strong>Step 3:</strong> Include token in all requests: <code>Authorization: Bearer {token}</code><br>
                <strong>Step 4:</strong> Handle 401 responses by redirecting to login
            </div>
            
            <h4>Example Login Request:</h4>
            <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 9px;">
POST /api/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}

Response:
{
    "success": true,
    "data": {
        "user": {...},
        "token": "1|abc123..."
    }
}
            </pre>
        </div>

        <div class="subsection">
            <h3 class="subsection-title">Error Handling</h3>
            <div class="warning-box">
                All API responses follow a consistent format. Always check the <code>success</code> field before processing data.
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>HTTP Status</th>
                        <th>Meaning</th>
                        <th>Frontend Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>200</td><td>Success</td><td>Process response data</td></tr>
                    <tr><td>401</td><td>Unauthorized</td><td>Redirect to login</td></tr>
                    <tr><td>403</td><td>Forbidden</td><td>Show permission error</td></tr>
                    <tr><td>422</td><td>Validation Error</td><td>Display field errors</td></tr>
                    <tr><td>500</td><td>Server Error</td><td>Show generic error message</td></tr>
                </tbody>
            </table>
        </div>

        <div class="subsection">
            <h3 class="subsection-title">Pagination</h3>
            <div class="info-box">
                Most list endpoints support pagination. Use <code>page</code> and <code>per_page</code> parameters.
            </div>
            
            <h4>Example Pagination Request:</h4>
            <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 9px;">
GET /api/participants?page=2&per_page=20&search=john

Response:
{
    "success": true,
    "data": [...],
    "meta": {
        "current_page": 2,
        "last_page": 5,
        "per_page": 20,
        "total": 87
    }
}
            </pre>
        </div>

        <div class="subsection">
            <h3 class="subsection-title">Common Query Parameters</h3>
            <table>
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Usage</th>
                        <th>Example</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>search</td><td>Text search</td><td><code>?search=basketball</code></td></tr>
                    <tr><td>filter[field]</td><td>Filter by field</td><td><code>?filter[school_id]=1</code></td></tr>
                    <tr><td>sort</td><td>Sort results</td><td><code>?sort=-created_at</code></td></tr>
                    <tr><td>include</td><td>Include relations</td><td><code>?include=school,category</code></td></tr>
                    <tr><td>page</td><td>Page number</td><td><code>?page=2</code></td></tr>
                    <tr><td>per_page</td><td>Items per page</td><td><code>?per_page=50</code></td></tr>
                </tbody>
            </table>
        </div>

        <div class="subsection">
            <h3 class="subsection-title">Role-Based Access</h3>
            <table>
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Frontend Considerations</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><span class="role-badge">admin</span></td><td>Full system access</td><td>Show all management features</td></tr>
                    <tr><td><span class="role-badge">manager</span></td><td>Tournament & event management</td><td>Hide user management, show event controls</td></tr>
                    <tr><td><span class="role-badge">user</span></td><td>View & basic operations</td><td>Read-only interface with limited actions</td></tr>
                </tbody>
            </table>
        </div>
    </div>
        
    <div class="section page-break" id="data-structures">
        <h2 class="section-title">20. Data Structures</h2>
        
        @php
            $models = [
                'User' => ['first_name', 'last_name', 'email', 'contact_number', 'role'],
                'School' => ['name', 'address', 'region', 'contact_person', 'contact_number'],
                'Participant' => ['first_name', 'last_name', 'date_of_birth', 'gender', 'school_id', 'category_id'],
                'Category' => ['name', 'description', 'age_group'],
                'Sport' => ['name', 'description', 'category', 'rules'],
                'Team' => ['name', 'sport_id', 'school_id', 'category_id'],
                'Venue' => ['name', 'location', 'capacity', 'type', 'facilities'],
                'Tournament' => ['name', 'description', 'start_date', 'end_date', 'level', 'prisaa_year_id'],
                'Schedule' => ['tournament_id', 'match_id', 'venue_id', 'scheduled_date', 'start_time', 'end_time'],
                'GameMatch' => ['tournament_id', 'team1_id', 'team2_id', 'schedule_id', 'status', 'result_id'],
                'Official' => ['first_name', 'last_name', 'role', 'sport_id', 'certification_level'],
                'Result' => ['match_id', 'winner_id', 'score_team1', 'score_team2', 'status'],
                'Ranking' => ['tournament_id', 'team_id', 'participant_id', 'rank', 'points'],
                'MedalTally' => ['school_id', 'tournament_id', 'gold', 'silver', 'bronze', 'total_points'],
                'PrisaaYear' => ['year', 'host_region', 'host_city', 'start_date', 'end_date', 'total_participants', 'highlights', 'achievements'],
                'OverallChampion' => ['prisaa_year_id', 'school_id', 'level', 'total_gold', 'total_silver', 'total_bronze', 'total_points', 'overall_rank']
            ];
        @endphp

        @foreach ($models as $model => $fields)
            <div class="subsection">
                <h3 class="subsection-title">{{ $model }} Object</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>id</td><td><span class="data-type">integer</span></td><td>Unique identifier</td></tr>
                        @foreach ($fields as $field)
                            <tr><td>{{$field}}</td><td><span class="data-type">string</span></td><td></td></tr>
                        @endforeach
                        <tr><td>created_at</td><td><span class="data-type">timestamp</span></td><td>Creation timestamp</td></tr>
                        <tr><td>updated_at</td><td><span class="data-type">timestamp</span></td><td>Last update timestamp</td></tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <div class="footer">
        <p><strong>PRISAA Management System API Documentation</strong></p>
        <p>Version 3.0.0 | Generated {{ date('Y-m-d H:i:s') }}</p>
        <p>Complete API for Private Schools Athletic Association Games Management</p>
    </div>
</body>
</html>
</body>
</html>