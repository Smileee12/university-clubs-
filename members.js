document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const clubId = urlParams.get('id');

    if (!clubId) {
        alert('Invalid club ID');
        return;
    }

    fetchClubData(clubId);
});

async function fetchClubData(clubId) {
    try {
        const response = await fetch(`manage_members.php?id=${clubId}`);
        const data = await response.json();
        
        document.getElementById('club-name').textContent = data.club.name;
        renderMemberTable(data.members);
    } catch (error) {
        console.error('Error fetching club data:', error);
    }
}

function renderMemberTable(members) {
    const tbody = document.querySelector('#members-table tbody');
    tbody.innerHTML = '';

    members.forEach(member => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${member.full_name}</td>
            <td>${member.student_id}</td>
            <td>${member.email}</td>
            <td>${member.phone}</td>
            <td>
                <select name="role" onchange="updateRole(${member.id}, this.value)">
                    <option value="member" ${member.role === 'member' ? 'selected' : ''}>Member</option>
                    <option value="admin" ${member.role === 'admin' ? 'selected' : ''}>Admin</option>
                    <option value="vice_president" ${member.role === 'vice_president' ? 'selected' : ''}>Vice President</option>
                    <option value="secretary" ${member.role === 'secretary' ? 'selected' : ''}>Secretary</option>
                </select>
            </td>
            <td>${member.status}</td>
            <td>
                <button onclick="showEditForm(${member.id})" class="admin-button">Edit</button>
                <button onclick="approveMember(${member.id})" class="admin-button">Approve</button>
                <button onclick="deleteMember(${member.id})" class="delete-button">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);

        const editTr = document.createElement('tr');
        editTr.id = `edit-form-${member.id}`;
        editTr.className = 'edit-form';
        editTr.innerHTML = `
            <td colspan="7">
                <form onsubmit="updateMember(event, ${member.id})">
                    <label>Full Name: <input type="text" name="full_name" value="${member.full_name}"></label>
                    <label>Email: <input type="email" name="email" value="${member.email}"></label>
                    <label>Phone: <input type="tel" name="phone" value="${member.phone}"></label>
                    <button type="submit" class="admin-button">Update</button>
                </form>
            </td>
        `;
        tbody.appendChild(editTr);
    });
}

function showEditForm(memberId) {
    const editForm = document.getElementById(`edit-form-${memberId}`);
    if (editForm) {
        editForm.classList.toggle('active');
    }
}

async function updateMember(event, memberId) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('action', 'update');
    formData.append('member_id', memberId);

    try {
        const response = await fetch('manage_members.php', {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            alert('Member updated successfully');
            fetchClubData(new URLSearchParams(window.location.search).get('id'));
        } else {
            alert('Failed to update member');
        }
    } catch (error) {
        console.error('Error updating member:', error);
    }
}

async function updateRole(memberId, role) {
    const formData = new FormData();
    formData.append('action', 'assign_role');
    formData.append('member_id', memberId);
    formData.append('role', role);

    try {
        const response = await fetch('manage_members.php', {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            alert('Role updated successfully');
        } else {
            alert('Failed to update role');
        }
    } catch (error) {
        console.error('Error updating role:', error);
    }
}

async function approveMember(memberId) {
    const formData = new FormData();
    formData.append('action', 'approve');
    formData.append('member_id', memberId);

    try {
        const response = await fetch('manage_members.php', {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            alert('Member approved successfully');
            fetchClubData(new URLSearchParams(window.location.search).get('id'));
        } else {
            alert('Failed to approve member');
        }
    } catch (error) {
        console.error('Error approving member:', error);
    }
}

async function deleteMember(memberId) {
    if (confirm('Are you sure you want to delete this member?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('member_id', memberId);

        try {
            const response = await fetch('manage_members.php', {
                method: 'POST',
                body: formData
            });
            if (response.ok) {
                alert('Member deleted successfully');
                fetchClubData(new URLSearchParams(window.location.search).get('id'));
            } else {
                alert('Failed to delete member');
            }
        } catch (error) {
            console.error('Error deleting member:', error);
        }
    }
}

document.getElementById('searchInput').addEventListener('keyup', filterTable);

function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.querySelector('#members-table tbody');
    const rows = table.querySelectorAll('tr:not(.edit-form)');

    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        let match = false;

        columns.forEach((column, index) => {
            if (index < columns.length - 1 && column.textContent.toLowerCase().includes(filter)) {
                match = true;
            }
        });

        row.style.display = match ? '' : 'none';
    });
}

// Add event listener for the search input
document.getElementById('searchInput').addEventListener('input', filterTable);

