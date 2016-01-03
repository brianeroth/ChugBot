<?php
    session_start();
    include 'functions.php';
    bounceToLogin();
    $err = $dbErr = "";
    $assignmentResults = array(); // Associative
    
    // We assume we got here from a POST.  If not, go to the home page.
    if (! $_SERVER["REQUEST_METHOD"] == "POST") {
        $err = errorString("Unknown request method - please hit 'Back' and try again.");
    }
    $edah = intval(test_input($_POST["edah"]));
    $block = intval(test_input($_POST["block"]));
    $levelHomeUrl = urlIfy("levelHome.html");
    $levelHomeUrl .= "?edah=$edah&block=$block";
    
    // Check for an existing assignment set.
    $mysqli = connect_db();
    $sql = "SELECT * FROM ASSIGNMENTS WHERE edah_id = $edah AND block_id = $block_id";
    $result = $mysqli->query($sql);
    if ($result == FALSE) {
        $dbErr = dbErrorString($sql, $mysqli->error);
    }
    if ($result->num_rows > 0) {
        // We have an existing assignment: redirect to the display/edit page.
        header("Location: $levelHomeUrl");
        exit;
    }
    
    // We're now ready to build our assignments.  We iterate over each activity
    // group, and make an assignment for each one.
    $sql = "SELECT group_id, name FROM groups"
    $result = $mysqli->query($sql);
    if ($result == FALSE) {
        $dbErr = dbErrorString($sql, $mysqli->error);
    }
    // Do the actual assignments, recording results as we go.
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
        $group_id = intval($row[0])
        $group_name = $row[1];
        $err = "";
        $ok = do_assignment($edah, $block, $group_id, $err);
        if ($ok) {
            $assignmentResults[$group_name] = "OK";
        } else {
            $assignmentResults[$group_name] = $err;
        }
    }
    
    ?>
    
