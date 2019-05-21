<?php 
 
$servername = "192.168.12.219";
$username = "laljiy";
$password = "L@lj!y";
$dbname = "test";
 
// Create connection
 
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} 
 
echo "connected";
 
#use spout\src\Spout\Reader\ReaderFactory;
#use spout\src\Spout\Common\Type;
 
// Include Spout library 
require_once 'spout/src/Spout/Autoloader/autoload.php';
 print_r($_FILES);exit;
// check file name is not empty
if (!empty($_FILES['file']['name'])) {
      
    // Get File extension eg. 'xlsx' to check file is excel sheet
    $pathinfo = pathinfo($_FILES["file"]["name"]);
     
    // check file has extension xlsx, xls and also check 
    // file is not empty
   if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') 
           && $_FILES['file']['size'] > 0 ) {
         
        // Temporary file name
        $inputFileName = $_FILES['file']['tmp_name']; 
    
        // Read excel file by using ReadFactory object.
        $reader = ReaderFactory::create(Type::XLSX);
	print_r($reader); 
        // Open file
        $reader->open($inputFileName);
        $count = 1;
 
        // Number of sheet in excel file
        foreach ($reader->getSheetIterator() as $sheet) {
             
            // Number of Rows in Excel sheet
            foreach ($sheet->getRowIterator() as $row) {
 
                // It reads data after header. In the my excel sheet, 
                // header is in the first row. 
                if ($count > 1) { 
 
                    // Data of excel sheet
                    $data['name'] = $row[0];
                    $data['email'] = $row[1];
                    $data['phone'] = $row[2];
                    $data['city'] = $row[3];
                     
                    //Here, You can insert data into database. 
                    print_r(data);
                     
                }
                $count++;
            }
        }
 
        // Close excel file
        $reader->close();
 
    } else {
 
        echo "Please Select Valid Excel File";
    }
 
} else {
 
    echo "Please Select Excel File";
     
}
?>
