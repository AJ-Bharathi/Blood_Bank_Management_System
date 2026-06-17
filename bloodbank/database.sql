CREATE DATABASE IF NOT EXISTS bloodbank_db;
USE bloodbank_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'donor', 'patient') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    blood_group VARCHAR(5),
    phone VARCHAR(15),
    location VARCHAR(100),
    last_donated DATE,
    is_available TINYINT(1) DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    blood_group VARCHAR(5),
    phone VARCHAR(15),
    location VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE blood_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blood_group VARCHAR(5) UNIQUE,
    units_available INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    blood_group VARCHAR(5),
    units_needed INT,
    location VARCHAR(100),
    status ENUM('pending', 'fulfilled', 'cancelled') DEFAULT 'pending',
    donor_committed TINYINT(1) DEFAULT 0,
    committed_donor_name VARCHAR(100) DEFAULT NULL,
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id)
);

CREATE TABLE donor_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT,
    donor_id INT,
    response ENUM('accepted', 'declined') DEFAULT 'accepted',
    responded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES blood_requests(id),
    FOREIGN KEY (donor_id) REFERENCES donors(id)
);

INSERT INTO blood_stock (blood_group, units_available) VALUES
('A+', 10), ('A-', 5), ('B+', 8), ('B-', 3),
('AB+', 6), ('AB-', 2), ('O+', 15), ('O-', 7);

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@bloodbank.com', MD5('admin123'), 'admin');

-- Users table

-- 40 Donors
INSERT INTO users (name, email, password, role) VALUES
('Arjun Sharma', 'arjun.sharma@gmail.com', MD5('donor123'), 'donor'),
('Preethi Nair', 'preethi.nair@gmail.com', MD5('donor123'), 'donor'),
('Karthik Rajan', 'karthik.rajan@gmail.com', MD5('donor123'), 'donor'),
('Divya Menon', 'divya.menon@gmail.com', MD5('donor123'), 'donor'),
('Rahul Verma', 'rahul.verma@gmail.com', MD5('donor123'), 'donor'),
('Sneha Pillai', 'sneha.pillai@gmail.com', MD5('donor123'), 'donor'),
('Vijay Kumar', 'vijay.kumar@gmail.com', MD5('donor123'), 'donor'),
('Anitha Krishnan', 'anitha.krishnan@gmail.com', MD5('donor123'), 'donor'),
('Manoj Patel', 'manoj.patel@gmail.com', MD5('donor123'), 'donor'),
('Lakshmi Suresh', 'lakshmi.suresh@gmail.com', MD5('donor123'), 'donor'),
('Suresh Babu', 'suresh.babu@gmail.com', MD5('donor123'), 'donor'),
('Kavitha Raj', 'kavitha.raj@gmail.com', MD5('donor123'), 'donor'),
('Arun Prakash', 'arun.prakash@gmail.com', MD5('donor123'), 'donor'),
('Meena Devi', 'meena.devi@gmail.com', MD5('donor123'), 'donor'),
('Naveen Reddy', 'naveen.reddy@gmail.com', MD5('donor123'), 'donor'),
('Pooja Iyer', 'pooja.iyer@gmail.com', MD5('donor123'), 'donor'),
('Deepak Nair', 'deepak.nair@gmail.com', MD5('donor123'), 'donor'),
('Saranya Kumar', 'saranya.kumar@gmail.com', MD5('donor123'), 'donor'),
('Ramesh Chandran', 'ramesh.chandran@gmail.com', MD5('donor123'), 'donor'),
('Nithya Priya', 'nithya.priya@gmail.com', MD5('donor123'), 'donor'),
('Ganesh Murthy', 'ganesh.murthy@gmail.com', MD5('donor123'), 'donor'),
('Lavanya Raj', 'lavanya.raj@gmail.com', MD5('donor123'), 'donor'),
('Sathish Kumar', 'sathish.kumar@gmail.com', MD5('donor123'), 'donor'),
('Rekha Nair', 'rekha.nair@gmail.com', MD5('donor123'), 'donor'),
('Prashanth Reddy', 'prashanth.reddy@gmail.com', MD5('donor123'), 'donor'),
('Uma Shankar', 'uma.shankar@gmail.com', MD5('donor123'), 'donor'),
('Bala Krishnan', 'bala.krishnan@gmail.com', MD5('donor123'), 'donor'),
('Revathi Mohan', 'revathi.mohan@gmail.com', MD5('donor123'), 'donor'),
('Dinesh Babu', 'dinesh.babu@gmail.com', MD5('donor123'), 'donor'),
('Padma Priya', 'padma.priya@gmail.com', MD5('donor123'), 'donor'),
('Venkatesh Rao', 'venkatesh.rao@gmail.com', MD5('donor123'), 'donor'),
('Geetha Lakshmi', 'geetha.lakshmi@gmail.com', MD5('donor123'), 'donor'),
('Muthu Raman', 'muthu.raman@gmail.com', MD5('donor123'), 'donor'),
('Sangeetha Devi', 'sangeetha.devi@gmail.com', MD5('donor123'), 'donor'),
('Prakash Raj', 'prakash.raj@gmail.com', MD5('donor123'), 'donor'),
('Indira Devi', 'indira.devi@gmail.com', MD5('donor123'), 'donor'),
('Selvam Kumar', 'selvam.kumar@gmail.com', MD5('donor123'), 'donor'),
('Chitra Balan', 'chitra.balan@gmail.com', MD5('donor123'), 'donor'),
('Muthukumar R', 'muthukumar.r@gmail.com', MD5('donor123'), 'donor'),
('Vimala Devi', 'vimala.devi@gmail.com', MD5('donor123'), 'donor');

