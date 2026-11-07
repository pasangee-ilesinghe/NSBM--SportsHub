-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2026 at 10:18 AM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nsbm_sportshub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(2, 'Super Admin', '$2y$10$DYY.aWSOtY336oZKMEaMtuw.OuEfUMevtL3LYwHSmeH68LZ38LcMK');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `club_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `master_in_charge` varchar(100) DEFAULT NULL,
  `mistress_in_charge` varchar(100) DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `img1` varchar(255) DEFAULT NULL,
  `img2` varchar(255) DEFAULT NULL,
  `img3` varchar(255) DEFAULT NULL,
  `recent_results` text DEFAULT NULL,
  `banner_img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `club_name`, `description`, `category`, `master_in_charge`, `mistress_in_charge`, `venue`, `email`, `img1`, `img2`, `img3`, `recent_results`, `banner_img`) VALUES
(1, 'Badminton Club', 'The NSBM Badminton Club is vibrant community for students who share a passion for badminton and an active lifestyle. The club welcomes players of all skill levels, from beginners to experienced athletes. Members have opportunities to participate in regular training sessions, friendly matches, and competitive tournaments. The club focuses on developing technical skills, fitness, teamwork, and sportsmanship. It provides a supportive environment where students can improve their game while building lasting friendships. Whether you play for fun or aim to compete, the NSBM Badminton Club is a great place to challenge yourself and enjoy the sport.', 'Badminton', 'Mr. Thisal Yatawara', 'Ms. Isuri Caldera', 'Recreation Center', 'badminton@nsbm.ac.lk', 'images/badminton1.webp', 'images/badminton2.jpg', 'images/badminton3.webp', 'Colombo Shuttle saga 2025 - Runners up\r\nICBT Smash Down 2024(Girls) - 2nd Runners up\r\nGSF 2024 - Runners up\r\nUsports 2023 Boys - Champions', 'images/banner_badminton.jpg'),
(2, 'Cricket Club', 'The NSBM Cricket Club brings together passionate students who love the game and the spirit of competition. Open to players of all skill levels, the club provides opportunities to train, improve, and compete in friendly and inter-university matches. Members develop their batting, bowling, fielding, fitness, and teamwork skills while building strong connections with fellow players. Whether you’re a beginner or an experienced cricketer, the club offers a supportive environment to grow, compete, and enjoy the game. Join the team, represent NSBM, and make every match count.', 'Cricket', 'Mr. Gayan Perera', 'Ms. Ruwanthi Perera', 'NSBM Playground', 'cricket@nsbm.ac.lk', 'images/cricket2.jpg', 'images/cricket1.jpg', 'images/cricket3.jpg', 'NSBM USport 2022 – Runner Up\r\nAPIIT Extravaganza 2022 – Runner Up\r\nKDU Leaders Trophy 2018 – Runner Up', 'images/banner_cricket.jpg'),
(3, 'Rugby Club', 'The NSBM Rugby Club is a community of passionate students united by the spirit of rugby and competition. Open to players of all skill levels, the club provides opportunities to train, improve, and compete in exciting matches and tournaments. Members develop their fitness, technique, teamwork, and game strategy while building strong bonds with fellow players. The club promotes discipline, sportsmanship, and a strong team spirit both on and off the field. Whether you’re a beginner or an experienced player, there’s a place for you on the team. Join the club, embrace the challenge, and represent NSBM with pride.', 'Rugby', 'Prof. Baratha Dodankotuwa', 'Ms. Shehani Joseph', 'NSBM Main Grounds', 'rugby@nsbm.ac.lk', 'images/rugby2.jpg', 'images/rugby1.webp', 'images/rugby3.jpg', 'CINEC Rugby Showdown – Championship 2024\r\nAPIIT Extravaganza Rugby Championship 2023\r\nMORA 7s Rugby Champions 2023\r\nUsports Rugby Champions 2022', 'images/banner_rugby.jpg'),
(4, 'Athletics Club', 'The NSBM Athletics Club brings together students with a passion for running, fitness, and competitive athletics. The club welcomes athletes of all skill levels who are eager to improve their performance and challenge themselves. Members have opportunities to participate in training sessions, athletic meets, and inter-university competitions. The club focuses on developing speed, strength, endurance, technique, and overall fitness. It also promotes discipline, determination, teamwork, and a strong competitive spirit. Whether you are aiming for competition or simply looking to stay active, the NSBM Athletics Club provides the perfect platform to push your limits and achieve your goals.', 'Athletics', 'Mr. Venura Colombage', 'Ms. Aushadharie Vidanalage', 'University Stadium Track', 'athletics@nsbm.ac.lk', 'images/athletic1.jpg', 'images/athletic2.jpg', 'images/athletic3.webp', 'SLIIT Relay Competition – 2023\r\nOverall Runner-up (University Boys Category) Overall 2nd Runer-up (University Girls Category)\r\n4 x 200m (University Boys Category) – Gold Medal\r\nMedley Relay (University Boys Category) – Gold Medal\r\n \r\nSLTC Athletic Meet (2022)\r\n2nd Runners up (Invitation Relay)', 'images/banner_athletic.jpg'),
(5, 'Swimming Club', 'The NSBM Swimming Club provides students with access to a state-of-the-art, international-level swimming pool and a supportive environment for aquatic excellence. Established in 2017 through the NSBM Inter-University Swimming Championship, the club quickly made its mark by becoming champions of the USPORTS competition. Since then, our swimmers have continued to represent NSBM with dedication, discipline, and outstanding performance. The club welcomes everyone, from experienced swimmers aiming for competitive excellence to beginners taking their first steps in the water. Members have opportunities to develop their swimming skills, improve fitness, and participate in competitive events. With a strong spirit of teamwork and camaraderie, the NSBM Swimming Club is the perfect place to challenge yourself and make a splash.\r\n\r\n\r\n\r\n', 'Swimming', 'Mr.Shamila Kulatunga', 'Ms.Samithri Wannichy', 'Campus Six-Lane Swimming Pool', 'swimming@nsbm.ac.lk', 'images/swimming1.jpg', 'images/swimming2.jpg', 'images/swimming3.jpeg', 'SLIIT Aquaclash 2026 - 50m*4 Mixed medley relay 2nd place\r\nExtravaganza 2025 - 50m*4 Freestyle Relay 2nd place', 'images/banner_swim.jpg'),
(6, 'Table Tennis Club', 'Table Tennis is a fast-paced sport which requires fitness and speed, and it has been identified as one of the fastest sports in the world. Table Tennis was introduced to NSBM Green University in the year 2017 and since the Club has been an active club that has provided many undergraduates the opportunity to improve their hand-eye coordination, reflections, and mental alertness.', 'Table Tennis', 'Mr. Piumal Herath', 'Ms. Tharani Sooriyaarachchi', 'Recreation Center', 'tabletennis@nsbm.ac.lk', 'images/table1.webp', 'images/table2.webp', 'images/table3.jpg', 'U Sports 2022 : Mens – 2nd Place , Womens’ – 2nd Place , Mixed Event – Champions', 'images/banner_tabletennis.jpg'),
(7, 'Chess Club', 'The NSBM Chess Club brings together students who share a passion for strategy, critical thinking, and the game of chess. Open to players of all skill levels, the club provides a friendly environment to learn, practice, and improve. Members can participate in training sessions, friendly matches, chess tournaments, and inter-university competitions. The club helps develop concentration, problem-solving skills, strategic thinking, and patience both on and off the board. Whether you are a beginner learning your first moves or an experienced player looking for a challenge, there is a place for you. Join the NSBM Chess Club, sharpen your mind, and make your next move count.', 'Chess', 'Mr. P.Thilakarathne', 'Ms. Subodhi Rathnayake', 'Computing Faculty Hall', 'chess@nsbm.ac.lk', 'images/chess1.jpg', 'images/chess2.webp', 'images/chess3.jpg\r\n', '12th Ethugalpura International Rated Open Chess Championship (Higher Education Sector)\r\nChampion – NSBM Boys team\r\nSabra Chess Championship\r\nChampion – NSBM A Team\r\nRunners-up – NSBM Boys team\r\n9th Kalutara International Rated Open Chess Championship (Higher Education Sector)\r\n1st Runners up – NSBM Team\r\n5th SLIIT Castle Fest 2021 (Held in 2022)\r\nRunners-up – NSBM Girls Chess team\r\nNSBM Usport\r\n2nd Runners-up – NSBM Girls Chess team', 'images/banner_chess.jpg'),
(8, 'Archery Club', 'The NSBM Archery Club brings together students with a passion for precision, focus, and competitive archery. The club welcomes beginners and experienced archers who are eager to learn, train, and improve their skills. Members have opportunities to take part in regular training sessions, friendly competitions, and inter-university events. The club focuses on developing accuracy, concentration, coordination, discipline, and confidence. Beyond competition, it provides a supportive environment where members can challenge themselves and connect with fellow archery enthusiasts. Whether you are picking up a bow for the first time or aiming for competitive excellence, the NSBM Archery Club is the perfect place to take aim and hit your goals.', 'Archery', 'Mr. Anton Jayakody', 'Ms. Anusha Nanayakkara', 'Open Air Sports Lawn', 'archery@nsbm.ac.lk', 'images/archery1.jpg', 'images/archery2.jpg', 'images/archery3.jpg', NULL, 'images/banner_archery.jpg'),
(9, 'Volleyball Club', 'The purpose of NSBM Volleyball club is to provide a safe environment where young athletets will improve in the game of volleyball and learn the important values of team, spirit and sportsmanship. We will encourage the development of skills and techniques necessary for players to compete at a high level, focusing on the physical, mental, and emotional development. We will utilize the sport of volleyball to teach life-long lessons to our players (leadership, teamwork, hard work ethic, dealing with adversity and success, communication, ethics, loyalty etc.) We also want players to understand the importance of academics and other necessities beyond the volleyball court in order to be successful in life. We will strive to “Develop Better Players, Better Teams and Better People.”', 'Volleyball', 'Mr. Praveen Ranaweera', 'Ms. V K M Chandima', 'Outdoor Court Complex', 'volleyball@nsbm.ac.lk', 'images/volley1.jpg', 'images/volley2.jpg', 'images/volley3.jpg', 'Friendly vollyball match 2025[NSBM vs UOC] - Champions ', 'images/banner_volleyball.jpg'),
(11, 'Football Club', 'The NSBM Football Club brings together students who share a passion for football, teamwork, and competitive spirit. The club welcomes players of all skill levels, providing a supportive environment to train, improve, and enjoy the game. Members have opportunities to participate in regular training sessions, friendly matches, and inter-university competitions. The club focuses on developing technical skills, fitness, teamwork, game awareness, and tactical ability. Beyond the pitch, it promotes discipline, sportsmanship, friendship, and a strong sense of team spirit. Whether you’re a beginner or an experienced player, the NSBM Football Club is the perfect place to grow, compete, and represent NSBM with pride.', 'Football', 'Mr. Dineth Bamunukula', 'Ms. Hiruni Jayawardena', 'NSBM Football Turf', 'football@nsbm.ac.lk', 'images/football1.jpg', 'images/football2.jpg', 'images/football3.jpg', 'The Shield of Greens 2025 - Champions', 'images/banner_football.jpg'),
(14, 'Karate Club', 'The NSBM Karate Club is a dynamic sports community that promotes discipline, strength, self-confidence, and martial arts skills among students. It provides opportunities for members to train, develop their abilities, and represent NSBM in Karate competitions.', 'Karate', 'Mr. Maleesha Edirisinghe', 'Ms. Rhythmani Perera', 'Recreation Center', 'karate@nsbm.ac.lk', 'images/karate1.jpg', 'images/karate2.jpg', 'images/karate3.png', '10th Commonwealth Karate Championship (Birminghanm – England) – 2nd Runners Up\r\nKanninjuku International Karate Championship – 11 medals\r\nInternational Peter Gimo Super Cup Open Karate Championship – 5 Gold Medals, 2 Silver Medals and 1 Bronze Medal\r\n25th International Open Karate Championships 2022 – 5 Gold Medals\r\n', 'images/banner_karate.jpg'),
(16, 'Basketball Club', 'The NSBM Basketball Club is a dynamic student-led sports community at NSBM Green University focused on athletic growth and teamwork. It offers professional training to help members build agility, court skills, and strong physical conditioning. The club actively represents the university in inter-university matches and national 3x3 tournaments. Emphasizing discipline, leadership, and sportsmanship, it helps students balance academics with an active campus life.', NULL, 'Mr. Savindu Dhamsara', NULL, 'NSBM Playground', 'basketball@nsbm.ac.lk', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `club_memberships`
--

CREATE TABLE `club_memberships` (
  `id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `club_id` int(11) NOT NULL,
  `joined_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `student_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_memberships`
--

INSERT INTO `club_memberships` (`id`, `student_name`, `club_id`, `joined_date`, `status`, `student_id`) VALUES
(8, 'Pasangee Jayodhya Ilesinghe', 1, '2026-10-28', 'Approved', '42153'),
(10, 'Pasangee Jayodhya Ilesinghe', 2, '2026-10-28', 'Approved', '42153'),
(13, 'Dilshi Kandage', 1, '2026-10-28', 'Approved', '42057'),
(14, 'Dilshi Kandage', 6, '2026-10-28', 'Approved', '42057'),
(16, 'Pasangee Jayodhya Ilesinghe', 3, '2026-10-31', 'Approved', '42153');

-- --------------------------------------------------------

--
-- Table structure for table `club_schedules`
--

CREATE TABLE `club_schedules` (
  `id` int(11) NOT NULL,
  `club_name` varchar(100) NOT NULL,
  `session_day` varchar(50) NOT NULL,
  `time` varchar(50) NOT NULL,
  `venue` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_schedules`
--

INSERT INTO `club_schedules` (`id`, `club_name`, `session_day`, `time`, `venue`) VALUES
(4, 'Badminton Club', 'Monday', '9.00 a.m. - 12.00 p.m.', 'Recreation Centre'),
(5, 'Badminton Club', 'Tuesday', '12.30 p.m. - 4.00 p.m.', 'Recreation Centre'),
(6, 'Cricket Club', 'Monday', '3.00 p.m. - 5.00 p.m.', 'NSBM Playground'),
(7, 'Rugby Club', 'Wednesday', '3.00 p.m. - 5.00 p.m.', 'NSBM Playground'),
(9, 'Football Club', 'Friday', '9.00 a.m. - 12.00 p.m.', 'NSBM Playground'),
(10, 'Swimming Club', 'Tuesday', '3.00 p.m. - 5.00 p.m.', 'Swimming Pool');

-- --------------------------------------------------------

--
-- Table structure for table `club_special_sessions`
--

CREATE TABLE `club_special_sessions` (
  `id` int(11) NOT NULL,
  `club_name` varchar(100) NOT NULL,
  `session_date` date NOT NULL,
  `time` varchar(50) NOT NULL,
  `venue` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT 'Special Session'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_special_sessions`
--

INSERT INTO `club_special_sessions` (`id`, `club_name`, `session_date`, `time`, `venue`, `description`) VALUES
(1, 'Cricket Club', '2026-10-31', '12.30 p.m. - 3.00 p.m.', 'NSBM Playground', 'Inter-club practice'),
(2, 'Swimming Club', '2026-11-26', '10.00 a.m. - 12.00 p.m.', 'Swimming Pool', 'Inter-club practice');

-- --------------------------------------------------------

--
-- Table structure for table `facility_bookings`
--

CREATE TABLE `facility_bookings` (
  `id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `facility_name` varchar(150) NOT NULL,
  `booking_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `student_id` varchar(50) NOT NULL,
  `admin_hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility_bookings`
--

INSERT INTO `facility_bookings` (`id`, `student_name`, `facility_name`, `booking_date`, `status`, `student_id`, `admin_hidden`) VALUES
(4, 'Dilshi Kandage', 'Swimming Pool', '2026-10-30', 'Approved', '42057', 0),
(5, 'Pasangee Jayodhya Ilesinghe', 'Swimming Pool', '2026-10-29', 'Approved', '42153', 0),
(14, 'Pasangee Jayodhya Ilesinghe', 'Swimming Pool', '2026-12-24', 'Approved', '42153', 0),
(16, 'Pasangee Jayodhya Ilesinghe', 'Gymnasiums', '2026-12-08', 'Approved', '42153', 1);

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `match_date` varchar(50) DEFAULT NULL,
  `sport` varchar(50) DEFAULT NULL,
  `opponent` varchar(100) DEFAULT NULL,
  `score` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Completed',
  `venue` varchar(255) DEFAULT 'NSBM Playground',
  `outcome` varchar(100) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `match_date`, `sport`, `opponent`, `score`, `status`, `venue`, `outcome`) VALUES
(10, '2026-10-24', 'Cricket', 'CINEC campus', 'NSBM won by 4 wickets  223/4(46.2)', 'Completed', 'NSBM Playground', 'Win'),
(11, '2026-10-21', 'Badminton', 'SLIIT Campus', '14 : 7', 'Completed', 'Indoor Stadium', 'Gold Medal'),
(12, '2026-12-17', 'Cricket', 'SLIIT Campus', '', 'Upcoming', 'NSBM Playground', ''),
(13, '2026-12-08', 'Football', 'CINEC campus', '', 'Upcoming', 'NSBM Playground', ''),
(14, '2026-11-30', 'Cricket', 'Colombo Campus', '', 'Upcoming', 'NSBM Playground', ''),
(15, '2026-10-06', 'Football', 'Colombo Campus', '3-2', 'Completed', 'NSBM Playground', 'Win'),
(16, '2026-11-30', 'Cricket', 'Colombo Campus', '', 'Upcoming', 'NSBM Playground', '');

-- --------------------------------------------------------

--
-- Table structure for table `tournaments`
--

CREATE TABLE `tournaments` (
  `id` int(11) NOT NULL,
  `tournament_name` varchar(150) NOT NULL,
  `sport` varchar(100) NOT NULL DEFAULT '',
  `tournament_date` date NOT NULL,
  `venue` varchar(150) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournaments`
--

INSERT INTO `tournaments` (`id`, `tournament_name`, `sport`, `tournament_date`, `venue`, `description`) VALUES
(1, 'Inter-university cricket', 'Cricket', '2026-11-20', 'NSBM Playground', 'Witness an exciting clash between university cricket teams as they compete for victory and pride. Experience thrilling batting, bowling, and unforgettable moments on the field!'),
(2, 'NSBM U-Sports Athletic Championship', 'Athletic', '2026-12-04', 'NSBM Playground', 'Experience the excitement of the NSBM U-Sport Athletic Championship, where talented athletes compete with passion and determination. Celebrate speed, strength, and sportsmanship as students strive for excellence on the track and field.'),
(5, 'NSBM Inter-Faculty Badminton Tournament 2026', 'Badminton', '2026-12-07', 'Recreation Centre', 'Get ready for the NSBM Inter-Faculty Badminton Tournament 2026!\r\nThis upcoming campus event will bring together student-athletes from all faculties at NSBM Green University for a day of exhilarating matches and intense competition. Mark your calendars, bring your energy, and support your faculty as they smash their way to victory!');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_registrations`
--

CREATE TABLE `tournament_registrations` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `tournament_name` varchar(255) NOT NULL,
  `sport` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'Open Category',
  `registration_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Registered',
  `student_id` varchar(50) NOT NULL,
  `tournament_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament_registrations`
--

INSERT INTO `tournament_registrations` (`id`, `student_name`, `tournament_name`, `sport`, `category`, `registration_date`, `status`, `student_id`, `tournament_id`) VALUES
(14, 'Pasangee Jayodhya Ilesinghe', 'NSBM U-Sports Athletic Championship', 'Athletic', 'Under-19', '2026-11-01', 'Registered', '42153', 2),
(16, 'Pasangee Jayodhya Ilesinghe', 'Inter-university cricket', 'Cricket', 'Under-19', '2026-11-01', 'Registered', '42153', 1),
(17, 'Pasangee Jayodhya Ilesinghe', 'NSBM Inter-Faculty Badminton Tournament 2026', 'Badminton', 'Under-19', '2026-11-05', 'Registered', '42153', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `name` varchar(100) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`name`, `student_id`, `email`, `password`, `role`) VALUES
('Senuri Sisara', '41994', 'senui@gmail.com', '$2y$10$4jl7iICQaNNDk31MRKAre.YDoEqLKhyhLQ7N9E.0C4OovxNtFumH2', 'student'),
('Dilshi Kandage', '42057', 'dilshi@gmail.com', '$2y$10$rhx.VglUWaIhZUeoy0XGF.zW0CqXHANGDvsfk/ByreMQg3022XMXW', 'student'),
('Pasangee Jayodhya Ilesinghe', '42153', 'pasangee@gmail.com', '$2y$10$kMUKxkje4JvJA10QzzA/H.2gQIXcpgEKDkOfIfFCw8yzQV6bQAKbi', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `club_memberships`
--
ALTER TABLE `club_memberships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_club` (`student_id`,`club_id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `club_schedules`
--
ALTER TABLE `club_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `club_special_sessions`
--
ALTER TABLE `club_special_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility_bookings`
--
ALTER TABLE `facility_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tournaments`
--
ALTER TABLE `tournaments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tournament_registrations`
--
ALTER TABLE `tournament_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_tournament` (`student_id`,`tournament_id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `club_memberships`
--
ALTER TABLE `club_memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `club_schedules`
--
ALTER TABLE `club_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `club_special_sessions`
--
ALTER TABLE `club_special_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `facility_bookings`
--
ALTER TABLE `facility_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tournaments`
--
ALTER TABLE `tournaments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tournament_registrations`
--
ALTER TABLE `tournament_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `club_memberships`
--
ALTER TABLE `club_memberships`
  ADD CONSTRAINT `fk_membership_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_membership_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `facility_bookings`
--
ALTER TABLE `facility_bookings`
  ADD CONSTRAINT `fk_booking_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tournament_registrations`
--
ALTER TABLE `tournament_registrations`
  ADD CONSTRAINT `fk_reg_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reg_tournament` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
