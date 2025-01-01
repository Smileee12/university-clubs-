document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editClubForm');
    const fileInput = document.getElementById('clubImage');
    const imagePreview = document.getElementById('uploadIcon');
    const uploadArea = document.getElementById('uploadArea');

    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function() {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
        handleFileSelect(e.dataTransfer.files[0]);
    });

    fileInput.addEventListener('change', function() {
        handleFileSelect(this.files[0]);
    });

    function handleFileSelect(file) {
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        }
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // You can add client-side validation here if needed

        // If validation passes, submit the form
        this.submit();
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const profileIcon = document.getElementById("profileIcon");
    const userDropdown = document.getElementById("userDropdown");
    const userInfo = document.getElementById("userInfo");
    const logoutButton = document.getElementById("logoutButton");
    const welcomeMessage = document.getElementById("welcomeMessage");
  
    // Get user data from localStorage
    const user = JSON.parse(localStorage.getItem("user"));
  
    if (user) {
      // Update user info in the dropdown
      userInfo.innerHTML = `
        <p><strong>Name:</strong> ${user.full_name || "N/A"}</p>
        <p><strong>Email:</strong> ${user.email || "N/A"}</p>
        <p><strong>User Type:</strong> ${user.user_type || "N/A"}</p>
      `;
  
      // Personalize the welcome message
      welcomeMessage.textContent = `Welcome, ${user.full_name}!`;
    } else {
      // Redirect to login page if user is not logged in
      alert("Please log in to access this page.");
      window.location.href = "LogInPage.html";
    }
  
    // Toggle dropdown visibility
    profileIcon.addEventListener("click", function (e) {
      e.stopPropagation();
      userDropdown.style.display =
        userDropdown.style.display === "block" ? "none" : "block";
    });
  
    // Close dropdown when clicking outside
    document.addEventListener("click", function () {
      userDropdown.style.display = "none";
    });
  
    // Prevent dropdown from closing when clicking inside it
    userDropdown.addEventListener("click", function (e) {
      e.stopPropagation();
    });
  


  
    
  });
  
