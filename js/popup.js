
$(document).ready(function() {
    $('#test').on('submit', function(e) {
        e.preventDefault(); // Sprečava standardno slanje forme

        $.ajax({
            url: 'check_availability.php', // Ili druga ciljana PHP skripta
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                openPopup(response);
            },
            error: function() {
                openPopup("Došlo je do greške pri provjeri dostupnosti.");
            }
        });
    });
});

function openPopup(message) {
    document.getElementById('availabilityMessage').innerText = message;
    document.getElementById('availabilityPopup').style.display = 'block';
    document.getElementById('backdrop').style.display = 'block'; // Prikazuje backdrop
}

function closePopup() {
    document.getElementById('availabilityPopup').style.display = 'none';
    document.getElementById('backdrop').style.display = 'none'; // Skriva backdrop
}

