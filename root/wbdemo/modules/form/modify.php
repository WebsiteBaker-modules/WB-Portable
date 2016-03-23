<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id:  $
 * @filesource      $HeadURL:  $
 * @lastmodified    $Date:  $
 * @description
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

//overwrite php.ini on Apache servers for valid SESSION ID Separator
$sQuerySep = ini_get('arg_separator.output');
//if(function_exists('ini_set')) {
//    ini_set('arg_separator.output', '&amp;');
//}

$sModulName = basename(__DIR__);
$sModulName = $sModulName;
$ModuleRel = '/modules/'.basename(__DIR__).'/';
$ModuleUrl = WB_URL.'/modules/'.basename(__DIR__).'/';
$ModulePath = WB_PATH.'/modules/'.basename(__DIR__).'/';

// load module language file
$sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}

if( !function_exists( 'make_dir' ) )  {  require(WB_PATH.'/framework/functions.php');  }

$sec_anchor = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? '#'.SEC_ANCHOR.$section['section_id'] : '' );

//Delete all form fields with no title
$sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_form_fields` ';
$sql .= 'WHERE page_id = '.(int)$page_id.' ';
$sql .=   'AND section_id = '.(int)$section_id.' ';
$sql .=   'AND title=\'\' ';
if( !$database->query($sql) ) {
// error msg
}

// later in upgrade.php
$table_name = TABLE_PREFIX.'mod_form_settings';
$field_name = 'perpage_submissions';
$description = "INT NOT NULL DEFAULT '10' AFTER `max_submissions`";
if(!$database->field_exists($table_name,$field_name)) {
    $database->field_add($table_name, $field_name, $description);
}
$FTAN = $admin->getFTAN('');

?><table class="mod_form" style="width: 100%;">
<tbody>
<tr>
    <td >
        <form action="<?php echo $ModuleUrl; ?>add_field.php" method="post" class="mod_form" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="<?php echo $TEXT['ADD'].' '.$TEXT['FIELD']; ?>" style="width: 100%;" />
        </form>
    </td>
    <td >
        <form action="<?php echo $ModuleUrl; ?>modify_settings.php" method="post" class="mod_form" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="<?php echo $TEXT['SETTINGS']; ?>" style="width: 100%;" />
        </form>
    </td>
<?php if( $admin->ami_group_member('1') ) {  ?>
    <td >
        <form action="<?php echo WB_URL; ?>/modules/form/reorgPosition.php" method="post" class="mod_form" >
            <input type="hidden" value="<?php echo $page_id; ?>" name="page_id">
            <input type="hidden" value="<?php echo $section_id; ?>" name="section_id">
            <input type="hidden" value="<?php echo $FTAN['value'];?>" name="<?php echo $FTAN['name'];?>">
            <input type="submit" value="Reorg Position" style="width: 100%;" />
        </form>
    </td>
<?php } ?>
</tr>
</tbody>
</table>

<br />

<h2><?php echo $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$TEXT['FIELD']; ?></h2>
<?php

// Loop through existing fields
$sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_form_fields` '
      . 'WHERE `section_id` = '.(int)$section_id.' '
      . 'ORDER BY `position` ASC';
