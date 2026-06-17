<?php
require_once 'config.php';

if(isset($_SESSION['user_id'])) {
    header("Location: " . $_SESSION['role'] . "_dashboard.php");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = MD5(trim($_POST['password']));
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        header("Location: " . $user['role'] . "_dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        /* HEADER */
        .header {
            background: #b22222;
            padding: 14px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header .site-title {
            color: white;
            font-size: 17px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .header .header-links {
            display: flex;
            gap: 8px;
        }
        .header .header-links a {
            color: white;
            text-decoration: none;
            font-size: 13px;
            padding: 6px 16px;
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: 4px;
            transition: background 0.2s;
        }
        .header .header-links a:hover {
            background: rgba(255,255,255,0.15);
        }
        .header .header-links a.register-btn {
            background: white;
            color: #b22222;
            border-color: white;
        }
        .header .header-links a.register-btn:hover {
            background: #f0f0f0;
        }

        /* BANNER */
        .banner {
            background: #b22222;
            color: white;
            padding: 50px 30px;
            text-align: center;
            border-bottom: 4px solid #8b0000;
        }
        .banner h1 {
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }
        .banner p {
            font-size: 15px;
            opacity: 0.88;
            max-width: 560px;
            margin: 0 auto 22px;
            line-height: 1.7;
        }
        .banner a {
            display: inline-block;
            background: white;
            color: #b22222;
            padding: 10px 28px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }
        .banner a:hover {
            background: #f5f5f5;
        }

        /* SECTION COMMON */
        .section {
            padding: 50px 30px;
            max-width: 960px;
            margin: 0 auto;
        }
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #b22222;
            margin-bottom: 6px;
            border-left: 4px solid #b22222;
            padding-left: 12px;
        }
        .section-desc {
            color: #666;
            font-size: 14px;
            margin-bottom: 28px;
            padding-left: 16px;
        }

        /* WHY DONATE */
        .why-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
        }
        .why-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-top: 3px solid #b22222;
            padding: 22px 18px;
            border-radius: 4px;
        }
        .why-card h3 {
            font-size: 14px;
            font-weight: 700;
            color: #222;
            margin-bottom: 8px;
        }
        .why-card p {
            font-size: 13px;
            color: #555;
            line-height: 1.6;
        }

        /* ELIGIBILITY */
        .eligibility-wrap {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }
        .eligibility-wrap table {
            width: 100%;
            border-collapse: collapse;
        }
        .eligibility-wrap table th {
            background: #f9f9f9;
            padding: 10px 16px;
            text-align: left;
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e0e0e0;
        }
        .eligibility-wrap table td {
            padding: 11px 16px;
            font-size: 13px;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
        }
        .eligibility-wrap table tr:last-child td {
            border-bottom: none;
        }

        /* DOS DONTS */
        .dd-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .dd-box {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }
        .dd-box .dd-head {
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 600;
        }
        .dd-box.dos .dd-head {
            background: #f0faf3;
            color: #1e7e34;
            border-bottom: 1px solid #d4edda;
        }
        .dd-box.donts .dd-head {
            background: #fff5f5;
            color: #b22222;
            border-bottom: 1px solid #f5c6cb;
        }
        .dd-box ul {
            list-style: none;
            padding: 0;
        }
        .dd-box ul li {
            padding: 9px 18px;
            font-size: 13px;
            color: #444;
            border-bottom: 1px solid #f8f8f8;
            line-height: 1.5;
        }
        .dd-box ul li:last-child {
            border-bottom: none;
        }
        .dd-period {
            font-size: 14px;
            font-weight: 600;
            color: #444;
            margin-bottom: 12px;
            margin-top: 20px;
        }

        /* COMPATIBILITY TABLE */
        .compat-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            font-size: 13px;
        }
        .compat-table th {
            background: #b22222;
            color: white;
            padding: 11px 15px;
            text-align: left;
        }
        .compat-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }
        .compat-table tr:last-child td { border-bottom: none; }
        .compat-table tr:hover td { background: #fdf5f5; }
        .compat-table td:first-child {
            font-weight: 700;
            color: #b22222;
        }

        /* LOGIN SECTION */
        .login-section {
            background: #fff;
            border-top: 3px solid #b22222;
            padding: 50px 30px;
        }
        .login-inner {
            max-width: 960px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: start;
        }
        .login-info h2 {
            font-size: 20px;
            color: #b22222;
            margin-bottom: 14px;
            border-left: 4px solid #b22222;
            padding-left: 12px;
        }
        .login-info p {
            font-size: 13px;
            color: #555;
            line-height: 1.8;
            margin-bottom: 18px;
        }
        .login-info ul {
            padding-left: 18px;
        }
        .login-info ul li {
            font-size: 13px;
            color: #444;
            padding: 4px 0;
            line-height: 1.6;
        }
        .login-form-box {
            background: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 28px;
        }
        .login-form-box h3 {
            font-size: 16px;
            color: #222;
            margin-bottom: 4px;
        }
        .login-form-box .sub {
            font-size: 12px;
            color: #888;
            margin-bottom: 22px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .form-group input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 13px;
            color: #333;
        }
        .form-group input:focus {
            border-color: #b22222;
            outline: none;
        }
        .login-btn {
            width: 100%;
            padding: 10px;
            background: #b22222;
            color: white;
            border: none;
            border-radius: 3px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 4px;
        }
        .login-btn:hover { background: #8b0000; }
        .register-link {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: #666;
        }
        .register-link a {
            color: #b22222;
            font-weight: 600;
            text-decoration: none;
        }
        .alert-error {
            background: #fff5f5;
            border: 1px solid #f5c6cb;
            color: #b22222;
            padding: 9px 12px;
            border-radius: 3px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        /* FOOTER */
        .footer {
            background: #b22222;
            color: white;
            text-align: center;
            padding: 18px;
            font-size: 12px;
        }

        /* DIVIDER */
        .full-bg {
            background: #f5f5f5;
        }

        @media(max-width: 650px) {
            .dd-wrap { grid-template-columns: 1fr; }
            .login-inner { grid-template-columns: 1fr; }
            .why-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header class="header">
    <div class="site-title">
        <img src="logo.png" alt="logo" 
     style="height:34px; vertical-align:middle; margin-right:10px; 
            background:white; padding:3px; border-radius:4px;">
            Blood Bank Management System
    </div>
    <div class="header-links">
        <a href="#login">Login</a>
        <a href="register.php" class="register-btn">Register</a>
    </div>
</header>

<!-- BANNER -->
<div class="banner">
    <h1>Donate Blood, Save Lives</h1>
    <p>Blood cannot be manufactured — it can only come from generous donors. 
       Register today and help patients in need get the blood they require.</p>
    <a href="register.php">Register as Donor</a>
</div>

<!-- WHY DONATE -->
<div class="full-bg">
    <div class="section">
        <div class="section-title">Why Blood Donation Matters</div>
        <p class="section-desc">Blood is needed every day in hospitals across the country for various medical situations.</p>
        <div class="why-grid">
            <div class="why-card">
                <h3>Accident & Emergency Cases</h3>
                <p>Patients who lose blood in accidents often require immediate transfusions to survive. 
                   Timely availability of blood can be the difference between life and death.</p>
            </div>
            <div class="why-card">
                <h3>Surgical Procedures</h3>
                <p>Many surgeries including heart operations, organ transplants, 
                   and caesarean sections require blood transfusions during or after the procedure.</p>
            </div>
            <div class="why-card">
                <h3>Cancer & Chronic Illness</h3>
                <p>Patients undergoing chemotherapy or suffering from conditions like 
                   thalassemia and sickle cell anaemia need regular blood transfusions.</p>
            </div>
            <div class="why-card">
                <h3>Maternal & Child Health</h3>
                <p>Complications during childbirth and premature newborns often 
                   require blood. Adequate blood supply directly impacts maternal mortality rates.</p>
            </div>
        </div>
    </div>
</div>

<!-- ELIGIBILITY -->
<div style="background:white; border-top:1px solid #e8e8e8; border-bottom:1px solid #e8e8e8;">
    <div class="section">
        <div class="section-title">Eligibility Criteria</div>
        <p class="section-desc">You must meet the following basic requirements to donate blood.</p>
        <div class="eligibility-wrap">
            <table>
                <tr>
                    <th>Criteria</th>
                    <th>Requirement</th>
                    <th>Remarks</th>
                </tr>
                <tr>
                    <td>Age</td>
                    <td>18 to 65 years</td>
                    <td>First-time donors above 60 need physician approval</td>
                </tr>
                <tr>
                    <td>Body Weight</td>
                    <td>Minimum 50 kg</td>
                    <td>Lighter donors may not tolerate blood loss safely</td>
                </tr>
                <tr>
                    <td>Haemoglobin Level</td>
                    <td>12.5 g/dL or above</td>
                    <td>Checked at the donation center before collection</td>
                </tr>
                <tr>
                    <td>Blood Pressure</td>
                    <td>Within normal range</td>
                    <td>Systolic 90–160, Diastolic 60–100 mmHg</td>
                </tr>
                <tr>
                    <td>Pulse Rate</td>
                    <td>60 to 100 beats per minute</td>
                    <td>Should be regular at time of donation</td>
                </tr>
                <tr>
                    <td>Gap Between Donations</td>
                    <td>Minimum 3 months</td>
                    <td>Allows body time to replenish lost blood</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<!-- DOS AND DONTS -->
<div class="full-bg">
    <div class="section">
        <div class="section-title">Do's and Don'ts for Blood Donation</div>
        <p class="section-desc">Following these guidelines helps ensure a safe and comfortable donation experience.</p>

        <div class="dd-period">Before Donation</div>
        <div class="dd-wrap">
            <div class="dd-box dos">
                <div class="dd-head">Do's</div>
                <ul>
                    <li>Drink at least 500 ml of water 2 hours before donation</li>
                    <li>Have a light meal around 2 to 3 hours before going</li>
                    <li>Sleep well the night before donation</li>
                    <li>Wear clothing with sleeves that can be rolled up easily</li>
                    <li>Carry a valid government photo ID to the center</li>
                    <li>Let the staff know if you are on any medication</li>
                </ul>
            </div>
            <div class="dd-box donts">
                <div class="dd-head">Don'ts</div>
                <ul>
                    <li>Do not smoke for at least 2 hours before donation</li>
                    <li>Avoid alcohol consumption 24 hours before donating</li>
                    <li>Do not take aspirin or blood thinners 48 hours prior</li>
                    <li>Avoid heavy physical exercise on the day of donation</li>
                    <li>Do not donate if you have a cold, fever, or active infection</li>
                    <li>Avoid eating oily or fatty food before donation</li>
                </ul>
            </div>
        </div>

        <div class="dd-period">After Donation</div>
        <div class="dd-wrap">
            <div class="dd-box dos">
                <div class="dd-head">Do's</div>
                <ul>
                    <li>Drink extra fluids throughout the day after donation</li>
                    <li>Eat iron-rich foods such as spinach, dates, and legumes</li>
                    <li>Rest for at least 10 to 15 minutes before leaving the center</li>
                    <li>Keep the bandage on the arm for 4 to 5 hours</li>
                    <li>Contact the blood bank if you feel unwell after donation</li>
                    <li>Resume light activities gradually during the day</li>
                </ul>
            </div>
            <div class="dd-box donts">
                <div class="dd-head">Don'ts</div>
                <ul>
                    <li>Do not drive immediately if you feel dizzy or weak</li>
                    <li>Avoid lifting heavy objects with the donation arm for 24 hours</li>
                    <li>Do not consume alcohol for at least 24 hours after donating</li>
                    <li>Avoid standing in direct sun for extended periods</li>
                    <li>Do not remove the bandage for at least 4 hours</li>
                    <li>Avoid strenuous exercise or sports activity for the rest of the day</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- COMPATIBILITY CHART -->
<div style="background:white; border-top:1px solid #e8e8e8; border-bottom:1px solid #e8e8e8;">
    <div class="section">
        <div class="section-title">Blood Group Compatibility</div>
        <p class="section-desc">This table shows which blood groups can donate to and receive from each other.</p>
        <table class="compat-table">
            <tr>
                <th>Blood Group</th>
                <th>Can Donate To</th>
                <th>Can Receive From</th>
                <th>Notes</th>
            </tr>
            <tr><td>A+</td><td>A+, AB+</td><td>A+, A-, O+, O-</td><td>Second most common group</td></tr>
            <tr><td>A-</td><td>A+, A-, AB+, AB-</td><td>A-, O-</td><td>Can donate to all A and AB types</td></tr>
            <tr><td>B+</td><td>B+, AB+</td><td>B+, B-, O+, O-</td><td>Common in South Asian population</td></tr>
            <tr><td>B-</td><td>B+, B-, AB+, AB-</td><td>B-, O-</td><td>Relatively rare</td></tr>
            <tr><td>AB+</td><td>AB+ only</td><td>All blood groups</td><td>Universal recipient</td></tr>
            <tr><td>AB-</td><td>AB+, AB-</td><td>A-, B-, AB-, O-</td><td>Rarest blood type</td></tr>
            <tr><td>O+</td><td>A+, B+, AB+, O+</td><td>O+, O-</td><td>Most commonly found group</td></tr>
            <tr><td>O-</td><td>All blood groups</td><td>O- only</td><td>Universal donor — most needed</td></tr>
        </table>
    </div>
</div>

<!-- LOGIN -->
<div class="login-section" id="login">
    <div class="login-inner">
        <div class="login-info">
            <h2>Access Your Account</h2>
            <p>Login to manage your donor profile, submit blood requests, 
               or access the admin panel to monitor blood stock and requests.</p>
            <p>New users can register as a donor or patient using the Register button above. 
               Registration is free and takes less than a minute.</p>
            <ul>
                <li>Donors — update availability and respond to requests</li>
                <li>Patients — submit blood requests and find donors</li>
                <li>Admin — manage stock, donors, and fulfill requests</li>
            </ul>
        </div>

        <div class="login-form-box">
            <h3>Login</h3>
            <p class="sub">Enter your registered email and password</p>

            <?php if($error): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Blood Bank Management System. All Rights Reserved.</p>
</footer>

</body>
</html>