# Next.js Frontend Integration Guide
## Core API Routes for PRISAA Sports Management System

### 🔐 **1. Authentication Routes (Priority: HIGH)**
```typescript
// Base URL: http://localhost:8000/api

// Authentication endpoints - Essential for user management
POST   /auth/register          // User registration
POST   /auth/login             // User login (returns Bearer token)
POST   /auth/logout            // Logout current session
POST   /auth/logout-all        // Logout all devices
GET    /auth/me                // Get current user info
POST   /auth/refresh           // Refresh token
POST   /auth/forgot-password   // Password reset request
POST   /auth/reset-password    // Password reset confirmation
```

### 🌐 **2. Public API Routes (Priority: HIGH)**
*No authentication required - Perfect for homepage, dashboards, and public views*

```typescript
// Schools & Basic Data
GET    /public/schools                    // List all schools
GET    /public/schools/{id}               // Get specific school
GET    /public/schools/region/{region}    // Schools by region

// Sports Information
GET    /public/sports                     // List all sports
GET    /public/sports/{id}                // Get specific sport
GET    /public/sports/category/{category} // Sports by category

// Live Data & Results
GET    /public/matches                    // All matches
GET    /public/matches/upcoming           // Upcoming matches
GET    /public/matches/completed          // Completed matches

GET    /public/results                    // All results
GET    /public/results/recent            // Recent results

// Rankings & Performance
GET    /public/rankings                         // All rankings
GET    /public/rankings/sport/{sportId}/teams   // Team rankings by sport
GET    /public/rankings/sport/{sportId}/individuals // Individual rankings
GET    /public/rankings/top-performers          // Top performers

// Medal Tallies
GET    /public/medals                     // All medal tallies
GET    /public/medals/school-ranking      // School medal rankings
GET    /public/medals/statistics          // Overall medal statistics

// Tournaments & Schedules
GET    /public/tournaments                // All tournaments
GET    /public/tournaments/upcoming       // Upcoming tournaments
GET    /public/tournaments/ongoing        // Ongoing tournaments
GET    /public/tournaments/completed      // Completed tournaments

GET    /public/schedules                  // All schedules
GET    /public/schedules/upcoming         // Upcoming events
```

### 🏆 **3. Core Protected Routes (Priority: MEDIUM)**
*Require Bearer token authentication*

```typescript
// User Profile Management
GET    /users/profile          // Get own profile
PUT    /users/profile          // Update own profile
POST   /users/change-password  // Change password

// School Management (for admin/manager roles)
GET    /schools                // List schools (with admin features)
POST   /schools                // Create school (admin)
GET    /schools/{id}           // Get school details
PUT    /schools/{id}           // Update school (admin)
GET    /schools/{id}/statistics // School performance stats

// Sports Management
GET    /sports                 // List sports (with management features)
POST   /sports                 // Create sport (admin/manager)
PUT    /sports/{id}            // Update sport
GET    /sports/{id}/statistics // Sport statistics

// Tournament Management
GET    /tournaments            // List tournaments (with management)
POST   /tournaments            // Create tournament (admin/manager)
PUT    /tournaments/{id}       // Update tournament
POST   /tournaments/{id}/register // Register for tournament
PATCH  /tournaments/{id}/status    // Update tournament status
GET    /tournaments/{id}/statistics // Tournament stats
```

### 📊 **4. Advanced Data Routes (Priority: MEDIUM)**

```typescript
// Athletes/Participants
GET    /athletes                    // List athletes
GET    /athletes/school/{schoolId}  // Athletes by school
GET    /athletes/sport/{sportId}    // Athletes by sport

// Teams
GET    /teams                      // List teams
GET    /teams/school/{schoolId}    // Teams by school
GET    /teams/sport/{sportId}      // Teams by sport
GET    /teams/{id}/statistics      // Team performance

// Matches (Protected version with more details)
GET    /matches                    // All matches (with management features)
PATCH  /matches/{id}/score         // Update match score

// Results Management
GET    /results                    // All results (with management)
POST   /results                    // Create result (admin/manager)
PATCH  /results/{id}/verify        // Verify result
```

### 🏅 **5. Historical & Analytics Routes (Priority: LOW)**

