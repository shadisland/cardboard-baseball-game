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
    <title>Baseball Cards - Edit Card Details</title>
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
            $('#submitForm').on('submit', function(e){
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "insert_card.php",
                    data: $(this).serialize(),
                    success: function(response){
                        alert(response);
                        $('#submitForm')[0].reset();
                    },
                    error: function(){
                        alert("Error submitting data.");
                    }
                });
            });
            
            $('#submitShortForm').on('submit', function(e){
		    e.preventDefault();

		    $.ajax({
			type: "POST",
			url: "insert_card_short_form.php",
			data: $(this).serialize(),
			success: function(response){
			    alert(response);
			    $('#submitShortForm')[0].reset();
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
 
 	<h2>Insert Baseball Card Details</h2>
      Short Form for image URLs in coded format:<br/>
      [sub-folder in the images folder]/[Year]_[Brand]_[Card Number]_[Player Name]__[Grading Company]-Grade-[Grade]_Auth-[Serial Number]_Front.jpg
      <br/><br/>
      <form id="submitShortForm">
              <label for="img_url">Image URL:</label>
        	<input type="text" id="img_url" size="45" name="img_url" required> <br/>
        	( SGC-016-web/1967_Topps_210_Bob_Gibson__SGC-Grade-5_Auth-3146996_Front.jpg)<br/><br/>
        	 <input type="submit" value="Submit Card By Coded URL">
        </form>
        <br/>
      <hr/>
        <br/>
    <form id="submitForm">
        <label for="year">Year:</label>
        <input type="text" id="year" name="year" required><br><br>
        <label for="brand">Brand:</label>
        <input type="text" id="brand" name="brand" required><br><br>
        <label for="number">Number:</label>
        <input type="text" id="number" name="number" required><br><br>
        <label for="name">Name:</label>
        <input type="text" id="player_name" name="player_name" required><br><br>
        <label for="img_url">Image URL:</label>
        <input type="text" id="img_url" name="img_url" style="width:200px;" required> ( SGC-016-web/1977_Topps_231_George_Brett_Record_Breaker__SGC-Grade-8pt5_Auth-0621546_Front.jpg )<br><br>
        <label for="grade_company">Grade Company:</label>
        <input type="text" id="grade_company" name="grade_company" required><br><br>
        <label for="grade">Grade:</label>
        <input type="text" id="grade" name="grade" required><br><br>
        <label for="grade_serial">Grade Serial:</label>
        <input type="text" id="grade_serial" name="grade_serial" required><br><br>
        <label for="i_own_it">I Own It?:</label>
         <input type="radio" id="i_own_it_yes" name="i_own_it" value="1" checked />
	    <label for="i_own_it_yes">Yes</label>
	  
	    <input type="radio" id="i_own_it_no" name="i_own_it" value="0" />
	    <label for="i_own_it_no">No</label>
  	
       <br><br>
        <input type="submit" value="Submit Card Form">
    </form>
   
   
    
   
    
    <?php require 'footer.php';?>
    
</body>
</html>