// Function to handle form submission
function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const action = form.getAttribute('data-action');
    formData.append('action', action);

    fetch('manage_members.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            fetchClubData(new URLSearchParams(window.location.search).get('id'));
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Add event listeners to all forms
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', handleFormSubmit);
});

// Function to toggle dark mode
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

// Check for saved dark mode preference
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
}

// Add event listener for dark mode toggle
document.getElementById('darkModeToggle').addEventListener('click', toggleDarkMode);

// Initialize tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Function to export table data to CSV
function exportToCSV() {
    const table = document.getElementById('members-table');
    let csv = [];
    for (let i = 0; i < table.rows.length; i++) {
        let row = [], cols = table.rows[i].cells;
        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        csv.push(row.join(","));
    }
    const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    const downloadLink = document.createElement("a");
    downloadLink.download = "club_members.csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}

// Add event listener for export button
document.getElementById('exportButton').addEventListener('click', exportToCSV);

// Function to handle pagination
function handlePagination(page) {
    const itemsPerPage = 10;
    const rows = document.querySelectorAll('#members-table tbody tr:not(.edit-form)');
    const totalPages = Math.ceil(rows.length / itemsPerPage);

    rows.forEach((row, index) => {
        if (index >= (page - 1) * itemsPerPage && index < page * itemsPerPage) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    updatePaginationButtons(page, totalPages);
}

// Function to update pagination buttons
function updatePaginationButtons(currentPage, totalPages) {
    const paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.classList.add('pagination-button');
        if (i === currentPage) {
            button.classList.add('active');
        }
        button.addEventListener('click', () => handlePagination(i));
        paginationContainer.appendChild(button);
    }
}

// Initialize pagination
handlePagination(1);

// Function to show/hide columns
function toggleColumn(columnIndex) {
    const table = document.getElementById('members-table');
    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        const cell = row.cells[columnIndex];
        if (cell) {
            cell.classList.toggle('hidden');
        }
    });
}

// Add event listeners for column toggle checkboxes
document.querySelectorAll('.column-toggle').forEach((checkbox, index) => {
    checkbox.addEventListener('change', () => toggleColumn(index));
});

// Function to handle sorting
function sortTable(columnIndex) {
    const table = document.getElementById('members-table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr:not(.edit-form)'));
    const isAscending = table.querySelector(`th:nth-child(${columnIndex + 1})`).classList.toggle('asc');

    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
    });

    tbody.append(...rows);
}

// Add event listeners for sorting
document.querySelectorAll('#members-table th').forEach((th, index) => {
    th.addEventListener('click', () => sortTable(index));
});

// Function to handle bulk actions
function handleBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const selectedMembers = Array.from(document.querySelectorAll('input[name="selectedMembers"]:checked')).map(cb => cb.value);

    if (selectedMembers.length === 0) {
        alert('Please select at least one member.');
        return;
    }

    const formData = new FormData();
    formData.append('action', action);
    formData.append('members', JSON.stringify(selectedMembers));

    fetch('manage_members.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            fetchClubData(new URLSearchParams(window.location.search).get('id'));
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Add event listener for bulk action button
document.getElementById('applyBulkAction').addEventListener('click', handleBulkAction);

// Function to handle file import
function handleFileImport(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const csv = e.target.result;
        const lines = csv.split('\n');
        const headers = lines[0].split(',');
        const data = [];

        for (let i = 1; i < lines.length; i++) {
            const values = lines[i].split(',');
            if (values.length === headers.length) {
                const entry = {};
                for (let j = 0; j < headers.length; j++) {
                    entry[headers[j].trim()] = values[j].trim();
                }
                data.push(entry);
            }
        }

        importMembers(data);
    };

    reader.readAsText(file);
}

// Function to import members
function importMembers(data) {
    const formData = new FormData();
    formData.append('action', 'import');
    formData.append('data', JSON.stringify(data));

    fetch('manage_members.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Members imported successfully');
            fetchClubData(new URLSearchParams(window.location.search).get('id'));
        } else {
            alert('Error importing members: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while importing members');
    });
}

// Add event listener for file import
document.getElementById('importFile').addEventListener('change', handleFileImport);

// Function to show/hide password
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.getElementById('togglePassword');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.textContent = 'Hide';
    } else {
        passwordInput.type = 'password';
        toggleButton.textContent = 'Show';
    }
}

// Add event listener for password toggle button
document.getElementById('togglePassword').addEventListener('click', togglePasswordVisibility);

// Function to validate form inputs
function validateForm() {
    const form = document.getElementById('memberForm');
    const inputs = form.querySelectorAll('input[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('invalid');
            isValid = false;
        } else {
            input.classList.remove('invalid');
        }
    });

    return isValid;
}

