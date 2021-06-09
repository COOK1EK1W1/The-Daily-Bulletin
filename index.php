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
                $tags = explode(", ", $_POST['tags']); //parse tags
                $clean_tags = array(); 
                foreach ($tags as $tag){
                    if ($tag != ""){
                        array_push($clean_tags, $tag);
                    }
                }
                unset($tag);
                if ($clean_tags == array()){ //if there are no tags add "No Tags" to tags
                    $clean_tags = array("No Tags");
                } //call add_notice() from connection.php
                add_notice($_POST['Title'], 
                $_POST['Description'],
                $_POST['Teacher'],
                $_POST['start_date'],
                $_POST['end_date'],
                $_POST['repeat'],
                $clean_tags);
                break;

            case "Archive": //add notice to archive then delete from current
                archive_notice($_POST['notice']); //both function from connection.php
                remove_notice($_POST['notice']);


            case "Remove":
                remove_notice($_POST['notice']);
                break;


            case "Archive_old": //remove notices that are past the end date
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
            echo "<br>";
            $needArchive = False; //create needArchive for archiving old notices
            
            if ($_SESSION['loggedIn']){
                require("html/add_button.html"); //add notice button
                $notices = get_all_notices(); //if logged in call get_all_notices() from connection.php
            
                foreach($notices as $notice){//loop for each notice and see if it is past end date
                    if ($notice['Display'] == 3){
                        $needArchive = True;
                    }
                }
                unset($notice);
            }else{
                $notices = get_today_notices(); //if not logged in call get_today_notices() from connection.php
            }
            
            if (count($notices) > 0){ //if there are notices
                echo "<table border='2'><tr>"; //open table
                echo "<th id='title'>Title</th>
                    <th id='description'>Description</th>
                    <th id='teacher'>Teacher</th>";//add the titles of the columns

                if ($_SESSION['loggedIn']){ //add the extra column headers if loggedin
                    echo "<th id='dates'>Display Dates</th>
                        <th id='action'>Action</th>";
                }
                echo "</tr>";

                foreach($notices as $row){//for each notice
                    echo "<tr>";
                    echo '<td id="title">'.htmlify($row['Title']); //add all the information for the notices
                    echo "<div class='hide'>".implode(",",$row['tags'])."</div></td>";
                    echo '<td id="description">'.htmlify($row['Description'])."</td>";
                    echo '<td id="teacher">'.htmlify($row['Teacher'])."</td>";

                    if ($_SESSION['loggedIn']){ //if logged in add the display dates
                        $date_colours = array("green", "purple", "black", "red");
                        echo "<td id='dates' style='color:".$date_colours[$row['Display']]."'>";
                        echo format_shown_dates($row["InitialDate"], $row["EndDate"], $row["Repeata"])."</td>";
                        
                        // if loggedin add the notice actions
                        echo '<td id="action">
                                <form method="POST" action="add.php" class="action_form">
                                    <input name="notice" value='.$row['NoticeID'].' class="hide">
                                    <input type="submit" name="page_action" value="Edit" class="action_button">
                                </form>
                                
                                <form method="POST" action="/" class="action_form" onsubmit="return confirm(\'Are you sure you want to remove this notice\')">
                                    <input name="notice" value='.$row["NoticeID"].' class="hide">
                                    <input type="submit" name="page_action" value="Remove" class="action_button">
                                </form>

                                <form method="POST" action="/" class="action_form">
                                    <input name="notice" value='.$row['NoticeID'].' class="hide">
                                    <input type="submit" name="page_action" value="ODH" class="action_button">
                                </form>

                                <form method="POST" action="/" class="action_form">
                                    <input name="notice" value='.$row['NoticeID'].' class="hide">
                                    <input type="submit" name="page_action" value="Archive" class="action_button">
                                </form>
                             </td>';

                    }
                    echo "</tr>";//close the row
                }
                unset($row);
                echo "</table>"; //close the table
                
                $tags = get_tags($notices);//get the tags for all displayed notices
                echo "<ul class='tag_list'>";
                foreach($tags as $tag){ //create list of all tags
                    echo "<li><label><input class='tag_input' name='".$tag."'  type='checkbox' onchange='update()' />".$tag."</label></li>";
                }
                unset($tag);
                echo "</ul>";
                
            }else{ //no notices
                echo "<h2>No notices today</h2>";
            }
            echo "<h2>Can't find what your looking for?<br>Try looking in the <a href='/archive.php'>Archive</a></h2>";
            
            if ($needArchive){
                echo '<form method="POST" action="/" class="action_form" class="hide" id="archive_old">
                        <input name="page_action" value="Archive_old" class="action_button">
                    </form>';
                echo '<script src="archive_old.js"></script>';
            }
        ?>
        <script src="tag_update.js"></script>
	</body>
	<script>//script to remove page history
		if ( window.history.replaceState ) {window.history.replaceState( null, null, window.location.href );}
	</script>
</html>