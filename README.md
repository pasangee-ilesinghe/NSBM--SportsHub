# NSBM-SportsHub – Sports Club Management System

> **Module:** Web and Mobile Application Development | **Batch:** 26.1 IFSL
> **Faculty of Computing, NSBM Green University**

A web application that helps NSBM university sports clubs manage memberships, training schedules, matches and tournaments, while allowing students to join clubs, book facilities and track their participation.

---

## Project Links

| Item | Link |
|------|------|
| 🌐 Live application | `https://nsbm-sporthub.ct.ws` |
| 🎥 Demo video | `https://drive.google.com/file/d/1ohNvHfkoFx9s4cpAyM25LCQMaJuoYTNN/view?usp=sharing` |

---

## Features

### Admin Panel
- Secure admin login with role-protected pages
- **Manage clubs:** add, edit, delete clubs (details, venue, in-charge staff, images, recent results)
- **Membership approvals:** approve, reject or remove membership requests
- **Schedule sessions:** recurring weekly training sessions and one-off special sessions
- **Matches and results:** schedule matches, record scores and outcomes
- **Tournaments:** create, edit and delete tournaments
- **Facility bookings:** approve, reject or delete booking requests
- **Reports:** participation and performance reports with date filters, plus **PDF export** (FPDF)

### Student Panel
- Register and log in
- Browse and search clubs, and view club details, schedules and results
- Join clubs, register for tournaments and book training facilities
- View match fixtures and results
- Personal dashboard: participation history, cancel bookings or registrations, leave clubs
- Edit profile and change password

### Security
- Passwords hashed with `password_hash()` and verified with `password_verify()`
- Prepared statements (MySQLi) for all database queries
- Role-based access control on admin pages
- CSRF tokens on every form, with approve / reject / delete / cancel actions sent by POST
- Session ID regeneration after login and output escaping with `htmlspecialchars()`
- Client-side validation (`validation.js`) backed by server-side validation in PHP
- Mobile-friendly layout with a collapsible navbar

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript, Bootstrap 5.3 |
| Backend | PHP (MySQLi) |
| Database | MySQL / MariaDB |
| Reports | FPDF (PDF export) |
| Local server | XAMPP |

---

## Project Structure

```
nsbm_sportshub/
├── index.php                      # Home page
├── login.php , logout.php         # Login and logout
├── register.php                   # Student registration
├── clubs.php, club_detail.php     # Browse clubs and club details
├── matches.php                    # Fixtures and results
├── tournaments.php                # Tournament list
├── register_tournament.php        # Tournament registration
├── book_facility.php              # Facility booking
├── booking_cleanup.php            # Hide past bookings
├── contact.php                    # Contact page
├── db.php                         # Database connection
├── csrf.php                       # CSRF token and POST action helpers
├── validation.js                  # Client-side form validation
├── style.css                      # Styling
├── README.md
├── images/
├── student/
│   ├── student_dashboard.php
│   ├── edit_profile.php
│   └── change_password.php
├── admin/
│   ├── login.php , logout.php
│   ├── auth_check.php             # Admin access control
│   ├── dashboard.php
│   ├── manage_clubs.php, edit_club.php
│   ├── manage_club_memberships.php
│   ├── schedule_sessions.php
│   ├── add_result.php
│   ├── manage_tournaments.php
│   ├── manage_bookings.php
│   ├── reports.php, export_pdf.php
│   └── fpdf.php, font/            # FPDF library
└── database/
    └── nsbm_sportshub.sql
```

---

## Database

Database name: `nsbm_sportshub` (import `database/nsbm_sportshub.sql`).

Students are linked to memberships, bookings and tournament registrations by `student_id` using **foreign keys**, and unique keys prevent duplicate joins and registrations.

| Table | Purpose |
|-------|---------|
| admins | Administrator accounts |
| users | Student accounts |
| clubs | Club profiles, in-charge staff, images, results |
| club_memberships | Student and club membership requests and status |
| club_schedules | Recurring weekly training sessions |
| club_special_sessions | One-off special sessions |
| matches | Fixtures, scores and outcomes |
| tournaments | Tournament details |
| tournament_registrations | Student tournament registrations |
| facility_bookings | Facility booking requests and status |

---

## Installation (Local – XAMPP)

1. **Clone** the repository into XAMPP's web root:
   ```bash
   git clone https://github.com/pasangee-ilesinghe/NSBM--SportsHub.git C:/xampp/htdocs/nsbm_sportshub
   ```
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open **phpMyAdmin** and create a database named `nsbm_sportshub`.
4. Select the database, click **Import**, and choose `database/nsbm_sportshub.sql`.
5. Visit **http://localhost/nsbm_sportshub/**

`db.php` uses the default XAMPP credentials (`root`, no password) on `localhost`.

---

## Login Details

| Role | Login page | Credentials |
|------|-----------|-------------|
| Admin | `/login.php` (select **Admin**) | Username: `Super Admin` / Password: `Admin2004` |
| Student | `/login.php` | Register a new account, or use Username: `student@gmail.com` / Password: `Student2004` |

---

## Academic Note

Developed for academic purposes at NSBM Green University.
