<?php

// Function to set sssions if cookies are set
function set_login() {
    global $dbc;
    
    if((isset($_COOKIE['username']) && isset($_COOKIE['user_id'])) &&
    (!isset($_SESSION['username']) || !isset($_SESSION['user_id']))) {
        $user_id   = clean_string($_COOKIE['user_id']);
        $user_name = clean_string($_COOKIE['username']);
        
        $query  = "SELECT user_id FROM users WHERE user_id = '$user_id' AND ";
        $query .= "user_name = '$user_name' LIMIT 1";
        $data   = mysqli_query($dbc, $query);
        if(mysqli_num_rows($data) == 1) {
           $_SESSION['user_id']  = $user_id;
           $_SESSION['username'] = $user_name;
        }
    }
}

function is_ajax_request() {
    if($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest"){
        //NOTE: uncomment this statement b4 hosting
        //if($_SERVER['HTTP_HOST'] != HTTP_HOST) return false;
        return true;
    }else {
        return false;
    }
}

//replaces whitespace with an underscore for use in links
function gen_url_param($name) {
    global $Database;
    return $Database->clean_data(strtolower(str_replace(" ", "-", $name)));
}

// replaces underscores with whitespace
function stringify_url_param($name) {
    global $Database;
    return $Database->clean_data(strtolower(str_replace("-", " ", $name)));
}

/** CORE Functions */
function include_template($file_name="") {
    $file_name = strtolower($file_name);
    include(TEM_DIR.DS.$file_name);
}

// require functions
function require_class($class_name="") {
    $class_name = strtolower($class_name);
    return require_once(INC_DIR.DS.$class_name);
}

function redirect_to($location) {
    header("Location: $location");
}
?>