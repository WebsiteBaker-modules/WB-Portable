//:Load the view.php from any other section-module
//:Use [[SectionPicker?sid=123]]
    global $database, $wb, $TEXT, $DGTEXT;
    $content = '';
    $_sFrontendCss = '';
    $sid = isset( $sid) ? intval( $sid) : 0;
    if ( intval( $sid) > 0) {
        $now = time();
        $sql = 'SELECT * FROM `'.TABLE_PREFIX.'sections` `s`'
             . 'WHERE `section_id` = '.( int)$sid.' '
             .   'AND ('
             .         '('.$now.'>=`publ_start` OR `publ_start`=0) AND '
             .         '('.$now.'<=`publ_end` OR `publ_end`=0) '
             .       ')'
             . '';
        $sql = 'SELECT `s`.*'
              .     ', `p`.`viewing_groups`'
              .     ', `p`.`visibility`'
              .     ', `p`.`menu_title`'
              .     ', `p`.`link` '
              . 'FROM `'.TABLE_PREFIX.'sections` `s`'
              . 'INNER JOIN `'.TABLE_PREFIX.'pages` `p` '
              .    'ON `p`.`page_id`=`s`.`page_id` '
             . 'WHERE `s`.`section_id` = '.( int)$sid.' '
             .   'AND ('
             .         '('.$now.'>=`s`.`publ_start` OR `s`.`publ_start`=0) AND '
             .         '('.$now.'<=`s`.`publ_end` OR `s`.`publ_end`=0) '
             .       ')'
             .   'AND `p`.`visibility` NOT IN (\'none\',\'hidden\',\'deleted\') '
             .   '  ';
        if ( $oSection = $database->query( $sql)) {
            while ( $aSection = $oSection->fetchRow( MYSQLI_ASSOC)) {
                $section_id = $aSection['section_id'];
                $module = $aSection['module'];
                ob_start();
                require ( WB_PATH.'/modules/'.$module.'/view.php');
                $content = ob_get_clean();
                $_sFrontendCss = '/modules/'.$module.'/frontend.css';
                $_sFrontendCssrUrl = WB_URL.$_sFrontendCss;
                $_sSearch = preg_quote( WB_URL.'/modules/'.$module.'/frontend.css', '/');
                if ( preg_match( '/<link[^>]*?href\s*=\s*\"'.$_sSearch.'\".*?\/>/si', $content)) {
                    $_sFrontendCss = '';
                } else {
                    $_sFrontendCss = '<link href="'.WB_URL.$_sFrontendCss.'" rel="stylesheet" type="text/css" media="screen" />';
                }
            }
        }
    }
    return $_sFrontendCss.$content;