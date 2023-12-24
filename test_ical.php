<?php
$icalUrl = 'https://fr.airbnb.ch/calendar/ical/891043665434773846.ics?s=4cc20d2929c9bb980745f5c5443dc64d';

// Vaša funkcija getIcalEvents...

// Funkcija za provjeru raspoloživosti
function checkAvailability($events, $startDate, $endDate) {
    foreach ($events as $event) {
        $eventStart = DateTime::createFromFormat('Ymd', $event['start']);
        $eventEnd = DateTime::createFromFormat('Ymd', $event['end']);

        if ($startDate < $eventEnd && $endDate > $eventStart) {
            return "Nije dostupno za boravak od " . $startDate->format('Y-m-d') . " do " . $endDate->format('Y-m-d');
        }
    }

    return "Dostupno za boravak od " . $startDate->format('Y-m-d') . " do " . $endDate->format('Y-m-d');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['checkin']) && !empty($_POST['checkout'])) {
        $checkin = DateTime::createFromFormat('Y-m-d', $_POST['checkin']);
        $checkout = DateTime::createFromFormat('Y-m-d', $_POST['checkout']);
        $minStay = $checkout->diff($checkin)->days;

        if ($checkin === false || $checkout === false) {
            echo "Neispravan format datuma.";
        } else if ($checkout <= $checkin) {
            echo "Datum odjave mora biti nakon datuma prijave.";
        } else {
            $events = getIcalEvents($icalUrl);
            if ($events === null) {
                echo "Greška u dohvaćanju iCal podataka.";
            } else {
                echo checkAvailability($events, $checkin, $checkout);
            }
        }
    } else {
        echo "Molimo unesite datume prijave i odjave.";
    }
} else {
    echo "Forma nije poslana.";
}
?>