-- 17 Patients
INSERT INTO users (name, email, password, role) VALUES
('Ravi Shankar', 'ravi.shankar@gmail.com', MD5('patient123'), 'patient'),
('Sudha Rajan', 'sudha.rajan@gmail.com', MD5('patient123'), 'patient'),
('Harish Babu', 'harish.babu@gmail.com', MD5('patient123'), 'patient'),
('Malathi Devi', 'malathi.devi@gmail.com', MD5('patient123'), 'patient'),
('Rajesh Kumar', 'rajesh.kumar@gmail.com', MD5('patient123'), 'patient'),
('Nirmala Rani', 'nirmala.rani@gmail.com', MD5('patient123'), 'patient'),
('Sunil Verma', 'sunil.verma@gmail.com', MD5('patient123'), 'patient'),
('Kamala Devi', 'kamala.devi@gmail.com', MD5('patient123'), 'patient'),
('Aravind Raj', 'aravind.raj@gmail.com', MD5('patient123'), 'patient'),
('Savitha Nair', 'savitha.nair@gmail.com', MD5('patient123'), 'patient'),
('Murugan S', 'murugan.s@gmail.com', MD5('patient123'), 'patient'),
('Ambika Devi', 'ambika.devi@gmail.com', MD5('patient123'), 'patient'),
('Senthil Kumar', 'senthil.kumar@gmail.com', MD5('patient123'), 'patient'),
('Vasantha Devi', 'vasantha.devi@gmail.com', MD5('patient123'), 'patient'),
('Gopal Krishna', 'gopal.krishna@gmail.com', MD5('patient123'), 'patient'),
('Sumathi Raj', 'sumathi.raj@gmail.com', MD5('patient123'), 'patient'),
('Anand Babu', 'anand.babu@gmail.com', MD5('patient123'), 'patient');

