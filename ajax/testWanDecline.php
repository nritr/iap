<?php
//NICO: si descomentas esta linea, te guarda todo el json recibido como string en test.txt
file_put_contents('testwanDecline.txt', file_get_contents('php://input'));
?>