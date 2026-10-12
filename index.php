<?php
require_once __DIR__ . '/config.php';
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'contact') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $subject = trim((string)($_POST['subject'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));
    $honeypot = trim((string)($_POST['website'] ?? ''));
    if ($honeypot !== '') {
        flash('success', 'Thanks for your message.');
        redirect('index.php#contact');
    }
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
        flash('error', 'Please enter your name, a valid email address, and a message.');
        redirect('index.php#contact');
    }
    if (strlen($name) > 120 || strlen($email) > 254 || strlen($subject) > 180 || strlen($message) > 5000) {
        flash('error', 'One or more fields are too long.');
        redirect('index.php#contact');
    }
    $stmt = $pdo->prepare('INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)');
    $stmt->execute([$name, $email, $subject !== '' ? $subject : null, $message]);
    flash('success', 'Thanks for reaching out. Your message has been submitted.');
    redirect('index.php#contact');
}
$profile = [
    'name' => setting('name', 'Ashraful Alam Shihab'),
    'role' => setting('role', 'Senior Desktop Support Engineer'),
    'location' => setting('location', 'Dhaka, Bangladesh'),
    'headline' => setting('headline', 'IT support, systems administration, and reliable technology experiences.'),
    'intro' => setting('intro', 'I help people and teams work confidently with technology.'),
    'linkedin' => setting('linkedin', 'https://www.linkedin.com/in/ashraful98/'),
    'email' => setting('email', ''),
    'phone' => setting('phone', ''),
    'photo' => setting('photo', '')
];
$skills = $pdo->query('SELECT * FROM skills WHERE is_visible=1 ORDER BY sort_order,id')->fetchAll();
$experience = $pdo->query('SELECT * FROM experience WHERE is_visible=1 ORDER BY sort_order,id')->fetchAll();
$projects = $pdo->query('SELECT * FROM projects WHERE is_visible=1 ORDER BY sort_order,id')->fetchAll();
$education = $pdo->query('SELECT * FROM education WHERE is_visible=1 ORDER BY sort_order,id')->fetchAll();
$groups = [];
foreach ($skills as $skill) $groups[$skill['group_name']] = true;
$flashes = take_flashes();
$first = explode(' ', trim($profile['name']));
$firstName = array_shift($first);
$remainingName = implode(' ', $first);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($profile['name']) ?> — <?= e($profile['role']) ?> portfolio">
    <meta name="theme-color" content="#0b0d12">
    <title><?= e($profile['name']) ?> — IT Support & Operations</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script defer src="assets/js/main.js"></script>
</head>

