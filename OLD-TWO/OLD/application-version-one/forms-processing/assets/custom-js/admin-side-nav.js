document.addEventListener("DOMContentLoaded", function () {
    // Get the sidebar navigation element
    const sidebarNav = document.getElementById("sidebarnav");

    // Check if the element exists
    if (sidebarNav) {
        // Clear all existing <li> elements
        sidebarNav.innerHTML = "";

        // Create the new sidebar items
        const newItems = [
            {
                href: "admin-dashboard.html",
                icon: "mdi mdi-view-dashboard",
                text: "Dashboard",
            },
            {
                href: "admin-short-concept-note-list-dash.html",
                icon: "mdi mdi-chart-pie",
                text: "Short Concept Notes",
            },
            {
                href: "admin-concept-note-list-dash.html",
                icon: "mdi mdi-chart-bar",
                text: "Concept Notes",
            },
            {
                href: "admin-grant-application-list-dash.html",
                icon: "mdi mdi-border-inside",
                text: "Grant Applications",
            },
            {
                href: "admin-create-user.html",
                icon: "mdi mdi-face",
                text: "Users",
            },
            {
                href: "admin-service-agreement-list-dash.html",
                icon: "mdi mdi-receipt",
                text: "Service Agreements",
            },
        ];

        // Loop through the items and append them to the sidebar
        newItems.forEach(item => {
            const li = document.createElement("li");
            li.classList.add("sidebar-item");

            const a = document.createElement("a");
            a.classList.add("sidebar-link", "waves-effect", "waves-dark", "sidebar-link");
            a.href = item.href;
            a.setAttribute("aria-expanded", "false");

            const i = document.createElement("i");
            i.className = item.icon;

            const span = document.createElement("span");
            span.classList.add("hide-menu");
            span.textContent = item.text;

            a.appendChild(i);
            a.appendChild(span);
            li.appendChild(a);

            sidebarNav.appendChild(li);
        });
    } else {
        console.error("Sidebar navigation element with id 'sidebarnav' does not exist.");
    }
});
