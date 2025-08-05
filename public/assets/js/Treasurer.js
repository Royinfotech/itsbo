document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ Treasurer Script Loaded!");

    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.querySelector(".toggle-btn");
    const mainContent = document.querySelector(".main-content");
    const menuItems = document.querySelectorAll(".sidebar ul li a");

    const overlay = document.createElement("div");
    overlay.classList.add("overlay");
    document.body.appendChild(overlay);

    function toggleSidebar(show) {
        if (show === undefined) {
            sidebar.classList.toggle("active");
        } else {
            sidebar.classList.toggle("active", show);
        }
        overlay.style.display = sidebar.classList.contains("active") ? "block" : "none";
    }

    function checkScreenSize() {
        if (window.innerWidth < 900) {
            toggleSidebar(false);
        }
    }

    toggleBtn.addEventListener("click", function (event) {
        event.stopPropagation();
        toggleSidebar();
    });

    document.addEventListener("click", function (event) {
        if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
            toggleSidebar(false);
        }
    });

    overlay.addEventListener("click", function () {
        toggleSidebar(false);
    });

    // Handle Menu Clicks
    menuItems.forEach(item => {
        item.addEventListener("click", (event) => {
            event.preventDefault();
            const url = item.getAttribute('href');
            
            // Create and set up iframe
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.className = 'content-frame';
            
            // Clear main content and add iframe
            mainContent.innerHTML = '';
            mainContent.appendChild(iframe);

            // Auto-hide sidebar for mobile
            if (window.innerWidth < 900) {
                toggleSidebar(false);
            }
        });
    });

    window.addEventListener("resize", checkScreenSize);
    checkScreenSize();

    // Load default page if no content is present
    if (!mainContent.innerHTML.trim()) {
        const defaultUrl = document.querySelector('#itsboOfficers').getAttribute('href');
        const iframe = document.createElement('iframe');
        iframe.src = defaultUrl;
        iframe.className = 'content-frame';
        mainContent.appendChild(iframe);
    }
});
