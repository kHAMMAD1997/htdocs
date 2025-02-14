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
            if (data.status== "success") {
                const permissions = data.data;

                // Show admin features if user is an admin
                if (parseInt(user.is_admin)== 1) {
                    setDisplayByClass("admin-feature", "block");
                }

                // Check and display features based on permissions
                setDisplayByClass("concept-note", permissions.concept_note== "1" ? "" : "none");
                setDisplayByClass("narrative-report", permissions.narrative_report== "1" ? "" : "none");
                setDisplayByClass("grant-application", permissions.grant_application== "1" ? "" : "none");
                setDisplayByClass("short-concept-note", permissions.short_concept_note== "1" ? "" : "none");
            } else {
                console.error("Error fetching permissions:", data.message);
            }
        })
        .catch(error => console.error("Error:", error));
} else {
    console.error("No user information found in cookie");
}


    // Select the logo link using its class
    const logoElement = document.querySelector('.navbar-brand');

    // Apply styles dynamically
    if (logoElement) {
        logoElement.style.display = 'flex';
        logoElement.style.justifyContent = 'center';
        logoElement.style.alignItems = 'center';
        logoElement.style.width = '100%';

        // Optional: Style the logo image directly
        const logoImage = logoElement.querySelector('.light-logo');
        if (logoImage) {
            logoImage.style.maxHeight = '40px';
        }
    }



    document.addEventListener('DOMContentLoaded', function() {
        // Select the target div using its class
        const dropdownMenu = document.querySelector('.dropdown-menu.dropdown-menu-right.user-dd.animated');

        if (dropdownMenu) {
            // Clear all child elements
            dropdownMenu.innerHTML = '';

            // Add the new child element
            dropdownMenu.innerHTML = `
                <a class="dropdown-item" id="logoutLink" href="javascript:void(0)">
                    <i class="fa fa-power-off m-r-5 m-l-5"></i> Logout
                </a>
            `;

            // Function to delete a cookie by name
            function deleteCookie(name) {
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            }

            // Add event listener to the logout link
            const logoutLink = document.getElementById('logoutLink');
            logoutLink.addEventListener('click', function() {
                // Remove the user cookie
                deleteCookie('user');
                
                // Optionally, clear localStorage if user info is stored there
                // localStorage.removeItem('user');

                // Redirect to login page or home page
                window.location.href = '/forms-processing/login.html';
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Select the target <a> element with the specific classes
        const sidebarToggler = document.querySelector('.nav-link.sidebartoggler.waves-effect.waves-light');
    
        // Check if the element exists, then set its visibility to hidden
        if (sidebarToggler) {
            sidebarToggler.style.visibility = 'hidden';
        }
    });