<!DOCTYPE html>
@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
@endphp
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>دار القرآن - منصة تعليمية لإدارة المدارس القرآنية</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
        
        <!-- Bootstrap RTL CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --primary-color: #006400;
                --secondary-color: #92d36e;
                --accent-color: #d4af37;
                --light-green: #e8f5e9;
                --dark-green: #004d00;
            }
            
            body {
                font-family: 'Tajawal', sans-serif;
                background-color: #fafafa;
                color: #333;
            }
            
            .navbar {
                background-color: var(--primary-color);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            
            .navbar-brand {
                font-weight: 700;
                color: white !important;
            }
            
            .nav-link {
                color: rgba(255, 255, 255, 0.85) !important;
                font-weight: 500;
            }
            
            .nav-link:hover {
                color: white !important;
            }
            
            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }
            
            .btn-primary:hover {
                background-color: var(--dark-green);
                border-color: var(--dark-green);
            }
            
            .btn-outline-primary {
                color: var(--primary-color);
                border-color: var(--primary-color);
            }
            
            .btn-outline-primary:hover {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
                color: white;
            }
            
            .hero {
                background: linear-gradient(rgba(0, 100, 0, 0.8), rgba(0, 100, 0, 0.9)), url('/img/mosque-pattern.png');
                background-size: cover;
                color: white;
                padding: 100px 0;
                position: relative;
            }
            
            .hero::before {
                content: "";
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 50px;
                background: linear-gradient(to right bottom, transparent 49%, #fafafa 50%);
            }
            
            .hero h1 {
                font-weight: 700;
                margin-bottom: 20px;
            }
            
            .feature-card {
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                height: 100%;
                border: none;
            }
            
            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }
            
            .feature-icon {
                height: 80px;
                width: 80px;
                margin: 0 auto 20px;
                background-color: var(--light-green);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                color: var(--primary-color);
            }
            
            .testimonial {
                background-color: #fff;
                border-radius: 10px;
                padding: 30px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                margin: 10px 5px;
            }
            
            .cta-section {
                background-color: var(--light-green);
                padding: 80px 0;
                border-top: 5px solid var(--primary-color);
                border-bottom: 5px solid var(--primary-color);
            }
            
            .footer {
                background-color: var(--primary-color);
                color: white;
                padding: 50px 0 20px;
            }
            
            .footer-links h5 {
                font-weight: 700;
                margin-bottom: 20px;
                position: relative;
                display: inline-block;
            }
            
            .footer-links h5::after {
                content: '';
                position: absolute;
                bottom: -10px;
                left: 0;
                width: 50px;
                height: 2px;
                background-color: var(--accent-color);
            }
            
            .footer-links ul {
                list-style: none;
                padding-left: 0;
            }
            
            .footer-links ul li {
                margin-bottom: 10px;
            }
            
            .footer-links a {
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                transition: all 0.3s ease;
            }
            
            .footer-links a:hover {
                color: white;
                text-decoration: none;
            }
            
            .social-icons a {
                display: inline-block;
                width: 40px;
                height: 40px;
                background-color: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                text-align: center;
                line-height: 40px;
                margin-right: 10px;
                color: white;
                transition: all 0.3s ease;
            }
            
            .social-icons a:hover {
                background-color: var(--accent-color);
                transform: translateY(-3px);
            }
            
            .copyright {
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                padding-top: 20px;
                margin-top: 30px;
            }
            
            @media (max-width: 767.98px) {
                .hero {
                    padding: 60px 0;
                }
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="#"><i class="fas fa-book-quran me-2"></i> دار القرآن</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">الرئيسية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">المميزات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#login-options">تسجيل الدخول</a>
                        </li>
                    </ul>
                    <div class="d-flex">
                    @auth
                            @if(Auth::user()->role->name === 'moderator')
                                <a href="{{ route('moderator.dashboard') }}" class="btn btn-light me-2">لوحة التحكم</a>
                            @elseif(Auth::user()->role->name === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-light me-2">لوحة التحكم</a>
                            @elseif(Auth::user()->role->name === 'teacher')
                                <a href="{{ route('teacher.dashboard') }}" class="btn btn-light me-2">لوحة التحكم</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light">تسجيل الخروج</button>
                    </form>
                    @else
                    <a href="{{ route('student.login') }}" class="btn btn-light me-2"><i class="fas fa-user-graduate me-1"></i> دخول الطالب</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light"><i class="fas fa-user-tie me-1"></i> دخول المعلم</a>
                    @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero text-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <h1>دار القرآن</h1>
                        <p class="lead mb-4">منصة متكاملة لإدارة المدارس القرآنية والفصول التعليمية</p>
                        <div>
                            @auth
                                @if(Auth::user()->role->name === 'moderator')
                                    <a href="{{ route('moderator.dashboard') }}" class="btn btn-outline-light btn-lg px-4 me-2">لوحة التحكم</a>
                                @elseif(Auth::user()->role->name === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-lg px-4 me-2">لوحة التحكم</a>
                                @elseif(Auth::user()->role->name === 'teacher')
                                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-light btn-lg px-4 me-2">لوحة التحكم</a>
            @endif
                            @else
                                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4 me-2">سجل الآن</a>
                            @endauth
                            <a href="#features" class="btn btn-light btn-lg px-4">تعرف على المنصة</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add this after your hero section but before the features section -->
        <section id="login-options" class="py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold">تسجيل الدخول</h2>
                    <p class="text-muted">اختر نوع الحساب الخاص بك للدخول إلى المنصة</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-5 mb-4">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="card-body text-center p-5">
                                <div class="mb-4">
                                    <i class="fas fa-user-graduate fa-4x text-primary"></i>
                                </div>
                                <h3 class="card-title mb-3">طالب</h3>
                                <p class="card-text mb-4">
                                    إذا كنت طالباً في أحد الفصول، قم بتسجيل الدخول هنا لعرض جدولك الدراسي وسجل حضورك والرسائل من المعلمين.
                                </p>
                                <div class="d-grid">
                                    <a href="{{ route('student.login') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        دخول الطالب
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 mb-4">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="card-body text-center p-5">
                                <div class="mb-4">
                                    <i class="fas fa-user-tie fa-4x text-primary"></i>
                                </div>
                                <h3 class="card-title mb-3">معلم</h3>
                                <p class="card-text mb-4">
                                    إذا كنت معلماً، قم بتسجيل الدخول هنا لإدارة فصولك الدراسية وتسجيل حضور الطلاب وإرسال الرسائل.
                                </p>
                                <div class="d-grid">
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        دخول المعلم
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-5" id="features">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold">مميزات المنصة</h2>
                    <p class="text-muted">تقدم منصة دار القرآن العديد من المميزات التي تسهل عملية إدارة المدارس القرآنية</p>
                        </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="feature-card card text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h4>إدارة المعلمين</h4>
                            <p>سهولة إدارة المعلمين ومتابعة أدائهم وتوزيع الفصول عليهم</p>
                        </div>
                            </div>

                    <div class="col-md-4 mb-4">
                        <div class="feature-card card text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h4>إدارة الطلاب</h4>
                            <p>تسجيل الطلاب وتتبع حضورهم ومتابعة تقدمهم في الحفظ والتلاوة</p>
                        </div>
                                </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="feature-card card text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h4>تسجيل الحضور</h4>
                            <p>نظام متكامل لتسجيل حضور وغياب الطلاب وإنشاء تقارير دورية</p>
                            </div>
                        </div>

                    <div class="col-md-4 mb-4">
                        <div class="feature-card card text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4>التقارير والإحصائيات</h4>
                            <p>تقارير شاملة ومفصلة عن أداء المدارس والمعلمين والطلاب</p>
                        </div>
                            </div>

                    <div class="col-md-4 mb-4">
                        <div class="feature-card card text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h4>الإشعارات والتنبيهات</h4>
                            <p>نظام تنبيهات متكامل للمدارس والمعلمين والطلاب</p>
                            </div>
                        </div>

                    <div class="col-md-4 mb-4">
                        <div class="feature-card card text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h4>واجهة سهلة الاستخدام</h4>
                            <p>واجهة بسيطة وسهلة الاستخدام متوافقة مع جميع الأجهزة</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">                    
                <div class="text-center copyright">
                    <p class="mb-0">جميع الحقوق محفوظة &copy; {{ date('Y') }} دار القرآن</p>
                </div>
            </div>
        </footer>

        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
