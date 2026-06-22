ts = '2026-02-16 07:11:08'
rows = []
lid = 1

def L(cid, title, content, sort):
    global lid
    c = content.replace("'", "''")
    rows.append(f"({lid},{cid},'{title}','{c}',{sort},1,'{ts}')")
    lid += 1


# ── COURSE 1: Graphic Design (id=1) ──────────────────────────────────────────
L(1,"Introduction to Graphic Design","""## What is Graphic Design?

Graphic design is the art of communicating ideas through visual elements — typography, colour, imagery, and layout. It appears in logos, posters, websites, packaging, and every piece of visual communication around us.

## The 6 Principles of Design

**1. Balance** — Distribute visual weight evenly. Symmetrical balance feels formal; asymmetrical balance feels dynamic.
**2. Contrast** — Use opposing elements (dark/light, large/small, bold/thin) to create visual interest and hierarchy.
**3. Alignment** — Every element should have a visual connection to something else on the page.
**4. Repetition** — Repeat visual elements (colours, fonts, shapes) to create consistency and unity.
**5. Proximity** — Group related items together. Unrelated items should be separated.
**6. White Space** — Intentional empty space gives designs room to breathe and directs the eye.

## Tools of the Trade

- **Adobe Illustrator** — Vector graphics, logos, icons
- **Adobe Photoshop** — Photo editing, raster graphics, compositing
- **Adobe InDesign** — Multi-page layouts, brochures, books
- **Canva** — Quick designs, social media, presentations

## Raster vs Vector

**Raster** images (JPG, PNG) are made of pixels. They lose quality when scaled up. Use for photos.
**Vector** images (SVG, AI, EPS) are made of mathematical paths. They scale infinitely. Use for logos.

## Practical Task

Open Canva (free at canva.com). Create a simple A4 event poster. Apply at least 4 of the 6 design principles. Export as PDF and review your work against each principle.

## Self-Check
1. Name the 6 principles of design and give one example of each.
2. What is the difference between raster and vector graphics?
3. Which tool would you use to design a company logo and why?""",1)

L(1,"Colour Theory & Typography","""## Colour Theory

Colour is one of the most powerful tools in design. Understanding how colours interact helps you create harmonious, effective designs.

### The Colour Wheel
- **Primary**: Red, Blue, Yellow
- **Secondary**: Orange, Green, Violet
- **Tertiary**: Red-orange, Yellow-green, etc.

### Colour Harmonies
- **Complementary**: Opposite on the wheel (blue & orange). High contrast.
- **Analogous**: Adjacent colours (blue, blue-green, green). Harmonious.
- **Triadic**: Three evenly spaced colours. Vibrant yet balanced.
- **Monochromatic**: Tints and shades of one colour. Elegant and cohesive.

### Colour Psychology
- Red: Energy, urgency, passion
- Blue: Trust, calm, professionalism
- Green: Growth, health, nature
- Yellow: Optimism, warmth, attention
- Black: Elegance, power, sophistication

## Typography

Typography is the art of arranging type to make written language legible, readable, and visually appealing.

### Font Categories
- **Serif** (Times New Roman, Georgia): Traditional, formal, trustworthy
- **Sans-serif** (Inter, Arial): Modern, clean, digital-friendly
- **Script**: Elegant, personal, decorative — use sparingly
- **Display**: Bold, expressive — headlines only

### Typography Rules
1. Limit to 2-3 fonts per design
2. Establish clear hierarchy: Heading > Subheading > Body > Caption
3. Ensure sufficient contrast between text and background
4. Use line spacing of 1.4-1.6x the font size for body text

## Practical Task

Design a business card (85mm x 55mm) for a fictional professional. Choose a complementary colour palette. Use one serif and one sans-serif font. Apply proper typographic hierarchy.

## Self-Check
1. What are complementary colours? Give a real-world brand example.
2. Why should you limit fonts in a design?
3. What does colour psychology mean for brand identity?""",2)

L(1,"Logo Design & Branding","""## What is a Brand?

A brand is the complete identity of a business — how it looks, sounds, and feels to its audience. It includes the logo, colours, typography, tone of voice, and core values.

## The 5 Qualities of a Great Logo

1. **Simple** — Works at any size, instantly recognisable
2. **Memorable** — Leaves a lasting impression
3. **Timeless** — Avoids trends that date quickly
4. **Versatile** — Works in colour, black & white, large and small
5. **Appropriate** — Fits the industry and target audience

## Logo Types
- **Wordmark**: Styled company name (Google, Coca-Cola)
- **Lettermark**: Initials only (IBM, HP)
- **Icon/Symbol**: Standalone graphic (Apple, Nike swoosh)
- **Combination mark**: Icon + wordmark (Adidas, Burger King)
- **Emblem**: Text inside a symbol (Starbucks, Harley-Davidson)

## The Logo Design Process
1. Brief — Understand the client, audience, and goals
2. Research — Study competitors and industry trends
3. Sketch — Generate 15-20 rough concepts on paper
4. Refine — Select 3 strongest and develop digitally
5. Present — Show options in context with rationale
6. Deliver — SVG, AI, EPS, PNG, PDF in colour and black & white

## Practical Task

Design a logo for a fictional tech startup called 'NovaByte'. Create 3 concepts on paper, then develop the strongest in Illustrator or Canva. Present it on white and dark backgrounds.

## Self-Check
1. What are the 5 qualities of a great logo?
2. What is the difference between a wordmark and a combination mark?
3. Why must logos be delivered in vector format?""",3)

L(1,"Print Design & Layout","""## Print vs Digital Design

Print design requires understanding physical production constraints. Getting these wrong results in costly reprints.

## Essential Print Concepts

**Bleed**: Extra artwork (3mm) beyond the trim edge. Prevents white borders after cutting.
**Trim**: The final cut size of the printed piece.
**Safe Zone**: Keep all important content at least 5mm inside the trim line.
**CMYK**: Cyan, Magenta, Yellow, Key (Black) — the colour model used in printing.
**DPI**: Dots Per Inch — minimum 300 DPI for sharp print quality.

## Common Print Products & Sizes
- Business card: 85mm x 55mm
- A4 flyer: 210mm x 297mm
- Tri-fold brochure: 99mm x 210mm per panel
- Pull-up banner: 850mm x 2000mm

## Layout & Grid Systems

Grids provide structure and consistency:
- **Column grid**: Used in magazines and newspapers
- **Modular grid**: Rows and columns for complex layouts
- **Baseline grid**: Aligns text across columns

## File Preparation Checklist
- Document set to CMYK colour mode
- Resolution 300 DPI minimum
- Bleed set to 3mm on all sides
- All fonts embedded or outlined
- Export as PDF/X-1a for print

## Practical Task

Design a tri-fold brochure for a fictional restaurant. Set up the document with correct bleed and safe zones. Include a menu section, about section, and contact details. Export as print-ready PDF.

## Self-Check
1. What is bleed and why is it important in print design?
2. What colour mode should print designs use?
3. What is the minimum DPI for print quality?""",4)

L(1,"Digital & Social Media Design","""## Designing for Digital Platforms

Digital design differs from print: screens use RGB colour, resolution is in pixels, and designs must be optimised for fast loading and mobile viewing.

## Key Social Media Dimensions (2025)

- Instagram Square post: 1080 x 1080px
- Instagram Portrait post: 1080 x 1350px
- Instagram Story/Reel: 1080 x 1920px
- Facebook Cover photo: 820 x 312px
- Twitter/X Header: 1500 x 500px
- LinkedIn Banner: 1584 x 396px
- YouTube Thumbnail: 1280 x 720px

## Design for Engagement

1. Mobile-first: Most users view on phones — use large, readable text
2. 3-second rule: Your message must be clear within 3 seconds
3. Brand consistency: Use your brand colours and fonts on every post
4. Clear CTA: Every post should have one clear call-to-action
5. File optimisation: Use WebP or compressed PNG/JPG for fast loading

## Content Types
- Static posts: Single image or graphic
- Carousel posts: Multiple swipeable images (high engagement)
- Stories: Vertical, ephemeral, interactive
- Reels/Short video: Highest organic reach on most platforms
- Infographics: Data visualisation, highly shareable

## Practical Task

Create a 3-post social media campaign for a fictional product launch. Design for Instagram (1080x1080), Facebook (1200x630), and an Instagram Story (1080x1920). Maintain consistent branding across all three.

## Self-Check
1. What colour mode do digital designs use?
2. What are the correct Instagram square post dimensions?
3. Why is mobile-first design important for social media?""",5)

L(1,"Photo Editing & Retouching","""## Adobe Photoshop Fundamentals

Photoshop is the industry standard for photo editing, compositing, and digital art.

## Non-Destructive Workflow

Always edit non-destructively — preserve the original image so you can undo any change:
- Use Adjustment Layers instead of direct adjustments
- Use Layer Masks instead of erasing
- Use Smart Objects to preserve original image data
- Work with Layers — never flatten until final export

## Essential Tools

- Crop Tool: Resize and reframe images
- Quick Selection / Magic Wand: Select areas by colour/tone
- Pen Tool: Precise path-based selections
- Healing Brush: Remove blemishes, blend with surroundings
- Clone Stamp: Copy pixels from one area to another
- Adjustment Layers: Non-destructive colour/tone corrections
- Camera Raw Filter: Professional RAW photo processing

## Colour Correction Workflow

1. Open in Camera Raw — fix white balance and exposure
2. Check histogram — identify clipping in highlights/shadows
3. Apply Curves adjustment layer — fine-tune contrast
4. Adjust Hue/Saturation — correct specific colour ranges
5. Add Vibrance (not Saturation) for natural colour boost
6. Sharpen using Smart Sharpen or Unsharp Mask
7. Export: JPG 72 DPI for web, TIFF 300 DPI for print

## Practical Task

Take a portrait photo (your own or a free stock image from Unsplash). Perform: skin smoothing with Healing Brush, background removal with Select Subject, colour grading with Curves, and add a text overlay. Export as JPG for web.

## Self-Check
1. What is a non-destructive editing workflow and why does it matter?
2. What is the difference between Vibrance and Saturation?
3. When should you use a Layer Mask instead of the Eraser tool?""",6)

L(1,"Portfolio & Client Work","""## Building a Design Portfolio

Your portfolio is your most important marketing tool as a designer. It should showcase your best work, demonstrate range, and tell your design story.

## What to Include
- 6-12 of your strongest, most recent projects
- A variety of work types (logo, print, digital, branding)
- Case studies showing your process: Brief > Research > Concept > Final
- A clear About page with your skills and background
- Contact information and links to social profiles

## Portfolio Platforms
- Behance: Industry standard, free, large community
- Dribbble: High-quality showcase, invite-based
- Adobe Portfolio: Included with Creative Cloud
- Personal website: Full control, best for SEO

## Writing a Case Study

Structure:
1. The Challenge — What problem were you solving?
2. The Process — Research, sketches, iterations
3. The Solution — Final design with rationale
4. The Result — Outcome, client feedback, metrics

## Working with Clients

1. Discovery call — Understand goals, audience, budget, timeline
2. Proposal & contract — Scope of work, payment terms, revision policy
3. Design brief — Written document confirming all requirements
4. Feedback rounds — Typically 2-3 rounds of revisions
5. Final delivery — All agreed file formats, organised and labelled

## Pricing Your Work
- Research market rates in your region
- Price by project value, not by hour
- Include a kill fee (25-50%) in contracts
- Never start work without a deposit (30-50%)

## Practical Task

Create a Behance project for one of your designs from this course. Write a full case study with the 4-part structure above. Include process images alongside the final design.

## Self-Check
1. How many projects should a beginner portfolio contain?
2. What is a kill fee and why is it important?
3. What are the 4 parts of a design case study?""",7)

L(1,"Capstone: Brand Identity Project","""## Final Project Brief

You will create a complete brand identity for a fictional business of your choice. This project demonstrates everything you have learned in this course.

## Deliverables

### 1. Brand Strategy (written, 1 page)
- Business name and tagline
- Target audience profile
- Brand personality (3-5 adjectives)
- Competitor analysis (2-3 competitors)

### 2. Visual Identity
- Primary logo (full colour, vector)
- Logo variations: reversed, black only, icon only
- Colour palette: 2 primary + 2 secondary colours with HEX, RGB, CMYK codes
- Typography system: heading font + body font with usage examples

### 3. Brand Applications
- Business card (front and back, print-ready)
- Letterhead (A4)
- Social media profile image and cover photo
- One marketing material: flyer, poster, or brochure

### 4. Brand Guidelines Document (PDF)
- Logo usage rules: clear space, minimum size, incorrect usage
- Colour specifications: HEX, RGB, CMYK values
- Typography guidelines: font names, sizes, weights, usage
- Tone of voice: 3-5 sentences describing how the brand communicates

## Evaluation Criteria
- Concept strength and originality (25%)
- Application of design principles (25%)
- Consistency across all brand touchpoints (20%)
- Quality of execution and file preparation (20%)
- Clarity and completeness of brand guidelines (10%)

## Self-Check
1. Does your logo work in black and white at 20mm wide?
2. Is your colour palette accessible (minimum 4.5:1 contrast ratio)?
3. Would a stranger understand your brand from the guidelines document alone?""",8)


# ── COURSE 3: Web Design (id=3) ───────────────────────────────────────────────
L(3,"Foundations of Web Design","""## What is Web Design?

Web design is the process of planning, conceptualising, and arranging content online. It combines visual design, user experience (UX), and technical knowledge to create websites that are both beautiful and functional.

## Web Design vs Web Development

**Web Design**: Visual layout, colour, typography, user experience — the look and feel.
**Web Development**: Code that makes the design work — HTML, CSS, JavaScript, PHP.

A web designer creates the blueprint; a developer builds it.

## Core Web Design Principles

1. **Visual Hierarchy**: Guide the user's eye to the most important content first
2. **Consistency**: Use the same colours, fonts, and spacing throughout
3. **Simplicity**: Remove everything that doesn't serve the user
4. **Accessibility**: Design for all users, including those with disabilities
5. **Mobile-First**: Design for small screens first, then scale up
6. **Performance**: Fast-loading pages improve user experience and SEO

## Anatomy of a Web Page

- **Header**: Logo, navigation, call-to-action
- **Hero section**: Main headline, subheading, primary CTA
- **Features/Benefits**: What the product/service offers
- **Social proof**: Testimonials, logos, statistics
- **Footer**: Links, contact info, legal

## Tools for Web Design

- **Figma**: Industry standard for UI/UX design (free tier available)
- **Adobe XD**: Adobe's UI design tool
- **Sketch**: Mac-only, popular in agencies
- **Webflow**: Visual web design with real HTML/CSS output

## Practical Task

Sketch a wireframe (on paper or in Figma) for a 5-page website: Home, About, Services, Portfolio, Contact. Define the layout and content for each page. No colours or images yet — just structure.

## Self-Check
1. What is the difference between web design and web development?
2. Name the 6 core web design principles.
3. What are the main sections of a typical web page?""",1)

L(3,"UI Design Fundamentals","""## What is UI Design?

User Interface (UI) design is the process of designing the visual elements that users interact with — buttons, forms, navigation, icons, and layouts.

## UI Design Components

### Buttons
- Use clear, action-oriented labels: 'Get Started', 'Download Now', 'Learn More'
- Primary button: Filled, high contrast — for the main action
- Secondary button: Outlined or ghost — for secondary actions
- Disabled state: Reduced opacity, no pointer cursor
- Minimum touch target: 44x44px for mobile

### Forms
- Label every input field clearly
- Show placeholder text as an example, not a replacement for labels
- Validate inline (show errors as the user types, not only on submit)
- Group related fields together
- Use appropriate input types: email, tel, date, number

### Navigation
- Keep navigation items to 5-7 maximum
- Highlight the current page/section
- Mobile: Use a hamburger menu or bottom navigation bar
- Breadcrumbs for deep navigation structures

### Icons
- Use universally understood icons (hamburger menu, search magnifier, cart)
- Always pair icons with text labels for clarity
- Maintain consistent icon style throughout (outline vs filled)

## Spacing & Layout

Use a consistent spacing scale (multiples of 4 or 8px):
- 4px: Tight spacing (between icon and label)
- 8px: Small spacing (between form elements)
- 16px: Medium spacing (between sections within a card)
- 24px: Large spacing (between cards)
- 48px: Section spacing

## Practical Task

Design a sign-up form in Figma. Include: name, email, password, confirm password, and a submit button. Apply proper labels, spacing, and validation states (default, focus, error, success).

## Self-Check
1. What is the minimum touch target size for mobile buttons?
2. Why should placeholder text not replace form labels?
3. What spacing scale is commonly used in UI design?""",2)

L(3,"UX Design & User Research","""## What is UX Design?

User Experience (UX) design is the process of creating products that provide meaningful, relevant, and enjoyable experiences to users. It focuses on the entire journey — from first awareness to long-term use.

## The UX Design Process

1. **Research** — Understand users, their goals, and pain points
2. **Define** — Synthesise research into clear problem statements
3. **Ideate** — Generate many possible solutions
4. **Prototype** — Build low-fidelity representations of solutions
5. **Test** — Validate with real users and iterate

## User Research Methods

**Qualitative** (understanding why):
- User interviews: 1-on-1 conversations about goals and frustrations
- Contextual inquiry: Observe users in their natural environment
- Usability testing: Watch users attempt tasks on your product

**Quantitative** (understanding how many):
- Surveys: Collect data from many users at once
- Analytics: Track clicks, scroll depth, conversion rates
- A/B testing: Compare two versions to see which performs better

## User Personas

A persona is a fictional representation of your target user based on research:
- Name and photo (makes them feel real)
- Demographics: Age, location, occupation
- Goals: What they want to achieve
- Frustrations: What gets in their way
- Behaviours: How they use technology

## Information Architecture

IA is the organisation and structure of content:
- **Card sorting**: Users group content into categories they find logical
- **Tree testing**: Test navigation structure without visual design
- **Sitemap**: Visual diagram of all pages and their relationships

## Practical Task

Conduct 3 user interviews about a website or app you use regularly. Ask about goals, frustrations, and workarounds. Create one user persona based on your findings. Build a sitemap for a 10-page website.

## Self-Check
1. What are the 5 stages of the UX design process?
2. What is the difference between qualitative and quantitative research?
3. What is a user persona and what does it contain?""",3)

L(3,"Responsive Design & CSS Layouts","""## What is Responsive Design?

Responsive design means a website adapts its layout and content to fit any screen size — from a 320px mobile phone to a 2560px desktop monitor.

## Breakpoints

Breakpoints are the screen widths at which the layout changes:
- Mobile: 0-767px
- Tablet: 768-1023px
- Desktop: 1024-1279px
- Large desktop: 1280px+

## CSS Flexbox

Flexbox is a one-dimensional layout system (row or column):

```css
.container {
  display: flex;
  justify-content: space-between; /* horizontal alignment */
  align-items: center;            /* vertical alignment */
  gap: 16px;
}
```

Key properties:
- `flex-direction`: row | column
- `justify-content`: flex-start | center | space-between | space-around
- `align-items`: flex-start | center | flex-end | stretch
- `flex-wrap`: wrap | nowrap
- `flex`: shorthand for flex-grow, flex-shrink, flex-basis

## CSS Grid

Grid is a two-dimensional layout system (rows AND columns):

```css
.grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}
```

Key properties:
- `grid-template-columns`: Define column widths
- `grid-template-rows`: Define row heights
- `grid-column`: Span across columns
- `grid-row`: Span across rows

## Mobile-First CSS

Write styles for mobile first, then add media queries for larger screens:

```css
/* Mobile (default) */
.card { width: 100%; }

/* Tablet and up */
@media (min-width: 768px) {
  .card { width: 50%; }
}

/* Desktop and up */
@media (min-width: 1024px) {
  .card { width: 33.333%; }
}
```

## Practical Task

Build a responsive 3-column card grid using CSS Grid. On mobile it should be 1 column, on tablet 2 columns, on desktop 3 columns. Each card should have an image, title, description, and button.

## Self-Check
1. What are the standard breakpoints for responsive design?
2. What is the difference between Flexbox and CSS Grid?
3. Why do we write mobile-first CSS?""",4)

L(3,"Typography & Colour for the Web","""## Web Typography

Typography on the web has unique considerations: font loading performance, screen rendering, and responsive sizing.

## Web-Safe Fonts vs Web Fonts

**Web-safe fonts**: Pre-installed on most devices (Arial, Georgia, Times New Roman). No loading required.
**Web fonts**: Custom fonts loaded from a server (Google Fonts, Adobe Fonts, self-hosted).

Google Fonts is free and easy to use:
```html
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
```

## Fluid Typography

Use CSS clamp() for font sizes that scale smoothly between breakpoints:

```css
h1 {
  font-size: clamp(1.75rem, 4vw, 3rem);
}
```

This means: minimum 1.75rem, preferred 4vw, maximum 3rem.

## CSS Custom Properties for Typography

```css
:root {
  --font-body: 'Inter', sans-serif;
  --text-xs:   0.75rem;
  --text-sm:   0.875rem;
  --text-base: 1rem;
  --text-lg:   1.125rem;
  --text-xl:   1.25rem;
  --text-2xl:  1.5rem;
  --text-3xl:  1.875rem;
  --text-4xl:  2.25rem;
}
```

## Colour for the Web

### CSS Custom Properties for Colour

```css
:root {
  --color-primary:    #4f46e5;
  --color-secondary:  #06b6d4;
  --color-success:    #10b981;
  --color-danger:     #ef4444;
  --color-text:       #0f172a;
  --color-muted:      #64748b;
  --color-bg:         #f8fafc;
  --color-border:     #e2e8f0;
}
```

### Colour Accessibility
- Normal text: minimum 4.5:1 contrast ratio (WCAG AA)
- Large text (18px+ or 14px+ bold): minimum 3:1
- UI components and graphics: minimum 3:1
- Test with: WebAIM Contrast Checker, browser DevTools

## Practical Task

Create a CSS design system file (variables.css) for a fictional brand. Define typography scale, colour palette, spacing scale, and border radius values using CSS custom properties. Apply them to a simple landing page.

## Self-Check
1. What is the difference between web-safe fonts and web fonts?
2. How does CSS clamp() work for fluid typography?
3. What is the WCAG AA contrast ratio requirement for normal text?""",5)

L(3,"Web Design with Figma","""## Why Figma?

Figma is the industry-standard tool for UI/UX design. It is browser-based, collaborative, and free for individuals. Teams can design, prototype, and hand off to developers — all in one tool.

## Figma Fundamentals

### Frames
Frames are the containers for your designs. Create frames at standard device sizes:
- Mobile: 390 x 844px (iPhone 14)
- Tablet: 768 x 1024px (iPad)
- Desktop: 1440 x 900px

### Components
Components are reusable design elements. Create a component once, use it everywhere. When you update the master component, all instances update automatically.

Use components for: buttons, cards, navigation bars, form inputs, icons.

### Auto Layout
Auto Layout makes frames resize automatically based on their content — like CSS Flexbox.

Properties:
- Direction: Horizontal or Vertical
- Spacing: Gap between items
- Padding: Space inside the frame
- Resizing: Fixed, Hug contents, Fill container

### Styles
Styles save reusable values for colours, typography, and effects:
- Colour styles: Brand colours, semantic colours
- Text styles: Heading 1, Heading 2, Body, Caption
- Effect styles: Drop shadows, blurs

## Prototyping in Figma

Connect frames with interactions to create clickable prototypes:
1. Select an element (button, link)
2. In the Prototype panel, drag the connection to the destination frame
3. Set the trigger (On Click) and animation (Smart Animate)
4. Press Play to preview

## Developer Handoff

Figma's Inspect panel shows developers the exact CSS values for any element: font size, colour, spacing, border radius. Export assets directly from Figma as PNG, SVG, or PDF.

## Practical Task

Design a complete mobile app screen set in Figma (5 screens): Splash, Onboarding, Home, Profile, Settings. Use components for the navigation bar and buttons. Create a clickable prototype connecting all screens.

## Self-Check
1. What is a Figma component and why is it useful?
2. How does Auto Layout relate to CSS Flexbox?
3. What does the Inspect panel provide for developers?""",6)

L(3,"Website Performance & SEO Basics","""## Why Performance Matters

A 1-second delay in page load time can reduce conversions by 7%. Google uses page speed as a ranking factor. Users abandon pages that take more than 3 seconds to load.

## Core Web Vitals

Google's key performance metrics:
- **LCP (Largest Contentful Paint)**: Time for the main content to load. Target: under 2.5 seconds.
- **FID (First Input Delay)**: Time from first interaction to browser response. Target: under 100ms.
- **CLS (Cumulative Layout Shift)**: Visual stability — how much the page shifts during loading. Target: under 0.1.

## Image Optimisation

Images are typically the largest files on a web page:
- Use **WebP** format (30-50% smaller than JPG/PNG with same quality)
- Compress images with TinyPNG, Squoosh, or ImageOptim
- Use `width` and `height` attributes to prevent layout shift
- Use `loading="lazy"` for images below the fold
- Use responsive images with `srcset` for different screen sizes

## Performance Best Practices

1. Minify CSS, JavaScript, and HTML
2. Enable GZIP or Brotli compression on the server
3. Use a Content Delivery Network (CDN) for static assets
4. Reduce HTTP requests (combine files, use CSS sprites)
5. Defer non-critical JavaScript
6. Use browser caching with appropriate cache headers

## SEO Fundamentals for Web Designers

**On-page SEO elements designers control:**
- `<title>` tag: 50-60 characters, include primary keyword
- Meta description: 150-160 characters, compelling summary
- Heading hierarchy: One H1 per page, logical H2/H3 structure
- Alt text on images: Descriptive, keyword-relevant
- URL structure: Short, descriptive, hyphen-separated
- Internal linking: Connect related pages

## Practical Task

Audit a website using Google PageSpeed Insights (pagespeed.web.dev). Identify the top 3 performance issues. Write a brief report with specific recommendations to fix each issue.

## Self-Check
1. What are the three Core Web Vitals and their targets?
2. Why should you use WebP format for images?
3. What SEO elements does a web designer directly control?""",7)

L(3,"Capstone: Full Website Design","""## Final Project Brief

You will design a complete, responsive website for a fictional business. This project demonstrates your web design, UX, and visual design skills.

## Scenario

Design a website for 'Luminary Studio' — a fictional creative agency based in Lagos, Nigeria. They offer branding, web design, and digital marketing services to SMEs across Africa.

## Deliverables

### 1. UX Research & Planning
- User persona (1 persona for the target client)
- Sitemap (all pages and their relationships)
- Wireframes for all pages (low-fidelity, in Figma or on paper)

### 2. Visual Design (in Figma)
Design all pages at desktop (1440px) and mobile (390px):
- Home page: Hero, services overview, portfolio preview, testimonials, CTA
- About page: Team, story, values
- Services page: Detailed service descriptions with pricing
- Portfolio page: Project grid with filter by category
- Contact page: Contact form, map, social links

### 3. Design System
- Colour palette (CSS custom properties)
- Typography scale
- Component library: buttons, cards, form inputs, navigation

### 4. Prototype
- Clickable Figma prototype connecting all pages
- Mobile and desktop versions

## Evaluation Criteria
- UX thinking and user-centred approach (20%)
- Visual design quality and consistency (30%)
- Responsive design (mobile + desktop) (20%)
- Component system completeness (15%)
- Prototype functionality (15%)

## Self-Check
1. Does every page have a clear primary call-to-action?
2. Is the design consistent across all pages and both breakpoints?
3. Would a developer be able to build this from your Figma file?""",8)


