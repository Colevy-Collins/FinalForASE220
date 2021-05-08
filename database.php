<?php
require(__DIR__.'/API/lib_db.php');
header('Content-type: application/json');
/*
// events
$pdo->query("CREATE TABLE events (
  ID int(11) NOT NULL,
  title varchar(140) CHARACTER SET utf8 NOT NULL,
  content text CHARACTER SET utf8 DEFAULT NULL,
  date datetime NOT NULL,
  capacity int(7) NOT NULL,
  user_ID int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->query("INSERT INTO events (ID, title, content, date, capacity, user_ID) VALUES
(1, 'The first event', 'we do stuff', '2021-05-03 07:06:14', 50, 1),
(2, 'this is the second post', 'yoyyoyo', '0000-00-00 00:00:00', 40, 3),
(3, 'this is the second post edited', 'yoyyoyo', '0000-00-00 00:00:00', 40, 3),
(8, 'the first posts v2', 'we do stuff', '2021-05-01 15:11:00', 50, 8),
(12, 'biteme', 'we did stuff', '0000-00-00 00:00:00', 50, 16)");

$pdo->query("ALTER TABLE events
  ADD PRIMARY KEY (ID)");

$pdo->query("ALTER TABLE events
  MODIFY ID int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;");
 */
// registation
/*
$pdo->query("CREATE TABLE registration (
  event_ID int(11) NOT NULL,
  user_ID int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->query("INSERT INTO registration (event_ID, user_ID) VALUES
(1, 3),
(10, 3),
(12, 17);");

$pdo->query("ALTER TABLE registration
  ADD PRIMARY KEY (event_ID,user_ID);");
*/
//users
$pdo->query("CREATE TABLE users (
  ID int(11) NOT NULL,
  email varchar(64) NOT NULL,
  password varchar(96) NOT NULL,
  firstname varchar(48) DEFAULT NULL,
  lastname varchar(48) DEFAULT NULL,
  is_admin int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$pdo->query('INSERT INTO users (ID, email, password, firstname, lastname, is_admin) VALUES
(3, \'c@c\', \'$2y$10$.UHjiAmARptU9QADXNRXAuBCH5R70m9sbTbiXmCPtfZoDRAc6o3ma\', \'joe\', \'mama\', 1),
(4, \'d@d\', \'$2y$10$sHWOjEoJZ9yNxcDLHD6eE.wzMTiUTGUCWhL4YGFO7DjPoJ7QEyW..\', NULL, NULL, 0),
(5, \'s@s\', \'$2y$10$UH6GEU/CRm0yPUDmDK1dyO7BksgZCqsN3UFkE1e1tsTBZSWFFxeRG\', NULL, NULL, 0),
(6, \'a@a\', \'$2y$10$h6UEqY6a5ZIMoNIPMjE4ceWfaa/tn6O8G0Y49cO2BHdx.XmLmOsMS\', \'asda\', \'asdas\', 1),
(7, \'\', \'$2y$10$hmSP8CziHhPx5MmmupdIM.o8rsstascNvJiYO5Ds8w/VoBhHjBT8S\', \'dfs\', \'adfadf\', 0),
(8, \'c@e\', \'$2y$10$vGNwpKbhHcETBfUQF3YJnusjnv4uCib37hRWMLbNuaCHEqaeGq.su\', NULL, NULL, 0),
(9, \'as@as\', \'$2y$10$WjeKiR/ruVb3KJ8s206k/un/0/m.XNvqgHk1QcaT0IaRMvTeuVL6y\', NULL, NULL, 0),
(16, \'1@2\', \'$2y$10$ExyIplXevLm2d1KEZPOw5OpuKb8e.gcmbYHomPpKRwcyKthvRFjQW\', \'me\', \'you\', 0);');

$pdo->query("ALTER TABLE users
  ADD PRIMARY KEY (ID);");

$pdo->query("ALTER TABLE users
  MODIFY ID int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;");

