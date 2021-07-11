<?php 
    session_start();
    if (!$_SESSION['loggedIn']){//if not logged in
        echo "nah mate";
        die();
    }
    require("connection.php");
    require("UsefulFunctions.php");

    $editingNotice = NULL; //set notice to POST notice for editing

    if(isset($_POST['notice'])){
        $editingNotice = get_notice($_POST['notice']);
    }

    if ($editingNotice == NULL){//get default notice if no post
        $editingNotice = array(
            "Title"=> "", 
            "Description"=> "", 
            "Teacher"=> "",
            "InitialDate"=> date("Y-m-d"), 
            "EndDate"=> date("Y-m-d"), 
            "Repeata"=> "once", 
            "Tags"=>["No Tags"]);
    }

?>
<html>
    <head>
        <title>DHS Daily Bulletin</title>
        <link rel="stylesheet" href="Bulletin.css">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
        <meta charset="UTF-8">
        <script src="scripts/update.js"></script>
        <script src="scripts/validate_notice.js"></script>
    </head>
    <body>
        <?php require("html-lib/version.html")?>
        <?php require("html-lib/heading.php")?>
        

        <form method="POST" action="/" onsubmit="return validate_notice()">

            <div id='information'>
                <h2>Information</h2>
                <label style="padding-right:10%">Notice Title: <br>
                    <input name="Title" value='<?=$editingNotice['Title']?>' required maxlength="30"/>
                </label>

                <label>Teacher:<br>
                    <input name="Teacher" value="<?=$editingNotice['Teacher']?>" required maxlength="30"/>
                </label><br><br>

                <label style="width:100%;">Description:<br>
                    <textarea name='Description' required maxlength="20000" rows="8"><?=$editingNotice['Description']?></textarea>
                </label><br><br>
            </div>

            <div id='displayDates'>
                <h2>Display Dates</h2>
                <div id="startDateContainer"> 
                    <label>Select start date:
                        <input type="date" name="start_date" value="<?=$editingNotice['InitialDate']?>" required onchange="on_date_update();"/>
                    </label>
                </div><br>

                <div id="endDateContainer">
                    <label>Select end date:
                        <input type="date" name="end_date" value="<?=$editingNotice['EndDate']?>" required onchange="on_date_update();" />
                    </label>
                </div><br>


                <label for="repeat">Choose how often you want notices to be displayed:

                    <select name="repeat" id="repeat" required onchange="on_date_update();">
                        <option value="once" <?php if ($editingNotice['Repeata'] == "once") echo "selected"?>>Once</option>
                        <option value="daily" <?php if ($editingNotice['Repeata'] == "daily") echo "selected"?>>Daily</option>                        
                        <option value="weekly" <?php if ($editingNotice['Repeata'] == "weekly") echo "selected"?>>Weekly</option>
                    </select>
                </label><br><br>
            </div>

            
            <div id='tags'>
                <h2>Tags</h2>
                <textarea name="Tags" placeholder="Tag1, Tag2, Tag3" rows="3" cols="20"><?php echo implode(", ", $editingNotice['Tags'])?></textarea>
                <ul class='tag_input_list'>
                    <?php
                        $allTags = read_notices("data/notices.json");
                        $tags = get_all_tags_from($allTags);
                        foreach($tags as $tag){
                            echo "<input type='button' value='".$tag."' onclick='add_to_tags(\"".$tag."\")'>";
                        }
                        unset($tag);
                    ?>
                </lu>
            </div>
            <?php
                if (isset($_POST['notice'])){
                    echo "<input name='Remove' value=".$_POST['notice']." class='hide'>";
                }
            ?>
            <br>
            <hr>
            <div>
                <h2>Viewer</h2>
                <table border=2>
                    <tr>
                        <th id='title'>Title</th>
                        <th id='description'>Description</th>
                        <th id='teacher'>Teacher</th>
                    </tr>
                    <tr>
                        <td id='title'></th>
                        <td id='description'></th>
                        <td id='teacher'></th>
                    </tr>
                </table>

                <ul class='tag_list'>
                </ul>
                <p id='date_text'></p>
                

            </div>
            <br>
            <input type="submit" name="page_action" value="Add" class="big_action_button"/>
        </form>
    </body>
    <script>
        on_date_update();
        setInterval(on_text_update, 60);
    </script>
</html>