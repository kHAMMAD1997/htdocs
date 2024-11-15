
// Function to retrieve user data from cookie
function getUserFromCookie() {
    const cookies = document.cookie.split("; ");
    const userCookie = cookies.find(cookie => cookie.startsWith("user="));
    if (userCookie) {
        const userData = JSON.parse(decodeURIComponent(userCookie.split("=")[1]));
        return userData;
    }
    return null;
}

// Function to set display for elements by class name
function setDisplayByClass(className, displayStyle) {
    const elements = document.querySelectorAll(`.${className}`);
    elements.forEach(element => {
        element.style.display = displayStyle;
    });
}

// Get user data from cookie
const user = getUserFromCookie();
if (user) {
    // Fetch permissions using user_id
    fetch(`/forms-processing/api/permissions-api.php?user_id=${user.user_id}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                const permissions = data.data;

                // Show admin features if user is an admin
                if (parseInt(user.is_admin) === 1) {
                    setDisplayByClass("admin-feature", "block");
                }

                // Check and display features based on permissions
                setDisplayByClass("concept-note", permissions.concept_note === "1" ? "" : "none");
                setDisplayByClass("narrative-report", permissions.narrative_report === "1" ? "" : "none");
                setDisplayByClass("grant-application", permissions.grant_application === "1" ? "" : "none");
            } else {
                console.error("Error fetching permissions:", data.message);
            }
        })
        .catch(error => console.error("Error:", error));
} else {
    console.error("No user information found in cookie");
}
