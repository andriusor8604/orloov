<?php
    if ( $_FILES['file']['error'] > 0 ){
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
        if(move_uploaded_file($_FILES['file']['tmp_name'], 'upload/' . $_FILES['file']['name']))
        {
            echo $_FILES['file']['name'];
        }
    }

?>