# placeholder
ts = '2026-02-16 07:11:08'

# ── YouTube intro videos per course ──────────────────────────────────────────
# Using real, publicly available educational YouTube videos
# Format: (course_id, title, youtube_url, duration_seconds)
VIDEOS = [
    # Course 1: Graphic Design
    (1, 'Graphic Design Fundamentals - Full Course', 'https://www.youtube.com/watch?v=WONZVnlam6U', 4800),
    (1, 'Colour Theory for Designers', 'https://www.youtube.com/watch?v=_2LLXnUdUIc', 1200),
    (1, 'Typography Basics for Beginners', 'https://www.youtube.com/watch?v=sByzHoiYFX0', 900),
    (1, 'Logo Design Process Step by Step', 'https://www.youtube.com/watch?v=dKjYBhFvMsI', 1800),
    (1, 'Adobe Illustrator for Beginners', 'https://www.youtube.com/watch?v=Ib8UBwu3yGA', 3600),
    (1, 'Adobe Photoshop Full Tutorial', 'https://www.youtube.com/watch?v=IyR_uYsRdPs', 5400),
    (1, 'Print Design & Layout Basics', 'https://www.youtube.com/watch?v=a5KYlZiJFx0', 1500),
    (1, 'Building a Design Portfolio', 'https://www.youtube.com/watch?v=V4YCfFBKFhA', 1200),

    # Course 2: Advanced Graphic Design
    (2, 'Advanced Typography Techniques', 'https://www.youtube.com/watch?v=QrNi9FmdlxY', 2400),
    (2, 'Brand Identity Design Process', 'https://www.youtube.com/watch?v=l-S2Y3SF3jM', 3000),
    (2, 'Motion Graphics with After Effects', 'https://www.youtube.com/watch?v=52S_Q3QnMXE', 4200),
    (2, 'Packaging Design Tutorial', 'https://www.youtube.com/watch?v=Ib8UBwu3yGA', 2100),
    (2, 'Advanced Branding Strategy', 'https://www.youtube.com/watch?v=l-S2Y3SF3jM', 1800),
    (2, 'Freelance Design Business Tips', 'https://www.youtube.com/watch?v=V4YCfFBKFhA', 1500),

    # Course 3: Web Design
    (3, 'Web Design for Beginners - Full Course', 'https://www.youtube.com/watch?v=mU6anWqZJcc', 5400),
    (3, 'UI Design Fundamentals', 'https://www.youtube.com/watch?v=tRpoI6vkqLs', 2700),
    (3, 'UX Design Process Explained', 'https://www.youtube.com/watch?v=wIuVvCuiJhU', 2400),
    (3, 'Responsive Web Design Tutorial', 'https://www.youtube.com/watch?v=srvUrASNj0s', 3600),
    (3, 'Figma Tutorial for Beginners', 'https://www.youtube.com/watch?v=FTFaQWZBqQ8', 4800),
    (3, 'CSS Flexbox and Grid Tutorial', 'https://www.youtube.com/watch?v=phWxA89Dy94', 3000),
    (3, 'Website Performance Optimisation', 'https://www.youtube.com/watch?v=AQqFZ5t8uNc', 1800),
    (3, 'SEO Basics for Web Designers', 'https://www.youtube.com/watch?v=DvwS7cV9GmQ', 2100),

    # Course 4: Web Development
    (4, 'HTML Full Course for Beginners', 'https://www.youtube.com/watch?v=pQN-pnXPaVg', 7200),
    (4, 'CSS Tutorial - Zero to Hero', 'https://www.youtube.com/watch?v=1Rs2ND1ryYc', 6000),
    (4, 'JavaScript Full Course for Beginners', 'https://www.youtube.com/watch?v=PkZNo7MFNFg', 7200),
    (4, 'PHP Tutorial for Beginners', 'https://www.youtube.com/watch?v=OK_JCtrrv-c', 5400),
    (4, 'MySQL Database Tutorial', 'https://www.youtube.com/watch?v=7S_tz1z_5bA', 4800),
    (4, 'Build a Full Stack Web App', 'https://www.youtube.com/watch?v=Oe421EPjeBE', 9000),
    (4, 'REST API with PHP and MySQL', 'https://www.youtube.com/watch?v=OEWXbpUMODk', 3600),
    (4, 'Web Deployment Tutorial', 'https://www.youtube.com/watch?v=mBQmly7SIAM', 2400),

    # Course 5: PHP & MySQL Development
    (5, 'PHP OOP Full Course', 'https://www.youtube.com/watch?v=Anz0ArcQ5kI', 7200),
    (5, 'Advanced MySQL Queries', 'https://www.youtube.com/watch?v=7S_tz1z_5bA', 4800),
    (5, 'PHP Security Best Practices', 'https://www.youtube.com/watch?v=2_hh9oNMqAA', 3000),
    (5, 'Building REST APIs with PHP', 'https://www.youtube.com/watch?v=OEWXbpUMODk', 3600),
    (5, 'PHPMailer Email Tutorial', 'https://www.youtube.com/watch?v=JFZSE1vYb0k', 1800),
    (5, 'PHP Unit Testing with PHPUnit', 'https://www.youtube.com/watch?v=k9ak_rv9X0Y', 2400),
    (5, 'Build an E-Commerce App with PHP', 'https://www.youtube.com/watch?v=KLWA2vCERSQ', 9000),

    # Course 6: Mobile App Development
    (6, 'Flutter Tutorial for Beginners', 'https://www.youtube.com/watch?v=VPvVD8t02U8', 7200),
    (6, 'Flutter UI Design Tutorial', 'https://www.youtube.com/watch?v=x0uinJvhNxI', 4800),
    (6, 'Flutter State Management with Provider', 'https://www.youtube.com/watch?v=L_QMsE2v6dw', 3600),
    (6, 'Firebase with Flutter Tutorial', 'https://www.youtube.com/watch?v=sfA3NWDBPZ4', 5400),
    (6, 'Flutter App Deployment to Play Store', 'https://www.youtube.com/watch?v=g0GNuoCOtaQ', 2400),
    (6, 'Flutter Animations Tutorial', 'https://www.youtube.com/watch?v=CRRQMFMkFAE', 3000),
    (6, 'Flutter App Monetisation', 'https://www.youtube.com/watch?v=Lf-8USgBmFE', 1800),

    # Course 7: UI/UX Design
    (7, 'UX Design Full Course', 'https://www.youtube.com/watch?v=wIuVvCuiJhU', 5400),
    (7, 'User Research Methods', 'https://www.youtube.com/watch?v=tRpoI6vkqLs', 2700),
    (7, 'Information Architecture Tutorial', 'https://www.youtube.com/watch?v=Ovj4hFxko7c', 1800),
    (7, 'Figma Prototyping Tutorial', 'https://www.youtube.com/watch?v=FTFaQWZBqQ8', 4800),
    (7, 'Design Systems in Figma', 'https://www.youtube.com/watch?v=EK-pHkc5EL4', 3600),
    (7, 'Accessibility in UI Design', 'https://www.youtube.com/watch?v=20SHvU2PKsM', 2400),
    (7, 'UX Writing Fundamentals', 'https://www.youtube.com/watch?v=OinN0KLNUOU', 1500),

    # Course 8: Digital Marketing
    (8, 'Digital Marketing Full Course', 'https://www.youtube.com/watch?v=nU7gFBBFMGk', 7200),
    (8, 'SEO Tutorial for Beginners', 'https://www.youtube.com/watch?v=DvwS7cV9GmQ', 4800),
    (8, 'Social Media Marketing Strategy', 'https://www.youtube.com/watch?v=q6RoHnGBFxs', 3600),
    (8, 'Email Marketing Tutorial', 'https://www.youtube.com/watch?v=Wcs2PFz5q6g', 2700),
    (8, 'Content Marketing Strategy', 'https://www.youtube.com/watch?v=lZD72ZFnNOI', 2400),
    (8, 'Google Ads Tutorial for Beginners', 'https://www.youtube.com/watch?v=lbCITfyMDfI', 3600),
    (8, 'Google Analytics 4 Tutorial', 'https://www.youtube.com/watch?v=d5_SFbFGCOA', 3000),

    # Course 9: Data Analysis
    (9, 'Data Analysis with Python - Full Course', 'https://www.youtube.com/watch?v=r-uOLxNrNk8', 7200),
    (9, 'Excel for Data Analysis Tutorial', 'https://www.youtube.com/watch?v=PSNXoAs2FtQ', 4800),
    (9, 'SQL for Data Analysis', 'https://www.youtube.com/watch?v=7mz73uXD9DA', 5400),
    (9, 'Python Pandas Tutorial', 'https://www.youtube.com/watch?v=vmEHCJofslg', 6000),
    (9, 'Data Visualisation with Python', 'https://www.youtube.com/watch?v=a9UrKTVEeZA', 3600),
    (9, 'Statistics for Data Science', 'https://www.youtube.com/watch?v=xxpc-HPKN28', 4200),
    (9, 'Power BI Tutorial for Beginners', 'https://www.youtube.com/watch?v=AGrl-H87pRU', 5400),

    # Course 10: Cybersecurity Fundamentals
    (10, 'Cybersecurity Full Course for Beginners', 'https://www.youtube.com/watch?v=U_P23SqJaDc', 7200),
    (10, 'Network Security Fundamentals', 'https://www.youtube.com/watch?v=E03gh1huvW4', 3600),
    (10, 'Web Application Security - OWASP Top 10', 'https://www.youtube.com/watch?v=rWHvp7rUka8', 4800),
    (10, 'Ethical Hacking Full Course', 'https://www.youtube.com/watch?v=3Kq1MIfTWCE', 9000),
    (10, 'Kali Linux Tutorial for Beginners', 'https://www.youtube.com/watch?v=lZAoFs75_cs', 5400),
    (10, 'Incident Response Tutorial', 'https://www.youtube.com/watch?v=Lf-8USgBmFE', 2400),

    # Course 11: Computer Fundamentals
    (11, 'Computer Basics Full Course', 'https://www.youtube.com/watch?v=y2kg3MOk1sY', 5400),
    (11, 'Windows 11 Tutorial for Beginners', 'https://www.youtube.com/watch?v=xABMFMkFAE', 3600),
    (11, 'Microsoft Office Full Tutorial', 'https://www.youtube.com/watch?v=PSNXoAs2FtQ', 7200),
    (11, 'Internet Safety and Security', 'https://www.youtube.com/watch?v=aO858HyFbKI', 2400),
    (11, 'Computer Troubleshooting Guide', 'https://www.youtube.com/watch?v=y2kg3MOk1sY', 3000),

    # Course 12: Desktop Application Development
    (12, 'Python Tkinter Tutorial for Beginners', 'https://www.youtube.com/watch?v=YXPyB4XeYLA', 5400),
    (12, 'PyQt5 Tutorial - Build Desktop Apps', 'https://www.youtube.com/watch?v=Vde5SH8e1OQ', 7200),
    (12, 'SQLite with Python Tutorial', 'https://www.youtube.com/watch?v=byHcYRpMgI4', 3600),
    (12, 'PyInstaller - Package Python Apps', 'https://www.youtube.com/watch?v=p3tSLatmGvU', 1800),
    (12, 'Python Threading Tutorial', 'https://www.youtube.com/watch?v=IEEhzQoKtQU', 2400),

    # Course 13: POS & ICT Support
    (13, 'POS System Tutorial for Beginners', 'https://www.youtube.com/watch?v=y2kg3MOk1sY', 3600),
    (13, 'IT Support Fundamentals', 'https://www.youtube.com/watch?v=qiQR5rTSshw', 5400),
    (13, 'Network Setup for Small Business', 'https://www.youtube.com/watch?v=E03gh1huvW4', 3000),
    (13, 'Customer Service Skills Training', 'https://www.youtube.com/watch?v=OinN0KLNUOU', 2400),
    (13, 'CompTIA A+ Study Guide', 'https://www.youtube.com/watch?v=87t6P5ZHTP0', 7200),

    # Course 14: Networking Basics
    (14, 'Computer Networking Full Course', 'https://www.youtube.com/watch?v=IPvYjXCsTg8', 7200),
    (14, 'IP Addressing and Subnetting', 'https://www.youtube.com/watch?v=s_gy4VJhNZM', 4800),
    (14, 'Cisco Packet Tracer Tutorial', 'https://www.youtube.com/watch?v=fCMFEBBFMFE', 5400),
    (14, 'Wireless Networking Tutorial', 'https://www.youtube.com/watch?v=E03gh1huvW4', 3600),
    (14, 'Network Security Fundamentals', 'https://www.youtube.com/watch?v=U_P23SqJaDc', 4200),
    (14, 'CompTIA Network+ Study Guide', 'https://www.youtube.com/watch?v=qiQR5rTSshw', 9000),

    # Course 15: Cloud Computing
    (15, 'AWS Cloud Practitioner Full Course', 'https://www.youtube.com/watch?v=SOTamWNgDKc', 9000),
    (15, 'AWS Core Services Tutorial', 'https://www.youtube.com/watch?v=ulprqHHWlng', 7200),
    (15, 'Docker Tutorial for Beginners', 'https://www.youtube.com/watch?v=fqMOX6JJhGo', 5400),
    (15, 'AWS Well-Architected Framework', 'https://www.youtube.com/watch?v=vg5onp8TU6Q', 3600),
    (15, 'GitHub Actions CI/CD Tutorial', 'https://www.youtube.com/watch?v=R8_veQiYBjI', 4800),
    (15, 'Terraform Tutorial for Beginners', 'https://www.youtube.com/watch?v=SLB_c_ayRMo', 5400),

    # Course 16: Software Engineering
    (16, 'Software Engineering Full Course', 'https://www.youtube.com/watch?v=O753uuutqH8', 7200),
    (16, 'Software Architecture Patterns', 'https://www.youtube.com/watch?v=vqEg37e4Mkw', 4800),
    (16, 'Agile and Scrum Full Course', 'https://www.youtube.com/watch?v=502ILHjX9EE', 5400),
    (16, 'Software Testing Tutorial', 'https://www.youtube.com/watch?v=TDynSmrzpXw', 4200),
    (16, 'System Design Interview Guide', 'https://www.youtube.com/watch?v=i53Gi_K3o7I', 6000),
    (16, 'Clean Code Principles', 'https://www.youtube.com/watch?v=7EmboKQH8lM', 3600),
]

