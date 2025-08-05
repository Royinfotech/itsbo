document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ Script Loaded!");

    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.querySelector(".toggle-btn");
    const mainContent = document.querySelector(".main-content");
    const menuItems = document.querySelectorAll(".sidebar ul li a");

    // Updated route mappings based on your Laravel routes
    const pageMappings = {
        "itsboOfficers": "/pages/itsboOfficers",
        "manageUsers": "/admin-manage",
        "Dashboard": "/pages/secdashboard",
        "events": "/pages/events" // Load Calendar of Activities in Events
    };

    function toggleSidebar(show) {
        const isActive = sidebar.classList.contains("active");
        const shouldShow = show !== undefined ? show : !isActive;

        sidebar.classList.toggle("active", shouldShow);
        mainContent.classList.toggle("shifted", shouldShow);
    }

    function checkScreenSize() {
        if (window.innerWidth < 768) {
            toggleSidebar(false);
        }
    }

    function loadPage(page) {
        console.log(`📄 Loading ${page}...`);
        mainContent.innerHTML = `<iframe src="${page}" class="content-frame"></iframe>`;
    }

    menuItems.forEach(item => {
        item.addEventListener("click", (event) => {
            event.preventDefault();
            const itemId = item.getAttribute("id");

            if (pageMappings[itemId]) {
                loadPage(pageMappings[itemId]);
            } else {
                console.error("❌ Page mapping not found for:", itemId);
            }

            if (window.innerWidth < 768) {
                toggleSidebar(false);
            }
        });
    });

    toggleBtn?.addEventListener("click", () => toggleSidebar());
    window.addEventListener("resize", checkScreenSize);

    checkScreenSize();
    if (!mainContent.innerHTML.trim()) {
        loadPage("/pages/orgstruct"); // Default to ITSBO Org Structure
    }
});
