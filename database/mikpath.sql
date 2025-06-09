-- Database structure for MIKPath application
CREATE DATABASE mikpath_db;
USE mikpath_db;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Career roadmap table
CREATE TABLE career_roadmap (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level VARCHAR(50) NOT NULL,
    position VARCHAR(100) NOT NULL,
    competencies TEXT NOT NULL,
    certifications TEXT NOT NULL,
    description TEXT NOT NULL,
    salary_range VARCHAR(50),
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Alumni stories table
CREATE TABLE alumni_stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumni_name VARCHAR(100) NOT NULL,
    graduation_year YEAR NOT NULL,
    institution VARCHAR(150) NOT NULL,
    position VARCHAR(100) NOT NULL,
    story TEXT NOT NULL,
    job_opportunities TEXT,
    image VARCHAR(255) DEFAULT NULL,
    is_anonymous BOOLEAN DEFAULT TRUE,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table for career levels
CREATE TABLE career_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL,
    description TEXT,
    color_code VARCHAR(7) DEFAULT '#6366f1'
);

-- Insert sample data
INSERT INTO career_categories (category_name, description, color_code) VALUES
('Entry Level', 'Posisi untuk fresh graduate', '#10b981'),
('Junior', 'Posisi dengan pengalaman 1-3 tahun', '#3b82f6'),
('Mid Level', 'Posisi dengan pengalaman 3-5 tahun', '#f59e0b'),
('Senior', 'Posisi dengan pengalaman 5+ tahun', '#ef4444');

INSERT INTO career_roadmap (level, position, competencies, certifications, description, salary_range) VALUES
('Entry Level', 'Staf Rekam Medis', 'ICD-10, Excel, SIMRS Dasar, Komunikasi', 'Sertifikasi Dasar MIK', 'Cocok untuk fresh graduate yang ingin memulai karier di bidang informasi kesehatan', 'Rp 3-5 juta'),
('Junior', 'Health Information Analyst', 'Dashboard SIMRS, CHIA, Analisis Data Kesehatan', 'Pelatihan CHIA, Sertifikat Analisis Data', 'Posisi yang banyak dibutuhkan di RS dan Puskesmas', 'Rp 5-8 juta'),
('Mid Level', 'Koordinator Rekam Medis', 'Klaim BPJS, Supervisi Tim, Manajemen Kualitas Data', 'STR MIK, Sertifikat Manajemen', 'Membutuhkan pengalaman 3-5 tahun di bidang rekam medis', 'Rp 8-12 juta'),
('Senior', 'Data Manager / Konsultan SIMRS', 'Interoperabilitas, ISO 27001, Project Management', 'Pelatihan SATUSEHAT, PMP', 'Posisi strategis dengan tanggung jawab besar', 'Rp 12-20 juta');

INSERT INTO alumni_stories (alumni_name, graduation_year, institution, position, story, job_opportunities) VALUES
('Dina R.', 2020, 'RS Sardjito Yogyakarta', 'Data Analyst', 'Awalnya sempat ragu dengan prospek karier MIK, tapi setelah bekerja di RS Sardjito sebagai data analyst, saya sangat menikmati pekerjaan ini. Setiap hari belajar hal baru tentang analisis data kesehatan.', 'Data Analyst - Yogyakarta'),
('Raka T.', 2019, 'DTO Kemenkes RI', 'Health Data Officer', 'Beruntung bisa langsung diterima kerja setelah magang di DTO Kemenkes. Pengalaman magang sangat membantu dalam memahami sistem informasi kesehatan nasional.', 'Health Data Officer - Jakarta'),
('Ayu P.', 2021, 'RSIA Mitra Bunda', 'Koordinator Rekam Medis', 'PKL di rumah sakit menjadi modal utama untuk memahami alur kerja rekam medis. Sekarang sebagai koordinator, saya bertanggung jawab mengawasi kualitas data pasien.', 'Koordinator RM - Bandung'),
('Fajar L.', 2018, 'Freelance Consultant', 'Konsultan SIM Klinik', 'Memilih jadi freelance consultant ternyata sangat menarik. Saya membantu banyak klinik di daerah untuk implementasi sistem informasi manajemen.', 'Konsultan SIM - Remote');

-- Insert admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@mikpath.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