# ── COURSE 4: Web Development (id=4) ─────────────────────────────────────────
L(4,"HTML5 Fundamentals","""## What is HTML?

HTML (HyperText Markup Language) is the standard language for creating web pages. It defines the structure and content of a page using elements represented by tags.

## Document Structure

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Page Title</title>
</head>
<body>
  <!-- Content goes here -->
</body>
</html>
```

## Semantic HTML5 Elements

Semantic elements describe their meaning to both the browser and the developer:

```html
<header>   <!-- Site header, logo, navigation -->
<nav>      <!-- Navigation links -->
<main>     <!-- Main content of the page -->
<article>  <!-- Self-contained content (blog post, news article) -->
<section>  <!-- Thematic grouping of content -->
<aside>    <!-- Sidebar, related content -->
<footer>   <!-- Site footer -->
<figure>   <!-- Image with caption -->
<figcaption> <!-- Caption for a figure -->
```

## Common HTML Elements

```html
<!-- Headings -->
<h1>Main Heading</h1>
<h2>Subheading</h2>

<!-- Text -->
<p>Paragraph text</p>
<strong>Bold/important</strong>
<em>Italic/emphasis</em>

<!-- Links -->
<a href="https://example.com" target="_blank">Link text</a>

<!-- Images -->
<img src="photo.jpg" alt="Description of image" width="800" height="600">

<!-- Lists -->
<ul><li>Unordered item</li></ul>
<ol><li>Ordered item</li></ol>

<!-- Tables -->
<table>
  <thead><tr><th>Name</th><th>Age</th></tr></thead>
  <tbody><tr><td>John</td><td>25</td></tr></tbody>
</table>

<!-- Forms -->
<form action="/submit" method="POST">
  <input type="text" name="username" placeholder="Enter username" required>
  <input type="email" name="email" required>
  <button type="submit">Submit</button>
</form>
```

## Practical Task

Build a personal profile page using only HTML (no CSS yet). Include: a header with your name, a navigation bar, an about section, a skills list, a projects table, and a contact form. Use semantic elements throughout.

## Self-Check
1. What is the difference between semantic and non-semantic HTML elements?
2. What does the `alt` attribute on an image do?
3. What is the difference between `<strong>` and `<b>`?""",1)

L(4,"CSS3 & Modern Styling","""## CSS Fundamentals

CSS (Cascading Style Sheets) controls the visual presentation of HTML elements.

## The Box Model

Every HTML element is a rectangular box:
- **Content**: The actual text or image
- **Padding**: Space between content and border
- **Border**: The border around the padding
- **Margin**: Space outside the border

```css
.box {
  width: 300px;
  padding: 20px;
  border: 2px solid #333;
  margin: 16px;
  box-sizing: border-box; /* padding included in width */
}
```

## CSS Selectors

```css
/* Element selector */
p { color: #333; }

/* Class selector */
.card { background: white; }

/* ID selector */
#header { position: sticky; }

/* Descendant selector */
.nav a { color: white; }

/* Pseudo-class */
a:hover { color: blue; }
button:focus { outline: 2px solid blue; }

/* Pseudo-element */
p::first-line { font-weight: bold; }
```

## CSS Variables (Custom Properties)

```css
:root {
  --color-primary: #4f46e5;
  --spacing-md: 16px;
  --radius: 8px;
}

.button {
  background: var(--color-primary);
  padding: var(--spacing-md);
  border-radius: var(--radius);
}
```

## Flexbox Layout

```css
.container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}
```

## CSS Grid Layout

```css
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}
```

## Transitions & Animations

```css
.button {
  transition: background 0.2s ease, transform 0.1s ease;
}
.button:hover {
  background: #3730a3;
  transform: translateY(-2px);
}
```

## Practical Task

Style the HTML profile page from Lesson 1. Create a responsive layout using Flexbox and Grid. Add a colour scheme using CSS variables. Include hover effects on links and buttons. Make it fully responsive for mobile.

## Self-Check
1. Explain the CSS box model and its four components.
2. What is the difference between `margin` and `padding`?
3. When would you use Flexbox vs CSS Grid?""",2)

L(4,"JavaScript Essentials","""## What is JavaScript?

JavaScript is the programming language of the web. It makes web pages interactive — responding to user actions, updating content dynamically, and communicating with servers.

## Variables & Data Types

```javascript
// Variables
let name = 'John';        // Can be reassigned
const age = 25;           // Cannot be reassigned
var old = 'avoid this';   // Old way, avoid

// Data types
let str = 'Hello';        // String
let num = 42;             // Number
let bool = true;          // Boolean
let arr = [1, 2, 3];      // Array
let obj = { key: 'val' }; // Object
let nothing = null;       // Null
let undef;                // Undefined
```

## Functions

```javascript
// Function declaration
function greet(name) {
  return `Hello, ${name}!`;
}

// Arrow function
const greet = (name) => `Hello, ${name}!`;

// Default parameters
function add(a, b = 0) {
  return a + b;
}
```

## DOM Manipulation

```javascript
// Select elements
const btn = document.getElementById('myBtn');
const cards = document.querySelectorAll('.card');

// Change content
btn.textContent = 'Click me';
btn.innerHTML = '<strong>Click me</strong>';

// Change styles
btn.style.backgroundColor = '#4f46e5';
btn.classList.add('active');
btn.classList.toggle('hidden');

// Event listeners
btn.addEventListener('click', function() {
  alert('Button clicked!');
});

// Create and append elements
const div = document.createElement('div');
div.className = 'card';
div.textContent = 'New card';
document.body.appendChild(div);
```

## Fetch API (AJAX)

```javascript
// GET request
fetch('/api/users')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error(error));

// POST request
fetch('/api/users', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ name: 'John', email: 'john@example.com' })
})
  .then(res => res.json())
  .then(data => console.log(data));
```

## Practical Task

Build an interactive to-do list using HTML, CSS, and JavaScript. Features: add tasks, mark as complete (toggle class), delete tasks, show task count. Store tasks in localStorage so they persist on page refresh.

## Self-Check
1. What is the difference between `let`, `const`, and `var`?
2. How do you select an element by class name in JavaScript?
3. What does the Fetch API do?""",3)

L(4,"PHP Backend Development","""## What is PHP?

PHP (Hypertext Preprocessor) is a server-side scripting language designed for web development. It runs on the server and generates HTML that is sent to the browser.

## PHP Basics

```php
<?php
// Variables
$name = 'John';
$age = 25;
$price = 99.99;
$active = true;

// String interpolation
echo "Hello, $name! You are $age years old.";

// Arrays
$fruits = ['apple', 'banana', 'mango'];
echo $fruits[0]; // apple

// Associative arrays
$user = [
    'name' => 'John',
    'email' => 'john@example.com',
    'age' => 25
];
echo $user['name']; // John
```

## Control Structures

```php
// If/else
if ($age >= 18) {
    echo 'Adult';
} elseif ($age >= 13) {
    echo 'Teenager';
} else {
    echo 'Child';
}

// Loops
foreach ($fruits as $fruit) {
    echo $fruit . '<br>';
}

for ($i = 0; $i < 10; $i++) {
    echo $i;
}
```

## Functions

```php
function formatMoney(float $amount): string {
    return '₦' . number_format($amount, 2);
}

echo formatMoney(15000); // ₦15,000.00
```

## Handling Forms

```php
// form.html
// <form method="POST" action="process.php">
//   <input type="text" name="username">
//   <button type="submit">Submit</button>
// </form>

// process.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    if ($username === '') {
        echo 'Username is required';
    } else {
        echo "Welcome, $username!";
    }
}
```

## Sessions

```php
session_start();

// Store data
$_SESSION['user_id'] = 42;
$_SESSION['username'] = 'john';

// Read data
$userId = $_SESSION['user_id'] ?? null;

// Destroy session (logout)
session_destroy();
```

## Practical Task

Build a simple contact form with PHP validation. Fields: name, email, message. Validate all fields server-side. Show success message on valid submission. Show specific error messages for each invalid field. Use sessions to persist form data on error.

## Self-Check
1. What is the difference between `$_GET` and `$_POST`?
2. Why should you always sanitise user input?
3. What is a PHP session and when would you use it?""",4)

L(4,"MySQL & Database Design","""## What is a Database?

A database is an organised collection of structured data. MySQL is a relational database management system (RDBMS) — data is stored in tables with rows and columns, and tables can be related to each other.

## SQL Fundamentals

```sql
-- Create a table
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert data
INSERT INTO users (name, email, password)
VALUES ('John Doe', 'john@example.com', 'hashed_password');

-- Select data
SELECT id, name, email FROM users WHERE id = 1;
SELECT * FROM users ORDER BY created_at DESC LIMIT 10;

-- Update data
UPDATE users SET name = 'Jane Doe' WHERE id = 1;

-- Delete data
DELETE FROM users WHERE id = 1;
```

## PHP PDO (Database Connection)

```php
$pdo = new PDO(
    'mysql:host=localhost;dbname=mydb;charset=utf8mb4',
    'root',
    'password',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Prepared statement (prevents SQL injection)
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
```

## Database Relationships

**One-to-Many**: One user has many posts
```sql
CREATE TABLE posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**Many-to-Many**: Students enrol in many courses; courses have many students
```sql
CREATE TABLE enrollments (
    student_id INT UNSIGNED,
    course_id INT UNSIGNED,
    PRIMARY KEY (student_id, course_id)
);
```

## Database Design Principles

1. **Normalisation**: Eliminate data redundancy (1NF, 2NF, 3NF)
2. **Primary keys**: Every table needs a unique identifier
3. **Foreign keys**: Enforce referential integrity between tables
4. **Indexes**: Speed up queries on frequently searched columns
5. **Prepared statements**: Always use them to prevent SQL injection

## Practical Task

Design a database for a simple blog. Tables: users, posts, categories, comments, post_categories. Write the CREATE TABLE statements with proper data types, constraints, and foreign keys. Insert sample data and write 5 SELECT queries.

## Self-Check
1. What is the difference between a primary key and a foreign key?
2. Why should you always use prepared statements?
3. What is database normalisation?""",5)

L(4,"Full Stack Project: Blog Application","""## Project Overview

You will build a complete blog application with user authentication, CRUD operations, and a clean UI. This integrates everything from HTML, CSS, JavaScript, PHP, and MySQL.

## Features to Build

### Authentication
- User registration with email and password
- Login with session management
- Logout
- Password hashing with password_hash()

### Blog Posts (CRUD)
- Create: Form to write and publish posts
- Read: List all posts, view single post
- Update: Edit existing posts (author only)
- Delete: Remove posts (author only)

### Additional Features
- Categories for posts
- Comment system
- Search functionality
- Pagination (10 posts per page)

## Project Structure

```
blog/
├── config/
│   └── db.php          # Database connection
├── includes/
│   ├── header.php      # Shared header HTML
│   ├── footer.php      # Shared footer HTML
│   └── helpers.php     # Utility functions
├── uploads/            # User-uploaded images
├── index.php           # Home page (list posts)
├── post.php            # Single post view
├── create.php          # Create new post
├── edit.php            # Edit post
├── delete.php          # Delete post handler
├── login.php           # Login form
├── register.php        # Registration form
├── logout.php          # Logout handler
└── search.php          # Search results
```

## Security Checklist

- [ ] All user input sanitised with htmlspecialchars()
- [ ] All database queries use prepared statements
- [ ] Passwords hashed with password_hash(PASSWORD_DEFAULT)
- [ ] CSRF tokens on all forms
- [ ] File uploads validated (type, size, extension)
- [ ] Session regenerated after login
- [ ] Error messages don't reveal system details

## Practical Task

Build the complete blog application following the structure above. Implement all CRUD operations, authentication, and at least one additional feature (categories, comments, or search). Deploy to a local XAMPP server.

## Self-Check
1. What is CSRF and how do you prevent it?
2. Why should you never store plain-text passwords?
3. What is the difference between authentication and authorisation?""",6)

L(4,"APIs & JavaScript Frameworks","""## What is an API?

An API (Application Programming Interface) is a set of rules that allows different software applications to communicate with each other. A REST API uses HTTP methods to perform operations on resources.

## REST API Concepts

HTTP Methods:
- **GET**: Retrieve data
- **POST**: Create new data
- **PUT/PATCH**: Update existing data
- **DELETE**: Remove data

HTTP Status Codes:
- 200 OK: Success
- 201 Created: Resource created
- 400 Bad Request: Invalid input
- 401 Unauthorized: Not authenticated
- 403 Forbidden: Not authorised
- 404 Not Found: Resource doesn't exist
- 500 Internal Server Error: Server-side error

## Building a REST API in PHP

```php
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method === 'GET' && $path === '/api/users') {
    $users = $pdo->query('SELECT id, name, email FROM users')->fetchAll();
    echo json_encode(['ok' => true, 'data' => $users]);
    exit;
}

if ($method === 'POST' && $path === '/api/users') {
    $body = json_decode(file_get_contents('php://input'), true);
    // validate and insert...
    echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}
```

## Introduction to Vue.js

Vue.js is a progressive JavaScript framework for building user interfaces:

```html
<div id="app">
  <input v-model="message" placeholder="Type something">
  <p>You typed: {{ message }}</p>
  <button @click="clearMessage">Clear</button>
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
Vue.createApp({
  data() {
    return { message: '' }
  },
  methods: {
    clearMessage() { this.message = '' }
  }
}).mount('#app')
</script>
```

## Practical Task

Build a simple task manager SPA (Single Page Application) using Vue.js that connects to a PHP REST API backend. Features: list tasks (GET), add task (POST), mark complete (PATCH), delete task (DELETE). Store data in MySQL.

## Self-Check
1. What are the 4 main HTTP methods and what does each do?
2. What does HTTP status code 401 mean?
3. What is the difference between a REST API and a traditional web page?""",7)

L(4,"Deployment & DevOps Basics","""## Taking Your Website Live

Deployment is the process of making your web application available on the internet. Understanding the basics of servers, hosting, and deployment is essential for every web developer.

## Hosting Options

| Type | Best For | Examples |
|---|---|---|
| Shared hosting | Small sites, beginners | Namecheap, Bluehost, cPanel hosts |
| VPS (Virtual Private Server) | Growing sites, more control | DigitalOcean, Linode, Vultr |
| Cloud hosting | Scalable, enterprise | AWS, Google Cloud, Azure |
| Platform as a Service | Easy deployment | Heroku, Railway, Render |
| Static hosting | HTML/CSS/JS only | Netlify, Vercel, GitHub Pages |

## Domain & DNS

1. Register a domain (Namecheap, GoDaddy, Google Domains)
2. Point DNS A record to your server IP address
3. Wait for DNS propagation (up to 48 hours)
4. Set up SSL certificate (HTTPS) — free with Let's Encrypt

## Deploying a PHP Application

### Via cPanel (Shared Hosting)
1. Upload files via File Manager or FTP (FileZilla)
2. Create MySQL database in cPanel
3. Import your SQL schema
4. Update config/db.php with production credentials
5. Set file permissions (755 for directories, 644 for files)

### Via SSH (VPS)
```bash
# Connect to server
ssh user@your-server-ip

# Install LAMP stack
sudo apt update
sudo apt install apache2 mysql-server php php-mysql

# Clone your project
git clone https://github.com/yourname/project.git /var/www/html/project

# Set permissions
sudo chown -R www-data:www-data /var/www/html/project
```

## Environment Variables

Never hardcode sensitive data (passwords, API keys) in your code:

```php
// .env file (never commit to git)
DB_HOST=localhost
DB_NAME=mydb
DB_USER=root
DB_PASS=secret

// config/db.php
$host = $_ENV['DB_HOST'] ?? 'localhost';
```

Add `.env` to your `.gitignore` file.

## Git & Version Control

```bash
git init                    # Initialise repository
git add .                   # Stage all changes
git commit -m "Add login"   # Commit with message
git push origin main        # Push to GitHub
git pull origin main        # Pull latest changes
```

## Practical Task

Deploy your blog application from Lesson 6 to a live server. Set up a domain, configure HTTPS with Let's Encrypt, and use environment variables for database credentials. Test all features on the live server.

## Self-Check
1. What is the difference between shared hosting and a VPS?
2. Why should you never commit your .env file to Git?
3. What is SSL/HTTPS and why is it required?""",8)


# ── COURSES 5-16: Remaining courses ──────────────────────────────────────────
# Course 5: PHP & MySQL Development
L(5,"PHP Environment Setup & Syntax","""## Setting Up Your PHP Development Environment

Before writing PHP, you need a local server environment.

## XAMPP Installation (Windows/Mac/Linux)

XAMPP bundles Apache, MySQL, and PHP in one installer:
1. Download from apachefriends.org
2. Install and launch XAMPP Control Panel
3. Start Apache and MySQL services
4. Place your PHP files in C:/xampp/htdocs/
5. Access via http://localhost/yourfolder/

## PHP Syntax Fundamentals

```php
<?php
// This is a comment
echo "Hello, World!";  // Output text

// Variables (always start with $)
$name = "Amara";
$age = 22;
$price = 4500.50;
$isActive = true;

// String operations
$fullName = "Amara" . " " . "Okafor";  // Concatenation
$upper = strtoupper($name);             // AMARA
$length = strlen($name);                // 5
$trimmed = trim("  hello  ");           // "hello"

// Number operations
$sum = 10 + 5;
$product = 10 * 5;
$remainder = 10 % 3;  // 1
$power = 2 ** 8;      // 256

// Type juggling
$num = "5" + 3;  // 8 (PHP converts string to number)
```

## Arrays

```php
// Indexed array
$colours = ["red", "green", "blue"];
echo $colours[0];  // red
$colours[] = "yellow";  // Append

// Associative array
$student = [
    "name" => "Amara",
    "age"  => 22,
    "gpa"  => 3.8
];
echo $student["name"];  // Amara

// Multidimensional array
$students = [
    ["name" => "Amara", "score" => 85],
    ["name" => "Chidi", "score" => 92],
];

// Array functions
$numbers = [3, 1, 4, 1, 5, 9, 2, 6];
sort($numbers);           // Sort ascending
$count = count($numbers); // 8
$sum = array_sum($numbers); // 31
$unique = array_unique($numbers); // Remove duplicates
```

## Control Flow

```php
// Match expression (PHP 8+)
$status = "active";
$label = match($status) {
    "active"    => "Active User",
    "suspended" => "Suspended",
    "inactive"  => "Inactive",
    default     => "Unknown"
};

// Null coalescing
$username = $_GET["user"] ?? "Guest";

// Ternary
$greeting = $age >= 18 ? "Adult" : "Minor";
```

## Practical Task

Write a PHP script that: accepts a student name and 5 test scores via a form, calculates the average, assigns a grade (A=90+, B=80+, C=70+, D=60+, F=below 60), and displays a formatted result card.

## Self-Check
1. What is the difference between `echo` and `print` in PHP?
2. How do you append an item to a PHP array?
3. What does the null coalescing operator (??) do?""",1)

L(5,"Object-Oriented PHP","""## Why Object-Oriented Programming?

OOP organises code into objects — self-contained units that combine data (properties) and behaviour (methods). It makes code more reusable, maintainable, and scalable.

## Classes & Objects

```php
class Student {
    // Properties
    public string $name;
    public string $email;
    private float $gpa;

    // Constructor
    public function __construct(string $name, string $email, float $gpa) {
        $this->name  = $name;
        $this->email = $email;
        $this->gpa   = $gpa;
    }

    // Methods
    public function getGrade(): string {
        return match(true) {
            $this->gpa >= 3.7 => 'First Class',
            $this->gpa >= 3.3 => 'Second Class Upper',
            $this->gpa >= 2.7 => 'Second Class Lower',
            default           => 'Pass',
        };
    }

    public function getGpa(): float {
        return $this->gpa;
    }
}

// Create an object
$student = new Student('Amara', 'amara@example.com', 3.8);
echo $student->name;         // Amara
echo $student->getGrade();   // First Class
```

## Inheritance

```php
class Person {
    public function __construct(
        public string $name,
        public string $email
    ) {}

    public function greet(): string {
        return "Hello, I am {$this->name}";
    }
}

class Instructor extends Person {
    public function __construct(
        string $name,
        string $email,
        public string $subject
    ) {
        parent::__construct($name, $email);
    }

    public function introduce(): string {
        return "{$this->greet()} and I teach {$this->subject}";
    }
}
```

## Interfaces & Abstract Classes

```php
interface Payable {
    public function calculateFee(): float;
    public function processPayment(float $amount): bool;
}

class CourseEnrollment implements Payable {
    public function calculateFee(): float {
        return 150000.00;
    }
    public function processPayment(float $amount): bool {
        // Payment logic here
        return $amount >= $this->calculateFee();
    }
}
```

## Practical Task

Build a simple library management system using OOP. Classes: Book (title, author, isbn, available), Member (name, email, borrowedBooks), Library (books array, members array). Methods: addBook(), registerMember(), borrowBook(), returnBook(), listAvailableBooks().

## Self-Check
1. What is the difference between `public`, `protected`, and `private` visibility?
2. What is the difference between an interface and an abstract class?
3. What does `$this` refer to inside a class method?""",2)

L(5,"Advanced MySQL & Query Optimisation","""## Advanced SQL Queries

### JOINs

```sql
-- INNER JOIN: Only matching rows from both tables
SELECT s.name, c.title, e.paid_amount
FROM lms_students s
INNER JOIN lms_enrollments e ON e.student_id = s.id
INNER JOIN lms_courses c ON c.id = e.course_id;

-- LEFT JOIN: All rows from left table, matching from right
SELECT s.name, COUNT(e.id) AS course_count
FROM lms_students s
LEFT JOIN lms_enrollments e ON e.student_id = s.id
GROUP BY s.id, s.name;

-- Subquery
SELECT * FROM lms_students
WHERE id IN (
    SELECT student_id FROM lms_enrollments
    WHERE status = 'paid'
);
```

### Aggregate Functions

```sql
SELECT
    course_id,
    COUNT(*) AS total_students,
    SUM(paid_amount) AS total_revenue,
    AVG(paid_amount) AS avg_payment,
    MAX(paid_amount) AS highest_payment
FROM lms_enrollments
GROUP BY course_id
HAVING COUNT(*) > 5
ORDER BY total_revenue DESC;
```

### Window Functions (MySQL 8+)

```sql
SELECT
    name,
    paid_amount,
    RANK() OVER (ORDER BY paid_amount DESC) AS payment_rank,
    SUM(paid_amount) OVER () AS total_all_payments
FROM lms_enrollments e
JOIN lms_students s ON s.id = e.student_id;
```

## Query Optimisation

### Indexes

```sql
-- Add index on frequently searched column
ALTER TABLE lms_students ADD INDEX idx_email (email);
ALTER TABLE lms_enrollments ADD INDEX idx_student (student_id);

-- Composite index for multi-column queries
ALTER TABLE lms_payments ADD INDEX idx_student_status (student_id, status);

-- Check if query uses indexes
EXPLAIN SELECT * FROM lms_students WHERE email = 'test@example.com';
```

### Optimisation Rules
1. Always index foreign key columns
2. Index columns used in WHERE, JOIN ON, and ORDER BY
3. Avoid SELECT * — specify only needed columns
4. Use LIMIT to restrict result sets
5. Avoid functions on indexed columns in WHERE clauses
6. Use prepared statements (also prevents SQL injection)

## Transactions

```sql
START TRANSACTION;

INSERT INTO lms_enrollments (student_id, course_id) VALUES (1, 5);
INSERT INTO lms_payments (student_id, enrollment_id, amount) VALUES (1, LAST_INSERT_ID(), 150000);

COMMIT;  -- Save both changes
-- ROLLBACK;  -- Undo both if something went wrong
```

## Practical Task

Write a stored procedure that enrols a student in a course: checks if already enrolled, creates the enrollment record, creates a pending payment record, and returns a success/error status. Test with sample data.

## Self-Check
1. What is the difference between INNER JOIN and LEFT JOIN?
2. Why should you add indexes to foreign key columns?
3. What is a database transaction and when would you use one?""",3)

L(5,"Authentication & Security","""## Web Application Security

Security is not optional. A single vulnerability can expose all your users' data. These are the most critical security practices for PHP developers.

## Password Security

```php
// NEVER store plain text passwords
// WRONG:
$password = $_POST['password'];
$sql = "INSERT INTO users (password) VALUES ('$password')";

// CORRECT: Hash with bcrypt
$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
// Stores something like: $2y$10$...

// Verify on login
if (password_verify($_POST['password'], $storedHash)) {
    // Login successful
}
```

## SQL Injection Prevention

```php
// VULNERABLE - Never do this:
$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = $id";
// Attacker sends: ?id=1 OR 1=1 -- (returns all users)

// SAFE - Always use prepared statements:
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);
$user = $stmt->fetch();
```

## XSS (Cross-Site Scripting) Prevention

```php
// VULNERABLE:
echo $_GET['name'];  // Attacker sends: <script>alert('hacked')</script>

// SAFE - Always escape output:
echo htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8');
```

## CSRF (Cross-Site Request Forgery) Prevention

```php
// Generate token on form load
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// In form:
// <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

// Verify on form submission:
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    http_response_code(419);
    exit('Invalid CSRF token');
}
```

## File Upload Security

```php
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
$maxSize = 3 * 1024 * 1024; // 3MB

$file = $_FILES['upload'];

// Check MIME type (not just extension)
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!in_array($mimeType, $allowedTypes)) {
    exit('Invalid file type');
}

if ($file['size'] > $maxSize) {
    exit('File too large');
}

// Generate safe filename
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$safeName = bin2hex(random_bytes(8)) . '.' . $ext;
move_uploaded_file($file['tmp_name'], 'uploads/' . $safeName);
```

## Practical Task

Audit the blog application from the Web Development course. Find and fix: any SQL injection vulnerabilities, any XSS vulnerabilities, missing CSRF protection, and insecure file uploads. Document each vulnerability found and the fix applied.

## Self-Check
1. Why should you never store plain-text passwords?
2. What is SQL injection and how do prepared statements prevent it?
3. What is the difference between XSS and CSRF attacks?""",4)

L(5,"RESTful API Development","""## Building a Professional REST API

A REST API allows your PHP backend to serve data to any frontend — web, mobile, or third-party applications.

## API Design Principles

### Resource Naming
- Use nouns, not verbs: `/api/users` not `/api/getUsers`
- Use plural nouns: `/api/courses` not `/api/course`
- Use hierarchy for relationships: `/api/courses/5/lessons`
- Use query parameters for filtering: `/api/courses?level=beginner&limit=10`

### HTTP Methods & Status Codes

| Method | Action | Success Code |
|---|---|---|
| GET | Retrieve resource(s) | 200 OK |
| POST | Create new resource | 201 Created |
| PUT | Replace entire resource | 200 OK |
| PATCH | Update part of resource | 200 OK |
| DELETE | Remove resource | 204 No Content |

## Building the API

```php
// api/index.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

// Route: GET /api/courses
if ($method === 'GET' && $segments[1] === 'courses') {
    $courses = $pdo->query("SELECT * FROM lms_courses WHERE is_active=1")->fetchAll();
    http_response_code(200);
    echo json_encode(['ok' => true, 'data' => $courses]);
    exit;
}

// Route: POST /api/courses
if ($method === 'POST' && $segments[1] === 'courses') {
    $body = json_decode(file_get_contents('php://input'), true);
    // validate...
    $stmt = $pdo->prepare("INSERT INTO lms_courses (title, price) VALUES (?,?)");
    $stmt->execute([$body['title'], $body['price']]);
    http_response_code(201);
    echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

// 404 fallback
http_response_code(404);
echo json_encode(['ok' => false, 'error' => 'Endpoint not found']);
```

## API Authentication with JWT

JSON Web Tokens (JWT) are a standard for API authentication:

1. User logs in with email/password
2. Server validates credentials and returns a JWT token
3. Client stores the token (localStorage or cookie)
4. Client sends token in every request: `Authorization: Bearer {token}`
5. Server validates the token on each request

## API Documentation

Good APIs have clear documentation. Use tools like:
- **Postman**: Test and document APIs
- **Swagger/OpenAPI**: Standard API documentation format
- **Insomnia**: Alternative to Postman

## Practical Task

Build a complete REST API for a course catalogue. Endpoints: GET /api/courses (list all), GET /api/courses/{id} (single course), POST /api/courses (create), PUT /api/courses/{id} (update), DELETE /api/courses/{id} (delete). Add JWT authentication. Test all endpoints in Postman.

## Self-Check
1. What is the difference between PUT and PATCH?
2. What HTTP status code should a successful POST return?
3. How does JWT authentication work?""",5)