// Add event listener for form submission
document.getElementById('memberForm').addEventListener('submit', function(event) {
    if (!validateForm()) {
        event.preventDefault();
        alert('Please fill in all required fields.');
    }
});

// Function to handle infinite scroll
function handleInfiniteScroll() {
    const tableContainer = document.querySelector('.member-list');
    const table = document.getElementById('members-table');
    const loadingIndicator = document.getElementById('loadingIndicator');

    let page = 1;
    let isLoading = false;

    tableContainer.addEventListener('scroll', () => {
        if (isLoading) return;

        const { scrollTop, scrollHeight, clientHeight } = tableContainer;
        if (scrollTop + clientHeight >= scrollHeight - 5) {
            loadMoreMembers();
        }
    });

    function loadMoreMembers() {
        isLoading = true;
        loadingIndicator.style.display = 'block';

        // Simulating API call with setTimeout
        setTimeout(() => {
            // Fetch more data and append to the table
            // For demonstration, we'll just duplicate existing rows
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr:not(.edit-form)');
            rows.forEach(row => {
                const clone = row.cloneNode(true);
                tbody.appendChild(clone);
            });

            isLoading = false;
            loadingIndicator.style.display = 'none';
            page++;
        }, 1000);
    }
}

// Initialize infinite scroll
handleInfiniteScroll();

// Function to handle drag and drop file upload
function handleDragDrop() {
    const dropZone = document.getElementById('dropZone');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropZone.classList.add('highlight');
    }

    function unhighlight() {
        dropZone.classList.remove('highlight');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        ([...files]).forEach(uploadFile);
    }

    function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('action', 'upload');

        fetch('manage_members.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('File uploaded successfully');
                fetchClubData(new URLSearchParams(window.location.search).get('id'));
            } else {
                alert('Error uploading file: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while uploading the file');
        });
    }
}

// Initialize drag and drop
handleDragDrop();

// Function to handle real-time updates
function setupRealTimeUpdates() {
    const eventSource = new EventSource('manage_members.php?action=stream');

    eventSource.onmessage = function(event) {
        const data = JSON.parse(event.data);
        updateMemberRow(data);
    };

    eventSource.onerror = function(error) {
        console.error('EventSource failed:', error);
        eventSource.close();
    };
}

function updateMemberRow(memberData) {
    const row = document.querySelector(`#members-table tr[data-id="${memberData.id}"]`);
    if (row) {
        row.querySelector('.member-name').textContent = memberData.full_name;
        row.querySelector('.member-email').textContent = memberData.email;
        row.querySelector('.member-phone').textContent = memberData.phone;
        row.querySelector('.member-role').textContent = memberData.role;
        row.querySelector('.member-status').textContent = memberData.status;
    } else {
        // If the row doesn't exist, add a new one
        const tbody = document.querySelector('#members-table tbody');
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-id', memberData.id);
        newRow.innerHTML = `
            <td class="member-name">${memberData.full_name}</td>
            <td class="member-email">${memberData.email}</td>
            <td class="member-phone">${memberData.phone}</td>
            <td class="member-role">${memberData.role}</td>
            <td class="member-status">${memberData.status}</td>
            <td>
                <button onclick="showEditForm(${memberData.id})" class="admin-button">Edit</button>
                <button onclick="approveMember(${memberData.id})" class="admin-button">Approve</button>
                <button onclick="deleteMember(${memberData.id})" class="delete-button">Delete</button>
            </td>
        `;
        tbody.appendChild(newRow);
    }
}

// Initialize real-time updates
setupRealTimeUpdates();

// Function to handle member statistics
function updateMemberStatistics() {
    const totalMembers = document.querySelectorAll('#members-table tbody tr:not(.edit-form)').length;
    const approvedMembers = document.querySelectorAll('#members-table tbody tr:not(.edit-form) td:nth-child(6):contains("approved")').length;
    const pendingMembers = totalMembers - approvedMembers;

    document.getElementById('totalMembers').textContent = totalMembers;
    document.getElementById('approvedMembers').textContent = approvedMembers;
    document.getElementById('pendingMembers').textContent = pendingMembers;

    // Update chart if using a charting library
    // updateMemberChart(approvedMembers, pendingMembers);
}

// Call updateMemberStatistics after fetching data
fetchClubData(new URLSearchParams(window.location.search).get('id')).then(() => {
    updateMemberStatistics();
});

