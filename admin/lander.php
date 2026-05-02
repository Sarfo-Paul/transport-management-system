
<!doctype html>

<html
  lang="en"
  class="layout-navbar-fixed layout-menu-fixed layout-compact layout-menu-collapsed"
  dir="ltr"
  data-skin="default"
  data-assets-path="assets/"
  data-template="vertical-menu-template"
  data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>UG TRANSPORT MANAMGENT SYSTEM</title>

    <meta name="description" content="" />

<!-- Favicon -->
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' version='1.1'>
  <defs><linearGradient id='TSgradient' x1='0%' y1='0%' x2='100%' y2='100%'>
  <stop offset='0%' style='stop-color:%237367F0;stop-opacity:1'/>
<stop offset='100%' style='stop-color:%23A66FFE;stop-opacity:1'/></linearGradient>
</defs><rect fill='url(%23TSgradient)' x='0' y='0' width='32' height='32' rx='6'></rect><text x='16' y='22' font-family='Arial,sans-serif' font-size='16' font-weight='bold' text-anchor='middle' fill='%23FFFFFF'>TS</text></svg>" />
    <meta name="description" content="Transpass UG streamlines University of Ghana campus transport with route maps, schedules, role dashboards, and secure bookings." />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a0ca3;
            --secondary: #7209b7;
            --accent: #4895ef;
            --accent-alt: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --success: #38b000;
            --warning: #f72585;
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            --gradient-alt: linear-gradient(135deg, var(--accent) 0%, var(--accent-alt) 100%);
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
            background-color: #fafbff;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            position: fixed;
            width: 100%;
            z-index: 1000;
            transition: all 0.4s ease;
        }
        
        header.scrolled {
            padding: 5px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            transition: all 0.3s ease;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logo i {
            margin-right: 10px;
            font-size: 28px;
            transition: all 0.3s ease;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-left: 30px;
            position: relative;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient);
            transition: width 0.3s ease;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .nav-btn {
            background: var(--gradient);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            text-decoration: none;
            display: inline-block;
        }
        
        .nav-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--dark);
            cursor: pointer;
            z-index: 1001;
        }

        /* Hero Section */
        .hero {
            background: var(--gradient);
            color: white;
            padding: 200px 0 120px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.1' d='M0,128L48,117.3C96,107,192,85,288,112C384,139,480,213,576,218.7C672,224,768,160,864,138.7C960,117,1056,139,1152,149.3C1248,160,1344,160,1392,160L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            background-position: center;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.2;
            animation: fadeInUp 1s ease;
        }
        
        .hero p {
            font-size: 1.25rem;
            max-width: 700px;
            margin: 0 auto 40px;
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.2s forwards;
            opacity: 0;
        }
        
        .hero-btns {
            display: flex;
            justify-content: center;
            gap: 20px;
            animation: fadeInUp 1s ease 0.4s forwards;
            opacity: 0;
        }
        
        .btn {
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background-color: white;
            color: var(--primary);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.3);
        }
        
        .btn-outline {
            border: 2px solid rgba(255, 255, 255, 0.7);
            color: white;
            background: transparent;
            backdrop-filter: blur(10px);
        }
        
        .btn-outline:hover {
            background-color: white;
            color: var(--primary);
            border-color: white;
        }

        /* Trusted strip */
        .trusted-strip {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px 25px;
            margin-top: 50px;
            display: flex;
            gap: 20px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeIn 1s ease 0.8s forwards;
            opacity: 0;
        }
        
        .trusted-badge {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }
        
        .trusted-logo {
            color: white;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .trusted-logo:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        /* Features Section */
        .section {
            padding: 100px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 70px;
        }
        
        .section-title .tag {
            display: inline-block;
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .section-title p {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.1rem;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background-color: white;
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 0;
            background: var(--gradient);
            z-index: -1;
            transition: all 0.4s ease;
            opacity: 0;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(67, 97, 238, 0.1);
            color: white;
        }
        
        .feature-card:hover::before {
            height: 100%;
            opacity: 1;
        }
        
        .feature-card:hover .feature-icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: scale(1.1);
        }
        
        .feature-card:hover h3,
        .feature-card:hover p {
            color: white;
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            color: var(--primary);
            font-size: 32px;
            transition: all 0.4s ease;
        }
        
        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .feature-card p {
            transition: all 0.3s ease;
        }
        
        /* Audience Section */
        .audience-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .audience-card {
            background: #fff;
            border-radius: 16px;
            padding: 35px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,.06);
            height: 100%;
            transition: all 0.4s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .audience-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gradient);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }
        
        .audience-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(67, 97, 238, 0.1);
        }
        
        .audience-card:hover::after {
            transform: scaleX(1);
        }
        
        .audience-card .feature-icon {
            margin: 0 auto 20px;
        }
        
        .audience-card h3 {
            margin: 15px 0 12px;
            font-size: 22px;
        }
        
        .audience-card p {
            color: var(--gray);
        }
        
        /* How it works */
        .hiw-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            counter-reset: step-counter;
        }
        
        .hiw-step {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,.06);
            position: relative;
            transition: all 0.4s ease;
        }
        
        .hiw-step:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(67, 97, 238, 0.1);
        }
        
        .hiw-num {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--gradient);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 18px;
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .hiw-step h3 {
            font-size: 20px;
            margin-bottom: 12px;
        }
        
        .hiw-step p {
            color: var(--gray);
        }
        
        /* Stats Section */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            text-align: center;
        }
        
        .stat-card {
            padding: 40px 30px;
            border-radius: 16px;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(67, 97, 238, 0.1);
            background: var(--gradient);
            color: white;
        }
        
        .stat-card:hover h3 {
            color: white;
        }
        
        .stat-card h3 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        /* FAQ Section */
        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .faq-item {
            margin-bottom: 20px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .faq-question {
            background-color: white;
            padding: 25px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .faq-question i {
            transition: transform 0.3s ease;
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }
        
        .faq-answer {
            padding: 0 25px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
            background-color: #f8f9fa;
        }
        
        .faq-item.active .faq-answer {
            padding: 25px;
            max-height: 300px;
        }
        
        /* CTA Section */
        .cta {
            background: var(--gradient);
            color: white;
            text-align: center;
            padding: 80px 0;
            border-radius: 20px;
            margin: 100px auto;
            max-width: 1200px;
            position: relative;
            overflow: hidden;
        }
        
        .cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.1' d='M0,128L48,117.3C96,107,192,85,288,112C384,139,480,213,576,218.7C672,224,768,160,864,138.7C960,117,1056,139,1152,149.3C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            background-position: center;
        }
        
        .cta-content {
            position: relative;
            z-index: 2;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
        }
        
        .cta p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }
        
      /* Footer Styles */
