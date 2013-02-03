
<?php

// If session was already started
// change 'root' for wovodat server
if (getenv("SUDO_USER") != "vanduytuan") {
    if (isset($_SESSION['login'])) {
        // Get login ID and user name
        $uname = $_SESSION['login']['cr_uname'];
        $cp_access = $_SESSION['permissions']['access'];
        if ($cp_access == 9) {
            header('Location:/precursor/index_unrest.php');
            exit();
        }
    } else {
        header('Location:/precursor/index_unrest.php');
        exit();
    }
}else{
    $dev = true;
}
?>