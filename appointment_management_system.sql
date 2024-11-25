-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2024 at 02:24 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `appointment_management_system`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `makeAppointment` (IN `p_stud_id` INT, IN `p_stud_fn` VARCHAR(255), IN `p_stud_m` VARCHAR(255), IN `p_stud_ln` VARCHAR(255), IN `p_item_string` VARCHAR(255), IN `p_amount` DECIMAL(10,2), IN `p_appt_date` DATE, IN `p_status` VARCHAR(20), IN `shift_type` ENUM("Morning","Afternoon"))   BEGIN
	DECLARE new_qr_id INT;
	DECLARE new_shift_id INT;
	DECLARE EXIT HANDLER for SQLEXCEPTION
	BEGIN
		ROLLBACK;
	END;
	START TRANSACTION;
		INSERT INTO qr_data(item_string, amount, stud_ln, stud_fn, stud_m)
		SELECT 
		    p_item_string, 
		    p_amount, 
		    s.stud_ln, 
		    s.stud_fn, 
		    s.stud_m
		From student s
		WHERE s.stud_id = p_stud_id;
		SET new_qr_id = LAST_INSERT_ID();
		
		IF shift_type = 'Morning' THEN
			INSERT INTO shifts(shift_type, start_time, end_time)
			VALUES(shift_type, "07:00:00", "11:00:00");
			SET new_shift_id = LAST_INSERT_ID();
		ELSEIF shift_type = 'Afternoon' Then
			INSERT INTO shifts(shift_type, start_time, end_time)
			VALUES(shift_type, "11:00:00", "17:00:00");
			SET new_shift_id = LAST_INSERT_ID();
		END IF;
		
		INSERT INTO appointments(appointment_date, `status`, qr_id, stud_id, shift_id)
		VALUES (p_appt_date, p_status, new_qr_id, p_stud_id, new_shift_id);
	COMMIT;
END$$

CREATE DEFINER=`christian`@`localhost` PROCEDURE `makeNotif` ()   BEGIN
	INSERT INTO notifications (notify_time, stud_id, appointment_id)
	SELECT 
		DATE_SUB(CONCAT(a.appointment_date, ' ', s.start_time), INTERVAL 12 hour),
		a.stud_id, 
		a.appointment_id
	FROM appointments a
	JOIN shifts s ON a.shift_id = s.shift_id
	WHERE DATE_SUB(CONCAT(a.appointment_date, ' ', s.start_time), INTERVAL 12 day) <= NOW()
	AND NOT EXISTS(SELECT 1 FROM notifications n WHERE n.appointment_id = a.appointment_id);
END$$

CREATE DEFINER=`christian`@`localhost` PROCEDURE `queue_init` ()   BEGIN
	DECLARE current_datetime DATETIME DEFAULT NOW();
	DECLARE cur_date DATE DEFAULT CURDATE();
	DECLARE cur_time TIME DEFAULT CURTIME();  -- Correct type for time comparison
	DECLARE queue_pos INT DEFAULT 1;  -- Variable to simulate the queue position
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
	END;
    
	START TRANSACTION;
		SET @queue_pos := 0;
		IF cur_time < '11:00:00' THEN
			INSERT INTO queue(appointment_id, queue_position)
			SELECT a.appointment_id, (@queue_pos := @queue_pos + 1) AS queue_position
			FROM appointments a
			JOIN shifts s ON s.shift_id = a.shift_id
			WHERE a.appointment_date = cur_date
			AND s.shift_type = 'Morning'
			AND a.status = "Waiting";
		ELSEIF cur_time >= '11:00:00' THEN
			INSERT INTO queue(appointment_id, queue_position)
			SELECT a.appointment_id, (@queue_pos := @queue_pos + 1) AS queue_position
			FROM appointments a
			JOIN shifts s ON s.shift_id = a.shift_id
			WHERE a.appointment_date = cur_date
			AND s.shift_type = 'Afternoon'
			AND a.status = "Waiting";
		END IF;

		UPDATE appointments
		SET status = 'Queued'
		WHERE appointment_id IN (
			SELECT appointment_id
			FROM queue
			WHERE appointment_id IN (
				SELECT a.appointment_id
				FROM appointments a
				JOIN shifts s ON s.shift_id = a.shift_id
				WHERE a.appointment_date = cur_date
				AND ((s.shift_type = 'Morning' AND cur_time < '11:00:00') OR 
					(s.shift_type = 'Afternoon' AND cur_time >= '11:00:00'))
				AND a.status = "Waiting"
			)
		);
	    COMMIT;
