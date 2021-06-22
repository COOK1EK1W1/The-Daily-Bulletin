<?php
	session_start();

	require("connection.php"); //includes the file containing all the "database" connection methods
	require("UsefulFunctions.php"); //includes the file containing some useful functions

	if (!(isset($_SESSION['loggedIn']))){ //if new session creates loggedIn variable
		$_SESSION['loggedIn'] = False;
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        switch ($_POST['page_action']){

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

		    case "Remove":
                remove_notice("archive.json", $_POST['notice']);
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
		
		<?php //add login / logout form
            echo '<form method="POST" class="login_form">';
            if ($_SESSION['loggedIn']){
                echo '<input type="submit" name="page_action" value="Logout">';
            }else{
                echo '<input type="password" name="password">
                    <input type="submit" name="page_action" value="Login">';
            }
            echo '</form>';

            require("html/version.html"); //add bulletin verion
            require("html/heading.php"); //add the heading
		?>
        <h2>Archive</h2>
		<?php
            $archived_notices = get_notices("archive.json"); //call get_archived() from connection.php for notices
            
            if (count($archived_notices) > 0){ //if there are notices
                echo "<table border=2><tr>";// open the table
                echo "<th id='title'>Title</th>
                    <th id='description'>Description</th>
                    <th id='teacher'>Teacher</th>
                    <th id='dates'>Display Dates</td>";//add the titles of the columns
            
                if ($_SESSION['loggedIn']){//add the extra column headers if loggedin
                    echo "<th id='arch-action'>Action</th>";
                }
                echo "</tr>";
                foreach ($archived_notices as $row){//for each notice
                    echo "<tr>";
                    echo '<td id="title">'.htmlify($row['Title']);//add all the information for the notices
                    echo "<div class='hide'>".implode(",",$row['tags'])."</div></td>";
                    echo '<td id="description">'.htmlify($row['Description'])."</td>";
                    echo '<td id="teacher">'.htmlify($row['Teacher'])."</td>";
                    echo "<td id='dates'>";
                    echo format_shown_dates($row["InitialDate"], $row["EndDate"], $row["Repeata"])."</td>";
                    if ($_SESSION['loggedIn']){
                        echo '<td id="arch-action">                        
                            <form method="POST" action="/archive.php" class="action_form" onsubmit="return confirm(\'Are you sure you want to remove this notice\')">
                                <input name="notice" value='.$row["NoticeID"].' class="hide">
                                <input type="submit" name="page_action" value="Remove" class="action_button">
                            </form>
                        </td>';

                    }
                    echo "</tr>";//close the row
                }
                unset($row);
                echo "</table>"; //close the table
                
                $tags = get_tags($archived_notices);//get the tags for all displayed notices
                echo "<ul class='tag_list'>";
                foreach ($tags as $tag){ //create list of all tags
                    echo "<li><label><input class='tag_input' name='".$tag."'  type='checkbox' onchange='update()' />".$tag."</label></li>";
                }
                echo "</ul>";
                
            }else{//no notices
                echo "<h2>No notices here</h2>";
            }
		?>
        <h2>Notice not here?<br>I guess it was just a figment of your imagination. <br>Head back <a href='/'>here</a></h2>
        <script src="tag_update.js"></script>
	</body>
	<script>//script to remove page history
		if ( window.history.replaceState ) {window.history.replaceState( null, null, window.location.href );}
	</script>
</html>