L(5,"Email, File Handling & Cron Jobs","""## Sending Email with PHP

### PHPMailer (Recommended)

PHPMailer is the most popular PHP email library. Install via Composer:

```bash
composer require phpmailer/phpmailer
```

```php
use PHPMailer\\PHPMailer\\PHPMailer;

$mail = new PHPMailer(true);

// SMTP Configuration
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'your@gmail.com';
$mail->Password   = 'your-app-password';
$mail->SMTPSecure = 'tls';
$mail->Port       = 587;

// Email content
$mail->setFrom('noreply@yourdomain.com', 'Mirror LMS');
$mail->addAddress('student@example.com', 'Student Name');
$mail->Subject = 'Welcome to Mirror LMS';
$mail->isHTML(true);
$mail->Body = '<h1>Welcome!</h1><p>Your account is ready.</p>';

$mail->send();
```

## File Handling

```php
// Read a file
$content = file_get_contents('data.txt');

// Write to a file
file_put_contents('log.txt', "Error: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Check if file exists
if (file_exists('uploads/photo.jpg')) {
    // process file
}

// Get file info
$info = pathinfo('document.pdf');
echo $info['extension'];  // pdf
echo $info['filename'];   // document

// List files in directory
$files = glob('uploads/*.jpg');
foreach ($files as $file) {
    echo basename($file) . "\n";
}

// Delete a file
unlink('uploads/old-photo.jpg');
```

## CSV Import/Export

```php
// Export to CSV
$data = $pdo->query("SELECT name, email, course FROM lms_students")->fetchAll();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students.csv"');

$fp = fopen('php://output', 'w');
fputcsv($fp, ['Name', 'Email', 'Course']); // Header row
foreach ($data as $row) {
    fputcsv($fp, $row);
}
fclose($fp);

// Import from CSV
if (($handle = fopen('students.csv', 'r')) !== false) {
    fgetcsv($handle); // Skip header
    while (($row = fgetcsv($handle)) !== false) {
        [$name, $email, $course] = $row;
        // Insert into database...
    }
    fclose($handle);
}
```

## Cron Jobs (Scheduled Tasks)

Cron jobs run PHP scripts automatically at scheduled times:

```bash
# Edit crontab
crontab -e

# Format: minute hour day month weekday command
# Run every day at 8am
0 8 * * * php /var/www/html/lms/cron/send_reminders.php

# Run every hour
0 * * * * php /var/www/html/lms/cron/check_due_payments.php

# Run every Monday at 9am
0 9 * * 1 php /var/www/html/lms/cron/weekly_report.php
```

## Practical Task

Build a payment reminder system: a cron job script that queries the database for installment students whose next_due_date is within 3 days, and sends them a reminder email using PHPMailer. Log all sent emails to a file.

## Self-Check
1. Why should you use PHPMailer instead of PHP's built-in mail() function?
2. What does FILE_APPEND do in file_put_contents()?
3. What does the cron expression `0 8 * * *` mean?""",6)

L(5,"Testing & Code Quality","""## Why Testing Matters

Untested code is broken code waiting to be discovered. Testing catches bugs early, documents expected behaviour, and gives you confidence to refactor.

## Types of Testing

**Unit Testing**: Test individual functions/methods in isolation
**Integration Testing**: Test how components work together
**End-to-End Testing**: Test the complete user flow through the application
**Manual Testing**: Human testers following test scripts

## PHPUnit

PHPUnit is the standard testing framework for PHP:

```bash
composer require --dev phpunit/phpunit
```

```php
// src/Calculator.php
class Calculator {
    public function add(float $a, float $b): float {
        return $a + $b;
    }

    public function divide(float $a, float $b): float {
        if ($b === 0.0) {
            throw new InvalidArgumentException('Cannot divide by zero');
        }
        return $a / $b;
    }
}

// tests/CalculatorTest.php
use PHPUnit\\Framework\\TestCase;

class CalculatorTest extends TestCase {
    private Calculator $calc;

    protected function setUp(): void {
        $this->calc = new Calculator();
    }

    public function testAdd(): void {
        $this->assertEquals(5, $this->calc->add(2, 3));
        $this->assertEquals(0, $this->calc->add(-1, 1));
    }

    public function testDivideByZeroThrowsException(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->calc->divide(10, 0);
    }
}
```

## Code Quality Tools

**PHP_CodeSniffer**: Checks coding standards (PSR-12)
**PHPStan**: Static analysis — finds bugs without running code
**PHP-CS-Fixer**: Automatically fixes code style issues

## PSR Standards

PSR (PHP Standards Recommendations) are coding standards:
- **PSR-1**: Basic coding standard (class names, method names)
- **PSR-4**: Autoloading standard (namespace to directory mapping)
- **PSR-12**: Extended coding style guide

## Practical Task

Write unit tests for the Student class from Lesson 2. Test: constructor sets properties correctly, getGrade() returns correct grade for different GPA values, edge cases (GPA = 0, GPA = 4.0). Achieve 100% code coverage for the Student class.

## Self-Check
1. What is the difference between unit testing and integration testing?
2. What does PHPStan do?
3. What is PSR-12?""",7)

L(5,"Capstone: E-Commerce Application","""## Final Project: Build a Complete E-Commerce Store

You will build a fully functional e-commerce application using PHP, MySQL, and modern web technologies.

## Features Required

### Customer-Facing
- Product catalogue with categories and search
- Product detail page with images and description
- Shopping cart (session-based)
- Checkout with order summary
- User registration and login
- Order history and tracking
- Email confirmation on order placement

### Admin Panel
- Dashboard with sales statistics
- Product management (CRUD)
- Category management
- Order management (view, update status)
- Customer list
- Basic sales report (CSV export)

## Database Schema

```sql
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT UNSIGNED DEFAULT 0,
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL
);
```

## Security Requirements
- All inputs sanitised and validated
- Prepared statements for all queries
- CSRF protection on all forms
- Password hashing with bcrypt
- Admin routes protected by role check
- File uploads validated (images only, max 2MB)

## Evaluation Criteria
- Feature completeness (30%)
- Code quality and security (25%)
- Database design (15%)
- UI/UX quality (15%)
- Testing coverage (15%)

## Self-Check
1. How do you prevent a customer from accessing admin routes?
2. How would you handle a payment failure mid-checkout?
3. What indexes would you add to the orders table for performance?""",8)


# ── COURSES 6-16: Condensed but real content ─────────────────────────────────

# Course 6: Mobile App Development
for i, (title, content) in enumerate([
  ("Introduction to Mobile Development", "## Mobile Development Overview\n\nMobile apps run on smartphones and tablets. The two dominant platforms are Android (Google, ~72% market share) and iOS (Apple, ~27%).\n\n## Development Approaches\n\n**Native**: Built specifically for one platform.\n- Android: Kotlin or Java, Android Studio IDE\n- iOS: Swift or Objective-C, Xcode IDE\n- Pros: Best performance, full platform access\n- Cons: Two separate codebases to maintain\n\n**Cross-Platform**: One codebase for both platforms.\n- React Native: JavaScript, by Meta\n- Flutter: Dart language, by Google\n- Xamarin: C#, by Microsoft\n- Pros: Single codebase, faster development\n- Cons: Slightly lower performance, some native features harder to access\n\n**Progressive Web Apps (PWA)**: Web apps that behave like native apps.\n- Built with HTML, CSS, JavaScript\n- Work offline with Service Workers\n- Can be installed on home screen\n- No app store required\n\n## Setting Up Flutter\n\n1. Download Flutter SDK from flutter.dev\n2. Install Android Studio and Android SDK\n3. Run `flutter doctor` to check setup\n4. Create project: `flutter create my_app`\n5. Run on emulator: `flutter run`\n\n## Dart Basics\n\n```dart\nvoid main() {\n  String name = 'Amara';\n  int age = 22;\n  print('Hello, $name! You are $age years old.');\n\n  List<String> courses = ['Flutter', 'Dart', 'Firebase'];\n  for (var course in courses) {\n    print(course);\n  }\n}\n```\n\n## Practical Task\n\nInstall Flutter and create your first app. Modify the default counter app to count down instead of up. Add a reset button. Run on an Android emulator.\n\n## Self-Check\n1. What is the difference between native and cross-platform development?\n2. What language does Flutter use?\n3. What is a Progressive Web App?"),
  ("Flutter UI Fundamentals", "## Flutter Widget System\n\nIn Flutter, everything is a widget. Widgets are the building blocks of a Flutter app's UI.\n\n## Widget Types\n\n**Stateless Widgets**: Don't change over time.\n```dart\nclass WelcomeCard extends StatelessWidget {\n  final String name;\n  const WelcomeCard({required this.name});\n\n  @override\n  Widget build(BuildContext context) {\n    return Card(\n      child: Padding(\n        padding: EdgeInsets.all(16),\n        child: Text('Welcome, $name!', style: TextStyle(fontSize: 18)),\n      ),\n    );\n  }\n}\n```\n\n**Stateful Widgets**: Can change over time (user interaction, data loading).\n```dart\nclass Counter extends StatefulWidget {\n  @override\n  _CounterState createState() => _CounterState();\n}\n\nclass _CounterState extends State<Counter> {\n  int _count = 0;\n\n  @override\n  Widget build(BuildContext context) {\n    return Column(\n      children: [\n        Text('Count: $_count', style: TextStyle(fontSize: 24)),\n        ElevatedButton(\n          onPressed: () => setState(() => _count++),\n          child: Text('Increment'),\n        ),\n      ],\n    );\n  }\n}\n```\n\n## Layout Widgets\n\n- **Column**: Vertical layout\n- **Row**: Horizontal layout\n- **Stack**: Overlapping widgets\n- **Container**: Box with padding, margin, decoration\n- **Expanded**: Fill available space\n- **ListView**: Scrollable list\n- **GridView**: Scrollable grid\n\n## Practical Task\n\nBuild a student profile card app. Display: profile photo (placeholder), name, course, GPA, and a list of enrolled subjects. Use Column, Row, Card, and ListView widgets.\n\n## Self-Check\n1. What is the difference between StatelessWidget and StatefulWidget?\n2. What does setState() do?\n3. When would you use Stack instead of Column?"),
  ("State Management & Navigation", "## State Management in Flutter\n\nAs apps grow, managing state across multiple screens becomes complex. Flutter offers several state management solutions.\n\n## Provider (Recommended for Beginners)\n\n```dart\n// 1. Create a ChangeNotifier\nclass CartProvider extends ChangeNotifier {\n  List<String> _items = [];\n  List<String> get items => _items;\n  int get count => _items.length;\n\n  void addItem(String item) {\n    _items.add(item);\n    notifyListeners(); // Rebuild listening widgets\n  }\n\n  void removeItem(String item) {\n    _items.remove(item);\n    notifyListeners();\n  }\n}\n\n// 2. Wrap app with ChangeNotifierProvider\nMultiProvider(\n  providers: [ChangeNotifierProvider(create: (_) => CartProvider())],\n  child: MyApp(),\n)\n\n// 3. Read state in any widget\nfinal cart = Provider.of<CartProvider>(context);\nText('Cart: ${cart.count} items');\n```\n\n## Navigation\n\n```dart\n// Push a new screen\nNavigator.push(\n  context,\n  MaterialPageRoute(builder: (context) => DetailScreen(id: 5)),\n);\n\n// Pop back\nNavigator.pop(context);\n\n// Named routes\nNavigator.pushNamed(context, '/profile', arguments: {'userId': 42});\n\n// Bottom navigation\nBottomNavigationBar(\n  currentIndex: _selectedIndex,\n  onTap: (index) => setState(() => _selectedIndex = index),\n  items: [\n    BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Home'),\n    BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profile'),\n  ],\n)\n```\n\n## Practical Task\n\nBuild a 3-screen app with bottom navigation: Home (list of courses), Detail (course info), Profile (user info). Use Provider to share the selected course between screens.\n\n## Self-Check\n1. What is the purpose of notifyListeners() in Provider?\n2. What is the difference between push and pushReplacement in navigation?\n3. When would you use named routes?"),
  ("Firebase & Backend Integration", "## What is Firebase?\n\nFirebase is Google's mobile and web application development platform. It provides backend services without writing server code.\n\n## Firebase Services\n\n- **Firestore**: NoSQL cloud database, real-time sync\n- **Authentication**: Email/password, Google, Facebook, phone login\n- **Storage**: File storage for images and videos\n- **Cloud Functions**: Serverless backend logic\n- **Analytics**: App usage tracking\n- **Cloud Messaging (FCM)**: Push notifications\n\n## Setting Up Firebase in Flutter\n\n```bash\n# Install FlutterFire CLI\ndart pub global activate flutterfire_cli\n\n# Configure Firebase\nflutterfire configure\n\n# Add dependencies to pubspec.yaml\nfirebase_core: ^2.0.0\ncloud_firestore: ^4.0.0\nfirebase_auth: ^4.0.0\n```\n\n## Firestore CRUD\n\n```dart\nfinal db = FirebaseFirestore.instance;\n\n// Create\nawait db.collection('students').add({\n  'name': 'Amara',\n  'email': 'amara@example.com',\n  'course': 'Flutter Development',\n  'createdAt': FieldValue.serverTimestamp(),\n});\n\n// Read (real-time stream)\nStreamBuilder<QuerySnapshot>(\n  stream: db.collection('students').snapshots(),\n  builder: (context, snapshot) {\n    if (!snapshot.hasData) return CircularProgressIndicator();\n    final docs = snapshot.data!.docs;\n    return ListView.builder(\n      itemCount: docs.length,\n      itemBuilder: (context, i) => ListTile(\n        title: Text(docs[i]['name']),\n      ),\n    );\n  },\n)\n\n// Update\nawait db.collection('students').doc(docId).update({'course': 'Advanced Flutter'});\n\n// Delete\nawait db.collection('students').doc(docId).delete();\n```\n\n## Firebase Authentication\n\n```dart\nfinal auth = FirebaseAuth.instance;\n\n// Register\nawait auth.createUserWithEmailAndPassword(\n  email: 'user@example.com',\n  password: 'SecurePass123',\n);\n\n// Login\nawait auth.signInWithEmailAndPassword(\n  email: 'user@example.com',\n  password: 'SecurePass123',\n);\n\n// Logout\nawait auth.signOut();\n\n// Check auth state\nStreamBuilder<User?>(\n  stream: auth.authStateChanges(),\n  builder: (context, snapshot) {\n    if (snapshot.hasData) return HomeScreen();\n    return LoginScreen();\n  },\n)\n```\n\n## Practical Task\n\nBuild a note-taking app with Firebase. Features: email/password authentication, create/read/update/delete notes stored in Firestore, real-time sync across devices. Each user sees only their own notes.\n\n## Self-Check\n1. What is the difference between Firestore and Firebase Realtime Database?\n2. How does authStateChanges() work?\n3. What is a Firestore collection vs a document?"),
  ("Publishing & App Store Deployment", "## Preparing Your App for Release\n\nBefore publishing, your app must be production-ready: no debug code, proper icons, splash screen, and optimised performance.\n\n## App Icons & Splash Screen\n\n```bash\n# flutter_launcher_icons package\nflutter pub add flutter_launcher_icons\n\n# pubspec.yaml\nflutter_launcher_icons:\n  android: true\n  ios: true\n  image_path: 'assets/icon.png'  # 1024x1024px\n\nflutter pub run flutter_launcher_icons\n```\n\n## Building for Release\n\n```bash\n# Android APK\nflutter build apk --release\n# Output: build/app/outputs/flutter-apk/app-release.apk\n\n# Android App Bundle (required for Play Store)\nflutter build appbundle --release\n# Output: build/app/outputs/bundle/release/app-release.aab\n\n# iOS (requires Mac + Xcode)\nflutter build ios --release\n```\n\n## Google Play Store Submission\n\n1. Create Google Play Developer account ($25 one-time fee)\n2. Create a new app in Play Console\n3. Complete store listing: title, description, screenshots, icon\n4. Set content rating\n5. Set up pricing and distribution\n6. Upload your .aab file\n7. Submit for review (typically 1-3 days)\n\n## App Store (iOS) Submission\n\n1. Enrol in Apple Developer Program ($99/year)\n2. Create App ID in Apple Developer portal\n3. Create app in App Store Connect\n4. Archive and upload from Xcode\n5. Complete metadata: description, screenshots, keywords\n6. Submit for review (typically 1-2 days)\n\n## App Store Optimisation (ASO)\n\nASO is SEO for app stores:\n- **Title**: Include primary keyword (50 chars max)\n- **Description**: First 3 lines are most important\n- **Keywords**: Research with Sensor Tower or AppFollow\n- **Screenshots**: Show key features, use device frames\n- **Ratings**: Prompt users to rate at the right moment\n\n## Practical Task\n\nPrepare your note-taking app for release. Add a proper app icon and splash screen. Build a release APK. Create a complete Play Store listing with description, screenshots, and content rating. (You don't need to actually publish — just prepare all assets.)\n\n## Self-Check\n1. What is the difference between an APK and an App Bundle?\n2. What is App Store Optimisation?\n3. What is the annual cost of an Apple Developer account?"),
  ("Advanced Flutter: Animations & Performance", "## Flutter Animations\n\nAnimations make apps feel polished and responsive. Flutter provides several animation systems.\n\n## Implicit Animations (Easiest)\n\nImplicit animations automatically animate between values:\n\n```dart\nAnimatedContainer(\n  duration: Duration(milliseconds: 300),\n  curve: Curves.easeInOut,\n  width: _isExpanded ? 200 : 100,\n  height: _isExpanded ? 200 : 100,\n  color: _isExpanded ? Colors.blue : Colors.red,\n  child: Text('Tap me'),\n)\n\nAnimatedOpacity(\n  opacity: _isVisible ? 1.0 : 0.0,\n  duration: Duration(milliseconds: 500),\n  child: Text('Fade me'),\n)\n```\n\n## Explicit Animations (More Control)\n\n```dart\nclass SpinningLogo extends StatefulWidget {\n  @override\n  _SpinningLogoState createState() => _SpinningLogoState();\n}\n\nclass _SpinningLogoState extends State<SpinningLogo>\n    with SingleTickerProviderStateMixin {\n  late AnimationController _controller;\n\n  @override\n  void initState() {\n    super.initState();\n    _controller = AnimationController(\n      duration: Duration(seconds: 2),\n      vsync: this,\n    )..repeat();\n  }\n\n  @override\n  Widget build(BuildContext context) {\n    return RotationTransition(\n      turns: _controller,\n      child: FlutterLogo(size: 100),\n    );\n  }\n\n  @override\n  void dispose() {\n    _controller.dispose();\n    super.dispose();\n  }\n}\n```\n\n## Performance Optimisation\n\n1. Use `const` constructors wherever possible\n2. Use `ListView.builder` instead of `ListView` for long lists\n3. Avoid rebuilding the entire widget tree — use `Consumer` or `Selector` with Provider\n4. Use `RepaintBoundary` to isolate expensive widgets\n5. Profile with Flutter DevTools: identify jank (frames taking >16ms)\n\n## Practical Task\n\nAdd animations to your note-taking app: animated list item insertion/deletion, a hero animation when opening a note, and a loading shimmer effect while data loads from Firestore.\n\n## Self-Check\n1. What is the difference between implicit and explicit animations?\n2. Why should you always call dispose() on an AnimationController?\n3. What is the target frame rate for smooth Flutter animations?"),
  ("Monetisation & Analytics", "## Monetising Your App\n\nThere are several business models for mobile apps:\n\n**Free with Ads**: Show ads using Google AdMob. Low revenue per user but scales with downloads.\n**Freemium**: Free basic features, paid premium features. Most popular model.\n**Paid App**: One-time purchase. Works for niche, high-value apps.\n**Subscription**: Recurring monthly/annual fee. Best for content and services.\n**In-App Purchases**: Buy virtual goods, extra content, or features.\n\n## Google AdMob Integration\n\n```dart\n// pubspec.yaml\ngoogle_mobile_ads: ^3.0.0\n\n// Banner ad\nfinal BannerAd myBanner = BannerAd(\n  adUnitId: 'ca-app-pub-3940256099942544/6300978111', // Test ID\n  size: AdSize.banner,\n  request: AdRequest(),\n  listener: BannerAdListener(\n    onAdLoaded: (ad) => setState(() => _isBannerLoaded = true),\n    onAdFailedToLoad: (ad, error) => ad.dispose(),\n  ),\n);\nmyBanner.load();\n```\n\n## In-App Purchases\n\n```dart\n// in_app_purchase package\nfinal InAppPurchase _iap = InAppPurchase.instance;\n\n// Check availability\nfinal bool available = await _iap.isAvailable();\n\n// Load products\nconst Set<String> ids = {'premium_monthly', 'premium_annual'};\nfinal ProductDetailsResponse response = await _iap.queryProductDetails(ids);\n\n// Purchase\nawait _iap.buyNonConsumable(\n  purchaseParam: PurchaseParam(productDetails: response.productDetails.first),\n);\n```\n\n## Firebase Analytics\n\n```dart\nfinal analytics = FirebaseAnalytics.instance;\n\n// Log custom event\nawait analytics.logEvent(\n  name: 'course_started',\n  parameters: {'course_id': '5', 'course_name': 'Flutter Dev'},\n);\n\n// Log screen view\nawait analytics.logScreenView(screenName: 'CourseDetail');\n\n// Set user property\nawait analytics.setUserProperty(name: 'subscription_tier', value: 'premium');\n```\n\n## Practical Task\n\nAdd a freemium model to your note-taking app: free users can create up to 5 notes, premium users have unlimited notes. Implement a paywall screen with in-app purchase. Add Firebase Analytics events for key user actions.\n\n## Self-Check\n1. What is the difference between a consumable and non-consumable in-app purchase?\n2. What is the freemium model?\n3. What Firebase Analytics event would you log when a user completes a purchase?"),
  ("Capstone: Full Mobile Application", "## Final Project: Build a Complete Mobile App\n\nYou will build a production-ready mobile application using Flutter and Firebase.\n\n## Project Options (Choose One)\n\n### Option A: Learning App\nA mobile version of a course platform:\n- Browse courses by category\n- Enrol and track progress\n- Watch video lessons\n- Take quizzes\n- View certificates\n\n### Option B: Marketplace App\nA buy/sell marketplace:\n- List items for sale with photos\n- Browse and search listings\n- Chat between buyer and seller\n- User profiles and ratings\n\n### Option C: Health & Fitness Tracker\n- Log daily workouts\n- Track calories and water intake\n- View progress charts\n- Set and track goals\n- Reminders via push notifications\n\n## Required Technical Features\n\n- Firebase Authentication (email/password + Google Sign-In)\n- Firestore database with proper security rules\n- Firebase Storage for image uploads\n- Push notifications (FCM)\n- Offline support (Firestore offline persistence)\n- At least 2 animations\n- Responsive layout (phone + tablet)\n- Published to Google Play (internal testing track)\n\n## Evaluation Criteria\n- Feature completeness (25%)\n- Code quality and architecture (25%)\n- UI/UX design (20%)\n- Firebase integration (15%)\n- Performance and animations (15%)\n\n## Self-Check\n1. Does your app work offline?\n2. Are your Firestore security rules preventing unauthorised access?\n3. Have you tested on both a small phone (5 inch) and a tablet?"),
], 1):
    L(6, title, content, i)


# ── Course 7: UI/UX Design ────────────────────────────────────────────────────
L(7,"UX Research & User Personas",
"## What is UX Research?\n\nUX research uncovers user needs, behaviours, and pain points through systematic investigation. Good design is grounded in evidence, not assumptions.\n\n## Research Methods\n\n**Qualitative (understanding why)**\n- User interviews: 1-on-1 conversations (30-60 min). Ask open-ended questions.\n- Contextual inquiry: Observe users in their real environment.\n- Diary studies: Users log their experiences over days or weeks.\n\n**Quantitative (understanding how many)**\n- Surveys: Collect data from many users at once.\n- Analytics: Heatmaps, click tracking, funnel analysis.\n- A/B testing: Compare two versions to see which performs better.\n\n## User Personas\n\nA persona is a fictional but research-based representation of your target user:\n- Name and photo (makes them feel real)\n- Demographics: Age, location, occupation\n- Goals: What they want to achieve\n- Frustrations: What gets in their way\n- Behaviours: How they use technology\n- Quote: A sentence capturing their attitude\n\n**Example Persona:**\nName: Chidinma, 28, Lagos. Marketing Manager.\nGoal: Learn digital marketing to advance her career.\nFrustration: Most courses are too theoretical and not Nigeria-specific.\nBehaviour: Learns on mobile during commute, prefers video over text.\nQuote: I need practical skills I can use at work on Monday.\n\n## Jobs-to-be-Done Framework\n\nFocus on what job the user is hiring your product to do:\nWhen [situation], I want to [motivation], so I can [outcome].\n\nExample: When commuting to work, I want to learn a skill in short sessions, so I can advance my career without sacrificing family time.\n\n## Practical Task\n\nConduct 3 user interviews about a digital product you use regularly. Ask about goals, frustrations, and workarounds. Create 2 user personas based on your findings. Write 3 Jobs-to-be-Done statements.\n\n## Self-Check\n1. What is the difference between qualitative and quantitative research?\n2. What are the 5 components of a user persona?\n3. What is the Jobs-to-be-Done framework?",
1)

L(7,"Information Architecture & Wireframing",
"## Information Architecture\n\nIA is the organisation, structure, and labelling of content to help users find what they need.\n\n## IA Deliverables\n\n**Sitemap**: A visual diagram showing all pages and their hierarchy.\n\nExample sitemap for an LMS:\n- Home\n- Courses > Course Detail > Enrol\n- Dashboard > My Courses > Assignments > Exams\n- Profile\n- Payments\n\n**Card Sorting**: Users group content into categories they find logical.\n**Tree Testing**: Test navigation structure without visual design.\n\n## Wireframing\n\nWireframes are low-fidelity blueprints of a screen. They show layout and structure without colour, images, or final copy.\n\n**Fidelity Levels:**\n- Lo-fi: Sketches on paper. Fast, cheap, easy to change.\n- Mid-fi: Greyscale digital wireframes in Figma or Balsamiq.\n- Hi-fi: Full colour, real content, interactive prototype.\n\n**Wireframe Conventions:**\n- Boxes with X = image placeholder\n- Lorem ipsum = placeholder text\n- Grey boxes = content areas\n- Annotations explain functionality\n\n## Practical Task\n\nCreate a sitemap for a 10-page e-commerce website. Then wireframe 3 key screens: Home, Product Listing, and Product Detail. Use Figma or paper. Focus on layout and content hierarchy, not visual design.\n\n## Self-Check\n1. What is the difference between a sitemap and a wireframe?\n2. What are the three fidelity levels of wireframes?\n3. What is card sorting and when would you use it?",
2)

L(7,"Visual Design & UI Patterns",
"## UI Design Patterns\n\nUI patterns are reusable solutions to common design problems. Using established patterns reduces cognitive load.\n\n## Common UI Patterns\n\n**Navigation Patterns:**\n- Top navigation bar: Desktop websites\n- Bottom navigation bar: Mobile apps (max 5 items)\n- Hamburger menu: Hidden navigation for mobile\n- Breadcrumbs: Show location in hierarchy\n- Tabs: Switch between related content sections\n\n**Content Patterns:**\n- Card grid: Display collections of items\n- List view: Detailed rows with actions\n- Infinite scroll: Load more content as user scrolls\n- Pagination: Navigate between pages\n\n**Feedback Patterns:**\n- Toast notifications: Brief, non-blocking messages\n- Modal dialogs: Require user action before continuing\n- Skeleton screens: Show layout while content loads\n- Empty states: Guide users when there is no content\n\n## Visual Hierarchy in UI\n\nGuide the eye to the most important content:\n1. Size: Larger = more important\n2. Colour: High contrast = more important\n3. Weight: Bold = more important\n4. Position: Top-left = seen first (F-pattern)\n5. Whitespace: Isolated elements draw attention\n\n## Practical Task\n\nDesign a dashboard screen for a student learning app. Include: a welcome header, 3 stat cards, a list of enrolled courses with progress bars, and a recent activity feed. Apply visual hierarchy principles. Design in Figma at 390px (mobile) and 1440px (desktop).\n\n## Self-Check\n1. What is a UI design pattern and why are they useful?\n2. Name 3 navigation patterns and when to use each.\n3. What is a skeleton screen?",
3)

