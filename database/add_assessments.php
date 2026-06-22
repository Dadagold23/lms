<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db.php';
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$ts = '2026-04-15 08:00:00';

// Get all lessons grouped by course
$lessons = $pdo->query("SELECT id, course_id, title, sort_order FROM lms_lessons ORDER BY course_id, sort_order, id")->fetchAll(PDO::FETCH_ASSOC);

// Group by course
$byCourse = [];
foreach ($lessons as $l) {
    $byCourse[$l['course_id']][] = $l;
}

$aStmt = $pdo->prepare("INSERT IGNORE INTO lms_lesson_assessments (lesson_id,course_id,type,title,instructions,pass_score,is_required,created_at) VALUES (?,?,?,?,?,60,1,?)");
$qStmt = $pdo->prepare("INSERT IGNORE INTO lms_assessment_questions (assessment_id,question,option_a,option_b,option_c,option_d,correct_option,marks,sort_order) VALUES (?,?,?,?,?,?,?,1,?)");

$totalAssessments = 0;
$totalQuestions   = 0;

function addAssessment(PDO $pdo, PDOStatement $aStmt, PDOStatement $qStmt, int $lessonId, int $courseId, string $type, string $title, string $instructions, array $questions, string $ts, int &$ta, int &$tq): void {
    $aStmt->execute([$lessonId, $courseId, $type, $title, $instructions, $ts]);
    $aid = (int)$pdo->lastInsertId();
    if ($aid === 0) return; // already exists
    foreach ($questions as $i => $q) {
        $qStmt->execute([$aid, $q[0], $q[1], $q[2], $q[3] ?? null, $q[4] ?? null, $q[5], $i + 1]);
        $tq++;
    }
    $ta++;
}

