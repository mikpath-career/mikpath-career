<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$database = new Database();
$db = $database->getConnection();

// Get career roadmap data with category join
$roadmap_query = "SELECT cr.*, cc.color_code, cr.competencies, cr.certifications, cr.description
                  FROM career_roadmap cr 
                  LEFT JOIN career_categories cc ON cr.level = cc.category_name 
                  ORDER BY 
                    CASE cr.level 
                        WHEN 'Entry Level' THEN 1 
                        WHEN 'Junior' THEN 2 
                        WHEN 'Mid Level' THEN 3 
                        WHEN 'Senior' THEN 4 
                        ELSE 5 
                    END";
$roadmap_stmt = $db->prepare($roadmap_query);
$roadmap_stmt->execute();
$roadmap_data = $roadmap_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get approved alumni stories
$alumni_query = "SELECT * FROM alumni_stories WHERE status = 'approved' ORDER BY created_at DESC";
$alumni_stmt = $db->prepare($alumni_query);
$alumni_stmt->execute();
$alumni_data = $alumni_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM career_roadmap) as total_careers,
    (SELECT COUNT(*) FROM alumni_stories WHERE status = 'approved') as total_stories,
    (SELECT COUNT(*) FROM users) as total_users";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIKPath: Peta Karier Manajemen Informasi Kesehatan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="#home" class="nav-logo">MIKPath</a>
            <div class="nav-menu" id="navMenu">
                <a href="#home" class="nav-link active">Beranda</a>
                <a href="#roadmap" class="nav-link">Roadmap</a>
                <a href="#alumni" class="nav-link">Alumni</a>
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="logout.php" class="nav-link">Logout</a>
                <?php endif; ?>
            </div>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <div class="hero-badge">Dapatkan panduan karier terbaik</div>
                <h1 class="hero-title">Kembangkan karier MIK-mu bersama kami.</h1>
                <p class="hero-subtitle">Temukan jalur karier yang tepat dengan panduan dari mentor berpengalaman dan cerita inspiratif dari alumni yang telah sukses di bidang Manajemen Informasi Kesehatan.</p>
                
                <div class="hero-search">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Cari jalur karier..." id="careerSearch">
                    <button class="search-btn" onclick="searchCareers()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="hero-features">
                    <div class="hero-feature">Panduan Terstruktur</div>
                    <div class="hero-feature">Mentor Berpengalaman</div>
                    <div class="hero-feature">Komunitas Alumni</div>
                </div>
            </div>
            
            <div class="hero-visual">
                <div class="hero-image">
                    <img src="assets/hero-image.png" alt="MIK Professional">
                </div>
                
                <!-- Floating Stats -->
                <div class="hero-stats">
                    <div class="hero-stats-title">No of students</div>
                    <div class="hero-stats-chart">
                        <div class="hero-stats-bar"></div>
                        <div class="hero-stats-bar"></div>
                        <div class="hero-stats-bar"></div>
                        <div class="hero-stats-bar"></div>
                    </div>
                </div>
                
                <div class="hero-course-count">
                    <span class="count"><?php echo $stats['total_careers']; ?>+</span>
                    <span class="label">Jalur karier tersedia</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted Companies -->
    <section class="trusted-companies">
        <div class="container">
            <h3 class="trusted-title">Dipercaya oleh institusi kesehatan terkemuka</h3>
            <div class="companies-grid">
                <div class="company-logo">RS Sardjito</div>
                <div class="company-logo">RSCM</div>
                <div class="company-logo">Kemenkes RI</div>
                <div class="company-logo">BPJS Kesehatan</div>
                <div class="company-logo">Siloam</div>
            </div>
        </div>
    </section>

    <!-- Popular Courses/Roadmap -->
    <section id="roadmap" class="courses">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Jalur karier populer.</h2>
                <a href="#" class="explore-link" onclick="showAllCareers()">
                    Jelajahi semua jalur <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="courses-grid">
                <?php foreach (array_slice($roadmap_data, 0, 3) as $index => $career): ?>
                <div class="course-card" onclick="showCareerDetail(<?php echo htmlspecialchars(json_encode($career)); ?>)">
                    <div class="course-image">
                        <?php if ($career['image']): ?>
                            <img src="uploads/careers/<?php echo $career['image']; ?>" alt="<?php echo htmlspecialchars($career['position']); ?>">
                        <?php else: ?>
                            <img src="/placeholder.svg?height=200&width=350" alt="<?php echo htmlspecialchars($career['position']); ?>">
                        <?php endif; ?>
                        <div class="best-seller-badge">POPULER</div>
                    </div>
                    <div class="course-content">
                        <h3 class="course-title"><?php echo htmlspecialchars($career['position']); ?></h3>
                        <p class="course-instructor"><?php echo htmlspecialchars($career['level']); ?></p>
                        
                        <div class="course-rating">
                            <span class="rating-score">4.8</span>
                            <div class="rating-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        
                        <div class="course-price"><?php echo htmlspecialchars($career['salary_range'] ?? 'Kompetitif'); ?></div>
                        
                        <div class="course-meta">
                            <div class="course-meta-item">
                                <i class="fas fa-play-circle"></i>
                                <span>5 tahapan</span>
                            </div>
                            <div class="course-meta-item">
                                <i class="fas fa-users"></i>
                                <span><?php echo rand(50, 200); ?> alumni</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Alumni Stories Section -->
    <section id="alumni" class="alumni-stories">
        <div class="container">
            <div class="alumni-header">
                <i class="fas fa-graduation-cap alumni-icon"></i>
                <h2 class="alumni-title">Cerita Alumni</h2>
                <p class="alumni-subtitle">Inspirasi dari pengalaman nyata lulusan Manajemen Informasi Kesehatan yang telah berkarier</p>
            </div>
            
            <div class="alumni-grid" id="alumniGrid">
                <?php foreach ($alumni_data as $alumni): ?>
                <div class="alumni-card">
                    <div class="alumni-profile">
                        <div class="alumni-avatar">
                            <?php if ($alumni['image']): ?>
                                <img src="uploads/alumni/<?php echo $alumni['image']; ?>" alt="<?php echo htmlspecialchars($alumni['alumni_name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <div class="alumni-info">
                            <h4><?php echo htmlspecialchars($alumni['alumni_name']); ?></h4>
                            <p class="graduation-year">Lulusan <?php echo $alumni['graduation_year']; ?></p>
                            <p class="institution"><?php echo htmlspecialchars($alumni['institution']); ?></p>
                            <p class="position"><?php echo htmlspecialchars($alumni['position']); ?></p>
                        </div>
                    </div>
                    <div class="alumni-story">
                        <p><?php echo htmlspecialchars($alumni['story']); ?></p>
                    </div>
                    <?php if ($alumni['job_opportunities']): ?>
                    <div class="job-opportunities">
                        <strong>Lowongan Terkait:</strong>
                        <p><?php echo htmlspecialchars($alumni['job_opportunities']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Share Your Story -->
    <section class="share-story">
        <div class="container">
            <div class="share-story-content">
                <i class="fas fa-heart share-icon"></i>
                <h2 class="share-title">Bagikan Ceritamu</h2>
                <p class="share-subtitle">Cerita yang Anda kirim akan langsung ditampilkan di halaman alumni untuk menginspirasi adik-adik tingkat.</p>
                <button class="btn-share" onclick="showAddStoryModal()">
                    <i class="fas fa-plus"></i> Tambah Cerita
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <div class="footer-logo">MIKPath</div>
                    <p class="footer-description">Platform peta karier untuk mahasiswa dan alumni Manajemen Informasi Kesehatan. Membantu menemukan jalur karier yang tepat di era digital kesehatan.</p>
                    <div class="footer-social">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Links</h4>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#roadmap">Roadmap</a></li>
                        <li><a href="#team">Mentor</a></li>
                        <li><a href="#testimonials">Testimonial</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="dashboard.php">Dashboard</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Other</h4>
                    <ul class="footer-links">
                        <li><a href="#alumni">Alumni</a></li>
                        <li><a href="#team">Our Team</a></li>
                        <li><a href="#roadmap">Career</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <div class="footer-contact">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Jakarta, Indonesia</span>
                    </div>
                    <div class="footer-contact">
                        <i class="fas fa-phone"></i>
                        <span>+62 123 456 789</span>
                    </div>
                    <div class="footer-contact">
                        <i class="fas fa-envelope"></i>
                        <span>info@mikpath.com</span>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy;2024 MIKPath. All Rights Reserved by MIKPath.com</p>
                <div class="footer-bottom-links">
                    <a href="#">Privacy policy</a>
                    <a href="#">Terms & conditions</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Career Detail Modal -->
    <div id="careerDetailModal" class="modal">
        <div class="modal-content career-detail-modal">
            <span class="close" onclick="closeModal('careerDetailModal')">&times;</span>
            <div class="career-detail-header">
                <i class="fas fa-route roadmap-icon"></i>
                <h2>Detail Karier</h2>
            </div>
            <div id="careerDetailContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- All Careers Modal -->
    <div id="allCareersModal" class="modal">
        <div class="modal-content all-careers-modal">
            <span class="close" onclick="closeModal('allCareersModal')">&times;</span>
            <div class="roadmap-header">
                <i class="fas fa-route roadmap-icon"></i>
                <h2>Roadmap Karier</h2>
            </div>
            <p class="roadmap-subtitle">Eksplorasi jalur karier dalam bidang Manajemen Informasi Kesehatan dari entry level hingga expert</p>
            
            <div class="roadmap-grid">
                <?php foreach ($roadmap_data as $index => $career): ?>
                <div class="roadmap-card level-<?php echo $index + 1; ?>">
                    <div class="roadmap-header-card">
                        <h3><?php echo htmlspecialchars($career['level']); ?></h3>
                        <span class="level-number"><?php echo $index + 1; ?></span>
                    </div>
                    <div class="roadmap-content">
                        <h4 class="position-title"><?php echo htmlspecialchars($career['position']); ?></h4>
                        
                        <div class="roadmap-detail">
                            <div class="detail-item">
                                <div class="detail-icon-wrapper">
                                    <i class="fas fa-cogs detail-icon"></i>
                                    <strong>Kompetensi:</strong>
                                </div>
                                <p><?php echo htmlspecialchars($career['competencies']); ?></p>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon-wrapper">
                                    <i class="fas fa-certificate detail-icon"></i>
                                    <strong>Sertifikasi:</strong>
                                </div>
                                <p><?php echo htmlspecialchars($career['certifications']); ?></p>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon-wrapper">
                                    <i class="fas fa-money-bill-wave detail-icon"></i>
                                    <strong>Gaji:</strong>
                                </div>
                                <p><?php echo htmlspecialchars($career['salary_range'] ?? 'Kompetitif'); ?></p>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon-wrapper">
                                    <i class="fas fa-info-circle detail-icon"></i>
                                    <strong>Keterangan:</strong>
                                </div>
                                <p><?php echo htmlspecialchars($career['description']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <?php include 'includes/modals.php'; ?>

    <script src="js/script.js"></script>
    <script>
        // Search functionality
        function searchCareers() {
            const searchTerm = document.getElementById('careerSearch').value;
            if (searchTerm.trim()) {
                // Scroll to roadmap section and filter
                document.getElementById('roadmap').scrollIntoView({ behavior: 'smooth' });
                // Add search functionality here
                console.log('Searching for:', searchTerm);
            }
        }

        // Enter key search
        document.getElementById('careerSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchCareers();
            }
        });

        // Show career detail modal
        function showCareerDetail(career) {
            const modal = document.getElementById('careerDetailModal');
            const content = document.getElementById('careerDetailContent');
            
            const levelColors = {
                'Entry Level': 'linear-gradient(135deg, #10b981, #059669)',
                'Junior': 'linear-gradient(135deg, #3b82f6, #2563eb)',
                'Mid Level': 'linear-gradient(135deg, #f59e0b, #d97706)',
                'Senior': 'linear-gradient(135deg, #ef4444, #dc2626)'
            };
            
            content.innerHTML = `
                <div class="career-detail-card">
                    <div class="career-detail-header-card" style="background: ${levelColors[career.level] || '#6366f1'}">
                        <h3>${career.level}</h3>
                        <span class="level-badge">DETAIL</span>
                    </div>
                    <div class="career-detail-body">
                        <h4 class="career-detail-title">${career.position}</h4>
                        
                        <div class="career-detail-grid">
                            <div class="detail-item">
                                <div class="detail-icon-wrapper">
                                    <i class="fas fa-cogs detail-icon"></i>
                                    <strong>Kompetensi:</strong>
                                </div>
                                <p>${career.competencies}</p>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon-wrapper">
                                    <i class="fas fa-certificate detail-icon"></i>
                                    <strong>Sertifikasi:</strong>
                                </div>
                                <p>${career.certifications}</p>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon-wrapper">
                                    <i class="fas fa-money-bill-wave detail-icon"></i>
                                    <strong>Rentang Gaji:</strong>
                                </div>
                                <p>${career.salary_range || 'Kompetitif'}</p>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon-wrapper">
                                    <i class="fas fa-info-circle detail-icon"></i>
                                    <strong>Keterangan:</strong>
                                </div>
                                <p>${career.description}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            modal.style.display = 'block';
        }

        // Show all careers modal
        function showAllCareers() {
            document.getElementById('allCareersModal').style.display = 'block';
        }

        // Add new alumni story to the grid
        function addAlumniStory(story) {
            const alumniGrid = document.getElementById('alumniGrid');
            const newCard = document.createElement('div');
            newCard.className = 'alumni-card';
            
            newCard.innerHTML = `
                <div class="alumni-profile">
                    <div class="alumni-avatar">
                        ${story.image ? 
                            `<img src="uploads/alumni/${story.image}" alt="${story.alumni_name}">` : 
                            '<i class="fas fa-user"></i>'
                        }
                    </div>
                    <div class="alumni-info">
                        <h4>${story.alumni_name}</h4>
                        <p class="graduation-year">Lulusan ${story.graduation_year}</p>
                        <p class="institution">${story.institution}</p>
                        <p class="position">${story.position}</p>
                    </div>
                </div>
                <div class="alumni-story">
                    <p>${story.story}</p>
                </div>
                ${story.job_opportunities ? `
                    <div class="job-opportunities">
                        <strong>Lowongan Terkait:</strong>
                        <p>${story.job_opportunities}</p>
                    </div>
                ` : ''}
            `;
            
            // Add animation
            newCard.style.opacity = '0';
            newCard.style.transform = 'translateY(20px)';
            alumniGrid.insertBefore(newCard, alumniGrid.firstChild);
            
            // Animate in
            setTimeout(() => {
                newCard.style.transition = 'all 0.5s ease';
                newCard.style.opacity = '1';
                newCard.style.transform = 'translateY(0)';
            }, 100);
        }

        // Close modal function
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>