END$$

CREATE DEFINER=`christian`@`localhost` PROCEDURE `requeue` ()   BEGIN
	declare cur_time time default curtime();
	declare cur_date date default curdate();
	
	update 	shifts s
	SET 	s.shift_type = 'Afternoon', start_time = '11:00:00', end_time = '17:00:00'
	WHERE 	s.shift_id = (select shift_id from appointments a where  appointment_date = cur_date)
	AND	s.end_time > cur_time; 
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `transaction_ins` (IN `p_stud_id` INT, IN `p_stud_ln` VARCHAR(25), IN `p_stud_fn` VARCHAR(25), IN `p_stud_m` VARCHAR(25), IN `p_cashier_id` INT, IN `p_amount` INT, OUT `new_transaction_id` INT)   BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
	END;
	START TRANSACTION;
	
		INSERT INTO transactions(amount)
		VALUES (p_amount);
			
		SET new_transaction_id = LAST_INSERT_ID();
		
		INSERT INTO student_transaction(transaction_id, stud_id)
		VALUES(new_transaction_id, p_stud_id);
		
		INSERT INTO cashier_transaction(transaction_id, cashier_id)
		Values(new_transaction_id, p_cashier_id);
		
	COMMIT;

END$$

CREATE DEFINER=`christian`@`localhost` PROCEDURE `updateQueuePositionAfterDelete` ()   BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE deleted_position INT;
	declare deleted_id int;
	DECLARE cur_queue_position INT;
	DECLARE cur_appointment_id INT;
	DECLARE new_position INT DEFAULT 1;

	-- Declare the cursor
	DECLARE queue_cursor CURSOR FOR
	SELECT a.appointment_id, q.queue_position
	FROM appointments a
	Join queue q on a.appointment_id = q.appointment_id
	WHERE q.queue_position > deleted_position
	ORDER BY q.queue_position ASC;

	-- Declare a CONTINUE HANDLER for the cursor to handle when there are no more rows to process
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	-- Get the queue_position of the appointment to be deleted
	SELECT MIN(queue_position) INTO deleted_position
	FROM queue; 
	
	set deleted_id = (select appointment_id from queue where queue_position = deleted_position);
	-- Delete the appointment
	DELETE FROM queue WHERE queue_position = 1;
	
	-- add code to update appointment enum to completed!
	UPDATE appointments
	SET `status` = 'Completed'
	WHERE appointment_id = deleted_id;
	-- Open the cursor
	OPEN queue_cursor;

	-- Loop through the rows in the cursor
	read_loop: LOOP
		FETCH queue_cursor INTO cur_appointment_id, cur_queue_position;
		IF done THEN
			LEAVE read_loop;
		END IF;

		-- Update the queue_position for each appointment
		UPDATE queue
		SET queue_position = new_position
		WHERE appointment_id = cur_appointment_id;
		
		
		-- Increment the new position
		SET new_position = new_position + 1;
	END LOOP;

	-- Close the cursor
	CLOSE queue_cursor;
END$$

CREATE DEFINER=`christian`@`localhost` PROCEDURE `voidAppointment` ()   BEGIN
		DECLARE cur_date DATE DEFAULT CURDATE();
		
		update 	appointments a
		set	a.status = 'Void'
		WHERE 	a.appointment_date < cur_date
		and	a.attended = 0;
	END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `status` enum('Waiting','Completed','Queued','Cancelled','Void') NOT NULL DEFAULT 'Waiting',
  `attended` tinyint(1) NOT NULL DEFAULT 0,
  `qr_id` int(10) UNSIGNED NOT NULL,
  `stud_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `appointment_date`, `status`, `attended`, `qr_id`, `stud_id`, `shift_id`) VALUES
(118, '2024-11-25', 'Queued', 0, 107, 2, 81),
(119, '2024-11-26', 'Cancelled', 0, 108, 2, 82),
(125, '2024-11-18', 'Void', 0, 114, 2, 88);

--
-- Triggers `appointments`
--
DELIMITER $$
CREATE TRIGGER `after_appointment_delete` AFTER DELETE ON `appointments` FOR EACH ROW BEGIN
    -- Check if there are no more appointments associated with the shift
    DELETE FROM shifts
    WHERE shift_id = OLD.shift_id
    AND NOT EXISTS (
        SELECT 1
        FROM appointments
        WHERE shift_id = OLD.shift_id
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cashiers`
--