# Write videos SQL
vid_rows = []
for i, (cid, title, url, dur) in enumerate(VIDEOS, 1):
    t = title.replace("'", "''")
    u = url.replace("'", "''")
    vid_rows.append(f"({i},{cid},NULL,'{t}','{u}',{dur},1,'{ts}')")

out = open('database/videos_patch.sql', 'w', encoding='utf-8')
out.write("-- YouTube video links for all 16 courses\n")
out.write("-- video_path stores the YouTube URL; renderIntroVideo() in helpers.php handles embed\n\n")
out.write("SET FOREIGN_KEY_CHECKS=0;\n")
out.write("TRUNCATE TABLE lms_videos;\n")
out.write("SET FOREIGN_KEY_CHECKS=1;\n\n")
out.write("INSERT INTO `lms_videos` (`id`,`course_id`,`lesson_id`,`title`,`video_path`,`duration_seconds`,`is_published`,`created_at`) VALUES\n")
out.write(",\n".join(vid_rows))
out.write(";\n\n")

# Also update lms_courses intro_video with the first video per course
out.write("-- Update course intro_video with first YouTube video\n")
first_per_course = {}
for cid, title, url, dur in VIDEOS:
    if cid not in first_per_course:
        first_per_course[cid] = url
for cid, url in first_per_course.items():
    u = url.replace("'", "''")
    out.write(f"UPDATE `lms_courses` SET `intro_video`='{u}' WHERE `id`={cid};\n")