```typescript
// PRISAA Years (Historical data)
GET    /prisaa-years                        // List all PRISAA years (2017-2025)
GET    /prisaa-years/{year}                 // Specific year details
GET    /prisaa-years/{year}/statistics      // Year statistics
GET    /prisaa-years/{year}/multi-level     // Multi-level breakdown

// Overall Champions
GET    /overall-champions                   // All champions
GET    /overall-champions/by-year/{year}    // Champions by year
GET    /overall-champions/by-level/{level}  // Champions by level (Provincial/Regional/National)
```

---

## 🚀 **Next.js Implementation Strategy**

### **1. API Client Setup**
```typescript
// lib/api.ts
const API_BASE_URL = 'http://localhost:8000/api';

class ApiClient {
  private baseURL = API_BASE_URL;
  
  // Public endpoints (no auth required)
  public = {
    schools: () => this.get('/public/schools'),
    sports: () => this.get('/public/sports'),
    matches: {
      upcoming: () => this.get('/public/matches/upcoming'),
      completed: () => this.get('/public/matches/completed'),
    },
    results: {
      recent: () => this.get('/public/results/recent'),
    },
    rankings: {
      topPerformers: () => this.get('/public/rankings/top-performers'),
    },
    medals: {
      schoolRanking: () => this.get('/public/medals/school-ranking'),
      statistics: () => this.get('/public/medals/statistics'),
    },
    tournaments: {
      upcoming: () => this.get('/public/tournaments/upcoming'),
      ongoing: () => this.get('/public/tournaments/ongoing'),
    }
  };
  
  // Authentication
  auth = {
    login: (credentials) => this.post('/auth/login', credentials),
    register: (userData) => this.post('/auth/register', userData),
    me: () => this.get('/auth/me'),
    logout: () => this.post('/auth/logout'),
  };
}
```

### **2. Frontend Pages Priority**

**🎯 High Priority Pages (Use Public APIs):**
1. **Homepage** - Recent results, upcoming matches, medal standings
2. **Live Dashboard** - Real-time scores, ongoing tournaments
3. **Schools Directory** - Public school listings and profiles  
4. **Sports Calendar** - Upcoming events and schedules
5. **Medal Tally** - Current standings and statistics
6. **Rankings** - Top performers and team rankings

**🔐 Medium Priority Pages (Require Auth):**
1. **User Dashboard** - Personal profile and team management
2. **Tournament Management** - For admin/manager roles
3. **Results Entry** - For officials and managers
4. **School Management** - Admin features

**📊 Low Priority Pages (Analytics):**
1. **Historical Data** - PRISAA years archive
2. **Advanced Analytics** - Detailed performance metrics
3. **Championship History** - Overall champions tracking

### **3. Recommended Route Usage by Page**

```typescript
// Homepage Component
const Homepage = () => {
  const { data: recentResults } = useSWR('/public/results/recent');
  const { data: upcomingMatches } = useSWR('/public/matches/upcoming');
  const { data: medalStandings } = useSWR('/public/medals/school-ranking');
  const { data: topPerformers } = useSWR('/public/rankings/top-performers');
  
  // Render dashboard with live data
};

// Live Scores Page
const LiveScores = () => {
  const { data: ongoingTournaments } = useSWR('/public/tournaments/ongoing');
  const { data: upcomingMatches } = useSWR('/public/matches/upcoming');
  const { data: recentResults } = useSWR('/public/results/recent');
  
  // Real-time updates
};

// Schools Directory
const SchoolsPage = () => {
  const { data: schools } = useSWR('/public/schools');
  const { data: medalStats } = useSWR('/public/medals/statistics');
  
  // Filterable school listings
};
```

---

## 🔧 **Essential Implementation Notes**

### **Authentication Flow:**
1. Use `/auth/login` to get Bearer token
2. Store token in localStorage/cookies
3. Include in all protected requests: `Authorization: Bearer {token}`
4. Use `/auth/me` to verify token validity
5. Handle 401 responses by redirecting to login

### **Data Fetching Strategy:**
- **Public routes**: Use for initial page loads and SEO
- **Protected routes**: Use for user-specific data
- **Real-time updates**: Consider WebSocket or polling for live scores
- **Caching**: Implement SWR/React Query for optimal performance

### **Route Parameters & Filtering:**
Most routes support query parameters:
- `?page=1&per_page=20` - Pagination
- `?search=basketball` - Text search
- `?filter[school_id]=1` - Filtering
- `?sort=-created_at` - Sorting
- `?include=school,sport` - Include relationships

This gives you **48 core routes** out of 215 total that will handle 90% of your Next.js frontend needs! 🎯
