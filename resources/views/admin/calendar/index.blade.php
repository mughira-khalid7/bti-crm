@extends('admin.layouts.master')

@section('title', 'Calendar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Calendar - {{ $currentDate->format('F Y') }}
                    </h3>
                    <div class="calendar-controls">
                        <button class="btn btn-outline-primary btn-sm" onclick="previousMonth()">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="nextMonth()">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="calendar-container">
                        <div class="calendar-header">
                            <div class="day-header">Sun</div>
                            <div class="day-header">Mon</div>
                            <div class="day-header">Tue</div>
                            <div class="day-header">Wed</div>
                            <div class="day-header">Thu</div>
                            <div class="day-header">Fri</div>
                            <div class="day-header">Sat</div>
                        </div>
                        <div class="calendar-grid">
                            @foreach($calendarData as $week)
                                <div class="calendar-week">
                                    @foreach($week as $day)
                                        <div class="calendar-day {{ $day && $day['isToday'] ? 'today' : '' }} {{ $day && !$day['isCurrentMonth'] ? 'other-month' : '' }}">
                                            @if($day)
                                                <div class="day-number">{{ $day['day'] }}</div>
                                                <div class="day-content">
                                                    @php
                                                        $dayEvents = collect($events)->filter(function($event) use ($day) {
                                                            $eventDate = $event['date'] instanceof \Carbon\Carbon ? $event['date'] : \Carbon\Carbon::parse($event['date']);
                                                            return $eventDate->day == $day['day'] &&
                                                                   $eventDate->month == $day['date']->month &&
                                                                   $eventDate->year == $day['date']->year;
                                                        })->take(3);
                                                    @endphp

                                                    @foreach($dayEvents as $event)
                                                        @php
                                                            // Ensure all event properties are strings
                                                            $event = array_map(function($value) {
                                                                if (is_array($value)) {
                                                                    return json_encode($value);
                                                                }
                                                                if (is_object($value)) {
                                                                    return (string) $value;
                                                                }
                                                                if (is_null($value)) {
                                                                    return '';
                                                                }
                                                                return (string) $value;
                                                            }, $event);
                                                        @endphp
                                                        <div class="event-item"
                                                             style="background-color: {{ $event['color'] }}20; border-left: 3px solid {{ $event['color'] }};"
                                                             data-event-id="{{ is_string($event['id']) ? $event['id'] : 'event_' . uniqid() }}"
                                                             data-event-type="{{ is_string($event['type']) ? $event['type'] : 'unknown' }}"
                                                             data-event-title="{{ is_string($event['title']) ? $event['title'] : 'Event' }}"
                                                             data-event-description="{{ is_string($event['description']) ? $event['description'] : 'No description' }}"
                                                             data-event-time="{{ $event['time'] ?? (($event['date'] instanceof \Carbon\Carbon ? $event['date'] : \Carbon\Carbon::parse($event['date']))->format('g:i A')) }}"
                                                             data-event-user="{{ is_string($event['user'] ?? 'System') ? ($event['user'] ?? 'System') : 'System' }}"
                                                             title="{{ (is_string($event['title']) ? $event['title'] : 'Event') }} - {{ (is_string($event['description']) ? $event['description'] : 'No description') }}">
                                                            <i class="{{ is_string($event['icon']) ? $event['icon'] : 'fas fa-info-circle' }} event-icon"></i>
                                                            <span class="event-title">{{ Str::limit(is_string($event['title']) ? $event['title'] : 'Event', 20) }}</span>
                                                        </div>
                                                    @endforeach

                                                    @if($dayEvents->count() > 3)
                                                        <div class="more-events">
                                                            +{{ $dayEvents->count() - 3 }} more
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div id="eventModal" class="event-modal">
    <div class="event-modal-content">
        <div class="event-modal-header">
            <div class="event-modal-title">
                <i id="modalEventIcon" class="fas fa-info-circle"></i>
                <span id="modalEventTitle">Event Details</span>
            </div>
            <span class="event-modal-close" onclick="closeEventModal()">&times;</span>
        </div>
        <div class="event-modal-body">
            <div class="event-detail">
                <i class="fas fa-calendar"></i>
                <span id="modalEventDate">Date</span>
            </div>
            <div class="event-detail">
                <i class="fas fa-clock"></i>
                <span id="modalEventTime">Time</span>
            </div>
            <div class="event-detail">
                <i class="fas fa-user"></i>
                <span id="modalEventUser">User</span>
            </div>
            <div class="event-detail">
                <i class="fas fa-info-circle"></i>
                <span id="modalEventDescription">Description</span>
            </div>
        </div>
    </div>
</div>

<style>
.calendar-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.day-header {
    padding: 12px 8px;
    text-align: center;
    font-weight: 600;
    color: #495057;
    border-right: 1px solid #dee2e6;
    font-size: 0.875rem;
}

.day-header:last-child {
    border-right: none;
}

.calendar-grid {
    display: flex;
    flex-direction: column;
}

.calendar-week {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    min-height: 100px;
}

.calendar-day {
    border-right: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    padding: 8px;
    position: relative;
    background: white;
    transition: background-color 0.2s ease;
}

.calendar-day:hover {
    background-color: #f8f9fa;
}

.calendar-day.today {
    background-color: #e3f2fd;
    border-color: #2196f3;
}

.calendar-day.today .day-number {
    background-color: #2196f3;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.calendar-day.other-month {
    background-color: #f8f9fa;
    color: #6c757d;
}

.calendar-day.other-month .day-number {
    color: #adb5bd;
}

.day-number {
    font-weight: 500;
    margin-bottom: 4px;
    font-size: 0.875rem;
}

.day-content {
    font-size: 0.75rem;
    color: #6c757d;
}

.calendar-controls {
    display: flex;
    gap: 8px;
}

.calendar-controls .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Event styling */
.event-item {
    margin: 2px 0;
    padding: 4px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
}

.event-item:hover {
    transform: translateX(2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.event-icon {
    font-size: 0.6rem;
    flex-shrink: 0;
}

.event-title {
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.more-events {
    font-size: 0.6rem;
    color: #6c757d;
    font-style: italic;
    margin-top: 2px;
    text-align: center;
}

/* Event modal */
.event-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.event-modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    position: relative;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.event-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}

.event-modal-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.event-modal-close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.event-modal-close:hover {
    color: #000;
}

.event-modal-body {
    margin-bottom: 15px;
}

.event-detail {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.event-detail i {
    width: 16px;
    color: #6c757d;
}

.event-detail span {
    color: #495057;
}

/* Responsive design */
@media (max-width: 768px) {
    .calendar-week {
        min-height: 100px;
    }

    .calendar-day {
        padding: 4px;
    }

    .day-number {
        font-size: 0.75rem;
    }

    .day-content {
        font-size: 0.625rem;
    }

    .event-item {
        padding: 2px 4px;
        font-size: 0.6rem;
    }

    .event-modal-content {
        margin: 10% auto;
        width: 95%;
        padding: 15px;
    }
}
</style>

<script>
function previousMonth() {
    // This would typically make an AJAX request to load the previous month
    // For now, we'll just show an alert
    alert('Previous month functionality would be implemented here');
}

function nextMonth() {
    // This would typically make an AJAX request to load the next month
    // For now, we'll just show an alert
    alert('Next month functionality would be implemented here');
}

// Event modal functionality
function showEventModal(eventElement) {
    const modal = document.getElementById('eventModal');
    const eventId = eventElement.getAttribute('data-event-id');
    const eventType = eventElement.getAttribute('data-event-type');
    const eventTitle = eventElement.getAttribute('data-event-title');
    const eventDescription = eventElement.getAttribute('data-event-description');
    const eventTime = eventElement.getAttribute('data-event-time');
    const eventUser = eventElement.getAttribute('data-event-user');

    // Get the date from the calendar day
    const dayElement = eventElement.closest('.calendar-day');
    const dayNumber = dayElement.querySelector('.day-number').textContent;
    const currentMonth = '{{ $currentDate->format("F") }}';
    const currentYear = '{{ $currentDate->format("Y") }}';

    // Update modal content
    document.getElementById('modalEventTitle').textContent = eventTitle;
    document.getElementById('modalEventDescription').textContent = eventDescription;
    document.getElementById('modalEventTime').textContent = eventTime;
    document.getElementById('modalEventUser').textContent = eventUser;
    document.getElementById('modalEventDate').textContent = `${currentMonth} ${dayNumber}, ${currentYear}`;

    // Set appropriate icon based on event type
    const iconMap = {
        'proposal': 'fas fa-file-alt',
        'meeting': 'fas fa-video',
        'interview': 'fas fa-user-tie',
        'deadline': 'fas fa-clock',
        'upwork': 'fas fa-user-circle',
        'goal': 'fas fa-bullseye',
        'activity': 'fas fa-info-circle'
    };

    const iconElement = document.getElementById('modalEventIcon');
    iconElement.className = iconMap[eventType] || 'fas fa-info-circle';

    // Show modal
    modal.style.display = 'block';
}

function closeEventModal() {
    document.getElementById('eventModal').style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('eventModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Add click event listeners to all event items
document.addEventListener('DOMContentLoaded', function() {
    const eventItems = document.querySelectorAll('.event-item');
    eventItems.forEach(function(item) {
        item.addEventListener('click', function() {
            showEventModal(this);
        });
    });
});
</script>
@endsection
