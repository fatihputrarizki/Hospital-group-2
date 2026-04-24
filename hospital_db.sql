-- ============================================================
-- DATABASE: hospital_db
-- RS Medika Nusantara - Hospital Management System
-- ============================================================

CREATE DATABASE IF NOT EXISTS hospital_db;
USE hospital_db;

-- ============================================================
-- TABLE: doctors
-- ============================================================
CREATE TABLE IF NOT EXISTS doctors (
    doctor_id   INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    phone       VARCHAR(20),
    email       VARCHAR(100),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABLE: patients
-- ============================================================
CREATE TABLE IF NOT EXISTS patients (
    patient_id  INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    gender      ENUM('Male','Female') NOT NULL,
    birth_date  DATE NOT NULL,
    address     TEXT,
    phone       VARCHAR(20),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABLE: appointments
-- ============================================================
CREATE TABLE IF NOT EXISTS appointments (
    appointment_id  INT AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT NOT NULL,
    doctor_id       INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    status          ENUM('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
    notes           TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id)  REFERENCES doctors(doctor_id)  ON DELETE CASCADE
);

-- ============================================================
-- TABLE: medical_records
-- ============================================================
CREATE TABLE IF NOT EXISTS medical_records (
    record_id   INT AUTO_INCREMENT PRIMARY KEY,
    patient_id  INT NOT NULL,
    doctor_id   INT NOT NULL,
    diagnosis   VARCHAR(255) NOT NULL,
    treatment   TEXT,
    record_date DATE NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id)  REFERENCES doctors(doctor_id)  ON DELETE CASCADE
);

-- ============================================================
-- TABLE: medications
-- ============================================================
CREATE TABLE IF NOT EXISTS medications (
    med_id      INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    category    VARCHAR(100),
    price       DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock       INT NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABLE: prescriptions
-- ============================================================
CREATE TABLE IF NOT EXISTS prescriptions (
    prescription_id INT AUTO_INCREMENT PRIMARY KEY,
    record_id       INT NOT NULL,
    med_id          INT NOT NULL,
    dosage          VARCHAR(100) NOT NULL,
    duration_days   INT DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (record_id) REFERENCES medical_records(record_id) ON DELETE CASCADE,
    FOREIGN KEY (med_id)    REFERENCES medications(med_id)        ON DELETE CASCADE
);

-- ============================================================
-- SAMPLE DATA
-- ============================================================
INSERT INTO doctors (name, specialization, phone, email) VALUES
('Dr. Budi Santoso',   'Umum',         '081234567890', 'budi@rsmedikagroup.com'),
('Dr. Sari Dewi',      'Kardiologi',   '081234567891', 'sari@rsmedikagroup.com'),
('Dr. Ahmad Fauzi',    'Neurologi',    '081234567892', 'ahmad@rsmedikagroup.com'),
('Dr. Rina Wulandari', 'Pediatri',     '081234567893', 'rina@rsmedikagroup.com');

INSERT INTO patients (name, gender, birth_date, address, phone) VALUES
('Andi Prasetyo',   'Male',   '1990-05-12', 'Jl. Merdeka No.1, Jakarta',   '082111111111'),
('Dewi Lestari',    'Female', '1985-08-20', 'Jl. Sudirman No.5, Bandung',  '082222222222'),
('Eko Kurniawan',   'Male',   '2000-01-30', 'Jl. Gatot Subroto No.7, Surabaya', '082333333333'),
('Fitri Handayani', 'Female', '1978-11-15', 'Jl. Imam Bonjol No.2, Semarang',   '082444444444');

INSERT INTO medications (name, category, price, stock) VALUES
('Paracetamol 500mg', 'Analgesik',     5000,  200),
('Amoxicillin 500mg', 'Antibiotik',   12000,  150),
('Omeprazole 20mg',   'Antasida',      8000,  100),
('Cetirizine 10mg',   'Antihistamin',  6500,   80),
('Metformin 500mg',   'Antidiabetes', 15000,  120);

INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, notes) VALUES
(1, 1, '2025-04-25 09:00:00', 'Scheduled', 'Kontrol rutin'),
(2, 2, '2025-04-25 10:00:00', 'Scheduled', 'Keluhan dada sesak'),
(3, 3, '2025-04-26 08:00:00', 'Scheduled', 'Sakit kepala berulang'),
(4, 4, '2025-04-26 11:00:00', 'Completed', 'Pemeriksaan anak');

INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, record_date) VALUES
(1, 1, 'Infeksi Saluran Pernapasan', 'Antibiotik dan istirahat', '2025-04-20'),
(2, 2, 'Hipertensi Ringan',         'Modifikasi gaya hidup',    '2025-04-21'),
(4, 4, 'Demam Anak',                'Antipiretik',              '2025-04-26');

INSERT INTO prescriptions (record_id, med_id, dosage, duration_days) VALUES
(1, 2, '3x1 sehari sesudah makan', 7),
(1, 1, '3x1 sehari',               5),
(2, 1, '2x1 sehari',               14),
(3, 4, '1x1 sehari malam',         10);
