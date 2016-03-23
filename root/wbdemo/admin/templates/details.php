<?php
/**
 *
 * @category        admin
 * @package         templates
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: details.php 1625 2012-02-29 00:50:57Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/admin/templates/details.php $
 * @lastmodified    $Date: 2012-02-29 01:50:57 +0100 (Mi, 29. Feb 2012) $
 *
 */
// Include config file and admin class file
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }

require_once(WB_PATH .'/framework/functions.php');
// suppress to print the header, so no new FTAN will be set
$admin = new admin('Addons', 'templates_view', false);

$js_back = ADMIN_URL.'/templates/index.php';

if( !$admin->checkFTAN() )
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL );
}
$admin->print_header();

$file = trim( ( !isset($_POST['file']) || $_POST['file'] == false ) ? '' : $_POST['file'] );

// Get template name
if( $file == '' ) {
    $admin->print_error( $MESSAGE['GENERIC_FORGOT_OPTIONS'], $js_back );
} else {
    $file = preg_replace('/[^a-z0-9_-]/i', "", $file);  // fix secunia 2010-92-2
}

// Check if the template exists
if(!file_exists(WB_PATH.'/templates/'.$file)) {
    $admin->print_error($MESSAGE['GENERIC_NOT_INSTALLED'], $js_back );
}

// Setup template object, parse vars to it, then parse it
// Create new template object
$template = new Template(dirname($admin->correct_theme_source('templates_details.htt')));
// $template->debug = true;
$template->set_file('page', 'templates_details.htt');
$template->set_block('page', 'main_block', 'main');
$template->set_var('FTAN', $admin->getFTAN());

// Insert values
$sql = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
.'WHERE `type` = \'template\' '
.'AND `directory` = \''.$file.'\'';
if($result = $database->query($sql)) {
    $row = $result->fetchRow(MYSQLI_ASSOC);
}
// check if a template description exists for the displayed backend language
$tool_description = false;
if(function_exists('file_get_contents') && file_exists(WB_PATH.'/templates/'.$file.'/languages/'.LANGUAGE .'.php')) {
    // read contents of the template language file into string
    $data = @file_get_contents(WB_PATH .'/templates/' .$file .'/languages/' .LANGUAGE .'.php');
    // use regular expressions to fetch the content of the variable from the string
    $tool_description = get_variable_content('template_description', $data, false, false);
    // replace optional placeholder {WB_URL} with value stored in config.php
    if($tool_description !== false && strlen(trim($tool_description)) != 0) {
        $tool_description = str_replace('{WB_URL}', WB_URL, $tool_description);
    } else {
        $tool_description = false;
    }
}
if($tool_description !== false) {
    // Override the template-description with correct desription in users language
    $row['description'] = $tool_description;
}    

$template->set_var(array(
                                'NAME' => $row['name'],
                                'TYPE' => $row['function'],
                                'AUTHOR' => $row['author'],
                                'DESCRIPTION' => $row['description'],
                                'VERSION' => $row['version'],
                                'DESIGNED_FOR' => $row['platform']
                                )
                        );

// Insert language headings
$template->set_var(array(
                                'HEADING_TEMPLATE_DETAILS' => $HEADING['TEMPLATE_DETAILS']
                                )
                        );
// Insert language text and messages
$template->set_var(array(
                                'TEXT_NAME' => $TEXT['NAME'],
                                'TEXT_TYPE' => $TEXT['TYPE'],
                                'TEXT_AUTHOR' => $TEXT['AUTHOR'],
                                'TEXT_VERSION' => $TEXT['VERSION'],
                                'TEXT_DESIGNED_FOR' => $TEXT['DESIGNED_FOR'],
                                'TEXT_DESCRIPTION' => $TEXT['DESCRIPTION'],
                                'TEXT_BACK' => $TEXT['BACK']
                                )
                        );

// Parse template object
$template->parse('main', 'main_block', false);
$template->pparse('output', 'page');

// Print admin footer
$admin->print_footer();
