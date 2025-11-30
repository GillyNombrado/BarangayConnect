function deleteResident(id) {
    if (confirm('Are you sure you want to delete this resident?')) {
        fetch('resident_delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`row-${id}`).remove();
                alert('Resident deleted successfully.');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }
}