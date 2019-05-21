<?php
include_once('config.php');
#print_r($_FILES);
#print_r($_POST);#exit;
#echo "TATAT";exit;
if(isset($_POST["Import"]))
{
#print_r($_POST);
#print_r($_FILES);#exit;
 $extension = end(explode(".", $_FILES["file"]["name"])); // For getting Extension of selected file
 $allowed_extension = array("xls", "xlsx", "csv"); //allowed extension
 if(in_array($extension, $allowed_extension)) //check selected file extension is present in allowed extension array
 {
   	$file = $_FILES["file"]["tmp_name"]; // getting temporary source of excel file
   	$targetPath =dirname(__FILE__).'/uploads/'.$_FILES['file']['name'];
#echo "TATAT";exit;
  	move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
	$extension =  strtolower( pathinfo($_FILES['file']['tmp_name'], PATHINFO_EXTENSION) );
##########################EXCEL##########################################
/*	require_once 'PHPExcel/Classes/PHPExcel.php';

	require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';



$objPHPExcel = PHPExcel_IOFactory::load($targetPath);
print_r($objPHPExcel);exit;
*/
##########################EXCELEND###############################################
echo "TATAT".$targetPath;exit;

 // Excel reader from http://code.google.com/p/php-excel-reader/
	require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
	require('spreadsheet-reader-master/SpreadsheetReader.php');

	date_default_timezone_set('UTC');
	$connect =  getdb(); 
	$Spreadsheet = new SpreadsheetReader($targetPath);
	$BaseMem = memory_get_usage();
	$Sheets = $Spreadsheet -> Sheets();
	
	foreach ($Sheets as $Index => $Name)
	{
		$Spreadsheet -> ChangeSheet($Index);
		$i=1;
		$j=0;
		#print_r($Spreadsheet);exit;
			foreach ($Spreadsheet as $Key => $Row)
			{ #echo "<pre>".$Key;print_r($Row);continue;
				$i++;
			#echo count($Row);continue;
/*
				if($i==10000){
					print_r($clear_sales_array);
					print_r($incentives_array);exit;
				}
*/
				if( ($Name == 'Clear_Sale' && $i < 6) ||  ($Name == 'Incentives' && $i < 5) || empty($Row) )//column name 
					continue;
			#	 print_r($Row);exit;
					if($Name == 'Clear_Sale'){
						$date = $Row[0];
						$date = DateTime::createFromFormat("d/m/Y" , $date);
						$sales_month = mysqli_real_escape_string($connect,$date->format('Y-m-d'));
				 		$branch = mysqli_real_escape_string($connect, $Row[1]);
				 		$emp_code = mysqli_real_escape_string($connect, $Row[2]);
   				 		$emp_name = mysqli_real_escape_string($connect,$Row[3]);
				 		$dept = mysqli_real_escape_string($connect, $Row[4]);
				 		$std_team_name = mysqli_real_escape_string($connect, $Row[5]);
				 		$ecs_cont_count = mysqli_real_escape_string($connect, $Row[6]);
						$ecs_cont_amt = mysqli_real_escape_string($connect, $Row[7]);
						$nonecs_cont_count = mysqli_real_escape_string($connect, $Row[8]);
						$nonecs_cont_amt = mysqli_real_escape_string($connect, $Row[9]);
						$ecs_clearance = mysqli_real_escape_string($connect, $Row[10]);
						$total_cont_count = mysqli_real_escape_string($connect, $Row[11]);
						$total_sales_excl_ecs_accrued = mysqli_real_escape_string($connect, $Row[12]);
						$total_sales_incl_ecs_accrued = mysqli_real_escape_string($connect, $Row[13]);
						$clear_sales_array[$j++] = array("id"=>$j,
									"sales_month"=>$sales_month,
									"branch"=>$branch,
									"emp_code"=>$emp_code,
									"emp_name"=>$emp_name,
									"dept"=>$dept,
									"std_team_name"=>$std_team_name,
									"ecs_cont_count"=>$ecs_cont_count,
									"ecs_cont_amt"=>$ecs_cont_amt,
									"nonecs_cont_count"=>$nonecs_cont_count,
									"nonecs_cont_amt"=>$nonecs_cont_amt,
									"ecs_clearance"=>$ecs_clearance,
									"total_cont_count"=>$total_cont_count,
									"total_sales_excl_ecs_accrued"=>$total_sales_excl_ecs_accrued,
									"total_sales_incl_ecs_accrued"=>$total_sales_incl_ecs_accrued,
								    );
						##SSO##
						$doj = mysqli_real_escape_string($connect, $Row[14]);
						$dol = mysqli_real_escape_string($connect, $Row[15]);
						$status = mysqli_real_escape_string($connect, $Row[16]);
						$nwd = mysqli_real_escape_string($connect, $Row[17]);		
						##SSO##
						$query = "INSERT INTO test.tbl_clear_sales(sales_month,branch,emp_code,emp_name,dept,std_team_name,ecs_cont_count,ecs_cont_amt,nonecs_cont_count,nonecs_cont_amt,ecs_clearance,total_cont_count,total_sales_excl_ecs_accrued,total_sales_incl_ecs_accrued,insert_date) VALUES ('".$sales_month."', '".$branch."','".$emp_code."','".$emp_name."','".$dept."','".$std_team_name."','".$ecs_cont_count."','".$ecs_cont_amt."','".$nonecs_cont_count."','".$nonecs_cont_amt."','".$ecs_clearance."','".$total_cont_count."','".$total_sales_excl_ecs_accrued."','".$total_sales_incl_ecs_accrued."',CURRENT_TIMESTAMP)";
                                                mysqli_query($connect, $query);
					}else if($Name == 'Incentives'){
						$branch = mysqli_real_escape_string($connect, $Row[0]);
						$date = $Row[1];
                                                $date = DateTime::createFromFormat("d/m/Y" , $date);
						$expense_month = mysqli_real_escape_string($connect,$date->format('Y-m-d'));
						$incentive_type = mysqli_real_escape_string($connect, $Row[2]);
						$emp_code = mysqli_real_escape_string($connect, $Row[3]);
						$emp_name = mysqli_real_escape_string($connect, $Row[4]);
						$incentives = mysqli_real_escape_string($connect, $Row[5]);
						$dept = mysqli_real_escape_string($connect, $Row[6]);
						$incentives_array[$j++]= array("id"=>$j,
										"branch"=>$branch,
										"expense_month"=>$expense_month,
										"incentive_type"=>$incentive_type,
										"emp_code"=>$emp_code,
										"emp_name"=>$emp_name,
										"incentives"=>$incentives,
										"dept"=>$dept
										
										);
						$query = "INSERT INTO test.tbl_incentives(branch,expense_month,incentive_type,emp_code,emp_name,incentives,dept,insert_date) VALUES ('".$branch."', '".$expense_month."','".$incentive_type."','".$emp_code."','".$emp_name."','".$incentives."','".$dept."',CURRENT_TIMESTAMP)";
                                 		mysqli_query($connect, $query);


					}
			#echo"<pre>".	 $query = "INSERT INTO test.tbl_excel(excel_name, excel_email) VALUES ('".$name."', '".$email."')";
    				 #mysqli_query($connect, $query);

			}
	}

#echo"<pre>clear_sales_array:".print_r($clear_sales_array);
#echo"<pre>incentives_array:". print_r($incentives_array);
$output = '<label class="text-danger">Data Uploaded Sucessfully </label>'; //if non excel file then
header('Location: index.php');
}else
 {
  $output = '<label class="text-danger">Invalid File</label>'; //if non excel file then
 }
}

