<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ActionLog;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        // Static date for demonstration - you can modify this as needed
        $currentDate = now();

        // Get calendar data for the current month
        $calendarData = $this->getCalendarData($currentDate);

        // Get CRM events for the current month
        $events = $this->getCrmEvents($currentDate);

        return view('admin.calendar.index', compact('calendarData', 'currentDate', 'events'));
    }

    private function getCalendarData($date)
    {
        $year = $date->year;
        $month = $date->month;

        // Get first day of the month
        $firstDay = $date->copy()->startOfMonth();
        $lastDay = $date->copy()->endOfMonth();

        // Get the day of the week for the first day (0 = Sunday, 1 = Monday, etc.)
        $firstDayOfWeek = $firstDay->dayOfWeek;

        // Get the number of days in the month
        $daysInMonth = $lastDay->day;

        // Create calendar grid
        $calendar = [];
        $week = [];

        // Add empty cells for days before the first day of the month
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $week[] = null;
        }

        // Add days of the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $week[] = [
                'day' => $day,
                'date' => $date->copy()->day($day),
                'isToday' => $date->copy()->day($day)->isToday(),
                'isCurrentMonth' => true
            ];

            // If we have 7 days in the week, start a new week
            if (count($week) === 7) {
                $calendar[] = $week;
                $week = [];
            }
        }

        // Add remaining days from next month to complete the last week
        $nextMonth = $date->copy()->addMonth();
        $dayNumber = 1;
        while (count($week) < 7 && count($week) > 0) {
            $week[] = [
                'day' => $dayNumber,
                'date' => $nextMonth->copy()->day($dayNumber),
                'isToday' => false,
                'isCurrentMonth' => false
            ];
            $dayNumber++;
        }

        // Add the last week if it's not empty
        if (!empty($week)) {
            $calendar[] = $week;
        }

        return $calendar;
    }

    private function getCrmEvents($date)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $events = [];

        // Get proposals submitted in this month
        $proposals = Proposal::whereBetween('submitted_at', [$startOfMonth, $endOfMonth])
            ->with('user')
            ->get();

        foreach ($proposals as $proposal) {
            $events[] = [
                'id' => 'proposal_' . (string) $proposal->id,
                'type' => 'proposal',
                'title' => 'Proposal Submitted',
                'description' => (string) ($proposal->title ?? 'No title'),
                'date' => $proposal->submitted_at,
                'user' => (string) (($proposal->user && $proposal->user->name) ? $proposal->user->name : 'Unknown'),
                'status' => (string) ($proposal->status ?? 'unknown'),
                'color' => (string) $this->getEventColor('proposal', $proposal->status),
                'icon' => 'fas fa-file-alt'
            ];
        }

        // Get recent activity logs for this month
        $activities = ActionLog::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();

        foreach ($activities as $activity) {
            $events[] = [
                'id' => 'activity_' . (string) $activity->id,
                'type' => 'activity',
                'title' => (string) ucfirst(str_replace('_', ' ', $activity->action ?? 'unknown')),
                'description' => (string) ($activity->description ?? 'No description'),
                'date' => $activity->created_at,
                'user' => (string) (($activity->user && $activity->user->name) ? $activity->user->name : 'System'),
                'color' => (string) $this->getEventColor('activity', $activity->action),
                'icon' => (string) $this->getActivityIcon($activity->action)
            ];
        }

        // Add static demo events for better visualization
        $events = array_merge($events, $this->getStaticEvents($date));

        // Sort events by date
        usort($events, function($a, $b) {
            $dateA = $a['date'] instanceof \Carbon\Carbon ? $a['date'] : \Carbon\Carbon::parse($a['date']);
            $dateB = $b['date'] instanceof \Carbon\Carbon ? $b['date'] : \Carbon\Carbon::parse($b['date']);
            return $dateA->timestamp - $dateB->timestamp;
        });

        // Ensure all event properties are strings or proper types
        $events = array_map(function($event) {
            // Helper function to safely convert to string
            $toString = function($value, $default = '') {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (is_object($value)) {
                    return (string) $value;
                }
                if (is_null($value)) {
                    return $default;
                }
                return (string) $value;
            };

            return [
                'id' => $toString($event['id'] ?? null, 'unknown'),
                'type' => $toString($event['type'] ?? null, 'unknown'),
                'title' => $toString($event['title'] ?? null, 'Event'),
                'description' => $toString($event['description'] ?? null, 'No description'),
                'date' => $event['date'],
                'user' => $toString($event['user'] ?? null, 'System'),
                'color' => $toString($event['color'] ?? null, '#95a5a6'),
                'icon' => $toString($event['icon'] ?? null, 'fas fa-info-circle'),
                'time' => isset($event['time']) ? $toString($event['time']) : null,
                'status' => isset($event['status']) ? $toString($event['status']) : null,
                'isStatic' => isset($event['isStatic']) ? (bool) $event['isStatic'] : false,
            ];
        }, $events);

        return $events;
    }

    private function getStaticEvents($date)
    {
        $events = [];
        $currentMonth = $date->month;
        $currentYear = $date->year;

        // Add some static events for demonstration
        $staticEvents = [
            [
                'type' => 'meeting',
                'title' => 'Client Meeting - Proposal Review',
                'description' => 'Meeting with TechCorp regarding web development proposal',
                'day' => 5,
                'time' => '10:00 AM',
                'color' => '#e74c3c',
                'icon' => 'fas fa-video'
            ],
            [
                'type' => 'interview',
                'title' => 'Interview - Sarah Johnson',
                'description' => 'Technical interview for React developer position',
                'day' => 8,
                'time' => '2:00 PM',
                'color' => '#3498db',
                'icon' => 'fas fa-user-tie'
            ],
            [
                'type' => 'deadline',
                'title' => 'Proposal Deadline',
                'description' => 'E-commerce platform proposal submission deadline',
                'day' => 12,
                'time' => '11:59 PM',
                'color' => '#f39c12',
                'icon' => 'fas fa-clock'
            ],
            [
                'type' => 'upwork',
                'title' => 'Upwork Profile Update',
                'description' => 'Monthly profile optimization and skill updates',
                'day' => 15,
                'time' => '9:00 AM',
                'color' => '#2ecc71',
                'icon' => 'fas fa-user-circle'
            ],
            [
                'type' => 'meeting',
                'title' => 'Team Standup',
                'description' => 'Weekly team meeting and progress review',
                'day' => 18,
                'time' => '9:30 AM',
                'color' => '#9b59b6',
                'icon' => 'fas fa-users'
            ],
            [
                'type' => 'goal',
                'title' => 'Monthly Goal Review',
                'description' => 'Review and set new monthly goals for BD team',
                'day' => 25,
                'time' => '3:00 PM',
                'color' => '#1abc9c',
                'icon' => 'fas fa-bullseye'
            ],
            [
                'type' => 'interview',
                'title' => 'Interview - Mike Chen',
                'description' => 'Final interview for senior developer role',
                'day' => 28,
                'time' => '1:00 PM',
                'color' => '#3498db',
                'icon' => 'fas fa-user-tie'
            ]
        ];

        foreach ($staticEvents as $event) {
            $eventDate = Carbon::create($currentYear, $currentMonth, $event['day'], 9, 0);
            $events[] = [
                'id' => 'static_' . (string) $event['type'] . '_' . (string) $event['day'],
                'type' => (string) $event['type'],
                'title' => (string) $event['title'],
                'description' => (string) $event['description'],
                'date' => $eventDate,
                'time' => (string) $event['time'],
                'user' => 'Admin',
                'color' => (string) $event['color'],
                'icon' => (string) $event['icon'],
                'isStatic' => true
            ];
        }

        return $events;
    }

    private function getEventColor($type, $status = null)
    {
        $colors = [
            'proposal' => [
                'submitted' => '#3498db',
                'interviewing' => '#f39c12',
                'hired' => '#2ecc71',
                'rejected' => '#e74c3c'
            ],
            'activity' => [
                'created' => '#2ecc71',
                'updated' => '#f39c12',
                'deleted' => '#e74c3c',
                'login' => '#9b59b6'
            ],
            'meeting' => '#e74c3c',
            'interview' => '#3498db',
            'deadline' => '#f39c12',
            'upwork' => '#2ecc71',
            'goal' => '#1abc9c'
        ];

        if ($type === 'proposal' && isset($colors['proposal'][$status])) {
            return (string) $colors['proposal'][$status];
        }

        if ($type === 'activity' && isset($colors['activity'][$status])) {
            return (string) $colors['activity'][$status];
        }

        // Handle case where type exists but status doesn't match
        if ($type === 'proposal') {
            return (string) ($colors['proposal']['submitted'] ?? '#95a5a6');
        }

        if ($type === 'activity') {
            return (string) ($colors['activity']['created'] ?? '#95a5a6');
        }

        return (string) ($colors[$type] ?? '#95a5a6');
    }

    private function getActivityIcon($action)
    {
        $icons = [
            'created' => 'fas fa-plus-circle',
            'updated' => 'fas fa-edit',
            'deleted' => 'fas fa-trash',
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt'
        ];

        return (string) ($icons[$action] ?? 'fas fa-info-circle');
    }
}