L(7,"Prototyping & Usability Testing",
"## Prototyping\n\nA prototype is a simulation of your product used to test ideas before building them. Prototypes save time and money by catching problems early.\n\n## Prototype Fidelity\n\n- Paper prototype: Sketches on paper. Fastest to create.\n- Digital wireframe prototype: Clickable greyscale screens.\n- High-fidelity prototype: Full visual design with interactions.\n\n## Prototyping in Figma\n\n1. Design your screens\n2. Switch to Prototype mode (top right panel)\n3. Select an element and drag the blue arrow to the destination screen\n4. Set trigger: On Click, On Hover, After Delay\n5. Set animation: Smart Animate, Dissolve, Slide In\n6. Press Play to preview\n\nSmart Animate: Figma automatically animates matching layers between screens. Name layers identically on both screens for smooth transitions.\n\n## Usability Testing\n\nUsability testing observes real users attempting tasks on your prototype.\n\n**Process:**\n1. Define tasks: What do you want users to do?\n2. Recruit participants: 5 users reveal ~85% of usability issues\n3. Conduct sessions: Observe, do not help. Ask users to think aloud.\n4. Analyse findings: Group issues by frequency and severity\n5. Iterate: Fix the most critical issues and test again\n\n**Severity Ratings:**\n- Critical: Prevents task completion\n- Major: Causes significant difficulty\n- Minor: Causes slight confusion\n- Cosmetic: Aesthetic issue only\n\n## Practical Task\n\nCreate a clickable prototype of your dashboard design. Write 3 usability test tasks. Conduct a usability test with 2 people. Document findings using severity ratings. Iterate on the top 2 issues found.\n\n## Self-Check\n1. How many users do you need for a usability test to find most issues?\n2. What is Smart Animate in Figma?\n3. What are the 4 severity ratings for usability issues?",
4)

L(7,"Design Systems & Component Libraries",
"## What is a Design System?\n\nA design system is a collection of reusable components, guided by clear standards, that can be assembled to build any number of applications.\n\nExamples: Google Material Design, Apple Human Interface Guidelines, IBM Carbon.\n\n## Design System Foundations\n\n- Colour tokens: primary, secondary, semantic, neutral\n- Typography scale: font families, sizes, weights, line heights\n- Spacing scale: 4px, 8px, 16px, 24px, 32px, 48px, 64px\n- Border radius: none, sm, md, lg, full\n- Shadow/elevation levels\n- Grid and layout system\n\n## Component Hierarchy\n\n- Atoms: Button, Input, Badge, Icon, Avatar\n- Molecules: Form field (label + input + error), Card, Search bar\n- Organisms: Navigation bar, Data table, Modal dialog\n- Templates: Page layouts\n\n## Building a Design System in Figma\n\n1. Create a dedicated Figma file for your design system\n2. Set up colour styles (right panel > Styles > +)\n3. Set up text styles for each typography level\n4. Create components for each UI element\n5. Add variants for different states (default, hover, focus, disabled, error)\n6. Document usage guidelines in the file\n\n## Component Variants in Figma\n\nButton component variants:\n- Type: Primary | Secondary | Ghost | Danger\n- Size: Small | Medium | Large\n- State: Default | Hover | Focus | Disabled | Loading\n\n## Practical Task\n\nBuild a mini design system in Figma for a fictional brand. Include: colour tokens, typography scale, spacing scale, and these components with all states: Button (4 types x 3 sizes), Input field, Card, Badge, and Navigation bar.\n\n## Self-Check\n1. What is the difference between atoms, molecules, and organisms?\n2. What are component variants in Figma?\n3. Name 3 real-world design systems.",
5)

L(7,"Accessibility & Inclusive Design",
"## What is Accessibility?\n\nAccessibility (a11y) means designing products that can be used by people with disabilities. 1 in 7 people worldwide has a disability. Accessible design is ethical and often legally required.\n\n## WCAG 4 Principles\n\n**Perceivable**: Information must be presentable in ways users can perceive.\n- Provide text alternatives for images (alt text)\n- Provide captions for videos\n- Ensure sufficient colour contrast (4.5:1 for normal text)\n- Do not use colour alone to convey information\n\n**Operable**: UI components must be operable.\n- All functionality available via keyboard\n- No content that flashes more than 3 times per second\n- Minimum touch target size: 44x44px\n\n**Understandable**: Information and UI must be understandable.\n- Use clear, simple language\n- Provide helpful error messages\n- Do not change context unexpectedly\n\n**Robust**: Content must work with assistive technologies.\n- Use semantic HTML\n- Use ARIA labels where needed\n- Test with screen readers (NVDA, VoiceOver)\n\n## Colour Contrast\n\nMinimum contrast ratios (WCAG AA):\n- Normal text: 4.5:1\n- Large text (18px+ or 14px+ bold): 3:1\n- UI components and graphics: 3:1\n\nTools: WebAIM Contrast Checker, Figma plugins (Contrast, A11y Annotation Kit)\n\n## Practical Task\n\nAudit your dashboard design for accessibility. Check: colour contrast ratios, touch target sizes, keyboard navigation order, and screen reader labels. Fix all WCAG AA violations. Document your findings and fixes.\n\n## Self-Check\n1. What does WCAG stand for and what are its 4 principles?\n2. What is the minimum contrast ratio for normal text (WCAG AA)?\n3. What is the minimum touch target size for mobile?",
6)

L(7,"UX Writing & Microcopy",
"## What is UX Writing?\n\nUX writing is the practice of crafting the words that appear in digital products: buttons, labels, error messages, onboarding flows, and empty states. Good UX writing guides users, reduces friction, and builds trust.\n\n## Principles of Good UX Writing\n\n- Clear: Use plain language. Avoid jargon.\n- Concise: Every word must earn its place. Cut ruthlessly.\n- Useful: Tell users what they need to know to complete their task.\n- Consistent: Use the same terms throughout.\n- Human: Write like a helpful person, not a legal document.\n\n## Microcopy Examples\n\n**Button labels:**\n- Bad: Submit | Good: Create Account\n- Bad: OK | Good: Got it\n- Bad: Delete | Good: Delete Course (be specific)\n\n**Error messages:**\n- Bad: Error 404 | Good: We could not find that page. Try searching or go back to the homepage.\n- Bad: Invalid input | Good: Please enter a valid email address (e.g. name@example.com)\n\n**Empty states:**\n- Bad: No data | Good: You have not enrolled in any courses yet. Browse our catalogue to get started.\n\n## Tone of Voice\n\nTone of voice defines how your brand communicates:\n- Friendly but professional\n- Encouraging but honest\n- Simple but not simplistic\n- Direct but not blunt\n\n## Practical Task\n\nRewrite the microcopy for 5 screens of your dashboard design: empty state, error state, success message, onboarding tooltip, and a confirmation dialog. Apply the 5 principles of good UX writing. Get feedback from 2 people on clarity.\n\n## Self-Check\n1. What are the 5 principles of good UX writing?\n2. Rewrite this error message: Invalid credentials. Make it helpful.\n3. What is an empty state and why does it matter?",
7)

L(7,"Capstone: End-to-End UX Project",
"## Final Project Brief\n\nYou will complete a full UX design project from research to high-fidelity prototype.\n\n## Project Scenario\n\nDesign a mobile app for SkillBridge Nigeria: a platform connecting Nigerian graduates with short-term freelance projects to build their portfolios and earn income while job hunting.\n\n## Phase 1: Research\n- Conduct 5 user interviews with recent graduates\n- Create 2 user personas\n- Write 5 Jobs-to-be-Done statements\n- Competitive analysis: 3 competitor apps\n\n## Phase 2: Define & Ideate\n- Affinity mapping: Group research findings into themes\n- Problem statement: How might we [problem] for [user] so that [outcome]?\n- Sitemap: All screens and their relationships\n- User flow: Step-by-step path for the primary task\n\n## Phase 3: Design\n- Lo-fi wireframes: All key screens on paper\n- Mid-fi wireframes: Greyscale in Figma\n- Design system: Colours, typography, components\n- Hi-fi mockups: Full visual design in Figma\n\n## Phase 4: Test & Iterate\n- Clickable prototype in Figma\n- Usability test with 5 participants\n- Severity-rated findings report\n- Iterated designs addressing critical and major issues\n\n## Deliverables\n- Research report (personas, JTBD, competitive analysis)\n- Sitemap and user flow diagram\n- Figma file with all screens (lo-fi, mid-fi, hi-fi)\n- Design system\n- Clickable prototype\n- Usability test report\n\n## Evaluation Criteria\n- Research quality and insight depth (20%)\n- Information architecture clarity (15%)\n- Visual design quality (25%)\n- Prototype completeness (20%)\n- Usability test rigour and iteration (20%)\n\n## Self-Check\n1. Does every design decision trace back to a research finding?\n2. Did you test with real users (not just classmates)?\n3. Is your prototype realistic enough to test the core user flow?",
8)


# ── Course 8: Digital Marketing ───────────────────────────────────────────────
L(8,"Digital Marketing Fundamentals",
"## What is Digital Marketing?\n\nDigital marketing is the promotion of products or services through digital channels: search engines, social media, email, websites, and mobile apps.\n\n## The Digital Marketing Ecosystem\n\n**Owned Media**: Channels you control — your website, blog, email list, social profiles.\n**Earned Media**: Coverage you earn — press mentions, shares, reviews, word of mouth.\n**Paid Media**: Channels you pay for — Google Ads, Facebook Ads, sponsored content.\n\n## Key Digital Marketing Channels\n\n1. **SEO (Search Engine Optimisation)**: Rank higher in Google search results organically.\n2. **SEM (Search Engine Marketing)**: Pay-per-click ads on Google and Bing.\n3. **Social Media Marketing**: Organic and paid content on Facebook, Instagram, LinkedIn, TikTok.\n4. **Email Marketing**: Direct communication with subscribers.\n5. **Content Marketing**: Blog posts, videos, podcasts, infographics.\n6. **Affiliate Marketing**: Partners promote your product for a commission.\n7. **Influencer Marketing**: Partner with creators who have your target audience.\n\n## The Marketing Funnel\n\n- **Awareness**: Customer discovers your brand (SEO, social media, ads)\n- **Interest**: Customer learns more (blog, video, email)\n- **Consideration**: Customer evaluates options (case studies, reviews, demos)\n- **Intent**: Customer shows buying signals (adds to cart, requests quote)\n- **Purchase**: Customer buys\n- **Loyalty**: Customer returns and refers others\n\n## Key Metrics\n\n- **Impressions**: How many times your content was shown\n- **Reach**: How many unique people saw your content\n- **CTR (Click-Through Rate)**: Clicks / Impressions x 100\n- **Conversion Rate**: Conversions / Visitors x 100\n- **CPA (Cost Per Acquisition)**: Total spend / Number of customers\n- **ROI**: (Revenue - Cost) / Cost x 100\n- **LTV (Lifetime Value)**: Total revenue from a customer over their lifetime\n\n## Practical Task\n\nCreate a digital marketing plan for a fictional Nigerian SME. Define: target audience, 3 marketing channels, content calendar for 1 month, KPIs for each channel, and a monthly budget allocation.\n\n## Self-Check\n1. What is the difference between owned, earned, and paid media?\n2. Name the 6 stages of the marketing funnel.\n3. What does CTR stand for and how is it calculated?",
1)

L(8,"SEO: Search Engine Optimisation",
"## How Search Engines Work\n\nSearch engines crawl the web, index content, and rank pages based on relevance and authority. Understanding this process helps you optimise your content to rank higher.\n\n## On-Page SEO\n\nElements on your page that you control:\n\n**Title Tag**: The most important on-page SEO element.\n- 50-60 characters\n- Include primary keyword near the beginning\n- Each page must have a unique title\n- Example: Digital Marketing Course in Lagos | Mirror LMS\n\n**Meta Description**: Appears in search results below the title.\n- 150-160 characters\n- Include primary keyword\n- Write a compelling summary that encourages clicks\n\n**Heading Structure**:\n- One H1 per page (main topic)\n- H2 for main sections\n- H3 for subsections\n- Include keywords naturally\n\n**URL Structure**:\n- Short and descriptive: /digital-marketing-course\n- Use hyphens, not underscores\n- Include primary keyword\n\n**Image Optimisation**:\n- Descriptive file names: digital-marketing-course-lagos.jpg\n- Alt text: Descriptive, keyword-relevant\n- Compress images for fast loading\n\n## Technical SEO\n\n- **Page speed**: Use Google PageSpeed Insights. Target under 3 seconds.\n- **Mobile-friendly**: Use Google Mobile-Friendly Test.\n- **HTTPS**: Secure sites rank higher.\n- **Sitemap.xml**: Submit to Google Search Console.\n- **Robots.txt**: Tell search engines what to crawl.\n- **Structured data**: Schema markup for rich snippets.\n\n## Off-Page SEO\n\n**Backlinks**: Links from other websites to yours. Quality matters more than quantity.\n- Guest posting on relevant blogs\n- Creating shareable content (infographics, research)\n- Building relationships with journalists and bloggers\n- Local citations for local businesses\n\n## Keyword Research\n\nTools: Google Keyword Planner (free), Ahrefs, SEMrush, Ubersuggest.\n\nKeyword types:\n- Short-tail: digital marketing (high volume, high competition)\n- Long-tail: digital marketing course for beginners in Lagos (lower volume, lower competition, higher intent)\n\n## Practical Task\n\nConduct keyword research for a fictional digital marketing agency in Lagos. Find 10 target keywords using Google Keyword Planner. Optimise a sample blog post for your primary keyword. Check your work with a free SEO tool like Yoast or Rank Math.\n\n## Self-Check\n1. What is the ideal length for a title tag?\n2. What is the difference between on-page and off-page SEO?\n3. What is a long-tail keyword and why is it valuable?",
2)

L(8,"Social Media Marketing",
"## Social Media Strategy\n\nA social media strategy defines your goals, audience, content, and metrics. Without a strategy, you are just posting randomly.\n\n## Platform Selection\n\nChoose platforms where your audience spends time:\n\n| Platform | Best For | Primary Audience |\n|---|---|---|\n| Facebook | Community, ads, local business | 25-54 years |\n| Instagram | Visual brands, lifestyle, products | 18-34 years |\n| LinkedIn | B2B, professional services, recruitment | Professionals |\n| TikTok | Entertainment, Gen Z, viral content | Under 30 |\n| Twitter/X | News, tech, real-time conversation | 25-49 years |\n| YouTube | Long-form video, tutorials, reviews | All ages |\n| WhatsApp | Direct communication, customer service | All ages (Nigeria) |\n\n## Content Strategy\n\nThe 80/20 rule: 80% valuable content, 20% promotional content.\n\nContent types:\n- Educational: Tips, how-tos, tutorials\n- Entertaining: Memes, behind-the-scenes, stories\n- Inspirational: Success stories, quotes, transformations\n- Promotional: Product features, offers, announcements\n- User-generated: Customer photos, reviews, testimonials\n\n## Content Calendar\n\nPlan content in advance:\n- Posting frequency: 3-5x per week on Instagram, 1-2x on LinkedIn\n- Best times: Tuesday-Thursday, 9am-11am and 6pm-8pm (test for your audience)\n- Themes: Assign content themes to days (Monday Motivation, Wednesday Tips)\n\n## Social Media Advertising\n\nFacebook/Instagram Ads Manager:\n1. Campaign objective: Awareness, Traffic, Engagement, Leads, Sales\n2. Target audience: Demographics, interests, behaviours, lookalike audiences\n3. Ad format: Image, video, carousel, stories, reels\n4. Budget: Daily or lifetime budget\n5. Bidding: Automatic or manual\n\n## Analytics & Reporting\n\nKey metrics per platform:\n- Reach and impressions\n- Engagement rate: (Likes + Comments + Shares) / Reach x 100\n- Follower growth rate\n- Link clicks and website traffic\n- Conversion rate from social traffic\n\n## Practical Task\n\nCreate a 30-day social media content calendar for a fictional Nigerian food brand. Include: platform selection rationale, content themes, 20 post ideas with captions and hashtags, and a simple reporting template.\n\n## Self-Check\n1. What is the 80/20 rule in social media content?\n2. What is engagement rate and how is it calculated?\n3. Which platform would you prioritise for a B2B software company in Nigeria and why?",
3)

L(8,"Email Marketing & Automation",
"## Why Email Marketing?\n\nEmail marketing has the highest ROI of any digital marketing channel: an average of $36 for every $1 spent. Unlike social media, you own your email list.\n\n## Building an Email List\n\n- Lead magnets: Offer something valuable in exchange for an email (free guide, checklist, discount)\n- Opt-in forms: Website pop-ups, embedded forms, landing pages\n- Social media: Promote your lead magnet on social channels\n- Events: Collect emails at webinars and workshops\n\nNever buy email lists. It damages deliverability and violates GDPR/CAN-SPAM.\n\n## Email Types\n\n- **Welcome email**: Sent immediately after sign-up. Highest open rates.\n- **Newsletter**: Regular updates, content, and news.\n- **Promotional**: Sales, discounts, product launches.\n- **Transactional**: Order confirmations, receipts, password resets.\n- **Re-engagement**: Win back inactive subscribers.\n- **Drip campaigns**: Automated sequence of emails over time.\n\n## Email Copywriting\n\n**Subject line** (most important):\n- 40-50 characters (shows fully on mobile)\n- Create curiosity or urgency\n- Personalise with first name: John, your course is waiting\n- A/B test subject lines\n\n**Preview text**: The text shown after the subject line in the inbox. Treat it as a second subject line.\n\n**Body copy**:\n- One clear goal per email\n- Short paragraphs (2-3 sentences)\n- One primary CTA button\n- Mobile-optimised design\n\n## Email Automation\n\nAutomation sends the right email to the right person at the right time:\n\n- Welcome sequence: 3-5 emails over 2 weeks introducing your brand\n- Abandoned cart: Remind users of items left in cart\n- Post-purchase: Thank you, upsell, review request\n- Birthday: Personalised offer on subscriber birthday\n- Re-engagement: If no opens in 90 days, send a win-back email\n\n## Tools\n\n- Mailchimp: Free up to 500 contacts\n- Brevo (formerly Sendinblue): Free up to 300 emails/day\n- ConvertKit: Best for creators and course sellers\n- Klaviyo: Best for e-commerce\n\n## Practical Task\n\nCreate a 5-email welcome sequence for a fictional online course platform. Write subject lines, preview text, and body copy for each email. Set up the automation flow in Mailchimp (free account). A/B test two subject lines for email 1.\n\n## Self-Check\n1. What is a lead magnet?\n2. What is the average ROI of email marketing?\n3. What is an email drip campaign?",
4)

L(8,"Content Marketing & Blogging",
"## What is Content Marketing?\n\nContent marketing is the creation and distribution of valuable, relevant content to attract and retain a clearly defined audience, with the goal of driving profitable customer action.\n\nKey principle: Help first, sell second.\n\n## Content Marketing Strategy\n\n1. Define your audience: Who are you creating content for?\n2. Define your goals: Brand awareness, lead generation, sales, retention?\n3. Choose content types: Blog, video, podcast, infographic, case study?\n4. Keyword research: What questions is your audience asking?\n5. Content calendar: Plan topics, formats, and publishing dates\n6. Distribution: Where will you publish and promote?\n7. Measurement: How will you track success?\n\n## Blog Writing for SEO\n\n**Structure of a high-ranking blog post:**\n1. Title: Include primary keyword, 50-60 characters\n2. Introduction: Hook, problem statement, what the reader will learn\n3. Table of contents (for long posts)\n4. Body: H2 and H3 headings, short paragraphs, bullet points\n5. Images: Relevant, compressed, with alt text\n6. Internal links: Link to related posts on your site\n7. External links: Link to authoritative sources\n8. CTA: What should the reader do next?\n9. Meta description: 150-160 characters\n\n## Content Formats\n\n- **How-to guides**: Step-by-step instructions (high search intent)\n- **Listicles**: 10 Best X for Y (easy to scan, highly shareable)\n- **Case studies**: Real results with data (builds trust)\n- **Comparison posts**: X vs Y (captures decision-stage searchers)\n- **Ultimate guides**: Comprehensive resource on a topic (earns backlinks)\n- **Infographics**: Visual data (highly shareable)\n\n## Content Distribution\n\nCreate once, distribute everywhere:\n- Publish on your blog\n- Share on social media (different formats per platform)\n- Send to email list\n- Repurpose: Turn blog post into video, podcast, infographic\n- Syndicate: Republish on Medium, LinkedIn Articles\n\n## Practical Task\n\nWrite a 1,000-word SEO-optimised blog post for a fictional digital marketing agency. Topic: 5 Digital Marketing Strategies for Nigerian SMEs in 2025. Include: keyword-optimised title, meta description, proper heading structure, internal links, and a CTA.\n\n## Self-Check\n1. What is the key principle of content marketing?\n2. What are the 7 steps of a content marketing strategy?\n3. What is content repurposing and why is it valuable?",
5)

L(8,"Google Ads & Paid Advertising",
"## What is Pay-Per-Click (PPC) Advertising?\n\nPPC advertising means you pay each time someone clicks your ad. Google Ads is the largest PPC platform, showing ads in Google search results and across the web.\n\n## Google Ads Campaign Types\n\n- **Search campaigns**: Text ads shown in Google search results\n- **Display campaigns**: Image/banner ads shown on websites in the Google Display Network\n- **Shopping campaigns**: Product listings with images and prices\n- **Video campaigns**: Ads on YouTube\n- **Performance Max**: AI-driven campaigns across all Google channels\n\n## Search Campaign Structure\n\nCampaign > Ad Groups > Keywords > Ads\n\n**Campaign**: Set budget, location, language, bidding strategy\n**Ad Group**: Group of related keywords (e.g. digital marketing courses)\n**Keywords**: The search terms that trigger your ads\n**Ads**: The text shown to users\n\n## Keyword Match Types\n\n- **Broad match**: digital marketing course (shows for related searches)\n- **Phrase match**: \"digital marketing course\" (must contain this phrase)\n- **Exact match**: [digital marketing course] (must match exactly)\n- **Negative keywords**: -free (exclude searches containing this word)\n\n## Writing Effective Ad Copy\n\nGoogle Search Ad structure:\n- Headline 1 (30 chars): Include primary keyword\n- Headline 2 (30 chars): Key benefit or USP\n- Headline 3 (30 chars): Call to action\n- Description 1 (90 chars): Expand on the benefit\n- Description 2 (90 chars): Social proof or urgency\n- Display URL: yoursite.com/digital-marketing\n\n## Quality Score\n\nGoogle rates your ads 1-10 based on:\n- Expected CTR\n- Ad relevance to the keyword\n- Landing page experience\n\nHigher Quality Score = lower cost per click and better ad position.\n\n## Key Metrics\n\n- **Impressions**: How many times your ad was shown\n- **Clicks**: How many times your ad was clicked\n- **CTR**: Clicks / Impressions x 100\n- **CPC (Cost Per Click)**: Total spend / Clicks\n- **Conversion rate**: Conversions / Clicks x 100\n- **ROAS (Return on Ad Spend)**: Revenue / Ad spend\n\n## Practical Task\n\nCreate a Google Ads campaign plan for a fictional Lagos-based digital marketing course. Define: campaign type, target keywords (10 keywords with match types), 3 negative keywords, 2 ad copy variations, target CPA, and monthly budget.\n\n## Self-Check\n1. What is the difference between broad match and exact match keywords?\n2. What is Quality Score and what affects it?\n3. How is ROAS calculated?",
6)

L(8,"Analytics & Data-Driven Marketing",
"## Why Analytics Matters\n\nData-driven marketing means making decisions based on evidence, not intuition. Analytics tells you what is working, what is not, and where to invest your budget.\n\n## Google Analytics 4 (GA4)\n\nGA4 is Google's current analytics platform. Key concepts:\n\n**Events**: Every user interaction is an event (page_view, click, scroll, purchase).\n**Parameters**: Additional data attached to events (page_title, item_name, value).\n**Conversions**: Events you mark as important business goals.\n**Dimensions**: Attributes of your data (country, device, source).\n**Metrics**: Quantitative measurements (sessions, users, revenue).\n\n## Key GA4 Reports\n\n- **Acquisition**: Where your traffic comes from (organic, paid, social, email, direct)\n- **Engagement**: How users interact with your site (pages viewed, time on site, scroll depth)\n- **Monetisation**: Revenue, transactions, average order value\n- **Retention**: How many users return\n- **Demographics**: Age, gender, location, interests\n\n## UTM Parameters\n\nUTM parameters track the source of your traffic in GA4:\n\nhttps://yoursite.com/course?utm_source=facebook&utm_medium=social&utm_campaign=course_launch\n\n- utm_source: Where the traffic comes from (facebook, google, newsletter)\n- utm_medium: The marketing channel (social, cpc, email)\n- utm_campaign: The specific campaign name\n- utm_content: The specific ad or link (for A/B testing)\n\nUse Google Campaign URL Builder to create UTM links.\n\n## Conversion Rate Optimisation (CRO)\n\nCRO is the process of increasing the percentage of visitors who take a desired action.\n\nCRO process:\n1. Analyse: Find pages with high traffic but low conversion\n2. Hypothesise: Why is the conversion rate low?\n3. Test: A/B test your hypothesis\n4. Implement: Roll out the winning variation\n5. Repeat\n\nTools: Google Optimize (free), VWO, Optimizely, Hotjar.\n\n## Reporting\n\nA good marketing report includes:\n- Executive summary: Key wins and challenges\n- KPI dashboard: Traffic, leads, conversions, revenue\n- Channel performance: Which channels drove the most value\n- Campaign results: Specific campaign metrics\n- Recommendations: What to do next month\n\n## Practical Task\n\nSet up Google Analytics 4 on a test website. Create a custom report showing traffic by source/medium. Set up 3 conversion events. Add UTM parameters to 5 sample marketing links. Write a 1-page monthly marketing report template.\n\n## Self-Check\n1. What is the difference between a dimension and a metric in GA4?\n2. What are UTM parameters and why are they important?\n3. What is Conversion Rate Optimisation?",
7)

L(8,"Capstone: Full Digital Marketing Campaign",
"## Final Project Brief\n\nYou will plan and execute a complete digital marketing campaign for a fictional Nigerian business.\n\n## Business Scenario\n\nYour client is TechLearn Nigeria: an online learning platform offering tech courses to Nigerian professionals aged 22-35. They have a monthly marketing budget of 500,000 NGN and want to acquire 200 new students in 3 months.\n\n## Deliverables\n\n### 1. Marketing Strategy Document\n- Target audience analysis (2 personas)\n- Competitive analysis (3 competitors)\n- SWOT analysis\n- Channel selection with rationale\n- Budget allocation across channels\n- KPIs and targets for each channel\n\n### 2. SEO Plan\n- 20 target keywords with search volume and difficulty\n- On-page optimisation checklist for 5 key pages\n- 3-month content calendar (12 blog post topics)\n- Link building strategy\n\n### 3. Social Media Plan\n- Platform selection (2 platforms) with rationale\n- 30-day content calendar with post copy and visuals\n- Paid social campaign: audience targeting, ad copy, budget\n\n### 4. Email Marketing\n- Lead magnet concept\n- 5-email welcome sequence (full copy)\n- Monthly newsletter template\n\n### 5. Paid Advertising\n- Google Ads campaign structure\n- 10 keywords with match types\n- 3 ad copy variations\n- Landing page brief\n\n### 6. Analytics & Reporting\n- KPI dashboard template\n- Monthly reporting template\n- Attribution model recommendation\n\n## Evaluation Criteria\n- Strategic thinking and audience insight (25%)\n- Channel strategy and budget allocation (20%)\n- Content quality and creativity (25%)\n- Technical accuracy (SEO, ads, analytics) (20%)\n- Presentation and professionalism (10%)\n\n## Self-Check\n1. Does your budget allocation reflect the channels most likely to reach your target audience?\n2. Are your KPIs specific, measurable, and time-bound?\n3. How will you attribute conversions across multiple channels?",
8)


# ── Courses 9-16: Concise but real content ────────────────────────────────────