if($oFields = $database->query($sql)) {
    $num_fields = $oFields->numRows();
    if($num_fields) {
        ?><div class="jsadmin hide"></div>
        <table class="mod_form" >
        <thead>
            <tr >
                <th style="padding-left: 5px; width: 3%;">&nbsp;</th>
                <th style="text-align: right; width: 3%;">ID</th>
                <th style=" width: 50%;"><?php print $TEXT['FIELD']; ?></th>
                <th style=" width: 20%;"><?php print $TEXT['TYPE']; ?></th>
                <th style=" width: 5%;"><?php print $TEXT['REQUIRED']; ?></th>
                <th style=" width: 5%;">
                <?php
                    echo $TEXT['MULTISELECT'];
                ?>
                </th>
                <th style=" width: 10%;" colspan="3">
                <?php
                    echo $TEXT['ACTIONS'];
                ?>
                <th style=" width: 3%;">POS</th>
                </th>
            </tr>
        </thead>
        <tbody>
<?php
        while($aFields = $oFields->fetchRow(MYSQL_ASSOC)) {
          $sFielIdkey = $admin->getIDKEY($aFields['field_id']);
?><tr class=" sectionrow">
                <td style="padding-left: 5px;">
                    <a href="<?php echo $ModuleUrl; ?>modify_field.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;field_id=<?php echo $sFielIdkey; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/modify_16.png" alt="^" />
                    </a>
                </td>
                <td style="text-align: right;">
                    <a style=" font-weight: normal;" href="<?php echo $ModuleUrl; ?>modify_field.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;field_id=<?php echo $sFielIdkey; ?>">
                        <?php echo $aFields['field_id']; ?>
                    </a>
                </td>
                <td>
                    <a href="<?php echo $ModuleUrl; ?>modify_field.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;field_id=<?php echo $sFielIdkey; ?>">
                        <?php echo $aFields['title']; ?>
                    </a>
                </td>
                <td>
<?php
                    $key = $aFields['type'];
                    switch ($key):
                        case 'textfield':
                            $sTitle = $TEXT['SHORT_TEXT'];
                            break;
                        case 'textarea':
                            $sTitle = $TEXT['LONG_TEXT'];
                            break;
                        case 'heading':
                            $sTitle = $TEXT['HEADING'];
                            break;
                        case 'select':
                            $sTitle = $TEXT['SELECT_BOX'];
                            break;
                        case 'checkbox':
                            $sTitle = $TEXT['CHECKBOX_GROUP'];
                            break;
                        case 'radio':
                            $sTitle = $TEXT['RADIO_BUTTON_GROUP'];
                            break;
                        case 'email':
                            $sTitle = $TEXT['EMAIL_ADDRESS'];
                            break;
                        default:
                        break;
                    endswitch;
                    echo $sTitle;
/**
 * 
                    if($aFields['type'] == 'textfield') {
                        echo $TEXT['SHORT_TEXT'];
                    } elseif($aFields['type'] == 'textarea') {
                        echo $TEXT['LONG_TEXT'];
                    } elseif($aFields['type'] == 'heading') {
                        echo $TEXT['HEADING'];
                    } elseif($aFields['type'] == 'select') {
                        echo $TEXT['SELECT_BOX'];
                    } elseif($aFields['type'] == 'checkbox') {
                        echo $TEXT['CHECKBOX_GROUP'];
                    } elseif($aFields['type'] == 'radio') {
                        echo $TEXT['RADIO_BUTTON_GROUP'];
                    } elseif($aFields['type'] == 'email') {
                        echo $TEXT['EMAIL_ADDRESS'];
                    }
 */
?></td>
                <td style="text-align: center;">
<?php
                if ($aFields['type'] != 'group_begin') {
                    if($aFields['required'] == 1) { echo $TEXT['YES']; } else { echo $TEXT['NO']; }
                }
?>
                </td>
                <td>
<?php
                if ($aFields['type'] == 'select') {
                    $aFields['extra'] = explode(',',$aFields['extra']);
                     if($aFields['extra'][1] == 'multiple') { echo $TEXT['YES']; } else { echo $TEXT['NO']; }
                }
?>
                </td>
                <td style="text-align: center;">
<?php if($aFields['position'] != 1) { ?>
                    <a href="<?php echo $ModuleUrl; ?>move_up.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;field_id=<?php echo $sFielIdkey; ?>&amp;move_id=<?php echo $aFields['field_id']; ?>&amp;position=<?php echo $aFields['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>"> 
                        <img src="<?php echo THEME_URL; ?>/images/up_16.png" alt="^" />
                    </a>
<?php } ?>
                </td>
                <td  style="text-align: center;">
<?php if($aFields['position'] != $num_fields) { ?>
                    <a href="<?php echo $ModuleUrl; ?>move_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;field_id=<?php echo $sFielIdkey; ?>&amp;move_id=<?php echo $aFields['field_id']; ?>&amp;position=<?php echo $aFields['position']; ?>&amp;module=<?php echo $sModulName; ?>" title="<?php echo $TEXT['MOVE_DOWN']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/down_16.png" alt="v" />
                    </a>
<?php } ?>
                </td>
                <td style="text-align: center;">
<?php
                $url = ($ModuleUrl.'delete_field.php?page_id='.$page_id.'&amp;section_id='.$section_id.'&amp;field_id='.$sFielIdkey)
?>
                    <a href="javascript:confirm_link('<?php echo url_encode($TEXT['ARE_YOU_SURE']); ?>','<?php echo $url; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/delete_16.png" border="0" alt="X" />
                    </a>
                </td>
                <td style="text-align: right; padding-right: 5px;">
<?php
if ( DEBUG ) { 
                    echo $aFields['position'];
}
?>
                </td>
            </tr>
<?php
            // Alternate row color
        }
?>
        </tbody>
        </table>
        <?php
    } else {
        echo $TEXT['NONE_FOUND'];
    }
}
?>

<br /><br />

<h2><?php echo $TEXT['SUBMISSIONS']; ?></h2>

<?php
// Query submissions table
/*
$sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_form_submissions`  ';
$sql .= 'WHERE `section_id` = '.(int)$section_id.' ';
$sql .= 'ORDER BY `submitted_when` ASC ';
*/
$sql  = 'SELECT s.*, u.`display_name`, u.`email` ';
$sql .=            'FROM `'.TABLE_PREFIX.'mod_form_submissions` s ';
$sql .= 'LEFT OUTER JOIN `'.TABLE_PREFIX.'users` u ';
$sql .= 'ON u.`user_id` = s.`submitted_by` ';
$sql .= 'WHERE s.`section_id` = '.(int)$section_id.' ';
$sql .= 'ORDER BY s.`submitted_when` DESC ';