footer {
  background: var(--dark);
  color: white;
  padding: 80px 0 20px;
  position: relative;
  overflow: hidden;
}

footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%234361ee' fill-opacity='0.1' d='M0,128L48,117.3C96,107,192,85,288,112C384,139,480,213,576,218.7C672,224,768,160,864,138.7C960,117,1056,139,1152,149.3C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
  background-size: cover;
  background-position: center;
}

.footer-content {
  position: relative;
  z-index: 2;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 40px;
  margin-bottom: 50px;
}

.footer-col h4 {
  font-size: 20px;
  margin-bottom: 20px;
  position: relative;
  padding-bottom: 10px;
  font-weight: 700;
}

.footer-col h4::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  width: 40px;
  height: 3px;
  background: var(--gradient);
  border-radius: 3px;
}

.footer-logo {
  display: inline-flex;
  margin-bottom: 20px;
  font-size: 24px;
  color: white;
}

.footer-logo:hover {
  color: white;
  transform: none;
}

.footer-about {
  color: #adb5bd;
  margin-bottom: 25px;
  line-height: 1.7;
}

.footer-links {
  list-style: none;
}

.footer-links li {
  margin-bottom: 12px;
}

.footer-links a {
  color: #adb5bd;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-block;
}

.footer-links a:hover {
  color: white;
  padding-left: 5px;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  color: #adb5bd;
}

.contact-item i {
  color: var(--accent);
  margin-top: 4px;
  min-width: 16px;
}

.contact-item a {
  color: #adb5bd;
  text-decoration: none;
  transition: all 0.3s ease;
}