<body>
    <div class="page-glow glow-one"></div>
    <div class="page-glow glow-two"></div>
    <header class="site-header" id="top">
        <nav class="nav container" aria-label="Main navigation"><a class="brand" href="#top"><span class="brand-mark">S.</span><span>SHIHAB<span class="brand-dot">/</span></span></a><button class="menu-toggle" id="menuToggle" aria-label="Open navigation" aria-expanded="false"><span></span><span></span></button>
            <div class="nav-links" id="navLinks"><a href="#about">About</a><a href="#skills">Skills</a><a href="#experience">Experience</a><a href="#projects">Projects</a><a href="#education">Education</a><a class="nav-contact" href="#contact">Let's talk <span>↗</span></a><button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">☼</button></div>
        </nav>
    </header>
    <main>
        <section class="hero container" id="home">
            <div class="hero-copy">
                <div class="availability"><span class="status-dot"></span> IT SUPPORT · OPERATIONS · INFRASTRUCTURE</div>
                <p class="eyebrow">HELLO, I'M</p>
                <h1><?= e($firstName) ?><br><span><?= e($remainingName) ?>.</span></h1>
                <p class="hero-role"><?= e($profile['role']) ?> <span class="role-divider">/</span> <?= e($profile['location']) ?></p>
                <p class="hero-description"><?= e($profile['intro']) ?></p>
                <div class="hero-actions"><a class="button button-primary" href="#experience">Explore my experience <span>↘</span></a><a class="button button-quiet" href="#contact">Contact me <span>↗</span></a></div>
                <div class="hero-socials"><?php if ($profile['linkedin']): ?><a href="<?= e($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer">LinkedIn <span>↗</span></a><?php endif; ?><span class="social-separator"></span><a href="#projects">Selected work <span>↘</span></a></div><?php if ($profile['phone'] || $profile['email']): ?><div class="hero-contact-details" aria-label="Contact details"><?php if ($profile['phone']): ?><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $profile['phone'])) ?>"><span class="detail-label">☎ </span><span><?= e($profile['phone']) ?></span></a><?php endif; ?><?php if ($profile['email']): ?><a href="mailto:<?= e($profile['email']) ?>"><span class="detail-label"> | 📧 </span><span><?= e($profile['email']) ?></span></a><?php endif; ?></div><?php endif; ?>
            </div>
            <div class="hero-visual">
                <div class="visual-orbit orbit-a"></div>
                <div class="visual-orbit orbit-b"></div>
                <div class="portrait-card">
                    <div class="portrait-topline"><span>PROFILE / 001</span><span>IT · SYSTEMS</span></div>
                    <div class="portrait-placeholder" role="img" aria-label="Profile portrait"><?php if ($profile['photo'] && is_file(__DIR__ . '/uploads/' . $profile['photo'])): ?><img src="uploads/<?= e($profile['photo']) ?>" alt="<?= e($profile['name']) ?>" class="profile-photo"><?php else: ?><div class="portrait-grid"></div>
                            <div class="portrait-initials">AS<span>.</span></div>
                            <div class="portrait-hint">ADD YOUR PHOTO IN ADMIN</div><?php endif; ?>
                    </div>
                    <div class="portrait-caption">
                        <div><strong><?= e($profile['name']) ?></strong><span>Support. Systems. Solutions.</span></div><span class="caption-arrow">↗</span>
                    </div>
                </div>
                <div class="floating-chip chip-top"><span class="chip-icon">✳</span><span>Reliable IT support</span></div>
                <div class="floating-chip chip-bottom"><span class="chip-pulse"></span><span>Always learning</span></div>
            </div><a class="scroll-cue" href="#about"><span class="scroll-line"></span> SCROLL TO EXPLORE</a>
        </section>
        <section class="section about-section" id="about">
            <div class="container section-grid">
                <div class="section-heading reveal">
                    <p class="eyebrow">01 / ABOUT</p>
                    <h2>Making technology<br><span>work for people.</span></h2>
                </div>
                <div class="about-content reveal">
                    <p class="lead"><?= e($profile['headline']) ?></p>
                    <p><?= e($profile['intro']) ?></p>
                    <div class="stat-grid">
                        <div class="stat"><strong>L1/L2</strong><span>End-user support</span></div>
                        <div class="stat"><strong>IT</strong><span>Support & operations</span></div>
                        <div class="stat"><strong>4+</strong><span>Years in IT support roles</span></div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section skills-section" id="skills">
            <div class="container">
                <div class="section-heading section-heading-wide reveal">
                    <div>
                        <p class="eyebrow">02 / TOOLKIT</p>
                        <h2>Skills & <span>strengths.</span></h2>
                    </div>
                    <p class="section-intro">A practical mix of user support, SaaS administration, IT operations, and developing infrastructure skills.</p>
                </div>
                <div class="skill-filters reveal"><button class="filter-button active" data-filter="all">All skills</button><?php foreach (array_keys($groups) as $group): ?><button class="filter-button" data-filter="<?= e(strtolower(str_replace(' ', '-', $group))) ?>"><?= e($group) ?></button><?php endforeach; ?></div>
                <div class="skills-grid"><?php foreach ($skills as $i => $skill): ?><article class="skill-card reveal" data-group="<?= e(strtolower(str_replace(' ', '-', $skill['group_name']))) ?>">
                            <div class="skill-card-top"><span class="skill-symbol">✳</span><span class="skill-status"><?= e($skill['level']) ?></span></div>
                            <h3><?= e($skill['name']) ?></h3>
                            <p><?= e($skill['group_name']) ?></p>
                        </article><?php endforeach; ?></div>
                <div class="learning-note reveal"><span class="learning-icon">↗</span>
                    <div><strong>Currently growing</strong>
                        <p>Microsoft 365 Administration · Microsoft Entra ID · Azure Administrator (AZ-104 learning path)</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="section experience-section" id="experience">
            <div class="container">
                <div class="section-heading section-heading-wide reveal">
                    <div>
                        <p class="eyebrow">03 / CAREER</p>
                        <h2>Work <span>experience.</span></h2>
                    </div>
                    <p class="section-intro">The roles, responsibilities, and practical experience that have shaped my approach to IT.</p>
                </div>
                <div class="timeline"><?php foreach ($experience as $i => $job): ?><article class="timeline-item reveal">
                            <div class="timeline-marker"><span></span></div>
                            <div class="timeline-date"><?= e($job['period']) ?></div>
                            <div class="timeline-content">
                                <div class="job-heading">
                                    <div>
                                        <h3><?= e($job['role']) ?></h3>
                                        <p class="company"><?= e($job['company']) ?> <span>·</span> <?= e($job['location'] ?? '') ?></p>
                                    </div><span class="job-index"><?= e(str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                                </div>
                                <ul><?php foreach (parse_json_list($job['description']) as $point): ?><li><?= e((string)$point) ?></li><?php endforeach; ?></ul>
                            </div>
                        </article><?php endforeach; ?></div>
            </div>
        </section>
        <section class="section projects-section" id="projects">
            <div class="container">
                <div class="section-heading section-heading-wide reveal">
                    <div>
                        <p class="eyebrow">04 / SELECTED WORK</p>
                        <h2>Work that <span>matters.</span></h2>
                    </div>
                    <p class="section-intro">Experience highlights and initiatives.</p>
                </div>
                <div class="projects-grid"><?php foreach ($projects as $i => $project): ?><article class="project-card reveal">
                            <div class="project-art project-art-<?= ($i % 3) + 1 ?>"><span class="project-number"><?= e(str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                                <div class="network-art"><i></i><i></i><i></i><i></i><b></b><b></b><b></b></div><span class="project-art-label"><?= e($project['project_type']) ?></span>
                            </div>
                            <div class="project-info">
                                <p class="project-type"><?= e($project['project_type']) ?></p>
                                <h3><?= e($project['title']) ?></h3>
                                <p><?= e($project['description']) ?></p>
                                <div class="tag-list"><?php foreach (parse_json_list($project['tags']) as $tag): ?><span><?= e((string)$tag) ?></span><?php endforeach; ?></div>
                            </div>
                        </article><?php endforeach; ?></div>
            </div>
        </section>
        <section class="section education-section" id="education">
            <div class="container section-grid">
                <div class="section-heading reveal">
                    <p class="eyebrow">05 / EDUCATION</p>
                    <h2>Built on a<br><span>strong foundation.</span></h2>
                </div>
                <div class="education-list"><?php foreach ($education as $item): ?><article class="education-card reveal">
                            <div class="education-icon">↗</div>
                            <div>
                                <p class="education-period"><?= e($item['period']) ?></p>
                                <h3><?= e($item['degree']) ?></h3>
                                <p class="education-institution"><?= e($item['institution']) ?></p>
                                <p class="education-detail"><?= e($item['detail'] ?? '') ?></p>
                            </div>
                        </article><?php endforeach; ?><div class="learning-card reveal">
                        <div class="learning-icon">◎</div>
                        <div>
                            <p class="eyebrow">CONTINUOUS LEARNING</p>
                            <h3>Building toward cloud & systems administration</h3>
                            <p>Focused on Microsoft 365, Entra ID, Azure administration, and practical lab-based learning.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section contact-section" id="contact">
            <div class="container contact-layout">
                <div class="contact-copy reveal">
                    <p class="eyebrow">06 / GET IN TOUCH</p>
                    <h2>Have a challenge?<br><span>Let’s talk.</span></h2>
                    <p>For IT support opportunities, collaboration, or a professional conversation, send me a message.</p><?php if ($profile['linkedin']): ?><a class="contact-link" href="<?= e($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer"><span class="contact-link-icon">in</span><span><small>CONNECT ON</small><strong>LinkedIn</strong></span><span class="contact-arrow">↗</span></a><?php endif; ?><?php if ($profile['email']): ?><a class="contact-link" href="mailto:<?= e($profile['email']) ?>"><span class="contact-link-icon">@</span><span><small>EMAIL ME</small><strong><?= e($profile['email']) ?></strong></span><span class="contact-arrow">↗</span></a><?php endif; ?><?php if ($profile['phone']): ?><a class="contact-link" href="tel:<?= e(preg_replace('/[^0-9+]/', '', $profile['phone'])) ?>"><span class="contact-link-icon">☎</span><span><small>CALL ME</small><strong><?= e($profile['phone']) ?></strong></span><span class="contact-arrow">↗</span></a><?php endif; ?><div class="contact-location"><span class="status-dot"></span> Based in <?= e($profile['location']) ?></div>
                </div>
                <div class="contact-form-wrap reveal">
                    <div class="form-header"><span>MESSAGE / 001</span><span class="form-asterisk">✳</span></div><?php foreach ($flashes as $f): ?><div class="flash flash-<?= e($f['type']) ?>" role="status"><?= e($f['message']) ?></div><?php endforeach; ?><form action="index.php#contact" method="post" id="contactForm"><input type="hidden" name="action" value="contact"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <div class="honeypot" aria-hidden="true"><label for="website">Leave empty</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div>
                        <div class="form-row"><label for="name">Your name <span>*</span><input id="name" name="name" type="text" placeholder="Jane Smith" maxlength="120" required></label><label for="email">Email address <span>*</span><input id="email" name="email" type="email" placeholder="jane@example.com" maxlength="254" required></label></div><label for="subject">Subject<input id="subject" name="subject" type="text" placeholder="What would you like to discuss?" maxlength="180"></label><label for="message">Your message <span>*</span><textarea id="message" name="message" rows="5" maxlength="5000" placeholder="Write your message here..." required></textarea></label>
                        <div class="form-bottom">
                            <p><span>*</span> Required fields</p><button class="button button-primary submit-button" type="submit">Send message <span>↗</span></button>
                        </div>
                        <p class="privacy-note">Your message is stored for follow-up. Please avoid sending sensitive personal information.</p>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <footer class="site-footer">
        <div class="container footer-inner"><a class="brand" href="#top"><span class="brand-mark">S.</span><span>SHIHAB<span class="brand-dot">/</span></span></a>
            <p>Designed with purpose. Built to keep things moving.</p><a href="#top" class="back-top">BACK TO TOP ↑</a><span class="copyright">© <?= date('Y') ?> <?= e($profile['name']) ?></span><a class="admin-footer-link" href="admin/login.php">Admin</a>
        </div>
    </footer>
</body>

</html>