<?php

namespace App\Http\Controllers\BD;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ActionLog;
use App\Models\Goal;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        // Static date for demonstration - you can modify this as needed
        $currentDate = now();

        // Get calendar data for the current month
        $calendarData = $this->getCalendarData($currentDate);

        // Get CRM events for the current month (user-specific)
        $events = $this->getCrmEvents($currentDate);

        return view('bd.calendar.index', compact('calendarData', 'currentDate', 'events'));
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
        $userId = Auth::id();

        $events = [];

        // Get user's proposals submitted in this month
        $proposals = Proposal::where('user_id', $userId)
            ->whereBetween('submitted_at', [$startOfMonth, $endOfMonth])
            ->get();

        foreach ($proposals as $proposal) {
            $events[] = [
                'id' => 'proposal_' . (string) $proposal->id,
                'type' => 'proposal',
                'title' => 'My Proposal Submitted',
                'description' => (string) ($proposal->title ?? 'No title'),
                'date' => $proposal->submitted_at,
                'status' => (string) ($proposal->status ?? 'unknown'),
                'color' => (string) $this->getEventColor('proposal', $proposal->status),
                'icon' => 'fas fa-file-alt'
            ];
        }

        // Get user's recent activity logs for this month
        $activities = ActionLog::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->latest()
            ->limit(15)
            ->get();

        foreach ($activities as $activity) {
            $events[] = [
                'id' => 'activity_' . (string) $activity->id,
                'type' => 'activity',
                'title' => (string) ucfirst(str_replace('_', ' ', $activity->action ?? 'unknown')),
                'description' => (string) ($activity->description ?? 'No description'),
                'date' => $activity->created_at,
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
                'title' => 'Client Call - Project Discussion',
                'description' => 'Follow-up call with potential client about web development project',
                'day' => 3,
                'time' => '2:00 PM',
                'color' => '#e74c3c',
                'icon' => 'fas fa-phone'
            ],
            [
                'type' => 'proposal',
                'title' => 'Proposal Review Meeting',
                'description' => 'Review proposal with team before submission',
                'day' => 7,
                'time' => '10:00 AM',
                'color' => '#3498db',
                'icon' => 'fas fa-file-alt'
            ],
            [
                'type' => 'upwork',
                'title' => 'Upwork Profile Optimization',
                'description' => 'Update skills and portfolio on Upwork profile',
                'day' => 10,
                'time' => '11:00 AM',
                'color' => '#2ecc71',
                'icon' => 'fas fa-user-circle'
            ],
            [
                'type' => 'deadline',
                'title' => 'Proposal Submission Deadline',
                'description' => 'E-commerce platform proposal due today',
                'day' => 14,
                'time' => '11:59 PM',
                'color' => '#f39c12',
                'icon' => 'fas fa-clock'
            ],
            [
                'type' => 'interview',
                'title' => 'Client Interview',
                'description' => 'Technical interview for mobile app development project',
                'day' => 17,
                'time' => '3:00 PM',
                'color' => '#9b59b6',
                'icon' => 'fas fa-video'
            ],
            [
                'type' => 'goal',
                'title' => 'Weekly Goal Check-in',
                'description' => 'Review weekly proposal submission goals',
                'day' => 21,
                'time' => '9:00 AM',
                'color' => '#1abc9c',
                'icon' => 'fas fa-bullseye'
            ],
            [
                'type' => 'meeting',
                'title' => 'Team Sync Meeting',
                'description' => 'Weekly team meeting to discuss progress and challenges',
                'day' => 24,
                'time' => '2:30 PM',
                'color' => '#8e44ad',
                'icon' => 'fas fa-users'
            ],
            [
                'type' => 'proposal',
                'title' => 'New Proposal Draft',
                'description' => 'Start working on new proposal for AI project',
                'day' => 29,
                'time' => '1:00 PM',
                'color' => '#3498db',
                'icon' => 'fas fa-edit'
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
            'interview' => '#9b59b6',
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
