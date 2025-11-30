<?php
/**
 * Certificate Generator
 * Generates printable certificate based on request ID
 */

require_once 'db.php';

// Get request ID
$requestID = isset($_GET['requestID']) ? (int)$_GET['requestID'] : 0;

if ($requestID <= 0) {
    die('Invalid request ID.');
}

try {
    // Fetch certificate request with all related data
    $stmt = $pdo->prepare("
        SELECT 
            cr.requestID,
            cr.purpose,
            cr.date_requested,
            r.residentID,
            r.first_name,
            r.last_name,
            r.birthdate,
            r.gender,
            r.civil_status,
            r.address,
            b.barangayID,
            b.barangayName,
            ct.cert_types
        FROM cert_requests cr
        INNER JOIN residents r ON cr.residentID = r.residentID
        INNER JOIN barangays b ON r.barangayID = b.barangayID
        INNER JOIN cert_type ct ON cr.cert_typeID = ct.cert_typeID
        WHERE cr.requestID = :requestID
    ");
    
    $stmt->bindParam(':requestID', $requestID, PDO::PARAM_INT);
    $stmt->execute();
    
    $data = $stmt->fetch();
    
    if (!$data) {
        die('Certificate request not found.');
    }
    
    // Calculate age
    $birthDate = new DateTime($data['birthdate']);
    $today = new DateTime();
    $age = $birthDate->diff($today)->y;
    
    // Format date
    $dateRequested = new DateTime($data['date_requested']);
    $formattedDate = $dateRequested->format('jS \d\a\y \o\f F, Y');
    
    // Check for barangay logo
    $logoPath = "img/Nagcarlan_Laguna_seal_logo/{$data['barangayID']}.png";
    if (!file_exists($logoPath)) {
        $logoPath = "img/Nagcarlan_Laguna_seal_logo.png";
    }
    
    // Get full name
    $fullName = htmlspecialchars($data['first_name'] . ' ' . $data['last_name']);
    $barangayName = htmlspecialchars($data['barangayName']);
    $certType = htmlspecialchars($data['cert_types']);
    $purpose = htmlspecialchars($data['purpose']);
    $address = htmlspecialchars($data['address']);
    
} catch (PDOException $e) {
    error_log("Error generating certificate: " . $e->getMessage());
    die('Failed to generate certificate.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $certType; ?> - <?php echo $fullName; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .certificate-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .certificate {
            border: 8px double #000;
            padding: 30px;
            position: relative;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .republic {
            font-size: 14px;
            margin-bottom: 3px;
        }
        
        .province {
            font-size: 13px;
            margin-bottom: 3px;
        }
        
        .municipality {
            font-size: 13px;
            margin-bottom: 8px;
        }
        
        .barangay-name {
            font-family: 'Brush Script MT', cursive;
            font-size: 32px;
            font-weight: bold;
            color: #1a5490;
            margin-bottom: 20px;
        }
        
        .divider {
            border-bottom: 2px solid #000;
            margin: 15px 50px;
        }
        
        .office-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 25px 0;
            letter-spacing: 2px;
        }
        
        .cert-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin: 25px 0;
            letter-spacing: 3px;
        }
        
        .to-whom {
            font-size: 14px;
            font-weight: bold;
            margin: 30px 0 20px 0;
        }
        
        .content {
            text-align: justify;
            line-height: 2;
            font-size: 14px;
            margin: 20px 0;
        }
        
        .name-field {
            display: inline-block;
            min-width: 250px;
            border-bottom: 1px solid #000;
            text-align: center;
            font-weight: bold;
            font-size: 15px;
        }
        
        .age-field {
            display: inline-block;
            min-width: 60px;
            border-bottom: 1px solid #000;
            text-align: center;
            font-weight: bold;
        }
        
        .issued-section {
            margin-top: 30px;
            font-size: 14px;
        }
        
        .signature-section {
            margin-top: 50px;
            text-align: right;
        }
        
        .signature-line {
            display: inline-block;
            min-width: 250px;
            border-bottom: 2px solid #000;
            margin-bottom: 5px;
        }
        
        .captain-title {
            font-weight: bold;
            font-size: 14px;
        }
        
        .footer {
            margin-top: 40px;
            font-size: 12px;
        }
        
        .footer-line {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        
        .print-button {
            text-align: center;
            margin: 20px 0;
        }
        
        .print-button button {
            background: #1a5490;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        
        .print-button button:hover {
            background: #0d3a6f;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .certificate-container {
                box-shadow: none;
                padding: 0;
            }
            
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()">🖨️ Print Certificate</button>
    </div>
    
    <div class="certificate-container">
        <div class="certificate">
            <div class="header">
                <div class="logo">
                    <img src="<?php echo $logoPath; ?>" alt="Barangay Logo">
                </div>
                <div class="republic">Republic of the Philippines</div>
                <div class="province">Province of Albay</div>
                <div class="municipality">Municipality of Sto. Domingo</div>
                <div class="barangay-name">Barangay <?php echo $barangayName; ?></div>
            </div>
            
            <div class="divider"></div>
            
            <div class="office-title">OFFICE OF THE BARANGAY CAPTAIN</div>
            
            <div class="cert-title"><?php echo strtoupper($certType); ?></div>
            
            <div class="to-whom">TO WHOM IT MAY CONCERN:</div>
            
            <div class="content">
                <p style="text-indent: 50px;">
                    This is to certify that 
                    <span class="name-field"><?php echo $fullName; ?></span>, 
                    <span class="age-field"><?php echo $age; ?></span> years old, 
                    and a resident of <?php echo $address ? $address . ', ' : ''; ?>Barangay <?php echo $barangayName; ?>, Sto. Domingo, Albay is known to be of good moral character and law-abiding citizen in the community.
                </p>
            </div>
            
            <div class="content">
                <p style="text-indent: 50px;">
                    To certify further, that he/she has no derogatory and/or criminal records filed in this barangay.
                </p>
            </div>
            
            <div class="issued-section">
                <p>
                    <strong>ISSUED</strong> this <?php echo $formattedDate; ?> at Barangay <?php echo $barangayName; ?>, Sto. Domingo, Albay upon request of the interested party for <strong><?php echo strtolower($purpose); ?></strong>.
                </p>
            </div>
            
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="captain-title">Barangay Captain</div>
            </div>
            
            <div class="footer">
                <div class="footer-line">
                    <span>O.R No. ___________________</span>
                </div>
                <div class="footer-line">
                    <span>Date Issued: ___________________</span>
                </div>
                <div class="footer-line">
                    <span>Doc. Stamp: Paid</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="print-button">
        <button onclick="window.print()">🖨️ Print Certificate</button>
    </div>
</body>
</html>