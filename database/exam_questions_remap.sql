-- Exam questions with correct exam_ids (46-60)

SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE lms_exam_questions;
SET FOREIGN_KEY_CHECKS=1;

INSERT INTO `lms_exam_questions` (`id`,`exam_id`,`question`,`option_a`,`option_b`,`option_c`,`option_d`,`correct_option`,`marks`,`created_at`) VALUES
(1,46,'What does the principle of ''contrast'' in design refer to?','Using the same colour throughout','Using opposing elements to create visual interest','Placing all elements in the centre','Removing all white space','B',1,'2026-02-16 07:11:08'),
(2,46,'Which colour model is used for print design?','RGB','HSL','CMYK','HEX','C',1,'2026-02-16 07:11:08'),
(3,46,'What is the minimum DPI for print-quality images?','72','150','300','600','C',1,'2026-02-16 07:11:08'),
(4,46,'A logo that uses only the company''s initials is called a:','Wordmark','Lettermark','Emblem','Combination mark','B',1,'2026-02-16 07:11:08'),
(5,46,'Which Adobe tool is best for creating vector logos?','Photoshop','InDesign','Illustrator','Premiere','C',1,'2026-02-16 07:11:08'),
(6,46,'What is ''bleed'' in print design?','Extra artwork beyond the trim edge','The ink that bleeds through paper','A type of font style','A colour correction technique','A',1,'2026-02-16 07:11:08'),
(7,46,'The ''rule of thirds'' divides the canvas into how many equal parts?','4','6','9','12','C',1,'2026-02-16 07:11:08'),
(8,46,'Which font category is best for body text on screens?','Script','Display','Serif','Sans-serif','D',1,'2026-02-16 07:11:08'),
(9,46,'What does ''kerning'' refer to in typography?','Line spacing','Space between specific character pairs','Font weight','Letter height','B',1,'2026-02-16 07:11:08'),
(10,46,'A brand''s complete visual identity includes:','Only the logo','Logo, colours, and typography','Only the colour palette','Only the website design','B',1,'2026-02-16 07:11:08'),
(11,47,'What is a ''type scale'' in typography?','A ruler for measuring fonts','A defined set of font sizes used consistently','A font family','A typographic error','B',1,'2026-02-16 07:11:08'),
(12,47,'WCAG AA requires a minimum contrast ratio of:','2:1','3:1','4.5:1','7:1','C',1,'2026-02-16 07:11:08'),
(13,47,'What is a ''variable font''?','A font that changes colour','A font containing multiple styles in one file','A font used only for headings','A decorative font','B',1,'2026-02-16 07:11:08'),
(14,47,'In motion design, ''easing'' refers to:','The speed of the entire animation','Controlling acceleration and deceleration','The direction of movement','The colour of the animation','B',1,'2026-02-16 07:11:08'),
(15,47,'A ''dieline'' in packaging design is:','The final printed package','The flat unfolded template of a package','A cutting tool','A type of ink','B',1,'2026-02-16 07:11:08'),
(16,47,'Brand architecture where one master brand covers everything is called:','House of Brands','Endorsed brand','Branded House','Sub-brand','C',1,'2026-02-16 07:11:08'),
(17,47,'What is a ''kill fee'' in freelance design?','A fee for cancelling a project','A fee for rush work','A fee for extra revisions','A fee for file delivery','A',1,'2026-02-16 07:11:08'),
(18,47,'The 12 principles of animation were developed by:','Adobe','Pixar','Disney','DreamWorks','C',1,'2026-02-16 07:11:08'),
(19,47,'What does ''ASO'' stand for in mobile app marketing?','App Store Optimisation','Application Security Operations','Automated Software Output','App Scaling Options','A',1,'2026-02-16 07:11:08'),
(20,47,'A ''positioning statement'' in branding defines:','The logo placement','Where the brand sits in the market relative to competitors','The physical location of the business','The price of the product','B',1,'2026-02-16 07:11:08'),
(21,48,'What does ''responsive design'' mean?','A website that responds to user clicks','A website that adapts to different screen sizes','A website that loads quickly','A website with animations','B',1,'2026-02-16 07:11:08'),
(22,48,'Which CSS property is used to create a flexible one-dimensional layout?','Grid','Flexbox','Float','Position','B',1,'2026-02-16 07:11:08'),
(23,48,'What is the standard breakpoint for mobile devices?','480px','768px','1024px','1200px','B',1,'2026-02-16 07:11:08'),
(24,48,'In Figma, what is a ''component''?','A page in the design file','A reusable design element','A colour style','A font style','B',1,'2026-02-16 07:11:08'),
(25,48,'What does LCP stand for in Core Web Vitals?','Largest Contentful Paint','Longest Content Period','Least Cumulative Performance','Last Content Processed','A',1,'2026-02-16 07:11:08'),
(26,48,'Which HTML element is used for the main navigation?','<header>','<main>','<nav>','<section>','C',1,'2026-02-16 07:11:08'),
(27,48,'What is the purpose of alt text on images?','To style the image','To describe the image for screen readers and SEO','To set the image size','To link the image','B',1,'2026-02-16 07:11:08'),
(28,48,'CSS custom properties are also known as:','CSS classes','CSS variables','CSS functions','CSS selectors','B',1,'2026-02-16 07:11:08'),
(29,48,'What is the ''F-pattern'' in web design?','A font naming convention','How users visually scan web content','A CSS layout technique','A colour scheme','B',1,'2026-02-16 07:11:08'),
(30,48,'Which tool is the industry standard for UI/UX design?','Sketch','Adobe XD','Figma','InVision','C',1,'2026-02-16 07:11:08'),
(31,49,'What does HTML stand for?','Hyper Text Markup Language','High Tech Modern Language','Hyper Transfer Markup Language','Home Tool Markup Language','A',1,'2026-02-16 07:11:08'),
(32,49,'Which CSS property controls the space inside an element''s border?','Margin','Padding','Border','Outline','B',1,'2026-02-16 07:11:08'),
(33,49,'What is the correct way to declare a constant in JavaScript?','var x = 5','let x = 5','const x = 5','define x = 5','C',1,'2026-02-16 07:11:08'),
(34,49,'Which PHP function hashes a password securely?','md5()','sha1()','password_hash()','encrypt()','C',1,'2026-02-16 07:11:08'),
(35,49,'What does SQL stand for?','Structured Query Language','Simple Query Language','Standard Question Language','System Query Logic','A',1,'2026-02-16 07:11:08'),
(36,49,'Which HTTP method is used to retrieve data from a server?','POST','PUT','GET','DELETE','C',1,'2026-02-16 07:11:08'),
(37,49,'What is a prepared statement in PHP?','A pre-written SQL query','A parameterised query that prevents SQL injection','A stored procedure','A database view','B',1,'2026-02-16 07:11:08'),
(38,49,'What does ''responsive design'' require in HTML?','A viewport meta tag','A CSS framework','JavaScript','A CDN','A',1,'2026-02-16 07:11:08'),
(39,49,'Which command initialises a new Git repository?','git start','git init','git create','git new','B',1,'2026-02-16 07:11:08'),
(40,49,'What is the purpose of a .env file?','Store HTML templates','Store environment variables and secrets','Store CSS styles','Store database records','B',1,'2026-02-16 07:11:08'),
(41,50,'What does OOP stand for?','Object Oriented Programming','Open Output Processing','Ordered Object Protocol','Optional Output Parameters','A',1,'2026-02-16 07:11:08'),
(42,50,'In PHP OOP, what keyword is used to create an object from a class?','create','make','new','build','C',1,'2026-02-16 07:11:08'),
(43,50,'What is the difference between public and private in PHP classes?','Public methods are faster','Private members can only be accessed within the class','Public members cannot be inherited','Private methods are static','B',1,'2026-02-16 07:11:08'),
(44,50,'Which PHP function verifies a hashed password?','password_check()','hash_verify()','password_verify()','verify_hash()','C',1,'2026-02-16 07:11:08'),
(45,50,'What does PDO stand for?','PHP Data Objects','PHP Database Operations','Prepared Data Output','PHP Dynamic Objects','A',1,'2026-02-16 07:11:08'),
(46,50,'Which SQL clause filters grouped results?','WHERE','HAVING','GROUP BY','ORDER BY','B',1,'2026-02-16 07:11:08'),
(47,50,'What is a database transaction?','A payment record','A group of SQL operations that succeed or fail together','A database backup','A stored procedure','B',1,'2026-02-16 07:11:08'),
(48,50,'What is CSRF?','Cross-Site Request Forgery','Cross-Server Resource Fetch','Client-Side Request Filter','Content Security Response Format','A',1,'2026-02-16 07:11:08'),
(49,50,'Which HTTP status code means ''Created''?','200','201','400','404','B',1,'2026-02-16 07:11:08'),
(50,50,'What does JWT stand for?','Java Web Token','JSON Web Token','JavaScript Web Transfer','JSON Web Transfer','B',1,'2026-02-16 07:11:08'),
(51,51,'What language does Flutter use?','JavaScript','Kotlin','Dart','Swift','C',1,'2026-02-16 07:11:08'),
(52,51,'What is the difference between StatelessWidget and StatefulWidget?','StatelessWidget is faster','StatefulWidget can change over time','StatelessWidget uses more memory','StatefulWidget cannot be reused','B',1,'2026-02-16 07:11:08'),
(53,51,'What does setState() do in Flutter?','Saves data to the database','Triggers a rebuild of the widget','Navigates to a new screen','Sends an HTTP request','B',1,'2026-02-16 07:11:08'),
(54,51,'What is Firebase Firestore?','A SQL database','A NoSQL cloud database with real-time sync','A file storage service','An authentication service','B',1,'2026-02-16 07:11:08'),
(55,51,'What is the difference between an APK and an App Bundle?','APK is for iOS, App Bundle is for Android','App Bundle is required for Play Store, APK is a direct install file','They are the same thing','APK is newer than App Bundle','B',1,'2026-02-16 07:11:08'),
(56,51,'What does the Provider package do in Flutter?','Provides HTTP requests','Manages state across the widget tree','Provides database access','Provides animations','B',1,'2026-02-16 07:11:08'),
(57,51,'What is ASO?','App Store Optimisation','Android System Operations','Application Security Overview','Automated Store Output','A',1,'2026-02-16 07:11:08'),
(58,51,'Which Firebase service handles user login?','Firestore','Firebase Storage','Firebase Authentication','Firebase Analytics','C',1,'2026-02-16 07:11:08'),
(59,51,'What is the minimum touch target size for mobile buttons?','24x24px','32x32px','44x44px','56x56px','C',1,'2026-02-16 07:11:08'),
(60,51,'What does PWA stand for?','Progressive Web App','Portable Web Application','PHP Web App','Public Web Access','A',1,'2026-02-16 07:11:08'),
(61,52,'What does UX stand for?','User Experience','User Extension','Unified Experience','User Execution','A',1,'2026-02-16 07:11:08'),
(62,52,'How many users are typically needed to find 85% of usability issues?','2','5','10','20','B',1,'2026-02-16 07:11:08'),
(63,52,'What is a user persona?','A real user account','A fictional but research-based representation of a target user','A user''s password','A user interface element','B',1,'2026-02-16 07:11:08'),
(64,52,'What is ''card sorting'' used for?','Sorting playing cards','Testing navigation structure with users','Organising design files','Creating colour palettes','B',1,'2026-02-16 07:11:08'),
(65,52,'What does WCAG stand for?','Web Content Accessibility Guidelines','Web Coding and Graphics','Website Content and Graphics','Web Component Architecture Guide','A',1,'2026-02-16 07:11:08'),
(66,52,'What is a ''skeleton screen''?','A wireframe','A loading placeholder that shows the layout before content loads','A dark mode design','An empty state','B',1,'2026-02-16 07:11:08'),
(67,52,'In Figma, what does ''Auto Layout'' do?','Automatically creates animations','Makes frames resize based on content, like CSS Flexbox','Automatically names layers','Exports designs automatically','B',1,'2026-02-16 07:11:08'),
(68,52,'What is the Jobs-to-be-Done framework?','A project management method','A framework focusing on what job users hire a product to do','A design system','A testing methodology','B',1,'2026-02-16 07:11:08'),
(69,52,'What is the minimum contrast ratio for normal text (WCAG AA)?','2:1','3:1','4.5:1','7:1','C',1,'2026-02-16 07:11:08'),
(70,52,'What is a ''design system''?','A collection of reusable components guided by clear standards','A project management tool','A type of software','A colour palette','A',1,'2026-02-16 07:11:08'),
(71,53,'What does SEO stand for?','Search Engine Optimisation','Social Engagement Operations','Site Engagement Output','Search Engine Operations','A',1,'2026-02-16 07:11:08'),
(72,53,'What is the ideal length for a title tag?','20-30 characters','50-60 characters','80-100 characters','150-160 characters','B',1,'2026-02-16 07:11:08'),
(73,53,'What does CTR stand for?','Click Through Rate','Content Transfer Rate','Customer Tracking Report','Conversion Tracking Rate','A',1,'2026-02-16 07:11:08'),
(74,53,'What is a ''lead magnet''?','A type of advertisement','Something valuable offered in exchange for an email address','A social media post','A type of backlink','B',1,'2026-02-16 07:11:08'),
(75,53,'What is the average ROI of email marketing?','$5 per $1 spent','$12 per $1 spent','$36 per $1 spent','$100 per $1 spent','C',1,'2026-02-16 07:11:08'),
(76,53,'What does ROAS stand for?','Return on Ad Spend','Rate of Audience Segmentation','Revenue on All Sales','Return on Asset Strategy','A',1,'2026-02-16 07:11:08'),
(77,53,'What are UTM parameters used for?','Tracking the source of website traffic','Improving page speed','Creating email templates','Setting up Google Ads','A',1,'2026-02-16 07:11:08'),
(78,53,'What is the 80/20 rule in social media content?','80% promotional, 20% educational','80% valuable content, 20% promotional','80% images, 20% text','80% paid, 20% organic','B',1,'2026-02-16 07:11:08'),
(79,53,'What does CPA stand for in digital marketing?','Cost Per Acquisition','Content Per Article','Click Per Advertisement','Customer Profile Analysis','A',1,'2026-02-16 07:11:08'),
(80,53,'What is A/B testing?','Testing two versions to see which performs better','Testing a website on two browsers','Testing two different products','Testing two marketing teams','A',1,'2026-02-16 07:11:08'),
(81,54,'What does the AVERAGE function calculate?','The most frequent value','The middle value','The sum divided by count','The highest value','C',1,'2026-02-16 07:11:08'),
(82,54,'What is a pivot table used for?','Creating charts','Summarising large datasets quickly','Writing SQL queries','Formatting cells','B',1,'2026-02-16 07:11:08'),
(83,54,'What does SQL GROUP BY do?','Sorts results','Groups rows with the same values for aggregation','Filters rows','Joins tables','B',1,'2026-02-16 07:11:08'),
(84,54,'What is the difference between mean and median?','They are the same','Mean is the middle value;

-- Update total_questions on actual exams
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=46;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=47;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=48;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=49;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=50;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=51;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=52;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=53;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=54;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=55;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=56;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=57;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=58;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=59;
UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id=60;
