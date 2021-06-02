<?php
	session_start();
	
	require("connection.php"); //includes the file containing all the "database" connection methods
	require("UsefulFunctions.php"); //includes the file containing some useful functions

	if (!(isset($_SESSION['loggedIn']))){ //if new session creates loggedIn variable
		$_SESSION['loggedIn'] = False;
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		if (isset($_POST['Remove'])){
			remove_notice($_POST['Remove']); //call remove_notice() from connection.php
		}

        switch( $_POST['page_action']){//page action handling
            
            case 'Login':
                $password = fopen("password.txt", "r"); //open password file
                if(hash("sha256", $_POST['password']) == fread($password,filesize("password.txt"))){ //compare password
                    $_SESSION['loggedIn'] = True; //set session variable
                }
                fclose($password);//close password file
                break;


            case 'Logout':
                $_SESSION['loggedIn'] = False;
                break;


            case 'ODH':
                hold($_POST['notice']); //call hold() from connection.php
                break;


            case 'Add':
                if (isset($_POST['tags'])){
                    $tags = explode(", ", $_POST['tags']);
                    $clean_tags = array();
                    for ($i = 0; $i < count($tags);$i++){
                        if ($tags[$i] != ""){
                            array_push($clean_tags, $tags[$i]);
                        }
                    }
                }else{
                    $clean_tags = array("No Tags");
                }
                if ($clean_tags == array()){
                    $clean_tags = array("No Tags");
                }
                add_notice($_POST['Title'],
                $_POST['Description'],
                $_POST['Teacher'],
                $_POST['start_date'],
                $_POST['end_date'],
                $_POST['repeat'],
                $clean_tags);
                break;

            case "Archive":
                archive_notice($_POST['notice']);
                remove_notice($_POST['notice']);


            case "Remove":
                remove_notice($_POST['notice']);
                break;


            case "Archive_old":
                archive_old();
                break;
            
        }
    }
?>


<html lang="en">
	<head>
		<title>DHS Daily Bulletin</title>
		<link rel="stylesheet" href="Bulletin.css">
		<meta charset="UTF-8">
	</head>
	<body>
		
		<?php
        echo '<form method="POST" class="login_form">';
		if ($_SESSION['loggedIn']){
			echo '<input type="submit" name="page_action" value="Logout">';
		}else{
			echo '<input type="password" name="password">
			      <input type="submit" name="page_action" value="Login">';
		}
        echo '</form>'?>


		<?php 
        require("html/version.html");
        require("heading.php");
		$needArchive = False;
		
		if ($_SESSION['loggedIn']){
			require("html/add_button.html");
			$notices = get_all_notices();
		
			for($i = 0;$i<count($notices);$i++){
				if ($notices[$i]['Display'] == 3){
					$needArchive = True;
				}
			}
		}else{
			$notices = get_today_notices();
		}
		
		if (count($notices) > 0){
			echo "<table border='2'><tbody><tr>";
			echo "<th id='title'>Title</th>
                  <th id='description'>Description</th>
                  <th id='teacher'>Teacher</th>";
			if ($_SESSION['loggedIn']){
				echo "<th id='dates'>Display Dates</th>
                      <th id='action'>Action</th>";
			}
			echo "</tr>";
			foreach($notices as $row){
                //for each docs https://www.php.net/manual/en/control-structures.foreach.php
                //its a bit stupid but oh well
				echo "<tr>";
				echo '<td id="title">'.htmlify($row['Title']);
				echo "<div style='display: none;'>".implode(",",$row['tags'])."</div></td>";
				echo '<td id="description">'.htmlify($row['Description']);
				echo '<td id="teacher">'.htmlify($row['Teacher']);
				if ($_SESSION['loggedIn']){
					$date_colours = array("green", "purple", "black", "red");
					echo "<td id='dates' style='color:".$date_colours[$row['Display']]."'>";
					echo format_shown_dates($row["InitialDate"], $row["EndDate"], $row["Repeata"])."</td>";
					
					echo '<td id="action">
                        <form method="POST" action="add.php" class="action_form">
                            <input name="notice" value='.$row['NoticeID'].' style="display:None;">
                            <input type="submit" name="page_action" value="Edit" class="action_button">
                        </form>
                        
                        <form method="POST" action="/" class="action_form" onsubmit="return confirm(\'Are you sure you want to remove this notice\')">
                            <input name="notice" value='.$row["NoticeID"].' style="display:None;">
                            <input type="submit" name="page_action" value="Remove" class="action_button">
                        </form>

                        <form method="POST" action="/" class="action_form">
                            <input name="notice" value='.$row['NoticeID'].' style="display:None;">
                            <input type="submit" name="page_action" value="ODH" class="action_button">
                        </form>

                        <form method="POST" action="/" class="action_form">
                            <input name="notice" value='.$row['NoticeID'].' style="display:None;">
                            <input type="submit" name="page_action" value="Archive" class="action_button">
                        </form>

                    </td>';

				}
				echo "</tr>";
			}
            unset($row);//for each being stupid

			echo "</tbody></table>";
			
			$tags = get_tags($notices);
			echo "<ul>";
			foreach($tags as $tag){
				echo "<li><label style='width:100%'><input class='tag_input' name='".$tag."'  type='checkbox' onchange='update()' />".$tag."</label></li>";
			}
            unset($tag);
			echo "</ul>";
			
		}else{
			echo "<h2>No notices today</h2>";
		}
		echo "<h2 style='clear:both;'>Can't find what your looking for?<br>Try looking in the <a href='/archive.php'>Archive</a></h2>";
		
		if ($needArchive){
			echo '<form method="POST" action="/" class="action_form" style="display:none" id="archive_old"><input name="page_action" value="Archive_old" class="action_button"></form>';
			echo '<script src="archive_old.js"></script>';
		}?>
    <script src="tag_update.js"></script>
	</body>
	<script>
		if ( window.history.replaceState ) {window.history.replaceState( null, null, window.location.href );}
	</script>
</html>