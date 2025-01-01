document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('addEventForm');
    const fileInput = document.getElementById('event_photo');
    const preview = document.createElement('img');
    preview.style.maxWidth = '200px';
    preview.style.marginTop = '10px';
    fileInput.parentElement.appendChild(preview);

    // Image preview functionality
    fileInput.addEventListener('change', function () {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    form.addEventListener('submit', function (e) {
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const dateTime = document.getElementById('date_time').value;
        const maxRegistrations = document.getElementById('max_registrations').value;
        const event_id = document.getElementById('event_id').value;

        const eventPhoto = fileInput.files[0];

        // Validate fields
        if (!title || !description || !dateTime || !eventPhoto || maxRegistrations === '') {
            alert('Please fill in all fields, including selecting a photo.');
            e.preventDefault();
            return;
        }

        if (isNaN(maxRegistrations) || maxRegistrations < 1) {
            alert('Maximum registrations must be a positive number.');
            e.preventDefault();
            return;
        }

        // File size validation (2MB limit)
        if (eventPhoto.size > 2 * 1024 * 1024) {
            alert('The file size should not exceed 2MB.');
            e.preventDefault();
            return;
        }

        // File type validation
        const allowedTypes = ['image/jpeg', 'image/png'];
        if (!allowedTypes.includes(eventPhoto.type)) {
            alert('Please upload a valid image file (JPEG, PNG).');
            e.preventDefault();
            return;
        }

        // Disable submit button to prevent duplicate submissions
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        // alert('Form is ready to be submitted.');
    });
});