if($oSubmissions = $database->query($sql)) {
?>
<!-- submissions -->
    <div class="frm-ScrollTableDiv">
        <table id="frm-ScrollTable">
        <thead class="frm-Scroll">
        <tr id="frm-Scroll">
            <th class="frm-Scroll" style="text-align: center; width: 3%;">&nbsp;</th>
            <th class="frm-Scroll" style="text-align: center; width: 3%;"> ID </th>
            <th class="frm-Scroll" style=" width: 19%;"><?php echo $TEXT['SUBMITTED'] ?></th>
            <th class="frm-Scroll" style=" width: 19%;"><?php echo $TEXT['USER']; ?></th>
            <th class="frm-Scroll" style=" width: 10%;"><?php echo $TEXT['EMAIL'].' '.$MOD_FORM['FROM'] ?></th>
            <th class="frm-Scroll" style="text-align: center; width: 5%;">&nbsp;</th>
            <th class="frm-Scroll" style="text-align: center; width: 5%;">&nbsp;</th>
            <th class="frm-Scroll" style="text-align: center; width: 3%;">&nbsp;</th>
            <th class="frm-Scroll" style="text-align: center; width: 3%;">&nbsp;</th>
        </tr>
        </thead>
        <tfoot>
            <tr><td colspan="9"></td></tr>
        </tfoot>
        <tbody class="scrolling">
<?php
    if($oSubmissions->numRows() > 0) {
        // List submissions
        while($submission = $oSubmissions->fetchRow(MYSQL_ASSOC)) {
            $submission['display_name'] = (($submission['display_name']!=null) ? $submission['display_name'] : '');
            $sBody = $submission['body'];
            $regex = "/[a-z0-9\-_]?[a-z0-9.\-_]+[a-z0-9\-_]?@[a-z0-9.-]+\.[a-z]{2,}/i";
            preg_match ($regex, $sBody, $output);
// workout if output is empty
            $submission['email'] = (isset($output['0']) ? $output['0'] : '');
            $sSubmissionIdkey = $admin->getIDKEY($submission['submission_id']);
?>
            <tr class="frm-Scroll" >
                <td class="frm-Scroll" style="text-align: center; width: 3%;">
                    <a href="<?php echo WB_URL; ?>/modules/form/view_submission.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;submission_id=<?php echo $sSubmissionIdkey; ?>" title="<?php echo $TEXT['OPEN']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/folder_16.png" alt="<?php echo $TEXT['OPEN']; ?>" />
                    </a>
                </td>
                <td class="frm-Scroll" style="padding-right: 15px;text-align: right; width: 3%; font-weight: normal;"><?php echo $submission['submission_id']; ?></td>
                <td class="frm-Scroll" style=" width: 16%;"><?php echo gmdate(DATE_FORMAT.', '.TIME_FORMAT, $submission['submitted_when']+TIMEZONE ); ?></td>
                <td class="frm-Scroll" style=" width: 33%;"><?php echo $submission['display_name']; ?></td>
                <td class="frm-Scroll" style=" width: 30%;" ><?php echo $submission['email']; ?></td>
                <td class="frm-Scroll" style="text-align: center; width: 5%;">&nbsp;</td>
                <td class="frm-Scroll" style=" width: 5%;"  >&nbsp;</td>
                <td class="frm-Scroll"  style="text-align: center; width: 5%;">
<?php 
                $url = (WB_URL.'/modules/form/delete_submission.php?page_id='.$page_id.'&amp;section_id='.$section_id.'&amp;submission_id='.$sSubmissionIdkey)
?>
                    <a href="javascript:confirm_link('<?php echo url_encode($TEXT['ARE_YOU_SURE']); ?>', '<?php echo $url; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
                        <img src="<?php echo THEME_URL; ?>/images/delete_16.png" alt="X" />
                    </a>
                </td>
<?php 
if ( DEBUG ) { ?>
                <td class="frm-Scroll" style=" width: 3%;" ><?php echo $sSubmissionIdkey; ?></td>
<?php } else  { ?>
                <td class="frm-Scroll" style=" width: 3%;" >&nbsp;</td>
<?php }  ?>

            </tr>
<?php
        }
    } else {
?>
<tr><td colspan="8"><?php echo $TEXT['NONE_FOUND'] ?></td></tr>
<?php
    }
?>
        </tbody>
        </table><br />
    </div>
<?php
} else {
    echo $database->get_error().'<br />';
    echo $sql;
}