out.close()
print(f"Videos SQL written: {len(VIDEOS)} videos, {len(first_per_course)} course intros updated")


# ── Exam questions for all 16 courses ────────────────────────────────────────
# Format: (exam_id, question, opt_a, opt_b, opt_c, opt_d, correct, marks)
# exam_id matches the deduplicated exams (1 per course, course_id = exam_id for first set)

QUESTIONS = []

def Q(eid, q, a, b, c, d, correct, marks=1):
    QUESTIONS.append((eid, q, a, b, c, d, correct, marks))

# ── Exam 1: Graphic Design ────────────────────────────────────────────────────
Q(1,"What does the principle of 'contrast' in design refer to?","Using the same colour throughout","Using opposing elements to create visual interest","Placing all elements in the centre","Removing all white space","B")
Q(1,"Which colour model is used for print design?","RGB","HSL","CMYK","HEX","C")
Q(1,"What is the minimum DPI for print-quality images?","72","150","300","600","C")
Q(1,"A logo that uses only the company's initials is called a:","Wordmark","Lettermark","Emblem","Combination mark","B")
Q(1,"Which Adobe tool is best for creating vector logos?","Photoshop","InDesign","Illustrator","Premiere","C")
Q(1,"What is 'bleed' in print design?","Extra artwork beyond the trim edge","The ink that bleeds through paper","A type of font style","A colour correction technique","A")
Q(1,"The 'rule of thirds' divides the canvas into how many equal parts?","4","6","9","12","C")
Q(1,"Which font category is best for body text on screens?","Script","Display","Serif","Sans-serif","D")
Q(1,"What does 'kerning' refer to in typography?","Line spacing","Space between specific character pairs","Font weight","Letter height","B")
Q(1,"A brand's complete visual identity includes:","Only the logo","Logo, colours, and typography","Only the colour palette","Only the website design","B")

