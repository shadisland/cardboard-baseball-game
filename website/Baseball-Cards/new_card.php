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
    <title>Baseball Cards - Insert Card Details</title>
    <link rel="stylesheet" href="/css/styles.css">
    
    <?php require 'head_scripts_include.php';?>
    
        <style>        
    	input[type=submit] {
    		background-color: rgba(12, 25, 64, 1.0);
    		color: white;
    		border-style: outset;
    		border-color: #0066A2;
    		height: auto;
    		width: auto;
    		font: bold 18px arial,sans-serif;
    		text-shadow: none;
    		border:0 none;
    		cursor:pointer;
    		-webkit-border-radius: 5px;
    		border-radius: 5px;
    		padding: 7px;
    	}       
    	input[type=text] {
    		background-color: rgba(12, 25, 64, 0.7);
    		color: white;
    		border-style: outset;
    		border: 2px solid rgba(12, 25, 64, 0.7) !important;
    		height: 25px;
    		font: bold 18px arial,sans-serif;
    		text-shadow: none;
    		border:0 none;
    		-webkit-border-radius: 5px;
    		border-radius: 5px; 
		outline: 5px solid rgba(12, 25, 64, 0.2); 
		outline-offset: -8px; 
		padding: 5px;
    	}       
    	input[type=file] {
    		background-color: rgba(12, 25, 64, 0.7);
    		color: white;
    		border-style: outset;
    		border: 2px solid rgba(12, 25, 64, 0.7) !important;
    		height: 25px;
    		width: 700px;
    		font: bold 18px arial,sans-serif;
    		text-shadow: none;
    		border:0 none;
    		-webkit-border-radius: 5px;
    		border-radius: 5px; 
		outline: 5px solid rgba(12, 25, 64, 0.2); 
		outline-offset: -8px; 
		padding: 5px;
    	}   
    	select {
    		background-color: rgba(12, 25, 64, 0.7);
    		color: white;
    		border-style: outset;
    		border: 2px solid rgba(12, 25, 64, 0.7) !important;
    		height: 40px;
    		font: bold 18px arial,sans-serif;
    		text-shadow: none;
    		border:0 none;
    		-webkit-border-radius: 5px;
    		border-radius: 5px; 
		outline: 5px solid rgba(12, 25, 64, 0.2); 
		outline-offset: -8px; 
		padding: 5px;
    	}
    	label {
    		width: 325px;
    		text-align: left;
    		display: block;
    		font-weight: bold;
    	}
	.display-container {
		box-shadow: 0 0 0 3px #0C2340;
		border: 7px solid #BD3039 !important; 
		border-radius: 5px !important; 
		outline: 3px solid #0C2340; 
		outline-offset: -10px; 
		background-color: rgba(255,255,255,0.7) !important;
		width: 860px !important;
		margin-top: 20px !important; 
		padding-bottom: 20px !important;
		font-size: 18px;
	}
    </style>
    <script>
        $(document).ready(function(){
            
	$('#submitShortForm').on('submit', function(e){
		e.preventDefault();
		var formData = new FormData(this);
		$.ajax({
			type: "POST",
	    		url: "insert_card_short_form.php",
			data: formData,
			processData: false,
			contentType: false,

			success: function (response) {
				alert(response);
				$('#submitShortForm')[0].reset();
			},
			error: function(){
			    alert("Error submitting data.");
			}
		});		   
            });
            
             
		    
            $('#submitForm').on('submit', function(e){
                e.preventDefault();
		var formData = new FormData(this);

                $.ajax({
			type: "POST",
			url: "insert_card.php",
			data: formData,
			processData: false,
			contentType: false,
			success: function(response){
				alert(response);
				$('#submitForm')[0].reset();
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
 
 	<h2>Add New Baseball Card</h2>
      <!--
      [sub-folder in the images folder]/[Year]_[Brand]_[Card Number]_[Player Name]__[Grading Company]-Grade-[Grade]_Auth-[Serial Number]_Front.jpg
      -->
      <div class="display-container" style="border: 1px solid #000; margin-left: 15px; padding: 10px 20px;">
	      <form id="submitShortForm">
			<div style="font-size: 22px; font-weight: bold; margin: 0px 0px 10px 0px;">
				Submit A New Card with Encoded Filename
			</div>		
			<div style="font-weight: normal; margin: 0px 0px 10px 0px;">
				(Short Form for uploading a new card with its filename in encoded format)
			</div>
			<div style="font-weight: normal; margin: 0px 0px 10px 0px;">Select appropriate image sub-folder &nbsp;     	
				<select id="image_sub_folder" name="image_sub_folder">
					<option value="raw-web">raw-web</option>
					<option value="PSA-Scans-web">PSA-Scans-web</option>
					<option value="SGC-016-web">SGC-016-web</option>
					<option value="SGC-309-web">SGC-309-web</option>
					<option value="SGC-Scans-web">SGC-Scans-web</option>
				</select>
			</div>
			<div style="font-weight: normal; margin: 0px 0px 10px 0px;">
				File name format: 1967_Topps_210_Bob_Gibson__SGC-Grade-5_Auth-3146996_Front.jpg
			</div>
			<div style="font-weight: bold; margin: 0px 0px 15px 0px;">
				Card Image - FRONT: <input type="file" id="image_upload" name="image_upload" accept="image/jpeg" class="team-form-item"  style="font-size: 16px;" >
			</div>
			<div style="font-weight: bold; margin: 0px 0px 15px 0px;">
				Card Image - BACK: <input type="file" id="image_upload_back" name="image_upload_back" accept="image/jpeg" class="team-form-item"  style="font-size: 16px;" >
			</div>
			<div style="font-weight: normal; margin: 0px 0px 15px 0px;">
				<input type="submit" value="Submit Card">
			</div>	
			<div style="font-weight: normal; margin: 0px 0px 10px 0px;">
				Excel formula: =CONCATENATE(A1,"_",B1,"_",C1,"_",D1,"__",E1,"-Grade-",F1,"_Auth-",G1,"_Front.jpg")
			</div>
		</form>
        </div>

	<div class="display-container" style="border: 1px solid #000; margin-left: 15px; padding: 10px 20px;">
			<div style="font-size: 22px; font-weight: bold; margin: 0px 0px 10px 0px;">
				Submit A New Card 
				<br>
				(with any unique filename--filename encoding not necessary)
			</div>
		<form id="submitForm">
			<label for="year">Year:</label>
			<input type="text" id="year" name="year" style="width: 110px;" required><br><br>
			<label for="brand">Brand: (Topps, Fleer, Goudey, etc.)</label>
			<input type="text" id="brand" name="brand" style="width: 110px;"  required><br><br>
			<label for="number">Number:</label>
			<input type="text" id="number" name="number" style="width: 110px;" " required><br><br>
			<label for="name">Name:</label>
			<input type="text" id="player_name" name="player_name" style="width: 300px;"  required><br><br>	
			
			<label for="image_sub_folder">Select appropriate image sub-folder:</label>
			<select id="image_sub_folder" name="image_sub_folder">
				<option value="raw-web">raw-web</option>
				<option value="PSA-Scans-web">PSA-Scans-web</option>
				<option value="SGC-016-web">SGC-016-web</option>
				<option value="SGC-309-web">SGC-309-web</option>
				<option value="SGC-Scans-web">SGC-Scans-web</option>
			</select><br><br>	
				
			
			<label for="image_upload">Card Image - FRONT:</label>
			<input type="file" id="image_upload" name="image_upload" accept="image/jpeg" class="team-form-item"  style="font-size: 16px;" ><br><br>	
			
			<label for="image_upload_back">Card Image - BACK:</label>
			<input type="file" id="image_upload_back" name="image_upload_back" accept="image/jpeg" class="team-form-item"  style="font-size: 16px;" ><br><br>	
			
			<label for="grade_company">Grade Company: (raw, SGC, PSA, etc.)</label>
			<input type="text" id="grade_company" name="grade_company" style="width: 110px;" required><br><br>
			<label for="grade">Grade:</label>
			<input type="text" id="grade" name="grade" style="width: 110px;" required><br><br>
			<label for="grade_serial">Grade Serial:</label>
			<input type="text" id="grade_serial" name="grade_serial" style="width: 110px;" required><br><br>
			<label for="i_own_it">I Own It?:</label>
			<input type="radio" id="i_own_it_yes" name="i_own_it" value="1" checked />
			    <label for="i_own_it_yes">Yes</label>

			<input type="radio" id="i_own_it_no" name="i_own_it" value="0" />
			    <label for="i_own_it_no">No</label>

		       <br><br>
			<input type="submit" value="Submit Card">
		</form>
	</div>
    <?php require 'footer.php';?>
    
</body>
</html>
