document.addEventListener("DOMContentLoaded", function () {
    const sidebarNav = document.querySelector(".sidebar-nav");

    if (sidebarNav) {
        sidebarNav.innerHTML = " ";
        sidebarNav.innerHTML = `
            <ul id="sidebarnav" class="p-t-30">
                        <li class="sidebar-item text-center" style="margin-bottom: 15px;">
                            <a href="admin-dashboard.html" class="btn btn-info btn-lg w-100" style="background-color: #00984a !important; color: white; font-weight: bold;">
                                <i class="mdi mdi-view-dashboard me-2" style="color: white !important;"></i> Dashboard
                            </a>
                        </li>
                        <li class="sidebar-item text-center" style="margin-bottom: 15px;">
                            <a href="admin-short-concept-note-list-dash.html" class="btn btn-info btn-lg w-100" style="background-color: #00984a !important; color: white; font-weight: bold;">
                                <i class="mdi mdi-chart-pie me-2" style="color: white !important;"></i> Short Concept Notes
                            </a>
                        </li>
                        <li class="sidebar-item text-center" style="margin-bottom: 15px;">
                            <a href="admin-concept-note-list-dash.html" class="btn btn-info btn-lg w-100" style="background-color: #00984a !important; color: white; font-weight: bold;">
                                <i class="mdi mdi-chart-bar me-2" style="color: white !important;"></i> Concept Notes
                            </a>
                        </li>
                        <li class="sidebar-item text-center" style="margin-bottom: 15px;">
                            <a href="admin-grant-application-list-dash.html" class="btn btn-info btn-lg w-100" style="background-color: #00984a !important; color: white; font-weight: bold;">
                                <i class="mdi mdi-border-inside me-2" style="color: white !important;"></i> Grant Applications
                            </a>
                        </li>
                        <li class="sidebar-item text-center" style="margin-bottom: 15px;">
                            <a href="admin-service-agreement-list-dash.html" class="btn btn-info btn-lg w-100" style="background-color: #00984a !important; color: white; font-weight: bold;">
                                <i class="mdi mdi-receipt me-2" style="color: white !important;"></i> Service Agreements
                            </a>
                        </li>

                        <li class="sidebar-item text-center">
                        <a href="admin-create-user.html" class="btn btn-info btn-lg w-100" style="background-color: #00984a !important; color: white; font-weight: bold;">
                                <i class="mdi mdi-face me-2" style="color: white !important;"></i> Users
                            </a>

                        </li>
                        
                        
                    </ul>
        `;
    } else {
        console.error("Sidebar navigation element not found.");
    }
});


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






document.addEventListener("DOMContentLoaded", function() {
    // Create a style element
    const style = document.createElement("style");
    style.type = "text/css";
    
    // Add the CSS rules
    style.innerHTML = `
        #navbarSupportedContent {
            background-color: #00984a !important;
        }
        #sidebarnav, .navbar-header, .scroll-sidebar, .sidebar, aside {
            background-color: white !important;
        }
    `;
    
    // Append the style element to the document head
    document.head.appendChild(style);
});

document.addEventListener("DOMContentLoaded", function() {
    // Select the breadcrumb <ol> element
    const breadcrumb = document.querySelector(".breadcrumb");

    if (breadcrumb) {
        // Clear all existing <li> items
        breadcrumb.innerHTML = "";

        // Create the new <li> element
        const newLi = document.createElement("li");
        newLi.className = "breadcrumb-item";

        // Create the new <a> element
        const newLink = document.createElement("a");
        newLink.href = "admin-dashboard.html";
        newLink.className = "btn btn-info";
        newLink.style.backgroundColor = "#36a9e1";
        newLink.style.color = "white";
        newLink.style.fontWeight = "bold";
        newLink.textContent = "Home";

        // Append the <a> to the <li>
        newLi.appendChild(newLink);

        // Append the new <li> to the breadcrumb <ol>
        breadcrumb.appendChild(newLi);
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