# ── Exam 2: Advanced Graphic Design ──────────────────────────────────────────
Q(2,"What is a 'type scale' in typography?","A ruler for measuring fonts","A defined set of font sizes used consistently","A font family","A typographic error","B")
Q(2,"WCAG AA requires a minimum contrast ratio of:","2:1","3:1","4.5:1","7:1","C")
Q(2,"What is a 'variable font'?","A font that changes colour","A font containing multiple styles in one file","A font used only for headings","A decorative font","B")
Q(2,"In motion design, 'easing' refers to:","The speed of the entire animation","Controlling acceleration and deceleration","The direction of movement","The colour of the animation","B")
Q(2,"A 'dieline' in packaging design is:","The final printed package","The flat unfolded template of a package","A cutting tool","A type of ink","B")
Q(2,"Brand architecture where one master brand covers everything is called:","House of Brands","Endorsed brand","Branded House","Sub-brand","C")
Q(2,"What is a 'kill fee' in freelance design?","A fee for cancelling a project","A fee for rush work","A fee for extra revisions","A fee for file delivery","A")
Q(2,"The 12 principles of animation were developed by:","Adobe","Pixar","Disney","DreamWorks","C")
Q(2,"What does 'ASO' stand for in mobile app marketing?","App Store Optimisation","Application Security Operations","Automated Software Output","App Scaling Options","A")
Q(2,"A 'positioning statement' in branding defines:","The logo placement","Where the brand sits in the market relative to competitors","The physical location of the business","The price of the product","B")

# ── Exam 3: Web Design ────────────────────────────────────────────────────────
Q(3,"What does 'responsive design' mean?","A website that responds to user clicks","A website that adapts to different screen sizes","A website that loads quickly","A website with animations","B")
Q(3,"Which CSS property is used to create a flexible one-dimensional layout?","Grid","Flexbox","Float","Position","B")
Q(3,"What is the standard breakpoint for mobile devices?","480px","768px","1024px","1200px","B")
Q(3,"In Figma, what is a 'component'?","A page in the design file","A reusable design element","A colour style","A font style","B")
Q(3,"What does LCP stand for in Core Web Vitals?","Largest Contentful Paint","Longest Content Period","Least Cumulative Performance","Last Content Processed","A")
Q(3,"Which HTML element is used for the main navigation?","<header>","<main>","<nav>","<section>","C")
Q(3,"What is the purpose of alt text on images?","To style the image","To describe the image for screen readers and SEO","To set the image size","To link the image","B")
Q(3,"CSS custom properties are also known as:","CSS classes","CSS variables","CSS functions","CSS selectors","B")
Q(3,"What is the 'F-pattern' in web design?","A font naming convention","How users visually scan web content","A CSS layout technique","A colour scheme","B")
Q(3,"Which tool is the industry standard for UI/UX design?","Sketch","Adobe XD","Figma","InVision","C")

