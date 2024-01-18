/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


CREATE TABLE `sectors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sector_name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sector_value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `sectors_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `sectors` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `sector_value` int(11) NOT NULL,
  `sector_name` varchar(255) DEFAULT NULL,
  `terms` enum('agreed','denied') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sectors` (`id`, `sector_name`, `parent_id`, `sector_value`) VALUES
(1, 'Manufacturing', NULL, 1),
(2, 'Service', NULL, 2),
(3, 'Other', NULL, 3),
(4, 'Printing', 1, 5),
(5, 'Food and Beverage', 1, 6),
(6, 'Textile and Clothing', 1, 7),
(7, 'Wood', 1, 8),
(8, 'Plastic and Rubber', 1, 9),
(9, 'Metalworking', 1, 11),
(10, 'Machinery', 1, 12),
(11, 'Furniture', 1, 13),
(12, 'Electronics and Optics', 1, 18),
(13, 'Construction materials', 1, 19),
(14, 'Transport and Logistics', 2, 21),
(15, 'Tourism', 2, 22),
(16, 'Business services', 2, 25),
(17, 'Information Technology and Telecommunications', 2, 28),
(18, 'Engineering', 2, 35),
(19, 'Translation services', 2, 141),
(20, 'Energy technology', 3, 29),
(21, 'Environment', 3, 33),
(22, 'Creative industries', 3, 37),
(23, 'Labelling and packaging printing', 4, 145),
(24, 'Advertising', 4, 148),
(25, 'Book/Periodicals printing', 4, 150),
(26, 'Milk & dairy products', 5, 39),
(27, 'Meat & meat products', 5, 40),
(28, 'Fish & fish products', 5, 42),
(29, 'Beverages', 5, 43),
(30, 'Bakery & confectionery products', 5, 342),
(31, 'Sweets & snack food', 5, 378),
(32, 'Other', 5, 437),
(33, 'Clothing', 6, 44),
(34, 'Textile', 6, 45),
(35, 'Wooden houses', 7, 47),
(36, 'Wooden building materials', 7, 51),
(37, 'Other (Wood)', 7, 337),
(38, 'Packaging', 8, 54),
(39, 'Plastic goods', 8, 556),
(40, 'Plastic processing technology', 8, 559),
(41, 'Plastic profiles', 8, 560),
(42, 'Construction of metal structures', 9, 67),
(43, 'Houses and buildings', 9, 263),
(44, 'Metal products', 9, 267),
(45, 'Metal works', 9, 542),
(46, 'Machinery equipment/tools', 10, 91),
(47, 'Metal structures', 10, 93),
(48, 'Machinery components', 10, 94),
(49, 'Maritime', 10, 97),
(50, 'Manufacture of machinery', 10, 224),
(51, 'Repair and maintenance service', 10, 227),
(52, 'Other', 10, 508),
(53, 'Kitchen', 11, 98),
(54, 'Project furniture', 11, 99),
(55, 'Living room', 11, 101),
(56, 'Outdoor', 11, 341),
(57, 'Bedroom', 11, 385),
(58, 'Bathroom/sauna', 11, 389),
(59, 'Children’s room', 11, 390),
(60, 'Office', 11, 392),
(61, 'Other (Furniture)', 11, 394),
(62, 'Air', 14, 111),
(63, 'Road', 14, 112),
(64, 'Water', 14, 113),
(65, 'Rail', 14, 114),
(66, 'Software, Hardware', 17, 121),
(67, 'Telecommunications', 17, 122),
(68, 'Programming, Consultancy', 17, 576),
(69, 'Data processing, Web portals, E-marketing', 17, 581),
(70, 'Plastic welding and processing', 40, 53),
(71, 'Blowing', 40, 55),
(72, 'Moulding', 40, 57),
(73, 'Forgings, Fasteners', 45, 62),
(74, 'MIG, TIG, Aluminium welding', 45, 66),
(75, 'Gas, Plasma, Laser cutting', 45, 69),
(76, 'CNC-machining', 45, 75),
(77, 'Ship repair and conversion', 49, 230),
(78, 'Boat/Yacht building', 49, 269),
(79, 'Aluminium and steel workboats', 49, 271);



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;