# Course 9: Data Analysis
for i,(t,c) in enumerate([
("Introduction to Data Analysis","## What is Data Analysis?\n\nData analysis is the process of inspecting, cleaning, transforming, and modelling data to discover useful information, draw conclusions, and support decision-making.\n\n## The Data Analysis Process\n\n1. **Ask**: Define the business question. What problem are we solving?\n2. **Collect**: Gather data from relevant sources.\n3. **Clean**: Remove errors, duplicates, and inconsistencies.\n4. **Analyse**: Apply statistical and analytical techniques.\n5. **Visualise**: Create charts and dashboards to communicate findings.\n6. **Act**: Make data-driven recommendations.\n\n## Types of Data Analysis\n\n- **Descriptive**: What happened? (Sales last month were 2.3M NGN)\n- **Diagnostic**: Why did it happen? (Sales dropped because of a competitor promotion)\n- **Predictive**: What will happen? (Sales will grow 15% next quarter)\n- **Prescriptive**: What should we do? (Increase ad spend in the North-West region)\n\n## Data Types\n\n**Quantitative (numerical):**\n- Discrete: Countable values (number of students, number of orders)\n- Continuous: Any value in a range (height, temperature, revenue)\n\n**Qualitative (categorical):**\n- Nominal: No order (gender, country, product category)\n- Ordinal: Has order (rating 1-5, education level)\n\n## Tools for Data Analysis\n\n- **Microsoft Excel / Google Sheets**: Accessible, widely used, good for small datasets\n- **Python (pandas, numpy)**: Powerful, free, handles large datasets\n- **R**: Statistical computing, popular in academia\n- **SQL**: Query databases directly\n- **Power BI / Tableau**: Data visualisation and dashboards\n- **Google Looker Studio**: Free dashboards connected to Google data\n\n## Practical Task\n\nDownload a free dataset from Kaggle (e.g. Nigerian e-commerce sales data). Open it in Excel or Google Sheets. Answer 5 business questions using formulas and pivot tables. Create 3 charts to visualise your findings.\n\n## Self-Check\n1. What are the 6 steps of the data analysis process?\n2. What is the difference between descriptive and predictive analysis?\n3. What is the difference between quantitative and qualitative data?"),
("Excel & Google Sheets for Analysis","## Excel/Sheets as an Analysis Tool\n\nSpreadsheets are the most widely used data analysis tool in business. Mastering them is essential for any data analyst.\n\n## Essential Functions\n\n```\n=SUM(A2:A100)          -- Add a range\n=AVERAGE(B2:B100)      -- Calculate mean\n=COUNT(C2:C100)        -- Count numbers\n=COUNTA(D2:D100)       -- Count non-empty cells\n=MAX(E2:E100)          -- Largest value\n=MIN(F2:F100)          -- Smallest value\n=IF(A2>100,\"High\",\"Low\")  -- Conditional logic\n=VLOOKUP(A2,Sheet2!A:B,2,FALSE)  -- Look up value\n=COUNTIF(A:A,\"Lagos\")  -- Count matching cells\n=SUMIF(A:A,\"Lagos\",B:B) -- Sum where condition is met\n=AVERAGEIF(A:A,\"Lagos\",B:B) -- Average where condition is met\n```\n\n## Pivot Tables\n\nPivot tables summarise large datasets quickly:\n1. Select your data range\n2. Insert > Pivot Table\n3. Drag fields to Rows, Columns, Values, Filters\n4. Change value aggregation: Sum, Count, Average, Max\n\nExample: Sales by region and product category.\n\n## Data Cleaning in Spreadsheets\n\n- Remove duplicates: Data > Remove Duplicates\n- Trim whitespace: =TRIM(A2)\n- Fix case: =PROPER(A2), =UPPER(A2), =LOWER(A2)\n- Split text: Data > Split text to columns\n- Find and replace: Ctrl+H\n- Filter and sort: Data > Filter\n\n## Charts & Visualisation\n\nChoose the right chart type:\n- **Bar/Column chart**: Compare categories\n- **Line chart**: Show trends over time\n- **Pie chart**: Show proportions (use sparingly, max 5 slices)\n- **Scatter plot**: Show correlation between two variables\n- **Histogram**: Show distribution of a single variable\n\n## Practical Task\n\nUsing a sales dataset (create or download one), build a dashboard in Google Sheets with: total revenue, revenue by region (bar chart), monthly trend (line chart), top 5 products (table), and a slicer to filter by date range.\n\n## Self-Check\n1. What is the difference between VLOOKUP and INDEX/MATCH?\n2. What is a pivot table and when would you use one?\n3. Which chart type is best for showing a trend over time?"),
("SQL for Data Analysis","## SQL in Data Analysis\n\nSQL (Structured Query Language) is the standard language for querying databases. Most business data lives in relational databases, making SQL an essential skill for data analysts.\n\n## Core SQL for Analysis\n\n```sql\n-- Basic SELECT\nSELECT customer_name, order_date, total_amount\nFROM orders\nWHERE total_amount > 50000\nORDER BY total_amount DESC\nLIMIT 10;\n\n-- Aggregate functions\nSELECT\n    region,\n    COUNT(*) AS order_count,\n    SUM(total_amount) AS total_revenue,\n    AVG(total_amount) AS avg_order_value,\n    MAX(total_amount) AS largest_order\nFROM orders\nGROUP BY region\nHAVING COUNT(*) > 100\nORDER BY total_revenue DESC;\n\n-- JOIN for combining tables\nSELECT\n    c.name AS customer,\n    COUNT(o.id) AS total_orders,\n    SUM(o.total_amount) AS lifetime_value\nFROM customers c\nLEFT JOIN orders o ON o.customer_id = c.id\nGROUP BY c.id, c.name\nORDER BY lifetime_value DESC;\n\n-- Date functions\nSELECT\n    DATE_FORMAT(order_date, '%Y-%m') AS month,\n    COUNT(*) AS orders,\n    SUM(total_amount) AS revenue\nFROM orders\nWHERE order_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)\nGROUP BY month\nORDER BY month;\n\n-- Subquery\nSELECT * FROM customers\nWHERE id IN (\n    SELECT customer_id FROM orders\n    WHERE total_amount > 100000\n);\n```\n\n## Window Functions\n\n```sql\nSELECT\n    customer_name,\n    total_amount,\n    RANK() OVER (ORDER BY total_amount DESC) AS revenue_rank,\n    SUM(total_amount) OVER () AS grand_total,\n    total_amount / SUM(total_amount) OVER () * 100 AS pct_of_total\nFROM orders;\n```\n\n## Practical Task\n\nUsing the LMS database schema, write SQL queries to answer: (1) How many students enrolled per month? (2) Which course has the highest revenue? (3) What is the average payment per student? (4) Which students have an outstanding balance? (5) What is the monthly revenue trend for the last 6 months?\n\n## Self-Check\n1. What is the difference between WHERE and HAVING?\n2. What does a LEFT JOIN return that an INNER JOIN does not?\n3. What is a window function?"),
("Python for Data Analysis","## Why Python for Data Analysis?\n\nPython is the most popular language for data analysis and data science. Its libraries (pandas, numpy, matplotlib) make it easy to work with large datasets, perform complex analysis, and create visualisations.\n\n## Setting Up\n\n```bash\n# Install Anaconda (includes Python + all data science libraries)\n# Or install individually:\npip install pandas numpy matplotlib seaborn jupyter\n\n# Launch Jupyter Notebook\njupyter notebook\n```\n\n## pandas Fundamentals\n\n```python\nimport pandas as pd\nimport numpy as np\n\n# Load data\ndf = pd.read_csv('sales_data.csv')\ndf = pd.read_excel('sales_data.xlsx')\n\n# Explore data\ndf.head(10)          # First 10 rows\ndf.info()            # Column types and null counts\ndf.describe()        # Statistical summary\ndf.shape             # (rows, columns)\ndf.columns           # Column names\ndf.isnull().sum()    # Count missing values per column\n\n# Select data\ndf['revenue']                    # Single column\ndf[['name', 'revenue', 'date']]  # Multiple columns\ndf[df['revenue'] > 50000]        # Filter rows\ndf.loc[0:5, 'name':'revenue']    # Slice by label\n\n# Aggregate\ndf.groupby('region')['revenue'].sum()\ndf.groupby('region').agg({'revenue': ['sum', 'mean', 'count']})\n\n# Clean data\ndf.dropna()                          # Remove rows with any null\ndf.fillna(0)                         # Replace nulls with 0\ndf.drop_duplicates()                 # Remove duplicate rows\ndf['date'] = pd.to_datetime(df['date'])  # Convert to datetime\ndf['revenue'] = df['revenue'].str.replace(',','').astype(float)\n```\n\n## Data Visualisation with matplotlib\n\n```python\nimport matplotlib.pyplot as plt\n\n# Line chart\ndf.groupby('month')['revenue'].sum().plot(kind='line')\nplt.title('Monthly Revenue')\nplt.xlabel('Month')\nplt.ylabel('Revenue (NGN)')\nplt.tight_layout()\nplt.savefig('revenue_trend.png')\nplt.show()\n\n# Bar chart\ndf.groupby('region')['revenue'].sum().plot(kind='bar', color='steelblue')\nplt.xticks(rotation=45)\nplt.show()\n```\n\n## Practical Task\n\nDownload a Nigerian business dataset from Kaggle. Load it into a Jupyter Notebook using pandas. Clean the data (handle nulls, fix data types). Answer 5 business questions with code. Create 4 visualisations. Export your findings as a PDF report.\n\n## Self-Check\n1. What is the difference between df.loc and df.iloc?\n2. How do you handle missing values in pandas?\n3. What is the difference between groupby and pivot_table?"),
("Data Visualisation & Dashboards","## Principles of Data Visualisation\n\nGood data visualisation communicates insights clearly and honestly. Bad visualisation misleads or confuses.\n\n## Chart Selection Guide\n\n| Goal | Chart Type |\n|---|---|\n| Compare categories | Bar chart, Column chart |\n| Show trend over time | Line chart, Area chart |\n| Show proportions | Pie chart (max 5 slices), Treemap |\n| Show distribution | Histogram, Box plot |\n| Show correlation | Scatter plot, Bubble chart |\n| Show geographic data | Map chart, Choropleth |\n| Show part-to-whole | Stacked bar, Waterfall |\n\n## Visualisation Best Practices\n\n1. Start the y-axis at zero (unless showing change)\n2. Use colour purposefully (not decoratively)\n3. Label axes clearly with units\n4. Use a descriptive title that states the insight\n5. Remove chart junk (unnecessary gridlines, borders, 3D effects)\n6. Highlight the key data point\n7. Keep it simple — one insight per chart\n\n## Power BI Fundamentals\n\nPower BI is Microsoft's business intelligence tool:\n1. Get Data: Connect to Excel, SQL, web, or 100+ data sources\n2. Transform: Clean and shape data in Power Query\n3. Model: Define relationships between tables\n4. Visualise: Drag fields onto the canvas to create charts\n5. Publish: Share dashboards with your organisation\n\n## DAX (Data Analysis Expressions)\n\n```dax\n-- Total Revenue\nTotal Revenue = SUM(Orders[Amount])\n\n-- Revenue YTD\nRevenue YTD = TOTALYTD(SUM(Orders[Amount]), Dates[Date])\n\n-- Month-over-Month Growth\nMoM Growth = \n    DIVIDE(\n        [Total Revenue] - CALCULATE([Total Revenue], PREVIOUSMONTH(Dates[Date])),\n        CALCULATE([Total Revenue], PREVIOUSMONTH(Dates[Date]))\n    )\n```\n\n## Google Looker Studio (Free)\n\nLooker Studio connects to Google Analytics, Google Sheets, BigQuery, and more. Create interactive dashboards and share with a link.\n\n## Practical Task\n\nBuild a sales dashboard in Power BI or Google Looker Studio. Include: total revenue KPI card, revenue by region (bar chart), monthly trend (line chart), top 10 customers (table), and a date range filter. The dashboard should update automatically when data changes.\n\n## Self-Check\n1. Which chart type would you use to show the distribution of student ages?\n2. Why should you start the y-axis at zero?\n3. What is DAX and what is it used for?"),
("Statistics for Data Analysis","## Why Statistics?\n\nStatistics provides the mathematical foundation for data analysis. Without it, you cannot distinguish meaningful patterns from random noise.\n\n## Descriptive Statistics\n\n**Measures of Central Tendency:**\n- Mean: Sum of all values / count. Sensitive to outliers.\n- Median: Middle value when sorted. Robust to outliers.\n- Mode: Most frequent value.\n\n**Measures of Spread:**\n- Range: Max - Min\n- Variance: Average squared deviation from the mean\n- Standard Deviation: Square root of variance. Same units as the data.\n- IQR (Interquartile Range): Q3 - Q1. Robust to outliers.\n\n**Example:**\nStudent scores: 45, 60, 65, 70, 72, 75, 78, 80, 85, 95\nMean = 72.5, Median = 73.5, Mode = none, Std Dev = 13.8\n\n## Probability Distributions\n\n**Normal Distribution (Bell Curve):**\n- Symmetric around the mean\n- 68% of data within 1 standard deviation\n- 95% within 2 standard deviations\n- 99.7% within 3 standard deviations\n\n**Skewness:**\n- Right-skewed (positive): Long tail to the right. Mean > Median. (Income distribution)\n- Left-skewed (negative): Long tail to the left. Mean < Median.\n\n## Correlation\n\nCorrelation measures the strength and direction of the relationship between two variables.\n\n- Correlation coefficient (r): -1 to +1\n- r = +1: Perfect positive correlation\n- r = -1: Perfect negative correlation\n- r = 0: No linear correlation\n\nImportant: Correlation does not imply causation.\n\n## Hypothesis Testing\n\n1. State the null hypothesis (H0): There is no effect.\n2. State the alternative hypothesis (H1): There is an effect.\n3. Choose significance level (alpha = 0.05 is standard).\n4. Calculate the test statistic and p-value.\n5. If p-value < alpha, reject H0.\n\n## Practical Task\n\nUsing a dataset of student exam scores, calculate: mean, median, mode, standard deviation, and IQR. Create a histogram to visualise the distribution. Test whether students who attended more than 80% of classes scored significantly higher than those who attended less.\n\n## Self-Check\n1. What is the difference between mean and median? When is each more appropriate?\n2. What does a correlation coefficient of -0.8 mean?\n3. What is a p-value?"),
("Business Intelligence & Reporting","## What is Business Intelligence?\n\nBusiness Intelligence (BI) is the process of collecting, analysing, and presenting business data to support better decision-making. BI turns raw data into actionable insights.\n\n## BI vs Data Science\n\n**Business Intelligence**: Focuses on historical data. What happened? Why? Descriptive and diagnostic analysis. Tools: Power BI, Tableau, Looker.\n\n**Data Science**: Focuses on future predictions and complex modelling. What will happen? What should we do? Predictive and prescriptive analysis. Tools: Python, R, machine learning.\n\n## KPIs (Key Performance Indicators)\n\nKPIs are measurable values that demonstrate how effectively a company is achieving its objectives.\n\n**Good KPIs are:**\n- Specific: Clearly defined\n- Measurable: Can be quantified\n- Achievable: Realistic targets\n- Relevant: Aligned with business goals\n- Time-bound: Have a deadline\n\n**Examples by department:**\n- Sales: Monthly revenue, conversion rate, average deal size\n- Marketing: CAC, ROAS, organic traffic growth\n- Operations: Order fulfilment time, defect rate, customer satisfaction\n- HR: Employee turnover rate, time to hire, training completion rate\n\n## Dashboard Design Principles\n\n1. Know your audience: What decisions will this dashboard support?\n2. Prioritise: Show the most important KPIs first (top-left)\n3. Context: Show targets, benchmarks, and period-over-period comparisons\n4. Interactivity: Allow filtering by date, region, product\n5. Simplicity: Remove everything that does not add value\n6. Consistency: Use the same colours, fonts, and chart styles throughout\n\n## Storytelling with Data\n\nData storytelling combines data, visuals, and narrative to communicate insights persuasively:\n1. Context: What is the situation?\n2. Conflict: What is the problem or opportunity?\n3. Resolution: What does the data show we should do?\n\n## Practical Task\n\nBuild an executive dashboard for a fictional Nigerian retail company. Include: 5 KPI cards (revenue, orders, customers, AOV, return rate), revenue trend (12 months), top 5 products, regional performance map, and a month-over-month comparison table. Present it as a 5-minute data story.\n\n## Self-Check\n1. What is the difference between BI and data science?\n2. What makes a good KPI? Name the 5 criteria.\n3. What are the 3 components of data storytelling?"),
("Capstone: Data Analysis Project","## Final Project Brief\n\nYou will conduct a complete data analysis project from raw data to actionable business recommendations.\n\n## Project Scenario\n\nYou are a data analyst at a Nigerian e-commerce company. The CEO wants to understand why revenue declined 18% in Q3 compared to Q2, and what actions to take to recover in Q4.\n\n## Dataset\n\nYou will be provided with (or create) a dataset containing:\n- 12 months of order data (order_id, date, customer_id, product, category, region, amount, status)\n- Customer data (customer_id, name, city, state, registration_date, segment)\n- Product data (product_id, name, category, cost_price, selling_price)\n\n## Analysis Requirements\n\n### 1. Data Cleaning\n- Identify and handle missing values\n- Remove duplicates\n- Fix data type issues\n- Document all cleaning steps\n\n### 2. Exploratory Data Analysis\n- Revenue trend by month\n- Revenue by region, category, and customer segment\n- Top 10 products by revenue and by volume\n- Customer acquisition and retention rates\n- Average order value trend\n\n### 3. Root Cause Analysis\n- Identify which regions, categories, or segments drove the Q3 decline\n- Analyse customer behaviour changes (new vs returning customers)\n- Identify any product or category-level issues\n\n### 4. Recommendations\n- 3-5 specific, data-backed recommendations\n- Projected impact of each recommendation\n- Priority ranking\n\n## Deliverables\n- Jupyter Notebook or Excel workbook with all analysis\n- Power BI or Looker Studio dashboard\n- 10-slide presentation with data story\n- 1-page executive summary\n\n## Evaluation Criteria\n- Data cleaning thoroughness (15%)\n- Analysis depth and accuracy (30%)\n- Visualisation quality (20%)\n- Insight quality and business relevance (25%)\n- Presentation clarity (10%)\n\n## Self-Check\n1. Have you validated your findings against the raw data?\n2. Are your recommendations specific and actionable?\n3. Could a non-technical executive understand your presentation?"),
], 1):
    L(9, t, c, i)


# ── Courses 10-16: 6 lessons each ─────────────────────────────────────────────

# Course 10: Cybersecurity Fundamentals
for i,(t,c) in enumerate([
("Introduction to Cybersecurity","## What is Cybersecurity?\n\nCybersecurity is the practice of protecting systems, networks, and programs from digital attacks. These attacks aim to access, change, or destroy sensitive information, extort money, or disrupt normal business operations.\n\n## The CIA Triad\n\nThe three core principles of information security:\n\n**Confidentiality**: Ensuring information is accessible only to those authorised to access it.\n- Encryption, access controls, authentication\n\n**Integrity**: Ensuring information is accurate and has not been tampered with.\n- Hashing, digital signatures, checksums\n\n**Availability**: Ensuring systems and data are available when needed.\n- Redundancy, backups, DDoS protection\n\n## Types of Cyber Threats\n\n- **Malware**: Malicious software (viruses, worms, ransomware, spyware)\n- **Phishing**: Deceptive emails/messages to steal credentials or install malware\n- **Man-in-the-Middle (MitM)**: Intercepting communication between two parties\n- **SQL Injection**: Inserting malicious SQL code into a database query\n- **Cross-Site Scripting (XSS)**: Injecting malicious scripts into web pages\n- **DDoS (Distributed Denial of Service)**: Overwhelming a server with traffic\n- **Social Engineering**: Manipulating people into revealing confidential information\n- **Insider Threats**: Malicious or negligent actions by employees\n\n## The Cybersecurity Landscape in Nigeria\n\nNigeria loses an estimated $500 million annually to cybercrime. Common threats:\n- Business Email Compromise (BEC)\n- Online banking fraud\n- SIM swap attacks\n- Ransomware targeting businesses\n\n## Career Paths in Cybersecurity\n\n- Security Analyst: Monitor and respond to security incidents\n- Penetration Tester (Ethical Hacker): Find vulnerabilities before attackers do\n- Security Engineer: Build and maintain security systems\n- Incident Responder: Investigate and contain security breaches\n- CISO (Chief Information Security Officer): Lead an organisation's security strategy\n\n## Practical Task\n\nConduct a personal security audit. Check: Are your passwords unique and strong? Do you use 2FA on all important accounts? Is your software up to date? Have any of your accounts been breached (check haveibeenpwned.com)? Write a 1-page personal security improvement plan.\n\n## Self-Check\n1. What does the CIA triad stand for?\n2. What is the difference between a virus and ransomware?\n3. What is social engineering?"),
("Network Security Fundamentals","## How Networks Work\n\nUnderstanding networks is essential for cybersecurity. Most attacks travel over networks.\n\n## Network Basics\n\n**IP Address**: A unique identifier for a device on a network.\n- IPv4: 192.168.1.1 (32-bit, ~4.3 billion addresses)\n- IPv6: 2001:0db8:85a3::8a2e:0370:7334 (128-bit, virtually unlimited)\n\n**Ports**: Virtual endpoints for network communication.\n- Port 80: HTTP (unencrypted web)\n- Port 443: HTTPS (encrypted web)\n- Port 22: SSH (secure remote access)\n- Port 3306: MySQL database\n- Port 25: SMTP (email sending)\n\n**Protocols**: Rules for communication.\n- TCP: Reliable, connection-oriented (web, email, file transfer)\n- UDP: Fast, connectionless (video streaming, DNS, gaming)\n- HTTP/HTTPS: Web communication\n- DNS: Translates domain names to IP addresses\n\n## Network Security Controls\n\n**Firewall**: Monitors and controls incoming/outgoing network traffic based on rules.\n- Packet filtering: Inspect individual packets\n- Stateful inspection: Track connection state\n- Application layer: Inspect application-level traffic\n\n**VPN (Virtual Private Network)**: Encrypts all traffic between your device and a VPN server. Hides your IP address and protects data on public Wi-Fi.\n\n**IDS/IPS (Intrusion Detection/Prevention System)**: Monitors network traffic for suspicious activity.\n\n**Network Segmentation**: Divide the network into zones. If one zone is compromised, attackers cannot easily move to others.\n\n## Common Network Attacks\n\n- **Port scanning**: Discovering open ports on a target (Nmap tool)\n- **ARP poisoning**: Redirecting network traffic through the attacker's machine\n- **DNS spoofing**: Redirecting domain name lookups to malicious IP addresses\n- **Packet sniffing**: Capturing unencrypted network traffic (Wireshark tool)\n\n## Practical Task\n\nInstall Wireshark (free, wireshark.org). Capture network traffic on your local network for 5 minutes. Identify: what protocols are being used, which IP addresses are communicating, and any unencrypted HTTP traffic. Write a brief report of your findings.\n\n## Self-Check\n1. What is the difference between TCP and UDP?\n2. What does a firewall do?\n3. What is a VPN and when should you use one?"),
("Web Application Security","## OWASP Top 10\n\nThe OWASP (Open Web Application Security Project) Top 10 is the standard reference for web application security risks.\n\n## Top 5 Most Critical Vulnerabilities\n\n**1. Broken Access Control**\nUsers can access resources they should not be able to.\nExample: Changing ?user_id=123 to ?user_id=124 to view another user's data.\nFix: Validate authorisation on every request server-side.\n\n**2. Cryptographic Failures**\nSensitive data exposed due to weak or missing encryption.\nExample: Storing passwords in plain text or using MD5 hashing.\nFix: Use bcrypt for passwords. Use HTTPS for all data in transit. Encrypt sensitive data at rest.\n\n**3. Injection**\nUntrusted data sent to an interpreter as part of a command.\nExample: SQL injection, command injection, LDAP injection.\nFix: Use prepared statements. Validate and sanitise all input.\n\n**4. Insecure Design**\nMissing or ineffective security controls in the design phase.\nFix: Threat modelling during design. Security requirements alongside functional requirements.\n\n**5. Security Misconfiguration**\nDefault credentials, unnecessary features enabled, verbose error messages.\nExample: Leaving phpMyAdmin accessible on a production server.\nFix: Harden all configurations. Disable unused features. Use different credentials per environment.\n\n## Security Testing Tools\n\n- **Burp Suite**: Web application security testing (intercept and modify requests)\n- **OWASP ZAP**: Free web application scanner\n- **SQLMap**: Automated SQL injection testing\n- **Nikto**: Web server scanner\n\n## Secure Development Practices\n\n- Input validation: Validate all input on the server side\n- Output encoding: Encode all output to prevent XSS\n- Prepared statements: Prevent SQL injection\n- HTTPS everywhere: Encrypt all data in transit\n- Principle of least privilege: Grant minimum necessary permissions\n- Security headers: Content-Security-Policy, X-Frame-Options, HSTS\n\n## Practical Task\n\nSet up a deliberately vulnerable web application (DVWA - Damn Vulnerable Web Application) on your local XAMPP. Exploit the SQL injection vulnerability. Then fix it using prepared statements. Document the attack and the fix.\n\n## Self-Check\n1. What is SQL injection and how do you prevent it?\n2. What is the difference between authentication and authorisation?\n3. What is the principle of least privilege?"),
("Ethical Hacking & Penetration Testing","## What is Ethical Hacking?\n\nEthical hacking (penetration testing) is the authorised practice of bypassing system security to identify potential data breaches and threats. The key word is authorised — always get written permission before testing any system.\n\n## The Penetration Testing Process\n\n1. **Planning & Reconnaissance**: Define scope, gather information about the target\n2. **Scanning**: Identify open ports, services, and vulnerabilities\n3. **Gaining Access**: Exploit vulnerabilities to gain entry\n4. **Maintaining Access**: Simulate what an attacker would do after gaining access\n5. **Reporting**: Document findings, risk ratings, and remediation recommendations\n\n## Reconnaissance Tools\n\n- **Nmap**: Network scanner. Discover hosts, open ports, and services.\n  ```bash\n  nmap -sV -sC 192.168.1.1  # Scan with version detection and scripts\n  nmap -p 1-1000 192.168.1.1  # Scan first 1000 ports\n  ```\n- **Shodan**: Search engine for internet-connected devices\n- **theHarvester**: Gather emails, subdomains, and IPs from public sources\n- **Maltego**: Visual link analysis and data mining\n\n## Common Exploitation Techniques\n\n- **Password attacks**: Brute force, dictionary attacks, credential stuffing\n- **Phishing**: Craft convincing emails to steal credentials\n- **Metasploit**: Framework for developing and executing exploits\n- **Social engineering**: Manipulate people rather than systems\n\n## Certifications in Ethical Hacking\n\n- **CEH (Certified Ethical Hacker)**: EC-Council, widely recognised\n- **OSCP (Offensive Security Certified Professional)**: Hands-on, highly respected\n- **CompTIA Security+**: Entry-level, vendor-neutral\n- **CompTIA PenTest+**: Penetration testing focused\n\n## Legal & Ethical Considerations\n\n- Always get written authorisation before testing\n- Stay within the defined scope\n- Do not access data you are not authorised to view\n- Report all findings to the client\n- Never use skills for malicious purposes\n\n## Practical Task\n\nSet up a home lab using VirtualBox. Install Kali Linux (attacker) and Metasploitable 2 (vulnerable target). Use Nmap to scan Metasploitable. Identify 3 vulnerabilities. Research the CVE numbers for each. Write a penetration test report with risk ratings and remediation recommendations.\n\n## Self-Check\n1. What are the 5 phases of penetration testing?\n2. What is the difference between a vulnerability scan and a penetration test?\n3. Why is written authorisation essential before ethical hacking?"),
("Incident Response & Digital Forensics","## What is Incident Response?\n\nIncident response (IR) is the organised approach to addressing and managing the aftermath of a security breach or cyberattack. The goal is to handle the situation in a way that limits damage and reduces recovery time and costs.\n\n## The Incident Response Process (NIST)\n\n1. **Preparation**: Establish IR team, tools, and procedures before an incident occurs\n2. **Detection & Analysis**: Identify that an incident has occurred and understand its scope\n3. **Containment**: Stop the spread of the attack\n4. **Eradication**: Remove the threat from the environment\n5. **Recovery**: Restore systems to normal operation\n6. **Post-Incident Activity**: Learn from the incident and improve defences\n\n## Common Security Incidents\n\n- Ransomware attack: Encrypt files and demand payment\n- Data breach: Unauthorised access to sensitive data\n- DDoS attack: Overwhelm servers with traffic\n- Insider threat: Employee steals or leaks data\n- Phishing compromise: Employee credentials stolen\n\n## Digital Forensics\n\nDigital forensics is the process of collecting, preserving, and analysing digital evidence.\n\n**Forensic Principles:**\n- Preserve the original evidence (work on a copy)\n- Maintain chain of custody (document who handled evidence and when)\n- Document everything\n- Use validated tools\n\n**Common Forensic Tasks:**\n- Disk imaging: Create a bit-for-bit copy of a hard drive\n- Memory analysis: Examine RAM for running processes and network connections\n- Log analysis: Review system, application, and network logs\n- File recovery: Recover deleted files\n- Timeline analysis: Reconstruct the sequence of events\n\n## Tools\n\n- **Autopsy**: Free digital forensics platform\n- **Volatility**: Memory forensics framework\n- **Wireshark**: Network traffic analysis\n- **FTK (Forensic Toolkit)**: Commercial forensics suite\n- **Splunk**: Log management and SIEM\n\n## Practical Task\n\nSimulate a ransomware incident response. Scenario: A company employee opened a phishing email and ransomware encrypted 50 files. Write a complete incident response report covering all 6 NIST phases. Include: timeline of events, containment actions, eradication steps, recovery plan, and lessons learned.\n\n## Self-Check\n1. What are the 6 phases of the NIST incident response process?\n2. What is the chain of custody in digital forensics?\n3. What is the first thing you should do when you discover a ransomware infection?"),
("Cybersecurity Capstone","## Final Project: Security Assessment\n\nYou will conduct a comprehensive security assessment of a fictional organisation and produce a professional security report.\n\n## Scenario\n\nYou have been hired as a cybersecurity consultant for MirrorBank Nigeria, a fictional digital bank. They have experienced a suspicious increase in failed login attempts and want a full security assessment.\n\n## Assessment Scope\n\n### 1. Threat Modelling\n- Identify assets (customer data, financial transactions, admin systems)\n- Identify threats (external attackers, insider threats, third-party vendors)\n- Identify vulnerabilities\n- Calculate risk = Likelihood x Impact\n- Prioritise risks by severity\n\n### 2. Network Security Review\n- Review firewall rules\n- Identify open ports and services\n- Check for network segmentation\n- Review VPN and remote access controls\n\n### 3. Web Application Assessment\n- Test for OWASP Top 10 vulnerabilities\n- Review authentication mechanisms (password policy, MFA)\n- Check session management\n- Review API security\n\n### 4. Social Engineering Assessment\n- Design a phishing simulation email\n- Identify which employees would be most vulnerable\n- Recommend security awareness training\n\n### 5. Incident Response Plan Review\n- Evaluate the existing IR plan (or create one if none exists)\n- Identify gaps\n- Recommend improvements\n\n## Deliverables\n- Executive summary (1 page, non-technical)\n- Technical findings report (detailed vulnerabilities with CVE references)\n- Risk register (all risks with likelihood, impact, and priority)\n- Remediation roadmap (short-term, medium-term, long-term actions)\n- Security awareness training outline\n\n## Evaluation Criteria\n- Thoroughness of assessment (25%)\n- Accuracy of risk ratings (20%)\n- Quality of remediation recommendations (25%)\n- Report professionalism (20%)\n- Presentation clarity (10%)\n\n## Self-Check\n1. Are your risk ratings consistent and justified?\n2. Are your remediation recommendations specific and actionable?\n3. Could a non-technical executive understand your executive summary?"),
], 1):
    L(10, t, c, i)


