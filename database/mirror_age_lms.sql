-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2026 at 03:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mirror_age_lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `lms_activity_logs`
--

CREATE TABLE `lms_activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `lms_admins`
--

CREATE TABLE `lms_admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `lms_ai_chats`
--

CREATE TABLE `lms_ai_chats` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED DEFAULT NULL,
  `role` enum('user','assistant') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_assessment_questions`
--

CREATE TABLE `lms_assessment_questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `assessment_id` int(10) UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(300) NOT NULL,
  `option_b` varchar(300) NOT NULL,
  `option_c` varchar(300) DEFAULT NULL,
  `option_d` varchar(300) DEFAULT NULL,
  `correct_option` enum('A','B','C','D') NOT NULL,
  `marks` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_assessment_questions`
--

INSERT INTO `lms_assessment_questions` (`id`, `assessment_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `marks`, `sort_order`) VALUES
(1, 1, 'Which of the following is a principle of design?', 'Contrast', 'Coding', 'Compression', 'Compilation', 'A', 1, 1),
(2, 1, 'What does \"white space\" in design refer to?', 'Empty areas that give designs room to breathe', 'Spaces filled with white colour only', 'Margins on a printed page', 'The background of a website', 'A', 1, 2),
(3, 1, 'Which tool is best for creating vector logos?', 'Adobe Photoshop', 'Adobe Illustrator', 'Microsoft Word', 'Adobe Premiere', 'B', 1, 3),
(4, 1, 'What is the difference between raster and vector graphics?', 'Raster uses paths; vector uses pixels', 'Vector uses paths that scale infinitely; raster uses pixels and loses quality when scaled', 'They are the same thing', 'Raster is for print; vector is for screens only', 'B', 1, 4),
(5, 1, 'Which principle groups related design elements together?', 'Contrast', 'Alignment', 'Proximity', 'Repetition', 'C', 1, 5),
(6, 2, 'What are complementary colours?', 'Colours that are similar to each other', 'Colours that are opposite each other on the colour wheel', 'Colours from the same family', 'Colours used in printing', 'B', 1, 1),
(7, 2, 'Which colour psychology association is correct?', 'Blue = Energy and urgency', 'Red = Trust and calm', 'Green = Growth and health', 'Yellow = Sophistication', 'C', 1, 2),
(8, 2, 'What is the recommended line spacing (leading) for body text?', '0.8x the font size', '1.0x the font size', '1.4-1.6x the font size', '2.0x the font size', 'C', 1, 3),
(9, 2, 'How many fonts should you typically use in a single design?', '1 only', '2-3 maximum', '5-6 for variety', 'As many as needed', 'B', 1, 4),
(10, 2, 'What is a triadic colour harmony?', 'Two colours opposite on the wheel', 'Three colours evenly spaced on the colour wheel', 'All colours from one family', 'Tints and shades of one colour', 'B', 1, 5),
(11, 3, 'What type of logo uses only the company initials?', 'Wordmark', 'Lettermark', 'Emblem', 'Combination mark', 'B', 1, 1),
(12, 3, 'Which quality ensures a logo works at any size?', 'Colourful', 'Simple', 'Complex', 'Animated', 'B', 1, 2),
(13, 3, 'What file format should logos be delivered in for scalability?', 'JPG', 'PNG', 'SVG/AI (vector)', 'GIF', 'C', 1, 3),
(14, 3, 'What is a \"combination mark\" logo?', 'A logo with only text', 'A logo with only an icon', 'A logo combining an icon and wordmark', 'A logo with multiple colours', 'C', 1, 4),
(15, 3, 'Why should logos be timeless?', 'To save design time', 'To avoid trends that date quickly and maintain brand consistency', 'To reduce printing costs', 'To make them easier to copy', 'B', 1, 5),
(16, 4, 'What is \"bleed\" in print design?', 'Extra artwork beyond the trim edge to prevent white borders after cutting', 'The ink that bleeds through paper', 'A type of font style', 'A colour correction technique', 'A', 1, 1),
(17, 4, 'What colour mode should print designs use?', 'RGB', 'HSL', 'CMYK', 'HEX', 'C', 1, 2),
(18, 4, 'What is the minimum DPI for print-quality images?', '72', '150', '300', '600', 'C', 1, 3),
(19, 4, 'What is the \"safe zone\" in print design?', 'The area where important content must stay to avoid being cut off', 'The area outside the bleed', 'The printer margin', 'The colour-safe area', 'A', 1, 4),
(20, 4, 'What does a \"tri-fold brochure\" have?', '3 pages', '3 panels created by 2 folds', '3 colours', '3 fonts', 'B', 1, 5),
(21, 5, 'What are the correct Instagram square post dimensions?', '800x800px', '1080x1080px', '1200x1200px', '720x720px', 'B', 1, 1),
(22, 5, 'What colour mode do digital designs use?', 'CMYK', 'RGB', 'HSL', 'Pantone', 'B', 1, 2),
(23, 5, 'What is the \"3-second rule\" in social media design?', 'Post 3 times per day', 'Your message must be clear within 3 seconds', 'Use 3 colours maximum', 'Post every 3 hours', 'B', 1, 3),
(24, 5, 'What image format is best for web performance?', 'BMP', 'TIFF', 'WebP or compressed PNG/JPG', 'RAW', 'C', 1, 4),
(25, 5, 'What is a \"call-to-action\" (CTA) in design?', 'A phone number', 'A clear instruction telling the viewer what to do next', 'A colour choice', 'A font style', 'B', 1, 5),
(26, 6, 'What is a \"non-destructive\" editing workflow?', 'Editing that cannot be undone', 'Editing that preserves the original file so changes can be reversed', 'Editing that reduces file size', 'Editing that improves quality permanently', 'B', 1, 1),
(27, 6, 'What is the difference between Vibrance and Saturation?', 'They are the same', 'Vibrance boosts muted colours naturally; Saturation boosts all colours equally', 'Saturation is for portraits; Vibrance is for landscapes', 'Vibrance reduces colours; Saturation increases them', 'B', 1, 2),
(28, 6, 'When should you use a Layer Mask instead of the Eraser tool?', 'When you want to permanently delete pixels', 'When you want to hide pixels non-destructively so they can be restored', 'When you want to add colour', 'When you want to resize an image', 'B', 1, 3),
(29, 6, 'What does \"Camera Raw\" in Photoshop do?', 'Converts images to black and white', 'Provides professional RAW photo processing with white balance and exposure controls', 'Adds camera effects', 'Reduces file size', 'B', 1, 4),
(30, 6, 'What resolution should you export images for web use?', '300 DPI', '150 DPI', '72 DPI', '600 DPI', 'C', 1, 5),
(31, 7, 'How many projects should a beginner design portfolio contain?', '1-2', '6-12', '20-30', '50+', 'B', 1, 1),
(32, 7, 'What is a \"kill fee\" in freelance design?', 'A fee for cancelling a project mid-way', 'A fee for rush work', 'A fee for extra revisions', 'A fee for file delivery', 'A', 1, 2),
(33, 7, 'What are the 4 parts of a design case study?', 'Intro, Body, Conclusion, References', 'Challenge, Process, Solution, Result', 'Brief, Concept, Design, Delivery', 'Research, Sketch, Digital, Print', 'B', 1, 3),
(34, 7, 'What is the recommended deposit percentage before starting freelance work?', '10%', '30-50%', '75%', '100%', 'B', 1, 4),
(35, 7, 'Which platform is the industry standard for design portfolios?', 'LinkedIn', 'Behance', 'Instagram', 'Pinterest', 'B', 1, 5),
(36, 8, 'What does \"responsive design\" mean?', 'A website that responds to user clicks', 'A website that adapts its layout to different screen sizes', 'A website that loads quickly', 'A website with animations', 'B', 1, 1),
(37, 8, 'What is the difference between web design and web development?', 'They are the same', 'Web design focuses on visual layout and UX; web development focuses on code', 'Web design is harder', 'Web development is cheaper', 'B', 1, 2),
(38, 8, 'Which HTML element is used for the main navigation?', '<header>', '<main>', '<nav>', '<section>', 'C', 1, 3),
(39, 8, 'What is \"visual hierarchy\" in web design?', 'Using many fonts', 'Guiding the user\'s eye to the most important content first', 'Making everything the same size', 'Using bright colours', 'B', 1, 4),
(40, 8, 'What is the \"F-pattern\" in web design?', 'A font naming convention', 'How users visually scan web content — horizontally then vertically', 'A CSS layout technique', 'A colour scheme', 'B', 1, 5),
(41, 9, 'What is the minimum touch target size for mobile buttons?', '24x24px', '32x32px', '44x44px', '56x56px', 'C', 1, 1),
(42, 9, 'Why should placeholder text NOT replace form labels?', 'It looks bad', 'Placeholder text disappears when the user starts typing, making the field purpose unclear', 'It is too small', 'It is not accessible', 'B', 1, 2),
(43, 9, 'What spacing scale is commonly used in UI design?', 'Multiples of 4 or 8px', 'Multiples of 10px', 'Multiples of 3px', 'Random spacing', 'A', 1, 3),
(44, 9, 'What is a \"primary button\" in UI design?', 'The largest button', 'The button for the main action, styled with high contrast', 'The first button on the page', 'A button with an icon', 'B', 1, 4),
(45, 9, 'What does \"inline validation\" mean in forms?', 'Validating the entire form on submit', 'Showing errors as the user types, not only on submit', 'Validating on the server', 'Checking for duplicate entries', 'B', 1, 5),
(46, 10, 'What are the 5 stages of the UX design process?', 'Plan, Design, Build, Test, Launch', 'Research, Define, Ideate, Prototype, Test', 'Brief, Sketch, Design, Review, Deliver', 'Discover, Define, Develop, Deliver, Deploy', 'B', 1, 1),
(47, 10, 'What is a user persona?', 'A real user account', 'A fictional but research-based representation of a target user', 'A user\'s password', 'A UI element', 'B', 1, 2),
(48, 10, 'What is \"card sorting\" used for?', 'Sorting playing cards', 'Testing navigation structure with users to understand how they categorise content', 'Organising design files', 'Creating colour palettes', 'B', 1, 3),
(49, 10, 'What is the difference between qualitative and quantitative research?', 'Qualitative is faster', 'Qualitative understands why; quantitative measures how many', 'They are the same', 'Quantitative is more accurate', 'B', 1, 4),
(50, 10, 'What is the Jobs-to-be-Done framework?', 'A project management method', 'A framework focusing on what job users hire a product to do', 'A design system', 'A testing methodology', 'B', 1, 5),
(51, 11, 'What are the standard breakpoints for responsive design?', '480px, 960px, 1440px', '767px, 1023px, 1279px', '600px, 900px, 1200px', '320px, 768px, 1024px', 'B', 1, 1),
(52, 11, 'What is the difference between Flexbox and CSS Grid?', 'Flexbox is newer', 'Flexbox is one-dimensional (row or column); Grid is two-dimensional (rows AND columns)', 'Grid is only for images', 'Flexbox is only for navigation', 'B', 1, 2),
(53, 11, 'Why do we write mobile-first CSS?', 'Mobile devices are more popular', 'It is easier to add complexity for larger screens than to remove it for smaller ones', 'It loads faster', 'Google requires it', 'B', 1, 3),
(54, 11, 'What does \"min-width\" in a media query mean?', 'Apply styles when screen is smaller than this width', 'Apply styles when screen is at least this width', 'The minimum font size', 'The minimum container width', 'B', 1, 4),
(55, 11, 'What CSS property creates a flexible one-dimensional layout?', 'display: grid', 'display: flex', 'display: block', 'display: inline', 'B', 1, 5),
(56, 12, 'What is the WCAG AA contrast ratio requirement for normal text?', '2:1', '3:1', '4.5:1', '7:1', 'C', 1, 1),
(57, 12, 'What is the difference between web-safe fonts and web fonts?', 'Web-safe fonts are better quality', 'Web-safe fonts are pre-installed on devices; web fonts are loaded from a server', 'Web fonts are free; web-safe fonts cost money', 'They are the same', 'B', 1, 2),
(58, 12, 'How does CSS clamp() work for fluid typography?', 'It limits the number of fonts', 'It sets a minimum, preferred, and maximum font size that scales with the viewport', 'It clamps text to one line', 'It prevents text overflow', 'B', 1, 3),
(59, 12, 'What are CSS custom properties (variables) used for?', 'Storing JavaScript values', 'Storing reusable values like colours and spacing that can be changed in one place', 'Storing images', 'Storing database values', 'B', 1, 4),
(60, 12, 'What is the purpose of a CSS design system?', 'To make CSS files larger', 'To create consistent, reusable design tokens and components across a project', 'To slow down development', 'To replace HTML', 'B', 1, 5),
(61, 13, 'What is a Figma component and why is it useful?', 'A colour style', 'A reusable design element — update the master and all instances update automatically', 'A font style', 'A page in Figma', 'B', 1, 1),
(62, 13, 'How does Auto Layout in Figma relate to CSS Flexbox?', 'They are unrelated', 'Auto Layout makes frames resize based on content, similar to how CSS Flexbox works', 'Auto Layout is for animations', 'Auto Layout is only for text', 'B', 1, 2),
(63, 13, 'What does the Inspect panel in Figma provide for developers?', 'Animation settings', 'Exact CSS values for any element: font size, colour, spacing, border radius', 'Database connections', 'Server configuration', 'B', 1, 3),
(64, 13, 'What is \"Smart Animate\" in Figma prototyping?', 'A plugin', 'Figma automatically animates matching layers between screens for smooth transitions', 'A colour tool', 'A grid system', 'B', 1, 4),
(65, 13, 'What is the purpose of a clickable prototype?', 'To write code', 'To simulate the product and test it with users before building', 'To create the final design', 'To export assets', 'B', 1, 5),
(66, 14, 'What are the three Core Web Vitals?', 'Speed, Size, Style', 'LCP (Largest Contentful Paint), FID (First Input Delay), CLS (Cumulative Layout Shift)', 'Load, Interact, Stable', 'Fast, Responsive, Accessible', 'B', 1, 1),
(67, 14, 'What is the target LCP (Largest Contentful Paint) time?', 'Under 1 second', 'Under 2.5 seconds', 'Under 5 seconds', 'Under 10 seconds', 'B', 1, 2),
(68, 14, 'Why should you use WebP format for images?', 'It is easier to edit', 'WebP is 30-50% smaller than JPG/PNG with the same quality, improving load speed', 'It supports animation', 'It is the only format browsers support', 'B', 1, 3),
(69, 14, 'What is the ideal length for a title tag?', '20-30 characters', '50-60 characters', '80-100 characters', '150-160 characters', 'B', 1, 4),
(70, 14, 'What SEO elements does a web designer directly control?', 'Server configuration', 'Title tags, meta descriptions, heading structure, alt text, URL structure', 'Database queries', 'Payment processing', 'B', 1, 5),
(71, 15, 'What is the difference between semantic and non-semantic HTML?', 'Semantic elements are faster', 'Semantic elements describe their meaning to both browser and developer; non-semantic do not', 'Non-semantic elements are newer', 'They are the same', 'B', 1, 1),
(72, 15, 'What does the alt attribute on an image do?', 'Sets the image size', 'Describes the image for screen readers and SEO when the image cannot be displayed', 'Links the image to another page', 'Adds a border to the image', 'B', 1, 2),
(73, 15, 'What is the difference between <strong> and <b>?', 'They look different', '<strong> indicates importance (semantic); <b> is just visual bold (presentational)', '<b> is newer', '<strong> is for headings only', 'B', 1, 3),
(74, 15, 'Which HTML5 element represents the main content of a page?', '<div>', '<section>', '<main>', '<article>', 'C', 1, 4),
(75, 15, 'What attribute makes a form field required?', 'mandatory', 'required', 'must-fill', 'validate', 'B', 1, 5),
(76, 16, 'Explain the CSS box model and its four components.', 'Width, Height, Colour, Font', 'Content, Padding, Border, Margin', 'Top, Right, Bottom, Left', 'Display, Position, Float, Clear', 'B', 1, 1),
(77, 16, 'What is the difference between margin and padding?', 'They are the same', 'Margin is space outside the border; padding is space between content and border', 'Padding is outside; margin is inside', 'Margin affects colour; padding affects size', 'B', 1, 2),
(78, 16, 'When would you use Flexbox vs CSS Grid?', 'Always use Grid', 'Flexbox for one-dimensional layouts (row or column); Grid for two-dimensional layouts', 'Always use Flexbox', 'Use neither; use float instead', 'B', 1, 3),
(79, 16, 'What does box-sizing: border-box do?', 'Removes the border', 'Makes padding and border included in the element\'s total width and height', 'Adds a box shadow', 'Changes the display type', 'B', 1, 4),
(80, 16, 'What is a CSS pseudo-class?', 'A fake class', 'A keyword added to a selector that specifies a special state (e.g. :hover, :focus)', 'A class that inherits from another', 'A class for animations', 'B', 1, 5),
(81, 17, 'What is the difference between let, const, and var?', 'They are the same', 'const cannot be reassigned; let can be reassigned; var is function-scoped and should be avoided', 'let is faster than const', 'var is the newest', 'B', 1, 1),
(82, 17, 'How do you select an element by class name in JavaScript?', 'document.getElementById()', 'document.querySelector() or document.querySelectorAll()', 'document.getClass()', 'document.findElement()', 'B', 1, 2),
(83, 17, 'What does the Fetch API do?', 'Fetches images', 'Makes HTTP requests to servers and returns Promises', 'Fetches CSS files', 'Fetches database records', 'B', 1, 3),
(84, 17, 'What is localStorage in JavaScript?', 'A server-side database', 'A browser-based key-value storage that persists after the page is closed', 'A temporary session storage', 'A cookie', 'B', 1, 4),
(85, 17, 'What does addEventListener() do?', 'Adds a new HTML element', 'Attaches a function to be called when a specific event occurs on an element', 'Adds a CSS class', 'Adds a new variable', 'B', 1, 5),
(86, 18, 'What is the difference between $_GET and $_POST?', '$_GET is faster', '$_GET sends data in the URL (visible); $_POST sends data in the request body (hidden)', '$_POST is more secure for all cases', 'They are the same', 'B', 1, 1),
(87, 18, 'Why should you always sanitise user input?', 'To make it look better', 'To prevent XSS, SQL injection, and other security attacks', 'To reduce file size', 'To improve performance', 'B', 1, 2),
(88, 18, 'What is a PHP session and when would you use it?', 'A database connection', 'A way to store user data across multiple pages (e.g. login state, form data)', 'A type of cookie', 'A server configuration', 'B', 1, 3),
(89, 18, 'What does htmlspecialchars() do in PHP?', 'Converts HTML to text', 'Converts special characters to HTML entities to prevent XSS attacks', 'Removes HTML tags', 'Adds HTML formatting', 'B', 1, 4),
(90, 18, 'What is the difference between include and require in PHP?', 'They are the same', 'require causes a fatal error if the file is not found; include only gives a warning', 'include is faster', 'require is for classes only', 'B', 1, 5),
(91, 19, 'What is the difference between a primary key and a foreign key?', 'They are the same', 'A primary key uniquely identifies a row in its table; a foreign key references a primary key in another table', 'A foreign key is faster', 'A primary key can be null', 'B', 1, 1),
(92, 19, 'Why should you always use prepared statements?', 'They are faster', 'They prevent SQL injection by separating SQL code from user data', 'They are required by MySQL', 'They reduce database size', 'B', 1, 2),
(93, 19, 'What is database normalisation?', 'Making the database faster', 'Organising data to reduce redundancy and improve data integrity', 'Adding more tables', 'Removing all foreign keys', 'B', 1, 3),
(94, 19, 'What does INNER JOIN return?', 'All rows from both tables', 'Only rows where there is a match in both tables', 'All rows from the left table', 'All rows from the right table', 'B', 1, 4),
(95, 19, 'What is an index in a database?', 'A list of all tables', 'A data structure that speeds up queries on frequently searched columns', 'A type of foreign key', 'A backup of the database', 'B', 1, 5),
(96, 20, 'What is CSRF and how do you prevent it?', 'A type of database error; prevented by indexing', 'Cross-Site Request Forgery; prevented by including a unique token in every form', 'A JavaScript error; prevented by try/catch', 'A CSS issue; prevented by validation', 'B', 1, 1),
(97, 20, 'Why should you never store plain-text passwords?', 'They take more space', 'If the database is compromised, all passwords are immediately exposed', 'They are harder to type', 'PHP does not support them', 'B', 1, 2),
(98, 20, 'What is the difference between authentication and authorisation?', 'They are the same', 'Authentication verifies who you are; authorisation determines what you can do', 'Authentication is for admins only', 'Authorisation happens before authentication', 'B', 1, 3),
(99, 20, 'What does password_hash() do in PHP?', 'Encrypts the password reversibly', 'Creates a secure one-way hash of the password using bcrypt', 'Stores the password in a cookie', 'Sends the password by email', 'B', 1, 4),
(100, 20, 'What is a REST API?', 'A type of database', 'An architectural style for building web services that use HTTP methods to perform operations on resources', 'A PHP framework', 'A CSS methodology', 'B', 1, 5),
(101, 21, 'What are the 4 main HTTP methods and what does each do?', 'GET/POST/PUT/DELETE — all retrieve data', 'GET retrieves, POST creates, PUT/PATCH updates, DELETE removes', 'GET and POST are the only ones', 'PUT is for images only', 'B', 1, 1),
(102, 21, 'What does HTTP status code 401 mean?', 'Not Found', 'Unauthorized — the request requires authentication', 'Server Error', 'Bad Request', 'B', 1, 2),
(103, 21, 'What is the difference between a REST API and a traditional web page?', 'REST APIs are faster', 'REST APIs return data (JSON/XML) for any client to consume; web pages return HTML for browsers', 'REST APIs require a database', 'Web pages are more secure', 'B', 1, 3),
(104, 21, 'What does json_encode() do in PHP?', 'Decodes JSON to PHP array', 'Converts a PHP array or object to a JSON string', 'Validates JSON format', 'Sends JSON to the browser', 'B', 1, 4),
(105, 21, 'What is Vue.js used for?', 'Server-side rendering', 'Building reactive user interfaces in the browser with JavaScript', 'Database management', 'CSS styling', 'B', 1, 5),
(106, 22, 'What is a type scale?', 'A ruler for measuring fonts', 'A defined set of font sizes used consistently in a design system', 'A font family', 'A typographic error', 'B', 1, 1),
(107, 22, 'What is the difference between tracking and kerning?', 'They are the same', 'Tracking adjusts uniform spacing between all characters; kerning adjusts space between specific pairs', 'Kerning is for headings only', 'Tracking is for body text only', 'B', 1, 2),
(108, 22, 'What is a variable font?', 'A font that changes colour', 'A font containing multiple styles in a single file, reducing file size', 'A font used only for headings', 'A decorative font', 'B', 1, 3),
(109, 22, 'What is leading in typography?', 'Space between characters', 'Vertical space between lines of text', 'Font weight', 'Letter height', 'B', 1, 4),
(110, 22, 'What does a baseline grid do?', 'Aligns images', 'Creates consistent vertical rhythm so all text aligns across columns', 'Defines column widths', 'Sets font sizes', 'B', 1, 5),
(111, 23, 'What is the WCAG AA contrast ratio for normal text?', '2:1', '3:1', '4.5:1', '7:1', 'C', 1, 1),
(112, 23, 'What are colour tokens?', 'Paint colours', 'Named variables representing colours in a design system', 'Colour swatches', 'Printer inks', 'B', 1, 2),
(113, 23, 'Why is dark mode not simply inverting colours?', 'It is too complex', 'Inverting creates harsh contrasts; dark mode needs reduced saturation and maintained contrast ratios', 'It requires special software', 'Browsers do not support it', 'B', 1, 3),
(114, 23, 'What is a semantic colour?', 'A colour that looks good', 'A colour with functional meaning: success=green, error=red, warning=amber', 'A brand colour', 'A colour for text only', 'B', 1, 4),
(115, 23, 'What is a diverging colour palette used for?', 'Categorical data', 'Data with a meaningful midpoint such as temperature above and below zero', 'Sequential data', 'Random data', 'B', 1, 5),
(116, 24, 'Why is the 12-column grid so widely used?', 'It looks better', '12 is divisible by 2, 3, 4, and 6 allowing many layout configurations', 'It is the oldest grid', 'Bootstrap requires it', 'B', 1, 1),
(117, 24, 'What is the Z-pattern in layout?', 'A font style', 'How the eye moves in a Z shape on text-heavy pages', 'A grid system', 'A colour scheme', 'B', 1, 2),
(118, 24, 'What is the Golden Ratio in design?', 'A colour formula', '1:1.618 proportion that creates naturally pleasing layouts', 'A font size ratio', 'A grid measurement', 'B', 1, 3),
(119, 24, 'What is a modular grid?', 'A grid with only columns', 'A grid with both rows and columns creating a matrix of modules for complex layouts', 'A grid for mobile only', 'A grid for images only', 'B', 1, 4),
(120, 24, 'What does the F-pattern describe?', 'A font category', 'How users scan web content: horizontally then vertically down the left side', 'A layout for print', 'A colour arrangement', 'B', 1, 5),
(121, 25, 'What is easing in animation?', 'The speed of the entire animation', 'Controlling acceleration and deceleration to make movement feel natural', 'The direction of movement', 'The colour of the animation', 'B', 1, 1),
(122, 25, 'Which easing is most natural for UI animations?', 'Linear', 'Ease In', 'Ease Out: starts fast, ends slow', 'Ease In-Out', 'C', 1, 2),
(123, 25, 'What is anticipation in animation?', 'The final pose', 'A small movement before the main action that prepares the viewer', 'The background', 'The colour change', 'B', 1, 3),
(124, 25, 'What does squash and stretch give to animated objects?', 'Colour', 'A sense of weight and flexibility', 'Speed', 'Direction', 'B', 1, 4),
(125, 25, 'What is secondary action in animation?', 'Replacing the main action', 'Supporting actions that add richness and realism to the main action', 'Slowing down the animation', 'Adding colour', 'B', 1, 5),
(126, 26, 'What is the difference between brand strategy and brand identity?', 'They are the same', 'Strategy is the long-term plan; identity is the visual and verbal expression of that strategy', 'Identity comes before strategy', 'Strategy is only for large companies', 'B', 1, 1),
(127, 26, 'What are the 3 types of brand architecture?', 'Logo, Colour, Font', 'Monolithic (Branded House), Endorsed, Pluralistic (House of Brands)', 'Primary, Secondary, Tertiary', 'National, Regional, Local', 'B', 1, 2),
(128, 26, 'What is a brand positioning statement?', 'A tagline', 'A statement defining where the brand sits in the market relative to competitors for a specific audience', 'A mission statement', 'A product description', 'B', 1, 3),
(129, 26, 'When should a company rebrand?', 'Every year', 'When there is a merger, significant audience shift, outdated identity, or reputation issue', 'Never', 'When they change prices', 'B', 1, 4),
(130, 26, 'What is brand equity?', 'The monetary value of a brand', 'The value a brand adds beyond the functional product, built through recognition and trust', 'The cost of branding', 'The number of brand colours', 'B', 1, 5),
(131, 27, 'What is professional indemnity insurance?', 'Health insurance', 'Insurance that protects against client claims if your work causes financial loss', 'Life insurance', 'Equipment insurance', 'B', 1, 1),
(132, 27, 'What must a client contract include?', 'Only the price', 'Scope of work, timeline, payment terms, revision rounds, kill fee, and IP ownership', 'Only the deadline', 'Only the client name', 'B', 1, 2),
(133, 27, 'What is the difference between project-based and value-based pricing?', 'Project-based is always more expensive', 'Project-based charges a fixed fee for defined scope; value-based charges based on the impact delivered', 'Value-based is for beginners', 'Project-based is for large companies only', 'B', 1, 3),
(134, 27, 'What is a kill fee?', 'A fee for completing a project', 'A fee paid to the designer if the client cancels the project mid-way', 'A fee for extra revisions', 'A fee for rush delivery', 'A', 1, 4),
(135, 27, 'Why should you never start work without a deposit?', 'It is illegal', 'It protects you if the client cancels or disappears before paying', 'It is industry standard', 'Clients expect it', 'B', 1, 5),
(136, 28, 'What does OOP stand for?', 'Object Oriented Programming', 'Open Output Processing', 'Ordered Object Protocol', 'Optional Output Parameters', 'A', 1, 1),
(137, 28, 'What keyword creates an object from a class in PHP?', 'create', 'make', 'new', 'build', 'C', 1, 2),
(138, 28, 'What is the difference between public and private in PHP?', 'Public is faster', 'Private members can only be accessed within the class; public can be accessed anywhere', 'Public cannot be inherited', 'Private methods are static', 'B', 1, 3),
(139, 28, 'What does PDO stand for?', 'PHP Data Objects', 'PHP Database Operations', 'Prepared Data Output', 'PHP Dynamic Objects', 'A', 1, 4),
(140, 28, 'What does password_hash() do?', 'Encrypts reversibly', 'Creates a secure one-way bcrypt hash of the password', 'Stores in a cookie', 'Sends by email', 'B', 1, 5),
(141, 29, 'What is the difference between an interface and an abstract class?', 'They are the same', 'An interface defines a contract with no implementation; an abstract class can have partial implementation', 'Abstract classes are faster', 'Interfaces are for databases only', 'B', 1, 1),
(142, 29, 'What does this refer to inside a class method?', 'The parent class', 'The current object instance', 'The database connection', 'The session', 'B', 1, 2),
(143, 29, 'What is inheritance in OOP?', 'Copying code', 'A class extending another class to reuse and extend its functionality', 'A type of loop', 'A database relationship', 'B', 1, 3),
(144, 29, 'What is encapsulation?', 'Hiding the database', 'Bundling data and methods together and restricting direct access to internal state', 'A type of inheritance', 'A design pattern', 'B', 1, 4),
(145, 29, 'What is a constructor in PHP?', 'A function that destroys objects', 'A special method called automatically when an object is created', 'A static method', 'A database query', 'B', 1, 5),
(146, 30, 'What is the difference between WHERE and HAVING?', 'They are the same', 'WHERE filters rows before grouping; HAVING filters groups after GROUP BY', 'HAVING is faster', 'WHERE is for joins only', 'B', 1, 1),
(147, 30, 'What does a LEFT JOIN return?', 'Only matching rows', 'All rows from the left table plus matching rows from the right (NULLs for no match)', 'All rows from both tables', 'Only non-matching rows', 'B', 1, 2),
(148, 30, 'What is a database transaction?', 'A payment record', 'A group of SQL operations that succeed or fail together as a unit', 'A database backup', 'A stored procedure', 'B', 1, 3),
(149, 30, 'What is an index in MySQL?', 'A list of all tables', 'A data structure that speeds up queries on frequently searched columns', 'A type of foreign key', 'A backup', 'B', 1, 4),
(150, 30, 'What does EXPLAIN do in MySQL?', 'Deletes a query', 'Shows how MySQL executes a query including whether indexes are used', 'Explains the database structure', 'Shows all tables', 'B', 1, 5),
(151, 31, 'What is SQL injection?', 'A type of virus', 'An attack that inserts malicious SQL code into unsanitised database queries', 'A network attack', 'A password attack', 'B', 1, 1),
(152, 31, 'What is XSS?', 'A CSS framework', 'Cross-Site Scripting: injecting malicious scripts into web pages viewed by other users', 'A type of database', 'A PHP function', 'B', 1, 2),
(153, 31, 'What is CSRF?', 'A CSS rule', 'Cross-Site Request Forgery: tricking a user into submitting a malicious request', 'A PHP error', 'A database error', 'B', 1, 3),
(154, 31, 'How do prepared statements prevent SQL injection?', 'They are faster', 'They separate SQL code from user data so malicious input cannot alter the query structure', 'They encrypt the data', 'They validate the input', 'B', 1, 4),
(155, 31, 'What does htmlspecialchars() do?', 'Removes HTML', 'Converts special characters to HTML entities to prevent XSS attacks', 'Adds HTML formatting', 'Validates HTML', 'B', 1, 5),
(156, 32, 'What are the 4 main HTTP methods?', 'GET/POST/PUT/DELETE: all retrieve data', 'GET retrieves, POST creates, PUT/PATCH updates, DELETE removes', 'GET and POST are the only ones', 'PUT is for images only', 'B', 1, 1),
(157, 32, 'What HTTP status code should a successful POST return?', '200 OK', '201 Created', '400 Bad Request', '404 Not Found', 'B', 1, 2),
(158, 32, 'What does JWT stand for?', 'Java Web Token', 'JSON Web Token: a standard for securely transmitting information between parties', 'JavaScript Web Transfer', 'JSON Web Transfer', 'B', 1, 3),
(159, 32, 'What is the difference between PUT and PATCH?', 'They are the same', 'PUT replaces the entire resource; PATCH updates only specified fields', 'PATCH is newer', 'PUT is for files only', 'B', 1, 4),
(160, 32, 'What HTTP status code means Unauthorized?', '200', '201', '401', '404', 'C', 1, 5),
(161, 33, 'What is the difference between authentication and authorisation?', 'They are the same', 'Authentication verifies who you are; authorisation determines what you can do', 'Authentication is for admins only', 'Authorisation happens before authentication', 'B', 1, 1),
(162, 33, 'What is a session in PHP?', 'A database connection', 'A way to store user data across multiple pages such as login state', 'A type of cookie', 'A server configuration', 'B', 1, 2),
(163, 33, 'What is the purpose of a CSRF token?', 'To speed up forms', 'To prevent Cross-Site Request Forgery by verifying that form submissions come from your own site', 'To validate email addresses', 'To encrypt passwords', 'B', 1, 3),
(164, 33, 'What is the difference between include and require in PHP?', 'They are the same', 'require causes a fatal error if the file is not found; include only gives a warning', 'include is faster', 'require is for classes only', 'B', 1, 4),
(165, 33, 'What does session_regenerate_id() do after login?', 'Logs the user out', 'Creates a new session ID to prevent session fixation attacks', 'Saves the session', 'Deletes old sessions', 'B', 1, 5),
(166, 34, 'What language does Flutter use?', 'JavaScript', 'Kotlin', 'Dart', 'Swift', 'C', 1, 1),
(167, 34, 'What is the difference between StatelessWidget and StatefulWidget?', 'StatelessWidget is faster', 'StatefulWidget can change over time; StatelessWidget cannot', 'StatelessWidget uses more memory', 'StatefulWidget cannot be reused', 'B', 1, 2),
(168, 34, 'What does setState() do?', 'Saves data', 'Triggers a widget rebuild with updated state', 'Navigates to a new screen', 'Sends an HTTP request', 'B', 1, 3),
(169, 34, 'What is the Provider package for?', 'HTTP requests', 'State management across the widget tree', 'Database access', 'Animations', 'B', 1, 4),
(170, 34, 'What is the difference between APK and App Bundle?', 'They are the same', 'App Bundle is required for Play Store and optimised per device; APK is a direct install file', 'APK is for iOS', 'App Bundle is older', 'B', 1, 5),
(171, 35, 'What is Firebase Firestore?', 'A SQL database', 'A NoSQL cloud database with real-time synchronisation', 'A file storage service', 'An authentication service', 'B', 1, 1),
(172, 35, 'How does authStateChanges() work?', 'It checks the password', 'It returns a stream that emits the current user whenever auth state changes', 'It stores the user in a cookie', 'It sends an email', 'B', 1, 2),
(173, 35, 'What is the difference between a Firestore collection and a document?', 'They are the same', 'A collection is a container of documents; a document is a single record with fields', 'A document contains collections only', 'A collection is a single record', 'B', 1, 3),
(174, 35, 'What does notifyListeners() do in Provider?', 'Saves data', 'Tells all listening widgets to rebuild with the updated state', 'Sends a notification', 'Logs an event', 'B', 1, 4),
(175, 35, 'What is the minimum touch target size for mobile?', '24x24px', '32x32px', '44x44px', '56x56px', 'C', 1, 5),
(176, 36, 'What is the difference between qualitative and quantitative research?', 'Qualitative is faster', 'Qualitative understands why; quantitative measures how many', 'They are the same', 'Quantitative is more accurate', 'B', 1, 1),
(177, 36, 'What are the 5 components of a user persona?', 'Name, Age, Job, Salary, Location', 'Name/photo, demographics, goals, frustrations, behaviours', 'Name, Email, Password, Role, Status', 'Title, Description, Priority, Status, Owner', 'B', 1, 2),
(178, 36, 'What is card sorting used for?', 'Sorting design files', 'Testing how users categorise content to inform information architecture', 'Creating colour palettes', 'Organising code', 'B', 1, 3),
(179, 36, 'What is tree testing?', 'Testing a decision tree', 'Testing navigation structure with users using a text-only hierarchy without visual design', 'Testing a database', 'Testing code performance', 'B', 1, 4),
(180, 36, 'What is the Jobs-to-be-Done framework?', 'A project management tool', 'A framework focusing on what job users hire a product to do: When [situation] I want [motivation] so I can [outcome]', 'A design system', 'A testing methodology', 'B', 1, 5),
(181, 37, 'What is the WCAG AA contrast ratio for normal text?', '2:1', '3:1', '4.5:1', '7:1', 'C', 1, 1),
(182, 37, 'What is the difference between atoms, molecules, and organisms?', 'Size categories', 'Atoms are basic elements; molecules combine atoms; organisms combine molecules into complex components', 'Colour categories', 'Animation levels', 'B', 1, 2),
(183, 37, 'What are component variants in Figma?', 'Different file versions', 'Multiple states and styles of a component such as button primary/secondary/ghost and default/hover/disabled', 'Different pages', 'Different fonts', 'B', 1, 3),
(184, 37, 'What is the minimum touch target size for mobile?', '24x24px', '32x32px', '44x44px', '56x56px', 'C', 1, 4),
(185, 37, 'What does WCAG stand for?', 'Web Coding and Graphics', 'Web Content Accessibility Guidelines', 'Website Content and Graphics', 'Web Component Architecture Guide', 'B', 1, 5),
(186, 38, 'What is the ideal length for a title tag?', '20-30 characters', '50-60 characters', '80-100 characters', '150-160 characters', 'B', 1, 1),
(187, 38, 'What is the difference between on-page and off-page SEO?', 'On-page is more important', 'On-page optimises elements on your website; off-page builds authority through backlinks and mentions', 'Off-page is free', 'On-page is only for images', 'B', 1, 2),
(188, 38, 'What is a long-tail keyword?', 'A very long keyword', 'A specific lower-competition keyword phrase with higher purchase intent', 'A keyword with many results', 'A keyword used in headings only', 'B', 1, 3),
(189, 38, 'What does CTR stand for?', 'Click Through Rate: clicks divided by impressions times 100', 'Content Transfer Rate', 'Customer Tracking Report', 'Conversion Tracking Rate', 'A', 1, 4),
(190, 38, 'What is the 80/20 rule in social media content?', 'Post 80 times per month', '80% valuable content and 20% promotional content', '80% images and 20% text', '80% paid and 20% organic', 'B', 1, 5),
(191, 39, 'What is a lead magnet?', 'A type of advertisement', 'Something valuable offered in exchange for an email address such as a free guide or checklist', 'A social media post', 'A type of backlink', 'B', 1, 1),
(192, 39, 'What is an email drip campaign?', 'A leaking email server', 'An automated sequence of emails sent over time based on user behaviour or schedule', 'A bulk email blast', 'A single promotional email', 'B', 1, 2),
(193, 39, 'What is A/B testing in email marketing?', 'Testing two email servers', 'Sending two versions of an email to different segments to see which performs better', 'Testing email deliverability', 'Testing email design on two browsers', 'B', 1, 3),
(194, 39, 'Why should you never buy email lists?', 'It is expensive', 'It damages deliverability, violates anti-spam laws, and the contacts have not consented', 'It is too slow', 'Email lists are free anyway', 'B', 1, 4),
(195, 39, 'What is the purpose of a UTM parameter?', 'To speed up links', 'To track the source, medium, and campaign of website traffic in analytics', 'To shorten URLs', 'To encrypt links', 'B', 1, 5),
(196, 40, 'What is the difference between a NumPy array and a pandas DataFrame?', 'They are the same', 'NumPy arrays are homogeneous numerical arrays; DataFrames are tabular with mixed types and labels', 'DataFrames are faster', 'NumPy is for visualisation', 'B', 1, 1),
(197, 40, 'How do you handle missing values in pandas?', 'Delete the entire dataset', 'Use dropna() to remove rows or fillna() to impute with statistics', 'Ignore them', 'Replace with zeros always', 'B', 1, 2),
(198, 40, 'What chart type shows correlation between two variables?', 'Bar chart', 'Pie chart', 'Scatter plot', 'Line chart', 'C', 1, 3),
(199, 40, 'What does df.describe() return?', 'The first 5 rows', 'Statistical summary: count, mean, std, min, quartiles, max for numeric columns', 'The column names', 'The data types', 'B', 1, 4),
(200, 40, 'What is the difference between df.loc and df.iloc?', 'They are the same', 'loc selects by label/name; iloc selects by integer position', 'iloc is faster', 'loc is for rows only', 'B', 1, 5),
(201, 41, 'When should you use median instead of mean?', 'Always', 'When the data has outliers or is skewed, as median is more robust', 'When the data is normally distributed', 'When you have categorical data', 'B', 1, 1),
(202, 41, 'What does a p-value of 0.03 mean?', 'The result is 3% accurate', 'There is a 3% probability of observing results this extreme if the null hypothesis is true', 'The effect size is 3%', 'The sample size is 3', 'B', 1, 2),
(203, 41, 'What is the difference between correlation and causation?', 'They are the same', 'Correlation shows two variables move together; causation means one directly causes the other', 'Causation is stronger', 'Correlation is always positive', 'B', 1, 3),
(204, 41, 'What is a normal distribution?', 'A distribution with no outliers', 'A symmetric bell-shaped distribution where 68% of data falls within 1 standard deviation of the mean', 'A distribution with equal values', 'A distribution for categorical data', 'B', 1, 4),
(205, 41, 'What is the IQR?', 'Max minus Min', 'Q3 minus Q1: the range of the middle 50% of data, robust to outliers', 'Mean minus Median', 'Standard deviation squared', 'B', 1, 5),
(206, 42, 'What does the CIA triad stand for?', 'Confidentiality, Integrity, Availability', 'Cyber Intelligence Agency', 'Computer Information Access', 'Control, Identify, Analyse', 'A', 1, 1),
(207, 42, 'What is phishing?', 'A type of malware', 'Deceptive messages designed to steal credentials or install malware', 'A network attack', 'A password cracking technique', 'B', 1, 2),
(208, 42, 'What does SQL injection exploit?', 'Weak passwords', 'Unsanitised database queries that allow attackers to manipulate SQL commands', 'Open network ports', 'Unencrypted connections', 'B', 1, 3),
(209, 42, 'What is the purpose of a firewall?', 'Speed up internet', 'Monitor and control network traffic based on predefined security rules', 'Store passwords', 'Encrypt data', 'B', 1, 4),
(210, 42, 'What is the principle of least privilege?', 'Give users maximum access', 'Grant only the minimum permissions necessary for a user to perform their job', 'Share passwords with the team', 'Use the same password everywhere', 'B', 1, 5),
(211, 43, 'What is the difference between TCP and UDP?', 'TCP is faster', 'TCP is reliable and connection-oriented; UDP is fast and connectionless', 'UDP is more secure', 'They are the same', 'B', 1, 1),
(212, 43, 'What does a VPN do?', 'Speeds up internet', 'Encrypts all traffic between your device and a VPN server, hiding your IP and protecting data', 'Blocks advertisements', 'Stores passwords', 'B', 1, 2),
(213, 43, 'What is network segmentation?', 'Speeding up the network', 'Dividing a network into zones to limit the spread of attacks if one zone is compromised', 'Adding more routers', 'Encrypting the network', 'B', 1, 3),
(214, 43, 'What is the difference between IDS and IPS?', 'They are the same', 'IDS detects and alerts on suspicious activity; IPS detects and actively blocks it', 'IPS is older', 'IDS is more expensive', 'B', 1, 4),
(215, 43, 'What is ARP poisoning?', 'A food contamination issue', 'An attack that redirects network traffic through the attacker machine by sending fake ARP messages', 'A type of malware', 'A firewall bypass', 'B', 1, 5),
(216, 44, 'What is the difference between RAM and storage?', 'RAM is permanent; storage is temporary', 'RAM is temporary working memory; storage is permanent data storage', 'They are the same', 'RAM is slower than storage', 'B', 1, 1),
(217, 44, 'Which storage type is faster?', 'HDD', 'SSD', 'CD-ROM', 'Floppy disk', 'B', 1, 2),
(218, 44, 'What does the CPU do?', 'Stores data permanently', 'Executes instructions and processes data: the brain of the computer', 'Displays output', 'Connects to the internet', 'B', 1, 3),
(219, 44, 'What command shows your IP address on Windows?', 'netstat', 'ping', 'ipconfig', 'tracert', 'C', 1, 4),
(220, 44, 'What is the difference between CC and BCC in email?', 'CC is faster', 'CC recipients are visible to all; BCC recipients are hidden from other recipients', 'CC is for attachments', 'BCC is for urgent emails', 'B', 1, 5),
(221, 45, 'What is the 10-20-30 rule for presentations?', '10 slides, 20 minutes, 30pt minimum font size', '10 minutes, 20 slides, 30 words per slide', '10 points, 20 images, 30 seconds', '10 colours, 20 fonts, 30 slides', 'A', 1, 1),
(222, 45, 'What is the difference between a formula and a function in Excel?', 'They are the same', 'A formula is any expression starting with =; a function is a built-in named formula like SUM() or IF()', 'Functions are faster', 'Formulas are for text only', 'B', 1, 2),
(223, 45, 'Why should you use Styles in Word instead of manual formatting?', 'Styles look better', 'Styles ensure consistency and allow you to update formatting across the entire document at once', 'Manual formatting is slower', 'Styles are required by Word', 'B', 1, 3),
(224, 45, 'What does VLOOKUP do in Excel?', 'Calculates the average', 'Looks up a value in the first column of a range and returns a value from a specified column in the same row', 'Sorts data alphabetically', 'Creates a chart', 'B', 1, 4),
(225, 45, 'What is the purpose of a pivot table?', 'Creating charts', 'Summarising and analysing large datasets quickly by grouping and aggregating data', 'Formatting cells', 'Writing formulas', 'B', 1, 5),
(226, 46, 'What is the difference between a desktop app and a web app?', 'Desktop apps are always free', 'Desktop apps run natively on the OS with full system access; web apps run in a browser', 'Web apps are always faster', 'Desktop apps require internet', 'B', 1, 1),
(227, 46, 'What does root.mainloop() do in Tkinter?', 'Closes the application', 'Starts the event loop that keeps the window open and responds to user events', 'Creates a new window', 'Saves the application', 'B', 1, 2),
(228, 46, 'What is PyInstaller used for?', 'Testing Python code', 'Packaging Python applications into standalone executables that run without Python installed', 'Installing Python packages', 'Debugging Python code', 'B', 1, 3),
(229, 46, 'Why should long-running operations run in a background thread?', 'To use less memory', 'To keep the UI responsive: blocking the main thread freezes the interface', 'To run faster', 'To save battery', 'B', 1, 4),
(230, 46, 'What is SQLite and why is it good for desktop apps?', 'A cloud database', 'A lightweight file-based database requiring no server: perfect for local desktop applications', 'A web database', 'A NoSQL database', 'B', 1, 5),
(231, 47, 'What is a PyQt5 Signal?', 'A network request', 'A mechanism for communication between objects: emitted when an event occurs, connected to a slot function', 'A database connection', 'A file operation', 'B', 1, 1),
(232, 47, 'What is semantic versioning format?', 'date.month.year', 'MAJOR.MINOR.PATCH: major for breaking changes, minor for features, patch for bug fixes', 'version.build.release', 'alpha.beta.stable', 'B', 1, 2),
(233, 47, 'What does conn.row_factory = sqlite3.Row do?', 'Speeds up queries', 'Allows accessing query results by column name instead of index position', 'Creates a new table', 'Deletes all rows', 'B', 1, 3),
(234, 47, 'What is the difference between joblib and pickle?', 'They are the same', 'joblib is optimised for large NumPy arrays; pickle is general-purpose Python serialisation', 'pickle is faster', 'joblib is for databases only', 'B', 1, 4),
(235, 47, 'What does the --onefile flag in PyInstaller do?', 'Creates multiple files', 'Bundles the entire application and all dependencies into a single executable file', 'Installs the app', 'Creates a shortcut', 'B', 1, 5),
(236, 48, 'What does POS stand for?', 'Point of Sale', 'Point of Service', 'Payment Operations System', 'Purchase Order System', 'A', 1, 1),
(237, 48, 'What should you do at end of business day with a POS system?', 'Turn it off immediately', 'Reconcile the cash drawer, back up transaction data, and review the daily sales report', 'Delete all transactions', 'Change the password', 'B', 1, 2),
(238, 48, 'What are the 3 tiers of ICT support?', 'Basic, Advanced, Expert', 'Help Desk (Tier 1), Technical Support (Tier 2), Expert/Specialist Support (Tier 3)', 'Level 1, Level 2, Level 3', 'All of the above', 'B', 1, 3),
(239, 48, 'What is a runbook in ICT support?', 'A physical book', 'Step-by-step documented procedures for handling common IT tasks and incidents', 'A list of employees', 'A network diagram', 'B', 1, 4),
(240, 48, 'What is the first step when a POS terminal stops working?', 'Call the vendor immediately', 'Check the power cable and connections, then restart the terminal', 'Replace the terminal', 'Refund all customers', 'B', 1, 5),
(241, 49, 'What is active listening in customer service?', 'Listening while doing other tasks', 'Fully concentrating on what the speaker is saying, asking clarifying questions, and summarising what you heard', 'Recording a conversation', 'Listening to music', 'B', 1, 1),
(242, 49, 'Which Wi-Fi security protocol should you use?', 'WEP', 'WPA', 'WPA2 or WPA3', 'No security needed', 'C', 1, 2),
(243, 49, 'What does DHCP do on a network?', 'Encrypts network traffic', 'Automatically assigns IP addresses to devices on the network', 'Blocks malicious websites', 'Speeds up internet', 'B', 1, 3),
(244, 49, 'What is the CompTIA A+ certification for?', 'Network engineering', 'Entry-level IT support covering hardware, software, networking, and troubleshooting', 'Cybersecurity', 'Cloud computing', 'B', 1, 4),
(245, 49, 'What is a ticketing system in IT support?', 'A bus ticket system', 'Software used to track, manage, and resolve IT support requests and incidents', 'A project management tool', 'A billing system', 'B', 1, 5),
(246, 50, 'What does LAN stand for?', 'Large Area Network', 'Local Area Network: a network covering a small area like a home or office', 'Linked Access Node', 'Long Area Network', 'B', 1, 1),
(247, 50, 'How many layers does the OSI model have?', '4', '5', '7', '10', 'C', 1, 2);
INSERT INTO `lms_assessment_questions` (`id`, `assessment_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `marks`, `sort_order`) VALUES
(248, 50, 'What is the difference between a switch and a router?', 'They are the same', 'A switch connects devices within the same network; a router connects different networks together', 'A router is faster', 'A switch connects to the internet', 'B', 1, 3),
(249, 50, 'What does CIDR /24 mean?', '24 devices on the network', '24 bits are the network portion leaving 8 bits for hosts (254 usable hosts)', '24 available IP addresses', '24 subnets', 'B', 1, 4),
(250, 50, 'What is NAT?', 'A type of cable', 'A technique that allows multiple devices with private IPs to share a single public IP address', 'A network protocol', 'A firewall type', 'B', 1, 5),
(251, 51, 'What is the difference between 2.4 GHz and 5 GHz Wi-Fi?', '2.4 GHz is always faster', '2.4 GHz has longer range but slower speeds; 5 GHz has shorter range but faster speeds', 'They are identical', '5 GHz has longer range', 'B', 1, 1),
(252, 51, 'What is a VLAN?', 'A type of cable', 'A logical network segment within a physical network that isolates traffic between groups', 'A wireless network', 'A type of router', 'B', 1, 2),
(253, 51, 'What is the implicit deny rule in firewall configuration?', 'Allow all traffic by default', 'All traffic not explicitly permitted by a rule is automatically denied', 'Allow traffic from trusted IPs only', 'Deny traffic from unknown countries', 'B', 1, 3),
(254, 51, 'What is a rogue access point?', 'A broken router', 'A fake Wi-Fi hotspot set up by an attacker to intercept network traffic', 'A misconfigured switch', 'An outdated router', 'B', 1, 4),
(255, 51, 'What does DNS do?', 'Encrypts network traffic', 'Translates human-readable domain names to IP addresses', 'Assigns IP addresses', 'Blocks malicious websites', 'B', 1, 5),
(256, 52, 'What is the difference between IaaS, PaaS, and SaaS?', 'They are the same', 'IaaS provides infrastructure; PaaS provides a platform to build on; SaaS provides ready-to-use software', 'SaaS is the most technical', 'IaaS is the cheapest', 'B', 1, 1),
(257, 52, 'What does S3 stand for in AWS?', 'Simple Storage Service', 'Secure Server System', 'Scalable Storage Solution', 'Standard Storage Service', 'A', 1, 2),
(258, 52, 'What is the difference between a Docker image and a container?', 'They are the same', 'An image is a read-only template; a container is a running instance of an image', 'A container is a template', 'Images are larger', 'B', 1, 3),
(259, 52, 'What does CI/CD stand for?', 'Computer Integration/Deployment', 'Continuous Integration/Continuous Delivery: automating build, test, and deployment pipelines', 'Cloud Infrastructure/Deployment', 'Code Integration/Delivery', 'B', 1, 4),
(260, 52, 'What is Infrastructure as Code?', 'Writing code on a server', 'Defining and managing infrastructure using code files for repeatability, version control, and automation', 'A programming language', 'A cloud service', 'B', 1, 5),
(261, 53, 'What is Auto Scaling in AWS?', 'Manually adding servers', 'Automatically adjusting the number of EC2 instances based on demand', 'Scaling the database', 'Scaling the network', 'B', 1, 1),
(262, 53, 'What is the AWS Well-Architected Framework?', 'A type of server', '6 pillars for building reliable, secure, efficient cloud systems: Operational Excellence, Security, Reliability, Performance, Cost, Sustainability', 'A programming framework', 'A database design pattern', 'B', 1, 2),
(263, 53, 'What is a CDN?', 'A type of database', 'A network of servers worldwide that delivers content from the closest location to the user for faster loading', 'A cloud database', 'A container service', 'B', 1, 3),
(264, 53, 'Why should you never commit your .env file to Git?', 'It is too large', 'It contains sensitive credentials that would be exposed publicly', 'Git does not support it', 'It slows down Git', 'B', 1, 4),
(265, 53, 'What is the difference between public and private cloud?', 'Public cloud is free', 'Public cloud is shared infrastructure managed by a provider; private cloud is dedicated to one organisation', 'Private cloud is always cheaper', 'Public cloud is more secure', 'B', 1, 5),
(266, 54, 'What are the 7 phases of the SDLC?', 'Plan, Code, Test, Deploy, Monitor, Fix, Repeat', 'Planning, Requirements, Design, Implementation, Testing, Deployment, Maintenance', 'Brief, Sketch, Build, Review, Launch, Monitor, Retire', 'Discover, Define, Design, Develop, Deploy, Deliver, Decommission', 'B', 1, 1),
(267, 54, 'What is the difference between Waterfall and Agile?', 'Waterfall is faster', 'Waterfall is sequential with fixed phases; Agile is iterative with short sprints and continuous feedback', 'Agile is older', 'They are the same', 'B', 1, 2),
(268, 54, 'What does SOLID stand for?', 'Single, Open, Liskov, Interface, Dependency', 'Simple, Object, Linked, Integrated, Dynamic', 'Standard, Open, Linked, Interface, Dependency', 'Single, Object, Linked, Integrated, Dependency', 'A', 1, 3),
(269, 54, 'What is a user story in Scrum?', 'A blog post', 'A feature description from the user perspective: As a [user] I want [goal] so that [reason]', 'A user manual', 'A database record', 'B', 1, 4),
(270, 54, 'What is TDD?', 'Testing after development', 'Writing tests before writing code: Red (failing test) then Green (make it pass) then Refactor', 'Testing during deployment', 'Testing by users', 'B', 1, 5),
(271, 55, 'What is the difference between vertical and horizontal scaling?', 'Vertical is better', 'Vertical scaling adds more resources to one server; horizontal scaling adds more servers', 'Horizontal is always cheaper', 'They are the same', 'B', 1, 1),
(272, 55, 'What is caching and why does it improve performance?', 'Storing backups', 'Storing frequently accessed data in fast memory to reduce database load and response time', 'Compressing files', 'Encrypting data', 'B', 1, 2),
(273, 55, 'What is a message queue?', 'A chat system', 'A system for asynchronous communication between services so they do not need to wait for each other', 'A database table', 'A type of API', 'B', 1, 3),
(274, 55, 'What is the Repository pattern?', 'A Git repository', 'An abstraction layer that separates data access logic from business logic making code testable', 'A type of database', 'A design pattern for UI', 'B', 1, 4),
(275, 55, 'What is horizontal scaling?', 'Adding more RAM to one server', 'Adding more servers to distribute load allowing virtually unlimited capacity growth', 'Scaling the database only', 'Scaling the network', 'B', 1, 5),
(276, 56, 'What are the 9 stages of the data science lifecycle?', 'Collect, Clean, Analyse, Visualise, Deploy, Monitor, Iterate, Report, Archive', 'Problem Definition, Data Collection, Cleaning, EDA, Feature Engineering, Modelling, Evaluation, Deployment, Monitoring', 'Plan, Gather, Process, Model, Test, Deploy, Review, Report, Close', 'Import, Clean, Explore, Model, Evaluate, Deploy, Monitor, Document, Retire', 'B', 1, 1),
(277, 56, 'What is EDA?', 'Exploratory Data Analysis: understanding data through statistics and visualisation before modelling', 'Extended Data Architecture', 'Evaluated Data Algorithm', 'Extracted Data Application', 'A', 1, 2),
(278, 56, 'What Python library is primarily used for data manipulation?', 'NumPy', 'Matplotlib', 'pandas', 'Seaborn', 'C', 1, 3),
(279, 56, 'What is the difference between supervised and unsupervised learning?', 'Supervised uses more data', 'Supervised learns from labelled examples; unsupervised finds patterns in unlabelled data', 'Supervised is faster', 'Unsupervised requires more computing power', 'B', 1, 4),
(280, 56, 'What chart type is best for showing a trend over time?', 'Pie chart', 'Bar chart', 'Line chart', 'Scatter plot', 'C', 1, 5),
(281, 57, 'What does df.describe() return?', 'The first 5 rows', 'Statistical summary: count, mean, std, min, quartiles, max for numeric columns', 'The column names', 'The data types', 'B', 1, 1),
(282, 57, 'What is one-hot encoding?', 'A security technique', 'Converting categorical variables into binary columns for each category so ML algorithms can process them', 'A type of normalisation', 'A data cleaning step', 'B', 1, 2),
(283, 57, 'Why do we scale features before modelling?', 'To make data look better', 'To ensure features with large values do not dominate features with small values in distance-based algorithms', 'To reduce file size', 'To improve visualisation', 'B', 1, 3),
(284, 57, 'What is the difference between dropping and imputing missing values?', 'They are the same', 'Dropping removes rows with missing data; imputing fills missing values with statistics or predictions', 'Imputing is always better', 'Dropping is always better', 'B', 1, 4),
(285, 57, 'What is the difference between df.loc and df.iloc?', 'They are the same', 'loc selects by label/name; iloc selects by integer position', 'iloc is faster', 'loc is for rows only', 'B', 1, 5),
(286, 58, 'What type of AI is designed for one specific task?', 'General AI', 'Super AI', 'Narrow AI', 'Symbolic AI', 'C', 1, 1),
(287, 58, 'What is the activation function most commonly used in hidden layers?', 'Sigmoid', 'Tanh', 'Softmax', 'ReLU', 'D', 1, 2),
(288, 58, 'What is transfer learning?', 'Training from scratch', 'Using a pre-trained model as a starting point for a new task saving time and data', 'Transferring data between servers', 'Moving a model to production', 'B', 1, 3),
(289, 58, 'What is the key innovation of the Transformer architecture?', 'Convolutional layers for text', 'Self-attention mechanism allowing each token to attend to all other tokens simultaneously', 'Recurrent connections', 'Pooling layers', 'B', 1, 4),
(290, 58, 'What is hallucination in LLMs?', 'When the model crashes', 'When the model generates false but confident information not grounded in facts', 'When the model is too slow', 'When the model refuses to answer', 'B', 1, 5),
(291, 59, 'What is prompt engineering?', 'Writing code for AI', 'The art of crafting effective prompts to get the best responses from AI models', 'Training AI models', 'Testing AI systems', 'B', 1, 1),
(292, 59, 'What is RAG?', 'Random Aggregation Generator', 'Retrieval-Augmented Generation: combining LLMs with a knowledge base to generate answers grounded in retrieved documents', 'Recurrent Attention Gate', 'Regularised Activation Graph', 'B', 1, 2),
(293, 59, 'What is the difference between fine-tuning and RAG?', 'They are the same', 'Fine-tuning trains the model on new data; RAG retrieves external knowledge at inference time without retraining', 'RAG is more expensive', 'Fine-tuning is faster', 'B', 1, 3),
(294, 59, 'What is algorithmic bias?', 'When an algorithm runs slowly', 'When an AI system produces systematically unfair outcomes due to biased training data or design', 'When an algorithm has too many parameters', 'When a model overfits', 'B', 1, 4),
(295, 59, 'What is the EU AI Act?', 'A European AI company', 'Risk-based regulation of AI systems in the EU classifying AI by risk level and imposing requirements accordingly', 'A programming framework', 'An AI certification', 'B', 1, 5),
(296, 60, 'What is overfitting?', 'When a model is too simple', 'When a model performs well on training data but poorly on new unseen data due to memorising noise', 'When a model takes too long to train', 'When a model has too few parameters', 'B', 1, 1),
(297, 60, 'What does SMOTE stand for?', 'Supervised Model Optimisation Technique', 'Synthetic Minority Over-sampling Technique: creates synthetic examples of the minority class to balance datasets', 'Standard Model Output Testing', 'Stochastic Model Optimisation Training', 'B', 1, 2),
(298, 60, 'What metric is most appropriate for a fraud detection model?', 'Accuracy', 'Mean Squared Error', 'Recall: to minimise missed fraud cases since false negatives are costly', 'R-squared', 'C', 1, 3),
(299, 60, 'What is cross-validation?', 'Splitting data once', 'Getting a more reliable performance estimate by training and evaluating on multiple different data splits', 'Cleaning the training data', 'Reducing the number of features', 'B', 1, 4),
(300, 60, 'What is data drift?', 'Corrupted training data', 'When the statistical properties of production data change over time causing model performance to degrade', 'A model bug', 'Retraining too frequently', 'B', 1, 5),
(301, 61, 'What is MLflow used for?', 'Building neural networks', 'Tracking ML experiments, parameters, metrics, and models for reproducibility and comparison', 'Deploying models to mobile', 'Cleaning data', 'B', 1, 1),
(302, 61, 'What is a model card?', 'A business card for ML engineers', 'A document describing a model: purpose, performance metrics, limitations, fairness considerations, and intended use', 'A type of neural network', 'A deployment configuration', 'B', 1, 2),
(303, 61, 'What is the difference between model deployment and MLOps?', 'They are the same', 'Deployment is putting a model into production; MLOps is the full practice of deploying, monitoring, and maintaining models over time', 'MLOps is only for large companies', 'Deployment is more complex', 'B', 1, 3),
(304, 61, 'What should you monitor in a deployed ML model?', 'Only the server uptime', 'Prediction distribution, feature distribution (data drift), model performance on labelled production samples, and latency', 'Only error rates', 'Only the number of requests', 'B', 1, 4),
(305, 61, 'What is the difference between joblib and pickle?', 'They are the same', 'joblib is optimised for large NumPy arrays (ML models); pickle is general-purpose Python serialisation', 'pickle is faster', 'joblib is for databases only', 'B', 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `lms_assessment_submissions`
--

CREATE TABLE `lms_assessment_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `assessment_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `score` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `total` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `attempt` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `lms_assignments`
--

CREATE TABLE `lms_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `instructions` mediumtext DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_assignment_submissions`
--

CREATE TABLE `lms_assignment_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `assignment_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `submission_text` longtext DEFAULT NULL,
  `score` int(10) UNSIGNED DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `status` enum('submitted','graded','resubmitted') NOT NULL DEFAULT 'submitted',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `graded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_certificates`
--

CREATE TABLE `lms_certificates` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `certificate_code` varchar(80) NOT NULL,
  `issued_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_courses`
--

CREATE TABLE `lms_courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `intro_video` varchar(255) DEFAULT NULL,
  `workspace_type` varchar(30) NOT NULL DEFAULT 'default',
  `workspace_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_courses`
--

INSERT INTO `lms_courses` (`id`, `title`, `slug`, `description`, `short_description`, `price`, `level`, `intro_video`, `workspace_type`, `workspace_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Graphic Design', 'graphic-design', 'Learn professional graphic design from beginner to advanced level.', 'Complete graphic design mastery.', 150000.00, 'beginner', 'https://www.youtube.com/watch?v=WONZVnlam6U', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:10'),
(2, 'Advanced Graphic Design', 'advanced-graphic-design', 'Advance your design skills with branding, layout and advanced tools.', 'Advanced design and branding.', 200000.00, 'intermediate', 'https://www.youtube.com/watch?v=QrNi9FmdlxY', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:10'),
(3, 'Web Design', 'web-design', 'Design modern responsive websites with UI principles and best practices.', 'UI-focused web design.', 180000.00, 'beginner', 'https://www.youtube.com/watch?v=mU6anWqZJcc', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:10'),
(4, 'Web Development', 'web-development', 'Full stack web development using HTML, CSS, JS, PHP & MySQL.', 'Become a full stack developer.', 250000.00, 'intermediate', 'https://www.youtube.com/watch?v=pQN-pnXPaVg', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:10'),
(5, 'PHP & MySQL Development', 'php-mysql-development', 'Build dynamic web apps using PHP and MySQL with real projects.', 'Backend development with PHP.', 300000.00, 'intermediate', 'https://www.youtube.com/watch?v=Anz0ArcQ5kI', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:10'),
(6, 'Mobile App Development', 'mobile-app-development', 'Build modern mobile applications and publish real apps.', 'Android/iOS app development.', 300000.00, 'intermediate', 'https://www.youtube.com/watch?v=VPvVD8t02U8', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:10'),
(7, 'UI/UX Design', 'ui-ux-design', 'Design user experiences, wireframes, prototypes and product UI.', 'UI/UX product design.', 180000.00, 'beginner', 'https://www.youtube.com/watch?v=wIuVvCuiJhU', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:10'),
(8, 'Digital Marketing', 'digital-marketing', 'Learn SEO, social media marketing, ads, content strategy and analytics.', 'Marketing and growth skills.', 150000.00, 'beginner', 'https://www.youtube.com/watch?v=nU7gFBBFMGk', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:10'),
(9, 'Data Analysis', 'data-analysis', 'Analyze data using tools and dashboards; real datasets and reporting.', 'Data reporting and insights.', 280000.00, 'intermediate', 'https://www.youtube.com/watch?v=r-uOLxNrNk8', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:10'),
(10, 'Cybersecurity Fundamentals', 'cybersecurity-fundamentals', 'Understand security basics, threats, protection and best practices.', 'Start cybersecurity path.', 220000.00, 'beginner', 'https://www.youtube.com/watch?v=U_P23SqJaDc', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:10'),
(11, 'Computer Fundamentals', 'computer-fundamentals', 'Learn computer basics, productivity tools and digital literacy.', 'Beginner computer skills.', 100000.00, 'beginner', 'https://www.youtube.com/watch?v=y2kg3MOk1sY', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:11'),
(12, 'Desktop Application Dev', 'desktop-application-dev', 'Build desktop applications with proper structure and deployment.', 'Desktop apps training.', 260000.00, 'intermediate', 'https://www.youtube.com/watch?v=YXPyB4XeYLA', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:11'),
(13, 'POS & ICT Support', 'pos-ict-support', 'POS operations, troubleshooting, and ICT support essentials.', 'POS support skills.', 120000.00, 'beginner', 'https://www.youtube.com/watch?v=y2kg3MOk1sY', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:11'),
(14, 'Networking Basics', 'networking-basics', 'Learn networking concepts, LAN/WAN, IP, routing and practical setup.', 'Networking essentials.', 180000.00, 'beginner', 'https://www.youtube.com/watch?v=IPvYjXCsTg8', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:11'),
(15, 'Cloud Computing', 'cloud-computing', 'Cloud concepts, deployments, services and practical cloud skills.', 'Cloud foundation.', 320000.00, 'intermediate', 'https://www.youtube.com/watch?v=SOTamWNgDKc', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:11'),
(16, 'Software Engineering', 'software-engineering', 'Advanced software engineering principles and system architecture.', 'Enterprise-level development training.', 350000.00, 'advanced', 'https://www.youtube.com/watch?v=O753uuutqH8', 'default', NULL, 1, '2026-02-13 13:48:31', '2026-04-12 05:01:11'),
(17, 'Data Science', 'data-science', 'Master data science from data wrangling to machine learning, statistical analysis, and real-world project deployment.', 'From raw data to actionable insights.', 380000.00, 'advanced', 'https://www.youtube.com/watch?v=ua-CiDNNj30', 'default', NULL, 1, '2026-04-15 07:00:00', '2026-04-15 01:44:51'),
(18, 'Artificial Intelligence (AI)', 'artificial-intelligence', 'Comprehensive AI course covering neural networks, deep learning, NLP, computer vision, and AI ethics with hands-on projects.', 'Build intelligent systems with AI.', 420000.00, 'advanced', 'https://www.youtube.com/watch?v=JMUxmLyrhSk', 'default', NULL, 1, '2026-04-15 07:00:00', '2026-04-15 01:44:51'),
(19, 'Machine Learning (ML)', 'machine-learning', 'Learn supervised, unsupervised, and reinforcement learning algorithms, model evaluation, feature engineering, and ML deployment.', 'Algorithms that learn from data.', 400000.00, 'advanced', 'https://www.youtube.com/watch?v=GwIo3gDZCVQ', 'default', NULL, 1, '2026-04-15 07:00:00', '2026-04-15 01:44:51');

-- --------------------------------------------------------

--
-- Table structure for table `lms_enrollments`
--

CREATE TABLE `lms_enrollments` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `next_due_date` date DEFAULT NULL,
  `access_expires_at` datetime DEFAULT NULL,
  `payment_type` enum('full','installment') NOT NULL DEFAULT 'full',
  `status` enum('active','paid','installment','expired','cancelled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `lms_exams`
--

CREATE TABLE `lms_exams` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `duration_minutes` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `total_marks` int(10) UNSIGNED DEFAULT NULL,
  `pass_mark` int(10) UNSIGNED NOT NULL DEFAULT 50,
  `total_questions` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_exams`
--

INSERT INTO `lms_exams` (`id`, `course_id`, `title`, `duration_minutes`, `total_marks`, `pass_mark`, `total_questions`, `is_published`, `created_at`) VALUES
(46, 1, 'Graphic Design - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(47, 2, 'Advanced Graphic Design - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(48, 3, 'Web Design - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(49, 4, 'Web Development - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(50, 5, 'PHP & MySQL Development - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(51, 6, 'Mobile App Development - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(52, 7, 'UI/UX Design - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(53, 8, 'Digital Marketing - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(54, 9, 'Data Analysis - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(55, 10, 'Cybersecurity Fundamentals - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(56, 11, 'Computer Fundamentals - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(12, 12, 'Desktop Application Dev - Final Exam', 40, 10, 50, 10, 1, '2026-04-19 03:45:43'),
(57, 13, 'POS & ICT Support - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(58, 14, 'Networking Basics - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(59, 15, 'Cloud Computing - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(60, 16, 'Software Engineering - Final Exam', 40, 10, 50, 10, 1, '2026-02-16 15:01:41'),
(61, 17, 'Data Science — Final Exam', 40, 10, 50, 10, 1, '2026-04-15 07:00:00'),
(62, 18, 'Artificial Intelligence — Final Exam', 40, 10, 50, 10, 1, '2026-04-15 07:00:00'),
(63, 19, 'Machine Learning — Final Exam', 40, 10, 50, 10, 1, '2026-04-15 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `lms_exam_questions`
--

CREATE TABLE `lms_exam_questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `exam_id` int(10) UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_option` enum('A','B','C','D') NOT NULL,
  `marks` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_exam_questions`
--

INSERT INTO `lms_exam_questions` (`id`, `exam_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `marks`, `created_at`) VALUES
(1, 46, 'What does the principle of \'contrast\' in design refer to?', 'Using the same colour throughout', 'Using opposing elements to create visual interest', 'Placing all elements in the centre', 'Removing all white space', 'B', 1, '2026-02-16 06:11:08'),
(2, 46, 'Which colour model is used for print design?', 'RGB', 'HSL', 'CMYK', 'HEX', 'C', 1, '2026-02-16 06:11:08'),
(3, 46, 'What is the minimum DPI for print-quality images?', '72', '150', '300', '600', 'C', 1, '2026-02-16 06:11:08'),
(4, 46, 'A logo that uses only the company\'s initials is called a:', 'Wordmark', 'Lettermark', 'Emblem', 'Combination mark', 'B', 1, '2026-02-16 06:11:08'),
(5, 46, 'Which Adobe tool is best for creating vector logos?', 'Photoshop', 'InDesign', 'Illustrator', 'Premiere', 'C', 1, '2026-02-16 06:11:08'),
(6, 46, 'What is \'bleed\' in print design?', 'Extra artwork beyond the trim edge', 'The ink that bleeds through paper', 'A type of font style', 'A colour correction technique', 'A', 1, '2026-02-16 06:11:08'),
(7, 46, 'The \'rule of thirds\' divides the canvas into how many equal parts?', '4', '6', '9', '12', 'C', 1, '2026-02-16 06:11:08'),
(8, 46, 'Which font category is best for body text on screens?', 'Script', 'Display', 'Serif', 'Sans-serif', 'D', 1, '2026-02-16 06:11:08'),
(9, 46, 'What does \'kerning\' refer to in typography?', 'Line spacing', 'Space between specific character pairs', 'Font weight', 'Letter height', 'B', 1, '2026-02-16 06:11:08'),
(10, 46, 'A brand\'s complete visual identity includes:', 'Only the logo', 'Logo, colours, and typography', 'Only the colour palette', 'Only the website design', 'B', 1, '2026-02-16 06:11:08'),
(11, 47, 'What is a \'type scale\' in typography?', 'A ruler for measuring fonts', 'A defined set of font sizes used consistently', 'A font family', 'A typographic error', 'B', 1, '2026-02-16 06:11:08'),
(12, 47, 'WCAG AA requires a minimum contrast ratio of:', '2:1', '3:1', '4.5:1', '7:1', 'C', 1, '2026-02-16 06:11:08'),
(13, 47, 'What is a \'variable font\'?', 'A font that changes colour', 'A font containing multiple styles in one file', 'A font used only for headings', 'A decorative font', 'B', 1, '2026-02-16 06:11:08'),
(14, 47, 'In motion design, \'easing\' refers to:', 'The speed of the entire animation', 'Controlling acceleration and deceleration', 'The direction of movement', 'The colour of the animation', 'B', 1, '2026-02-16 06:11:08'),
(15, 47, 'A \'dieline\' in packaging design is:', 'The final printed package', 'The flat unfolded template of a package', 'A cutting tool', 'A type of ink', 'B', 1, '2026-02-16 06:11:08'),
(16, 47, 'Brand architecture where one master brand covers everything is called:', 'House of Brands', 'Endorsed brand', 'Branded House', 'Sub-brand', 'C', 1, '2026-02-16 06:11:08'),
(17, 47, 'What is a \'kill fee\' in freelance design?', 'A fee for cancelling a project', 'A fee for rush work', 'A fee for extra revisions', 'A fee for file delivery', 'A', 1, '2026-02-16 06:11:08'),
(18, 47, 'The 12 principles of animation were developed by:', 'Adobe', 'Pixar', 'Disney', 'DreamWorks', 'C', 1, '2026-02-16 06:11:08'),
(19, 47, 'What does \'ASO\' stand for in mobile app marketing?', 'App Store Optimisation', 'Application Security Operations', 'Automated Software Output', 'App Scaling Options', 'A', 1, '2026-02-16 06:11:08'),
(20, 47, 'A \'positioning statement\' in branding defines:', 'The logo placement', 'Where the brand sits in the market relative to competitors', 'The physical location of the business', 'The price of the product', 'B', 1, '2026-02-16 06:11:08'),
(21, 48, 'What does \'responsive design\' mean?', 'A website that responds to user clicks', 'A website that adapts to different screen sizes', 'A website that loads quickly', 'A website with animations', 'B', 1, '2026-02-16 06:11:08'),
(22, 48, 'Which CSS property is used to create a flexible one-dimensional layout?', 'Grid', 'Flexbox', 'Float', 'Position', 'B', 1, '2026-02-16 06:11:08'),
(23, 48, 'What is the standard breakpoint for mobile devices?', '480px', '768px', '1024px', '1200px', 'B', 1, '2026-02-16 06:11:08'),
(24, 48, 'In Figma, what is a \'component\'?', 'A page in the design file', 'A reusable design element', 'A colour style', 'A font style', 'B', 1, '2026-02-16 06:11:08'),
(25, 48, 'What does LCP stand for in Core Web Vitals?', 'Largest Contentful Paint', 'Longest Content Period', 'Least Cumulative Performance', 'Last Content Processed', 'A', 1, '2026-02-16 06:11:08'),
(26, 48, 'Which HTML element is used for the main navigation?', '<header>', '<main>', '<nav>', '<section>', 'C', 1, '2026-02-16 06:11:08'),
(27, 48, 'What is the purpose of alt text on images?', 'To style the image', 'To describe the image for screen readers and SEO', 'To set the image size', 'To link the image', 'B', 1, '2026-02-16 06:11:08'),
(28, 48, 'CSS custom properties are also known as:', 'CSS classes', 'CSS variables', 'CSS functions', 'CSS selectors', 'B', 1, '2026-02-16 06:11:08'),
(29, 48, 'What is the \'F-pattern\' in web design?', 'A font naming convention', 'How users visually scan web content', 'A CSS layout technique', 'A colour scheme', 'B', 1, '2026-02-16 06:11:08'),
(30, 48, 'Which tool is the industry standard for UI/UX design?', 'Sketch', 'Adobe XD', 'Figma', 'InVision', 'C', 1, '2026-02-16 06:11:08'),
(31, 49, 'What does HTML stand for?', 'Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Transfer Markup Language', 'Home Tool Markup Language', 'A', 1, '2026-02-16 06:11:08'),
(32, 49, 'Which CSS property controls the space inside an element\'s border?', 'Margin', 'Padding', 'Border', 'Outline', 'B', 1, '2026-02-16 06:11:08'),
(33, 49, 'What is the correct way to declare a constant in JavaScript?', 'var x = 5', 'let x = 5', 'const x = 5', 'define x = 5', 'C', 1, '2026-02-16 06:11:08'),
(34, 49, 'Which PHP function hashes a password securely?', 'md5()', 'sha1()', 'password_hash()', 'encrypt()', 'C', 1, '2026-02-16 06:11:08'),
(35, 49, 'What does SQL stand for?', 'Structured Query Language', 'Simple Query Language', 'Standard Question Language', 'System Query Logic', 'A', 1, '2026-02-16 06:11:08'),
(36, 49, 'Which HTTP method is used to retrieve data from a server?', 'POST', 'PUT', 'GET', 'DELETE', 'C', 1, '2026-02-16 06:11:08'),
(37, 49, 'What is a prepared statement in PHP?', 'A pre-written SQL query', 'A parameterised query that prevents SQL injection', 'A stored procedure', 'A database view', 'B', 1, '2026-02-16 06:11:08'),
(38, 49, 'What does \'responsive design\' require in HTML?', 'A viewport meta tag', 'A CSS framework', 'JavaScript', 'A CDN', 'A', 1, '2026-02-16 06:11:08'),
(39, 49, 'Which command initialises a new Git repository?', 'git start', 'git init', 'git create', 'git new', 'B', 1, '2026-02-16 06:11:08'),
(40, 49, 'What is the purpose of a .env file?', 'Store HTML templates', 'Store environment variables and secrets', 'Store CSS styles', 'Store database records', 'B', 1, '2026-02-16 06:11:08'),
(41, 50, 'What does OOP stand for?', 'Object Oriented Programming', 'Open Output Processing', 'Ordered Object Protocol', 'Optional Output Parameters', 'A', 1, '2026-02-16 06:11:08'),
(42, 50, 'In PHP OOP, what keyword is used to create an object from a class?', 'create', 'make', 'new', 'build', 'C', 1, '2026-02-16 06:11:08'),
(43, 50, 'What is the difference between public and private in PHP classes?', 'Public methods are faster', 'Private members can only be accessed within the class', 'Public members cannot be inherited', 'Private methods are static', 'B', 1, '2026-02-16 06:11:08'),
(44, 50, 'Which PHP function verifies a hashed password?', 'password_check()', 'hash_verify()', 'password_verify()', 'verify_hash()', 'C', 1, '2026-02-16 06:11:08'),
(45, 50, 'What does PDO stand for?', 'PHP Data Objects', 'PHP Database Operations', 'Prepared Data Output', 'PHP Dynamic Objects', 'A', 1, '2026-02-16 06:11:08'),
(46, 50, 'Which SQL clause filters grouped results?', 'WHERE', 'HAVING', 'GROUP BY', 'ORDER BY', 'B', 1, '2026-02-16 06:11:08'),
(47, 50, 'What is a database transaction?', 'A payment record', 'A group of SQL operations that succeed or fail together', 'A database backup', 'A stored procedure', 'B', 1, '2026-02-16 06:11:08'),
(48, 50, 'What is CSRF?', 'Cross-Site Request Forgery', 'Cross-Server Resource Fetch', 'Client-Side Request Filter', 'Content Security Response Format', 'A', 1, '2026-02-16 06:11:08'),
(49, 50, 'Which HTTP status code means \'Created\'?', '200', '201', '400', '404', 'B', 1, '2026-02-16 06:11:08'),
(50, 50, 'What does JWT stand for?', 'Java Web Token', 'JSON Web Token', 'JavaScript Web Transfer', 'JSON Web Transfer', 'B', 1, '2026-02-16 06:11:08'),
(51, 51, 'What language does Flutter use?', 'JavaScript', 'Kotlin', 'Dart', 'Swift', 'C', 1, '2026-02-16 06:11:08'),
(52, 51, 'What is the difference between StatelessWidget and StatefulWidget?', 'StatelessWidget is faster', 'StatefulWidget can change over time', 'StatelessWidget uses more memory', 'StatefulWidget cannot be reused', 'B', 1, '2026-02-16 06:11:08'),
(53, 51, 'What does setState() do in Flutter?', 'Saves data to the database', 'Triggers a rebuild of the widget', 'Navigates to a new screen', 'Sends an HTTP request', 'B', 1, '2026-02-16 06:11:08'),
(54, 51, 'What is Firebase Firestore?', 'A SQL database', 'A NoSQL cloud database with real-time sync', 'A file storage service', 'An authentication service', 'B', 1, '2026-02-16 06:11:08'),
(55, 51, 'What is the difference between an APK and an App Bundle?', 'APK is for iOS, App Bundle is for Android', 'App Bundle is required for Play Store, APK is a direct install file', 'They are the same thing', 'APK is newer than App Bundle', 'B', 1, '2026-02-16 06:11:08'),
(56, 51, 'What does the Provider package do in Flutter?', 'Provides HTTP requests', 'Manages state across the widget tree', 'Provides database access', 'Provides animations', 'B', 1, '2026-02-16 06:11:08'),
(57, 51, 'What is ASO?', 'App Store Optimisation', 'Android System Operations', 'Application Security Overview', 'Automated Store Output', 'A', 1, '2026-02-16 06:11:08'),
(58, 51, 'Which Firebase service handles user login?', 'Firestore', 'Firebase Storage', 'Firebase Authentication', 'Firebase Analytics', 'C', 1, '2026-02-16 06:11:08'),
(59, 51, 'What is the minimum touch target size for mobile buttons?', '24x24px', '32x32px', '44x44px', '56x56px', 'C', 1, '2026-02-16 06:11:08'),
(60, 51, 'What does PWA stand for?', 'Progressive Web App', 'Portable Web Application', 'PHP Web App', 'Public Web Access', 'A', 1, '2026-02-16 06:11:08'),
(61, 52, 'What does UX stand for?', 'User Experience', 'User Extension', 'Unified Experience', 'User Execution', 'A', 1, '2026-02-16 06:11:08'),
(62, 52, 'How many users are typically needed to find 85% of usability issues?', '2', '5', '10', '20', 'B', 1, '2026-02-16 06:11:08'),
(63, 52, 'What is a user persona?', 'A real user account', 'A fictional but research-based representation of a target user', 'A user\'s password', 'A user interface element', 'B', 1, '2026-02-16 06:11:08'),
(64, 52, 'What is \'card sorting\' used for?', 'Sorting playing cards', 'Testing navigation structure with users', 'Organising design files', 'Creating colour palettes', 'B', 1, '2026-02-16 06:11:08'),
(65, 52, 'What does WCAG stand for?', 'Web Content Accessibility Guidelines', 'Web Coding and Graphics', 'Website Content and Graphics', 'Web Component Architecture Guide', 'A', 1, '2026-02-16 06:11:08'),
(66, 52, 'What is a \'skeleton screen\'?', 'A wireframe', 'A loading placeholder that shows the layout before content loads', 'A dark mode design', 'An empty state', 'B', 1, '2026-02-16 06:11:08'),
(67, 52, 'In Figma, what does \'Auto Layout\' do?', 'Automatically creates animations', 'Makes frames resize based on content, like CSS Flexbox', 'Automatically names layers', 'Exports designs automatically', 'B', 1, '2026-02-16 06:11:08'),
(68, 52, 'What is the Jobs-to-be-Done framework?', 'A project management method', 'A framework focusing on what job users hire a product to do', 'A design system', 'A testing methodology', 'B', 1, '2026-02-16 06:11:08'),
(69, 52, 'What is the minimum contrast ratio for normal text (WCAG AA)?', '2:1', '3:1', '4.5:1', '7:1', 'C', 1, '2026-02-16 06:11:08'),
(70, 52, 'What is a \'design system\'?', 'A collection of reusable components guided by clear standards', 'A project management tool', 'A type of software', 'A colour palette', 'A', 1, '2026-02-16 06:11:08'),
(71, 53, 'What does SEO stand for?', 'Search Engine Optimisation', 'Social Engagement Operations', 'Site Engagement Output', 'Search Engine Operations', 'A', 1, '2026-02-16 06:11:08'),
(72, 53, 'What is the ideal length for a title tag?', '20-30 characters', '50-60 characters', '80-100 characters', '150-160 characters', 'B', 1, '2026-02-16 06:11:08'),
(73, 53, 'What does CTR stand for?', 'Click Through Rate', 'Content Transfer Rate', 'Customer Tracking Report', 'Conversion Tracking Rate', 'A', 1, '2026-02-16 06:11:08'),
(74, 53, 'What is a \'lead magnet\'?', 'A type of advertisement', 'Something valuable offered in exchange for an email address', 'A social media post', 'A type of backlink', 'B', 1, '2026-02-16 06:11:08'),
(75, 53, 'What is the average ROI of email marketing?', '$5 per $1 spent', '$12 per $1 spent', '$36 per $1 spent', '$100 per $1 spent', 'C', 1, '2026-02-16 06:11:08'),
(76, 53, 'What does ROAS stand for?', 'Return on Ad Spend', 'Rate of Audience Segmentation', 'Revenue on All Sales', 'Return on Asset Strategy', 'A', 1, '2026-02-16 06:11:08'),
(77, 53, 'What are UTM parameters used for?', 'Tracking the source of website traffic', 'Improving page speed', 'Creating email templates', 'Setting up Google Ads', 'A', 1, '2026-02-16 06:11:08'),
(78, 53, 'What is the 80/20 rule in social media content?', '80% promotional, 20% educational', '80% valuable content, 20% promotional', '80% images, 20% text', '80% paid, 20% organic', 'B', 1, '2026-02-16 06:11:08'),
(79, 53, 'What does CPA stand for in digital marketing?', 'Cost Per Acquisition', 'Content Per Article', 'Click Per Advertisement', 'Customer Profile Analysis', 'A', 1, '2026-02-16 06:11:08'),
(80, 53, 'What is A/B testing?', 'Testing two versions to see which performs better', 'Testing a website on two browsers', 'Testing two different products', 'Testing two marketing teams', 'A', 1, '2026-02-16 06:11:08'),
(81, 54, 'What does the AVERAGE function calculate?', 'The most frequent value', 'The middle value', 'The sum divided by count', 'The highest value', 'C', 1, '2026-02-16 06:11:08'),
(82, 54, 'What is a pivot table used for?', 'Creating charts', 'Summarising large datasets quickly', 'Writing SQL queries', 'Formatting cells', 'B', 1, '2026-02-16 06:11:08'),
(83, 54, 'What does SQL GROUP BY do?', 'Sorts results', 'Groups rows with the same values for aggregation', 'Filters rows', 'Joins tables', 'B', 1, '2026-02-16 06:11:08'),
(84, 54, 'What is the difference between mean and median?', 'They are the same', 'Mean is the middle value; median is the average', 'Mean is the average; median is the middle value', 'Mean is the most frequent value', 'C', 1, '2026-02-16 06:11:08'),
(85, 54, 'What does LCP stand for in Core Web Vitals?', 'Largest Contentful Paint', 'Longest Content Period', 'Least Cumulative Performance', 'Last Content Processed', 'A', 1, '2026-02-16 06:11:08'),
(86, 54, 'What is a correlation coefficient of -0.9 closest to?', 'No correlation', 'Weak positive correlation', 'Strong negative correlation', 'Perfect positive correlation', 'C', 1, '2026-02-16 06:11:08'),
(87, 54, 'What does ETL stand for in data engineering?', 'Extract, Transform, Load', 'Edit, Test, Launch', 'Evaluate, Track, Log', 'Export, Transfer, Link', 'A', 1, '2026-02-16 06:11:08'),
(88, 54, 'Which Python library is used for data manipulation?', 'NumPy', 'Matplotlib', 'pandas', 'Seaborn', 'C', 1, '2026-02-16 06:11:08'),
(89, 54, 'What is a KPI?', 'Key Performance Indicator', 'Key Process Integration', 'Knowledge Performance Index', 'Key Product Information', 'A', 1, '2026-02-16 06:11:08'),
(90, 54, 'What chart type is best for showing a trend over time?', 'Pie chart', 'Bar chart', 'Line chart', 'Scatter plot', 'C', 1, '2026-02-16 06:11:08'),
(91, 55, 'What does the CIA triad stand for?', 'Confidentiality, Integrity, Availability', 'Cyber Intelligence Agency', 'Computer Information Access', 'Control, Identify, Analyse', 'A', 1, '2026-02-16 06:11:08'),
(92, 55, 'What is phishing?', 'A type of malware', 'Deceptive messages to steal credentials or install malware', 'A network attack', 'A password cracking technique', 'B', 1, '2026-02-16 06:11:08'),
(93, 55, 'What does SQL injection exploit?', 'Weak passwords', 'Unsanitised database queries', 'Open network ports', 'Unencrypted connections', 'B', 1, '2026-02-16 06:11:08'),
(94, 55, 'What is the purpose of a firewall?', 'Speed up internet connection', 'Monitor and control network traffic based on rules', 'Store passwords securely', 'Encrypt data', 'B', 1, '2026-02-16 06:11:08'),
(95, 55, 'What does HTTPS provide that HTTP does not?', 'Faster loading', 'Encrypted communication', 'Better SEO', 'Larger file transfers', 'B', 1, '2026-02-16 06:11:08'),
(96, 55, 'What is a VPN used for?', 'Speeding up internet', 'Encrypting traffic and hiding IP address', 'Blocking advertisements', 'Storing passwords', 'B', 1, '2026-02-16 06:11:08'),
(97, 55, 'What is the OWASP Top 10?', 'A list of top 10 websites', 'A standard reference for web application security risks', 'A list of top 10 hackers', 'A cybersecurity certification', 'B', 1, '2026-02-16 06:11:08'),
(98, 55, 'What is social engineering?', 'Building social media profiles', 'Manipulating people into revealing confidential information', 'A type of network attack', 'A programming technique', 'B', 1, '2026-02-16 06:11:08'),
(99, 55, 'What does 2FA stand for?', 'Two-Factor Authentication', 'Two-File Access', 'Two-Firewall Architecture', 'Two-Form Application', 'A', 1, '2026-02-16 06:11:08'),
(100, 55, 'What is the principle of least privilege?', 'Give users maximum access', 'Grant only the minimum permissions necessary', 'Share passwords with the team', 'Use the same password everywhere', 'B', 1, '2026-02-16 06:11:08'),
(101, 56, 'What does CPU stand for?', 'Central Processing Unit', 'Computer Power Unit', 'Central Program Utility', 'Core Processing Unit', 'A', 1, '2026-02-16 06:11:08'),
(102, 56, 'What is the difference between RAM and storage?', 'RAM is permanent; storage is temporary', 'RAM is temporary working memory; storage is permanent', 'They are the same thing', 'RAM is slower than storage', 'B', 1, '2026-02-16 06:11:08'),
(103, 56, 'Which storage type is faster?', 'HDD', 'SSD', 'CD-ROM', 'Floppy disk', 'B', 1, '2026-02-16 06:11:08'),
(104, 56, 'What does OS stand for?', 'Online System', 'Operating System', 'Output Software', 'Open Source', 'B', 1, '2026-02-16 06:11:08'),
(105, 56, 'Which command shows your IP address on Windows?', 'ipconfig', 'netstat', 'ping', 'tracert', 'A', 1, '2026-02-16 06:11:08'),
(106, 56, 'What is the difference between CC and BCC in email?', 'CC is faster than BCC', 'BCC recipients are hidden from other recipients', 'CC is for attachments', 'BCC is for urgent emails', 'B', 1, '2026-02-16 06:11:08'),
(107, 56, 'What does HTTPS mean?', 'Hyper Text Transfer Protocol Secure', 'High Tech Transfer Protocol System', 'Hyper Text Transfer Protocol Standard', 'Home Transfer Protocol Secure', 'A', 1, '2026-02-16 06:11:08'),
(108, 56, 'What is a phishing email?', 'A spam email', 'A fake email designed to steal your information', 'An email with attachments', 'An email from an unknown sender', 'B', 1, '2026-02-16 06:11:08'),
(109, 56, 'What is the 10-20-30 rule for presentations?', '10 slides, 20 minutes, 30pt minimum font', '10 minutes, 20 slides, 30 words per slide', '10 points, 20 images, 30 seconds', '10 colours, 20 fonts, 30 slides', 'A', 1, '2026-02-16 06:11:08'),
(110, 56, 'What does VLOOKUP do in Excel?', 'Calculates the average', 'Looks up a value in a table and returns a related value', 'Sorts data alphabetically', 'Creates a chart', 'B', 1, '2026-02-16 06:11:08'),
(111, 12, 'What is Tkinter?', 'A Python web framework', 'Python\'s built-in GUI library', 'A database library', 'A testing framework', 'B', 1, '2026-02-16 06:11:08'),
(112, 12, 'What does root.mainloop() do in Tkinter?', 'Closes the application', 'Starts the event loop and keeps the window open', 'Creates a new window', 'Saves the application', 'B', 1, '2026-02-16 06:11:08'),
(113, 12, 'What is PyInstaller used for?', 'Testing Python code', 'Packaging Python apps into standalone executables', 'Installing Python packages', 'Debugging Python code', 'B', 1, '2026-02-16 06:11:08'),
(114, 12, 'What is SQLite?', 'A cloud database', 'A lightweight file-based database', 'A web database', 'A NoSQL database', 'B', 1, '2026-02-16 06:11:08'),
(115, 12, 'Why should long-running operations run in a background thread?', 'To use less memory', 'To keep the UI responsive', 'To run faster', 'To save battery', 'B', 1, '2026-02-16 06:11:08'),
(116, 12, 'What is semantic versioning format?', 'date.month.year', 'MAJOR.MINOR.PATCH', 'version.build.release', 'alpha.beta.stable', 'B', 1, '2026-02-16 06:11:08'),
(117, 12, 'What does the --onefile flag do in PyInstaller?', 'Creates multiple files', 'Bundles everything into a single executable', 'Installs the app', 'Creates a shortcut', 'B', 1, '2026-02-16 06:11:08'),
(118, 12, 'What is a PyQt5 Signal?', 'A network request', 'A mechanism to communicate between threads and widgets', 'A database connection', 'A file operation', 'B', 1, '2026-02-16 06:11:08'),
(119, 12, 'What is the difference between a desktop app and a web app?', 'Desktop apps are always free', 'Desktop apps run natively on the OS; web apps run in a browser', 'Web apps are always faster', 'Desktop apps require internet', 'B', 1, '2026-02-16 06:11:08'),
(120, 12, 'What does conn.row_factory = sqlite3.Row do?', 'Speeds up queries', 'Allows accessing columns by name instead of index', 'Creates a new table', 'Deletes all rows', 'B', 1, '2026-02-16 06:11:08'),
(121, 57, 'What does POS stand for?', 'Point of Sale', 'Point of Service', 'Payment Operations System', 'Purchase Order System', 'A', 1, '2026-02-16 06:11:08'),
(122, 57, 'What is the purpose of a receipt printer in a POS system?', 'To scan barcodes', 'To print customer receipts', 'To process card payments', 'To display prices', 'B', 1, '2026-02-16 06:11:08'),
(123, 57, 'What should you do at the end of each business day with a POS system?', 'Turn it off immediately', 'Reconcile the cash drawer and back up data', 'Delete all transactions', 'Change the password', 'B', 1, '2026-02-16 06:11:08'),
(124, 57, 'What are the 3 tiers of ICT support?', 'Basic, Advanced, Expert', 'Help Desk, Technical Support, Expert Support', 'Level 1, Level 2, Level 3', 'All of the above', 'D', 1, '2026-02-16 06:11:08'),
(125, 57, 'What is a runbook in ICT support?', 'A physical book', 'Step-by-step procedures for common tasks', 'A list of employees', 'A network diagram', 'B', 1, '2026-02-16 06:11:08'),
(126, 57, 'Which Wi-Fi security protocol should you use?', 'WEP', 'WPA', 'WPA2 or WPA3', 'No security needed', 'C', 1, '2026-02-16 06:11:08'),
(127, 57, 'What is active listening?', 'Listening while doing other tasks', 'Fully concentrating on what the speaker is saying', 'Listening to music', 'Recording a conversation', 'B', 1, '2026-02-16 06:11:08'),
(128, 57, 'What does DHCP do?', 'Encrypts network traffic', 'Automatically assigns IP addresses to devices', 'Blocks malicious websites', 'Speeds up internet', 'B', 1, '2026-02-16 06:11:08'),
(129, 57, 'What is the CompTIA A+ certification for?', 'Network engineering', 'Entry-level IT support', 'Cybersecurity', 'Cloud computing', 'B', 1, '2026-02-16 06:11:08'),
(130, 57, 'What is the first step when a POS terminal stops working?', 'Call the vendor immediately', 'Check the power cable and restart the terminal', 'Replace the terminal', 'Refund all customers', 'B', 1, '2026-02-16 06:11:08'),
(131, 58, 'What does LAN stand for?', 'Large Area Network', 'Local Area Network', 'Linked Access Node', 'Long Area Network', 'B', 1, '2026-02-16 06:11:08'),
(132, 58, 'How many layers does the OSI model have?', '4', '5', '7', '10', 'C', 1, '2026-02-16 06:11:08'),
(133, 58, 'What is the difference between a switch and a router?', 'They are the same', 'A switch connects devices within a network; a router connects different networks', 'A router is faster', 'A switch connects to the internet', 'B', 1, '2026-02-16 06:11:08'),
(134, 58, 'What does CIDR /24 mean?', '24 devices on the network', '24 bits are the network portion', '24 available IP addresses', '24 subnets', 'B', 1, '2026-02-16 06:11:08'),
(135, 58, 'How many usable hosts does a /24 network have?', '254', '256', '255', '252', 'A', 1, '2026-02-16 06:11:08'),
(136, 58, 'What is NAT?', 'Network Address Translation', 'Network Access Token', 'Node Address Table', 'Network Authentication Type', 'A', 1, '2026-02-16 06:11:08'),
(137, 58, 'What is the difference between 2.4 GHz and 5 GHz Wi-Fi?', '2.4 GHz is faster', '5 GHz has longer range', '2.4 GHz has longer range but is slower; 5 GHz is faster but shorter range', 'They are identical', 'C', 1, '2026-02-16 06:11:08'),
(138, 58, 'What does DNS do?', 'Encrypts network traffic', 'Translates domain names to IP addresses', 'Assigns IP addresses', 'Blocks malicious websites', 'B', 1, '2026-02-16 06:11:08'),
(139, 58, 'What is a VLAN?', 'A type of cable', 'A logical network segment within a physical network', 'A wireless network', 'A type of router', 'B', 1, '2026-02-16 06:11:08'),
(140, 58, 'What is the implicit deny rule in firewall configuration?', 'Allow all traffic by default', 'Deny all traffic not explicitly allowed', 'Allow traffic from trusted IPs', 'Deny traffic from unknown countries', 'B', 1, '2026-02-16 06:11:08'),
(141, 59, 'What does IaaS stand for?', 'Internet as a Service', 'Infrastructure as a Service', 'Integration as a Service', 'Information as a Service', 'B', 1, '2026-02-16 06:11:08'),
(142, 59, 'What is the difference between public and private cloud?', 'Public cloud is free', 'Private cloud is operated solely for one organisation', 'Public cloud is more secure', 'Private cloud is always cheaper', 'B', 1, '2026-02-16 06:11:08'),
(143, 59, 'What does S3 stand for in AWS?', 'Simple Storage Service', 'Secure Server System', 'Scalable Storage Solution', 'Standard Storage Service', 'A', 1, '2026-02-16 06:11:08'),
(144, 59, 'What is Docker used for?', 'Writing Python code', 'Packaging applications in containers', 'Managing databases', 'Monitoring servers', 'B', 1, '2026-02-16 06:11:08'),
(145, 59, 'What is the difference between a Docker image and a container?', 'They are the same', 'An image is a template; a container is a running instance of an image', 'A container is a template; an image is running', 'Images are larger than containers', 'B', 1, '2026-02-16 06:11:08'),
(146, 59, 'What does CI/CD stand for?', 'Computer Integration/Computer Deployment', 'Continuous Integration/Continuous Delivery', 'Cloud Infrastructure/Cloud Deployment', 'Code Integration/Code Delivery', 'B', 1, '2026-02-16 06:11:08'),
(147, 59, 'What is Infrastructure as Code?', 'Writing code on a server', 'Defining infrastructure in code files for repeatability', 'A type of programming language', 'A cloud service', 'B', 1, '2026-02-16 06:11:08'),
(148, 59, 'What is the AWS Well-Architected Framework?', 'A type of server', '6 pillars for building reliable, secure, efficient cloud systems', 'A programming framework', 'A database design pattern', 'B', 1, '2026-02-16 06:11:08'),
(149, 59, 'What does Auto Scaling do?', 'Manually adds servers', 'Automatically adjusts the number of instances based on demand', 'Scales the database', 'Scales the network', 'B', 1, '2026-02-16 06:11:08'),
(150, 59, 'What is a CDN?', 'A type of database', 'A network of servers that delivers content from the closest location to the user', 'A cloud database', 'A container service', 'B', 1, '2026-02-16 06:11:08'),
(151, 60, 'What does SDLC stand for?', 'Software Development Life Cycle', 'System Design and Launch Cycle', 'Software Deployment and Launch Cycle', 'System Development and Launch Cycle', 'A', 1, '2026-02-16 06:11:08'),
(152, 60, 'What is the difference between Waterfall and Agile?', 'Waterfall is faster', 'Waterfall is sequential; Agile is iterative and adaptive', 'Agile is older than Waterfall', 'They are the same methodology', 'B', 1, '2026-02-16 06:11:08'),
(153, 60, 'What does SOLID stand for in OOP?', 'Single, Open, Liskov, Interface, Dependency', 'Simple, Object, Linked, Integrated, Dynamic', 'Standard, Open, Linked, Interface, Dependency', 'Single, Object, Linked, Integrated, Dependency', 'A', 1, '2026-02-16 06:11:08'),
(154, 60, 'What is a user story?', 'A blog post about users', 'A feature description from the user\'s perspective: As a [user], I want [goal] so that [reason]', 'A user manual', 'A database record', 'B', 1, '2026-02-16 06:11:08'),
(155, 60, 'What is TDD?', 'Test-Driven Development', 'Technology Design Document', 'Test Data Definition', 'Technical Design Diagram', 'A', 1, '2026-02-16 06:11:08'),
(156, 60, 'What is the Repository pattern?', 'A Git repository', 'An abstraction layer for data access logic', 'A type of database', 'A design pattern for UI', 'B', 1, '2026-02-16 06:11:08'),
(157, 60, 'What does the Single Responsibility Principle state?', 'A class should have many responsibilities', 'A class should have only one reason to change', 'A class should be private', 'A class should not be inherited', 'B', 1, '2026-02-16 06:11:08'),
(158, 60, 'What is a Sprint in Scrum?', 'A fast run', 'A time-boxed iteration of 1-4 weeks', 'A type of meeting', 'A project phase', 'B', 1, '2026-02-16 06:11:08'),
(159, 60, 'What is horizontal scaling?', 'Adding more resources to one server', 'Adding more servers to handle load', 'Scaling the database', 'Scaling the network', 'B', 1, '2026-02-16 06:11:08'),
(160, 60, 'What is caching used for?', 'Storing user passwords', 'Storing frequently accessed data in fast memory to reduce database load', 'Backing up data', 'Encrypting data', 'B', 1, '2026-02-16 06:11:08'),
(161, 61, 'What does the acronym EDA stand for in data science?', 'Exploratory Data Analysis', 'Extended Data Architecture', 'Evaluated Data Algorithm', 'Extracted Data Application', 'A', 1, '2026-04-15 07:00:00'),
(162, 61, 'Which Python library is primarily used for data manipulation and analysis?', 'NumPy', 'Matplotlib', 'pandas', 'Seaborn', 'C', 1, '2026-04-15 07:00:00'),
(163, 61, 'What is the purpose of the train/test split in machine learning?', 'To speed up training', 'To evaluate model performance on unseen data', 'To reduce the dataset size', 'To clean the data', 'B', 1, '2026-04-15 07:00:00'),
(164, 61, 'Which metric measures the proportion of variance explained by a regression model?', 'MAE', 'RMSE', 'R-squared (R2)', 'MAPE', 'C', 1, '2026-04-15 07:00:00'),
(165, 61, 'What does \'data wrangling\' refer to?', 'Visualising data', 'Collecting data from APIs', 'Cleaning and transforming raw data into a usable format', 'Training machine learning models', 'C', 1, '2026-04-15 07:00:00'),
(166, 61, 'Which of the following is NOT a measure of central tendency?', 'Mean', 'Median', 'Standard Deviation', 'Mode', 'C', 1, '2026-04-15 07:00:00'),
(167, 61, 'What is a p-value in hypothesis testing?', 'The probability that the null hypothesis is true', 'The probability of observing results as extreme as the data, assuming the null hypothesis is true', 'The significance level', 'The test statistic', 'B', 1, '2026-04-15 07:00:00'),
(168, 61, 'Which tool is used to build interactive data dashboards in Python?', 'Matplotlib', 'Streamlit', 'NumPy', 'scikit-learn', 'B', 1, '2026-04-15 07:00:00'),
(169, 61, 'What are the 5 Vs of Big Data?', 'Volume, Velocity, Variety, Veracity, Value', 'Volume, Vision, Variety, Veracity, Value', 'Volume, Velocity, Variety, Validity, Value', 'Volume, Velocity, Vision, Veracity, Value', 'A', 1, '2026-04-15 07:00:00'),
(170, 61, 'What is the main advantage of Apache Spark over traditional Hadoop MapReduce?', 'It is free to use', 'It processes data in-memory, making it up to 100x faster', 'It requires less storage', 'It supports more programming languages', 'B', 1, '2026-04-15 07:00:00'),
(171, 62, 'What type of AI is designed for one specific task, such as a spam filter?', 'General AI (AGI)', 'Super AI (ASI)', 'Narrow AI (ANI)', 'Symbolic AI', 'C', 1, '2026-04-15 07:00:00'),
(172, 62, 'Which year was the term \'Artificial Intelligence\' first coined?', '1950', '1956', '1969', '1980', 'B', 1, '2026-04-15 07:00:00'),
(173, 62, 'What is the activation function most commonly used in hidden layers of neural networks?', 'Sigmoid', 'Tanh', 'Softmax', 'ReLU', 'D', 1, '2026-04-15 07:00:00'),
(174, 62, 'What is transfer learning in the context of deep learning?', 'Training a model from scratch on a new dataset', 'Using a pre-trained model as a starting point for a new task', 'Transferring data between servers', 'Moving a model from development to production', 'B', 1, '2026-04-15 07:00:00'),
(175, 62, 'What does NLP stand for?', 'Neural Learning Process', 'Natural Language Processing', 'Network Layer Protocol', 'Numerical Learning Pipeline', 'B', 1, '2026-04-15 07:00:00'),
(176, 62, 'What is the key innovation of the Transformer architecture introduced in 2017?', 'Convolutional layers for text', 'Self-attention mechanism allowing each token to attend to all others', 'Recurrent connections for sequence processing', 'Pooling layers for dimensionality reduction', 'B', 1, '2026-04-15 07:00:00'),
(177, 62, 'What is \'hallucination\' in the context of Large Language Models?', 'When the model crashes', 'When the model generates false but confident information', 'When the model is too slow', 'When the model refuses to answer', 'B', 1, '2026-04-15 07:00:00'),
(178, 62, 'What is RAG in AI?', 'Random Aggregation Generator', 'Retrieval-Augmented Generation — combining LLMs with a knowledge base', 'Recurrent Attention Gate', 'Regularised Activation Graph', 'B', 1, '2026-04-15 07:00:00'),
(179, 62, 'What is algorithmic bias in AI?', 'When an algorithm runs too slowly', 'When an AI system produces systematically unfair outcomes due to biased training data or design', 'When an algorithm has too many parameters', 'When an AI model overfits the training data', 'B', 1, '2026-04-15 07:00:00'),
(180, 62, 'Which framework is most commonly used for building neural networks in Python?', 'scikit-learn', 'pandas', 'TensorFlow/Keras', 'Matplotlib', 'C', 1, '2026-04-15 07:00:00'),
(181, 63, 'What is the difference between supervised and unsupervised learning?', 'Supervised uses more data', 'Supervised learns from labelled examples; unsupervised finds patterns in unlabelled data', 'Supervised is faster', 'Unsupervised requires more computing power', 'B', 1, '2026-04-15 07:00:00'),
(182, 63, 'What is overfitting in machine learning?', 'When a model is too simple to learn the data', 'When a model performs well on training data but poorly on new data', 'When a model takes too long to train', 'When a model has too few parameters', 'B', 1, '2026-04-15 07:00:00'),
(183, 63, 'What does SMOTE stand for and what is it used for?', 'Supervised Model Optimisation Technique — for speeding up training', 'Synthetic Minority Over-sampling Technique — for handling imbalanced datasets', 'Standard Model Output Testing — for evaluation', 'Stochastic Model Optimisation Training — for gradient descent', 'B', 1, '2026-04-15 07:00:00'),
(184, 63, 'Which metric is most appropriate for evaluating a fraud detection model?', 'Accuracy', 'Mean Squared Error', 'Recall (to minimise missed fraud cases)', 'R-squared', 'C', 1, '2026-04-15 07:00:00'),
(185, 63, 'What is the purpose of cross-validation?', 'To split data into training and test sets', 'To get a more reliable estimate of model performance by training on multiple data splits', 'To clean the training data', 'To reduce the number of features', 'B', 1, '2026-04-15 07:00:00'),
(186, 63, 'What is the \'elbow method\' used for in K-Means clustering?', 'Determining the optimal learning rate', 'Finding the optimal number of clusters (k)', 'Selecting the best features', 'Evaluating model accuracy', 'B', 1, '2026-04-15 07:00:00'),
(187, 63, 'What does PCA stand for and what does it do?', 'Principal Component Analysis — reduces dimensionality while retaining maximum variance', 'Predictive Clustering Algorithm — groups similar data points', 'Polynomial Coefficient Adjustment — tunes model parameters', 'Probabilistic Classification Approach — classifies data probabilistically', 'A', 1, '2026-04-15 07:00:00'),
(188, 63, 'What is data drift in the context of deployed ML models?', 'When training data is corrupted', 'When the statistical properties of production data change over time, degrading model performance', 'When the model\'s code has a bug', 'When the model is retrained too frequently', 'B', 1, '2026-04-15 07:00:00'),
(189, 63, 'What is MLflow used for?', 'Building neural networks', 'Tracking ML experiments, parameters, metrics, and models', 'Deploying models to mobile devices', 'Cleaning and preprocessing data', 'B', 1, '2026-04-15 07:00:00'),
(190, 63, 'What is the difference between L1 (Lasso) and L2 (Ridge) regularisation?', 'L1 is faster; L2 is more accurate', 'L1 can reduce some coefficients to exactly zero (feature selection); L2 shrinks all coefficients but rarely to zero', 'L1 is for regression; L2 is for classification', 'L1 uses squared penalties; L2 uses absolute value penalties', 'B', 1, '2026-04-15 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `lms_exam_results`
--

CREATE TABLE `lms_exam_results` (
  `id` int(10) UNSIGNED NOT NULL,
  `exam_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `score` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` enum('pass','fail') NOT NULL DEFAULT 'fail',
  `taken_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_instructors`
--

CREATE TABLE `lms_instructors` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `experience_years` tinyint(3) UNSIGNED DEFAULT 0,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_instructor_courses`
--

CREATE TABLE `lms_instructor_courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `instructor_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_lessons`
--

CREATE TABLE `lms_lessons` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` mediumtext DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_lessons`
--

INSERT INTO `lms_lessons` (`id`, `course_id`, `title`, `content`, `sort_order`, `is_published`, `created_at`) VALUES
(1, 1, 'Introduction to Graphic Design', '## What is Graphic Design?\r\n\r\nGraphic design is the art of communicating ideas through visual elements — typography, colour, imagery, and layout. It appears in logos, posters, websites, packaging, and every piece of visual communication around us.\r\n\r\n## The 6 Principles of Design\r\n\r\n**1. Balance** — Distribute visual weight evenly. Symmetrical balance feels formal; asymmetrical balance feels dynamic.\r\n**2. Contrast** — Use opposing elements (dark/light, large/small, bold/thin) to create visual interest and hierarchy.\r\n**3. Alignment** — Every element should have a visual connection to something else on the page.\r\n**4. Repetition** — Repeat visual elements (colours, fonts, shapes) to create consistency and unity.\r\n**5. Proximity** — Group related items together. Unrelated items should be separated.\r\n**6. White Space** — Intentional empty space gives designs room to breathe and directs the eye.\r\n\r\n## Tools of the Trade\r\n\r\n- **Adobe Illustrator** — Vector graphics, logos, icons\r\n- **Adobe Photoshop** — Photo editing, raster graphics, compositing\r\n- **Adobe InDesign** — Multi-page layouts, brochures, books\r\n- **Canva** — Quick designs, social media, presentations\r\n\r\n## Raster vs Vector\r\n\r\n**Raster** images (JPG, PNG) are made of pixels. They lose quality when scaled up. Use for photos.\r\n**Vector** images (SVG, AI, EPS) are made of mathematical paths. They scale infinitely. Use for logos.\r\n\r\n## Practical Task\r\n\r\nOpen Canva (free at canva.com). Create a simple A4 event poster. Apply at least 4 of the 6 design principles. Export as PDF and review your work against each principle.\r\n\r\n## Self-Check\r\n1. Name the 6 principles of design and give one example of each.\r\n2. What is the difference between raster and vector graphics?\r\n3. Which tool would you use to design a company logo and why?', 1, 1, '2026-02-16 06:11:08'),
(2, 1, 'Colour Theory & Typography', '## Colour Theory\r\n\r\nColour is one of the most powerful tools in design. Understanding how colours interact helps you create harmonious, effective designs.\r\n\r\n### The Colour Wheel\r\n- **Primary**: Red, Blue, Yellow\r\n- **Secondary**: Orange, Green, Violet\r\n- **Tertiary**: Red-orange, Yellow-green, etc.\r\n\r\n### Colour Harmonies\r\n- **Complementary**: Opposite on the wheel (blue & orange). High contrast.\r\n- **Analogous**: Adjacent colours (blue, blue-green, green). Harmonious.\r\n- **Triadic**: Three evenly spaced colours. Vibrant yet balanced.\r\n- **Monochromatic**: Tints and shades of one colour. Elegant and cohesive.\r\n\r\n### Colour Psychology\r\n- Red: Energy, urgency, passion\r\n- Blue: Trust, calm, professionalism\r\n- Green: Growth, health, nature\r\n- Yellow: Optimism, warmth, attention\r\n- Black: Elegance, power, sophistication\r\n\r\n## Typography\r\n\r\nTypography is the art of arranging type to make written language legible, readable, and visually appealing.\r\n\r\n### Font Categories\r\n- **Serif** (Times New Roman, Georgia): Traditional, formal, trustworthy\r\n- **Sans-serif** (Inter, Arial): Modern, clean, digital-friendly\r\n- **Script**: Elegant, personal, decorative — use sparingly\r\n- **Display**: Bold, expressive — headlines only\r\n\r\n### Typography Rules\r\n1. Limit to 2-3 fonts per design\r\n2. Establish clear hierarchy: Heading > Subheading > Body > Caption\r\n3. Ensure sufficient contrast between text and background\r\n4. Use line spacing of 1.4-1.6x the font size for body text\r\n\r\n## Practical Task\r\n\r\nDesign a business card (85mm x 55mm) for a fictional professional. Choose a complementary colour palette. Use one serif and one sans-serif font. Apply proper typographic hierarchy.\r\n\r\n## Self-Check\r\n1. What are complementary colours? Give a real-world brand example.\r\n2. Why should you limit fonts in a design?\r\n3. What does colour psychology mean for brand identity?', 2, 1, '2026-02-16 06:11:08'),
(3, 1, 'Logo Design & Branding', '## What is a Brand?\r\n\r\nA brand is the complete identity of a business — how it looks, sounds, and feels to its audience. It includes the logo, colours, typography, tone of voice, and core values.\r\n\r\n## The 5 Qualities of a Great Logo\r\n\r\n1. **Simple** — Works at any size, instantly recognisable\r\n2. **Memorable** — Leaves a lasting impression\r\n3. **Timeless** — Avoids trends that date quickly\r\n4. **Versatile** — Works in colour, black & white, large and small\r\n5. **Appropriate** — Fits the industry and target audience\r\n\r\n## Logo Types\r\n- **Wordmark**: Styled company name (Google, Coca-Cola)\r\n- **Lettermark**: Initials only (IBM, HP)\r\n- **Icon/Symbol**: Standalone graphic (Apple, Nike swoosh)\r\n- **Combination mark**: Icon + wordmark (Adidas, Burger King)\r\n- **Emblem**: Text inside a symbol (Starbucks, Harley-Davidson)\r\n\r\n## The Logo Design Process\r\n1. Brief — Understand the client, audience, and goals\r\n2. Research — Study competitors and industry trends\r\n3. Sketch — Generate 15-20 rough concepts on paper\r\n4. Refine — Select 3 strongest and develop digitally\r\n5. Present — Show options in context with rationale\r\n6. Deliver — SVG, AI, EPS, PNG, PDF in colour and black & white\r\n\r\n## Practical Task\r\n\r\nDesign a logo for a fictional tech startup called \'NovaByte\'. Create 3 concepts on paper, then develop the strongest in Illustrator or Canva. Present it on white and dark backgrounds.\r\n\r\n## Self-Check\r\n1. What are the 5 qualities of a great logo?\r\n2. What is the difference between a wordmark and a combination mark?\r\n3. Why must logos be delivered in vector format?', 3, 1, '2026-02-16 06:11:08'),
(4, 1, 'Print Design & Layout', '## Print vs Digital Design\r\n\r\nPrint design requires understanding physical production constraints. Getting these wrong results in costly reprints.\r\n\r\n## Essential Print Concepts\r\n\r\n**Bleed**: Extra artwork (3mm) beyond the trim edge. Prevents white borders after cutting.\r\n**Trim**: The final cut size of the printed piece.\r\n**Safe Zone**: Keep all important content at least 5mm inside the trim line.\r\n**CMYK**: Cyan, Magenta, Yellow, Key (Black) — the colour model used in printing.\r\n**DPI**: Dots Per Inch — minimum 300 DPI for sharp print quality.\r\n\r\n## Common Print Products & Sizes\r\n- Business card: 85mm x 55mm\r\n- A4 flyer: 210mm x 297mm\r\n- Tri-fold brochure: 99mm x 210mm per panel\r\n- Pull-up banner: 850mm x 2000mm\r\n\r\n## Layout & Grid Systems\r\n\r\nGrids provide structure and consistency:\r\n- **Column grid**: Used in magazines and newspapers\r\n- **Modular grid**: Rows and columns for complex layouts\r\n- **Baseline grid**: Aligns text across columns\r\n\r\n## File Preparation Checklist\r\n- Document set to CMYK colour mode\r\n- Resolution 300 DPI minimum\r\n- Bleed set to 3mm on all sides\r\n- All fonts embedded or outlined\r\n- Export as PDF/X-1a for print\r\n\r\n## Practical Task\r\n\r\nDesign a tri-fold brochure for a fictional restaurant. Set up the document with correct bleed and safe zones. Include a menu section, about section, and contact details. Export as print-ready PDF.\r\n\r\n## Self-Check\r\n1. What is bleed and why is it important in print design?\r\n2. What colour mode should print designs use?\r\n3. What is the minimum DPI for print quality?', 4, 1, '2026-02-16 06:11:08'),
(5, 1, 'Digital & Social Media Design', '## Designing for Digital Platforms\r\n\r\nDigital design differs from print: screens use RGB colour, resolution is in pixels, and designs must be optimised for fast loading and mobile viewing.\r\n\r\n## Key Social Media Dimensions (2025)\r\n\r\n- Instagram Square post: 1080 x 1080px\r\n- Instagram Portrait post: 1080 x 1350px\r\n- Instagram Story/Reel: 1080 x 1920px\r\n- Facebook Cover photo: 820 x 312px\r\n- Twitter/X Header: 1500 x 500px\r\n- LinkedIn Banner: 1584 x 396px\r\n- YouTube Thumbnail: 1280 x 720px\r\n\r\n## Design for Engagement\r\n\r\n1. Mobile-first: Most users view on phones — use large, readable text\r\n2. 3-second rule: Your message must be clear within 3 seconds\r\n3. Brand consistency: Use your brand colours and fonts on every post\r\n4. Clear CTA: Every post should have one clear call-to-action\r\n5. File optimisation: Use WebP or compressed PNG/JPG for fast loading\r\n\r\n## Content Types\r\n- Static posts: Single image or graphic\r\n- Carousel posts: Multiple swipeable images (high engagement)\r\n- Stories: Vertical, ephemeral, interactive\r\n- Reels/Short video: Highest organic reach on most platforms\r\n- Infographics: Data visualisation, highly shareable\r\n\r\n## Practical Task\r\n\r\nCreate a 3-post social media campaign for a fictional product launch. Design for Instagram (1080x1080), Facebook (1200x630), and an Instagram Story (1080x1920). Maintain consistent branding across all three.\r\n\r\n## Self-Check\r\n1. What colour mode do digital designs use?\r\n2. What are the correct Instagram square post dimensions?\r\n3. Why is mobile-first design important for social media?', 5, 1, '2026-02-16 06:11:08'),
(6, 1, 'Photo Editing & Retouching', '## Adobe Photoshop Fundamentals\r\n\r\nPhotoshop is the industry standard for photo editing, compositing, and digital art.\r\n\r\n## Non-Destructive Workflow\r\n\r\nAlways edit non-destructively — preserve the original image so you can undo any change:\r\n- Use Adjustment Layers instead of direct adjustments\r\n- Use Layer Masks instead of erasing\r\n- Use Smart Objects to preserve original image data\r\n- Work with Layers — never flatten until final export\r\n\r\n## Essential Tools\r\n\r\n- Crop Tool: Resize and reframe images\r\n- Quick Selection / Magic Wand: Select areas by colour/tone\r\n- Pen Tool: Precise path-based selections\r\n- Healing Brush: Remove blemishes, blend with surroundings\r\n- Clone Stamp: Copy pixels from one area to another\r\n- Adjustment Layers: Non-destructive colour/tone corrections\r\n- Camera Raw Filter: Professional RAW photo processing\r\n\r\n## Colour Correction Workflow\r\n\r\n1. Open in Camera Raw — fix white balance and exposure\r\n2. Check histogram — identify clipping in highlights/shadows\r\n3. Apply Curves adjustment layer — fine-tune contrast\r\n4. Adjust Hue/Saturation — correct specific colour ranges\r\n5. Add Vibrance (not Saturation) for natural colour boost\r\n6. Sharpen using Smart Sharpen or Unsharp Mask\r\n7. Export: JPG 72 DPI for web, TIFF 300 DPI for print\r\n\r\n## Practical Task\r\n\r\nTake a portrait photo (your own or a free stock image from Unsplash). Perform: skin smoothing with Healing Brush, background removal with Select Subject, colour grading with Curves, and add a text overlay. Export as JPG for web.\r\n\r\n## Self-Check\r\n1. What is a non-destructive editing workflow and why does it matter?\r\n2. What is the difference between Vibrance and Saturation?\r\n3. When should you use a Layer Mask instead of the Eraser tool?', 6, 1, '2026-02-16 06:11:08'),
(7, 1, 'Portfolio & Client Work', '## Building a Design Portfolio\r\n\r\nYour portfolio is your most important marketing tool as a designer. It should showcase your best work, demonstrate range, and tell your design story.\r\n\r\n## What to Include\r\n- 6-12 of your strongest, most recent projects\r\n- A variety of work types (logo, print, digital, branding)\r\n- Case studies showing your process: Brief > Research > Concept > Final\r\n- A clear About page with your skills and background\r\n- Contact information and links to social profiles\r\n\r\n## Portfolio Platforms\r\n- Behance: Industry standard, free, large community\r\n- Dribbble: High-quality showcase, invite-based\r\n- Adobe Portfolio: Included with Creative Cloud\r\n- Personal website: Full control, best for SEO\r\n\r\n## Writing a Case Study\r\n\r\nStructure:\r\n1. The Challenge — What problem were you solving?\r\n2. The Process — Research, sketches, iterations\r\n3. The Solution — Final design with rationale\r\n4. The Result — Outcome, client feedback, metrics\r\n\r\n## Working with Clients\r\n\r\n1. Discovery call — Understand goals, audience, budget, timeline\r\n2. Proposal & contract — Scope of work, payment terms, revision policy\r\n3. Design brief — Written document confirming all requirements\r\n4. Feedback rounds — Typically 2-3 rounds of revisions\r\n5. Final delivery — All agreed file formats, organised and labelled\r\n\r\n## Pricing Your Work\r\n- Research market rates in your region\r\n- Price by project value, not by hour\r\n- Include a kill fee (25-50%) in contracts\r\n- Never start work without a deposit (30-50%)\r\n\r\n## Practical Task\r\n\r\nCreate a Behance project for one of your designs from this course. Write a full case study with the 4-part structure above. Include process images alongside the final design.\r\n\r\n## Self-Check\r\n1. How many projects should a beginner portfolio contain?\r\n2. What is a kill fee and why is it important?\r\n3. What are the 4 parts of a design case study?', 7, 1, '2026-02-16 06:11:08'),
(8, 1, 'Capstone: Brand Identity Project', '## Final Project Brief\r\n\r\nYou will create a complete brand identity for a fictional business of your choice. This project demonstrates everything you have learned in this course.\r\n\r\n## Deliverables\r\n\r\n### 1. Brand Strategy (written, 1 page)\r\n- Business name and tagline\r\n- Target audience profile\r\n- Brand personality (3-5 adjectives)\r\n- Competitor analysis (2-3 competitors)\r\n\r\n### 2. Visual Identity\r\n- Primary logo (full colour, vector)\r\n- Logo variations: reversed, black only, icon only\r\n- Colour palette: 2 primary + 2 secondary colours with HEX, RGB, CMYK codes\r\n- Typography system: heading font + body font with usage examples\r\n\r\n### 3. Brand Applications\r\n- Business card (front and back, print-ready)\r\n- Letterhead (A4)\r\n- Social media profile image and cover photo\r\n- One marketing material: flyer, poster, or brochure\r\n\r\n### 4. Brand Guidelines Document (PDF)\r\n- Logo usage rules: clear space, minimum size, incorrect usage\r\n- Colour specifications: HEX, RGB, CMYK values\r\n- Typography guidelines: font names, sizes, weights, usage\r\n- Tone of voice: 3-5 sentences describing how the brand communicates\r\n\r\n## Evaluation Criteria\r\n- Concept strength and originality (25%)\r\n- Application of design principles (25%)\r\n- Consistency across all brand touchpoints (20%)\r\n- Quality of execution and file preparation (20%)\r\n- Clarity and completeness of brand guidelines (10%)\r\n\r\n## Self-Check\r\n1. Does your logo work in black and white at 20mm wide?\r\n2. Is your colour palette accessible (minimum 4.5:1 contrast ratio)?\r\n3. Would a stranger understand your brand from the guidelines document alone?', 8, 1, '2026-02-16 06:11:08'),
(9, 3, 'Foundations of Web Design', '## What is Web Design?\r\n\r\nWeb design is the process of planning, conceptualising, and arranging content online. It combines visual design, user experience (UX), and technical knowledge to create websites that are both beautiful and functional.\r\n\r\n## Web Design vs Web Development\r\n\r\n**Web Design**: Visual layout, colour, typography, user experience — the look and feel.\r\n**Web Development**: Code that makes the design work — HTML, CSS, JavaScript, PHP.\r\n\r\nA web designer creates the blueprint; a developer builds it.\r\n\r\n## Core Web Design Principles\r\n\r\n1. **Visual Hierarchy**: Guide the user\'s eye to the most important content first\r\n2. **Consistency**: Use the same colours, fonts, and spacing throughout\r\n3. **Simplicity**: Remove everything that doesn\'t serve the user\r\n4. **Accessibility**: Design for all users, including those with disabilities\r\n5. **Mobile-First**: Design for small screens first, then scale up\r\n6. **Performance**: Fast-loading pages improve user experience and SEO\r\n\r\n## Anatomy of a Web Page\r\n\r\n- **Header**: Logo, navigation, call-to-action\r\n- **Hero section**: Main headline, subheading, primary CTA\r\n- **Features/Benefits**: What the product/service offers\r\n- **Social proof**: Testimonials, logos, statistics\r\n- **Footer**: Links, contact info, legal\r\n\r\n## Tools for Web Design\r\n\r\n- **Figma**: Industry standard for UI/UX design (free tier available)\r\n- **Adobe XD**: Adobe\'s UI design tool\r\n- **Sketch**: Mac-only, popular in agencies\r\n- **Webflow**: Visual web design with real HTML/CSS output\r\n\r\n## Practical Task\r\n\r\nSketch a wireframe (on paper or in Figma) for a 5-page website: Home, About, Services, Portfolio, Contact. Define the layout and content for each page. No colours or images yet — just structure.\r\n\r\n## Self-Check\r\n1. What is the difference between web design and web development?\r\n2. Name the 6 core web design principles.\r\n3. What are the main sections of a typical web page?', 1, 1, '2026-02-16 06:11:08'),
(10, 3, 'UI Design Fundamentals', '## What is UI Design?\r\n\r\nUser Interface (UI) design is the process of designing the visual elements that users interact with — buttons, forms, navigation, icons, and layouts.\r\n\r\n## UI Design Components\r\n\r\n### Buttons\r\n- Use clear, action-oriented labels: \'Get Started\', \'Download Now\', \'Learn More\'\r\n- Primary button: Filled, high contrast — for the main action\r\n- Secondary button: Outlined or ghost — for secondary actions\r\n- Disabled state: Reduced opacity, no pointer cursor\r\n- Minimum touch target: 44x44px for mobile\r\n\r\n### Forms\r\n- Label every input field clearly\r\n- Show placeholder text as an example, not a replacement for labels\r\n- Validate inline (show errors as the user types, not only on submit)\r\n- Group related fields together\r\n- Use appropriate input types: email, tel, date, number\r\n\r\n### Navigation\r\n- Keep navigation items to 5-7 maximum\r\n- Highlight the current page/section\r\n- Mobile: Use a hamburger menu or bottom navigation bar\r\n- Breadcrumbs for deep navigation structures\r\n\r\n### Icons\r\n- Use universally understood icons (hamburger menu, search magnifier, cart)\r\n- Always pair icons with text labels for clarity\r\n- Maintain consistent icon style throughout (outline vs filled)\r\n\r\n## Spacing & Layout\r\n\r\nUse a consistent spacing scale (multiples of 4 or 8px):\r\n- 4px: Tight spacing (between icon and label)\r\n- 8px: Small spacing (between form elements)\r\n- 16px: Medium spacing (between sections within a card)\r\n- 24px: Large spacing (between cards)\r\n- 48px: Section spacing\r\n\r\n## Practical Task\r\n\r\nDesign a sign-up form in Figma. Include: name, email, password, confirm password, and a submit button. Apply proper labels, spacing, and validation states (default, focus, error, success).\r\n\r\n## Self-Check\r\n1. What is the minimum touch target size for mobile buttons?\r\n2. Why should placeholder text not replace form labels?\r\n3. What spacing scale is commonly used in UI design?', 2, 1, '2026-02-16 06:11:08'),
(11, 3, 'UX Design & User Research', '## What is UX Design?\r\n\r\nUser Experience (UX) design is the process of creating products that provide meaningful, relevant, and enjoyable experiences to users. It focuses on the entire journey — from first awareness to long-term use.\r\n\r\n## The UX Design Process\r\n\r\n1. **Research** — Understand users, their goals, and pain points\r\n2. **Define** — Synthesise research into clear problem statements\r\n3. **Ideate** — Generate many possible solutions\r\n4. **Prototype** — Build low-fidelity representations of solutions\r\n5. **Test** — Validate with real users and iterate\r\n\r\n## User Research Methods\r\n\r\n**Qualitative** (understanding why):\r\n- User interviews: 1-on-1 conversations about goals and frustrations\r\n- Contextual inquiry: Observe users in their natural environment\r\n- Usability testing: Watch users attempt tasks on your product\r\n\r\n**Quantitative** (understanding how many):\r\n- Surveys: Collect data from many users at once\r\n- Analytics: Track clicks, scroll depth, conversion rates\r\n- A/B testing: Compare two versions to see which performs better\r\n\r\n## User Personas\r\n\r\nA persona is a fictional representation of your target user based on research:\r\n- Name and photo (makes them feel real)\r\n- Demographics: Age, location, occupation\r\n- Goals: What they want to achieve\r\n- Frustrations: What gets in their way\r\n- Behaviours: How they use technology\r\n\r\n## Information Architecture\r\n\r\nIA is the organisation and structure of content:\r\n- **Card sorting**: Users group content into categories they find logical\r\n- **Tree testing**: Test navigation structure without visual design\r\n- **Sitemap**: Visual diagram of all pages and their relationships\r\n\r\n## Practical Task\r\n\r\nConduct 3 user interviews about a website or app you use regularly. Ask about goals, frustrations, and workarounds. Create one user persona based on your findings. Build a sitemap for a 10-page website.\r\n\r\n## Self-Check\r\n1. What are the 5 stages of the UX design process?\r\n2. What is the difference between qualitative and quantitative research?\r\n3. What is a user persona and what does it contain?', 3, 1, '2026-02-16 06:11:08'),
(12, 3, 'Responsive Design & CSS Layouts', '## What is Responsive Design?\r\n\r\nResponsive design means a website adapts its layout and content to fit any screen size — from a 320px mobile phone to a 2560px desktop monitor.\r\n\r\n## Breakpoints\r\n\r\nBreakpoints are the screen widths at which the layout changes:\r\n- Mobile: 0-767px\r\n- Tablet: 768-1023px\r\n- Desktop: 1024-1279px\r\n- Large desktop: 1280px+\r\n\r\n## CSS Flexbox\r\n\r\nFlexbox is a one-dimensional layout system (row or column):\r\n\r\n```css\r\n.container {\r\n  display: flex;\r\n  justify-content: space-between; /* horizontal alignment */\r\n  align-items: center;            /* vertical alignment */\r\n  gap: 16px;\r\n}\r\n```\r\n\r\nKey properties:\r\n- `flex-direction`: row | column\r\n- `justify-content`: flex-start | center | space-between | space-around\r\n- `align-items`: flex-start | center | flex-end | stretch\r\n- `flex-wrap`: wrap | nowrap\r\n- `flex`: shorthand for flex-grow, flex-shrink, flex-basis\r\n\r\n## CSS Grid\r\n\r\nGrid is a two-dimensional layout system (rows AND columns):\r\n\r\n```css\r\n.grid {\r\n  display: grid;\r\n  grid-template-columns: repeat(3, 1fr);\r\n  gap: 24px;\r\n}\r\n```\r\n\r\nKey properties:\r\n- `grid-template-columns`: Define column widths\r\n- `grid-template-rows`: Define row heights\r\n- `grid-column`: Span across columns\r\n- `grid-row`: Span across rows\r\n\r\n## Mobile-First CSS\r\n\r\nWrite styles for mobile first, then add media queries for larger screens:\r\n\r\n```css\r\n/* Mobile (default) */\r\n.card { width: 100%; }\r\n\r\n/* Tablet and up */\r\n@media (min-width: 768px) {\r\n  .card { width: 50%; }\r\n}\r\n\r\n/* Desktop and up */\r\n@media (min-width: 1024px) {\r\n  .card { width: 33.333%; }\r\n}\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a responsive 3-column card grid using CSS Grid. On mobile it should be 1 column, on tablet 2 columns, on desktop 3 columns. Each card should have an image, title, description, and button.\r\n\r\n## Self-Check\r\n1. What are the standard breakpoints for responsive design?\r\n2. What is the difference between Flexbox and CSS Grid?\r\n3. Why do we write mobile-first CSS?', 4, 1, '2026-02-16 06:11:08'),
(13, 3, 'Typography & Colour for the Web', '## Web Typography\r\n\r\nTypography on the web has unique considerations: font loading performance, screen rendering, and responsive sizing.\r\n\r\n## Web-Safe Fonts vs Web Fonts\r\n\r\n**Web-safe fonts**: Pre-installed on most devices (Arial, Georgia, Times New Roman). No loading required.\r\n**Web fonts**: Custom fonts loaded from a server (Google Fonts, Adobe Fonts, self-hosted).\r\n\r\nGoogle Fonts is free and easy to use:\r\n```html\r\n<link href=\"https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap\" rel=\"stylesheet\">\r\n```\r\n\r\n## Fluid Typography\r\n\r\nUse CSS clamp() for font sizes that scale smoothly between breakpoints:\r\n\r\n```css\r\nh1 {\r\n  font-size: clamp(1.75rem, 4vw, 3rem);\r\n}\r\n```\r\n\r\nThis means: minimum 1.75rem, preferred 4vw, maximum 3rem.\r\n\r\n## CSS Custom Properties for Typography\r\n\r\n```css\r\n:root {\r\n  --font-body: \'Inter\', sans-serif;\r\n  --text-xs:   0.75rem;\r\n  --text-sm:   0.875rem;\r\n  --text-base: 1rem;\r\n  --text-lg:   1.125rem;\r\n  --text-xl:   1.25rem;\r\n  --text-2xl:  1.5rem;\r\n  --text-3xl:  1.875rem;\r\n  --text-4xl:  2.25rem;\r\n}\r\n```\r\n\r\n## Colour for the Web\r\n\r\n### CSS Custom Properties for Colour\r\n\r\n```css\r\n:root {\r\n  --color-primary:    #4f46e5;\r\n  --color-secondary:  #06b6d4;\r\n  --color-success:    #10b981;\r\n  --color-danger:     #ef4444;\r\n  --color-text:       #0f172a;\r\n  --color-muted:      #64748b;\r\n  --color-bg:         #f8fafc;\r\n  --color-border:     #e2e8f0;\r\n}\r\n```\r\n\r\n### Colour Accessibility\r\n- Normal text: minimum 4.5:1 contrast ratio (WCAG AA)\r\n- Large text (18px+ or 14px+ bold): minimum 3:1\r\n- UI components and graphics: minimum 3:1\r\n- Test with: WebAIM Contrast Checker, browser DevTools\r\n\r\n## Practical Task\r\n\r\nCreate a CSS design system file (variables.css) for a fictional brand. Define typography scale, colour palette, spacing scale, and border radius values using CSS custom properties. Apply them to a simple landing page.\r\n\r\n## Self-Check\r\n1. What is the difference between web-safe fonts and web fonts?\r\n2. How does CSS clamp() work for fluid typography?\r\n3. What is the WCAG AA contrast ratio requirement for normal text?', 5, 1, '2026-02-16 06:11:08'),
(14, 3, 'Web Design with Figma', '## Why Figma?\r\n\r\nFigma is the industry-standard tool for UI/UX design. It is browser-based, collaborative, and free for individuals. Teams can design, prototype, and hand off to developers — all in one tool.\r\n\r\n## Figma Fundamentals\r\n\r\n### Frames\r\nFrames are the containers for your designs. Create frames at standard device sizes:\r\n- Mobile: 390 x 844px (iPhone 14)\r\n- Tablet: 768 x 1024px (iPad)\r\n- Desktop: 1440 x 900px\r\n\r\n### Components\r\nComponents are reusable design elements. Create a component once, use it everywhere. When you update the master component, all instances update automatically.\r\n\r\nUse components for: buttons, cards, navigation bars, form inputs, icons.\r\n\r\n### Auto Layout\r\nAuto Layout makes frames resize automatically based on their content — like CSS Flexbox.\r\n\r\nProperties:\r\n- Direction: Horizontal or Vertical\r\n- Spacing: Gap between items\r\n- Padding: Space inside the frame\r\n- Resizing: Fixed, Hug contents, Fill container\r\n\r\n### Styles\r\nStyles save reusable values for colours, typography, and effects:\r\n- Colour styles: Brand colours, semantic colours\r\n- Text styles: Heading 1, Heading 2, Body, Caption\r\n- Effect styles: Drop shadows, blurs\r\n\r\n## Prototyping in Figma\r\n\r\nConnect frames with interactions to create clickable prototypes:\r\n1. Select an element (button, link)\r\n2. In the Prototype panel, drag the connection to the destination frame\r\n3. Set the trigger (On Click) and animation (Smart Animate)\r\n4. Press Play to preview\r\n\r\n## Developer Handoff\r\n\r\nFigma\'s Inspect panel shows developers the exact CSS values for any element: font size, colour, spacing, border radius. Export assets directly from Figma as PNG, SVG, or PDF.\r\n\r\n## Practical Task\r\n\r\nDesign a complete mobile app screen set in Figma (5 screens): Splash, Onboarding, Home, Profile, Settings. Use components for the navigation bar and buttons. Create a clickable prototype connecting all screens.\r\n\r\n## Self-Check\r\n1. What is a Figma component and why is it useful?\r\n2. How does Auto Layout relate to CSS Flexbox?\r\n3. What does the Inspect panel provide for developers?', 6, 1, '2026-02-16 06:11:08'),
(15, 3, 'Website Performance & SEO Basics', '## Why Performance Matters\r\n\r\nA 1-second delay in page load time can reduce conversions by 7%. Google uses page speed as a ranking factor. Users abandon pages that take more than 3 seconds to load.\r\n\r\n## Core Web Vitals\r\n\r\nGoogle\'s key performance metrics:\r\n- **LCP (Largest Contentful Paint)**: Time for the main content to load. Target: under 2.5 seconds.\r\n- **FID (First Input Delay)**: Time from first interaction to browser response. Target: under 100ms.\r\n- **CLS (Cumulative Layout Shift)**: Visual stability — how much the page shifts during loading. Target: under 0.1.\r\n\r\n## Image Optimisation\r\n\r\nImages are typically the largest files on a web page:\r\n- Use **WebP** format (30-50% smaller than JPG/PNG with same quality)\r\n- Compress images with TinyPNG, Squoosh, or ImageOptim\r\n- Use `width` and `height` attributes to prevent layout shift\r\n- Use `loading=\"lazy\"` for images below the fold\r\n- Use responsive images with `srcset` for different screen sizes\r\n\r\n## Performance Best Practices\r\n\r\n1. Minify CSS, JavaScript, and HTML\r\n2. Enable GZIP or Brotli compression on the server\r\n3. Use a Content Delivery Network (CDN) for static assets\r\n4. Reduce HTTP requests (combine files, use CSS sprites)\r\n5. Defer non-critical JavaScript\r\n6. Use browser caching with appropriate cache headers\r\n\r\n## SEO Fundamentals for Web Designers\r\n\r\n**On-page SEO elements designers control:**\r\n- `<title>` tag: 50-60 characters, include primary keyword\r\n- Meta description: 150-160 characters, compelling summary\r\n- Heading hierarchy: One H1 per page, logical H2/H3 structure\r\n- Alt text on images: Descriptive, keyword-relevant\r\n- URL structure: Short, descriptive, hyphen-separated\r\n- Internal linking: Connect related pages\r\n\r\n## Practical Task\r\n\r\nAudit a website using Google PageSpeed Insights (pagespeed.web.dev). Identify the top 3 performance issues. Write a brief report with specific recommendations to fix each issue.\r\n\r\n## Self-Check\r\n1. What are the three Core Web Vitals and their targets?\r\n2. Why should you use WebP format for images?\r\n3. What SEO elements does a web designer directly control?', 7, 1, '2026-02-16 06:11:08'),
(16, 3, 'Capstone: Full Website Design', '## Final Project Brief\r\n\r\nYou will design a complete, responsive website for a fictional business. This project demonstrates your web design, UX, and visual design skills.\r\n\r\n## Scenario\r\n\r\nDesign a website for \'Luminary Studio\' — a fictional creative agency based in Lagos, Nigeria. They offer branding, web design, and digital marketing services to SMEs across Africa.\r\n\r\n## Deliverables\r\n\r\n### 1. UX Research & Planning\r\n- User persona (1 persona for the target client)\r\n- Sitemap (all pages and their relationships)\r\n- Wireframes for all pages (low-fidelity, in Figma or on paper)\r\n\r\n### 2. Visual Design (in Figma)\r\nDesign all pages at desktop (1440px) and mobile (390px):\r\n- Home page: Hero, services overview, portfolio preview, testimonials, CTA\r\n- About page: Team, story, values\r\n- Services page: Detailed service descriptions with pricing\r\n- Portfolio page: Project grid with filter by category\r\n- Contact page: Contact form, map, social links\r\n\r\n### 3. Design System\r\n- Colour palette (CSS custom properties)\r\n- Typography scale\r\n- Component library: buttons, cards, form inputs, navigation\r\n\r\n### 4. Prototype\r\n- Clickable Figma prototype connecting all pages\r\n- Mobile and desktop versions\r\n\r\n## Evaluation Criteria\r\n- UX thinking and user-centred approach (20%)\r\n- Visual design quality and consistency (30%)\r\n- Responsive design (mobile + desktop) (20%)\r\n- Component system completeness (15%)\r\n- Prototype functionality (15%)\r\n\r\n## Self-Check\r\n1. Does every page have a clear primary call-to-action?\r\n2. Is the design consistent across all pages and both breakpoints?\r\n3. Would a developer be able to build this from your Figma file?', 8, 1, '2026-02-16 06:11:08'),
(17, 4, 'HTML5 Fundamentals', '## What is HTML?\r\n\r\nHTML (HyperText Markup Language) is the standard language for creating web pages. It defines the structure and content of a page using elements represented by tags.\r\n\r\n## Document Structure\r\n\r\n```html\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Page Title</title>\r\n</head>\r\n<body>\r\n  <!-- Content goes here -->\r\n</body>\r\n</html>\r\n```\r\n\r\n## Semantic HTML5 Elements\r\n\r\nSemantic elements describe their meaning to both the browser and the developer:\r\n\r\n```html\r\n<header>   <!-- Site header, logo, navigation -->\r\n<nav>      <!-- Navigation links -->\r\n<main>     <!-- Main content of the page -->\r\n<article>  <!-- Self-contained content (blog post, news article) -->\r\n<section>  <!-- Thematic grouping of content -->\r\n<aside>    <!-- Sidebar, related content -->\r\n<footer>   <!-- Site footer -->\r\n<figure>   <!-- Image with caption -->\r\n<figcaption> <!-- Caption for a figure -->\r\n```\r\n\r\n## Common HTML Elements\r\n\r\n```html\r\n<!-- Headings -->\r\n<h1>Main Heading</h1>\r\n<h2>Subheading</h2>\r\n\r\n<!-- Text -->\r\n<p>Paragraph text</p>\r\n<strong>Bold/important</strong>\r\n<em>Italic/emphasis</em>\r\n\r\n<!-- Links -->\r\n<a href=\"https://example.com\" target=\"_blank\">Link text</a>\r\n\r\n<!-- Images -->\r\n<img src=\"photo.jpg\" alt=\"Description of image\" width=\"800\" height=\"600\">\r\n\r\n<!-- Lists -->\r\n<ul><li>Unordered item</li></ul>\r\n<ol><li>Ordered item</li></ol>\r\n\r\n<!-- Tables -->\r\n<table>\r\n  <thead><tr><th>Name</th><th>Age</th></tr></thead>\r\n  <tbody><tr><td>John</td><td>25</td></tr></tbody>\r\n</table>\r\n\r\n<!-- Forms -->\r\n<form action=\"/submit\" method=\"POST\">\r\n  <input type=\"text\" name=\"username\" placeholder=\"Enter username\" required>\r\n  <input type=\"email\" name=\"email\" required>\r\n  <button type=\"submit\">Submit</button>\r\n</form>\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a personal profile page using only HTML (no CSS yet). Include: a header with your name, a navigation bar, an about section, a skills list, a projects table, and a contact form. Use semantic elements throughout.\r\n\r\n## Self-Check\r\n1. What is the difference between semantic and non-semantic HTML elements?\r\n2. What does the `alt` attribute on an image do?\r\n3. What is the difference between `<strong>` and `<b>`?', 1, 1, '2026-02-16 06:11:08'),
(18, 4, 'CSS3 & Modern Styling', '## CSS Fundamentals\r\n\r\nCSS (Cascading Style Sheets) controls the visual presentation of HTML elements.\r\n\r\n## The Box Model\r\n\r\nEvery HTML element is a rectangular box:\r\n- **Content**: The actual text or image\r\n- **Padding**: Space between content and border\r\n- **Border**: The border around the padding\r\n- **Margin**: Space outside the border\r\n\r\n```css\r\n.box {\r\n  width: 300px;\r\n  padding: 20px;\r\n  border: 2px solid #333;\r\n  margin: 16px;\r\n  box-sizing: border-box; /* padding included in width */\r\n}\r\n```\r\n\r\n## CSS Selectors\r\n\r\n```css\r\n/* Element selector */\r\np { color: #333; }\r\n\r\n/* Class selector */\r\n.card { background: white; }\r\n\r\n/* ID selector */\r\n#header { position: sticky; }\r\n\r\n/* Descendant selector */\r\n.nav a { color: white; }\r\n\r\n/* Pseudo-class */\r\na:hover { color: blue; }\r\nbutton:focus { outline: 2px solid blue; }\r\n\r\n/* Pseudo-element */\r\np::first-line { font-weight: bold; }\r\n```\r\n\r\n## CSS Variables (Custom Properties)\r\n\r\n```css\r\n:root {\r\n  --color-primary: #4f46e5;\r\n  --spacing-md: 16px;\r\n  --radius: 8px;\r\n}\r\n\r\n.button {\r\n  background: var(--color-primary);\r\n  padding: var(--spacing-md);\r\n  border-radius: var(--radius);\r\n}\r\n```\r\n\r\n## Flexbox Layout\r\n\r\n```css\r\n.container {\r\n  display: flex;\r\n  justify-content: space-between;\r\n  align-items: center;\r\n  gap: 16px;\r\n  flex-wrap: wrap;\r\n}\r\n```\r\n\r\n## CSS Grid Layout\r\n\r\n```css\r\n.grid {\r\n  display: grid;\r\n  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));\r\n  gap: 24px;\r\n}\r\n```\r\n\r\n## Transitions & Animations\r\n\r\n```css\r\n.button {\r\n  transition: background 0.2s ease, transform 0.1s ease;\r\n}\r\n.button:hover {\r\n  background: #3730a3;\r\n  transform: translateY(-2px);\r\n}\r\n```\r\n\r\n## Practical Task\r\n\r\nStyle the HTML profile page from Lesson 1. Create a responsive layout using Flexbox and Grid. Add a colour scheme using CSS variables. Include hover effects on links and buttons. Make it fully responsive for mobile.\r\n\r\n## Self-Check\r\n1. Explain the CSS box model and its four components.\r\n2. What is the difference between `margin` and `padding`?\r\n3. When would you use Flexbox vs CSS Grid?', 2, 1, '2026-02-16 06:11:08'),
(19, 4, 'JavaScript Essentials', '## What is JavaScript?\r\n\r\nJavaScript is the programming language of the web. It makes web pages interactive — responding to user actions, updating content dynamically, and communicating with servers.\r\n\r\n## Variables & Data Types\r\n\r\n```javascript\r\n// Variables\r\nlet name = \'John\';        // Can be reassigned\r\nconst age = 25;           // Cannot be reassigned\r\nvar old = \'avoid this\';   // Old way, avoid\r\n\r\n// Data types\r\nlet str = \'Hello\';        // String\r\nlet num = 42;             // Number\r\nlet bool = true;          // Boolean\r\nlet arr = [1, 2, 3];      // Array\r\nlet obj = { key: \'val\' }; // Object\r\nlet nothing = null;       // Null\r\nlet undef;                // Undefined\r\n```\r\n\r\n## Functions\r\n\r\n```javascript\r\n// Function declaration\r\nfunction greet(name) {\r\n  return `Hello, ${name}!`;\r\n}\r\n\r\n// Arrow function\r\nconst greet = (name) => `Hello, ${name}!`;\r\n\r\n// Default parameters\r\nfunction add(a, b = 0) {\r\n  return a + b;\r\n}\r\n```\r\n\r\n## DOM Manipulation\r\n\r\n```javascript\r\n// Select elements\r\nconst btn = document.getElementById(\'myBtn\');\r\nconst cards = document.querySelectorAll(\'.card\');\r\n\r\n// Change content\r\nbtn.textContent = \'Click me\';\r\nbtn.innerHTML = \'<strong>Click me</strong>\';\r\n\r\n// Change styles\r\nbtn.style.backgroundColor = \'#4f46e5\';\r\nbtn.classList.add(\'active\');\r\nbtn.classList.toggle(\'hidden\');\r\n\r\n// Event listeners\r\nbtn.addEventListener(\'click\', function() {\r\n  alert(\'Button clicked!\');\r\n});\r\n\r\n// Create and append elements\r\nconst div = document.createElement(\'div\');\r\ndiv.className = \'card\';\r\ndiv.textContent = \'New card\';\r\ndocument.body.appendChild(div);\r\n```\r\n\r\n## Fetch API (AJAX)\r\n\r\n```javascript\r\n// GET request\r\nfetch(\'/api/users\')\r\n  .then(response => response.json())\r\n  .then(data => console.log(data))\r\n  .catch(error => console.error(error));\r\n\r\n// POST request\r\nfetch(\'/api/users\', {\r\n  method: \'POST\',\r\n  headers: { \'Content-Type\': \'application/json\' },\r\n  body: JSON.stringify({ name: \'John\', email: \'john@example.com\' })\r\n})\r\n  .then(res => res.json())\r\n  .then(data => console.log(data));\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild an interactive to-do list using HTML, CSS, and JavaScript. Features: add tasks, mark as complete (toggle class), delete tasks, show task count. Store tasks in localStorage so they persist on page refresh.\r\n\r\n## Self-Check\r\n1. What is the difference between `let`, `const`, and `var`?\r\n2. How do you select an element by class name in JavaScript?\r\n3. What does the Fetch API do?', 3, 1, '2026-02-16 06:11:08'),
(20, 4, 'PHP Backend Development', '## What is PHP?\r\n\r\nPHP (Hypertext Preprocessor) is a server-side scripting language designed for web development. It runs on the server and generates HTML that is sent to the browser.\r\n\r\n## PHP Basics\r\n\r\n```php\r\n<?php\r\n// Variables\r\n$name = \'John\';\r\n$age = 25;\r\n$price = 99.99;\r\n$active = true;\r\n\r\n// String interpolation\r\necho \"Hello, $name! You are $age years old.\";\r\n\r\n// Arrays\r\n$fruits = [\'apple\', \'banana\', \'mango\'];\r\necho $fruits[0]; // apple\r\n\r\n// Associative arrays\r\n$user = [\r\n    \'name\' => \'John\',\r\n    \'email\' => \'john@example.com\',\r\n    \'age\' => 25\r\n];\r\necho $user[\'name\']; // John\r\n```\r\n\r\n## Control Structures\r\n\r\n```php\r\n// If/else\r\nif ($age >= 18) {\r\n    echo \'Adult\';\r\n} elseif ($age >= 13) {\r\n    echo \'Teenager\';\r\n} else {\r\n    echo \'Child\';\r\n}\r\n\r\n// Loops\r\nforeach ($fruits as $fruit) {\r\n    echo $fruit . \'<br>\';\r\n}\r\n\r\nfor ($i = 0; $i < 10; $i++) {\r\n    echo $i;\r\n}\r\n```\r\n\r\n## Functions\r\n\r\n```php\r\nfunction formatMoney(float $amount): string {\r\n    return \'₦\' . number_format($amount, 2);\r\n}\r\n\r\necho formatMoney(15000); // ₦15,000.00\r\n```\r\n\r\n## Handling Forms\r\n\r\n```php\r\n// form.html\r\n// <form method=\"POST\" action=\"process.php\">\r\n//   <input type=\"text\" name=\"username\">\r\n//   <button type=\"submit\">Submit</button>\r\n// </form>\r\n\r\n// process.php\r\nif ($_SERVER[\'REQUEST_METHOD\'] === \'POST\') {\r\n    $username = htmlspecialchars(trim($_POST[\'username\'] ?? \'\'));\r\n    if ($username === \'\') {\r\n        echo \'Username is required\';\r\n    } else {\r\n        echo \"Welcome, $username!\";\r\n    }\r\n}\r\n```\r\n\r\n## Sessions\r\n\r\n```php\r\nsession_start();\r\n\r\n// Store data\r\n$_SESSION[\'user_id\'] = 42;\r\n$_SESSION[\'username\'] = \'john\';\r\n\r\n// Read data\r\n$userId = $_SESSION[\'user_id\'] ?? null;\r\n\r\n// Destroy session (logout)\r\nsession_destroy();\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a simple contact form with PHP validation. Fields: name, email, message. Validate all fields server-side. Show success message on valid submission. Show specific error messages for each invalid field. Use sessions to persist form data on error.\r\n\r\n## Self-Check\r\n1. What is the difference between `$_GET` and `$_POST`?\r\n2. Why should you always sanitise user input?\r\n3. What is a PHP session and when would you use it?', 4, 1, '2026-02-16 06:11:08'),
(21, 4, 'MySQL & Database Design', '## What is a Database?\r\n\r\nA database is an organised collection of structured data. MySQL is a relational database management system (RDBMS) — data is stored in tables with rows and columns, and tables can be related to each other.\r\n\r\n## SQL Fundamentals\r\n\r\n```sql\r\n-- Create a table\r\nCREATE TABLE users (\r\n    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n    name VARCHAR(100) NOT NULL,\r\n    email VARCHAR(190) UNIQUE NOT NULL,\r\n    password VARCHAR(255) NOT NULL,\r\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\r\n);\r\n\r\n-- Insert data\r\nINSERT INTO users (name, email, password)\r\nVALUES (\'John Doe\', \'john@example.com\', \'hashed_password\');\r\n\r\n-- Select data\r\nSELECT id, name, email FROM users WHERE id = 1;\r\nSELECT * FROM users ORDER BY created_at DESC LIMIT 10;\r\n\r\n-- Update data\r\nUPDATE users SET name = \'Jane Doe\' WHERE id = 1;\r\n\r\n-- Delete data\r\nDELETE FROM users WHERE id = 1;\r\n```\r\n\r\n## PHP PDO (Database Connection)\r\n\r\n```php\r\n$pdo = new PDO(\r\n    \'mysql:host=localhost;dbname=mydb;charset=utf8mb4\',\r\n    \'root\',\r\n    \'password\',\r\n    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]\r\n);\r\n\r\n// Prepared statement (prevents SQL injection)\r\n$stmt = $pdo->prepare(\'SELECT * FROM users WHERE email = ?\');\r\n$stmt->execute([$email]);\r\n$user = $stmt->fetch(PDO::FETCH_ASSOC);\r\n```\r\n\r\n## Database Relationships\r\n\r\n**One-to-Many**: One user has many posts\r\n```sql\r\nCREATE TABLE posts (\r\n    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n    user_id INT UNSIGNED NOT NULL,\r\n    title VARCHAR(200) NOT NULL,\r\n    FOREIGN KEY (user_id) REFERENCES users(id)\r\n);\r\n```\r\n\r\n**Many-to-Many**: Students enrol in many courses; courses have many students\r\n```sql\r\nCREATE TABLE enrollments (\r\n    student_id INT UNSIGNED,\r\n    course_id INT UNSIGNED,\r\n    PRIMARY KEY (student_id, course_id)\r\n);\r\n```\r\n\r\n## Database Design Principles\r\n\r\n1. **Normalisation**: Eliminate data redundancy (1NF, 2NF, 3NF)\r\n2. **Primary keys**: Every table needs a unique identifier\r\n3. **Foreign keys**: Enforce referential integrity between tables\r\n4. **Indexes**: Speed up queries on frequently searched columns\r\n5. **Prepared statements**: Always use them to prevent SQL injection\r\n\r\n## Practical Task\r\n\r\nDesign a database for a simple blog. Tables: users, posts, categories, comments, post_categories. Write the CREATE TABLE statements with proper data types, constraints, and foreign keys. Insert sample data and write 5 SELECT queries.\r\n\r\n## Self-Check\r\n1. What is the difference between a primary key and a foreign key?\r\n2. Why should you always use prepared statements?\r\n3. What is database normalisation?', 5, 1, '2026-02-16 06:11:08'),
(22, 4, 'Full Stack Project: Blog Application', '## Project Overview\r\n\r\nYou will build a complete blog application with user authentication, CRUD operations, and a clean UI. This integrates everything from HTML, CSS, JavaScript, PHP, and MySQL.\r\n\r\n## Features to Build\r\n\r\n### Authentication\r\n- User registration with email and password\r\n- Login with session management\r\n- Logout\r\n- Password hashing with password_hash()\r\n\r\n### Blog Posts (CRUD)\r\n- Create: Form to write and publish posts\r\n- Read: List all posts, view single post\r\n- Update: Edit existing posts (author only)\r\n- Delete: Remove posts (author only)\r\n\r\n### Additional Features\r\n- Categories for posts\r\n- Comment system\r\n- Search functionality\r\n- Pagination (10 posts per page)\r\n\r\n## Project Structure\r\n\r\n```\r\nblog/\r\n├── config/\r\n│   └── db.php          # Database connection\r\n├── includes/\r\n│   ├── header.php      # Shared header HTML\r\n│   ├── footer.php      # Shared footer HTML\r\n│   └── helpers.php     # Utility functions\r\n├── uploads/            # User-uploaded images\r\n├── index.php           # Home page (list posts)\r\n├── post.php            # Single post view\r\n├── create.php          # Create new post\r\n├── edit.php            # Edit post\r\n├── delete.php          # Delete post handler\r\n├── login.php           # Login form\r\n├── register.php        # Registration form\r\n├── logout.php          # Logout handler\r\n└── search.php          # Search results\r\n```\r\n\r\n## Security Checklist\r\n\r\n- [ ] All user input sanitised with htmlspecialchars()\r\n- [ ] All database queries use prepared statements\r\n- [ ] Passwords hashed with password_hash(PASSWORD_DEFAULT)\r\n- [ ] CSRF tokens on all forms\r\n- [ ] File uploads validated (type, size, extension)\r\n- [ ] Session regenerated after login\r\n- [ ] Error messages don\'t reveal system details\r\n\r\n## Practical Task\r\n\r\nBuild the complete blog application following the structure above. Implement all CRUD operations, authentication, and at least one additional feature (categories, comments, or search). Deploy to a local XAMPP server.\r\n\r\n## Self-Check\r\n1. What is CSRF and how do you prevent it?\r\n2. Why should you never store plain-text passwords?\r\n3. What is the difference between authentication and authorisation?', 6, 1, '2026-02-16 06:11:08'),
(23, 4, 'APIs & JavaScript Frameworks', '## What is an API?\r\n\r\nAn API (Application Programming Interface) is a set of rules that allows different software applications to communicate with each other. A REST API uses HTTP methods to perform operations on resources.\r\n\r\n## REST API Concepts\r\n\r\nHTTP Methods:\r\n- **GET**: Retrieve data\r\n- **POST**: Create new data\r\n- **PUT/PATCH**: Update existing data\r\n- **DELETE**: Remove data\r\n\r\nHTTP Status Codes:\r\n- 200 OK: Success\r\n- 201 Created: Resource created\r\n- 400 Bad Request: Invalid input\r\n- 401 Unauthorized: Not authenticated\r\n- 403 Forbidden: Not authorised\r\n- 404 Not Found: Resource doesn\'t exist\r\n- 500 Internal Server Error: Server-side error\r\n\r\n## Building a REST API in PHP\r\n\r\n```php\r\nheader(\'Content-Type: application/json\');\r\n\r\n$method = $_SERVER[\'REQUEST_METHOD\'];\r\n$path = parse_url($_SERVER[\'REQUEST_URI\'], PHP_URL_PATH);\r\n\r\nif ($method === \'GET\' && $path === \'/api/users\') {\r\n    $users = $pdo->query(\'SELECT id, name, email FROM users\')->fetchAll();\r\n    echo json_encode([\'ok\' => true, \'data\' => $users]);\r\n    exit;\r\n}\r\n\r\nif ($method === \'POST\' && $path === \'/api/users\') {\r\n    $body = json_decode(file_get_contents(\'php://input\'), true);\r\n    // validate and insert...\r\n    echo json_encode([\'ok\' => true, \'id\' => $pdo->lastInsertId()]);\r\n    exit;\r\n}\r\n```\r\n\r\n## Introduction to Vue.js\r\n\r\nVue.js is a progressive JavaScript framework for building user interfaces:\r\n\r\n```html\r\n<div id=\"app\">\r\n  <input v-model=\"message\" placeholder=\"Type something\">\r\n  <p>You typed: {{ message }}</p>\r\n  <button @click=\"clearMessage\">Clear</button>\r\n</div>\r\n\r\n<script src=\"https://unpkg.com/vue@3/dist/vue.global.js\"></script>\r\n<script>\r\nVue.createApp({\r\n  data() {\r\n    return { message: \'\' }\r\n  },\r\n  methods: {\r\n    clearMessage() { this.message = \'\' }\r\n  }\r\n}).mount(\'#app\')\r\n</script>\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a simple task manager SPA (Single Page Application) using Vue.js that connects to a PHP REST API backend. Features: list tasks (GET), add task (POST), mark complete (PATCH), delete task (DELETE). Store data in MySQL.\r\n\r\n## Self-Check\r\n1. What are the 4 main HTTP methods and what does each do?\r\n2. What does HTTP status code 401 mean?\r\n3. What is the difference between a REST API and a traditional web page?', 7, 1, '2026-02-16 06:11:08');
INSERT INTO `lms_lessons` (`id`, `course_id`, `title`, `content`, `sort_order`, `is_published`, `created_at`) VALUES
(24, 4, 'Deployment & DevOps Basics', '## Taking Your Website Live\r\n\r\nDeployment is the process of making your web application available on the internet. Understanding the basics of servers, hosting, and deployment is essential for every web developer.\r\n\r\n## Hosting Options\r\n\r\n| Type | Best For | Examples |\r\n|---|---|---|\r\n| Shared hosting | Small sites, beginners | Namecheap, Bluehost, cPanel hosts |\r\n| VPS (Virtual Private Server) | Growing sites, more control | DigitalOcean, Linode, Vultr |\r\n| Cloud hosting | Scalable, enterprise | AWS, Google Cloud, Azure |\r\n| Platform as a Service | Easy deployment | Heroku, Railway, Render |\r\n| Static hosting | HTML/CSS/JS only | Netlify, Vercel, GitHub Pages |\r\n\r\n## Domain & DNS\r\n\r\n1. Register a domain (Namecheap, GoDaddy, Google Domains)\r\n2. Point DNS A record to your server IP address\r\n3. Wait for DNS propagation (up to 48 hours)\r\n4. Set up SSL certificate (HTTPS) — free with Let\'s Encrypt\r\n\r\n## Deploying a PHP Application\r\n\r\n### Via cPanel (Shared Hosting)\r\n1. Upload files via File Manager or FTP (FileZilla)\r\n2. Create MySQL database in cPanel\r\n3. Import your SQL schema\r\n4. Update config/db.php with production credentials\r\n5. Set file permissions (755 for directories, 644 for files)\r\n\r\n### Via SSH (VPS)\r\n```bash\r\n# Connect to server\r\nssh user@your-server-ip\r\n\r\n# Install LAMP stack\r\nsudo apt update\r\nsudo apt install apache2 mysql-server php php-mysql\r\n\r\n# Clone your project\r\ngit clone https://github.com/yourname/project.git /var/www/html/project\r\n\r\n# Set permissions\r\nsudo chown -R www-data:www-data /var/www/html/project\r\n```\r\n\r\n## Environment Variables\r\n\r\nNever hardcode sensitive data (passwords, API keys) in your code:\r\n\r\n```php\r\n// .env file (never commit to git)\r\nDB_HOST=localhost\r\nDB_NAME=mydb\r\nDB_USER=root\r\nDB_PASS=secret\r\n\r\n// config/db.php\r\n$host = $_ENV[\'DB_HOST\'] ?? \'localhost\';\r\n```\r\n\r\nAdd `.env` to your `.gitignore` file.\r\n\r\n## Git & Version Control\r\n\r\n```bash\r\ngit init                    # Initialise repository\r\ngit add .                   # Stage all changes\r\ngit commit -m \"Add login\"   # Commit with message\r\ngit push origin main        # Push to GitHub\r\ngit pull origin main        # Pull latest changes\r\n```\r\n\r\n## Practical Task\r\n\r\nDeploy your blog application from Lesson 6 to a live server. Set up a domain, configure HTTPS with Let\'s Encrypt, and use environment variables for database credentials. Test all features on the live server.\r\n\r\n## Self-Check\r\n1. What is the difference between shared hosting and a VPS?\r\n2. Why should you never commit your .env file to Git?\r\n3. What is SSL/HTTPS and why is it required?', 8, 1, '2026-02-16 06:11:08'),
(25, 5, 'PHP Environment Setup & Syntax', '## Setting Up Your PHP Development Environment\r\n\r\nBefore writing PHP, you need a local server environment.\r\n\r\n## XAMPP Installation (Windows/Mac/Linux)\r\n\r\nXAMPP bundles Apache, MySQL, and PHP in one installer:\r\n1. Download from apachefriends.org\r\n2. Install and launch XAMPP Control Panel\r\n3. Start Apache and MySQL services\r\n4. Place your PHP files in C:/xampp/htdocs/\r\n5. Access via http://localhost/yourfolder/\r\n\r\n## PHP Syntax Fundamentals\r\n\r\n```php\r\n<?php\r\n// This is a comment\r\necho \"Hello, World!\";  // Output text\r\n\r\n// Variables (always start with $)\r\n$name = \"Amara\";\r\n$age = 22;\r\n$price = 4500.50;\r\n$isActive = true;\r\n\r\n// String operations\r\n$fullName = \"Amara\" . \" \" . \"Okafor\";  // Concatenation\r\n$upper = strtoupper($name);             // AMARA\r\n$length = strlen($name);                // 5\r\n$trimmed = trim(\"  hello  \");           // \"hello\"\r\n\r\n// Number operations\r\n$sum = 10 + 5;\r\n$product = 10 * 5;\r\n$remainder = 10 % 3;  // 1\r\n$power = 2 ** 8;      // 256\r\n\r\n// Type juggling\r\n$num = \"5\" + 3;  // 8 (PHP converts string to number)\r\n```\r\n\r\n## Arrays\r\n\r\n```php\r\n// Indexed array\r\n$colours = [\"red\", \"green\", \"blue\"];\r\necho $colours[0];  // red\r\n$colours[] = \"yellow\";  // Append\r\n\r\n// Associative array\r\n$student = [\r\n    \"name\" => \"Amara\",\r\n    \"age\"  => 22,\r\n    \"gpa\"  => 3.8\r\n];\r\necho $student[\"name\"];  // Amara\r\n\r\n// Multidimensional array\r\n$students = [\r\n    [\"name\" => \"Amara\", \"score\" => 85],\r\n    [\"name\" => \"Chidi\", \"score\" => 92],\r\n];\r\n\r\n// Array functions\r\n$numbers = [3, 1, 4, 1, 5, 9, 2, 6];\r\nsort($numbers);           // Sort ascending\r\n$count = count($numbers); // 8\r\n$sum = array_sum($numbers); // 31\r\n$unique = array_unique($numbers); // Remove duplicates\r\n```\r\n\r\n## Control Flow\r\n\r\n```php\r\n// Match expression (PHP 8+)\r\n$status = \"active\";\r\n$label = match($status) {\r\n    \"active\"    => \"Active User\",\r\n    \"suspended\" => \"Suspended\",\r\n    \"inactive\"  => \"Inactive\",\r\n    default     => \"Unknown\"\r\n};\r\n\r\n// Null coalescing\r\n$username = $_GET[\"user\"] ?? \"Guest\";\r\n\r\n// Ternary\r\n$greeting = $age >= 18 ? \"Adult\" : \"Minor\";\r\n```\r\n\r\n## Practical Task\r\n\r\nWrite a PHP script that: accepts a student name and 5 test scores via a form, calculates the average, assigns a grade (A=90+, B=80+, C=70+, D=60+, F=below 60), and displays a formatted result card.\r\n\r\n## Self-Check\r\n1. What is the difference between `echo` and `print` in PHP?\r\n2. How do you append an item to a PHP array?\r\n3. What does the null coalescing operator (??) do?', 1, 1, '2026-02-16 06:11:08'),
(26, 5, 'Object-Oriented PHP', '## Why Object-Oriented Programming?\r\n\r\nOOP organises code into objects — self-contained units that combine data (properties) and behaviour (methods). It makes code more reusable, maintainable, and scalable.\r\n\r\n## Classes & Objects\r\n\r\n```php\r\nclass Student {\r\n    // Properties\r\n    public string $name;\r\n    public string $email;\r\n    private float $gpa;\r\n\r\n    // Constructor\r\n    public function __construct(string $name, string $email, float $gpa) {\r\n        $this->name  = $name;\r\n        $this->email = $email;\r\n        $this->gpa   = $gpa;\r\n    }\r\n\r\n    // Methods\r\n    public function getGrade(): string {\r\n        return match(true) {\r\n            $this->gpa >= 3.7 => \'First Class\',\r\n            $this->gpa >= 3.3 => \'Second Class Upper\',\r\n            $this->gpa >= 2.7 => \'Second Class Lower\',\r\n            default           => \'Pass\',\r\n        };\r\n    }\r\n\r\n    public function getGpa(): float {\r\n        return $this->gpa;\r\n    }\r\n}\r\n\r\n// Create an object\r\n$student = new Student(\'Amara\', \'amara@example.com\', 3.8);\r\necho $student->name;         // Amara\r\necho $student->getGrade();   // First Class\r\n```\r\n\r\n## Inheritance\r\n\r\n```php\r\nclass Person {\r\n    public function __construct(\r\n        public string $name,\r\n        public string $email\r\n    ) {}\r\n\r\n    public function greet(): string {\r\n        return \"Hello, I am {$this->name}\";\r\n    }\r\n}\r\n\r\nclass Instructor extends Person {\r\n    public function __construct(\r\n        string $name,\r\n        string $email,\r\n        public string $subject\r\n    ) {\r\n        parent::__construct($name, $email);\r\n    }\r\n\r\n    public function introduce(): string {\r\n        return \"{$this->greet()} and I teach {$this->subject}\";\r\n    }\r\n}\r\n```\r\n\r\n## Interfaces & Abstract Classes\r\n\r\n```php\r\ninterface Payable {\r\n    public function calculateFee(): float;\r\n    public function processPayment(float $amount): bool;\r\n}\r\n\r\nclass CourseEnrollment implements Payable {\r\n    public function calculateFee(): float {\r\n        return 150000.00;\r\n    }\r\n    public function processPayment(float $amount): bool {\r\n        // Payment logic here\r\n        return $amount >= $this->calculateFee();\r\n    }\r\n}\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a simple library management system using OOP. Classes: Book (title, author, isbn, available), Member (name, email, borrowedBooks), Library (books array, members array). Methods: addBook(), registerMember(), borrowBook(), returnBook(), listAvailableBooks().\r\n\r\n## Self-Check\r\n1. What is the difference between `public`, `protected`, and `private` visibility?\r\n2. What is the difference between an interface and an abstract class?\r\n3. What does `$this` refer to inside a class method?', 2, 1, '2026-02-16 06:11:08'),
(27, 5, 'Advanced MySQL & Query Optimisation', '## Advanced SQL Queries\r\n\r\n### JOINs\r\n\r\n```sql\r\n-- INNER JOIN: Only matching rows from both tables\r\nSELECT s.name, c.title, e.paid_amount\r\nFROM lms_students s\r\nINNER JOIN lms_enrollments e ON e.student_id = s.id\r\nINNER JOIN lms_courses c ON c.id = e.course_id;\r\n\r\n-- LEFT JOIN: All rows from left table, matching from right\r\nSELECT s.name, COUNT(e.id) AS course_count\r\nFROM lms_students s\r\nLEFT JOIN lms_enrollments e ON e.student_id = s.id\r\nGROUP BY s.id, s.name;\r\n\r\n-- Subquery\r\nSELECT * FROM lms_students\r\nWHERE id IN (\r\n    SELECT student_id FROM lms_enrollments\r\n    WHERE status = \'paid\'\r\n);\r\n```\r\n\r\n### Aggregate Functions\r\n\r\n```sql\r\nSELECT\r\n    course_id,\r\n    COUNT(*) AS total_students,\r\n    SUM(paid_amount) AS total_revenue,\r\n    AVG(paid_amount) AS avg_payment,\r\n    MAX(paid_amount) AS highest_payment\r\nFROM lms_enrollments\r\nGROUP BY course_id\r\nHAVING COUNT(*) > 5\r\nORDER BY total_revenue DESC;\r\n```\r\n\r\n### Window Functions (MySQL 8+)\r\n\r\n```sql\r\nSELECT\r\n    name,\r\n    paid_amount,\r\n    RANK() OVER (ORDER BY paid_amount DESC) AS payment_rank,\r\n    SUM(paid_amount) OVER () AS total_all_payments\r\nFROM lms_enrollments e\r\nJOIN lms_students s ON s.id = e.student_id;\r\n```\r\n\r\n## Query Optimisation\r\n\r\n### Indexes\r\n\r\n```sql\r\n-- Add index on frequently searched column\r\nALTER TABLE lms_students ADD INDEX idx_email (email);\r\nALTER TABLE lms_enrollments ADD INDEX idx_student (student_id);\r\n\r\n-- Composite index for multi-column queries\r\nALTER TABLE lms_payments ADD INDEX idx_student_status (student_id, status);\r\n\r\n-- Check if query uses indexes\r\nEXPLAIN SELECT * FROM lms_students WHERE email = \'test@example.com\';\r\n```\r\n\r\n### Optimisation Rules\r\n1. Always index foreign key columns\r\n2. Index columns used in WHERE, JOIN ON, and ORDER BY\r\n3. Avoid SELECT * — specify only needed columns\r\n4. Use LIMIT to restrict result sets\r\n5. Avoid functions on indexed columns in WHERE clauses\r\n6. Use prepared statements (also prevents SQL injection)\r\n\r\n## Transactions\r\n\r\n```sql\r\nSTART TRANSACTION;\r\n\r\nINSERT INTO lms_enrollments (student_id, course_id) VALUES (1, 5);\r\nINSERT INTO lms_payments (student_id, enrollment_id, amount) VALUES (1, LAST_INSERT_ID(), 150000);\r\n\r\nCOMMIT;  -- Save both changes\r\n-- ROLLBACK;  -- Undo both if something went wrong\r\n```\r\n\r\n## Practical Task\r\n\r\nWrite a stored procedure that enrols a student in a course: checks if already enrolled, creates the enrollment record, creates a pending payment record, and returns a success/error status. Test with sample data.\r\n\r\n## Self-Check\r\n1. What is the difference between INNER JOIN and LEFT JOIN?\r\n2. Why should you add indexes to foreign key columns?\r\n3. What is a database transaction and when would you use one?', 3, 1, '2026-02-16 06:11:08'),
(28, 5, 'Authentication & Security', '## Web Application Security\r\n\r\nSecurity is not optional. A single vulnerability can expose all your users\' data. These are the most critical security practices for PHP developers.\r\n\r\n## Password Security\r\n\r\n```php\r\n// NEVER store plain text passwords\r\n// WRONG:\r\n$password = $_POST[\'password\'];\r\n$sql = \"INSERT INTO users (password) VALUES (\'$password\')\";\r\n\r\n// CORRECT: Hash with bcrypt\r\n$hash = password_hash($_POST[\'password\'], PASSWORD_DEFAULT);\r\n// Stores something like: $2y$10$...\r\n\r\n// Verify on login\r\nif (password_verify($_POST[\'password\'], $storedHash)) {\r\n    // Login successful\r\n}\r\n```\r\n\r\n## SQL Injection Prevention\r\n\r\n```php\r\n// VULNERABLE - Never do this:\r\n$id = $_GET[\'id\'];\r\n$sql = \"SELECT * FROM users WHERE id = $id\";\r\n// Attacker sends: ?id=1 OR 1=1 -- (returns all users)\r\n\r\n// SAFE - Always use prepared statements:\r\n$stmt = $pdo->prepare(\"SELECT * FROM users WHERE id = ?\");\r\n$stmt->execute([$_GET[\'id\']]);\r\n$user = $stmt->fetch();\r\n```\r\n\r\n## XSS (Cross-Site Scripting) Prevention\r\n\r\n```php\r\n// VULNERABLE:\r\necho $_GET[\'name\'];  // Attacker sends: <script>alert(\'hacked\')</script>\r\n\r\n// SAFE - Always escape output:\r\necho htmlspecialchars($_GET[\'name\'], ENT_QUOTES, \'UTF-8\');\r\n```\r\n\r\n## CSRF (Cross-Site Request Forgery) Prevention\r\n\r\n```php\r\n// Generate token on form load\r\nsession_start();\r\n$_SESSION[\'csrf_token\'] = bin2hex(random_bytes(32));\r\n\r\n// In form:\r\n// <input type=\"hidden\" name=\"csrf_token\" value=\"<?= $_SESSION[\'csrf_token\'] ?>\">\r\n\r\n// Verify on form submission:\r\nif (!hash_equals($_SESSION[\'csrf_token\'], $_POST[\'csrf_token\'] ?? \'\')) {\r\n    http_response_code(419);\r\n    exit(\'Invalid CSRF token\');\r\n}\r\n```\r\n\r\n## File Upload Security\r\n\r\n```php\r\n$allowedTypes = [\'image/jpeg\', \'image/png\', \'image/webp\'];\r\n$maxSize = 3 * 1024 * 1024; // 3MB\r\n\r\n$file = $_FILES[\'upload\'];\r\n\r\n// Check MIME type (not just extension)\r\n$finfo = new finfo(FILEINFO_MIME_TYPE);\r\n$mimeType = $finfo->file($file[\'tmp_name\']);\r\n\r\nif (!in_array($mimeType, $allowedTypes)) {\r\n    exit(\'Invalid file type\');\r\n}\r\n\r\nif ($file[\'size\'] > $maxSize) {\r\n    exit(\'File too large\');\r\n}\r\n\r\n// Generate safe filename\r\n$ext = pathinfo($file[\'name\'], PATHINFO_EXTENSION);\r\n$safeName = bin2hex(random_bytes(8)) . \'.\' . $ext;\r\nmove_uploaded_file($file[\'tmp_name\'], \'uploads/\' . $safeName);\r\n```\r\n\r\n## Practical Task\r\n\r\nAudit the blog application from the Web Development course. Find and fix: any SQL injection vulnerabilities, any XSS vulnerabilities, missing CSRF protection, and insecure file uploads. Document each vulnerability found and the fix applied.\r\n\r\n## Self-Check\r\n1. Why should you never store plain-text passwords?\r\n2. What is SQL injection and how do prepared statements prevent it?\r\n3. What is the difference between XSS and CSRF attacks?', 4, 1, '2026-02-16 06:11:08'),
(29, 5, 'RESTful API Development', '## Building a Professional REST API\r\n\r\nA REST API allows your PHP backend to serve data to any frontend — web, mobile, or third-party applications.\r\n\r\n## API Design Principles\r\n\r\n### Resource Naming\r\n- Use nouns, not verbs: `/api/users` not `/api/getUsers`\r\n- Use plural nouns: `/api/courses` not `/api/course`\r\n- Use hierarchy for relationships: `/api/courses/5/lessons`\r\n- Use query parameters for filtering: `/api/courses?level=beginner&limit=10`\r\n\r\n### HTTP Methods & Status Codes\r\n\r\n| Method | Action | Success Code |\r\n|---|---|---|\r\n| GET | Retrieve resource(s) | 200 OK |\r\n| POST | Create new resource | 201 Created |\r\n| PUT | Replace entire resource | 200 OK |\r\n| PATCH | Update part of resource | 200 OK |\r\n| DELETE | Remove resource | 204 No Content |\r\n\r\n## Building the API\r\n\r\n```php\r\n// api/index.php\r\nheader(\'Content-Type: application/json\');\r\nheader(\'Access-Control-Allow-Origin: *\');\r\n\r\n$method = $_SERVER[\'REQUEST_METHOD\'];\r\n$uri = parse_url($_SERVER[\'REQUEST_URI\'], PHP_URL_PATH);\r\n$segments = explode(\'/\', trim($uri, \'/\'));\r\n\r\n// Route: GET /api/courses\r\nif ($method === \'GET\' && $segments[1] === \'courses\') {\r\n    $courses = $pdo->query(\"SELECT * FROM lms_courses WHERE is_active=1\")->fetchAll();\r\n    http_response_code(200);\r\n    echo json_encode([\'ok\' => true, \'data\' => $courses]);\r\n    exit;\r\n}\r\n\r\n// Route: POST /api/courses\r\nif ($method === \'POST\' && $segments[1] === \'courses\') {\r\n    $body = json_decode(file_get_contents(\'php://input\'), true);\r\n    // validate...\r\n    $stmt = $pdo->prepare(\"INSERT INTO lms_courses (title, price) VALUES (?,?)\");\r\n    $stmt->execute([$body[\'title\'], $body[\'price\']]);\r\n    http_response_code(201);\r\n    echo json_encode([\'ok\' => true, \'id\' => $pdo->lastInsertId()]);\r\n    exit;\r\n}\r\n\r\n// 404 fallback\r\nhttp_response_code(404);\r\necho json_encode([\'ok\' => false, \'error\' => \'Endpoint not found\']);\r\n```\r\n\r\n## API Authentication with JWT\r\n\r\nJSON Web Tokens (JWT) are a standard for API authentication:\r\n\r\n1. User logs in with email/password\r\n2. Server validates credentials and returns a JWT token\r\n3. Client stores the token (localStorage or cookie)\r\n4. Client sends token in every request: `Authorization: Bearer {token}`\r\n5. Server validates the token on each request\r\n\r\n## API Documentation\r\n\r\nGood APIs have clear documentation. Use tools like:\r\n- **Postman**: Test and document APIs\r\n- **Swagger/OpenAPI**: Standard API documentation format\r\n- **Insomnia**: Alternative to Postman\r\n\r\n## Practical Task\r\n\r\nBuild a complete REST API for a course catalogue. Endpoints: GET /api/courses (list all), GET /api/courses/{id} (single course), POST /api/courses (create), PUT /api/courses/{id} (update), DELETE /api/courses/{id} (delete). Add JWT authentication. Test all endpoints in Postman.\r\n\r\n## Self-Check\r\n1. What is the difference between PUT and PATCH?\r\n2. What HTTP status code should a successful POST return?\r\n3. How does JWT authentication work?', 5, 1, '2026-02-16 06:11:08'),
(30, 5, 'Email, File Handling & Cron Jobs', '## Sending Email with PHP\r\n\r\n### PHPMailer (Recommended)\r\n\r\nPHPMailer is the most popular PHP email library. Install via Composer:\r\n\r\n```bash\r\ncomposer require phpmailer/phpmailer\r\n```\r\n\r\n```php\r\nuse PHPMailerPHPMailerPHPMailer;\r\n\r\n$mail = new PHPMailer(true);\r\n\r\n// SMTP Configuration\r\n$mail->isSMTP();\r\n$mail->Host       = \'smtp.gmail.com\';\r\n$mail->SMTPAuth   = true;\r\n$mail->Username   = \'your@gmail.com\';\r\n$mail->Password   = \'your-app-password\';\r\n$mail->SMTPSecure = \'tls\';\r\n$mail->Port       = 587;\r\n\r\n// Email content\r\n$mail->setFrom(\'noreply@yourdomain.com\', \'Mirror LMS\');\r\n$mail->addAddress(\'student@example.com\', \'Student Name\');\r\n$mail->Subject = \'Welcome to Mirror LMS\';\r\n$mail->isHTML(true);\r\n$mail->Body = \'<h1>Welcome!</h1><p>Your account is ready.</p>\';\r\n\r\n$mail->send();\r\n```\r\n\r\n## File Handling\r\n\r\n```php\r\n// Read a file\r\n$content = file_get_contents(\'data.txt\');\r\n\r\n// Write to a file\r\nfile_put_contents(\'log.txt\', \"Error: \" . date(\'Y-m-d H:i:s\') . \"\r\n\", FILE_APPEND);\r\n\r\n// Check if file exists\r\nif (file_exists(\'uploads/photo.jpg\')) {\r\n    // process file\r\n}\r\n\r\n// Get file info\r\n$info = pathinfo(\'document.pdf\');\r\necho $info[\'extension\'];  // pdf\r\necho $info[\'filename\'];   // document\r\n\r\n// List files in directory\r\n$files = glob(\'uploads/*.jpg\');\r\nforeach ($files as $file) {\r\n    echo basename($file) . \"\r\n\";\r\n}\r\n\r\n// Delete a file\r\nunlink(\'uploads/old-photo.jpg\');\r\n```\r\n\r\n## CSV Import/Export\r\n\r\n```php\r\n// Export to CSV\r\n$data = $pdo->query(\"SELECT name, email, course FROM lms_students\")->fetchAll();\r\n\r\nheader(\'Content-Type: text/csv\');\r\nheader(\'Content-Disposition: attachment; filename=\"students.csv\"\');\r\n\r\n$fp = fopen(\'php://output\', \'w\');\r\nfputcsv($fp, [\'Name\', \'Email\', \'Course\']); // Header row\r\nforeach ($data as $row) {\r\n    fputcsv($fp, $row);\r\n}\r\nfclose($fp);\r\n\r\n// Import from CSV\r\nif (($handle = fopen(\'students.csv\', \'r\')) !== false) {\r\n    fgetcsv($handle); // Skip header\r\n    while (($row = fgetcsv($handle)) !== false) {\r\n        [$name, $email, $course] = $row;\r\n        // Insert into database...\r\n    }\r\n    fclose($handle);\r\n}\r\n```\r\n\r\n## Cron Jobs (Scheduled Tasks)\r\n\r\nCron jobs run PHP scripts automatically at scheduled times:\r\n\r\n```bash\r\n# Edit crontab\r\ncrontab -e\r\n\r\n# Format: minute hour day month weekday command\r\n# Run every day at 8am\r\n0 8 * * * php /var/www/html/lms/cron/send_reminders.php\r\n\r\n# Run every hour\r\n0 * * * * php /var/www/html/lms/cron/check_due_payments.php\r\n\r\n# Run every Monday at 9am\r\n0 9 * * 1 php /var/www/html/lms/cron/weekly_report.php\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a payment reminder system: a cron job script that queries the database for installment students whose next_due_date is within 3 days, and sends them a reminder email using PHPMailer. Log all sent emails to a file.\r\n\r\n## Self-Check\r\n1. Why should you use PHPMailer instead of PHP\'s built-in mail() function?\r\n2. What does FILE_APPEND do in file_put_contents()?\r\n3. What does the cron expression `0 8 * * *` mean?', 6, 1, '2026-02-16 06:11:08'),
(31, 5, 'Testing & Code Quality', '## Why Testing Matters\r\n\r\nUntested code is broken code waiting to be discovered. Testing catches bugs early, documents expected behaviour, and gives you confidence to refactor.\r\n\r\n## Types of Testing\r\n\r\n**Unit Testing**: Test individual functions/methods in isolation\r\n**Integration Testing**: Test how components work together\r\n**End-to-End Testing**: Test the complete user flow through the application\r\n**Manual Testing**: Human testers following test scripts\r\n\r\n## PHPUnit\r\n\r\nPHPUnit is the standard testing framework for PHP:\r\n\r\n```bash\r\ncomposer require --dev phpunit/phpunit\r\n```\r\n\r\n```php\r\n// src/Calculator.php\r\nclass Calculator {\r\n    public function add(float $a, float $b): float {\r\n        return $a + $b;\r\n    }\r\n\r\n    public function divide(float $a, float $b): float {\r\n        if ($b === 0.0) {\r\n            throw new InvalidArgumentException(\'Cannot divide by zero\');\r\n        }\r\n        return $a / $b;\r\n    }\r\n}\r\n\r\n// tests/CalculatorTest.php\r\nuse PHPUnitFrameworkTestCase;\r\n\r\nclass CalculatorTest extends TestCase {\r\n    private Calculator $calc;\r\n\r\n    protected function setUp(): void {\r\n        $this->calc = new Calculator();\r\n    }\r\n\r\n    public function testAdd(): void {\r\n        $this->assertEquals(5, $this->calc->add(2, 3));\r\n        $this->assertEquals(0, $this->calc->add(-1, 1));\r\n    }\r\n\r\n    public function testDivideByZeroThrowsException(): void {\r\n        $this->expectException(InvalidArgumentException::class);\r\n        $this->calc->divide(10, 0);\r\n    }\r\n}\r\n```\r\n\r\n## Code Quality Tools\r\n\r\n**PHP_CodeSniffer**: Checks coding standards (PSR-12)\r\n**PHPStan**: Static analysis — finds bugs without running code\r\n**PHP-CS-Fixer**: Automatically fixes code style issues\r\n\r\n## PSR Standards\r\n\r\nPSR (PHP Standards Recommendations) are coding standards:\r\n- **PSR-1**: Basic coding standard (class names, method names)\r\n- **PSR-4**: Autoloading standard (namespace to directory mapping)\r\n- **PSR-12**: Extended coding style guide\r\n\r\n## Practical Task\r\n\r\nWrite unit tests for the Student class from Lesson 2. Test: constructor sets properties correctly, getGrade() returns correct grade for different GPA values, edge cases (GPA = 0, GPA = 4.0). Achieve 100% code coverage for the Student class.\r\n\r\n## Self-Check\r\n1. What is the difference between unit testing and integration testing?\r\n2. What does PHPStan do?\r\n3. What is PSR-12?', 7, 1, '2026-02-16 06:11:08'),
(32, 5, 'Capstone: E-Commerce Application', '## Final Project: Build a Complete E-Commerce Store\r\n\r\nYou will build a fully functional e-commerce application using PHP, MySQL, and modern web technologies.\r\n\r\n## Features Required\r\n\r\n### Customer-Facing\r\n- Product catalogue with categories and search\r\n- Product detail page with images and description\r\n- Shopping cart (session-based)\r\n- Checkout with order summary\r\n- User registration and login\r\n- Order history and tracking\r\n- Email confirmation on order placement\r\n\r\n### Admin Panel\r\n- Dashboard with sales statistics\r\n- Product management (CRUD)\r\n- Category management\r\n- Order management (view, update status)\r\n- Customer list\r\n- Basic sales report (CSV export)\r\n\r\n## Database Schema\r\n\r\n```sql\r\nCREATE TABLE products (\r\n    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n    category_id INT UNSIGNED,\r\n    name VARCHAR(200) NOT NULL,\r\n    description TEXT,\r\n    price DECIMAL(10,2) NOT NULL,\r\n    stock INT UNSIGNED DEFAULT 0,\r\n    image VARCHAR(255),\r\n    is_active TINYINT(1) DEFAULT 1,\r\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\r\n);\r\n\r\nCREATE TABLE orders (\r\n    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n    user_id INT UNSIGNED,\r\n    total DECIMAL(10,2) NOT NULL,\r\n    status ENUM(\'pending\',\'processing\',\'shipped\',\'delivered\',\'cancelled\') DEFAULT \'pending\',\r\n    shipping_address TEXT,\r\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\r\n);\r\n\r\nCREATE TABLE order_items (\r\n    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n    order_id INT UNSIGNED NOT NULL,\r\n    product_id INT UNSIGNED NOT NULL,\r\n    quantity INT UNSIGNED NOT NULL,\r\n    unit_price DECIMAL(10,2) NOT NULL\r\n);\r\n```\r\n\r\n## Security Requirements\r\n- All inputs sanitised and validated\r\n- Prepared statements for all queries\r\n- CSRF protection on all forms\r\n- Password hashing with bcrypt\r\n- Admin routes protected by role check\r\n- File uploads validated (images only, max 2MB)\r\n\r\n## Evaluation Criteria\r\n- Feature completeness (30%)\r\n- Code quality and security (25%)\r\n- Database design (15%)\r\n- UI/UX quality (15%)\r\n- Testing coverage (15%)\r\n\r\n## Self-Check\r\n1. How do you prevent a customer from accessing admin routes?\r\n2. How would you handle a payment failure mid-checkout?\r\n3. What indexes would you add to the orders table for performance?', 8, 1, '2026-02-16 06:11:08'),
(33, 6, 'Introduction to Mobile Development', '## Mobile Development Overview\r\n\r\nMobile apps run on smartphones and tablets. The two dominant platforms are Android (Google, ~72% market share) and iOS (Apple, ~27%).\r\n\r\n## Development Approaches\r\n\r\n**Native**: Built specifically for one platform.\r\n- Android: Kotlin or Java, Android Studio IDE\r\n- iOS: Swift or Objective-C, Xcode IDE\r\n- Pros: Best performance, full platform access\r\n- Cons: Two separate codebases to maintain\r\n\r\n**Cross-Platform**: One codebase for both platforms.\r\n- React Native: JavaScript, by Meta\r\n- Flutter: Dart language, by Google\r\n- Xamarin: C#, by Microsoft\r\n- Pros: Single codebase, faster development\r\n- Cons: Slightly lower performance, some native features harder to access\r\n\r\n**Progressive Web Apps (PWA)**: Web apps that behave like native apps.\r\n- Built with HTML, CSS, JavaScript\r\n- Work offline with Service Workers\r\n- Can be installed on home screen\r\n- No app store required\r\n\r\n## Setting Up Flutter\r\n\r\n1. Download Flutter SDK from flutter.dev\r\n2. Install Android Studio and Android SDK\r\n3. Run `flutter doctor` to check setup\r\n4. Create project: `flutter create my_app`\r\n5. Run on emulator: `flutter run`\r\n\r\n## Dart Basics\r\n\r\n```dart\r\nvoid main() {\r\n  String name = \'Amara\';\r\n  int age = 22;\r\n  print(\'Hello, $name! You are $age years old.\');\r\n\r\n  List<String> courses = [\'Flutter\', \'Dart\', \'Firebase\'];\r\n  for (var course in courses) {\r\n    print(course);\r\n  }\r\n}\r\n```\r\n\r\n## Practical Task\r\n\r\nInstall Flutter and create your first app. Modify the default counter app to count down instead of up. Add a reset button. Run on an Android emulator.\r\n\r\n## Self-Check\r\n1. What is the difference between native and cross-platform development?\r\n2. What language does Flutter use?\r\n3. What is a Progressive Web App?', 1, 1, '2026-02-16 06:11:08'),
(34, 6, 'Flutter UI Fundamentals', '## Flutter Widget System\r\n\r\nIn Flutter, everything is a widget. Widgets are the building blocks of a Flutter app\'s UI.\r\n\r\n## Widget Types\r\n\r\n**Stateless Widgets**: Don\'t change over time.\r\n```dart\r\nclass WelcomeCard extends StatelessWidget {\r\n  final String name;\r\n  const WelcomeCard({required this.name});\r\n\r\n  @override\r\n  Widget build(BuildContext context) {\r\n    return Card(\r\n      child: Padding(\r\n        padding: EdgeInsets.all(16),\r\n        child: Text(\'Welcome, $name!\', style: TextStyle(fontSize: 18)),\r\n      ),\r\n    );\r\n  }\r\n}\r\n```\r\n\r\n**Stateful Widgets**: Can change over time (user interaction, data loading).\r\n```dart\r\nclass Counter extends StatefulWidget {\r\n  @override\r\n  _CounterState createState() => _CounterState();\r\n}\r\n\r\nclass _CounterState extends State<Counter> {\r\n  int _count = 0;\r\n\r\n  @override\r\n  Widget build(BuildContext context) {\r\n    return Column(\r\n      children: [\r\n        Text(\'Count: $_count\', style: TextStyle(fontSize: 24)),\r\n        ElevatedButton(\r\n          onPressed: () => setState(() => _count++),\r\n          child: Text(\'Increment\'),\r\n        ),\r\n      ],\r\n    );\r\n  }\r\n}\r\n```\r\n\r\n## Layout Widgets\r\n\r\n- **Column**: Vertical layout\r\n- **Row**: Horizontal layout\r\n- **Stack**: Overlapping widgets\r\n- **Container**: Box with padding, margin, decoration\r\n- **Expanded**: Fill available space\r\n- **ListView**: Scrollable list\r\n- **GridView**: Scrollable grid\r\n\r\n## Practical Task\r\n\r\nBuild a student profile card app. Display: profile photo (placeholder), name, course, GPA, and a list of enrolled subjects. Use Column, Row, Card, and ListView widgets.\r\n\r\n## Self-Check\r\n1. What is the difference between StatelessWidget and StatefulWidget?\r\n2. What does setState() do?\r\n3. When would you use Stack instead of Column?', 2, 1, '2026-02-16 06:11:08'),
(35, 6, 'State Management & Navigation', '## State Management in Flutter\r\n\r\nAs apps grow, managing state across multiple screens becomes complex. Flutter offers several state management solutions.\r\n\r\n## Provider (Recommended for Beginners)\r\n\r\n```dart\r\n// 1. Create a ChangeNotifier\r\nclass CartProvider extends ChangeNotifier {\r\n  List<String> _items = [];\r\n  List<String> get items => _items;\r\n  int get count => _items.length;\r\n\r\n  void addItem(String item) {\r\n    _items.add(item);\r\n    notifyListeners(); // Rebuild listening widgets\r\n  }\r\n\r\n  void removeItem(String item) {\r\n    _items.remove(item);\r\n    notifyListeners();\r\n  }\r\n}\r\n\r\n// 2. Wrap app with ChangeNotifierProvider\r\nMultiProvider(\r\n  providers: [ChangeNotifierProvider(create: (_) => CartProvider())],\r\n  child: MyApp(),\r\n)\r\n\r\n// 3. Read state in any widget\r\nfinal cart = Provider.of<CartProvider>(context);\r\nText(\'Cart: ${cart.count} items\');\r\n```\r\n\r\n## Navigation\r\n\r\n```dart\r\n// Push a new screen\r\nNavigator.push(\r\n  context,\r\n  MaterialPageRoute(builder: (context) => DetailScreen(id: 5)),\r\n);\r\n\r\n// Pop back\r\nNavigator.pop(context);\r\n\r\n// Named routes\r\nNavigator.pushNamed(context, \'/profile\', arguments: {\'userId\': 42});\r\n\r\n// Bottom navigation\r\nBottomNavigationBar(\r\n  currentIndex: _selectedIndex,\r\n  onTap: (index) => setState(() => _selectedIndex = index),\r\n  items: [\r\n    BottomNavigationBarItem(icon: Icon(Icons.home), label: \'Home\'),\r\n    BottomNavigationBarItem(icon: Icon(Icons.person), label: \'Profile\'),\r\n  ],\r\n)\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a 3-screen app with bottom navigation: Home (list of courses), Detail (course info), Profile (user info). Use Provider to share the selected course between screens.\r\n\r\n## Self-Check\r\n1. What is the purpose of notifyListeners() in Provider?\r\n2. What is the difference between push and pushReplacement in navigation?\r\n3. When would you use named routes?', 3, 1, '2026-02-16 06:11:08'),
(36, 6, 'Firebase & Backend Integration', '## What is Firebase?\r\n\r\nFirebase is Google\'s mobile and web application development platform. It provides backend services without writing server code.\r\n\r\n## Firebase Services\r\n\r\n- **Firestore**: NoSQL cloud database, real-time sync\r\n- **Authentication**: Email/password, Google, Facebook, phone login\r\n- **Storage**: File storage for images and videos\r\n- **Cloud Functions**: Serverless backend logic\r\n- **Analytics**: App usage tracking\r\n- **Cloud Messaging (FCM)**: Push notifications\r\n\r\n## Setting Up Firebase in Flutter\r\n\r\n```bash\r\n# Install FlutterFire CLI\r\ndart pub global activate flutterfire_cli\r\n\r\n# Configure Firebase\r\nflutterfire configure\r\n\r\n# Add dependencies to pubspec.yaml\r\nfirebase_core: ^2.0.0\r\ncloud_firestore: ^4.0.0\r\nfirebase_auth: ^4.0.0\r\n```\r\n\r\n## Firestore CRUD\r\n\r\n```dart\r\nfinal db = FirebaseFirestore.instance;\r\n\r\n// Create\r\nawait db.collection(\'students\').add({\r\n  \'name\': \'Amara\',\r\n  \'email\': \'amara@example.com\',\r\n  \'course\': \'Flutter Development\',\r\n  \'createdAt\': FieldValue.serverTimestamp(),\r\n});\r\n\r\n// Read (real-time stream)\r\nStreamBuilder<QuerySnapshot>(\r\n  stream: db.collection(\'students\').snapshots(),\r\n  builder: (context, snapshot) {\r\n    if (!snapshot.hasData) return CircularProgressIndicator();\r\n    final docs = snapshot.data!.docs;\r\n    return ListView.builder(\r\n      itemCount: docs.length,\r\n      itemBuilder: (context, i) => ListTile(\r\n        title: Text(docs[i][\'name\']),\r\n      ),\r\n    );\r\n  },\r\n)\r\n\r\n// Update\r\nawait db.collection(\'students\').doc(docId).update({\'course\': \'Advanced Flutter\'});\r\n\r\n// Delete\r\nawait db.collection(\'students\').doc(docId).delete();\r\n```\r\n\r\n## Firebase Authentication\r\n\r\n```dart\r\nfinal auth = FirebaseAuth.instance;\r\n\r\n// Register\r\nawait auth.createUserWithEmailAndPassword(\r\n  email: \'user@example.com\',\r\n  password: \'SecurePass123\',\r\n);\r\n\r\n// Login\r\nawait auth.signInWithEmailAndPassword(\r\n  email: \'user@example.com\',\r\n  password: \'SecurePass123\',\r\n);\r\n\r\n// Logout\r\nawait auth.signOut();\r\n\r\n// Check auth state\r\nStreamBuilder<User?>(\r\n  stream: auth.authStateChanges(),\r\n  builder: (context, snapshot) {\r\n    if (snapshot.hasData) return HomeScreen();\r\n    return LoginScreen();\r\n  },\r\n)\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a note-taking app with Firebase. Features: email/password authentication, create/read/update/delete notes stored in Firestore, real-time sync across devices. Each user sees only their own notes.\r\n\r\n## Self-Check\r\n1. What is the difference between Firestore and Firebase Realtime Database?\r\n2. How does authStateChanges() work?\r\n3. What is a Firestore collection vs a document?', 4, 1, '2026-02-16 06:11:08'),
(37, 6, 'Publishing & App Store Deployment', '## Preparing Your App for Release\r\n\r\nBefore publishing, your app must be production-ready: no debug code, proper icons, splash screen, and optimised performance.\r\n\r\n## App Icons & Splash Screen\r\n\r\n```bash\r\n# flutter_launcher_icons package\r\nflutter pub add flutter_launcher_icons\r\n\r\n# pubspec.yaml\r\nflutter_launcher_icons:\r\n  android: true\r\n  ios: true\r\n  image_path: \'assets/icon.png\'  # 1024x1024px\r\n\r\nflutter pub run flutter_launcher_icons\r\n```\r\n\r\n## Building for Release\r\n\r\n```bash\r\n# Android APK\r\nflutter build apk --release\r\n# Output: build/app/outputs/flutter-apk/app-release.apk\r\n\r\n# Android App Bundle (required for Play Store)\r\nflutter build appbundle --release\r\n# Output: build/app/outputs/bundle/release/app-release.aab\r\n\r\n# iOS (requires Mac + Xcode)\r\nflutter build ios --release\r\n```\r\n\r\n## Google Play Store Submission\r\n\r\n1. Create Google Play Developer account ($25 one-time fee)\r\n2. Create a new app in Play Console\r\n3. Complete store listing: title, description, screenshots, icon\r\n4. Set content rating\r\n5. Set up pricing and distribution\r\n6. Upload your .aab file\r\n7. Submit for review (typically 1-3 days)\r\n\r\n## App Store (iOS) Submission\r\n\r\n1. Enrol in Apple Developer Program ($99/year)\r\n2. Create App ID in Apple Developer portal\r\n3. Create app in App Store Connect\r\n4. Archive and upload from Xcode\r\n5. Complete metadata: description, screenshots, keywords\r\n6. Submit for review (typically 1-2 days)\r\n\r\n## App Store Optimisation (ASO)\r\n\r\nASO is SEO for app stores:\r\n- **Title**: Include primary keyword (50 chars max)\r\n- **Description**: First 3 lines are most important\r\n- **Keywords**: Research with Sensor Tower or AppFollow\r\n- **Screenshots**: Show key features, use device frames\r\n- **Ratings**: Prompt users to rate at the right moment\r\n\r\n## Practical Task\r\n\r\nPrepare your note-taking app for release. Add a proper app icon and splash screen. Build a release APK. Create a complete Play Store listing with description, screenshots, and content rating. (You don\'t need to actually publish — just prepare all assets.)\r\n\r\n## Self-Check\r\n1. What is the difference between an APK and an App Bundle?\r\n2. What is App Store Optimisation?\r\n3. What is the annual cost of an Apple Developer account?', 5, 1, '2026-02-16 06:11:08'),
(38, 6, 'Advanced Flutter: Animations & Performance', '## Flutter Animations\r\n\r\nAnimations make apps feel polished and responsive. Flutter provides several animation systems.\r\n\r\n## Implicit Animations (Easiest)\r\n\r\nImplicit animations automatically animate between values:\r\n\r\n```dart\r\nAnimatedContainer(\r\n  duration: Duration(milliseconds: 300),\r\n  curve: Curves.easeInOut,\r\n  width: _isExpanded ? 200 : 100,\r\n  height: _isExpanded ? 200 : 100,\r\n  color: _isExpanded ? Colors.blue : Colors.red,\r\n  child: Text(\'Tap me\'),\r\n)\r\n\r\nAnimatedOpacity(\r\n  opacity: _isVisible ? 1.0 : 0.0,\r\n  duration: Duration(milliseconds: 500),\r\n  child: Text(\'Fade me\'),\r\n)\r\n```\r\n\r\n## Explicit Animations (More Control)\r\n\r\n```dart\r\nclass SpinningLogo extends StatefulWidget {\r\n  @override\r\n  _SpinningLogoState createState() => _SpinningLogoState();\r\n}\r\n\r\nclass _SpinningLogoState extends State<SpinningLogo>\r\n    with SingleTickerProviderStateMixin {\r\n  late AnimationController _controller;\r\n\r\n  @override\r\n  void initState() {\r\n    super.initState();\r\n    _controller = AnimationController(\r\n      duration: Duration(seconds: 2),\r\n      vsync: this,\r\n    )..repeat();\r\n  }\r\n\r\n  @override\r\n  Widget build(BuildContext context) {\r\n    return RotationTransition(\r\n      turns: _controller,\r\n      child: FlutterLogo(size: 100),\r\n    );\r\n  }\r\n\r\n  @override\r\n  void dispose() {\r\n    _controller.dispose();\r\n    super.dispose();\r\n  }\r\n}\r\n```\r\n\r\n## Performance Optimisation\r\n\r\n1. Use `const` constructors wherever possible\r\n2. Use `ListView.builder` instead of `ListView` for long lists\r\n3. Avoid rebuilding the entire widget tree — use `Consumer` or `Selector` with Provider\r\n4. Use `RepaintBoundary` to isolate expensive widgets\r\n5. Profile with Flutter DevTools: identify jank (frames taking >16ms)\r\n\r\n## Practical Task\r\n\r\nAdd animations to your note-taking app: animated list item insertion/deletion, a hero animation when opening a note, and a loading shimmer effect while data loads from Firestore.\r\n\r\n## Self-Check\r\n1. What is the difference between implicit and explicit animations?\r\n2. Why should you always call dispose() on an AnimationController?\r\n3. What is the target frame rate for smooth Flutter animations?', 6, 1, '2026-02-16 06:11:08'),
(39, 6, 'Monetisation & Analytics', '## Monetising Your App\r\n\r\nThere are several business models for mobile apps:\r\n\r\n**Free with Ads**: Show ads using Google AdMob. Low revenue per user but scales with downloads.\r\n**Freemium**: Free basic features, paid premium features. Most popular model.\r\n**Paid App**: One-time purchase. Works for niche, high-value apps.\r\n**Subscription**: Recurring monthly/annual fee. Best for content and services.\r\n**In-App Purchases**: Buy virtual goods, extra content, or features.\r\n\r\n## Google AdMob Integration\r\n\r\n```dart\r\n// pubspec.yaml\r\ngoogle_mobile_ads: ^3.0.0\r\n\r\n// Banner ad\r\nfinal BannerAd myBanner = BannerAd(\r\n  adUnitId: \'ca-app-pub-3940256099942544/6300978111\', // Test ID\r\n  size: AdSize.banner,\r\n  request: AdRequest(),\r\n  listener: BannerAdListener(\r\n    onAdLoaded: (ad) => setState(() => _isBannerLoaded = true),\r\n    onAdFailedToLoad: (ad, error) => ad.dispose(),\r\n  ),\r\n);\r\nmyBanner.load();\r\n```\r\n\r\n## In-App Purchases\r\n\r\n```dart\r\n// in_app_purchase package\r\nfinal InAppPurchase _iap = InAppPurchase.instance;\r\n\r\n// Check availability\r\nfinal bool available = await _iap.isAvailable();\r\n\r\n// Load products\r\nconst Set<String> ids = {\'premium_monthly\', \'premium_annual\'};\r\nfinal ProductDetailsResponse response = await _iap.queryProductDetails(ids);\r\n\r\n// Purchase\r\nawait _iap.buyNonConsumable(\r\n  purchaseParam: PurchaseParam(productDetails: response.productDetails.first),\r\n);\r\n```\r\n\r\n## Firebase Analytics\r\n\r\n```dart\r\nfinal analytics = FirebaseAnalytics.instance;\r\n\r\n// Log custom event\r\nawait analytics.logEvent(\r\n  name: \'course_started\',\r\n  parameters: {\'course_id\': \'5\', \'course_name\': \'Flutter Dev\'},\r\n);\r\n\r\n// Log screen view\r\nawait analytics.logScreenView(screenName: \'CourseDetail\');\r\n\r\n// Set user property\r\nawait analytics.setUserProperty(name: \'subscription_tier\', value: \'premium\');\r\n```\r\n\r\n## Practical Task\r\n\r\nAdd a freemium model to your note-taking app: free users can create up to 5 notes, premium users have unlimited notes. Implement a paywall screen with in-app purchase. Add Firebase Analytics events for key user actions.\r\n\r\n## Self-Check\r\n1. What is the difference between a consumable and non-consumable in-app purchase?\r\n2. What is the freemium model?\r\n3. What Firebase Analytics event would you log when a user completes a purchase?', 7, 1, '2026-02-16 06:11:08'),
(40, 6, 'Capstone: Full Mobile Application', '## Final Project: Build a Complete Mobile App\r\n\r\nYou will build a production-ready mobile application using Flutter and Firebase.\r\n\r\n## Project Options (Choose One)\r\n\r\n### Option A: Learning App\r\nA mobile version of a course platform:\r\n- Browse courses by category\r\n- Enrol and track progress\r\n- Watch video lessons\r\n- Take quizzes\r\n- View certificates\r\n\r\n### Option B: Marketplace App\r\nA buy/sell marketplace:\r\n- List items for sale with photos\r\n- Browse and search listings\r\n- Chat between buyer and seller\r\n- User profiles and ratings\r\n\r\n### Option C: Health & Fitness Tracker\r\n- Log daily workouts\r\n- Track calories and water intake\r\n- View progress charts\r\n- Set and track goals\r\n- Reminders via push notifications\r\n\r\n## Required Technical Features\r\n\r\n- Firebase Authentication (email/password + Google Sign-In)\r\n- Firestore database with proper security rules\r\n- Firebase Storage for image uploads\r\n- Push notifications (FCM)\r\n- Offline support (Firestore offline persistence)\r\n- At least 2 animations\r\n- Responsive layout (phone + tablet)\r\n- Published to Google Play (internal testing track)\r\n\r\n## Evaluation Criteria\r\n- Feature completeness (25%)\r\n- Code quality and architecture (25%)\r\n- UI/UX design (20%)\r\n- Firebase integration (15%)\r\n- Performance and animations (15%)\r\n\r\n## Self-Check\r\n1. Does your app work offline?\r\n2. Are your Firestore security rules preventing unauthorised access?\r\n3. Have you tested on both a small phone (5 inch) and a tablet?', 8, 1, '2026-02-16 06:11:08'),
(41, 7, 'UX Research & User Personas', '## What is UX Research?\r\n\r\nUX research uncovers user needs, behaviours, and pain points through systematic investigation. Good design is grounded in evidence, not assumptions.\r\n\r\n## Research Methods\r\n\r\n**Qualitative (understanding why)**\r\n- User interviews: 1-on-1 conversations (30-60 min). Ask open-ended questions.\r\n- Contextual inquiry: Observe users in their real environment.\r\n- Diary studies: Users log their experiences over days or weeks.\r\n\r\n**Quantitative (understanding how many)**\r\n- Surveys: Collect data from many users at once.\r\n- Analytics: Heatmaps, click tracking, funnel analysis.\r\n- A/B testing: Compare two versions to see which performs better.\r\n\r\n## User Personas\r\n\r\nA persona is a fictional but research-based representation of your target user:\r\n- Name and photo (makes them feel real)\r\n- Demographics: Age, location, occupation\r\n- Goals: What they want to achieve\r\n- Frustrations: What gets in their way\r\n- Behaviours: How they use technology\r\n- Quote: A sentence capturing their attitude\r\n\r\n**Example Persona:**\r\nName: Chidinma, 28, Lagos. Marketing Manager.\r\nGoal: Learn digital marketing to advance her career.\r\nFrustration: Most courses are too theoretical and not Nigeria-specific.\r\nBehaviour: Learns on mobile during commute, prefers video over text.\r\nQuote: I need practical skills I can use at work on Monday.\r\n\r\n## Jobs-to-be-Done Framework\r\n\r\nFocus on what job the user is hiring your product to do:\r\nWhen [situation], I want to [motivation], so I can [outcome].\r\n\r\nExample: When commuting to work, I want to learn a skill in short sessions, so I can advance my career without sacrificing family time.\r\n\r\n## Practical Task\r\n\r\nConduct 3 user interviews about a digital product you use regularly. Ask about goals, frustrations, and workarounds. Create 2 user personas based on your findings. Write 3 Jobs-to-be-Done statements.\r\n\r\n## Self-Check\r\n1. What is the difference between qualitative and quantitative research?\r\n2. What are the 5 components of a user persona?\r\n3. What is the Jobs-to-be-Done framework?', 1, 1, '2026-02-16 06:11:08'),
(42, 7, 'Information Architecture & Wireframing', '## Information Architecture\r\n\r\nIA is the organisation, structure, and labelling of content to help users find what they need.\r\n\r\n## IA Deliverables\r\n\r\n**Sitemap**: A visual diagram showing all pages and their hierarchy.\r\n\r\nExample sitemap for an LMS:\r\n- Home\r\n- Courses > Course Detail > Enrol\r\n- Dashboard > My Courses > Assignments > Exams\r\n- Profile\r\n- Payments\r\n\r\n**Card Sorting**: Users group content into categories they find logical.\r\n**Tree Testing**: Test navigation structure without visual design.\r\n\r\n## Wireframing\r\n\r\nWireframes are low-fidelity blueprints of a screen. They show layout and structure without colour, images, or final copy.\r\n\r\n**Fidelity Levels:**\r\n- Lo-fi: Sketches on paper. Fast, cheap, easy to change.\r\n- Mid-fi: Greyscale digital wireframes in Figma or Balsamiq.\r\n- Hi-fi: Full colour, real content, interactive prototype.\r\n\r\n**Wireframe Conventions:**\r\n- Boxes with X = image placeholder\r\n- Lorem ipsum = placeholder text\r\n- Grey boxes = content areas\r\n- Annotations explain functionality\r\n\r\n## Practical Task\r\n\r\nCreate a sitemap for a 10-page e-commerce website. Then wireframe 3 key screens: Home, Product Listing, and Product Detail. Use Figma or paper. Focus on layout and content hierarchy, not visual design.\r\n\r\n## Self-Check\r\n1. What is the difference between a sitemap and a wireframe?\r\n2. What are the three fidelity levels of wireframes?\r\n3. What is card sorting and when would you use it?', 2, 1, '2026-02-16 06:11:08'),
(43, 7, 'Visual Design & UI Patterns', '## UI Design Patterns\r\n\r\nUI patterns are reusable solutions to common design problems. Using established patterns reduces cognitive load.\r\n\r\n## Common UI Patterns\r\n\r\n**Navigation Patterns:**\r\n- Top navigation bar: Desktop websites\r\n- Bottom navigation bar: Mobile apps (max 5 items)\r\n- Hamburger menu: Hidden navigation for mobile\r\n- Breadcrumbs: Show location in hierarchy\r\n- Tabs: Switch between related content sections\r\n\r\n**Content Patterns:**\r\n- Card grid: Display collections of items\r\n- List view: Detailed rows with actions\r\n- Infinite scroll: Load more content as user scrolls\r\n- Pagination: Navigate between pages\r\n\r\n**Feedback Patterns:**\r\n- Toast notifications: Brief, non-blocking messages\r\n- Modal dialogs: Require user action before continuing\r\n- Skeleton screens: Show layout while content loads\r\n- Empty states: Guide users when there is no content\r\n\r\n## Visual Hierarchy in UI\r\n\r\nGuide the eye to the most important content:\r\n1. Size: Larger = more important\r\n2. Colour: High contrast = more important\r\n3. Weight: Bold = more important\r\n4. Position: Top-left = seen first (F-pattern)\r\n5. Whitespace: Isolated elements draw attention\r\n\r\n## Practical Task\r\n\r\nDesign a dashboard screen for a student learning app. Include: a welcome header, 3 stat cards, a list of enrolled courses with progress bars, and a recent activity feed. Apply visual hierarchy principles. Design in Figma at 390px (mobile) and 1440px (desktop).\r\n\r\n## Self-Check\r\n1. What is a UI design pattern and why are they useful?\r\n2. Name 3 navigation patterns and when to use each.\r\n3. What is a skeleton screen?', 3, 1, '2026-02-16 06:11:08');
INSERT INTO `lms_lessons` (`id`, `course_id`, `title`, `content`, `sort_order`, `is_published`, `created_at`) VALUES
(44, 7, 'Prototyping & Usability Testing', '## Prototyping\r\n\r\nA prototype is a simulation of your product used to test ideas before building them. Prototypes save time and money by catching problems early.\r\n\r\n## Prototype Fidelity\r\n\r\n- Paper prototype: Sketches on paper. Fastest to create.\r\n- Digital wireframe prototype: Clickable greyscale screens.\r\n- High-fidelity prototype: Full visual design with interactions.\r\n\r\n## Prototyping in Figma\r\n\r\n1. Design your screens\r\n2. Switch to Prototype mode (top right panel)\r\n3. Select an element and drag the blue arrow to the destination screen\r\n4. Set trigger: On Click, On Hover, After Delay\r\n5. Set animation: Smart Animate, Dissolve, Slide In\r\n6. Press Play to preview\r\n\r\nSmart Animate: Figma automatically animates matching layers between screens. Name layers identically on both screens for smooth transitions.\r\n\r\n## Usability Testing\r\n\r\nUsability testing observes real users attempting tasks on your prototype.\r\n\r\n**Process:**\r\n1. Define tasks: What do you want users to do?\r\n2. Recruit participants: 5 users reveal ~85% of usability issues\r\n3. Conduct sessions: Observe, do not help. Ask users to think aloud.\r\n4. Analyse findings: Group issues by frequency and severity\r\n5. Iterate: Fix the most critical issues and test again\r\n\r\n**Severity Ratings:**\r\n- Critical: Prevents task completion\r\n- Major: Causes significant difficulty\r\n- Minor: Causes slight confusion\r\n- Cosmetic: Aesthetic issue only\r\n\r\n## Practical Task\r\n\r\nCreate a clickable prototype of your dashboard design. Write 3 usability test tasks. Conduct a usability test with 2 people. Document findings using severity ratings. Iterate on the top 2 issues found.\r\n\r\n## Self-Check\r\n1. How many users do you need for a usability test to find most issues?\r\n2. What is Smart Animate in Figma?\r\n3. What are the 4 severity ratings for usability issues?', 4, 1, '2026-02-16 06:11:08'),
(45, 7, 'Design Systems & Component Libraries', '## What is a Design System?\r\n\r\nA design system is a collection of reusable components, guided by clear standards, that can be assembled to build any number of applications.\r\n\r\nExamples: Google Material Design, Apple Human Interface Guidelines, IBM Carbon.\r\n\r\n## Design System Foundations\r\n\r\n- Colour tokens: primary, secondary, semantic, neutral\r\n- Typography scale: font families, sizes, weights, line heights\r\n- Spacing scale: 4px, 8px, 16px, 24px, 32px, 48px, 64px\r\n- Border radius: none, sm, md, lg, full\r\n- Shadow/elevation levels\r\n- Grid and layout system\r\n\r\n## Component Hierarchy\r\n\r\n- Atoms: Button, Input, Badge, Icon, Avatar\r\n- Molecules: Form field (label + input + error), Card, Search bar\r\n- Organisms: Navigation bar, Data table, Modal dialog\r\n- Templates: Page layouts\r\n\r\n## Building a Design System in Figma\r\n\r\n1. Create a dedicated Figma file for your design system\r\n2. Set up colour styles (right panel > Styles > +)\r\n3. Set up text styles for each typography level\r\n4. Create components for each UI element\r\n5. Add variants for different states (default, hover, focus, disabled, error)\r\n6. Document usage guidelines in the file\r\n\r\n## Component Variants in Figma\r\n\r\nButton component variants:\r\n- Type: Primary | Secondary | Ghost | Danger\r\n- Size: Small | Medium | Large\r\n- State: Default | Hover | Focus | Disabled | Loading\r\n\r\n## Practical Task\r\n\r\nBuild a mini design system in Figma for a fictional brand. Include: colour tokens, typography scale, spacing scale, and these components with all states: Button (4 types x 3 sizes), Input field, Card, Badge, and Navigation bar.\r\n\r\n## Self-Check\r\n1. What is the difference between atoms, molecules, and organisms?\r\n2. What are component variants in Figma?\r\n3. Name 3 real-world design systems.', 5, 1, '2026-02-16 06:11:08'),
(46, 7, 'Accessibility & Inclusive Design', '## What is Accessibility?\r\n\r\nAccessibility (a11y) means designing products that can be used by people with disabilities. 1 in 7 people worldwide has a disability. Accessible design is ethical and often legally required.\r\n\r\n## WCAG 4 Principles\r\n\r\n**Perceivable**: Information must be presentable in ways users can perceive.\r\n- Provide text alternatives for images (alt text)\r\n- Provide captions for videos\r\n- Ensure sufficient colour contrast (4.5:1 for normal text)\r\n- Do not use colour alone to convey information\r\n\r\n**Operable**: UI components must be operable.\r\n- All functionality available via keyboard\r\n- No content that flashes more than 3 times per second\r\n- Minimum touch target size: 44x44px\r\n\r\n**Understandable**: Information and UI must be understandable.\r\n- Use clear, simple language\r\n- Provide helpful error messages\r\n- Do not change context unexpectedly\r\n\r\n**Robust**: Content must work with assistive technologies.\r\n- Use semantic HTML\r\n- Use ARIA labels where needed\r\n- Test with screen readers (NVDA, VoiceOver)\r\n\r\n## Colour Contrast\r\n\r\nMinimum contrast ratios (WCAG AA):\r\n- Normal text: 4.5:1\r\n- Large text (18px+ or 14px+ bold): 3:1\r\n- UI components and graphics: 3:1\r\n\r\nTools: WebAIM Contrast Checker, Figma plugins (Contrast, A11y Annotation Kit)\r\n\r\n## Practical Task\r\n\r\nAudit your dashboard design for accessibility. Check: colour contrast ratios, touch target sizes, keyboard navigation order, and screen reader labels. Fix all WCAG AA violations. Document your findings and fixes.\r\n\r\n## Self-Check\r\n1. What does WCAG stand for and what are its 4 principles?\r\n2. What is the minimum contrast ratio for normal text (WCAG AA)?\r\n3. What is the minimum touch target size for mobile?', 6, 1, '2026-02-16 06:11:08'),
(47, 7, 'UX Writing & Microcopy', '## What is UX Writing?\r\n\r\nUX writing is the practice of crafting the words that appear in digital products: buttons, labels, error messages, onboarding flows, and empty states. Good UX writing guides users, reduces friction, and builds trust.\r\n\r\n## Principles of Good UX Writing\r\n\r\n- Clear: Use plain language. Avoid jargon.\r\n- Concise: Every word must earn its place. Cut ruthlessly.\r\n- Useful: Tell users what they need to know to complete their task.\r\n- Consistent: Use the same terms throughout.\r\n- Human: Write like a helpful person, not a legal document.\r\n\r\n## Microcopy Examples\r\n\r\n**Button labels:**\r\n- Bad: Submit | Good: Create Account\r\n- Bad: OK | Good: Got it\r\n- Bad: Delete | Good: Delete Course (be specific)\r\n\r\n**Error messages:**\r\n- Bad: Error 404 | Good: We could not find that page. Try searching or go back to the homepage.\r\n- Bad: Invalid input | Good: Please enter a valid email address (e.g. name@example.com)\r\n\r\n**Empty states:**\r\n- Bad: No data | Good: You have not enrolled in any courses yet. Browse our catalogue to get started.\r\n\r\n## Tone of Voice\r\n\r\nTone of voice defines how your brand communicates:\r\n- Friendly but professional\r\n- Encouraging but honest\r\n- Simple but not simplistic\r\n- Direct but not blunt\r\n\r\n## Practical Task\r\n\r\nRewrite the microcopy for 5 screens of your dashboard design: empty state, error state, success message, onboarding tooltip, and a confirmation dialog. Apply the 5 principles of good UX writing. Get feedback from 2 people on clarity.\r\n\r\n## Self-Check\r\n1. What are the 5 principles of good UX writing?\r\n2. Rewrite this error message: Invalid credentials. Make it helpful.\r\n3. What is an empty state and why does it matter?', 7, 1, '2026-02-16 06:11:08'),
(48, 7, 'Capstone: End-to-End UX Project', '## Final Project Brief\r\n\r\nYou will complete a full UX design project from research to high-fidelity prototype.\r\n\r\n## Project Scenario\r\n\r\nDesign a mobile app for SkillBridge Nigeria: a platform connecting Nigerian graduates with short-term freelance projects to build their portfolios and earn income while job hunting.\r\n\r\n## Phase 1: Research\r\n- Conduct 5 user interviews with recent graduates\r\n- Create 2 user personas\r\n- Write 5 Jobs-to-be-Done statements\r\n- Competitive analysis: 3 competitor apps\r\n\r\n## Phase 2: Define & Ideate\r\n- Affinity mapping: Group research findings into themes\r\n- Problem statement: How might we [problem] for [user] so that [outcome]?\r\n- Sitemap: All screens and their relationships\r\n- User flow: Step-by-step path for the primary task\r\n\r\n## Phase 3: Design\r\n- Lo-fi wireframes: All key screens on paper\r\n- Mid-fi wireframes: Greyscale in Figma\r\n- Design system: Colours, typography, components\r\n- Hi-fi mockups: Full visual design in Figma\r\n\r\n## Phase 4: Test & Iterate\r\n- Clickable prototype in Figma\r\n- Usability test with 5 participants\r\n- Severity-rated findings report\r\n- Iterated designs addressing critical and major issues\r\n\r\n## Deliverables\r\n- Research report (personas, JTBD, competitive analysis)\r\n- Sitemap and user flow diagram\r\n- Figma file with all screens (lo-fi, mid-fi, hi-fi)\r\n- Design system\r\n- Clickable prototype\r\n- Usability test report\r\n\r\n## Evaluation Criteria\r\n- Research quality and insight depth (20%)\r\n- Information architecture clarity (15%)\r\n- Visual design quality (25%)\r\n- Prototype completeness (20%)\r\n- Usability test rigour and iteration (20%)\r\n\r\n## Self-Check\r\n1. Does every design decision trace back to a research finding?\r\n2. Did you test with real users (not just classmates)?\r\n3. Is your prototype realistic enough to test the core user flow?', 8, 1, '2026-02-16 06:11:08'),
(49, 8, 'Digital Marketing Fundamentals', '## What is Digital Marketing?\r\n\r\nDigital marketing is the promotion of products or services through digital channels: search engines, social media, email, websites, and mobile apps.\r\n\r\n## The Digital Marketing Ecosystem\r\n\r\n**Owned Media**: Channels you control — your website, blog, email list, social profiles.\r\n**Earned Media**: Coverage you earn — press mentions, shares, reviews, word of mouth.\r\n**Paid Media**: Channels you pay for — Google Ads, Facebook Ads, sponsored content.\r\n\r\n## Key Digital Marketing Channels\r\n\r\n1. **SEO (Search Engine Optimisation)**: Rank higher in Google search results organically.\r\n2. **SEM (Search Engine Marketing)**: Pay-per-click ads on Google and Bing.\r\n3. **Social Media Marketing**: Organic and paid content on Facebook, Instagram, LinkedIn, TikTok.\r\n4. **Email Marketing**: Direct communication with subscribers.\r\n5. **Content Marketing**: Blog posts, videos, podcasts, infographics.\r\n6. **Affiliate Marketing**: Partners promote your product for a commission.\r\n7. **Influencer Marketing**: Partner with creators who have your target audience.\r\n\r\n## The Marketing Funnel\r\n\r\n- **Awareness**: Customer discovers your brand (SEO, social media, ads)\r\n- **Interest**: Customer learns more (blog, video, email)\r\n- **Consideration**: Customer evaluates options (case studies, reviews, demos)\r\n- **Intent**: Customer shows buying signals (adds to cart, requests quote)\r\n- **Purchase**: Customer buys\r\n- **Loyalty**: Customer returns and refers others\r\n\r\n## Key Metrics\r\n\r\n- **Impressions**: How many times your content was shown\r\n- **Reach**: How many unique people saw your content\r\n- **CTR (Click-Through Rate)**: Clicks / Impressions x 100\r\n- **Conversion Rate**: Conversions / Visitors x 100\r\n- **CPA (Cost Per Acquisition)**: Total spend / Number of customers\r\n- **ROI**: (Revenue - Cost) / Cost x 100\r\n- **LTV (Lifetime Value)**: Total revenue from a customer over their lifetime\r\n\r\n## Practical Task\r\n\r\nCreate a digital marketing plan for a fictional Nigerian SME. Define: target audience, 3 marketing channels, content calendar for 1 month, KPIs for each channel, and a monthly budget allocation.\r\n\r\n## Self-Check\r\n1. What is the difference between owned, earned, and paid media?\r\n2. Name the 6 stages of the marketing funnel.\r\n3. What does CTR stand for and how is it calculated?', 1, 1, '2026-02-16 06:11:08'),
(50, 8, 'SEO: Search Engine Optimisation', '## How Search Engines Work\r\n\r\nSearch engines crawl the web, index content, and rank pages based on relevance and authority. Understanding this process helps you optimise your content to rank higher.\r\n\r\n## On-Page SEO\r\n\r\nElements on your page that you control:\r\n\r\n**Title Tag**: The most important on-page SEO element.\r\n- 50-60 characters\r\n- Include primary keyword near the beginning\r\n- Each page must have a unique title\r\n- Example: Digital Marketing Course in Lagos | Mirror LMS\r\n\r\n**Meta Description**: Appears in search results below the title.\r\n- 150-160 characters\r\n- Include primary keyword\r\n- Write a compelling summary that encourages clicks\r\n\r\n**Heading Structure**:\r\n- One H1 per page (main topic)\r\n- H2 for main sections\r\n- H3 for subsections\r\n- Include keywords naturally\r\n\r\n**URL Structure**:\r\n- Short and descriptive: /digital-marketing-course\r\n- Use hyphens, not underscores\r\n- Include primary keyword\r\n\r\n**Image Optimisation**:\r\n- Descriptive file names: digital-marketing-course-lagos.jpg\r\n- Alt text: Descriptive, keyword-relevant\r\n- Compress images for fast loading\r\n\r\n## Technical SEO\r\n\r\n- **Page speed**: Use Google PageSpeed Insights. Target under 3 seconds.\r\n- **Mobile-friendly**: Use Google Mobile-Friendly Test.\r\n- **HTTPS**: Secure sites rank higher.\r\n- **Sitemap.xml**: Submit to Google Search Console.\r\n- **Robots.txt**: Tell search engines what to crawl.\r\n- **Structured data**: Schema markup for rich snippets.\r\n\r\n## Off-Page SEO\r\n\r\n**Backlinks**: Links from other websites to yours. Quality matters more than quantity.\r\n- Guest posting on relevant blogs\r\n- Creating shareable content (infographics, research)\r\n- Building relationships with journalists and bloggers\r\n- Local citations for local businesses\r\n\r\n## Keyword Research\r\n\r\nTools: Google Keyword Planner (free), Ahrefs, SEMrush, Ubersuggest.\r\n\r\nKeyword types:\r\n- Short-tail: digital marketing (high volume, high competition)\r\n- Long-tail: digital marketing course for beginners in Lagos (lower volume, lower competition, higher intent)\r\n\r\n## Practical Task\r\n\r\nConduct keyword research for a fictional digital marketing agency in Lagos. Find 10 target keywords using Google Keyword Planner. Optimise a sample blog post for your primary keyword. Check your work with a free SEO tool like Yoast or Rank Math.\r\n\r\n## Self-Check\r\n1. What is the ideal length for a title tag?\r\n2. What is the difference between on-page and off-page SEO?\r\n3. What is a long-tail keyword and why is it valuable?', 2, 1, '2026-02-16 06:11:08'),
(51, 8, 'Social Media Marketing', '## Social Media Strategy\r\n\r\nA social media strategy defines your goals, audience, content, and metrics. Without a strategy, you are just posting randomly.\r\n\r\n## Platform Selection\r\n\r\nChoose platforms where your audience spends time:\r\n\r\n| Platform | Best For | Primary Audience |\r\n|---|---|---|\r\n| Facebook | Community, ads, local business | 25-54 years |\r\n| Instagram | Visual brands, lifestyle, products | 18-34 years |\r\n| LinkedIn | B2B, professional services, recruitment | Professionals |\r\n| TikTok | Entertainment, Gen Z, viral content | Under 30 |\r\n| Twitter/X | News, tech, real-time conversation | 25-49 years |\r\n| YouTube | Long-form video, tutorials, reviews | All ages |\r\n| WhatsApp | Direct communication, customer service | All ages (Nigeria) |\r\n\r\n## Content Strategy\r\n\r\nThe 80/20 rule: 80% valuable content, 20% promotional content.\r\n\r\nContent types:\r\n- Educational: Tips, how-tos, tutorials\r\n- Entertaining: Memes, behind-the-scenes, stories\r\n- Inspirational: Success stories, quotes, transformations\r\n- Promotional: Product features, offers, announcements\r\n- User-generated: Customer photos, reviews, testimonials\r\n\r\n## Content Calendar\r\n\r\nPlan content in advance:\r\n- Posting frequency: 3-5x per week on Instagram, 1-2x on LinkedIn\r\n- Best times: Tuesday-Thursday, 9am-11am and 6pm-8pm (test for your audience)\r\n- Themes: Assign content themes to days (Monday Motivation, Wednesday Tips)\r\n\r\n## Social Media Advertising\r\n\r\nFacebook/Instagram Ads Manager:\r\n1. Campaign objective: Awareness, Traffic, Engagement, Leads, Sales\r\n2. Target audience: Demographics, interests, behaviours, lookalike audiences\r\n3. Ad format: Image, video, carousel, stories, reels\r\n4. Budget: Daily or lifetime budget\r\n5. Bidding: Automatic or manual\r\n\r\n## Analytics & Reporting\r\n\r\nKey metrics per platform:\r\n- Reach and impressions\r\n- Engagement rate: (Likes + Comments + Shares) / Reach x 100\r\n- Follower growth rate\r\n- Link clicks and website traffic\r\n- Conversion rate from social traffic\r\n\r\n## Practical Task\r\n\r\nCreate a 30-day social media content calendar for a fictional Nigerian food brand. Include: platform selection rationale, content themes, 20 post ideas with captions and hashtags, and a simple reporting template.\r\n\r\n## Self-Check\r\n1. What is the 80/20 rule in social media content?\r\n2. What is engagement rate and how is it calculated?\r\n3. Which platform would you prioritise for a B2B software company in Nigeria and why?', 3, 1, '2026-02-16 06:11:08'),
(52, 8, 'Email Marketing & Automation', '## Why Email Marketing?\r\n\r\nEmail marketing has the highest ROI of any digital marketing channel: an average of $36 for every $1 spent. Unlike social media, you own your email list.\r\n\r\n## Building an Email List\r\n\r\n- Lead magnets: Offer something valuable in exchange for an email (free guide, checklist, discount)\r\n- Opt-in forms: Website pop-ups, embedded forms, landing pages\r\n- Social media: Promote your lead magnet on social channels\r\n- Events: Collect emails at webinars and workshops\r\n\r\nNever buy email lists. It damages deliverability and violates GDPR/CAN-SPAM.\r\n\r\n## Email Types\r\n\r\n- **Welcome email**: Sent immediately after sign-up. Highest open rates.\r\n- **Newsletter**: Regular updates, content, and news.\r\n- **Promotional**: Sales, discounts, product launches.\r\n- **Transactional**: Order confirmations, receipts, password resets.\r\n- **Re-engagement**: Win back inactive subscribers.\r\n- **Drip campaigns**: Automated sequence of emails over time.\r\n\r\n## Email Copywriting\r\n\r\n**Subject line** (most important):\r\n- 40-50 characters (shows fully on mobile)\r\n- Create curiosity or urgency\r\n- Personalise with first name: John, your course is waiting\r\n- A/B test subject lines\r\n\r\n**Preview text**: The text shown after the subject line in the inbox. Treat it as a second subject line.\r\n\r\n**Body copy**:\r\n- One clear goal per email\r\n- Short paragraphs (2-3 sentences)\r\n- One primary CTA button\r\n- Mobile-optimised design\r\n\r\n## Email Automation\r\n\r\nAutomation sends the right email to the right person at the right time:\r\n\r\n- Welcome sequence: 3-5 emails over 2 weeks introducing your brand\r\n- Abandoned cart: Remind users of items left in cart\r\n- Post-purchase: Thank you, upsell, review request\r\n- Birthday: Personalised offer on subscriber birthday\r\n- Re-engagement: If no opens in 90 days, send a win-back email\r\n\r\n## Tools\r\n\r\n- Mailchimp: Free up to 500 contacts\r\n- Brevo (formerly Sendinblue): Free up to 300 emails/day\r\n- ConvertKit: Best for creators and course sellers\r\n- Klaviyo: Best for e-commerce\r\n\r\n## Practical Task\r\n\r\nCreate a 5-email welcome sequence for a fictional online course platform. Write subject lines, preview text, and body copy for each email. Set up the automation flow in Mailchimp (free account). A/B test two subject lines for email 1.\r\n\r\n## Self-Check\r\n1. What is a lead magnet?\r\n2. What is the average ROI of email marketing?\r\n3. What is an email drip campaign?', 4, 1, '2026-02-16 06:11:08'),
(53, 8, 'Content Marketing & Blogging', '## What is Content Marketing?\r\n\r\nContent marketing is the creation and distribution of valuable, relevant content to attract and retain a clearly defined audience, with the goal of driving profitable customer action.\r\n\r\nKey principle: Help first, sell second.\r\n\r\n## Content Marketing Strategy\r\n\r\n1. Define your audience: Who are you creating content for?\r\n2. Define your goals: Brand awareness, lead generation, sales, retention?\r\n3. Choose content types: Blog, video, podcast, infographic, case study?\r\n4. Keyword research: What questions is your audience asking?\r\n5. Content calendar: Plan topics, formats, and publishing dates\r\n6. Distribution: Where will you publish and promote?\r\n7. Measurement: How will you track success?\r\n\r\n## Blog Writing for SEO\r\n\r\n**Structure of a high-ranking blog post:**\r\n1. Title: Include primary keyword, 50-60 characters\r\n2. Introduction: Hook, problem statement, what the reader will learn\r\n3. Table of contents (for long posts)\r\n4. Body: H2 and H3 headings, short paragraphs, bullet points\r\n5. Images: Relevant, compressed, with alt text\r\n6. Internal links: Link to related posts on your site\r\n7. External links: Link to authoritative sources\r\n8. CTA: What should the reader do next?\r\n9. Meta description: 150-160 characters\r\n\r\n## Content Formats\r\n\r\n- **How-to guides**: Step-by-step instructions (high search intent)\r\n- **Listicles**: 10 Best X for Y (easy to scan, highly shareable)\r\n- **Case studies**: Real results with data (builds trust)\r\n- **Comparison posts**: X vs Y (captures decision-stage searchers)\r\n- **Ultimate guides**: Comprehensive resource on a topic (earns backlinks)\r\n- **Infographics**: Visual data (highly shareable)\r\n\r\n## Content Distribution\r\n\r\nCreate once, distribute everywhere:\r\n- Publish on your blog\r\n- Share on social media (different formats per platform)\r\n- Send to email list\r\n- Repurpose: Turn blog post into video, podcast, infographic\r\n- Syndicate: Republish on Medium, LinkedIn Articles\r\n\r\n## Practical Task\r\n\r\nWrite a 1,000-word SEO-optimised blog post for a fictional digital marketing agency. Topic: 5 Digital Marketing Strategies for Nigerian SMEs in 2025. Include: keyword-optimised title, meta description, proper heading structure, internal links, and a CTA.\r\n\r\n## Self-Check\r\n1. What is the key principle of content marketing?\r\n2. What are the 7 steps of a content marketing strategy?\r\n3. What is content repurposing and why is it valuable?', 5, 1, '2026-02-16 06:11:08'),
(54, 8, 'Google Ads & Paid Advertising', '## What is Pay-Per-Click (PPC) Advertising?\r\n\r\nPPC advertising means you pay each time someone clicks your ad. Google Ads is the largest PPC platform, showing ads in Google search results and across the web.\r\n\r\n## Google Ads Campaign Types\r\n\r\n- **Search campaigns**: Text ads shown in Google search results\r\n- **Display campaigns**: Image/banner ads shown on websites in the Google Display Network\r\n- **Shopping campaigns**: Product listings with images and prices\r\n- **Video campaigns**: Ads on YouTube\r\n- **Performance Max**: AI-driven campaigns across all Google channels\r\n\r\n## Search Campaign Structure\r\n\r\nCampaign > Ad Groups > Keywords > Ads\r\n\r\n**Campaign**: Set budget, location, language, bidding strategy\r\n**Ad Group**: Group of related keywords (e.g. digital marketing courses)\r\n**Keywords**: The search terms that trigger your ads\r\n**Ads**: The text shown to users\r\n\r\n## Keyword Match Types\r\n\r\n- **Broad match**: digital marketing course (shows for related searches)\r\n- **Phrase match**: \"digital marketing course\" (must contain this phrase)\r\n- **Exact match**: [digital marketing course] (must match exactly)\r\n- **Negative keywords**: -free (exclude searches containing this word)\r\n\r\n## Writing Effective Ad Copy\r\n\r\nGoogle Search Ad structure:\r\n- Headline 1 (30 chars): Include primary keyword\r\n- Headline 2 (30 chars): Key benefit or USP\r\n- Headline 3 (30 chars): Call to action\r\n- Description 1 (90 chars): Expand on the benefit\r\n- Description 2 (90 chars): Social proof or urgency\r\n- Display URL: yoursite.com/digital-marketing\r\n\r\n## Quality Score\r\n\r\nGoogle rates your ads 1-10 based on:\r\n- Expected CTR\r\n- Ad relevance to the keyword\r\n- Landing page experience\r\n\r\nHigher Quality Score = lower cost per click and better ad position.\r\n\r\n## Key Metrics\r\n\r\n- **Impressions**: How many times your ad was shown\r\n- **Clicks**: How many times your ad was clicked\r\n- **CTR**: Clicks / Impressions x 100\r\n- **CPC (Cost Per Click)**: Total spend / Clicks\r\n- **Conversion rate**: Conversions / Clicks x 100\r\n- **ROAS (Return on Ad Spend)**: Revenue / Ad spend\r\n\r\n## Practical Task\r\n\r\nCreate a Google Ads campaign plan for a fictional Lagos-based digital marketing course. Define: campaign type, target keywords (10 keywords with match types), 3 negative keywords, 2 ad copy variations, target CPA, and monthly budget.\r\n\r\n## Self-Check\r\n1. What is the difference between broad match and exact match keywords?\r\n2. What is Quality Score and what affects it?\r\n3. How is ROAS calculated?', 6, 1, '2026-02-16 06:11:08'),
(55, 8, 'Analytics & Data-Driven Marketing', '## Why Analytics Matters\r\n\r\nData-driven marketing means making decisions based on evidence, not intuition. Analytics tells you what is working, what is not, and where to invest your budget.\r\n\r\n## Google Analytics 4 (GA4)\r\n\r\nGA4 is Google\'s current analytics platform. Key concepts:\r\n\r\n**Events**: Every user interaction is an event (page_view, click, scroll, purchase).\r\n**Parameters**: Additional data attached to events (page_title, item_name, value).\r\n**Conversions**: Events you mark as important business goals.\r\n**Dimensions**: Attributes of your data (country, device, source).\r\n**Metrics**: Quantitative measurements (sessions, users, revenue).\r\n\r\n## Key GA4 Reports\r\n\r\n- **Acquisition**: Where your traffic comes from (organic, paid, social, email, direct)\r\n- **Engagement**: How users interact with your site (pages viewed, time on site, scroll depth)\r\n- **Monetisation**: Revenue, transactions, average order value\r\n- **Retention**: How many users return\r\n- **Demographics**: Age, gender, location, interests\r\n\r\n## UTM Parameters\r\n\r\nUTM parameters track the source of your traffic in GA4:\r\n\r\nhttps://yoursite.com/course?utm_source=facebook&utm_medium=social&utm_campaign=course_launch\r\n\r\n- utm_source: Where the traffic comes from (facebook, google, newsletter)\r\n- utm_medium: The marketing channel (social, cpc, email)\r\n- utm_campaign: The specific campaign name\r\n- utm_content: The specific ad or link (for A/B testing)\r\n\r\nUse Google Campaign URL Builder to create UTM links.\r\n\r\n## Conversion Rate Optimisation (CRO)\r\n\r\nCRO is the process of increasing the percentage of visitors who take a desired action.\r\n\r\nCRO process:\r\n1. Analyse: Find pages with high traffic but low conversion\r\n2. Hypothesise: Why is the conversion rate low?\r\n3. Test: A/B test your hypothesis\r\n4. Implement: Roll out the winning variation\r\n5. Repeat\r\n\r\nTools: Google Optimize (free), VWO, Optimizely, Hotjar.\r\n\r\n## Reporting\r\n\r\nA good marketing report includes:\r\n- Executive summary: Key wins and challenges\r\n- KPI dashboard: Traffic, leads, conversions, revenue\r\n- Channel performance: Which channels drove the most value\r\n- Campaign results: Specific campaign metrics\r\n- Recommendations: What to do next month\r\n\r\n## Practical Task\r\n\r\nSet up Google Analytics 4 on a test website. Create a custom report showing traffic by source/medium. Set up 3 conversion events. Add UTM parameters to 5 sample marketing links. Write a 1-page monthly marketing report template.\r\n\r\n## Self-Check\r\n1. What is the difference between a dimension and a metric in GA4?\r\n2. What are UTM parameters and why are they important?\r\n3. What is Conversion Rate Optimisation?', 7, 1, '2026-02-16 06:11:08'),
(56, 8, 'Capstone: Full Digital Marketing Campaign', '## Final Project Brief\r\n\r\nYou will plan and execute a complete digital marketing campaign for a fictional Nigerian business.\r\n\r\n## Business Scenario\r\n\r\nYour client is TechLearn Nigeria: an online learning platform offering tech courses to Nigerian professionals aged 22-35. They have a monthly marketing budget of 500,000 NGN and want to acquire 200 new students in 3 months.\r\n\r\n## Deliverables\r\n\r\n### 1. Marketing Strategy Document\r\n- Target audience analysis (2 personas)\r\n- Competitive analysis (3 competitors)\r\n- SWOT analysis\r\n- Channel selection with rationale\r\n- Budget allocation across channels\r\n- KPIs and targets for each channel\r\n\r\n### 2. SEO Plan\r\n- 20 target keywords with search volume and difficulty\r\n- On-page optimisation checklist for 5 key pages\r\n- 3-month content calendar (12 blog post topics)\r\n- Link building strategy\r\n\r\n### 3. Social Media Plan\r\n- Platform selection (2 platforms) with rationale\r\n- 30-day content calendar with post copy and visuals\r\n- Paid social campaign: audience targeting, ad copy, budget\r\n\r\n### 4. Email Marketing\r\n- Lead magnet concept\r\n- 5-email welcome sequence (full copy)\r\n- Monthly newsletter template\r\n\r\n### 5. Paid Advertising\r\n- Google Ads campaign structure\r\n- 10 keywords with match types\r\n- 3 ad copy variations\r\n- Landing page brief\r\n\r\n### 6. Analytics & Reporting\r\n- KPI dashboard template\r\n- Monthly reporting template\r\n- Attribution model recommendation\r\n\r\n## Evaluation Criteria\r\n- Strategic thinking and audience insight (25%)\r\n- Channel strategy and budget allocation (20%)\r\n- Content quality and creativity (25%)\r\n- Technical accuracy (SEO, ads, analytics) (20%)\r\n- Presentation and professionalism (10%)\r\n\r\n## Self-Check\r\n1. Does your budget allocation reflect the channels most likely to reach your target audience?\r\n2. Are your KPIs specific, measurable, and time-bound?\r\n3. How will you attribute conversions across multiple channels?', 8, 1, '2026-02-16 06:11:08'),
(57, 9, 'Introduction to Data Analysis', '## What is Data Analysis?\r\n\r\nData analysis is the process of inspecting, cleaning, transforming, and modelling data to discover useful information, draw conclusions, and support decision-making.\r\n\r\n## The Data Analysis Process\r\n\r\n1. **Ask**: Define the business question. What problem are we solving?\r\n2. **Collect**: Gather data from relevant sources.\r\n3. **Clean**: Remove errors, duplicates, and inconsistencies.\r\n4. **Analyse**: Apply statistical and analytical techniques.\r\n5. **Visualise**: Create charts and dashboards to communicate findings.\r\n6. **Act**: Make data-driven recommendations.\r\n\r\n## Types of Data Analysis\r\n\r\n- **Descriptive**: What happened? (Sales last month were 2.3M NGN)\r\n- **Diagnostic**: Why did it happen? (Sales dropped because of a competitor promotion)\r\n- **Predictive**: What will happen? (Sales will grow 15% next quarter)\r\n- **Prescriptive**: What should we do? (Increase ad spend in the North-West region)\r\n\r\n## Data Types\r\n\r\n**Quantitative (numerical):**\r\n- Discrete: Countable values (number of students, number of orders)\r\n- Continuous: Any value in a range (height, temperature, revenue)\r\n\r\n**Qualitative (categorical):**\r\n- Nominal: No order (gender, country, product category)\r\n- Ordinal: Has order (rating 1-5, education level)\r\n\r\n## Tools for Data Analysis\r\n\r\n- **Microsoft Excel / Google Sheets**: Accessible, widely used, good for small datasets\r\n- **Python (pandas, numpy)**: Powerful, free, handles large datasets\r\n- **R**: Statistical computing, popular in academia\r\n- **SQL**: Query databases directly\r\n- **Power BI / Tableau**: Data visualisation and dashboards\r\n- **Google Looker Studio**: Free dashboards connected to Google data\r\n\r\n## Practical Task\r\n\r\nDownload a free dataset from Kaggle (e.g. Nigerian e-commerce sales data). Open it in Excel or Google Sheets. Answer 5 business questions using formulas and pivot tables. Create 3 charts to visualise your findings.\r\n\r\n## Self-Check\r\n1. What are the 6 steps of the data analysis process?\r\n2. What is the difference between descriptive and predictive analysis?\r\n3. What is the difference between quantitative and qualitative data?', 1, 1, '2026-02-16 06:11:08'),
(58, 9, 'Excel & Google Sheets for Analysis', '## Excel/Sheets as an Analysis Tool\r\n\r\nSpreadsheets are the most widely used data analysis tool in business. Mastering them is essential for any data analyst.\r\n\r\n## Essential Functions\r\n\r\n```\r\n=SUM(A2:A100)          -- Add a range\r\n=AVERAGE(B2:B100)      -- Calculate mean\r\n=COUNT(C2:C100)        -- Count numbers\r\n=COUNTA(D2:D100)       -- Count non-empty cells\r\n=MAX(E2:E100)          -- Largest value\r\n=MIN(F2:F100)          -- Smallest value\r\n=IF(A2>100,\"High\",\"Low\")  -- Conditional logic\r\n=VLOOKUP(A2,Sheet2!A:B,2,FALSE)  -- Look up value\r\n=COUNTIF(A:A,\"Lagos\")  -- Count matching cells\r\n=SUMIF(A:A,\"Lagos\",B:B) -- Sum where condition is met\r\n=AVERAGEIF(A:A,\"Lagos\",B:B) -- Average where condition is met\r\n```\r\n\r\n## Pivot Tables\r\n\r\nPivot tables summarise large datasets quickly:\r\n1. Select your data range\r\n2. Insert > Pivot Table\r\n3. Drag fields to Rows, Columns, Values, Filters\r\n4. Change value aggregation: Sum, Count, Average, Max\r\n\r\nExample: Sales by region and product category.\r\n\r\n## Data Cleaning in Spreadsheets\r\n\r\n- Remove duplicates: Data > Remove Duplicates\r\n- Trim whitespace: =TRIM(A2)\r\n- Fix case: =PROPER(A2), =UPPER(A2), =LOWER(A2)\r\n- Split text: Data > Split text to columns\r\n- Find and replace: Ctrl+H\r\n- Filter and sort: Data > Filter\r\n\r\n## Charts & Visualisation\r\n\r\nChoose the right chart type:\r\n- **Bar/Column chart**: Compare categories\r\n- **Line chart**: Show trends over time\r\n- **Pie chart**: Show proportions (use sparingly, max 5 slices)\r\n- **Scatter plot**: Show correlation between two variables\r\n- **Histogram**: Show distribution of a single variable\r\n\r\n## Practical Task\r\n\r\nUsing a sales dataset (create or download one), build a dashboard in Google Sheets with: total revenue, revenue by region (bar chart), monthly trend (line chart), top 5 products (table), and a slicer to filter by date range.\r\n\r\n## Self-Check\r\n1. What is the difference between VLOOKUP and INDEX/MATCH?\r\n2. What is a pivot table and when would you use one?\r\n3. Which chart type is best for showing a trend over time?', 2, 1, '2026-02-16 06:11:08'),
(59, 9, 'SQL for Data Analysis', '## SQL in Data Analysis\r\n\r\nSQL (Structured Query Language) is the standard language for querying databases. Most business data lives in relational databases, making SQL an essential skill for data analysts.\r\n\r\n## Core SQL for Analysis\r\n\r\n```sql\r\n-- Basic SELECT\r\nSELECT customer_name, order_date, total_amount\r\nFROM orders\r\nWHERE total_amount > 50000\r\nORDER BY total_amount DESC\r\nLIMIT 10;\r\n\r\n-- Aggregate functions\r\nSELECT\r\n    region,\r\n    COUNT(*) AS order_count,\r\n    SUM(total_amount) AS total_revenue,\r\n    AVG(total_amount) AS avg_order_value,\r\n    MAX(total_amount) AS largest_order\r\nFROM orders\r\nGROUP BY region\r\nHAVING COUNT(*) > 100\r\nORDER BY total_revenue DESC;\r\n\r\n-- JOIN for combining tables\r\nSELECT\r\n    c.name AS customer,\r\n    COUNT(o.id) AS total_orders,\r\n    SUM(o.total_amount) AS lifetime_value\r\nFROM customers c\r\nLEFT JOIN orders o ON o.customer_id = c.id\r\nGROUP BY c.id, c.name\r\nORDER BY lifetime_value DESC;\r\n\r\n-- Date functions\r\nSELECT\r\n    DATE_FORMAT(order_date, \'%Y-%m\') AS month,\r\n    COUNT(*) AS orders,\r\n    SUM(total_amount) AS revenue\r\nFROM orders\r\nWHERE order_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)\r\nGROUP BY month\r\nORDER BY month;\r\n\r\n-- Subquery\r\nSELECT * FROM customers\r\nWHERE id IN (\r\n    SELECT customer_id FROM orders\r\n    WHERE total_amount > 100000\r\n);\r\n```\r\n\r\n## Window Functions\r\n\r\n```sql\r\nSELECT\r\n    customer_name,\r\n    total_amount,\r\n    RANK() OVER (ORDER BY total_amount DESC) AS revenue_rank,\r\n    SUM(total_amount) OVER () AS grand_total,\r\n    total_amount / SUM(total_amount) OVER () * 100 AS pct_of_total\r\nFROM orders;\r\n```\r\n\r\n## Practical Task\r\n\r\nUsing the LMS database schema, write SQL queries to answer: (1) How many students enrolled per month? (2) Which course has the highest revenue? (3) What is the average payment per student? (4) Which students have an outstanding balance? (5) What is the monthly revenue trend for the last 6 months?\r\n\r\n## Self-Check\r\n1. What is the difference between WHERE and HAVING?\r\n2. What does a LEFT JOIN return that an INNER JOIN does not?\r\n3. What is a window function?', 3, 1, '2026-02-16 06:11:08'),
(60, 9, 'Python for Data Analysis', '## Why Python for Data Analysis?\r\n\r\nPython is the most popular language for data analysis and data science. Its libraries (pandas, numpy, matplotlib) make it easy to work with large datasets, perform complex analysis, and create visualisations.\r\n\r\n## Setting Up\r\n\r\n```bash\r\n# Install Anaconda (includes Python + all data science libraries)\r\n# Or install individually:\r\npip install pandas numpy matplotlib seaborn jupyter\r\n\r\n# Launch Jupyter Notebook\r\njupyter notebook\r\n```\r\n\r\n## pandas Fundamentals\r\n\r\n```python\r\nimport pandas as pd\r\nimport numpy as np\r\n\r\n# Load data\r\ndf = pd.read_csv(\'sales_data.csv\')\r\ndf = pd.read_excel(\'sales_data.xlsx\')\r\n\r\n# Explore data\r\ndf.head(10)          # First 10 rows\r\ndf.info()            # Column types and null counts\r\ndf.describe()        # Statistical summary\r\ndf.shape             # (rows, columns)\r\ndf.columns           # Column names\r\ndf.isnull().sum()    # Count missing values per column\r\n\r\n# Select data\r\ndf[\'revenue\']                    # Single column\r\ndf[[\'name\', \'revenue\', \'date\']]  # Multiple columns\r\ndf[df[\'revenue\'] > 50000]        # Filter rows\r\ndf.loc[0:5, \'name\':\'revenue\']    # Slice by label\r\n\r\n# Aggregate\r\ndf.groupby(\'region\')[\'revenue\'].sum()\r\ndf.groupby(\'region\').agg({\'revenue\': [\'sum\', \'mean\', \'count\']})\r\n\r\n# Clean data\r\ndf.dropna()                          # Remove rows with any null\r\ndf.fillna(0)                         # Replace nulls with 0\r\ndf.drop_duplicates()                 # Remove duplicate rows\r\ndf[\'date\'] = pd.to_datetime(df[\'date\'])  # Convert to datetime\r\ndf[\'revenue\'] = df[\'revenue\'].str.replace(\',\',\'\').astype(float)\r\n```\r\n\r\n## Data Visualisation with matplotlib\r\n\r\n```python\r\nimport matplotlib.pyplot as plt\r\n\r\n# Line chart\r\ndf.groupby(\'month\')[\'revenue\'].sum().plot(kind=\'line\')\r\nplt.title(\'Monthly Revenue\')\r\nplt.xlabel(\'Month\')\r\nplt.ylabel(\'Revenue (NGN)\')\r\nplt.tight_layout()\r\nplt.savefig(\'revenue_trend.png\')\r\nplt.show()\r\n\r\n# Bar chart\r\ndf.groupby(\'region\')[\'revenue\'].sum().plot(kind=\'bar\', color=\'steelblue\')\r\nplt.xticks(rotation=45)\r\nplt.show()\r\n```\r\n\r\n## Practical Task\r\n\r\nDownload a Nigerian business dataset from Kaggle. Load it into a Jupyter Notebook using pandas. Clean the data (handle nulls, fix data types). Answer 5 business questions with code. Create 4 visualisations. Export your findings as a PDF report.\r\n\r\n## Self-Check\r\n1. What is the difference between df.loc and df.iloc?\r\n2. How do you handle missing values in pandas?\r\n3. What is the difference between groupby and pivot_table?', 4, 1, '2026-02-16 06:11:08'),
(61, 9, 'Data Visualisation & Dashboards', '## Principles of Data Visualisation\r\n\r\nGood data visualisation communicates insights clearly and honestly. Bad visualisation misleads or confuses.\r\n\r\n## Chart Selection Guide\r\n\r\n| Goal | Chart Type |\r\n|---|---|\r\n| Compare categories | Bar chart, Column chart |\r\n| Show trend over time | Line chart, Area chart |\r\n| Show proportions | Pie chart (max 5 slices), Treemap |\r\n| Show distribution | Histogram, Box plot |\r\n| Show correlation | Scatter plot, Bubble chart |\r\n| Show geographic data | Map chart, Choropleth |\r\n| Show part-to-whole | Stacked bar, Waterfall |\r\n\r\n## Visualisation Best Practices\r\n\r\n1. Start the y-axis at zero (unless showing change)\r\n2. Use colour purposefully (not decoratively)\r\n3. Label axes clearly with units\r\n4. Use a descriptive title that states the insight\r\n5. Remove chart junk (unnecessary gridlines, borders, 3D effects)\r\n6. Highlight the key data point\r\n7. Keep it simple — one insight per chart\r\n\r\n## Power BI Fundamentals\r\n\r\nPower BI is Microsoft\'s business intelligence tool:\r\n1. Get Data: Connect to Excel, SQL, web, or 100+ data sources\r\n2. Transform: Clean and shape data in Power Query\r\n3. Model: Define relationships between tables\r\n4. Visualise: Drag fields onto the canvas to create charts\r\n5. Publish: Share dashboards with your organisation\r\n\r\n## DAX (Data Analysis Expressions)\r\n\r\n```dax\r\n-- Total Revenue\r\nTotal Revenue = SUM(Orders[Amount])\r\n\r\n-- Revenue YTD\r\nRevenue YTD = TOTALYTD(SUM(Orders[Amount]), Dates[Date])\r\n\r\n-- Month-over-Month Growth\r\nMoM Growth = \r\n    DIVIDE(\r\n        [Total Revenue] - CALCULATE([Total Revenue], PREVIOUSMONTH(Dates[Date])),\r\n        CALCULATE([Total Revenue], PREVIOUSMONTH(Dates[Date]))\r\n    )\r\n```\r\n\r\n## Google Looker Studio (Free)\r\n\r\nLooker Studio connects to Google Analytics, Google Sheets, BigQuery, and more. Create interactive dashboards and share with a link.\r\n\r\n## Practical Task\r\n\r\nBuild a sales dashboard in Power BI or Google Looker Studio. Include: total revenue KPI card, revenue by region (bar chart), monthly trend (line chart), top 10 customers (table), and a date range filter. The dashboard should update automatically when data changes.\r\n\r\n## Self-Check\r\n1. Which chart type would you use to show the distribution of student ages?\r\n2. Why should you start the y-axis at zero?\r\n3. What is DAX and what is it used for?', 5, 1, '2026-02-16 06:11:08'),
(62, 9, 'Statistics for Data Analysis', '## Why Statistics?\r\n\r\nStatistics provides the mathematical foundation for data analysis. Without it, you cannot distinguish meaningful patterns from random noise.\r\n\r\n## Descriptive Statistics\r\n\r\n**Measures of Central Tendency:**\r\n- Mean: Sum of all values / count. Sensitive to outliers.\r\n- Median: Middle value when sorted. Robust to outliers.\r\n- Mode: Most frequent value.\r\n\r\n**Measures of Spread:**\r\n- Range: Max - Min\r\n- Variance: Average squared deviation from the mean\r\n- Standard Deviation: Square root of variance. Same units as the data.\r\n- IQR (Interquartile Range): Q3 - Q1. Robust to outliers.\r\n\r\n**Example:**\r\nStudent scores: 45, 60, 65, 70, 72, 75, 78, 80, 85, 95\r\nMean = 72.5, Median = 73.5, Mode = none, Std Dev = 13.8\r\n\r\n## Probability Distributions\r\n\r\n**Normal Distribution (Bell Curve):**\r\n- Symmetric around the mean\r\n- 68% of data within 1 standard deviation\r\n- 95% within 2 standard deviations\r\n- 99.7% within 3 standard deviations\r\n\r\n**Skewness:**\r\n- Right-skewed (positive): Long tail to the right. Mean > Median. (Income distribution)\r\n- Left-skewed (negative): Long tail to the left. Mean < Median.\r\n\r\n## Correlation\r\n\r\nCorrelation measures the strength and direction of the relationship between two variables.\r\n\r\n- Correlation coefficient (r): -1 to +1\r\n- r = +1: Perfect positive correlation\r\n- r = -1: Perfect negative correlation\r\n- r = 0: No linear correlation\r\n\r\nImportant: Correlation does not imply causation.\r\n\r\n## Hypothesis Testing\r\n\r\n1. State the null hypothesis (H0): There is no effect.\r\n2. State the alternative hypothesis (H1): There is an effect.\r\n3. Choose significance level (alpha = 0.05 is standard).\r\n4. Calculate the test statistic and p-value.\r\n5. If p-value < alpha, reject H0.\r\n\r\n## Practical Task\r\n\r\nUsing a dataset of student exam scores, calculate: mean, median, mode, standard deviation, and IQR. Create a histogram to visualise the distribution. Test whether students who attended more than 80% of classes scored significantly higher than those who attended less.\r\n\r\n## Self-Check\r\n1. What is the difference between mean and median? When is each more appropriate?\r\n2. What does a correlation coefficient of -0.8 mean?\r\n3. What is a p-value?', 6, 1, '2026-02-16 06:11:08'),
(63, 9, 'Business Intelligence & Reporting', '## What is Business Intelligence?\r\n\r\nBusiness Intelligence (BI) is the process of collecting, analysing, and presenting business data to support better decision-making. BI turns raw data into actionable insights.\r\n\r\n## BI vs Data Science\r\n\r\n**Business Intelligence**: Focuses on historical data. What happened? Why? Descriptive and diagnostic analysis. Tools: Power BI, Tableau, Looker.\r\n\r\n**Data Science**: Focuses on future predictions and complex modelling. What will happen? What should we do? Predictive and prescriptive analysis. Tools: Python, R, machine learning.\r\n\r\n## KPIs (Key Performance Indicators)\r\n\r\nKPIs are measurable values that demonstrate how effectively a company is achieving its objectives.\r\n\r\n**Good KPIs are:**\r\n- Specific: Clearly defined\r\n- Measurable: Can be quantified\r\n- Achievable: Realistic targets\r\n- Relevant: Aligned with business goals\r\n- Time-bound: Have a deadline\r\n\r\n**Examples by department:**\r\n- Sales: Monthly revenue, conversion rate, average deal size\r\n- Marketing: CAC, ROAS, organic traffic growth\r\n- Operations: Order fulfilment time, defect rate, customer satisfaction\r\n- HR: Employee turnover rate, time to hire, training completion rate\r\n\r\n## Dashboard Design Principles\r\n\r\n1. Know your audience: What decisions will this dashboard support?\r\n2. Prioritise: Show the most important KPIs first (top-left)\r\n3. Context: Show targets, benchmarks, and period-over-period comparisons\r\n4. Interactivity: Allow filtering by date, region, product\r\n5. Simplicity: Remove everything that does not add value\r\n6. Consistency: Use the same colours, fonts, and chart styles throughout\r\n\r\n## Storytelling with Data\r\n\r\nData storytelling combines data, visuals, and narrative to communicate insights persuasively:\r\n1. Context: What is the situation?\r\n2. Conflict: What is the problem or opportunity?\r\n3. Resolution: What does the data show we should do?\r\n\r\n## Practical Task\r\n\r\nBuild an executive dashboard for a fictional Nigerian retail company. Include: 5 KPI cards (revenue, orders, customers, AOV, return rate), revenue trend (12 months), top 5 products, regional performance map, and a month-over-month comparison table. Present it as a 5-minute data story.\r\n\r\n## Self-Check\r\n1. What is the difference between BI and data science?\r\n2. What makes a good KPI? Name the 5 criteria.\r\n3. What are the 3 components of data storytelling?', 7, 1, '2026-02-16 06:11:08'),
(64, 9, 'Capstone: Data Analysis Project', '## Final Project Brief\r\n\r\nYou will conduct a complete data analysis project from raw data to actionable business recommendations.\r\n\r\n## Project Scenario\r\n\r\nYou are a data analyst at a Nigerian e-commerce company. The CEO wants to understand why revenue declined 18% in Q3 compared to Q2, and what actions to take to recover in Q4.\r\n\r\n## Dataset\r\n\r\nYou will be provided with (or create) a dataset containing:\r\n- 12 months of order data (order_id, date, customer_id, product, category, region, amount, status)\r\n- Customer data (customer_id, name, city, state, registration_date, segment)\r\n- Product data (product_id, name, category, cost_price, selling_price)\r\n\r\n## Analysis Requirements\r\n\r\n### 1. Data Cleaning\r\n- Identify and handle missing values\r\n- Remove duplicates\r\n- Fix data type issues\r\n- Document all cleaning steps\r\n\r\n### 2. Exploratory Data Analysis\r\n- Revenue trend by month\r\n- Revenue by region, category, and customer segment\r\n- Top 10 products by revenue and by volume\r\n- Customer acquisition and retention rates\r\n- Average order value trend\r\n\r\n### 3. Root Cause Analysis\r\n- Identify which regions, categories, or segments drove the Q3 decline\r\n- Analyse customer behaviour changes (new vs returning customers)\r\n- Identify any product or category-level issues\r\n\r\n### 4. Recommendations\r\n- 3-5 specific, data-backed recommendations\r\n- Projected impact of each recommendation\r\n- Priority ranking\r\n\r\n## Deliverables\r\n- Jupyter Notebook or Excel workbook with all analysis\r\n- Power BI or Looker Studio dashboard\r\n- 10-slide presentation with data story\r\n- 1-page executive summary\r\n\r\n## Evaluation Criteria\r\n- Data cleaning thoroughness (15%)\r\n- Analysis depth and accuracy (30%)\r\n- Visualisation quality (20%)\r\n- Insight quality and business relevance (25%)\r\n- Presentation clarity (10%)\r\n\r\n## Self-Check\r\n1. Have you validated your findings against the raw data?\r\n2. Are your recommendations specific and actionable?\r\n3. Could a non-technical executive understand your presentation?', 8, 1, '2026-02-16 06:11:08');
INSERT INTO `lms_lessons` (`id`, `course_id`, `title`, `content`, `sort_order`, `is_published`, `created_at`) VALUES
(65, 10, 'Introduction to Cybersecurity', '## What is Cybersecurity?\r\n\r\nCybersecurity is the practice of protecting systems, networks, and programs from digital attacks. These attacks aim to access, change, or destroy sensitive information, extort money, or disrupt normal business operations.\r\n\r\n## The CIA Triad\r\n\r\nThe three core principles of information security:\r\n\r\n**Confidentiality**: Ensuring information is accessible only to those authorised to access it.\r\n- Encryption, access controls, authentication\r\n\r\n**Integrity**: Ensuring information is accurate and has not been tampered with.\r\n- Hashing, digital signatures, checksums\r\n\r\n**Availability**: Ensuring systems and data are available when needed.\r\n- Redundancy, backups, DDoS protection\r\n\r\n## Types of Cyber Threats\r\n\r\n- **Malware**: Malicious software (viruses, worms, ransomware, spyware)\r\n- **Phishing**: Deceptive emails/messages to steal credentials or install malware\r\n- **Man-in-the-Middle (MitM)**: Intercepting communication between two parties\r\n- **SQL Injection**: Inserting malicious SQL code into a database query\r\n- **Cross-Site Scripting (XSS)**: Injecting malicious scripts into web pages\r\n- **DDoS (Distributed Denial of Service)**: Overwhelming a server with traffic\r\n- **Social Engineering**: Manipulating people into revealing confidential information\r\n- **Insider Threats**: Malicious or negligent actions by employees\r\n\r\n## The Cybersecurity Landscape in Nigeria\r\n\r\nNigeria loses an estimated $500 million annually to cybercrime. Common threats:\r\n- Business Email Compromise (BEC)\r\n- Online banking fraud\r\n- SIM swap attacks\r\n- Ransomware targeting businesses\r\n\r\n## Career Paths in Cybersecurity\r\n\r\n- Security Analyst: Monitor and respond to security incidents\r\n- Penetration Tester (Ethical Hacker): Find vulnerabilities before attackers do\r\n- Security Engineer: Build and maintain security systems\r\n- Incident Responder: Investigate and contain security breaches\r\n- CISO (Chief Information Security Officer): Lead an organisation\'s security strategy\r\n\r\n## Practical Task\r\n\r\nConduct a personal security audit. Check: Are your passwords unique and strong? Do you use 2FA on all important accounts? Is your software up to date? Have any of your accounts been breached (check haveibeenpwned.com)? Write a 1-page personal security improvement plan.\r\n\r\n## Self-Check\r\n1. What does the CIA triad stand for?\r\n2. What is the difference between a virus and ransomware?\r\n3. What is social engineering?', 1, 1, '2026-02-16 06:11:08'),
(66, 10, 'Network Security Fundamentals', '## How Networks Work\r\n\r\nUnderstanding networks is essential for cybersecurity. Most attacks travel over networks.\r\n\r\n## Network Basics\r\n\r\n**IP Address**: A unique identifier for a device on a network.\r\n- IPv4: 192.168.1.1 (32-bit, ~4.3 billion addresses)\r\n- IPv6: 2001:0db8:85a3::8a2e:0370:7334 (128-bit, virtually unlimited)\r\n\r\n**Ports**: Virtual endpoints for network communication.\r\n- Port 80: HTTP (unencrypted web)\r\n- Port 443: HTTPS (encrypted web)\r\n- Port 22: SSH (secure remote access)\r\n- Port 3306: MySQL database\r\n- Port 25: SMTP (email sending)\r\n\r\n**Protocols**: Rules for communication.\r\n- TCP: Reliable, connection-oriented (web, email, file transfer)\r\n- UDP: Fast, connectionless (video streaming, DNS, gaming)\r\n- HTTP/HTTPS: Web communication\r\n- DNS: Translates domain names to IP addresses\r\n\r\n## Network Security Controls\r\n\r\n**Firewall**: Monitors and controls incoming/outgoing network traffic based on rules.\r\n- Packet filtering: Inspect individual packets\r\n- Stateful inspection: Track connection state\r\n- Application layer: Inspect application-level traffic\r\n\r\n**VPN (Virtual Private Network)**: Encrypts all traffic between your device and a VPN server. Hides your IP address and protects data on public Wi-Fi.\r\n\r\n**IDS/IPS (Intrusion Detection/Prevention System)**: Monitors network traffic for suspicious activity.\r\n\r\n**Network Segmentation**: Divide the network into zones. If one zone is compromised, attackers cannot easily move to others.\r\n\r\n## Common Network Attacks\r\n\r\n- **Port scanning**: Discovering open ports on a target (Nmap tool)\r\n- **ARP poisoning**: Redirecting network traffic through the attacker\'s machine\r\n- **DNS spoofing**: Redirecting domain name lookups to malicious IP addresses\r\n- **Packet sniffing**: Capturing unencrypted network traffic (Wireshark tool)\r\n\r\n## Practical Task\r\n\r\nInstall Wireshark (free, wireshark.org). Capture network traffic on your local network for 5 minutes. Identify: what protocols are being used, which IP addresses are communicating, and any unencrypted HTTP traffic. Write a brief report of your findings.\r\n\r\n## Self-Check\r\n1. What is the difference between TCP and UDP?\r\n2. What does a firewall do?\r\n3. What is a VPN and when should you use one?', 2, 1, '2026-02-16 06:11:08'),
(67, 10, 'Web Application Security', '## OWASP Top 10\r\n\r\nThe OWASP (Open Web Application Security Project) Top 10 is the standard reference for web application security risks.\r\n\r\n## Top 5 Most Critical Vulnerabilities\r\n\r\n**1. Broken Access Control**\r\nUsers can access resources they should not be able to.\r\nExample: Changing ?user_id=123 to ?user_id=124 to view another user\'s data.\r\nFix: Validate authorisation on every request server-side.\r\n\r\n**2. Cryptographic Failures**\r\nSensitive data exposed due to weak or missing encryption.\r\nExample: Storing passwords in plain text or using MD5 hashing.\r\nFix: Use bcrypt for passwords. Use HTTPS for all data in transit. Encrypt sensitive data at rest.\r\n\r\n**3. Injection**\r\nUntrusted data sent to an interpreter as part of a command.\r\nExample: SQL injection, command injection, LDAP injection.\r\nFix: Use prepared statements. Validate and sanitise all input.\r\n\r\n**4. Insecure Design**\r\nMissing or ineffective security controls in the design phase.\r\nFix: Threat modelling during design. Security requirements alongside functional requirements.\r\n\r\n**5. Security Misconfiguration**\r\nDefault credentials, unnecessary features enabled, verbose error messages.\r\nExample: Leaving phpMyAdmin accessible on a production server.\r\nFix: Harden all configurations. Disable unused features. Use different credentials per environment.\r\n\r\n## Security Testing Tools\r\n\r\n- **Burp Suite**: Web application security testing (intercept and modify requests)\r\n- **OWASP ZAP**: Free web application scanner\r\n- **SQLMap**: Automated SQL injection testing\r\n- **Nikto**: Web server scanner\r\n\r\n## Secure Development Practices\r\n\r\n- Input validation: Validate all input on the server side\r\n- Output encoding: Encode all output to prevent XSS\r\n- Prepared statements: Prevent SQL injection\r\n- HTTPS everywhere: Encrypt all data in transit\r\n- Principle of least privilege: Grant minimum necessary permissions\r\n- Security headers: Content-Security-Policy, X-Frame-Options, HSTS\r\n\r\n## Practical Task\r\n\r\nSet up a deliberately vulnerable web application (DVWA - Damn Vulnerable Web Application) on your local XAMPP. Exploit the SQL injection vulnerability. Then fix it using prepared statements. Document the attack and the fix.\r\n\r\n## Self-Check\r\n1. What is SQL injection and how do you prevent it?\r\n2. What is the difference between authentication and authorisation?\r\n3. What is the principle of least privilege?', 3, 1, '2026-02-16 06:11:08'),
(68, 10, 'Ethical Hacking & Penetration Testing', '## What is Ethical Hacking?\r\n\r\nEthical hacking (penetration testing) is the authorised practice of bypassing system security to identify potential data breaches and threats. The key word is authorised — always get written permission before testing any system.\r\n\r\n## The Penetration Testing Process\r\n\r\n1. **Planning & Reconnaissance**: Define scope, gather information about the target\r\n2. **Scanning**: Identify open ports, services, and vulnerabilities\r\n3. **Gaining Access**: Exploit vulnerabilities to gain entry\r\n4. **Maintaining Access**: Simulate what an attacker would do after gaining access\r\n5. **Reporting**: Document findings, risk ratings, and remediation recommendations\r\n\r\n## Reconnaissance Tools\r\n\r\n- **Nmap**: Network scanner. Discover hosts, open ports, and services.\r\n  ```bash\r\n  nmap -sV -sC 192.168.1.1  # Scan with version detection and scripts\r\n  nmap -p 1-1000 192.168.1.1  # Scan first 1000 ports\r\n  ```\r\n- **Shodan**: Search engine for internet-connected devices\r\n- **theHarvester**: Gather emails, subdomains, and IPs from public sources\r\n- **Maltego**: Visual link analysis and data mining\r\n\r\n## Common Exploitation Techniques\r\n\r\n- **Password attacks**: Brute force, dictionary attacks, credential stuffing\r\n- **Phishing**: Craft convincing emails to steal credentials\r\n- **Metasploit**: Framework for developing and executing exploits\r\n- **Social engineering**: Manipulate people rather than systems\r\n\r\n## Certifications in Ethical Hacking\r\n\r\n- **CEH (Certified Ethical Hacker)**: EC-Council, widely recognised\r\n- **OSCP (Offensive Security Certified Professional)**: Hands-on, highly respected\r\n- **CompTIA Security+**: Entry-level, vendor-neutral\r\n- **CompTIA PenTest+**: Penetration testing focused\r\n\r\n## Legal & Ethical Considerations\r\n\r\n- Always get written authorisation before testing\r\n- Stay within the defined scope\r\n- Do not access data you are not authorised to view\r\n- Report all findings to the client\r\n- Never use skills for malicious purposes\r\n\r\n## Practical Task\r\n\r\nSet up a home lab using VirtualBox. Install Kali Linux (attacker) and Metasploitable 2 (vulnerable target). Use Nmap to scan Metasploitable. Identify 3 vulnerabilities. Research the CVE numbers for each. Write a penetration test report with risk ratings and remediation recommendations.\r\n\r\n## Self-Check\r\n1. What are the 5 phases of penetration testing?\r\n2. What is the difference between a vulnerability scan and a penetration test?\r\n3. Why is written authorisation essential before ethical hacking?', 4, 1, '2026-02-16 06:11:08'),
(69, 10, 'Incident Response & Digital Forensics', '## What is Incident Response?\r\n\r\nIncident response (IR) is the organised approach to addressing and managing the aftermath of a security breach or cyberattack. The goal is to handle the situation in a way that limits damage and reduces recovery time and costs.\r\n\r\n## The Incident Response Process (NIST)\r\n\r\n1. **Preparation**: Establish IR team, tools, and procedures before an incident occurs\r\n2. **Detection & Analysis**: Identify that an incident has occurred and understand its scope\r\n3. **Containment**: Stop the spread of the attack\r\n4. **Eradication**: Remove the threat from the environment\r\n5. **Recovery**: Restore systems to normal operation\r\n6. **Post-Incident Activity**: Learn from the incident and improve defences\r\n\r\n## Common Security Incidents\r\n\r\n- Ransomware attack: Encrypt files and demand payment\r\n- Data breach: Unauthorised access to sensitive data\r\n- DDoS attack: Overwhelm servers with traffic\r\n- Insider threat: Employee steals or leaks data\r\n- Phishing compromise: Employee credentials stolen\r\n\r\n## Digital Forensics\r\n\r\nDigital forensics is the process of collecting, preserving, and analysing digital evidence.\r\n\r\n**Forensic Principles:**\r\n- Preserve the original evidence (work on a copy)\r\n- Maintain chain of custody (document who handled evidence and when)\r\n- Document everything\r\n- Use validated tools\r\n\r\n**Common Forensic Tasks:**\r\n- Disk imaging: Create a bit-for-bit copy of a hard drive\r\n- Memory analysis: Examine RAM for running processes and network connections\r\n- Log analysis: Review system, application, and network logs\r\n- File recovery: Recover deleted files\r\n- Timeline analysis: Reconstruct the sequence of events\r\n\r\n## Tools\r\n\r\n- **Autopsy**: Free digital forensics platform\r\n- **Volatility**: Memory forensics framework\r\n- **Wireshark**: Network traffic analysis\r\n- **FTK (Forensic Toolkit)**: Commercial forensics suite\r\n- **Splunk**: Log management and SIEM\r\n\r\n## Practical Task\r\n\r\nSimulate a ransomware incident response. Scenario: A company employee opened a phishing email and ransomware encrypted 50 files. Write a complete incident response report covering all 6 NIST phases. Include: timeline of events, containment actions, eradication steps, recovery plan, and lessons learned.\r\n\r\n## Self-Check\r\n1. What are the 6 phases of the NIST incident response process?\r\n2. What is the chain of custody in digital forensics?\r\n3. What is the first thing you should do when you discover a ransomware infection?', 5, 1, '2026-02-16 06:11:08'),
(70, 10, 'Cybersecurity Capstone', '## Final Project: Security Assessment\r\n\r\nYou will conduct a comprehensive security assessment of a fictional organisation and produce a professional security report.\r\n\r\n## Scenario\r\n\r\nYou have been hired as a cybersecurity consultant for MirrorBank Nigeria, a fictional digital bank. They have experienced a suspicious increase in failed login attempts and want a full security assessment.\r\n\r\n## Assessment Scope\r\n\r\n### 1. Threat Modelling\r\n- Identify assets (customer data, financial transactions, admin systems)\r\n- Identify threats (external attackers, insider threats, third-party vendors)\r\n- Identify vulnerabilities\r\n- Calculate risk = Likelihood x Impact\r\n- Prioritise risks by severity\r\n\r\n### 2. Network Security Review\r\n- Review firewall rules\r\n- Identify open ports and services\r\n- Check for network segmentation\r\n- Review VPN and remote access controls\r\n\r\n### 3. Web Application Assessment\r\n- Test for OWASP Top 10 vulnerabilities\r\n- Review authentication mechanisms (password policy, MFA)\r\n- Check session management\r\n- Review API security\r\n\r\n### 4. Social Engineering Assessment\r\n- Design a phishing simulation email\r\n- Identify which employees would be most vulnerable\r\n- Recommend security awareness training\r\n\r\n### 5. Incident Response Plan Review\r\n- Evaluate the existing IR plan (or create one if none exists)\r\n- Identify gaps\r\n- Recommend improvements\r\n\r\n## Deliverables\r\n- Executive summary (1 page, non-technical)\r\n- Technical findings report (detailed vulnerabilities with CVE references)\r\n- Risk register (all risks with likelihood, impact, and priority)\r\n- Remediation roadmap (short-term, medium-term, long-term actions)\r\n- Security awareness training outline\r\n\r\n## Evaluation Criteria\r\n- Thoroughness of assessment (25%)\r\n- Accuracy of risk ratings (20%)\r\n- Quality of remediation recommendations (25%)\r\n- Report professionalism (20%)\r\n- Presentation clarity (10%)\r\n\r\n## Self-Check\r\n1. Are your risk ratings consistent and justified?\r\n2. Are your remediation recommendations specific and actionable?\r\n3. Could a non-technical executive understand your executive summary?', 6, 1, '2026-02-16 06:11:08'),
(71, 11, 'Computer Hardware & Components', '## What is a Computer?\r\n\r\nA computer is an electronic device that processes data according to instructions (programs). It accepts input, processes it, stores results, and produces output.\r\n\r\n## Core Hardware Components\r\n\r\n**CPU (Central Processing Unit)**: The brain of the computer. Executes instructions.\r\n- Clock speed: Measured in GHz (e.g. 3.5 GHz = 3.5 billion cycles per second)\r\n- Cores: Modern CPUs have 4-16+ cores for parallel processing\r\n- Cache: Ultra-fast memory built into the CPU (L1, L2, L3)\r\n- Popular brands: Intel (Core i3/i5/i7/i9), AMD (Ryzen)\r\n\r\n**RAM (Random Access Memory)**: Temporary working memory. Faster than storage.\r\n- Measured in GB (8GB minimum for modern use, 16GB recommended)\r\n- Data is lost when power is off\r\n- More RAM = more programs running simultaneously\r\n\r\n**Storage**: Permanent data storage.\r\n- HDD (Hard Disk Drive): Mechanical, slower, cheaper, larger capacity\r\n- SSD (Solid State Drive): No moving parts, much faster, more expensive\r\n- NVMe SSD: Even faster, connects directly to the motherboard\r\n\r\n**Motherboard**: The main circuit board connecting all components.\r\n**GPU (Graphics Processing Unit)**: Handles visual output. Essential for gaming, video editing, AI.\r\n**PSU (Power Supply Unit)**: Converts AC power to DC power for components.\r\n**Cooling**: Fans, heat sinks, or liquid cooling to prevent overheating.\r\n\r\n## Input & Output Devices\r\n\r\nInput: Keyboard, mouse, microphone, webcam, scanner, touchscreen\r\nOutput: Monitor, printer, speakers, projector\r\nStorage I/O: USB drives, external hard drives, SD cards\r\n\r\n## Practical Task\r\n\r\nIdentify all hardware components in a computer (your own or a lab computer). Record the CPU model, RAM size, storage type and size, and GPU. Research the specifications online and determine if the computer meets the requirements for: (1) basic office work, (2) video editing, (3) gaming.\r\n\r\n## Self-Check\r\n1. What is the difference between RAM and storage?\r\n2. What is the difference between an HDD and an SSD?\r\n3. What does the CPU do?', 1, 1, '2026-02-16 06:11:08'),
(72, 11, 'Operating Systems', '## What is an Operating System?\r\n\r\nAn operating system (OS) is system software that manages computer hardware and software resources and provides common services for computer programs.\r\n\r\n## Major Operating Systems\r\n\r\n**Windows (Microsoft)**\r\n- Most widely used desktop OS (~75% market share)\r\n- Best for: Office work, gaming, business software\r\n- Versions: Windows 10, Windows 11\r\n\r\n**macOS (Apple)**\r\n- Unix-based, exclusive to Apple hardware\r\n- Best for: Creative professionals (design, video, music)\r\n- Known for stability and integration with iPhone/iPad\r\n\r\n**Linux**\r\n- Open-source, free, highly customisable\r\n- Best for: Servers, developers, cybersecurity professionals\r\n- Popular distributions: Ubuntu, Fedora, Debian, Kali Linux\r\n- Powers ~96% of the world\'s top 1 million web servers\r\n\r\n**Android & iOS**: Mobile operating systems.\r\n\r\n## OS Functions\r\n\r\n- **Process management**: Schedule and manage running programs\r\n- **Memory management**: Allocate RAM to programs\r\n- **File system management**: Organise files and directories\r\n- **Device management**: Communicate with hardware via drivers\r\n- **Security**: User accounts, permissions, firewall\r\n- **User interface**: GUI (graphical) or CLI (command line)\r\n\r\n## File Systems\r\n\r\n- **NTFS**: Windows default. Supports large files, permissions, encryption.\r\n- **FAT32**: Compatible with all devices. Max file size 4GB.\r\n- **exFAT**: For USB drives. No file size limit.\r\n- **ext4**: Linux default.\r\n- **APFS**: macOS default.\r\n\r\n## Command Line Basics (Windows)\r\n\r\n```cmd\r\ndir                    -- List files in current directory\r\ncd Documents           -- Change directory\r\nmkdir NewFolder        -- Create a folder\r\ncopy file.txt backup/  -- Copy a file\r\ndel file.txt           -- Delete a file\r\nipconfig               -- Show network configuration\r\nping google.com        -- Test network connectivity\r\ntasklist               -- List running processes\r\n```\r\n\r\n## Practical Task\r\n\r\nComplete these tasks on your computer: (1) Navigate the file system using only the command line. (2) Create a folder structure for a project. (3) Check your IP address and test connectivity to 3 websites using ping. (4) List all running processes and identify any unfamiliar ones.\r\n\r\n## Self-Check\r\n1. What are the 3 major desktop operating systems?\r\n2. What is the difference between a GUI and a CLI?\r\n3. What command shows your IP address on Windows?', 2, 1, '2026-02-16 06:11:08'),
(73, 11, 'Microsoft Office Productivity', '## Microsoft Office Suite\r\n\r\nMicrosoft Office is the most widely used productivity software in business. Proficiency in Office is required for most office jobs.\r\n\r\n## Microsoft Word\r\n\r\n**Essential Skills:**\r\n- Formatting: Font, size, bold, italic, underline, colour\r\n- Paragraph formatting: Alignment, line spacing, indentation\r\n- Styles: Heading 1, Heading 2, Normal — for consistent formatting\r\n- Tables: Insert, format, merge cells\r\n- Mail merge: Create personalised letters from a data source\r\n- Track changes: Collaborate with others on a document\r\n- Table of contents: Auto-generated from heading styles\r\n\r\n**Professional Document Tips:**\r\n- Use styles, not manual formatting\r\n- Set margins: 2.5cm all sides for formal documents\r\n- Use page numbers and headers/footers\r\n- Save as PDF for sharing\r\n\r\n## Microsoft Excel\r\n\r\n**Essential Skills:**\r\n- Data entry and formatting\r\n- Formulas: SUM, AVERAGE, IF, VLOOKUP, COUNTIF\r\n- Sorting and filtering\r\n- Pivot tables\r\n- Charts and graphs\r\n- Conditional formatting\r\n- Data validation\r\n\r\n## Microsoft PowerPoint\r\n\r\n**Presentation Design Principles:**\r\n- One idea per slide\r\n- Maximum 6 bullet points per slide\r\n- Use images instead of text where possible\r\n- Consistent theme and colour scheme\r\n- Large, readable fonts (minimum 24pt for body text)\r\n- Slide notes for speaker reference\r\n\r\n**Presentation Delivery:**\r\n- Practice until you can present without reading slides\r\n- Make eye contact with the audience\r\n- Use the 10-20-30 rule: 10 slides, 20 minutes, 30pt minimum font\r\n\r\n## Google Workspace\r\n\r\nGoogle Docs, Sheets, and Slides are free, cloud-based alternatives:\r\n- Real-time collaboration\r\n- Auto-save to Google Drive\r\n- Access from any device\r\n- Free with a Google account\r\n\r\n## Practical Task\r\n\r\nCreate a professional CV in Microsoft Word using styles and proper formatting. Create a budget spreadsheet in Excel with formulas, conditional formatting, and a chart. Create a 10-slide presentation in PowerPoint about a topic of your choice. Apply the 10-20-30 rule.\r\n\r\n## Self-Check\r\n1. What is the 10-20-30 rule for presentations?\r\n2. What is the difference between a formula and a function in Excel?\r\n3. Why should you use Styles in Word instead of manual formatting?', 3, 1, '2026-02-16 06:11:08'),
(74, 11, 'Internet & Email Fundamentals', '## How the Internet Works\r\n\r\nThe internet is a global network of interconnected computers. When you visit a website:\r\n1. You type a URL (e.g. www.google.com)\r\n2. Your computer asks a DNS server to translate the domain to an IP address\r\n3. Your browser sends an HTTP/HTTPS request to that IP address\r\n4. The web server sends back the HTML, CSS, and JavaScript files\r\n5. Your browser renders the page\r\n\r\n## Web Browsers\r\n\r\nPopular browsers: Chrome, Firefox, Edge, Safari, Brave.\r\n\r\n**Browser Features:**\r\n- Address bar: Type URLs or search queries\r\n- Bookmarks: Save frequently visited pages\r\n- Extensions: Add functionality (ad blockers, password managers)\r\n- Developer Tools (F12): Inspect HTML, CSS, network requests\r\n- Private/Incognito mode: Does not save browsing history locally\r\n\r\n## Internet Safety\r\n\r\n- Use HTTPS websites (look for the padlock icon)\r\n- Do not click suspicious links in emails or messages\r\n- Use strong, unique passwords for every account\r\n- Enable two-factor authentication (2FA)\r\n- Keep software and browsers updated\r\n- Use a reputable antivirus program\r\n- Be careful what you share on social media\r\n- Use a VPN on public Wi-Fi\r\n\r\n## Email Fundamentals\r\n\r\n**Email Anatomy:**\r\n- From: Sender\'s email address\r\n- To: Primary recipient(s)\r\n- CC (Carbon Copy): Additional recipients who should be informed\r\n- BCC (Blind Carbon Copy): Recipients hidden from others\r\n- Subject: Brief description of the email content\r\n- Body: The message\r\n- Attachment: Files attached to the email\r\n\r\n**Professional Email Writing:**\r\n- Clear subject line: Action required: Invoice #1234 due Friday\r\n- Greeting: Dear Mr. Okafor, / Hi Amara,\r\n- One topic per email\r\n- Short paragraphs\r\n- Clear call to action\r\n- Professional sign-off: Kind regards, / Best wishes,\r\n\r\n**Email Security:**\r\n- Phishing: Fake emails pretending to be legitimate organisations\r\n- Spam: Unsolicited bulk email\r\n- Never click links in unexpected emails — go directly to the website\r\n- Verify sender email addresses carefully\r\n\r\n## Practical Task\r\n\r\nWrite 3 professional emails: (1) A job application email with CV attached. (2) A follow-up email after a job interview. (3) A complaint email to a service provider. Apply all professional email writing principles.\r\n\r\n## Self-Check\r\n1. What is the difference between CC and BCC?\r\n2. How can you identify a phishing email?\r\n3. What does HTTPS mean and why is it important?', 4, 1, '2026-02-16 06:11:08'),
(75, 11, 'Troubleshooting & IT Support', '## The Troubleshooting Process\r\n\r\nEffective troubleshooting follows a systematic process:\r\n\r\n1. **Identify the problem**: What exactly is not working? When did it start? What changed recently?\r\n2. **Establish a theory**: What are the possible causes?\r\n3. **Test the theory**: Try the most likely cause first\r\n4. **Establish a plan**: If the theory is confirmed, plan the fix\r\n5. **Implement the solution**: Apply the fix\r\n6. **Verify functionality**: Confirm the problem is resolved\r\n7. **Document**: Record the problem, cause, and solution\r\n\r\n## Common Computer Problems & Solutions\r\n\r\n**Computer is slow:**\r\n- Check Task Manager for high CPU/RAM usage\r\n- Disable startup programs\r\n- Run disk cleanup and defragmentation (HDD only)\r\n- Check for malware\r\n- Upgrade RAM or switch to SSD\r\n\r\n**No internet connection:**\r\n- Check if Wi-Fi is enabled\r\n- Restart the router and modem\r\n- Run Windows Network Troubleshooter\r\n- Check IP configuration (ipconfig)\r\n- Ping the router (ping 192.168.1.1)\r\n- Check if other devices can connect\r\n\r\n**Computer will not start:**\r\n- Check power cable and power button\r\n- Listen for beep codes (hardware error indicators)\r\n- Try booting in Safe Mode (F8 during startup)\r\n- Check if the monitor is connected and powered\r\n- Remove recently added hardware\r\n\r\n**Blue Screen of Death (BSOD):**\r\n- Note the error code\r\n- Search the error code online\r\n- Common causes: Driver issues, RAM failure, overheating, malware\r\n\r\n## Remote Support Tools\r\n\r\n- **TeamViewer**: Remote desktop access (free for personal use)\r\n- **AnyDesk**: Fast remote desktop\r\n- **Windows Remote Desktop**: Built into Windows\r\n- **Chrome Remote Desktop**: Browser-based, free\r\n\r\n## Ticketing Systems\r\n\r\nIT support teams use ticketing systems to track issues:\r\n- **Freshdesk**: Free tier available\r\n- **Zendesk**: Enterprise-grade\r\n- **Jira Service Management**: Popular in tech companies\r\n- **osTicket**: Free, open-source\r\n\r\n## Practical Task\r\n\r\nDocument 5 real IT support scenarios you have encountered or can research. For each: describe the problem, list 3 possible causes, describe the troubleshooting steps, and document the solution. Create a simple troubleshooting guide for non-technical users.\r\n\r\n## Self-Check\r\n1. What are the 7 steps of the troubleshooting process?\r\n2. What is the first thing to check when a computer cannot connect to the internet?\r\n3. What is a ticketing system and why do IT teams use them?', 5, 1, '2026-02-16 06:11:08'),
(76, 11, 'Capstone: IT Fundamentals Assessment', '## Final Assessment Overview\r\n\r\nThis capstone evaluates your understanding of all computer fundamentals topics covered in this course.\r\n\r\n## Written Assessment (40%)\r\n\r\nAnswer the following questions in detail:\r\n\r\n1. A user reports their computer is running very slowly. Describe your complete troubleshooting process, including at least 5 specific steps you would take.\r\n\r\n2. Explain the difference between RAM and storage. A user asks: My computer has 1TB storage but only 8GB RAM. Should I upgrade the storage or the RAM? Justify your recommendation.\r\n\r\n3. Compare Windows, macOS, and Linux. For each, describe: the target user, key advantages, key disadvantages, and one scenario where it is the best choice.\r\n\r\n4. A colleague receives an email from their bank asking them to click a link and verify their account details. The email looks legitimate. What advice would you give them? How would you identify if it is a phishing email?\r\n\r\n5. Explain how the internet works when you type www.google.com into your browser. Include: DNS, HTTP/HTTPS, IP addresses, and the role of the web server.\r\n\r\n## Practical Assessment (60%)\r\n\r\n**Task 1: Hardware Identification (10%)**\r\nIdentify and document all hardware components in a provided computer. Include specifications and purpose of each component.\r\n\r\n**Task 2: OS Navigation (15%)**\r\nComplete 10 command-line tasks on Windows: create folders, copy files, check network configuration, list processes, and more.\r\n\r\n**Task 3: Office Productivity (20%)**\r\nCreate a professional report in Word, a budget spreadsheet in Excel with formulas and charts, and a 5-slide presentation in PowerPoint.\r\n\r\n**Task 4: Troubleshooting (15%)**\r\nDiagnose and resolve 3 simulated computer problems. Document your process for each.\r\n\r\n## Evaluation Criteria\r\n- Accuracy of technical knowledge (30%)\r\n- Practical skill demonstration (40%)\r\n- Problem-solving approach (20%)\r\n- Documentation quality (10%)\r\n\r\n## Self-Check\r\n1. Can you explain how a computer works to a complete beginner?\r\n2. Can you troubleshoot common problems without looking them up?\r\n3. Are you proficient in Word, Excel, and PowerPoint?', 6, 1, '2026-02-16 06:11:08'),
(77, 12, 'Introduction to Desktop Development', '## Desktop vs Web vs Mobile\r\n\r\nDesktop applications run natively on an operating system (Windows, macOS, Linux). They offer better performance, offline capability, and deeper OS integration than web apps.\r\n\r\n## Desktop Development Frameworks\r\n\r\n**Python + Tkinter**: Built into Python. Simple, good for tools and utilities.\r\n**Python + PyQt/PySide**: Professional-grade UI. Cross-platform.\r\n**Electron**: Build desktop apps with HTML, CSS, JavaScript. Used by VS Code, Slack, Discord.\r\n**JavaFX**: Java-based, cross-platform, good for enterprise apps.\r\n**C# + WPF/WinForms**: Windows-only, deep Windows integration.\r\n**Flutter Desktop**: Same codebase as Flutter mobile, newer.\r\n\r\n## Why Python for Desktop?\r\n\r\nPython is an excellent choice for desktop development:\r\n- Easy to learn and read\r\n- Large standard library\r\n- Excellent for data-heavy applications\r\n- Cross-platform (Windows, macOS, Linux)\r\n- Can package into standalone executables (PyInstaller)\r\n\r\n## Setting Up Python Desktop Development\r\n\r\n```bash\r\n# Install Python from python.org\r\n# Install PyQt5\r\npip install PyQt5\r\n\r\n# Or install Tkinter (usually included with Python)\r\npython -m tkinter  # Test if Tkinter is available\r\n```\r\n\r\n## Your First Tkinter App\r\n\r\n```python\r\nimport tkinter as tk\r\nfrom tkinter import messagebox\r\n\r\nroot = tk.Tk()\r\nroot.title(\'My First App\')\r\nroot.geometry(\'400x300\')\r\n\r\nlabel = tk.Label(root, text=\'Hello, World!\', font=(\'Arial\', 18))\r\nlabel.pack(pady=20)\r\n\r\ndef on_click():\r\n    messagebox.showinfo(\'Message\', \'Button clicked!\')\r\n\r\nbtn = tk.Button(root, text=\'Click Me\', command=on_click,\r\n                bg=\'#4f46e5\', fg=\'white\', padx=20, pady=10)\r\nbtn.pack()\r\n\r\nroot.mainloop()\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a simple calculator app using Tkinter. It should have: number buttons (0-9), operation buttons (+, -, *, /), a display screen, a clear button, and an equals button. The calculator should handle basic arithmetic correctly.\r\n\r\n## Self-Check\r\n1. What is the difference between a desktop app and a web app?\r\n2. Name 3 popular desktop development frameworks.\r\n3. What does root.mainloop() do in Tkinter?', 1, 1, '2026-02-16 06:11:08'),
(78, 12, 'GUI Design with PyQt5', '## Why PyQt5?\r\n\r\nPyQt5 is a set of Python bindings for Qt, one of the most powerful cross-platform GUI frameworks. It produces professional-looking applications that run on Windows, macOS, and Linux.\r\n\r\n## PyQt5 Fundamentals\r\n\r\n```python\r\nimport sys\r\nfrom PyQt5.QtWidgets import (QApplication, QMainWindow, QWidget,\r\n                              QVBoxLayout, QHBoxLayout, QPushButton,\r\n                              QLabel, QLineEdit, QTableWidget,\r\n                              QTableWidgetItem, QMessageBox)\r\nfrom PyQt5.QtCore import Qt\r\nfrom PyQt5.QtGui import QFont, QColor\r\n\r\nclass MainWindow(QMainWindow):\r\n    def __init__(self):\r\n        super().__init__()\r\n        self.setWindowTitle(\'Student Manager\')\r\n        self.setMinimumSize(800, 600)\r\n        self.setup_ui()\r\n\r\n    def setup_ui(self):\r\n        central = QWidget()\r\n        self.setCentralWidget(central)\r\n        layout = QVBoxLayout(central)\r\n\r\n        # Title\r\n        title = QLabel(\'Student Manager\')\r\n        title.setFont(QFont(\'Arial\', 18, QFont.Bold))\r\n        title.setAlignment(Qt.AlignCenter)\r\n        layout.addWidget(title)\r\n\r\n        # Input row\r\n        input_row = QHBoxLayout()\r\n        self.name_input = QLineEdit()\r\n        self.name_input.setPlaceholderText(\'Student name\')\r\n        self.add_btn = QPushButton(\'Add Student\')\r\n        self.add_btn.clicked.connect(self.add_student)\r\n        input_row.addWidget(self.name_input)\r\n        input_row.addWidget(self.add_btn)\r\n        layout.addLayout(input_row)\r\n\r\n        # Table\r\n        self.table = QTableWidget(0, 3)\r\n        self.table.setHorizontalHeaderLabels([\'ID\', \'Name\', \'Actions\'])\r\n        layout.addWidget(self.table)\r\n\r\n    def add_student(self):\r\n        name = self.name_input.text().strip()\r\n        if not name:\r\n            QMessageBox.warning(self, \'Error\', \'Please enter a name\')\r\n            return\r\n        row = self.table.rowCount()\r\n        self.table.insertRow(row)\r\n        self.table.setItem(row, 0, QTableWidgetItem(str(row + 1)))\r\n        self.table.setItem(row, 1, QTableWidgetItem(name))\r\n        self.name_input.clear()\r\n\r\napp = QApplication(sys.argv)\r\nwindow = MainWindow()\r\nwindow.show()\r\nsys.exit(app.exec_())\r\n```\r\n\r\n## Qt Designer\r\n\r\nQt Designer is a visual UI builder. Design your interface visually, then load the .ui file in Python:\r\n\r\n```python\r\nfrom PyQt5 import uic\r\nclass MainWindow(QMainWindow):\r\n    def __init__(self):\r\n        super().__init__()\r\n        uic.loadUi(\'main_window.ui\', self)\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a contact book application with PyQt5. Features: add contacts (name, phone, email), display in a table, search by name, edit existing contacts, delete contacts. Use Qt Designer to design the UI.\r\n\r\n## Self-Check\r\n1. What is the difference between QVBoxLayout and QHBoxLayout?\r\n2. How do you connect a button click to a function in PyQt5?\r\n3. What is Qt Designer and how does it help development?', 2, 1, '2026-02-16 06:11:08'),
(79, 12, 'Database Integration in Desktop Apps', '## Connecting Desktop Apps to SQLite\r\n\r\nSQLite is a lightweight, file-based database perfect for desktop applications. No server required — the entire database is a single file.\r\n\r\n## Python sqlite3 Module\r\n\r\n```python\r\nimport sqlite3\r\nfrom contextlib import contextmanager\r\n\r\nDB_PATH = \'students.db\'\r\n\r\ndef init_db():\r\n    with sqlite3.connect(DB_PATH) as conn:\r\n        conn.execute(\'\'\'\r\n            CREATE TABLE IF NOT EXISTS students (\r\n                id INTEGER PRIMARY KEY AUTOINCREMENT,\r\n                name TEXT NOT NULL,\r\n                email TEXT UNIQUE NOT NULL,\r\n                course TEXT,\r\n                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\r\n            )\r\n        \'\'\')\r\n        conn.commit()\r\n\r\ndef add_student(name: str, email: str, course: str) -> int:\r\n    with sqlite3.connect(DB_PATH) as conn:\r\n        cursor = conn.execute(\r\n            \'INSERT INTO students (name, email, course) VALUES (?,?,?)\',\r\n            (name, email, course)\r\n        )\r\n        conn.commit()\r\n        return cursor.lastrowid\r\n\r\ndef get_all_students() -> list:\r\n    with sqlite3.connect(DB_PATH) as conn:\r\n        conn.row_factory = sqlite3.Row\r\n        return conn.execute(\'SELECT * FROM students ORDER BY name\').fetchall()\r\n\r\ndef search_students(query: str) -> list:\r\n    with sqlite3.connect(DB_PATH) as conn:\r\n        conn.row_factory = sqlite3.Row\r\n        return conn.execute(\r\n            \'SELECT * FROM students WHERE name LIKE ? OR email LIKE ?\',\r\n            (f\'%{query}%\', f\'%{query}%\')\r\n        ).fetchall()\r\n\r\ndef delete_student(student_id: int) -> None:\r\n    with sqlite3.connect(DB_PATH) as conn:\r\n        conn.execute(\'DELETE FROM students WHERE id = ?\', (student_id,))\r\n        conn.commit()\r\n```\r\n\r\n## Integrating with PyQt5\r\n\r\n```python\r\ndef load_students(self):\r\n    students = get_all_students()\r\n    self.table.setRowCount(0)\r\n    for student in students:\r\n        row = self.table.rowCount()\r\n        self.table.insertRow(row)\r\n        self.table.setItem(row, 0, QTableWidgetItem(str(student[\'id\'])))\r\n        self.table.setItem(row, 1, QTableWidgetItem(student[\'name\']))\r\n        self.table.setItem(row, 2, QTableWidgetItem(student[\'email\']))\r\n```\r\n\r\n## Practical Task\r\n\r\nUpgrade your contact book from Lesson 2 to use SQLite for persistent storage. All contacts should be saved to a database file and loaded on startup. Add: search functionality, edit contact dialog, and export to CSV.\r\n\r\n## Self-Check\r\n1. What is SQLite and why is it good for desktop apps?\r\n2. Why should you always use parameterised queries (?) instead of string formatting?\r\n3. What does conn.row_factory = sqlite3.Row do?', 3, 1, '2026-02-16 06:11:08'),
(80, 12, 'Packaging & Distribution', '## Packaging Python Desktop Apps\r\n\r\nTo distribute your app to users who do not have Python installed, you need to package it as a standalone executable.\r\n\r\n## PyInstaller\r\n\r\nPyInstaller bundles your Python app and all its dependencies into a single executable.\r\n\r\n```bash\r\n# Install PyInstaller\r\npip install pyinstaller\r\n\r\n# Create a single executable file\r\npyinstaller --onefile --windowed main.py\r\n\r\n# With a custom icon\r\npyinstaller --onefile --windowed --icon=app.ico main.py\r\n\r\n# Output: dist/main.exe (Windows) or dist/main (macOS/Linux)\r\n```\r\n\r\n**Options:**\r\n- `--onefile`: Bundle everything into a single .exe file\r\n- `--windowed` or `--noconsole`: Hide the console window (for GUI apps)\r\n- `--icon`: Set the application icon (.ico for Windows)\r\n- `--name`: Set the output filename\r\n\r\n## Creating an Installer (Windows)\r\n\r\nInno Setup is a free tool for creating Windows installers:\r\n1. Download Inno Setup from jrsoftware.org\r\n2. Create a script defining your app name, version, files, and shortcuts\r\n3. Compile to create a setup.exe installer\r\n4. Users run setup.exe to install your app\r\n\r\n## Auto-Update Mechanism\r\n\r\n```python\r\nimport requests\r\nimport json\r\n\r\nCURRENT_VERSION = \'1.0.0\'\r\nUPDATE_URL = \'https://yourserver.com/version.json\'\r\n\r\ndef check_for_updates():\r\n    try:\r\n        response = requests.get(UPDATE_URL, timeout=5)\r\n        data = response.json()\r\n        latest = data[\'version\']\r\n        if latest != CURRENT_VERSION:\r\n            return latest, data[\'download_url\']\r\n    except Exception:\r\n        pass\r\n    return None, None\r\n```\r\n\r\n## App Versioning\r\n\r\nUse semantic versioning: MAJOR.MINOR.PATCH\r\n- MAJOR: Breaking changes\r\n- MINOR: New features, backward compatible\r\n- PATCH: Bug fixes\r\n\r\nExample: 1.0.0 -> 1.0.1 (bug fix) -> 1.1.0 (new feature) -> 2.0.0 (breaking change)\r\n\r\n## Practical Task\r\n\r\nPackage your contact book application using PyInstaller. Create a single .exe file. Test it on a computer without Python installed. Create a simple Inno Setup installer script. Document the build and distribution process.\r\n\r\n## Self-Check\r\n1. What does the --onefile flag do in PyInstaller?\r\n2. What is semantic versioning?\r\n3. Why do you need to package a Python app before distributing it?', 4, 1, '2026-02-16 06:11:08'),
(81, 12, 'Advanced Features: Threads & APIs', '## Threading in Desktop Apps\r\n\r\nLong-running operations (database queries, file processing, API calls) should run in background threads to keep the UI responsive.\r\n\r\n```python\r\nfrom PyQt5.QtCore import QThread, pyqtSignal\r\n\r\nclass DataWorker(QThread):\r\n    finished = pyqtSignal(list)  # Signal to send data back to UI\r\n    error = pyqtSignal(str)\r\n\r\n    def __init__(self, query):\r\n        super().__init__()\r\n        self.query = query\r\n\r\n    def run(self):\r\n        try:\r\n            # This runs in a background thread\r\n            results = fetch_data_from_api(self.query)\r\n            self.finished.emit(results)\r\n        except Exception as e:\r\n            self.error.emit(str(e))\r\n\r\n# In your main window:\r\ndef start_search(self):\r\n    self.search_btn.setEnabled(False)\r\n    self.worker = DataWorker(self.search_input.text())\r\n    self.worker.finished.connect(self.on_results_ready)\r\n    self.worker.error.connect(self.on_error)\r\n    self.worker.start()\r\n\r\ndef on_results_ready(self, results):\r\n    self.search_btn.setEnabled(True)\r\n    self.display_results(results)\r\n```\r\n\r\n## Consuming REST APIs\r\n\r\n```python\r\nimport requests\r\n\r\ndef fetch_exchange_rates(base_currency=\'NGN\'):\r\n    url = f\'https://api.exchangerate-api.com/v4/latest/{base_currency}\'\r\n    response = requests.get(url, timeout=10)\r\n    response.raise_for_status()\r\n    return response.json()[\'rates\']\r\n\r\ndef fetch_weather(city):\r\n    api_key = \'your_api_key\'\r\n    url = f\'https://api.openweathermap.org/data/2.5/weather?q={city}&appid={api_key}\'\r\n    response = requests.get(url, timeout=10)\r\n    data = response.json()\r\n    return {\r\n        \'temp\': data[\'main\'][\'temp\'] - 273.15,  # Kelvin to Celsius\r\n        \'description\': data[\'weather\'][0][\'description\'],\r\n        \'humidity\': data[\'main\'][\'humidity\'],\r\n    }\r\n```\r\n\r\n## Charts in Desktop Apps\r\n\r\n```python\r\nimport matplotlib.pyplot as plt\r\nfrom matplotlib.backends.backend_qt5agg import FigureCanvasQTAgg\r\n\r\nclass ChartWidget(FigureCanvasQTAgg):\r\n    def __init__(self):\r\n        fig, self.ax = plt.subplots(figsize=(8, 4))\r\n        super().__init__(fig)\r\n\r\n    def plot_bar(self, labels, values, title):\r\n        self.ax.clear()\r\n        self.ax.bar(labels, values, color=\'#4f46e5\')\r\n        self.ax.set_title(title)\r\n        self.draw()\r\n```\r\n\r\n## Practical Task\r\n\r\nBuild a currency converter desktop app. Features: fetch live exchange rates from a free API, convert between any two currencies, display a chart of the last 7 days of exchange rates, run API calls in a background thread so the UI stays responsive.\r\n\r\n## Self-Check\r\n1. Why should long-running operations run in a background thread?\r\n2. What is a PyQt5 Signal and how does it work?\r\n3. How do you handle API errors gracefully in a desktop app?', 5, 1, '2026-02-16 06:11:08'),
(82, 12, 'Capstone: Desktop Application Project', '## Final Project Brief\r\n\r\nYou will build a complete, production-ready desktop application that solves a real problem.\r\n\r\n## Project Options (Choose One)\r\n\r\n### Option A: School Management System\r\nA desktop app for a small school:\r\n- Student registration and management\r\n- Course/class management\r\n- Attendance tracking\r\n- Grade recording and report cards\r\n- Fee payment tracking\r\n- Reports: attendance summary, grade report, fee outstanding\r\n\r\n### Option B: Inventory Management System\r\nA desktop app for a small business:\r\n- Product catalogue with categories\r\n- Stock tracking (add stock, record sales)\r\n- Low stock alerts\r\n- Supplier management\r\n- Sales reports and charts\r\n- Export reports to PDF/Excel\r\n\r\n### Option C: Personal Finance Tracker\r\nA desktop app for personal budgeting:\r\n- Income and expense tracking by category\r\n- Monthly budget setting\r\n- Visual charts (spending by category, monthly trend)\r\n- Bill reminders\r\n- Export to CSV\r\n- Password protection\r\n\r\n## Technical Requirements\r\n\r\n- Built with Python + PyQt5\r\n- SQLite database for persistent storage\r\n- At least one background thread for data operations\r\n- At least one chart (matplotlib)\r\n- Packaged as a standalone .exe with PyInstaller\r\n- Proper error handling throughout\r\n- Clean, professional UI\r\n\r\n## Evaluation Criteria\r\n- Feature completeness (25%)\r\n- Code quality and organisation (25%)\r\n- UI/UX design (20%)\r\n- Database design (15%)\r\n- Packaging and distribution (15%)\r\n\r\n## Self-Check\r\n1. Does your app handle errors gracefully (no crashes on bad input)?\r\n2. Is your database schema properly normalised?\r\n3. Can a non-technical user install and use your app without help?', 6, 1, '2026-02-16 06:11:08'),
(83, 13, 'POS Systems & Operations', '## What is a POS System?\r\n\r\nA Point of Sale (POS) system is the combination of hardware and software used to process sales transactions. Modern POS systems do much more than just process payments.\r\n\r\n## POS Hardware Components\r\n\r\n- **POS Terminal**: The main computer running the POS software\r\n- **Receipt Printer**: Thermal printer for customer receipts\r\n- **Barcode Scanner**: Reads product barcodes for quick item entry\r\n- **Cash Drawer**: Stores cash, opens automatically on cash transactions\r\n- **Card Reader/POS Terminal**: Processes debit and credit card payments\r\n- **Customer Display**: Shows transaction details to the customer\r\n- **Weighing Scale**: For items sold by weight (supermarkets, markets)\r\n\r\n## POS Software Features\r\n\r\n- Sales processing: Scan items, apply discounts, process payment\r\n- Inventory management: Track stock levels, low stock alerts\r\n- Customer management: Loyalty programmes, purchase history\r\n- Reporting: Daily sales, best-selling products, staff performance\r\n- Multi-location: Manage multiple branches from one system\r\n\r\n## Payment Methods\r\n\r\n- Cash: Count change accurately, reconcile at end of day\r\n- Debit/Credit card: POS terminal, chip and PIN or contactless\r\n- Bank transfer: USSD or mobile banking\r\n- Mobile money: Opay, Palmpay, Moniepoint\r\n- QR code payment: Scan to pay\r\n\r\n## Common POS Systems in Nigeria\r\n\r\n- Moniepoint POS: Widely used by small businesses\r\n- Paystack Terminal: Integrated with Paystack payments\r\n- Quickteller POS: Interswitch product\r\n- Kudi POS: Agent banking focused\r\n- Custom POS software: Built for specific business needs\r\n\r\n## Practical Task\r\n\r\nVisit a local business that uses a POS system. Observe and document: the hardware components used, the payment methods accepted, how receipts are generated, and how the cashier handles a transaction error. Write a 1-page report.\r\n\r\n## Self-Check\r\n1. Name 5 hardware components of a POS system.\r\n2. What is the difference between a POS terminal and a cash register?\r\n3. Name 3 mobile payment methods used in Nigeria.', 1, 1, '2026-02-16 06:11:08');
INSERT INTO `lms_lessons` (`id`, `course_id`, `title`, `content`, `sort_order`, `is_published`, `created_at`) VALUES
(84, 13, 'ICT Support & Troubleshooting', '## ICT Support Roles\r\n\r\nICT (Information and Communications Technology) support professionals maintain and troubleshoot technology systems in organisations.\r\n\r\n## Support Tiers\r\n\r\n**Tier 1 (Help Desk)**: First point of contact. Handle common issues: password resets, basic software problems, connectivity issues. Escalate complex issues to Tier 2.\r\n\r\n**Tier 2 (Technical Support)**: Handle more complex issues: hardware failures, software configuration, network problems. Escalate to Tier 3 if needed.\r\n\r\n**Tier 3 (Expert Support)**: Specialists: network engineers, database administrators, security experts. Handle the most complex issues.\r\n\r\n## Common ICT Support Tasks\r\n\r\n- Setting up new computers and user accounts\r\n- Installing and configuring software\r\n- Troubleshooting hardware failures\r\n- Network connectivity issues\r\n- Email configuration (Outlook, Gmail)\r\n- Printer setup and troubleshooting\r\n- Data backup and recovery\r\n- Antivirus installation and updates\r\n- User training\r\n\r\n## Remote Support\r\n\r\nMost ICT support is now done remotely:\r\n- TeamViewer: Remote desktop access\r\n- AnyDesk: Fast, lightweight remote access\r\n- Microsoft Remote Desktop: Built into Windows\r\n- Zoom/Teams screen sharing: For guided support\r\n\r\n## Documentation\r\n\r\nGood ICT support requires thorough documentation:\r\n- Asset register: All hardware and software in the organisation\r\n- Network diagram: Visual map of the network\r\n- Runbooks: Step-by-step procedures for common tasks\r\n- Incident log: Record of all support tickets\r\n- Change log: Record of all system changes\r\n\r\n## Practical Task\r\n\r\nCreate an ICT support runbook for 5 common issues: (1) Computer cannot connect to Wi-Fi, (2) Printer not printing, (3) Outlook not receiving emails, (4) Computer running slowly, (5) User forgot their password. Each runbook should have step-by-step troubleshooting instructions.\r\n\r\n## Self-Check\r\n1. What are the 3 tiers of ICT support?\r\n2. What is a runbook?\r\n3. Name 3 remote support tools.', 2, 1, '2026-02-16 06:11:08'),
(85, 13, 'Network Setup & Configuration', '## Setting Up a Small Office Network\r\n\r\nMost small businesses need a simple network: internet connection, Wi-Fi, and shared resources (printer, file server).\r\n\r\n## Network Equipment\r\n\r\n- **Modem**: Connects to your ISP (Internet Service Provider). Converts the ISP signal to Ethernet.\r\n- **Router**: Connects multiple devices to the internet. Assigns IP addresses via DHCP. Provides Wi-Fi.\r\n- **Switch**: Connects multiple wired devices in a network. Extends the number of Ethernet ports.\r\n- **Access Point**: Extends Wi-Fi coverage to areas the router cannot reach.\r\n- **Ethernet Cable (Cat5e/Cat6)**: Wired connection. Faster and more reliable than Wi-Fi.\r\n\r\n## IP Addressing\r\n\r\n**Private IP ranges (not routable on the internet):**\r\n- 192.168.0.0 - 192.168.255.255 (most home/office networks)\r\n- 10.0.0.0 - 10.255.255.255 (larger networks)\r\n- 172.16.0.0 - 172.31.255.255\r\n\r\n**DHCP**: Automatically assigns IP addresses to devices.\r\n**Static IP**: Manually assigned. Use for servers, printers, and network equipment.\r\n\r\n## Wi-Fi Security\r\n\r\n- Use WPA3 or WPA2 encryption (never WEP)\r\n- Use a strong, unique Wi-Fi password (12+ characters)\r\n- Change the default router admin password\r\n- Disable WPS (Wi-Fi Protected Setup) - it has known vulnerabilities\r\n- Create a separate guest network for visitors\r\n- Hide the SSID (network name) for extra security\r\n\r\n## Basic Router Configuration\r\n\r\n1. Connect to router admin panel (usually 192.168.1.1 or 192.168.0.1)\r\n2. Change admin username and password\r\n3. Set Wi-Fi name (SSID) and password\r\n4. Configure DHCP range\r\n5. Set up port forwarding if needed\r\n6. Enable firewall\r\n7. Update router firmware\r\n\r\n## Practical Task\r\n\r\nDraw a network diagram for a fictional small office with: 10 computers, 2 printers, 1 file server, 1 router, 1 switch, and Wi-Fi coverage. Label all devices with IP addresses. Write a network setup guide for a non-technical office manager.\r\n\r\n## Self-Check\r\n1. What is the difference between a router and a switch?\r\n2. What is DHCP?\r\n3. Which Wi-Fi security protocol should you use?', 3, 1, '2026-02-16 06:11:08'),
(86, 13, 'POS Maintenance & Security', '## POS System Maintenance\r\n\r\nRegular maintenance prevents downtime and data loss.\r\n\r\n## Daily Maintenance Tasks\r\n\r\n- Reconcile cash drawer at end of day\r\n- Back up transaction data\r\n- Check receipt paper level\r\n- Clean card reader contacts\r\n- Review daily sales report for anomalies\r\n\r\n## Weekly Maintenance Tasks\r\n\r\n- Clean all hardware (screens, keyboards, scanners)\r\n- Check for software updates\r\n- Review and clear old transaction logs\r\n- Test backup restoration\r\n- Check network connectivity and speed\r\n\r\n## POS Security\r\n\r\n**Physical Security:**\r\n- Secure the POS terminal to the counter (cable lock)\r\n- Restrict access to the cash drawer\r\n- Install CCTV cameras at POS stations\r\n- Never leave the POS unattended while logged in\r\n\r\n**Software Security:**\r\n- Use strong, unique passwords for each staff member\r\n- Enable automatic screen lock after inactivity\r\n- Restrict staff access to only the functions they need\r\n- Keep POS software updated\r\n- Use antivirus software\r\n\r\n**Transaction Security:**\r\n- Never store card numbers\r\n- Use end-to-end encrypted card readers\r\n- Train staff to recognise card skimming devices\r\n- Monitor for unusual transaction patterns\r\n- Require manager approval for refunds and voids\r\n\r\n## Common POS Problems & Solutions\r\n\r\n- Receipt printer not printing: Check paper, check cable, restart printer\r\n- Card reader not working: Clean contacts, check cable, restart terminal\r\n- Barcode scanner not reading: Clean scanner glass, check cable, adjust scan angle\r\n- POS software frozen: Force close and restart, check for updates\r\n- Network connectivity lost: Restart router, check cables, contact ISP\r\n\r\n## Practical Task\r\n\r\nCreate a POS maintenance schedule for a fictional retail shop. Include: daily, weekly, monthly, and annual tasks. Create a troubleshooting guide for the 5 most common POS problems. Design a staff training checklist for new cashiers.\r\n\r\n## Self-Check\r\n1. What daily maintenance tasks should a cashier perform?\r\n2. How do you secure a POS system against internal theft?\r\n3. What should you do if the card reader stops working?', 4, 1, '2026-02-16 06:11:08'),
(87, 13, 'Customer Service & Business Skills', '## Customer Service in ICT & POS\r\n\r\nTechnical skills alone are not enough. Excellent customer service is what builds a successful career in ICT support and POS operations.\r\n\r\n## The Customer Service Mindset\r\n\r\n- The customer is not always right, but they are always the customer\r\n- Every interaction is an opportunity to build trust\r\n- Solve the problem, not just the symptom\r\n- Follow up to ensure the issue is fully resolved\r\n- Treat every customer with respect, regardless of their technical knowledge\r\n\r\n## Communication Skills\r\n\r\n**Active Listening:**\r\n- Let the customer finish speaking before responding\r\n- Ask clarifying questions\r\n- Summarise what you heard: So what you are saying is...\r\n- Do not interrupt\r\n\r\n**Explaining Technical Issues:**\r\n- Avoid jargon with non-technical customers\r\n- Use analogies: The router is like a traffic controller for your internet\r\n- Check for understanding: Does that make sense?\r\n- Provide written instructions for complex procedures\r\n\r\n**Handling Difficult Customers:**\r\n- Stay calm and professional\r\n- Acknowledge their frustration: I understand this is frustrating\r\n- Focus on solutions, not blame\r\n- Escalate if you cannot resolve the issue\r\n- Never argue or become defensive\r\n\r\n## Business Skills for ICT Professionals\r\n\r\n- **Time management**: Prioritise tickets by urgency and impact\r\n- **Documentation**: Write clear, concise reports and runbooks\r\n- **Project management**: Plan and execute IT projects on time and budget\r\n- **Vendor management**: Evaluate and manage relationships with suppliers\r\n- **Budget awareness**: Understand the cost implications of your recommendations\r\n\r\n## Professional Development\r\n\r\nCertifications that boost your ICT career:\r\n- CompTIA A+: Entry-level IT support\r\n- CompTIA Network+: Networking fundamentals\r\n- Microsoft Certified: Modern Desktop Administrator\r\n- Google IT Support Certificate: Free on Coursera\r\n\r\n## Practical Task\r\n\r\nRole-play 3 customer service scenarios with a partner: (1) A customer whose POS terminal stopped working during a busy period. (2) A customer who does not understand why their card was declined. (3) A customer who is angry about a double charge. Write a reflection on what you learned from each scenario.\r\n\r\n## Self-Check\r\n1. What is active listening?\r\n2. How do you explain a technical issue to a non-technical customer?\r\n3. Name 2 IT certifications suitable for a beginner.', 5, 1, '2026-02-16 06:11:08'),
(88, 13, 'Capstone: POS & ICT Support Project', '## Final Project Brief\r\n\r\nYou will demonstrate your POS operations and ICT support skills through a practical assessment.\r\n\r\n## Part 1: POS Operations Assessment (40%)\r\n\r\n**Practical Tasks:**\r\n1. Process 10 simulated transactions including: cash sale, card payment, discount application, refund, and void\r\n2. Perform end-of-day reconciliation\r\n3. Generate and interpret a daily sales report\r\n4. Troubleshoot 3 simulated POS problems\r\n\r\n**Written Tasks:**\r\n1. Write a cashier training manual (2 pages) covering: opening procedures, processing transactions, handling errors, and closing procedures\r\n2. Create a daily maintenance checklist\r\n\r\n## Part 2: ICT Support Assessment (40%)\r\n\r\n**Practical Tasks:**\r\n1. Set up a new computer: install OS, configure network, install required software\r\n2. Troubleshoot 5 simulated IT problems (provided by instructor)\r\n3. Set up a small network: configure router, connect 3 devices, test connectivity\r\n4. Provide remote support to a simulated user using TeamViewer\r\n\r\n**Written Tasks:**\r\n1. Create an IT asset register for a fictional 10-person office\r\n2. Write an incident report for a simulated security breach\r\n\r\n## Part 3: Customer Service Assessment (20%)\r\n\r\n1. Role-play 2 customer service scenarios (assessed by instructor)\r\n2. Write a customer service policy for a fictional ICT support company\r\n\r\n## Evaluation Criteria\r\n- Technical accuracy (35%)\r\n- Problem-solving approach (25%)\r\n- Documentation quality (20%)\r\n- Customer service skills (20%)\r\n\r\n## Self-Check\r\n1. Can you process all transaction types on a POS system without assistance?\r\n2. Can you troubleshoot common IT problems systematically?\r\n3. Can you explain technical issues clearly to non-technical users?', 6, 1, '2026-02-16 06:11:08'),
(89, 14, 'Networking Fundamentals', '## What is a Computer Network?\r\n\r\nA computer network is a collection of interconnected devices that can communicate and share resources. Networks enable file sharing, internet access, email, video calls, and cloud services.\r\n\r\n## Network Types\r\n\r\n- **LAN (Local Area Network)**: Covers a small area (home, office, school). Fast, low latency.\r\n- **WAN (Wide Area Network)**: Covers large geographic areas. The internet is the largest WAN.\r\n- **MAN (Metropolitan Area Network)**: Covers a city or campus.\r\n- **PAN (Personal Area Network)**: Very short range (Bluetooth, USB). Connects personal devices.\r\n- **WLAN (Wireless LAN)**: Wi-Fi network.\r\n- **VPN (Virtual Private Network)**: Secure tunnel over the internet.\r\n\r\n## Network Topologies\r\n\r\n- **Bus**: All devices connected to a single cable. Simple but a single failure breaks the network.\r\n- **Star**: All devices connect to a central switch/hub. Most common in modern networks.\r\n- **Ring**: Devices connected in a circle. Data travels in one direction.\r\n- **Mesh**: Every device connects to every other device. Highly redundant, expensive.\r\n- **Hybrid**: Combination of topologies.\r\n\r\n## The OSI Model\r\n\r\nThe OSI (Open Systems Interconnection) model describes how data travels across a network in 7 layers:\r\n\r\n1. Physical: Cables, signals, bits\r\n2. Data Link: MAC addresses, switches, frames\r\n3. Network: IP addresses, routers, packets\r\n4. Transport: TCP/UDP, ports, segments\r\n5. Session: Establishing and managing connections\r\n6. Presentation: Encryption, compression, data format\r\n7. Application: HTTP, FTP, DNS, SMTP\r\n\r\nMemory aid: Please Do Not Throw Sausage Pizza Away\r\n\r\n## Practical Task\r\n\r\nDraw a network diagram for your home or school network. Identify: all devices, their connection types (wired/wireless), the router, and the internet connection. Label each device with its approximate IP address. Identify which OSI layer each device primarily operates at.\r\n\r\n## Self-Check\r\n1. What is the difference between a LAN and a WAN?\r\n2. What are the 7 layers of the OSI model?\r\n3. Which network topology is most common in modern offices?', 1, 1, '2026-02-16 06:11:08'),
(90, 14, 'IP Addressing & Subnetting', '## IP Addressing\r\n\r\nEvery device on a network needs a unique IP address to communicate.\r\n\r\n## IPv4 Address Structure\r\n\r\nAn IPv4 address is 32 bits, written as 4 octets separated by dots:\r\n192.168.1.100\r\n\r\nEach octet is 8 bits (0-255).\r\n\r\n## IP Address Classes\r\n\r\n| Class | Range | Default Subnet Mask | Use |\r\n|---|---|---|---|\r\n| A | 1.0.0.0 - 126.255.255.255 | 255.0.0.0 | Large networks |\r\n| B | 128.0.0.0 - 191.255.255.255 | 255.255.0.0 | Medium networks |\r\n| C | 192.0.0.0 - 223.255.255.255 | 255.255.255.0 | Small networks |\r\n\r\n## Private IP Ranges\r\n\r\nThese ranges are reserved for private networks (not routable on the internet):\r\n- 10.0.0.0/8 (Class A private)\r\n- 172.16.0.0/12 (Class B private)\r\n- 192.168.0.0/16 (Class C private)\r\n\r\n## Subnet Mask\r\n\r\nA subnet mask defines which part of an IP address is the network and which is the host:\r\n\r\nIP: 192.168.1.100\r\nMask: 255.255.255.0 (/24)\r\nNetwork: 192.168.1.0\r\nHost range: 192.168.1.1 - 192.168.1.254\r\nBroadcast: 192.168.1.255\r\nUsable hosts: 254\r\n\r\n## CIDR Notation\r\n\r\nCIDR (Classless Inter-Domain Routing) notation: 192.168.1.0/24\r\nThe /24 means 24 bits are the network portion.\r\n\r\nCommon CIDR values:\r\n- /24 = 255.255.255.0 = 254 hosts\r\n- /25 = 255.255.255.128 = 126 hosts\r\n- /26 = 255.255.255.192 = 62 hosts\r\n- /30 = 255.255.255.252 = 2 hosts (point-to-point links)\r\n\r\n## IPv6\r\n\r\nIPv4 addresses are running out. IPv6 uses 128-bit addresses:\r\n2001:0db8:85a3:0000:0000:8a2e:0370:7334\r\n\r\nIPv6 provides 340 undecillion addresses (3.4 x 10^38).\r\n\r\n## Practical Task\r\n\r\nYou have been given the network 192.168.10.0/24. Divide it into 4 equal subnets. For each subnet, calculate: network address, subnet mask, first usable host, last usable host, broadcast address, and number of usable hosts.\r\n\r\n## Self-Check\r\n1. What is the difference between a public and private IP address?\r\n2. What does /24 mean in CIDR notation?\r\n3. How many usable hosts does a /24 network have?', 2, 1, '2026-02-16 06:11:08'),
(91, 14, 'Routing & Switching', '## Switches\r\n\r\nA switch operates at Layer 2 (Data Link) of the OSI model. It connects devices within the same network using MAC addresses.\r\n\r\n**How a switch works:**\r\n1. Device A sends a frame to Device B\r\n2. The switch reads the destination MAC address\r\n3. The switch looks up its MAC address table\r\n4. If found, it forwards the frame only to the correct port\r\n5. If not found, it floods the frame to all ports (except the source)\r\n\r\n**VLANs (Virtual LANs)**: Logically segment a network without physical separation. Devices on different VLANs cannot communicate without a router.\r\n\r\n## Routers\r\n\r\nA router operates at Layer 3 (Network) of the OSI model. It connects different networks using IP addresses.\r\n\r\n**How a router works:**\r\n1. Receives a packet\r\n2. Reads the destination IP address\r\n3. Looks up the routing table\r\n4. Forwards the packet to the next hop\r\n\r\n**Routing Table**: A list of network destinations and the next hop to reach them.\r\n\r\n## Routing Protocols\r\n\r\n**Static routing**: Manually configured routes. Simple, predictable, no overhead. Good for small networks.\r\n\r\n**Dynamic routing**: Routers automatically discover and share routes:\r\n- **RIP (Routing Information Protocol)**: Simple, uses hop count. Max 15 hops.\r\n- **OSPF (Open Shortest Path First)**: Fast convergence, uses cost metric. Good for enterprise.\r\n- **BGP (Border Gateway Protocol)**: The routing protocol of the internet.\r\n\r\n## NAT (Network Address Translation)\r\n\r\nNAT allows multiple devices with private IP addresses to share a single public IP address:\r\n- Your router has one public IP (assigned by your ISP)\r\n- All devices in your home have private IPs (192.168.x.x)\r\n- NAT translates between private and public IPs\r\n\r\n## Practical Task\r\n\r\nUsing Cisco Packet Tracer (free from Cisco), build a network with: 2 routers, 2 switches, and 4 PCs (2 per switch). Configure IP addresses, default gateways, and static routes so all PCs can ping each other. Document your configuration.\r\n\r\n## Self-Check\r\n1. What is the difference between a switch and a router?\r\n2. What is a VLAN and why would you use one?\r\n3. What is NAT and why is it needed?', 3, 1, '2026-02-16 06:11:08'),
(92, 14, 'Wireless Networking', '## Wi-Fi Standards\r\n\r\nWi-Fi standards define the speed and frequency of wireless communication:\r\n\r\n| Standard | Max Speed | Frequency | Also Known As |\r\n|---|---|---|---|\r\n| 802.11b | 11 Mbps | 2.4 GHz | Wi-Fi 1 |\r\n| 802.11g | 54 Mbps | 2.4 GHz | Wi-Fi 3 |\r\n| 802.11n | 600 Mbps | 2.4/5 GHz | Wi-Fi 4 |\r\n| 802.11ac | 3.5 Gbps | 5 GHz | Wi-Fi 5 |\r\n| 802.11ax | 9.6 Gbps | 2.4/5/6 GHz | Wi-Fi 6 |\r\n\r\n## 2.4 GHz vs 5 GHz\r\n\r\n**2.4 GHz:**\r\n- Longer range\r\n- Better penetration through walls\r\n- More interference (microwaves, Bluetooth, neighbours)\r\n- Slower speeds\r\n\r\n**5 GHz:**\r\n- Shorter range\r\n- Less interference\r\n- Faster speeds\r\n- Better for video streaming and gaming\r\n\r\n**6 GHz (Wi-Fi 6E)**: New band, very fast, very short range.\r\n\r\n## Wireless Security\r\n\r\n**WEP (Wired Equivalent Privacy)**: Broken. Never use.\r\n**WPA (Wi-Fi Protected Access)**: Improved but still vulnerable.\r\n**WPA2**: Current standard. Use AES encryption.\r\n**WPA3**: Latest standard. Stronger encryption, better protection against brute force.\r\n\r\n**Best practices:**\r\n- Use WPA3 or WPA2-AES\r\n- Use a strong password (12+ characters, mixed case, numbers, symbols)\r\n- Change default router admin credentials\r\n- Disable WPS\r\n- Create a separate guest network\r\n- Regularly check connected devices\r\n\r\n## Wireless Troubleshooting\r\n\r\n- Weak signal: Move closer to router, add access point, check for interference\r\n- Slow speed: Check for interference, switch to 5 GHz, check ISP speed\r\n- Cannot connect: Check password, restart router, forget and reconnect\r\n- Intermittent drops: Check for interference, update router firmware, check cable connections\r\n\r\n## Practical Task\r\n\r\nConduct a Wi-Fi survey of your home or school. Use a Wi-Fi analyser app (Android: WiFi Analyzer) to: identify all nearby networks, check signal strength in different rooms, identify the least congested channel, and recommend the optimal channel and placement for the router.\r\n\r\n## Self-Check\r\n1. What is the difference between 2.4 GHz and 5 GHz Wi-Fi?\r\n2. Which Wi-Fi security protocol should you use?\r\n3. What is the maximum theoretical speed of Wi-Fi 6?', 4, 1, '2026-02-16 06:11:08'),
(93, 14, 'Network Security & Monitoring', '## Network Security Fundamentals\r\n\r\nNetwork security protects the integrity, confidentiality, and availability of data as it travels across or is stored in a network.\r\n\r\n## Firewall Configuration\r\n\r\nFirewalls control traffic based on rules:\r\n\r\n**Allow rules (whitelist approach):**\r\n- Allow TCP port 443 (HTTPS) from any to web server\r\n- Allow TCP port 22 (SSH) from admin IP only to servers\r\n- Allow TCP port 3306 (MySQL) from web server only to database server\r\n\r\n**Deny rules:**\r\n- Deny all traffic from known malicious IP ranges\r\n- Deny all inbound traffic not matching an allow rule (implicit deny)\r\n\r\n## Network Monitoring Tools\r\n\r\n- **Wireshark**: Capture and analyse network packets\r\n- **Nmap**: Network scanner, discover hosts and open ports\r\n- **Nagios**: Monitor network devices and services, send alerts\r\n- **PRTG**: Network monitoring with dashboards\r\n- **Zabbix**: Open-source monitoring platform\r\n- **ntopng**: Real-time network traffic analysis\r\n\r\n## Common Network Attacks\r\n\r\n- **DDoS**: Overwhelm a server with traffic from many sources\r\n- **ARP Poisoning**: Redirect traffic through the attacker\'s machine\r\n- **DNS Spoofing**: Redirect domain lookups to malicious IPs\r\n- **VLAN Hopping**: Gain access to a VLAN you should not be on\r\n- **Rogue Access Point**: Fake Wi-Fi hotspot to intercept traffic\r\n\r\n## Network Hardening Checklist\r\n\r\n- Change all default passwords on network equipment\r\n- Disable unused ports and services\r\n- Enable port security on switches (limit MAC addresses per port)\r\n- Implement 802.1X authentication for network access\r\n- Use VLANs to segment sensitive systems\r\n- Enable logging on all network devices\r\n- Regularly review firewall rules\r\n- Keep firmware updated\r\n\r\n## Practical Task\r\n\r\nInstall Wireshark and capture 5 minutes of network traffic. Identify: the top 5 protocols by packet count, any unencrypted HTTP traffic, DNS queries being made, and the IP addresses communicating most frequently. Write a brief security assessment of what you observed.\r\n\r\n## Self-Check\r\n1. What is the implicit deny rule in firewall configuration?\r\n2. What is a rogue access point?\r\n3. Name 3 network monitoring tools.', 5, 1, '2026-02-16 06:11:08'),
(94, 14, 'Networking Capstone', '## Final Project: Network Design & Implementation\r\n\r\nYou will design and implement a complete network solution for a fictional organisation.\r\n\r\n## Scenario\r\n\r\nMirror Academy Nigeria is opening a new campus with the following requirements:\r\n- 3 buildings: Admin Block, Classroom Block, Computer Lab\r\n- 50 computers in the Computer Lab\r\n- 20 computers in Admin Block\r\n- Wi-Fi coverage in all buildings and outdoor areas\r\n- Separate networks for staff and students\r\n- Internet connection shared across all buildings\r\n- A file server accessible to all staff\r\n- A web server hosting the school website\r\n\r\n## Deliverables\r\n\r\n### 1. Network Design Document\r\n- IP addressing scheme (subnets for each building and VLAN)\r\n- Equipment list with specifications and estimated costs\r\n- Network topology diagram (logical and physical)\r\n- Security policy (firewall rules, Wi-Fi security, access control)\r\n\r\n### 2. Cisco Packet Tracer Implementation\r\n- Build the complete network in Packet Tracer\r\n- Configure all IP addresses, VLANs, and routing\r\n- Test connectivity between all buildings\r\n- Verify that staff and student networks are isolated\r\n\r\n### 3. Network Documentation\r\n- Network diagram (export from Packet Tracer)\r\n- IP address table (all devices with their IPs)\r\n- VLAN table\r\n- Firewall rule table\r\n- Maintenance schedule\r\n\r\n### 4. Presentation\r\n- 10-minute presentation explaining your design decisions\r\n- Demonstrate the working network in Packet Tracer\r\n- Explain how the design meets each requirement\r\n\r\n## Evaluation Criteria\r\n- Network design quality and scalability (25%)\r\n- IP addressing and subnetting accuracy (20%)\r\n- Security implementation (20%)\r\n- Packet Tracer implementation (25%)\r\n- Documentation quality (10%)\r\n\r\n## Self-Check\r\n1. Does your design meet all the stated requirements?\r\n2. Is your IP addressing scheme logical and scalable?\r\n3. Are staff and student networks properly isolated?', 6, 1, '2026-02-16 06:11:08'),
(95, 15, 'Cloud Computing Fundamentals', '## What is Cloud Computing?\r\n\r\nCloud computing is the delivery of computing services (servers, storage, databases, networking, software, analytics) over the internet (the cloud) on a pay-as-you-go basis.\r\n\r\n## Cloud Service Models\r\n\r\n**IaaS (Infrastructure as a Service)**: Rent virtual machines, storage, and networking. You manage the OS and everything above.\r\nExamples: AWS EC2, Google Compute Engine, Azure Virtual Machines.\r\n\r\n**PaaS (Platform as a Service)**: Rent a platform to build and deploy applications. The provider manages the infrastructure and OS.\r\nExamples: Heroku, Google App Engine, AWS Elastic Beanstalk.\r\n\r\n**SaaS (Software as a Service)**: Use software over the internet. The provider manages everything.\r\nExamples: Gmail, Microsoft 365, Salesforce, Zoom.\r\n\r\n## Cloud Deployment Models\r\n\r\n**Public Cloud**: Resources owned and operated by a third-party provider (AWS, Azure, GCP). Shared infrastructure.\r\n**Private Cloud**: Cloud infrastructure operated solely for one organisation. More control, more expensive.\r\n**Hybrid Cloud**: Combination of public and private cloud. Sensitive data on private, scalable workloads on public.\r\n**Multi-Cloud**: Using services from multiple cloud providers to avoid vendor lock-in.\r\n\r\n## Benefits of Cloud Computing\r\n\r\n- **Cost savings**: No upfront hardware investment. Pay only for what you use.\r\n- **Scalability**: Scale up or down instantly based on demand.\r\n- **Reliability**: Built-in redundancy and disaster recovery.\r\n- **Global reach**: Deploy in data centres worldwide.\r\n- **Security**: Enterprise-grade security managed by the provider.\r\n- **Speed**: Deploy new resources in minutes, not weeks.\r\n\r\n## Major Cloud Providers\r\n\r\n- **AWS (Amazon Web Services)**: Market leader, 200+ services\r\n- **Microsoft Azure**: Strong enterprise integration, Office 365\r\n- **Google Cloud Platform (GCP)**: Strong in AI/ML and data analytics\r\n- **Oracle Cloud**: Strong in databases and enterprise applications\r\n\r\n## Practical Task\r\n\r\nCreate a free AWS account (aws.amazon.com/free). Explore the AWS Management Console. Identify 5 services you would use to host a web application. Write a 1-page comparison of AWS, Azure, and GCP for a Nigerian startup.\r\n\r\n## Self-Check\r\n1. What is the difference between IaaS, PaaS, and SaaS?\r\n2. What is the difference between public and private cloud?\r\n3. Name 3 benefits of cloud computing.', 1, 1, '2026-02-16 06:11:08'),
(96, 15, 'AWS Core Services', '## AWS Global Infrastructure\r\n\r\nAWS operates in Regions (geographic areas) and Availability Zones (isolated data centres within a region).\r\n\r\n- 33 Regions worldwide (as of 2025)\r\n- Each Region has 2-6 Availability Zones\r\n- Choose the Region closest to your users for lowest latency\r\n- Closest to Nigeria: eu-west-1 (Ireland) or af-south-1 (Cape Town)\r\n\r\n## Core AWS Services\r\n\r\n**Compute:**\r\n- **EC2 (Elastic Compute Cloud)**: Virtual machines. Choose instance type (CPU, RAM, storage).\r\n- **Lambda**: Serverless functions. Run code without managing servers. Pay per execution.\r\n- **ECS/EKS**: Container services (Docker, Kubernetes).\r\n\r\n**Storage:**\r\n- **S3 (Simple Storage Service)**: Object storage. Store files, images, backups. 99.999999999% durability.\r\n- **EBS (Elastic Block Store)**: Block storage for EC2 instances (like a hard drive).\r\n- **EFS (Elastic File System)**: Shared file storage for multiple EC2 instances.\r\n\r\n**Database:**\r\n- **RDS (Relational Database Service)**: Managed MySQL, PostgreSQL, SQL Server, Oracle.\r\n- **DynamoDB**: Managed NoSQL database. Millisecond latency at any scale.\r\n- **ElastiCache**: Managed Redis/Memcached for caching.\r\n\r\n**Networking:**\r\n- **VPC (Virtual Private Cloud)**: Your private network in AWS.\r\n- **Route 53**: DNS service and domain registration.\r\n- **CloudFront**: CDN (Content Delivery Network) for fast global content delivery.\r\n- **ELB (Elastic Load Balancer)**: Distribute traffic across multiple EC2 instances.\r\n\r\n**Security:**\r\n- **IAM (Identity and Access Management)**: Control who can access what in AWS.\r\n- **WAF (Web Application Firewall)**: Protect web applications from common attacks.\r\n- **Shield**: DDoS protection.\r\n\r\n## Practical Task\r\n\r\nLaunch a free-tier EC2 instance (t2.micro, Amazon Linux 2). Connect via SSH. Install Apache web server. Create a simple HTML page. Access it via the public IP address. Take a screenshot of your working website.\r\n\r\n## Self-Check\r\n1. What is the difference between EC2 and Lambda?\r\n2. What is S3 used for?\r\n3. What is IAM and why is it important?', 2, 1, '2026-02-16 06:11:08'),
(97, 15, 'Containerisation with Docker', '## What is Docker?\r\n\r\nDocker is a platform for developing, shipping, and running applications in containers. A container packages your application and all its dependencies into a single, portable unit.\r\n\r\n## Why Containers?\r\n\r\n**The problem**: It works on my machine but not on the server.\r\n**The solution**: Containers include everything the app needs to run.\r\n\r\nBenefits:\r\n- Consistent environments (dev, test, production)\r\n- Fast startup (seconds, not minutes)\r\n- Lightweight (share the host OS kernel)\r\n- Portable (run anywhere Docker is installed)\r\n- Scalable (spin up many containers quickly)\r\n\r\n## Docker vs Virtual Machines\r\n\r\n**Virtual Machine**: Includes a full OS. Heavy (GBs). Slow to start (minutes).\r\n**Container**: Shares the host OS kernel. Lightweight (MBs). Fast to start (seconds).\r\n\r\n## Docker Fundamentals\r\n\r\n```bash\r\n# Install Docker Desktop from docker.com\r\n\r\n# Pull an image from Docker Hub\r\ndocker pull nginx\r\ndocker pull mysql:8.0\r\n\r\n# Run a container\r\ndocker run -d -p 8080:80 --name my-nginx nginx\r\n# -d: Run in background (detached)\r\n# -p 8080:80: Map host port 8080 to container port 80\r\n# --name: Give the container a name\r\n\r\n# List running containers\r\ndocker ps\r\n\r\n# Stop and remove a container\r\ndocker stop my-nginx\r\ndocker rm my-nginx\r\n\r\n# View container logs\r\ndocker logs my-nginx\r\n```\r\n\r\n## Dockerfile\r\n\r\nA Dockerfile defines how to build a custom image:\r\n\r\n```dockerfile\r\n# Start from an official PHP image\r\nFROM php:8.2-apache\r\n\r\n# Install extensions\r\nRUN docker-php-ext-install pdo pdo_mysql\r\n\r\n# Copy application files\r\nCOPY . /var/www/html/\r\n\r\n# Set permissions\r\nRUN chown -R www-data:www-data /var/www/html\r\n\r\n# Expose port 80\r\nEXPOSE 80\r\n```\r\n\r\n## Docker Compose\r\n\r\nDocker Compose defines multi-container applications:\r\n\r\n```yaml\r\n# docker-compose.yml\r\nversion: \'3.8\'\r\nservices:\r\n  web:\r\n    build: .\r\n    ports:\r\n      - \'8080:80\'\r\n    depends_on:\r\n      - db\r\n    environment:\r\n      DB_HOST: db\r\n      DB_NAME: lms\r\n\r\n  db:\r\n    image: mysql:8.0\r\n    environment:\r\n      MYSQL_ROOT_PASSWORD: secret\r\n      MYSQL_DATABASE: lms\r\n    volumes:\r\n      - db_data:/var/lib/mysql\r\n\r\nvolumes:\r\n  db_data:\r\n```\r\n\r\n## Practical Task\r\n\r\nContainerise your PHP blog application from the Web Development course. Create a Dockerfile for the PHP app and a docker-compose.yml that includes the PHP app and a MySQL database. Run it locally with docker-compose up. Verify the app works in the container.\r\n\r\n## Self-Check\r\n1. What is the difference between a Docker image and a container?\r\n2. What does the -p flag do in docker run?\r\n3. What is Docker Compose used for?', 3, 1, '2026-02-16 06:11:08'),
(98, 15, 'Cloud Architecture & Best Practices', '## Well-Architected Framework\r\n\r\nAWS Well-Architected Framework defines 6 pillars for building reliable, secure, efficient cloud systems:\r\n\r\n1. **Operational Excellence**: Run and monitor systems to deliver business value\r\n2. **Security**: Protect information, systems, and assets\r\n3. **Reliability**: Recover from failures and meet demand\r\n4. **Performance Efficiency**: Use computing resources efficiently\r\n5. **Cost Optimisation**: Avoid unnecessary costs\r\n6. **Sustainability**: Minimise environmental impact\r\n\r\n## High Availability & Fault Tolerance\r\n\r\n**High Availability**: System remains operational despite component failures.\r\n- Deploy across multiple Availability Zones\r\n- Use load balancers to distribute traffic\r\n- Use auto-scaling to handle demand spikes\r\n\r\n**Fault Tolerance**: System continues operating even when components fail.\r\n- Redundant components (no single point of failure)\r\n- Automatic failover\r\n- Data replication across regions\r\n\r\n## Auto Scaling\r\n\r\nAuto Scaling automatically adjusts the number of EC2 instances based on demand:\r\n- Scale out: Add instances when CPU > 70%\r\n- Scale in: Remove instances when CPU < 30%\r\n- Minimum instances: 2 (always running)\r\n- Maximum instances: 10 (cost cap)\r\n\r\n## Cloud Cost Optimisation\r\n\r\n- **Right-sizing**: Use the smallest instance that meets your needs\r\n- **Reserved Instances**: Commit to 1-3 years for up to 72% discount\r\n- **Spot Instances**: Use spare AWS capacity for up to 90% discount (can be interrupted)\r\n- **Auto Scaling**: Only pay for what you use\r\n- **S3 Lifecycle Policies**: Move old data to cheaper storage tiers\r\n- **Delete unused resources**: Unattached EBS volumes, unused Elastic IPs\r\n\r\n## Infrastructure as Code (IaC)\r\n\r\nDefine your infrastructure in code for repeatability and version control:\r\n\r\n```yaml\r\n# AWS CloudFormation template\r\nAWSTemplateFormatVersion: \'2010-09-09\'\r\nResources:\r\n  WebServer:\r\n    Type: AWS::EC2::Instance\r\n    Properties:\r\n      InstanceType: t3.micro\r\n      ImageId: ami-0c55b159cbfafe1f0\r\n      SecurityGroups:\r\n        - !Ref WebSecurityGroup\r\n```\r\n\r\nOther IaC tools: Terraform (multi-cloud), Ansible (configuration management).\r\n\r\n## Practical Task\r\n\r\nDesign a highly available architecture for a web application on AWS. Draw a diagram showing: VPC with public and private subnets across 2 AZs, Application Load Balancer, Auto Scaling Group with EC2 instances, RDS Multi-AZ database, S3 for static assets, and CloudFront CDN. Estimate the monthly cost using the AWS Pricing Calculator.\r\n\r\n## Self-Check\r\n1. What are the 6 pillars of the AWS Well-Architected Framework?\r\n2. What is the difference between high availability and fault tolerance?\r\n3. What is Infrastructure as Code?', 4, 1, '2026-02-16 06:11:08'),
(99, 15, 'DevOps & CI/CD', '## What is DevOps?\r\n\r\nDevOps is a set of practices that combines software development (Dev) and IT operations (Ops) to shorten the development lifecycle and deliver high-quality software continuously.\r\n\r\n## DevOps Principles\r\n\r\n- **Collaboration**: Dev and Ops teams work together, not in silos\r\n- **Automation**: Automate repetitive tasks (testing, deployment, monitoring)\r\n- **Continuous Improvement**: Measure, learn, and improve constantly\r\n- **Customer Focus**: Deliver value to users quickly and reliably\r\n\r\n## CI/CD Pipeline\r\n\r\n**CI (Continuous Integration)**: Developers merge code frequently. Each merge triggers automated tests.\r\n**CD (Continuous Delivery)**: Code is always in a deployable state. Deploy to production with one click.\r\n**CD (Continuous Deployment)**: Every passing build is automatically deployed to production.\r\n\r\n## GitHub Actions (CI/CD)\r\n\r\n```yaml\r\n# .github/workflows/deploy.yml\r\nname: Deploy to AWS\r\n\r\non:\r\n  push:\r\n    branches: [main]\r\n\r\njobs:\r\n  test:\r\n    runs-on: ubuntu-latest\r\n    steps:\r\n      - uses: actions/checkout@v3\r\n      - name: Run tests\r\n        run: |\r\n          composer install\r\n          php vendor/bin/phpunit\r\n\r\n  deploy:\r\n    needs: test\r\n    runs-on: ubuntu-latest\r\n    steps:\r\n      - uses: actions/checkout@v3\r\n      - name: Deploy to server\r\n        uses: appleboy/ssh-action@master\r\n        with:\r\n          host: ${{ secrets.SERVER_HOST }}\r\n          username: ${{ secrets.SERVER_USER }}\r\n          key: ${{ secrets.SSH_PRIVATE_KEY }}\r\n          script: |\r\n            cd /var/www/html/app\r\n            git pull origin main\r\n            composer install --no-dev\r\n            php artisan migrate --force\r\n```\r\n\r\n## Monitoring & Observability\r\n\r\n**The 3 Pillars of Observability:**\r\n- **Logs**: Detailed records of events (what happened)\r\n- **Metrics**: Numerical measurements over time (CPU, memory, request rate)\r\n- **Traces**: Track a request through multiple services\r\n\r\nTools: AWS CloudWatch, Datadog, Grafana, Prometheus, ELK Stack.\r\n\r\n## Practical Task\r\n\r\nSet up a CI/CD pipeline for your PHP application using GitHub Actions. The pipeline should: run PHPUnit tests on every push, deploy to an EC2 instance if tests pass, send a Slack notification on success or failure. Document the pipeline and test it with a code change.\r\n\r\n## Self-Check\r\n1. What is the difference between CI and CD?\r\n2. What are the 3 pillars of observability?\r\n3. What triggers a GitHub Actions workflow?', 5, 1, '2026-02-16 06:11:08'),
(100, 15, 'Cloud Computing Capstone', '## Final Project: Cloud Architecture & Deployment\r\n\r\nYou will design, build, and deploy a complete cloud-based application on AWS.\r\n\r\n## Project Brief\r\n\r\nDeploy the Mirror LMS application (or a similar web application) to AWS with a production-grade architecture.\r\n\r\n## Architecture Requirements\r\n\r\n### Infrastructure\r\n- VPC with public and private subnets across 2 Availability Zones\r\n- Application Load Balancer in the public subnet\r\n- EC2 instances in an Auto Scaling Group in private subnets\r\n- RDS MySQL in a private subnet (Multi-AZ for high availability)\r\n- S3 bucket for file uploads and static assets\r\n- CloudFront distribution for global content delivery\r\n- Route 53 for DNS management\r\n\r\n### Security\r\n- IAM roles with least privilege for all services\r\n- Security groups allowing only necessary traffic\r\n- RDS not publicly accessible\r\n- HTTPS only (SSL certificate via AWS Certificate Manager)\r\n- S3 bucket not publicly accessible (accessed via CloudFront)\r\n\r\n### DevOps\r\n- GitHub repository for the application code\r\n- GitHub Actions CI/CD pipeline\r\n- Automated deployment on push to main branch\r\n- CloudWatch alarms for CPU, memory, and error rate\r\n\r\n## Deliverables\r\n\r\n1. Architecture diagram (AWS icons, all components labelled)\r\n2. Terraform or CloudFormation template for the infrastructure\r\n3. GitHub Actions workflow file\r\n4. Working application accessible via a domain name\r\n5. CloudWatch dashboard screenshot\r\n6. Cost estimate (AWS Pricing Calculator)\r\n7. 10-minute presentation explaining architecture decisions\r\n\r\n## Evaluation Criteria\r\n- Architecture design quality (25%)\r\n- Security implementation (20%)\r\n- Working deployment (25%)\r\n- CI/CD pipeline (15%)\r\n- Documentation and presentation (15%)\r\n\r\n## Self-Check\r\n1. Is your application accessible via HTTPS?\r\n2. Is your database in a private subnet?\r\n3. Does your CI/CD pipeline run tests before deploying?', 6, 1, '2026-02-16 06:11:08'),
(101, 16, 'Software Engineering Principles', '## What is Software Engineering?\r\n\r\nSoftware engineering is the systematic application of engineering principles to the design, development, testing, and maintenance of software. It goes beyond coding — it is about building reliable, maintainable, and scalable systems.\r\n\r\n## Software Development Life Cycle (SDLC)\r\n\r\n1. **Planning**: Define scope, timeline, budget, and feasibility\r\n2. **Requirements Analysis**: Gather and document what the system must do\r\n3. **System Design**: Architecture, database design, UI design\r\n4. **Implementation**: Write the code\r\n5. **Testing**: Verify the system works correctly\r\n6. **Deployment**: Release to production\r\n7. **Maintenance**: Fix bugs, add features, optimise performance\r\n\r\n## SDLC Models\r\n\r\n**Waterfall**: Sequential phases. Each phase must complete before the next begins. Good for well-defined, stable requirements.\r\n\r\n**Agile**: Iterative development in short sprints (1-4 weeks). Deliver working software frequently. Adapt to changing requirements.\r\n\r\n**Scrum**: Agile framework with defined roles (Product Owner, Scrum Master, Development Team) and ceremonies (Sprint Planning, Daily Standup, Sprint Review, Retrospective).\r\n\r\n**Kanban**: Visual workflow management. Work items move through columns (To Do, In Progress, Done). No fixed sprints.\r\n\r\n## Software Quality Attributes\r\n\r\n- **Functionality**: Does it do what it is supposed to do?\r\n- **Reliability**: Does it work consistently without failures?\r\n- **Usability**: Is it easy to use?\r\n- **Efficiency**: Does it use resources (CPU, memory) efficiently?\r\n- **Maintainability**: Is it easy to modify and extend?\r\n- **Portability**: Can it run on different platforms?\r\n- **Security**: Is it protected against threats?\r\n\r\n## Practical Task\r\n\r\nChoose a software project you want to build. Write a complete project plan including: problem statement, target users, functional requirements (what it does), non-functional requirements (performance, security, usability), SDLC model choice with justification, and a 3-month timeline with milestones.\r\n\r\n## Self-Check\r\n1. What are the 7 phases of the SDLC?\r\n2. What is the difference between Waterfall and Agile?\r\n3. What are the 7 software quality attributes?', 1, 1, '2026-02-16 06:11:08'),
(102, 16, 'Software Architecture & Design Patterns', '## What is Software Architecture?\r\n\r\nSoftware architecture is the high-level structure of a software system — the major components, their relationships, and the principles governing their design and evolution.\r\n\r\n## Architectural Patterns\r\n\r\n**Monolithic Architecture**: All components in a single deployable unit.\r\n- Pros: Simple to develop and deploy initially\r\n- Cons: Hard to scale, hard to maintain as it grows\r\n\r\n**Microservices Architecture**: Application split into small, independent services.\r\n- Pros: Independent scaling, independent deployment, technology flexibility\r\n- Cons: Complex to manage, network overhead, distributed system challenges\r\n\r\n**MVC (Model-View-Controller)**: Separates application into 3 components.\r\n- Model: Data and business logic\r\n- View: User interface\r\n- Controller: Handles user input, coordinates Model and View\r\n\r\n**Event-Driven Architecture**: Components communicate through events.\r\n- Producer publishes an event\r\n- Consumer subscribes to events\r\n- Decoupled, scalable, asynchronous\r\n\r\n## SOLID Principles\r\n\r\n**S - Single Responsibility**: A class should have only one reason to change.\r\n**O - Open/Closed**: Open for extension, closed for modification.\r\n**L - Liskov Substitution**: Subclasses should be substitutable for their base class.\r\n**I - Interface Segregation**: Many specific interfaces are better than one general interface.\r\n**D - Dependency Inversion**: Depend on abstractions, not concretions.\r\n\r\n## Design Patterns\r\n\r\n**Creational Patterns:**\r\n- Singleton: Ensure only one instance of a class exists\r\n- Factory: Create objects without specifying the exact class\r\n- Builder: Construct complex objects step by step\r\n\r\n**Structural Patterns:**\r\n- Adapter: Make incompatible interfaces work together\r\n- Decorator: Add behaviour to objects dynamically\r\n- Repository: Abstract data access logic\r\n\r\n**Behavioural Patterns:**\r\n- Observer: Notify multiple objects when state changes\r\n- Strategy: Define a family of algorithms and make them interchangeable\r\n- Command: Encapsulate a request as an object\r\n\r\n## Practical Task\r\n\r\nRefactor a simple PHP application to use the MVC pattern. Separate: database queries (Model), HTML templates (View), and request handling (Controller). Apply the Repository pattern for data access. Apply at least 2 SOLID principles.\r\n\r\n## Self-Check\r\n1. What is the difference between monolithic and microservices architecture?\r\n2. What does SOLID stand for?\r\n3. What is the Repository pattern?', 2, 1, '2026-02-16 06:11:08'),
(103, 16, 'Agile & Scrum in Practice', '## Agile Manifesto\r\n\r\nThe Agile Manifesto (2001) values:\r\n- Individuals and interactions over processes and tools\r\n- Working software over comprehensive documentation\r\n- Customer collaboration over contract negotiation\r\n- Responding to change over following a plan\r\n\r\n## Scrum Framework\r\n\r\n**Roles:**\r\n- **Product Owner**: Represents the customer. Owns the Product Backlog. Prioritises features.\r\n- **Scrum Master**: Facilitates the process. Removes impediments. Coaches the team.\r\n- **Development Team**: Self-organising, cross-functional. Typically 3-9 people.\r\n\r\n**Artefacts:**\r\n- **Product Backlog**: Ordered list of everything that might be needed in the product\r\n- **Sprint Backlog**: Items selected for the current sprint\r\n- **Increment**: The working software produced at the end of each sprint\r\n\r\n**Events:**\r\n- **Sprint**: Time-boxed iteration (1-4 weeks). Fixed duration.\r\n- **Sprint Planning**: Team selects items from Product Backlog for the sprint\r\n- **Daily Scrum (Standup)**: 15-minute daily sync. What did I do yesterday? What will I do today? Any blockers?\r\n- **Sprint Review**: Demo working software to stakeholders\r\n- **Sprint Retrospective**: What went well? What could improve? What will we change?\r\n\r\n## User Stories\r\n\r\nUser stories describe features from the user\'s perspective:\r\n\r\nAs a [type of user], I want [some goal] so that [some reason].\r\n\r\nExample: As a student, I want to see my assignment due dates on the dashboard so that I never miss a submission.\r\n\r\n**Acceptance Criteria**: Conditions that must be met for the story to be considered done.\r\n- Given [context], When [action], Then [outcome]\r\n\r\n**Story Points**: Relative measure of effort (1, 2, 3, 5, 8, 13, 21 — Fibonacci sequence).\r\n\r\n## Kanban\r\n\r\nKanban visualises work on a board:\r\n- Columns: Backlog | To Do | In Progress | Review | Done\r\n- WIP (Work in Progress) limits: Limit items in each column to prevent overload\r\n- Lead time: Time from request to delivery\r\n- Cycle time: Time from start to completion\r\n\r\n## Tools\r\n\r\n- Jira: Most popular Agile project management tool\r\n- Trello: Simple Kanban boards\r\n- Linear: Modern, fast issue tracker\r\n- GitHub Projects: Integrated with GitHub\r\n- Notion: Flexible workspace\r\n\r\n## Practical Task\r\n\r\nSet up a Trello board for your capstone project. Create columns: Backlog, To Do, In Progress, Review, Done. Write 10 user stories for your project. Estimate story points for each. Plan a 2-week sprint by selecting stories that fit your capacity.\r\n\r\n## Self-Check\r\n1. What are the 3 Scrum roles?\r\n2. What is the purpose of the Daily Scrum?\r\n3. What is a user story and what format does it follow?', 3, 1, '2026-02-16 06:11:08');
INSERT INTO `lms_lessons` (`id`, `course_id`, `title`, `content`, `sort_order`, `is_published`, `created_at`) VALUES
(104, 16, 'Testing & Quality Assurance', '## Why Testing?\r\n\r\nSoftware testing verifies that a system works as expected and meets requirements. The cost of fixing a bug increases dramatically the later it is found:\r\n- In development: 1x cost\r\n- In testing: 10x cost\r\n- In production: 100x cost\r\n\r\n## Testing Types\r\n\r\n**Unit Testing**: Test individual functions or methods in isolation.\r\n**Integration Testing**: Test how components work together.\r\n**System Testing**: Test the complete system end-to-end.\r\n**Acceptance Testing (UAT)**: Users verify the system meets their requirements.\r\n**Regression Testing**: Verify that new changes have not broken existing functionality.\r\n**Performance Testing**: Test system behaviour under load.\r\n**Security Testing**: Test for vulnerabilities.\r\n\r\n## Test-Driven Development (TDD)\r\n\r\nTDD is a development approach where you write tests before writing code:\r\n1. Write a failing test (Red)\r\n2. Write the minimum code to make the test pass (Green)\r\n3. Refactor the code while keeping tests passing (Refactor)\r\n\r\nBenefits: Forces clear requirements, produces testable code, provides a safety net for refactoring.\r\n\r\n## PHPUnit Example\r\n\r\n```php\r\nclass PaymentCalculatorTest extends TestCase {\r\n    private PaymentCalculator $calc;\r\n\r\n    protected function setUp(): void {\r\n        $this->calc = new PaymentCalculator();\r\n    }\r\n\r\n    public function testFullPaymentReturnsCorrectAmount(): void {\r\n        $result = $this->calc->calculate(150000, \'full\');\r\n        $this->assertEquals(150000, $result[\'amount\']);\r\n        $this->assertEquals(\'full\', $result[\'type\']);\r\n    }\r\n\r\n    public function testInstallmentReturnsHalfAmount(): void {\r\n        $result = $this->calc->calculate(150000, \'installment\');\r\n        $this->assertEquals(75000, $result[\'amount\']);\r\n        $this->assertEquals(\'installment\', $result[\'type\']);\r\n    }\r\n\r\n    public function testZeroPriceThrowsException(): void {\r\n        $this->expectException(InvalidArgumentException::class);\r\n        $this->calc->calculate(0, \'full\');\r\n    }\r\n}\r\n```\r\n\r\n## Code Coverage\r\n\r\nCode coverage measures what percentage of your code is executed by tests:\r\n- Line coverage: % of lines executed\r\n- Branch coverage: % of branches (if/else) executed\r\n- Function coverage: % of functions called\r\n\r\nTarget: 80%+ coverage for critical business logic.\r\n\r\n## Practical Task\r\n\r\nWrite a test suite for the payment module of the LMS application. Cover: full payment calculation, installment calculation, payment verification, enrollment status update after payment, and edge cases (zero amount, negative amount, already paid). Achieve 90%+ code coverage.\r\n\r\n## Self-Check\r\n1. What is the difference between unit testing and integration testing?\r\n2. What are the 3 steps of TDD (Red-Green-Refactor)?\r\n3. What is code coverage?', 4, 1, '2026-02-16 06:11:08'),
(105, 16, 'System Design & Scalability', '## System Design Fundamentals\r\n\r\nSystem design is the process of defining the architecture, components, modules, interfaces, and data for a system to satisfy specified requirements.\r\n\r\n## Scalability\r\n\r\n**Vertical Scaling (Scale Up)**: Add more resources to a single server (more CPU, RAM, storage). Simple but has limits.\r\n\r\n**Horizontal Scaling (Scale Out)**: Add more servers. More complex but virtually unlimited.\r\n\r\n## Load Balancing\r\n\r\nDistributes incoming traffic across multiple servers:\r\n- **Round Robin**: Each server gets requests in turn\r\n- **Least Connections**: Send to the server with fewest active connections\r\n- **IP Hash**: Same client always goes to the same server (session persistence)\r\n\r\n## Caching\r\n\r\nCaching stores frequently accessed data in fast memory to reduce database load:\r\n\r\n**Application Cache (Redis/Memcached):**\r\n```php\r\n$redis = new Redis();\r\n$redis->connect(\'127.0.0.1\', 6379);\r\n\r\n// Cache a database query result for 1 hour\r\n$key = \'courses:all\';\r\n$courses = $redis->get($key);\r\nif (!$courses) {\r\n    $courses = $pdo->query(\'SELECT * FROM lms_courses\')->fetchAll();\r\n    $redis->setex($key, 3600, serialize($courses));\r\n} else {\r\n    $courses = unserialize($courses);\r\n}\r\n```\r\n\r\n**CDN (Content Delivery Network)**: Cache static assets (images, CSS, JS) on servers worldwide.\r\n\r\n## Database Scaling\r\n\r\n**Read Replicas**: One primary database handles writes. Multiple replicas handle reads.\r\n**Sharding**: Split data across multiple databases (e.g. users A-M on DB1, N-Z on DB2).\r\n**Connection Pooling**: Reuse database connections instead of creating new ones for each request.\r\n\r\n## Message Queues\r\n\r\nDecouple components and handle asynchronous tasks:\r\n- User registers -> Add to queue -> Send welcome email asynchronously\r\n- Tools: RabbitMQ, AWS SQS, Redis Queue\r\n\r\n## Designing for 1 Million Users\r\n\r\n1. Start with a single server\r\n2. Add a database server\r\n3. Add a load balancer + multiple web servers\r\n4. Add caching (Redis)\r\n5. Add a CDN for static assets\r\n6. Add read replicas for the database\r\n7. Add a message queue for async tasks\r\n8. Consider microservices for independent scaling\r\n\r\n## Practical Task\r\n\r\nDesign the system architecture for a Nigerian ride-hailing app (like Bolt or Uber). Consider: user registration and authentication, real-time driver location tracking, ride matching algorithm, payment processing, notifications, and rating system. Draw the architecture diagram and explain your technology choices.\r\n\r\n## Self-Check\r\n1. What is the difference between vertical and horizontal scaling?\r\n2. What is caching and why does it improve performance?\r\n3. What is a message queue and when would you use one?', 5, 1, '2026-02-16 06:11:08'),
(106, 16, 'Capstone: Software Engineering Project', '## Final Project Brief\r\n\r\nYou will design and build a complete software system applying all software engineering principles from this course.\r\n\r\n## Project: Build a SaaS Product\r\n\r\nDesign and build a Software as a Service (SaaS) product for a Nigerian market problem of your choice.\r\n\r\n## Examples\r\n\r\n- School management system for Nigerian secondary schools\r\n- Inventory and invoicing system for Nigerian SMEs\r\n- Telemedicine platform connecting patients with doctors\r\n- Agricultural marketplace connecting farmers with buyers\r\n- HR and payroll system for small businesses\r\n\r\n## Phase 1: Requirements & Design (Week 1-2)\r\n\r\n**Requirements Document:**\r\n- Problem statement and target market\r\n- User personas (2-3)\r\n- Functional requirements (user stories with acceptance criteria)\r\n- Non-functional requirements (performance, security, scalability)\r\n- Out of scope (what you will NOT build)\r\n\r\n**Technical Design:**\r\n- System architecture diagram\r\n- Database schema (ERD)\r\n- API design (endpoints, request/response format)\r\n- UI wireframes (key screens)\r\n\r\n## Phase 2: Development (Week 3-6)\r\n\r\n- Set up Git repository with branching strategy\r\n- Implement features in sprints (2-week sprints)\r\n- Write unit tests for all business logic\r\n- Code review for every pull request\r\n- Daily standups (even if solo — write a daily log)\r\n\r\n## Phase 3: Testing & Deployment (Week 7-8)\r\n\r\n- Complete test suite (80%+ coverage)\r\n- Performance testing (handle 100 concurrent users)\r\n- Security audit (OWASP Top 10 checklist)\r\n- Deploy to AWS or similar cloud platform\r\n- Set up monitoring and alerting\r\n\r\n## Deliverables\r\n\r\n1. Requirements document\r\n2. Technical design document with architecture diagram and ERD\r\n3. GitHub repository with clean commit history\r\n4. Working application deployed to the cloud\r\n5. Test suite with coverage report\r\n6. 15-minute demo presentation\r\n7. Post-mortem: What went well, what you would do differently\r\n\r\n## Evaluation Criteria\r\n- Requirements quality and completeness (15%)\r\n- Architecture and design quality (20%)\r\n- Code quality and test coverage (25%)\r\n- Working product functionality (25%)\r\n- Deployment and DevOps (10%)\r\n- Presentation and documentation (5%)\r\n\r\n## Self-Check\r\n1. Does your architecture handle the expected load?\r\n2. Is your code covered by tests?\r\n3. Would you be comfortable showing this to a potential employer?', 6, 1, '2026-02-16 06:11:08'),
(107, 2, 'Advanced Typography & Type Systems', 'Advanced typography creates consistent, scalable type hierarchies across all brand touchpoints.\\n\\n## Type Scale\\nA type scale defines the sizes used in a design system. Use tools like typescale.com to generate scales based on ratios like Major Third (1.25x) or Perfect Fourth (1.333x).\\n\\n## Variable Fonts\\nVariable fonts contain multiple styles in a single file, reducing file size and enabling smooth weight animations. Examples: Inter, Roboto Flex.\\n\\n## Kerning & Tracking\\n- Tracking: Uniform spacing between all characters\\n- Kerning: Adjusting space between specific pairs (AV, WA)\\n- Leading: Vertical space between lines\\n\\n## Advanced Layout\\n- Drop caps: Large initial letter at paragraph start\\n- Pull quotes: Highlighted quotes from body text\\n- Hanging punctuation: Quotation marks outside the text margin\\n- Baseline grid: All text aligns to consistent vertical rhythm\\n\\n## Practical Task\\nCreate a 4-page editorial layout in InDesign. Establish a type scale, apply a baseline grid, and use at least 3 levels of typographic hierarchy. Include a pull quote and drop cap.\\n\\n## Self-Check\\n1. What is the difference between tracking and kerning?\\n2. What is a variable font and what are its advantages?\\n3. How does a type scale improve design consistency?', 1, 1, '2026-02-16 06:11:08'),
(108, 2, 'Advanced Colour & Brand Systems', 'A colour system defines how colour is used consistently across all brand touchpoints.\\n\\n## Colour Tokens\\nColour tokens are named variables:\\n- Primary: Main brand colour (buttons, links)\\n- Secondary: Supporting colour (accents)\\n- Neutral: Greys for text, backgrounds, borders\\n- Semantic: success=green, error=red, warning=amber, info=blue\\n\\n## Colour Accessibility\\nWCAG AA standard: 4.5:1 for normal text, 3:1 for large text.\\nTools: WebAIM Contrast Checker, Figma Contrast plugin.\\n\\n## Dark Mode\\nDark mode is not simply inverting colours:\\n- Use dark grey (not pure black): #121212 or #1E1E1E\\n- Reduce saturation in dark mode\\n- Maintain the same contrast ratios\\n- Test all semantic colours in both modes\\n\\n## Data Visualisation Colour\\n- Sequential palettes: ordered data (light to dark)\\n- Diverging palettes: data with a meaningful midpoint\\n- Categorical palettes: unordered groups\\n- Always provide non-colour indicators for accessibility\\n\\n## Practical Task\\nCreate a complete colour system for a fictional SaaS product. Define primary, secondary, neutral, and semantic tokens. Test all combinations for WCAG AA compliance.\\n\\n## Self-Check\\n1. What is the WCAG AA contrast ratio for normal text?\\n2. What are colour tokens and why are they useful?\\n3. Why is dark mode not simply an inversion of light mode?', 2, 1, '2026-02-16 06:11:08'),
(109, 2, 'Advanced Layout & Grid Systems', 'A modular grid divides the page into both columns and rows, creating a matrix of modules for maximum layout flexibility.\\n\\n## The 12-Column Grid\\nThe 12-column grid is the most widely used because 12 is divisible by 2, 3, 4, and 6. Bootstrap, Material Design, and most CSS frameworks use it.\\n\\n## Gestalt Principles\\n- Figure/Ground: Distinguishing object from background\\n- Similarity: Similar elements are perceived as a group\\n- Continuation: The eye follows lines and curves\\n- Closure: The mind completes incomplete shapes\\n- Common Fate: Elements moving together are grouped\\n\\n## Editorial Layout Techniques\\n- Z-pattern: Eye moves in a Z shape (text-heavy pages)\\n- F-pattern: Eye scans horizontally then vertically (web content)\\n- Golden ratio: 1:1.618 proportion creates pleasing layouts\\n- Rule of thirds: Place key elements at grid intersections\\n\\n## Responsive Layout\\n- Mobile: 320-767px  single column\\n- Tablet: 768-1023px  2-column layouts\\n- Desktop: 1024px+  full multi-column layouts\\n\\n## Practical Task\\nDesign a magazine cover and 2-page spread using a modular grid. Apply the rule of thirds for the cover image. Use the F-pattern for the article layout.\\n\\n## Self-Check\\n1. Why is the 12-column grid so widely used?\\n2. Name 3 Gestalt principles and explain how they affect layout.\\n3. What is the difference between Z-pattern and F-pattern layouts?', 3, 1, '2026-02-16 06:11:08'),
(110, 2, 'Motion Graphics & Animation', 'Motion design adds the dimension of time to graphic design, used in title sequences, explainer videos, social media animations, and UI transitions.\\n\\n## 12 Principles of Animation (Disney)\\n1. Squash and Stretch: Gives weight and flexibility\\n2. Anticipation: Small movement before the main action\\n3. Staging: Presenting an idea clearly\\n4. Slow In and Slow Out (Easing): Gradual acceleration/deceleration\\n5. Arc: Natural movements follow curved paths\\n6. Secondary Action: Supporting actions that add richness\\n7. Timing: Number of frames determines speed\\n8. Exaggeration: Amplifying actions for effect\\n9. Appeal: Designs that are engaging and charismatic\\n\\n## Adobe After Effects Basics\\n- Composition: The canvas where animation happens\\n- Timeline: Where keyframes are placed\\n- Keyframes: Points in time defining a property value\\n- Easing: Controlling acceleration (Easy Ease = natural movement)\\n\\n## Easing Functions\\n- Linear: Constant speed  feels mechanical\\n- Ease In: Starts slow, ends fast\\n- Ease Out: Starts fast, ends slow (most natural for UI)\\n- Ease In-Out: Slow start and end  best for most animations\\n\\n## Practical Task\\nCreate a 10-second animated logo reveal in After Effects. Apply at least 3 animation principles. Export as MP4 and GIF.\\n\\n## Self-Check\\n1. What is easing and why does it make animations feel natural?\\n2. Name 5 of the 12 principles of animation.\\n3. What is the difference between Ease In and Ease Out?', 4, 1, '2026-02-16 06:11:08'),
(111, 2, 'Advanced Branding & Strategy', 'Brand strategy is the long-term plan for developing a brand to achieve specific goals. Strategy comes first; identity follows.\\n\\n## Brand Architecture\\n- Monolithic (Branded House): One master brand for everything (Apple, Google)\\n- Endorsed: Sub-brands endorsed by the parent (Marriott Courtyard)\\n- Pluralistic (House of Brands): Independent brands under one company (P&G)\\n\\n## Brand Positioning\\nPositioning defines where your brand sits in the market:\\n\\nFor [target audience], [brand name] is the [category] that [key benefit] because [reason to believe].\\n\\nExample: For young professionals, Slack is the team communication tool that reduces email overload because it organises conversations by channel.\\n\\n## Rebranding\\nWhen to rebrand:\\n- Mergers and acquisitions\\n- Significant shift in target audience\\n- Outdated visual identity\\n- Reputation management\\n- Expansion into new markets\\n\\nRebranding risks: alienating existing customers, losing brand equity. Always research before rebranding.\\n\\n## Practical Task\\nConduct a brand audit for a real or fictional company. Analyse their current brand identity, positioning, and consistency across touchpoints. Write a 1-page brand strategy recommendation.\\n\\n## Self-Check\\n1. What is the difference between brand strategy and brand identity?\\n2. What are the three types of brand architecture?\\n3. Write a positioning statement for a fictional brand.', 5, 1, '2026-02-16 06:11:08'),
(112, 2, 'Freelancing & Design Business', 'Freelancing gives you creative freedom and flexibility, but requires business skills alongside design skills.\\n\\n## Setting Up Your Business\\n1. Register your business (sole trader or limited company)\\n2. Open a business bank account\\n3. Get professional indemnity insurance\\n4. Set up accounting (Wave, FreshBooks, or QuickBooks)\\n5. Create contracts  never work without a signed contract\\n\\n## Finding Clients\\n- Warm outreach: Friends, family, former colleagues\\n- Cold outreach: Research target companies, send personalised emails\\n- Social media: LinkedIn, Instagram  share your work consistently\\n- Freelance platforms: Upwork, Fiverr, 99designs\\n- Referrals: Your best clients come from happy existing clients\\n\\n## Pricing Models\\n- Project-based: Defined scope, most common\\n- Hourly: Undefined scope, ongoing work\\n- Retainer: Ongoing relationship, predictable income\\n- Value-based: High-impact projects, experienced designers\\n\\n## The Client Contract Must Include\\n- Scope of work (exactly what is included)\\n- Timeline and milestones\\n- Payment terms (deposit, instalments, final payment)\\n- Number of revision rounds\\n- Kill fee clause\\n- Intellectual property ownership\\n\\n## Practical Task\\nWrite a project proposal and contract for a fictional logo design project. Include all contract elements. Set a project price using the project-based model.\\n\\n## Self-Check\\n1. What is professional indemnity insurance?\\n2. What must a client contract include?\\n3. What is the difference between project-based and value-based pricing?', 6, 1, '2026-02-16 06:11:08'),
(113, 2, 'Packaging & Environmental Design', 'Packaging design is where graphic design meets the physical world. It must be functional, protective, and visually compelling.\\n\\n## Packaging Design Considerations\\n- Structural: Shape, material, opening mechanism, stackability\\n- Visual: Brand identity, typography, colour, imagery\\n- Regulatory: Ingredients, nutritional info, barcodes, legal text\\n- Sustainability: Recyclable materials, minimal waste, eco-friendly inks\\n\\n## Dieline\\nA dieline is the flat, unfolded template of a package. Designers work on the dieline, which is then folded into the 3D package.\\n\\n## Label Design\\n- Front panel: Brand name, product name, key visual\\n- Back panel: Ingredients, instructions, legal text, barcode\\n- Neck label (bottles): Brand mark or tagline\\n\\n## Environmental & Wayfinding Design\\n- Wayfinding: Signs, maps, and directional systems (airports, hospitals)\\n- Retail design: In-store graphics, window displays, point-of-sale\\n- Exhibition design: Trade show booths, museum displays\\n- Murals & supergraphics: Large-scale wall art\\n\\n## Practical Task\\nDesign packaging for a fictional artisan coffee brand. Create the dieline for a 250g coffee bag. Design all panels: front, back, side gussets. Include brand identity, product information, and a sustainability message.\\n\\n## Self-Check\\n1. What is a dieline and how is it used in packaging design?\\n2. What information is legally required on food packaging?\\n3. What is wayfinding design? Give two real-world examples.', 7, 1, '2026-02-16 06:11:08'),
(114, 2, 'Capstone: Advanced Brand Campaign', 'You will create a comprehensive brand campaign for a fictional company undergoing a rebrand.\\n\\n## Scenario\\nApex Digital, a 10-year-old Nigerian tech company, is rebranding to appeal to a younger, pan-African audience. They offer software solutions for SMEs.\\n\\n## Deliverables\\n\\n### 1. Brand Audit\\n- Analysis of the existing brand\\n- Competitor landscape (3 competitors)\\n- Target audience personas (2 personas)\\n- Positioning statement\\n\\n### 2. New Visual Identity\\n- Logo suite (primary, secondary, icon)\\n- Colour system (tokens with HEX, RGB, CMYK)\\n- Typography system\\n- Photography/illustration direction\\n\\n### 3. Campaign Materials\\n- Billboard design (3000 x 1000px)\\n- Social media campaign (5 posts)\\n- Email newsletter header\\n- App icon and splash screen\\n- Branded merchandise mockup\\n\\n### 4. Brand Guidelines (PDF, minimum 20 pages)\\n- Full brand story and values\\n- Logo usage rules\\n- Colour and typography specifications\\n- Tone of voice with examples\\n- Incorrect usage examples\\n\\n## Evaluation Criteria\\n- Strategic thinking and rationale (20%)\\n- Visual identity strength (25%)\\n- Campaign consistency and creativity (25%)\\n- Production quality (20%)\\n- Brand guidelines completeness (10%)\\n\\n## Self-Check\\n1. Does every deliverable feel like it belongs to the same brand?\\n2. Is your brand strategy clearly reflected in the visual choices?\\n3. Could another designer maintain this brand using your guidelines?', 8, 1, '2026-02-16 06:11:08'),
(115, 17, 'Introduction to Data Science', '## What is Data Science?\n\nData science is an interdisciplinary field that uses scientific methods, processes, algorithms, and systems to extract knowledge and insights from structured and unstructured data.\n\n## The Data Science Lifecycle\n\n1. Problem Definition: What business question are we answering?\n2. Data Collection: Gather data from databases, APIs, web scraping, surveys\n3. Data Cleaning: Handle missing values, outliers, duplicates, inconsistencies\n4. Exploratory Data Analysis (EDA): Understand distributions, correlations, patterns\n5. Feature Engineering: Create and select the most predictive variables\n6. Modelling: Apply statistical or machine learning models\n7. Evaluation: Measure model performance with appropriate metrics\n8. Deployment: Serve the model in production\n9. Monitoring: Track model performance over time\n\n## Data Science vs Related Fields\n\n- Data Analysis: Focuses on describing and interpreting existing data\n- Data Engineering: Builds pipelines and infrastructure for data\n- Machine Learning: Algorithms that learn patterns from data\n- Data Science: Combines all three with domain expertise\n\n## Essential Tools\n\n- Python: Primary language (pandas, numpy, scikit-learn, matplotlib)\n- R: Statistical computing and visualisation\n- SQL: Query and manipulate relational databases\n- Jupyter Notebook: Interactive development environment\n- Git: Version control for code and experiments\n\n## Practical Task\n\nInstall Anaconda (anaconda.com). Create a new Jupyter Notebook. Import pandas and numpy. Load a CSV file from Kaggle. Display the first 10 rows, check data types, and count missing values.\n\n## Self-Check\n1. What are the 9 stages of the data science lifecycle?\n2. What is the difference between data science and data analysis?\n3. What Python libraries are essential for data science?', 1, 1, '2026-04-15 07:00:00'),
(116, 17, 'Python for Data Science', '## NumPy and pandas Fundamentals\n\n```python\nimport numpy as np\nimport pandas as pd\n\n# NumPy arrays\narr = np.array([1, 2, 3, 4, 5])\nprint(arr.mean(), arr.std())  # 3.0, 1.41\n\n# pandas DataFrame\ndf = pd.read_csv(\'students.csv\')\nprint(df.shape)           # (rows, cols)\nprint(df.describe())      # Statistical summary\nprint(df.isnull().sum())  # Missing values\n\n# Filter and aggregate\ndf[df[\'score\'] > 70].groupby(\'course\')[\'score\'].agg([\'mean\',\'count\'])\n\n# Clean\ndf.dropna(subset=[\'score\'])\ndf[\'score\'].fillna(df[\'score\'].mean())\ndf.drop_duplicates(subset=[\'email\'])\n```\n\n## Visualisation\n\n```python\nimport matplotlib.pyplot as plt\nimport seaborn as sns\n\nsns.histplot(df[\'score\'], bins=20, kde=True)\nsns.heatmap(df.corr(), annot=True, cmap=\'coolwarm\')\nsns.scatterplot(data=df, x=\'study_hours\', y=\'score\', hue=\'grade\')\n```\n\n## Practical Task\n\nDownload a Nigerian Students Performance dataset from Kaggle. Load it into pandas. Perform: (1) data cleaning, (2) descriptive statistics, (3) correlation analysis, (4) 3 visualisations. Write a 1-paragraph insight summary.\n\n## Self-Check\n1. What is the difference between a NumPy array and a pandas DataFrame?\n2. How do you handle missing values in pandas?\n3. What chart type shows correlation between two variables?', 2, 1, '2026-04-15 07:00:00'),
(117, 17, 'Statistical Analysis & Probability', '## Descriptive Statistics\n\nMeasures of Central Tendency: Mean (sensitive to outliers), Median (robust), Mode (most frequent)\nMeasures of Spread: Variance, Standard Deviation, IQR, Range\nShape: Skewness (asymmetry), Kurtosis (tail heaviness)\n\n## Probability Fundamentals\n\n- P(A): Probability of event A (0 to 1)\n- P(A|B): Conditional probability\n- Bayes Theorem: P(A|B) = P(B|A) * P(A) / P(B)\n\n## Key Distributions\n\n- Normal: Bell curve. 68-95-99.7 rule.\n- Binomial: Number of successes in n trials\n- Poisson: Number of events in a fixed interval\n\n## Hypothesis Testing\n\n```python\nfrom scipy import stats\n\n# t-test: compare two group means\nt_stat, p_value = stats.ttest_ind(group_a, group_b)\nprint(f\'t={t_stat:.3f}, p={p_value:.4f}\')\n# If p < 0.05: reject null hypothesis\n\n# Correlation\nr, p = stats.pearsonr(df[\'study_hours\'], df[\'score\'])\nprint(f\'r={r:.3f}, p={p:.4f}\')\n```\n\nCommon tests: t-test (compare means), Chi-square (categorical independence), ANOVA (3+ groups), Pearson/Spearman correlation\n\n## Practical Task\n\nUsing exam score data: (1) Calculate all descriptive statistics. (2) Test whether students who studied 5+ hours scored significantly higher (t-test). (3) Check if gender and pass/fail are independent (chi-square). Report findings with interpretation.\n\n## Self-Check\n1. When should you use median instead of mean?\n2. What does a p-value of 0.03 mean?\n3. What is the difference between correlation and causation?', 3, 1, '2026-04-15 07:00:00'),
(118, 17, 'Data Wrangling & Feature Engineering', '## Data Wrangling\n\nData scientists spend 60-80% of their time cleaning and preparing data. Common issues: missing values, duplicates, wrong types, inconsistent categories, outliers.\n\n## Handling Missing Values\n\n```python\n# Check\ndf.isnull().mean() * 100  # Percentage missing per column\n\n# Drop\ndf.dropna(subset=[\'critical_col\'])\n\n# Impute\ndf[\'age\'].fillna(df[\'age\'].median(), inplace=True)\ndf[\'city\'].fillna(\'Unknown\', inplace=True)\n\n# KNN imputation\nfrom sklearn.impute import KNNImputer\nimputer = KNNImputer(n_neighbors=5)\ndf_imputed = pd.DataFrame(imputer.fit_transform(df), columns=df.columns)\n```\n\n## Feature Engineering\n\n```python\n# Date features\ndf[\'year\'] = df[\'date\'].dt.year\ndf[\'month\'] = df[\'date\'].dt.month\ndf[\'is_weekend\'] = df[\'date\'].dt.dayofweek.isin([5,6]).astype(int)\n\n# Binning\ndf[\'age_group\'] = pd.cut(df[\'age\'], bins=[0,18,35,60,100],\n                         labels=[\'Youth\',\'Adult\',\'Middle\',\'Senior\'])\n\n# Encoding\ndf = pd.get_dummies(df, columns=[\'city\'], drop_first=True)  # One-hot\ndf[\'gender_enc\'] = df[\'gender\'].map({\'Male\':1,\'Female\':0})   # Label\n\n# Scaling\nfrom sklearn.preprocessing import StandardScaler\nscaler = StandardScaler()\ndf[[\'age\',\'income\']] = scaler.fit_transform(df[[\'age\',\'income\']])\n\n# Interaction\ndf[\'income_per_age\'] = df[\'income\'] / (df[\'age\'] + 1)\n```\n\n## Practical Task\n\nTake a messy dataset with intentional issues. Perform a complete wrangling pipeline: audit, clean, engineer 3 new features, encode categoricals, scale numerics. Document every decision.\n\n## Self-Check\n1. What is the difference between dropping and imputing missing values?\n2. Why do we scale features before modelling?\n3. What is one-hot encoding and when do you use it?', 4, 1, '2026-04-15 07:00:00'),
(119, 17, 'Machine Learning for Data Scientists', '## Key ML Algorithms for Data Scientists\n\nRegression: Linear, Ridge, Lasso, Random Forest, XGBoost\nClassification: Logistic Regression, Decision Tree, Random Forest, XGBoost, SVM\n\n```python\nfrom sklearn.ensemble import RandomForestClassifier\nfrom sklearn.model_selection import train_test_split, cross_val_score\nfrom sklearn.metrics import classification_report, roc_auc_score\n\nX_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)\n\nmodel = RandomForestClassifier(n_estimators=100, random_state=42)\nmodel.fit(X_train, y_train)\n\ny_pred = model.predict(X_test)\ny_prob = model.predict_proba(X_test)[:,1]\n\nprint(classification_report(y_test, y_pred))\nprint(f\'ROC-AUC: {roc_auc_score(y_test, y_prob):.4f}\')\n\n# Cross-validation\nscores = cross_val_score(model, X, y, cv=5, scoring=\'f1_macro\')\nprint(f\'CV F1: {scores.mean():.3f} +/- {scores.std():.3f}\')\n```\n\n## Model Evaluation Metrics\n\nClassification: Accuracy, Precision, Recall, F1, ROC-AUC\nRegression: MAE, RMSE, R2, MAPE\n\nKey insight: Accuracy is misleading for imbalanced datasets. Use F1 or ROC-AUC.\n\n## Practical Task\n\nBuild a student pass/fail prediction model. Features: study hours, attendance, previous scores, assignment completion. Use Random Forest. Evaluate with classification report and ROC-AUC. Identify the top 3 most important features.\n\n## Self-Check\n1. What is the difference between precision and recall?\n2. Why is accuracy a poor metric for imbalanced datasets?\n3. What is cross-validation and why is it important?', 5, 1, '2026-04-15 07:00:00'),
(120, 17, 'Data Visualisation & Storytelling', '## Choosing the Right Chart\n\n| Goal | Chart Type |\n|---|---|\n| Compare categories | Bar chart |\n| Show trend over time | Line chart |\n| Show distribution | Histogram, Box plot |\n| Show correlation | Scatter plot |\n| Show proportions | Pie chart (max 5 slices) |\n\n## Advanced Visualisation with Plotly\n\n```python\nimport plotly.express as px\n\n# Interactive scatter\nfig = px.scatter(df, x=\'study_hours\', y=\'score\', color=\'grade\',\n                 size=\'attendance\', hover_data=[\'name\'],\n                 title=\'Study Hours vs Score\')\nfig.show()\n```\n\n## Streamlit Dashboard\n\n```python\nimport streamlit as st\nimport plotly.express as px\n\nst.title(\'Student Performance Dashboard\')\ncourse = st.selectbox(\'Select Course\', df[\'course\'].unique())\nfiltered = df[df[\'course\'] == course]\n\ncol1, col2 = st.columns(2)\ncol1.metric(\'Average Score\', f\"{filtered[\'score\'].mean():.1f}\")\ncol2.metric(\'Pass Rate\', f\"{(filtered[\'score\']>=50).mean()*100:.1f}%\")\n\nfig = px.histogram(filtered, x=\'score\', title=\'Score Distribution\')\nst.plotly_chart(fig)\n```\n\n## Data Storytelling\n\nStructure: Context (situation) -> Conflict (problem/opportunity) -> Resolution (what data shows we should do)\n\n## Practical Task\n\nBuild a Streamlit dashboard for a student performance dataset. Include: 3 KPI metrics, score distribution chart, trend over time, top 10 students table, and a course filter.\n\n## Self-Check\n1. What chart type is best for showing a trend over time?\n2. What is data storytelling and why does it matter?\n3. What is the difference between Plotly and Matplotlib?', 6, 1, '2026-04-15 07:00:00'),
(121, 17, 'Big Data & Cloud Data Science', '## What is Big Data?\n\nBig Data: datasets too large for traditional tools. Characterised by 5 Vs: Volume, Velocity, Variety, Veracity, Value.\n\n## Big Data Technologies\n\nStorage: Hadoop HDFS, Amazon S3, Google Cloud Storage\nProcessing: Apache Spark (100x faster than MapReduce), Apache Kafka (streaming)\nDatabases: MongoDB (document), Cassandra (wide-column), BigQuery (data warehouse)\n\n## PySpark Basics\n\n```python\nfrom pyspark.sql import SparkSession\n\nspark = SparkSession.builder.appName(\'DataScience\').getOrCreate()\ndf = spark.read.csv(\'large_dataset.csv\', header=True, inferSchema=True)\n\n# Lazy evaluation\ndf.filter(df[\'score\'] > 70).groupBy(\'course\').agg({\'score\': \'mean\'}).show()\n\n# Convert small results to pandas\npandas_df = df.toPandas()\n```\n\n## Cloud Platforms\n\n- AWS SageMaker: Build, train, deploy ML models at scale\n- Google Vertex AI: Unified ML platform on GCP\n- Azure Machine Learning: Microsoft ML platform\n- Databricks: Spark-based unified analytics\n- Google Colab: Free GPU/TPU notebooks\n\n## Practical Task\n\nCreate a Google Colab notebook. Load a large dataset (1M+ rows). Perform aggregations and visualisations. Compare pandas vs PySpark performance for the same operations.\n\n## Self-Check\n1. What are the 5 Vs of Big Data?\n2. What is the main advantage of Apache Spark over Hadoop MapReduce?\n3. Name 3 cloud data science platforms.', 7, 1, '2026-04-15 07:00:00'),
(122, 17, 'Capstone: End-to-End Data Science Project', '## Final Project: Loan Default Prediction\n\nYou are a data scientist at a Nigerian fintech company. Build a model to predict loan defaults and a dashboard for the risk team.\n\n## Dataset Features\n\napplicant_age, income, employment_years, loan_amount, loan_purpose, credit_score, existing_loans, education_level, state, gender, default (target)\n\n## Phase 1: EDA\n- Descriptive statistics for all features\n- Missing value analysis and treatment\n- Distribution plots and correlation analysis\n- Class imbalance check\n\n## Phase 2: Feature Engineering\n- Create debt-to-income ratio\n- Encode categorical variables\n- Scale numeric features\n- Handle class imbalance (SMOTE or class weights)\n- Feature selection (top 10 by importance)\n\n## Phase 3: Modelling\n- Train 3 models: Logistic Regression, Random Forest, XGBoost\n- Cross-validate each (5-fold)\n- Tune hyperparameters\n- Select best model by ROC-AUC and F1\n\n## Phase 4: Evaluation\n- Confusion matrix, ROC curve, AUC\n- Feature importance plot\n- SHAP values for explainability\n\n## Phase 5: Deployment\n- Save model with joblib\n- Build Streamlit app: input applicant details, get default probability\n- Deploy to Streamlit Cloud (free)\n\n## Deliverables\n- Jupyter Notebook, Streamlit dashboard, 10-slide presentation, 1-page executive summary\n\n## Self-Check\n1. What metric would you prioritise for a fraud detection model and why?\n2. How would you explain your model to a non-technical bank manager?\n3. What steps if your model performs well in testing but poorly in production?', 8, 1, '2026-04-15 07:00:00'),
(123, 19, 'Introduction to Machine Learning', '## What is Machine Learning?\n\nMachine Learning is a subset of AI that enables systems to learn and improve from experience without being explicitly programmed. Instead of writing rules, you provide data and the algorithm discovers the rules.\n\n## Why Machine Learning Now?\n\n- Data explosion: Smartphones, IoT, social media generate massive data\n- Computing power: GPUs make training large models feasible\n- Algorithm advances: Better architectures and optimisation methods\n- Open source: TensorFlow, PyTorch, scikit-learn freely available\n\n## The ML Landscape\n\nArtificial Intelligence > Machine Learning > Supervised Learning (Classification, Regression), Unsupervised Learning (Clustering, Dimensionality Reduction), Reinforcement Learning > Deep Learning (CNNs, RNNs, Transformers)\n\n## Your First ML Model\n\n```python\nfrom sklearn.datasets import load_breast_cancer\nfrom sklearn.model_selection import train_test_split\nfrom sklearn.linear_model import LogisticRegression\nfrom sklearn.metrics import accuracy_score\n\ndata = load_breast_cancer()\nX, y = data.data, data.target\nX_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)\n\nmodel = LogisticRegression(max_iter=10000)\nmodel.fit(X_train, y_train)\ny_pred = model.predict(X_test)\nprint(f\'Accuracy: {accuracy_score(y_test, y_pred):.4f}\')\n```\n\n## Practical Task\n\nBuild your first ML model to predict whether a student will pass or fail based on: study hours, attendance percentage, assignment completion rate, and previous test scores. Use Logistic Regression. Report accuracy, precision, and recall.\n\n## Self-Check\n1. What is the difference between AI and Machine Learning?\n2. Name the 3 main types of machine learning.\n3. What is the purpose of splitting data into training and test sets?', 1, 1, '2026-04-15 07:00:00'),
(124, 19, 'Supervised Learning: Regression', '## Regression Algorithms\n\nRegression predicts a continuous numerical value. It answers: How much? How many? What price?\n\n## Linear Regression\n\n```python\nfrom sklearn.linear_model import LinearRegression, Ridge, Lasso\nfrom sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score\nimport numpy as np\n\nmodel = LinearRegression()\nmodel.fit(X_train, y_train)\ny_pred = model.predict(X_test)\n\nprint(f\'MAE:  {mean_absolute_error(y_test, y_pred):.2f}\')\nprint(f\'RMSE: {np.sqrt(mean_squared_error(y_test, y_pred)):.2f}\')\nprint(f\'R2:   {r2_score(y_test, y_pred):.4f}\')\n\nridge = Ridge(alpha=1.0)   # L2 regularisation\nlasso = Lasso(alpha=0.1)   # L1 regularisation (feature selection)\n```\n\n## XGBoost Regression\n\n```python\nimport xgboost as xgb\nfrom sklearn.model_selection import GridSearchCV\n\nxgb_model = xgb.XGBRegressor(n_estimators=100, learning_rate=0.1, max_depth=6, random_state=42)\n\nparam_grid = {\'n_estimators\':[100,200],\'learning_rate\':[0.05,0.1],\'max_depth\':[4,6,8]}\ngrid = GridSearchCV(xgb_model, param_grid, cv=5, scoring=\'r2\')\ngrid.fit(X_train, y_train)\nprint(f\'Best R2: {grid.best_score_:.4f}\')\n```\n\n## Regression Metrics\n\n- MAE: Mean Absolute Error — average absolute difference\n- RMSE: Root Mean Squared Error — penalises large errors more\n- R2: Proportion of variance explained (1.0 = perfect)\n- MAPE: Mean Absolute Percentage Error — interpretable as %\n\n## Practical Task\n\nBuild a house price prediction model for Lagos properties. Features: size (sqm), bedrooms, bathrooms, location, age, parking spaces. Compare Linear Regression, Ridge, and XGBoost. Use RMSE and R2 as metrics.\n\n## Self-Check\n1. What is the difference between MAE and RMSE?\n2. What is regularisation and why is it needed?\n3. When would you use Lasso over Ridge regression?', 2, 1, '2026-04-15 07:00:00'),
(125, 19, 'Supervised Learning: Classification', '## Classification Algorithms\n\nClassification predicts which category an input belongs to.\n\n## Logistic Regression\n\n```python\nfrom sklearn.linear_model import LogisticRegression\nfrom sklearn.metrics import classification_report, roc_auc_score\n\nmodel = LogisticRegression(C=1.0, max_iter=1000)\nmodel.fit(X_train, y_train)\ny_pred = model.predict(X_test)\ny_prob = model.predict_proba(X_test)[:,1]\n\nprint(classification_report(y_test, y_pred))\nprint(f\'ROC-AUC: {roc_auc_score(y_test, y_prob):.4f}\')\n```\n\n## Random Forest\n\n```python\nfrom sklearn.ensemble import RandomForestClassifier\n\nrf = RandomForestClassifier(n_estimators=200, max_depth=10, n_jobs=-1, random_state=42)\nrf.fit(X_train, y_train)\n\nimportances = pd.Series(rf.feature_importances_, index=feature_names)\nimportances.nlargest(10).plot(kind=\'barh\')\n```\n\n## XGBoost Classification\n\n```python\nimport xgboost as xgb\n\nmodel = xgb.XGBClassifier(n_estimators=300, learning_rate=0.05, max_depth=6,\n                           subsample=0.8, colsample_bytree=0.8, random_state=42)\nmodel.fit(X_train, y_train, eval_set=[(X_test, y_test)],\n          early_stopping_rounds=20, verbose=False)\n```\n\n## Handling Imbalanced Classes\n\n```python\nfrom imblearn.over_sampling import SMOTE\nsmote = SMOTE(random_state=42)\nX_resampled, y_resampled = smote.fit_resample(X_train, y_train)\n\n# Or use class_weight\nmodel = RandomForestClassifier(class_weight=\'balanced\')\n```\n\n## Practical Task\n\nBuild a fraud detection model for mobile money transactions. Handle class imbalance. Compare models. Optimise for recall (catching fraud is more important than false alarms).\n\n## Self-Check\n1. What is the difference between precision and recall? Which matters more for fraud detection?\n2. What is a Random Forest and how does it differ from a single Decision Tree?\n3. What is SMOTE and when do you use it?', 3, 1, '2026-04-15 07:00:00'),
(126, 19, 'Unsupervised Learning', '## What is Unsupervised Learning?\n\nUnsupervised learning finds hidden patterns in data without labelled examples.\n\n## K-Means Clustering\n\n```python\nfrom sklearn.cluster import KMeans\nfrom sklearn.preprocessing import StandardScaler\n\nscaler = StandardScaler()\nX_scaled = scaler.fit_transform(X)\n\n# Elbow method to find optimal k\ninertias = []\nfor k in range(1, 11):\n    km = KMeans(n_clusters=k, random_state=42, n_init=10)\n    km.fit(X_scaled)\n    inertias.append(km.inertia_)\n\n# Fit with optimal k\nkm = KMeans(n_clusters=4, random_state=42, n_init=10)\ndf[\'cluster\'] = km.fit_predict(X_scaled)\n```\n\n## DBSCAN\n\n```python\nfrom sklearn.cluster import DBSCAN\ndbscan = DBSCAN(eps=0.5, min_samples=5)\ndf[\'cluster\'] = dbscan.fit_predict(X_scaled)\n# Cluster -1 = outliers/noise\n```\n\n## PCA — Dimensionality Reduction\n\n```python\nfrom sklearn.decomposition import PCA\n\npca = PCA(n_components=2)\nX_2d = pca.fit_transform(X_scaled)\nprint(f\'Variance retained: {pca.explained_variance_ratio_.sum():.3f}\')\n\nimport matplotlib.pyplot as plt\nplt.scatter(X_2d[:,0], X_2d[:,1], c=y, cmap=\'viridis\')\n```\n\n## Anomaly Detection\n\n```python\nfrom sklearn.ensemble import IsolationForest\niso = IsolationForest(contamination=0.05, random_state=42)\ndf[\'anomaly\'] = iso.fit_predict(X_scaled)  # -1=anomaly, 1=normal\n```\n\n## Practical Task\n\nPerform customer segmentation for a Nigerian e-commerce company. Features: purchase frequency, average order value, days since last purchase. Use K-Means (find optimal k). Visualise with PCA. Name each segment and write a marketing strategy for each.\n\n## Self-Check\n1. What is the difference between K-Means and DBSCAN?\n2. What does PCA do and why is it useful?\n3. What is anomaly detection and give a real-world use case?', 4, 1, '2026-04-15 07:00:00'),
(127, 19, 'Model Evaluation & Hyperparameter Tuning', '## Train/Validation/Test Split\n\n```python\nfrom sklearn.model_selection import train_test_split\n\nX_trainval, X_test, y_trainval, y_test = train_test_split(X, y, test_size=0.2, random_state=42)\nX_train, X_val, y_train, y_val = train_test_split(X_trainval, y_trainval, test_size=0.25, random_state=42)\n# Result: 60% train, 20% val, 20% test\n```\n\n## Cross-Validation\n\n```python\nfrom sklearn.model_selection import cross_val_score, StratifiedKFold\n\ncv = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)\nscores = cross_val_score(model, X, y, cv=cv, scoring=\'f1_macro\')\nprint(f\'CV F1: {scores.mean():.4f} +/- {scores.std():.4f}\')\n```\n\n## Optuna — Bayesian Optimisation\n\n```python\nimport optuna\n\ndef objective(trial):\n    params = {\n        \'n_estimators\': trial.suggest_int(\'n_estimators\', 50, 500),\n        \'max_depth\': trial.suggest_int(\'max_depth\', 3, 12),\n        \'learning_rate\': trial.suggest_float(\'learning_rate\', 0.01, 0.3, log=True),\n    }\n    model = xgb.XGBClassifier(**params, random_state=42)\n    return cross_val_score(model, X_train, y_train, cv=5, scoring=\'roc_auc\').mean()\n\nstudy = optuna.create_study(direction=\'maximize\')\nstudy.optimize(objective, n_trials=100)\nprint(f\'Best params: {study.best_params}\')\n```\n\n## Learning Curves\n\n```python\nfrom sklearn.model_selection import learning_curve\n\ntrain_sizes, train_scores, val_scores = learning_curve(model, X, y, cv=5, n_jobs=-1)\nplt.plot(train_sizes, train_scores.mean(axis=1), label=\'Training\')\nplt.plot(train_sizes, val_scores.mean(axis=1), label=\'Validation\')\nplt.legend()\n```\n\n## Practical Task\n\nTune your fraud detection model using Optuna (100 trials). Plot learning curves to diagnose overfitting/underfitting. Compare tuned vs untuned model. Report the improvement in ROC-AUC.\n\n## Self-Check\n1. What is the difference between Grid Search and Bayesian Optimisation?\n2. What do learning curves tell you about your model?\n3. Why should you never tune hyperparameters on the test set?', 5, 1, '2026-04-15 07:00:00'),
(128, 19, 'Feature Engineering & Selection', '## Feature Engineering\n\nFeature engineering creates new variables that better represent underlying patterns.\n\n```python\n# Interaction features\ndf[\'income_to_loan_ratio\'] = df[\'income\'] / (df[\'loan_amount\'] + 1)\ndf[\'age_times_experience\'] = df[\'age\'] * df[\'work_experience\']\n\n# Date features\ndf[\'hour\'] = df[\'timestamp\'].dt.hour\ndf[\'is_business_hours\'] = df[\'hour\'].between(9, 17).astype(int)\ndf[\'days_since_registration\'] = (pd.Timestamp.now() - df[\'reg_date\']).dt.days\n\n# Aggregation\ndf[\'avg_score_by_course\'] = df.groupby(\'course\')[\'score\'].transform(\'mean\')\ndf[\'rank_in_course\'] = df.groupby(\'course\')[\'score\'].rank(ascending=False)\n```\n\n## Feature Selection\n\n```python\nfrom sklearn.feature_selection import SelectKBest, f_classif, RFE\nfrom sklearn.ensemble import RandomForestClassifier\n\n# Filter: Statistical test\nselector = SelectKBest(f_classif, k=10)\nX_selected = selector.fit_transform(X, y)\n\n# Wrapper: Recursive Feature Elimination\nrfe = RFE(RandomForestClassifier(n_estimators=50), n_features_to_select=10)\nrfe.fit(X_train, y_train)\n\n# Embedded: Tree feature importance\nrf = RandomForestClassifier(n_estimators=100)\nrf.fit(X_train, y_train)\ntop_features = pd.Series(rf.feature_importances_, index=X.columns).nlargest(10).index\n\n# SHAP values\nimport shap\nexplainer = shap.TreeExplainer(rf)\nshap_values = explainer.shap_values(X_test)\nshap.summary_plot(shap_values[1], X_test)\n```\n\n## Practical Task\n\nTake a dataset with 50+ features. Apply feature engineering to create 10 new features. Use 3 different feature selection methods. Compare model performance with all features vs top 10. Which approach gives the best result?\n\n## Self-Check\n1. What is the difference between filter, wrapper, and embedded feature selection?\n2. What are SHAP values and why are they useful?\n3. Why can too many features hurt model performance?', 6, 1, '2026-04-15 07:00:00'),
(129, 19, 'ML Deployment & MLOps', '## Saving and Loading Models\n\n```python\nimport joblib\n\n# Save\njoblib.dump(model, \'model.joblib\')\njoblib.dump(scaler, \'scaler.joblib\')\n\n# Load\nmodel = joblib.load(\'model.joblib\')\nscaler = joblib.load(\'scaler.joblib\')\n```\n\n## Flask REST API\n\n```python\nfrom flask import Flask, request, jsonify\nimport joblib, numpy as np\n\napp = Flask(__name__)\nmodel = joblib.load(\'model.joblib\')\nscaler = joblib.load(\'scaler.joblib\')\n\n@app.route(\'/predict\', methods=[\'POST\'])\ndef predict():\n    data = request.get_json()\n    features = np.array(data[\'features\']).reshape(1, -1)\n    features_scaled = scaler.transform(features)\n    prediction = model.predict(features_scaled)[0]\n    probability = model.predict_proba(features_scaled)[0].max()\n    return jsonify({\'prediction\': int(prediction), \'probability\': float(probability)})\n\nif __name__ == \'__main__\':\n    app.run(host=\'0.0.0.0\', port=5000)\n```\n\n## MLflow Experiment Tracking\n\n```python\nimport mlflow, mlflow.sklearn\n\nwith mlflow.start_run():\n    model = RandomForestClassifier(n_estimators=100, max_depth=6)\n    model.fit(X_train, y_train)\n    accuracy = accuracy_score(y_test, model.predict(X_test))\n    mlflow.log_param(\'n_estimators\', 100)\n    mlflow.log_metric(\'accuracy\', accuracy)\n    mlflow.sklearn.log_model(model, \'model\')\n```\n\n## Model Monitoring\n\nModels degrade over time as data distribution changes (data drift). Monitor:\n- Prediction distribution over time\n- Feature distribution vs training data\n- Model performance on labelled production samples\n\nTools: Evidently AI, WhyLabs, Arize, MLflow\n\n## Practical Task\n\nDeploy your best model as a Flask REST API. Containerise with Docker. Set up basic monitoring: log every prediction with timestamp, input features, and output. Create a dashboard showing prediction volume and average confidence over time.\n\n## Self-Check\n1. What is data drift and why does it matter for deployed models?\n2. What is MLflow used for?\n3. What is the difference between model deployment and MLOps?', 7, 1, '2026-04-15 07:00:00');
INSERT INTO `lms_lessons` (`id`, `course_id`, `title`, `content`, `sort_order`, `is_published`, `created_at`) VALUES
(130, 19, 'Capstone: ML System in Production', '## Final Project: Customer Churn Prediction\n\nYou are the lead ML engineer at a Nigerian telecommunications company. Build a model to predict which customers are likely to churn in the next 30 days.\n\n## Dataset Features\n\ncustomer_id, tenure_months, monthly_charge, total_charges, contract_type (month-to-month/1-year/2-year), internet_service, phone_service, payment_method, num_support_calls, num_complaints, churn (target: 0=stayed, 1=churned)\n\n## Phase 1: Exploratory Analysis\n- Churn rate overall and by segment\n- Feature distributions for churned vs retained customers\n- Correlation analysis\n- Identify top 5 churn risk factors\n\n## Phase 2: Feature Engineering\n- Create charge_per_month_tenure ratio\n- Encode contract type (ordinal)\n- Create high_support_user flag (calls > 3)\n- Scale numeric features\n\n## Phase 3: Modelling\n- Baseline: Logistic Regression\n- Advanced: XGBoost with Optuna tuning\n- Handle class imbalance with SMOTE\n- Evaluate: ROC-AUC, Precision-Recall curve, F1\n- Interpret: SHAP values for top 10 features\n\n## Phase 4: Business Impact\n- Calculate expected revenue saved if top 20% at-risk customers are retained\n- Determine optimal probability threshold\n- Create customer risk segments: High/Medium/Low\n\n## Phase 5: Deployment\n- Flask API: POST /predict with customer features, returns churn probability and risk segment\n- Streamlit dashboard: Upload CSV, get predictions, download results\n- MLflow: Track all experiments\n- Docker: Containerise the application\n\n## Deliverables\n1. GitHub repository (clean code, README, requirements.txt)\n2. Jupyter Notebook with complete analysis\n3. Deployed Flask API\n4. Streamlit dashboard\n5. Business presentation (10 slides)\n6. Model card (purpose, performance, limitations, fairness)\n\n## Evaluation Criteria\n- EDA quality and business insight (15%)\n- Feature engineering (15%)\n- Model performance ROC-AUC > 0.80 (25%)\n- Code quality and MLOps practices (20%)\n- Business impact analysis (15%)\n- Presentation and documentation (10%)\n\n## Self-Check\n1. What ROC-AUC score did you achieve and is it good enough for production?\n2. How would you explain the model decision to a customer flagged as high churn risk?\n3. What would you monitor in production to detect model degradation?', 8, 1, '2026-04-15 07:00:00'),
(131, 18, 'Introduction to Artificial Intelligence', '## What is Artificial Intelligence?\n\nArtificial Intelligence (AI) is the simulation of human intelligence processes by machines. These processes include learning, reasoning, problem-solving, perception, and language understanding.\n\n## Types of AI\n\nBy Capability:\n- Narrow AI (ANI): Designed for one specific task (Siri, chess engines, spam filters)\n- General AI (AGI): Human-level intelligence across all domains (not yet achieved)\n- Super AI (ASI): Surpasses human intelligence in all areas (theoretical)\n\nBy Approach:\n- Rule-based AI: Expert systems with hand-coded rules\n- Machine Learning: Learns patterns from data\n- Deep Learning: Neural networks with many layers\n- Generative AI: Creates new content (text, images, code)\n\n## AI History Timeline\n\n- 1950: Alan Turing proposes the Turing Test\n- 1956: Term AI coined at Dartmouth Conference\n- 1997: IBM Deep Blue beats chess world champion\n- 2012: Deep learning revolution (AlexNet wins ImageNet)\n- 2016: AlphaGo beats world Go champion\n- 2017: Transformer architecture introduced\n- 2022: ChatGPT launches, mainstream AI adoption begins\n- 2024: GPT-4o, Gemini, Claude 3 — multimodal AI\n\n## AI Applications in Nigeria and Africa\n\n- Agriculture: Crop disease detection from smartphone photos\n- Healthcare: Malaria diagnosis from blood smear images\n- Finance: Fraud detection, credit scoring for the unbanked\n- Education: Personalised learning, automated grading\n- Language: NLP for Yoruba, Igbo, Hausa, Pidgin\n\n## Setting Up Your AI Environment\n\n```bash\npip install numpy pandas scikit-learn tensorflow torch transformers openai\n```\n\n## Practical Task\n\nResearch 3 AI applications currently being used in Nigerian companies or government. For each: describe the problem being solved, the AI approach used, and the impact. Write a 1-page report.\n\n## Self-Check\n1. What is the difference between Narrow AI and General AI?\n2. What was the significance of the 2012 AlexNet breakthrough?\n3. Name 3 AI applications relevant to the Nigerian context.', 1, 1, '2026-04-15 07:00:00'),
(132, 18, 'Machine Learning Foundations', '## The Machine Learning Paradigm\n\nTraditional programming: Rules + Data = Output\nMachine Learning: Data + Output = Rules\n\nInstead of writing rules, we feed examples to an algorithm that learns the rules automatically.\n\n## Types of Machine Learning\n\nSupervised Learning: Learn from labelled examples\n- Classification: Predict a category (spam/not spam)\n- Regression: Predict a number (house price)\n\nUnsupervised Learning: Find patterns in unlabelled data\n- Clustering: Group similar items (customer segments)\n- Dimensionality Reduction: Compress data (PCA)\n\nReinforcement Learning: Learn by trial and error with rewards\n- Agent takes actions, receives rewards or penalties\n- Applications: Game playing, robotics, trading\n\n## The ML Workflow\n\n```python\nfrom sklearn.datasets import load_iris\nfrom sklearn.model_selection import train_test_split\nfrom sklearn.preprocessing import StandardScaler\nfrom sklearn.svm import SVC\nfrom sklearn.metrics import accuracy_score, classification_report\n\nX, y = load_iris(return_X_y=True)\nX_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)\n\nscaler = StandardScaler()\nX_train = scaler.fit_transform(X_train)\nX_test  = scaler.transform(X_test)\n\nmodel = SVC(kernel=\'rbf\', C=1.0)\nmodel.fit(X_train, y_train)\n\ny_pred = model.predict(X_test)\nprint(f\'Accuracy: {accuracy_score(y_test, y_pred):.3f}\')\nprint(classification_report(y_test, y_pred))\n```\n\n## Overfitting vs Underfitting\n\n- Underfitting: Model too simple, high bias, poor on training and test data\n- Overfitting: Model too complex, high variance, great on training but poor on test\n- Good fit: Low bias and low variance, generalises well\n\nSolutions:\n- Underfitting: More features, more complex model, more training\n- Overfitting: More data, regularisation, dropout, cross-validation\n\n## Practical Task\n\nBuild a spam email classifier using the SMS Spam Collection dataset from UCI. Try 3 algorithms: Naive Bayes, Logistic Regression, Random Forest. Compare accuracy, precision, recall, and F1.\n\n## Self-Check\n1. What is the difference between supervised and unsupervised learning?\n2. What is overfitting and how do you prevent it?\n3. What is the difference between classification and regression?', 2, 1, '2026-04-15 07:00:00'),
(133, 18, 'Neural Networks & Deep Learning', '## What is a Neural Network?\n\nA neural network is a computational model inspired by the human brain. It consists of layers of interconnected nodes (neurons) that transform input data into output predictions.\n\n## Architecture\n\n- Input Layer: Receives raw features\n- Hidden Layers: Learn intermediate representations\n- Output Layer: Produces final prediction\n- Weights: Parameters learned during training\n- Activation Functions: Introduce non-linearity\n\n## Activation Functions\n\n- ReLU: max(0, x) — most common for hidden layers\n- Sigmoid: 1/(1+e^-x) — binary classification output\n- Softmax: Multi-class classification output\n- Tanh: Range -1 to 1, used in RNNs\n\n## Building a Neural Network with Keras\n\n```python\nimport tensorflow as tf\nfrom tensorflow import keras\n\n(X_train, y_train), (X_test, y_test) = keras.datasets.mnist.load_data()\nX_train = X_train.reshape(-1, 784) / 255.0\nX_test  = X_test.reshape(-1, 784) / 255.0\n\nmodel = keras.Sequential([\n    keras.layers.Dense(256, activation=\'relu\', input_shape=(784,)),\n    keras.layers.Dropout(0.3),\n    keras.layers.Dense(128, activation=\'relu\'),\n    keras.layers.Dropout(0.3),\n    keras.layers.Dense(10, activation=\'softmax\')\n])\n\nmodel.compile(optimizer=\'adam\', loss=\'sparse_categorical_crossentropy\', metrics=[\'accuracy\'])\nhistory = model.fit(X_train, y_train, epochs=10, batch_size=32, validation_split=0.1)\n\ntest_loss, test_acc = model.evaluate(X_test, y_test)\nprint(f\'Test accuracy: {test_acc:.4f}\')\n```\n\n## Backpropagation\n\n1. Forward pass: Compute predictions\n2. Calculate loss (error)\n3. Backward pass: Compute gradients using chain rule\n4. Update weights: w = w - learning_rate * gradient\n\n## Practical Task\n\nBuild a neural network to classify handwritten digits (MNIST). Experiment with layers, neurons, dropout rates, and learning rates. Plot training and validation accuracy curves. Achieve at least 98% test accuracy.\n\n## Self-Check\n1. What is the role of activation functions in neural networks?\n2. What is dropout and why does it help prevent overfitting?\n3. Explain backpropagation in simple terms.', 3, 1, '2026-04-15 07:00:00'),
(134, 18, 'Computer Vision with CNNs', '## What is Computer Vision?\n\nComputer vision enables machines to interpret and understand visual information from images and videos. It is one of the most successful applications of deep learning.\n\n## Convolutional Neural Networks (CNNs)\n\nCNNs are designed specifically for image data. They use convolutional layers to automatically learn spatial features.\n\nKey Components:\n- Convolutional Layer: Applies filters to detect features (edges, textures, shapes)\n- Pooling Layer: Reduces spatial dimensions (Max Pooling)\n- Flatten Layer: Converts 2D feature maps to 1D vector\n- Dense Layer: Final classification\n\n```python\nimport tensorflow as tf\nfrom tensorflow import keras\n\nmodel = keras.Sequential([\n    keras.layers.Conv2D(32, (3,3), activation=\'relu\', input_shape=(224,224,3)),\n    keras.layers.MaxPooling2D(2,2),\n    keras.layers.Conv2D(64, (3,3), activation=\'relu\'),\n    keras.layers.MaxPooling2D(2,2),\n    keras.layers.Conv2D(128, (3,3), activation=\'relu\'),\n    keras.layers.MaxPooling2D(2,2),\n    keras.layers.Flatten(),\n    keras.layers.Dense(512, activation=\'relu\'),\n    keras.layers.Dropout(0.5),\n    keras.layers.Dense(10, activation=\'softmax\')\n])\n```\n\n## Transfer Learning\n\n```python\nbase_model = keras.applications.MobileNetV2(weights=\'imagenet\', include_top=False, input_shape=(224,224,3))\nbase_model.trainable = False\n\nmodel = keras.Sequential([\n    base_model,\n    keras.layers.GlobalAveragePooling2D(),\n    keras.layers.Dense(256, activation=\'relu\'),\n    keras.layers.Dense(num_classes, activation=\'softmax\')\n])\n```\n\nPopular pre-trained models: VGG16, ResNet50, InceptionV3, EfficientNet, MobileNet\n\n## Real-World Applications\n\n- Medical imaging: Detect malaria, tuberculosis, cancer from scans\n- Agriculture: Identify crop diseases from leaf photos\n- Security: Face recognition, object detection\n- Autonomous vehicles: Lane detection, pedestrian detection\n\n## Practical Task\n\nBuild a crop disease classifier using transfer learning (MobileNetV2). Dataset: PlantVillage on Kaggle. Train to classify 5 disease types. Achieve 90%+ validation accuracy. Build a simple interface where a farmer can upload a leaf photo and get a diagnosis.\n\n## Self-Check\n1. What is the purpose of a convolutional layer?\n2. What is transfer learning and why is it useful?\n3. Name 3 real-world applications of computer vision in Africa.', 4, 1, '2026-04-15 07:00:00'),
(135, 18, 'Natural Language Processing (NLP)', '## What is NLP?\n\nNatural Language Processing (NLP) enables computers to understand, interpret, and generate human language.\n\n## NLP Pipeline\n\n1. Text Preprocessing: Tokenisation, lowercasing, punctuation removal\n2. Stop Word Removal: Remove common words (the, is, at)\n3. Stemming/Lemmatisation: Reduce words to root form\n4. Feature Extraction: Convert text to numbers\n5. Modelling: Apply ML/DL model\n\n## Text Preprocessing\n\n```python\nimport re, nltk\nfrom nltk.tokenize import word_tokenize\nfrom nltk.corpus import stopwords\nfrom nltk.stem import WordNetLemmatizer\n\ndef preprocess(text):\n    text = text.lower()\n    text = re.sub(r\'[^a-z0-9\\s]\', \'\', text)\n    tokens = word_tokenize(text)\n    stop_words = set(stopwords.words(\'english\'))\n    tokens = [t for t in tokens if t not in stop_words]\n    lemmatizer = WordNetLemmatizer()\n    return \' \'.join([lemmatizer.lemmatize(t) for t in tokens])\n```\n\n## TF-IDF Classification\n\n```python\nfrom sklearn.feature_extraction.text import TfidfVectorizer\nfrom sklearn.linear_model import LogisticRegression\n\nvectorizer = TfidfVectorizer(max_features=5000, ngram_range=(1,2))\nX_train_vec = vectorizer.fit_transform(X_train)\nX_test_vec  = vectorizer.transform(X_test)\n\nmodel = LogisticRegression()\nmodel.fit(X_train_vec, y_train)\n```\n\n## Transformers & BERT\n\n```python\nfrom transformers import pipeline\n\nsentiment = pipeline(\'sentiment-analysis\')\nresult = sentiment(\'This course is absolutely amazing!\')\nprint(result)  # [{\'label\': \'POSITIVE\', \'score\': 0.9998}]\n\nner = pipeline(\'ner\', grouped_entities=True)\nresult = ner(\'Dangote Group is headquartered in Lagos, Nigeria.\')\n```\n\n## NLP for African Languages\n\nProjects: Masakhane (masakhane.io), AfriSenti, Yoruba/Hausa/Igbo datasets on HuggingFace\n\n## Practical Task\n\nBuild a sentiment analyser for Nigerian social media comments. Collect 500 comments about a Nigerian brand. Preprocess the text. Train a classifier (Logistic Regression + TF-IDF). Evaluate with F1 score.\n\n## Self-Check\n1. What is the difference between stemming and lemmatisation?\n2. What is TF-IDF and why is it better than simple word counts?\n3. What is a Transformer model and what problem does it solve?', 5, 1, '2026-04-15 07:00:00'),
(136, 18, 'Generative AI & Large Language Models', '## What is Generative AI?\n\nGenerative AI creates new content — text, images, audio, video, code — that did not exist before. It is powered by large models trained on massive datasets.\n\n## Large Language Models (LLMs)\n\nLLMs are neural networks trained on billions of text tokens. They learn to predict the next token, enabling coherent text generation.\n\nKey LLMs:\n- GPT-4o / GPT-5 (OpenAI): Most capable general-purpose LLM\n- Claude 3.5 (Anthropic): Strong reasoning and safety\n- Gemini 1.5 (Google): Multimodal, long context\n- Llama 3 (Meta): Open-source, can run locally\n\n## Using OpenAI Responses API\n\n```python\nfrom openai import OpenAI\n\nclient = OpenAI(api_key=\'your-api-key\')\n\n# Responses API (gpt-5-nano)\nresponse = client.responses.create(\n    model=\'gpt-5-nano\',\n    instructions=\'You are a helpful tutor for Nigerian students.\',\n    input=\'Explain machine learning in simple terms.\',\n    store=True\n)\nprint(response.output_text)\n```\n\n## Prompt Engineering\n\n- Zero-shot: Ask directly without examples\n- Few-shot: Provide 2-3 examples before the question\n- Chain-of-thought: Ask the model to think step by step\n- Role prompting: Assign a persona (You are an expert...)\n- Structured output: Ask for JSON, tables, or specific formats\n\n## RAG (Retrieval-Augmented Generation)\n\nRAG combines LLMs with a knowledge base:\n1. Store documents in a vector database (Pinecone, ChromaDB)\n2. Convert query to embedding\n3. Retrieve most similar documents\n4. Feed documents + query to LLM\n5. LLM generates answer grounded in your documents\n\n## Practical Task\n\nBuild an AI tutor chatbot for one of your courses using the OpenAI API. Features: (1) Answer questions about course content, (2) Generate practice questions, (3) Explain concepts at different difficulty levels.\n\n## Self-Check\n1. What is the key innovation of the Transformer architecture?\n2. What is prompt engineering and why does it matter?\n3. What is RAG and when would you use it instead of fine-tuning?', 6, 1, '2026-04-15 07:00:00'),
(137, 18, 'AI Ethics, Safety & Responsible AI', '## Why AI Ethics Matters\n\nAI systems can cause significant harm if not designed and deployed responsibly.\n\n## Key AI Ethics Principles\n\n- Fairness: AI should not discriminate based on protected characteristics\n- Transparency: Users should understand how AI makes decisions\n- Accountability: Someone must be responsible when AI causes harm\n- Privacy: AI should not violate users\' privacy\n- Safety: AI systems should be reliable and not cause unintended harm\n- Beneficence: AI should benefit humanity\n\n## AI Bias\n\nSources of bias:\n- Historical bias: Training data reflects past discrimination\n- Representation bias: Underrepresented groups in training data\n- Measurement bias: Proxy variables correlating with protected attributes\n\nExample: A hiring AI trained on historical data may discriminate against women if historically fewer women were hired.\n\n## Detecting Bias\n\n```python\nfrom sklearn.metrics import classification_report\n\n# Check performance by demographic group\nfor group in df[\'gender\'].unique():\n    mask = df[\'gender\'] == group\n    print(f\'--- {group} ---\')\n    print(classification_report(y_test[mask], y_pred[mask]))\n\n# Fairness metrics\nfrom fairlearn.metrics import demographic_parity_difference\ndpd = demographic_parity_difference(y_test, y_pred, sensitive_features=df[\'gender\'])\nprint(f\'Demographic Parity Difference: {dpd:.3f}\')\n```\n\n## AI Safety\n\n- Alignment: Ensuring AI pursues human-intended goals\n- Robustness: AI should work reliably in unexpected situations\n- Adversarial attacks: Inputs designed to fool AI models\n- Hallucination: LLMs generating false but confident information\n\n## AI Regulation\n\n- EU AI Act (2024): Risk-based regulation of AI systems\n- Nigeria: NITDA AI Policy Framework\n- US: Executive Order on AI Safety\n\n## Practical Task\n\nAudit a machine learning model for bias. Train a loan approval model. Check if the model has different approval rates for different demographic groups. Calculate fairness metrics. Propose 3 interventions to reduce bias.\n\n## Self-Check\n1. What is algorithmic bias and how does it arise?\n2. What is the difference between AI safety and AI ethics?\n3. What is the EU AI Act and what does it regulate?', 7, 1, '2026-04-15 07:00:00'),
(138, 18, 'Capstone: AI Application Project', '## Final Project Brief\n\nDesign and build a complete AI application that solves a real problem relevant to Nigeria or Africa.\n\n## Project Options (Choose One)\n\n### Option A: AI Health Assistant\nA chatbot that helps Nigerians understand medical symptoms and find nearby healthcare facilities.\n- NLP for symptom extraction from natural language\n- Medical knowledge base (RAG)\n- Multilingual support (English + Pidgin)\n- Integration with Google Maps API for facility lookup\n\n### Option B: Agricultural AI Advisor\nAn AI system that helps Nigerian farmers make better decisions.\n- Crop disease detection from photos (CNN)\n- Weather-based planting recommendations\n- Market price prediction (time series)\n- Voice interface in Hausa or Yoruba\n\n### Option C: Financial Inclusion AI\nAn AI system for credit scoring of unbanked Nigerians.\n- Alternative data features (mobile money, airtime usage)\n- Fairness-aware model (no discrimination by ethnicity/gender)\n- Explainable AI (SHAP values for each decision)\n- Simple mobile interface\n\n## Technical Requirements\n\n- At least 2 AI/ML components\n- Training data: minimum 1,000 examples\n- Model evaluation with appropriate metrics\n- Bias audit with fairness metrics\n- Deployed application (Streamlit, Flask, or mobile)\n- API integration (OpenAI or similar)\n\n## Deliverables\n\n1. GitHub repository with clean, documented code\n2. Jupyter Notebook with model development\n3. Deployed application (live URL)\n4. 15-minute demo presentation\n5. Technical report (5 pages)\n6. Ethical impact assessment\n\n## Evaluation Criteria\n- Problem relevance and impact (20%)\n- Technical implementation quality (30%)\n- Model performance (20%)\n- Responsible AI practices (15%)\n- Presentation and documentation (15%)\n\n## Self-Check\n1. Does your application solve a real problem for real people?\n2. Have you tested your model for bias and fairness?\n3. Can a non-technical user understand and use your application?', 8, 1, '2026-04-15 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `lms_lesson_assessments`
--

CREATE TABLE `lms_lesson_assessments` (
  `id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `type` enum('test','practical','assignment') NOT NULL DEFAULT 'test',
  `title` varchar(200) NOT NULL,
  `instructions` text DEFAULT NULL,
  `pass_score` tinyint(3) UNSIGNED NOT NULL DEFAULT 60,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_lesson_assessments`
--

INSERT INTO `lms_lesson_assessments` (`id`, `lesson_id`, `course_id`, `type`, `title`, `instructions`, `pass_score`, `is_required`, `created_at`) VALUES
(1, 1, 1, 'test', 'Lesson 1 Test: Graphic Design Fundamentals', 'Answer all 5 questions. You need 60% to proceed to the next lesson.', 60, 1, '2026-04-15 07:00:00'),
(2, 2, 1, 'practical', 'Lesson 2 Practical: Colour Theory Application', 'Create a business card design using a complementary colour palette. Apply at least 2 typography rules. Submit a screenshot of your design.', 60, 1, '2026-04-15 07:00:00'),
(3, 3, 1, 'assignment', 'Lesson 3 Assignment: Logo Design Project', 'Design a logo for a fictional Nigerian tech startup called \"NovaByte\". Create 3 concepts on paper, develop the strongest digitally. Submit your final logo on white and dark backgrounds.', 60, 1, '2026-04-15 07:00:00'),
(4, 4, 1, 'test', 'Lesson 4 Test: Print Design & Layout', 'Answer all questions about print design fundamentals.', 60, 1, '2026-04-15 07:00:00'),
(5, 5, 1, 'practical', 'Lesson 5 Practical: Social Media Campaign', 'Create a 3-post social media campaign for a fictional brand. Design for Instagram (1080x1080), Facebook (1200x630), and an Instagram Story (1080x1920). Submit all 3 designs.', 60, 1, '2026-04-15 07:00:00'),
(6, 6, 1, 'practical', 'Lesson 6 Practical: Photo Editing', 'Take a portrait photo (your own or from Unsplash). Perform: skin smoothing, background removal, colour grading with Curves, and add a text overlay. Submit before and after images.', 60, 1, '2026-04-15 07:00:00'),
(7, 7, 1, 'assignment', 'Lesson 7 Assignment: Portfolio Case Study', 'Create a Behance project for one of your designs from this course. Write a full case study with: The Challenge, The Process, The Solution, and The Result. Include process images alongside the final design.', 60, 1, '2026-04-15 07:00:00'),
(8, 9, 3, 'test', 'Lesson 1 Test: Web Design Foundations', 'Test your understanding of web design fundamentals.', 60, 1, '2026-04-15 07:00:00'),
(9, 10, 3, 'test', 'Lesson 2 Test: UI Design Fundamentals', 'Test your knowledge of UI design components and principles.', 60, 1, '2026-04-15 07:00:00'),
(10, 11, 3, 'practical', 'Lesson 3 Practical: UX Research', 'Conduct 3 user interviews about a website or app you use regularly. Create 1 user persona based on your findings. Write 3 Jobs-to-be-Done statements. Submit your persona document.', 60, 1, '2026-04-15 07:00:00'),
(11, 12, 3, 'practical', 'Lesson 4 Practical: Responsive Layout', 'Build a responsive 3-column card grid using CSS Grid. On mobile: 1 column. On tablet: 2 columns. On desktop: 3 columns. Each card must have an image, title, description, and button. Submit your code.', 60, 1, '2026-04-15 07:00:00'),
(12, 13, 3, 'practical', 'Lesson 5 Practical: CSS Design System', 'Create a CSS design system file (variables.css) for a fictional brand. Define: typography scale, colour palette, spacing scale, and border radius values using CSS custom properties. Apply them to a simple landing page.', 60, 1, '2026-04-15 07:00:00'),
(13, 14, 3, 'practical', 'Lesson 6 Practical: Figma Prototype', 'Design a complete mobile app screen set in Figma (5 screens): Splash, Onboarding, Home, Profile, Settings. Use components for the navigation bar and buttons. Create a clickable prototype connecting all screens. Submit the Figma share link.', 60, 1, '2026-04-15 07:00:00'),
(14, 15, 3, 'test', 'Lesson 7 Test: Website Performance & SEO', 'Test your knowledge of web performance and SEO fundamentals.', 60, 1, '2026-04-15 07:00:00'),
(15, 17, 4, 'practical', 'Lesson 1 Practical: HTML Profile Page', 'Build a personal profile page using only HTML (no CSS). Include: header with your name, navigation bar, about section, skills list, projects table, and a contact form. Use semantic elements throughout. Submit your HTML file.', 60, 1, '2026-04-15 07:00:00'),
(16, 18, 4, 'practical', 'Lesson 2 Practical: CSS Styling', 'Style the HTML profile page from Lesson 1. Create a responsive layout using Flexbox and Grid. Add a colour scheme using CSS variables. Include hover effects on links and buttons. Make it fully responsive for mobile.', 60, 1, '2026-04-15 07:00:00'),
(17, 19, 4, 'practical', 'Lesson 3 Practical: JavaScript To-Do List', 'Build an interactive to-do list using HTML, CSS, and JavaScript. Features: add tasks, mark as complete (toggle class), delete tasks, show task count. Store tasks in localStorage so they persist on page refresh. Submit your code.', 60, 1, '2026-04-15 07:00:00'),
(18, 20, 4, 'practical', 'Lesson 4 Practical: PHP Contact Form', 'Build a contact form with PHP validation. Fields: name, email, message. Validate all fields server-side. Show success message on valid submission. Show specific error messages for each invalid field. Use sessions to persist form data on error.', 60, 1, '2026-04-15 07:00:00'),
(19, 21, 4, 'practical', 'Lesson 5 Practical: MySQL Blog Database', 'Design a database for a simple blog. Tables: users, posts, categories, comments, post_categories. Write the CREATE TABLE statements with proper data types, constraints, and foreign keys. Insert sample data and write 5 SELECT queries.', 60, 1, '2026-04-15 07:00:00'),
(20, 22, 4, 'assignment', 'Lesson 6 Assignment: Blog Application', 'Build a complete blog application with: user registration/login, create/read/update/delete posts, categories, comment system, and search. Deploy to your local XAMPP server. Submit screenshots of all features working.', 60, 1, '2026-04-15 07:00:00'),
(21, 23, 4, 'test', 'Lesson 7 Test: APIs & JavaScript Frameworks', 'Test your knowledge of REST APIs and modern JavaScript.', 60, 1, '2026-04-15 07:00:00'),
(22, 107, 2, 'test', 'Lesson 1 Test: Advanced Typography', 'Test your advanced typography knowledge.', 60, 1, '2026-04-15 07:00:00'),
(23, 108, 2, 'test', 'Lesson 2 Test: Colour Systems', 'Test your knowledge of advanced colour systems.', 60, 1, '2026-04-15 07:00:00'),
(24, 109, 2, 'practical', 'Lesson 3 Practical: Editorial Layout', 'Design a magazine cover and 2-page spread using a modular grid. Apply the rule of thirds for the cover image. Use the F-pattern for the article layout. Submit at A4 size.', 60, 1, '2026-04-15 07:00:00'),
(25, 110, 2, 'test', 'Lesson 4 Test: Motion Graphics', 'Test your knowledge of animation principles.', 60, 1, '2026-04-15 07:00:00'),
(26, 111, 2, 'assignment', 'Lesson 5 Assignment: Brand Strategy', 'Conduct a brand audit for a real or fictional company. Analyse their current brand identity, positioning, and consistency. Write a 1-page brand strategy recommendation with a positioning statement.', 60, 1, '2026-04-15 07:00:00'),
(27, 112, 2, 'practical', 'Lesson 6 Practical: Freelance Proposal', 'Write a project proposal and contract for a fictional logo design project. Include: scope of work, timeline, payment terms, revision rounds, kill fee clause, and IP ownership. Submit as a PDF.', 60, 1, '2026-04-15 07:00:00'),
(28, 25, 5, 'test', 'Lesson 1 Test: PHP Syntax', 'Test your PHP fundamentals.', 60, 1, '2026-04-15 07:00:00'),
(29, 26, 5, 'practical', 'Lesson 2 Practical: OOP Library System', 'Build a library management system using OOP. Classes: Book, Member, Library. Methods: addBook(), registerMember(), borrowBook(), returnBook(). Submit your PHP file.', 60, 1, '2026-04-15 07:00:00'),
(30, 27, 5, 'test', 'Lesson 3 Test: Advanced MySQL', 'Test your advanced MySQL knowledge.', 60, 1, '2026-04-15 07:00:00'),
(31, 28, 5, 'test', 'Lesson 4 Test: Security', 'Test your PHP security knowledge.', 60, 1, '2026-04-15 07:00:00'),
(32, 29, 5, 'practical', 'Lesson 5 Practical: REST API', 'Build a complete REST API for a course catalogue. Endpoints: GET /api/courses, GET /api/courses/{id}, POST /api/courses, PUT /api/courses/{id}, DELETE /api/courses/{id}. Test all endpoints in Postman. Submit code and screenshots.', 60, 1, '2026-04-15 07:00:00'),
(33, 30, 5, 'assignment', 'Lesson 6 Assignment: E-Commerce App', 'Build a complete e-commerce store with: product catalogue, shopping cart, checkout, user auth, and admin panel. Deploy to XAMPP. Submit screenshots of all features.', 60, 1, '2026-04-15 07:00:00'),
(34, 33, 6, 'test', 'Flutter Basics Test', 'Test your Flutter fundamentals.', 60, 1, '2026-04-15 07:00:00'),
(35, 34, 6, 'practical', 'Firebase Practical: Note App', 'Build a note-taking app with Firebase. Features: email/password auth, CRUD notes in Firestore, real-time sync. Each user sees only their own notes. Submit screenshots.', 60, 1, '2026-04-15 07:00:00'),
(36, 41, 7, 'practical', 'UX Research Practical', 'Conduct 3 user interviews about a digital product. Create 2 user personas. Write 5 Jobs-to-be-Done statements. Build a sitemap for a 10-page website. Submit your research document.', 60, 1, '2026-04-15 07:00:00'),
(37, 42, 7, 'test', 'Design Systems Test', 'Test your knowledge of design systems and accessibility.', 60, 1, '2026-04-15 07:00:00'),
(38, 49, 8, 'test', 'SEO Test', 'Test your SEO and digital marketing knowledge.', 60, 1, '2026-04-15 07:00:00'),
(39, 50, 8, 'practical', 'Email Campaign Practical', 'Create a 5-email welcome sequence for a fictional online course platform. Write subject lines, preview text, and body copy for each email. Set up the automation flow in Mailchimp. Submit screenshots.', 60, 1, '2026-04-15 07:00:00'),
(40, 57, 9, 'practical', 'EDA Practical', 'Download a Nigerian dataset from Kaggle. Load into pandas. Perform: data cleaning, descriptive statistics, correlation analysis, 3 visualisations. Write a 1-paragraph insight summary. Submit your Jupyter Notebook.', 60, 1, '2026-04-15 07:00:00'),
(41, 58, 9, 'test', 'Statistics Test', 'Test your statistical analysis knowledge.', 60, 1, '2026-04-15 07:00:00'),
(42, 65, 10, 'test', 'Cybersecurity Fundamentals Test', 'Test your cybersecurity knowledge.', 60, 1, '2026-04-15 07:00:00'),
(43, 66, 10, 'practical', 'Network Security Practical', 'Install Wireshark. Capture 5 minutes of network traffic. Identify: top 5 protocols, any unencrypted HTTP traffic, DNS queries, and most active IP addresses. Write a brief security assessment report.', 60, 1, '2026-04-15 07:00:00'),
(44, 71, 11, 'test', 'Computer Fundamentals Test', 'Test your computer basics knowledge.', 60, 1, '2026-04-15 07:00:00'),
(45, 72, 11, 'practical', 'Office Productivity Practical', 'Create: (1) A professional CV in Word using styles. (2) A budget spreadsheet in Excel with SUM/IF formulas and a chart. (3) A 10-slide presentation in PowerPoint applying the 10-20-30 rule. Submit all 3 files.', 60, 1, '2026-04-15 07:00:00'),
(46, 77, 12, 'practical', 'Desktop App Practical: Calculator', 'Build a calculator app using Tkinter with number buttons, operations, display, clear, and equals. Handle arithmetic correctly. Submit your Python file.', 60, 1, '2026-04-15 07:00:00'),
(47, 78, 12, 'test', 'Desktop Dev Advanced Test', 'Test your knowledge of advanced desktop development.', 60, 1, '2026-04-15 07:00:00'),
(48, 83, 13, 'test', 'POS Operations Test', 'Test your POS and ICT support knowledge.', 60, 1, '2026-04-15 07:00:00'),
(49, 84, 13, 'practical', 'ICT Support Runbook', 'Create an ICT support runbook for 5 common issues: Wi-Fi not connecting, printer not printing, Outlook not receiving emails, computer running slowly, user forgot password. Each must have step-by-step troubleshooting. Submit as a document.', 60, 1, '2026-04-15 07:00:00'),
(50, 89, 14, 'test', 'Networking Fundamentals Test', 'Test your networking knowledge.', 60, 1, '2026-04-15 07:00:00'),
(51, 90, 14, 'practical', 'Network Design Practical', 'Using Cisco Packet Tracer, build a network with 2 routers, 2 switches, and 4 PCs (2 per switch). Configure IP addresses, default gateways, and static routes so all PCs can ping each other. Submit a screenshot of successful pings.', 60, 1, '2026-04-15 07:00:00'),
(52, 95, 15, 'test', 'Cloud Computing Test', 'Test your cloud computing knowledge.', 60, 1, '2026-04-15 07:00:00'),
(53, 96, 15, 'practical', 'AWS Deployment Practical', 'Launch a free-tier EC2 instance (t2.micro, Amazon Linux 2). Connect via SSH. Install Apache. Create a simple HTML page. Access it via the public IP. Submit a screenshot of your working website.', 60, 1, '2026-04-15 07:00:00'),
(54, 101, 16, 'test', 'Software Engineering Test', 'Test your software engineering knowledge.', 60, 1, '2026-04-15 07:00:00'),
(55, 102, 16, 'practical', 'System Design Practical', 'Design the system architecture for a Nigerian ride-hailing app. Consider: user auth, real-time driver location, ride matching, payment processing, notifications, and ratings. Draw the architecture diagram and explain your technology choices. Submit as a document or diagram.', 60, 1, '2026-04-15 07:00:00'),
(56, 115, 17, 'test', 'Data Science Intro Test', 'Test your data science fundamentals.', 60, 1, '2026-04-15 07:00:00'),
(57, 116, 17, 'practical', 'Python EDA Practical', 'Download a Nigerian dataset from Kaggle. Load into pandas. Perform: data cleaning, descriptive statistics, correlation analysis, 3 visualisations. Write a 1-paragraph insight summary. Submit your Jupyter Notebook.', 60, 1, '2026-04-15 07:00:00'),
(58, 131, 18, 'test', 'AI Fundamentals Test', 'Test your AI knowledge.', 60, 1, '2026-04-15 07:00:00'),
(59, 132, 18, 'practical', 'AI Application Practical', 'Build an AI tutor chatbot using the OpenAI API. Features: answer questions about a course topic, generate practice questions, explain concepts at different difficulty levels. Submit your code and a screenshot of it working.', 60, 1, '2026-04-15 07:00:00'),
(60, 123, 19, 'test', 'ML Fundamentals Test', 'Test your machine learning knowledge.', 60, 1, '2026-04-15 07:00:00'),
(61, 124, 19, 'practical', 'ML Deployment Practical', 'Deploy your best ML model as a Flask REST API. Endpoint: POST /predict with customer features, returns prediction and probability. Test with Postman or curl. Submit your code and a screenshot of a successful prediction.', 60, 1, '2026-04-15 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `lms_lesson_completions`
--

CREATE TABLE `lms_lesson_completions` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_lesson_completions`
--

INSERT INTO `lms_lesson_completions` (`id`, `student_id`, `lesson_id`, `course_id`, `completed_at`) VALUES
(19, 4, 57, 9, '2026-04-16 19:06:34'),
(22, 4, 71, 11, '2026-04-16 20:09:34');

-- --------------------------------------------------------

--
-- Table structure for table `lms_live_sessions`
--

CREATE TABLE `lms_live_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `instructor_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `anydesk_id` varchar(100) DEFAULT NULL,
  `meeting_link` varchar(500) DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `duration_minutes` int(10) UNSIGNED NOT NULL DEFAULT 60,
  `recording_url` varchar(500) DEFAULT NULL,
  `status` enum('scheduled','live','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `max_students` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_modules`
--

CREATE TABLE `lms_modules` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `position` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_modules`
--

INSERT INTO `lms_modules` (`id`, `course_id`, `title`, `description`, `position`, `created_at`) VALUES
(1, 1, 'Design Fundamentals', 'Design Fundamentals for Graphic Design.', 1, '2026-02-16 07:10:44'),
(2, 1, 'Photoshop Essentials', 'Photoshop Essentials for Graphic Design.', 2, '2026-02-16 07:10:44'),
(3, 1, 'Branding & Print', 'Branding & Print for Graphic Design.', 3, '2026-02-16 07:10:44'),
(4, 1, 'Portfolio & Freelance', 'Portfolio & Freelance for Graphic Design.', 4, '2026-02-16 07:10:44'),
(5, 2, 'Module 1: Foundations', 'Module 1: Foundations for Advanced Graphic Design.', 1, '2026-02-16 07:10:44'),
(6, 2, 'Module 2: Tools & Workflow', 'Module 2: Tools & Workflow for Advanced Graphic Design.', 2, '2026-02-16 07:10:44'),
(7, 2, 'Module 3: Projects', 'Module 3: Projects for Advanced Graphic Design.', 3, '2026-02-16 07:10:44'),
(8, 2, 'Module 4: Capstone & Career', 'Module 4: Capstone & Career for Advanced Graphic Design.', 4, '2026-02-16 07:10:44'),
(9, 3, 'Module 1: Foundations', 'Module 1: Foundations for Web Design.', 1, '2026-02-16 07:10:44'),
(10, 3, 'Module 2: Tools & Workflow', 'Module 2: Tools & Workflow for Web Design.', 2, '2026-02-16 07:10:44');

-- --------------------------------------------------------

--
-- Table structure for table `lms_payments`
--

CREATE TABLE `lms_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `enrollment_id` int(10) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `channel` enum('paystack','manual') NOT NULL DEFAULT 'paystack',
  `reference` varchar(120) DEFAULT NULL,
  `status` enum('pending','success','failed','reversed') NOT NULL DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `manual_note` varchar(255) DEFAULT NULL,
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_payments`
--

INSERT INTO `lms_payments` (`id`, `student_id`, `enrollment_id`, `amount`, `channel`, `reference`, `status`, `paid_at`, `created_at`, `manual_note`, `approved_by`, `approved_at`) VALUES
(19, 4, 14, 280000.00, 'paystack', 'LMS_34609ed11b33', 'failed', NULL, '2026-04-16 18:59:33', NULL, NULL, NULL),
(20, 4, 14, 280000.00, '', 'LMS_1566d91c868f', 'success', '2026-04-16 20:05:28', '2026-04-16 19:04:25', NULL, NULL, NULL),
(21, 4, 15, 100000.00, 'paystack', 'LMS_ff2d7da7d76f', 'failed', NULL, '2026-04-16 19:32:25', NULL, NULL, NULL),
(22, 4, 15, 100000.00, 'paystack', 'LMS_36367d5d171f', 'failed', NULL, '2026-04-16 19:37:42', NULL, NULL, NULL),
(23, 4, 15, 100000.00, 'paystack', 'LMS_7f8d3e3714b1', 'failed', NULL, '2026-04-16 20:08:22', NULL, NULL, NULL),
(24, 4, 15, 100000.00, '', 'LMS_aca3735081c5', 'success', '2026-04-16 21:09:12', '2026-04-16 20:08:35', NULL, NULL, NULL),
(25, 4, 16, 150000.00, '', 'LMS_963bdf2f6a66', 'success', '2026-04-19 03:04:50', '2026-04-19 02:03:01', NULL, NULL, NULL),
(26, 4, 17, 180000.00, '', 'LMS_c2d917ca283d', 'success', '2026-04-19 03:14:19', '2026-04-19 02:12:46', NULL, NULL, NULL),
(27, 4, 18, 130000.00, 'paystack', 'LMS_f506251cd17c', 'failed', NULL, '2026-04-19 02:20:58', NULL, NULL, NULL),
(28, 4, 18, 130000.00, '', 'LMS_934c0d9bbc36', 'success', '2026-04-19 04:45:22', '2026-04-19 03:43:57', NULL, NULL, NULL),
(29, 4, 18, 130000.00, 'paystack', 'LMS_5322ee5b999a', 'failed', NULL, '2026-04-19 03:54:27', NULL, NULL, NULL),
(30, 4, 19, 175000.00, 'paystack', 'LMS_fb60f9e70a1e', 'failed', NULL, '2026-04-19 13:33:55', NULL, NULL, NULL),
(31, 4, 19, 175000.00, 'manual', 'MANUAL_1033917b4a57', 'success', '2026-04-19 14:35:37', '2026-04-19 13:34:06', '', 1, '2026-04-19 15:35:37'),
(32, 4, 20, 300000.00, '', 'LMS_07336aad2583', 'success', '2026-04-19 14:45:28', '2026-04-19 13:44:51', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lms_progress`
--

CREATE TABLE `lms_progress` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `completed_lessons` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_lessons` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_progress`
--

INSERT INTO `lms_progress` (`id`, `student_id`, `course_id`, `completed_lessons`, `total_lessons`, `percent`, `updated_at`) VALUES
(19, 4, 9, 1, 8, 12.50, '2026-04-16 19:06:34'),
(22, 4, 11, 1, 6, 16.67, '2026-04-16 20:09:34');

-- --------------------------------------------------------

--
-- Table structure for table `lms_session_attendance`
--

CREATE TABLE `lms_session_attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_session_chat_messages`
--

CREATE TABLE `lms_session_chat_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED DEFAULT NULL,
  `instructor_id` int(10) UNSIGNED DEFAULT NULL,
  `sender_name` varchar(150) NOT NULL,
  `sender_role` enum('student','instructor','admin','system') NOT NULL DEFAULT 'student',
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_session_participants`
--

CREATE TABLE `lms_session_participants` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `participant_key` varchar(64) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `display_name` varchar(150) NOT NULL,
  `role` enum('student','instructor','admin') NOT NULL DEFAULT 'student',
  `last_seen_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_session_signals`
--

CREATE TABLE `lms_session_signals` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `from_key` varchar(64) NOT NULL,
  `to_key` varchar(64) NOT NULL,
  `signal_type` enum('offer','answer','ice') NOT NULL,
  `payload` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_settings`
--

CREATE TABLE `lms_settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_students`
--

CREATE TABLE `lms_students` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `other_names` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `nationality` varchar(80) DEFAULT NULL,
  `country` varchar(80) DEFAULT NULL,
  `state_of_origin` varchar(80) DEFAULT NULL,
  `lga` varchar(80) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `course` varchar(150) DEFAULT NULL,
  `course_price` decimal(10,2) DEFAULT NULL,
  `payment_option` enum('full','installment') DEFAULT NULL,
  `kyc_type` varchar(100) DEFAULT NULL,
  `kyc_number` varchar(100) DEFAULT NULL,
  `passport` varchar(255) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `lms_videos`
--

CREATE TABLE `lms_videos` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `video_path` varchar(255) NOT NULL,
  `duration_seconds` int(10) UNSIGNED DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_videos`
--

INSERT INTO `lms_videos` (`id`, `course_id`, `lesson_id`, `title`, `video_path`, `duration_seconds`, `is_published`, `created_at`) VALUES
(1, 1, NULL, 'Graphic Design Fundamentals - Full Course', 'https://www.youtube.com/watch?v=WONZVnlam6U', 4800, 1, '2026-02-16 06:11:08'),
(2, 1, NULL, 'Colour Theory for Designers', 'https://www.youtube.com/watch?v=_2LLXnUdUIc', 1200, 1, '2026-02-16 06:11:08'),
(3, 1, NULL, 'Typography Basics for Beginners', 'https://www.youtube.com/watch?v=sByzHoiYFX0', 900, 1, '2026-02-16 06:11:08'),
(4, 1, NULL, 'Logo Design Process Step by Step', 'https://www.youtube.com/watch?v=dKjYBhFvMsI', 1800, 1, '2026-02-16 06:11:08'),
(5, 1, NULL, 'Adobe Illustrator for Beginners', 'https://www.youtube.com/watch?v=Ib8UBwu3yGA', 3600, 1, '2026-02-16 06:11:08'),
(6, 1, NULL, 'Adobe Photoshop Full Tutorial', 'https://www.youtube.com/watch?v=IyR_uYsRdPs', 5400, 1, '2026-02-16 06:11:08'),
(7, 1, NULL, 'Print Design & Layout Basics', 'https://www.youtube.com/watch?v=a5KYlZiJFx0', 1500, 1, '2026-02-16 06:11:08'),
(8, 1, NULL, 'Building a Design Portfolio', 'https://www.youtube.com/watch?v=V4YCfFBKFhA', 1200, 1, '2026-02-16 06:11:08'),
(9, 2, NULL, 'Advanced Typography Techniques', 'https://www.youtube.com/watch?v=QrNi9FmdlxY', 2400, 1, '2026-02-16 06:11:08'),
(10, 2, NULL, 'Brand Identity Design Process', 'https://www.youtube.com/watch?v=l-S2Y3SF3jM', 3000, 1, '2026-02-16 06:11:08'),
(11, 2, NULL, 'Motion Graphics with After Effects', 'https://www.youtube.com/watch?v=52S_Q3QnMXE', 4200, 1, '2026-02-16 06:11:08'),
(12, 2, NULL, 'Packaging Design Tutorial', 'https://www.youtube.com/watch?v=Ib8UBwu3yGA', 2100, 1, '2026-02-16 06:11:08'),
(13, 2, NULL, 'Advanced Branding Strategy', 'https://www.youtube.com/watch?v=l-S2Y3SF3jM', 1800, 1, '2026-02-16 06:11:08'),
(14, 2, NULL, 'Freelance Design Business Tips', 'https://www.youtube.com/watch?v=V4YCfFBKFhA', 1500, 1, '2026-02-16 06:11:08'),
(15, 3, NULL, 'Web Design for Beginners - Full Course', 'https://www.youtube.com/watch?v=mU6anWqZJcc', 5400, 1, '2026-02-16 06:11:08'),
(16, 3, NULL, 'UI Design Fundamentals', 'https://www.youtube.com/watch?v=tRpoI6vkqLs', 2700, 1, '2026-02-16 06:11:08'),
(17, 3, NULL, 'UX Design Process Explained', 'https://www.youtube.com/watch?v=wIuVvCuiJhU', 2400, 1, '2026-02-16 06:11:08'),
(18, 3, NULL, 'Responsive Web Design Tutorial', 'https://www.youtube.com/watch?v=srvUrASNj0s', 3600, 1, '2026-02-16 06:11:08'),
(19, 3, NULL, 'Figma Tutorial for Beginners', 'https://www.youtube.com/watch?v=FTFaQWZBqQ8', 4800, 1, '2026-02-16 06:11:08'),
(20, 3, NULL, 'CSS Flexbox and Grid Tutorial', 'https://www.youtube.com/watch?v=phWxA89Dy94', 3000, 1, '2026-02-16 06:11:08'),
(21, 3, NULL, 'Website Performance Optimisation', 'https://www.youtube.com/watch?v=AQqFZ5t8uNc', 1800, 1, '2026-02-16 06:11:08'),
(22, 3, NULL, 'SEO Basics for Web Designers', 'https://www.youtube.com/watch?v=DvwS7cV9GmQ', 2100, 1, '2026-02-16 06:11:08'),
(23, 4, NULL, 'HTML Full Course for Beginners', 'https://www.youtube.com/watch?v=pQN-pnXPaVg', 7200, 1, '2026-02-16 06:11:08'),
(24, 4, NULL, 'CSS Tutorial - Zero to Hero', 'https://www.youtube.com/watch?v=1Rs2ND1ryYc', 6000, 1, '2026-02-16 06:11:08'),
(25, 4, NULL, 'JavaScript Full Course for Beginners', 'https://www.youtube.com/watch?v=PkZNo7MFNFg', 7200, 1, '2026-02-16 06:11:08'),
(26, 4, NULL, 'PHP Tutorial for Beginners', 'https://www.youtube.com/watch?v=OK_JCtrrv-c', 5400, 1, '2026-02-16 06:11:08'),
(27, 4, NULL, 'MySQL Database Tutorial', 'https://www.youtube.com/watch?v=7S_tz1z_5bA', 4800, 1, '2026-02-16 06:11:08'),
(28, 4, NULL, 'Build a Full Stack Web App', 'https://www.youtube.com/watch?v=Oe421EPjeBE', 9000, 1, '2026-02-16 06:11:08'),
(29, 4, NULL, 'REST API with PHP and MySQL', 'https://www.youtube.com/watch?v=OEWXbpUMODk', 3600, 1, '2026-02-16 06:11:08'),
(30, 4, NULL, 'Web Deployment Tutorial', 'https://www.youtube.com/watch?v=mBQmly7SIAM', 2400, 1, '2026-02-16 06:11:08'),
(31, 5, NULL, 'PHP OOP Full Course', 'https://www.youtube.com/watch?v=Anz0ArcQ5kI', 7200, 1, '2026-02-16 06:11:08'),
(32, 5, NULL, 'Advanced MySQL Queries', 'https://www.youtube.com/watch?v=7S_tz1z_5bA', 4800, 1, '2026-02-16 06:11:08'),
(33, 5, NULL, 'PHP Security Best Practices', 'https://www.youtube.com/watch?v=2_hh9oNMqAA', 3000, 1, '2026-02-16 06:11:08'),
(34, 5, NULL, 'Building REST APIs with PHP', 'https://www.youtube.com/watch?v=OEWXbpUMODk', 3600, 1, '2026-02-16 06:11:08'),
(35, 5, NULL, 'PHPMailer Email Tutorial', 'https://www.youtube.com/watch?v=JFZSE1vYb0k', 1800, 1, '2026-02-16 06:11:08'),
(36, 5, NULL, 'PHP Unit Testing with PHPUnit', 'https://www.youtube.com/watch?v=k9ak_rv9X0Y', 2400, 1, '2026-02-16 06:11:08'),
(37, 5, NULL, 'Build an E-Commerce App with PHP', 'https://www.youtube.com/watch?v=KLWA2vCERSQ', 9000, 1, '2026-02-16 06:11:08'),
(38, 6, NULL, 'Flutter Tutorial for Beginners', 'https://www.youtube.com/watch?v=VPvVD8t02U8', 7200, 1, '2026-02-16 06:11:08'),
(39, 6, NULL, 'Flutter UI Design Tutorial', 'https://www.youtube.com/watch?v=x0uinJvhNxI', 4800, 1, '2026-02-16 06:11:08'),
(40, 6, NULL, 'Flutter State Management with Provider', 'https://www.youtube.com/watch?v=L_QMsE2v6dw', 3600, 1, '2026-02-16 06:11:08'),
(41, 6, NULL, 'Firebase with Flutter Tutorial', 'https://www.youtube.com/watch?v=sfA3NWDBPZ4', 5400, 1, '2026-02-16 06:11:08'),
(42, 6, NULL, 'Flutter App Deployment to Play Store', 'https://www.youtube.com/watch?v=g0GNuoCOtaQ', 2400, 1, '2026-02-16 06:11:08'),
(43, 6, NULL, 'Flutter Animations Tutorial', 'https://www.youtube.com/watch?v=CRRQMFMkFAE', 3000, 1, '2026-02-16 06:11:08'),
(44, 6, NULL, 'Flutter App Monetisation', 'https://www.youtube.com/watch?v=Lf-8USgBmFE', 1800, 1, '2026-02-16 06:11:08'),
(45, 7, NULL, 'UX Design Full Course', 'https://www.youtube.com/watch?v=wIuVvCuiJhU', 5400, 1, '2026-02-16 06:11:08'),
(46, 7, NULL, 'User Research Methods', 'https://www.youtube.com/watch?v=tRpoI6vkqLs', 2700, 1, '2026-02-16 06:11:08'),
(47, 7, NULL, 'Information Architecture Tutorial', 'https://www.youtube.com/watch?v=Ovj4hFxko7c', 1800, 1, '2026-02-16 06:11:08'),
(48, 7, NULL, 'Figma Prototyping Tutorial', 'https://www.youtube.com/watch?v=FTFaQWZBqQ8', 4800, 1, '2026-02-16 06:11:08'),
(49, 7, NULL, 'Design Systems in Figma', 'https://www.youtube.com/watch?v=EK-pHkc5EL4', 3600, 1, '2026-02-16 06:11:08'),
(50, 7, NULL, 'Accessibility in UI Design', 'https://www.youtube.com/watch?v=20SHvU2PKsM', 2400, 1, '2026-02-16 06:11:08'),
(51, 7, NULL, 'UX Writing Fundamentals', 'https://www.youtube.com/watch?v=OinN0KLNUOU', 1500, 1, '2026-02-16 06:11:08'),
(52, 8, NULL, 'Digital Marketing Full Course', 'https://www.youtube.com/watch?v=nU7gFBBFMGk', 7200, 1, '2026-02-16 06:11:08'),
(53, 8, NULL, 'SEO Tutorial for Beginners', 'https://www.youtube.com/watch?v=DvwS7cV9GmQ', 4800, 1, '2026-02-16 06:11:08'),
(54, 8, NULL, 'Social Media Marketing Strategy', 'https://www.youtube.com/watch?v=q6RoHnGBFxs', 3600, 1, '2026-02-16 06:11:08'),
(55, 8, NULL, 'Email Marketing Tutorial', 'https://www.youtube.com/watch?v=Wcs2PFz5q6g', 2700, 1, '2026-02-16 06:11:08'),
(56, 8, NULL, 'Content Marketing Strategy', 'https://www.youtube.com/watch?v=lZD72ZFnNOI', 2400, 1, '2026-02-16 06:11:08'),
(57, 8, NULL, 'Google Ads Tutorial for Beginners', 'https://www.youtube.com/watch?v=lbCITfyMDfI', 3600, 1, '2026-02-16 06:11:08'),
(58, 8, NULL, 'Google Analytics 4 Tutorial', 'https://www.youtube.com/watch?v=d5_SFbFGCOA', 3000, 1, '2026-02-16 06:11:08'),
(59, 9, NULL, 'Data Analysis with Python - Full Course', 'https://www.youtube.com/watch?v=r-uOLxNrNk8', 7200, 1, '2026-02-16 06:11:08'),
(60, 9, NULL, 'Excel for Data Analysis Tutorial', 'https://www.youtube.com/watch?v=PSNXoAs2FtQ', 4800, 1, '2026-02-16 06:11:08'),
(61, 9, NULL, 'SQL for Data Analysis', 'https://www.youtube.com/watch?v=7mz73uXD9DA', 5400, 1, '2026-02-16 06:11:08'),
(62, 9, NULL, 'Python Pandas Tutorial', 'https://www.youtube.com/watch?v=vmEHCJofslg', 6000, 1, '2026-02-16 06:11:08'),
(63, 9, NULL, 'Data Visualisation with Python', 'https://www.youtube.com/watch?v=a9UrKTVEeZA', 3600, 1, '2026-02-16 06:11:08'),
(64, 9, NULL, 'Statistics for Data Science', 'https://www.youtube.com/watch?v=xxpc-HPKN28', 4200, 1, '2026-02-16 06:11:08'),
(65, 9, NULL, 'Power BI Tutorial for Beginners', 'https://www.youtube.com/watch?v=AGrl-H87pRU', 5400, 1, '2026-02-16 06:11:08'),
(66, 10, NULL, 'Cybersecurity Full Course for Beginners', 'https://www.youtube.com/watch?v=U_P23SqJaDc', 7200, 1, '2026-02-16 06:11:08'),
(67, 10, NULL, 'Network Security Fundamentals', 'https://www.youtube.com/watch?v=E03gh1huvW4', 3600, 1, '2026-02-16 06:11:08'),
(68, 10, NULL, 'Web Application Security - OWASP Top 10', 'https://www.youtube.com/watch?v=rWHvp7rUka8', 4800, 1, '2026-02-16 06:11:08'),
(69, 10, NULL, 'Ethical Hacking Full Course', 'https://www.youtube.com/watch?v=3Kq1MIfTWCE', 9000, 1, '2026-02-16 06:11:08'),
(70, 10, NULL, 'Kali Linux Tutorial for Beginners', 'https://www.youtube.com/watch?v=lZAoFs75_cs', 5400, 1, '2026-02-16 06:11:08'),
(71, 10, NULL, 'Incident Response Tutorial', 'https://www.youtube.com/watch?v=Lf-8USgBmFE', 2400, 1, '2026-02-16 06:11:08'),
(72, 11, NULL, 'Computer Basics Full Course', 'https://www.youtube.com/watch?v=y2kg3MOk1sY', 5400, 1, '2026-02-16 06:11:08'),
(73, 11, NULL, 'Windows 11 Tutorial for Beginners', 'https://www.youtube.com/watch?v=xABMFMkFAE', 3600, 1, '2026-02-16 06:11:08'),
(74, 11, NULL, 'Microsoft Office Full Tutorial', 'https://www.youtube.com/watch?v=PSNXoAs2FtQ', 7200, 1, '2026-02-16 06:11:08'),
(75, 11, NULL, 'Internet Safety and Security', 'https://www.youtube.com/watch?v=aO858HyFbKI', 2400, 1, '2026-02-16 06:11:08'),
(76, 11, NULL, 'Computer Troubleshooting Guide', 'https://www.youtube.com/watch?v=y2kg3MOk1sY', 3000, 1, '2026-02-16 06:11:08'),
(77, 12, NULL, 'Python Tkinter Tutorial for Beginners', 'https://www.youtube.com/watch?v=YXPyB4XeYLA', 5400, 1, '2026-02-16 06:11:08'),
(78, 12, NULL, 'PyQt5 Tutorial - Build Desktop Apps', 'https://www.youtube.com/watch?v=Vde5SH8e1OQ', 7200, 1, '2026-02-16 06:11:08'),
(79, 12, NULL, 'SQLite with Python Tutorial', 'https://www.youtube.com/watch?v=byHcYRpMgI4', 3600, 1, '2026-02-16 06:11:08'),
(80, 12, NULL, 'PyInstaller - Package Python Apps', 'https://www.youtube.com/watch?v=p3tSLatmGvU', 1800, 1, '2026-02-16 06:11:08'),
(81, 12, NULL, 'Python Threading Tutorial', 'https://www.youtube.com/watch?v=IEEhzQoKtQU', 2400, 1, '2026-02-16 06:11:08'),
(82, 13, NULL, 'POS System Tutorial for Beginners', 'https://www.youtube.com/watch?v=y2kg3MOk1sY', 3600, 1, '2026-02-16 06:11:08'),
(83, 13, NULL, 'IT Support Fundamentals', 'https://www.youtube.com/watch?v=qiQR5rTSshw', 5400, 1, '2026-02-16 06:11:08'),
(84, 13, NULL, 'Network Setup for Small Business', 'https://www.youtube.com/watch?v=E03gh1huvW4', 3000, 1, '2026-02-16 06:11:08'),
(85, 13, NULL, 'Customer Service Skills Training', 'https://www.youtube.com/watch?v=OinN0KLNUOU', 2400, 1, '2026-02-16 06:11:08'),
(86, 13, NULL, 'CompTIA A+ Study Guide', 'https://www.youtube.com/watch?v=87t6P5ZHTP0', 7200, 1, '2026-02-16 06:11:08'),
(87, 14, NULL, 'Computer Networking Full Course', 'https://www.youtube.com/watch?v=IPvYjXCsTg8', 7200, 1, '2026-02-16 06:11:08'),
(88, 14, NULL, 'IP Addressing and Subnetting', 'https://www.youtube.com/watch?v=s_gy4VJhNZM', 4800, 1, '2026-02-16 06:11:08'),
(89, 14, NULL, 'Cisco Packet Tracer Tutorial', 'https://www.youtube.com/watch?v=fCMFEBBFMFE', 5400, 1, '2026-02-16 06:11:08'),
(90, 14, NULL, 'Wireless Networking Tutorial', 'https://www.youtube.com/watch?v=E03gh1huvW4', 3600, 1, '2026-02-16 06:11:08'),
(91, 14, NULL, 'Network Security Fundamentals', 'https://www.youtube.com/watch?v=U_P23SqJaDc', 4200, 1, '2026-02-16 06:11:08'),
(92, 14, NULL, 'CompTIA Network+ Study Guide', 'https://www.youtube.com/watch?v=qiQR5rTSshw', 9000, 1, '2026-02-16 06:11:08'),
(93, 15, NULL, 'AWS Cloud Practitioner Full Course', 'https://www.youtube.com/watch?v=SOTamWNgDKc', 9000, 1, '2026-02-16 06:11:08'),
(94, 15, NULL, 'AWS Core Services Tutorial', 'https://www.youtube.com/watch?v=ulprqHHWlng', 7200, 1, '2026-02-16 06:11:08'),
(95, 15, NULL, 'Docker Tutorial for Beginners', 'https://www.youtube.com/watch?v=fqMOX6JJhGo', 5400, 1, '2026-02-16 06:11:08'),
(96, 15, NULL, 'AWS Well-Architected Framework', 'https://www.youtube.com/watch?v=vg5onp8TU6Q', 3600, 1, '2026-02-16 06:11:08'),
(97, 15, NULL, 'GitHub Actions CI/CD Tutorial', 'https://www.youtube.com/watch?v=R8_veQiYBjI', 4800, 1, '2026-02-16 06:11:08'),
(98, 15, NULL, 'Terraform Tutorial for Beginners', 'https://www.youtube.com/watch?v=SLB_c_ayRMo', 5400, 1, '2026-02-16 06:11:08'),
(99, 16, NULL, 'Software Engineering Full Course', 'https://www.youtube.com/watch?v=O753uuutqH8', 7200, 1, '2026-02-16 06:11:08'),
(100, 16, NULL, 'Software Architecture Patterns', 'https://www.youtube.com/watch?v=vqEg37e4Mkw', 4800, 1, '2026-02-16 06:11:08'),
(101, 16, NULL, 'Agile and Scrum Full Course', 'https://www.youtube.com/watch?v=502ILHjX9EE', 5400, 1, '2026-02-16 06:11:08'),
(102, 16, NULL, 'Software Testing Tutorial', 'https://www.youtube.com/watch?v=TDynSmrzpXw', 4200, 1, '2026-02-16 06:11:08'),
(103, 16, NULL, 'System Design Interview Guide', 'https://www.youtube.com/watch?v=i53Gi_K3o7I', 6000, 1, '2026-02-16 06:11:08'),
(104, 16, NULL, 'Clean Code Principles', 'https://www.youtube.com/watch?v=7EmboKQH8lM', 3600, 1, '2026-02-16 06:11:08');

-- --------------------------------------------------------

--
-- Table structure for table `ref_countries`
--

CREATE TABLE `ref_countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `iso2` char(2) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `phone_code` varchar(40) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ref_countries`
--

INSERT INTO `ref_countries` (`id`, `iso2`, `iso3`, `name`, `phone_code`, `created_at`) VALUES
(1, 'AF', 'AFG', 'Afghanistan', '+93', '2026-02-16 07:10:03'),
(2, 'AL', 'ALB', 'Albania', '+355', '2026-02-16 07:10:03'),
(3, 'DZ', 'DZA', 'Algeria', '+213', '2026-02-16 07:10:03'),
(4, 'AD', 'AND', 'Andorra', '+376', '2026-02-16 07:10:03'),
(5, 'AO', 'AGO', 'Angola', '+244', '2026-02-16 07:10:03'),
(6, 'AG', 'ATG', 'Antigua and Barbuda', '+1-268', '2026-02-16 07:10:03'),
(7, 'AR', 'ARG', 'Argentina', '+54', '2026-02-16 07:10:03'),
(8, 'AM', 'ARM', 'Armenia', '+374', '2026-02-16 07:10:03'),
(9, 'AU', 'AUS', 'Australia', '+61', '2026-02-16 07:10:03'),
(10, 'AT', 'AUT', 'Austria', '+43', '2026-02-16 07:10:03'),
(11, 'AZ', 'AZE', 'Azerbaijan', '+994', '2026-02-16 07:10:03'),
(12, 'BS', 'BHS', 'Bahamas', '+1-242', '2026-02-16 07:10:03'),
(13, 'BH', 'BHR', 'Bahrain', '+973', '2026-02-16 07:10:03'),
(14, 'BD', 'BGD', 'Bangladesh', '+880', '2026-02-16 07:10:03'),
(15, 'BB', 'BRB', 'Barbados', '+1-246', '2026-02-16 07:10:03'),
(16, 'BY', 'BLR', 'Belarus', '+375', '2026-02-16 07:10:03'),
(17, 'BE', 'BEL', 'Belgium', '+32', '2026-02-16 07:10:03'),
(18, 'BZ', 'BLZ', 'Belize', '+501', '2026-02-16 07:10:03'),
(19, 'BJ', 'BEN', 'Benin', '+229', '2026-02-16 07:10:03'),
(20, 'BT', 'BTN', 'Bhutan', '+975', '2026-02-16 07:10:03'),
(21, 'BO', 'BOL', 'Bolivia', '+591', '2026-02-16 07:10:03'),
(22, 'BA', 'BIH', 'Bosnia and Herzegovina', '+387', '2026-02-16 07:10:03'),
(23, 'BW', 'BWA', 'Botswana', '+267', '2026-02-16 07:10:03'),
(24, 'BR', 'BRA', 'Brazil', '+55', '2026-02-16 07:10:03'),
(25, 'BN', 'BRN', 'Brunei', '+673', '2026-02-16 07:10:03'),
(26, 'BG', 'BGR', 'Bulgaria', '+359', '2026-02-16 07:10:03'),
(27, 'BF', 'BFA', 'Burkina Faso', '+226', '2026-02-16 07:10:03'),
(28, 'BI', 'BDI', 'Burundi', '+257', '2026-02-16 07:10:03'),
(29, 'CV', 'CPV', 'Cabo Verde', '+238', '2026-02-16 07:10:03'),
(30, 'KH', 'KHM', 'Cambodia', '+855', '2026-02-16 07:10:03'),
(31, 'CM', 'CMR', 'Cameroon', '+237', '2026-02-16 07:10:03'),
(32, 'CA', 'CAN', 'Canada', '+1', '2026-02-16 07:10:03'),
(33, 'CF', 'CAF', 'Central African Republic', '+236', '2026-02-16 07:10:03'),
(34, 'TD', 'TCD', 'Chad', '+235', '2026-02-16 07:10:03'),
(35, 'CL', 'CHL', 'Chile', '+56', '2026-02-16 07:10:03'),
(36, 'CN', 'CHN', 'China', '+86', '2026-02-16 07:10:03'),
(37, 'CO', 'COL', 'Colombia', '+57', '2026-02-16 07:10:03'),
(38, 'KM', 'COM', 'Comoros', '+269', '2026-02-16 07:10:03'),
(39, 'CG', 'COG', 'Congo', '+242', '2026-02-16 07:10:03'),
(40, 'CD', 'COD', 'Congo (DRC)', '+243', '2026-02-16 07:10:03'),
(41, 'CR', 'CRI', 'Costa Rica', '+506', '2026-02-16 07:10:03'),
(42, 'HR', 'HRV', 'Croatia', '+385', '2026-02-16 07:10:03'),
(43, 'CU', 'CUB', 'Cuba', '+53', '2026-02-16 07:10:03'),
(44, 'CY', 'CYP', 'Cyprus', '+357', '2026-02-16 07:10:03'),
(45, 'CZ', 'CZE', 'Czech Republic', '+420', '2026-02-16 07:10:03'),
(46, 'DK', 'DNK', 'Denmark', '+45', '2026-02-16 07:10:03'),
(47, 'DJ', 'DJI', 'Djibouti', '+253', '2026-02-16 07:10:03'),
(48, 'DM', 'DMA', 'Dominica', '+1-767', '2026-02-16 07:10:03'),
(49, 'DO', 'DOM', 'Dominican Republic', '+1-809', '2026-02-16 07:10:03'),
(50, 'EC', 'ECU', 'Ecuador', '+593', '2026-02-16 07:10:03'),
(51, 'EG', 'EGY', 'Egypt', '+20', '2026-02-16 07:10:03'),
(52, 'SV', 'SLV', 'El Salvador', '+503', '2026-02-16 07:10:03'),
(53, 'GQ', 'GNQ', 'Equatorial Guinea', '+240', '2026-02-16 07:10:03'),
(54, 'ER', 'ERI', 'Eritrea', '+291', '2026-02-16 07:10:03'),
(55, 'EE', 'EST', 'Estonia', '+372', '2026-02-16 07:10:03'),
(56, 'SZ', 'SWZ', 'Eswatini', '+268', '2026-02-16 07:10:03'),
(57, 'ET', 'ETH', 'Ethiopia', '+251', '2026-02-16 07:10:03'),
(58, 'FJ', 'FJI', 'Fiji', '+679', '2026-02-16 07:10:03'),
(59, 'FI', 'FIN', 'Finland', '+358', '2026-02-16 07:10:03'),
(60, 'FR', 'FRA', 'France', '+33', '2026-02-16 07:10:03'),
(61, 'GA', 'GAB', 'Gabon', '+241', '2026-02-16 07:10:03'),
(62, 'GM', 'GMB', 'Gambia', '+220', '2026-02-16 07:10:03'),
(63, 'GE', 'GEO', 'Georgia', '+995', '2026-02-16 07:10:03'),
(64, 'DE', 'DEU', 'Germany', '+49', '2026-02-16 07:10:03'),
(65, 'GH', 'GHA', 'Ghana', '+233', '2026-02-16 07:10:03'),
(66, 'GR', 'GRC', 'Greece', '+30', '2026-02-16 07:10:03'),
(67, 'GD', 'GRD', 'Grenada', '+1-473', '2026-02-16 07:10:03'),
(68, 'GT', 'GTM', 'Guatemala', '+502', '2026-02-16 07:10:03'),
(69, 'GN', 'GIN', 'Guinea', '+224', '2026-02-16 07:10:03'),
(70, 'GW', 'GNB', 'Guinea-Bissau', '+245', '2026-02-16 07:10:03'),
(71, 'GY', 'GUY', 'Guyana', '+592', '2026-02-16 07:10:03'),
(72, 'HT', 'HTI', 'Haiti', '+509', '2026-02-16 07:10:03'),
(73, 'HN', 'HND', 'Honduras', '+504', '2026-02-16 07:10:03'),
(74, 'HU', 'HUN', 'Hungary', '+36', '2026-02-16 07:10:03'),
(75, 'IS', 'ISL', 'Iceland', '+354', '2026-02-16 07:10:03'),
(76, 'IN', 'IND', 'India', '+91', '2026-02-16 07:10:03'),
(77, 'ID', 'IDN', 'Indonesia', '+62', '2026-02-16 07:10:03'),
(78, 'IR', 'IRN', 'Iran', '+98', '2026-02-16 07:10:03'),
(79, 'IQ', 'IRQ', 'Iraq', '+964', '2026-02-16 07:10:03'),
(80, 'IE', 'IRL', 'Ireland', '+353', '2026-02-16 07:10:03'),
(81, 'IL', 'ISR', 'Israel', '+972', '2026-02-16 07:10:03'),
(82, 'IT', 'ITA', 'Italy', '+39', '2026-02-16 07:10:03'),
(83, 'JM', 'JAM', 'Jamaica', '+1-876', '2026-02-16 07:10:03'),
(84, 'JP', 'JPN', 'Japan', '+81', '2026-02-16 07:10:03'),
(85, 'JO', 'JOR', 'Jordan', '+962', '2026-02-16 07:10:03'),
(86, 'KZ', 'KAZ', 'Kazakhstan', '+7', '2026-02-16 07:10:03'),
(87, 'KE', 'KEN', 'Kenya', '+254', '2026-02-16 07:10:03'),
(88, 'KI', 'KIR', 'Kiribati', '+686', '2026-02-16 07:10:03'),
(89, 'KW', 'KWT', 'Kuwait', '+965', '2026-02-16 07:10:03'),
(90, 'KG', 'KGZ', 'Kyrgyzstan', '+996', '2026-02-16 07:10:03'),
(91, 'LA', 'LAO', 'Laos', '+856', '2026-02-16 07:10:03'),
(92, 'LV', 'LVA', 'Latvia', '+371', '2026-02-16 07:10:03'),
(93, 'LB', 'LBN', 'Lebanon', '+961', '2026-02-16 07:10:03'),
(94, 'LS', 'LSO', 'Lesotho', '+266', '2026-02-16 07:10:03'),
(95, 'LR', 'LBR', 'Liberia', '+231', '2026-02-16 07:10:03'),
(96, 'LY', 'LBY', 'Libya', '+218', '2026-02-16 07:10:03'),
(97, 'LI', 'LIE', 'Liechtenstein', '+423', '2026-02-16 07:10:03'),
(98, 'LT', 'LTU', 'Lithuania', '+370', '2026-02-16 07:10:03'),
(99, 'LU', 'LUX', 'Luxembourg', '+352', '2026-02-16 07:10:03'),
(100, 'MG', 'MDG', 'Madagascar', '+261', '2026-02-16 07:10:03'),
(101, 'MW', 'MWI', 'Malawi', '+265', '2026-02-16 07:10:03'),
(102, 'MY', 'MYS', 'Malaysia', '+60', '2026-02-16 07:10:03'),
(103, 'MV', 'MDV', 'Maldives', '+960', '2026-02-16 07:10:03'),
(104, 'ML', 'MLI', 'Mali', '+223', '2026-02-16 07:10:03'),
(105, 'MT', 'MLT', 'Malta', '+356', '2026-02-16 07:10:03'),
(106, 'MH', 'MHL', 'Marshall Islands', '+692', '2026-02-16 07:10:03'),
(107, 'MR', 'MRT', 'Mauritania', '+222', '2026-02-16 07:10:03'),
(108, 'MU', 'MUS', 'Mauritius', '+230', '2026-02-16 07:10:03'),
(109, 'MX', 'MEX', 'Mexico', '+52', '2026-02-16 07:10:03'),
(110, 'FM', 'FSM', 'Micronesia', '+691', '2026-02-16 07:10:03'),
(111, 'MD', 'MDA', 'Moldova', '+373', '2026-02-16 07:10:03'),
(112, 'MC', 'MCO', 'Monaco', '+377', '2026-02-16 07:10:03'),
(113, 'MN', 'MNG', 'Mongolia', '+976', '2026-02-16 07:10:03'),
(114, 'ME', 'MNE', 'Montenegro', '+382', '2026-02-16 07:10:03'),
(115, 'MA', 'MAR', 'Morocco', '+212', '2026-02-16 07:10:03'),
(116, 'MZ', 'MOZ', 'Mozambique', '+258', '2026-02-16 07:10:03'),
(117, 'MM', 'MMR', 'Myanmar', '+95', '2026-02-16 07:10:03'),
(118, 'NA', 'NAM', 'Namibia', '+264', '2026-02-16 07:10:03'),
(119, 'NR', 'NRU', 'Nauru', '+674', '2026-02-16 07:10:03'),
(120, 'NP', 'NPL', 'Nepal', '+977', '2026-02-16 07:10:03'),
(121, 'NL', 'NLD', 'Netherlands', '+31', '2026-02-16 07:10:03'),
(122, 'NZ', 'NZL', 'New Zealand', '+64', '2026-02-16 07:10:03'),
(123, 'NI', 'NIC', 'Nicaragua', '+505', '2026-02-16 07:10:03'),
(124, 'NE', 'NER', 'Niger', '+227', '2026-02-16 07:10:03'),
(125, 'NG', 'NGA', 'Nigeria', '+234', '2026-02-16 07:10:03'),
(126, 'NO', 'NOR', 'Norway', '+47', '2026-02-16 07:10:03'),
(127, 'OM', 'OMN', 'Oman', '+968', '2026-02-16 07:10:03'),
(128, 'PK', 'PAK', 'Pakistan', '+92', '2026-02-16 07:10:03'),
(129, 'PW', 'PLW', 'Palau', '+680', '2026-02-16 07:10:03'),
(130, 'PA', 'PAN', 'Panama', '+507', '2026-02-16 07:10:03'),
(131, 'PG', 'PNG', 'Papua New Guinea', '+675', '2026-02-16 07:10:03'),
(132, 'PY', 'PRY', 'Paraguay', '+595', '2026-02-16 07:10:03'),
(133, 'PE', 'PER', 'Peru', '+51', '2026-02-16 07:10:03'),
(134, 'PH', 'PHL', 'Philippines', '+63', '2026-02-16 07:10:03'),
(135, 'PL', 'POL', 'Poland', '+48', '2026-02-16 07:10:03'),
(136, 'PT', 'PRT', 'Portugal', '+351', '2026-02-16 07:10:03'),
(137, 'QA', 'QAT', 'Qatar', '+974', '2026-02-16 07:10:03'),
(138, 'RO', 'ROU', 'Romania', '+40', '2026-02-16 07:10:03'),
(139, 'RU', 'RUS', 'Russia', '+7', '2026-02-16 07:10:03'),
(140, 'RW', 'RWA', 'Rwanda', '+250', '2026-02-16 07:10:03'),
(141, 'KN', 'KNA', 'Saint Kitts and Nevis', '+1-869', '2026-02-16 07:10:03'),
(142, 'LC', 'LCA', 'Saint Lucia', '+1-758', '2026-02-16 07:10:03'),
(143, 'VC', 'VCT', 'Saint Vincent and the Grenadines', '+1-784', '2026-02-16 07:10:03'),
(144, 'WS', 'WSM', 'Samoa', '+685', '2026-02-16 07:10:03'),
(145, 'SM', 'SMR', 'San Marino', '+378', '2026-02-16 07:10:03'),
(146, 'ST', 'STP', 'Sao Tome and Principe', '+239', '2026-02-16 07:10:03'),
(147, 'SA', 'SAU', 'Saudi Arabia', '+966', '2026-02-16 07:10:03'),
(148, 'SN', 'SEN', 'Senegal', '+221', '2026-02-16 07:10:03'),
(149, 'RS', 'SRB', 'Serbia', '+381', '2026-02-16 07:10:03'),
(150, 'SC', 'SYC', 'Seychelles', '+248', '2026-02-16 07:10:03'),
(151, 'SL', 'SLE', 'Sierra Leone', '+232', '2026-02-16 07:10:03'),
(152, 'SG', 'SGP', 'Singapore', '+65', '2026-02-16 07:10:03'),
(153, 'SK', 'SVK', 'Slovakia', '+421', '2026-02-16 07:10:03'),
(154, 'SI', 'SVN', 'Slovenia', '+386', '2026-02-16 07:10:03'),
(155, 'SB', 'SLB', 'Solomon Islands', '+677', '2026-02-16 07:10:03'),
(156, 'SO', 'SOM', 'Somalia', '+252', '2026-02-16 07:10:03'),
(157, 'ZA', 'ZAF', 'South Africa', '+27', '2026-02-16 07:10:03'),
(158, 'SS', 'SSD', 'South Sudan', '+211', '2026-02-16 07:10:03'),
(159, 'ES', 'ESP', 'Spain', '+34', '2026-02-16 07:10:03'),
(160, 'LK', 'LKA', 'Sri Lanka', '+94', '2026-02-16 07:10:03'),
(161, 'SD', 'SDN', 'Sudan', '+249', '2026-02-16 07:10:03'),
(162, 'SR', 'SUR', 'Suriname', '+597', '2026-02-16 07:10:03'),
(163, 'SE', 'SWE', 'Sweden', '+46', '2026-02-16 07:10:03'),
(164, 'CH', 'CHE', 'Switzerland', '+41', '2026-02-16 07:10:03'),
(165, 'SY', 'SYR', 'Syria', '+963', '2026-02-16 07:10:03'),
(166, 'TW', 'TWN', 'Taiwan', '+886', '2026-02-16 07:10:03'),
(167, 'TJ', 'TJK', 'Tajikistan', '+992', '2026-02-16 07:10:03'),
(168, 'TZ', 'TZA', 'Tanzania', '+255', '2026-02-16 07:10:03'),
(169, 'TH', 'THA', 'Thailand', '+66', '2026-02-16 07:10:03'),
(170, 'TL', 'TLS', 'Timor-Leste', '+670', '2026-02-16 07:10:03'),
(171, 'TG', 'TGO', 'Togo', '+228', '2026-02-16 07:10:03'),
(172, 'TO', 'TON', 'Tonga', '+676', '2026-02-16 07:10:03'),
(173, 'TT', 'TTO', 'Trinidad and Tobago', '+1-868', '2026-02-16 07:10:03'),
(174, 'TN', 'TUN', 'Tunisia', '+216', '2026-02-16 07:10:03'),
(175, 'TR', 'TUR', 'Turkey', '+90', '2026-02-16 07:10:03'),
(176, 'TM', 'TKM', 'Turkmenistan', '+993', '2026-02-16 07:10:03'),
(177, 'TV', 'TUV', 'Tuvalu', '+688', '2026-02-16 07:10:03'),
(178, 'UG', 'UGA', 'Uganda', '+256', '2026-02-16 07:10:03'),
(179, 'UA', 'UKR', 'Ukraine', '+380', '2026-02-16 07:10:03'),
(180, 'AE', 'ARE', 'United Arab Emirates', '+971', '2026-02-16 07:10:03'),
(181, 'GB', 'GBR', 'United Kingdom', '+44', '2026-02-16 07:10:03'),
(182, 'US', 'USA', 'United States', '+1', '2026-02-16 07:10:03'),
(183, 'UY', 'URY', 'Uruguay', '+598', '2026-02-16 07:10:03'),
(184, 'UZ', 'UZB', 'Uzbekistan', '+998', '2026-02-16 07:10:03'),
(185, 'VU', 'VUT', 'Vanuatu', '+678', '2026-02-16 07:10:03'),
(186, 'VE', 'VEN', 'Venezuela', '+58', '2026-02-16 07:10:03'),
(187, 'VN', 'VNM', 'Vietnam', '+84', '2026-02-16 07:10:03'),
(188, 'YE', 'YEM', 'Yemen', '+967', '2026-02-16 07:10:03'),
(189, 'ZM', 'ZMB', 'Zambia', '+260', '2026-02-16 07:10:03'),
(190, 'ZW', 'ZWE', 'Zimbabwe', '+263', '2026-02-16 07:10:03');

-- --------------------------------------------------------

--
-- Table structure for table `ref_lgas`
--

CREATE TABLE `ref_lgas` (
  `id` int(10) UNSIGNED NOT NULL,
  `state_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(140) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ref_lgas`
--

INSERT INTO `ref_lgas` (`id`, `state_id`, `name`, `created_at`) VALUES
(1, 1, 'Demsa', '2026-02-16 07:10:03'),
(2, 1, 'Fufure', '2026-02-16 07:10:03'),
(3, 1, 'Ganye', '2026-02-16 07:10:03'),
(4, 1, 'Gayuk', '2026-02-16 07:10:03'),
(5, 1, 'Gombi', '2026-02-16 07:10:03'),
(6, 1, 'Grie', '2026-02-16 07:10:03'),
(7, 1, 'Hong', '2026-02-16 07:10:03'),
(8, 1, 'Jada', '2026-02-16 07:10:03'),
(9, 1, 'Larmurde', '2026-02-16 07:10:03'),
(10, 1, 'Madagali', '2026-02-16 07:10:03'),
(11, 1, 'Maiha', '2026-02-16 07:10:03'),
(12, 1, 'Mayo Belwa', '2026-02-16 07:10:03'),
(13, 1, 'Michika', '2026-02-16 07:10:03'),
(14, 1, 'Mubi North', '2026-02-16 07:10:03'),
(15, 1, 'Mubi South', '2026-02-16 07:10:03'),
(16, 1, 'Numan', '2026-02-16 07:10:03'),
(17, 1, 'Shelleng', '2026-02-16 07:10:03'),
(18, 1, 'Song', '2026-02-16 07:10:03'),
(19, 1, 'Toungo', '2026-02-16 07:10:03'),
(20, 1, 'Yola North', '2026-02-16 07:10:03'),
(21, 1, 'Yola South', '2026-02-16 07:10:03'),
(22, 2, 'Abak', '2026-02-16 07:10:03'),
(23, 2, 'Eastern Obolo', '2026-02-16 07:10:03'),
(24, 2, 'Eket', '2026-02-16 07:10:03'),
(25, 2, 'Esit Eket', '2026-02-16 07:10:03'),
(26, 2, 'Essien Udim', '2026-02-16 07:10:03'),
(27, 2, 'Etim Ekpo', '2026-02-16 07:10:03'),
(28, 2, 'Etinan', '2026-02-16 07:10:03'),
(29, 2, 'Ibeno', '2026-02-16 07:10:03'),
(30, 2, 'Ibesikpo Asutan', '2026-02-16 07:10:03'),
(31, 2, 'Ibiono-Ibom', '2026-02-16 07:10:03'),
(32, 2, 'Ikot Abasi', '2026-02-16 07:10:03'),
(33, 2, 'Ika', '2026-02-16 07:10:03'),
(34, 2, 'Ikono', '2026-02-16 07:10:03'),
(35, 2, 'Ikot Ekpene', '2026-02-16 07:10:03'),
(36, 2, 'Ini', '2026-02-16 07:10:03'),
(37, 2, 'Mkpat-Enin', '2026-02-16 07:10:03'),
(38, 2, 'Itu', '2026-02-16 07:10:03'),
(39, 2, 'Mbo', '2026-02-16 07:10:03'),
(40, 2, 'Nsit-Atai', '2026-02-16 07:10:03'),
(41, 2, 'Nsit-Ibom', '2026-02-16 07:10:03'),
(42, 2, 'Nsit-Ubium', '2026-02-16 07:10:03'),
(43, 2, 'Obot Akara', '2026-02-16 07:10:03'),
(44, 2, 'Okobo', '2026-02-16 07:10:03'),
(45, 2, 'Onna', '2026-02-16 07:10:03'),
(46, 2, 'Oron', '2026-02-16 07:10:03'),
(47, 2, 'Udung-Uko', '2026-02-16 07:10:03'),
(48, 2, 'Ukanafun', '2026-02-16 07:10:03'),
(49, 2, 'Oruk Anam', '2026-02-16 07:10:03'),
(50, 2, 'Uruan', '2026-02-16 07:10:03'),
(51, 2, 'Urue-Offong/Oruko', '2026-02-16 07:10:03'),
(52, 2, 'Uyo', '2026-02-16 07:10:03'),
(53, 3, 'Aguata', '2026-02-16 07:10:03'),
(54, 3, 'Anambra East', '2026-02-16 07:10:03'),
(55, 3, 'Anaocha', '2026-02-16 07:10:03'),
(56, 3, 'Awka North', '2026-02-16 07:10:03'),
(57, 3, 'Anambra West', '2026-02-16 07:10:03'),
(58, 3, 'Awka South', '2026-02-16 07:10:03'),
(59, 3, 'Ayamelum', '2026-02-16 07:10:03'),
(60, 3, 'Dunukofia', '2026-02-16 07:10:03'),
(61, 3, 'Ekwusigo', '2026-02-16 07:10:03'),
(62, 3, 'Idemili North', '2026-02-16 07:10:03'),
(63, 3, 'Idemili South', '2026-02-16 07:10:03'),
(64, 3, 'Ihiala', '2026-02-16 07:10:03'),
(65, 3, 'Njikoka', '2026-02-16 07:10:03'),
(66, 3, 'Nnewi North', '2026-02-16 07:10:03'),
(67, 3, 'Nnewi South', '2026-02-16 07:10:03'),
(68, 3, 'Ogbaru', '2026-02-16 07:10:03'),
(69, 3, 'Onitsha North', '2026-02-16 07:10:03'),
(70, 3, 'Onitsha South', '2026-02-16 07:10:03'),
(71, 3, 'Orumba North', '2026-02-16 07:10:03'),
(72, 3, 'Orumba South', '2026-02-16 07:10:03'),
(73, 3, 'Oyi', '2026-02-16 07:10:03'),
(74, 4, 'Abeokuta North', '2026-02-16 07:10:03'),
(75, 4, 'Abeokuta South', '2026-02-16 07:10:03'),
(76, 4, 'Ado-Odo/Ota', '2026-02-16 07:10:03'),
(77, 4, 'Egbado North', '2026-02-16 07:10:03'),
(78, 4, 'Ewekoro', '2026-02-16 07:10:03'),
(79, 4, 'Egbado South', '2026-02-16 07:10:03'),
(80, 4, 'Ijebu North', '2026-02-16 07:10:03'),
(81, 4, 'Ijebu East', '2026-02-16 07:10:03'),
(82, 4, 'Ifo', '2026-02-16 07:10:03'),
(83, 4, 'Ijebu Ode', '2026-02-16 07:10:03'),
(84, 4, 'Ijebu North East', '2026-02-16 07:10:03'),
(85, 4, 'Imeko Afon', '2026-02-16 07:10:03'),
(86, 4, 'Ikenne', '2026-02-16 07:10:03'),
(87, 4, 'Ipokia', '2026-02-16 07:10:03'),
(88, 4, 'Odeda', '2026-02-16 07:10:03'),
(89, 4, 'Obafemi Owode', '2026-02-16 07:10:03'),
(90, 4, 'Odogbolu', '2026-02-16 07:10:03'),
(91, 4, 'Remo North', '2026-02-16 07:10:03'),
(92, 4, 'Ogun Waterside', '2026-02-16 07:10:03'),
(93, 4, 'Shagamu', '2026-02-16 07:10:03'),
(94, 5, 'Akoko North-East', '2026-02-16 07:10:03'),
(95, 5, 'Akoko North-West', '2026-02-16 07:10:03'),
(96, 5, 'Akoko South-West', '2026-02-16 07:10:03'),
(97, 5, 'Akoko South-East', '2026-02-16 07:10:03'),
(98, 5, 'Akure North', '2026-02-16 07:10:03'),
(99, 5, 'Akure South', '2026-02-16 07:10:03'),
(100, 5, 'Ese Odo', '2026-02-16 07:10:03'),
(101, 5, 'Idanre', '2026-02-16 07:10:03'),
(102, 5, 'Ifedore', '2026-02-16 07:10:03'),
(103, 5, 'Ilaje', '2026-02-16 07:10:03'),
(104, 5, 'Irele', '2026-02-16 07:10:03'),
(105, 5, 'Ile Oluji/Okeigbo', '2026-02-16 07:10:03'),
(106, 5, 'Odigbo', '2026-02-16 07:10:03'),
(107, 5, 'Okitipupa', '2026-02-16 07:10:03'),
(108, 5, 'Ondo West', '2026-02-16 07:10:03'),
(109, 5, 'Ose', '2026-02-16 07:10:03'),
(110, 5, 'Ondo East', '2026-02-16 07:10:03'),
(111, 5, 'Owo', '2026-02-16 07:10:03'),
(112, 6, 'Abua/Odual', '2026-02-16 07:10:03'),
(113, 6, 'Ahoada East', '2026-02-16 07:10:03'),
(114, 6, 'Ahoada West', '2026-02-16 07:10:03'),
(115, 6, 'Andoni', '2026-02-16 07:10:03'),
(116, 6, 'Akuku-Toru', '2026-02-16 07:10:03'),
(117, 6, 'Asari-Toru', '2026-02-16 07:10:03'),
(118, 6, 'Bonny', '2026-02-16 07:10:03'),
(119, 6, 'Degema', '2026-02-16 07:10:03'),
(120, 6, 'Emuoha', '2026-02-16 07:10:03'),
(121, 6, 'Eleme', '2026-02-16 07:10:03'),
(122, 6, 'Ikwerre', '2026-02-16 07:10:03'),
(123, 6, 'Etche', '2026-02-16 07:10:03'),
(124, 6, 'Gokana', '2026-02-16 07:10:03'),
(125, 6, 'Khana', '2026-02-16 07:10:03'),
(126, 6, 'Obio/Akpor', '2026-02-16 07:10:03'),
(127, 6, 'Ogba/Egbema/Ndoni', '2026-02-16 07:10:03'),
(128, 6, 'Ogu/Bolo', '2026-02-16 07:10:03'),
(129, 6, 'Okrika', '2026-02-16 07:10:03'),
(130, 6, 'Omuma', '2026-02-16 07:10:03'),
(131, 6, 'Opobo/Nkoro', '2026-02-16 07:10:03'),
(132, 6, 'Oyigbo', '2026-02-16 07:10:03'),
(133, 6, 'Port Harcourt', '2026-02-16 07:10:03'),
(134, 6, 'Tai', '2026-02-16 07:10:03'),
(135, 7, 'Alkaleri', '2026-02-16 07:10:03'),
(136, 7, 'Bauchi', '2026-02-16 07:10:03'),
(137, 7, 'Bogoro', '2026-02-16 07:10:03'),
(138, 7, 'Damban', '2026-02-16 07:10:03'),
(139, 7, 'Darazo', '2026-02-16 07:10:03'),
(140, 7, 'Dass', '2026-02-16 07:10:03'),
(141, 7, 'Gamawa', '2026-02-16 07:10:03'),
(142, 7, 'Ganjuwa', '2026-02-16 07:10:03'),
(143, 7, 'Giade', '2026-02-16 07:10:03'),
(144, 7, 'Itas/Gadau', '2026-02-16 07:10:03'),
(145, 7, 'Jama\'are', '2026-02-16 07:10:03'),
(146, 7, 'Katagum', '2026-02-16 07:10:03'),
(147, 7, 'Kirfi', '2026-02-16 07:10:03'),
(148, 7, 'Misau', '2026-02-16 07:10:03'),
(149, 7, 'Ningi', '2026-02-16 07:10:03'),
(150, 7, 'Shira', '2026-02-16 07:10:03'),
(151, 7, 'Tafawa Balewa', '2026-02-16 07:10:03'),
(152, 7, 'Toro', '2026-02-16 07:10:03'),
(153, 7, 'Warji', '2026-02-16 07:10:03'),
(154, 7, 'Zaki', '2026-02-16 07:10:03'),
(155, 8, 'Agatu', '2026-02-16 07:10:03'),
(156, 8, 'Apa', '2026-02-16 07:10:03'),
(157, 8, 'Ado', '2026-02-16 07:10:03'),
(158, 8, 'Buruku', '2026-02-16 07:10:03'),
(159, 8, 'Gboko', '2026-02-16 07:10:03'),
(160, 8, 'Guma', '2026-02-16 07:10:03'),
(161, 8, 'Gwer East', '2026-02-16 07:10:03'),
(162, 8, 'Gwer West', '2026-02-16 07:10:03'),
(163, 8, 'Katsina-Ala', '2026-02-16 07:10:03'),
(164, 8, 'Konshisha', '2026-02-16 07:10:03'),
(165, 8, 'Kwande', '2026-02-16 07:10:03'),
(166, 8, 'Logo', '2026-02-16 07:10:03'),
(167, 8, 'Makurdi', '2026-02-16 07:10:03'),
(168, 8, 'Obi', '2026-02-16 07:10:03'),
(169, 8, 'Ogbadibo', '2026-02-16 07:10:03'),
(170, 8, 'Ohimini', '2026-02-16 07:10:03'),
(171, 8, 'Oju', '2026-02-16 07:10:03'),
(172, 8, 'Okpokwu', '2026-02-16 07:10:03'),
(173, 8, 'Oturkpo', '2026-02-16 07:10:03'),
(174, 8, 'Tarka', '2026-02-16 07:10:03'),
(175, 8, 'Ukum', '2026-02-16 07:10:03'),
(176, 8, 'Ushongo', '2026-02-16 07:10:03'),
(177, 8, 'Vandeikya', '2026-02-16 07:10:03'),
(178, 9, 'Abadam', '2026-02-16 07:10:03'),
(179, 9, 'Askira/Uba', '2026-02-16 07:10:03'),
(180, 9, 'Bama', '2026-02-16 07:10:03'),
(181, 9, 'Bayo', '2026-02-16 07:10:03'),
(182, 9, 'Biu', '2026-02-16 07:10:03'),
(183, 9, 'Chibok', '2026-02-16 07:10:03'),
(184, 9, 'Damboa', '2026-02-16 07:10:03'),
(185, 9, 'Dikwa', '2026-02-16 07:10:03'),
(186, 9, 'Guzamala', '2026-02-16 07:10:03'),
(187, 9, 'Gubio', '2026-02-16 07:10:03'),
(188, 9, 'Hawul', '2026-02-16 07:10:03'),
(189, 9, 'Gwoza', '2026-02-16 07:10:03'),
(190, 9, 'Jere', '2026-02-16 07:10:03'),
(191, 9, 'Kaga', '2026-02-16 07:10:03'),
(192, 9, 'Kala/Balge', '2026-02-16 07:10:03'),
(193, 9, 'Konduga', '2026-02-16 07:10:03'),
(194, 9, 'Kukawa', '2026-02-16 07:10:03'),
(195, 9, 'Kwaya Kusar', '2026-02-16 07:10:03'),
(196, 9, 'Mafa', '2026-02-16 07:10:03'),
(197, 9, 'Magumeri', '2026-02-16 07:10:03'),
(198, 9, 'Maiduguri', '2026-02-16 07:10:03'),
(199, 9, 'Mobbar', '2026-02-16 07:10:03'),
(200, 9, 'Marte', '2026-02-16 07:10:03'),
(201, 9, 'Monguno', '2026-02-16 07:10:03'),
(202, 9, 'Ngala', '2026-02-16 07:10:03'),
(203, 9, 'Nganzai', '2026-02-16 07:10:03'),
(204, 9, 'Shani', '2026-02-16 07:10:03'),
(205, 10, 'Brass', '2026-02-16 07:10:03'),
(206, 10, 'Ekeremor', '2026-02-16 07:10:03'),
(207, 10, 'Kolokuma/Opokuma', '2026-02-16 07:10:03'),
(208, 10, 'Nembe', '2026-02-16 07:10:03'),
(209, 10, 'Ogbia', '2026-02-16 07:10:03'),
(210, 10, 'Sagbama', '2026-02-16 07:10:03'),
(211, 10, 'Southern Ijaw', '2026-02-16 07:10:03'),
(212, 10, 'Yenagoa', '2026-02-16 07:10:03'),
(213, 11, 'Abi', '2026-02-16 07:10:03'),
(214, 11, 'Akamkpa', '2026-02-16 07:10:03'),
(215, 11, 'Akpabuyo', '2026-02-16 07:10:03'),
(216, 11, 'Bakassi', '2026-02-16 07:10:03'),
(217, 11, 'Bekwarra', '2026-02-16 07:10:03'),
(218, 11, 'Biase', '2026-02-16 07:10:03'),
(219, 11, 'Boki', '2026-02-16 07:10:03'),
(220, 11, 'Calabar Municipal', '2026-02-16 07:10:03'),
(221, 11, 'Calabar South', '2026-02-16 07:10:03'),
(222, 11, 'Etung', '2026-02-16 07:10:03'),
(223, 11, 'Ikom', '2026-02-16 07:10:03'),
(224, 11, 'Obanliku', '2026-02-16 07:10:03'),
(225, 11, 'Obubra', '2026-02-16 07:10:03'),
(226, 11, 'Obudu', '2026-02-16 07:10:03'),
(227, 11, 'Odukpani', '2026-02-16 07:10:03'),
(228, 11, 'Ogoja', '2026-02-16 07:10:03'),
(229, 11, 'Yakuur', '2026-02-16 07:10:03'),
(230, 11, 'Yala', '2026-02-16 07:10:03'),
(231, 12, 'Aniocha North', '2026-02-16 07:10:03'),
(232, 12, 'Aniocha South', '2026-02-16 07:10:03'),
(233, 12, 'Bomadi', '2026-02-16 07:10:03'),
(234, 12, 'Burutu', '2026-02-16 07:10:03'),
(235, 12, 'Ethiope West', '2026-02-16 07:10:03'),
(236, 12, 'Ethiope East', '2026-02-16 07:10:03'),
(237, 12, 'Ika North East', '2026-02-16 07:10:03'),
(238, 12, 'Ika South', '2026-02-16 07:10:03'),
(239, 12, 'Isoko North', '2026-02-16 07:10:03'),
(240, 12, 'Isoko South', '2026-02-16 07:10:03'),
(241, 12, 'Ndokwa East', '2026-02-16 07:10:03'),
(242, 12, 'Ndokwa West', '2026-02-16 07:10:03'),
(243, 12, 'Okpe', '2026-02-16 07:10:03'),
(244, 12, 'Oshimili North', '2026-02-16 07:10:03'),
(245, 12, 'Oshimili South', '2026-02-16 07:10:03'),
(246, 12, 'Patani', '2026-02-16 07:10:03'),
(247, 12, 'Sapele', '2026-02-16 07:10:03'),
(248, 12, 'Udu', '2026-02-16 07:10:03'),
(249, 12, 'Ughelli North', '2026-02-16 07:10:03'),
(250, 12, 'Ukwuani', '2026-02-16 07:10:03'),
(251, 12, 'Ughelli South', '2026-02-16 07:10:03'),
(252, 12, 'Uvwie', '2026-02-16 07:10:03'),
(253, 12, 'Warri North', '2026-02-16 07:10:03'),
(254, 12, 'Warri South', '2026-02-16 07:10:03'),
(255, 12, 'Warri South West', '2026-02-16 07:10:03'),
(256, 13, 'Abakaliki', '2026-02-16 07:10:03'),
(257, 13, 'Afikpo North', '2026-02-16 07:10:03'),
(258, 13, 'Ebonyi', '2026-02-16 07:10:03'),
(259, 13, 'Afikpo South', '2026-02-16 07:10:03'),
(260, 13, 'Ezza North', '2026-02-16 07:10:03'),
(261, 13, 'Ikwo', '2026-02-16 07:10:03'),
(262, 13, 'Ezza South', '2026-02-16 07:10:03'),
(263, 13, 'Ivo', '2026-02-16 07:10:03'),
(264, 13, 'Ishielu', '2026-02-16 07:10:03'),
(265, 13, 'Izzi', '2026-02-16 07:10:03'),
(266, 13, 'Ohaozara', '2026-02-16 07:10:03'),
(267, 13, 'Ohaukwu', '2026-02-16 07:10:03'),
(268, 13, 'Onicha', '2026-02-16 07:10:03'),
(269, 14, 'Akoko-Edo', '2026-02-16 07:10:03'),
(270, 14, 'Egor', '2026-02-16 07:10:03'),
(271, 14, 'Esan Central', '2026-02-16 07:10:03'),
(272, 14, 'Esan North-East', '2026-02-16 07:10:03'),
(273, 14, 'Esan South-East', '2026-02-16 07:10:03'),
(274, 14, 'Esan West', '2026-02-16 07:10:03'),
(275, 14, 'Etsako Central', '2026-02-16 07:10:03'),
(276, 14, 'Etsako East', '2026-02-16 07:10:03'),
(277, 14, 'Etsako West', '2026-02-16 07:10:03'),
(278, 14, 'Igueben', '2026-02-16 07:10:03'),
(279, 14, 'Ikpoba Okha', '2026-02-16 07:10:03'),
(280, 14, 'Orhionmwon', '2026-02-16 07:10:03'),
(281, 14, 'Oredo', '2026-02-16 07:10:03'),
(282, 14, 'Ovia North-East', '2026-02-16 07:10:03'),
(283, 14, 'Ovia South-West', '2026-02-16 07:10:03'),
(284, 14, 'Owan East', '2026-02-16 07:10:03'),
(285, 14, 'Owan West', '2026-02-16 07:10:03'),
(286, 14, 'Uhunmwonde', '2026-02-16 07:10:03'),
(287, 15, 'Ado Ekiti', '2026-02-16 07:10:03'),
(288, 15, 'Efon', '2026-02-16 07:10:03'),
(289, 15, 'Ekiti East', '2026-02-16 07:10:03'),
(290, 15, 'Ekiti South-West', '2026-02-16 07:10:03'),
(291, 15, 'Ekiti West', '2026-02-16 07:10:03'),
(292, 15, 'Emure', '2026-02-16 07:10:03'),
(293, 15, 'Gbonyin', '2026-02-16 07:10:03'),
(294, 15, 'Ido/Osi', '2026-02-16 07:10:03'),
(295, 15, 'Ijero', '2026-02-16 07:10:03'),
(296, 15, 'Ikere', '2026-02-16 07:10:03'),
(297, 15, 'Ikole', '2026-02-16 07:10:03'),
(298, 15, 'Ilejemeje', '2026-02-16 07:10:03'),
(299, 15, 'Irepodun/Ifelodun', '2026-02-16 07:10:03'),
(300, 15, 'Ise/Orun', '2026-02-16 07:10:03'),
(301, 15, 'Moba', '2026-02-16 07:10:03'),
(302, 15, 'Oye', '2026-02-16 07:10:03'),
(303, 16, 'Aninri', '2026-02-16 07:10:03'),
(304, 16, 'Awgu', '2026-02-16 07:10:03'),
(305, 16, 'Enugu East', '2026-02-16 07:10:03'),
(306, 16, 'Enugu North', '2026-02-16 07:10:03'),
(307, 16, 'Enugu South', '2026-02-16 07:10:03'),
(308, 16, 'Ezeagu', '2026-02-16 07:10:03'),
(309, 16, 'Igbo Etiti', '2026-02-16 07:10:03'),
(310, 16, 'Igbo Eze North', '2026-02-16 07:10:03'),
(311, 16, 'Igbo Eze South', '2026-02-16 07:10:03'),
(312, 16, 'Isi Uzo', '2026-02-16 07:10:03'),
(313, 16, 'Nkanu East', '2026-02-16 07:10:03'),
(314, 16, 'Nkanu West', '2026-02-16 07:10:03'),
(315, 16, 'Nsukka', '2026-02-16 07:10:03'),
(316, 16, 'Oji River', '2026-02-16 07:10:03'),
(317, 16, 'Udenu', '2026-02-16 07:10:03'),
(318, 16, 'Udi', '2026-02-16 07:10:03'),
(319, 16, 'Uzo Uwani', '2026-02-16 07:10:03'),
(320, 17, 'Abaji', '2026-02-16 07:10:03'),
(321, 17, 'Bwari', '2026-02-16 07:10:03'),
(322, 17, 'Gwagwalada', '2026-02-16 07:10:03'),
(323, 17, 'Kuje', '2026-02-16 07:10:03'),
(324, 17, 'Kwali', '2026-02-16 07:10:03'),
(325, 17, 'Municipal Area Council', '2026-02-16 07:10:03'),
(326, 18, 'Akko', '2026-02-16 07:10:03'),
(327, 18, 'Balanga', '2026-02-16 07:10:03'),
(328, 18, 'Billiri', '2026-02-16 07:10:03'),
(329, 18, 'Dukku', '2026-02-16 07:10:03'),
(330, 18, 'Funakaye', '2026-02-16 07:10:03'),
(331, 18, 'Gombe', '2026-02-16 07:10:03'),
(332, 18, 'Kaltungo', '2026-02-16 07:10:03'),
(333, 18, 'Kwami', '2026-02-16 07:10:03'),
(334, 18, 'Nafada', '2026-02-16 07:10:03'),
(335, 18, 'Shongom', '2026-02-16 07:10:03'),
(336, 18, 'Yamaltu/Deba', '2026-02-16 07:10:03'),
(337, 19, 'Aboh Mbaise', '2026-02-16 07:10:03'),
(338, 19, 'Ahiazu Mbaise', '2026-02-16 07:10:03'),
(339, 19, 'Ehime Mbano', '2026-02-16 07:10:03'),
(340, 19, 'Ezinihitte', '2026-02-16 07:10:03'),
(341, 19, 'Ideato North', '2026-02-16 07:10:03'),
(342, 19, 'Ideato South', '2026-02-16 07:10:03'),
(343, 19, 'Ihitte/Uboma', '2026-02-16 07:10:03'),
(344, 19, 'Ikeduru', '2026-02-16 07:10:03'),
(345, 19, 'Isiala Mbano', '2026-02-16 07:10:03'),
(346, 19, 'Isu', '2026-02-16 07:10:03'),
(347, 19, 'Mbaitoli', '2026-02-16 07:10:03'),
(348, 19, 'Ngor Okpala', '2026-02-16 07:10:03'),
(349, 19, 'Njaba', '2026-02-16 07:10:03'),
(350, 19, 'Nkwerre', '2026-02-16 07:10:03'),
(351, 19, 'Nwangele', '2026-02-16 07:10:03'),
(352, 19, 'Obowo', '2026-02-16 07:10:03'),
(353, 19, 'Oguta', '2026-02-16 07:10:03'),
(354, 19, 'Ohaji/Egbema', '2026-02-16 07:10:03'),
(355, 19, 'Okigwe', '2026-02-16 07:10:03'),
(356, 19, 'Orlu', '2026-02-16 07:10:03'),
(357, 19, 'Orsu', '2026-02-16 07:10:03'),
(358, 19, 'Oru East', '2026-02-16 07:10:03'),
(359, 19, 'Oru West', '2026-02-16 07:10:03'),
(360, 19, 'Owerri Municipal', '2026-02-16 07:10:03'),
(361, 19, 'Owerri North', '2026-02-16 07:10:03'),
(362, 19, 'Owerri West', '2026-02-16 07:10:03'),
(363, 19, 'Unuimo', '2026-02-16 07:10:03'),
(364, 20, 'Auyo', '2026-02-16 07:10:03'),
(365, 20, 'Babura', '2026-02-16 07:10:03'),
(366, 20, 'Biriniwa', '2026-02-16 07:10:03'),
(367, 20, 'Birnin Kudu', '2026-02-16 07:10:03'),
(368, 20, 'Buji', '2026-02-16 07:10:03'),
(369, 20, 'Dutse', '2026-02-16 07:10:03'),
(370, 20, 'Gagarawa', '2026-02-16 07:10:03'),
(371, 20, 'Garki', '2026-02-16 07:10:03'),
(372, 20, 'Gumel', '2026-02-16 07:10:03'),
(373, 20, 'Guri', '2026-02-16 07:10:03'),
(374, 20, 'Gwaram', '2026-02-16 07:10:03'),
(375, 20, 'Gwiwa', '2026-02-16 07:10:03'),
(376, 20, 'Hadejia', '2026-02-16 07:10:03'),
(377, 20, 'Jahun', '2026-02-16 07:10:03'),
(378, 20, 'Kafin Hausa', '2026-02-16 07:10:03'),
(379, 20, 'Kaugama', '2026-02-16 07:10:03'),
(380, 20, 'Kazaure', '2026-02-16 07:10:03'),
(381, 20, 'Kiri Kasama', '2026-02-16 07:10:03'),
(382, 20, 'Kiyawa', '2026-02-16 07:10:03'),
(383, 20, 'Maigatari', '2026-02-16 07:10:03'),
(384, 20, 'Malam Madori', '2026-02-16 07:10:03'),
(385, 20, 'Miga', '2026-02-16 07:10:03'),
(386, 20, 'Ringim', '2026-02-16 07:10:03'),
(387, 20, 'Roni', '2026-02-16 07:10:03'),
(388, 20, 'Sule Tankarkar', '2026-02-16 07:10:03'),
(389, 20, 'Taura', '2026-02-16 07:10:03'),
(390, 20, 'Yankwashi', '2026-02-16 07:10:03'),
(391, 21, 'Birnin Gwari', '2026-02-16 07:10:03'),
(392, 21, 'Chikun', '2026-02-16 07:10:03'),
(393, 21, 'Giwa', '2026-02-16 07:10:03'),
(394, 21, 'Igabi', '2026-02-16 07:10:03'),
(395, 21, 'Ikara', '2026-02-16 07:10:03'),
(396, 21, 'Jaba', '2026-02-16 07:10:03'),
(397, 21, 'Jema\'a', '2026-02-16 07:10:03'),
(398, 21, 'Kachia', '2026-02-16 07:10:03'),
(399, 21, 'Kaduna North', '2026-02-16 07:10:03'),
(400, 21, 'Kaduna South', '2026-02-16 07:10:03'),
(401, 21, 'Kagarko', '2026-02-16 07:10:03'),
(402, 21, 'Kajuru', '2026-02-16 07:10:03'),
(403, 21, 'Kaura', '2026-02-16 07:10:03'),
(404, 21, 'Kauru', '2026-02-16 07:10:03'),
(405, 21, 'Kubau', '2026-02-16 07:10:03'),
(406, 21, 'Kudan', '2026-02-16 07:10:03'),
(407, 21, 'Lere', '2026-02-16 07:10:03'),
(408, 21, 'Makarfi', '2026-02-16 07:10:03'),
(409, 21, 'Sabon Gari', '2026-02-16 07:10:03'),
(410, 21, 'Sanga', '2026-02-16 07:10:03'),
(411, 21, 'Soba', '2026-02-16 07:10:03'),
(412, 21, 'Zangon Kataf', '2026-02-16 07:10:03'),
(413, 21, 'Zaria', '2026-02-16 07:10:03'),
(414, 22, 'Ajingi', '2026-02-16 07:10:03'),
(415, 22, 'Albasu', '2026-02-16 07:10:03'),
(416, 22, 'Bagwai', '2026-02-16 07:10:03'),
(417, 22, 'Bebeji', '2026-02-16 07:10:03'),
(418, 22, 'Bichi', '2026-02-16 07:10:03'),
(419, 22, 'Bunkure', '2026-02-16 07:10:03'),
(420, 22, 'Dala', '2026-02-16 07:10:03'),
(421, 22, 'Dambatta', '2026-02-16 07:10:03'),
(422, 22, 'Dawakin Kudu', '2026-02-16 07:10:03'),
(423, 22, 'Dawakin Tofa', '2026-02-16 07:10:03'),
(424, 22, 'Doguwa', '2026-02-16 07:10:03'),
(425, 22, 'Fagge', '2026-02-16 07:10:03'),
(426, 22, 'Gabasawa', '2026-02-16 07:10:03'),
(427, 22, 'Garko', '2026-02-16 07:10:03'),
(428, 22, 'Garun Mallam', '2026-02-16 07:10:03'),
(429, 22, 'Gaya', '2026-02-16 07:10:03'),
(430, 22, 'Gezawa', '2026-02-16 07:10:03'),
(431, 22, 'Gwale', '2026-02-16 07:10:03'),
(432, 22, 'Gwarzo', '2026-02-16 07:10:03'),
(433, 22, 'Kabo', '2026-02-16 07:10:03'),
(434, 22, 'Kano Municipal', '2026-02-16 07:10:03'),
(435, 22, 'Karaye', '2026-02-16 07:10:03'),
(436, 22, 'Kibiya', '2026-02-16 07:10:03'),
(437, 22, 'Kiru', '2026-02-16 07:10:03'),
(438, 22, 'Kumbotso', '2026-02-16 07:10:03'),
(439, 22, 'Kunchi', '2026-02-16 07:10:03'),
(440, 22, 'Kura', '2026-02-16 07:10:03'),
(441, 22, 'Madobi', '2026-02-16 07:10:03'),
(442, 22, 'Makoda', '2026-02-16 07:10:03'),
(443, 22, 'Minjibir', '2026-02-16 07:10:03'),
(444, 22, 'Nasarawa', '2026-02-16 07:10:03'),
(445, 22, 'Rano', '2026-02-16 07:10:03'),
(446, 22, 'Rimin Gado', '2026-02-16 07:10:03'),
(447, 22, 'Rogo', '2026-02-16 07:10:03'),
(448, 22, 'Shanono', '2026-02-16 07:10:03'),
(449, 22, 'Sumaila', '2026-02-16 07:10:03'),
(450, 22, 'Takai', '2026-02-16 07:10:03'),
(451, 22, 'Tarauni', '2026-02-16 07:10:03'),
(452, 22, 'Tofa', '2026-02-16 07:10:03'),
(453, 22, 'Tsanyawa', '2026-02-16 07:10:03'),
(454, 22, 'Tudun Wada', '2026-02-16 07:10:03'),
(455, 22, 'Ungogo', '2026-02-16 07:10:03'),
(456, 22, 'Warawa', '2026-02-16 07:10:03'),
(457, 22, 'Wudil', '2026-02-16 07:10:03'),
(458, 23, 'Bakori', '2026-02-16 07:10:03'),
(459, 23, 'Batagarawa', '2026-02-16 07:10:03'),
(460, 23, 'Batsari', '2026-02-16 07:10:03'),
(461, 23, 'Baure', '2026-02-16 07:10:03'),
(462, 23, 'Bindawa', '2026-02-16 07:10:03'),
(463, 23, 'Charanchi', '2026-02-16 07:10:03'),
(464, 23, 'Dan Musa', '2026-02-16 07:10:03'),
(465, 23, 'Dandume', '2026-02-16 07:10:03'),
(466, 23, 'Danja', '2026-02-16 07:10:03'),
(467, 23, 'Daura', '2026-02-16 07:10:03'),
(468, 23, 'Dutsi', '2026-02-16 07:10:03'),
(469, 23, 'Dutsin Ma', '2026-02-16 07:10:03'),
(470, 23, 'Faskari', '2026-02-16 07:10:03'),
(471, 23, 'Funtua', '2026-02-16 07:10:03'),
(472, 23, 'Ingawa', '2026-02-16 07:10:03'),
(473, 23, 'Jibia', '2026-02-16 07:10:03'),
(474, 23, 'Kafur', '2026-02-16 07:10:03'),
(475, 23, 'Kaita', '2026-02-16 07:10:03'),
(476, 23, 'Kankara', '2026-02-16 07:10:03'),
(477, 23, 'Kankia', '2026-02-16 07:10:03'),
(478, 23, 'Katsina', '2026-02-16 07:10:03'),
(479, 23, 'Kurfi', '2026-02-16 07:10:03'),
(480, 23, 'Kusada', '2026-02-16 07:10:03'),
(481, 23, 'Mai\'Adua', '2026-02-16 07:10:03'),
(482, 23, 'Malumfashi', '2026-02-16 07:10:03'),
(483, 23, 'Mani', '2026-02-16 07:10:03'),
(484, 23, 'Mashi', '2026-02-16 07:10:03'),
(485, 23, 'Matazu', '2026-02-16 07:10:03'),
(486, 23, 'Musawa', '2026-02-16 07:10:03'),
(487, 23, 'Rimi', '2026-02-16 07:10:03'),
(488, 23, 'Sabuwa', '2026-02-16 07:10:03'),
(489, 23, 'Safana', '2026-02-16 07:10:03'),
(490, 23, 'Sandamu', '2026-02-16 07:10:03'),
(491, 23, 'Zango', '2026-02-16 07:10:03'),
(492, 24, 'Aleiro', '2026-02-16 07:10:03'),
(493, 24, 'Arewa Dandi', '2026-02-16 07:10:03'),
(494, 24, 'Argungu', '2026-02-16 07:10:03'),
(495, 24, 'Augie', '2026-02-16 07:10:03'),
(496, 24, 'Bagudo', '2026-02-16 07:10:03'),
(497, 24, 'Birnin Kebbi', '2026-02-16 07:10:03'),
(498, 24, 'Bunza', '2026-02-16 07:10:03'),
(499, 24, 'Dandi', '2026-02-16 07:10:03'),
(500, 24, 'Fakai', '2026-02-16 07:10:03'),
(501, 24, 'Gwandu', '2026-02-16 07:10:03'),
(502, 24, 'Jega', '2026-02-16 07:10:03'),
(503, 24, 'Kalgo', '2026-02-16 07:10:03'),
(504, 24, 'Koko/Besse', '2026-02-16 07:10:03'),
(505, 24, 'Maiyama', '2026-02-16 07:10:03'),
(506, 24, 'Ngaski', '2026-02-16 07:10:03'),
(507, 24, 'Sakaba', '2026-02-16 07:10:03'),
(508, 24, 'Shanga', '2026-02-16 07:10:03'),
(509, 24, 'Suru', '2026-02-16 07:10:03'),
(510, 24, 'Wasagu/Danko', '2026-02-16 07:10:03'),
(511, 24, 'Yauri', '2026-02-16 07:10:03'),
(512, 24, 'Zuru', '2026-02-16 07:10:03'),
(513, 25, 'Adavi', '2026-02-16 07:10:03'),
(514, 25, 'Ajaokuta', '2026-02-16 07:10:03'),
(515, 25, 'Ankpa', '2026-02-16 07:10:03'),
(516, 25, 'Bassa', '2026-02-16 07:10:03'),
(517, 25, 'Dekina', '2026-02-16 07:10:03'),
(518, 25, 'Ibaji', '2026-02-16 07:10:03'),
(519, 25, 'Idah', '2026-02-16 07:10:03'),
(520, 25, 'Igalamela Odolu', '2026-02-16 07:10:03'),
(521, 25, 'Ijumu', '2026-02-16 07:10:03'),
(522, 25, 'Kabba/Bunu', '2026-02-16 07:10:03'),
(523, 25, 'Kogi', '2026-02-16 07:10:03'),
(524, 25, 'Lokoja', '2026-02-16 07:10:03'),
(525, 25, 'Mopa Muro', '2026-02-16 07:10:03'),
(526, 25, 'Ofu', '2026-02-16 07:10:03'),
(527, 25, 'Ogori/Magongo', '2026-02-16 07:10:03'),
(528, 25, 'Okehi', '2026-02-16 07:10:03'),
(529, 25, 'Okene', '2026-02-16 07:10:03'),
(530, 25, 'Olamaboro', '2026-02-16 07:10:03'),
(531, 25, 'Omala', '2026-02-16 07:10:03'),
(532, 25, 'Yagba East', '2026-02-16 07:10:03'),
(533, 25, 'Yagba West', '2026-02-16 07:10:03'),
(534, 26, 'Asa', '2026-02-16 07:10:03'),
(535, 26, 'Baruten', '2026-02-16 07:10:03'),
(536, 26, 'Edu', '2026-02-16 07:10:03'),
(537, 26, 'Ekiti', '2026-02-16 07:10:03'),
(538, 26, 'Ifelodun', '2026-02-16 07:10:03'),
(539, 26, 'Ilorin East', '2026-02-16 07:10:03'),
(540, 26, 'Ilorin South', '2026-02-16 07:10:03'),
(541, 26, 'Ilorin West', '2026-02-16 07:10:03'),
(542, 26, 'Irepodun', '2026-02-16 07:10:03'),
(543, 26, 'Isin', '2026-02-16 07:10:03'),
(544, 26, 'Kaiama', '2026-02-16 07:10:03'),
(545, 26, 'Moro', '2026-02-16 07:10:03'),
(546, 26, 'Offa', '2026-02-16 07:10:03'),
(547, 26, 'Oke Ero', '2026-02-16 07:10:03'),
(548, 26, 'Oyun', '2026-02-16 07:10:03'),
(549, 26, 'Pategi', '2026-02-16 07:10:03'),
(550, 27, 'Agege', '2026-02-16 07:10:03'),
(551, 27, 'Ajeromi-Ifelodun', '2026-02-16 07:10:03'),
(552, 27, 'Alimosho', '2026-02-16 07:10:03'),
(553, 27, 'Amuwo-Odofin', '2026-02-16 07:10:03'),
(554, 27, 'Apapa', '2026-02-16 07:10:03'),
(555, 27, 'Badagry', '2026-02-16 07:10:03'),
(556, 27, 'Epe', '2026-02-16 07:10:03'),
(557, 27, 'Eti Osa', '2026-02-16 07:10:03'),
(558, 27, 'Ibeju-Lekki', '2026-02-16 07:10:03'),
(559, 27, 'Ifako-Ijaiye', '2026-02-16 07:10:03'),
(560, 27, 'Ikeja', '2026-02-16 07:10:03'),
(561, 27, 'Ikorodu', '2026-02-16 07:10:03'),
(562, 27, 'Kosofe', '2026-02-16 07:10:03'),
(563, 27, 'Lagos Island', '2026-02-16 07:10:03'),
(564, 27, 'Lagos Mainland', '2026-02-16 07:10:03'),
(565, 27, 'Mushin', '2026-02-16 07:10:03'),
(566, 27, 'Ojo', '2026-02-16 07:10:03'),
(567, 27, 'Oshodi-Isolo', '2026-02-16 07:10:03'),
(568, 27, 'Shomolu', '2026-02-16 07:10:03'),
(569, 27, 'Surulere', '2026-02-16 07:10:03'),
(570, 28, 'Akwanga', '2026-02-16 07:10:03'),
(571, 28, 'Awe', '2026-02-16 07:10:03'),
(572, 28, 'Doma', '2026-02-16 07:10:03'),
(573, 28, 'Karu', '2026-02-16 07:10:03'),
(574, 28, 'Keana', '2026-02-16 07:10:03'),
(575, 28, 'Keffi', '2026-02-16 07:10:03'),
(576, 28, 'Kokona', '2026-02-16 07:10:03'),
(577, 28, 'Lafia', '2026-02-16 07:10:03'),
(578, 28, 'Nasarawa', '2026-02-16 07:10:03'),
(579, 28, 'Nasarawa Egon', '2026-02-16 07:10:03'),
(580, 28, 'Obi', '2026-02-16 07:10:03'),
(581, 28, 'Toto', '2026-02-16 07:10:03'),
(582, 28, 'Wamba', '2026-02-16 07:10:03'),
(583, 29, 'Agaie', '2026-02-16 07:10:03'),
(584, 29, 'Agwara', '2026-02-16 07:10:03'),
(585, 29, 'Bida', '2026-02-16 07:10:03'),
(586, 29, 'Borgu', '2026-02-16 07:10:03'),
(587, 29, 'Bosso', '2026-02-16 07:10:03'),
(588, 29, 'Chanchaga', '2026-02-16 07:10:03'),
(589, 29, 'Edati', '2026-02-16 07:10:03'),
(590, 29, 'Gbako', '2026-02-16 07:10:03'),
(591, 29, 'Gurara', '2026-02-16 07:10:03'),
(592, 29, 'Katcha', '2026-02-16 07:10:03'),
(593, 29, 'Kontagora', '2026-02-16 07:10:03'),
(594, 29, 'Lapai', '2026-02-16 07:10:03'),
(595, 29, 'Lavun', '2026-02-16 07:10:03'),
(596, 29, 'Magama', '2026-02-16 07:10:03'),
(597, 29, 'Mariga', '2026-02-16 07:10:03'),
(598, 29, 'Mashegu', '2026-02-16 07:10:03'),
(599, 29, 'Mokwa', '2026-02-16 07:10:03'),
(600, 29, 'Munya', '2026-02-16 07:10:03'),
(601, 29, 'Paikoro', '2026-02-16 07:10:03'),
(602, 29, 'Rafi', '2026-02-16 07:10:03'),
(603, 29, 'Rijau', '2026-02-16 07:10:03'),
(604, 29, 'Shiroro', '2026-02-16 07:10:03'),
(605, 29, 'Suleja', '2026-02-16 07:10:03'),
(606, 29, 'Tafa', '2026-02-16 07:10:03'),
(607, 29, 'Wushishi', '2026-02-16 07:10:03'),
(608, 30, 'Aiyedaade', '2026-02-16 07:10:03'),
(609, 30, 'Aiyedire', '2026-02-16 07:10:03'),
(610, 30, 'Atakumosa East', '2026-02-16 07:10:03'),
(611, 30, 'Atakumosa West', '2026-02-16 07:10:03'),
(612, 30, 'Boluwaduro', '2026-02-16 07:10:03'),
(613, 30, 'Boripe', '2026-02-16 07:10:03'),
(614, 30, 'Ede North', '2026-02-16 07:10:03'),
(615, 30, 'Ede South', '2026-02-16 07:10:03'),
(616, 30, 'Egbedore', '2026-02-16 07:10:03'),
(617, 30, 'Ejigbo', '2026-02-16 07:10:03'),
(618, 30, 'Ife Central', '2026-02-16 07:10:03'),
(619, 30, 'Ife East', '2026-02-16 07:10:03'),
(620, 30, 'Ife North', '2026-02-16 07:10:03'),
(621, 30, 'Ife South', '2026-02-16 07:10:03'),
(622, 30, 'Ifedayo', '2026-02-16 07:10:03'),
(623, 30, 'Ifelodun', '2026-02-16 07:10:03'),
(624, 30, 'Ila', '2026-02-16 07:10:03'),
(625, 30, 'Ilesa East', '2026-02-16 07:10:03'),
(626, 30, 'Ilesa West', '2026-02-16 07:10:03'),
(627, 30, 'Irepodun', '2026-02-16 07:10:03'),
(628, 30, 'Irewole', '2026-02-16 07:10:03'),
(629, 30, 'Isokan', '2026-02-16 07:10:03'),
(630, 30, 'Iwo', '2026-02-16 07:10:03'),
(631, 30, 'Obokun', '2026-02-16 07:10:03'),
(632, 30, 'Odo Otin', '2026-02-16 07:10:03'),
(633, 30, 'Ola Oluwa', '2026-02-16 07:10:03'),
(634, 30, 'Olorunda', '2026-02-16 07:10:03'),
(635, 30, 'Oriade', '2026-02-16 07:10:03'),
(636, 30, 'Orolu', '2026-02-16 07:10:03'),
(637, 30, 'Osogbo', '2026-02-16 07:10:03'),
(638, 31, 'Afijio', '2026-02-16 07:10:03'),
(639, 31, 'Akinyele', '2026-02-16 07:10:03'),
(640, 31, 'Atiba', '2026-02-16 07:10:03'),
(641, 31, 'Atisbo', '2026-02-16 07:10:03'),
(642, 31, 'Egbeda', '2026-02-16 07:10:03'),
(643, 31, 'Ibadan North', '2026-02-16 07:10:03'),
(644, 31, 'Ibadan North-East', '2026-02-16 07:10:03'),
(645, 31, 'Ibadan North-West', '2026-02-16 07:10:03'),
(646, 31, 'Ibadan South-East', '2026-02-16 07:10:03'),
(647, 31, 'Ibadan South-West', '2026-02-16 07:10:03'),
(648, 31, 'Ibarapa Central', '2026-02-16 07:10:03'),
(649, 31, 'Ibarapa East', '2026-02-16 07:10:03'),
(650, 31, 'Ibarapa North', '2026-02-16 07:10:03'),
(651, 31, 'Ido', '2026-02-16 07:10:03'),
(652, 31, 'Irepo', '2026-02-16 07:10:03'),
(653, 31, 'Iseyin', '2026-02-16 07:10:03'),
(654, 31, 'Itesiwaju', '2026-02-16 07:10:03'),
(655, 31, 'Iwajowa', '2026-02-16 07:10:03'),
(656, 31, 'Kajola', '2026-02-16 07:10:03'),
(657, 31, 'Lagelu', '2026-02-16 07:10:03'),
(658, 31, 'Ogbomosho North', '2026-02-16 07:10:03'),
(659, 31, 'Ogbomosho South', '2026-02-16 07:10:03'),
(660, 31, 'Ogo Oluwa', '2026-02-16 07:10:03'),
(661, 31, 'Olorunsogo', '2026-02-16 07:10:03'),
(662, 31, 'Oluyole', '2026-02-16 07:10:03'),
(663, 31, 'Ona Ara', '2026-02-16 07:10:03'),
(664, 31, 'Orelope', '2026-02-16 07:10:03'),
(665, 31, 'Ori Ire', '2026-02-16 07:10:03'),
(666, 31, 'Oyo East', '2026-02-16 07:10:03'),
(667, 31, 'Oyo West', '2026-02-16 07:10:03'),
(668, 31, 'Saki East', '2026-02-16 07:10:03'),
(669, 31, 'Saki West', '2026-02-16 07:10:03'),
(670, 31, 'Surulere', '2026-02-16 07:10:03'),
(671, 32, 'Barkin Ladi', '2026-02-16 07:10:03'),
(672, 32, 'Bassa', '2026-02-16 07:10:03'),
(673, 32, 'Bokkos', '2026-02-16 07:10:03'),
(674, 32, 'Jos East', '2026-02-16 07:10:03'),
(675, 32, 'Jos North', '2026-02-16 07:10:03'),
(676, 32, 'Jos South', '2026-02-16 07:10:03'),
(677, 32, 'Kanam', '2026-02-16 07:10:03'),
(678, 32, 'Kanke', '2026-02-16 07:10:03'),
(679, 32, 'Langtang North', '2026-02-16 07:10:03'),
(680, 32, 'Langtang South', '2026-02-16 07:10:03'),
(681, 32, 'Mangu', '2026-02-16 07:10:03'),
(682, 32, 'Mikang', '2026-02-16 07:10:03'),
(683, 32, 'Pankshin', '2026-02-16 07:10:03'),
(684, 32, 'Qua\'an Pan', '2026-02-16 07:10:03'),
(685, 32, 'Riyom', '2026-02-16 07:10:03'),
(686, 32, 'Shendam', '2026-02-16 07:10:03'),
(687, 32, 'Wase', '2026-02-16 07:10:03'),
(688, 33, 'Binji', '2026-02-16 07:10:03'),
(689, 33, 'Bodinga', '2026-02-16 07:10:03'),
(690, 33, 'Dange Shuni', '2026-02-16 07:10:03'),
(691, 33, 'Gada', '2026-02-16 07:10:03'),
(692, 33, 'Goronyo', '2026-02-16 07:10:03'),
(693, 33, 'Gudu', '2026-02-16 07:10:03'),
(694, 33, 'Gwadabawa', '2026-02-16 07:10:03'),
(695, 33, 'Illela', '2026-02-16 07:10:03'),
(696, 33, 'Isa', '2026-02-16 07:10:03'),
(697, 33, 'Kebbe', '2026-02-16 07:10:03'),
(698, 33, 'Kware', '2026-02-16 07:10:03'),
(699, 33, 'Rabah', '2026-02-16 07:10:03'),
(700, 33, 'Sabon Birni', '2026-02-16 07:10:03'),
(701, 33, 'Shagari', '2026-02-16 07:10:03'),
(702, 33, 'Silame', '2026-02-16 07:10:03'),
(703, 33, 'Sokoto North', '2026-02-16 07:10:03'),
(704, 33, 'Sokoto South', '2026-02-16 07:10:03'),
(705, 33, 'Tambuwal', '2026-02-16 07:10:03'),
(706, 33, 'Tangaza', '2026-02-16 07:10:03'),
(707, 33, 'Tureta', '2026-02-16 07:10:03'),
(708, 33, 'Wamako', '2026-02-16 07:10:03'),
(709, 33, 'Wurno', '2026-02-16 07:10:03'),
(710, 33, 'Yabo', '2026-02-16 07:10:03'),
(711, 34, 'Ardo Kola', '2026-02-16 07:10:03'),
(712, 34, 'Bali', '2026-02-16 07:10:03'),
(713, 34, 'Donga', '2026-02-16 07:10:03'),
(714, 34, 'Gashaka', '2026-02-16 07:10:03'),
(715, 34, 'Gassol', '2026-02-16 07:10:03'),
(716, 34, 'Ibi', '2026-02-16 07:10:03'),
(717, 34, 'Jalingo', '2026-02-16 07:10:03'),
(718, 34, 'Karim Lamido', '2026-02-16 07:10:03'),
(719, 34, 'Kumi', '2026-02-16 07:10:03'),
(720, 34, 'Lau', '2026-02-16 07:10:03'),
(721, 34, 'Sardauna', '2026-02-16 07:10:03'),
(722, 34, 'Takum', '2026-02-16 07:10:03'),
(723, 34, 'Ussa', '2026-02-16 07:10:03'),
(724, 34, 'Wukari', '2026-02-16 07:10:03'),
(725, 34, 'Yorro', '2026-02-16 07:10:03'),
(726, 34, 'Zing', '2026-02-16 07:10:03'),
(727, 35, 'Bade', '2026-02-16 07:10:03'),
(728, 35, 'Bursari', '2026-02-16 07:10:03'),
(729, 35, 'Damaturu', '2026-02-16 07:10:03'),
(730, 35, 'Fika', '2026-02-16 07:10:03'),
(731, 35, 'Fune', '2026-02-16 07:10:03'),
(732, 35, 'Geidam', '2026-02-16 07:10:03'),
(733, 35, 'Gujba', '2026-02-16 07:10:03'),
(734, 35, 'Gulani', '2026-02-16 07:10:03'),
(735, 35, 'Jakusko', '2026-02-16 07:10:03'),
(736, 35, 'Karasuwa', '2026-02-16 07:10:03'),
(737, 35, 'Machina', '2026-02-16 07:10:03'),
(738, 35, 'Nangere', '2026-02-16 07:10:03'),
(739, 35, 'Nguru', '2026-02-16 07:10:03'),
(740, 35, 'Potiskum', '2026-02-16 07:10:03'),
(741, 35, 'Tarmuwa', '2026-02-16 07:10:03'),
(742, 35, 'Yunusari', '2026-02-16 07:10:03'),
(743, 35, 'Yusufari', '2026-02-16 07:10:03'),
(744, 36, 'Anka', '2026-02-16 07:10:03'),
(745, 36, 'Bakura', '2026-02-16 07:10:03'),
(746, 36, 'Birnin Magaji/Kiyaw', '2026-02-16 07:10:03'),
(747, 36, 'Bukkuyum', '2026-02-16 07:10:03'),
(748, 36, 'Bungudu', '2026-02-16 07:10:03'),
(749, 36, 'Gummi', '2026-02-16 07:10:03'),
(750, 36, 'Gusau', '2026-02-16 07:10:03'),
(751, 36, 'Kaura Namoda', '2026-02-16 07:10:03'),
(752, 36, 'Maradun', '2026-02-16 07:10:03'),
(753, 36, 'Maru', '2026-02-16 07:10:03'),
(754, 36, 'Shinkafi', '2026-02-16 07:10:03'),
(755, 36, 'Talata Mafara', '2026-02-16 07:10:03'),
(756, 36, 'Tsafe', '2026-02-16 07:10:03'),
(757, 36, 'Zurmi', '2026-02-16 07:10:03'),
(758, 37, 'Aba North', '2026-02-16 07:10:03'),
(759, 37, 'Aba South', '2026-02-16 07:10:03'),
(760, 37, 'Arochukwu', '2026-02-16 07:10:03'),
(761, 37, 'Bende', '2026-02-16 07:10:03'),
(762, 37, 'Ikwuano', '2026-02-16 07:10:03'),
(763, 37, 'Isiala Ngwa North', '2026-02-16 07:10:03'),
(764, 37, 'Isiala Ngwa South', '2026-02-16 07:10:03'),
(765, 37, 'Isuikwuato', '2026-02-16 07:10:03'),
(766, 37, 'Obi Ngwa', '2026-02-16 07:10:03'),
(767, 37, 'Ohafia', '2026-02-16 07:10:03'),
(768, 37, 'Osisioma', '2026-02-16 07:10:03'),
(769, 37, 'Ugwunagbo', '2026-02-16 07:10:03'),
(770, 37, 'Ukwa East', '2026-02-16 07:10:03'),
(771, 37, 'Ukwa West', '2026-02-16 07:10:03'),
(772, 37, 'Umuahia North', '2026-02-16 07:10:03'),
(773, 37, 'Umuahia South', '2026-02-16 07:10:03'),
(774, 37, 'Umu Nneochi', '2026-02-16 07:10:03');

-- --------------------------------------------------------

--
-- Table structure for table `ref_states`
--

CREATE TABLE `ref_states` (
  `id` int(10) UNSIGNED NOT NULL,
  `country_iso2` char(2) NOT NULL,
  `name` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ref_states`
--

INSERT INTO `ref_states` (`id`, `country_iso2`, `name`, `created_at`) VALUES
(1, 'NG', 'Adamawa', '2026-02-16 07:10:03'),
(2, 'NG', 'Akwa Ibom', '2026-02-16 07:10:03'),
(3, 'NG', 'Anambra', '2026-02-16 07:10:03'),
(4, 'NG', 'Ogun', '2026-02-16 07:10:03'),
(5, 'NG', 'Ondo', '2026-02-16 07:10:03'),
(6, 'NG', 'Rivers', '2026-02-16 07:10:03'),
(7, 'NG', 'Bauchi', '2026-02-16 07:10:03'),
(8, 'NG', 'Benue', '2026-02-16 07:10:03'),
(9, 'NG', 'Borno', '2026-02-16 07:10:03'),
(10, 'NG', 'Bayelsa', '2026-02-16 07:10:03'),
(11, 'NG', 'Cross River', '2026-02-16 07:10:03'),
(12, 'NG', 'Delta', '2026-02-16 07:10:03'),
(13, 'NG', 'Ebonyi', '2026-02-16 07:10:03'),
(14, 'NG', 'Edo', '2026-02-16 07:10:03'),
(15, 'NG', 'Ekiti', '2026-02-16 07:10:03'),
(16, 'NG', 'Enugu', '2026-02-16 07:10:03'),
(17, 'NG', 'FCT - Abuja', '2026-02-16 07:10:03'),
(18, 'NG', 'Gombe', '2026-02-16 07:10:03'),
(19, 'NG', 'Imo', '2026-02-16 07:10:03'),
(20, 'NG', 'Jigawa', '2026-02-16 07:10:03'),
(21, 'NG', 'Kaduna', '2026-02-16 07:10:03'),
(22, 'NG', 'Kano', '2026-02-16 07:10:03'),
(23, 'NG', 'Katsina', '2026-02-16 07:10:03'),
(24, 'NG', 'Kebbi', '2026-02-16 07:10:03'),
(25, 'NG', 'Kogi', '2026-02-16 07:10:03'),
(26, 'NG', 'Kwara', '2026-02-16 07:10:03'),
(27, 'NG', 'Lagos', '2026-02-16 07:10:03'),
(28, 'NG', 'Nasarawa', '2026-02-16 07:10:03'),
(29, 'NG', 'Niger', '2026-02-16 07:10:03'),
(30, 'NG', 'Osun', '2026-02-16 07:10:03'),
(31, 'NG', 'Oyo', '2026-02-16 07:10:03'),
(32, 'NG', 'Plateau', '2026-02-16 07:10:03'),
(33, 'NG', 'Sokoto', '2026-02-16 07:10:03'),
(34, 'NG', 'Taraba', '2026-02-16 07:10:03'),
(35, 'NG', 'Yobe', '2026-02-16 07:10:03'),
(36, 'NG', 'Zamfara', '2026-02-16 07:10:03'),
(37, 'NG', 'Abia', '2026-02-16 07:10:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lms_activity_logs`
--
ALTER TABLE `lms_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `lms_admins`
--
ALTER TABLE `lms_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admin_email` (`email`);

--
-- Indexes for table `lms_ai_chats`
--
ALTER TABLE `lms_ai_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_course` (`student_id`,`course_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `lms_assessment_questions`
--
ALTER TABLE `lms_assessment_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_assessment` (`assessment_id`);

--
-- Indexes for table `lms_assessment_submissions`
--
ALTER TABLE `lms_assessment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_assessment` (`student_id`,`assessment_id`);

--
-- Indexes for table `lms_assignments`
--
ALTER TABLE `lms_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_assign_course` (`course_id`),
  ADD KEY `idx_assign_lesson` (`lesson_id`);

--
-- Indexes for table `lms_assignment_submissions`
--
ALTER TABLE `lms_assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_assignment_student` (`assignment_id`,`student_id`),
  ADD KEY `idx_student` (`student_id`);

--
-- Indexes for table `lms_certificates`
--
ALTER TABLE `lms_certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_code` (`certificate_code`),
  ADD UNIQUE KEY `uq_cert_student_course` (`student_id`,`course_id`),
  ADD KEY `fk_cert_course` (`course_id`);

--
-- Indexes for table `lms_courses`
--
ALTER TABLE `lms_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_level` (`level`),
  ADD KEY `idx_title` (`title`);

--
-- Indexes for table `lms_enrollments`
--
ALTER TABLE `lms_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_student_course` (`student_id`,`course_id`),
  ADD UNIQUE KEY `uq_enrollment_student_course` (`student_id`,`course_id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_course` (`course_id`);

--
-- Indexes for table `lms_exams`
--
ALTER TABLE `lms_exams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_course_exam_title` (`course_id`,`title`),
  ADD KEY `idx_course` (`course_id`);

--
-- Indexes for table `lms_exam_questions`
--
ALTER TABLE `lms_exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_exam` (`exam_id`);

--
-- Indexes for table `lms_exam_results`
--
ALTER TABLE `lms_exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_exam_student` (`exam_id`,`student_id`);

--
-- Indexes for table `lms_instructors`
--
ALTER TABLE `lms_instructors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_instructor_email` (`email`);

--
-- Indexes for table `lms_instructor_courses`
--
ALTER TABLE `lms_instructor_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_instructor_course` (`instructor_id`,`course_id`),
  ADD KEY `idx_course` (`course_id`);

--
-- Indexes for table `lms_lessons`
--
ALTER TABLE `lms_lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lessons_course` (`course_id`),
  ADD KEY `idx_course_sort` (`course_id`,`sort_order`);

--
-- Indexes for table `lms_lesson_assessments`
--
ALTER TABLE `lms_lesson_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lesson` (`lesson_id`),
  ADD KEY `idx_course` (`course_id`);

--
-- Indexes for table `lms_lesson_completions`
--
ALTER TABLE `lms_lesson_completions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_student_lesson` (`student_id`,`lesson_id`),
  ADD KEY `idx_student_course` (`student_id`,`course_id`),
  ADD KEY `fk_lc_lesson` (`lesson_id`),
  ADD KEY `fk_lc_course` (`course_id`);

--
-- Indexes for table `lms_live_sessions`
--
ALTER TABLE `lms_live_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_scheduled` (`scheduled_at`);

--
-- Indexes for table `lms_modules`
--
ALTER TABLE `lms_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mod_course` (`course_id`);

--
-- Indexes for table `lms_payments`
--
ALTER TABLE `lms_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_enroll` (`enrollment_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `lms_progress`
--
ALTER TABLE `lms_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_progress` (`student_id`,`course_id`),
  ADD KEY `fk_progress_course` (`course_id`);

--
-- Indexes for table `lms_session_attendance`
--
ALTER TABLE `lms_session_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_session_student` (`session_id`,`student_id`),
  ADD KEY `idx_student` (`student_id`);

--
-- Indexes for table `lms_session_chat_messages`
--
ALTER TABLE `lms_session_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_created` (`session_id`,`created_at`);

--
-- Indexes for table `lms_session_participants`
--
ALTER TABLE `lms_session_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_session_participant_key` (`session_id`,`participant_key`),
  ADD KEY `idx_session_last_seen` (`session_id`,`last_seen_at`);

--
-- Indexes for table `lms_session_signals`
--
ALTER TABLE `lms_session_signals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_to_key` (`session_id`,`to_key`,`id`);

--
-- Indexes for table `lms_settings`
--
ALTER TABLE `lms_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `lms_students`
--
ALTER TABLE `lms_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `lms_videos`
--
ALTER TABLE `lms_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_lesson` (`lesson_id`);

--
-- Indexes for table `ref_countries`
--
ALTER TABLE `ref_countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iso2` (`iso2`);

--
-- Indexes for table `ref_lgas`
--
ALTER TABLE `ref_lgas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_state_lga` (`state_id`,`name`);

--
-- Indexes for table `ref_states`
--
ALTER TABLE `ref_states`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_country_state` (`country_iso2`,`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lms_activity_logs`
--
ALTER TABLE `lms_activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `lms_admins`
--
ALTER TABLE `lms_admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lms_ai_chats`
--
ALTER TABLE `lms_ai_chats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lms_assessment_questions`
--
ALTER TABLE `lms_assessment_questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT for table `lms_assessment_submissions`
--
ALTER TABLE `lms_assessment_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lms_assignments`
--
ALTER TABLE `lms_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_assignment_submissions`
--
ALTER TABLE `lms_assignment_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_certificates`
--
ALTER TABLE `lms_certificates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `lms_courses`
--
ALTER TABLE `lms_courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `lms_enrollments`
--
ALTER TABLE `lms_enrollments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `lms_exams`
--
ALTER TABLE `lms_exams`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `lms_exam_questions`
--
ALTER TABLE `lms_exam_questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `lms_exam_results`
--
ALTER TABLE `lms_exam_results`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lms_instructors`
--
ALTER TABLE `lms_instructors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lms_instructor_courses`
--
ALTER TABLE `lms_instructor_courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_lessons`
--
ALTER TABLE `lms_lessons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `lms_lesson_assessments`
--
ALTER TABLE `lms_lesson_assessments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `lms_lesson_completions`
--
ALTER TABLE `lms_lesson_completions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `lms_live_sessions`
--
ALTER TABLE `lms_live_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_modules`
--
ALTER TABLE `lms_modules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lms_payments`
--
ALTER TABLE `lms_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `lms_progress`
--
ALTER TABLE `lms_progress`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `lms_session_attendance`
--
ALTER TABLE `lms_session_attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_session_chat_messages`
--
ALTER TABLE `lms_session_chat_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_session_participants`
--
ALTER TABLE `lms_session_participants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_session_signals`
--
ALTER TABLE `lms_session_signals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_students`
--
ALTER TABLE `lms_students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lms_videos`
--
ALTER TABLE `lms_videos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `ref_countries`
--
ALTER TABLE `ref_countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `ref_lgas`
--
ALTER TABLE `ref_lgas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=775;

--
-- AUTO_INCREMENT for table `ref_states`
--
ALTER TABLE `ref_states`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lms_activity_logs`
--
ALTER TABLE `lms_activity_logs`
  ADD CONSTRAINT `fk_logs_student` FOREIGN KEY (`student_id`) REFERENCES `lms_students` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lms_assignments`
--
ALTER TABLE `lms_assignments`
  ADD CONSTRAINT `fk_assign_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assign_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `lms_lessons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lms_assignment_submissions`
--
ALTER TABLE `lms_assignment_submissions`
  ADD CONSTRAINT `fk_sub_assign` FOREIGN KEY (`assignment_id`) REFERENCES `lms_assignments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sub_student` FOREIGN KEY (`student_id`) REFERENCES `lms_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lms_certificates`
--
ALTER TABLE `lms_certificates`
  ADD CONSTRAINT `fk_cert_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cert_student` FOREIGN KEY (`student_id`) REFERENCES `lms_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lms_enrollments`
--
ALTER TABLE `lms_enrollments`
  ADD CONSTRAINT `fk_enroll_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enroll_student` FOREIGN KEY (`student_id`) REFERENCES `lms_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lms_exams`
--
ALTER TABLE `lms_exams`
  ADD CONSTRAINT `fk_exams_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lms_exam_questions`
--
ALTER TABLE `lms_exam_questions`
  ADD CONSTRAINT `fk_questions_exam` FOREIGN KEY (`exam_id`) REFERENCES `lms_exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lms_exam_results`
--
ALTER TABLE `lms_exam_results`
  ADD CONSTRAINT `fk_results_exam` FOREIGN KEY (`exam_id`) REFERENCES `lms_exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_results_student` FOREIGN KEY (`student_id`) REFERENCES `lms_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lms_instructor_courses`
--
ALTER TABLE `lms_instructor_courses`
  ADD CONSTRAINT `fk_ic_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ic_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `lms_instructors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lms_lessons`
--
ALTER TABLE `lms_lessons`
  ADD CONSTRAINT `fk_lessons_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lms_lesson_completions`
--
ALTER TABLE `lms_lesson_completions`
  ADD CONSTRAINT `fk_lc_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lc_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `lms_lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lc_student` FOREIGN KEY (`student_id`) REFERENCES `lms_students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lms_modules`
--
ALTER TABLE `lms_modules`
  ADD CONSTRAINT `fk_mod_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lms_payments`
--
ALTER TABLE `lms_payments`
  ADD CONSTRAINT `fk_pay_enroll` FOREIGN KEY (`enrollment_id`) REFERENCES `lms_enrollments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pay_student` FOREIGN KEY (`student_id`) REFERENCES `lms_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lms_progress`
--
ALTER TABLE `lms_progress`
  ADD CONSTRAINT `fk_progress_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_progress_student` FOREIGN KEY (`student_id`) REFERENCES `lms_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lms_videos`
--
ALTER TABLE `lms_videos`
  ADD CONSTRAINT `fk_videos_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_videos_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `lms_lessons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ref_lgas`
--
ALTER TABLE `ref_lgas`
  ADD CONSTRAINT `fk_lgas_state` FOREIGN KEY (`state_id`) REFERENCES `ref_states` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_states`
--
ALTER TABLE `ref_states`
  ADD CONSTRAINT `fk_states_country` FOREIGN KEY (`country_iso2`) REFERENCES `ref_countries` (`iso2`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
