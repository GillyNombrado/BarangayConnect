Barangay Certificate Generator
A complete, secure certificate generation system for Barangay offices using PHP, MySQL (PDO), JavaScript, and CSS.

🚀 Features
✅ Secure database access using PDO with prepared statements
✅ Real-time resident name autocomplete
✅ Barangay-specific logos and certificates
✅ Common purpose suggestions based on certificate type
✅ Professional, printable certificate design
✅ Modern, responsive UI
✅ Input validation and XSS prevention
✅ JSON API endpoints
📁 File Structure
project-root/
│
├── index.html                          # Main certificate request form
├── db.php                              # Database connection (PDO)
├── generate_certificate.php            # Certificate generator page
│
├── api/
│   ├── get_barangays.php              # Fetch barangays
│   ├── get_cert_types.php             # Fetch certificate types
│   ├── search_residents.php           # Search residents (autocomplete)
│   ├── get_common_purposes.php        # Fetch common purposes
│   └── submit_request.php             # Submit certificate request
│
├── assets/
│   ├── css/
│   │   └── cert-generator.css         # Stylesheet
│   ├── js/
│   │   └── cert-generator.js          # Frontend JavaScript
│   └── logos/
│       ├── 1.png                      # Barangay ID 1 logo
│       ├── 2.png                      # Barangay ID 2 logo
│       ├── ...
│       └── default.png                # Fallback logo
│
└── README.md                          # This file
🔧 Installation
1. Database Setup
The database bcdb and tables should already exist based on your schema. If not, run the CREATE TABLE statements provided in your requirements document.

2. Configure Database Connection
Edit db.php and update the database credentials:

php
define('DB_HOST', 'localhost');
define('DB_NAME', 'bcdb');
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');               // Your MySQL password
IMPORTANT: For production, use environment variables or a secure config file outside the web root.

3. Add Barangay Logos
Place barangay logo images in the assets/logos/ directory with the following naming convention:

assets/logos/{barangayID}.png
Examples:

Barangay ID 1 (Poblacion I) → assets/logos/1.png
Barangay ID 2 (Poblacion II) → assets/logos/2.png
Barangay ID 3 (Poblacion III) → assets/logos/3.png
etc.
Fallback Logo:

Create assets/logos/default.png as a fallback image when a barangay-specific logo is missing.
Recommended Image Specs:

Format: PNG with transparent background
Size: 200x200 pixels (square)
Resolution: 72-150 DPI
4. Set Permissions
Ensure the web server has read access to all files:

bash
chmod -R 755 /path/to/project
5. Access the Application
Open your browser and navigate to:

http://localhost/your-project-folder/index.html
Or if using a virtual host:

http://your-domain.local
📡 API Endpoints
All API endpoints return JSON responses in this format:

json
{
  "success": true|false,
  "message": "Human-readable message",
  "data": { ... }
}
GET /api/get_barangays.php
Returns list of all barangays.

Response:

json
{
  "success": true,
  "data": [
    {"barangayID": 1, "barangayName": "Poblacion I"},
    {"barangayID": 2, "barangayName": "Poblacion II"}
  ]
}
GET /api/get_cert_types.php
Returns list of certificate types.

Response:

json
{
  "success": true,
  "data": [
    {"cert_typeID": 1, "cert_types": "Barangay Certificate"},
    {"cert_typeID": 2, "cert_types": "Certificate of Residency"}
  ]
}
GET /api/search_residents.php
Search residents by name with optional barangay filter.

Parameters:

q (required) - Search query (min 2 characters)
barangayID (optional) - Filter by barangay ID
Example:

GET /api/search_residents.php?q=mik&barangayID=1
Response:

json
{
  "success": true,
  "data": [
    {
      "residentID": 1,
      "first_name": "Mikayla Aspen",
      "last_name": "Ortiona",
      "birthdate": "2005-05-24",
      "address": "Cariaga",
      "barangayID": 1,
      "barangayName": "Poblacion I"
    }
  ]
}
GET /api/get_common_purposes.php
Get most commonly used purposes for a certificate type.

Parameters:

cert_typeID (required) - Certificate type ID
Example:

GET /api/get_common_purposes.php?cert_typeID=1
Response:

json
{
  "success": true,
  "data": [
    {"purpose": "To prove that I have no pending cases", "usage_count": 5},
    {"purpose": "For visa application", "usage_count": 3}
  ]
}
POST /api/submit_request.php
Submit a certificate request.

Content-Type: application/json

Request Body:

