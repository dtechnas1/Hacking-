-- Cultural Heritage Website Database Schema
-- Run this SQL to set up the database

CREATE DATABASE IF NOT EXISTS culture_website;
USE culture_website;

-- Categories table (shared across gallery, video, ethics)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('gallery', 'video', 'ethics') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Gallery items table
CREATE TABLE IF NOT EXISTS gallery_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(500) DEFAULT NULL,
    category_label ENUM('events', 'traditional_dress', 'activities') NOT NULL,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Videos table
CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    video_url VARCHAR(500) NOT NULL,
    thumbnail VARCHAR(500) DEFAULT NULL,
    category_label ENUM('dance', 'interviews', 'programs') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ethics content table
CREATE TABLE IF NOT EXISTS ethics_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    section ENUM('traditions', 'moral_teachings', 'history') NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================
-- Seed Data
-- =====================

-- Categories: 3 gallery, 3 video, 3 ethics
INSERT INTO categories (name, type, description) VALUES
('Cultural Events', 'gallery', 'Photos from cultural events and celebrations'),
('Traditional Dress', 'gallery', 'Traditional clothing and attire from our heritage'),
('Activities', 'gallery', 'Cultural activities and community gatherings'),
('Cultural Dance', 'video', 'Traditional dance performances and tutorials'),
('Interviews', 'video', 'Interviews with elders and cultural leaders'),
('Programs', 'video', 'Cultural programs and educational content'),
('Traditions', 'ethics', 'Our time-honored traditions and customs'),
('Moral Teachings', 'ethics', 'Ethical principles passed down through generations'),
('History', 'ethics', 'The history and origins of our cultural heritage');

-- Gallery items seed data
INSERT INTO gallery_items (category_id, title, description, image_path, category_label, is_featured) VALUES
(1, 'Annual Heritage Festival', 'A vibrant celebration of our cultural roots with music, food, and traditional performances.', 'uploads/gallery/heritage-festival.jpg', 'events', 1),
(1, 'Community Gathering 2024', 'Elders and youth coming together to share stories and celebrate our shared heritage.', 'uploads/gallery/community-gathering.jpg', 'events', 1),
(1, 'Cultural Night Ceremony', 'An evening dedicated to traditional ceremonies and rituals passed down through generations.', 'uploads/gallery/cultural-night.jpg', 'events', 0),
(2, 'Traditional Wedding Attire', 'Exquisite hand-woven garments worn during traditional wedding ceremonies.', 'uploads/gallery/wedding-attire.jpg', 'traditional_dress', 1),
(2, 'Ceremonial Robes', 'Elegant robes adorned with symbolic patterns representing our ancestral heritage.', 'uploads/gallery/ceremonial-robes.jpg', 'traditional_dress', 1),
(2, 'Festival Costumes', 'Colorful costumes worn during annual cultural festivals and parades.', 'uploads/gallery/festival-costumes.jpg', 'traditional_dress', 0),
(3, 'Traditional Cooking Workshop', 'Hands-on workshop teaching the art of preparing traditional dishes and recipes.', 'uploads/gallery/cooking-workshop.jpg', 'activities', 1),
(3, 'Storytelling Circle', 'Elders sharing ancient tales and folklore with the younger generation around a fire.', 'uploads/gallery/storytelling.jpg', 'activities', 1),
(3, 'Craft Making Session', 'Community members creating traditional crafts using techniques passed down for centuries.', 'uploads/gallery/craft-making.jpg', 'activities', 0);

