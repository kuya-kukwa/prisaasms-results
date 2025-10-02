import { HomeIcon, UsersIcon, TrophyIcon, CalendarIcon } from "@heroicons/react/24/outline";

export const adminNavItems = [
  { title: "Dashboard", url: "/dashboard/admin", icon: HomeIcon },
  { title: "Profiles", url: "/dashboard/admin/profiles", icon: UsersIcon },
  {
    title: "Schedules",
    url: "/dashboard/admin/schedules",
    icon: CalendarIcon,
    items: [
      { title: "Overview", url: "/dashboard/admin/schedules" },
      { title: "Sports", url: "/dashboard/admin/schedules/sports" },
      { title: "Tournaments", url: "/dashboard/admin/schedules/tournaments" },
    ]
  },
  
{
  title: "Matches ",
    url: "/dashboard/admin/matches",
    icon: TrophyIcon,
    items: [
      { title: "Overview", url: "/dashboard/admin/matches" },
      { title: "Upcoming Matches", url: "/dashboard/admin/matches/upcoming-matches" },
      { title: "Standing", url: "/dashboard/admin/matches/standings" },
    ]
},

{
    title: "Results",
    url: "/dashboard/admin/results",
    icon: TrophyIcon,
    items: [
      { title: "Overview", url: "/dashboard/admin/results" },
      { title: "Past Results", url: "/dashboard/admin/results/past-results" },
      { title: "Medal Tallies", url: "/dashboard/admin/results/medal-tallies" },
    ]
  },
];
export const coachNavItems = [
  { title: "Dashboard", url: "/dashboard/coach", icon: HomeIcon },
  { title: "My Athletes", url: "/dashboard/coach/my-athletes", icon: UsersIcon },
  {
    title: "Schedules",
    url: "/dashboard/coach/schedules",
    icon: CalendarIcon,
    items: [
      { title: "Overview", url: "/dashboard/coach/schedules" },
      { title: "Sports", url: "/dashboard/coach/schedules/sports" },
      { title: "Tournaments", url: "/dashboard/coach/schedules/tournaments" },
    ]
  },
  { title: "Results", url: "/dashboard/coach/results", icon: TrophyIcon },
];

export const tmNavItems = [
  { title: "Dashboard", url: "/dashboard/tournament-manager", icon: HomeIcon },
  { title: "Manage Tournaments", url: "/dashboard/tournament-manager/tournaments", icon: TrophyIcon },
  {
    title: "Schedules",
    url: "/dashboard/tournament-manager/schedules",
    icon: CalendarIcon,
    items: [
      { title: "Overview", url: "/dashboard/tournament-manager/schedules" },
      { title: "Sports", url: "/dashboard/tournament-manager/schedules/sports" },
      { title: "Tournaments", url: "/dashboard/tournament-manager/schedules/tournaments" },
    ]
  },
  { title: "Results", url: "/dashboard/tournament-manager/results", icon: TrophyIcon },
];