/* ═══════════════════════════════════════════════════════
   COURSE 1: Graphic Design
═══════════════════════════════════════════════════════ */
if (!empty($byCourse[1])) {
    $ls = $byCourse[1];
    // Lesson 1: Introduction to Graphic Design — Test
    addAssessment($pdo,$aStmt,$qStmt,$ls[0]['id'],1,'test','Lesson 1 Test: Graphic Design Fundamentals',
        'Answer all 5 questions. You need 60% to proceed to the next lesson.',
        [
            ['Which of the following is a principle of design?','Contrast','Coding','Compression','Compilation','A'],
            ['What does "white space" in design refer to?','Empty areas that give designs room to breathe','Spaces filled with white colour only','Margins on a printed page','The background of a website','A'],
            ['Which tool is best for creating vector logos?','Adobe Photoshop','Adobe Illustrator','Microsoft Word','Adobe Premiere','B'],
            ['What is the difference between raster and vector graphics?','Raster uses paths; vector uses pixels','Vector uses paths that scale infinitely; raster uses pixels and loses quality when scaled','They are the same thing','Raster is for print; vector is for screens only','B'],
            ['Which principle groups related design elements together?','Contrast','Alignment','Proximity','Repetition','C'],
        ], $ts, $totalAssessments, $totalQuestions);

    // Lesson 2: Colour Theory — Practical
    addAssessment($pdo,$aStmt,$qStmt,$ls[1]['id'],1,'practical','Lesson 2 Practical: Colour Theory Application',
        'Create a business card design using a complementary colour palette. Apply at least 2 typography rules. Submit a screenshot of your design.',
        [
            ['What are complementary colours?','Colours that are similar to each other','Colours that are opposite each other on the colour wheel','Colours from the same family','Colours used in printing','B'],
            ['Which colour psychology association is correct?','Blue = Energy and urgency','Red = Trust and calm','Green = Growth and health','Yellow = Sophistication','C'],
            ['What is the recommended line spacing (leading) for body text?','0.8x the font size','1.0x the font size','1.4-1.6x the font size','2.0x the font size','C'],
            ['How many fonts should you typically use in a single design?','1 only','2-3 maximum','5-6 for variety','As many as needed','B'],
            ['What is a triadic colour harmony?','Two colours opposite on the wheel','Three colours evenly spaced on the colour wheel','All colours from one family','Tints and shades of one colour','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    // Lesson 3: Logo Design — Assignment
    addAssessment($pdo,$aStmt,$qStmt,$ls[2]['id'],1,'assignment','Lesson 3 Assignment: Logo Design Project',
        'Design a logo for a fictional Nigerian tech startup called "NovaByte". Create 3 concepts on paper, develop the strongest digitally. Submit your final logo on white and dark backgrounds.',
        [
            ['What type of logo uses only the company initials?','Wordmark','Lettermark','Emblem','Combination mark','B'],
            ['Which quality ensures a logo works at any size?','Colourful','Simple','Complex','Animated','B'],
            ['What file format should logos be delivered in for scalability?','JPG','PNG','SVG/AI (vector)','GIF','C'],
            ['What is a "combination mark" logo?','A logo with only text','A logo with only an icon','A logo combining an icon and wordmark','A logo with multiple colours','C'],
            ['Why should logos be timeless?','To save design time','To avoid trends that date quickly and maintain brand consistency','To reduce printing costs','To make them easier to copy','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    // Lesson 4: Print Design — Test
    addAssessment($pdo,$aStmt,$qStmt,$ls[3]['id'],1,'test','Lesson 4 Test: Print Design & Layout',
        'Answer all questions about print design fundamentals.',
        [
            ['What is "bleed" in print design?','Extra artwork beyond the trim edge to prevent white borders after cutting','The ink that bleeds through paper','A type of font style','A colour correction technique','A'],
            ['What colour mode should print designs use?','RGB','HSL','CMYK','HEX','C'],
            ['What is the minimum DPI for print-quality images?','72','150','300','600','C'],
            ['What is the "safe zone" in print design?','The area where important content must stay to avoid being cut off','The area outside the bleed','The printer margin','The colour-safe area','A'],
            ['What does a "tri-fold brochure" have?','3 pages','3 panels created by 2 folds','3 colours','3 fonts','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    // Lesson 5: Digital Design — Practical
    addAssessment($pdo,$aStmt,$qStmt,$ls[4]['id'],1,'practical','Lesson 5 Practical: Social Media Campaign',
        'Create a 3-post social media campaign for a fictional brand. Design for Instagram (1080x1080), Facebook (1200x630), and an Instagram Story (1080x1920). Submit all 3 designs.',
        [
            ['What are the correct Instagram square post dimensions?','800x800px','1080x1080px','1200x1200px','720x720px','B'],
            ['What colour mode do digital designs use?','CMYK','RGB','HSL','Pantone','B'],
            ['What is the "3-second rule" in social media design?','Post 3 times per day','Your message must be clear within 3 seconds','Use 3 colours maximum','Post every 3 hours','B'],
            ['What image format is best for web performance?','BMP','TIFF','WebP or compressed PNG/JPG','RAW','C'],
            ['What is a "call-to-action" (CTA) in design?','A phone number','A clear instruction telling the viewer what to do next','A colour choice','A font style','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    // Lesson 6: Photo Editing — Practical
    addAssessment($pdo,$aStmt,$qStmt,$ls[5]['id'],1,'practical','Lesson 6 Practical: Photo Editing',
        'Take a portrait photo (your own or from Unsplash). Perform: skin smoothing, background removal, colour grading with Curves, and add a text overlay. Submit before and after images.',
        [
            ['What is a "non-destructive" editing workflow?','Editing that cannot be undone','Editing that preserves the original file so changes can be reversed','Editing that reduces file size','Editing that improves quality permanently','B'],
            ['What is the difference between Vibrance and Saturation?','They are the same','Vibrance boosts muted colours naturally; Saturation boosts all colours equally','Saturation is for portraits; Vibrance is for landscapes','Vibrance reduces colours; Saturation increases them','B'],
            ['When should you use a Layer Mask instead of the Eraser tool?','When you want to permanently delete pixels','When you want to hide pixels non-destructively so they can be restored','When you want to add colour','When you want to resize an image','B'],
            ['What does "Camera Raw" in Photoshop do?','Converts images to black and white','Provides professional RAW photo processing with white balance and exposure controls','Adds camera effects','Reduces file size','B'],
            ['What resolution should you export images for web use?','300 DPI','150 DPI','72 DPI','600 DPI','C'],
        ], $ts, $totalAssessments, $totalQuestions);

    // Lesson 7: Portfolio — Assignment
    addAssessment($pdo,$aStmt,$qStmt,$ls[6]['id'],1,'assignment','Lesson 7 Assignment: Portfolio Case Study',
        'Create a Behance project for one of your designs from this course. Write a full case study with: The Challenge, The Process, The Solution, and The Result. Include process images alongside the final design.',
        [
            ['How many projects should a beginner design portfolio contain?','1-2','6-12','20-30','50+','B'],
            ['What is a "kill fee" in freelance design?','A fee for cancelling a project mid-way','A fee for rush work','A fee for extra revisions','A fee for file delivery','A'],
            ['What are the 4 parts of a design case study?','Intro, Body, Conclusion, References','Challenge, Process, Solution, Result','Brief, Concept, Design, Delivery','Research, Sketch, Digital, Print','B'],
            ['What is the recommended deposit percentage before starting freelance work?','10%','30-50%','75%','100%','B'],
            ['Which platform is the industry standard for design portfolios?','LinkedIn','Behance','Instagram','Pinterest','B'],
        ], $ts, $totalAssessments, $totalQuestions);
}

/* ═══════════════════════════════════════════════════════
   COURSE 3: Web Design
═══════════════════════════════════════════════════════ */
if (!empty($byCourse[3])) {
    $ls = $byCourse[3];
    addAssessment($pdo,$aStmt,$qStmt,$ls[0]['id'],3,'test','Lesson 1 Test: Web Design Foundations',
        'Test your understanding of web design fundamentals.',
        [
            ['What does "responsive design" mean?','A website that responds to user clicks','A website that adapts its layout to different screen sizes','A website that loads quickly','A website with animations','B'],
            ['What is the difference between web design and web development?','They are the same','Web design focuses on visual layout and UX; web development focuses on code','Web design is harder','Web development is cheaper','B'],
            ['Which HTML element is used for the main navigation?','<header>','<main>','<nav>','<section>','C'],
            ['What is "visual hierarchy" in web design?','Using many fonts','Guiding the user\'s eye to the most important content first','Making everything the same size','Using bright colours','B'],
            ['What is the "F-pattern" in web design?','A font naming convention','How users visually scan web content — horizontally then vertically','A CSS layout technique','A colour scheme','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[1]['id'],3,'test','Lesson 2 Test: UI Design Fundamentals',
        'Test your knowledge of UI design components and principles.',
        [
            ['What is the minimum touch target size for mobile buttons?','24x24px','32x32px','44x44px','56x56px','C'],
            ['Why should placeholder text NOT replace form labels?','It looks bad','Placeholder text disappears when the user starts typing, making the field purpose unclear','It is too small','It is not accessible','B'],
            ['What spacing scale is commonly used in UI design?','Multiples of 4 or 8px','Multiples of 10px','Multiples of 3px','Random spacing','A'],
            ['What is a "primary button" in UI design?','The largest button','The button for the main action, styled with high contrast','The first button on the page','A button with an icon','B'],
            ['What does "inline validation" mean in forms?','Validating the entire form on submit','Showing errors as the user types, not only on submit','Validating on the server','Checking for duplicate entries','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[2]['id'],3,'practical','Lesson 3 Practical: UX Research',
        'Conduct 3 user interviews about a website or app you use regularly. Create 1 user persona based on your findings. Write 3 Jobs-to-be-Done statements. Submit your persona document.',
        [
            ['What are the 5 stages of the UX design process?','Plan, Design, Build, Test, Launch','Research, Define, Ideate, Prototype, Test','Brief, Sketch, Design, Review, Deliver','Discover, Define, Develop, Deliver, Deploy','B'],
            ['What is a user persona?','A real user account','A fictional but research-based representation of a target user','A user\'s password','A UI element','B'],
            ['What is "card sorting" used for?','Sorting playing cards','Testing navigation structure with users to understand how they categorise content','Organising design files','Creating colour palettes','B'],
            ['What is the difference between qualitative and quantitative research?','Qualitative is faster','Qualitative understands why; quantitative measures how many','They are the same','Quantitative is more accurate','B'],
            ['What is the Jobs-to-be-Done framework?','A project management method','A framework focusing on what job users hire a product to do','A design system','A testing methodology','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[3]['id'],3,'practical','Lesson 4 Practical: Responsive Layout',
        'Build a responsive 3-column card grid using CSS Grid. On mobile: 1 column. On tablet: 2 columns. On desktop: 3 columns. Each card must have an image, title, description, and button. Submit your code.',
        [
            ['What are the standard breakpoints for responsive design?','480px, 960px, 1440px','767px, 1023px, 1279px','600px, 900px, 1200px','320px, 768px, 1024px','B'],
            ['What is the difference between Flexbox and CSS Grid?','Flexbox is newer','Flexbox is one-dimensional (row or column); Grid is two-dimensional (rows AND columns)','Grid is only for images','Flexbox is only for navigation','B'],
            ['Why do we write mobile-first CSS?','Mobile devices are more popular','It is easier to add complexity for larger screens than to remove it for smaller ones','It loads faster','Google requires it','B'],
            ['What does "min-width" in a media query mean?','Apply styles when screen is smaller than this width','Apply styles when screen is at least this width','The minimum font size','The minimum container width','B'],
            ['What CSS property creates a flexible one-dimensional layout?','display: grid','display: flex','display: block','display: inline','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[4]['id'],3,'practical','Lesson 5 Practical: CSS Design System',
        'Create a CSS design system file (variables.css) for a fictional brand. Define: typography scale, colour palette, spacing scale, and border radius values using CSS custom properties. Apply them to a simple landing page.',
        [
            ['What is the WCAG AA contrast ratio requirement for normal text?','2:1','3:1','4.5:1','7:1','C'],
            ['What is the difference between web-safe fonts and web fonts?','Web-safe fonts are better quality','Web-safe fonts are pre-installed on devices; web fonts are loaded from a server','Web fonts are free; web-safe fonts cost money','They are the same','B'],
            ['How does CSS clamp() work for fluid typography?','It limits the number of fonts','It sets a minimum, preferred, and maximum font size that scales with the viewport','It clamps text to one line','It prevents text overflow','B'],
            ['What are CSS custom properties (variables) used for?','Storing JavaScript values','Storing reusable values like colours and spacing that can be changed in one place','Storing images','Storing database values','B'],
            ['What is the purpose of a CSS design system?','To make CSS files larger','To create consistent, reusable design tokens and components across a project','To slow down development','To replace HTML','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[5]['id'],3,'practical','Lesson 6 Practical: Figma Prototype',
        'Design a complete mobile app screen set in Figma (5 screens): Splash, Onboarding, Home, Profile, Settings. Use components for the navigation bar and buttons. Create a clickable prototype connecting all screens. Submit the Figma share link.',
        [
            ['What is a Figma component and why is it useful?','A colour style','A reusable design element — update the master and all instances update automatically','A font style','A page in Figma','B'],
            ['How does Auto Layout in Figma relate to CSS Flexbox?','They are unrelated','Auto Layout makes frames resize based on content, similar to how CSS Flexbox works','Auto Layout is for animations','Auto Layout is only for text','B'],
            ['What does the Inspect panel in Figma provide for developers?','Animation settings','Exact CSS values for any element: font size, colour, spacing, border radius','Database connections','Server configuration','B'],
            ['What is "Smart Animate" in Figma prototyping?','A plugin','Figma automatically animates matching layers between screens for smooth transitions','A colour tool','A grid system','B'],
            ['What is the purpose of a clickable prototype?','To write code','To simulate the product and test it with users before building','To create the final design','To export assets','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[6]['id'],3,'test','Lesson 7 Test: Website Performance & SEO',
        'Test your knowledge of web performance and SEO fundamentals.',
        [
            ['What are the three Core Web Vitals?','Speed, Size, Style','LCP (Largest Contentful Paint), FID (First Input Delay), CLS (Cumulative Layout Shift)','Load, Interact, Stable','Fast, Responsive, Accessible','B'],
            ['What is the target LCP (Largest Contentful Paint) time?','Under 1 second','Under 2.5 seconds','Under 5 seconds','Under 10 seconds','B'],
            ['Why should you use WebP format for images?','It is easier to edit','WebP is 30-50% smaller than JPG/PNG with the same quality, improving load speed','It supports animation','It is the only format browsers support','B'],
            ['What is the ideal length for a title tag?','20-30 characters','50-60 characters','80-100 characters','150-160 characters','B'],
            ['What SEO elements does a web designer directly control?','Server configuration','Title tags, meta descriptions, heading structure, alt text, URL structure','Database queries','Payment processing','B'],
        ], $ts, $totalAssessments, $totalQuestions);
}

/* ═══════════════════════════════════════════════════════
   COURSE 4: Web Development
═══════════════════════════════════════════════════════ */
if (!empty($byCourse[4])) {
    $ls = $byCourse[4];
    addAssessment($pdo,$aStmt,$qStmt,$ls[0]['id'],4,'practical','Lesson 1 Practical: HTML Profile Page',
        'Build a personal profile page using only HTML (no CSS). Include: header with your name, navigation bar, about section, skills list, projects table, and a contact form. Use semantic elements throughout. Submit your HTML file.',
        [
            ['What is the difference between semantic and non-semantic HTML?','Semantic elements are faster','Semantic elements describe their meaning to both browser and developer; non-semantic do not','Non-semantic elements are newer','They are the same','B'],
            ['What does the alt attribute on an image do?','Sets the image size','Describes the image for screen readers and SEO when the image cannot be displayed','Links the image to another page','Adds a border to the image','B'],
            ['What is the difference between <strong> and <b>?','They look different','<strong> indicates importance (semantic); <b> is just visual bold (presentational)','<b> is newer','<strong> is for headings only','B'],
            ['Which HTML5 element represents the main content of a page?','<div>','<section>','<main>','<article>','C'],
            ['What attribute makes a form field required?','mandatory','required','must-fill','validate','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[1]['id'],4,'practical','Lesson 2 Practical: CSS Styling',
        'Style the HTML profile page from Lesson 1. Create a responsive layout using Flexbox and Grid. Add a colour scheme using CSS variables. Include hover effects on links and buttons. Make it fully responsive for mobile.',
        [
            ['Explain the CSS box model and its four components.','Width, Height, Colour, Font','Content, Padding, Border, Margin','Top, Right, Bottom, Left','Display, Position, Float, Clear','B'],
            ['What is the difference between margin and padding?','They are the same','Margin is space outside the border; padding is space between content and border','Padding is outside; margin is inside','Margin affects colour; padding affects size','B'],
            ['When would you use Flexbox vs CSS Grid?','Always use Grid','Flexbox for one-dimensional layouts (row or column); Grid for two-dimensional layouts','Always use Flexbox','Use neither; use float instead','B'],
            ['What does box-sizing: border-box do?','Removes the border','Makes padding and border included in the element\'s total width and height','Adds a box shadow','Changes the display type','B'],
            ['What is a CSS pseudo-class?','A fake class','A keyword added to a selector that specifies a special state (e.g. :hover, :focus)','A class that inherits from another','A class for animations','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[2]['id'],4,'practical','Lesson 3 Practical: JavaScript To-Do List',
        'Build an interactive to-do list using HTML, CSS, and JavaScript. Features: add tasks, mark as complete (toggle class), delete tasks, show task count. Store tasks in localStorage so they persist on page refresh. Submit your code.',
        [
            ['What is the difference between let, const, and var?','They are the same','const cannot be reassigned; let can be reassigned; var is function-scoped and should be avoided','let is faster than const','var is the newest','B'],
            ['How do you select an element by class name in JavaScript?','document.getElementById()','document.querySelector() or document.querySelectorAll()','document.getClass()','document.findElement()','B'],
            ['What does the Fetch API do?','Fetches images','Makes HTTP requests to servers and returns Promises','Fetches CSS files','Fetches database records','B'],
            ['What is localStorage in JavaScript?','A server-side database','A browser-based key-value storage that persists after the page is closed','A temporary session storage','A cookie','B'],
            ['What does addEventListener() do?','Adds a new HTML element','Attaches a function to be called when a specific event occurs on an element','Adds a CSS class','Adds a new variable','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[3]['id'],4,'practical','Lesson 4 Practical: PHP Contact Form',
        'Build a contact form with PHP validation. Fields: name, email, message. Validate all fields server-side. Show success message on valid submission. Show specific error messages for each invalid field. Use sessions to persist form data on error.',
        [
            ['What is the difference between $_GET and $_POST?','$_GET is faster','$_GET sends data in the URL (visible); $_POST sends data in the request body (hidden)','$_POST is more secure for all cases','They are the same','B'],
            ['Why should you always sanitise user input?','To make it look better','To prevent XSS, SQL injection, and other security attacks','To reduce file size','To improve performance','B'],
            ['What is a PHP session and when would you use it?','A database connection','A way to store user data across multiple pages (e.g. login state, form data)','A type of cookie','A server configuration','B'],
            ['What does htmlspecialchars() do in PHP?','Converts HTML to text','Converts special characters to HTML entities to prevent XSS attacks','Removes HTML tags','Adds HTML formatting','B'],
            ['What is the difference between include and require in PHP?','They are the same','require causes a fatal error if the file is not found; include only gives a warning','include is faster','require is for classes only','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[4]['id'],4,'practical','Lesson 5 Practical: MySQL Blog Database',
        'Design a database for a simple blog. Tables: users, posts, categories, comments, post_categories. Write the CREATE TABLE statements with proper data types, constraints, and foreign keys. Insert sample data and write 5 SELECT queries.',
        [
            ['What is the difference between a primary key and a foreign key?','They are the same','A primary key uniquely identifies a row in its table; a foreign key references a primary key in another table','A foreign key is faster','A primary key can be null','B'],
            ['Why should you always use prepared statements?','They are faster','They prevent SQL injection by separating SQL code from user data','They are required by MySQL','They reduce database size','B'],
            ['What is database normalisation?','Making the database faster','Organising data to reduce redundancy and improve data integrity','Adding more tables','Removing all foreign keys','B'],
            ['What does INNER JOIN return?','All rows from both tables','Only rows where there is a match in both tables','All rows from the left table','All rows from the right table','B'],
            ['What is an index in a database?','A list of all tables','A data structure that speeds up queries on frequently searched columns','A type of foreign key','A backup of the database','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[5]['id'],4,'assignment','Lesson 6 Assignment: Blog Application',
        'Build a complete blog application with: user registration/login, create/read/update/delete posts, categories, comment system, and search. Deploy to your local XAMPP server. Submit screenshots of all features working.',
        [
            ['What is CSRF and how do you prevent it?','A type of database error; prevented by indexing','Cross-Site Request Forgery; prevented by including a unique token in every form','A JavaScript error; prevented by try/catch','A CSS issue; prevented by validation','B'],
            ['Why should you never store plain-text passwords?','They take more space','If the database is compromised, all passwords are immediately exposed','They are harder to type','PHP does not support them','B'],
            ['What is the difference between authentication and authorisation?','They are the same','Authentication verifies who you are; authorisation determines what you can do','Authentication is for admins only','Authorisation happens before authentication','B'],
            ['What does password_hash() do in PHP?','Encrypts the password reversibly','Creates a secure one-way hash of the password using bcrypt','Stores the password in a cookie','Sends the password by email','B'],
            ['What is a REST API?','A type of database','An architectural style for building web services that use HTTP methods to perform operations on resources','A PHP framework','A CSS methodology','B'],
        ], $ts, $totalAssessments, $totalQuestions);

    addAssessment($pdo,$aStmt,$qStmt,$ls[6]['id'],4,'test','Lesson 7 Test: APIs & JavaScript Frameworks',
        'Test your knowledge of REST APIs and modern JavaScript.',
        [
            ['What are the 4 main HTTP methods and what does each do?','GET/POST/PUT/DELETE — all retrieve data','GET retrieves, POST creates, PUT/PATCH updates, DELETE removes','GET and POST are the only ones','PUT is for images only','B'],
            ['What does HTTP status code 401 mean?','Not Found','Unauthorized — the request requires authentication','Server Error','Bad Request','B'],
            ['What is the difference between a REST API and a traditional web page?','REST APIs are faster','REST APIs return data (JSON/XML) for any client to consume; web pages return HTML for browsers','REST APIs require a database','Web pages are more secure','B'],
            ['What does json_encode() do in PHP?','Decodes JSON to PHP array','Converts a PHP array or object to a JSON string','Validates JSON format','Sends JSON to the browser','B'],
            ['What is Vue.js used for?','Server-side rendering','Building reactive user interfaces in the browser with JavaScript','Database management','CSS styling','B'],
        ], $ts, $totalAssessments, $totalQuestions);
}

echo "Assessments inserted: {$totalAssessments}\n";
echo "Questions inserted: {$totalQuestions}\n";