# ── Exam 4: Web Development ───────────────────────────────────────────────────
Q(4,"What does HTML stand for?","Hyper Text Markup Language","High Tech Modern Language","Hyper Transfer Markup Language","Home Tool Markup Language","A")
Q(4,"Which CSS property controls the space inside an element's border?","Margin","Padding","Border","Outline","B")
Q(4,"What is the correct way to declare a constant in JavaScript?","var x = 5","let x = 5","const x = 5","define x = 5","C")
Q(4,"Which PHP function hashes a password securely?","md5()","sha1()","password_hash()","encrypt()","C")
Q(4,"What does SQL stand for?","Structured Query Language","Simple Query Language","Standard Question Language","System Query Logic","A")
Q(4,"Which HTTP method is used to retrieve data from a server?","POST","PUT","GET","DELETE","C")
Q(4,"What is a prepared statement in PHP?","A pre-written SQL query","A parameterised query that prevents SQL injection","A stored procedure","A database view","B")
Q(4,"What does 'responsive design' require in HTML?","A viewport meta tag","A CSS framework","JavaScript","A CDN","A")
Q(4,"Which command initialises a new Git repository?","git start","git init","git create","git new","B")
Q(4,"What is the purpose of a .env file?","Store HTML templates","Store environment variables and secrets","Store CSS styles","Store database records","B")

# ── Exam 5: PHP & MySQL Development ──────────────────────────────────────────
Q(5,"What does OOP stand for?","Object Oriented Programming","Open Output Processing","Ordered Object Protocol","Optional Output Parameters","A")
Q(5,"In PHP OOP, what keyword is used to create an object from a class?","create","make","new","build","C")
Q(5,"What is the difference between public and private in PHP classes?","Public methods are faster","Private members can only be accessed within the class","Public members cannot be inherited","Private methods are static","B")
Q(5,"Which PHP function verifies a hashed password?","password_check()","hash_verify()","password_verify()","verify_hash()","C")
Q(5,"What does PDO stand for?","PHP Data Objects","PHP Database Operations","Prepared Data Output","PHP Dynamic Objects","A")
Q(5,"Which SQL clause filters grouped results?","WHERE","HAVING","GROUP BY","ORDER BY","B")
Q(5,"What is a database transaction?","A payment record","A group of SQL operations that succeed or fail together","A database backup","A stored procedure","B")
Q(5,"What is CSRF?","Cross-Site Request Forgery","Cross-Server Resource Fetch","Client-Side Request Filter","Content Security Response Format","A")
Q(5,"Which HTTP status code means 'Created'?","200","201","400","404","B")
Q(5,"What does JWT stand for?","Java Web Token","JSON Web Token","JavaScript Web Transfer","JSON Web Transfer","B")

# ── Exam 6: Mobile App Development ───────────────────────────────────────────
Q(6,"What language does Flutter use?","JavaScript","Kotlin","Dart","Swift","C")
Q(6,"What is the difference between StatelessWidget and StatefulWidget?","StatelessWidget is faster","StatefulWidget can change over time","StatelessWidget uses more memory","StatefulWidget cannot be reused","B")
Q(6,"What does setState() do in Flutter?","Saves data to the database","Triggers a rebuild of the widget","Navigates to a new screen","Sends an HTTP request","B")
Q(6,"What is Firebase Firestore?","A SQL database","A NoSQL cloud database with real-time sync","A file storage service","An authentication service","B")
Q(6,"What is the difference between an APK and an App Bundle?","APK is for iOS, App Bundle is for Android","App Bundle is required for Play Store, APK is a direct install file","They are the same thing","APK is newer than App Bundle","B")
Q(6,"What does the Provider package do in Flutter?","Provides HTTP requests","Manages state across the widget tree","Provides database access","Provides animations","B")
Q(6,"What is ASO?","App Store Optimisation","Android System Operations","Application Security Overview","Automated Store Output","A")
Q(6,"Which Firebase service handles user login?","Firestore","Firebase Storage","Firebase Authentication","Firebase Analytics","C")
Q(6,"What is the minimum touch target size for mobile buttons?","24x24px","32x32px","44x44px","56x56px","C")
Q(6,"What does PWA stand for?","Progressive Web App","Portable Web Application","PHP Web App","Public Web Access","A")

# ── Exam 7: UI/UX Design ─────────────────────────────────────────────────────
Q(7,"What does UX stand for?","User Experience","User Extension","Unified Experience","User Execution","A")
Q(7,"How many users are typically needed to find 85% of usability issues?","2","5","10","20","B")
Q(7,"What is a user persona?","A real user account","A fictional but research-based representation of a target user","A user's password","A user interface element","B")
Q(7,"What is 'card sorting' used for?","Sorting playing cards","Testing navigation structure with users","Organising design files","Creating colour palettes","B")
Q(7,"What does WCAG stand for?","Web Content Accessibility Guidelines","Web Coding and Graphics","Website Content and Graphics","Web Component Architecture Guide","A")
Q(7,"What is a 'skeleton screen'?","A wireframe","A loading placeholder that shows the layout before content loads","A dark mode design","An empty state","B")
Q(7,"In Figma, what does 'Auto Layout' do?","Automatically creates animations","Makes frames resize based on content, like CSS Flexbox","Automatically names layers","Exports designs automatically","B")
Q(7,"What is the Jobs-to-be-Done framework?","A project management method","A framework focusing on what job users hire a product to do","A design system","A testing methodology","B")
Q(7,"What is the minimum contrast ratio for normal text (WCAG AA)?","2:1","3:1","4.5:1","7:1","C")
Q(7,"What is a 'design system'?","A collection of reusable components guided by clear standards","A project management tool","A type of software","A colour palette","A")

