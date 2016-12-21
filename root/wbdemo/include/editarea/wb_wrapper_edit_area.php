<?php
/**
 *
 * @category        framework
 * @package         include
 * @author          Christophe Dolivet (EditArea), Christian Sommer
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       Website Baker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: wb_wrapper_edit_area.php 2040 2013-12-17 23:52:38Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/include/editarea/wb_wrapper_edit_area.php $
 * @lastmodified    $Date: 2013-12-18 00:52:38 +0100 (Mi, 18. Dez 2013) $
 *
 */

if (!function_exists('loader_help')) {
function loader_help()
{

?>
<script type="text/javascript">
/*<![CDATA[*/
        var url  = '<?php print WB_URL; ?>/include/editarea/edit_area_full.js';
        try{
            script = document.createElement("script");
            script.type = "text/javascript";
            script.src  = url;
            script.charset= "UTF-8";
            head = document.getElementsByTagName("head")[0];
            head[0].appendChild(script);
        }catch(e){
            document.write("<script type='text/javascript' src='" + url + "' charset=\"UTF-8\"><"+"/script>");
        }
/*]]>*/
</script>

<?php }
}
if (!function_exists('getEditareaDefaultSettings')) {
    function getEditareaDefaultSettings() {

    /**
     * toolbar: define the toolbar that will be displayed, each element being separated by a ",".
     * Type: String (combinaison of: "|", "*", "search", "go_to_line", "undo", "redo", "change_smooth_selection", "reset_highlight", "highlight", "word_wrap", "help", "save", "load", "new_document", "syntax_selection")
     * "|" or "separator" make appears a separator in the toolbar.
     * "*" or "return" make appears a line-break in the toolbar
     * Default: "search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, word_wrap, |, help"
     */

    return array (
            'id' => "src"    // should contain the id of the textarea that should be converted into an editor
            ,'language' => "en"
            ,'syntax' => ""
            ,'start_highlight' => false    // if start with highlight
            ,'is_multi_files' => false        // enable the multi file mode (the textarea content is ignored)
            ,'min_width' => 400
            ,'min_height' => 125
            ,'allow_resize' => "y"    // possible values: "no", "both", "x", "y"
            ,'allow_toggle' => true        // true or false
            ,'plugins' => "" // comma separated plugin list
            ,'browsers' => "all"    // all or known
            ,'display' => "onload"         // onload or later
            ,'toolbar' => "search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, word_wrap, |, help"
            ,'begin_toolbar' => ""        //  "new_document, save, load, |"
            ,'end_toolbar' => ""        // or end_toolbar
            ,'font_size' => "12"        // not for IE
            ,'font_family' => "monospace, verdana"    // can be "verdana,monospace". Allow non monospace font but Firefox get smaller tabulation with non monospace fonts. IE doesn't change the tabulation width and Opera doesn't take this option into account...
            ,'cursor_position' => "begin"
            ,'gecko_spellcheck' => false    // enable/disable by default the gecko_spellcheck
            ,'max_undo' => 30
            ,'fullscreen' => false
            ,'is_editable' => true
            ,'word_wrap' => false        // define if the text is wrapped of not in the textarea
            ,'replace_tab_by_spaces' => false
            ,'debug' => false        // used to display some debug information into a newly created textarea. Can be usefull to display trace info in it if you want to modify the code
            ,'show_line_colors' => false    // if the highlight is disabled for the line currently beeing edited (if enabled => heavy CPU use)
            ,'syntax_selection_allow' => "basic,brainfuck,c,coldfusion,cpp,css,html,java,js,pas,perl,php,python,ruby,robotstxt,sql,tsql,vb,xml"
            ,'smooth_selection' => true
            ,'autocompletion' => false    // NOT IMPLEMENTED
            ,'load_callback' => ""        // click on load button (function name)
            ,'save_callback' => ""        // click on save button (function name)
            ,'change_callback' => ""    // textarea onchange trigger (function name)
            ,'submit_callback' => ""    // form submited (function name)
            ,'EA_init_callback' => ""    // EditArea initiliazed (function name)
            ,'EA_delete_callback' => ""    // EditArea deleted (function name)
            ,'EA_load_callback' => ""    // EditArea fully loaded and displayed (function name)
            ,'EA_unload_callback' => ""    // EditArea delete while being displayed (function name)
            ,'EA_toggle_on_callback' => ""             // EditArea toggled on (function name)
            ,'EA_toggle_off_callback' => ""            // EditArea toggled off (function name)
            ,'EA_file_switch_on_callback' => ""        // a new tab is selected (called for the newly selected file)
            ,'EA_file_switch_off_callback' => ""    // a new tab is selected (called for the previously selected file)
            ,'EA_file_close_callback' => ""            // close a tab
        );
    }
}
if (!function_exists('registerEditArea')) {

function registerEditArea(
     $initId = null
    ,$syntax = "php"
    ,$syntax_selection = true
    ,$allow_resize = "both"
    ,$allow_toggle = true
    ,$start_highlight = true
    ,$min_width = 600
    ,$min_height = 450
    ,$toolbar = "default"
    ) {
        $isArray = true;
        if( is_array($initId) ){
        //    $json = json_encode($sInitId, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
            $aInitEditArea = getEditareaDefaultSettings();
            $aInitEditArea = array_merge($aInitEditArea,$initId);
        } else {
            $id = $initId;
            $aInitEditArea = getEditareaDefaultSettings();
            $aInitEditArea['id'] = $initId;
            $aInitEditArea['syntax'] = $syntax;
            $aInitEditArea['syntax_selection_allow'] = $syntax_selection;
            $aInitEditArea['allow_resize'] = $allow_resize;
            $aInitEditArea['allow_toggle'] = $allow_toggle;
            $aInitEditArea['start_highlight'] = $start_highlight;
            $aInitEditArea['min_width'] = $min_width;
            $aInitEditArea['min_height'] = $min_height;
            $aInitEditArea['toolbar'] = $toolbar;
        }
        // set default toolbar if no user defined was specified
        if ($aInitEditArea['toolbar'] == 'default') {
            $aInitEditArea['toolbar'] = 'search, fullscreen, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help';
//            $aInitEditArea['toolbar'] = (!$aInitEditArea['syntax_selection_allow']) ? str_replace('syntax_selection,', '', $aInitEditArea['toolbar']) : $aInitEditArea['toolbar'];
        }
        // check if used Website Baker backend language is supported by EditArea
        $language = 'en';
        if (defined('LANGUAGE') && is_readable(dirname(__FILE__).'/langs/'.strtolower(LANGUAGE).'.js'))
        {
            $aInitEditArea['language'] = strtolower(LANGUAGE);
        }
        // check if highlight syntax is supported by edit_area
        $aInitEditArea['syntax'] = in_array($aInitEditArea['syntax'], array('css', 'html', 'js', 'php', 'xml')) ? $aInitEditArea['syntax'] : 'basic';
        // check if resize option is supported by edit_area
        $aInitEditArea['allow_resize'] = in_array($aInitEditArea['allow_resize'], array('no', 'both', 'x', 'y')) ? $aInitEditArea['allow_resize'] : 'no';
        if(!defined('LOADER_HELP')) {
            loader_help();
            define('LOADER_HELP', true);
        }
        if(version_compare(PHP_VERSION, '5.4', '<'))
        {
            $json = json_encode($aInitEditArea);
        } else {
            $json = json_encode($aInitEditArea, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
        }
        // return the Javascript code
        $result = <<< EOT
        <script type="text/javascript">
            editAreaLoader.init( $json );
        </script>
EOT;
        return $result;
    }
}

if (!function_exists('getEditAreaSyntax')) {
    function getEditAreaSyntax($file)
    {
        // returns the highlight scheme for edit_area
        $syntax = 'php';
        if (is_readable($file)) {
            // extract file extension
            $file_info = pathinfo($file);

            switch ($file_info['extension']) {
                case 'htm': case 'html': case 'htt':
                    $syntax = 'html';
                      break;

                 case 'css':
                    $syntax = 'css';
                      break;

                case 'js':
                    $syntax = 'js';
                    break;

                case 'xml':
                    $syntax = 'xml';
                    break;

                 case 'php': case 'php3': case 'php4': case 'php5':
                    $syntax = 'php';
                      break;

                default:
                    $syntax = 'php';
                    break;
            }
        }
        return $syntax ;
    }
}
