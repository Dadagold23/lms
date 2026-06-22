-- YouTube video links for all 16 courses
-- video_path stores the YouTube URL; renderIntroVideo() in helpers.php handles embed

SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE lms_videos;
SET FOREIGN_KEY_CHECKS=1;

INSERT INTO `lms_videos` (`id`,`course_id`,`lesson_id`,`title`,`video_path`,`duration_seconds`,`is_published`,`created_at`) VALUES
(1,1,NULL,'Graphic Design Fundamentals - Full Course','https://www.youtube.com/watch?v=WONZVnlam6U',4800,1,'2026-02-16 07:11:08'),
(2,1,NULL,'Colour Theory for Designers','https://www.youtube.com/watch?v=_2LLXnUdUIc',1200,1,'2026-02-16 07:11:08'),
(3,1,NULL,'Typography Basics for Beginners','https://www.youtube.com/watch?v=sByzHoiYFX0',900,1,'2026-02-16 07:11:08'),
(4,1,NULL,'Logo Design Process Step by Step','https://www.youtube.com/watch?v=dKjYBhFvMsI',1800,1,'2026-02-16 07:11:08'),
(5,1,NULL,'Adobe Illustrator for Beginners','https://www.youtube.com/watch?v=Ib8UBwu3yGA',3600,1,'2026-02-16 07:11:08'),
(6,1,NULL,'Adobe Photoshop Full Tutorial','https://www.youtube.com/watch?v=IyR_uYsRdPs',5400,1,'2026-02-16 07:11:08'),
(7,1,NULL,'Print Design & Layout Basics','https://www.youtube.com/watch?v=a5KYlZiJFx0',1500,1,'2026-02-16 07:11:08'),
(8,1,NULL,'Building a Design Portfolio','https://www.youtube.com/watch?v=V4YCfFBKFhA',1200,1,'2026-02-16 07:11:08'),
(9,2,NULL,'Advanced Typography Techniques','https://www.youtube.com/watch?v=QrNi9FmdlxY',2400,1,'2026-02-16 07:11:08'),
(10,2,NULL,'Brand Identity Design Process','https://www.youtube.com/watch?v=l-S2Y3SF3jM',3000,1,'2026-02-16 07:11:08'),
(11,2,NULL,'Motion Graphics with After Effects','https://www.youtube.com/watch?v=52S_Q3QnMXE',4200,1,'2026-02-16 07:11:08'),
(12,2,NULL,'Packaging Design Tutorial','https://www.youtube.com/watch?v=Ib8UBwu3yGA',2100,1,'2026-02-16 07:11:08'),
(13,2,NULL,'Advanced Branding Strategy','https://www.youtube.com/watch?v=l-S2Y3SF3jM',1800,1,'2026-02-16 07:11:08'),
(14,2,NULL,'Freelance Design Business Tips','https://www.youtube.com/watch?v=V4YCfFBKFhA',1500,1,'2026-02-16 07:11:08'),
(15,3,NULL,'Web Design for Beginners - Full Course','https://www.youtube.com/watch?v=mU6anWqZJcc',5400,1,'2026-02-16 07:11:08'),
(16,3,NULL,'UI Design Fundamentals','https://www.youtube.com/watch?v=tRpoI6vkqLs',2700,1,'2026-02-16 07:11:08'),
(17,3,NULL,'UX Design Process Explained','https://www.youtube.com/watch?v=wIuVvCuiJhU',2400,1,'2026-02-16 07:11:08'),
(18,3,NULL,'Responsive Web Design Tutorial','https://www.youtube.com/watch?v=srvUrASNj0s',3600,1,'2026-02-16 07:11:08'),
(19,3,NULL,'Figma Tutorial for Beginners','https://www.youtube.com/watch?v=FTFaQWZBqQ8',4800,1,'2026-02-16 07:11:08'),
(20,3,NULL,'CSS Flexbox and Grid Tutorial','https://www.youtube.com/watch?v=phWxA89Dy94',3000,1,'2026-02-16 07:11:08'),
(21,3,NULL,'Website Performance Optimisation','https://www.youtube.com/watch?v=AQqFZ5t8uNc',1800,1,'2026-02-16 07:11:08'),
(22,3,NULL,'SEO Basics for Web Designers','https://www.youtube.com/watch?v=DvwS7cV9GmQ',2100,1,'2026-02-16 07:11:08'),
(23,4,NULL,'HTML Full Course for Beginners','https://www.youtube.com/watch?v=pQN-pnXPaVg',7200,1,'2026-02-16 07:11:08'),
(24,4,NULL,'CSS Tutorial - Zero to Hero','https://www.youtube.com/watch?v=1Rs2ND1ryYc',6000,1,'2026-02-16 07:11:08'),
(25,4,NULL,'JavaScript Full Course for Beginners','https://www.youtube.com/watch?v=PkZNo7MFNFg',7200,1,'2026-02-16 07:11:08'),
(26,4,NULL,'PHP Tutorial for Beginners','https://www.youtube.com/watch?v=OK_JCtrrv-c',5400,1,'2026-02-16 07:11:08'),
(27,4,NULL,'MySQL Database Tutorial','https://www.youtube.com/watch?v=7S_tz1z_5bA',4800,1,'2026-02-16 07:11:08'),
(28,4,NULL,'Build a Full Stack Web App','https://www.youtube.com/watch?v=Oe421EPjeBE',9000,1,'2026-02-16 07:11:08'),
(29,4,NULL,'REST API with PHP and MySQL','https://www.youtube.com/watch?v=OEWXbpUMODk',3600,1,'2026-02-16 07:11:08'),
(30,4,NULL,'Web Deployment Tutorial','https://www.youtube.com/watch?v=mBQmly7SIAM',2400,1,'2026-02-16 07:11:08'),
(31,5,NULL,'PHP OOP Full Course','https://www.youtube.com/watch?v=Anz0ArcQ5kI',7200,1,'2026-02-16 07:11:08'),
(32,5,NULL,'Advanced MySQL Queries','https://www.youtube.com/watch?v=7S_tz1z_5bA',4800,1,'2026-02-16 07:11:08'),
(33,5,NULL,'PHP Security Best Practices','https://www.youtube.com/watch?v=2_hh9oNMqAA',3000,1,'2026-02-16 07:11:08'),
(34,5,NULL,'Building REST APIs with PHP','https://www.youtube.com/watch?v=OEWXbpUMODk',3600,1,'2026-02-16 07:11:08'),
(35,5,NULL,'PHPMailer Email Tutorial','https://www.youtube.com/watch?v=JFZSE1vYb0k',1800,1,'2026-02-16 07:11:08'),
(36,5,NULL,'PHP Unit Testing with PHPUnit','https://www.youtube.com/watch?v=k9ak_rv9X0Y',2400,1,'2026-02-16 07:11:08'),
(37,5,NULL,'Build an E-Commerce App with PHP','https://www.youtube.com/watch?v=KLWA2vCERSQ',9000,1,'2026-02-16 07:11:08'),
(38,6,NULL,'Flutter Tutorial for Beginners','https://www.youtube.com/watch?v=VPvVD8t02U8',7200,1,'2026-02-16 07:11:08'),
(39,6,NULL,'Flutter UI Design Tutorial','https://www.youtube.com/watch?v=x0uinJvhNxI',4800,1,'2026-02-16 07:11:08'),
(40,6,NULL,'Flutter State Management with Provider','https://www.youtube.com/watch?v=L_QMsE2v6dw',3600,1,'2026-02-16 07:11:08'),
(41,6,NULL,'Firebase with Flutter Tutorial','https://www.youtube.com/watch?v=sfA3NWDBPZ4',5400,1,'2026-02-16 07:11:08'),
(42,6,NULL,'Flutter App Deployment to Play Store','https://www.youtube.com/watch?v=g0GNuoCOtaQ',2400,1,'2026-02-16 07:11:08'),
(43,6,NULL,'Flutter Animations Tutorial','https://www.youtube.com/watch?v=CRRQMFMkFAE',3000,1,'2026-02-16 07:11:08'),
(44,6,NULL,'Flutter App Monetisation','https://www.youtube.com/watch?v=Lf-8USgBmFE',1800,1,'2026-02-16 07:11:08'),
(45,7,NULL,'UX Design Full Course','https://www.youtube.com/watch?v=wIuVvCuiJhU',5400,1,'2026-02-16 07:11:08'),
(46,7,NULL,'User Research Methods','https://www.youtube.com/watch?v=tRpoI6vkqLs',2700,1,'2026-02-16 07:11:08'),
(47,7,NULL,'Information Architecture Tutorial','https://www.youtube.com/watch?v=Ovj4hFxko7c',1800,1,'2026-02-16 07:11:08'),
(48,7,NULL,'Figma Prototyping Tutorial','https://www.youtube.com/watch?v=FTFaQWZBqQ8',4800,1,'2026-02-16 07:11:08'),
(49,7,NULL,'Design Systems in Figma','https://www.youtube.com/watch?v=EK-pHkc5EL4',3600,1,'2026-02-16 07:11:08'),
(50,7,NULL,'Accessibility in UI Design','https://www.youtube.com/watch?v=20SHvU2PKsM',2400,1,'2026-02-16 07:11:08'),
(51,7,NULL,'UX Writing Fundamentals','https://www.youtube.com/watch?v=OinN0KLNUOU',1500,1,'2026-02-16 07:11:08'),
(52,8,NULL,'Digital Marketing Full Course','https://www.youtube.com/watch?v=nU7gFBBFMGk',7200,1,'2026-02-16 07:11:08'),
(53,8,NULL,'SEO Tutorial for Beginners','https://www.youtube.com/watch?v=DvwS7cV9GmQ',4800,1,'2026-02-16 07:11:08'),
(54,8,NULL,'Social Media Marketing Strategy','https://www.youtube.com/watch?v=q6RoHnGBFxs',3600,1,'2026-02-16 07:11:08'),
(55,8,NULL,'Email Marketing Tutorial','https://www.youtube.com/watch?v=Wcs2PFz5q6g',2700,1,'2026-02-16 07:11:08'),
(56,8,NULL,'Content Marketing Strategy','https://www.youtube.com/watch?v=lZD72ZFnNOI',2400,1,'2026-02-16 07:11:08'),
(57,8,NULL,'Google Ads Tutorial for Beginners','https://www.youtube.com/watch?v=lbCITfyMDfI',3600,1,'2026-02-16 07:11:08'),
(58,8,NULL,'Google Analytics 4 Tutorial','https://www.youtube.com/watch?v=d5_SFbFGCOA',3000,1,'2026-02-16 07:11:08'),
(59,9,NULL,'Data Analysis with Python - Full Course','https://www.youtube.com/watch?v=r-uOLxNrNk8',7200,1,'2026-02-16 07:11:08'),
(60,9,NULL,'Excel for Data Analysis Tutorial','https://www.youtube.com/watch?v=PSNXoAs2FtQ',4800,1,'2026-02-16 07:11:08'),
(61,9,NULL,'SQL for Data Analysis','https://www.youtube.com/watch?v=7mz73uXD9DA',5400,1,'2026-02-16 07:11:08'),
(62,9,NULL,'Python Pandas Tutorial','https://www.youtube.com/watch?v=vmEHCJofslg',6000,1,'2026-02-16 07:11:08'),
(63,9,NULL,'Data Visualisation with Python','https://www.youtube.com/watch?v=a9UrKTVEeZA',3600,1,'2026-02-16 07:11:08'),
(64,9,NULL,'Statistics for Data Science','https://www.youtube.com/watch?v=xxpc-HPKN28',4200,1,'2026-02-16 07:11:08'),
(65,9,NULL,'Power BI Tutorial for Beginners','https://www.youtube.com/watch?v=AGrl-H87pRU',5400,1,'2026-02-16 07:11:08'),
(66,10,NULL,'Cybersecurity Full Course for Beginners','https://www.youtube.com/watch?v=U_P23SqJaDc',7200,1,'2026-02-16 07:11:08'),
(67,10,NULL,'Network Security Fundamentals','https://www.youtube.com/watch?v=E03gh1huvW4',3600,1,'2026-02-16 07:11:08'),
(68,10,NULL,'Web Application Security - OWASP Top 10','https://www.youtube.com/watch?v=rWHvp7rUka8',4800,1,'2026-02-16 07:11:08'),
(69,10,NULL,'Ethical Hacking Full Course','https://www.youtube.com/watch?v=3Kq1MIfTWCE',9000,1,'2026-02-16 07:11:08'),
(70,10,NULL,'Kali Linux Tutorial for Beginners','https://www.youtube.com/watch?v=lZAoFs75_cs',5400,1,'2026-02-16 07:11:08'),
(71,10,NULL,'Incident Response Tutorial','https://www.youtube.com/watch?v=Lf-8USgBmFE',2400,1,'2026-02-16 07:11:08'),
(72,11,NULL,'Computer Basics Full Course','https://www.youtube.com/watch?v=y2kg3MOk1sY',5400,1,'2026-02-16 07:11:08'),
(73,11,NULL,'Windows 11 Tutorial for Beginners','https://www.youtube.com/watch?v=xABMFMkFAE',3600,1,'2026-02-16 07:11:08'),
(74,11,NULL,'Microsoft Office Full Tutorial','https://www.youtube.com/watch?v=PSNXoAs2FtQ',7200,1,'2026-02-16 07:11:08'),
(75,11,NULL,'Internet Safety and Security','https://www.youtube.com/watch?v=aO858HyFbKI',2400,1,'2026-02-16 07:11:08'),
(76,11,NULL,'Computer Troubleshooting Guide','https://www.youtube.com/watch?v=y2kg3MOk1sY',3000,1,'2026-02-16 07:11:08'),
(77,12,NULL,'Python Tkinter Tutorial for Beginners','https://www.youtube.com/watch?v=YXPyB4XeYLA',5400,1,'2026-02-16 07:11:08'),
(78,12,NULL,'PyQt5 Tutorial - Build Desktop Apps','https://www.youtube.com/watch?v=Vde5SH8e1OQ',7200,1,'2026-02-16 07:11:08'),
(79,12,NULL,'SQLite with Python Tutorial','https://www.youtube.com/watch?v=byHcYRpMgI4',3600,1,'2026-02-16 07:11:08'),
(80,12,NULL,'PyInstaller - Package Python Apps','https://www.youtube.com/watch?v=p3tSLatmGvU',1800,1,'2026-02-16 07:11:08'),
(81,12,NULL,'Python Threading Tutorial','https://www.youtube.com/watch?v=IEEhzQoKtQU',2400,1,'2026-02-16 07:11:08'),
(82,13,NULL,'POS System Tutorial for Beginners','https://www.youtube.com/watch?v=y2kg3MOk1sY',3600,1,'2026-02-16 07:11:08'),
(83,13,NULL,'IT Support Fundamentals','https://www.youtube.com/watch?v=qiQR5rTSshw',5400,1,'2026-02-16 07:11:08'),
(84,13,NULL,'Network Setup for Small Business','https://www.youtube.com/watch?v=E03gh1huvW4',3000,1,'2026-02-16 07:11:08'),
(85,13,NULL,'Customer Service Skills Training','https://www.youtube.com/watch?v=OinN0KLNUOU',2400,1,'2026-02-16 07:11:08'),
(86,13,NULL,'CompTIA A+ Study Guide','https://www.youtube.com/watch?v=87t6P5ZHTP0',7200,1,'2026-02-16 07:11:08'),
(87,14,NULL,'Computer Networking Full Course','https://www.youtube.com/watch?v=IPvYjXCsTg8',7200,1,'2026-02-16 07:11:08'),
(88,14,NULL,'IP Addressing and Subnetting','https://www.youtube.com/watch?v=s_gy4VJhNZM',4800,1,'2026-02-16 07:11:08'),
(89,14,NULL,'Cisco Packet Tracer Tutorial','https://www.youtube.com/watch?v=fCMFEBBFMFE',5400,1,'2026-02-16 07:11:08'),
(90,14,NULL,'Wireless Networking Tutorial','https://www.youtube.com/watch?v=E03gh1huvW4',3600,1,'2026-02-16 07:11:08'),
(91,14,NULL,'Network Security Fundamentals','https://www.youtube.com/watch?v=U_P23SqJaDc',4200,1,'2026-02-16 07:11:08'),
(92,14,NULL,'CompTIA Network+ Study Guide','https://www.youtube.com/watch?v=qiQR5rTSshw',9000,1,'2026-02-16 07:11:08'),
(93,15,NULL,'AWS Cloud Practitioner Full Course','https://www.youtube.com/watch?v=SOTamWNgDKc',9000,1,'2026-02-16 07:11:08'),
(94,15,NULL,'AWS Core Services Tutorial','https://www.youtube.com/watch?v=ulprqHHWlng',7200,1,'2026-02-16 07:11:08'),
(95,15,NULL,'Docker Tutorial for Beginners','https://www.youtube.com/watch?v=fqMOX6JJhGo',5400,1,'2026-02-16 07:11:08'),
(96,15,NULL,'AWS Well-Architected Framework','https://www.youtube.com/watch?v=vg5onp8TU6Q',3600,1,'2026-02-16 07:11:08'),
(97,15,NULL,'GitHub Actions CI/CD Tutorial','https://www.youtube.com/watch?v=R8_veQiYBjI',4800,1,'2026-02-16 07:11:08'),
(98,15,NULL,'Terraform Tutorial for Beginners','https://www.youtube.com/watch?v=SLB_c_ayRMo',5400,1,'2026-02-16 07:11:08'),
(99,16,NULL,'Software Engineering Full Course','https://www.youtube.com/watch?v=O753uuutqH8',7200,1,'2026-02-16 07:11:08'),
(100,16,NULL,'Software Architecture Patterns','https://www.youtube.com/watch?v=vqEg37e4Mkw',4800,1,'2026-02-16 07:11:08'),
(101,16,NULL,'Agile and Scrum Full Course','https://www.youtube.com/watch?v=502ILHjX9EE',5400,1,'2026-02-16 07:11:08'),
(102,16,NULL,'Software Testing Tutorial','https://www.youtube.com/watch?v=TDynSmrzpXw',4200,1,'2026-02-16 07:11:08'),
(103,16,NULL,'System Design Interview Guide','https://www.youtube.com/watch?v=i53Gi_K3o7I',6000,1,'2026-02-16 07:11:08'),
(104,16,NULL,'Clean Code Principles','https://www.youtube.com/watch?v=7EmboKQH8lM',3600,1,'2026-02-16 07:11:08');

-- Update course intro_video with first YouTube video
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=WONZVnlam6U' WHERE `id`=1;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=QrNi9FmdlxY' WHERE `id`=2;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=mU6anWqZJcc' WHERE `id`=3;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=pQN-pnXPaVg' WHERE `id`=4;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=Anz0ArcQ5kI' WHERE `id`=5;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=VPvVD8t02U8' WHERE `id`=6;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=wIuVvCuiJhU' WHERE `id`=7;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=nU7gFBBFMGk' WHERE `id`=8;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=r-uOLxNrNk8' WHERE `id`=9;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=U_P23SqJaDc' WHERE `id`=10;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=y2kg3MOk1sY' WHERE `id`=11;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=YXPyB4XeYLA' WHERE `id`=12;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=y2kg3MOk1sY' WHERE `id`=13;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=IPvYjXCsTg8' WHERE `id`=14;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=SOTamWNgDKc' WHERE `id`=15;
UPDATE `lms_courses` SET `intro_video`='https://www.youtube.com/watch?v=O753uuutqH8' WHERE `id`=16;
