<?php
/*
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * SimpleCommandDispatcher.inc
 *
 * @category     Addons
 * @package      Addons_Dispatcher
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      3.0.1
 * @lastmodified $Date: $
 * @since        File available since 17.12.2015
 * @description  xyz
 */

class Helpers
{

    protected $oDb  = null;
    protected $oReg = null;
    protected $oApp = null;
    protected $bSqlStatement = false;
    protected $sFilterPath = '';
    protected $aFilters = array();
    protected $aInsertSql = array();
    
    
    public function __construct(  ){
        $this->initialize();
    }

    public function __set($name, $value){
        throw new InvalidArgumentException('tried to set readonly or nonexisting property [ '.$name.' }!! ');
    }

    public function __get($name){
        throw new InvalidArgumentException('tried to get nonexisting property [ '.$name.' }!! ');
    }

    public function set($name, $value){
        $this->$name = $value;
    }

    public function get($name){
        if(is_array($this->$name)){ 
        return implode("\n", $this->$name);
        } else { return $this->$name; }
    }

    protected function initialize() 
    {
        $this->sFilterPath = dirname(__DIR__).'/filters/';
        $this->aFilters    = $this->buildFilterArray();
        $this->aInsertSql  = $this->buildFilterArray(true);
    }

    protected function getFilterInFolder(  ) 
    {
        return glob($this->sFilterPath.'*');
    }

    protected function buildFilterArray( $isSql = false ) {
        $i=1;
        $aFiles = $this->getFilterInFolder();
        $aFilters = array();
        $aInstallData  = array();
        $aInstallData[] = 'INSERT INTO `{TABLE_PREFIX}mod_output_filter` (`name`, `value`) VALUES'."\n";
        foreach ( $aFiles  as $Filter) {
            $sFileName = basename($Filter);
            $key = preg_replace("/\.[^.]+$/", "", $sFileName);
            $aFilters[$key] = true;
            $sFilter = '(\''.$key.'\', \'1\')';
            if( $i < sizeof($aFiles) ) { $sFilter .= ', '."\n"; }else{ $sFilter .= ';'; }
            $i++;
            $aInstallData[] = $sFilter;
        
        }
        return ( $isSql ? $aInstallData : $aFilters );
    }

} // end class Helpers

if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }


$oHelpers = new Helpers();
echo $oHelpers->get( 'sFilterPath' ).'<br />';
echo $oHelpers->get( 'aInsertSql' ).'<br />';
/**
echo $oHelpers;
 * 
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />'; 
print_r( $oHelpers ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die(); 
 */