function get_all_records(){

    $con = getdb();
    $Sql = "SELECT * FROM employeeinfo";
    $result = mysqli_query($con, $Sql);  


    if (mysqli_num_rows($result) > 0) {
     echo "<div class='table-responsive'><table id='myTable' class='table table-striped table-bordered'>
             <thead><tr><th>EMP ID</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Email</th>
                          <th>Registration Date</th>
                        </tr></thead><tbody>";


     while($row = mysqli_fetch_assoc($result)) {

         echo "<tr><td>" . $row['emp_id']."</td>
                   <td>" . $row['firstname']."</td>
                   <td>" . $row['lastname']."</td>
                   <td>" . $row['email']."</td>
                   <td>" . $row['reg_date']."</td></tr>";        
     }
    
     echo "</tbody></table></div>";
     
} else {
     echo "you have no records";
}
}

 if(isset($_POST["Export"])){
		 
      header('Content-Type: text/csv; charset=utf-8');  
      header('Content-Disposition: attachment; filename=data.csv');  
      $output = fopen("php://output", "w");  
      fputcsv($output, array('ID', 'First Name', 'Last Name', 'Email', 'Joining Date'));  
      $query = "SELECT * from employeeinfo ORDER BY emp_id DESC";  
      $result = mysqli_query($con, $query);  
      while($row = mysqli_fetch_assoc($result))  
      {  
           fputcsv($output, $row);  
      }  
      fclose($output);  
 }  
 ?>
