<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><?php
/**
 * $Id: index.php 946 2011-07-28 13:25:58Z Badknight $
 * WebsiteBaker template: PinzSimple
 * This is the standart template of WebsiteBaker
 * Feel free to modify or build up on this template.
 *
 * This file defines the template variables required by WebsiteBaker.
 *
 * LICENSE: In combination with any version of any kind of software which is 
 *          produced and originaly published by the WebsiteBaker.org Project 
 *          PinzSimple is available under the GNU General Public License.
 * 
 * @author     pinzweb.at OG
 * @copyright  pinzweb.at OG
 * @license    http://www.gnu.org/licenses/gpl.html
 * @version    0.90
 * @platform   WebsiteBaker 2.8.3
 *
 * WebsiteBaker is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * WebsiteBaker is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
*/

// Must include code to stop this file being access directly
if(!defined('WB_PATH')) { throw new Exception('illegal access!! ['.$_SERVER['PHP_SELF'].']'); }
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo defined('DEFAULT_CHARSET') ? DEFAULT_CHARSET : 'utf-8'; ?>" />
	<meta name="description" content="<?php page_description(); ?>" />
	<meta name="keywords" content="<?php page_keywords(); ?>" />
	
	<?php
	// automatically include optional WB module files (frontend.css)
	register_frontend_modfiles('css');
    ?>
	<link href="<?php echo TEMPLATE_DIR; ?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	
	<link rel="stylesheet" type="text/css" href="<?php echo TEMPLATE_DIR; ?>/css/cssreset.css" media="screen,projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo TEMPLATE_DIR; ?>/css/content.css" media="screen,projection" />

	<?php
		/* You can change the size of the boxes, just load the CSS File, you want
		*  screen_800_600_r.css 800*600 Layout with boxes on the right side 
		*  screen_800_600_l.css 800*600 Layout with boxes on the left side 
		*  screen_1024_760_l.css 1024*768 Layout with boxes on the left side
		*  screen_1024_760_r.css 1024*768 Layout with boxes on the right side 
		*/
		if(PAGE_ID == 3) {
			$css = "screen_800_600_r.css";
		} else if(PAGE_ID == 1) {
			$css = "screen_1024_760_l.css";		
		} else if(PAGE_ID == 4) {
			$css = "screen_1024_760_r.css";			
		} else {
			$css = "screen_800_600_l.css";
		}
		
	?>

	<link rel="stylesheet" type="text/css" href="<?php echo TEMPLATE_DIR; ?>/css/<?PHP echo $css; ?>" media="screen,projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo TEMPLATE_DIR; ?>/css/print.css" media="print" />

	<title><?php page_title('', '[WEBSITE_TITLE]'); ?></title>
	
	<?php
	// automatically include optional WB module files (jquery, frontend.js)
		register_frontend_modfiles('jquery');
		register_frontend_modfiles('js');
	 ?>

</head>