# Course 11: Computer Fundamentals
for i,(t,c) in enumerate([
("Computer Hardware & Components","## What is a Computer?\n\nA computer is an electronic device that processes data according to instructions (programs). It accepts input, processes it, stores results, and produces output.\n\n## Core Hardware Components\n\n**CPU (Central Processing Unit)**: The brain of the computer. Executes instructions.\n- Clock speed: Measured in GHz (e.g. 3.5 GHz = 3.5 billion cycles per second)\n- Cores: Modern CPUs have 4-16+ cores for parallel processing\n- Cache: Ultra-fast memory built into the CPU (L1, L2, L3)\n- Popular brands: Intel (Core i3/i5/i7/i9), AMD (Ryzen)\n\n**RAM (Random Access Memory)**: Temporary working memory. Faster than storage.\n- Measured in GB (8GB minimum for modern use, 16GB recommended)\n- Data is lost when power is off\n- More RAM = more programs running simultaneously\n\n**Storage**: Permanent data storage.\n- HDD (Hard Disk Drive): Mechanical, slower, cheaper, larger capacity\n- SSD (Solid State Drive): No moving parts, much faster, more expensive\n- NVMe SSD: Even faster, connects directly to the motherboard\n\n**Motherboard**: The main circuit board connecting all components.\n**GPU (Graphics Processing Unit)**: Handles visual output. Essential for gaming, video editing, AI.\n**PSU (Power Supply Unit)**: Converts AC power to DC power for components.\n**Cooling**: Fans, heat sinks, or liquid cooling to prevent overheating.\n\n## Input & Output Devices\n\nInput: Keyboard, mouse, microphone, webcam, scanner, touchscreen\nOutput: Monitor, printer, speakers, projector\nStorage I/O: USB drives, external hard drives, SD cards\n\n## Practical Task\n\nIdentify all hardware components in a computer (your own or a lab computer). Record the CPU model, RAM size, storage type and size, and GPU. Research the specifications online and determine if the computer meets the requirements for: (1) basic office work, (2) video editing, (3) gaming.\n\n## Self-Check\n1. What is the difference between RAM and storage?\n2. What is the difference between an HDD and an SSD?\n3. What does the CPU do?"),
("Operating Systems","## What is an Operating System?\n\nAn operating system (OS) is system software that manages computer hardware and software resources and provides common services for computer programs.\n\n## Major Operating Systems\n\n**Windows (Microsoft)**\n- Most widely used desktop OS (~75% market share)\n- Best for: Office work, gaming, business software\n- Versions: Windows 10, Windows 11\n\n**macOS (Apple)**\n- Unix-based, exclusive to Apple hardware\n- Best for: Creative professionals (design, video, music)\n- Known for stability and integration with iPhone/iPad\n\n**Linux**\n- Open-source, free, highly customisable\n- Best for: Servers, developers, cybersecurity professionals\n- Popular distributions: Ubuntu, Fedora, Debian, Kali Linux\n- Powers ~96% of the world's top 1 million web servers\n\n**Android & iOS**: Mobile operating systems.\n\n## OS Functions\n\n- **Process management**: Schedule and manage running programs\n- **Memory management**: Allocate RAM to programs\n- **File system management**: Organise files and directories\n- **Device management**: Communicate with hardware via drivers\n- **Security**: User accounts, permissions, firewall\n- **User interface**: GUI (graphical) or CLI (command line)\n\n## File Systems\n\n- **NTFS**: Windows default. Supports large files, permissions, encryption.\n- **FAT32**: Compatible with all devices. Max file size 4GB.\n- **exFAT**: For USB drives. No file size limit.\n- **ext4**: Linux default.\n- **APFS**: macOS default.\n\n## Command Line Basics (Windows)\n\n```cmd\ndir                    -- List files in current directory\ncd Documents           -- Change directory\nmkdir NewFolder        -- Create a folder\ncopy file.txt backup/  -- Copy a file\ndel file.txt           -- Delete a file\nipconfig               -- Show network configuration\nping google.com        -- Test network connectivity\ntasklist               -- List running processes\n```\n\n## Practical Task\n\nComplete these tasks on your computer: (1) Navigate the file system using only the command line. (2) Create a folder structure for a project. (3) Check your IP address and test connectivity to 3 websites using ping. (4) List all running processes and identify any unfamiliar ones.\n\n## Self-Check\n1. What are the 3 major desktop operating systems?\n2. What is the difference between a GUI and a CLI?\n3. What command shows your IP address on Windows?"),
("Microsoft Office Productivity","## Microsoft Office Suite\n\nMicrosoft Office is the most widely used productivity software in business. Proficiency in Office is required for most office jobs.\n\n## Microsoft Word\n\n**Essential Skills:**\n- Formatting: Font, size, bold, italic, underline, colour\n- Paragraph formatting: Alignment, line spacing, indentation\n- Styles: Heading 1, Heading 2, Normal — for consistent formatting\n- Tables: Insert, format, merge cells\n- Mail merge: Create personalised letters from a data source\n- Track changes: Collaborate with others on a document\n- Table of contents: Auto-generated from heading styles\n\n**Professional Document Tips:**\n- Use styles, not manual formatting\n- Set margins: 2.5cm all sides for formal documents\n- Use page numbers and headers/footers\n- Save as PDF for sharing\n\n## Microsoft Excel\n\n**Essential Skills:**\n- Data entry and formatting\n- Formulas: SUM, AVERAGE, IF, VLOOKUP, COUNTIF\n- Sorting and filtering\n- Pivot tables\n- Charts and graphs\n- Conditional formatting\n- Data validation\n\n## Microsoft PowerPoint\n\n**Presentation Design Principles:**\n- One idea per slide\n- Maximum 6 bullet points per slide\n- Use images instead of text where possible\n- Consistent theme and colour scheme\n- Large, readable fonts (minimum 24pt for body text)\n- Slide notes for speaker reference\n\n**Presentation Delivery:**\n- Practice until you can present without reading slides\n- Make eye contact with the audience\n- Use the 10-20-30 rule: 10 slides, 20 minutes, 30pt minimum font\n\n## Google Workspace\n\nGoogle Docs, Sheets, and Slides are free, cloud-based alternatives:\n- Real-time collaboration\n- Auto-save to Google Drive\n- Access from any device\n- Free with a Google account\n\n## Practical Task\n\nCreate a professional CV in Microsoft Word using styles and proper formatting. Create a budget spreadsheet in Excel with formulas, conditional formatting, and a chart. Create a 10-slide presentation in PowerPoint about a topic of your choice. Apply the 10-20-30 rule.\n\n## Self-Check\n1. What is the 10-20-30 rule for presentations?\n2. What is the difference between a formula and a function in Excel?\n3. Why should you use Styles in Word instead of manual formatting?"),
("Internet & Email Fundamentals","## How the Internet Works\n\nThe internet is a global network of interconnected computers. When you visit a website:\n1. You type a URL (e.g. www.google.com)\n2. Your computer asks a DNS server to translate the domain to an IP address\n3. Your browser sends an HTTP/HTTPS request to that IP address\n4. The web server sends back the HTML, CSS, and JavaScript files\n5. Your browser renders the page\n\n## Web Browsers\n\nPopular browsers: Chrome, Firefox, Edge, Safari, Brave.\n\n**Browser Features:**\n- Address bar: Type URLs or search queries\n- Bookmarks: Save frequently visited pages\n- Extensions: Add functionality (ad blockers, password managers)\n- Developer Tools (F12): Inspect HTML, CSS, network requests\n- Private/Incognito mode: Does not save browsing history locally\n\n## Internet Safety\n\n- Use HTTPS websites (look for the padlock icon)\n- Do not click suspicious links in emails or messages\n- Use strong, unique passwords for every account\n- Enable two-factor authentication (2FA)\n- Keep software and browsers updated\n- Use a reputable antivirus program\n- Be careful what you share on social media\n- Use a VPN on public Wi-Fi\n\n## Email Fundamentals\n\n**Email Anatomy:**\n- From: Sender's email address\n- To: Primary recipient(s)\n- CC (Carbon Copy): Additional recipients who should be informed\n- BCC (Blind Carbon Copy): Recipients hidden from others\n- Subject: Brief description of the email content\n- Body: The message\n- Attachment: Files attached to the email\n\n**Professional Email Writing:**\n- Clear subject line: Action required: Invoice #1234 due Friday\n- Greeting: Dear Mr. Okafor, / Hi Amara,\n- One topic per email\n- Short paragraphs\n- Clear call to action\n- Professional sign-off: Kind regards, / Best wishes,\n\n**Email Security:**\n- Phishing: Fake emails pretending to be legitimate organisations\n- Spam: Unsolicited bulk email\n- Never click links in unexpected emails — go directly to the website\n- Verify sender email addresses carefully\n\n## Practical Task\n\nWrite 3 professional emails: (1) A job application email with CV attached. (2) A follow-up email after a job interview. (3) A complaint email to a service provider. Apply all professional email writing principles.\n\n## Self-Check\n1. What is the difference between CC and BCC?\n2. How can you identify a phishing email?\n3. What does HTTPS mean and why is it important?"),
("Troubleshooting & IT Support","## The Troubleshooting Process\n\nEffective troubleshooting follows a systematic process:\n\n1. **Identify the problem**: What exactly is not working? When did it start? What changed recently?\n2. **Establish a theory**: What are the possible causes?\n3. **Test the theory**: Try the most likely cause first\n4. **Establish a plan**: If the theory is confirmed, plan the fix\n5. **Implement the solution**: Apply the fix\n6. **Verify functionality**: Confirm the problem is resolved\n7. **Document**: Record the problem, cause, and solution\n\n## Common Computer Problems & Solutions\n\n**Computer is slow:**\n- Check Task Manager for high CPU/RAM usage\n- Disable startup programs\n- Run disk cleanup and defragmentation (HDD only)\n- Check for malware\n- Upgrade RAM or switch to SSD\n\n**No internet connection:**\n- Check if Wi-Fi is enabled\n- Restart the router and modem\n- Run Windows Network Troubleshooter\n- Check IP configuration (ipconfig)\n- Ping the router (ping 192.168.1.1)\n- Check if other devices can connect\n\n**Computer will not start:**\n- Check power cable and power button\n- Listen for beep codes (hardware error indicators)\n- Try booting in Safe Mode (F8 during startup)\n- Check if the monitor is connected and powered\n- Remove recently added hardware\n\n**Blue Screen of Death (BSOD):**\n- Note the error code\n- Search the error code online\n- Common causes: Driver issues, RAM failure, overheating, malware\n\n## Remote Support Tools\n\n- **TeamViewer**: Remote desktop access (free for personal use)\n- **AnyDesk**: Fast remote desktop\n- **Windows Remote Desktop**: Built into Windows\n- **Chrome Remote Desktop**: Browser-based, free\n\n## Ticketing Systems\n\nIT support teams use ticketing systems to track issues:\n- **Freshdesk**: Free tier available\n- **Zendesk**: Enterprise-grade\n- **Jira Service Management**: Popular in tech companies\n- **osTicket**: Free, open-source\n\n## Practical Task\n\nDocument 5 real IT support scenarios you have encountered or can research. For each: describe the problem, list 3 possible causes, describe the troubleshooting steps, and document the solution. Create a simple troubleshooting guide for non-technical users.\n\n## Self-Check\n1. What are the 7 steps of the troubleshooting process?\n2. What is the first thing to check when a computer cannot connect to the internet?\n3. What is a ticketing system and why do IT teams use them?"),
("Capstone: IT Fundamentals Assessment","## Final Assessment Overview\n\nThis capstone evaluates your understanding of all computer fundamentals topics covered in this course.\n\n## Written Assessment (40%)\n\nAnswer the following questions in detail:\n\n1. A user reports their computer is running very slowly. Describe your complete troubleshooting process, including at least 5 specific steps you would take.\n\n2. Explain the difference between RAM and storage. A user asks: My computer has 1TB storage but only 8GB RAM. Should I upgrade the storage or the RAM? Justify your recommendation.\n\n3. Compare Windows, macOS, and Linux. For each, describe: the target user, key advantages, key disadvantages, and one scenario where it is the best choice.\n\n4. A colleague receives an email from their bank asking them to click a link and verify their account details. The email looks legitimate. What advice would you give them? How would you identify if it is a phishing email?\n\n5. Explain how the internet works when you type www.google.com into your browser. Include: DNS, HTTP/HTTPS, IP addresses, and the role of the web server.\n\n## Practical Assessment (60%)\n\n**Task 1: Hardware Identification (10%)**\nIdentify and document all hardware components in a provided computer. Include specifications and purpose of each component.\n\n**Task 2: OS Navigation (15%)**\nComplete 10 command-line tasks on Windows: create folders, copy files, check network configuration, list processes, and more.\n\n**Task 3: Office Productivity (20%)**\nCreate a professional report in Word, a budget spreadsheet in Excel with formulas and charts, and a 5-slide presentation in PowerPoint.\n\n**Task 4: Troubleshooting (15%)**\nDiagnose and resolve 3 simulated computer problems. Document your process for each.\n\n## Evaluation Criteria\n- Accuracy of technical knowledge (30%)\n- Practical skill demonstration (40%)\n- Problem-solving approach (20%)\n- Documentation quality (10%)\n\n## Self-Check\n1. Can you explain how a computer works to a complete beginner?\n2. Can you troubleshoot common problems without looking them up?\n3. Are you proficient in Word, Excel, and PowerPoint?"),
], 1):
    L(11, t, c, i)


# Course 12: Desktop Application Development
for i,(t,c) in enumerate([
("Introduction to Desktop Development","## Desktop vs Web vs Mobile\n\nDesktop applications run natively on an operating system (Windows, macOS, Linux). They offer better performance, offline capability, and deeper OS integration than web apps.\n\n## Desktop Development Frameworks\n\n**Python + Tkinter**: Built into Python. Simple, good for tools and utilities.\n**Python + PyQt/PySide**: Professional-grade UI. Cross-platform.\n**Electron**: Build desktop apps with HTML, CSS, JavaScript. Used by VS Code, Slack, Discord.\n**JavaFX**: Java-based, cross-platform, good for enterprise apps.\n**C# + WPF/WinForms**: Windows-only, deep Windows integration.\n**Flutter Desktop**: Same codebase as Flutter mobile, newer.\n\n## Why Python for Desktop?\n\nPython is an excellent choice for desktop development:\n- Easy to learn and read\n- Large standard library\n- Excellent for data-heavy applications\n- Cross-platform (Windows, macOS, Linux)\n- Can package into standalone executables (PyInstaller)\n\n## Setting Up Python Desktop Development\n\n```bash\n# Install Python from python.org\n# Install PyQt5\npip install PyQt5\n\n# Or install Tkinter (usually included with Python)\npython -m tkinter  # Test if Tkinter is available\n```\n\n## Your First Tkinter App\n\n```python\nimport tkinter as tk\nfrom tkinter import messagebox\n\nroot = tk.Tk()\nroot.title('My First App')\nroot.geometry('400x300')\n\nlabel = tk.Label(root, text='Hello, World!', font=('Arial', 18))\nlabel.pack(pady=20)\n\ndef on_click():\n    messagebox.showinfo('Message', 'Button clicked!')\n\nbtn = tk.Button(root, text='Click Me', command=on_click,\n                bg='#4f46e5', fg='white', padx=20, pady=10)\nbtn.pack()\n\nroot.mainloop()\n```\n\n## Practical Task\n\nBuild a simple calculator app using Tkinter. It should have: number buttons (0-9), operation buttons (+, -, *, /), a display screen, a clear button, and an equals button. The calculator should handle basic arithmetic correctly.\n\n## Self-Check\n1. What is the difference between a desktop app and a web app?\n2. Name 3 popular desktop development frameworks.\n3. What does root.mainloop() do in Tkinter?"),
("GUI Design with PyQt5","## Why PyQt5?\n\nPyQt5 is a set of Python bindings for Qt, one of the most powerful cross-platform GUI frameworks. It produces professional-looking applications that run on Windows, macOS, and Linux.\n\n## PyQt5 Fundamentals\n\n```python\nimport sys\nfrom PyQt5.QtWidgets import (QApplication, QMainWindow, QWidget,\n                              QVBoxLayout, QHBoxLayout, QPushButton,\n                              QLabel, QLineEdit, QTableWidget,\n                              QTableWidgetItem, QMessageBox)\nfrom PyQt5.QtCore import Qt\nfrom PyQt5.QtGui import QFont, QColor\n\nclass MainWindow(QMainWindow):\n    def __init__(self):\n        super().__init__()\n        self.setWindowTitle('Student Manager')\n        self.setMinimumSize(800, 600)\n        self.setup_ui()\n\n    def setup_ui(self):\n        central = QWidget()\n        self.setCentralWidget(central)\n        layout = QVBoxLayout(central)\n\n        # Title\n        title = QLabel('Student Manager')\n        title.setFont(QFont('Arial', 18, QFont.Bold))\n        title.setAlignment(Qt.AlignCenter)\n        layout.addWidget(title)\n\n        # Input row\n        input_row = QHBoxLayout()\n        self.name_input = QLineEdit()\n        self.name_input.setPlaceholderText('Student name')\n        self.add_btn = QPushButton('Add Student')\n        self.add_btn.clicked.connect(self.add_student)\n        input_row.addWidget(self.name_input)\n        input_row.addWidget(self.add_btn)\n        layout.addLayout(input_row)\n\n        # Table\n        self.table = QTableWidget(0, 3)\n        self.table.setHorizontalHeaderLabels(['ID', 'Name', 'Actions'])\n        layout.addWidget(self.table)\n\n    def add_student(self):\n        name = self.name_input.text().strip()\n        if not name:\n            QMessageBox.warning(self, 'Error', 'Please enter a name')\n            return\n        row = self.table.rowCount()\n        self.table.insertRow(row)\n        self.table.setItem(row, 0, QTableWidgetItem(str(row + 1)))\n        self.table.setItem(row, 1, QTableWidgetItem(name))\n        self.name_input.clear()\n\napp = QApplication(sys.argv)\nwindow = MainWindow()\nwindow.show()\nsys.exit(app.exec_())\n```\n\n## Qt Designer\n\nQt Designer is a visual UI builder. Design your interface visually, then load the .ui file in Python:\n\n```python\nfrom PyQt5 import uic\nclass MainWindow(QMainWindow):\n    def __init__(self):\n        super().__init__()\n        uic.loadUi('main_window.ui', self)\n```\n\n## Practical Task\n\nBuild a contact book application with PyQt5. Features: add contacts (name, phone, email), display in a table, search by name, edit existing contacts, delete contacts. Use Qt Designer to design the UI.\n\n## Self-Check\n1. What is the difference between QVBoxLayout and QHBoxLayout?\n2. How do you connect a button click to a function in PyQt5?\n3. What is Qt Designer and how does it help development?"),
("Database Integration in Desktop Apps","## Connecting Desktop Apps to SQLite\n\nSQLite is a lightweight, file-based database perfect for desktop applications. No server required — the entire database is a single file.\n\n## Python sqlite3 Module\n\n```python\nimport sqlite3\nfrom contextlib import contextmanager\n\nDB_PATH = 'students.db'\n\ndef init_db():\n    with sqlite3.connect(DB_PATH) as conn:\n        conn.execute('''\n            CREATE TABLE IF NOT EXISTS students (\n                id INTEGER PRIMARY KEY AUTOINCREMENT,\n                name TEXT NOT NULL,\n                email TEXT UNIQUE NOT NULL,\n                course TEXT,\n                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n            )\n        ''')\n        conn.commit()\n\ndef add_student(name: str, email: str, course: str) -> int:\n    with sqlite3.connect(DB_PATH) as conn:\n        cursor = conn.execute(\n            'INSERT INTO students (name, email, course) VALUES (?,?,?)',\n            (name, email, course)\n        )\n        conn.commit()\n        return cursor.lastrowid\n\ndef get_all_students() -> list:\n    with sqlite3.connect(DB_PATH) as conn:\n        conn.row_factory = sqlite3.Row\n        return conn.execute('SELECT * FROM students ORDER BY name').fetchall()\n\ndef search_students(query: str) -> list:\n    with sqlite3.connect(DB_PATH) as conn:\n        conn.row_factory = sqlite3.Row\n        return conn.execute(\n            'SELECT * FROM students WHERE name LIKE ? OR email LIKE ?',\n            (f'%{query}%', f'%{query}%')\n        ).fetchall()\n\ndef delete_student(student_id: int) -> None:\n    with sqlite3.connect(DB_PATH) as conn:\n        conn.execute('DELETE FROM students WHERE id = ?', (student_id,))\n        conn.commit()\n```\n\n## Integrating with PyQt5\n\n```python\ndef load_students(self):\n    students = get_all_students()\n    self.table.setRowCount(0)\n    for student in students:\n        row = self.table.rowCount()\n        self.table.insertRow(row)\n        self.table.setItem(row, 0, QTableWidgetItem(str(student['id'])))\n        self.table.setItem(row, 1, QTableWidgetItem(student['name']))\n        self.table.setItem(row, 2, QTableWidgetItem(student['email']))\n```\n\n## Practical Task\n\nUpgrade your contact book from Lesson 2 to use SQLite for persistent storage. All contacts should be saved to a database file and loaded on startup. Add: search functionality, edit contact dialog, and export to CSV.\n\n## Self-Check\n1. What is SQLite and why is it good for desktop apps?\n2. Why should you always use parameterised queries (?) instead of string formatting?\n3. What does conn.row_factory = sqlite3.Row do?"),
("Packaging & Distribution","## Packaging Python Desktop Apps\n\nTo distribute your app to users who do not have Python installed, you need to package it as a standalone executable.\n\n## PyInstaller\n\nPyInstaller bundles your Python app and all its dependencies into a single executable.\n\n```bash\n# Install PyInstaller\npip install pyinstaller\n\n# Create a single executable file\npyinstaller --onefile --windowed main.py\n\n# With a custom icon\npyinstaller --onefile --windowed --icon=app.ico main.py\n\n# Output: dist/main.exe (Windows) or dist/main (macOS/Linux)\n```\n\n**Options:**\n- `--onefile`: Bundle everything into a single .exe file\n- `--windowed` or `--noconsole`: Hide the console window (for GUI apps)\n- `--icon`: Set the application icon (.ico for Windows)\n- `--name`: Set the output filename\n\n## Creating an Installer (Windows)\n\nInno Setup is a free tool for creating Windows installers:\n1. Download Inno Setup from jrsoftware.org\n2. Create a script defining your app name, version, files, and shortcuts\n3. Compile to create a setup.exe installer\n4. Users run setup.exe to install your app\n\n## Auto-Update Mechanism\n\n```python\nimport requests\nimport json\n\nCURRENT_VERSION = '1.0.0'\nUPDATE_URL = 'https://yourserver.com/version.json'\n\ndef check_for_updates():\n    try:\n        response = requests.get(UPDATE_URL, timeout=5)\n        data = response.json()\n        latest = data['version']\n        if latest != CURRENT_VERSION:\n            return latest, data['download_url']\n    except Exception:\n        pass\n    return None, None\n```\n\n## App Versioning\n\nUse semantic versioning: MAJOR.MINOR.PATCH\n- MAJOR: Breaking changes\n- MINOR: New features, backward compatible\n- PATCH: Bug fixes\n\nExample: 1.0.0 -> 1.0.1 (bug fix) -> 1.1.0 (new feature) -> 2.0.0 (breaking change)\n\n## Practical Task\n\nPackage your contact book application using PyInstaller. Create a single .exe file. Test it on a computer without Python installed. Create a simple Inno Setup installer script. Document the build and distribution process.\n\n## Self-Check\n1. What does the --onefile flag do in PyInstaller?\n2. What is semantic versioning?\n3. Why do you need to package a Python app before distributing it?"),
("Advanced Features: Threads & APIs","## Threading in Desktop Apps\n\nLong-running operations (database queries, file processing, API calls) should run in background threads to keep the UI responsive.\n\n```python\nfrom PyQt5.QtCore import QThread, pyqtSignal\n\nclass DataWorker(QThread):\n    finished = pyqtSignal(list)  # Signal to send data back to UI\n    error = pyqtSignal(str)\n\n    def __init__(self, query):\n        super().__init__()\n        self.query = query\n\n    def run(self):\n        try:\n            # This runs in a background thread\n            results = fetch_data_from_api(self.query)\n            self.finished.emit(results)\n        except Exception as e:\n            self.error.emit(str(e))\n\n# In your main window:\ndef start_search(self):\n    self.search_btn.setEnabled(False)\n    self.worker = DataWorker(self.search_input.text())\n    self.worker.finished.connect(self.on_results_ready)\n    self.worker.error.connect(self.on_error)\n    self.worker.start()\n\ndef on_results_ready(self, results):\n    self.search_btn.setEnabled(True)\n    self.display_results(results)\n```\n\n## Consuming REST APIs\n\n```python\nimport requests\n\ndef fetch_exchange_rates(base_currency='NGN'):\n    url = f'https://api.exchangerate-api.com/v4/latest/{base_currency}'\n    response = requests.get(url, timeout=10)\n    response.raise_for_status()\n    return response.json()['rates']\n\ndef fetch_weather(city):\n    api_key = 'your_api_key'\n    url = f'https://api.openweathermap.org/data/2.5/weather?q={city}&appid={api_key}'\n    response = requests.get(url, timeout=10)\n    data = response.json()\n    return {\n        'temp': data['main']['temp'] - 273.15,  # Kelvin to Celsius\n        'description': data['weather'][0]['description'],\n        'humidity': data['main']['humidity'],\n    }\n```\n\n## Charts in Desktop Apps\n\n```python\nimport matplotlib.pyplot as plt\nfrom matplotlib.backends.backend_qt5agg import FigureCanvasQTAgg\n\nclass ChartWidget(FigureCanvasQTAgg):\n    def __init__(self):\n        fig, self.ax = plt.subplots(figsize=(8, 4))\n        super().__init__(fig)\n\n    def plot_bar(self, labels, values, title):\n        self.ax.clear()\n        self.ax.bar(labels, values, color='#4f46e5')\n        self.ax.set_title(title)\n        self.draw()\n```\n\n## Practical Task\n\nBuild a currency converter desktop app. Features: fetch live exchange rates from a free API, convert between any two currencies, display a chart of the last 7 days of exchange rates, run API calls in a background thread so the UI stays responsive.\n\n## Self-Check\n1. Why should long-running operations run in a background thread?\n2. What is a PyQt5 Signal and how does it work?\n3. How do you handle API errors gracefully in a desktop app?"),
("Capstone: Desktop Application Project","## Final Project Brief\n\nYou will build a complete, production-ready desktop application that solves a real problem.\n\n## Project Options (Choose One)\n\n### Option A: School Management System\nA desktop app for a small school:\n- Student registration and management\n- Course/class management\n- Attendance tracking\n- Grade recording and report cards\n- Fee payment tracking\n- Reports: attendance summary, grade report, fee outstanding\n\n### Option B: Inventory Management System\nA desktop app for a small business:\n- Product catalogue with categories\n- Stock tracking (add stock, record sales)\n- Low stock alerts\n- Supplier management\n- Sales reports and charts\n- Export reports to PDF/Excel\n\n### Option C: Personal Finance Tracker\nA desktop app for personal budgeting:\n- Income and expense tracking by category\n- Monthly budget setting\n- Visual charts (spending by category, monthly trend)\n- Bill reminders\n- Export to CSV\n- Password protection\n\n## Technical Requirements\n\n- Built with Python + PyQt5\n- SQLite database for persistent storage\n- At least one background thread for data operations\n- At least one chart (matplotlib)\n- Packaged as a standalone .exe with PyInstaller\n- Proper error handling throughout\n- Clean, professional UI\n\n## Evaluation Criteria\n- Feature completeness (25%)\n- Code quality and organisation (25%)\n- UI/UX design (20%)\n- Database design (15%)\n- Packaging and distribution (15%)\n\n## Self-Check\n1. Does your app handle errors gracefully (no crashes on bad input)?\n2. Is your database schema properly normalised?\n3. Can a non-technical user install and use your app without help?"),
], 1):
    L(12, t, c, i)


