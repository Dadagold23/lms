<?php
require_once dirname(__DIR__)."/config/db.php";
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$ts = "2026-04-15 08:00:00";
$lessons = $pdo->query("SELECT id,course_id,sort_order FROM lms_lessons ORDER BY course_id,sort_order,id")->fetchAll(PDO::FETCH_ASSOC);
$by = [];
foreach($lessons as $l) $by[$l["course_id"]][] = $l["id"];
$aS = $pdo->prepare("INSERT IGNORE INTO lms_lesson_assessments (lesson_id,course_id,type,title,instructions,pass_score,is_required,created_at) VALUES (?,?,?,?,?,60,1,?)");
$qS = $pdo->prepare("INSERT IGNORE INTO lms_assessment_questions (assessment_id,question,option_a,option_b,option_c,option_d,correct_option,marks,sort_order) VALUES (?,?,?,?,?,?,?,1,?)");
$ta=0; $tq=0;
function ins(PDO $p, PDOStatement $a, PDOStatement $q, int $lid, int $cid, string $type, string $title, string $inst, array $qs, string $ts, int &$ta, int &$tq): void {
    $a->execute([$lid,$cid,$type,$title,$inst,$ts]);
    $aid=(int)$p->lastInsertId();
    if($aid===0) return;
    foreach($qs as $i=>$x){ $q->execute([$aid,$x[0],$x[1],$x[2],$x[3]??null,$x[4]??null,$x[5],$i+1]); $tq++; }
    $ta++;
}

// Course 2: Advanced Graphic Design
if(isset($by[2]) && count($by[2])>=6){
    $ls=$by[2];
    ins($pdo,$aS,$qS,$ls[0],2,"test","Lesson 1 Test: Advanced Typography","Test your advanced typography knowledge.",
        [["What is a type scale?","A ruler for measuring fonts","A defined set of font sizes used consistently in a design system","A font family","A typographic error","B"],
         ["What is the difference between tracking and kerning?","They are the same","Tracking adjusts uniform spacing between all characters; kerning adjusts space between specific pairs","Kerning is for headings only","Tracking is for body text only","B"],
         ["What is a variable font?","A font that changes colour","A font containing multiple styles in a single file, reducing file size","A font used only for headings","A decorative font","B"],
         ["What is leading in typography?","Space between characters","Vertical space between lines of text","Font weight","Letter height","B"],
         ["What does a baseline grid do?","Aligns images","Creates consistent vertical rhythm so all text aligns across columns","Defines column widths","Sets font sizes","B"]],$ts,$ta,$tq);
    ins($pdo,$aS,$qS,$ls[1],2,"test","Lesson 2 Test: Colour Systems","Test your knowledge of advanced colour systems.",
        [["What is the WCAG AA contrast ratio for normal text?","2:1","3:1","4.5:1","7:1","C"],
         ["What are colour tokens?","Paint colours","Named variables representing colours in a design system","Colour swatches","Printer inks","B"],
         ["Why is dark mode not simply inverting colours?","It is too complex","Inverting creates harsh contrasts; dark mode needs reduced saturation and maintained contrast ratios","It requires special software","Browsers do not support it","B"],
         ["What is a semantic colour?","A colour that looks good","A colour with functional meaning: success=green, error=red, warning=amber","A brand colour","A colour for text only","B"],
         ["What is a diverging colour palette used for?","Categorical data","Data with a meaningful midpoint such as temperature above and below zero","Sequential data","Random data","B"]],$ts,$ta,$tq);
    ins($pdo,$aS,$qS,$ls[2],2,"practical","Lesson 3 Practical: Editorial Layout","Design a magazine cover and 2-page spread using a modular grid. Apply the rule of thirds for the cover image. Use the F-pattern for the article layout. Submit at A4 size.",
        [["Why is the 12-column grid so widely used?","It looks better","12 is divisible by 2, 3, 4, and 6 allowing many layout configurations","It is the oldest grid","Bootstrap requires it","B"],
         ["What is the Z-pattern in layout?","A font style","How the eye moves in a Z shape on text-heavy pages","A grid system","A colour scheme","B"],
         ["What is the Golden Ratio in design?","A colour formula","1:1.618 proportion that creates naturally pleasing layouts","A font size ratio","A grid measurement","B"],
         ["What is a modular grid?","A grid with only columns","A grid with both rows and columns creating a matrix of modules for complex layouts","A grid for mobile only","A grid for images only","B"],
         ["What does the F-pattern describe?","A font category","How users scan web content: horizontally then vertically down the left side","A layout for print","A colour arrangement","B"]],$ts,$ta,$tq);
    ins($pdo,$aS,$qS,$ls[3],2,"test","Lesson 4 Test: Motion Graphics","Test your knowledge of animation principles.",
        [["What is easing in animation?","The speed of the entire animation","Controlling acceleration and deceleration to make movement feel natural","The direction of movement","The colour of the animation","B"],
         ["Which easing is most natural for UI animations?","Linear","Ease In","Ease Out: starts fast, ends slow","Ease In-Out","C"],
         ["What is anticipation in animation?","The final pose","A small movement before the main action that prepares the viewer","The background","The colour change","B"],
         ["What does squash and stretch give to animated objects?","Colour","A sense of weight and flexibility","Speed","Direction","B"],
         ["What is secondary action in animation?","Replacing the main action","Supporting actions that add richness and realism to the main action","Slowing down the animation","Adding colour","B"]],$ts,$ta,$tq);
    ins($pdo,$aS,$qS,$ls[4],2,"assignment","Lesson 5 Assignment: Brand Strategy","Conduct a brand audit for a real or fictional company. Analyse their current brand identity, positioning, and consistency. Write a 1-page brand strategy recommendation with a positioning statement.",
        [["What is the difference between brand strategy and brand identity?","They are the same","Strategy is the long-term plan; identity is the visual and verbal expression of that strategy","Identity comes before strategy","Strategy is only for large companies","B"],
         ["What are the 3 types of brand architecture?","Logo, Colour, Font","Monolithic (Branded House), Endorsed, Pluralistic (House of Brands)","Primary, Secondary, Tertiary","National, Regional, Local","B"],
         ["What is a brand positioning statement?","A tagline","A statement defining where the brand sits in the market relative to competitors for a specific audience","A mission statement","A product description","B"],
         ["When should a company rebrand?","Every year","When there is a merger, significant audience shift, outdated identity, or reputation issue","Never","When they change prices","B"],
         ["What is brand equity?","The monetary value of a brand","The value a brand adds beyond the functional product, built through recognition and trust","The cost of branding","The number of brand colours","B"]],$ts,$ta,$tq);
    ins($pdo,$aS,$qS,$ls[5],2,"practical","Lesson 6 Practical: Freelance Proposal","Write a project proposal and contract for a fictional logo design project. Include: scope of work, timeline, payment terms, revision rounds, kill fee clause, and IP ownership. Submit as a PDF.",
        [["What is professional indemnity insurance?","Health insurance","Insurance that protects against client claims if your work causes financial loss","Life insurance","Equipment insurance","B"],
         ["What must a client contract include?","Only the price","Scope of work, timeline, payment terms, revision rounds, kill fee, and IP ownership","Only the deadline","Only the client name","B"],
         ["What is the difference between project-based and value-based pricing?","Project-based is always more expensive","Project-based charges a fixed fee for defined scope; value-based charges based on the impact delivered","Value-based is for beginners","Project-based is for large companies only","B"],
         ["What is a kill fee?","A fee for completing a project","A fee paid to the designer if the client cancels the project mid-way","A fee for extra revisions","A fee for rush delivery","A"],
         ["Why should you never start work without a deposit?","It is illegal","It protects you if the client cancels or disappears before paying","It is industry standard","Clients expect it","B"]],$ts,$ta,$tq);
}

