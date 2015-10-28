<?php
### English: Choose the directory which should used at serverstart. 
### Deutsch: Wähle das Verzeichnis, welches bei Serverstart verwendet werden soll. 

$start = 'docs';   // 'docs'  ,  'wbdemo'  ,  'wbdemo/admin'



### don't touch this
header("Location: http://127.0.0.1:4001/$start");
