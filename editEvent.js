document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editEventForm');
    const fileInput = document.getElementById('photo_url');
    const imagePreview = document.getElementById('uploadIcon');
    const uploadArea = document.getElementById('uploadArea');

    // Open file input when upload area is clicked
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // Handle drag over event
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    // Handle drag leave event
    uploadArea.addEventListener('dragleave', function() {
        uploadArea.classList.remove('dragover');
    });

    // Handle file drop event
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
        handleFileSelect(e.dataTransfer.files[0]);
    });

    // Handle file selection via file input
    fileInput.addEventListener('change', function() {
        handleFileSelect(this.files[0]);
    });

    function handleFileSelect(file) {
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width: 200px; height: 100px;">`;
            };
            reader.readAsDataURL(file);
        }
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Client-side validation can go here if needed

        // If validation passes, submit the form
        this.submit();
    });
});
