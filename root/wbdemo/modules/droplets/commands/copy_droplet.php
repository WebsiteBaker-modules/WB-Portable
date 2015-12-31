<?php

    $sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_droplets`'
         . 'WHERE `id` = '.$droplet_id;
    if ( $oRes = $database->query($sql) ) {
         $aRes = $oRes->fetchRow(MYSQLI_ASSOC);
    // Insert new row into database
    $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_droplets` SET '
    . '`name` = \'\', '
    . '`code` = \''.$aRes['code'].'\', '
    . '`description` = \''.$aRes['description'].'\', '
    . '`comments` = \''.$aRes['comments'].'\', '
    . '`active` = 1, '
    . '`modified_when` = '.$modified_when.', '
    . '`modified_by` = '.$modified_by.' ';
    $database->query($sql);
    } else {
      
      
    }