-- Videos seed data
INSERT INTO videos (category_id, title, description, video_url, thumbnail, category_label) VALUES
(4, 'Traditional Harvest Dance', 'A beautiful performance of the harvest dance, performed during the autumn festival.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/videos/harvest-dance-thumb.jpg', 'dance'),
(4, 'Warriors Welcome Dance', 'The ceremonial dance performed to welcome returning warriors and honored guests.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/videos/warriors-dance-thumb.jpg', 'dance'),
(4, 'Moonlight Celebration Dance', 'A graceful dance performed under the moonlight during the new year celebration.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/videos/moonlight-dance-thumb.jpg', 'dance'),
(5, 'Elder Wisdom: Chief Amara', 'An insightful interview with Chief Amara about preserving cultural identity in modern times.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/videos/chief-amara-thumb.jpg', 'interviews'),
(5, 'Youth Voices: Cultural Identity', 'Young community members discuss what their cultural heritage means to them today.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/videos/youth-voices-thumb.jpg', 'interviews'),
(5, 'Master Weaver Interview', 'A conversation with a master weaver about the symbolism in traditional textile patterns.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/videos/weaver-interview-thumb.jpg', 'interviews'),
(6, 'Heritage Language Program', 'An educational program teaching the native language to children and adults.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/videos/language-program-thumb.jpg', 'programs'),
(6, 'Cultural Preservation Workshop', 'A workshop on documenting and preserving intangible cultural heritage for future generations.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/videos/preservation-workshop-thumb.jpg', 'programs'),
(6, 'Traditional Music Masterclass', 'Learn about traditional instruments and musical scales in this educational masterclass.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'uploads/videos/music-masterclass-thumb.jpg', 'programs');

-- Ethics content seed data
INSERT INTO ethics_content (title, body, section, sort_order) VALUES
('Respect for Elders', 'In our culture, elders are regarded as the pillars of wisdom and guardians of our collective memory. Respecting elders is not merely a social expectation but a deeply held value that shapes our community. Young people are taught to seek guidance from elders before making important decisions, and their counsel is valued in all matters of community governance.', 'traditions', 1),
('Communal Living', 'Our tradition of communal living emphasizes that no individual exists in isolation. The community shares in both joys and sorrows, and resources are distributed according to need. This tradition manifests in communal farming, shared childcare responsibilities, and collective decision-making processes that ensure every voice is heard.', 'traditions', 2),
('Seasonal Ceremonies', 'Throughout the year, our community observes ceremonies tied to the natural seasons. The planting ceremony in spring asks for blessings on the crops, the harvest festival in autumn gives thanks for abundance, and the winter gathering strengthens community bonds during the cold months. Each ceremony includes specific rituals, songs, and dances passed down through generations.', 'traditions', 3),
('Honesty and Integrity', 'Our moral framework places honesty at its foundation. A person''s word is considered a sacred bond, and breaking a promise brings not only personal shame but also dishonors the family. Children learn from an early age that truthfulness, even when difficult, is the mark of a person of character.', 'moral_teachings', 1),
('Generosity and Sharing', 'The teaching of generosity runs deep in our cultural values. It is believed that wealth and resources are gifts to be shared, not hoarded. Those who have more are expected to give freely to those in need, and this cycle of giving creates a safety net that supports the entire community.', 'moral_teachings', 2),
('Harmony with Nature', 'Our ancestors taught us to live in harmony with the natural world. We believe that humans are not masters of nature but participants in a greater ecosystem. This teaching guides our farming practices, our use of natural resources, and our spiritual connection to the land that sustains us.', 'moral_teachings', 3),
('Origins of Our People', 'Our people trace their origins to the fertile valleys of the great river, where our ancestors first settled thousands of years ago. Archaeological evidence and oral histories tell of a migration from the eastern highlands, led by the legendary founder Mwamba, who was guided by a vision to find a land of abundance and peace.', 'history', 1),
('The Great Kingdom Period', 'During the 15th to 18th centuries, our ancestors established a prosperous kingdom known for its sophisticated governance, advanced agriculture, and rich artistic traditions. The kingdom maintained diplomatic relations with neighboring peoples and was renowned for its system of justice and equitable resource distribution.', 'history', 2),
('Modern Cultural Revival', 'In the 20th and 21st centuries, a cultural revival movement has worked tirelessly to preserve and promote our heritage. Elders and scholars have documented oral histories, traditional practices, and indigenous knowledge. Cultural centers, festivals, and educational programs have been established to ensure that future generations remain connected to their roots.', 'history', 3);

-- Contact messages seed data
INSERT INTO contact_messages (name, email, subject, message, is_read) VALUES
('John Doe', 'john@example.com', 'Cultural Event Inquiry', 'Hello, I would like to know more about the upcoming cultural events and how I can participate. Please send me the schedule and any registration details. Thank you!', 0),
('Sarah Smith', 'sarah@example.com', 'Research Collaboration', 'I am a cultural anthropologist at the university and would love to collaborate on documenting traditional practices. Could we arrange a meeting to discuss possibilities?', 0),
('Michael Johnson', 'michael@example.com', 'Volunteer Opportunity', 'I am interested in volunteering at the next heritage festival. I have experience in event coordination and would love to help preserve cultural traditions. Please let me know how I can get involved.', 1);
