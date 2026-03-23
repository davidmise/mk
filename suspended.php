<?php
// Configuration
$siteName = "MK Hotel";
$domain = "mkhotel.co.tz";
$providerName = "Pamoja Inc";
$providerUrl = "https://pamojainc.co.tz";
$currentYear = date("Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Notice | <?php echo $siteName; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            max-width: 500px;
            width: 90%;
        }
        .icon {
            font-size: 50px;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        p {
            line-height: 1.6;
            color: #666;
            margin-bottom: 25px;
        }
        .status-badge {
            display: inline-block;
            background: #fff5f5;
            color: #c53030;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: bold;
            border: 1px solid #feb2b2;
            text-transform: uppercase;
        }
        .btn-renew {
            display: inline-block;
            margin-top: 10px;
            padding: 12px 24px;
            background-color: #2c3e50;
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-renew:hover {
            background-color: #34495e;
        }
        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #999;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="icon">⚠️</div>
    <h1>Website Temporarily Unavailable</h1>
    <div class="status-badge">Service Period Expired</div>

    <p>
        The hosting services for <strong><?php echo $domain; ?></strong> have reached their expiration date.
        Access to the website has been restricted by the service provider.
    </p>

    <p>If you are the administrator, please renew your hosting subscription with <strong><?php echo $providerName; ?></strong> to restore services immediately.</p>

    <a href="<?php echo $providerUrl; ?>" class="btn-renew" target="_blank">Contact Provider</a>

    <div class="footer">
        &copy; <?php echo $currentYear; ?> <?php echo $siteName; ?> Management
    </div>
</div>

</body>
</html>
