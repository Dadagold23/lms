<?php
declare(strict_types=1);

if (!function_exists('getAffiliateCurriculum')) {
    function getAffiliateCurriculum(string $classLevel): ?array
    {
        static $curriculums = null;
        if ($curriculums === null) {
            $curriculums = [
                'JSS1' => [
                    [
                        'id' => 1001,
                        'sort_order' => 1,
                        'title' => 'Lesson 1: Introduction to Computer Systems',
                        'content' => "Welcome to JSS 1 Computer Studies!\n\nIn this lesson, we will explore the fundamental components of a computer system. A computer is an electronic device that accepts data (input), processes it, stores it, and produces output.\n\nKey Concepts:\n1. Input Devices: Keyboard, Mouse, Scanner.\n2. Output Devices: Monitor, Printer, Speakers.\n3. System Unit: The main case that houses the CPU (Central Processing Unit), memory, and other components.\n\nSummary:\nComputers help us work faster and more accurately. Understanding how they receive and process information is the first step in digital literacy.",
                        'assessment' => [
                            'id' => 10001,
                            'title' => 'Introduction to Computer Systems Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 100011,
                                    'question' => 'Which of the following is an input device?',
                                    'option_a' => 'Printer',
                                    'option_b' => 'Keyboard',
                                    'option_c' => 'Monitor',
                                    'option_d' => 'Speakers',
                                    'correct_option' => 'B'
                                ],
                                [
                                    'id' => 100012,
                                    'question' => 'What does CPU stand for?',
                                    'option_a' => 'Computer Processing Unit',
                                    'option_b' => 'Central Program Utility',
                                    'option_c' => 'Central Processing Unit',
                                    'option_d' => 'Central Processor Utility',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 1002,
                        'sort_order' => 2,
                        'title' => 'Lesson 2: Input and Output Devices in Detail',
                        'content' => "Input and output devices allow us to interact with the computer.\n\nInput Devices:\n- Keyboard: Used for typing text and entering commands.\n- Mouse: A pointing device used to click, drag, and select items on the screen.\n\nOutput Devices:\n- Monitor (Screen): Displays text, images, and videos.\n- Printer: Creates a physical copy (hard copy) of digital documents.\n\nActivity:\nTry listing five input and output devices you see in your school lab.",
                        'assessment' => [
                            'id' => 10002,
                            'title' => 'Input and Output Devices Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 100021,
                                    'question' => 'Which device creates a physical copy of digital documents?',
                                    'option_a' => 'Monitor',
                                    'option_b' => 'Keyboard',
                                    'option_c' => 'Printer',
                                    'option_d' => 'Mouse',
                                    'correct_option' => 'C'
                                ],
                                [
                                    'id' => 100022,
                                    'question' => 'A mouse is used for what purpose?',
                                    'option_a' => 'Printing',
                                    'option_b' => 'Clicking and selecting screen items',
                                    'option_c' => 'Displaying video',
                                    'option_d' => 'Typing letters',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 1003,
                        'sort_order' => 3,
                        'title' => 'Lesson 3: Introduction to Windows & File Management',
                        'content' => "An Operating System (like Windows) is the software that manages the computer's hardware and other software.\n\nKey Tasks:\n1. Creating a Folder: Right-click on the desktop -> New -> Folder. Give it a name!\n2. Copying and Pasting: Copy (Ctrl+C) and Paste (Ctrl+V) files to organize your work.\n3. Deleting Files: Recycle Bin stores deleted files temporarily.\n\nKeeping your files organized makes it easy to find your school projects.",
                        'assessment' => [
                            'id' => 10003,
                            'title' => 'Windows & File Management Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 100031,
                                    'question' => 'Which keyboard shortcut is used to copy a file?',
                                    'option_a' => 'Ctrl+V',
                                    'option_b' => 'Ctrl+C',
                                    'option_c' => 'Ctrl+X',
                                    'option_d' => 'Ctrl+Z',
                                    'correct_option' => 'B'
                                ],
                                [
                                    'id' => 100032,
                                    'question' => 'Where do deleted files go temporarily in Windows?',
                                    'option_a' => 'My Documents',
                                    'option_b' => 'Control Panel',
                                    'option_c' => 'Recycle Bin',
                                    'option_d' => 'Desktop',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 1004,
                        'sort_order' => 4,
                        'title' => 'Lesson 4: Creative Computing with MS Paint',
                        'content' => "Digital art is a fun way to use computers!\n\nUsing MS Paint:\n- Pencil & Brush Tools: Used for freehand drawing.\n- Shapes Tool: Quickly draw rectangles, circles, and stars.\n- Color Picker & Fill Bucket: Add vibrant colors to your designs.\n\nPractice:\nOpen Paint on your computer and design a greeting card for your best friend.",
                        'assessment' => [
                            'id' => 10004,
                            'title' => 'Creative Computing Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 100041,
                                    'question' => 'Which tool in MS Paint is used to fill a shape with color?',
                                    'option_a' => 'Pencil tool',
                                    'option_b' => 'Color Picker',
                                    'option_c' => 'Fill Bucket tool',
                                    'option_d' => 'Eraser tool',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 1005,
                        'sort_order' => 5,
                        'title' => 'Lesson 5: Introduction to Word Processing (WordPad)',
                        'content' => "Word processing means typing and formatting text documents.\n\nIn WordPad, you can:\n- Type sentences and paragraphs.\n- Change Font style, size, and color to make your text look professional.\n- Save your document using File -> Save.\n\nWriting Exercise:\nType a short paragraph about 'My Favorite Hobby' and format the title in bold and red.",
                        'assessment' => [
                            'id' => 10005,
                            'title' => 'Word Processing Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 100051,
                                    'question' => 'Which font styling makes text thicker and darker?',
                                    'option_a' => 'Italic',
                                    'option_b' => 'Underline',
                                    'option_c' => 'Bold',
                                    'option_d' => 'Strike-through',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ]
                ],
                'JSS2' => [
                    [
                        'id' => 2001,
                        'sort_order' => 1,
                        'title' => 'Lesson 1: What is the Internet and World Wide Web?',
                        'content' => "Welcome to JSS 2 Computer Studies!\n\nThe Internet is a global network of interconnected computers. The World Wide Web (WWW) is a system of websites accessed via the Internet.\n\nImportant Terms:\n- Web Browser: Chrome, Firefox, or Edge.\n- Search Engine: Google, Bing.\n- URL: Website address (e.g., www.google.com).\n\nHaving the internet connects us to knowledge from all over the world!",
                        'assessment' => [
                            'id' => 20001,
                            'title' => 'Internet & WWW Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 200011,
                                    'question' => 'Which of the following is a Web Browser?',
                                    'option_a' => 'Google Search',
                                    'option_b' => 'Chrome',
                                    'option_c' => 'Windows',
                                    'option_d' => 'PowerPoint',
                                    'correct_option' => 'B'
                                ],
                                [
                                    'id' => 200012,
                                    'question' => 'What is the full form of WWW?',
                                    'option_a' => 'World Wide Web',
                                    'option_b' => 'Wide Web World',
                                    'option_c' => 'Web World Wide',
                                    'option_d' => 'World Web Wide',
                                    'correct_option' => 'A'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 2002,
                        'sort_order' => 2,
                        'title' => 'Lesson 2: Web Browsing & Search Techniques',
                        'content' => "To find information efficiently, you need to use search engines effectively.\n\nSearch Tips:\n1. Use Specific Keywords: Instead of 'animals', search 'African elephant habitat'.\n2. Quotation Marks: Use \"solar system\" to find the exact phrase.\n3. Bookmarks: Save websites you visit often for quick access.\n\nAlways verify information across multiple websites to ensure accuracy.",
                        'assessment' => [
                            'id' => 20002,
                            'title' => 'Web Search Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 200021,
                                    'question' => 'Which search technique matches an exact phrase?',
                                    'option_a' => 'Adding a plus sign',
                                    'option_b' => 'Using quotation marks',
                                    'option_c' => 'Typing in ALL CAPS',
                                    'option_d' => 'Using standard keywords',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 2003,
                        'sort_order' => 3,
                        'title' => 'Lesson 3: Introduction to Spreadsheets (Microsoft Excel)',
                        'content' => "Spreadsheets help us organize data and perform calculations using tables.\n\nKey Concepts:\n- Columns (labeled A, B, C) and Rows (labeled 1, 2, 3).\n- Cell: The intersection of a row and a column (e.g., A1).\n- Formula Bar: Used to write equations like `=SUM(A1:A5)`.\n\nExcel is a powerful tool used by business professionals worldwide to manage numbers.",
                        'assessment' => [
                            'id' => 20003,
                            'title' => 'Spreadsheets Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 200031,
                                    'question' => 'In Excel, what is the intersection of a row and a column called?',
                                    'option_a' => 'Grid',
                                    'option_b' => 'Cell',
                                    'option_c' => 'Formula',
                                    'option_d' => 'Sheet',
                                    'correct_option' => 'B'
                                ],
                                [
                                    'id' => 200032,
                                    'question' => 'Which symbol must every Excel formula begin with?',
                                    'option_a' => '+',
                                    'option_b' => '@',
                                    'option_c' => '=',
                                    'option_d' => '#',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 2004,
                        'sort_order' => 4,
                        'title' => 'Lesson 4: Managing Data in Spreadsheets',
                        'content' => "Once data is entered, you can organize it using Excel tools.\n\nUseful Functions:\n- Sorting: Arrange names alphabetically (A to Z) or numbers from lowest to highest.\n- Filtering: Display only specific data (e.g. students who scored above 80%).\n- Charts: Convert tables into colorful pie charts or bar graphs.\n\nVisualizing data makes it much easier to spot trends.",
                        'assessment' => [
                            'id' => 20004,
                            'title' => 'Spreadsheets Data Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 200041,
                                    'question' => 'Which Excel feature allows you to display only rows that meet specific criteria?',
                                    'option_a' => 'Sorting',
                                    'option_b' => 'Filtering',
                                    'option_c' => 'Formulas',
                                    'option_d' => 'Formatting',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 2005,
                        'sort_order' => 5,
                        'title' => 'Lesson 5: Creating Multimedia Presentations',
                        'content' => "Microsoft PowerPoint allows you to share ideas visually using slides.\n\nCreating Slides:\n- Add Titles, Bullet points, and images.\n- Apply Slide Themes for a cohesive style.\n- Add simple Slide Transitions to make your presentation engaging.\n\nPresentation Tip: Keep slides clean. Use short bullet points instead of long paragraphs.",
                        'assessment' => [
                            'id' => 20005,
                            'title' => 'Presentations Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 200051,
                                    'question' => 'What is the recommended rule for slide design?',
                                    'option_a' => 'Type full paragraphs on slides',
                                    'option_b' => 'Use short bullet points instead of long text',
                                    'option_c' => 'Use as many animations as possible',
                                    'option_d' => 'Do not include images',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ]
                ],
                'JSS3' => [
                    [
                        'id' => 3001,
                        'sort_order' => 1,
                        'title' => 'Lesson 1: Algorithms and Problem Solving',
                        'content' => "Welcome to JSS 3 Computer Studies!\n\nAn Algorithm is a step-by-step set of instructions to solve a problem or perform a task.\n\nExample (Making Tea):\n1. Boil water.\n2. Add tea bag to cup.\n3. Pour hot water into cup.\n4. Add sugar and milk.\n5. Stir and serve.\n\nComputers cannot think on their own. They rely on algorithms written by programmers.",
                        'assessment' => [
                            'id' => 30001,
                            'title' => 'Algorithms Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 300011,
                                    'question' => 'What is an algorithm?',
                                    'option_a' => 'A computer screen brand',
                                    'option_b' => 'A step-by-step set of instructions to solve a problem',
                                    'option_c' => 'A coding language',
                                    'option_d' => 'A visual diagram',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 3002,
                        'sort_order' => 2,
                        'title' => 'Lesson 2: Flowcharts & Logical Diagrams',
                        'content' => "A Flowchart is a visual representation of an algorithm.\n\nFlowchart Shapes:\n- Oval (Terminal): Start and End points.\n- Rectangle (Process): Instructions or actions.\n- Diamond (Decision): Questions with Yes/No answers.\n- Parallelogram (Input/Output): Data read or written.\n\nDrawing flowcharts helps programmers plan their code before writing it.",
                        'assessment' => [
                            'id' => 30002,
                            'title' => 'Flowcharts Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 300021,
                                    'question' => 'Which flowchart shape represents a decision or a question?',
                                    'option_a' => 'Rectangle',
                                    'option_b' => 'Oval',
                                    'option_c' => 'Diamond',
                                    'option_d' => 'Parallelogram',
                                    'correct_option' => 'C'
                                ],
                                [
                                    'id' => 300022,
                                    'question' => 'What does an Oval shape represent in a flowchart?',
                                    'option_a' => 'Decision',
                                    'option_b' => 'Process',
                                    'option_c' => 'Start/End point',
                                    'option_d' => 'Input/Output',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 3003,
                        'sort_order' => 3,
                        'title' => 'Lesson 3: Introduction to Block-Based Coding (Scratch)',
                        'content' => "Scratch is a visual programming language where you snap blocks together to create games and animations.\n\nKey Concepts:\n- Sprite: Characters or objects on the stage.\n- Scripts: The blocks that control Sprite movements and sound.\n- Event Blocks: 'When Green Flag Clicked' starts the action.\n\nBlock-based coding makes learning programming syntax easy and fun!",
                        'assessment' => [
                            'id' => 30003,
                            'title' => 'Scratch Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 300031,
                                    'question' => 'In Scratch, what are the characters or objects on the stage called?',
                                    'option_a' => 'Blocks',
                                    'option_b' => 'Sprites',
                                    'option_c' => 'Scripts',
                                    'option_d' => 'Backdrops',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 3004,
                        'sort_order' => 4,
                        'title' => 'Lesson 4: Variables and Loops in Scratch',
                        'content' => "Control blocks add logic and memory to your programs.\n\nLoops & Variables:\n- Repeat Loops: Make sprites repeat actions (e.g., walking 10 steps 5 times).\n- Variables: Containers that store changing values (e.g. keeping score in a game).\n\nAdding variables and loops allows you to build interactive games.",
                        'assessment' => [
                            'id' => 30004,
                            'title' => 'Loops & Variables Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 300041,
                                    'question' => 'What is a variable in programming?',
                                    'option_a' => 'A sprite graphic',
                                    'option_b' => 'A container that stores changing data values',
                                    'option_c' => 'A repeat script',
                                    'option_d' => 'A sound effect',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 3005,
                        'sort_order' => 5,
                        'title' => 'Lesson 5: Tech Ethics & Safe Web Navigation',
                        'content' => "Being a good digital citizen is essential.\n\nSafe Habits:\n1. Private Info: Never share your full name, address, or passwords online.\n2. Cyberbullying: Treat others with respect. Report mean comments to a parent or teacher.\n3. Phishing: Beware of suspicious emails asking for login details.\n\nEnjoy the digital world safely and responsibly!",
                        'assessment' => [
                            'id' => 30005,
                            'title' => 'Ethics Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 300051,
                                    'question' => 'Which of the following is a safe online habit?',
                                    'option_a' => 'Sharing passwords with friends',
                                    'option_b' => 'Posting home address online',
                                    'option_c' => 'Keeping private information private',
                                    'option_d' => 'Responding to mean online messages',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ]
                ],
                'SSS1' => [
                    [
                        'id' => 4001,
                        'sort_order' => 1,
                        'title' => 'Lesson 1: Introduction to Software and Hardware Systems',
                        'content' => "Welcome to SSS 1 Computer Science!\n\nA computer system is divided into Hardware (physical components) and Software (logical programs).\n\nSoftware Categories:\n1. System Software: Coordinates hardware components (e.g., Windows, macOS, Linux).\n2. Application Software: Programs that help users perform tasks (e.g., Chrome, MS Word, Photoshop).\n\nUnderstanding software categories helps us manage system performance.",
                        'assessment' => [
                            'id' => 40001,
                            'title' => 'Software & Hardware Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 400011,
                                    'question' => 'Which of the following is System Software?',
                                    'option_a' => 'MS Word',
                                    'option_b' => 'Windows OS',
                                    'option_c' => 'Photoshop',
                                    'option_d' => 'Google Chrome',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 4002,
                        'sort_order' => 2,
                        'title' => 'Lesson 2: Computer Memory and Storage Units',
                        'content' => "Memory is where computers store active data and programs.\n\nMemory Types:\n- RAM (Random Access Memory): Volatile, temporary memory. Erased when powered off.\n- ROM (Read-Only Memory): Non-volatile, permanent memory containing startup instructions (BIOS).\n\nStorage Units:\n- Bit: The smallest unit (0 or 1).\n- Byte: 8 bits.\n- Kilobyte (KB), Megabyte (MB), Gigabyte (GB), Terabyte (TB).",
                        'assessment' => [
                            'id' => 40002,
                            'title' => 'Memory Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 400021,
                                    'question' => 'Which memory type loses its data when the computer is turned off?',
                                    'option_a' => 'ROM',
                                    'option_b' => 'RAM',
                                    'option_c' => 'Hard Disk',
                                    'option_d' => 'Flash Drive',
                                    'correct_option' => 'B'
                                ],
                                [
                                    'id' => 400022,
                                    'question' => 'How many bits make up 1 Byte?',
                                    'option_a' => '4',
                                    'option_b' => '8',
                                    'option_c' => '16',
                                    'option_d' => '1024',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 4003,
                        'sort_order' => 3,
                        'title' => 'Lesson 3: Introduction to Web Design and HTML',
                        'content' => "HTML (HyperText Markup Language) is the standard language for creating web pages.\n\nHTML Document Structure:\n```html\n<!DOCTYPE html>\n<html>\n<head>\n    <title>My First Page</title>\n</head>\n<body>\n    <h1>Hello World</h1>\n    <p>This is a paragraph.</p>\n</body>\n</html>\n```\n\nTags (like `<h1>`, `<p>`) define elements on the page.",
                        'assessment' => [
                            'id' => 40003,
                            'title' => 'HTML Basics Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 400031,
                                    'question' => 'What does HTML stand for?',
                                    'option_a' => 'HyperText Markup Language',
                                    'option_b' => 'HyperLink Text Makeup Language',
                                    'option_c' => 'HighText Markup Language',
                                    'option_d' => 'HyperText Medium Language',
                                    'correct_option' => 'A'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 4004,
                        'sort_order' => 4,
                        'title' => 'Lesson 4: Formatting and Links in HTML',
                        'content' => "To build complete websites, you must link pages together and add media.\n\nKey Tags:\n- Links: `<a href=\"https://google.com\">Click Here</a>`\n- Images: `<img src=\"logo.png\" alt=\"Logo\">`\n- Lists: Ordered (`<ol>`) and Unordered (`<ul>`) lists.\n\nCreating clear headings and lists makes content easy for users to read.",
                        'assessment' => [
                            'id' => 40004,
                            'title' => 'HTML Formatting Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 400041,
                                    'question' => 'Which HTML tag is used to insert a hyperlink?',
                                    'option_a' => '<link>',
                                    'option_b' => '<a>',
                                    'option_c' => '<href>',
                                    'option_d' => '<img>',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 4005,
                        'sort_order' => 5,
                        'title' => 'Lesson 5: Introduction to Styling with CSS',
                        'content' => "CSS (Cascading Style Sheets) styles and formats the HTML layout.\n\nCSS Syntax:\n```css\nh1 {\n    color: blue;\n    font-size: 24px;\n}\n```\n\nYou can apply CSS in three ways: Inline styles, Internal style blocks, and External stylesheet files. Separation of content (HTML) and style (CSS) is a professional web standard.",
                        'assessment' => [
                            'id' => 40005,
                            'title' => 'CSS Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 400051,
                                    'question' => 'What is CSS used for?',
                                    'option_a' => 'Structuring web layout content',
                                    'option_b' => 'Styling and formatting the HTML layout',
                                    'option_c' => 'Database storage',
                                    'option_d' => 'Server execution',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ]
                ],
                'SSS2' => [
                    [
                        'id' => 5001,
                        'sort_order' => 1,
                        'title' => 'Lesson 1: Introduction to Coding and Python Programming',
                        'content' => "Welcome to SSS 2 Computer Science!\n\nPython is a popular, high-level, easy-to-read programming language.\n\nYour First Script:\n```python\nprint(\"Hello, Python!\")\n```\n\nKey Syntax:\n- Comments start with `#`.\n- Python is case-sensitive (`Print` will throw an error).",
                        'assessment' => [
                            'id' => 50001,
                            'title' => 'Python Basics Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 500011,
                                    'question' => 'Which of the following is correct syntax to print a message in Python?',
                                    'option_a' => 'print \"Hello\"',
                                    'option_b' => 'print(\"Hello\")',
                                    'option_c' => 'Print(\"Hello\")',
                                    'option_d' => 'write(\"Hello\")',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 5002,
                        'sort_order' => 2,
                        'title' => 'Lesson 2: Variables and Data Types in Python',
                        'content' => "Variables are containers used to store data values.\n\nCommon Data Types:\n- Integer: Whole numbers (e.g. `age = 16`).\n- Float: Decimal numbers (e.g. `gpa = 4.5`).\n- String: Text characters (e.g. `name = \"David\"`).\n- Boolean: `True` or `False`.\n\nPython dynamically infers data types, making variables easy to declare.",
                        'assessment' => [
                            'id' => 50002,
                            'title' => 'Python Variables Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 500021,
                                    'question' => 'Which data type is used to store decimal values like 3.14?',
                                    'option_a' => 'Integer',
                                    'option_b' => 'Float',
                                    'option_c' => 'String',
                                    'option_d' => 'Boolean',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 5003,
                        'sort_order' => 3,
                        'title' => 'Lesson 3: Conditional Statements (if, elif, else)',
                        'content' => "Conditional statements execute code blocks based on logical conditions.\n\nSyntax:\n```python\nscore = 75\nif score >= 80:\n    print(\"Grade A\")\nelif score >= 60:\n    print(\"Grade B\")\nelse:\n    print(\"Grade C\")\n```\n\nNote: Python uses indentation (spaces/tabs) to define code blocks.",
                        'assessment' => [
                            'id' => 50003,
                            'title' => 'Python Conditionals Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 500031,
                                    'question' => 'How does Python define a block of code (e.g. inside an if statement)?',
                                    'option_a' => 'Curly braces {}',
                                    'option_b' => 'Parentheses ()',
                                    'option_c' => 'Indentation (spaces/tabs)',
                                    'option_d' => 'Semicolons ;',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 5004,
                        'sort_order' => 4,
                        'title' => 'Lesson 4: Loops and Repetitive Executions',
                        'content' => "Loops repeat a block of code while a condition is met.\n\nFor Loop:\n```python\nfor i in range(5):\n    print(\"Iteration\", i)\n```\n\nWhile Loop:\n```python\ncount = 1\nwhile count <= 3:\n    print(\"Count is\", count)\n    count += 1\n```",
                        'assessment' => [
                            'id' => 50004,
                            'title' => 'Python Loops Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 500041,
                                    'question' => 'How many times will `for i in range(3):` repeat?',
                                    'option_a' => '2',
                                    'option_b' => '3',
                                    'option_c' => '4',
                                    'option_d' => 'Infinite',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 5005,
                        'sort_order' => 5,
                        'title' => 'Lesson 5: Introduction to Python Functions',
                        'content' => "Functions are reusable blocks of code that perform specific tasks.\n\nDefining a Function:\n```python\ndef greet(name):\n    return \"Hello, \" + name + \"!\"\n\n# Calling the function\nprint(greet(\"Alice\"))\n```\n\nFunctions keep code modular and dry (Don't Repeat Yourself).",
                        'assessment' => [
                            'id' => 50005,
                            'title' => 'Python Functions Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 500051,
                                    'question' => 'Which keyword is used to define a function in Python?',
                                    'option_a' => 'function',
                                    'option_b' => 'def',
                                    'option_c' => 'define',
                                    'option_d' => 'func',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ]
                ],
                'SSS3' => [
                    [
                        'id' => 6001,
                        'sort_order' => 1,
                        'title' => 'Lesson 1: Introduction to Database Systems',
                        'content' => "Welcome to SSS 3 Computer Science!\n\nA Database is an organized collection of structured data, typically stored electronically.\n\nKey Concepts:\n- Relational Databases: Organize data in Tables containing columns and rows.\n- DBMS (Database Management System): Software used to create and manage databases (e.g., MySQL, SQLite, PostgreSQL).",
                        'assessment' => [
                            'id' => 60001,
                            'title' => 'Database Systems Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 600011,
                                    'question' => 'A relational database organizes data in what form?',
                                    'option_a' => 'Linear lists',
                                    'option_b' => 'Tables with columns and rows',
                                    'option_c' => 'Text documents',
                                    'option_d' => 'Unordered sets',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 6002,
                        'sort_order' => 2,
                        'title' => 'Lesson 2: SQL Basics — Queries and Tables',
                        'content' => "SQL (Structured Query Language) is used to communicate with databases.\n\nSQL Commands:\n1. CREATE TABLE: Defines table structure.\n2. INSERT INTO: Adds new records.\n3. SELECT: Retrieves data.\n\nExample Query:\n```sql\nSELECT first_name, grade FROM students WHERE score >= 75;\n```",
                        'assessment' => [
                            'id' => 60002,
                            'title' => 'SQL Basics Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 600021,
                                    'question' => 'Which SQL keyword is used to retrieve data from a database?',
                                    'option_a' => 'GET',
                                    'option_b' => 'SELECT',
                                    'option_c' => 'EXTRACT',
                                    'option_d' => 'FETCH',
                                    'correct_option' => 'B'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 6003,
                        'sort_order' => 3,
                        'title' => 'Lesson 3: Advanced SQL — Join Operations',
                        'content' => "A JOIN clause merges rows from two or more tables based on a related column.\n\nTypes of JOINs:\n- INNER JOIN: Returns matching rows in both tables.\n- LEFT JOIN: Returns all rows from left table, and matches from right.\n\nExample Join:\n```sql\nSELECT students.name, enrollments.course_title \nFROM students \nJOIN enrollments ON students.id = enrollments.student_id;\n```",
                        'assessment' => [
                            'id' => 60003,
                            'title' => 'SQL Joins Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 600031,
                                    'question' => 'Which type of JOIN returns only matching rows in both tables?',
                                    'option_a' => 'FULL JOIN',
                                    'option_b' => 'LEFT JOIN',
                                    'option_c' => 'INNER JOIN',
                                    'option_d' => 'RIGHT JOIN',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 6004,
                        'sort_order' => 4,
                        'title' => 'Lesson 4: Building Interactive Web Pages',
                        'content' => "Web applications combine HTML structure, CSS design, and Javascript logic.\n\nJavaScript Basics:\nJavaScript runs in the browser, enabling interactivity like button clicks and animations.\n\nExample Script:\n```javascript\ndocument.getElementById(\"btn\").addEventListener(\"click\", () => {\n    alert(\"Button Clicked!\");\n});\n```",
                        'assessment' => [
                            'id' => 60004,
                            'title' => 'Interactive Web Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 600041,
                                    'question' => 'Which language is used to add behavior and interactivity to web pages?',
                                    'option_a' => 'HTML',
                                    'option_b' => 'CSS',
                                    'option_c' => 'JavaScript',
                                    'option_d' => 'SQL',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 6005,
                        'sort_order' => 5,
                        'title' => 'Lesson 5: Introduction to Web Server hosting',
                        'content' => "To make your website public, you must host it on a web server.\n\nConcepts:\n- Web Hosting: Server space where files are stored.\n- Domain Name: Simple text name mapped to the server's IP address (e.g., www.myacademy.com).\n- SSL Certificate: Encrypts communication (HTTPS) between the browser and server.",
                        'assessment' => [
                            'id' => 60005,
                            'title' => 'Hosting Quiz',
                            'instructions' => 'Choose the correct answer for each question.',
                            'pass_score' => 50,
                            'questions' => [
                                [
                                    'id' => 600051,
                                    'question' => 'Which protocol indicates a secure, encrypted website connection?',
                                    'option_a' => 'HTTP',
                                    'option_b' => 'FTP',
                                    'option_c' => 'HTTPS',
                                    'option_d' => 'SMTP',
                                    'correct_option' => 'C'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $curriculums[$classLevel] ?? null;
    }
}

if (!function_exists('getAffiliateLessonsForCourse')) {
    function getAffiliateLessonsForCourse($pdo, $courseId, string $classLevel): array
    {
        $customCurriculum = getAffiliateCurriculum($classLevel);
        if (!$customCurriculum) {
            return [];
        }

        // Fetch the first 5 published database lessons for this course
        $stmt = $pdo->prepare("
            SELECT id, sort_order 
            FROM lms_lessons 
            WHERE course_id = ? AND is_published = 1 
            ORDER BY sort_order ASC, id ASC 
            LIMIT 5
        ");
        $stmt->execute([(int)$courseId]);
        $dbLessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mappedLessons = [];
        foreach ($customCurriculum as $index => $customLesson) {
            $dbLessonId = isset($dbLessons[$index]) ? (int)$dbLessons[$index]['id'] : $customLesson['id'];
            $dbSortOrder = isset($dbLessons[$index]) ? (int)$dbLessons[$index]['sort_order'] : $customLesson['sort_order'];

            $lesson = $customLesson;
            $lesson['id'] = $dbLessonId;
            $lesson['sort_order'] = $dbSortOrder;

            // Update assessment ID to match the database lesson ID for indexing
            if (isset($lesson['assessment'])) {
                $lesson['assessment']['id'] = $dbLessonId * 100 + 1; // Unique, deterministic ID
                foreach ($lesson['assessment']['questions'] as $qIdx => &$q) {
                    $q['id'] = $dbLessonId * 1000 + $qIdx + 1; // Unique, deterministic question IDs
                }
                unset($q); // break reference
            }

            $mappedLessons[] = $lesson;
        }

        return $mappedLessons;
    }
}
