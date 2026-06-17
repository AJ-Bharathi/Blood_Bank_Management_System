# 🩸 Blood Bank Management System

A web-based **Blood Bank Management System** developed as a mini project for the **Department of Information Technology**. This application serves as a centralized platform that connects blood donors with patients in need while enabling administrators to efficiently manage blood inventory and requests.

---

## 📌 Project Overview

Ensuring the timely availability of blood is one of the major challenges in healthcare. This project provides a digital solution by allowing donors to register their availability, patients to request blood, and administrators to supervise the entire process, including stock management and request fulfillment.

The system also supports automated email notifications. Whenever a patient raises a blood request, matching donors are notified via email. If a donor agrees to donate, the patient receives an email confirmation.

---

## ✨ Key Features

* Secure role-based authentication for **Admin**, **Donor**, and **Patient**
* Donor registration with blood group, location, and availability status
* Blood request submission with real-time stock verification
* Automatic email notifications to compatible donors upon new requests
* Donor response mechanism with email notifications to patients
* Administrative dashboard for monitoring donors, requests, and blood inventory
* Blood stock management with unit tracking for each blood group
* Visibility of committed donors before request fulfillment
* Blood group compatibility reference chart
* Blood donation guidelines (Do's and Don'ts)

---

## 🛠️ Technology Stack

| Component     | Technology                |
| ------------- | ------------------------- |
| Frontend      | HTML, CSS                 |
| Backend       | PHP                       |
| Database      | MySQL                     |
| Email Service | PHPMailer with Gmail SMTP |
| Web Server    | Apache (XAMPP)            |

---

## 🗄️ Database Schema

| Table             | Purpose                                   |
| ----------------- | ----------------------------------------- |
| `users`           | Stores user credentials and roles         |
| `donors`          | Contains donor-specific information       |
| `patients`        | Contains patient-specific information     |
| `blood_stock`     | Maintains available blood units by group  |
| `blood_requests`  | Records patient requests and their status |
| `donor_responses` | Tracks donor responses to requests        |

---

## 📂 Project Structure

```
bloodbank/
│
├── css/
│   └── style.css
│
├── PHPMailer/
│   ├── Exception.php
│   ├── PHPMailer.php
│   └── SMTP.php
│
├── config.php
├── index.php
├── register.php
├── logout.php
├── admin_dashboard.php
├── database.sql
├── donor_dashboard.php
├── patient_dashboard.php
├── donor_respond.php
├── fulfill_request.php
├── update_stock.php
├── send_email.php
└── logo.png
```

---

## 🚀 Getting Started

### Prerequisites

* XAMPP (Apache and MySQL)
* PHP
* MySQL Database
* PHPMailer

### Installation Steps

1. Install **XAMPP** and start **Apache** and **MySQL** services.
2. Clone the repository into:

```text
C:\xampp\htdocs\bloodbank
```

3. Create a database named:

```sql
bloodbank_db
```

4. Import the SQL queries from `database.sql`.
5. Configure your Gmail address and App Password in `send_email.php`.
6. Open your browser and visit:

```text
http://localhost/bloodbank
```

---

## 🔐 Default Admin Credentials

| Email                                             | Password |
| ------------------------------------------------- | -------- |
| [admin@bloodbank.com](mailto:admin@bloodbank.com) | admin123 |

---

## ⚙️ System Workflow

1. Users register as either **Donors** or **Patients**.
2. Patients submit blood requests through the system.
3. The system verifies blood stock availability.
4. Matching donors from the same location receive email notifications.
5. Interested donors respond by selecting **"I'll Donate"**.
6. Patients are notified when a donor accepts the request.
7. Administrators review donor commitments and fulfill requests.
8. Blood inventory is automatically updated after request completion.

---

## 📸 Screenshots

Include screenshots of the following pages:

* Home Page
* Registration Page
* Admin Dashboard
* Donor Dashboard
* Patient Dashboard
* Blood Request Page

---

## 👩‍💻 Developer

**Abarna Jeyabharathi L**
B.Tech – Information Technology
Velammal College of Engineering and Technology

---

## 📄 License

This project has been developed for academic and educational purposes.

