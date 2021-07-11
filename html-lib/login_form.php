<?php
    echo '<form method="POST" class="login_form">';
    if ($_SESSION['loggedIn']){
        echo '<input type="submit" name="page_action" value="Logout">';
    }else{
        echo '<input type="password" name="password">
        <input type="submit" name="page_action" value="Login">';
    }
    echo '</form>';
?>