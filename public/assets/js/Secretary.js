document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ Secretary Script Loaded!");

    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.querySelector(".toggle-btn");
    const mainContent = document.querySelector(".main-content");
    const menuItems = document.querySelectorAll(".sidebar ul li a");

    const overlay = document.createElement("div");
    overlay.classList.add("overlay");
    document.body.appendChild(overlay);

    const pageMappings = {
        "dashboard": "secdashboard",
        "events": "event",
        "pending students": "approvestudents",
        "officer registration": "officers",
        "attendance qr code": "attendance-qr",
        "orgstruct": "orgstruct"
    };

    function toggleSidebar(show) {
        if (show === undefined) {
            sidebar.classList.toggle("active");
        } else {
            sidebar.classList.toggle("active", show);
        }
        overlay.style.display = sidebar.classList.contains("active") ? "block" : "none";
    }

    function checkScreenSize() {
        if (window.innerWidth < 768) {
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

    async function loadPage(page) {
        console.log(`📄 Loading ${page}...`);
        try {
            mainContent.innerHTML = `<iframe src="/pages/${page}" class="content-frame"></iframe>`;
        } catch (error) {
            console.error(`❌ Error loading page ${page}:`, error);
        }
    }


    menuItems.forEach(item => {
        item.addEventListener("click", (event) => {
            event.preventDefault();
            const itemText = item.textContent.trim().toLowerCase();
            console.log("📌 Menu Clicked:", itemText);

            if (pageMappings[itemText]) {
                loadPage(pageMappings[itemText]);
            } else {
                console.error("❌ Page mapping not found for:", itemText);
            }

            if (window.innerWidth < 768) {
                toggleSidebar(false);
            }
        });
    });

    window.addEventListener("resize", checkScreenSize);
    checkScreenSize();

    if (!mainContent.innerHTML.trim()) {
        loadPage("orgstruct"); // Remove the leading slash
    }
});