json
{
  "residentID": 1,
  "cert_typeID": 1,
  "purpose": "For employment purposes",
  "barangayID": 1
}
Response (Success):

json
{
  "success": true,
  "message": "Certificate request submitted successfully.",
  "data": {
    "requestID": 9,
    "certificateURL": "../generate_certificate.php?requestID=9"
  }
}
Response (Error):

json
{
  "success": false,
  "message": "Please select a valid resident."
}
🔒 Security Features
PDO with Prepared Statements - All database queries use parameterized statements to prevent SQL injection
Input Validation - Server-side validation for all inputs
Output Escaping - All user data is escaped using htmlspecialchars() to prevent XSS
Type Casting - Integer inputs are explicitly cast to integers
Error Logging - Errors are logged securely without exposing sensitive information to users
No Hardcoded Passwords - Database credentials should be stored in environment variables
🖨️ Certificate Generation
The generate_certificate.php file creates a printable certificate with:

Barangay-specific logo and name
Resident's full name and age
Certificate type as title
Purpose of request
Official formatting matching Philippine barangay certificates
Print-friendly CSS
To generate a certificate manually:

http://localhost/your-project/generate_certificate.php?requestID=1
📱 How to Use
Select Barangay - Choose the barangay from the dropdown. The logo and name will display.
Search Resident - Start typing a resident's name. Autocomplete suggestions will appear.
Select Certificate Type - Choose the type of certificate needed.
Enter Purpose - Type the purpose or click a suggested common purpose.
Submit - Click "Generate Certificate" to create the request and view the certificate.
Print - The certificate opens in a new tab with a print button.
🎨 Customization
Change Colors
Edit assets/css/cert-generator.css and update the color variables:

css
/* Primary brand color */
background: linear-gradient(135deg, #1a5490 0%, #2a6bb0 100%);

/* Change to your preferred color */
background: linear-gradient(135deg, #YOUR_COLOR 0%, #YOUR_COLOR_LIGHT 100%);
Modify Certificate Template
Edit generate_certificate.php to change:

Header text (Republic, Province, Municipality)
Certificate body text
Footer content
Styling
Add More Fields
To add more fields to the form:

Add HTML input in index.html
Update JavaScript in cert-generator.js to capture the field
Update api/submit_request.php to accept and validate the field
Modify database schema if storing in database
Update generate_certificate.php to display the new field
🐛 Troubleshooting
Issue: Barangay logos not showing

Verify logo files exist in assets/logos/ with correct names (e.g., 1.png, 2.png)
Check file permissions (should be readable by web server)
Ensure default.png exists as fallback
Issue: Database connection failed

Verify MySQL credentials in db.php
Check if MySQL service is running
Ensure database bcdb exists
Verify user has proper permissions
Issue: Autocomplete not working

Check browser console for JavaScript errors
Verify API endpoint paths are correct
Ensure residents exist in database
Check network tab for failed requests
Issue: Certificate not generating

Verify generate_certificate.php is accessible
Check if requestID exists in database
Review PHP error logs for issues
Ensure all foreign key relationships are valid
📝 SQL Query Examples
Here are the main SQL queries used by the system:

Get Barangays
sql
SELECT barangayID, barangayName FROM barangays ORDER BY barangayName;
Get Certificate Types
sql
SELECT cert_typeID, cert_types FROM cert_type ORDER BY cert_types;
Search Residents
sql
SELECT 
    r.residentID, 
    r.first_name, 
    r.last_name, 
    r.birthdate, 
    r.address,
    r.barangayID,
    b.barangayName
FROM residents r
LEFT JOIN barangays b ON r.barangayID = b.barangayID
WHERE (
    CONCAT(r.first_name, ' ', r.last_name) LIKE :q 
    OR r.first_name LIKE :q 
    OR r.last_name LIKE :q
)
AND r.barangayID = :barangayID
ORDER BY r.first_name, r.last_name 
LIMIT 15;
Insert Certificate Request
sql
INSERT INTO cert_requests (residentID, cert_typeID, purpose, status, date_requested) 
VALUES (:residentID, :cert_typeID, :purpose, 'Approved', NOW());
Get Certificate Data
sql
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
WHERE cr.requestID = :requestID;
📞 Support
For issues or questions:

Check the troubleshooting section above
Review PHP error logs: /var/log/apache2/error.log or /var/log/php/error.log
Check browser console for JavaScript errors
Verify all file paths and permissions
📄 License
This system is provided as-is for barangay office use.

Version: 1.0
Last Updated: December 2024