// Course 5: PHP & MySQL
if(isset($by[5]) && count($by[5])>=6){
    $ls=$by[5];
    ins($pdo,$aS,$qS,$ls[0],5,"test","Lesson 1 Test: PHP Syntax","Test your PHP fundamentals.",
        [["What does OOP stand for?","Object Oriented Programming","Open Output Processing","Ordered Object Protocol","Optional Output Parameters","A"],
         ["What keyword creates an object from a class in PHP?","create","make","new","build","C"],
         ["What is the difference between public and private in PHP?","Public is faster","Private members can only be accessed within the class; public can be accessed anywhere","Public cannot be inherited","Private methods are static","B"],
         ["What does PDO stand for?","PHP Data Objects","PHP Database Operations","Prepared Data Output","PHP Dynamic Objects","A"],
         ["What does password_hash() do?","Encrypts reversibly","Creates a secure one-way bcrypt hash of the password","Stores in a cookie","Sends by email","B"]],$ts,$ta,$tq);
    ins($pdo,$aS,$qS,$ls[1],5,"practical","Lesson 2 Practical: OOP Library System","Build a library management system using OOP. Classes: Book, Member, Library. Methods: addBook(), registerMember(), borrowBook(), returnBook(). Submit your PHP file.",
        [["What is the difference between an interface and an abstract class?","They are the same","An interface defines a contract with no implementation; an abstract class can have partial implementation","Abstract classes are faster","Interfaces are for databases only","B"],
         ["What does this refer to inside a class method?","The parent class","The current object instance","The database connection","The session","B"],
         ["What is inheritance in OOP?","Copying code","A class extending another class to reuse and extend its functionality","A type of loop","A database relationship","B"],
         ["What is encapsulation?","Hiding the database","Bundling data and methods together and restricting direct access to internal state","A type of inheritance","A design pattern","B"],
         ["What is a constructor in PHP?","A function that destroys objects","A special method called automatically when an object is created","A static method","A database query","B"]],$ts,$ta,$tq);
    ins($pdo,$aS,$qS,$ls[2],5,"test","Lesson 3 Test: Advanced MySQL","Test your advanced MySQL knowledge.",
        [["What is the difference between WHERE and HAVING?","They are the same","WHERE filters rows before grouping; HAVING filters groups after GROUP BY","HAVING is faster","WHERE is for joins only","B"],
         ["What does a LEFT JOIN return?","Only matching rows","All rows from the left table plus matching rows from the right (NULLs for no match)","All rows from both tables","Only non-matching rows","B"],
         ["What is a database transaction?","A payment record","A group of SQL operations that succeed or fail together as a unit","A database backup","A stored procedure","B"],
         ["What is an index in MySQL?","A list of all tables","A data structure that speeds up queries on frequently searched columns","A type of foreign key","A backup","B"],
         ["What does EXPLAIN do in MySQL?","Deletes a query","Shows how MySQL executes a query including whether indexes are used","Explains the database structure","Shows all tables","B"]],$ts,$ta,$tq);
    ins($pdo,$aS,$qS,$ls[3],5,"test","Lesson 4 Test: Security","Test your PHP security knowledge.",
        [["What is SQL injection?","A type of virus","An attack that inserts malicious SQL code into unsanitised database queries","A network attack","A password attack","B"],
         ["What is XSS?","A CSS framework","Cross-Site Scripting: injecting malicious scripts into web pages viewed by other users","A type of database","A PHP function","B"],
         ["What is CSRF?","A CSS rule","Cross-Site Request Forgery: tricking a user into submitting a malicious request","A PHP error","A database error","B"],
         ["How do prepared statements prevent SQL injection?","They are faster","They separate SQL code from user data so malicious input cannot alter the query structure","They encrypt the data","They validate the input","B"],
         ["What does htmlspecialchars() do?","Removes HTML","Converts special characters to HTML entities to prevent XSS attacks","Adds HTML formatting","Validates HTML","B"]],$ts,$ta,$tq);
    ins($pdo,$aS,$qS,$ls[4],5,"practical","Lesson 5 Practical: REST API","Build a complete REST API for a course catalogue. Endpoints: GET /api/courses, GET /api/courses/{id}, POST /api/courses, PUT /api/courses/{id}, DELETE /api/courses/{id}. Test all endpoints in Postman. Submit code and screenshots.",
        [["What are the 4 main HTTP methods?","GET/POST/PUT/DELETE: all retrieve data","GET retrieves, POST creates, PUT/PATCH updates, DELETE removes","GET and POST are the only ones","PUT is for images only","B"],
         ["What HTTP status code should a successful POST return?","200 OK","201 Created","400 Bad Request","404 Not Found","B"],
         ["What does JWT stand for?","Java Web Token","JSON Web Token: a standard for securely transmitting information between parties","JavaScript Web Transfer","JSON Web Transfer","B"],
         ["What is the difference between PUT and PATCH?","They are the same","PUT replaces the entire resource; PATCH updates only specified fields","PATCH is newer","PUT is for files only","B"],
         ["What HTTP status code means Unauthorized?","200","201","401","404","C"]],$ts,$ta,$tq);
    ins($pdo,$aS,$qS,$ls[5],5,"assignment","Lesson 6 Assignment: E-Commerce App","Build a complete e-commerce store with: product catalogue, shopping cart, checkout, user auth, and admin panel. Deploy to XAMPP. Submit screenshots of all features.",
        [["What is the difference between authentication and authorisation?","They are the same","Authentication verifies who you are; authorisation determines what you can do","Authentication is for admins only","Authorisation happens before authentication","B"],
         ["What is a session in PHP?","A database connection","A way to store user data across multiple pages such as login state","A type of cookie","A server configuration","B"],
         ["What is the purpose of a CSRF token?","To speed up forms","To prevent Cross-Site Request Forgery by verifying that form submissions come from your own site","To validate email addresses","To encrypt passwords","B"],
         ["What is the difference between include and require in PHP?","They are the same","require causes a fatal error if the file is not found; include only gives a warning","include is faster","require is for classes only","B"],
         ["What does session_regenerate_id() do after login?","Logs the user out","Creates a new session ID to prevent session fixation attacks","Saves the session","Deletes old sessions","B"]],$ts,$ta,$tq);
}

