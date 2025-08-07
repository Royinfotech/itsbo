<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SecretaryController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SecretaryDashboardController;
use App\Http\Controllers\OrgController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\TreasurerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AttendanceQRController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\DashboardTreasurerController;
use App\Http\Controllers\PaymentTransactionReportController;
use App\Http\Controllers\SuperAdminController;

// Guest Routes (Public)
Route::middleware(['guest'])->group(function () {
    // Landing Page
    Route::get('/', [LandingPageController::class, 'index'])->name('landing.page');
    
    // Authentication Routes
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    
    // Registration Routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

// Authentication Common Routes
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::get('/admin', [AdminController::class, 'admin'])->name('admin.dashboard'); 
Route::get('/admin-manage', [AdminController::class, 'admin_manage'])->name('admin.manage'); 
Route::get('/pages/orgstruct', [AdminController::class, 'orgstruct'])->name('admin.orgstruct');
Route::get('/pages/events', [AdminController::class, 'events'])->name('admin.events');
Route::get('/pages/secdashboard', [AdminController::class, 'secdashboard'])->name('admin.secdashboard'); 
Route::get('/pages/itsboOfficers', [AdminController::class, 'orgstruct'])->name('admin.itsboOfficers'); 
Route::post('/admin/add-user', [AdminController::class, 'addUser'])->name('admin.addUser');
Route::get('/admin/users', [AdminController::class, 'getUsers'])->name('admin.getUsers');
Route::get('/admin/user/{id}', [AdminController::class, 'getUser'])->name('admin.getUser');
// Add this route to your routes file
Route::get('/admin/search-users', [AdminController::class, 'searchUsers'])->name('admin.search-users');
Route::post('/admin/update-user-status', [AdminController::class, 'updateUserStatus'])->name('admin.updateUserStatus');
 // Officer Management Routes for Admin
 Route::get('/admin/officers', [OfficerController::class, 'index'])->name('admin.officers.index');
 Route::post('/admin/officers', [OfficerController::class, 'store'])->name('admin.officers.store');
 Route::put('/admin/officers/{id}', [OfficerController::class, 'update'])->name('admin.officers.update');
 Route::get('/admin/officers/{id}', [OfficerController::class, 'show'])->name('admin.officers.show');

// Authentication Routes
Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
Route::post('/login', [AdminController::class, 'login'])->name('login.post');
Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

// Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
  
    Route::get('/get-students', [AttendanceQRController::class, 'getStudents']);
    Route::post('/save-attendance', [AttendanceQRController::class, 'saveAttendance']);
    Route::get('/get-attendance', [AttendanceQRController::class, 'getAttendance']);
    Route::get('/get-students-with-attendance', [AttendanceQRController::class, 'getStudentsWithAttendance']);
    Route::post('/update-attendance', [AttendanceQRController::class, 'updateAttendance']);
    Route::post('/finalize-attendance', [AttendanceQRController::class, 'finalizeAttendance']);
    Route::get('/print-attendance-report', [AttendanceQRController::class, 'printAttendanceReport']);
    Route::get('/get-attendance-logs', [AttendanceQRController::class, 'getAttendanceLogs']);

    Route::get('/pages/attendance-qr', [AttendanceQRController::class, 'index'])->name('secretary.attendanceqr');

    Route::get('/secretary/dashboard/events', [SecretaryDashboardController::class, 'getEvents']);
    Route::get('/secretary/dashboard/student-count', [SecretaryDashboardController::class, 'getStudentCount']);
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/check-conflict', [EventController::class, 'checkConflict']);
    Route::get('/events/check-duplicate', [EventController::class, 'checkDuplicate']);
    Route::post('/events/update', [EventController::class, 'update']);
    Route::get('/event/{id}', [EventController::class, 'getEventDetails'])->name('event.details');

    Route::middleware(['web'])->group(function () {
    Route::post('/save-attendance', [AttendanceQRController::class, 'saveAttendance'])->name('attendance.save');
    });

    Route::get('/org-structure', [OrgController::class, 'index'])->name('org.structure');

    Route::get('/secretary', [SecretaryController::class, 'secretary'])->name('secretary.dashboard');
    Route::get('/pages/event', [SecretaryController::class, 'events'])->name('secretary.event');
    Route::get('/pages/files', [SecretaryController::class, 'files'])->name('secretary.files');
    Route::get('/pages/orgstruct', [SecretaryController::class, 'orgstruct'])->name('secretary.orgstruct');
    Route::get('/pages/approvestudents', [App\Http\Controllers\SecretaryController::class, 'pendingStudents'])->name('secretary.approvestudents');
    Route::get('/pages/officers', function () { return view('Secretary.OfficerRegistration'); })->name('secretary.officers');
    Route::get('/pages/secdashboard', action: [SecretaryDashboardController::class, 'index'])->name('secretary.dashboard');
    Route::get('/pages/officers', [OfficerController::class, 'index'])->name('secretary.officers');
    Route::get('/pages/officers', [OfficerController::class, 'index'])->name('officers.index');
    Route::post('/pages/officers', [OfficerController::class, 'store'])->name('officers.store');
    Route::put('/pages/officers/{id}', [OfficerController::class, 'update'])->name('officers.update');

    Route::post('/scan', [AttendanceQRController::class, 'scanQr'])->name('attendance.scan');
    Route::post('/open-scan-type', [AttendanceQRController::class, 'openScanType'])->name('attendance.open-scan-type');
    Route::post('/close-scan-type', [AttendanceQRController::class, 'closeScanType'])->name('attendance.close-scan-type');
    Route::get('/open-scan-type', [AttendanceQRController::class, 'getOpenScanType'])->name('attendance.get-open-scan-type');
    Route::post('/finish-event', [AttendanceQRController::class, 'finishEvent'])->name('attendance.finish-event');

Route::get('/secretary/attendance/print/{event}', [SecretaryController::class, 'printAttendanceReport'])->name('secretary.printAttendanceReport');

// Event Management
Route::post('/secretary/store-event', [SecretaryController::class, 'storeEvent'])->name('secretary.storeEvent');
Route::get('/secretary/get-events', [SecretaryController::class, 'getEvents'])->name('secretary.getEvents');
Route::get('/secretary/get-event/{id}', [SecretaryController::class, 'getEvent'])->name('secretary.getEvent');

Route::get('/get-student-count', [SecretaryDashboardController::class, 'getStudentCount']);
Route::get('/get-events', [SecretaryDashboardController::class, 'getEvents']);
// Student Approval Routes
Route::post('/secretary/approve-student/{id}', [SecretaryController::class, 'approveStudent'])->name('secretary.approve.student')->middleware('web');
Route::post('/secretary/decline-student/{id}', [SecretaryController::class, 'declineStudent'])->name('secretary.decline.student');
Route::put('/secretary/update-student/{id}', [SecretaryController::class, 'updateStudent'])->name('secretary.update-student');

Route::prefix('treasurer')->group(function () {
    Route::get('/', [TreasurerController::class, 'index'])->name('treasurer.index');
    Route::get('/pages/orgstruct', [SecretaryController::class, 'orgstruct'])->name('secretary.orgstruct');
    Route::get('/dashboard', [DashboardTreasurerController::class, 'index'])->name('treasurer.dashboard');
    Route::get('/payment', [TreasurerController::class, 'payment'])->name('treasurer.payment');
    Route::get('/transaction', [TreasurerController::class, 'transaction'])->name('treasurer.transaction');
    Route::get('pages/eventsam', [TreasurerController::class, 'eventsam'])->name('treasurer.eventsam');
    Route::get('/itsboOfficers', [TreasurerController::class, 'itsboOfficers'])->name('treasurer.itsboOfficers');
    Route::get('/fines', [TreasurerController::class, 'fines'])->name('treasurer.fines');
    Route::get('/fines', [TreasurerController::class, 'showFines'])->name('treasurer.fines');

    // Payment Management Routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('treasurer.payments');
    Route::get('/payments/search', [PaymentController::class, 'search'])->name('treasurer.payments.search');
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('treasurer.payments.show');
    Route::post('/payments', [PaymentController::class, 'store'])->name('treasurer.payments.store');
    Route::get('/payments/{id}/history', [PaymentController::class, 'getPaymentHistory'])->name('treasurer.payments.history');
    Route::get('/payments/{id}/summary', [PaymentController::class, 'getPaymentSummary'])->name('treasurer.payments.summary');
    Route::get('/payments/{id}/edit', [PaymentController::class, 'edit']); // For getting payment details
    Route::put('/payments/{id}', [PaymentController::class, 'update']); // For updating payment
    // Payment For Routes
    Route::post('/payment-fors', [PaymentController::class, 'storePaymentFor'])->name('treasurer.payment-fors.store');
    Route::get('/payment-fors', [PaymentController::class, 'getPaymentFors'])->name('treasurer.payment-fors.index');
    Route::get('/payment-fors/{id}', [PaymentController::class, 'showPaymentFor'])->name('treasurer.payment-fors.show');
    Route::put('/payment-fors/{id}', [PaymentController::class, 'updatePaymentFor'])->name('treasurer.payment-fors.update');
    Route::get('/student-payments/{studentId}', [PaymentController::class, 'getStudentPayments'])->name('treasurer.student.payments');
    Route::get('/payment-summary', [PaymentController::class, 'getPaymentSummary'])->name('treasurer.payment.summary');});
    Route::get('/payments/edit/{id}', [PaymentController::class, 'edit']);
    Route::put('/payments/{id}', [PaymentController::class, 'update']); // Add this

Route::get('/superadmin/view/{role}/{page}', [SuperAdminController::class, 'viewPage'])->name('superadmin.view');
Route::get('/superadmin/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');
Route::get('/pages/orgstruct', [SuperAdminController::class, 'orgstruct'])->name('superadmin.orgstruct');
Route::get('/pages/dashboard', [DashboardTreasurerController::class, 'index'])->name('treasurer.dashboard');
Route::get('/pages/payment', [TreasurerController::class, 'payment'])->name('treasurer.payment');
Route::get('/pages/transaction', [TreasurerController::class, 'transaction'])->name('treasurer.transaction');
Route::get('/pages/secdashboard', [SecretaryDashboardController::class, 'index'])->name('secretary.dashboard');
Route::get('/pages/event', [SecretaryController::class, 'events'])->name('secretary.event');
Route::get('/pages/attendance', function () { return view('Secretary.Attendance'); })->name('secretary.attendance');
Route::get('/pages/officers', function () { return view('Secretary.Officers'); })->name('secretary.officers');
Route::get('/superadmin/schoolyear', [SuperAdminController::class, 'schoolYear'])->name('superadmin.schoolyear');
Route::post('/superadmin/schoolyear/open', [SuperAdminController::class, 'openSchoolYear'])->name('superadmin.schoolyear.open');
Route::post('/superadmin/schoolyear/close', [SuperAdminController::class, 'closeSchoolYear'])->name('superadmin.schoolyear.close');
Route::post('/superadmin/schoolyear/newsemester', [SuperAdminController::class, 'openNewSemester'])->name('superadmin.schoolyear.newsemester');
Route::get('/pages/attendance-qr', function () {return view('Secretary.AttendanceQR');})->name('superadmin.attendanceqr');
Route::get('/superadmin/archieve', [SuperAdminController::class, 'archieve'])->name('superadmin.archieve');


Route::post('/attendance/open-scan-type', [AttendanceQRController::class, 'openScanType'])->name('attendance.openScanType');
Route::post('/attendance/close-scan-type', [AttendanceQRController::class, 'closeScanType'])->name('attendance.closeScanType');
Route::get('/attendance/open-scan-type', [AttendanceQRController::class, 'getOpenScanType'])->name('attendance.getOpenScanType');


Route::get('/treasurer/orgstruct', [TreasurerController::class, 'orgstruct'])->name('treasurer.orgstruct');
Route::post('/attendance/finish-event', [AttendanceQRController::class, 'finishEvent'])->name('attendance.finishEvent');

    Route::get('/pages/attendance-qr', [AttendanceQRController::class, 'index'])->name('attendance.qr');
    Route::get('/api/events', [AttendanceQRController::class, 'getEvents'])->name('events.list');

// Student Auth Routes
Route::get('/student/login', [StudentLoginController::class, 'showStudentLogin'])->name('student.login');
Route::post('/student/login', [StudentLoginController::class, 'studentLogin'])->name('student.login.submit');
Route::post('/logout', [StudentController::class, 'studentlogout'])->name('student.logout');

// Student Routes (without auth middleware)
Route::prefix('student')->group(function () {
    Route::get('/dashboard/{student_id}', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/announcement', [StudentController::class, 'announcement'])->name('student.announcement');
    Route::get('/attendance/{student_id}', [StudentController::class, 'getAttendanceRecord'])->name('student.attendance');
    Route::get('/qrcode', [StudentController::class, 'qrcode'])->name('student.qrcode');
    Route::get('/orgstruct', [StudentController::class, 'orgstruct'])->name('student.orgstruct');

});
Route::post('/student/change-password/{student_id}', [StudentController::class, 'changePassword'])->name('student.change.password');
Route::get('/student/profile/{student_id}', [StudentController::class, 'profile'])->name('student.profile');
Route::post('/student/profile/update-photo/{student_id}', [StudentController::class, 'updatePhoto'])->name('student.update.photo');
Route::get('/student/accounts/{student_id}', [StudentController::class, 'accounts'])->name('student.accounts');

Route::prefix('student')->group(function () {
    Route::get('qrcode/{student_id}', [StudentController::class, 'qrcode'])->name('student.qrcode');
    Route::get('qrcode/refresh/{student_id}', [StudentController::class, 'refreshQRCode'])->name('student.qrcode.refresh');
});
Route::prefix('attendance')->group(function () {
    Route::post('/scan', [AttendanceQRController::class, 'scanQr'])->name('attendance.scan');
    Route::post('/open-scan-type', [AttendanceQRController::class, 'openScanType'])->name('attendance.open-scan-type');
    Route::post('/close-scan-type', [AttendanceQRController::class, 'closeScanType'])->name('attendance.close-scan-type');
    Route::get('/open-scan-type', [AttendanceQRController::class, 'getOpenScanType'])->name('attendance.get-open-scan-type');
    Route::post('/finish-event', [AttendanceQRController::class, 'finishEvent'])->name('attendance.finish-event');
});
// In your web.php file
Route::get('/payments/{id}', [PaymentController::class, 'show']); // Get student payment details
Route::post('/payments', [PaymentController::class, 'store']); // Create new payment
Route::get('/payment-fors', [PaymentController::class, 'getPaymentFors']); // Get all payment fors
Route::post('/payment-fors', [PaymentController::class, 'storePaymentFor']); // Create payment for
Route::get('/payment-fors/{id}', [PaymentController::class, 'showPaymentFor']); // Get payment for details
Route::put('/payment-fors/{id}', [PaymentController::class, 'updatePaymentFor']); // Update payment for

 // API Routes for AJAX calls
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard-data', [TreasurerController::class, 'getDashboardData'])->name('dashboard.data');
        Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
        Route::get('/payments/summary', [PaymentController::class, 'getSummary'])->name('payments.summary');
    });
    
    // Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/collections', [TreasurerController::class, 'collectionsReport'])->name('collections');
        Route::get('/students', [TreasurerController::class, 'studentsReport'])->name('students');
        Route::get('/export/collections', [TreasurerController::class, 'exportCollections'])->name('export.collections');
        Route::get('/export/students', [TreasurerController::class, 'exportStudents'])->name('export.students');
    });
use App\Http\Controllers\PaymentReportController;

Route::get('/attendance/print', [AttendanceQRController::class, 'printReport'])->name('attendance.print-report');

Route::get('/payment-transactions', [PaymentTransactionReportController::class, 'index'])->name('payment-transactions');
Route::get('/reports/payment-transactions', [PaymentTransactionReportController::class, 'index'])->name('reports.payment-transactions');

use App\Http\Controllers\Student\StudentForgotPasswordController;

// Student Forgot Password Route
Route::post('/student/forgot-password', [StudentForgotPasswordController::class, 'sendNewPassword'])
    ->name('student.forgot.password');
