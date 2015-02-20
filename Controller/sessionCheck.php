<?php
session_start();
header('Content-Type: application/json charset=UTF-8');
if(isset($_SESSION['prg_id'])) {
    echo json_encode(array('sessCheck' => true));
}
else {
    echo json_encode(array('sessCheck' => false));
}