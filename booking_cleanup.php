<?php


date_default_timezone_set('Asia/Colombo');   // so "today" follows Sri Lanka time

function cleanup_expired_bookings($conn, $delete_past_approved = false) {
    $today = date('Y-m-d');
    try {
        if ($delete_past_approved) {
            $stmt = $conn->prepare("DELETE FROM facility_bookings WHERE booking_date < ?");
        } else {
            $stmt = $conn->prepare("DELETE FROM facility_bookings WHERE booking_date < ? AND status <> 'Approved'");
        }
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $stmt->close();
    } catch (Throwable $e) {
        // housekeeping must never break a page
    }
}

// The admin's "Delete" only hides a booking from the admin list (admin_hidden = 1); the student keeps it in
// their own history. This adds that column automatically the first time it is needed.
function ensure_admin_hidden_column($conn) {
    try {
        $r = $conn->query("SHOW COLUMNS FROM facility_bookings LIKE 'admin_hidden'");
        if ($r && $r->num_rows === 0) {
            $conn->query("ALTER TABLE facility_bookings ADD COLUMN admin_hidden TINYINT(1) NOT NULL DEFAULT 0");
        }
    } catch (Throwable $e) {
        // ignore - see database/upgrade_admin_hidden.sql to add the column by hand
    }
}