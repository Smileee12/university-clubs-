
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
  
 // Logout functionality
logoutButton.addEventListener("click", function (e) {
    e.preventDefault();
    console.log("Logout button clicked"); // Debugging log
    localStorage.removeItem("user");
    console.log("User data removed from localStorage"); // Debugging log
    window.location.href = "LogInPage.html";
});


  
    
  });