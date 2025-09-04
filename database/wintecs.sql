
DROP TABLE IF EXISTS student;

CREATE TABLE `student` (
  `id` int NOT NULL,
  `refno` int NOT NULL,
  `emailid` varchar(255) NOT NULL,
  `sname` varchar(255) NOT NULL,
  `joindate` datetime NOT NULL,
  `about` text NOT NULL,
  `contact` varchar(255) NOT NULL,
  `jumuiya_id` int NOT NULL,
  `gender` varchar(20) NOT NULL,
  `marital_status` varchar(100) NOT NULL,
  `ubatizo` varchar(100) NOT NULL,
  `comonio` varchar(100) NOT NULL,
  `kipaimara` varchar(20) NOT NULL,
  `offering` varchar(20) NOT NULL,
  `delete_status` int(2)
);

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `refno`, `emailid`, `sname`, `joindate`, `about`, `contact`, `jumuiya_id`, `gender`, `marital_status`, `ubatizo`, `comonio`, `kipaimara`, `offering`, `delete_status`) VALUES
(36, 102499, 'winfrid31@gmail.com', 'winfrid Magnus Mapunda', '2025-07-12 00:00:00', '', '0793847871', 7, 'Male', 'Married', 'No', 'No', 'No', 'No', '0'),
(46, 575610, 'magetasijaona@gmail.com', 'Raphael Makundi', '2025-07-13 00:00:00', '', '0712201326', 0, 'Male', '', 'Yes', 'Yes', 'No', 'No', '0'),
(74, 569710, '', 'Dorris', '2025-07-26 00:00:00', '', '0793847871', 9, 'Female', 'Single', 'Yes', 'Yes', 'Yes', 'No', '0'),
(75, 102524, 'winfrid31@gmail.com', 'Winfrid Susa Mapunda', '2025-08-02 00:00:00', '', '0793847871', 98, 'Male', 'Single', 'Yes', 'No', 'No', 'No', '0'),
(76, 979857, 'smaziku@gmail.com', 'Neema Maurice', '2025-08-03 00:00:00', 'Amepata sakramenti nne', '0715262949', 100, 'Female', 'Single', 'Yes', 'Yes', 'Yes', 'No', '0'),
(77, 569899, 'smaziku@gmail.com', 'Neema Maurice', '2025-08-03 00:00:00', 'Amepata sakramenti nne', '0715262949', 100, 'Female', 'Single', 'Yes', 'Yes', 'Yes', 'No', '0');

