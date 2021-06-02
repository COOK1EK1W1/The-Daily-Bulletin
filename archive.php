<?php
	session_start();
	
	require("connection.php");
	require("UsefulFunctions.php");

	if (!(isset($_SESSION['loggedIn']))){
		$_SESSION['loggedIn'] = False;
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$page_action = $_POST['page_action'];

        switch ($_POST['page_action']){

		    case 'Login':
			    $password = fopen("password.txt", "r");
                if(hash("sha256", $_POST['password']) == fread($password,filesize("password.txt"))){
                    $_SESSION['loggedIn'] = True;
                }
                fclose($password);
                break;

		    case 'Logout':
			    $_SESSION['loggedIn'] = False;
                break;

		    case "Remove":
                remove_notice_archive($_POST['notice']);
                break;
        }
		
	}
?>
<html lang="en">
	<head>
		<title>DHS Daily Bulletin</title>
		<link rel="stylesheet" href="Bulletin.css">
		<link rel="shortcut icon" type="image/png" href="favicon.png">
		<meta charset="UTF-8">
	</head>
	<body>
		
		<?php
		if ($_SESSION['loggedIn']){
			echo '<form method="POST" class="login_form">
					<input type="submit" name="page_action" value="Logout">
				</form>';
		}else{
			echo '<form method="POST" class="login_form">
					<input type="password" name="password">
					<input type="submit" name="page_action" value="Login">
				</form>';
		}?>
		<div style='float:right;right:0;position:absolute;'>
			V3.1.1 ALPHA<br>Stable-ish
		</div>
        <h2>Archive</h2>
		<?php require("heading.php");
		
        $archived_notices = get_archived();
		
		if (count($archived_notices) > 0){
			echo "<table border=2><tr>";
			echo "<th id='title'>Title</th>
                  <th id='description'>Description</th>
                  <th id='teacher'>Teacher</th>
                  <th id='dates'>Display Dates</td>";
                  
			if ($_SESSION['loggedIn']){
				echo "<th id='action' style='width:60px;'>Action</th>";
			}
			echo "</tr>";
			foreach ($archived_notices as $row){
				echo "<tr>";
				echo '<td id="title">'.htmlify($row['Title']);
				echo "<div style='display: none;'>".implode(",",$row['tags'])."</div></td>";
				echo '<td id="description">'.htmlify($row['Description']);
				echo '<td id="teacher">'.htmlify($row['Teacher']);
                echo "<td id='dates'>";
                echo format_shown_dates($row["InitialDate"], $row["EndDate"], $row["Repeata"])."</td>";
				if ($_SESSION['loggedIn']){
					echo '<td id="action" style="width:60px;">                        
                        <form method="POST" action="/archive.php" class="action_form" onsubmit="return confirm(\'Are you sure you want to remove this notice\')">
                            <input name="notice" value='.$row["NoticeID"].' style="display:None;">
                            <input type="submit" name="page_action" value="Remove" class="action_button">
                        </form>
                    </td>';

				}
				echo "</tr>";
			}
            unset($row);
			echo "</table>";
			
			$tags = get_tags($archived_notices);
			echo "<ul>";
			foreach ($tags as $tag){
				echo "<li><label style='width:100%'><input class='tag_input' name='".$tag."'  type='checkbox' onchange='update()' />".$tag."</label></li>";
			}
			echo "</ul>";
			
		}else{
			echo "<h2>No notices today</h2>";
		}
		echo "<h2 style='clear:both;'>Notice not here?<br>I guess it was just a figment of your imagination. <br>Head back <a href='/'>here</a></h2>";
		
		echo '<script src="tag_update.js"></script>';
		?>
	</body>
	<script>
		if ( window.history.replaceState ) {window.history.replaceState( null, null, window.location.href );}
	</script>
</html>