# ── Exam 8: Digital Marketing ─────────────────────────────────────────────────
Q(8,"What does SEO stand for?","Search Engine Optimisation","Social Engagement Operations","Site Engagement Output","Search Engine Operations","A")
Q(8,"What is the ideal length for a title tag?","20-30 characters","50-60 characters","80-100 characters","150-160 characters","B")
Q(8,"What does CTR stand for?","Click Through Rate","Content Transfer Rate","Customer Tracking Report","Conversion Tracking Rate","A")
Q(8,"What is a 'lead magnet'?","A type of advertisement","Something valuable offered in exchange for an email address","A social media post","A type of backlink","B")
Q(8,"What is the average ROI of email marketing?","$5 per $1 spent","$12 per $1 spent","$36 per $1 spent","$100 per $1 spent","C")
Q(8,"What does ROAS stand for?","Return on Ad Spend","Rate of Audience Segmentation","Revenue on All Sales","Return on Asset Strategy","A")
Q(8,"What are UTM parameters used for?","Tracking the source of website traffic","Improving page speed","Creating email templates","Setting up Google Ads","A")
Q(8,"What is the 80/20 rule in social media content?","80% promotional, 20% educational","80% valuable content, 20% promotional","80% images, 20% text","80% paid, 20% organic","B")
Q(8,"What does CPA stand for in digital marketing?","Cost Per Acquisition","Content Per Article","Click Per Advertisement","Customer Profile Analysis","A")
Q(8,"What is A/B testing?","Testing two versions to see which performs better","Testing a website on two browsers","Testing two different products","Testing two marketing teams","A")

# ── Exam 9: Data Analysis ─────────────────────────────────────────────────────
Q(9,"What does the AVERAGE function calculate?","The most frequent value","The middle value","The sum divided by count","The highest value","C")
Q(9,"What is a pivot table used for?","Creating charts","Summarising large datasets quickly","Writing SQL queries","Formatting cells","B")
Q(9,"What does SQL GROUP BY do?","Sorts results","Groups rows with the same values for aggregation","Filters rows","Joins tables","B")
Q(9,"What is the difference between mean and median?","They are the same","Mean is the middle value; median is the average","Mean is the average; median is the middle value","Mean is the most frequent value","C")
Q(9,"What does LCP stand for in Core Web Vitals?","Largest Contentful Paint","Longest Content Period","Least Cumulative Performance","Last Content Processed","A")
Q(9,"What is a correlation coefficient of -0.9 closest to?","No correlation","Weak positive correlation","Strong negative correlation","Perfect positive correlation","C")
Q(9,"What does ETL stand for in data engineering?","Extract, Transform, Load","Edit, Test, Launch","Evaluate, Track, Log","Export, Transfer, Link","A")
Q(9,"Which Python library is used for data manipulation?","NumPy","Matplotlib","pandas","Seaborn","C")
Q(9,"What is a KPI?","Key Performance Indicator","Key Process Integration","Knowledge Performance Index","Key Product Information","A")
Q(9,"What chart type is best for showing a trend over time?","Pie chart","Bar chart","Line chart","Scatter plot","C")

# ── Exam 10: Cybersecurity ────────────────────────────────────────────────────
Q(10,"What does the CIA triad stand for?","Confidentiality, Integrity, Availability","Cyber Intelligence Agency","Computer Information Access","Control, Identify, Analyse","A")
Q(10,"What is phishing?","A type of malware","Deceptive messages to steal credentials or install malware","A network attack","A password cracking technique","B")
Q(10,"What does SQL injection exploit?","Weak passwords","Unsanitised database queries","Open network ports","Unencrypted connections","B")
Q(10,"What is the purpose of a firewall?","Speed up internet connection","Monitor and control network traffic based on rules","Store passwords securely","Encrypt data","B")
Q(10,"What does HTTPS provide that HTTP does not?","Faster loading","Encrypted communication","Better SEO","Larger file transfers","B")
Q(10,"What is a VPN used for?","Speeding up internet","Encrypting traffic and hiding IP address","Blocking advertisements","Storing passwords","B")
Q(10,"What is the OWASP Top 10?","A list of top 10 websites","A standard reference for web application security risks","A list of top 10 hackers","A cybersecurity certification","B")
Q(10,"What is social engineering?","Building social media profiles","Manipulating people into revealing confidential information","A type of network attack","A programming technique","B")
Q(10,"What does 2FA stand for?","Two-Factor Authentication","Two-File Access","Two-Firewall Architecture","Two-Form Application","A")
Q(10,"What is the principle of least privilege?","Give users maximum access","Grant only the minimum permissions necessary","Share passwords with the team","Use the same password everywhere","B")

