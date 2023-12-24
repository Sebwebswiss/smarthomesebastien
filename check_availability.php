<?php
// URL of your Airbnb iCal calendar
$icalUrl = 'https://fr.airbnb.ch/calendar/ical/891043665434773846.ics?s=4cc20d2929c9bb980745f5c5443dc64d';

// Function to fetch iCal data
function getIcalEvents($url) {
    $content = file_get_contents($url);
    if (!$content) {
        return null;
    }

    $pattern = '/BEGIN:VEVENT.*?END:VEVENT/si';
    preg_match_all($pattern, $content, $matches);

    $events = [];
    foreach ($matches[0] as $item) {
        $event = [];

        if (preg_match('/DTSTART;VALUE=DATE:(\w+)/', $item, $start)) {
            $event['start'] = $start[1];
        }

        if (preg_match('/DTEND;VALUE=DATE:(\w+)/', $item, $end)) {
            $event['end'] = $end[1];
        }

        $events[] = $event;
    }

    return $events;
}

// Function to check availability
function checkAvailability($events, $startDate, $endDate, $minStay) {
    // Check for minimum stay
    $interval = $startDate->diff($endDate);
    if ($interval->days < $minStay) {
        return "Minimum stay is $minStay nights.";
    }

    // Check if dates are in the past
    $today = new DateTime();
    $today->setTime(0, 0, 0); // Reset time to the start of the day
    if ($startDate < $today || $endDate <= $startDate) {
        return "Selected dates are in the past.";
    }

    foreach ($events as $event) {
        $eventStart = DateTime::createFromFormat('Ymd', $event['start']);
        $eventEnd = DateTime::createFromFormat('Ymd', $event['end']);

        if (($startDate < $eventEnd) && ($endDate > $eventStart)) {
            return "Not available for the selected period: " . $startDate->format('d.m.Y') . " to " . $endDate->format('d.m.Y');
        }
    }

    return "Available for the selected period: " . $startDate->format('d.m.Y') . " to " . $endDate->format('d.m.Y');
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['checkin']) && !empty($_POST['checkout'])) {
        $checkin = DateTime::createFromFormat('m/d/Y', $_POST['checkin']);
        $checkout = DateTime::createFromFormat('m/d/Y', $_POST['checkout']);

        if ($checkin === false || $checkout === false) {
            echo "Invalid date format.";
        } else {
            $events = getIcalEvents($icalUrl);
            if ($events === null) {
                echo "Error fetching iCal data.";
            } else {
                // Minimum number of nights
                $minStay = 3;
                echo checkAvailability($events, $checkin, $checkout, $minStay);
            }
        }
    } else {
        echo "Please enter check-in and check-out dates.";
    }
} else {
    echo "Form not submitted.";
}
?>
