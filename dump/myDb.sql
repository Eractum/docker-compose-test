SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";  

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `participants` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `mailto` varchar(40) NOT NULL,
  `position` varchar(20) NOT NULL,
  `shares_amount` int NOT NULL,
  `start_date` int NOT NULL,
  `parent_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `participants` AUTO_INCREMENT=1;

INSERT INTO `participants` (	
	`firstname`, 
	`lastname`, 
	`mailto`, 
	`position`,
	`shares_amount`,
	`start_date`,
	`parent_id`
) 
VALUES (
	'Mike',
	'Patterson',
	'email:mike_pat@example.org',
	'president',
	10000,
	1273449600,
	0
);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