# Courses 13-16: POS/ICT, Networking, Cloud, Software Engineering
# 6 lessons each, concise real content

for i,(t,c) in enumerate([
("POS Systems & Operations","## What is a POS System?\n\nA Point of Sale (POS) system is the combination of hardware and software used to process sales transactions. Modern POS systems do much more than just process payments.\n\n## POS Hardware Components\n\n- **POS Terminal**: The main computer running the POS software\n- **Receipt Printer**: Thermal printer for customer receipts\n- **Barcode Scanner**: Reads product barcodes for quick item entry\n- **Cash Drawer**: Stores cash, opens automatically on cash transactions\n- **Card Reader/POS Terminal**: Processes debit and credit card payments\n- **Customer Display**: Shows transaction details to the customer\n- **Weighing Scale**: For items sold by weight (supermarkets, markets)\n\n## POS Software Features\n\n- Sales processing: Scan items, apply discounts, process payment\n- Inventory management: Track stock levels, low stock alerts\n- Customer management: Loyalty programmes, purchase history\n- Reporting: Daily sales, best-selling products, staff performance\n- Multi-location: Manage multiple branches from one system\n\n## Payment Methods\n\n- Cash: Count change accurately, reconcile at end of day\n- Debit/Credit card: POS terminal, chip and PIN or contactless\n- Bank transfer: USSD or mobile banking\n- Mobile money: Opay, Palmpay, Moniepoint\n- QR code payment: Scan to pay\n\n## Common POS Systems in Nigeria\n\n- Moniepoint POS: Widely used by small businesses\n- Paystack Terminal: Integrated with Paystack payments\n- Quickteller POS: Interswitch product\n- Kudi POS: Agent banking focused\n- Custom POS software: Built for specific business needs\n\n## Practical Task\n\nVisit a local business that uses a POS system. Observe and document: the hardware components used, the payment methods accepted, how receipts are generated, and how the cashier handles a transaction error. Write a 1-page report.\n\n## Self-Check\n1. Name 5 hardware components of a POS system.\n2. What is the difference between a POS terminal and a cash register?\n3. Name 3 mobile payment methods used in Nigeria."),
("ICT Support & Troubleshooting","## ICT Support Roles\n\nICT (Information and Communications Technology) support professionals maintain and troubleshoot technology systems in organisations.\n\n## Support Tiers\n\n**Tier 1 (Help Desk)**: First point of contact. Handle common issues: password resets, basic software problems, connectivity issues. Escalate complex issues to Tier 2.\n\n**Tier 2 (Technical Support)**: Handle more complex issues: hardware failures, software configuration, network problems. Escalate to Tier 3 if needed.\n\n**Tier 3 (Expert Support)**: Specialists: network engineers, database administrators, security experts. Handle the most complex issues.\n\n## Common ICT Support Tasks\n\n- Setting up new computers and user accounts\n- Installing and configuring software\n- Troubleshooting hardware failures\n- Network connectivity issues\n- Email configuration (Outlook, Gmail)\n- Printer setup and troubleshooting\n- Data backup and recovery\n- Antivirus installation and updates\n- User training\n\n## Remote Support\n\nMost ICT support is now done remotely:\n- TeamViewer: Remote desktop access\n- AnyDesk: Fast, lightweight remote access\n- Microsoft Remote Desktop: Built into Windows\n- Zoom/Teams screen sharing: For guided support\n\n## Documentation\n\nGood ICT support requires thorough documentation:\n- Asset register: All hardware and software in the organisation\n- Network diagram: Visual map of the network\n- Runbooks: Step-by-step procedures for common tasks\n- Incident log: Record of all support tickets\n- Change log: Record of all system changes\n\n## Practical Task\n\nCreate an ICT support runbook for 5 common issues: (1) Computer cannot connect to Wi-Fi, (2) Printer not printing, (3) Outlook not receiving emails, (4) Computer running slowly, (5) User forgot their password. Each runbook should have step-by-step troubleshooting instructions.\n\n## Self-Check\n1. What are the 3 tiers of ICT support?\n2. What is a runbook?\n3. Name 3 remote support tools."),
("Network Setup & Configuration","## Setting Up a Small Office Network\n\nMost small businesses need a simple network: internet connection, Wi-Fi, and shared resources (printer, file server).\n\n## Network Equipment\n\n- **Modem**: Connects to your ISP (Internet Service Provider). Converts the ISP signal to Ethernet.\n- **Router**: Connects multiple devices to the internet. Assigns IP addresses via DHCP. Provides Wi-Fi.\n- **Switch**: Connects multiple wired devices in a network. Extends the number of Ethernet ports.\n- **Access Point**: Extends Wi-Fi coverage to areas the router cannot reach.\n- **Ethernet Cable (Cat5e/Cat6)**: Wired connection. Faster and more reliable than Wi-Fi.\n\n## IP Addressing\n\n**Private IP ranges (not routable on the internet):**\n- 192.168.0.0 - 192.168.255.255 (most home/office networks)\n- 10.0.0.0 - 10.255.255.255 (larger networks)\n- 172.16.0.0 - 172.31.255.255\n\n**DHCP**: Automatically assigns IP addresses to devices.\n**Static IP**: Manually assigned. Use for servers, printers, and network equipment.\n\n## Wi-Fi Security\n\n- Use WPA3 or WPA2 encryption (never WEP)\n- Use a strong, unique Wi-Fi password (12+ characters)\n- Change the default router admin password\n- Disable WPS (Wi-Fi Protected Setup) - it has known vulnerabilities\n- Create a separate guest network for visitors\n- Hide the SSID (network name) for extra security\n\n## Basic Router Configuration\n\n1. Connect to router admin panel (usually 192.168.1.1 or 192.168.0.1)\n2. Change admin username and password\n3. Set Wi-Fi name (SSID) and password\n4. Configure DHCP range\n5. Set up port forwarding if needed\n6. Enable firewall\n7. Update router firmware\n\n## Practical Task\n\nDraw a network diagram for a fictional small office with: 10 computers, 2 printers, 1 file server, 1 router, 1 switch, and Wi-Fi coverage. Label all devices with IP addresses. Write a network setup guide for a non-technical office manager.\n\n## Self-Check\n1. What is the difference between a router and a switch?\n2. What is DHCP?\n3. Which Wi-Fi security protocol should you use?"),
("POS Maintenance & Security","## POS System Maintenance\n\nRegular maintenance prevents downtime and data loss.\n\n## Daily Maintenance Tasks\n\n- Reconcile cash drawer at end of day\n- Back up transaction data\n- Check receipt paper level\n- Clean card reader contacts\n- Review daily sales report for anomalies\n\n## Weekly Maintenance Tasks\n\n- Clean all hardware (screens, keyboards, scanners)\n- Check for software updates\n- Review and clear old transaction logs\n- Test backup restoration\n- Check network connectivity and speed\n\n## POS Security\n\n**Physical Security:**\n- Secure the POS terminal to the counter (cable lock)\n- Restrict access to the cash drawer\n- Install CCTV cameras at POS stations\n- Never leave the POS unattended while logged in\n\n**Software Security:**\n- Use strong, unique passwords for each staff member\n- Enable automatic screen lock after inactivity\n- Restrict staff access to only the functions they need\n- Keep POS software updated\n- Use antivirus software\n\n**Transaction Security:**\n- Never store card numbers\n- Use end-to-end encrypted card readers\n- Train staff to recognise card skimming devices\n- Monitor for unusual transaction patterns\n- Require manager approval for refunds and voids\n\n## Common POS Problems & Solutions\n\n- Receipt printer not printing: Check paper, check cable, restart printer\n- Card reader not working: Clean contacts, check cable, restart terminal\n- Barcode scanner not reading: Clean scanner glass, check cable, adjust scan angle\n- POS software frozen: Force close and restart, check for updates\n- Network connectivity lost: Restart router, check cables, contact ISP\n\n## Practical Task\n\nCreate a POS maintenance schedule for a fictional retail shop. Include: daily, weekly, monthly, and annual tasks. Create a troubleshooting guide for the 5 most common POS problems. Design a staff training checklist for new cashiers.\n\n## Self-Check\n1. What daily maintenance tasks should a cashier perform?\n2. How do you secure a POS system against internal theft?\n3. What should you do if the card reader stops working?"),
("Customer Service & Business Skills","## Customer Service in ICT & POS\n\nTechnical skills alone are not enough. Excellent customer service is what builds a successful career in ICT support and POS operations.\n\n## The Customer Service Mindset\n\n- The customer is not always right, but they are always the customer\n- Every interaction is an opportunity to build trust\n- Solve the problem, not just the symptom\n- Follow up to ensure the issue is fully resolved\n- Treat every customer with respect, regardless of their technical knowledge\n\n## Communication Skills\n\n**Active Listening:**\n- Let the customer finish speaking before responding\n- Ask clarifying questions\n- Summarise what you heard: So what you are saying is...\n- Do not interrupt\n\n**Explaining Technical Issues:**\n- Avoid jargon with non-technical customers\n- Use analogies: The router is like a traffic controller for your internet\n- Check for understanding: Does that make sense?\n- Provide written instructions for complex procedures\n\n**Handling Difficult Customers:**\n- Stay calm and professional\n- Acknowledge their frustration: I understand this is frustrating\n- Focus on solutions, not blame\n- Escalate if you cannot resolve the issue\n- Never argue or become defensive\n\n## Business Skills for ICT Professionals\n\n- **Time management**: Prioritise tickets by urgency and impact\n- **Documentation**: Write clear, concise reports and runbooks\n- **Project management**: Plan and execute IT projects on time and budget\n- **Vendor management**: Evaluate and manage relationships with suppliers\n- **Budget awareness**: Understand the cost implications of your recommendations\n\n## Professional Development\n\nCertifications that boost your ICT career:\n- CompTIA A+: Entry-level IT support\n- CompTIA Network+: Networking fundamentals\n- Microsoft Certified: Modern Desktop Administrator\n- Google IT Support Certificate: Free on Coursera\n\n## Practical Task\n\nRole-play 3 customer service scenarios with a partner: (1) A customer whose POS terminal stopped working during a busy period. (2) A customer who does not understand why their card was declined. (3) A customer who is angry about a double charge. Write a reflection on what you learned from each scenario.\n\n## Self-Check\n1. What is active listening?\n2. How do you explain a technical issue to a non-technical customer?\n3. Name 2 IT certifications suitable for a beginner."),
("Capstone: POS & ICT Support Project","## Final Project Brief\n\nYou will demonstrate your POS operations and ICT support skills through a practical assessment.\n\n## Part 1: POS Operations Assessment (40%)\n\n**Practical Tasks:**\n1. Process 10 simulated transactions including: cash sale, card payment, discount application, refund, and void\n2. Perform end-of-day reconciliation\n3. Generate and interpret a daily sales report\n4. Troubleshoot 3 simulated POS problems\n\n**Written Tasks:**\n1. Write a cashier training manual (2 pages) covering: opening procedures, processing transactions, handling errors, and closing procedures\n2. Create a daily maintenance checklist\n\n## Part 2: ICT Support Assessment (40%)\n\n**Practical Tasks:**\n1. Set up a new computer: install OS, configure network, install required software\n2. Troubleshoot 5 simulated IT problems (provided by instructor)\n3. Set up a small network: configure router, connect 3 devices, test connectivity\n4. Provide remote support to a simulated user using TeamViewer\n\n**Written Tasks:**\n1. Create an IT asset register for a fictional 10-person office\n2. Write an incident report for a simulated security breach\n\n## Part 3: Customer Service Assessment (20%)\n\n1. Role-play 2 customer service scenarios (assessed by instructor)\n2. Write a customer service policy for a fictional ICT support company\n\n## Evaluation Criteria\n- Technical accuracy (35%)\n- Problem-solving approach (25%)\n- Documentation quality (20%)\n- Customer service skills (20%)\n\n## Self-Check\n1. Can you process all transaction types on a POS system without assistance?\n2. Can you troubleshoot common IT problems systematically?\n3. Can you explain technical issues clearly to non-technical users?"),
], 1):
    L(13, t, c, i)


# Course 14: Networking Basics
for i,(t,c) in enumerate([
("Networking Fundamentals","## What is a Computer Network?\n\nA computer network is a collection of interconnected devices that can communicate and share resources. Networks enable file sharing, internet access, email, video calls, and cloud services.\n\n## Network Types\n\n- **LAN (Local Area Network)**: Covers a small area (home, office, school). Fast, low latency.\n- **WAN (Wide Area Network)**: Covers large geographic areas. The internet is the largest WAN.\n- **MAN (Metropolitan Area Network)**: Covers a city or campus.\n- **PAN (Personal Area Network)**: Very short range (Bluetooth, USB). Connects personal devices.\n- **WLAN (Wireless LAN)**: Wi-Fi network.\n- **VPN (Virtual Private Network)**: Secure tunnel over the internet.\n\n## Network Topologies\n\n- **Bus**: All devices connected to a single cable. Simple but a single failure breaks the network.\n- **Star**: All devices connect to a central switch/hub. Most common in modern networks.\n- **Ring**: Devices connected in a circle. Data travels in one direction.\n- **Mesh**: Every device connects to every other device. Highly redundant, expensive.\n- **Hybrid**: Combination of topologies.\n\n## The OSI Model\n\nThe OSI (Open Systems Interconnection) model describes how data travels across a network in 7 layers:\n\n1. Physical: Cables, signals, bits\n2. Data Link: MAC addresses, switches, frames\n3. Network: IP addresses, routers, packets\n4. Transport: TCP/UDP, ports, segments\n5. Session: Establishing and managing connections\n6. Presentation: Encryption, compression, data format\n7. Application: HTTP, FTP, DNS, SMTP\n\nMemory aid: Please Do Not Throw Sausage Pizza Away\n\n## Practical Task\n\nDraw a network diagram for your home or school network. Identify: all devices, their connection types (wired/wireless), the router, and the internet connection. Label each device with its approximate IP address. Identify which OSI layer each device primarily operates at.\n\n## Self-Check\n1. What is the difference between a LAN and a WAN?\n2. What are the 7 layers of the OSI model?\n3. Which network topology is most common in modern offices?"),
("IP Addressing & Subnetting","## IP Addressing\n\nEvery device on a network needs a unique IP address to communicate.\n\n## IPv4 Address Structure\n\nAn IPv4 address is 32 bits, written as 4 octets separated by dots:\n192.168.1.100\n\nEach octet is 8 bits (0-255).\n\n## IP Address Classes\n\n| Class | Range | Default Subnet Mask | Use |\n|---|---|---|---|\n| A | 1.0.0.0 - 126.255.255.255 | 255.0.0.0 | Large networks |\n| B | 128.0.0.0 - 191.255.255.255 | 255.255.0.0 | Medium networks |\n| C | 192.0.0.0 - 223.255.255.255 | 255.255.255.0 | Small networks |\n\n## Private IP Ranges\n\nThese ranges are reserved for private networks (not routable on the internet):\n- 10.0.0.0/8 (Class A private)\n- 172.16.0.0/12 (Class B private)\n- 192.168.0.0/16 (Class C private)\n\n## Subnet Mask\n\nA subnet mask defines which part of an IP address is the network and which is the host:\n\nIP: 192.168.1.100\nMask: 255.255.255.0 (/24)\nNetwork: 192.168.1.0\nHost range: 192.168.1.1 - 192.168.1.254\nBroadcast: 192.168.1.255\nUsable hosts: 254\n\n## CIDR Notation\n\nCIDR (Classless Inter-Domain Routing) notation: 192.168.1.0/24\nThe /24 means 24 bits are the network portion.\n\nCommon CIDR values:\n- /24 = 255.255.255.0 = 254 hosts\n- /25 = 255.255.255.128 = 126 hosts\n- /26 = 255.255.255.192 = 62 hosts\n- /30 = 255.255.255.252 = 2 hosts (point-to-point links)\n\n## IPv6\n\nIPv4 addresses are running out. IPv6 uses 128-bit addresses:\n2001:0db8:85a3:0000:0000:8a2e:0370:7334\n\nIPv6 provides 340 undecillion addresses (3.4 x 10^38).\n\n## Practical Task\n\nYou have been given the network 192.168.10.0/24. Divide it into 4 equal subnets. For each subnet, calculate: network address, subnet mask, first usable host, last usable host, broadcast address, and number of usable hosts.\n\n## Self-Check\n1. What is the difference between a public and private IP address?\n2. What does /24 mean in CIDR notation?\n3. How many usable hosts does a /24 network have?"),
("Routing & Switching","## Switches\n\nA switch operates at Layer 2 (Data Link) of the OSI model. It connects devices within the same network using MAC addresses.\n\n**How a switch works:**\n1. Device A sends a frame to Device B\n2. The switch reads the destination MAC address\n3. The switch looks up its MAC address table\n4. If found, it forwards the frame only to the correct port\n5. If not found, it floods the frame to all ports (except the source)\n\n**VLANs (Virtual LANs)**: Logically segment a network without physical separation. Devices on different VLANs cannot communicate without a router.\n\n## Routers\n\nA router operates at Layer 3 (Network) of the OSI model. It connects different networks using IP addresses.\n\n**How a router works:**\n1. Receives a packet\n2. Reads the destination IP address\n3. Looks up the routing table\n4. Forwards the packet to the next hop\n\n**Routing Table**: A list of network destinations and the next hop to reach them.\n\n## Routing Protocols\n\n**Static routing**: Manually configured routes. Simple, predictable, no overhead. Good for small networks.\n\n**Dynamic routing**: Routers automatically discover and share routes:\n- **RIP (Routing Information Protocol)**: Simple, uses hop count. Max 15 hops.\n- **OSPF (Open Shortest Path First)**: Fast convergence, uses cost metric. Good for enterprise.\n- **BGP (Border Gateway Protocol)**: The routing protocol of the internet.\n\n## NAT (Network Address Translation)\n\nNAT allows multiple devices with private IP addresses to share a single public IP address:\n- Your router has one public IP (assigned by your ISP)\n- All devices in your home have private IPs (192.168.x.x)\n- NAT translates between private and public IPs\n\n## Practical Task\n\nUsing Cisco Packet Tracer (free from Cisco), build a network with: 2 routers, 2 switches, and 4 PCs (2 per switch). Configure IP addresses, default gateways, and static routes so all PCs can ping each other. Document your configuration.\n\n## Self-Check\n1. What is the difference between a switch and a router?\n2. What is a VLAN and why would you use one?\n3. What is NAT and why is it needed?"),
("Wireless Networking","## Wi-Fi Standards\n\nWi-Fi standards define the speed and frequency of wireless communication:\n\n| Standard | Max Speed | Frequency | Also Known As |\n|---|---|---|---|\n| 802.11b | 11 Mbps | 2.4 GHz | Wi-Fi 1 |\n| 802.11g | 54 Mbps | 2.4 GHz | Wi-Fi 3 |\n| 802.11n | 600 Mbps | 2.4/5 GHz | Wi-Fi 4 |\n| 802.11ac | 3.5 Gbps | 5 GHz | Wi-Fi 5 |\n| 802.11ax | 9.6 Gbps | 2.4/5/6 GHz | Wi-Fi 6 |\n\n## 2.4 GHz vs 5 GHz\n\n**2.4 GHz:**\n- Longer range\n- Better penetration through walls\n- More interference (microwaves, Bluetooth, neighbours)\n- Slower speeds\n\n**5 GHz:**\n- Shorter range\n- Less interference\n- Faster speeds\n- Better for video streaming and gaming\n\n**6 GHz (Wi-Fi 6E)**: New band, very fast, very short range.\n\n## Wireless Security\n\n**WEP (Wired Equivalent Privacy)**: Broken. Never use.\n**WPA (Wi-Fi Protected Access)**: Improved but still vulnerable.\n**WPA2**: Current standard. Use AES encryption.\n**WPA3**: Latest standard. Stronger encryption, better protection against brute force.\n\n**Best practices:**\n- Use WPA3 or WPA2-AES\n- Use a strong password (12+ characters, mixed case, numbers, symbols)\n- Change default router admin credentials\n- Disable WPS\n- Create a separate guest network\n- Regularly check connected devices\n\n## Wireless Troubleshooting\n\n- Weak signal: Move closer to router, add access point, check for interference\n- Slow speed: Check for interference, switch to 5 GHz, check ISP speed\n- Cannot connect: Check password, restart router, forget and reconnect\n- Intermittent drops: Check for interference, update router firmware, check cable connections\n\n## Practical Task\n\nConduct a Wi-Fi survey of your home or school. Use a Wi-Fi analyser app (Android: WiFi Analyzer) to: identify all nearby networks, check signal strength in different rooms, identify the least congested channel, and recommend the optimal channel and placement for the router.\n\n## Self-Check\n1. What is the difference between 2.4 GHz and 5 GHz Wi-Fi?\n2. Which Wi-Fi security protocol should you use?\n3. What is the maximum theoretical speed of Wi-Fi 6?"),
("Network Security & Monitoring","## Network Security Fundamentals\n\nNetwork security protects the integrity, confidentiality, and availability of data as it travels across or is stored in a network.\n\n## Firewall Configuration\n\nFirewalls control traffic based on rules:\n\n**Allow rules (whitelist approach):**\n- Allow TCP port 443 (HTTPS) from any to web server\n- Allow TCP port 22 (SSH) from admin IP only to servers\n- Allow TCP port 3306 (MySQL) from web server only to database server\n\n**Deny rules:**\n- Deny all traffic from known malicious IP ranges\n- Deny all inbound traffic not matching an allow rule (implicit deny)\n\n## Network Monitoring Tools\n\n- **Wireshark**: Capture and analyse network packets\n- **Nmap**: Network scanner, discover hosts and open ports\n- **Nagios**: Monitor network devices and services, send alerts\n- **PRTG**: Network monitoring with dashboards\n- **Zabbix**: Open-source monitoring platform\n- **ntopng**: Real-time network traffic analysis\n\n## Common Network Attacks\n\n- **DDoS**: Overwhelm a server with traffic from many sources\n- **ARP Poisoning**: Redirect traffic through the attacker's machine\n- **DNS Spoofing**: Redirect domain lookups to malicious IPs\n- **VLAN Hopping**: Gain access to a VLAN you should not be on\n- **Rogue Access Point**: Fake Wi-Fi hotspot to intercept traffic\n\n## Network Hardening Checklist\n\n- Change all default passwords on network equipment\n- Disable unused ports and services\n- Enable port security on switches (limit MAC addresses per port)\n- Implement 802.1X authentication for network access\n- Use VLANs to segment sensitive systems\n- Enable logging on all network devices\n- Regularly review firewall rules\n- Keep firmware updated\n\n## Practical Task\n\nInstall Wireshark and capture 5 minutes of network traffic. Identify: the top 5 protocols by packet count, any unencrypted HTTP traffic, DNS queries being made, and the IP addresses communicating most frequently. Write a brief security assessment of what you observed.\n\n## Self-Check\n1. What is the implicit deny rule in firewall configuration?\n2. What is a rogue access point?\n3. Name 3 network monitoring tools."),
("Networking Capstone","## Final Project: Network Design & Implementation\n\nYou will design and implement a complete network solution for a fictional organisation.\n\n## Scenario\n\nMirror Academy Nigeria is opening a new campus with the following requirements:\n- 3 buildings: Admin Block, Classroom Block, Computer Lab\n- 50 computers in the Computer Lab\n- 20 computers in Admin Block\n- Wi-Fi coverage in all buildings and outdoor areas\n- Separate networks for staff and students\n- Internet connection shared across all buildings\n- A file server accessible to all staff\n- A web server hosting the school website\n\n## Deliverables\n\n### 1. Network Design Document\n- IP addressing scheme (subnets for each building and VLAN)\n- Equipment list with specifications and estimated costs\n- Network topology diagram (logical and physical)\n- Security policy (firewall rules, Wi-Fi security, access control)\n\n### 2. Cisco Packet Tracer Implementation\n- Build the complete network in Packet Tracer\n- Configure all IP addresses, VLANs, and routing\n- Test connectivity between all buildings\n- Verify that staff and student networks are isolated\n\n### 3. Network Documentation\n- Network diagram (export from Packet Tracer)\n- IP address table (all devices with their IPs)\n- VLAN table\n- Firewall rule table\n- Maintenance schedule\n\n### 4. Presentation\n- 10-minute presentation explaining your design decisions\n- Demonstrate the working network in Packet Tracer\n- Explain how the design meets each requirement\n\n## Evaluation Criteria\n- Network design quality and scalability (25%)\n- IP addressing and subnetting accuracy (20%)\n- Security implementation (20%)\n- Packet Tracer implementation (25%)\n- Documentation quality (10%)\n\n## Self-Check\n1. Does your design meet all the stated requirements?\n2. Is your IP addressing scheme logical and scalable?\n3. Are staff and student networks properly isolated?"),
], 1):
    L(14, t, c, i)