// Function to handle member activity log
function logMemberActivity(memberId, action) {
    const formData = new FormData();
    formData.append('action', 'logActivity');
    formData.append('memberId', memberId);
    formData.append('activityType', action);

    fetch('manage_members.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            console.log('Activity logged successfully');
        } else {
            console.error('Error logging activity:', result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Add activity logging to member actions
function updateMember(event, memberId) {
    // ... existing updateMember code ...
    logMemberActivity(memberId, 'update');
}

function approveMember(memberId) {
    // ... existing approveMember code ...
    logMemberActivity(memberId, 'approve');
}

function deleteMember(memberId) {
    // ... existing deleteMember code ...
    logMemberActivity(memberId, 'delete');
}

// Function to display member activity log
function displayActivityLog() {
    fetch('manage_members.php?action=getActivityLog')
    .then(response => response.json())
    .then(data => {
        const logContainer = document.getElementById('activityLog');
        logContainer.innerHTML = '';
        data.forEach(entry => {
            const logEntry = document.createElement('div');
            logEntry.textContent = `${entry.timestamp}: ${entry.member_name} - ${entry.activity_type}`;
            logContainer.appendChild(logEntry);
        });
    })
    .catch(error => {
        console.error('Error fetching activity log:', error);
    });
}

// Call displayActivityLog periodically or after certain actions
setInterval(displayActivityLog, 60000); // Update every minute

// Function to handle member roles and permissions
function updateMemberPermissions(memberId, role) {
    const formData = new FormData();
    formData.append('action', 'updatePermissions');
    formData.append('memberId', memberId);
    formData.append('role', role);

    fetch('manage_members.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Member permissions updated successfully');
            fetchClubData(new URLSearchParams(window.location.search).get('id'));
        } else {
            alert('Error updating member permissions: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating member permissions');
    });
}

// Add event listener for role change
document.querySelectorAll('.role-select').forEach(select => {
    select.addEventListener('change', (event) => {
        const memberId = event.target.getAttribute('data-member-id');
        const newRole = event.target.value;
        updateMemberPermissions(memberId, newRole);
    });
});

// Function to handle member communication
function sendMemberMessage(memberId, message) {
    const formData = new FormData();
    formData.append('action', 'sendMessage');
    formData.append('memberId', memberId);
    formData.append('message', message);

    fetch('manage_members.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Message sent successfully');
        } else {
            alert('Error sending message: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending the message');
    });
}

// Add event listener for send message form
document.getElementById('sendMessageForm').addEventListener('submit', (event) => {
    event.preventDefault();
    const memberId = document.getElementById('messageRecipient').value;
    const message = document.getElementById('messageContent').value;
    sendMemberMessage(memberId, message);
});

// Function to handle member attendance tracking
function recordAttendance(eventId, presentMembers) {
    const formData = new FormData();
    formData.append('action', 'recordAttendance');
    formData.append('eventId', eventId);
    formData.append('presentMembers', JSON.stringify(presentMembers));

    fetch('manage_members.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Attendance recorded successfully');
        } else {
            alert('Error recording attendance: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while recording attendance');
    });
}

// Add event listener for attendance form
document.getElementById('attendanceForm').addEventListener('submit', (event) => {
    event.preventDefault();
    const eventId = document.getElementById('eventSelect').value;
    const presentMembers = Array.from(document.querySelectorAll('input[name="attendance"]:checked')).map(cb => cb.value);
    recordAttendance(eventId, presentMembers);
});

// Function to generate member reports
function generateMemberReport(reportType) {
    fetch(`manage_members.php?action=generateReport&type=${reportType}`)
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `member_report_${reportType}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    })
    .catch(error => {
        console.error('Error generating report:', error);
        alert('An error occurred while generating the report');
    });
}

// Add event listeners for report generation buttons
document.getElementById('generateAttendanceReport').addEventListener('click', () => generateMemberReport('attendance'));
document.getElementById('generateActivityReport').addEventListener('click', () => generateMemberReport('activity'));

// Function to handle member feedback
function submitMemberFeedback(memberId, feedback) {
    const formData = new FormData();
    formData.append('action', 'submitFeedback');
    formData.append('memberId', memberId);
    formData.append('feedback', feedback);

    fetch('manage_members.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Feedback submitted successfully');
        } else {
            alert('Error submitting feedback: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting feedback');
    });
}

// Add event listener for feedback form
document.getElementById('feedbackForm').addEventListener('submit', (event) => {
    event.preventDefault();
    const memberId = document.getElementById('feedbackMember').value;
    const feedback = document.getElementById('feedbackContent').value;
    submitMemberFeedback(memberId, feedback);
});

// Initialize all components
document.addEventListener('DOMContentLoaded', () => {
    fetchClubData(new URLSearchParams(window.location.search).get('id'));
    updateMemberStatistics();
    displayActivityLog();
    handleInfiniteScroll();
    handleDragDrop();
    setupRealTimeUpdates();
});

