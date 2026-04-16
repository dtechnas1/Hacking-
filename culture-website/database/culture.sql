CREATE DATABASE IF NOT EXISTS culture_website;
USE culture_website;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('gallery','video','ethics') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gallery_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255) NOT NULL,
    category_label ENUM('events','traditional_dress','activities') DEFAULT 'events',
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    video_url VARCHAR(500) NOT NULL,
    thumbnail VARCHAR(255),
    category_label ENUM('dance','interviews','programs') DEFAULT 'dance',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE ethics_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    section ENUM('traditions','moral_teachings','history') NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed categories
INSERT INTO categories (name, type) VALUES
('Cultural Events', 'gallery'),
('Traditional Dress', 'gallery'),
('Activities', 'gallery'),
('Cultural Dance', 'video'),
('Interviews', 'video'),
('Programs', 'video'),
('Traditions', 'ethics'),
('Moral Teachings', 'ethics'),
('History', 'ethics');

-- Seed gallery items (use placeholder image paths)
INSERT INTO gallery_items (category_id, title, description, image, category_label, is_featured) VALUES
(1, 'Annual Cultural Festival', 'Our community comes together every year to celebrate our rich heritage through music, dance, and food.', 'event1.jpg', 'events', 1),
(1, 'Harvest Celebration', 'The harvest festival marks the end of the farming season with traditional songs and feasting.', 'event2.jpg', 'events', 1),
(1, 'New Year Ceremony', 'Traditional new year celebrations featuring elders blessing the community.', 'event3.jpg', 'events', 0),
(2, 'Ceremonial Attire', 'The elaborate ceremonial dress worn during important cultural occasions.', 'dress1.jpg', 'traditional_dress', 1),
(2, 'Wedding Garments', 'Traditional wedding attire passed down through generations.', 'dress2.jpg', 'traditional_dress', 1),
(2, 'Festival Costumes', 'Colorful costumes worn during seasonal festivals.', 'dress3.jpg', 'traditional_dress', 0),
(3, 'Traditional Pottery', 'The art of pottery making has been practiced for centuries in our community.', 'activity1.jpg', 'activities', 1),
(3, 'Weaving Workshop', 'Community members gather to practice traditional weaving techniques.', 'activity2.jpg', 'activities', 1),
(3, 'Storytelling Circle', 'Elders share ancient stories with the younger generation around the fire.', 'activity3.jpg', 'activities', 0);

-- Seed videos
INSERT INTO videos (category_id, title, description, video_url, thumbnail, category_label) VALUES
(4, 'Traditional Welcome Dance', 'A beautiful performance of our traditional welcome dance performed at cultural gatherings.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'dance1.jpg', 'dance'),
(4, 'Harvest Dance Ritual', 'The sacred harvest dance performed to give thanks for a bountiful season.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'dance2.jpg', 'dance'),
(4, 'Youth Dance Competition', 'Young performers showcase their skills in traditional dance forms.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'dance3.jpg', 'dance'),
(5, 'Elder Interview: Cultural Preservation', 'An in-depth conversation with community elders about preserving our traditions.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'interview1.jpg', 'interviews'),
(5, 'Youth Voices: Our Heritage', 'Young community members share what their cultural heritage means to them.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'interview2.jpg', 'interviews'),
(6, 'Cultural Awareness Program', 'A community program designed to educate and promote cultural awareness.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'program1.jpg', 'programs'),
(6, 'Heritage Month Highlights', 'Highlights from our annual heritage month celebrations and activities.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'program2.jpg', 'programs');

-- Seed ethics content
INSERT INTO ethics_content (title, body, section, sort_order) VALUES
('Respect for Elders', 'In our culture, elders are the pillars of wisdom. Respecting and caring for our elders is not just a tradition but a sacred duty. Their guidance shapes the moral compass of our community, and their stories carry the weight of generations.', 'traditions', 1),
('Community Unity', 'The strength of our people lies in unity. We believe that a community that works together, celebrates together, and mourns together is one that endures through all challenges. Every member has a role and every voice matters.', 'traditions', 2),
('Sacred Ceremonies', 'Our ceremonies connect us to our ancestors and the spiritual world. Each ritual has been carefully preserved and passed down, carrying deep meaning and purpose that binds our community across time.', 'traditions', 3),
('Honesty and Integrity', 'Truth is the foundation of trust. Our ancestors taught that a person''s word is their bond. Honesty in all dealings — whether in trade, relationships, or governance — is a core value that defines our character.', 'moral_teachings', 1),
('Generosity and Sharing', 'We believe that abundance is meant to be shared. Generosity is not measured by wealth but by the willingness to give. Sharing food, knowledge, and time strengthens the bonds that hold our community together.', 'moral_teachings', 2),
('Stewardship of Nature', 'The land, rivers, and forests are not ours to own but to protect. Our ancestors lived in harmony with nature, taking only what was needed and giving back to the earth. This responsibility continues with each generation.', 'moral_teachings', 3),
('Origins of Our People', 'Our history stretches back thousands of years to the fertile valleys where our ancestors first settled. Through oral tradition and archaeological evidence, we trace our lineage to the earliest communities of this region.', 'history', 1),
('The Great Migration', 'Centuries ago, our people undertook a great migration driven by changing climates and the search for new opportunities. This journey shaped our identity and forged the resilience that defines us today.', 'history', 2),
('Modern Cultural Revival', 'In recent decades, there has been a powerful movement to revive and preserve our cultural practices. Community leaders, educators, and youth have come together to ensure our heritage thrives in the modern world.', 'history', 3);

-- Seed admin (password: admin123 — hashed with PHP password_hash)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$Sh7C.9X1NPzUDUUPtYLCoepqmy9ue.cfaySKJbzQdFoDomcZYx7Oa');
