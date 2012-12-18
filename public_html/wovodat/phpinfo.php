<?php
function test(){
    $a = 1;
    test1();
}
function test1(){
    global $a;
    echo $a;
    return;
}
test();
?>