<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpass - Smart Transport Solutions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --success: #4cc9f0;
            --warning: #f72585;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            z-index: 1000;
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        
        .logo i {
            margin-right: 10px;
            font-size: 28px;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-left: 30px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .nav-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .nav-btn:hover {
            background-color: var(--secondary);
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--dark);
            cursor: pointer;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            padding: 180px 0 100px;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 20px;
            max-width: 700px;
            margin: 0 auto 40px;
            opacity: 0.9;
        }
        
        .hero-btns {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: white;
            color: var(--primary);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .btn-outline {
            border: 2px solid white;
            color: white;
        }
        
        .btn-outline:hover {
            background-color: white;
            color: var(--primary);
        }
        
        /* Features Section */
        .section {
            padding: 100px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title .tag {
            display: inline-block;
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .section-title h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background-color: rgba(67, 97, 238, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--primary);
            font-size: 28px;
        }
        
        .feature-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
        }
        
        /* Testimonials */
        .testimonials {
            background-color: #f8f9fa;
        }
        
        .testimonial-slider {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .testimonial-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 20px;
        }
        
        .testimonial-card .rating {
            color: #ffc107;
            margin-bottom: 20px;
        }
        
        .testimonial-card p {
            font-style: italic;
            margin-bottom: 20px;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .author-info h4 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .author-info p {
            font-style: normal;
            color: var(--gray);
            font-size: 14px;
        }
        
        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            text-align: center;
        }
        
        .stat-card {
            padding: 30px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .stat-card h3 {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        /* FAQ */
        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .faq-item {
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .faq-question {
            background-color: white;
            padding: 20px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 300px;
        }
        
        /* CTA */
        .cta {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            text-align: center;
            padding: 80px 0;
            border-radius: 15px;
            margin: 100px auto;
            max-width: 1200px;
        }
        
        .cta h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        
        .cta p {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }
        
        /* Contact */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        
        .contact-info {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .contact-info h3 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .contact-method {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            background-color: rgba(67, 97, 238, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
            font-size: 20px;
        }
        
        .contact-text h4 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .contact-text p {
            color: var(--gray);
        }
        
        .contact-form {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .contact-form h3 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 80px 0 30px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
        }
        
        .footer-col h4 {
            font-size: 18px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-col h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background-color: var(--primary);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            color: white;
            background-color: rgba(255,255,255,0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        
        .social-links a:hover {
            background-color: var(--primary);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #adb5bd;
            font-size: 14px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 40px;
            }
            
            .hero p {
                font-size: 18px;
            }
            
            .section-title h2 {
                font-size: 32px;
            }
        }
        
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .hero {
                padding: 150px 0 80px;
            }
            
            .hero h1 {
                font-size: 32px;
            }
            
            .hero p {
                font-size: 16px;
            }
            
            .hero-btns {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
            
            .section {
                padding: 70px 0;
            }
            
            .section-title h2 {
                font-size: 28px;
            }
            
            .cta h2 {
                font-size: 28px;
            }
        }
        
        @media (max-width: 576px) {
            .hero {
                padding: 120px 0 60px;
            }
            
            .hero h1 {
                font-size: 28px;
            }
            
            .feature-card {
                padding: 20px;
            }
            
            .contact-info, .contact-form {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav>
                <a href="#" class="logo">
                    <i class="fas fa-bus"></i>
                    <span>Transpass</span>
                </a>
                
                <ul class="nav-links">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#testimonials">Testimonials</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="admin.html">Admin</a></li>
                </ul>
                
                <a href="login.php" class="nav-btn">Login / Register</a>                
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Smart Transportation Solutions for Ghana</h1>
            <p>Seamless ticketing, route planning and fleet management for modern transportation needs.</p>
            <div class="hero-btns">
                <a href="#" class="btn btn-primary">Get Started</a>
                <a href="#" class="btn btn-outline">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" id="features">
        <div class="container">
            <div class="section-title">
                <span class="tag">Key Features</span>
                <h2>Everything you need for smart transportation</h2>
                <p>Our platform provides comprehensive solutions for passengers, drivers and transport operators.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Ticketing</h3>
                    <p>Purchase and manage tickets directly from your smartphone with our easy-to-use mobile app.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Real-time Updates</h3>
                    <p>Get live updates on bus schedules, delays, and route changes to plan your journey better.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3>Route Optimization</h3>
                    <p>AI-powered route planning to reduce travel time and improve efficiency for transport operators.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3>Secure Payments</h3>
                    <p>Multiple payment options including mobile money, cards and cash with complete transaction security.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Support</h3>
                    <p>Dedicated customer support available round the clock for all users.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analytics Dashboard</h3>
                    <p>Comprehensive reports and insights for transport operators.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section testimonials" id="testimonials">
        <div class="container">
            <div class="section-title">
                <span class="tag">Testimonials</span>
                <h2>What our customers say</h2>
                <p>Hear from people who have transformed their transportation experience with Transpass.</p>
            </div>
            
            <div class="testimonial-slider swiper">
                <div class="swiper-wrapper">
                    <div class="testimonial-card swiper-slide">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"This service has revolutionized my daily commute. The mobile ticketing is incredibly convenient and the real-time updates help me plan perfectly."</p>
                        <div class="testimonial-author">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Kwame Asante">
                            <div class="author-info">
                                <h4>Kwame Asante</h4>
                                <p>Daily Commuter</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card swiper-slide">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"The operational insights have helped us optimize routes and reduce costs by 22%. The analytics dashboard is incredibly valuable for our business."</p>
                        <div class="testimonial-author">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Ama Mensah">
                            <div class="author-info">
                                <h4>Ama Mensah</h4>
                                <p>Operations Manager</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card swiper-slide">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"The payment integration is seamless and secure. I've completely stopped carrying cash for transportation thanks to this innovative solution."</p>
                        <div class="testimonial-author">
                            <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Yaw Boateng">
                            <div class="author-info">
                                <h4>Yaw Boateng</h4>
                                <p>University Student</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section">
        <div class="container">
            <div class="stats">
                <div class="stat-card">
                    <h3>50k+</h3>
                    <p>Happy Commuters</p>
                </div>
                
                <div class="stat-card">
                    <h3>500+</h3>
                    <p>Transport Partners</p>
                </div>
                
                <div class="stat-card">
                    <h3>4.8/5</h3>
                    <p>Customer Rating</p>
                </div>
                
                <div class="stat-card">
                    <h3>24/7</h3>
                    <p>Customer Support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section" id="faq">
        <div class="container">
            <div class="section-title">
                <span class="tag">FAQ</span>
                <h2>Frequently asked questions</h2>
                <p>Browse through these FAQs to find answers to commonly asked questions.</p>
            </div>
            
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How do I purchase tickets using Transpass?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>You can purchase tickets directly through our mobile app. Simply select your route, choose your payment method (mobile money, card, or cash), and receive your digital ticket instantly.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What payment methods do you accept?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We accept all major mobile money services (MTN, Vodafone, AirtelTigo), credit/debit cards, and cash payments at designated agents across Ghana.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How accurate are your real-time bus tracking updates?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Our real-time tracking is highly accurate, using GPS technology from partnered buses. Updates are provided every 30 seconds, giving you the most current information about bus locations and estimated arrival times.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Can I get a refund for unused tickets?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, unused tickets can be refunded within 7 days of purchase. Refunds are processed to the original payment method and may take 3-5 business days to reflect in your account.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How can transport operators benefit from Transpass?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Transport operators gain access to our route optimization tools, digital ticketing platform, fleet management dashboard, and valuable analytics to improve operations and increase revenue.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <div class="container">
        <div class="cta">
            <h2>Ready to Transform Your Commute?</h2>
            <p>Download the app today and experience seamless transportation</p>
            <a href="#" class="btn btn-primary">Get Started</a>
        </div>
    </div>

    <!-- Contact Section -->
    <section class="section" id="contact">
        <div class="container">
            <div class="section-title">
                <span class="tag">Contact Us</span>
                <h2>Let's work together</h2>
                <p>Any question or remark? Just write us a message</p>
            </div>
            
            <div class="contact-grid">
                <div class="contact-info">
                    <h3>Contact Information</h3>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Email</h4>
                            <p>info@transpassgh.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Phone</h4>
                            <p>+233 24 456 7890</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Address</h4>
                            <p>1 Transpass Avenue, Accra</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Working Hours</h4>
                            <p>Mon-Fri: 8am - 6pm</p>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h3>Send us a message</h3>
                    <form>
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" class="form-control" placeholder="John Doe">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-control" placeholder="johndoe@gmail.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" class="form-control" placeholder="How can we help?">
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" class="form-control" placeholder="Write your message here"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Transpass</h4>
                    <p>Smart transportation solutions for Ghana and beyond.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul class="footer-links">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Press</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Product</h4>
                    <ul class="footer-links">
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">API</a></li>
                        <li><a href="#">Integrations</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Status</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul class="footer-links">
                        <li><a href="#">Privacy</a></li>
                        <li><a href="#">Terms</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Transpass. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');
        
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
        });
        
        // FAQ Accordion
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentNode;
                item.classList.toggle('active');
                
                // Close other open items
                faqQuestions.forEach(q => {
                    if (q !== question) {
                        q.parentNode.classList.remove('active');
                    }
                });
            });
        });
        
        // Testimonial Slider
        const testimonialSlider = new Swiper('.testimonial-slider', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                }
            }
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    if (window.innerWidth <= 768) {
                        navLinks.style.display = 'none';
                    }
                }
            });
        });
    </script>
</body>
</html>