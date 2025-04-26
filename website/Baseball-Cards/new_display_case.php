<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
   exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Baseball Cards - Create New Virtual Display</title>
    <link rel="stylesheet" href="/css/styles.css">
    
    <?php require 'head_scripts_include.php';?>
    
    <style>
    
	input[type=submit] {
		background: #0066A2;color: white;border-style: outset;border-color: #0066A2;
		height: 25px;
		width: auto;font: bold15px arial,sans-serif;text-shadow: none;
		border:0 none;
		cursor:pointer;
		-webkit-border-radius: 5px;
		border-radius: 5px;
	}
    </style>
    <script>
        $(document).ready(function(){
            $('#insertDisplayCaseForm').on('submit', function(e){
                e.preventDefault();
		$(this).find(":submit").attr('disabled', 'disabled');
                $.ajax({
                    type: "POST",
                    url: "insert_display_case.php",
                    data: $(this).serialize(),
                    success: function(response){
                        //alert(response);
                        //$('#insertDisplayCaseForm')[0].reset();
                        $('#change-notification').text(response).fadeIn("slow").delay(2000).fadeOut("slow", function () {
				setTimeout(function () {
					location.assign("display_case.php");
				}, 1000);
			});
                    },
                    error: function(){
                        alert("Error submitting data.");
                    }
                });
            });
        });
    </script>
</head>
<body>

<?php require 'header.php';?>
 
 <div id="change-notification" style="">&nbsp;</div>
 
    <h2>Create A New Virtual Display</h2>
    
      
    <form id="insertDisplayCaseForm">
	Name: <input type="text" name="display_case_name" value="" required><br/><br/>
	Background Color: <input type="text" name="display_case_color" value="black" required> (black, white, #CCCCCC, etc.)
	<br/><br/>
	Grid Columns: <input type="text" name="display_case_grid_size" style="width: 40px;" value="6" required> (Any integer, usually between 4 and 14)
	<br/><br/>
	Font Family: <input type="text" name="display_case_font" style="width: 140px;" value="Georgia" required> (See font list below) 	        
	<br/><br/>
	Font Color: <input type="text" name="display_case_font_color" style="width: 140px;" value="lightblue" required> (black, white, #CCCCCC, etc.)
	<br/><br/>
	Font Shadow Color <input type="text" name="display_case_font_shadow" style="width: 140px;" value="black" required> (black, white, #CCCCCC, etc.)
	<br/><br/>
           <input type="submit" value="Create New Display">
    </form>

<br/>
Copy & Paste a font name into the "Font Family" field above.

<div style="margin-top: 10px; max-width: 850px; height: 500px;">
	<div style="float: left;">
	
		Standard web fonts: 
		<ul style="margin-top: 5px; padding-left: 25px;">        
		    <li style="font-family: Arial; font-size: 22px;">Arial</li>
		    <li style="font-family: Verdana; font-size: 22px;">Verdana</li>
		    <li style="font-family: Tahoma; font-size: 22px;">Tahoma</li>
		    <li style="font-family: Trebuchet MS; font-size: 22px;">Trebuchet MS</li>
		    <li style="font-family: Times New Roman; font-size: 22px;">Times New Roman</li>
		    <li style="font-family: Georgia; font-size: 22px;">Georgia</li>
		    <li style="font-family: Garamond; font-size: 22px;">Garamond</li>
		    <li style="font-family: Courier New; font-size: 22px;">Courier New</li>
		    <li style="font-family: Brush Script MT; font-size: 22px;">Brush Script MT (cursive)</li>
		</ul>
	</div>
	<div style="float: right;">
		Installed fonts: 
		<ul style="margin-top: 5px; padding-left: 25px;">
		    <li style="font-family: LHF_Ballpark_Script; font-size: 22px;">LHF_Ballpark_Script</li>
		    <li style="font-family: Bosox_Full; font-size: 22px;">Bosox_Full</li>
		    <li style="font-family: Eurostile_MN_Extended_Bold; font-size: 22px;">Eurostile_MN_Extended_Bold</li>
		    <li style="font-family: The_Blendhes; font-size: 22px;">The_Blendhes</li>
		    <li style="font-family: BlacklightD_Regular; font-size: 22px;">BlacklightD_Regular</li>
		    <li style="font-family: FabioloSmallCap-Light; font-size: 22px;">FabioloSmallCap-Light</li>
		    <li style="font-family: Adequate-ExtraLight; font-size: 22px;">Adequate-ExtraLight</li>
		</ul>
	</div>
</div>
    <?php require 'footer.php';?>
    
</body>
</html>