CREATE TABLE `cashiers` (
  `cashier_id` int(11) NOT NULL,
  `cashier_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cashiers`
--

INSERT INTO `cashiers` (`cashier_id`, `cashier_name`, `password`) VALUES
(1, 'Christian Bola', '123'),
(2, 'asd', 'pass');

-- --------------------------------------------------------

--
-- Table structure for table `cashier_transaction`
--

CREATE TABLE `cashier_transaction` (
  `cashier_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cashier_transaction`
--

INSERT INTO `cashier_transaction` (`cashier_id`, `transaction_id`) VALUES
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29);

-- --------------------------------------------------------

--
-- Stand-in structure for view `getnotification`
-- (See below for the actual view)
--
CREATE TABLE `getnotification` (
`notif_id` int(11)
,`notify_time` datetime
,`is_sent` tinyint(1)
,`created_at` timestamp
,`appointment_id` int(11)
,`stud_id` int(11)
,`appointment_date` date
,`start_time` time
,`stud_email` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `getuserappointments`
-- (See below for the actual view)
--
CREATE TABLE `getuserappointments` (
`appointment_id` int(11)
,`appointment_date` date
,`status` enum('Waiting','Completed','Queued','Cancelled','Void')
,`qr_id` int(10) unsigned
,`stud_id` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `item_price`) VALUES
(1234, 'Miscellaneous Fee', 4500),
(5679, 'Stageplay college 2024', 350),
(9889, 'ID', 150),
(10003, 'Lace', 150);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `notify_time` datetime NOT NULL,
  `is_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `appointment_id` int(11) NOT NULL,
  `stud_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notif_id`, `notify_time`, `is_sent`, `created_at`, `appointment_id`, `stud_id`) VALUES
(1232, '2024-11-24 19:00:00', 1, '2024-11-25 00:08:16', 118, 2),
(1233, '2024-11-25 23:00:00', 1, '2024-11-25 00:08:16', 119, 2),
(1235, '2024-11-25 19:00:00', 0, '2024-11-25 01:16:30', 125, 2);

-- --------------------------------------------------------

--
-- Table structure for table `paid_items`
--

CREATE TABLE `paid_items` (
  `transaction_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paid_items`
--

INSERT INTO `paid_items` (`transaction_id`, `item_id`) VALUES
(25, 1234),
(25, 5679),
(25, 9889),
(26, 1234),
(26, 5679),
(26, 9889),
(27, 1234),
(27, 5679),
(27, 9889),
(28, 1234),
(28, 5679),
(28, 9889),
(29, 1234),
(29, 5679),
(29, 9889);

-- --------------------------------------------------------

--
-- Table structure for table `qr_data`
--

CREATE TABLE `qr_data` (
  `qr_id` int(10) UNSIGNED NOT NULL,
  `item_string` varchar(255) DEFAULT 'NULL',
  `amount` decimal(10,2) NOT NULL,
  `stud_ln` varchar(50) NOT NULL,
  `stud_fn` varchar(50) NOT NULL,
  `stud_m` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qr_data`
--

INSERT INTO `qr_data` (`qr_id`, `item_string`, `amount`, `stud_ln`, `stud_fn`, `stud_m`) VALUES
(27, '1234', 1600.00, 'Alia', 'Christian', NULL),
(28, '1234', 1600.00, 'Alia', 'Christian', NULL),
(29, '1234', 1600.00, 'Alia', 'Christian', NULL),
(30, '1234', 1600.00, 'Alia', 'Christian', NULL),
(31, '1234', 1600.00, 'Alia', 'Christian', NULL),
(32, '1234', 1600.00, 'Alia', 'Christian', NULL),
(33, '1234', 1600.00, 'Alia', 'Christian', NULL),
(34, '1234', 1600.00, 'Alia', 'Christian', NULL),
(35, '1234', 1600.00, 'Alia', 'Christian', NULL),
(36, '1234', 1600.00, 'Alia', 'Christian', NULL),
(37, '1234', 1600.00, 'Alia', 'Christian', NULL),
(38, '1234', 1600.00, 'Alia', 'Christian', NULL),
(39, '1234', 1600.00, 'Alia', 'Christian', NULL),
(40, '1234', 1600.00, 'Alia', 'Christian', NULL),
(41, '1234', 1600.00, 'Alia', 'Christian', NULL),
(42, '1234', 1600.00, 'Alia', 'Christian', NULL),
(43, '1234', 1600.00, 'Alia', 'Christian', NULL),
(44, '1234', 1600.00, 'Alia', 'Christian', NULL),
(45, '1234', 1600.00, 'Alia', 'Christian', NULL),
(46, '1234', 1600.00, 'Alia', 'Christian', NULL),
(47, '1234', 1600.00, 'Alia', 'Christian', NULL),
(48, '1234', 1600.00, 'Alia', 'Christian', NULL),
(49, '1234', 1600.00, 'Alia', 'Christian', NULL),
(50, '1234', 1600.00, 'Alia', 'Christian', NULL),
(51, '1234', 1600.00, 'Alia', 'Christian', NULL),
(52, '1234', 1600.00, 'Alia', 'Christian', NULL),
(53, '1234', 1600.00, 'Alia', 'Christian', NULL),
(54, '1234', 1600.00, 'Alia', 'Christian', NULL),
(55, '1234', 1600.00, 'Alia', 'Christian', NULL),
(56, '1234', 1600.00, 'Alia', 'Christian', NULL),
(57, '1234', 1600.00, 'Alia', 'Christian', NULL),
(58, '1234', 1600.00, 'Alia', 'Christian', NULL),
(59, '1234', 1600.00, 'Alia', 'Christian', NULL),
(60, '1234', 1600.00, 'Alia', 'Christian', NULL),
(61, '1234', 1600.00, 'Alia', 'Christian', NULL),
(62, '1234', 1600.00, 'Alia', 'Christian', NULL),
(63, '1234', 1600.00, 'Alia', 'Christian', NULL),
(64, '1234', 1600.00, 'Alia', 'Christian', NULL),
(65, '1234', 1600.00, 'Alia', 'Christian', NULL),
(66, '1234', 1600.00, 'Alia', 'Christian', NULL),
(67, '1234', 1600.00, 'Alia', 'Christian', NULL),
(68, '1234', 1600.00, 'Alia', 'Christian', NULL),
(69, '1234 5679', 1600.00, 'Alia', 'Christian', NULL),
(70, '1234 5679', 1600.00, 'Alia', 'Christian', NULL),
(71, '1234 5679', 1600.00, 'Alia', 'Christian', NULL),
(72, '1234 5679', 1600.00, 'Alia', 'Christian', NULL),
(73, '1234 5679', 1600.00, 'Alia', 'Christian', NULL),
(74, '1234 5679', 1600.00, 'Alia', 'Christian', NULL),
(75, '1234 5679', 1600.00, 'Alia', 'Christian', NULL),
(76, '1234 5679', 1600.00, 'Alia', 'Christian', NULL),
(77, '1234 5679', 1600.00, 'Alia', 'Christian', NULL),
(78, '1234 5679 9889', 1600.00, 'Alia', 'Christian', NULL),
(79, '1234 5679 9889', 1600.00, 'Alia', 'Christian', NULL),
(80, '1234 5679 9889', 1600.00, 'Alia', 'Christian', NULL),
(81, '1234 5679 9889', 1600.00, 'Alia', 'Christian', NULL),
(82, '1234 5679 9889', 1600.00, 'Alia', 'Christian', NULL),
(83, '1234 5679 9889', 1600.00, 'Alia', 'Christian', NULL),
(84, '1234 5679 9889', 1600.00, 'Alia', 'Christian', NULL),
(85, '1234', 1600.00, 'Bola', 'Christian', NULL),
(86, '10003', 1600.00, 'Alia', 'Christian', NULL),
(87, '5679 9889', 1600.00, 'Alia', 'Christian', NULL),
(88, '1234', 1600.00, 'Alia', 'Christian', NULL),
(89, '1234', 1600.00, 'Alia', 'Christian', 'Aliaasdadas'),
(90, '1234', 1600.00, 'Alia', 'Christian', 'Aliaasdadas'),
(91, '1234', 1600.00, 'Bola', 'Christian', 'Alia'),
(92, '1234', 1600.00, 'Bola', 'Christian', 'Alia'),
(93, '1234', 1600.00, 'Bola', 'Christian', 'Alia'),
(94, '1234', 1600.00, 'Bola', 'Christian', 'Alia'),
(95, '1234', 1600.00, 'Bola', 'Christian', 'Alia'),
(96, '1234', 1600.00, 'Bola', 'Christian', 'Alia'),
(97, '1234', 1600.00, 'Bola', 'Christian', 'Alia'),
(98, '1234', 1600.00, 'Bola', 'Christian', 'Alia'),
(99, '1234', 1600.00, 'Bola', 'Christian', 'Alia'),
(100, '1234', 1600.00, 'Alia', 'Christian', 'A'),
(101, '1234', 1600.00, 'Alia', 'Christian', 'A'),
(102, '1234', 1600.00, 'Alia', 'Christian', 'A'),
(103, '1234', 1600.00, 'Alia', 'Christian', 'A'),
(104, '1234', 1600.00, 'Bola', 'Christian', 'A'),
(105, '1234', 1600.00, 'Bola', 'Christian', 'Alia'),
(106, '1234', 1600.00, 'Bola', 'Christian', 'A'),
(107, '1234', 1600.00, 'Roque', 'Jamie', 'G'),
(108, '1234 5679 9889 10003', 1600.00, 'Roque', 'Jamie', 'G'),
(109, '5679', 1600.00, 'Bola', 'Christian', 'A'),
(110, '1234', 1600.00, 'Bola', 'Christian', 'A'),
(111, '', 1600.00, 'Bola', 'Christian', 'A'),
(112, '', 1500.00, 'Bola', 'Christian', 'A'),
(113, '', 1600.00, 'Bola', 'Christian', 'A'),
(114, '1234', 1600.00, 'Roque', 'Jamie', 'G');

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `queue_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `queue_position` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`queue_id`, `appointment_id`, `queue_position`) VALUES
(302, 118, 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `queueinformation`
-- (See below for the actual view)
--
CREATE TABLE `queueinformation` (
`stud_id` int(11)
,`last_name` varchar(50)
,`first_name` varchar(50)
,`middle_name` varchar(50)
,`qr_id` int(10) unsigned
,`items` varchar(255)
,`amount` decimal(10,2)
,`appointment_id` int(11)
,`appointment_date` date
,`shift_type` enum('Morning','Afternoon')
,`start_time` time
,`end_time` time
,`status` enum('Waiting','Completed','Queued','Cancelled','Void')
,`position` int(11) unsigned
,`queue_length` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `sec_id` int(11) NOT NULL,
  `sec_no` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`sec_id`, `sec_no`) VALUES
(1, 3001),
(2, 3002),
(3, 3003),
(4, 3004),
(5, 3005),
(6, 3006),
(7, 3007),
(8, 3008),
(9, 3009),
(10, 3010),
(11, 3011),
(12, 3012),
(13, 3013),
(14, 3014),
(15, 3015),
(16, 3016),
(17, 3017),
(18, 3018),
(19, 3019),
(20, 3020),
(21, 3021),
(22, 3022),
(23, 3023),
(24, 3024),
(25, 3025);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL,
  `shift_type` enum('Morning','Afternoon') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`shift_id`, `shift_type`, `start_time`, `end_time`) VALUES
(81, 'Afternoon', '11:00:00', '17:00:00'),
(82, 'Afternoon', '11:00:00', '17:00:00'),
(88, 'Morning', '07:00:00', '11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `stud_id` int(11) NOT NULL,
  `stud_ln` varchar(25) DEFAULT NULL,
  `stud_fn` varchar(25) DEFAULT NULL,
  `stud_m` varchar(25) DEFAULT NULL,
  `stud_password` varchar(50) DEFAULT NULL,
  `stud_email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`stud_id`, `stud_ln`, `stud_fn`, `stud_m`, `stud_password`, `stud_email`) VALUES
(1, 'Bola', 'Christian', 'A', '123', 'loquezchristian@gmail.com'),
(2, 'Roque', 'Jamie', 'G', '123', 'amieee@gmail.com'),
(3, 'DOE', 'LOE', 'Row', '123', 'adads@gmail.com'),
(123456789, 'Doe', 'Koe', 'Loe', '123', 'doe@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `student_section`
--

CREATE TABLE `student_section` (
  `sec_id` int(11) NOT NULL,
  `stud_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_transaction`
--

CREATE TABLE `student_transaction` (
  `transaction_id` int(11) NOT NULL,
  `stud_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_transaction`
--

INSERT INTO `student_transaction` (`transaction_id`, `stud_id`) VALUES
(14, 123456789),
(15, 123456789),
(16, 123456789),
(17, 123456789),
(18, 123456789),
(19, 123456789),
(20, 123456789),
(21, 123456789),
(22, 123456789),
(23, 123456789),
(24, 123456789),
(25, 123456789),
(26, 123456789),
(27, 123456789),
(28, 123456789),
(29, 123456789);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `amount`, `datetime`) VALUES
(14, 1600, '2024-11-01 12:27:31'),
(15, 1600, '2024-11-01 12:27:54'),
(16, 1600, '2024-11-01 12:31:29'),
(17, 1600, '2024-11-01 12:33:36'),
(18, 1600, '2024-11-01 12:34:26'),
(19, 1600, '2024-11-01 12:35:21'),
(20, 1600, '2024-11-01 12:48:27'),
(21, 1600, '2024-11-01 12:48:57'),
(22, 1600, '2024-11-01 13:21:05'),
(23, 1600, '2024-11-01 13:21:51'),
(24, 1600, '2024-11-01 13:22:37'),
(25, 1600, '2024-11-01 13:28:28'),
(26, 1600, '2024-11-01 13:33:03'),
(27, 1600, '2024-11-01 16:07:15'),
(28, 1600, '2024-11-01 16:49:47'),
(29, 1600, '2024-11-01 16:53:52');

-- --------------------------------------------------------

--
-- Structure for view `getnotification`
--
DROP TABLE IF EXISTS `getnotification`;

CREATE ALGORITHM=UNDEFINED DEFINER=`christian`@`localhost` SQL SECURITY DEFINER VIEW `getnotification`  AS SELECT `n`.`notif_id` AS `notif_id`, `n`.`notify_time` AS `notify_time`, `n`.`is_sent` AS `is_sent`, `n`.`created_at` AS `created_at`, `n`.`appointment_id` AS `appointment_id`, `n`.`stud_id` AS `stud_id`, `a`.`appointment_date` AS `appointment_date`, `s`.`start_time` AS `start_time`, `st`.`stud_email` AS `stud_email` FROM (((`notifications` `n` join `appointments` `a` on(`n`.`appointment_id` = `a`.`appointment_id`)) join `shifts` `s` on(`a`.`shift_id` = `s`.`shift_id`)) join `student` `st` on(`st`.`stud_id` = `a`.`stud_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `getuserappointments`
--
DROP TABLE IF EXISTS `getuserappointments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `getuserappointments`  AS SELECT `a`.`appointment_id` AS `appointment_id`, `a`.`appointment_date` AS `appointment_date`, `a`.`status` AS `status`, `a`.`qr_id` AS `qr_id`, `a`.`stud_id` AS `stud_id` FROM `appointments` AS `a` ;

-- --------------------------------------------------------

--
-- Structure for view `queueinformation`
--
DROP TABLE IF EXISTS `queueinformation`;

CREATE ALGORITHM=UNDEFINED DEFINER=`christian`@`localhost` SQL SECURITY DEFINER VIEW `queueinformation`  AS SELECT `a`.`stud_id` AS `stud_id`, `qd`.`stud_ln` AS `last_name`, `qd`.`stud_fn` AS `first_name`, `qd`.`stud_m` AS `middle_name`, `qd`.`qr_id` AS `qr_id`, `qd`.`item_string` AS `items`, `qd`.`amount` AS `amount`, `a`.`appointment_id` AS `appointment_id`, `a`.`appointment_date` AS `appointment_date`, `s`.`shift_type` AS `shift_type`, `s`.`start_time` AS `start_time`, `s`.`end_time` AS `end_time`, `a`.`status` AS `status`, `q`.`queue_position` AS `position`, count(distinct `q`.`queue_id`) AS `queue_length` FROM (((`qr_data` `qd` join `appointments` `a` on(`a`.`qr_id` = `qd`.`qr_id`)) join `shifts` `s` on(`s`.`shift_id` = `a`.`shift_id`)) left join `queue` `q` on(`q`.`appointment_id` = `a`.`appointment_id`)) GROUP BY `a`.`appointment_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `qr_id` (`qr_id`),
  ADD KEY `stud_id` (`stud_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `cashiers`
--
ALTER TABLE `cashiers`
  ADD PRIMARY KEY (`cashier_id`);

--
-- Indexes for table `cashier_transaction`
--
ALTER TABLE `cashier_transaction`
  ADD KEY `cashier_transaction_ibfk_1` (`cashier_id`),
  ADD KEY `cashier_transaction_ibfk_2` (`transaction_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `student_id` (`stud_id`);

--
-- Indexes for table `paid_items`
--
ALTER TABLE `paid_items`
  ADD KEY `std_id` (`transaction_id`),
  ADD KEY `paid_items_ibfk_1` (`item_id`);

--
-- Indexes for table `qr_data`
--
ALTER TABLE `qr_data`
  ADD PRIMARY KEY (`qr_id`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`queue_id`),
  ADD KEY `appointmentID` (`appointment_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`sec_id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`shift_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`stud_id`);

--
-- Indexes for table `student_section`
--
ALTER TABLE `student_section`
  ADD KEY `student_section_ibfk_1` (`sec_id`),
  ADD KEY `student_section_ibfk_2` (`stud_id`);

--
-- Indexes for table `student_transaction`
--
ALTER TABLE `student_transaction`
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `stud_id` (`stud_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `cashiers`
--
ALTER TABLE `cashiers`
  MODIFY `cashier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10022;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1236;

--
-- AUTO_INCREMENT for table `qr_data`
--
ALTER TABLE `qr_data`
  MODIFY `qr_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `sec_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`qr_id`) REFERENCES `qr_data` (`qr_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`stud_id`) REFERENCES `student` (`stud_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cashier_transaction`
--
ALTER TABLE `cashier_transaction`
  ADD CONSTRAINT `cashier_transaction_ibfk_1` FOREIGN KEY (`cashier_id`) REFERENCES `cashiers` (`cashier_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cashier_transaction_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`stud_id`) REFERENCES `student` (`stud_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `paid_items`
--
ALTER TABLE `paid_items`
  ADD CONSTRAINT `paid_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `paid_items_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `queue`
--
ALTER TABLE `queue`
  ADD CONSTRAINT `queue_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_section`
--
ALTER TABLE `student_section`
  ADD CONSTRAINT `student_section_ibfk_1` FOREIGN KEY (`sec_id`) REFERENCES `section` (`sec_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_section_ibfk_2` FOREIGN KEY (`stud_id`) REFERENCES `student` (`stud_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_transaction`
--
ALTER TABLE `student_transaction`
  ADD CONSTRAINT `student_transaction_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_transaction_ibfk_2` FOREIGN KEY (`stud_id`) REFERENCES `student` (`stud_id`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`christian`@`localhost` EVENT `callQueueInit` ON SCHEDULE EVERY 1 SECOND STARTS '2024-11-21 14:58:52' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
	    call queue_init();
	END$$

CREATE DEFINER=`christian`@`localhost` EVENT `notify_upcoming_appointments` ON SCHEDULE EVERY 1 SECOND STARTS '2024-11-23 15:31:55' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
	call makeNotif();
END$$

CREATE DEFINER=`christian`@`localhost` EVENT `requeueCheck` ON SCHEDULE EVERY 1 SECOND STARTS '2024-11-25 08:08:22' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
	 CALL requeue();
	 CALL voidAppointment();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