-- Donors Table
INSERT INTO donors (user_id, blood_group, phone, location, is_available) VALUES
((SELECT id FROM users WHERE email='arjun.sharma@gmail.com'), 'A+', '9876501001', 'Chennai', 1),
((SELECT id FROM users WHERE email='preethi.nair@gmail.com'), 'B+', '9876501002', 'Chennai', 1),
((SELECT id FROM users WHERE email='karthik.rajan@gmail.com'), 'O+', '9876501003', 'Bangalore', 1),
((SELECT id FROM users WHERE email='divya.menon@gmail.com'), 'AB+', '9876501004', 'Chennai', 1),
((SELECT id FROM users WHERE email='rahul.verma@gmail.com'), 'A-', '9876501005', 'Mumbai', 1),
((SELECT id FROM users WHERE email='sneha.pillai@gmail.com'), 'B-', '9876501006', 'Chennai', 1),
((SELECT id FROM users WHERE email='vijay.kumar@gmail.com'), 'O-', '9876501007', 'Hyderabad', 1),
((SELECT id FROM users WHERE email='anitha.krishnan@gmail.com'), 'A+', '9876501008', 'Chennai', 1),
((SELECT id FROM users WHERE email='manoj.patel@gmail.com'), 'B+', '9876501009', 'Mumbai', 1),
((SELECT id FROM users WHERE email='lakshmi.suresh@gmail.com'), 'O+', '9876501010', 'Chennai', 1),
((SELECT id FROM users WHERE email='suresh.babu@gmail.com'), 'AB-', '9876501011', 'Bangalore', 1),
((SELECT id FROM users WHERE email='kavitha.raj@gmail.com'), 'A+', '9876501012', 'Chennai', 1),
((SELECT id FROM users WHERE email='arun.prakash@gmail.com'), 'B+', '9876501013', 'Chennai', 0),
((SELECT id FROM users WHERE email='meena.devi@gmail.com'), 'O+', '9876501014', 'Hyderabad', 1),
((SELECT id FROM users WHERE email='naveen.reddy@gmail.com'), 'A-', '9876501015', 'Bangalore', 1),
((SELECT id FROM users WHERE email='pooja.iyer@gmail.com'), 'AB+', '9876501016', 'Chennai', 1),
((SELECT id FROM users WHERE email='deepak.nair@gmail.com'), 'B-', '9876501017', 'Mumbai', 0),
((SELECT id FROM users WHERE email='saranya.kumar@gmail.com'), 'O-', '9876501018', 'Chennai', 1),
((SELECT id FROM users WHERE email='ramesh.chandran@gmail.com'), 'A+', '9876501019', 'Chennai', 1),
((SELECT id FROM users WHERE email='nithya.priya@gmail.com'), 'B+', '9876501020', 'Bangalore', 1),
((SELECT id FROM users WHERE email='ganesh.murthy@gmail.com'), 'O+', '9876501021', 'Chennai', 1),
((SELECT id FROM users WHERE email='lavanya.raj@gmail.com'), 'AB+', '9876501022', 'Mumbai', 1),
((SELECT id FROM users WHERE email='sathish.kumar@gmail.com'), 'A-', '9876501023', 'Chennai', 0),
((SELECT id FROM users WHERE email='rekha.nair@gmail.com'), 'B+', '9876501024', 'Chennai', 1),
((SELECT id FROM users WHERE email='prashanth.reddy@gmail.com'), 'O+', '9876501025', 'Hyderabad', 1),
((SELECT id FROM users WHERE email='uma.shankar@gmail.com'), 'A+', '9876501026', 'Chennai', 1),
((SELECT id FROM users WHERE email='bala.krishnan@gmail.com'), 'B-', '9876501027', 'Bangalore', 1),
((SELECT id FROM users WHERE email='revathi.mohan@gmail.com'), 'O-', '9876501028', 'Chennai', 1),
((SELECT id FROM users WHERE email='dinesh.babu@gmail.com'), 'AB+', '9876501029', 'Mumbai', 1),
((SELECT id FROM users WHERE email='padma.priya@gmail.com'), 'A+', '9876501030', 'Chennai', 1),
((SELECT id FROM users WHERE email='venkatesh.rao@gmail.com'), 'B+', '9876501031', 'Hyderabad', 0),
((SELECT id FROM users WHERE email='geetha.lakshmi@gmail.com'), 'O+', '9876501032', 'Chennai', 1),
((SELECT id FROM users WHERE email='muthu.raman@gmail.com'), 'A-', '9876501033', 'Bangalore', 1),
((SELECT id FROM users WHERE email='sangeetha.devi@gmail.com'), 'AB-', '9876501034', 'Chennai', 1),
((SELECT id FROM users WHERE email='prakash.raj@gmail.com'), 'B+', '9876501035', 'Chennai', 1),
((SELECT id FROM users WHERE email='indira.devi@gmail.com'), 'O+', '9876501036', 'Mumbai', 1),
((SELECT id FROM users WHERE email='selvam.kumar@gmail.com'), 'A+', '9876501037', 'Chennai', 0),
((SELECT id FROM users WHERE email='chitra.balan@gmail.com'), 'B-', '9876501038', 'Bangalore', 1),
((SELECT id FROM users WHERE email='muthukumar.r@gmail.com'), 'O-', '9876501039', 'Chennai', 1),
((SELECT id FROM users WHERE email='vimala.devi@gmail.com'), 'AB+', '9876501040', 'Hyderabad', 1);

-- Patient Table

INSERT INTO patients (user_id, blood_group, phone, location) VALUES
((SELECT id FROM users WHERE email='ravi.shankar@gmail.com'), 'A+', '9876502001', 'Chennai'),
((SELECT id FROM users WHERE email='sudha.rajan@gmail.com'), 'B+', '9876502002', 'Bangalore'),
((SELECT id FROM users WHERE email='harish.babu@gmail.com'), 'O+', '9876502003', 'Chennai'),
((SELECT id FROM users WHERE email='malathi.devi@gmail.com'), 'AB+', '9876502004', 'Mumbai'),
((SELECT id FROM users WHERE email='rajesh.kumar@gmail.com'), 'A-', '9876502005', 'Chennai'),
((SELECT id FROM users WHERE email='nirmala.rani@gmail.com'), 'B-', '9876502006', 'Hyderabad'),
((SELECT id FROM users WHERE email='sunil.verma@gmail.com'), 'O-', '9876502007', 'Bangalore'),
((SELECT id FROM users WHERE email='kamala.devi@gmail.com'), 'A+', '9876502008', 'Chennai'),
((SELECT id FROM users WHERE email='aravind.raj@gmail.com'), 'B+', '9876502009', 'Chennai'),
((SELECT id FROM users WHERE email='savitha.nair@gmail.com'), 'O+', '9876502010', 'Mumbai'),
((SELECT id FROM users WHERE email='murugan.s@gmail.com'), 'AB-', '9876502011', 'Chennai'),
((SELECT id FROM users WHERE email='ambika.devi@gmail.com'), 'A+', '9876502012', 'Bangalore'),
((SELECT id FROM users WHERE email='senthil.kumar@gmail.com'), 'B+', '9876502013', 'Chennai'),
((SELECT id FROM users WHERE email='vasantha.devi@gmail.com'), 'O+', '9876502014', 'Hyderabad'),
((SELECT id FROM users WHERE email='gopal.krishna@gmail.com'), 'A-', '9876502015', 'Chennai'),
((SELECT id FROM users WHERE email='sumathi.raj@gmail.com'), 'AB+', '9876502016', 'Bangalore'),
((SELECT id FROM users WHERE email='anand.babu@gmail.com'), 'O-', '9876502017', 'Chennai');
