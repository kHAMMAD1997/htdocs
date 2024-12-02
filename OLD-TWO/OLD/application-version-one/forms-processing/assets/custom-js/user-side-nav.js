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
                href: "user-dashboard.html",
                icon: "mdi mdi-view-dashboard",
                text: "Dashboard",
            },
            {
                href: "user-short-concept-note-list-dash.html",
                icon: "mdi mdi-chart-pie short-concept-note",
                text: "Short Concept Notes",
                additionalClasses: ["short-concept-note"], // Optional classes for this item
            },
            {
                href: "user-concept-note-list-dash.html",
                icon: "mdi mdi-chart-bar concept-note",
                text: "Concept Notes",
                additionalClasses: ["concept-note"], // Optional classes for this item
            },
            {
                href: "user-grant-application-list-dash.html",
                icon: "mdi mdi-border-inside grant-application",
                text: "Grant Applications",
                additionalClasses: ["grant-application"], // Optional classes for this item
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

            // Add any additional classes if specified
            if (item.additionalClasses) {
                item.additionalClasses.forEach(className => {
                    span.classList.add(className);
                });
            }

            a.appendChild(i);
            a.appendChild(span);
            li.appendChild(a);

            sidebarNav.appendChild(li);
        });
    } else {
        console.error("Sidebar navigation element with id 'sidebarnav' does not exist.");
    }
});
