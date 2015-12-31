<?php

// $Id: EN.php 1098 2009-07-27 07:37:22Z Ruebenwurzel $

/*

 Website Baker Project <http://www.websitebaker.org/>
 Copyright (C) 2004-2009, Ryan Djurovich

 Website Baker is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Website Baker is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Website Baker; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

// english module description
$module_description     = 'Droplets are small chunks of php code (just like the code module) that can be included in your template or any other content section. Including a droplet is done by encapsulating the droplet name in double brackets.';
// headers and text
$DR_TEXT['ADD']  = 'Add a Droplet';
$DR_TEXT['ADMIN_EDIT']    = 'Edit a Droplet';
$DR_TEXT['ADMIN_VIEW']    = 'View';
$DR_TEXT['BACKUP']        = 'Backup Droplets (Zip)';
$DR_TEXT['COPY']    = 'Copy a Droplet';
$DR_TEXT['DELETE']    = 'Delete a Droplet';
$DR_TEXT['DROPLET']  = 'Droplet';
$DR_TEXT['DROPLETS']  = 'Droplets';
$DR_TEXT['DROPLETS_DELETED'] = 'Droplets deleted successfully.';
$DR_TEXT['HELP']        = 'Help';
$DR_TEXT['IMPORT']        = 'Import selected Droplet';
$DR_TEXT['INVALIDCODE']    = 'This Droplet has invalid PHP code';
$DR_TEXT['INVALID_BACK']  = 'Invalid choice. Back to the overview';
$DR_TEXT['MODIFY']    = 'Edit';
$DR_TEXT['RESTORE']        = 'Droplets restored';
$DR_TEXT['README']        = 'readme.html';
$DR_TEXT['SHOW']  = 'Overview';
$DR_TEXT['SAVE']  = 'Save';
$DR_TEXT['NOTUNIQUE']    = 'This droplet name is used!';
$DR_TEXT['WYSIWYG']        = 'Wysiwyg';
$DR_TEXT['UPLOAD']  = 'Upload';
$DR_TEXT['USED']        = 'This droplet is used on the following page(-s):<br />';


$Droplet_Message = array (
    'ARCHIVE_DELETED' => 'Zip(s)s deleted successfully.',
    'ARCHIVE_NOT_DELETED' => 'Cannot delete the selected Zip(s).',
    'CONFIRM_DROPLET_DELETING' => 'Are you sure you want to delete the selected droplets?',
    'DELETED' => 'Droplets deleted successfully.',
    'DELETE_DROPLETS' => 'Delete a Droplet',
    'MISSING_UNMARKED_ARCHIVE_FILES' => 'No Droplet-File selected to restore.',
    'GENERIC_MISSING_ARCHIVE_FILE' => 'No Zip-File selected to delete!',
    'GENERIC_MISSING_TITLE' => 'Insert a Droplet name.',
    'GENERIC_LOCAL_DOWNLOAD' => 'Download Zip',
    'GENERIC_LOCAL_UPLOAD' => 'Load and restore a locale Zip',
);

$Droplet_Header = array (
    'INDEX' => 'Id',
    'PATH' => 'Folder',
    'FILENAME' => 'Dropletname',
    'DESCRIPTION' => 'Description',
    'SIZE' => 'Size',
    'DATE' => 'Date',
    'SELECT_DROPLET' => 'Select a Zip',
    );



$Droplet_Help = array (
    'DELETE' => 'Delete a Droplet. Click to delete the selected droplet in this row. Durch Auswahl lassen sich auch mehrere Droplets auf einmal löschen. ',

);

$Droplet_Import = array (
      'ARCHIV_LOADED' => 'Zip loaded successfully! Choose one or more droplets to restore.',
      'ARCHIV_IMPORTED' => 'Selected droplets import into the database ! ',
      'UPATE_EXISTING_DROPLETS' => 'Overwrite existing droplets?',
      );

