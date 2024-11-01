<?php
/**
 * @package SharePost
 * @author Noah Kretz
 * @version 1.1
 */
/*
Plugin Name: SharePost
Plugin URI: http://www.sharepost.com
Description: Appends the SharePost bookmarking or feed-subscription widget to posts.
Author: Noah Kretz
Version: 1.1
*/

if ( ! defined( 'WP_CONTENT_URL' ) )
{
	define( 'WP_CONTENT_URL', get_option('url') . '/wp-content' );
}

if ( ! defined( 'WP_PLUGIN_URL' ) )
{
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
}

define('SHAREPOST_PATH', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
load_plugin_textdomain( 'sharepost_text_domain', 'wp-content/plugins/sharepost', 'sharepost');

	
class sharepost_plugin
{
    /// SharePost credentials
    private $sharepost_username;
	private $sharepost_password;
	private $sharepost_eacct;
	private $sharepost_location;
	
	/// Button styles
    private $sb_btn_radio;
	private $sb_wtype_control;
	private $sb_btn_height;
	private $sb_btn_width;
	
	/// Language
	private $sb_language_control;									

	/// Custom service list
	private $sb_custom_services;
	private $radioCustom;
	
	/// Advanced look and feel
	private $sb_headertext_control;
	private $sb_theme_control;
	private $txtHeaderText;
	private $txtHeaderBg;
    private $txtBodyText;
	private $txtBodyBg;
	private $txtLinkBg;
	private $sb_leftoffset_control;
	private $sb_topoffset_control;
	
	public static $sb_languages = array('zh-CH'=>'汉语',
										'nl-NL'=>'Nederlands',
										'en-US'=>'English',
										'fr-FR'=>'Français',
										'de-DE'=>'Deutsch',
										'it-IT'=>'Italiano',
										'ko-KR'=>'한국어',
										'ja-JP'=>'日本語',
										'pt-PT'=>'Português',
										'ru-RU'=>'Русский',
										'es-ES'=>'Español');
										
    function sharepost_plugin()
    {

		add_action('admin_head', array(&$this, 'sharepost_admin_head'));		
        add_filter('admin_menu', array(&$this, 'sharepost_admin'));
		add_filter('the_content', array(&$this, 'sharepost_deploy'));
		
        add_option('sharepost_username');
		add_option('sharepost_password');
		add_option('sharepost_eacct');
		add_option('sharepost_location');
		add_option('sharepost_verified');
		add_option('sb_btn_radio',0);
		add_option('sb_btn_height',16);
		add_option('sb_btn_width',125);
		add_option('sb_wtype_control','sb');
	    add_option('sb_language_control', 'en-US');
		add_option('sb_custom_services','');
		add_option('radioCustom',0);
		add_option('sb_headertext_control','Bookmark');
		add_option('sb_theme_control','Black');
		add_option('txtHeaderText','#FFFFFF');
		add_option('txtHeaderBg','#000000');
		add_option('txtBodyText','#000000');
		add_option('txtBodyBg','#FFFFFF');
		add_option('txtLinkBg','#DDDDDD');
		add_option('sb_leftoffset_control','0');
		add_option('sb_topoffset_control','0');
		add_option('sharepost_showonpages', 0);
        add_option('sharepost_showonarchives', 0);
        add_option('sharepost_showoncats', 0);
		
        $this->sharepost_username = get_option('sharepost_username');
		$this->sharepost_password = get_option('sharepost_password');
		$this->sharepost_eacct = get_option('sharepost_eacct');
		$this->sharepost_location = get_option('sharepost_location');
		$this->sharepost_verified = get_option('sharepost_verified');
		$this->sb_btn_radio = get_option('sb_btn_radio');
		$this->sb_btn_height = get_option('sb_btn_height');
		$this->sb_btn_width = get_option('sb_btn_width');
		$this->sb_wtype_control = get_option('sb_wtype_control');
		$this->sb_language_control = get_option('sb_language_control');
		$this->sb_custom_services = get_option('sb_custom_services');
		$this->radioCustom = get_option('radioCustom');
		$this->sb_headertext_control = get_option('sb_headertext_control');
		$this->sb_theme_control = get_option('sb_theme_control');
		$this->txtHeaderText = get_option('txtHeaderText');
		$this->txtHeaderBg = get_option('txtHeaderBg');
		$this->txtBodyText = get_option('txtBodyText');
		$this->txtBodyBg = get_option('txtBodyBg');
		$this->txtLinkBg = get_option('txtLinkBg');
		$this->sb_leftoffset_control = get_option('sb_leftoffset_control');
		$this->sb_topoffset_control = get_option('sb_topoffset_control');
		$this->sharepost_showonposts = get_option('sharepost_showonposts');
		$this->sharepost_showonpages = get_option('sharepost_showonpages');
		$this->sharepost_showonarchives = get_option('sharepost_showonarchives');
		$this->sharepost_showoncats = get_option('sharepost_showoncats');
		
    }

    ///append button code to provided content
    public function sharepost_deploy($content)
    {
		if( 1 == $this->sharepost_verified )
		{
			if (!is_feed())
			{
				if (!(is_archive() && 0 == $this->sharepost_showonarchives) &&
				    !(is_category() && 0 == $this->sharepost_showoncats) &&
				    !(is_page() && 0 == $this->sharepost_showonpages))
				{
					$link  = get_permalink();
					$title = get_the_title($id);
					$svcs = ($this->radioCustom == 1) ? $this->sb_custom_services : '';
				    
					$widget = '<div><a href="http://www.sharepost.com/?langcode=' . $this->sb_language_control . '" onclick="return false;" target="_blank" style="margin:0;padding:0;border:0;">';
					$widget .= '<img src="http://loc1.hitsprocessor.com/sp/' . substr($this->sb_language_control, 0, 2) . '/' . $this->sb_wtype_control . '_btn_' . $this->sb_btn_radio . '.gif"';
					$widget .= 'onmouseover="try{SBPlaceWidget(this,\'' . $this->sb_wtype_control . '\',\'' . $this->sb_headertext_control . '\',\'' . $link . '\',\'' . $title . '\',[' . $svcs . ']);}catch(e){}"';
					$widget .= 'onmouseout="try{SBHideWidget_Start();}catch(e){}" width="' . $this->sb_btn_width . '" height="' . $this->sb_btn_height . '" style="margin:0;padding:0;border:0;" /></a>';
					$widget .= '<script type=\'text/javascript\' id=\'sb_u\' charset=\'utf-8\'></script><script type=\'text/javascript\'>/*<![CDATA[*/' . "\n";
					$widget .= 'if(window.sb_account === undefined){sb_account="' . $this->sharepost_eacct . '"; sb_location=' . $this->sharepost_location . ';sb_pageName=location.pathname;';
					$widget .= 'sb_language = "' . $this->sb_language_control . '";sb_headerText = "' . $this->txtHeaderText . '";sb_headerBg = "' . $this->txtHeaderBg . '";';
					$widget .= 'sb_bodyText = "' . $this->txtBodyText . '";sb_bodyBg = "' . $this->txtBodyBg . '";sb_linkBg = "' . $this->txtLinkBg . '";';
					$widget .= 'sb_offsetTop = "' . $this->sb_topoffset_control . '";sb_offsetLeft = "' . $this->sb_leftoffset_control . '";';
					$widget .= 'sb_hp=((location.href.substr(0,6).toLowerCase()=="https:")?"https":"http");document.getElementById("sb_u").src=sb_hp+"://loc1.hitsprocessor.com/sp/widget-external.js";}' . "\n";
					$widget .= "/*]]>*/</script></div>";
					
					$content .= $widget;
				}
			}
		}
		
		return $content;
    }

    public function sharepost_admin()
    {
        add_options_page('SharePost Options', 'SharePost', 8, __FILE__, 'sharepost_config_menu');
    }
	
	public function sharepost_admin_head()
	{
		echo '<script type="text/javascript" src="' . SHAREPOST_PATH . '/js/sb_config.js"></script>' . "\n";
		echo '<link rel="stylesheet" href="' . SHAREPOST_PATH . '/css/sbw.css"/>' . "\n";
	}
}

function sharepost_config_menu() {
?>
    <div class="wrap">
    <h2>SharePost</h2>
	
		<script>
		function sharepost_display_verify()
		{
			//hide account options and signup form
			document.getElementById('sharepost_account_options').style.display='none';
			document.getElementById('sharepost_signup_form').style.display='none';
			//show form
			document.getElementById('sharepost_verify_form').style.display='block';
		}
		
		function sharepost_handle_verify()
		{
			var jTag=document.getElementById('sharepost_jsonp');
			
			sharepost_in_request = false;
			
			//hide loading spinner
			document.getElementById('sharepost_account_loading').style.display='none';
						
			if(na_sp_ErrorCode===-1){
				//populate hidden fields
				document.getElementById('sharepost_username').value=na_sp_Acct;
				document.getElementById('sharepost_password').value=sb_crypto.decrypt(na_sp_EPass,sb_crypto.key).split('&')[0];
				document.getElementById('sharepost_eacct').value=na_sp_EAcct;
				document.getElementById('sharepost_location').value=na_sp_DataLocationID;
				document.getElementById('sharepost_verified').value=1;
				//clear verify form
				document.getElementById('sharepost_verify_username').value='';
				document.getElementById('sharepost_verify_password').value='';
				document.getElementById('sharepost_verify_error').innerHTML='';
				//show result
				document.getElementById('sharepost_account_status').innerHTML='<?php _e('You have verified the following account:', 'sharepost_text_domain'); ?> <b>' + na_sp_Acct + '</b>';
				document.getElementById('sharepost_account_options').style.display='block';
			}
			else{
				document.getElementById('sharepost_verified').value=0;
				document.getElementById('sharepost_verify_error').innerHTML=na_sp_ErrorDescription;
				document.getElementById('sharepost_verify_form').style.display='block';
			}
			
			document.body.removeChild(jTag);
		}
		function sharepost_request_verify()
		{
			var acct=sb_crypto.encrypt(document.getElementById('sharepost_verify_username').value,sb_crypto.key);
			var pass=sb_crypto.encrypt(document.getElementById('sharepost_verify_password').value,sb_crypto.key);
			var jTag=document.createElement("SCRIPT");
			
			//hide form
			document.getElementById('sharepost_verify_form').style.display='none';
			
			//show loading spinner
			document.getElementById('sharepost_account_loading').style.display='block';
			
			//set timeout handler
			sharepost_in_request=true;
			setTimeout("sharepost_handle_timeout(\"verify\")",15000)
			
			//launch request
			jTag.id='sharepost_jsonp';
			jTag.src='http://www.sharepost.com/widget-api.aspx?action=verify&acct=' + acct + '&pass=' + pass + '&devtoken=' + sb_crypto.key + '&functionname=sharepost_handle_verify()&prefix=na_sp_&widgettype=WordPress Plugin&langCode=<?php _e('en-US','sharepost_text_domain'); ?>';
			document.body.appendChild(jTag);
		}
		
		function sharepost_display_signup()
		{
			//hide account options and verify form
			document.getElementById('sharepost_account_options').style.display='none';
			document.getElementById('sharepost_verify_form').style.display='none';
			//show form
			document.getElementById('sharepost_signup_form').style.display='block';
		}
		function sharepost_request_signup()
		{
			//check password match
			if(document.getElementById('sharepost_signup_password').value === document.getElementById('sharepost_signup_retype_password').value){
				
				var acct=sb_crypto.encrypt(document.getElementById('sharepost_signup_username').value,sb_crypto.key);
				var pass=sb_crypto.encrypt(document.getElementById('sharepost_signup_password').value,sb_crypto.key);
				var siteurl=document.getElementById('sharepost_signup_url').value;
				var cat_control=document.getElementById('sharepost_signup_category');
				var category=cat_control.options[cat_control.selectedIndex].value;
				var lang_control=document.getElementById('sharepost_signup_language');
				var language=lang_control.options[lang_control.selectedIndex].value;
				var tz_control=document.getElementById('sharepost_signup_timezone');
				var timezone=tz_control.options[tz_control.selectedIndex].value;
				var fname=document.getElementById('sharepost_signup_fname').value;
				var lname=document.getElementById('sharepost_signup_lname').value;
				var email=document.getElementById('sharepost_signup_email').value;
				var jTag=document.createElement("SCRIPT");
				
				//hide form
				document.getElementById('sharepost_signup_form').style.display='none';
				
				//show loading spinner
				document.getElementById('sharepost_account_loading').style.display='block';
				
				//set timeout handler
				sharepost_in_request=true;
				setTimeout("sharepost_handle_timeout(\"signup\")",15000)
				
				//launch request
				jTag.id='sharepost_jsonp';
				jTag.src='http://www.sharepost.com/widget-api.aspx?action=signup&acct=' + acct + '&pass=' + pass + '&siteurl=' + siteurl + '&category=' + category + '&language=' + language + '&timezone=' + timezone + '&fname=' + fname + '&lname=' + lname + '&email=' + email + '&devtoken=' + sb_crypto.key + '&functionname=sharepost_handle_signup()&prefix=na_sp_&widgettype=WordPress&langCode=<?php _e('en-US','sharepost_text_domain'); ?>';
				document.body.appendChild(jTag);
			
			}else{
			
				//pass mismatch
				document.getElementById('sharepost_signup_error').innerHTML='<?php _e('Your passwords do not match.','sharepost_text_domain'); ?>';
			
			}
		}
		function sharepost_handle_signup()
		{
			var jTag=document.getElementById('sharepost_jsonp');
			
			sharepost_in_request = false;
			
			//hide loading spinner
			document.getElementById('sharepost_account_loading').style.display='none';
						
			if(na_sp_ErrorCode===-1){
				//populate hidden fields
				document.getElementById('sharepost_username').value=na_sp_Acct;
				document.getElementById('sharepost_password').value=sb_crypto.decrypt(na_sp_EPass,sb_crypto.key).split('&')[0];
				document.getElementById('sharepost_eacct').value=na_sp_EAcct;
				document.getElementById('sharepost_location').value=na_sp_DataLocationID;
				document.getElementById('sharepost_verified').value=1;
				//clear signup form
				document.getElementById('sharepost_verify_username').value='';
				document.getElementById('sharepost_verify_password').value='';
				document.getElementById('sharepost_verify_error').innerHTML='';
				//show result
				document.getElementById('sharepost_account_status').innerHTML='<?php _e('You have verified the following account:', 'sharepost_text_domain'); ?> <b>' + na_sp_Acct + '</b>';
				document.getElementById('sharepost_account_options').style.display='block';
			}
			else{
				document.getElementById('sharepost_verified').value=0;
				document.getElementById('sharepost_signup_error').innerHTML=na_sp_ErrorDescription;
				document.getElementById('sharepost_signup_form').style.display='block';
			}
			
			document.body.removeChild(jTag);
		}
		function sharepost_handle_timeout(actionType)
		{
			if(sharepost_in_request){
				var jTag=document.getElementById('sharepost_jsonp');
				document.body.removeChild(jTag);
				
				//hide loading spinner
				document.getElementById('sharepost_account_loading').style.display='none';
							
				document.getElementById('sharepost_verified').value=0;
				document.getElementById('sharepost_' + actionType + '_error').innerHTML='<?php _e('Request timed out', 'sharepost_text_domain'); ?>';
				document.getElementById('sharepost_' + actionType + '_form').style.display='block';
			}
		}
		function sharepost_display_options()
		{
			//hide signup and verify forms
			document.getElementById('sharepost_signup_form').style.display='none';
			document.getElementById('sharepost_verify_form').style.display='none';
			//show options
			document.getElementById('sharepost_account_options').style.display='block';
		}
		function checkSubmit(action, e) {
			
			if(window.event) // IE
			{
				key = e.keyCode;
			}
			else if(e.which) // Netscape/Firefox/Opera
			{
				key = e.which;
			}
			
			if(key == 13){
				e.returnValue=false;
				e.cancel = true;
				if(action=='signup'){
					sharepost_request_signup();
				}else if(action=='verify'){
					sharepost_request_verify();
				}
			}
		}
		
	</script>
	
    <form id="sb_form" name="sb_form" method="post" action="options.php" onsubmit="setWH();">
    <?php wp_nonce_field('update-options'); ?>
    
	<div id="sharepost_gui">
<div class="sb_tab_bar"><table cellpadding="0" cellspacing="0"><tr><td width="4"><img id="plugin_tab_l" src="<?php echo ( SHAREPOST_PATH . '/images/sp_tab_lc' . (get_option('sharepost_verified') == 1 ? '' : '_active') . '.gif'); ?>"></td><td onclick="setTab('plugin');return false;" id="plugin_tab" <?php echo (get_option('sharepost_verified') == 1 ? '' : 'style="background-image:url(\'' . SHAREPOST_PATH . '/images/sp_tab_fill_active.gif\');color:#0070f2;"'); ?>><?php _e('Plugin Settings', 'sharepost_text_domain') ?></td><td width="3"><img id="plugin_tab_r" src="<?php echo ( SHAREPOST_PATH . '/images/sp_tab_rc' . (get_option('sharepost_verified') == 1 ? '' : '_active') . '.gif'); ?>"></td><td width="4"><img id="basic_tab_l" src="<?php echo ( SHAREPOST_PATH . '/images/sp_tab_lc' . (get_option('sharepost_verified') == 0 ? '' : '_active') . '.gif'); ?>"></td><td onclick="setTab('basic');return false;" id="basic_tab" <?php echo (get_option('sharepost_verified') == 0 ? '' : 'style="background-image:url(\'' . SHAREPOST_PATH . '/images/sp_tab_fill_active.gif\');color:#0070f2;"'); ?>><?php _e('Basic Settings', 'sharepost_text_domain') ?></td><td width="3"><img id="basic_tab_r" src="<?php echo ( SHAREPOST_PATH . '/images/sp_tab_rc' . (get_option('sharepost_verified') == 0 ? '' : '_active') . '.gif'); ?>"></td><td width="4"><img id="services_tab_l" src="<?php echo ( SHAREPOST_PATH . '/images/sp_tab_lc.gif'); ?>"></td><td onclick="setTab('services');return false;" id="services_tab"><?php _e('Choose Services', 'sharepost_text_domain') ?></td><td width="3"><img id="services_tab_r" src="<?php echo ( SHAREPOST_PATH . '/images/sp_tab_rc.gif'); ?>"></td><td width="4"><img id="advanced_tab_l" src="<?php echo ( SHAREPOST_PATH . '/images/sp_tab_lc.gif'); ?>"></td><td onclick="setTab('advanced');return false;" id="advanced_tab"><?php _e('Advanced Settings', 'sharepost_text_domain') ?></td><td width="3"><img id="advanced_tab_r" src="<?php echo ( SHAREPOST_PATH . '/images/sp_tab_rc.gif'); ?>"></td><td width="1"><img src="<?php echo ( SHAREPOST_PATH . '/images/sp_tab_re.gif'); ?>"></td></tr></table></div>
<div id="sb_config_wrapper" <?php echo (get_option('sharepost_verified') == 1 ? '' : 'style="width:734px;"'); ?>>
	<div id="sb_accordion_wrapper">
	<div id="plugin_content" <?php echo (get_option('sharepost_verified') == 1 ? 'style="display:none;"' : ''); ?>>
		<div class="sb_config_header"><?php _e('Plugin Settings', 'sharepost_text_domain') ?></div>
		<div class="sb_config_content">
			<div class="sb_config_label"><?php _e("Account Information", 'sharepost_text_domain') ?></div>
			<div class="sb_config_choiceblock">
				<?php _e('A SharePost account is required to use this plugin.', 'sharepost_text_domain'); ?>
				<br /><br />
				<div id="sharepost_account_options">
					<div id="sharepost_account_status"><?php
						if (get_option('sharepost_verified') == 1)
						{
							_e('You have verified the following account:', 'sharepost_text_domain');
							echo ' <b>' . get_option('sharepost_username') . '</b>';
						}
						else
						{
							_e('You have not provided account information yet.', 'sharepost_text_domain');
						}
					?></div>
					<br />
					<a href="#" onclick="sharepost_display_verify();return false;"><?php _e("Use an existing SharePost account", 'sharepost_text_domain'); ?></a>
					<br />
					<a href="#" onclick="sharepost_display_signup();return false;"><?php _e("Sign up for a new Sharepost account", 'sharepost_text_domain'); ?></a>
				</div>
				<div id="sharepost_verify_form" style="display:none;">
					<div id="sharepost_verify_error" style="color:red;font-weight:bold;"></div>
					<table>
						<tr valign="top">
							<td><?php _e("Account:", 'sharepost_text_domain' ); ?></td>
							<td><input type="text" id="sharepost_verify_username" name="sharepost_verify_username" value="" onkeydown="checkSubmit('verify',event);"/></td>
						</tr>
						<tr valign="top">
							<td><?php _e("Password:", 'sharepost_text_domain' ); ?></td>
							<td><input type="password" id="sharepost_verify_password" name="sharepost_verify_password" value="" onkeydown="checkSubmit('verify',event);"/></td>
						</tr>
						<tr valign="top">
							<td></td>
							<td>
								<p class="submit">
									<input type="button" id="sharepost_verify_button" name="sharepost_verify_button" value="<?php _e('Verify Account', 'sharepost_text_domain'); ?>" onclick="sharepost_request_verify();"/>
									<input type="button" id="sharepost_verify_cancel" name="sharepost_verify_cancel" value="<?php _e('Cancel', 'sharepost_text_domain'); ?>" onclick="sharepost_display_options();"/>
								</p>
							</td>
						</tr>
					</table>
					<br />
					<a href="#" onclick="sharepost_display_signup();return false;"><?php _e("Sign up for a new Sharepost account", 'sharepost_text_domain'); ?></a>
				</div>
				<div id="sharepost_signup_form" style="display:none;">
					<div id="sharepost_signup_error" style="color:red;font-weight:bold;"></div>
					<table>
						<tr valign="top">
							<td><?php _e("Pick an account ID", 'sharepost_text_domain' ); ?>:</td>
							<td><input type="text" id="sharepost_signup_username" name="sharepost_signup_username" value="" onkeydown="checkSubmit('signup',event);"/></td>
						</tr>
						<tr valign="top">
							<td><?php _e("Pick a password", 'sharepost_text_domain' ); ?>:</td>
							<td><input type="password" id="sharepost_signup_password" name="sharepost_signup_password" value="" onkeydown="checkSubmit('signup',event);"/></td>
						</tr>
						<tr valign="top">
							<td><?php _e("Retype Password", 'sharepost_text_domain' ); ?>:</td>
							<td><input type="password" id="sharepost_signup_retype_password" name="sharepost_signup_retype_password" value="" onkeydown="checkSubmit('signup',event);"/></td>
						</tr>
						<tr valign="top">
							<td><?php _e("Site URL", 'sharepost_text_domain' ); ?>:</td>
							<td><input type="text" id="sharepost_signup_url" name="sharepost_signup_url" value="" onkeydown="checkSubmit('signup',event);"/></td>
						</tr>
						<tr valign="top">
							<td><?php _e("Site Category", 'sharepost_text_domain' ); ?>:</td>
							<td><select name="sharepost_signup_category" id="sharepost_signup_category" onkeydown="checkSubmit('signup',event);"><?php _e('<option value="0">Pick a category for your site...</option><option value="1">Advertising</option><option value="2">Agriculture</option><option value="3">Airlines</option><option value="4">Antiques and Collectibles</option><option value="5">Architecture</option><option value="6">Art</option><option value="7">Associations</option><option value="8">Athletic Organizations</option><option value="9">Attorneys</option><option value="10">Automotive</option><option value="11">Banking</option><option value="12">Bar/Tavern</option><option value="13">Baseball</option><option value="14">Basketball</option><option value="15">Beauty/Fashion</option><option value="16">Bed and Breakfast</option><option value="17">Bicycling</option><option value="18">Boating</option><option value="19">Books</option><option value="20">Brokerage</option><option value="21">Business</option><option value="23">Camping</option><option value="22">CD Products</option><option value="24">Celebrities</option><option value="25">Childrens Products</option><option value="26">Cities</option><option value="27">Colleges and Universities</option><option value="28">Commercial</option><option value="29">Computers</option><option value="30">Construction</option><option value="31">Cooking</option><option value="32">Countries</option><option value="33">Crafts</option><option value="34">Doctors</option><option value="35">Education</option><option value="36">Elderly Care</option><option value="37">Emergency Services</option><option value="38">Employment</option><option value="39">Entertainment</option><option value="40">Environmental</option><option value="41">Florists</option><option value="42">Food</option><option value="43">Football</option><option value="44">Games</option><option value="45">Gardening</option><option value="46">Genealogy</option><option value="47">Gifts</option><option value="48">Government and Politics</option><option value="50">Health and Fitness</option><option value="49">Health Care</option><option value="51">History</option><option value="52">Hobbies</option><option value="53">Holiday</option><option value="54">Home</option><option value="55">Hotel/Lodging</option><option value="56">Humanities</option><option value="57">Humor</option><option value="58">Hunting and Fishing</option><option value="59">Information</option><option value="60">Insurance</option><option value="61">International Business</option><option value="62">Internet Resources</option><option value="63">Internet Services</option><option value="64">Jewelry</option><option value="65">Law</option><option value="66">Libraries</option><option value="67">Life style</option><option value="69">Machinery</option><option value="70">Magazines</option><option value="71">Mail Order</option><option value="72">Marketing</option><option value="73">Media</option><option value="74">Medical</option><option value="75">Military</option><option value="76">Movies</option><option value="68">MP3</option><option value="77">Museums</option><option value="78">Music</option><option value="79">Nature</option><option value="80">News Media</option><option value="81">Newspapers</option><option value="82">Organizations</option><option value="83">Personal Homepage</option><option value="84">Pets</option><option value="85">Publications</option><option value="86">Publishers</option><option value="87">Racing</option><option value="88">Radio and Television</option><option value="89">Real Estate</option><option value="90">Recreation</option><option value="91">Relationships</option><option value="92">Religion</option><option value="93">Research</option><option value="94">Restaurants</option><option value="95">Science</option><option value="96">Services</option><option value="97">Shopping</option><option value="98">Software</option><option value="99">Sports</option><option value="100">Technology</option><option value="101">Telecommunications</option><option value="102"></option>', 'sharepost_text_domain') ?></select></td>
						</tr>
						<tr valign="top">
							<td><?php _e("Site Language", 'sharepost_text_domain' ); ?>:</td>
							<td><select name="sharepost_signup_language" id="sharepost_signup_language" onkeydown="checkSubmit('signup',event);"><?php _e('<option value="0">What language is your site in...</option><option value="1">English</option><option value="2">Czech</option><option value="3">Danish</option><option value="4">Dutch</option><option value="5">Chinese</option><option value="6">Estonian</option><option value="7">Finnish</option><option value="8">French</option><option value="9">German</option><option value="10">Greek</option><option value="11">Hebrew</option><option value="12">Hungarian</option><option value="13">Icelandic</option><option value="14">Italian</option><option value="15">Japanese</option><option value="16">Korean</option><option value="17">Latvian</option><option value="18">Lithuanian</option><option value="19">Norwegian</option><option value="20">Polish</option><option value="21">Romanian</option><option value="22">Portuguese</option><option value="23">Russian</option><option value="24">Spanish</option><option value="25">Swedish</option><option value="26">Afrikaans</option><option value="27">Albanian</option><option value="28">Alsatian</option><option value="29">Amharic</option><option value="30">Arabic</option><option value="31">Armenian</option><option value="32">Assamese</option><option value="33">Asturian</option><option value="34">Azeri</option><option value="35">Bashkir</option><option value="36">Basque</option><option value="37">Belarusian</option><option value="38">Bengali</option><option value="39">Bosnian</option><option value="40">Breton</option><option value="41">Bulgarian</option><option value="42">Catalan</option><option value="43">Corsican</option><option value="44">Croatian</option><option value="45">Dari</option><option value="46">Divehi</option><option value="47">Faeroese</option><option value="48">Filipino</option><option value="49">Frisian</option><option value="50">Friulian</option><option value="51">Galician</option><option value="52">Georgian</option><option value="53">Greenlandic</option><option value="54">Gujarati</option><option value="55">Hausa</option><option value="56">Hindi</option><option value="57">Indonesian</option><option value="58">Inuktitut</option><option value="59">Irish</option><option value="60">isiXhosa Xhosa</option><option value="61">isiZulu Zulu</option><option value="62">Kannada</option><option value="63">Kazakh</option><option value="64">Khmer</option><option value="65">Kiche</option><option value="66">Kinyarwanda</option><option value="67">Konkani</option><option value="68">Kyrgyz</option><option value="69">Lao</option><option value="70">Lower Sorbian</option><option value="71">Luxembourgish</option><option value="72">Macedonian</option><option value="73">Malay</option><option value="74">Malayalam</option><option value="75">Maltese</option><option value="76">Maori</option><option value="77">Mapudungun</option><option value="78">Marathi</option><option value="79">Mohawk</option><option value="80">Mongolian</option><option value="81">Nepali</option><option value="82">Norwegian Bokmål</option><option value="83">Norwegian Nynorsk</option><option value="84">Occitan</option><option value="85">Oriya</option><option value="86">Pashto</option><option value="87">Persian</option><option value="88">Punjabi</option><option value="89">Quechua</option><option value="90">Romansh</option><option value="91">Sami</option><option value="92">Sanskrit</option><option value="93">Serbian</option><option value="94">Sesotho sa Leboa</option><option value="95">Setswana Tswana</option><option value="96">Sinhala</option><option value="97">Slovak</option><option value="98">Slovenian</option><option value="99">Swahili</option><option value="100">Syriac</option><option value="101">Tajik</option><option value="102">Tamazight</option><option value="103">Tamil</option><option value="104">Tatar</option><option value="105">Telugu</option><option value="106">Thai</option><option value="107">Tibetan</option><option value="108">Turkish</option><option value="109">Turkmen</option>', 'sharepost_text_domain') ?></select></td>
						</tr>
						<tr valign="top">
							<td><?php _e("Your time zone", 'sharepost_text_domain' ); ?>:</td>
							<td><select name="sharepost_signup_timezone" id="sharepost_signup_timezone" onkeydown="checkSubmit('signup',event);"><?php _e('<option value="0">What time zone do you live in...</option><option value="1">GMT -11:00 - Midway Islands Time (MIT)</option><option value="2">GMT -10:00 - Hawaii Standard Time (HST)</option><option value="3">GMT -9:00 - Alaska Standard Time (AST)</option><option value="4">GMT -8:00 - Pacific Standard Time (PST)</option><option value="32">GMT -8:00 - Mexico/Pacific Standard Time (PXT)</option><option value="5">GMT -7:00 - Phoenix Standard Time (PNT)</option><option value="6">GMT -7:00 - Mountain Standard Time (MST)</option><option value="33">GMT -7:00 - Mexico/Mountain Standard Time (MXT)</option><option value="7">GMT -6:00 - Central Standard Time (CST)</option><option value="34">GMT -6:00 - Mexico/Central Standard Time (CXT)</option><option value="8">GMT -5:00 - Eastern Standard Time (EST)</option><option value="9">GMT -5:00 - Indiana Eastern Standard Time (IET)</option><option value="10">GMT -4:00 - Puerto Rico/US Virgin Islands Time (PRT)</option><option value="11">GMT -3:30 - Canada Newfoundland Time (CNT)</option><option value="12">GMT -3:00 - Argentina Standard Time (AGT)</option><option value="13">GMT -3:00 - Brazil Eastern Time (BET)</option><option value="14">GMT -1:00 - Central African Time (CAT)</option><option value="15">GMT 0:00 - Greenwich Mean Time (GMT)</option><option value="16">GMT +1:00 - European Central Time (ECT)</option><option value="17">GMT +1:00 - Eastern European Time (EET)</option><option value="18">GMT +2:00 - (Arabic) Egypt Standard Time (ART)</option><option value="19">GMT +3:00 - Eastern African Time (EAT)</option><option value="20">GMT +3:30 - Middle East Time (MET)</option><option value="21">GMT +4:00 - Near East Time (NET)</option><option value="22">GMT +5:00 - Pakistan Lahore Time (PLT)</option><option value="23">GMT +5:30 - India Standard Time (IST)</option><option value="24">GMT +6:00 - Bangladesh Standard Time (BST)</option><option value="25">GMT +7:00 - Vietnam Standard Time (VST)</option><option value="26">GMT +8:00 - China Taiwan Time (CTT)</option><option value="27">GMT +9:00 - Japan Standard Time (JST)</option><option value="28">GMT +9:30 - Australia Central Time (ACT)</option><option value="29">GMT +10:00 - Australia Eastern Time (AET)</option><option value="30">GMT +11:00 - Solomon Standard Time (SST)</option><option value="31">GMT +12:00 - New Zealand Standard Time (NST)</option>', 'sharepost_text_domain') ?></select></td>
						</tr>
						<tr valign="top">
							<td><?php _e("Your first name", 'sharepost_text_domain' ); ?>:</td>
							<td><input type="text" id="sharepost_signup_fname" name="sharepost_signup_fname" value="" onkeydown="checkSubmit('signup',event);"/></td>
						</tr>
						<tr valign="top">
							<td><?php _e("Your last name", 'sharepost_text_domain' ); ?>:</td>
							<td><input type="text" id="sharepost_signup_lname" name="sharepost_signup_lname" value="" onkeydown="checkSubmit('signup',event);"/></td>
						</tr>
						<tr valign="top">
							<td><?php _e("Your e-mail address", 'sharepost_text_domain' ); ?>:</td>
							<td><input type="text" id="sharepost_signup_email" name="sharepost_signup_email" value="" onkeydown="checkSubmit('signup',event);"/></td>
						</tr>
						<tr valign="top">
							<td></td>
							<td>
								<p class="submit">
									<input type="button" id="sharepost_signup_button" name="sharepost_signup_button" value="<?php _e('Sign Up', 'sharepost_text_domain'); ?>" onclick="sharepost_request_signup();"/>
									<input type="button" id="sharepost_signup_cancel" name="sharepost_signup_cancel" value="<?php _e('Cancel', 'sharepost_text_domain'); ?>" onclick="sharepost_display_options();"/>
								</p>
							</td>
						</tr>
					</table>
					<br />
					<a href="#" onclick="sharepost_display_verify();return false;"><?php _e("Use an existing SharePost account", 'sharepost_text_domain'); ?></a>
				</div>

				<div id="sharepost_account_loading" style="display:none;"><img src="<?php echo SHAREPOST_PATH; ?>/images/loading_animated.gif"></div>
			</div>
			<br/>
			<div class="sb_config_label"><?php _e('WordPress Options', 'sharepost_text_domain') ?></div>
			<div class="sb_config_choiceblock">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e("Show on archives:", 'sharepost_text_domain' ); ?></th>
						<td>
							<input type="radio" id="sharepost_showonarchives_yes" name="sharepost_showonarchives" value="1" <?php echo (get_option('sharepost_showonarchives') == 1 ? 'checked' : ''); ?>/> <?php _e('Yes', 'sharepost_text_domain') ?> &nbsp;&nbsp;
							<input type="radio" id="sharepost_showonarchives_no" name="sharepost_showonarchives" value="0" <?php echo (get_option('sharepost_showonarchives') == 0 ? 'checked' : ''); ?>/> <?php _e('No', 'sharepost_text_domain') ?> &nbsp;&nbsp;
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e("Show on categories:", 'sharepost_text_domain' ); ?></th>
						<td>
							<input type="radio" id="sharepost_showoncats_yes" name="sharepost_showoncats" value="1" <?php echo (get_option('sharepost_showoncats') == 1 ? 'checked' : ''); ?>/> <?php _e('Yes', 'sharepost_text_domain') ?> &nbsp;&nbsp;
							<input type="radio" id="sharepost_showoncats_no" name="sharepost_showoncats" value="0" <?php echo (get_option('sharepost_showoncats') == 0 ? 'checked' : ''); ?>/> <?php _e('No', 'sharepost_text_domain') ?> &nbsp;&nbsp;
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e("Show on pages:", 'sharepost_text_domain' ); ?></th>
						<td>
							<input type="radio" id="sharepost_showonpages_yes" name="sharepost_showonpages" value="1" <?php echo (get_option('sharepost_showonpages') == 1 ? 'checked' : ''); ?>/> <?php _e('Yes', 'sharepost_text_domain') ?> &nbsp;&nbsp;
							<input type="radio" id="sharepost_showonpages_no" name="sharepost_showonpages" value="0" <?php echo (get_option('sharepost_showonpages') == 0 ? 'checked' : ''); ?>/> <?php _e('No', 'sharepost_text_domain') ?> &nbsp;&nbsp;
						</td>
					</tr>
				</table>
				<input type="hidden" id="sharepost_username" name="sharepost_username" value="<?php echo get_option('sharepost_username'); ?>" />
				<input type="hidden" id="sharepost_password" name="sharepost_password" value="<?php echo get_option('sharepost_password'); ?>" />
				<input type="hidden" id="sharepost_eacct" name="sharepost_eacct" value="<?php echo get_option('sharepost_eacct'); ?>" />
				<input type="hidden" id="sharepost_location" name="sharepost_location" value="<?php echo get_option('sharepost_location'); ?>" />
				<input type="hidden" id="sharepost_verified" name="sharepost_verified" value="<?php echo get_option('sharepost_verified'); ?>" />
			</div>
		</div>
	</div>
	<div id="basic_content" <?php echo (get_option('sharepost_verified') == 1 ? '' : 'style="display:none;"'); ?>>
		<div class="sb_config_header"><?php _e('Basic Settings', 'sharepost_text_domain') ?></div>
		<div class="sb_config_content">
			<div class="sb_config_label"><?php _e('Widget Type', 'sharepost_text_domain') ?></div>
			<div class="sb_config_choiceblock">
				<select id="sb_wtype_control" name="sb_wtype_control" onchange="updateType(this);">
					<option value="sb" <?php echo (get_option('sb_wtype_control') == 'sb' ? 'selected' : ''); ?>><?php _e('Sharing Button', 'sharepost_text_domain') ?></option>

					<option value="rss" <?php echo (get_option('sb_wtype_control') == 'rss' ? 'selected' : ''); ?>><?php _e('RSS Feed Button', 'sharepost_text_domain') ?></option>
				</select>
			</div>
			<div class="sb_config_label"><?php _e('Language', 'sharepost_text_domain') ?></div>
			<div class="sb_config_choiceblock">
				<select id="sb_language_control" name="sb_language_control" onchange="updateLang();">
				<?php
                    foreach (sharepost_plugin::$sb_languages as $lang_code=>$lang_name)
                    {
                        echo '<option value="' . $lang_code . '"' . ($lang_code == get_option('sb_language_control') ? ' selected':'') . '>' . $lang_name . '</option>';
                    }
                ?>
				</select>
			</div>
			<div class="sb_config_label"><?php _e('Button Style', 'sharepost_text_domain') ?></div>
			<div class="sb_config_choiceblock">
				<div id="sb_button_choices">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr valign="top">
							<td width="20">
								<input id="sb_btn_radio_0" type="radio" name="sb_btn_radio" value="0" <?php echo (get_option('sb_btn_radio') == 0 ? 'checked' : ''); ?> onclick="redrawButtons();">
							</td>
							<td>
								<label for="sb_btn_radio_0"><img id="sb_image_0" src="<?php echo SHAREPOST_PATH; ?>/images/sp/en/sb_btn_0.gif"></label>

							</td>
							<td width="40" align="right">
								<input id="sb_btn_radio_1" type="radio" name="sb_btn_radio" value="1" <?php echo (get_option('sb_btn_radio') == 1 ? 'checked' : ''); ?> onclick="redrawButtons();">
							</td>
							<td>
								<label for="sb_btn_radio_1"><img id="sb_image_1" src="<?php echo SHAREPOST_PATH; ?>/images/sp/en/sb_btn_1.gif"></label>
							</td>
						</tr>
						<tr valign="top"><td colspan="4">&nbsp;</td></tr>

						<tr valign="top">
							<td width="20">
								<input id="sb_btn_radio_2" type="radio" name="sb_btn_radio" value="2" <?php echo (get_option('sb_btn_radio') == 2 ? 'checked' : ''); ?> onclick="redrawButtons();">
							</td>
							<td>
								<label for="sb_btn_radio_2"><img id="sb_image_2" src="<?php echo SHAREPOST_PATH; ?>/images/sp/en/sb_btn_2.gif"></label>
							</td>
							<td width="40" align="right">
								<input id="sb_btn_radio_3" type="radio" name="sb_btn_radio" value="3" <?php echo (get_option('sb_btn_radio') == 3 ? 'checked' : ''); ?> onclick="redrawButtons();">

							</td>
							<td>
								<label for="sb_btn_radio_3"><img id="sb_image_3" src="<?php echo SHAREPOST_PATH; ?>/images/sp/en/sb_btn_3.gif"></label>
							</td>
						</tr>
						<tr valign="top"><td colspan="4">&nbsp;</td></tr>
						<tr valign="top">
							<td width="20">
								<input id="sb_btn_radio_4" type="radio" name="sb_btn_radio" value="4" <?php echo (get_option('sb_btn_radio') == 4 ? 'checked' : ''); ?> onclick="redrawButtons();">

							</td>
							<td>
								<label for="sb_btn_radio_4"><img id="sb_image_4" src="<?php echo SHAREPOST_PATH; ?>/images/sp/en/sb_btn_4.gif"></label>
							</td>
							<td width="40" align="right">
								<input id="sb_btn_radio_5" type="radio" name="sb_btn_radio" value="5" <?php echo (get_option('sb_btn_radio') == 5 ? 'checked' : ''); ?> onclick="redrawButtons();">
							</td>
							<td>
								<label for="sb_btn_radio_5"><img id="sb_image_5" src="<?php echo SHAREPOST_PATH; ?>/images/sp/en/sb_btn_5.gif"></label>

							</td>
						</tr>
					</table>
				</div>
               <input type="hidden" name="sb_btn_height" id="sb_btn_height" value="<?php echo get_option('sb_btn_height'); ?>">
               <input type="hidden" name="sb_btn_width" id="sb_btn_width" value="<?php echo get_option('sb_btn_width'); ?>">
			</div>
            <div id="sb_feedurl_block" style="display:none;">
			    <div class="sb_config_label"><?php _e('Feed URL', 'sharepost_text_domain') ?></div>
   			<div class="sb_config_choiceblock">
	    			<input type="text" name="sb_feedurl_control" id="sb_feedurl_control" style="width:250px;">
               </div>
           </div>

		</div>
   </div>
	<div id="services_content" style="display:none;">
		<div class="sb_config_header"><?php _e('Choose Services', 'sharepost_text_domain') ?></div>
		<div class="sb_config_content">
			<div class="sb_config_label"><?php _e('Services to Display', 'sharepost_text_domain') ?></div>
			<div class="sb_config_choiceblock">
				<div><input id="sb_services_radio_default" name="radioCustom" type="radio" value="0" onclick="toggleCustom(true);" <?php echo (get_option('radioCustom') == 0 ? 'checked' : ''); ?>> <?php _e('Default (Top sites for selected language)', 'sharepost_text_domain') ?></div>

				<div><input id="sb_services_radio_custom" name="radioCustom" type="radio" value="1" onclick="toggleCustom(false);" <?php echo (get_option('radioCustom') == 1 ? 'checked' : ''); ?>> <?php _e('Custom (Services will appear in the order selected)', 'sharepost_text_domain') ?></div>
				<br />
				<div><a href="JavaScript:clearCustom();"><?php _e('De-select All', 'sharepost_text_domain') ?></a></div>
				<div id="sb_services">
                   <div class="sb_clear"></div>
				</div>
				<input type="hidden" id="sb_custom_services_old" name="sb_custom_services_old" value="<?php echo get_option('sb_custom_services'); ?>">
				<input type="hidden" id="sb_custom_services" name="sb_custom_services" value="<?php echo get_option('sb_custom_services'); ?>">
				<br />

				<div><?php _e('Are we missing a site?', 'sharepost_text_domain') ?> <a href="http://www.sharepost.com/service-submission.aspx" target="_blank"><?php _e('Let us know.', 'sharepost_text_domain') ?></a></div>
			</div>
		</div>
   </div>
	<div id="advanced_content" style="display:none;">
		<div class="sb_config_header"><?php _e('Advanced Settings', 'sharepost_text_domain') ?></div>
		<div class="sb_config_content">

			<div class="sb_config_label"><?php _e('Header Text', 'sharepost_text_domain') ?></div>
			<div class="sb_config_choiceblock">
				<input type="text" name="sb_headertext_control" id="sb_headertext_control" maxlength="40" value="<?php echo get_option('sb_headertext_control'); ?>" onkeyup="UpdateHeaderText(this.value,true)" onblur="UpdateHeaderText(this.value,true)">
			</div>
			<div class="sb_config_label"><?php _e('Colors', 'sharepost_text_domain') ?></div>
			<div class="sb_config_choiceblock">
				<table cellpadding="0" cellspacing="0" width="330">
					<tr>

						<td width="130"><?php _e('Theme', 'sharepost_text_domain') ?><br /><br /></td>
						<td colspan="2">
	            			<select id="sb_theme_control" name="sb_theme_control" onchange="updateTheme();">
							<?php
								echo '<option value="Black"' . ('Black' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('Black (default)','sharepost_text_domain') . '</option>';
								echo '<option value="Blue"' . ('Blue' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('Baby Blue','sharepost_text_domain') . '</option>';
								echo '<option value="GunmetalGray"' . ('GunmetalGray' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('Gunmetal Gray','sharepost_text_domain') . '</option>';
								echo '<option value="LemonLime"' . ('LemonLime' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('Lemon Lime','sharepost_text_domain') . '</option>';
								echo '<option value="MSDOS"' . ('MSDOS' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('MS DOS','sharepost_text_domain') . '</option>';
								echo '<option value="PrettyInPink"' . ('PrettyInPink' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('Pretty in Pink','sharepost_text_domain') . '</option>';
								echo '<option value="Raspberry"' . ('Raspberry' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('Raspberry','sharepost_text_domain') . '</option>';
								echo '<option value="RebelRed"' . ('RebelRed' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('Rebel Red','sharepost_text_domain') . '</option>';
								echo '<option value="SharePostBlue"' . ('SharePostBlue' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('SharePost Blue','sharepost_text_domain') . '</option>';
								echo '<option value="Silver"' . ('Silver' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('Silver','sharepost_text_domain') . '</option>';
								echo '<option value="Watermelon"' . ('Watermelon' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('Watermelon','sharepost_text_domain') . '</option>';
								echo '<option value="Custom"' . ('Custom' == get_option('sb_theme_control') ? ' selected':'') . '>' . __('Custom','sharepost_text_domain') . '</option>';
							?>
			            	</select><br /><br />
						</td>
					</tr>
				</table>
			</div>
			<div class="sb_config_choiceblock" id="sb_customcolors_control" style="display:none;">

				<table cellpadding="0" cellspacing="0" width="330">
					<tr>
						<td width="130"><?php _e('Header/Footer Text', 'sharepost_text_domain') ?></td>
						<td width="30">
							<div id="headerTextSwatch" style="background-color:#FFFFFF;width:20px;height:15px;border:1px solid #000000;" onclick="openColorPickerAdv(1);"></div>
							<div id="colorPickerAdvDiv1" style="visibility:hidden;padding:0;position:absolute;z-index:1"><IFRAME SRC="<?php echo SHAREPOST_PATH ?>/colorPickerAdv.html?item=1&controlName=txtHeaderText" WIDTH="350" HEIGHT="142" STYLE="border-style:outset;border-width:2px;" marginwidth=0 marginheight=0 noresize frameborder=0 border=0></IFRAME></div>
						</td>
						<td>

							<input type="text" size="8" name="txtHeaderText" id="txtHeaderText" value="<?php echo get_option('txtHeaderText'); ?>" onblur="UpdateColors(this.value, 'headerTextSwatch', 'sb_header', 'text');UpdateColors(this.value, 'headerTextSwatch', 'sb_footer', 'text');">
						</td>
					</tr>
					<tr>
						<td width="130"><?php _e('Header/Footer Background', 'sharepost_text_domain') ?></td>
						<td width="30">
							<div id="headerBgSwatch" style="background-color:#666666;width:20px;height:15px;border:1px solid #000000;" onclick="openColorPickerAdv(2);"></div>
							<div id="colorPickerAdvDiv2" style="visibility:hidden;padding:0;position:absolute;z-index:1"><IFRAME SRC=<?php echo SHAREPOST_PATH ?>/colorPickerAdv.html?item=2&controlName=txtHeaderBg WIDTH=350 HEIGHT=142 STYLE="border-style:outset;border-width:2px;" marginwidth=0 marginheight=0 noresize frameborder=0 border=0></IFRAME></div>

						</td>
						<td>
							<input type="text" size="8" name="txtHeaderBg" id="txtHeaderBg" value="<?php echo get_option('txtHeaderBg'); ?>" onblur="UpdateColors(this.value, 'headerBgSwatch', 'sb_header', 'bg');UpdateColors(this.value, 'headerBgSwatch', 'sb_footer', 'bg');">
						</td>
					</tr>
					<tr>
						<td width="130"><?php _e('Body Text', 'sharepost_text_domain') ?></td>
						<td width="30">

							<div id="bodyTextSwatch" style="background-color:#333333;width:20px;height:15px;border:1px solid #000000;" onclick="openColorPickerAdv(3);"></div>
							<div id="colorPickerAdvDiv3" style="visibility:hidden;padding:0;position:absolute;z-index:1"><IFRAME SRC=<?php echo SHAREPOST_PATH ?>/colorPickerAdv.html?item=3&controlName=txtBodyText WIDTH=350 HEIGHT=142 STYLE="border-style:outset;border-width:2px;" marginwidth=0 marginheight=0 noresize frameborder=0 border=0></IFRAME></div>
						</td>
						<td>
							<input type="text" size="8" name="txtBodyText" id="txtBodyText" value="<?php echo get_option('txtBodyText'); ?>" onblur="UpdateColors(this.value, 'bodyTextSwatch', '', 'text');UpdateLinkColor(this.value,'text');">
						</td>
					</tr>
					<tr>

						<td width="130"><?php _e('Body Background', 'sharepost_text_domain') ?></td>
						<td width="30">
							<div id="bodyBgSwatch" style="background-color:#FFFFFF;width:20px;height:15px;border:1px solid #000000;" onclick="openColorPickerAdv(4);"></div>
							<div id="colorPickerAdvDiv4" style="visibility:hidden;padding:0;position:absolute;z-index:1"><IFRAME SRC=<?php echo SHAREPOST_PATH ?>/colorPickerAdv.html?item=4&controlName=txtBodyBg WIDTH=350 HEIGHT=142 STYLE="border-style:outset;border-width:2px;" marginwidth=0 marginheight=0 noresize frameborder=0 border=0></IFRAME></div>
						</td>
						<td>
							<input type="text" size="8" name="txtBodyBg" id="txtBodyBg" value="<?php echo get_option('txtBodyBg'); ?>" onblur="UpdateColors(this.value, 'bodyBgSwatch', 'sb_popup', 'bg');">
						</td>

					</tr>
					<tr>
						<td width="130"><?php _e('Link Highlight', 'sharepost_text_domain') ?></td>
						<td width="30">
							<div id="linkBgSwatch" style="background-color:#FFFFFF;width:20px;height:15px;border:1px solid #000000;" onclick="openColorPickerAdv(5);"></div>
							<div id="colorPickerAdvDiv5" style="visibility:hidden;padding:0;position:absolute;z-index:1"><IFRAME SRC=<?php echo SHAREPOST_PATH ?>/colorPickerAdv.html?item=5&controlName=txtLinkBg WIDTH=350 HEIGHT=142 STYLE="border-style:outset;border-width:2px;" marginwidth=0 marginheight=0 noresize frameborder=0 border=0></IFRAME></div>
						</td>
						<td>

							<input type="text" size="8" name="txtLinkBg" id="txtLinkBg" value="<?php echo get_option('txtLinkBg'); ?>" onblur="UpdateColors(this.value, 'linkBgSwatch', '', 'bg');UpdateLinkColor(this.value,'bg');">
						</td>
					</tr>
				</table>
			</div>
			<div class="sb_config_label"><?php _e('Positioning', 'sharepost_text_domain') ?></div>
			<div class="sb_config_choiceblock">
				<table cellpadding="0" cellspacing="0" width="330">

					<tr>
						<td width="130"><?php _e('Left Offset', 'sharepost_text_domain') ?></td>
						<td>
							<input type="text" name="sb_leftoffset_control" id="sb_leftoffset_control" size="4" maxlength="3" value="<?php echo get_option('sb_leftoffset_control'); ?>" onblur="UpdateOffset(this.value, 'sb_popup', 'left');" onkeyup="UpdateOffset(this.value, 'sb_popup', 'left');">
						</td>
					</tr>
					<tr>
						<td width="130"><?php _e('Top Offset', 'sharepost_text_domain') ?></td>

						<td>
							<input type="text" name="sb_topoffset_control" id="sb_topoffset_control" size="4" maxlength="3" value="<?php echo get_option('sb_topoffset_control'); ?>" onblur="UpdateOffset(this.value, 'sb_popup', 'top');" onkeyup="UpdateOffset(this.value, 'sb_popup', 'top');">
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	</div>

	
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="sharepost_username,sharepost_password,sharepost_eacct,sharepost_location,sharepost_verified,sharepost_showonarchives,sharepost_showoncats,sharepost_showonpages,sb_wtype_control,sb_language_control,sb_btn_radio,sb_btn_height,sb_btn_width,sb_custom_services,radioCustom,sb_headertext_control,sb_theme_control,txtHeaderText,txtHeaderBg,txtBodyText,txtBodyBg,txtLinkBg,sb_leftoffset_control,sb_topoffset_control" />

    <p class="submit">
	<input type="button" name="Submit" value="<?php _e('Save Changes','sharepost_text_domain') ?>" onclick="this.form.submit();" />
    <input type="button" value="<?php _e('Reset Form', 'sharepost_text_domain') ?>" onclick="resetAll();">
    </p>
</form>
</div>
<div id="sb_preview_wrapper"<?php echo (get_option('sharepost_verified') == 1 ? '' : 'style="display:none;"'); ?>>
   <div class="sb_config_header"><?php _e('Preview', 'sharepost_text_domain') ?></div>
   <div class="sb_config_content">

      <img id="sb_button" src="<?php echo SHAREPOST_PATH ?>/images/sp/en/sb_btn_0.gif">      <div id="sb_popup">
         <div id="sb_header"><?php _e('Bookmark', 'sharepost_text_domain') ?></div>
         <div id="sb_body"></div>
         <a class="sb_footer" id="sb_footer" href="#"><img id="sb_logo" border="0" src="<?php echo SHAREPOST_PATH ?>/images/sp_w_logo.gif"></a>
      </div>
   </div>
</div>
<div class="sb_clear"></div>
<script type="text/javascript">
  sb_path="<?php echo SHAREPOST_PATH ?>";
  resetAll();
</script>

</div>
    </div>
<?php
}

// If we're not running in PHP 4, initialize
if (strpos(phpversion(), '4') !== 0) {
    $sharepost &= new sharepost_plugin();
}

?>