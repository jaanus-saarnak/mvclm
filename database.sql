-- MvcLM v1.0 - MVC Framework with Login Manager
--
-- Release date: 2021.09.30
--
-- LICENSE AND DISCLAIMER
--
-- THIS SOFTWARE CAN BE USED ON ONE DOMAIN FOR TESTING USE ONLY.
-- ALL OTHER RIGHTS ARE RESERVED.
--
-- THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
-- IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
-- FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
-- AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
-- LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
-- OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
-- SOFTWARE.
--
-- Software website: https://www.MvcLM.com


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `login_ip`
--

CREATE TABLE `login_ip` (
  `id` int(20) NOT NULL,
  `uid` int(20) DEFAULT NULL,
  `ip` varchar(100) NOT NULL,
  `time_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `password` enum('correct','wrong') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `accesstoken` varchar(200) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(20) NOT NULL,
  `username` varchar(16) NOT NULL,
  `password_hash` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `registered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login_ip` varchar(20) DEFAULT NULL,
  `logins_cookie_stats` varchar(2000) DEFAULT NULL,
  `status` int(10) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `password_hash`, `email`, `registered`, `last_login`, `last_login_ip`, `logins_cookie_stats`, `status`) VALUES
(1, 'admin', '$2y$10$Co3hrqFLQAP3yvCp62iE8eR2I//9EfD1ySuD66VsLJEk3CS1BU0N6', 'admin@example.com', '2020-05-14 17:19:57', '2021-03-15 22:46:35', '127.0.0.1', '1,3,5,6,7,8,9,2,4,10,11,12', 1),
(2, 'samantha', '$2y$10$lKv2kYWEpqqKQkbXS1W4becXg.2ZKfOZ.Iy3ZLfHO4.Vrtefxbf9K', 'samantha@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9', 1),
(3, 'anthony', '$2y$10$OmZERBa0Az/Po3e8jWVEmOdIJT6OkEMGHiJtKBknEg7G2QD23tW56', 'anthony@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9,2', 1),
(4, 'jennifer', '$2y$10$35t91fTZWfPU67CV9Nvh2e5N.FPSZx44KG7aZDPbH/KcfUIumuRr2', 'jennifer@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9,2', 1),
(5, 'james', '$2y$10$QfXXTnT1Rk6A0UgvRymCyO/mux8Hv93wv9eZ8k8AHjhKEXFpXIN2S', 'james@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9,2,4', 1),
(6, 'susan', '$2y$10$1zkUmTWfVvzjykCpmyFnKewwcRfD.iV0XWfLa8wdTOi36OtaJA8yy', 'susan@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9,2,4', 1),
(7, 'robert', '$2y$10$weCFpm1yyg1aSyotPplG1uCdG0eRjHHfaXHor8HnpJwbUx32fanQy', 'robert@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9,2,4', 1),
(8, 'laura', '$2y$10$jFWZLf.wzTOtQX9HSuygl.QYsCw/LE7YrMVbyEEGbrDvaMnlNg6Z2', 'laura@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9,2,4', 1),
(9, 'paul', '$2y$10$tI8K18i93eME3x6PCtdYf.UMl9DFE2PBCdrJk9wqWAEf8PW9kapYy', 'paul@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9,2,4', 1),
(10, 'megan', '$2y$10$YQ7mzLv9Nzy4BD1ruUIL7eq0L.tK78a.jiXn2rhhyK52Dvox1aXgS', 'megan@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9,2,4', 1),
(11, 'henry', '$2y$10$8RQBLZ84oC91jljvTNgjnOTxASi54VP/Dj/bWntwwZo3zlunimwBW', 'henry@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9,2,4,10', 1),
(12, 'stacy', '$2y$10$086zvN8gs8MaorDR4o8qvu9vBYf/0X9IQs.KFuvEBnT7tjLhhC006', 'stacy@example.com', '2020-05-14 17:19:57', '2020-05-15 11:25:42', '127.0.0.1', '1,3,5,6,7,8,9,2,4,10,11', 1);

--
-- Indexes for table `login_ip`
--
ALTER TABLE `login_ip`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for table `login_ip`
--
ALTER TABLE `login_ip`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
