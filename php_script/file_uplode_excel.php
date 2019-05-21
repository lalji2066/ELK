<?php
include_once('config.php');
#$connect = mysqli_connect("", "root", "", "test");
$connect = getdb();
$output = '';
if(isset($_POST["import"]))
{
#print_r($_POST);
#print_r($_FILES);exit;
 $extension = end(explode(".", $_FILES["excel"]["name"])); // For getting Extension of selected file
 $allowed_extension = array("xls", "xlsx", "csv"); //allowed extension
 if(in_array($extension, $allowed_extension)) //check selected file extension is present in allowed extension array
 {
  	$file = $_FILES["excel"]["tmp_name"]; // getting temporary source of excel file
  	$targetPath =dirname(__FILE__).'/uploads/'.$_FILES['excel']['name'];
  	move_uploaded_file($_FILES['excel']['tmp_name'], $targetPath);
	$extension =  strtolower( pathinfo($_FILES['excel']['tmp_name'], PATHINFO_EXTENSION) );
#echo "TATAT";exit;
 // Excel reader from http://code.google.com/p/php-excel-reader/
	require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
	require('spreadsheet-reader-master/SpreadsheetReader.php');

	date_default_timezone_set('UTC');

	$Spreadsheet = new SpreadsheetReader($targetPath);
	$BaseMem = memory_get_usage();
	$Sheets = $Spreadsheet -> Sheets();
	foreach ($Sheets as $Index => $Name)
	{
		$Spreadsheet -> ChangeSheet($Index);
			foreach ($Spreadsheet as $Key => $Row)
			{
				print_r($Row);exit;
				 $name = mysqli_real_escape_string($connect, $Row[0]);
   				 $email = mysqli_real_escape_string($connect,$Row[1]);
			echo	 $query = "INSERT INTO test.tbl_excel(excel_name, excel_email) VALUES ('".$name."', '".$email."')";
  #  				 mysqli_query($connect, $query);

			}
	}
	
}else
 {
  $output = '<label class="text-danger">Invalid File</label>'; //if non excel file then
 }
}
?>

<html>
 <head>
  <title>Import Excel to Mysql using PHPExcel in PHP</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
  <style>
  body
  {
   margin:0;
   padding:0;
   background-color:#f1f1f1;
  }
  .box
  {
   width:700px;
   border:1px solid #ccc;
   background-color:#fff;
   border-radius:5px;
   margin-top:100px;
  }
  
  </style>
 </head>
 <body>
  <div class="container box">
   <h3 align="center">Import Excel to Mysql</h3><br />
   <form method="post" enctype="multipart/form-data">

    <label>Select Excel File</label>
    <input type="file" name="excel" />
    <br />
    <input type="submit" name="import" class="btn btn-info" value="Import" />
   </form>
   <br />
   <br />
   <?php
   echo $output;
   ?>
  </div>
 </body>
</html>
