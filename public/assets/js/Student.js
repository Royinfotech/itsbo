document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ Student Script Loaded!");

    // DOM Elements
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.querySelector(".toggle-btn");
    const mainContent = document.querySelector(".main-content");
    const menuItems = document.querySelectorAll(".sidebar ul li a");
    const fbFeed = document.getElementById("fb-feed");

    // Create overlay for mobile sidebar
    const overlay = document.createElement("div");
    overlay.classList.add("overlay");
    document.body.appendChild(overlay);

    // Configuration
    const MOBILE_BREAKPOINT = 900;
    const LOADING_TIMEOUT = 30000; // 30 seconds

    // Utility Functions
    function getStudentId() {
        const studentId = document.body.getAttribute('data-student-id');
        if (!studentId) {
            console.error('❌ Student ID not found in body data attribute');
            throw new Error('Student ID not found');
        }
        return studentId;
    }

    function showLoadingState(message = 'Loading...') {
        mainContent.innerHTML = `
            <div class="loading-container" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 300px;
                color: #6c757d;
            ">
                <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom: 15px;"></i>
                <p>${message}</p>
            </div>`;
    }

    function showErrorState(message, retryAction = null) {
        const retryButton = retryAction ? `
            <button class="refresh-btn" onclick="${retryAction}" style="
                background: #800000;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                margin-top: 15px;
            ">
                <i class="fas fa-sync"></i> Retry
            </button>` : '';

        mainContent.innerHTML = `
            <div class="error-container" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 300px;
                text-align: center;
            ">
                <div class="error-message" style="
                    color: #721c24;
                    background: #f8d7da;
                    padding: 20px;
                    border-radius: 8px;
                    border: 1px solid #f5c6cb;
                    max-width: 500px;
                ">
                    <i class="fas fa-exclamation-circle" style="margin-right: 10px;"></i>
                    ${message}
                </div>
                ${retryButton}
            </div>`;
    }

    // Sidebar Functions
    function toggleSidebar(show) {
        if (show === undefined) {
            sidebar.classList.toggle("active");
        } else {
            sidebar.classList.toggle("active", show);
        }
        overlay.style.display = sidebar.classList.contains("active") ? "block" : "none";
    }

    function checkScreenSize() {
        if (window.innerWidth < MOBILE_BREAKPOINT) {
            toggleSidebar(false);
        }
    }

    // Page Loading Functions
    async function fetchWithTimeout(url, options = {}, timeout = LOADING_TIMEOUT) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), timeout);
        
        try {
            const response = await fetch(url, {
                ...options,
                signal: controller.signal
            });
            clearTimeout(timeoutId);
            return response;
        } catch (error) {
            clearTimeout(timeoutId);
            if (error.name === 'AbortError') {
                throw new Error('Request timed out. Please check your connection and try again.');
            }
            throw error;
        }
    }

    async function loadAnnouncement() {
        console.log('📢 Loading announcements...');
        showLoadingState('Loading announcements...');
        
        try {
            const response = await fetchWithTimeout('/student/announcement');
            if (!response.ok) {
                throw new Error(`Server error: ${response.status} ${response.statusText}`);
            }
            
            const content = await response.text();
            mainContent.innerHTML = content;
            
            // Reinitialize Facebook SDK if present
            if (window.FB) {
                FB.XFBML.parse();
            }
            console.log('✅ Announcements loaded successfully');
        } catch (error) {
            console.error('❌ Failed to load announcements:', error);
            showErrorState(`Failed to load announcements: ${error.message}`, 'loadPage("announcement")');
        }
    }

    async function loadProfile() {
        console.log('👤 Loading profile...');
        showLoadingState('Loading profile...');
        
        try {
            const studentId = getStudentId();
            const response = await fetchWithTimeout(`/student/profile/${studentId}`);
            
            if (!response.ok) {
                throw new Error(`Server error: ${response.status} ${response.statusText}`);
            }
            
            const content = await response.text();
            mainContent.innerHTML = content;
            console.log('✅ Profile loaded successfully');
        } catch (error) {
            console.error('❌ Failed to load profile:', error);
            showErrorState(`Failed to load profile: ${error.message}`, 'loadPage("profile")');
        }
    }

    async function loadQRCode() {
        console.log('📱 Loading QR code...');
        showLoadingState('Generating QR code...');
        
        try {
            const studentId = getStudentId();
            const response = await fetchWithTimeout(`/student/qrcode/${studentId}`);
            
            if (!response.ok) {
                throw new Error(`Server error: ${response.status} ${response.statusText}`);
            }
            
            const content = await response.text();
            mainContent.innerHTML = content;
            
            // Initialize QR code functionality
            await initQRCode(studentId);
            console.log('✅ QR code loaded successfully');
        } catch (error) {
            console.error('❌ Failed to load QR code:', error);
            showErrorState(`Failed to load QR code: ${error.message}`, 'loadPage("qrcode")');
        }
    }

    async function loadAttendanceRecord() {
        console.log('📊 Loading attendance record...');
        showLoadingState('Loading attendance records...');
        
        try {
            const studentId = getStudentId();
            const response = await fetchWithTimeout(`/student/attendance/${studentId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`Server error: ${response.status} ${response.statusText}`);
            }

            const content = await response.text();
            mainContent.innerHTML = content;
            console.log('✅ Attendance record loaded successfully');
        } catch (error) {
            console.error('❌ Failed to load attendance record:', error);
            showErrorState(`Failed to load attendance record: ${error.message}`, 'loadPage("attendancerecord")');
        }
    }

    async function loadGenericPage(page) {
        console.log(`📄 Loading ${page}...`);
        showLoadingState(`Loading ${page}...`);
        
        try {
            const response = await fetchWithTimeout(`/pages/${page}`);
            if (!response.ok) {
                throw new Error(`Server error: ${response.status} ${response.statusText}`);
            }
            
            mainContent.innerHTML = `<iframe src="/pages/${page}" class="content-frame" style="width: 100%; height: 80vh; border: none;"></iframe>`;
            console.log(`✅ ${page} loaded successfully`);
        } catch (error) {
            console.error(`❌ Failed to load ${page}:`, error);
            showErrorState(`Failed to load ${page}: ${error.message}`, `loadPage("${page}")`);
        }
    }

    // Main page loading function
    async function loadPage(page) {
        console.log(`📄 Loading page: ${page}`);
        
        try {
            switch (page) {
                case 'announcement':
                    await loadAnnouncement();
                    break;
                case 'profile':
                    await loadProfile();
                    break;
                case 'qrcode':
                    await loadQRCode();
                    break;
                case 'attendancerecord':
                    await loadAttendanceRecord();
                    break;
                default:
                    await loadGenericPage(page);
                    break;
            }
        } catch (error) {
            console.error(`❌ Unexpected error loading ${page}:`, error);
            showErrorState(`An unexpected error occurred: ${error.message}`, `loadPage("${page}")`);
        }
    }

    // QR Code Functions
    async function refreshQRCode(studentId) {
        console.log('🔄 Refreshing QR code...');
        try {
            const response = await fetchWithTimeout(`/student/qrcode/refresh/${studentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`Server error: ${response.status} ${response.statusText}`);
            }
            
            const data = await response.json();
            if (data.success) {
                await loadPage('qrcode');
                console.log('✅ QR code refreshed successfully');
            } else {
                throw new Error(data.message || 'Failed to refresh QR code');
            }
        } catch (error) {
            console.error('❌ Failed to refresh QR code:', error);
            alert(`Failed to refresh QR code: ${error.message}`);
            throw error;
        }
    }

    function downloadQRCode() {
        console.log('💾 Downloading QR code...');
        const svg = document.querySelector('.qr-code svg');
        if (!svg) {
            console.error('❌ QR code SVG not found');
            alert('QR code not found. Please refresh the page and try again.');
            return;
        }

        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const data = new XMLSerializer().serializeToString(svg);
            const DOMURL = window.URL || window.webkitURL || window;
            const img = new Image();
            const svgBlob = new Blob([data], { type: 'image/svg+xml;charset=utf-8' });
            const url = DOMURL.createObjectURL(svgBlob);

            img.onload = function () {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);
                DOMURL.revokeObjectURL(url);

                const imgURI = canvas.toDataURL('image/png');
                const downloadLink = document.createElement('a');
                const studentId = getStudentId();
                downloadLink.download = `qrcode_student_${studentId}_${new Date().toISOString().split('T')[0]}.png`;
                downloadLink.href = imgURI;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
                
                console.log('✅ QR code downloaded successfully');
            };

            img.onerror = function () {
                console.error('❌ Failed to load QR code image');
                alert('Failed to download QR code. Please try again.');
                DOMURL.revokeObjectURL(url);
            };

            img.src = url;
        } catch (error) {
            console.error('❌ Failed to download QR code:', error);
            alert(`Failed to download QR code: ${error.message}`);
        }
    }

    async function initQRCode(studentId) {
        console.log('🔄 Initializing QR code functionality...');
        
        try {
            // Wait a bit for DOM to be ready
            await new Promise(resolve => setTimeout(resolve, 100));
            
            const qrContainer = document.querySelector('.qr-container');
            if (!qrContainer) {
                throw new Error('QR container not found');
            }

            // Initialize download button
            const downloadBtn = qrContainer.querySelector('.download-btn');
            if (downloadBtn) {
                downloadBtn.removeEventListener('click', downloadQRCode); // Remove existing listener
                downloadBtn.addEventListener('click', downloadQRCode);
                console.log('✅ Download button initialized');
            }

            // Initialize refresh button
            const refreshBtn = qrContainer.querySelector('.refresh-btn');
            if (refreshBtn) {
                refreshBtn.removeEventListener('click', handleRefreshClick); // Remove existing listener
                refreshBtn.addEventListener('click', handleRefreshClick);
                console.log('✅ Refresh button initialized');
            }

            async function handleRefreshClick() {
                try {
                    refreshBtn.disabled = true;
                    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
                    await refreshQRCode(studentId);
                } catch (error) {
                    console.error('❌ Failed to refresh QR code:', error);
                } finally {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = '<i class="fas fa-sync"></i> Refresh QR Code';
                }
            }

            console.log('✅ QR code functionality initialized');
        } catch (error) {
            console.error('❌ Failed to initialize QR code:', error);
            throw error;
        }
    }

    // Facebook SDK Functions
    function loadFacebookSDK() {
        if (window.FB) {
            console.log("ℹ️ Facebook SDK already loaded.");
            return;
        }

        const fbScript = document.createElement("script");
        fbScript.async = true;
        fbScript.defer = true;
        fbScript.crossOrigin = "anonymous";
        fbScript.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v17.0";
        fbScript.onload = function () {
            console.log("📢 Facebook SDK Loaded!");
            const loadingText = document.querySelector('.fb-loading');
            if (loadingText) loadingText.style.display = "none";
            if (fbFeed) fbFeed.style.display = "block";
            adjustFbFeed();
        };
        fbScript.onerror = function () {
            console.warn("⚠️ Facebook SDK failed to load");
        };
        document.body.appendChild(fbScript);
    }

    function adjustFbFeed() {
        const fbPage = document.querySelector(".fb-page");
        if (fbPage && window.FB && fbFeed) {
            fbPage.setAttribute("data-width", fbFeed.clientWidth);
            FB.XFBML.parse();
        }
    }

    // Event Listeners
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function (event) {
            event.stopPropagation();
            toggleSidebar();
        });
    }

    document.addEventListener("click", function (event) {
        if (sidebar && !sidebar.contains(event.target) && 
            toggleBtn && !toggleBtn.contains(event.target)) {
            toggleSidebar(false);
        }
    });

    overlay.addEventListener("click", function () {
        toggleSidebar(false);
    });

    // Page mappings
    const pageMappings = {
        "announcement": "announcement",
        "profile": "profile", 
        "qrcode": "qrcode",
        "attendance": "attendancerecord",
        "accounts": "accounts",
        "orgstruct": "orgstruct"
    };

    // Menu item event listeners
    menuItems.forEach(item => {
        item.addEventListener("click", (event) => {
            event.preventDefault();
            const page = item.getAttribute('data-page');
            if (!page) {
                console.error("❌ No data-page attribute found");
                return;
            }

            const normalizedPage = page.toLowerCase();
            console.log("📌 Menu Clicked:", normalizedPage);

            // Remove active class from all menu items
            menuItems.forEach(menuItem => menuItem.classList.remove('active'));
            // Add active class to clicked item
            item.classList.add('active');

            if (pageMappings[normalizedPage]) {
                loadPage(pageMappings[normalizedPage]);
            } else {
                console.error("❌ Page mapping not found for:", normalizedPage);
                showErrorState(`Page "${normalizedPage}" not found.`);
            }

            if (window.innerWidth < MOBILE_BREAKPOINT) {
                toggleSidebar(false);
            }
        });
    });

    // Window event listeners
    window.addEventListener("resize", () => {
        checkScreenSize();
        adjustFbFeed();
    });

    // Global error handler
    window.addEventListener('error', function(event) {
        console.error('❌ Global error:', event.error);
    });

    window.addEventListener('unhandledrejection', function(event) {
        console.error('❌ Unhandled promise rejection:', event.reason);
    });

    // Expose functions globally for HTML onclick handlers
    window.loadPage = loadPage;
    window.refreshQRCode = refreshQRCode;
    window.downloadQRCode = downloadQRCode;

    // Initialize
    checkScreenSize();
    loadFacebookSDK();

    // Load default page
    if (!mainContent || !mainContent.innerHTML.trim()) {
        loadPage("announcement");
    }

    console.log("🚀 Student Script Initialization Complete!");
});