// Tab Switching Logic
function showTab(tabId, event) {
    const contents = document.querySelectorAll('.switch_content');
    contents.forEach(content => content.classList.remove('active'));

    const buttons = document.querySelectorAll('.switch_button');
    buttons.forEach(button => button.classList.remove('active'));

    document.getElementById(tabId).classList.add('active');
    if(event) {
        event.currentTarget.classList.add('active');
    }

    // Re-apply search filter when switching tabs
    const searchTerm = document.getElementById('search').value;
    filterTable(searchTerm);
}

// Search Logic
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('search').addEventListener('input', function() {
        filterTable(this.value);
    });
});

function filterTable(searchTerm) {
    const term = searchTerm.toLowerCase();
    const activeContainer = document.querySelector('.switch_content.active');
    if (!activeContainer) return;

    const rows = activeContainer.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
}

// Archive User Function
function archiveUser(id) {
    if (confirm('Are you sure you want to archive this user?')) {
        fetch('manageuser_delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `archive_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`row-${id}`);
                if(row) row.remove();
                alert('User archived successfully.');
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

// Restore User Function
function restoreUser(id) {
    if (confirm('Are you sure you want to restore this user?')) {
        fetch('manageuser_delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `restore_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`archived-row-${id}`);
                if(row) row.remove();
                alert('User restored successfully.');
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