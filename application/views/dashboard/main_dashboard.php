<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></title>
    <link href="<?php echo $config['base_url']; ?>assets/vendors/keenicons/styles.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.5;
            height: 100vh;
            overflow: hidden;
        }
        
        .header {
            background: white;
            padding: 12px 40px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 60px;
        }
        
        .logo {
            font-size: 22px;
            font-weight: bold;
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
            font-size: 14px;
        }
        
        .user-role {
            font-size: 12px;
            color: #666;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            background: #1677ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
            font-size: 14px;
        }
        
        .main-content {
            padding: 25px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 120px);
            padding-bottom: 80px;
        }
        
        .welcome-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .welcome-section h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        
        .welcome-section p {
            color: #666;
            font-size: 15px;
        }
        
        .alert {
            background: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #389e0d;
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 14px;
        }
        
        .card-container {
            display: flex;
            justify-content: center;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            position: relative;
            width: 280px;
            height: 240px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            background: #1677ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 20px;
            color: white;
        }
        
        .card h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .card p {
            color: #666;
            margin-bottom: 15px;
            font-size: 13px;
            line-height: 1.4;
        }
        
        .dropdown-btn {
            background: #1677ff;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.3s ease;
        }
        
        .dropdown-btn:hover { 
            background: #0958d9; 
        }
        
        .dropdown-arrow {
            font-size: 10px;
            transition: transform 0.3s ease;
        }
        
        .card-dropdown {
            position: absolute;
            top: 90%;
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
            transition: all 0.3s ease;
            z-index: 1000;
            margin-top: 0;
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
            padding: 10px 12px;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .dropdown-link {
            color: #666;
            text-decoration: none;
            font-weight: 500;
            font-size: 12px;
            transition: color 0.3s ease;
            display: block;
        }
        
        .dropdown-link:hover {
            color: #1677ff;
            text-decoration: none;
        }
        
        .footer {
            background: white;
            padding: 15px 40px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #666;
            font-size: 12px;
            height: 60px;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 999;
        }
        
        .footer a {
            color: #1677ff;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: #0958d9;
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

        <div class="card-container">
            <div class="card">
                <div>
                    <div class="card-icon"><i class="ki-filled ki-cube-2"></i> </div>
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
                            <a href="<?php echo $config['base_url']; ?>inventory/inv_ecct" class="dropdown-link">ECCT</a>
                        </div>
                        <div class="dropdown-item">
                            <a href="<?php echo $config['base_url']; ?>inventory/inv_ecbs" class="dropdown-link">ECBS</a>
                        </div>
                        <div class="dropdown-item">
                            <a href="<?php echo $config['base_url']; ?>inventory/massive_input" class="dropdown-link">Massive Input</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>2025© C-Techlabs x Edwar Medika | System 2.1 | Load Time : 0.13s</div>
        <div><a href="#">Support</a></div>
    </div>
</body>
</html>