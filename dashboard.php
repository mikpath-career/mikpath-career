<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();

// Get statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM career_roadmap) as total_careers,
    (SELECT COUNT(*) FROM alumni_stories WHERE status = 'approved') as total_stories,
    (SELECT COUNT(*) FROM alumni_stories WHERE status = 'pending') as pending_stories,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM alumni_stories WHERE DATE(created_at) = CURDATE()) as today_stories";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get recent alumni stories
$recent_stories_query = "SELECT * FROM alumni_stories ORDER BY created_at DESC LIMIT 5";
$recent_stories_stmt = $db->prepare($recent_stories_query);
$recent_stories_stmt->execute();
$recent_stories = $recent_stories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MIKPath</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            margin-top: 80px;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .dashboard-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: var(--white);
            padding: 1rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--gray-color);
            font-weight: 500;
        }
        
        .dashboard-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .action-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }
        
        .action-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .recent-stories {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }
        
        .recent-stories h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .story-item {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .story-item:last-child {
            border-bottom: none;
        }
        
        .story-info h4 {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }
        
        .story-meta {
            font-size: 0.875rem;
            color: var(--gray-color);
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <i class="fas fa-route"></i>
                <span>MIKPath Dashboard</span>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-link">Beranda</a>
                <a href="manage_careers.php" class="nav-link">Kelola Karier</a>
                <a href="manage_stories.php" class="nav-link">Kelola Cerita</a>
                <?php if (isAdmin()): ?>
                    <a href="manage_users.php" class="nav-link">Kelola User</a>
                <?php endif; ?>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">Dashboard MIKPath</h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php if ($_SESSION['profile_image']): ?>
                        <img src="uploads/<?php echo $_SESSION['profile_image']; ?>" alt="Profile">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                    <div style="font-size: 0.875rem; color: var(--gray-color);"><?php echo ucfirst($_SESSION['role']); ?></div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="color: var(--primary-color);">
                    <i class="fas fa-route"></i>
                </div>
                <div class="stat-number"><?php echo $stats['total_careers']; ?></div>
                <div class="stat-label">Total Karier</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="color: var(--success-color);">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-number"><?php echo $stats['total_stories']; ?></div>
                <div class="stat-label">Cerita Alumni</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="color: var(--warning-color);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $stats['pending_stories']; ?></div>
                <div class="stat-label">Menunggu Persetujuan</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="color: var(--secondary-color);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label">Total Pengguna</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="color: var(--success-color);">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-number"><?php echo $stats['today_stories']; ?></div>
                <div class="stat-label">Cerita Hari Ini</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-actions">
            <div class="action-card">
                <h3><i class="fas fa-route"></i> Kelola Karier</h3>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="showAddCareerModal()">
                        <i class="fas fa-plus"></i> Tambah Karier Baru
                    </button>
                    <a href="manage_careers.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Lihat Semua Karier
                    </a>
                </div>
            </div>
            
            <div class="action-card">
                <h3><i class="fas fa-graduation-cap"></i> Kelola Cerita Alumni</h3>
                <div class="action-buttons">
                    <a href="manage_stories.php?status=pending" class="btn btn-warning">
                        <i class="fas fa-clock"></i> Review Cerita (<?php echo $stats['pending_stories']; ?>)
                    </a>
                    <a href="manage_stories.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Lihat Semua Cerita
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Stories -->
        <div class="recent-stories">
            <h3><i class="fas fa-history"></i> Cerita Alumni Terbaru</h3>
            <?php if (empty($recent_stories)): ?>
                <p style="text-align: center; color: var(--gray-color); padding: 2rem;">
                    Belum ada cerita alumni yang tersedia.
                </p>
            <?php else: ?>
                <?php foreach ($recent_stories as $story): ?>
                <div class="story-item">
                    <div class="story-info">
                        <h4><?php echo htmlspecialchars($story['alumni_name']); ?></h4>
                        <div class="story-meta">
                            <?php echo htmlspecialchars($story['institution']); ?> • 
                            <?php echo htmlspecialchars($story['position']); ?> • 
                            <?php echo date('d M Y', strtotime($story['created_at'])); ?>
                        </div>
                    </div>
                    <span class="status-badge status-<?php echo $story['status']; ?>">
                        <?php echo ucfirst($story['status']); ?>
                    </span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include modals -->
    <?php include 'includes/modals.php'; ?>

    <script src="js/script.js"></script>
    <script>
        // Dashboard specific JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Show welcome message
            setTimeout(() => {
                showNotification(`Selamat datang di dashboard, <?php echo $_SESSION['username']; ?>! 👋`, 'success');
            }, 1000);
            
            // Auto-refresh stats every 30 seconds
            setInterval(refreshStats, 30000);
        });
        
        function refreshStats() {
            fetch('api/get_stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateStatsDisplay(data.stats);
                    }
                })
                .catch(error => {
                    console.log('Failed to refresh stats');
                });
        }
        
        function updateStatsDisplay(stats) {
            document.querySelector('.stat-card:nth-child(1) .stat-number').textContent = stats.total_careers;
            document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = stats.total_stories;
            document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = stats.pending_stories;
            document.querySelector('.stat-card:nth-child(4) .stat-number').textContent = stats.total_users;
        }
    </script>
</body>
</html>
