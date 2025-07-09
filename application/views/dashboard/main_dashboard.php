<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .header {
            background: white;
            padding: 15px 50px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-details {
            text-align: right;
        }
        
        .user-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .user-role {
            font-size: 12px;
            color: #666;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #1677ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
            font-size: 16px;
        }
        
        .main-content {
            padding: 30px 50px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .welcome-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .welcome-section h1 {
            font-size: 32px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .welcome-section p {
            color: #666;
            font-size: 16px;
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            transition: all 0.3s;
            position: relative;
            min-height: 280px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            background: #1677ff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
            color: white;
        }
        
        .card h3 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
        }
        
        .card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
            font-size: 14px;
        }
        
        .card-btn {
            background: #1677ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: background 0.3s;
            width: 100%;
            display: inline-block;
        }
        
        .card-btn:hover {
            background: #0958d9;
            color: white;
            text-decoration: none;
        }
        
        .dropdown-btn {
            background: #1677ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .dropdown-btn:hover { background: #0958d9; }
        
        .dropdown-arrow {
            font-size: 10px;
            transition: transform 0.3s;
        }
        
        .card-dropdown {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border: 1px solid #e4e6ef;
            border-radius: 6px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateX(-50%) translateY(-10px);
            transition: all 0.3s;
            z-index: 1000;
            margin-top: 8px;
        }
        
        .card-actions:hover .card-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }
        
        .card-actions:hover .dropdown-arrow {
            transform: rotate(180deg);
        }
        
        .dropdown-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .dropdown-link {
            color: #666;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            transition: color 0.3s;
        }
        
        .dropdown-link:hover {
            color: #1677ff;
            text-decoration: none;
        }
        
        .footer {
            background: white;
            padding: 20px 50px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #666;
            font-size: 14px;
        }
        
        .footer a {
            color: #1677ff;
            text-decoration: none;
        }
        
        .alert {
            background: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #389e0d;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .cards-grid { grid-template-columns: 1fr; }
            .header, .main-content { padding: 15px 20px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">C-Techlabs</div>
        <div class="user-info">
            <div class="user-details">
                <div class="user-name"><?php echo $config['adm_fullname']; ?></div>
                <div class="user-role"><?php echo ucfirst($config['adm_access']); ?></div>
            </div>
            <div class="user-avatar"><?php echo strtoupper(substr($config['adm_name'], 0, 1)); ?></div>
        </div>
    </div>

    <div class="main-content">
        <?php if(isset($message)): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="welcome-section">
            <h1>Hai Admin</h1>
            <p>Selamat datang di sistem manajemen data</p>
        </div>

        <div class="cards-grid">
            <div class="card">
                <div>
                    <div class="card-icon">📊</div>
                    <h3>Inventory</h3>
                    <p>Kelola data inventory dan sample management system</p>
                </div>
                <div class="card-actions">
                    <button class="dropdown-btn">
                        Access Inventory
                        <span class="dropdown-arrow">▼</span>
                    </button>
                    <div class="card-dropdown">
                        <div class="dropdown-item">
                            <a href="<?php echo $config['base_url']; ?>inventory/all_item" class="dropdown-link">All Items</a>
                        </div>
                        <div class="dropdown-item">
                            <a href="<?php echo $config['base_url']; ?>inventory/dataitem" class="dropdown-link">ECCT</a>
                        </div>
                        <div class="dropdown-item">
                            <a href="<?php echo $config['base_url']; ?>inventory/inv" class="dropdown-link">ECBS</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div>
                    <div class="card-icon">📈</div>
                    <h3>Data Element</h3>
                    <p>Manajemen elemen data dan konfigurasi sistem</p>
                </div>
                <a href="<?php echo $config['base_url']; ?>dashboard/reports" class="card-btn">View Data</a>
            </div>

            <div class="card">
                <div>
                    <div class="card-icon">🕒</div>
                    <h3>History</h3>
                    <p>Riwayat aktivitas dan log sistem</p>
                </div>
                <a href="<?php echo $config['base_url']; ?>dashboard/settings" class="card-btn">View History</a>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>2025© C-Techlabs x Edwar Medika | System 2.1 | Load Time : 0.13s</div>
        <div><a href="#">Support</a></div>
    </div>
</body>
</html>