.contact-item a:hover {
  color: white;
}

.social-links {
  display: flex;
  gap: 15px;
  margin-top: 20px;
}

.social-links a {
  color: white;
  background: rgba(255,255,255,0.1);
  width: 45px;
  height: 45px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.social-links a:hover {
  background: var(--gradient);
  transform: translateY(-3px);
}

.footer-bottom {
  text-align: center;
  padding-top: 30px;
  border-top: 1px solid rgba(255,255,255,0.1);
  color: #adb5bd;
  font-size: 14px;
}

.footer-bottom-content {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* Responsive Footer */
@media (max-width: 768px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 30px;
    text-align: center;
  }
  
  .footer-col h4::after {
    left: 50%;
    transform: translateX(-50%);
  }
  
  .contact-item {
    justify-content: center;
  }
  
  .footer-bottom-content {
    flex-direction: column;
    gap: 10px;
  }
}
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 2.8rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .section-title h2 {
                font-size: 2.2rem;
            }
        }
        
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: fixed;
                top: 0;
                right: 0;
                width: 280px;
                height: 100vh;
                background: white;
                flex-direction: column;
                padding: 100px 30px 30px;
                box-shadow: -5px 0 25px rgba(0,0,0,0.1);
                z-index: 1000;
                transition: transform 0.4s ease;
                transform: translateX(100%);
            }
            
            .nav-links.open {
                display: flex;
                transform: translateX(0);
            }
            
            .nav-links li {
                margin: 15px 0;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .hero {
                padding: 160px 0 80px;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1rem;
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
                font-size: 1.8rem;
            }
            
            .cta h2 {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero {
                padding: 140px 0 60px;
            }
            
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .feature-card, .audience-card, .hiw-step {
                padding: 25px 20px;
            }
            
            .trusted-strip {
                flex-direction: column;
                gap: 15px;
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
                    <span>Transpass UG</span>
                </a>
                
                <ul class="nav-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#audience">For You</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
                
                <a href="register.php" class="nav-btn">Register</a>                
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h1 data-aos="fade-up" data-aos-duration="1000">Smart Campus Transport for University of Ghana</h1>
                <p data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">Transpass UG streamlines campus transportation with role-based dashboards, interactive route maps, and real-time booking.</p>
                <div class="hero-btns" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <a href="register.php" class="btn btn-primary">Get Started</a>
                    <a href="#features" class="btn btn-outline">Learn More</a>
                </div>

                <div class="trusted-strip" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
                    <span class="trusted-badge">Exclusively for</span>
                    <span class="trusted-logo">University of Ghana</span>
                    <span class="trusted-logo">Legon Campus</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" id="features">
        <div class="container">
            <div class="section-title" data-aos="fade-up" data-aos-duration="1000">
                <span class="tag">Key Features</span>
                <h2>Everything you need for campus transportation</h2>
                <p>Our platform provides comprehensive solutions for students, staff, drivers and transport administrators.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3>Interactive Route Map</h3>
                    <p>View all available campus routes with detailed information on an interactive map interface.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Real-Time Scheduling</h3>
                    <p>Access up-to-date schedules with real-time availability and booking options.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3>Role-Based Access</h3>
                    <p>Different dashboards for students, staff, drivers, and administrators with appropriate permissions.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Real-Time Notifications</h3>
                    <p>Receive instant updates about booking confirmations, schedule changes, and important announcements.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="500">
                    <div class="feature-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>Administrative Dashboard</h3>
                    <p>Comprehensive management of vehicles, drivers, routes, and schedules for transport administrators.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
                    <div class="feature-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3>Centralized Database</h3>
                    <p>All transport data securely stored and managed in a centralized, reliable database system.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Audience Section -->
    <section class="section" id="audience">
        <div class="container">
            <div class="section-title" data-aos="fade-up" data-aos-duration="1000">
                <span class="tag">Built for the UG Community</span>
                <h2>Tailored experience for every user</h2>
                <p>Transpass UG simplifies campus transit for all members of the University of Ghana community.</p>
            </div>
            <div class="audience-grid">
                <div class="audience-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    <div class="feature-icon"><i class="fas fa-user-graduate"></i></div>
                    <h3>Students & Staff</h3>
                    <p>Browse routes, check schedules, book trips, and manage your reservations with ease.</p>
                </div>
                <div class="audience-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="feature-icon"><i class="fas fa-id-card"></i></div>
                    <h3>Drivers</h3>
                    <p>View assigned routes and schedules, report issues, and update your status in real-time.</p>
                </div>
                <div class="audience-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                    <div class="feature-icon"><i class="fas fa-clipboard-check"></i></div>
                    <h3>Administrators</h3>
                    <p>Manage the entire transport system - vehicles, drivers, routes, schedules, and user reports.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="section" id="how-it-works">
        <div class="container">
            <div class="section-title" data-aos="fade-up" data-aos-duration="1000">
                <span class="tag">How it works</span>
                <h2>Simple three-step process</h2>
                <p>Getting started with Transpass UG is quick and straightforward.</p>
            </div>
            <div class="hiw-steps">
                <div class="hiw-step" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    <div class="hiw-num">1</div>
                    <h3>Create account</h3>
                    <p>Sign up with your University of Ghana credentials and select your role (student, staff, driver, or administrator).</p>
                </div>
                <div class="hiw-step" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="hiw-num">2</div>
                    <h3>Access your dashboard</h3>
                    <p>Based on your role, you'll have access to specific features tailored to your transportation needs.</p>
                </div>
                <div class="hiw-step" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                    <div class="hiw-num">3</div>
                    <h3>Start using the system</h3>
                    <p>Book trips, manage schedules, or oversee operations depending on your role and permissions.</p>
                </div>
            </div>
        </div>
    </section>

   <!-- Stats Section -->
<section class="section">
    <div class="container">
        <div class="stats">
            <?php
            // Database connection
            $servername = "127.0.0.1:3307";
            $username = "root"; // Replace with your DB username
            $password = ""; // Replace with your DB password
            $dbname = "transport_management";
            
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            // Get total registered users
            $userCountQuery = "SELECT COUNT(*) as total_users FROM users";
            $userCountResult = $conn->query($userCountQuery);
            $userCount = $userCountResult->fetch_assoc()['total_users'];
            
            // Get active vehicles (vehicles with status 'Active')
            $vehicleCountQuery = "SELECT COUNT(*) as active_vehicles FROM vehicles WHERE status = 'Active'";
            $vehicleCountResult = $conn->query($vehicleCountQuery);
            $vehicleCount = $vehicleCountResult->fetch_assoc()['active_vehicles'];
            
          
            
            // Get total campus routes
            $routesCountQuery = "SELECT COUNT(*) as total_routes FROM routes WHERE status = 'Active'";
            $routesCountResult = $conn->query($routesCountQuery);
            $routesCount = $routesCountResult->fetch_assoc()['total_routes'];
            
            $conn->close();
            ?>
            
            <div class="stat-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                <h3 class="counter" data-target="<?php echo $userCount; ?>">0</h3>
                <p>Registered Users</p>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                <h3 class="counter" data-target="<?php echo $vehicleCount; ?>">0</h3>
                <p>Active Vehicles</p>
            </div>
           
            <div class="stat-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <h3 class="counter" data-target="<?php echo $routesCount; ?>">0</h3>
                <p>Campus Routes</p>
            </div>
        </div>
    </div>
</section>

  <!-- FAQ Section -->
<section class="section" id="faq">
    <div class="container">
        <div class="section-title" data-aos="fade-up" data-aos-duration="1000">
            <span class="tag">FAQ</span>
            <h2>Frequently asked questions</h2>
            <p>Find answers to common questions about using Transpass UG.</p>
        </div>
        
        <div class="faq-container">
            <div class="faq-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                <div class="faq-question">
                    <span>Who can use Transpass UG?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Transpass UG is exclusively for University of Ghana students, staff, drivers, and transport administrators with valid university credentials.</p>
                </div>
            </div>
            
            <div class="faq-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                <div class="faq-question">
                    <span>Is there a cost to use the service?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>No, Transpass UG is a free service provided by the University of Ghana to facilitate campus transportation. There are no payments or ticket purchases required.</p>
                </div>
            </div>
            
            <div class="faq-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                <div class="faq-question">
                    <span>How can I contact support?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Use <strong>Report an Issue</strong> for non-urgent matters. For urgent operational issues, follow the contact details provided by your institution.</p>
                </div>
            </div>
            
            <div class="faq-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <div class="faq-question">
                    <span>I left an item on the bus. What do I do?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Report it via <strong>Report an Issue</strong> with your booking number and a description. We’ll coordinate with the driver and contact you if it’s found.</p>
                </div>
            </div>
            
            <div class="faq-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="500">
                <div class="faq-question">
                    <span>How will I be notified about updates?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>You’ll see notifications in the top bar and on your dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</section>


    <!-- CTA Section -->
    <div class="container">
        <div class="cta" data-aos="fade-up" data-aos-duration="1000">
            <div class="cta-content">
                <h2>Ready to Simplify Your Campus Commute?</h2>
                <p>Register today and experience seamless transportation across the University of Ghana campus</p>
                <a href="register.php" class="btn btn-primary">Create Account</a>
            </div>
        </div>
    </div>

<!-- Footer -->
<footer>
  <div class="container footer-content">
    <div class="footer-grid">
      <!-- About Column -->
      <div class="footer-col" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
        <a href="#" class="logo footer-logo">
          <i class="fas fa-bus"></i>
          <span>Transpass UG</span>
        </a>
        <p class="footer-about">
          Transpass UG is the exclusive campus transportation management system for the University of Ghana, ensuring safe, timely, and convenient travel across campus.
        </p>
      </div>

      <!-- Quick Links Column -->
      <div class="footer-col" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
        <h4>Quick Links</h4>
        <ul class="footer-links">
          <li><a href="#home">Home</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#audience">For You</a></li>
          <li><a href="#how-it-works">How It Works</a></li>
          <li><a href="#faq">FAQ</a></li>
        </ul>
      </div>

      <!-- Contact Column -->
      <div class="footer-col" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
        <h4>Contact Info</h4>
        <div class="contact-info">
          <div class="contact-item">
            <i class="fas fa-user"></i>
            <div><strong>Developer: Paul Sarfo</strong></div>
          </div>
          <div class="contact-item">
            <i class="fas fa-phone"></i>
            <div><a href="tel:+233205687991">+233 20 568 7991</a></div>
          </div>
          <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <div><a href="mailto:junioratta64@gmail.com">junioratta64@gmail.com</a></div>
          </div>
          <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <div>Legon, Accra, Ghana</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
      <div class="footer-bottom-content">
        <p>&copy; <span id="year"></span> Transpass UG - University of Ghana. All rights reserved.</p>
        <p>Developed with <i class="fas fa-heart" style="color: var(--warning);"></i> by Paul Sarfo</p>
      </div>
    </div>
  </div>
</footer>


    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        // Mobile Menu Toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');
        
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('open');
            mobileMenuBtn.querySelector('i').classList.toggle('fa-bars');
            mobileMenuBtn.querySelector('i').classList.toggle('fa-times');
        });
        
        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
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
                        navLinks.classList.remove('open');
                        mobileMenuBtn.querySelector('i').classList.add('fa-bars');
                        mobileMenuBtn.querySelector('i').classList.remove('fa-times');
                    }
                }
            });
        });

        // Current year
        document.getElementById('year').textContent = new Date().getFullYear();

        // Counter animation
        const counters = document.querySelectorAll('.counter');
        let countersStarted = false;
        
        const startCounters = () => {
            if (countersStarted) return;
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'), 10) || 0;
                let current = 0;
                const increment = Math.max(1, Math.ceil(target / 60));
                
                const updateCounter = () => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target + (target >= 5000 ? '+' : '');
                    } else {
                        counter.textContent = current;
                        requestAnimationFrame(updateCounter);
                    }
                };
                
                updateCounter();
            });
            
            countersStarted = true;
        };

        // Intersection Observer for counters
        const statsSection = document.querySelector('.stats');
        if (statsSection) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        startCounters();
                    }
                });
            }, { threshold: 0.5 });
            
            observer.observe(statsSection);
        }
    </script>
</body>
</html>