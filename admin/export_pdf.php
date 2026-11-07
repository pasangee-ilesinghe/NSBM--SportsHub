<?php
session_start();

// Strict Admin Role Security Check (prevents students from accessing/downloading reports via direct URL)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';
require_once '../booking_cleanup.php';
cleanup_expired_bookings($conn);
require('fpdf.php'); // Make sure path matches where you put fpdf.php

class PDF extends FPDF {
    // Page header
    function Header() {
        // Brand Header Background
        $this->SetFillColor(10, 63, 143); // Deep NSBM Blue
        $this->Rect(0, 0, 210, 25, 'F');
        
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 15, 'NSBM SportsHub - System Analytics & Summary Report', 0, 1, 'C');
        
        // Date generated info
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 8, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'R');
        $this->Ln(5);
    }

    // Page footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' | NSBM SportsHub Admin Portal', 0, 0, 'C');
    }

    // Custom Section Headings
    function ChapterTitle($label) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(10, 63, 143);
        $this->Cell(0, 8, $label, 0, 1, 'L');
        $this->Ln(2);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);

// --- 1. OVERALL METRICS ---
$pdf->ChapterTitle('1. Overall Platform Summary');
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(240, 242, 245);
$pdf->SetTextColor(0, 0, 0);

// Fetch counts
$clubsCount = $conn->query("SELECT COUNT(*) as cnt FROM club_memberships")->fetch_assoc()['cnt'];
$bookingsCount = $conn->query("SELECT COUNT(*) as cnt FROM facility_bookings")->fetch_assoc()['cnt'];
$tournamentsCount = $conn->query("SELECT COUNT(*) as cnt FROM tournament_registrations")->fetch_assoc()['cnt'];

$pdf->Cell(63, 10, 'Total Club Memberships: ' . $clubsCount, 1, 0, 'C', true);
$pdf->Cell(63, 10, 'Total Facility Bookings: ' . $bookingsCount, 1, 0, 'C', true);
$pdf->Cell(64, 10, 'Tournament Signups: ' . $tournamentsCount, 1, 1, 'C', true);
$pdf->Ln(6);

// --- 2. MATCH PERFORMANCE ---
$pdf->ChapterTitle('2. Match Performance Overview');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(231, 243, 255);
$pdf->SetTextColor(10, 63, 143);

$pdf->Cell(47, 8, 'Matches Played', 1, 0, 'C', true);
$pdf->Cell(47, 8, 'Total Wins', 1, 0, 'C', true);
$pdf->Cell(47, 8, 'Total Losses', 1, 0, 'C', true);
$pdf->Cell(49, 8, 'Medals / Trophies', 1, 1, 'C', true);

// Fetch match stats safely
$mPlayed = $conn->query("SELECT COUNT(*) as cnt FROM matches WHERE status = 'Completed'")->fetch_assoc()['cnt'] ?? 0;
$mWins = $conn->query("SELECT COUNT(*) as cnt FROM matches WHERE outcome = 'Win' OR outcome LIKE '%Medal%' OR outcome LIKE '%Trophy%' OR outcome LIKE '%Gold%'")->fetch_assoc()['cnt'] ?? 0;
$mLosses = $conn->query("SELECT COUNT(*) as cnt FROM matches WHERE outcome = 'Loss'")->fetch_assoc()['cnt'] ?? 0;
$mMedals = $conn->query("SELECT COUNT(*) as cnt FROM matches WHERE outcome LIKE '%Medal%' OR outcome LIKE '%Trophy%' OR outcome LIKE '%Gold%'")->fetch_assoc()['cnt'] ?? 0;

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(47, 8, $mPlayed, 1, 0, 'C');
$pdf->Cell(47, 8, $mWins, 1, 0, 'C');
$pdf->Cell(47, 8, $mLosses, 1, 0, 'C');
$pdf->Cell(49, 8, $mMedals, 1, 1, 'C');
$pdf->Ln(6);

// --- 3. TOURNAMENT PARTICIPATION ---
$pdf->ChapterTitle('3. Tournament Participation Overview');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(231, 243, 255);
$pdf->SetTextColor(10, 63, 143);

$pdf->Cell(80, 8, 'Tournament Name', 1, 0, 'L', true);
$pdf->Cell(40, 8, 'Sport', 1, 0, 'L', true);
$pdf->Cell(40, 8, 'Date', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Participants', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);
$tQuery = "SELECT t.tournament_name, t.sport, t.tournament_date, COUNT(tr.id) as reg_count 
           FROM tournaments t LEFT JOIN tournament_registrations tr ON t.id = tr.tournament_id 
           GROUP BY t.id ORDER BY reg_count DESC";
$tRes = $conn->query($tQuery);

if ($tRes && $tRes->num_rows > 0) {
    while($row = $tRes->fetch_assoc()) {
        $pdf->Cell(80, 7, $row['tournament_name'], 1, 0, 'L');
        $pdf->Cell(40, 7, $row['sport'] ?? '-', 1, 0, 'L');
        $pdf->Cell(40, 7, $row['tournament_date'], 1, 0, 'C');
        $pdf->Cell(30, 7, $row['reg_count'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(190, 7, 'No tournaments found.', 1, 1, 'C');
}
$pdf->Ln(6);

// --- 4. CLUB MEMBERS BREAKDOWN ---
$pdf->ChapterTitle('4. Members Breakdown per Sport Club');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(231, 243, 255);
$pdf->SetTextColor(10, 63, 143);

$pdf->Cell(60, 8, 'Club Name', 1, 0, 'L', true);
$pdf->Cell(50, 8, 'Venue', 1, 0, 'L', true);
$pdf->Cell(50, 8, 'Master-in-Charge', 1, 0, 'L', true);
$pdf->Cell(30, 8, 'Enrolled', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);
$cQuery = "SELECT c.club_name, c.venue, c.master_in_charge, COUNT(cm.id) as member_count 
           FROM clubs c LEFT JOIN club_memberships cm ON c.id = cm.club_id 
           GROUP BY c.id ORDER BY member_count DESC";
$cRes = $conn->query($cQuery);

if ($cRes && $cRes->num_rows > 0) {
    while($row = $cRes->fetch_assoc()) {
        $pdf->Cell(60, 7, $row['club_name'], 1, 0, 'L');
        $pdf->Cell(50, 7, $row['venue'] ?? '-', 1, 0, 'L');
        $pdf->Cell(50, 7, $row['master_in_charge'] ?? '-', 1, 0, 'L');
        $pdf->Cell(30, 7, $row['member_count'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(190, 7, 'No club data found.', 1, 1, 'C');
}

// Output PDF for download/viewing in Adobe Reader
$pdf->Output('I', 'NSBM_SportsHub_Summary_Report.pdf');
exit();
?>