// Courses 6-9
foreach([
    [6,"Mobile App Development",[
        ["test","Flutter Basics Test","Test your Flutter fundamentals.",
            [["What language does Flutter use?","JavaScript","Kotlin","Dart","Swift","C"],
             ["What is the difference between StatelessWidget and StatefulWidget?","StatelessWidget is faster","StatefulWidget can change over time; StatelessWidget cannot","StatelessWidget uses more memory","StatefulWidget cannot be reused","B"],
             ["What does setState() do?","Saves data","Triggers a widget rebuild with updated state","Navigates to a new screen","Sends an HTTP request","B"],
             ["What is the Provider package for?","HTTP requests","State management across the widget tree","Database access","Animations","B"],
             ["What is the difference between APK and App Bundle?","They are the same","App Bundle is required for Play Store and optimised per device; APK is a direct install file","APK is for iOS","App Bundle is older","B"]]],
        ["practical","Firebase Practical: Note App","Build a note-taking app with Firebase. Features: email/password auth, CRUD notes in Firestore, real-time sync. Each user sees only their own notes. Submit screenshots.",
            [["What is Firebase Firestore?","A SQL database","A NoSQL cloud database with real-time synchronisation","A file storage service","An authentication service","B"],
             ["How does authStateChanges() work?","It checks the password","It returns a stream that emits the current user whenever auth state changes","It stores the user in a cookie","It sends an email","B"],
             ["What is the difference between a Firestore collection and a document?","They are the same","A collection is a container of documents; a document is a single record with fields","A document contains collections only","A collection is a single record","B"],
             ["What does notifyListeners() do in Provider?","Saves data","Tells all listening widgets to rebuild with the updated state","Sends a notification","Logs an event","B"],
             ["What is the minimum touch target size for mobile?","24x24px","32x32px","44x44px","56x56px","C"]]]
    ]],
    [7,"UI/UX Design",[
        ["practical","UX Research Practical","Conduct 3 user interviews about a digital product. Create 2 user personas. Write 5 Jobs-to-be-Done statements. Build a sitemap for a 10-page website. Submit your research document.",
            [["What is the difference between qualitative and quantitative research?","Qualitative is faster","Qualitative understands why; quantitative measures how many","They are the same","Quantitative is more accurate","B"],
             ["What are the 5 components of a user persona?","Name, Age, Job, Salary, Location","Name/photo, demographics, goals, frustrations, behaviours","Name, Email, Password, Role, Status","Title, Description, Priority, Status, Owner","B"],
             ["What is card sorting used for?","Sorting design files","Testing how users categorise content to inform information architecture","Creating colour palettes","Organising code","B"],
             ["What is tree testing?","Testing a decision tree","Testing navigation structure with users using a text-only hierarchy without visual design","Testing a database","Testing code performance","B"],
             ["What is the Jobs-to-be-Done framework?","A project management tool","A framework focusing on what job users hire a product to do: When [situation] I want [motivation] so I can [outcome]","A design system","A testing methodology","B"]]],
        ["test","Design Systems Test","Test your knowledge of design systems and accessibility.",
            [["What is the WCAG AA contrast ratio for normal text?","2:1","3:1","4.5:1","7:1","C"],
             ["What is the difference between atoms, molecules, and organisms?","Size categories","Atoms are basic elements; molecules combine atoms; organisms combine molecules into complex components","Colour categories","Animation levels","B"],
             ["What are component variants in Figma?","Different file versions","Multiple states and styles of a component such as button primary/secondary/ghost and default/hover/disabled","Different pages","Different fonts","B"],
             ["What is the minimum touch target size for mobile?","24x24px","32x32px","44x44px","56x56px","C"],
             ["What does WCAG stand for?","Web Coding and Graphics","Web Content Accessibility Guidelines","Website Content and Graphics","Web Component Architecture Guide","B"]]]
    ]],
    [8,"Digital Marketing",[
        ["test","SEO Test","Test your SEO and digital marketing knowledge.",
            [["What is the ideal length for a title tag?","20-30 characters","50-60 characters","80-100 characters","150-160 characters","B"],
             ["What is the difference between on-page and off-page SEO?","On-page is more important","On-page optimises elements on your website; off-page builds authority through backlinks and mentions","Off-page is free","On-page is only for images","B"],
             ["What is a long-tail keyword?","A very long keyword","A specific lower-competition keyword phrase with higher purchase intent","A keyword with many results","A keyword used in headings only","B"],
             ["What does CTR stand for?","Click Through Rate: clicks divided by impressions times 100","Content Transfer Rate","Customer Tracking Report","Conversion Tracking Rate","A"],
             ["What is the 80/20 rule in social media content?","Post 80 times per month","80% valuable content and 20% promotional content","80% images and 20% text","80% paid and 20% organic","B"]]],
        ["practical","Email Campaign Practical","Create a 5-email welcome sequence for a fictional online course platform. Write subject lines, preview text, and body copy for each email. Set up the automation flow in Mailchimp. Submit screenshots.",
            [["What is a lead magnet?","A type of advertisement","Something valuable offered in exchange for an email address such as a free guide or checklist","A social media post","A type of backlink","B"],
             ["What is an email drip campaign?","A leaking email server","An automated sequence of emails sent over time based on user behaviour or schedule","A bulk email blast","A single promotional email","B"],
             ["What is A/B testing in email marketing?","Testing two email servers","Sending two versions of an email to different segments to see which performs better","Testing email deliverability","Testing email design on two browsers","B"],
             ["Why should you never buy email lists?","It is expensive","It damages deliverability, violates anti-spam laws, and the contacts have not consented","It is too slow","Email lists are free anyway","B"],
             ["What is the purpose of a UTM parameter?","To speed up links","To track the source, medium, and campaign of website traffic in analytics","To shorten URLs","To encrypt links","B"]]]
    ]],
    [9,"Data Analysis",[
        ["practical","EDA Practical","Download a Nigerian dataset from Kaggle. Load into pandas. Perform: data cleaning, descriptive statistics, correlation analysis, 3 visualisations. Write a 1-paragraph insight summary. Submit your Jupyter Notebook.",
            [["What is the difference between a NumPy array and a pandas DataFrame?","They are the same","NumPy arrays are homogeneous numerical arrays; DataFrames are tabular with mixed types and labels","DataFrames are faster","NumPy is for visualisation","B"],
             ["How do you handle missing values in pandas?","Delete the entire dataset","Use dropna() to remove rows or fillna() to impute with statistics","Ignore them","Replace with zeros always","B"],
             ["What chart type shows correlation between two variables?","Bar chart","Pie chart","Scatter plot","Line chart","C"],
             ["What does df.describe() return?","The first 5 rows","Statistical summary: count, mean, std, min, quartiles, max for numeric columns","The column names","The data types","B"],
             ["What is the difference between df.loc and df.iloc?","They are the same","loc selects by label/name; iloc selects by integer position","iloc is faster","loc is for rows only","B"]]],
        ["test","Statistics Test","Test your statistical analysis knowledge.",
            [["When should you use median instead of mean?","Always","When the data has outliers or is skewed, as median is more robust","When the data is normally distributed","When you have categorical data","B"],
             ["What does a p-value of 0.03 mean?","The result is 3% accurate","There is a 3% probability of observing results this extreme if the null hypothesis is true","The effect size is 3%","The sample size is 3","B"],
             ["What is the difference between correlation and causation?","They are the same","Correlation shows two variables move together; causation means one directly causes the other","Causation is stronger","Correlation is always positive","B"],
             ["What is a normal distribution?","A distribution with no outliers","A symmetric bell-shaped distribution where 68% of data falls within 1 standard deviation of the mean","A distribution with equal values","A distribution for categorical data","B"],
             ["What is the IQR?","Max minus Min","Q3 minus Q1: the range of the middle 50% of data, robust to outliers","Mean minus Median","Standard deviation squared","B"]]]
    ]]
] as [$cid,$cname,$assessments]){
    if(!isset($by[$cid]) || count($by[$cid])<count($assessments)) continue;
    $ls=$by[$cid];
    foreach($assessments as $i=>[$type,$title,$inst,$qs])
        ins($pdo,$aS,$qS,$ls[$i],$cid,$type,$title,$inst,$qs,$ts,$ta,$tq);
}

