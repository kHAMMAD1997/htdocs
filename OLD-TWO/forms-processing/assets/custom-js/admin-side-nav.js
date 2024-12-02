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
                            <a href="admin-create-user.html" class="btn btn-info btn-lg w-100" style="background-color: #00984a !important; color: white; font-weight: bold;">
                                <i class="mdi mdi-face me-2" style="color: white !important;"></i> Users
                            </a>
                        </li>
                        <li class="sidebar-item text-center">
                            <a href="admin-service-agreement-list-dash.html" class="btn btn-info btn-lg w-100" style="background-color: #00984a !important; color: white; font-weight: bold;">
                                <i class="mdi mdi-receipt me-2" style="color: white !important;"></i> Service Agreements
                            </a>
                        </li>
                        
                        
                    </ul>
        `;
    } else {
        console.error("Sidebar navigation element not found.");
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