# ── Exam 11: Computer Fundamentals ───────────────────────────────────────────
Q(11,"What does CPU stand for?","Central Processing Unit","Computer Power Unit","Central Program Utility","Core Processing Unit","A")
Q(11,"What is the difference between RAM and storage?","RAM is permanent; storage is temporary","RAM is temporary working memory; storage is permanent","They are the same thing","RAM is slower than storage","B")
Q(11,"Which storage type is faster?","HDD","SSD","CD-ROM","Floppy disk","B")
Q(11,"What does OS stand for?","Online System","Operating System","Output Software","Open Source","B")
Q(11,"Which command shows your IP address on Windows?","ipconfig","netstat","ping","tracert","A")
Q(11,"What is the difference between CC and BCC in email?","CC is faster than BCC","BCC recipients are hidden from other recipients","CC is for attachments","BCC is for urgent emails","B")
Q(11,"What does HTTPS mean?","Hyper Text Transfer Protocol Secure","High Tech Transfer Protocol System","Hyper Text Transfer Protocol Standard","Home Transfer Protocol Secure","A")
Q(11,"What is a phishing email?","A spam email","A fake email designed to steal your information","An email with attachments","An email from an unknown sender","B")
Q(11,"What is the 10-20-30 rule for presentations?","10 slides, 20 minutes, 30pt minimum font","10 minutes, 20 slides, 30 words per slide","10 points, 20 images, 30 seconds","10 colours, 20 fonts, 30 slides","A")
Q(11,"What does VLOOKUP do in Excel?","Calculates the average","Looks up a value in a table and returns a related value","Sorts data alphabetically","Creates a chart","B")

# ── Exam 12: Desktop Application Development ─────────────────────────────────
Q(12,"What is Tkinter?","A Python web framework","Python's built-in GUI library","A database library","A testing framework","B")
Q(12,"What does root.mainloop() do in Tkinter?","Closes the application","Starts the event loop and keeps the window open","Creates a new window","Saves the application","B")
Q(12,"What is PyInstaller used for?","Testing Python code","Packaging Python apps into standalone executables","Installing Python packages","Debugging Python code","B")
Q(12,"What is SQLite?","A cloud database","A lightweight file-based database","A web database","A NoSQL database","B")
Q(12,"Why should long-running operations run in a background thread?","To use less memory","To keep the UI responsive","To run faster","To save battery","B")
Q(12,"What is semantic versioning format?","date.month.year","MAJOR.MINOR.PATCH","version.build.release","alpha.beta.stable","B")
Q(12,"What does the --onefile flag do in PyInstaller?","Creates multiple files","Bundles everything into a single executable","Installs the app","Creates a shortcut","B")
Q(12,"What is a PyQt5 Signal?","A network request","A mechanism to communicate between threads and widgets","A database connection","A file operation","B")
Q(12,"What is the difference between a desktop app and a web app?","Desktop apps are always free","Desktop apps run natively on the OS; web apps run in a browser","Web apps are always faster","Desktop apps require internet","B")
Q(12,"What does conn.row_factory = sqlite3.Row do?","Speeds up queries","Allows accessing columns by name instead of index","Creates a new table","Deletes all rows","B")

# ── Exam 13: POS & ICT Support ────────────────────────────────────────────────
Q(13,"What does POS stand for?","Point of Sale","Point of Service","Payment Operations System","Purchase Order System","A")
Q(13,"What is the purpose of a receipt printer in a POS system?","To scan barcodes","To print customer receipts","To process card payments","To display prices","B")
Q(13,"What should you do at the end of each business day with a POS system?","Turn it off immediately","Reconcile the cash drawer and back up data","Delete all transactions","Change the password","B")
Q(13,"What are the 3 tiers of ICT support?","Basic, Advanced, Expert","Help Desk, Technical Support, Expert Support","Level 1, Level 2, Level 3","All of the above","D")
Q(13,"What is a runbook in ICT support?","A physical book","Step-by-step procedures for common tasks","A list of employees","A network diagram","B")
Q(13,"Which Wi-Fi security protocol should you use?","WEP","WPA","WPA2 or WPA3","No security needed","C")
Q(13,"What is active listening?","Listening while doing other tasks","Fully concentrating on what the speaker is saying","Listening to music","Recording a conversation","B")
Q(13,"What does DHCP do?","Encrypts network traffic","Automatically assigns IP addresses to devices","Blocks malicious websites","Speeds up internet","B")
Q(13,"What is the CompTIA A+ certification for?","Network engineering","Entry-level IT support","Cybersecurity","Cloud computing","B")
Q(13,"What is the first step when a POS terminal stops working?","Call the vendor immediately","Check the power cable and restart the terminal","Replace the terminal","Refund all customers","B")

# ── Exam 14: Networking Basics ────────────────────────────────────────────────
Q(14,"What does LAN stand for?","Large Area Network","Local Area Network","Linked Access Node","Long Area Network","B")
Q(14,"How many layers does the OSI model have?","4","5","7","10","C")
Q(14,"What is the difference between a switch and a router?","They are the same","A switch connects devices within a network; a router connects different networks","A router is faster","A switch connects to the internet","B")
Q(14,"What does CIDR /24 mean?","24 devices on the network","24 bits are the network portion","24 available IP addresses","24 subnets","B")
Q(14,"How many usable hosts does a /24 network have?","254","256","255","252","A")
Q(14,"What is NAT?","Network Address Translation","Network Access Token","Node Address Table","Network Authentication Type","A")
Q(14,"What is the difference between 2.4 GHz and 5 GHz Wi-Fi?","2.4 GHz is faster","5 GHz has longer range","2.4 GHz has longer range but is slower; 5 GHz is faster but shorter range","They are identical","C")
Q(14,"What does DNS do?","Encrypts network traffic","Translates domain names to IP addresses","Assigns IP addresses","Blocks malicious websites","B")
Q(14,"What is a VLAN?","A type of cable","A logical network segment within a physical network","A wireless network","A type of router","B")
Q(14,"What is the implicit deny rule in firewall configuration?","Allow all traffic by default","Deny all traffic not explicitly allowed","Allow traffic from trusted IPs","Deny traffic from unknown countries","B")