// Courses 10-16
foreach([
    [10,"Cybersecurity",[
        ["test","Cybersecurity Fundamentals Test","Test your cybersecurity knowledge.",
            [["What does the CIA triad stand for?","Confidentiality, Integrity, Availability","Cyber Intelligence Agency","Computer Information Access","Control, Identify, Analyse","A"],
             ["What is phishing?","A type of malware","Deceptive messages designed to steal credentials or install malware","A network attack","A password cracking technique","B"],
             ["What does SQL injection exploit?","Weak passwords","Unsanitised database queries that allow attackers to manipulate SQL commands","Open network ports","Unencrypted connections","B"],
             ["What is the purpose of a firewall?","Speed up internet","Monitor and control network traffic based on predefined security rules","Store passwords","Encrypt data","B"],
             ["What is the principle of least privilege?","Give users maximum access","Grant only the minimum permissions necessary for a user to perform their job","Share passwords with the team","Use the same password everywhere","B"]]],
        ["practical","Network Security Practical","Install Wireshark. Capture 5 minutes of network traffic. Identify: top 5 protocols, any unencrypted HTTP traffic, DNS queries, and most active IP addresses. Write a brief security assessment report.",
            [["What is the difference between TCP and UDP?","TCP is faster","TCP is reliable and connection-oriented; UDP is fast and connectionless","UDP is more secure","They are the same","B"],
             ["What does a VPN do?","Speeds up internet","Encrypts all traffic between your device and a VPN server, hiding your IP and protecting data","Blocks advertisements","Stores passwords","B"],
             ["What is network segmentation?","Speeding up the network","Dividing a network into zones to limit the spread of attacks if one zone is compromised","Adding more routers","Encrypting the network","B"],
             ["What is the difference between IDS and IPS?","They are the same","IDS detects and alerts on suspicious activity; IPS detects and actively blocks it","IPS is older","IDS is more expensive","B"],
             ["What is ARP poisoning?","A food contamination issue","An attack that redirects network traffic through the attacker machine by sending fake ARP messages","A type of malware","A firewall bypass","B"]]]
    ]],
    [11,"Computer Fundamentals",[
        ["test","Computer Fundamentals Test","Test your computer basics knowledge.",
            [["What is the difference between RAM and storage?","RAM is permanent; storage is temporary","RAM is temporary working memory; storage is permanent data storage","They are the same","RAM is slower than storage","B"],
             ["Which storage type is faster?","HDD","SSD","CD-ROM","Floppy disk","B"],
             ["What does the CPU do?","Stores data permanently","Executes instructions and processes data: the brain of the computer","Displays output","Connects to the internet","B"],
             ["What command shows your IP address on Windows?","netstat","ping","ipconfig","tracert","C"],
             ["What is the difference between CC and BCC in email?","CC is faster","CC recipients are visible to all; BCC recipients are hidden from other recipients","CC is for attachments","BCC is for urgent emails","B"]]],
        ["practical","Office Productivity Practical","Create: (1) A professional CV in Word using styles. (2) A budget spreadsheet in Excel with SUM/IF formulas and a chart. (3) A 10-slide presentation in PowerPoint applying the 10-20-30 rule. Submit all 3 files.",
            [["What is the 10-20-30 rule for presentations?","10 slides, 20 minutes, 30pt minimum font size","10 minutes, 20 slides, 30 words per slide","10 points, 20 images, 30 seconds","10 colours, 20 fonts, 30 slides","A"],
             ["What is the difference between a formula and a function in Excel?","They are the same","A formula is any expression starting with =; a function is a built-in named formula like SUM() or IF()","Functions are faster","Formulas are for text only","B"],
             ["Why should you use Styles in Word instead of manual formatting?","Styles look better","Styles ensure consistency and allow you to update formatting across the entire document at once","Manual formatting is slower","Styles are required by Word","B"],
             ["What does VLOOKUP do in Excel?","Calculates the average","Looks up a value in the first column of a range and returns a value from a specified column in the same row","Sorts data alphabetically","Creates a chart","B"],
             ["What is the purpose of a pivot table?","Creating charts","Summarising and analysing large datasets quickly by grouping and aggregating data","Formatting cells","Writing formulas","B"]]]
    ]],
    [12,"Desktop Application Dev",[
        ["practical","Desktop App Practical: Calculator","Build a calculator app using Tkinter with number buttons, operations, display, clear, and equals. Handle arithmetic correctly. Submit your Python file.",
            [["What is the difference between a desktop app and a web app?","Desktop apps are always free","Desktop apps run natively on the OS with full system access; web apps run in a browser","Web apps are always faster","Desktop apps require internet","B"],
             ["What does root.mainloop() do in Tkinter?","Closes the application","Starts the event loop that keeps the window open and responds to user events","Creates a new window","Saves the application","B"],
             ["What is PyInstaller used for?","Testing Python code","Packaging Python applications into standalone executables that run without Python installed","Installing Python packages","Debugging Python code","B"],
             ["Why should long-running operations run in a background thread?","To use less memory","To keep the UI responsive: blocking the main thread freezes the interface","To run faster","To save battery","B"],
             ["What is SQLite and why is it good for desktop apps?","A cloud database","A lightweight file-based database requiring no server: perfect for local desktop applications","A web database","A NoSQL database","B"]]],
        ["test","Desktop Dev Advanced Test","Test your knowledge of advanced desktop development.",
            [["What is a PyQt5 Signal?","A network request","A mechanism for communication between objects: emitted when an event occurs, connected to a slot function","A database connection","A file operation","B"],
             ["What is semantic versioning format?","date.month.year","MAJOR.MINOR.PATCH: major for breaking changes, minor for features, patch for bug fixes","version.build.release","alpha.beta.stable","B"],
             ["What does conn.row_factory = sqlite3.Row do?","Speeds up queries","Allows accessing query results by column name instead of index position","Creates a new table","Deletes all rows","B"],
             ["What is the difference between joblib and pickle?","They are the same","joblib is optimised for large NumPy arrays; pickle is general-purpose Python serialisation","pickle is faster","joblib is for databases only","B"],
             ["What does the --onefile flag in PyInstaller do?","Creates multiple files","Bundles the entire application and all dependencies into a single executable file","Installs the app","Creates a shortcut","B"]]]
    ]],
    [13,"POS & ICT Support",[
        ["test","POS Operations Test","Test your POS and ICT support knowledge.",
            [["What does POS stand for?","Point of Sale","Point of Service","Payment Operations System","Purchase Order System","A"],
             ["What should you do at end of business day with a POS system?","Turn it off immediately","Reconcile the cash drawer, back up transaction data, and review the daily sales report","Delete all transactions","Change the password","B"],
             ["What are the 3 tiers of ICT support?","Basic, Advanced, Expert","Help Desk (Tier 1), Technical Support (Tier 2), Expert/Specialist Support (Tier 3)","Level 1, Level 2, Level 3","All of the above","B"],
             ["What is a runbook in ICT support?","A physical book","Step-by-step documented procedures for handling common IT tasks and incidents","A list of employees","A network diagram","B"],
             ["What is the first step when a POS terminal stops working?","Call the vendor immediately","Check the power cable and connections, then restart the terminal","Replace the terminal","Refund all customers","B"]]],
        ["practical","ICT Support Runbook","Create an ICT support runbook for 5 common issues: Wi-Fi not connecting, printer not printing, Outlook not receiving emails, computer running slowly, user forgot password. Each must have step-by-step troubleshooting. Submit as a document.",
            [["What is active listening in customer service?","Listening while doing other tasks","Fully concentrating on what the speaker is saying, asking clarifying questions, and summarising what you heard","Recording a conversation","Listening to music","B"],
             ["Which Wi-Fi security protocol should you use?","WEP","WPA","WPA2 or WPA3","No security needed","C"],
             ["What does DHCP do on a network?","Encrypts network traffic","Automatically assigns IP addresses to devices on the network","Blocks malicious websites","Speeds up internet","B"],
             ["What is the CompTIA A+ certification for?","Network engineering","Entry-level IT support covering hardware, software, networking, and troubleshooting","Cybersecurity","Cloud computing","B"],
             ["What is a ticketing system in IT support?","A bus ticket system","Software used to track, manage, and resolve IT support requests and incidents","A project management tool","A billing system","B"]]]
    ]],
    [14,"Networking Basics",[
        ["test","Networking Fundamentals Test","Test your networking knowledge.",
            [["What does LAN stand for?","Large Area Network","Local Area Network: a network covering a small area like a home or office","Linked Access Node","Long Area Network","B"],
             ["How many layers does the OSI model have?","4","5","7","10","C"],
             ["What is the difference between a switch and a router?","They are the same","A switch connects devices within the same network; a router connects different networks together","A router is faster","A switch connects to the internet","B"],
             ["What does CIDR /24 mean?","24 devices on the network","24 bits are the network portion leaving 8 bits for hosts (254 usable hosts)","24 available IP addresses","24 subnets","B"],
             ["What is NAT?","A type of cable","A technique that allows multiple devices with private IPs to share a single public IP address","A network protocol","A firewall type","B"]]],
        ["practical","Network Design Practical","Using Cisco Packet Tracer, build a network with 2 routers, 2 switches, and 4 PCs (2 per switch). Configure IP addresses, default gateways, and static routes so all PCs can ping each other. Submit a screenshot of successful pings.",
            [["What is the difference between 2.4 GHz and 5 GHz Wi-Fi?","2.4 GHz is always faster","2.4 GHz has longer range but slower speeds; 5 GHz has shorter range but faster speeds","They are identical","5 GHz has longer range","B"],
             ["What is a VLAN?","A type of cable","A logical network segment within a physical network that isolates traffic between groups","A wireless network","A type of router","B"],
             ["What is the implicit deny rule in firewall configuration?","Allow all traffic by default","All traffic not explicitly permitted by a rule is automatically denied","Allow traffic from trusted IPs only","Deny traffic from unknown countries","B"],
             ["What is a rogue access point?","A broken router","A fake Wi-Fi hotspot set up by an attacker to intercept network traffic","A misconfigured switch","An outdated router","B"],
             ["What does DNS do?","Encrypts network traffic","Translates human-readable domain names to IP addresses","Assigns IP addresses","Blocks malicious websites","B"]]]
    ]],
    [15,"Cloud Computing",[
        ["test","Cloud Computing Test","Test your cloud computing knowledge.",
            [["What is the difference between IaaS, PaaS, and SaaS?","They are the same","IaaS provides infrastructure; PaaS provides a platform to build on; SaaS provides ready-to-use software","SaaS is the most technical","IaaS is the cheapest","B"],
             ["What does S3 stand for in AWS?","Simple Storage Service","Secure Server System","Scalable Storage Solution","Standard Storage Service","A"],
             ["What is the difference between a Docker image and a container?","They are the same","An image is a read-only template; a container is a running instance of an image","A container is a template","Images are larger","B"],
             ["What does CI/CD stand for?","Computer Integration/Deployment","Continuous Integration/Continuous Delivery: automating build, test, and deployment pipelines","Cloud Infrastructure/Deployment","Code Integration/Delivery","B"],
             ["What is Infrastructure as Code?","Writing code on a server","Defining and managing infrastructure using code files for repeatability, version control, and automation","A programming language","A cloud service","B"]]],
        ["practical","AWS Deployment Practical","Launch a free-tier EC2 instance (t2.micro, Amazon Linux 2). Connect via SSH. Install Apache. Create a simple HTML page. Access it via the public IP. Submit a screenshot of your working website.",
            [["What is Auto Scaling in AWS?","Manually adding servers","Automatically adjusting the number of EC2 instances based on demand","Scaling the database","Scaling the network","B"],
             ["What is the AWS Well-Architected Framework?","A type of server","6 pillars for building reliable, secure, efficient cloud systems: Operational Excellence, Security, Reliability, Performance, Cost, Sustainability","A programming framework","A database design pattern","B"],
             ["What is a CDN?","A type of database","A network of servers worldwide that delivers content from the closest location to the user for faster loading","A cloud database","A container service","B"],
             ["Why should you never commit your .env file to Git?","It is too large","It contains sensitive credentials that would be exposed publicly","Git does not support it","It slows down Git","B"],
             ["What is the difference between public and private cloud?","Public cloud is free","Public cloud is shared infrastructure managed by a provider; private cloud is dedicated to one organisation","Private cloud is always cheaper","Public cloud is more secure","B"]]]
    ]],
    [16,"Software Engineering",[
        ["test","Software Engineering Test","Test your software engineering knowledge.",
            [["What are the 7 phases of the SDLC?","Plan, Code, Test, Deploy, Monitor, Fix, Repeat","Planning, Requirements, Design, Implementation, Testing, Deployment, Maintenance","Brief, Sketch, Build, Review, Launch, Monitor, Retire","Discover, Define, Design, Develop, Deploy, Deliver, Decommission","B"],
             ["What is the difference between Waterfall and Agile?","Waterfall is faster","Waterfall is sequential with fixed phases; Agile is iterative with short sprints and continuous feedback","Agile is older","They are the same","B"],
             ["What does SOLID stand for?","Single, Open, Liskov, Interface, Dependency","Simple, Object, Linked, Integrated, Dynamic","Standard, Open, Linked, Interface, Dependency","Single, Object, Linked, Integrated, Dependency","A"],
             ["What is a user story in Scrum?","A blog post","A feature description from the user perspective: As a [user] I want [goal] so that [reason]","A user manual","A database record","B"],
             ["What is TDD?","Testing after development","Writing tests before writing code: Red (failing test) then Green (make it pass) then Refactor","Testing during deployment","Testing by users","B"]]],
        ["practical","System Design Practical","Design the system architecture for a Nigerian ride-hailing app. Consider: user auth, real-time driver location, ride matching, payment processing, notifications, and ratings. Draw the architecture diagram and explain your technology choices. Submit as a document or diagram.",
            [["What is the difference between vertical and horizontal scaling?","Vertical is better","Vertical scaling adds more resources to one server; horizontal scaling adds more servers","Horizontal is always cheaper","They are the same","B"],
             ["What is caching and why does it improve performance?","Storing backups","Storing frequently accessed data in fast memory to reduce database load and response time","Compressing files","Encrypting data","B"],
             ["What is a message queue?","A chat system","A system for asynchronous communication between services so they do not need to wait for each other","A database table","A type of API","B"],
             ["What is the Repository pattern?","A Git repository","An abstraction layer that separates data access logic from business logic making code testable","A type of database","A design pattern for UI","B"],
             ["What is horizontal scaling?","Adding more RAM to one server","Adding more servers to distribute load allowing virtually unlimited capacity growth","Scaling the database only","Scaling the network","B"]]]
    ]]
] as [$cid,$cname,$assessments]){
    if(!isset($by[$cid]) || count($by[$cid])<count($assessments)) continue;
    $ls=$by[$cid];
    foreach($assessments as $i=>[$type,$title,$inst,$qs])
        ins($pdo,$aS,$qS,$ls[$i],$cid,$type,$title,$inst,$qs,$ts,$ta,$tq);
}