# Course 15: Cloud Computing
for i,(t,c) in enumerate([
("Cloud Computing Fundamentals","## What is Cloud Computing?\n\nCloud computing is the delivery of computing services (servers, storage, databases, networking, software, analytics) over the internet (the cloud) on a pay-as-you-go basis.\n\n## Cloud Service Models\n\n**IaaS (Infrastructure as a Service)**: Rent virtual machines, storage, and networking. You manage the OS and everything above.\nExamples: AWS EC2, Google Compute Engine, Azure Virtual Machines.\n\n**PaaS (Platform as a Service)**: Rent a platform to build and deploy applications. The provider manages the infrastructure and OS.\nExamples: Heroku, Google App Engine, AWS Elastic Beanstalk.\n\n**SaaS (Software as a Service)**: Use software over the internet. The provider manages everything.\nExamples: Gmail, Microsoft 365, Salesforce, Zoom.\n\n## Cloud Deployment Models\n\n**Public Cloud**: Resources owned and operated by a third-party provider (AWS, Azure, GCP). Shared infrastructure.\n**Private Cloud**: Cloud infrastructure operated solely for one organisation. More control, more expensive.\n**Hybrid Cloud**: Combination of public and private cloud. Sensitive data on private, scalable workloads on public.\n**Multi-Cloud**: Using services from multiple cloud providers to avoid vendor lock-in.\n\n## Benefits of Cloud Computing\n\n- **Cost savings**: No upfront hardware investment. Pay only for what you use.\n- **Scalability**: Scale up or down instantly based on demand.\n- **Reliability**: Built-in redundancy and disaster recovery.\n- **Global reach**: Deploy in data centres worldwide.\n- **Security**: Enterprise-grade security managed by the provider.\n- **Speed**: Deploy new resources in minutes, not weeks.\n\n## Major Cloud Providers\n\n- **AWS (Amazon Web Services)**: Market leader, 200+ services\n- **Microsoft Azure**: Strong enterprise integration, Office 365\n- **Google Cloud Platform (GCP)**: Strong in AI/ML and data analytics\n- **Oracle Cloud**: Strong in databases and enterprise applications\n\n## Practical Task\n\nCreate a free AWS account (aws.amazon.com/free). Explore the AWS Management Console. Identify 5 services you would use to host a web application. Write a 1-page comparison of AWS, Azure, and GCP for a Nigerian startup.\n\n## Self-Check\n1. What is the difference between IaaS, PaaS, and SaaS?\n2. What is the difference between public and private cloud?\n3. Name 3 benefits of cloud computing."),
("AWS Core Services","## AWS Global Infrastructure\n\nAWS operates in Regions (geographic areas) and Availability Zones (isolated data centres within a region).\n\n- 33 Regions worldwide (as of 2025)\n- Each Region has 2-6 Availability Zones\n- Choose the Region closest to your users for lowest latency\n- Closest to Nigeria: eu-west-1 (Ireland) or af-south-1 (Cape Town)\n\n## Core AWS Services\n\n**Compute:**\n- **EC2 (Elastic Compute Cloud)**: Virtual machines. Choose instance type (CPU, RAM, storage).\n- **Lambda**: Serverless functions. Run code without managing servers. Pay per execution.\n- **ECS/EKS**: Container services (Docker, Kubernetes).\n\n**Storage:**\n- **S3 (Simple Storage Service)**: Object storage. Store files, images, backups. 99.999999999% durability.\n- **EBS (Elastic Block Store)**: Block storage for EC2 instances (like a hard drive).\n- **EFS (Elastic File System)**: Shared file storage for multiple EC2 instances.\n\n**Database:**\n- **RDS (Relational Database Service)**: Managed MySQL, PostgreSQL, SQL Server, Oracle.\n- **DynamoDB**: Managed NoSQL database. Millisecond latency at any scale.\n- **ElastiCache**: Managed Redis/Memcached for caching.\n\n**Networking:**\n- **VPC (Virtual Private Cloud)**: Your private network in AWS.\n- **Route 53**: DNS service and domain registration.\n- **CloudFront**: CDN (Content Delivery Network) for fast global content delivery.\n- **ELB (Elastic Load Balancer)**: Distribute traffic across multiple EC2 instances.\n\n**Security:**\n- **IAM (Identity and Access Management)**: Control who can access what in AWS.\n- **WAF (Web Application Firewall)**: Protect web applications from common attacks.\n- **Shield**: DDoS protection.\n\n## Practical Task\n\nLaunch a free-tier EC2 instance (t2.micro, Amazon Linux 2). Connect via SSH. Install Apache web server. Create a simple HTML page. Access it via the public IP address. Take a screenshot of your working website.\n\n## Self-Check\n1. What is the difference between EC2 and Lambda?\n2. What is S3 used for?\n3. What is IAM and why is it important?"),
("Containerisation with Docker","## What is Docker?\n\nDocker is a platform for developing, shipping, and running applications in containers. A container packages your application and all its dependencies into a single, portable unit.\n\n## Why Containers?\n\n**The problem**: It works on my machine but not on the server.\n**The solution**: Containers include everything the app needs to run.\n\nBenefits:\n- Consistent environments (dev, test, production)\n- Fast startup (seconds, not minutes)\n- Lightweight (share the host OS kernel)\n- Portable (run anywhere Docker is installed)\n- Scalable (spin up many containers quickly)\n\n## Docker vs Virtual Machines\n\n**Virtual Machine**: Includes a full OS. Heavy (GBs). Slow to start (minutes).\n**Container**: Shares the host OS kernel. Lightweight (MBs). Fast to start (seconds).\n\n## Docker Fundamentals\n\n```bash\n# Install Docker Desktop from docker.com\n\n# Pull an image from Docker Hub\ndocker pull nginx\ndocker pull mysql:8.0\n\n# Run a container\ndocker run -d -p 8080:80 --name my-nginx nginx\n# -d: Run in background (detached)\n# -p 8080:80: Map host port 8080 to container port 80\n# --name: Give the container a name\n\n# List running containers\ndocker ps\n\n# Stop and remove a container\ndocker stop my-nginx\ndocker rm my-nginx\n\n# View container logs\ndocker logs my-nginx\n```\n\n## Dockerfile\n\nA Dockerfile defines how to build a custom image:\n\n```dockerfile\n# Start from an official PHP image\nFROM php:8.2-apache\n\n# Install extensions\nRUN docker-php-ext-install pdo pdo_mysql\n\n# Copy application files\nCOPY . /var/www/html/\n\n# Set permissions\nRUN chown -R www-data:www-data /var/www/html\n\n# Expose port 80\nEXPOSE 80\n```\n\n## Docker Compose\n\nDocker Compose defines multi-container applications:\n\n```yaml\n# docker-compose.yml\nversion: '3.8'\nservices:\n  web:\n    build: .\n    ports:\n      - '8080:80'\n    depends_on:\n      - db\n    environment:\n      DB_HOST: db\n      DB_NAME: lms\n\n  db:\n    image: mysql:8.0\n    environment:\n      MYSQL_ROOT_PASSWORD: secret\n      MYSQL_DATABASE: lms\n    volumes:\n      - db_data:/var/lib/mysql\n\nvolumes:\n  db_data:\n```\n\n## Practical Task\n\nContainerise your PHP blog application from the Web Development course. Create a Dockerfile for the PHP app and a docker-compose.yml that includes the PHP app and a MySQL database. Run it locally with docker-compose up. Verify the app works in the container.\n\n## Self-Check\n1. What is the difference between a Docker image and a container?\n2. What does the -p flag do in docker run?\n3. What is Docker Compose used for?"),
("Cloud Architecture & Best Practices","## Well-Architected Framework\n\nAWS Well-Architected Framework defines 6 pillars for building reliable, secure, efficient cloud systems:\n\n1. **Operational Excellence**: Run and monitor systems to deliver business value\n2. **Security**: Protect information, systems, and assets\n3. **Reliability**: Recover from failures and meet demand\n4. **Performance Efficiency**: Use computing resources efficiently\n5. **Cost Optimisation**: Avoid unnecessary costs\n6. **Sustainability**: Minimise environmental impact\n\n## High Availability & Fault Tolerance\n\n**High Availability**: System remains operational despite component failures.\n- Deploy across multiple Availability Zones\n- Use load balancers to distribute traffic\n- Use auto-scaling to handle demand spikes\n\n**Fault Tolerance**: System continues operating even when components fail.\n- Redundant components (no single point of failure)\n- Automatic failover\n- Data replication across regions\n\n## Auto Scaling\n\nAuto Scaling automatically adjusts the number of EC2 instances based on demand:\n- Scale out: Add instances when CPU > 70%\n- Scale in: Remove instances when CPU < 30%\n- Minimum instances: 2 (always running)\n- Maximum instances: 10 (cost cap)\n\n## Cloud Cost Optimisation\n\n- **Right-sizing**: Use the smallest instance that meets your needs\n- **Reserved Instances**: Commit to 1-3 years for up to 72% discount\n- **Spot Instances**: Use spare AWS capacity for up to 90% discount (can be interrupted)\n- **Auto Scaling**: Only pay for what you use\n- **S3 Lifecycle Policies**: Move old data to cheaper storage tiers\n- **Delete unused resources**: Unattached EBS volumes, unused Elastic IPs\n\n## Infrastructure as Code (IaC)\n\nDefine your infrastructure in code for repeatability and version control:\n\n```yaml\n# AWS CloudFormation template\nAWSTemplateFormatVersion: '2010-09-09'\nResources:\n  WebServer:\n    Type: AWS::EC2::Instance\n    Properties:\n      InstanceType: t3.micro\n      ImageId: ami-0c55b159cbfafe1f0\n      SecurityGroups:\n        - !Ref WebSecurityGroup\n```\n\nOther IaC tools: Terraform (multi-cloud), Ansible (configuration management).\n\n## Practical Task\n\nDesign a highly available architecture for a web application on AWS. Draw a diagram showing: VPC with public and private subnets across 2 AZs, Application Load Balancer, Auto Scaling Group with EC2 instances, RDS Multi-AZ database, S3 for static assets, and CloudFront CDN. Estimate the monthly cost using the AWS Pricing Calculator.\n\n## Self-Check\n1. What are the 6 pillars of the AWS Well-Architected Framework?\n2. What is the difference between high availability and fault tolerance?\n3. What is Infrastructure as Code?"),
("DevOps & CI/CD","## What is DevOps?\n\nDevOps is a set of practices that combines software development (Dev) and IT operations (Ops) to shorten the development lifecycle and deliver high-quality software continuously.\n\n## DevOps Principles\n\n- **Collaboration**: Dev and Ops teams work together, not in silos\n- **Automation**: Automate repetitive tasks (testing, deployment, monitoring)\n- **Continuous Improvement**: Measure, learn, and improve constantly\n- **Customer Focus**: Deliver value to users quickly and reliably\n\n## CI/CD Pipeline\n\n**CI (Continuous Integration)**: Developers merge code frequently. Each merge triggers automated tests.\n**CD (Continuous Delivery)**: Code is always in a deployable state. Deploy to production with one click.\n**CD (Continuous Deployment)**: Every passing build is automatically deployed to production.\n\n## GitHub Actions (CI/CD)\n\n```yaml\n# .github/workflows/deploy.yml\nname: Deploy to AWS\n\non:\n  push:\n    branches: [main]\n\njobs:\n  test:\n    runs-on: ubuntu-latest\n    steps:\n      - uses: actions/checkout@v3\n      - name: Run tests\n        run: |\n          composer install\n          php vendor/bin/phpunit\n\n  deploy:\n    needs: test\n    runs-on: ubuntu-latest\n    steps:\n      - uses: actions/checkout@v3\n      - name: Deploy to server\n        uses: appleboy/ssh-action@master\n        with:\n          host: ${{ secrets.SERVER_HOST }}\n          username: ${{ secrets.SERVER_USER }}\n          key: ${{ secrets.SSH_PRIVATE_KEY }}\n          script: |\n            cd /var/www/html/app\n            git pull origin main\n            composer install --no-dev\n            php artisan migrate --force\n```\n\n## Monitoring & Observability\n\n**The 3 Pillars of Observability:**\n- **Logs**: Detailed records of events (what happened)\n- **Metrics**: Numerical measurements over time (CPU, memory, request rate)\n- **Traces**: Track a request through multiple services\n\nTools: AWS CloudWatch, Datadog, Grafana, Prometheus, ELK Stack.\n\n## Practical Task\n\nSet up a CI/CD pipeline for your PHP application using GitHub Actions. The pipeline should: run PHPUnit tests on every push, deploy to an EC2 instance if tests pass, send a Slack notification on success or failure. Document the pipeline and test it with a code change.\n\n## Self-Check\n1. What is the difference between CI and CD?\n2. What are the 3 pillars of observability?\n3. What triggers a GitHub Actions workflow?"),
("Cloud Computing Capstone","## Final Project: Cloud Architecture & Deployment\n\nYou will design, build, and deploy a complete cloud-based application on AWS.\n\n## Project Brief\n\nDeploy the Mirror LMS application (or a similar web application) to AWS with a production-grade architecture.\n\n## Architecture Requirements\n\n### Infrastructure\n- VPC with public and private subnets across 2 Availability Zones\n- Application Load Balancer in the public subnet\n- EC2 instances in an Auto Scaling Group in private subnets\n- RDS MySQL in a private subnet (Multi-AZ for high availability)\n- S3 bucket for file uploads and static assets\n- CloudFront distribution for global content delivery\n- Route 53 for DNS management\n\n### Security\n- IAM roles with least privilege for all services\n- Security groups allowing only necessary traffic\n- RDS not publicly accessible\n- HTTPS only (SSL certificate via AWS Certificate Manager)\n- S3 bucket not publicly accessible (accessed via CloudFront)\n\n### DevOps\n- GitHub repository for the application code\n- GitHub Actions CI/CD pipeline\n- Automated deployment on push to main branch\n- CloudWatch alarms for CPU, memory, and error rate\n\n## Deliverables\n\n1. Architecture diagram (AWS icons, all components labelled)\n2. Terraform or CloudFormation template for the infrastructure\n3. GitHub Actions workflow file\n4. Working application accessible via a domain name\n5. CloudWatch dashboard screenshot\n6. Cost estimate (AWS Pricing Calculator)\n7. 10-minute presentation explaining architecture decisions\n\n## Evaluation Criteria\n- Architecture design quality (25%)\n- Security implementation (20%)\n- Working deployment (25%)\n- CI/CD pipeline (15%)\n- Documentation and presentation (15%)\n\n## Self-Check\n1. Is your application accessible via HTTPS?\n2. Is your database in a private subnet?\n3. Does your CI/CD pipeline run tests before deploying?"),
], 1):
    L(15, t, c, i)


# Course 16: Software Engineering
for i,(t,c) in enumerate([
("Software Engineering Principles","## What is Software Engineering?\n\nSoftware engineering is the systematic application of engineering principles to the design, development, testing, and maintenance of software. It goes beyond coding — it is about building reliable, maintainable, and scalable systems.\n\n## Software Development Life Cycle (SDLC)\n\n1. **Planning**: Define scope, timeline, budget, and feasibility\n2. **Requirements Analysis**: Gather and document what the system must do\n3. **System Design**: Architecture, database design, UI design\n4. **Implementation**: Write the code\n5. **Testing**: Verify the system works correctly\n6. **Deployment**: Release to production\n7. **Maintenance**: Fix bugs, add features, optimise performance\n\n## SDLC Models\n\n**Waterfall**: Sequential phases. Each phase must complete before the next begins. Good for well-defined, stable requirements.\n\n**Agile**: Iterative development in short sprints (1-4 weeks). Deliver working software frequently. Adapt to changing requirements.\n\n**Scrum**: Agile framework with defined roles (Product Owner, Scrum Master, Development Team) and ceremonies (Sprint Planning, Daily Standup, Sprint Review, Retrospective).\n\n**Kanban**: Visual workflow management. Work items move through columns (To Do, In Progress, Done). No fixed sprints.\n\n## Software Quality Attributes\n\n- **Functionality**: Does it do what it is supposed to do?\n- **Reliability**: Does it work consistently without failures?\n- **Usability**: Is it easy to use?\n- **Efficiency**: Does it use resources (CPU, memory) efficiently?\n- **Maintainability**: Is it easy to modify and extend?\n- **Portability**: Can it run on different platforms?\n- **Security**: Is it protected against threats?\n\n## Practical Task\n\nChoose a software project you want to build. Write a complete project plan including: problem statement, target users, functional requirements (what it does), non-functional requirements (performance, security, usability), SDLC model choice with justification, and a 3-month timeline with milestones.\n\n## Self-Check\n1. What are the 7 phases of the SDLC?\n2. What is the difference between Waterfall and Agile?\n3. What are the 7 software quality attributes?"),
("Software Architecture & Design Patterns","## What is Software Architecture?\n\nSoftware architecture is the high-level structure of a software system — the major components, their relationships, and the principles governing their design and evolution.\n\n## Architectural Patterns\n\n**Monolithic Architecture**: All components in a single deployable unit.\n- Pros: Simple to develop and deploy initially\n- Cons: Hard to scale, hard to maintain as it grows\n\n**Microservices Architecture**: Application split into small, independent services.\n- Pros: Independent scaling, independent deployment, technology flexibility\n- Cons: Complex to manage, network overhead, distributed system challenges\n\n**MVC (Model-View-Controller)**: Separates application into 3 components.\n- Model: Data and business logic\n- View: User interface\n- Controller: Handles user input, coordinates Model and View\n\n**Event-Driven Architecture**: Components communicate through events.\n- Producer publishes an event\n- Consumer subscribes to events\n- Decoupled, scalable, asynchronous\n\n## SOLID Principles\n\n**S - Single Responsibility**: A class should have only one reason to change.\n**O - Open/Closed**: Open for extension, closed for modification.\n**L - Liskov Substitution**: Subclasses should be substitutable for their base class.\n**I - Interface Segregation**: Many specific interfaces are better than one general interface.\n**D - Dependency Inversion**: Depend on abstractions, not concretions.\n\n## Design Patterns\n\n**Creational Patterns:**\n- Singleton: Ensure only one instance of a class exists\n- Factory: Create objects without specifying the exact class\n- Builder: Construct complex objects step by step\n\n**Structural Patterns:**\n- Adapter: Make incompatible interfaces work together\n- Decorator: Add behaviour to objects dynamically\n- Repository: Abstract data access logic\n\n**Behavioural Patterns:**\n- Observer: Notify multiple objects when state changes\n- Strategy: Define a family of algorithms and make them interchangeable\n- Command: Encapsulate a request as an object\n\n## Practical Task\n\nRefactor a simple PHP application to use the MVC pattern. Separate: database queries (Model), HTML templates (View), and request handling (Controller). Apply the Repository pattern for data access. Apply at least 2 SOLID principles.\n\n## Self-Check\n1. What is the difference between monolithic and microservices architecture?\n2. What does SOLID stand for?\n3. What is the Repository pattern?"),
("Agile & Scrum in Practice","## Agile Manifesto\n\nThe Agile Manifesto (2001) values:\n- Individuals and interactions over processes and tools\n- Working software over comprehensive documentation\n- Customer collaboration over contract negotiation\n- Responding to change over following a plan\n\n## Scrum Framework\n\n**Roles:**\n- **Product Owner**: Represents the customer. Owns the Product Backlog. Prioritises features.\n- **Scrum Master**: Facilitates the process. Removes impediments. Coaches the team.\n- **Development Team**: Self-organising, cross-functional. Typically 3-9 people.\n\n**Artefacts:**\n- **Product Backlog**: Ordered list of everything that might be needed in the product\n- **Sprint Backlog**: Items selected for the current sprint\n- **Increment**: The working software produced at the end of each sprint\n\n**Events:**\n- **Sprint**: Time-boxed iteration (1-4 weeks). Fixed duration.\n- **Sprint Planning**: Team selects items from Product Backlog for the sprint\n- **Daily Scrum (Standup)**: 15-minute daily sync. What did I do yesterday? What will I do today? Any blockers?\n- **Sprint Review**: Demo working software to stakeholders\n- **Sprint Retrospective**: What went well? What could improve? What will we change?\n\n## User Stories\n\nUser stories describe features from the user's perspective:\n\nAs a [type of user], I want [some goal] so that [some reason].\n\nExample: As a student, I want to see my assignment due dates on the dashboard so that I never miss a submission.\n\n**Acceptance Criteria**: Conditions that must be met for the story to be considered done.\n- Given [context], When [action], Then [outcome]\n\n**Story Points**: Relative measure of effort (1, 2, 3, 5, 8, 13, 21 — Fibonacci sequence).\n\n## Kanban\n\nKanban visualises work on a board:\n- Columns: Backlog | To Do | In Progress | Review | Done\n- WIP (Work in Progress) limits: Limit items in each column to prevent overload\n- Lead time: Time from request to delivery\n- Cycle time: Time from start to completion\n\n## Tools\n\n- Jira: Most popular Agile project management tool\n- Trello: Simple Kanban boards\n- Linear: Modern, fast issue tracker\n- GitHub Projects: Integrated with GitHub\n- Notion: Flexible workspace\n\n## Practical Task\n\nSet up a Trello board for your capstone project. Create columns: Backlog, To Do, In Progress, Review, Done. Write 10 user stories for your project. Estimate story points for each. Plan a 2-week sprint by selecting stories that fit your capacity.\n\n## Self-Check\n1. What are the 3 Scrum roles?\n2. What is the purpose of the Daily Scrum?\n3. What is a user story and what format does it follow?"),
("Testing & Quality Assurance","## Why Testing?\n\nSoftware testing verifies that a system works as expected and meets requirements. The cost of fixing a bug increases dramatically the later it is found:\n- In development: 1x cost\n- In testing: 10x cost\n- In production: 100x cost\n\n## Testing Types\n\n**Unit Testing**: Test individual functions or methods in isolation.\n**Integration Testing**: Test how components work together.\n**System Testing**: Test the complete system end-to-end.\n**Acceptance Testing (UAT)**: Users verify the system meets their requirements.\n**Regression Testing**: Verify that new changes have not broken existing functionality.\n**Performance Testing**: Test system behaviour under load.\n**Security Testing**: Test for vulnerabilities.\n\n## Test-Driven Development (TDD)\n\nTDD is a development approach where you write tests before writing code:\n1. Write a failing test (Red)\n2. Write the minimum code to make the test pass (Green)\n3. Refactor the code while keeping tests passing (Refactor)\n\nBenefits: Forces clear requirements, produces testable code, provides a safety net for refactoring.\n\n## PHPUnit Example\n\n```php\nclass PaymentCalculatorTest extends TestCase {\n    private PaymentCalculator $calc;\n\n    protected function setUp(): void {\n        $this->calc = new PaymentCalculator();\n    }\n\n    public function testFullPaymentReturnsCorrectAmount(): void {\n        $result = $this->calc->calculate(150000, 'full');\n        $this->assertEquals(150000, $result['amount']);\n        $this->assertEquals('full', $result['type']);\n    }\n\n    public function testInstallmentReturnsHalfAmount(): void {\n        $result = $this->calc->calculate(150000, 'installment');\n        $this->assertEquals(75000, $result['amount']);\n        $this->assertEquals('installment', $result['type']);\n    }\n\n    public function testZeroPriceThrowsException(): void {\n        $this->expectException(InvalidArgumentException::class);\n        $this->calc->calculate(0, 'full');\n    }\n}\n```\n\n## Code Coverage\n\nCode coverage measures what percentage of your code is executed by tests:\n- Line coverage: % of lines executed\n- Branch coverage: % of branches (if/else) executed\n- Function coverage: % of functions called\n\nTarget: 80%+ coverage for critical business logic.\n\n## Practical Task\n\nWrite a test suite for the payment module of the LMS application. Cover: full payment calculation, installment calculation, payment verification, enrollment status update after payment, and edge cases (zero amount, negative amount, already paid). Achieve 90%+ code coverage.\n\n## Self-Check\n1. What is the difference between unit testing and integration testing?\n2. What are the 3 steps of TDD (Red-Green-Refactor)?\n3. What is code coverage?"),
("System Design & Scalability","## System Design Fundamentals\n\nSystem design is the process of defining the architecture, components, modules, interfaces, and data for a system to satisfy specified requirements.\n\n## Scalability\n\n**Vertical Scaling (Scale Up)**: Add more resources to a single server (more CPU, RAM, storage). Simple but has limits.\n\n**Horizontal Scaling (Scale Out)**: Add more servers. More complex but virtually unlimited.\n\n## Load Balancing\n\nDistributes incoming traffic across multiple servers:\n- **Round Robin**: Each server gets requests in turn\n- **Least Connections**: Send to the server with fewest active connections\n- **IP Hash**: Same client always goes to the same server (session persistence)\n\n## Caching\n\nCaching stores frequently accessed data in fast memory to reduce database load:\n\n**Application Cache (Redis/Memcached):**\n```php\n$redis = new Redis();\n$redis->connect('127.0.0.1', 6379);\n\n// Cache a database query result for 1 hour\n$key = 'courses:all';\n$courses = $redis->get($key);\nif (!$courses) {\n    $courses = $pdo->query('SELECT * FROM lms_courses')->fetchAll();\n    $redis->setex($key, 3600, serialize($courses));\n} else {\n    $courses = unserialize($courses);\n}\n```\n\n**CDN (Content Delivery Network)**: Cache static assets (images, CSS, JS) on servers worldwide.\n\n## Database Scaling\n\n**Read Replicas**: One primary database handles writes. Multiple replicas handle reads.\n**Sharding**: Split data across multiple databases (e.g. users A-M on DB1, N-Z on DB2).\n**Connection Pooling**: Reuse database connections instead of creating new ones for each request.\n\n## Message Queues\n\nDecouple components and handle asynchronous tasks:\n- User registers -> Add to queue -> Send welcome email asynchronously\n- Tools: RabbitMQ, AWS SQS, Redis Queue\n\n## Designing for 1 Million Users\n\n1. Start with a single server\n2. Add a database server\n3. Add a load balancer + multiple web servers\n4. Add caching (Redis)\n5. Add a CDN for static assets\n6. Add read replicas for the database\n7. Add a message queue for async tasks\n8. Consider microservices for independent scaling\n\n## Practical Task\n\nDesign the system architecture for a Nigerian ride-hailing app (like Bolt or Uber). Consider: user registration and authentication, real-time driver location tracking, ride matching algorithm, payment processing, notifications, and rating system. Draw the architecture diagram and explain your technology choices.\n\n## Self-Check\n1. What is the difference between vertical and horizontal scaling?\n2. What is caching and why does it improve performance?\n3. What is a message queue and when would you use one?"),
("Capstone: Software Engineering Project","## Final Project Brief\n\nYou will design and build a complete software system applying all software engineering principles from this course.\n\n## Project: Build a SaaS Product\n\nDesign and build a Software as a Service (SaaS) product for a Nigerian market problem of your choice.\n\n## Examples\n\n- School management system for Nigerian secondary schools\n- Inventory and invoicing system for Nigerian SMEs\n- Telemedicine platform connecting patients with doctors\n- Agricultural marketplace connecting farmers with buyers\n- HR and payroll system for small businesses\n\n## Phase 1: Requirements & Design (Week 1-2)\n\n**Requirements Document:**\n- Problem statement and target market\n- User personas (2-3)\n- Functional requirements (user stories with acceptance criteria)\n- Non-functional requirements (performance, security, scalability)\n- Out of scope (what you will NOT build)\n\n**Technical Design:**\n- System architecture diagram\n- Database schema (ERD)\n- API design (endpoints, request/response format)\n- UI wireframes (key screens)\n\n## Phase 2: Development (Week 3-6)\n\n- Set up Git repository with branching strategy\n- Implement features in sprints (2-week sprints)\n- Write unit tests for all business logic\n- Code review for every pull request\n- Daily standups (even if solo — write a daily log)\n\n## Phase 3: Testing & Deployment (Week 7-8)\n\n- Complete test suite (80%+ coverage)\n- Performance testing (handle 100 concurrent users)\n- Security audit (OWASP Top 10 checklist)\n- Deploy to AWS or similar cloud platform\n- Set up monitoring and alerting\n\n## Deliverables\n\n1. Requirements document\n2. Technical design document with architecture diagram and ERD\n3. GitHub repository with clean commit history\n4. Working application deployed to the cloud\n5. Test suite with coverage report\n6. 15-minute demo presentation\n7. Post-mortem: What went well, what you would do differently\n\n## Evaluation Criteria\n- Requirements quality and completeness (15%)\n- Architecture and design quality (20%)\n- Code quality and test coverage (25%)\n- Working product functionality (25%)\n- Deployment and DevOps (10%)\n- Presentation and documentation (5%)\n\n## Self-Check\n1. Does your architecture handle the expected load?\n2. Is your code covered by tests?\n3. Would you be comfortable showing this to a potential employer?"),
], 1):
    L(16, t, c, i)


# ── Write SQL output ──────────────────────────────────────────────────────────
out = open('database/lessons_patch.sql', 'w', encoding='utf-8')
out.write("-- Real lesson content for all 16 courses\n")
out.write("-- Generated by gen_lessons.py\n\n")
out.write("SET FOREIGN_KEY_CHECKS=0;\n")
out.write("TRUNCATE TABLE lms_lessons;\n")
out.write("SET FOREIGN_KEY_CHECKS=1;\n\n")
out.write("INSERT INTO `lms_lessons` (`id`,`course_id`,`title`,`content`,`sort_order`,`is_published`,`created_at`) VALUES\n")
out.write(",\n".join(rows))
out.write(";\n")
out.close()
print(f"Done. {len(rows)} lessons written to database/lessons_patch.sql")
