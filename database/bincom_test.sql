CREATE TABLE IF NOT EXISTS state (
  state_id INT PRIMARY KEY AUTO_INCREMENT,
  state_name VARCHAR(255) NOT NULL,
  state_code VARCHAR(10) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS lga (
  uniqueid INT PRIMARY KEY AUTO_INCREMENT,
  lga_id INT NOT NULL,
  lga_name VARCHAR(255) NOT NULL,
  state_id INT NOT NULL,
  lga_description VARCHAR(255) DEFAULT NULL,
  entered_by_user VARCHAR(255) DEFAULT NULL,
  date_entered DATETIME DEFAULT NULL,
  user_ip_address VARCHAR(50) DEFAULT NULL,
  FOREIGN KEY (state_id) REFERENCES state(state_id)
);

CREATE TABLE IF NOT EXISTS ward (
  uniqueid INT PRIMARY KEY AUTO_INCREMENT,
  ward_id INT NOT NULL,
  ward_name VARCHAR(255) NOT NULL,
  lga_id INT NOT NULL,
  ward_description VARCHAR(255) DEFAULT NULL,
  entered_by_user VARCHAR(255) DEFAULT NULL,
  date_entered DATETIME DEFAULT NULL,
  user_ip_address VARCHAR(50) DEFAULT NULL,
  FOREIGN KEY (lga_id) REFERENCES lga(uniqueid)
);

CREATE TABLE IF NOT EXISTS polling_unit (
  uniqueid INT PRIMARY KEY AUTO_INCREMENT,
  polling_unit_id INT NOT NULL,
  ward_id INT NOT NULL,
  lga_id INT NOT NULL,
  state_id INT NOT NULL,
  polling_unit_number VARCHAR(50) DEFAULT NULL,
  polling_unit_name VARCHAR(255) DEFAULT NULL,
  polling_unit_description TEXT DEFAULT NULL,
  lat VARCHAR(255) DEFAULT NULL,
  `long` VARCHAR(255) DEFAULT NULL,
  entered_by_user VARCHAR(255) DEFAULT NULL,
  date_entered DATETIME DEFAULT NULL,
  user_ip_address VARCHAR(50) DEFAULT NULL,
  FOREIGN KEY (ward_id) REFERENCES ward(uniqueid),
  FOREIGN KEY (lga_id) REFERENCES lga(uniqueid),
  FOREIGN KEY (state_id) REFERENCES state(state_id)
);

CREATE TABLE IF NOT EXISTS party (
  uniqueid INT PRIMARY KEY AUTO_INCREMENT,
  partyid VARCHAR(100) NOT NULL,
  party_name VARCHAR(255) NOT NULL
);

-- Results at the polling-unit level (one row per party per PU)
CREATE TABLE IF NOT EXISTS announced_pu_results (
  result_id INT PRIMARY KEY AUTO_INCREMENT,
  polling_unit_uniqueid INT NOT NULL,
  party_abbreviation VARCHAR(50) NOT NULL,
  party_score INT NOT NULL,
  entered_by_user VARCHAR(255) DEFAULT NULL,
  date_entered DATETIME DEFAULT NULL,
  user_ip_address VARCHAR(50) DEFAULT NULL,
  FOREIGN KEY (polling_unit_uniqueid) REFERENCES polling_unit(uniqueid)
);

-- Pre-aggregated tables — we DON'T use these for calculations
-- (requirement says to compute manually via joins)
CREATE TABLE IF NOT EXISTS announced_lga_results (
  result_id INT PRIMARY KEY AUTO_INCREMENT,
  lga_name VARCHAR(255) NOT NULL,
  party_abbreviation VARCHAR(50) NOT NULL,
  party_score INT NOT NULL,
  entered_by_user VARCHAR(255) DEFAULT NULL,
  date_entered DATETIME DEFAULT NULL,
  user_ip_address VARCHAR(50) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS announced_state_results (
  result_id INT PRIMARY KEY AUTO_INCREMENT,
  state_name VARCHAR(255) NOT NULL,
  party_abbreviation VARCHAR(50) NOT NULL,
  party_score INT NOT NULL,
  entered_by_user VARCHAR(255) DEFAULT NULL,
  date_entered DATETIME DEFAULT NULL,
  user_ip_address VARCHAR(50) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS announced_ward_results (
  result_id INT PRIMARY KEY AUTO_INCREMENT,
  ward_name VARCHAR(255) NOT NULL,
  party_abbreviation VARCHAR(50) NOT NULL,
  party_score INT NOT NULL,
  entered_by_user VARCHAR(255) DEFAULT NULL,
  date_entered DATETIME DEFAULT NULL,
  user_ip_address VARCHAR(50) DEFAULT NULL
);

-- ── Sample data ──

INSERT INTO state (state_id, state_name, state_code) VALUES
(1, 'Oyo', 'OY'),
(2, 'Lagos', 'LA'),
(3, 'Abuja', 'FC');

INSERT INTO lga (uniqueid, lga_id, lga_name, state_id, lga_description, date_entered) VALUES
(1, 1, 'Ibadan North', 1, 'Ibadan North Local Government', NOW()),
(2, 2, 'Ibadan South', 1, 'Ibadan South Local Government', NOW()),
(3, 3, 'Ibadan East', 1, 'Ibadan East Local Government', NOW()),
(4, 4, 'Surulere', 2, 'Surulere Local Government', NOW()),
(5, 5, 'Amuwo-Odofin', 2, 'Amuwo-Odofin Local Government', NOW());

INSERT INTO ward (uniqueid, ward_id, ward_name, lga_id, ward_description, date_entered) VALUES
(1, 1, 'Agodi', 1, 'Agodi Ward', NOW()),
(2, 2, 'Bodija', 1, 'Bodija Ward', NOW()),
(3, 3, 'Mokola', 1, 'Mokola Ward', NOW()),
(4, 4, 'Challenge', 2, 'Challenge Ward', NOW()),
(5, 5, 'Oluyole', 2, 'Oluyole Ward', NOW()),
(6, 6, 'Dugbe', 3, 'Dugbe Ward', NOW());

INSERT INTO polling_unit (uniqueid, polling_unit_id, ward_id, lga_id, state_id, polling_unit_number, polling_unit_name, date_entered) VALUES
(1, 1, 1, 1, 1, 'PU001', 'Agodi Primary School', NOW()),
(2, 2, 1, 1, 1, 'PU002', 'Agodi Market Square', NOW()),
(3, 3, 2, 1, 1, 'PU003', 'Bodija Estate Hall', NOW()),
(4, 4, 3, 1, 1, 'PU004', 'Mokola Community Centre', NOW()),
(5, 5, 4, 2, 1, 'PU005', 'Challenge Town Hall', NOW()),
(6, 6, 5, 2, 1, 'PU006', 'Oluyole Pavilion', NOW()),
(7, 7, 6, 3, 1, 'PU007', 'Dugbe Market', NOW());

INSERT INTO party (uniqueid, partyid, party_name) VALUES
(1, 'PDP', 'Peoples Democratic Party'),
(2, 'DPP', 'Democratic Peoples Party'),
(3, 'ACN', 'Action Congress of Nigeria'),
(4, 'PPA', 'Peoples Party of Africa'),
(5, 'CDC', 'CDC'),
(6, 'JP', 'JP'),
(7, 'ANPP', 'All Nigeria Peoples Party'),
(8, 'LABOUR', 'Labour Party'),
(9, 'CPC', 'Congress for Progressive Change'),
(10, 'APC', 'All Progressives Congress');

INSERT INTO announced_pu_results (result_id, polling_unit_uniqueid, party_abbreviation, party_score, date_entered) VALUES
(1, 1, 'PDP', 802, NOW()),
(2, 1, 'DPP', 719, NOW()),
(3, 1, 'ACN', 416, NOW()),
(4, 1, 'PPA', 939, NOW()),
(5, 1, 'CDC', 302, NOW()),
(6, 1, 'JP', 150, NOW()),
(7, 1, 'ANPP', 210, NOW()),
(8, 1, 'LABOUR', 95, NOW()),
(9, 2, 'PDP', 650, NOW()),
(10, 2, 'DPP', 500, NOW()),
(11, 2, 'ACN', 380, NOW()),
(12, 2, 'PPA', 720, NOW()),
(13, 2, 'CDC', 250, NOW()),
(14, 2, 'JP', 180, NOW()),
(15, 2, 'ANPP', 300, NOW()),
(16, 2, 'LABOUR', 120, NOW()),
(17, 3, 'PDP', 910, NOW()),
(18, 3, 'DPP', 430, NOW()),
(19, 3, 'ACN', 560, NOW()),
(20, 3, 'PPA', 380, NOW()),
(21, 3, 'CDC', 410, NOW()),
(22, 3, 'JP', 200, NOW()),
(23, 3, 'ANPP', 150, NOW()),
(24, 3, 'LABOUR', 75, NOW()),
(25, 4, 'PDP', 345, NOW()),
(26, 4, 'DPP', 678, NOW()),
(27, 4, 'ACN', 890, NOW()),
(28, 4, 'PPA', 234, NOW()),
(29, 4, 'CDC', 567, NOW()),
(30, 4, 'JP', 123, NOW()),
(31, 4, 'ANPP', 456, NOW()),
(32, 4, 'LABOUR', 789, NOW()),
(33, 5, 'PDP', 555, NOW()),
(34, 5, 'DPP', 666, NOW()),
(35, 5, 'ACN', 777, NOW()),
(36, 5, 'PPA', 888, NOW()),
(37, 5, 'CDC', 111, NOW()),
(38, 5, 'JP', 222, NOW()),
(39, 5, 'ANPP', 333, NOW()),
(40, 5, 'LABOUR', 444, NOW()),
(41, 6, 'PDP', 100, NOW()),
(42, 6, 'DPP', 200, NOW()),
(43, 6, 'ACN', 300, NOW()),
(44, 6, 'PPA', 400, NOW()),
(45, 6, 'CDC', 500, NOW()),
(46, 6, 'JP', 600, NOW()),
(47, 6, 'ANPP', 700, NOW()),
(48, 6, 'LABOUR', 800, NOW()),
(49, 7, 'PDP', 999, NOW()),
(50, 7, 'DPP', 888, NOW()),
(51, 7, 'ACN', 777, NOW()),
(52, 7, 'PPA', 666, NOW()),
(53, 7, 'CDC', 555, NOW()),
(54, 7, 'JP', 444, NOW()),
(55, 7, 'ANPP', 333, NOW()),
(56, 7, 'LABOUR', 222, NOW());
