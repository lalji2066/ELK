 <html>  
      <head>  
           <title>PHPExcel - Export Excel file into Mysql Database using Ajax</title>  
           <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
           <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
           <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>  
           <style>  
                body  
                {  
                     margin:0;  
                     padding:0;  
                     background-color:#f1f1f1;  
                }  
                .box  
                {  
                     width:900px;  
                     padding:20px;  
                     background-color:#fff;  
                     border:1px solid #ccc;  
                     border-radius:5px;  
                     margin-top:100px;  
                }  
           </style>  
      </head>  
      <body>
    <span id="msg" style="color:red"></span><br/>
    <input type="file" id="photo"><br/>
  <script type="text/javascript" src="jquery-3.2.1.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $(document).on('change','#photo',function(){
        var property = document.getElementById('photo').files[0];
        var image_name = property.name;
        var image_extension = image_name.split('.').pop().toLowerCase();

        if(jQuery.inArray(image_extension,['gif','jpg','jpeg','xlsx','']) == -1){
          alert("Invalid image file");
        }

        var form_data = new FormData();
        form_data.append("file",property);
        $.ajax({
          url:'export.php',
          method:'POST',
          data:form_data,
          contentType:false,
          cache:false,
          processData:false,
          beforeSend:function(){
            $('#msg').html('Loading......');
          },
          success:function(data){
            console.log(data);
            $('#msg').html(data);
          }
        });
      });
    });
  </script>
</body>
 
 </html>  