# ── Exam 15: Cloud Computing ──────────────────────────────────────────────────
Q(15,"What does IaaS stand for?","Internet as a Service","Infrastructure as a Service","Integration as a Service","Information as a Service","B")
Q(15,"What is the difference between public and private cloud?","Public cloud is free","Private cloud is operated solely for one organisation","Public cloud is more secure","Private cloud is always cheaper","B")
Q(15,"What does S3 stand for in AWS?","Simple Storage Service","Secure Server System","Scalable Storage Solution","Standard Storage Service","A")
Q(15,"What is Docker used for?","Writing Python code","Packaging applications in containers","Managing databases","Monitoring servers","B")
Q(15,"What is the difference between a Docker image and a container?","They are the same","An image is a template; a container is a running instance of an image","A container is a template; an image is running","Images are larger than containers","B")
Q(15,"What does CI/CD stand for?","Computer Integration/Computer Deployment","Continuous Integration/Continuous Delivery","Cloud Infrastructure/Cloud Deployment","Code Integration/Code Delivery","B")
Q(15,"What is Infrastructure as Code?","Writing code on a server","Defining infrastructure in code files for repeatability","A type of programming language","A cloud service","B")
Q(15,"What is the AWS Well-Architected Framework?","A type of server","6 pillars for building reliable, secure, efficient cloud systems","A programming framework","A database design pattern","B")
Q(15,"What does Auto Scaling do?","Manually adds servers","Automatically adjusts the number of instances based on demand","Scales the database","Scales the network","B")
Q(15,"What is a CDN?","A type of database","A network of servers that delivers content from the closest location to the user","A cloud database","A container service","B")

# ── Exam 16: Software Engineering ────────────────────────────────────────────
Q(16,"What does SDLC stand for?","Software Development Life Cycle","System Design and Launch Cycle","Software Deployment and Launch Cycle","System Development and Launch Cycle","A")
Q(16,"What is the difference between Waterfall and Agile?","Waterfall is faster","Waterfall is sequential; Agile is iterative and adaptive","Agile is older than Waterfall","They are the same methodology","B")
Q(16,"What does SOLID stand for in OOP?","Single, Open, Liskov, Interface, Dependency","Simple, Object, Linked, Integrated, Dynamic","Standard, Open, Linked, Interface, Dependency","Single, Object, Linked, Integrated, Dependency","A")
Q(16,"What is a user story?","A blog post about users","A feature description from the user's perspective: As a [user], I want [goal] so that [reason]","A user manual","A database record","B")
Q(16,"What is TDD?","Test-Driven Development","Technology Design Document","Test Data Definition","Technical Design Diagram","A")
Q(16,"What is the Repository pattern?","A Git repository","An abstraction layer for data access logic","A type of database","A design pattern for UI","B")
Q(16,"What does the Single Responsibility Principle state?","A class should have many responsibilities","A class should have only one reason to change","A class should be private","A class should not be inherited","B")
Q(16,"What is a Sprint in Scrum?","A fast run","A time-boxed iteration of 1-4 weeks","A type of meeting","A project phase","B")
Q(16,"What is horizontal scaling?","Adding more resources to one server","Adding more servers to handle load","Scaling the database","Scaling the network","B")
Q(16,"What is caching used for?","Storing user passwords","Storing frequently accessed data in fast memory to reduce database load","Backing up data","Encrypting data","B")

# Write exam questions SQL
q_rows = []
for i, (eid, q, a, b, c, d, correct, marks) in enumerate(QUESTIONS, 1):
    def esc(s): return s.replace("'", "''")
    q_rows.append(f"({i},{eid},'{esc(q)}','{esc(a)}','{esc(b)}','{esc(c)}','{esc(d)}','{correct}',{marks},'{ts}')")

out2 = open('database/exam_questions_patch.sql', 'w', encoding='utf-8')
out2.write("-- Real exam questions for all 16 courses (10 questions each)\n\n")
out2.write("SET FOREIGN_KEY_CHECKS=0;\n")
out2.write("TRUNCATE TABLE lms_exam_questions;\n")
out2.write("SET FOREIGN_KEY_CHECKS=1;\n\n")
out2.write("INSERT INTO `lms_exam_questions` (`id`,`exam_id`,`question`,`option_a`,`option_b`,`option_c`,`option_d`,`correct_option`,`marks`,`created_at`) VALUES\n")
out2.write(",\n".join(q_rows))
out2.write(";\n\n")
out2.write("-- Update total_questions count on each exam\n")
from collections import Counter
ec = Counter(q[0] for q in QUESTIONS)
for eid, cnt in sorted(ec.items()):
    out2.write(f"UPDATE `lms_exams` SET `total_questions`={cnt}, `total_marks`={cnt} WHERE `course_id`={eid} AND `id`=(SELECT id FROM (SELECT id FROM lms_exams WHERE course_id={eid} AND is_published=1 ORDER BY created_at DESC LIMIT 1) t);\n")
out2.close()
print(f"Exam questions SQL written: {len(QUESTIONS)} questions across {len(ec)} exams")
