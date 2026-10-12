-- Create a database in your hosting control panel first, select it in phpMyAdmin, then import this file.
CREATE TABLE IF NOT EXISTS site_settings (
  setting_key VARCHAR(80) PRIMARY KEY,
  setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS skills (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(160) NOT NULL,
  group_name VARCHAR(100) NOT NULL DEFAULT 'General', level VARCHAR(120) NOT NULL DEFAULT 'Working knowledge',
  sort_order INT NOT NULL DEFAULT 0, is_visible TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS experience (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, role VARCHAR(180) NOT NULL, company VARCHAR(180) NOT NULL,
  period VARCHAR(100) NOT NULL, location VARCHAR(180) NULL, description LONGTEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0, is_visible TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180) NOT NULL, project_type VARCHAR(180) NOT NULL,
  description TEXT NOT NULL, tags TEXT NULL, sort_order INT NOT NULL DEFAULT 0,
  is_visible TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS education (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, degree VARCHAR(220) NOT NULL, institution VARCHAR(220) NOT NULL,
  period VARCHAR(100) NOT NULL, detail VARCHAR(255) NULL, sort_order INT NOT NULL DEFAULT 0,
  is_visible TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS contact_messages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(254) NOT NULL,
  subject VARCHAR(180) NULL, message TEXT NOT NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
  INDEX idx_contact_created_at (created_at), INDEX idx_contact_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('name','Ashraful Alam Shihab'),('role','Senior Desktop Support Engineer'),('location','Dhaka, Bangladesh'),
('headline','IT support, systems administration, and reliable technology experiences.'),
('intro','I help people and teams work confidently with technology—from end-user support and SaaS administration to IT assets, vendor coordination, and office infrastructure.'),
('linkedin','https://www.linkedin.com/in/ashraful98/'),('email',''),('phone',''),('photo','');

INSERT INTO skills (name,group_name,level,sort_order) SELECT 'L1/L2 Technical Support','Support','Core',1 WHERE NOT EXISTS (SELECT 1 FROM skills);
INSERT INTO skills (name,group_name,level,sort_order) SELECT 'Service Desk & Ticketing','Support','Core',2 WHERE (SELECT COUNT(*) FROM skills)=1;
INSERT INTO skills (name,group_name,level,sort_order) SELECT 'Hardware & Software Troubleshooting','Support','Core',3 WHERE (SELECT COUNT(*) FROM skills)=2;
INSERT INTO skills (name,group_name,level,sort_order) SELECT 'Google Workspace Administration','Administration','Core',4 WHERE (SELECT COUNT(*) FROM skills)=3;
INSERT INTO skills (name,group_name,level,sort_order) SELECT 'Mailbox & License Management','Administration','Core',5 WHERE (SELECT COUNT(*) FROM skills)=4;
INSERT INTO skills (name,group_name,level,sort_order) SELECT 'IT Asset Management','Operations','Core',6 WHERE (SELECT COUNT(*) FROM skills)=5;
INSERT INTO skills (name,group_name,level,sort_order) SELECT 'IT Procurement & Vendor Coordination','Operations','Core',7 WHERE (SELECT COUNT(*) FROM skills)=6;
INSERT INTO skills (name,group_name,level,sort_order) SELECT 'TCP/IP, DNS & LAN/WAN','Networking','Working knowledge',8 WHERE (SELECT COUNT(*) FROM skills)=7;
INSERT INTO skills (name,group_name,level,sort_order) SELECT 'Microsoft 365 & Entra ID','Learning','Currently developing',9 WHERE (SELECT COUNT(*) FROM skills)=8;
INSERT INTO skills (name,group_name,level,sort_order) SELECT 'Microsoft Azure (AZ-104 path)','Learning','Currently developing',10 WHERE (SELECT COUNT(*) FROM skills)=9;

INSERT INTO experience (role,company,period,location,description,sort_order) SELECT 'Senior Desktop Support Engineer','Noor Staffing Group','Mar 2026 – Present','Bangladesh',JSON_ARRAY('Provide L1/L2 support for end-user devices, software, peripherals, connectivity, and business applications.','Manage incidents and service requests through Zendesk.','Administer Google Workspace accounts, groups, permissions, mailboxes, and license allocation.','Manage RingCentral users and support LinkedIn and Indeed user administration.','Coordinate onboarding/offboarding, IT assets, purchasing, quotations, and vendor follow-up.','Supported the successful Bangladesh office network migration from Cat5 to Cat6.'),1 WHERE NOT EXISTS (SELECT 1 FROM experience);
INSERT INTO experience (role,company,period,location,description,sort_order) SELECT 'Desktop Support Specialist','Noor Staffing Group','Oct 2024 – Feb 2026','Bangladesh',JSON_ARRAY('Delivered desktop and end-user support for hardware, software, printers, applications, and connectivity.','Managed incidents and service requests using Jira Service Management.','Supported Google Workspace administration, onboarding, account setup, and access management.','Maintained IT asset records and supported workstation deployment.'),2 WHERE (SELECT COUNT(*) FROM experience)=1;
INSERT INTO experience (role,company,period,location,description,sort_order) SELECT 'Technical Support Advisor','Amber Software Solutions','Feb 2023 – Sep 2024','Remote / UK customers',JSON_ARRAY('Supported customers by phone, email, and remote sessions.','Troubleshot technical, application, hardware, software, and connectivity issues.','Assisted with Outlook configuration and email maintenance.','Contributed web portal updates using HTML, CSS, JavaScript, and core PHP.'),3 WHERE (SELECT COUNT(*) FROM experience)=2;
INSERT INTO experience (role,company,period,location,description,sort_order) SELECT 'IT Support Engineer','Flyte Solutions Ltd','Nov 2021 – Jan 2023','Bangladesh',JSON_ARRAY('Supported desktops, laptops, operating systems, printers, peripherals, email, and network connectivity.','Assisted with user accounts, shared resources, workstation deployment, upgrades, and maintenance.','Documented common support procedures and helped users with workplace technology.'),4 WHERE (SELECT COUNT(*) FROM experience)=3;
INSERT INTO experience (role,company,period,location,description,sort_order) SELECT 'Junior Back-End Developer Intern','JomRun Sdn Bhd','Oct 2021 – Dec 2021','Malaysia',JSON_ARRAY('Completed a short back-end development internship and gained exposure to software development workflows.'),5 WHERE (SELECT COUNT(*) FROM experience)=4;

INSERT INTO projects (title,project_type,description,tags,sort_order) SELECT 'Office Network Migration','Infrastructure · Networking','Supported the Bangladesh office migration from Cat5 to Cat6 cabling, coordinating implementation activities and helping deliver a successful upgrade.',JSON_ARRAY('Cat6 Cabling','Coordination','Office Infrastructure'),1 WHERE NOT EXISTS (SELECT 1 FROM projects);
INSERT INTO projects (title,project_type,description,tags,sort_order) SELECT 'Google Workspace Administration','SaaS · IT Operations','Manage mailbox and account requirements, groups, permissions, and license allocation with attention to access needs and cost-conscious administration.',JSON_ARRAY('Google Workspace','Licensing','Access Management'),2 WHERE (SELECT COUNT(*) FROM projects)=1;
INSERT INTO projects (title,project_type,description,tags,sort_order) SELECT 'Service Desk & Asset Operations','Support · Operations','Support ticket workflows, onboarding/offboarding, equipment allocation, inventory records, purchasing, and vendor follow-up.',JSON_ARRAY('Zendesk','IT Assets','Procurement'),3 WHERE (SELECT COUNT(*) FROM projects)=2;
INSERT INTO education (degree,institution,period,detail,sort_order) SELECT 'Bachelor of Science in Information Technology','Limkokwing University of Creative Technology','2019 – 2021','Cyberjaya, Selangor, Malaysia · CGPA: 3.91 / 4.00',1 WHERE NOT EXISTS (SELECT 1 FROM education);