// Courses 17-19: Data Science, AI, ML
foreach([
    [17,"Data Science",[
        ["test","Data Science Intro Test","Test your data science fundamentals.",
            [["What are the 9 stages of the data science lifecycle?","Collect, Clean, Analyse, Visualise, Deploy, Monitor, Iterate, Report, Archive","Problem Definition, Data Collection, Cleaning, EDA, Feature Engineering, Modelling, Evaluation, Deployment, Monitoring","Plan, Gather, Process, Model, Test, Deploy, Review, Report, Close","Import, Clean, Explore, Model, Evaluate, Deploy, Monitor, Document, Retire","B"],
             ["What is EDA?","Exploratory Data Analysis: understanding data through statistics and visualisation before modelling","Extended Data Architecture","Evaluated Data Algorithm","Extracted Data Application","A"],
             ["What Python library is primarily used for data manipulation?","NumPy","Matplotlib","pandas","Seaborn","C"],
             ["What is the difference between supervised and unsupervised learning?","Supervised uses more data","Supervised learns from labelled examples; unsupervised finds patterns in unlabelled data","Supervised is faster","Unsupervised requires more computing power","B"],
             ["What chart type is best for showing a trend over time?","Pie chart","Bar chart","Line chart","Scatter plot","C"]]],
        ["practical","Python EDA Practical","Download a Nigerian dataset from Kaggle. Load into pandas. Perform: data cleaning, descriptive statistics, correlation analysis, 3 visualisations. Write a 1-paragraph insight summary. Submit your Jupyter Notebook.",
            [["What does df.describe() return?","The first 5 rows","Statistical summary: count, mean, std, min, quartiles, max for numeric columns","The column names","The data types","B"],
             ["What is one-hot encoding?","A security technique","Converting categorical variables into binary columns for each category so ML algorithms can process them","A type of normalisation","A data cleaning step","B"],
             ["Why do we scale features before modelling?","To make data look better","To ensure features with large values do not dominate features with small values in distance-based algorithms","To reduce file size","To improve visualisation","B"],
             ["What is the difference between dropping and imputing missing values?","They are the same","Dropping removes rows with missing data; imputing fills missing values with statistics or predictions","Imputing is always better","Dropping is always better","B"],
             ["What is the difference between df.loc and df.iloc?","They are the same","loc selects by label/name; iloc selects by integer position","iloc is faster","loc is for rows only","B"]]]
    ]],
    [18,"Artificial Intelligence",[
        ["test","AI Fundamentals Test","Test your AI knowledge.",
            [["What type of AI is designed for one specific task?","General AI","Super AI","Narrow AI","Symbolic AI","C"],
             ["What is the activation function most commonly used in hidden layers?","Sigmoid","Tanh","Softmax","ReLU","D"],
             ["What is transfer learning?","Training from scratch","Using a pre-trained model as a starting point for a new task saving time and data","Transferring data between servers","Moving a model to production","B"],
             ["What is the key innovation of the Transformer architecture?","Convolutional layers for text","Self-attention mechanism allowing each token to attend to all other tokens simultaneously","Recurrent connections","Pooling layers","B"],
             ["What is hallucination in LLMs?","When the model crashes","When the model generates false but confident information not grounded in facts","When the model is too slow","When the model refuses to answer","B"]]],
        ["practical","AI Application Practical","Build an AI tutor chatbot using the OpenAI API. Features: answer questions about a course topic, generate practice questions, explain concepts at different difficulty levels. Submit your code and a screenshot of it working.",
            [["What is prompt engineering?","Writing code for AI","The art of crafting effective prompts to get the best responses from AI models","Training AI models","Testing AI systems","B"],
             ["What is RAG?","Random Aggregation Generator","Retrieval-Augmented Generation: combining LLMs with a knowledge base to generate answers grounded in retrieved documents","Recurrent Attention Gate","Regularised Activation Graph","B"],
             ["What is the difference between fine-tuning and RAG?","They are the same","Fine-tuning trains the model on new data; RAG retrieves external knowledge at inference time without retraining","RAG is more expensive","Fine-tuning is faster","B"],
             ["What is algorithmic bias?","When an algorithm runs slowly","When an AI system produces systematically unfair outcomes due to biased training data or design","When an algorithm has too many parameters","When a model overfits","B"],
             ["What is the EU AI Act?","A European AI company","Risk-based regulation of AI systems in the EU classifying AI by risk level and imposing requirements accordingly","A programming framework","An AI certification","B"]]]
    ]],
    [19,"Machine Learning",[
        ["test","ML Fundamentals Test","Test your machine learning knowledge.",
            [["What is overfitting?","When a model is too simple","When a model performs well on training data but poorly on new unseen data due to memorising noise","When a model takes too long to train","When a model has too few parameters","B"],
             ["What does SMOTE stand for?","Supervised Model Optimisation Technique","Synthetic Minority Over-sampling Technique: creates synthetic examples of the minority class to balance datasets","Standard Model Output Testing","Stochastic Model Optimisation Training","B"],
             ["What metric is most appropriate for a fraud detection model?","Accuracy","Mean Squared Error","Recall: to minimise missed fraud cases since false negatives are costly","R-squared","C"],
             ["What is cross-validation?","Splitting data once","Getting a more reliable performance estimate by training and evaluating on multiple different data splits","Cleaning the training data","Reducing the number of features","B"],
             ["What is data drift?","Corrupted training data","When the statistical properties of production data change over time causing model performance to degrade","A model bug","Retraining too frequently","B"]]],
        ["practical","ML Deployment Practical","Deploy your best ML model as a Flask REST API. Endpoint: POST /predict with customer features, returns prediction and probability. Test with Postman or curl. Submit your code and a screenshot of a successful prediction.",
            [["What is MLflow used for?","Building neural networks","Tracking ML experiments, parameters, metrics, and models for reproducibility and comparison","Deploying models to mobile","Cleaning data","B"],
             ["What is a model card?","A business card for ML engineers","A document describing a model: purpose, performance metrics, limitations, fairness considerations, and intended use","A type of neural network","A deployment configuration","B"],
             ["What is the difference between model deployment and MLOps?","They are the same","Deployment is putting a model into production; MLOps is the full practice of deploying, monitoring, and maintaining models over time","MLOps is only for large companies","Deployment is more complex","B"],
             ["What should you monitor in a deployed ML model?","Only the server uptime","Prediction distribution, feature distribution (data drift), model performance on labelled production samples, and latency","Only error rates","Only the number of requests","B"],
             ["What is the difference between joblib and pickle?","They are the same","joblib is optimised for large NumPy arrays (ML models); pickle is general-purpose Python serialisation","pickle is faster","joblib is for databases only","B"]]]
    ]]
] as [$cid,$cname,$assessments]){
    if(!isset($by[$cid]) || count($by[$cid])<count($assessments)) continue;
    $ls=$by[$cid];
    foreach($assessments as $i=>[$type,$title,$inst,$qs])
        ins($pdo,$aS,$qS,$ls[$i],$cid,$type,$title,$inst,$qs,$ts,$ta,$tq);
}

$pdo->exec("SET FOREIGN_KEY_CHECKS=1");
echo "Assessments inserted: {$ta}\nQuestions inserted: {$tq}\n";