<body>

	<div id="container">
	
		<div id="line_1">
		<div id="logo2"></div>
			<div id="content_1">
				<div id="logo">
			
					<a href="http://www.websitebaker.org" title="WebsiteBaker - Open Source Content Management">
			
						<img src="<?php echo TEMPLATE_DIR; ?>/css/img/logo.png" alt="WebsiteBaker - Open Source Content Management" height="143" width="480" />

					</a>
			
				</div>
			
				<div id="toplink">
			
					<a href="http://www.websitebaker2.org" title="WebsiteBaker - Open Source Content Management">
				
						www.websitebaker.org
				
					</a>
			
				</div>
			
				<div class="clear">
			
				</div>
				
			</div>
		
			<div id="line_2">
		
				<div id="content_2">
									
					<div id="right">
			
					<div id="left">
				
						<div id="menu">
						
							<?php show_menu2(); ?>
					
						</div>
					<?php if (SHOW_SEARCH) { ?>	
						<div id="wb-search">
						
							<div id="search_absolute">
						
								<form name="search" action="<?php echo WB_URL; ?>/search/index.php" method="get">
									<input type="hidden" name="referrer" value="<?php echo defined('REFERRER_ID') ? REFERRER_ID : PAGE_ID; ?>" />
									<input type="text" name="string" value="<?php echo $TEXT['SEARCH']; ?>" onfocus="if(this.value=='<?php echo $TEXT['SEARCH']; ?>') this.value=''" />
									<span id="suche_box_submit">
									<input type="submit" name="submit" value="<?php echo $TEXT['SEARCH']; ?>" />
									</span>
								</form>
								
								
							</div>
						
						</div>
				<?php } ?>					
						<?php if(FRONTEND_LOGIN AND !$wb->is_authenticated()) { ?>
							<div id="login">

								<form name="login" action="<?php echo LOGIN_URL; ?>" method="post">
			
								<table cellpadding="0" cellspacing="0" border="0" width="175" align="center">
									<tr>
										<td>
											<b><?php echo $TEXT['LOGIN']; ?></b><br /><br />
										</td>
									</tr>
									<tr>
										<td class="login3">
											<input type="text" name="username" value="<?php echo $TEXT['USERNAME']; ?>" onfocus="if(this.value=='<?php echo $TEXT['USERNAME']; ?>') this.value=''" />
										</td>
									</tr>

									<tr>
										<td class="login3">
											<input type="password" name="password" value="<?php echo $TEXT['PASSWORD']; ?>" onfocus="if(this.value=='<?php echo $TEXT['PASSWORD']; ?>') this.value=''" />
										</td>
									</tr>
									<tr>
										<td class="login2">
											<input type="submit" name="submit" value="<?php echo $TEXT['LOGIN']; ?>" />
											<a href="<?php echo FORGOT_URL; ?>"><?php echo $TEXT['FORGOT_DETAILS']; ?></a>
											<?php if (is_numeric(FRONTEND_SIGNUP)) { ?>
												<a href="<?php echo SIGNUP_URL; ?>"><?php echo $TEXT['SIGNUP']; ?></a>
											<?php } ?>
										</td>
									</tr>
								</table>
		
								</form>
							</div>
		
					<?php } elseif (FRONTEND_LOGIN AND $wb->is_authenticated()) { ?>
						<div id="login">
						
							<form name="logout" action="<?php echo LOGOUT_URL; ?>" method="post">
			
							<table cellpadding="0" cellspacing="0" border="0" width="175" align="center">
								<tr>
									<td class="login">
										<b><?php echo $TEXT['LOGGED_IN']; ?></b><br /><br />
									</td>
								</tr>
								<tr>
									<td class="login">
										<?php echo $TEXT['WELCOME_BACK']; ?>, <?php echo $wb->get_display_name(); ?><br /><br />
									</td>
								</tr>
								<tr>
									<td class="login">
										<a href="<?php echo PREFERENCES_URL; ?>"><?php echo $MENU['PREFERENCES']; ?></a>
										<input type="submit" name="submit" value="<?php echo $MENU['LOGOUT']; ?>" />
									</td>
								</tr>
							</table>
		
							</form>
						</div>
				<?php } ?>

					</div>
					
					<div id="text">
						
							<?php page_content(1); ?>
							
					</div>
				
					</div>
				
					<div class="clear">
				
					</div>
			
				</div>
		
			</div>
		
		</div>
		
		<div id="line_3">
		
			<div id="line_3_a">
			
				<div id="footer">
				
					<div id="social">
					
						<span id="follow">
						
							Follow us on: 
						
						</span>
						
						<a href="http://twitter.com/#!/websitebaker" title="Twitter" target="_blank"><img src="<?php echo TEMPLATE_DIR; ?>/css/img/twitter.jpg" height="28" width="28" alt="Twitter" /></a>
						<a href="http://www.facebook.com/pages/WebsiteBaker/254526557891443" title="Facebook" target="_blank"><img src="<?php echo TEMPLATE_DIR; ?>/css/img/facebook.jpg" height="28" width="28" alt="Facebook" /></a>
					
					</div>
					
					<div id="copyright">
					
						<?php page_footer(); ?>
					
					</div>
				
				</div>
			
			</div>
			
		</div>
	
	</div>

<?php
// automatically include optional WB module file frontend_body.js)
	// register_frontend_modfiles_body('jquery');
	register_frontend_modfiles_body('js');
?>
</body>

</html>