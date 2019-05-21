<?php 
print_r($_FILES);#exit; 
include_once('config.php');
 //export.php  
 if(!empty($_FILES["file"]))  
 {
	 $extension = end(explode(".", $_FILES["file"]["name"])); // For getting Extension of selected file
 $allowed_extension = array("xls", "xlsx", "csv"); //allowed extension
 if(in_array($extension, $allowed_extension)) //check selected file extension is present in allowed extension array
 {
  	$file = $_FILES["file"]["tmp_name"]; // getting temporary source of excel file
  	$targetPath =dirname(__FILE__).'/uploads/'.$_FILES['file']['name'];
  	move_uploaded_file($_FILES['excel']['tmp_name'], $targetPath);
	$extension =  strtolower( pathinfo($_FILES['file']['tmp_name'], PATHINFO_EXTENSION) );
echo "file_path:".$targetPath;exit;
 // Excel reader from http://code.google.com/p/php-excel-reader/
	require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
	require('spreadsheet-reader-master/SpreadsheetReader.php');

	date_default_timezone_set('UTC');

	$Spreadsheet = new SpreadsheetReader($_FILES['excel']['tmp_name']);
	print_r($Spreadsheet);exit;
	$BaseMem = memory_get_usage();
	$Sheets = $Spreadsheet -> Sheets();
	foreach ($Sheets as $Index => $Name)
	{
		$Spreadsheet -> ChangeSheet($Index);
			foreach ($Spreadsheet as $Key => $Row)
			{
				 if( ($Name == 'Clear_Sale' && $i < 6) ||  ($Name == 'Incentives' && $i < 5) || empty($Row) )//column name 
                                        continue;
                        #        print_r($Row);exit;
                                        if($Name == 'Clear_Sale'){
                                                $sales_month = mysqli_real_escape_string($connect, date('Y-m-t',strtotime($Row[0])));
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
@                                                $query = "INSERT INTO test.tbl_clear_sales(sales_month,branch,emp_code,emp_name,dept,std_team_name,ecs_cont_count,ecs_cont_amt,nonecs_cont_count,nonecs_cont_amt,ecs_clearance,total_cont_count,total_sales_excl_ecs_accrued,total_sales_incl_ecs_accrued,insert_date) VALUES ('".$sales_month."', '".$branch."','".$emp_code."','".$emp_name."','".$dept."','".$std_team_name."','".$ecs_cont_count."','".$ecs_cont_amt."','".$nonecs_cont_count."','".$nonecs_cont_amt."','".$ecs_clearance."','".$total_cont_count."','".$total_sales_excl_ecs_accrued."','".$total_sales_incl_ecs_accrued."',CURRENT_TIMESTAMP)";
                                                mysqli_query($connect, $query);
                                        }else if($Name == 'Incentives'){
                                                $branch = mysqli_real_escape_string($connect, $Row[0]);
                                                $expense_month = mysqli_real_escape_string($connect,date('Y-m-t',strtotime($Row[1])));
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
echo"<pre> Incentive".                                          $query = "INSERT INTO test.tbl_incentives(branch,expense_month,incentive_type,emp_code,emp_name,incentives,dept,insert_date) VALUES ('".$branch."', '".$expense_month."','".$incentive_type."','".$emp_code."','".$emp_name."','".$incentives."','".$dept."',CURRENT_TIMESTAMP)";
         #                                       mysqli_query($connect, $query);
					}
			}
	}

}else
 {
  $output = '<label class="text-danger">Invalid File</label>'; //if non excel file then
 }  
 }
 ?>  
