document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ Student Script Loaded!");

    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.querySelector(".toggle-btn");
    const mainContent = document.querySelector(".main-content");
    const menuItems = document.querySelectorAll(".sidebar ul li a");

    // Page route mappings (admin-style)
    const pageMappings = {
        "announcement": "/student/announcement",
        "profile": () => `/student/profile/${getStudentId()}`,
        "qrcode": () => `/student/qrcode/${getStudentId()}`,
        "attendancerecord": () => `/student/attendance/${getStudentId()}`,
        "accounts": () => `/student/accounts/${getStudentId()}`,
        "orgstruct": "/student/orgstruct"
    };

    // Get student ID from <body data-student-id="">
    function getStudentId() {
        const studentId = document.body.getAttribute('data-student-id');
        if (!studentId) {
            console.error('❌ Student ID not found in body');
            throw new Error('Student ID not found');
        }
        return studentId;
    }

    // Load content via iframe (admin-style)
    function loadPage(pageId) {
        const page = pageMappings[pageId];
        if (!page) {
            console.error("❌ Page mapping not found for:", pageId);
            return;
        }

        const resolvedUrl = typeof page === "function" ? page() : page;
        console.log(`📄 Loading: ${resolvedUrl}`);

        mainContent.innerHTML = `<iframe src="${resolvedUrl}" class="content-frame" style="width: 100%; height: 100vh; border: none;"></iframe>`;
    }

    // Sidebar toggle
    function toggleSidebar(show) {
        const isActive = sidebar.classList.contains("active");
        const shouldShow = show !== undefined ? show : !isActive;

        sidebar.classList.toggle("active", shouldShow);
        mainContent.classList.toggle("shifted", shouldShow);
    }

    // Collapse sidebar on small screens
    function checkScreenSize() {
        if (window.innerWidth < 900) {
            toggleSidebar(false);
        }
    }

    // Handle sidebar clicks
    menuItems.forEach(item => {
        item.addEventListener("click", (event) => {
            event.preventDefault();

            const itemId = item.getAttribute("id");
            if (!itemId) return;

            // Remove active class from all items
            menuItems.forEach(link => link.classList.remove("active"));
            item.classList.add("active");

            loadPage(itemId);

            // Auto close sidebar on mobile
            if (window.innerWidth < 900) {
                toggleSidebar(false);
            }
        });
    });

    // Sidebar toggle button
    toggleBtn?.addEventListener("click", () => toggleSidebar());

    // Responsive handling
    window.addEventListener("resize", checkScreenSize);
    checkScreenSize();

    // Always load default page on initial load
loadPage("announcement");


    console.log("🚀 Student Script Initialization Complete!");
});
