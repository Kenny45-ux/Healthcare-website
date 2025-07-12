<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html");
    exit();
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>MediConnect | User Panel</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>
<body class="is-preload">
    <div id="page-wrapper">

        <!-- Header -->
        <header id="header" class="alt">
            <h1><a href="index.php">MediConnect</a></h1>
            <nav><a href="#menu">Menu</a></nav>
        </header>

        <!-- Menu -->
        <nav id="menu">
            <div class="inner">
                <h2>Menu</h2>
                <ul class="links">
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#appointments">Book Appointment</a></li>
                    <li><a href="Plan.html">Plan</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
                <a href="#" class="close">Close</a>
            </div>
        </nav>

        <!-- Banner -->
        <section id="banner">
            <div class="inner">
                <div class="logo"><span class="icon fa-heartbeat"></span></div>
                <h2>Welcome to MediConnect</h2>
                <p>Health and Hope for Every Home</p>
            </div>
        </section>

        <!-- Services -->
        <section id="services" class="wrapper">
            <div class="inner">
                <h2 class="major">Available Services</h2>
                <section class="features">
                    <article>
                        <a href="services/consultation.html" class="image"><img src="images/service-consultation.jpg" alt="Consultation" /></a>
                        <h3 class="major">General Consultation</h3>
                        <p>Routine checkups and personalized care from licensed professionals.</p>
                    </article>
                    <article>
                        <a href="services/maternal.html" class="image"><img src="images/service-maternal.jpg" alt="Maternal Health" /></a>
                        <h3 class="major">Maternal Health</h3>
                        <p>Safe pregnancy support, prenatal care, and child wellness programs.</p>
                    </article>
                    <article>
                        <a href="services/hiv.html" class="image"><img src="images/service-hiv.jpg" alt="HIV Counseling" /></a>
                        <h3 class="major">HIV/AIDS Counseling</h3>
                        <p>Private and compassionate HIV testing and support services.</p>
                    </article>
                    <article>
                        <a href="services/malaria.html" class="image"><img src="images/service-malaria.jpg" alt="Malaria Treatment" /></a>
                        <h3 class="major">Malaria Treatment</h3>
                        <p>Diagnosis, treatment, and education on malaria prevention.</p>
                    </article>
                    <article>
                        <a href="services/ai-assistant.html" class="image"><img src="images/service-ai.jpg" alt="AI Health Assistant" /></a>
                        <h3 class="major">AI Health Assistant</h3>
                        <p>Ask health-related questions 24/7 using our smart AI assistant to receive guidance instantly before scheduling a visit.</p>
                    </article>
                    <article>
                        <a href="services/telemedicine.html" class="image"><img src="images/service-telemedicine.jpg" alt="Telemedicine" /></a>
                        <h3 class="major">Telemedicine Access</h3>
                        <p>Secure video consultations with doctors from the comfort of your home using a mobile phone or PC.</p>
                    </article>
                </section>
            </div>
        </section>

        <!-- Book Appointment -->
        <section id="appointments" class="wrapper spotlight style2">
            <div class="inner">
                <h2 class="major">Book an Appointment</h2>
                <form method="post" action="submit_appointment.php">
                    <div class="fields">
                        <div class="field">
                            <label for="user_name">Full Name</label>
                            <input type="text" name="user_name" id="user_name" required />
                        </div>

                        <div class="field">
                            <label for="user_email">Email</label>
                            <input type="email" name="user_email" id="user_email" required />
                        </div>

                        <div class="field">
                            <label for="reason">Service Required</label>
                            <select name="reason" id="reason" required>
                                <option value="">-- Select a Service --</option>
                                <option value="Consultation">Consultation</option>
                                <option value="Maternal Health">Maternal Health</option>
                                <option value="HIV/AIDS Support">HIV/AIDS Support</option>
                                <option value="Malaria Treatment">Malaria Treatment</option>
                                <option value="Telemedicine">Telemedicine</option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="appointment_date">Preferred Date</label>
                            <input type="date" name="appointment_date" id="appointment_date" required />
                        </div>

                        <div class="field">
                            <label for="appointment_time">Preferred Time</label>
                            <input type="time" name="appointment_time" id="appointment_time" required />
                        </div>

                        <div class="field">
                            <label for="message">Additional Info</label>
                            <textarea name="message" id="message" rows="4" placeholder="Optional notes or symptoms..."></textarea>
                        </div>
                    </div>

                    <ul class="actions">
                        <li><input type="submit" value="Book Now" class="primary" /></li>
                    </ul>
                </form>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faq" class="wrapper">
            <div class="inner">
                <h2 class="major">Frequently Asked Questions</h2>
                <dl>
                    <dt>Do I need an account to book an appointment?</dt>
                    <dd>No, you can book as a guest. But creating an account helps manage and view your records easily.</dd>
                    <dt>Is my health information secure?</dt>
                    <dd>Yes, we use SSL encryption and secure databases to protect all personal and medical information.</dd>
                    <dt>Can I get a virtual consultation?</dt>
                    <dd>Yes, just select "Telemedicine" when booking and our team will guide you to a video session.</dd>
                </dl>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="wrapper">
            <div class="inner">
                <h2 class="major">Contact Us</h2>
                <form method="post" action="send_message.php">
                    <div class="fields">
                        <div class="field">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" />
                        </div>
                        <div class="field">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" />
                        </div>
                        <div class="field">
                            <label for="message">Message</label>
                            <textarea name="message" id="message" rows="4"></textarea>
                        </div>
                    </div>
                    <ul class="actions">
                        <li><input type="submit" value="Send Message" /></li>
                    </ul>
                </form>
                <ul class="contact">
                    <li class="icon solid fa-phone">0776540229</li>
                    <li class="icon solid fa-envelope"><a href="#">kennychitalo45@gmail.com</a></li>
                    <li class="icon brands fa-facebook-f"><a href="#">facebook.com/yourclinic</a></li>
                </ul>
                <ul class="copyright">
                    <li>&copy; MediConnect. All rights reserved.</li>
                    <li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
                </ul>
            </div>
        </section>

    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
