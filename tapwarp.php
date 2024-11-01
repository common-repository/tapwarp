<?php
/*
Plugin Name: Tap Warp
Plugin URI: http://twdemo.qaon.net/
Description: Upload pictures from smartphone to wordpress easily.
Version:     0.1.9
Author:      Yuxiang Mao
Author URI:  http://qaon.net
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: TapWarp
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$is_tapwarp_upload_page=
	strstr($_SERVER['REQUEST_URI'],'/upload.php') ||
	strstr($_SERVER['REQUEST_URI'],'/media-new.php');

add_action( 'admin_init', 'tapwarp_on_admin_init' );

$tapwarp_use_image_server='wpimgsrv.qaon.net';

function tapwarp_on_admin_init()
{
	global $is_tapwarp_upload_page;
	if ($is_tapwarp_upload_page)
	{
		wp_enqueue_style( 'tapwarp-admin', plugin_dir_url(__FILE__).'css/tapwarp-admin.css' );
		wp_enqueue_script( 'tapwarp-check-upload', plugin_dir_url(__FILE__).'js/checkupload.js' );
	}

	if ( is_admin() )
	{
		register_setting( 'tapwarp-option-group' , 'tapwarp_accept_size_hint' );
		register_setting( 'tapwarp-option-group' , 'tapwarp_default_bgcolor' );
		register_setting( 'tapwarp-option-group' , 'tapwarp_accept_authKey' );
		wp_enqueue_script( 'tapwarp-options-page', plugin_dir_url(__FILE__).'js/optionspage.js' );

		add_action( 'wp_ajax_tapwarp_getqrcode', 'tapwarp_on_ajax_getqrcode' );
		add_action( 'wp_ajax_tapwarp_checkimage', 'tapwarp_on_ajax_checkimages' );
	}
}

if ($is_tapwarp_upload_page)
{

	add_action( 'pre-upload-ui' , 'tapwarp_on_before_upload_ui' );

	function tapwarp_on_before_upload_ui()
	{

	?>
	<div class="tapwarp-qrcode-container">
		<input type="hidden" id="tapwarp-please-refresh" value="<?php echo __('Please refresh page','TapWarp') ;?>">
		<img class="tapwarp-qrcode-image" src="data:">
		<div class="tapwarp-guide-stripe">
			<label for="tapwarp-guide-trigger"><?php echo __('Get Tap Warp','TapWarp') ;?></label>
			<img class="tapwarp-loading-image" src="data:image/gif;base64,<?php echo base64_encode(file_get_contents(plugin_dir_path(__FILE__).'image/loading.gif')) ?>"/>
		</div>
		<input type="checkbox" id="tapwarp-guide-trigger" style="display: none">
		<div class="tapwarp-guide-panel">
			<div>
				<a href="https://play.google.com/store/apps/details?id=net.qaon.tapwarp&hl=en" class="button" target="_blank">Android</a>
			</div>
			<div>
				<a href="https://itunes.apple.com/us/app/tap-warp/id1137457615?mt=8" class="button" target="_blank">iOS</a>
			</div>
		</div>
		<div class="tapwarp-text-code-container">
			<div>&nbsp;</div><div>
				<textarea readonly="readonly"><?php echo htmlspecialchars(tapwarp_path64_encode(json_encode(tapwarp_get_jsoninfo()))); ?></textarea>
				<div class="tapwarp-code-copied">
					<?php echo __('The code as been copied to your clipboard.') ?><br/>
					<?php echo __('You can paste the code into Tap Warp app.') ?>
				</div>
			</div>
		</div>
	</div>
	<?php
		
	}

	add_action( 'admin_print_footer_scripts' , 'tapwarp_on_footer_scripts' );

	function tapwarp_on_footer_scripts()
	{
	?>
	<script>
	jQuery(document).ready(function($) {
		var data={
			action: "tapwarp_getqrcode"
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('.tapwarp-qrcode-image').attr('src','data:image/png;base64,'+response);
		});

		jQuery('.tapwarp-qrcode-image').on('click',
			function () {
				var container=jQuery('.tapwarp-text-code-container');
				if (container.hasClass('show'))
				{
					container.removeClass('show');
					container.find('.tapwarp-code-copied').removeClass('show');
				}
				else
				{
					var ta=container.find('textarea');
					if (navigator.userAgent.indexOf('iPhone')>=0)
						location='https://twapxy.uniwits.com/'+ta.val();
					else if (navigator.userAgent.indexOf('Android')>=0)
						location='intent://twapxy.uniwits.com/'+ta.val()+'#intent;package=net.qaon.tapwarp';
					else
					{
						container.addClass('show');
						ta[0].focus();
						ta[0].select();
						if (document.execCommand('copy'))
							container.find('.tapwarp-code-copied').addClass('show');
					}
				}
			}
		);
	});
	</script>
	<?php
	}
}

add_action( 'admin_menu', 'tapwarp_on_admin_menu' );

function tapwarp_on_admin_menu()
{
	add_options_page(
		__('Tap Warp Settings','TapWarp'),
		__('Tap Warp','TapWarp'),
		'manage_options',
		'tapwarp-options-admin-menu',
		'tapwarp_output_options_admin_menu'
	);
}

function tapwarp_output_options_admin_menu()
{
?>
<div class="wrap">
<h2>Tap Warp Options</h2>
<form method="post" action="options.php">
<?php echo settings_fields( 'tapwarp-option-group' ) ;?>
<?php echo do_settings_sections( 'tapwarp-option-group' ) ;?>
<input type="hidden" name="tapwarp_accept_size_hint" id="tapwarp_accept_size_hint" value="<?php echo get_option('tapwarp_accept_size_hint') ;?>" />
<table class="form-table">
  <tr>
    <th>
		<?php echo __('Hint For Frame Size','TapWarp') ;?>
	</th>
    <td>
      <input size="6" maxlength="6" type="number" id="tapwarp-sizehint-width" style="max-width: 6em; text-align: right" />
      <select id="tapwarp-sizehint-wunit">
        <option value="">px</option>
        <option value="%">%</option>
      </select>
	  <?php echo __('by','TapWarp') ;?>
      <input size="6" maxlength="6" type="number" id="tapwarp-sizehint-height" style="max-width: 6em; text-align: right" />
      <select id="tapwarp-sizehint-hunit">
        <option value="">px</option>
        <option value="%">%</option>
      </select>
	</td>
  <tr>
    <th>
		<?php echo __('Hint For Rendering','TapWarp') ;?>
	</th>
    <td>
      <select id="tapwarp-sizehint-mode">
        <option value=""><?php echo __('Contained In Frame','TapWarp');?></option>
        <option value="^"><?php echo __('Contains Frame','TapWarp');?></option>
        <option value="!"><?php echo __('Exact Frame Size','TapWarp');?></option>
        <option value="&lt;"><?php echo __('Dimension Minimum','TapWarp');?></option>
        <option value=">"><?php echo __('Dimension Maximum','TapWarp');?></option>
      </select>
      <select id="tapwarp-sizehint-setframe">
        <option value=""><?php echo __('Do Not Draw Frame','TapWarp') ;?></option>
        <option value="*"><?php echo __('Draw Frame','TapWarp') ;?></option>
      </select>
	</td>
  </tr>
  <tr>
    <th>
		<?php echo __('Default BG Color','TapWarp') ;?>
	</th>
    <td>
	  <input 
	  	size="20" 
		maxlength="20" 
		type="color"
		name="tapwarp_default_bgcolor" 
		class="ltr"
		value="<?php echo esc_attr(get_option('tapwarp_default_bgcolor')) ;?>" /><br/>
	</td>
  </tr>
  <tr>
    <th>
		<?php echo __('Authentication Key','TapWarp') ;?>
	</th>
    <td>
	  <input 
	  	size="80" 
		maxlength="128" 
		type="text" 
		name="tapwarp_accept_authKey" 
		class="regular-text ltr"
		value="<?php echo esc_attr(get_option('tapwarp_accept_authKey')) ;?>" /><br/>
	  <input type="button" value="<?php echo __('Regenerate','TapWarp') ;?>" class="button" id="tapwarp-regenerate-authentication-key" />
	</td>
  </tr>
</table>
<?php submit_button(); ?>
</form>
</div>
<script><!--
tapwarp_initialize_sizehint_fields();
--></script>
<?php
}

function tapwarp_die()
{
	if (defined('WP_VERSION') && version_compare(WP_VERSION,'3.4.0','>='))
		wp_die("","",array());
	else
		die;
}

function tapwarp_path64_encode($str)
{
	return strtr(
		base64_encode($str),
		array(
			'='=>'-',
			'/'=>'_',
			'+'=>'.'
		)
	);
}

function tapwarp_get_jsoninfo()
{
	global $tapwarp_use_image_server;

	$sizeHint=get_option('tapwarp_accept_size_hint');
	$bgColor=get_option('tapwarp_default_bgcolor');
	$authKey=get_option('tapwarp_accept_authKey');
	$receiverid=get_option('tapwarp_receiver_id');
	if (!$receiverid)
	{
		$receiverid='';
		for ($i=0;$i<32;$i+=4)
			$receiverid.=sprintf("%04d",mt_rand(0,9999));
		update_option('tapwarp_receiver_id',$receiverid,'no');
	}
	if (!$authKey)
	{
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			$authKey=bin2hex(openssl_random_pseudo_bytes(64));
		}
		else
		{
			$authKey='';
			for ($i=0;$i<128;$i+=4)
				$authKey.=sprintf("%04d",mt_rand(0,9999));
		}
		add_option('tapwarp_accept_authKey',$authKey);
	}

	$info=new stdClass;
	$info->realm=get_bloginfo('name');
	$info->authKey=$authKey;
	$info->targetUrl="http://$tapwarp_use_image_server/putimg.php";
	$info->sizeHint=$sizeHint;
	$info->bgColor=$bgColor;
	$info->clientData=new stdClass;
	$info->clientData->rid=$receiverid;

	return $info;
}

function tapwarp_on_ajax_getqrcode()
{
	$info=tapwarp_get_jsoninfo();
	/*
	   GD library is required to let the plugin work.
	 */
	require( 'phpqrcode/phpqrcode.php' );
	ob_start();
	QRcode::png(json_encode($info));
	echo base64_encode(ob_get_clean());

	tapwarp_die();
}

function tapwarp__sequential_download($server_name,$receiverid,$picnames,$savdir,$quedir)
{
	for ($pi=0;$pi<count($picnames) && $pi<10;$pi++)
	{
		$getimgurl="http://$server_name/getimg.php?rid=$receiverid&pic={$picnames[$pi]}";
		$ext=pathinfo($picnames[$pi],PATHINFO_EXTENSION);
		$localfn=getmypid().sprintf("%.0d",microtime(true)*1000)."$pi.$ext";
		$localpath=$savdir."/".$localfn;
		copy($getimgurl,$localpath);

		$quepath=$quedir.'/'.basename($localpath);
		rename($localpath,$quepath);
	}
}

function tapwarp__parallel_download($server_name,$receiverid,$picnames,$savdir,$quedir)
{
    $handleArray=array();
 
    $multiHandle=curl_multi_init();
 
	for ($pi=0;$pi<count($picnames) && $pi<10;$pi++)
	{
		$getimgurl="http://$server_name/getimg.php?rid=$receiverid&pic={$picnames[$pi]}";

        $singleHandle=curl_init($getimgurl);
 
        curl_setopt($singleHandle,CURLOPT_HEADER,false);
 
        curl_setopt($singleHandle,CURLOPT_RETURNTRANSFER,true);
 
        $handleArray[]=$singleHandle;
        curl_multi_add_handle($multiHandle,$singleHandle);
    }
 
    do {
        $status=curl_multi_exec($multiHandle,$active);
		usleep(1);
    } while ($status == CURLM_CALL_MULTI_PERFORM);

	$image_no=0;
    while ($active && $status == CURLM_OK)
    {
        curl_multi_select($multiHandle);
        do {
            $status=curl_multi_exec($multiHandle,$active);
			usleep(1);
        } while ($status == CURLM_CALL_MULTI_PERFORM);

		if ($status==CURLM_OK)
		{
			$info=curl_multi_info_read($multiHandle);
			if ($info)
			{
				$singleHandle=$info['handle'];
				if ($singleHandle['result']==CURLE_OK)
				{
					$httpstatus=curl_getinfo($singleHandle,CURLINFO_HTTP_CODE);
			 
					if ($httpstatus==200)
					{
						$image_no++;
						$imgdata=curl_multi_getcontent($singleHandle);
						$realurl=curl_getinfo($singleHandle,CURLINFO_EFFECTIVE_URL);
						$ext=pathinfo($realurl,PATHINFO_EXTENSION);

						$localfn=getmypid().sprintf("%.0d",microtime(true)*1000)."$image_no.$ext";
						$localpath=$savdir."/".$localfn;
						file_put_contents($localpath,$imgdata);

						$quepath=$quedir.'/'.basename($localpath);
						rename($localpath,$quepath);
						curl_multi_remove_handle($multiHandle,$singleHandle);
						curl_close($singleHandle);
					}
				}
			}
		}
    }
 
    curl_multi_close($multiHandle);
}

function tapwarp_on_ajax_checkimages()
{
	global $tapwarp_use_image_server;

	header("Content-Type: application/json");

	$response=new stdClass();
	$response->errors=array();
	$response->images=array();

	$receiverid=get_option('tapwarp_receiver_id');
	if (!$receiverid)
	{
		echo json_encode($response);
		tapwarp_die();
	}

	$server_name=$tapwarp_use_image_server;
	$lsimgurl="http://$server_name/lsimg.php?rid=$receiverid";
	$listjson=@file_get_contents($lsimgurl);
	$picnames=json_decode($listjson);

	$udinfo=wp_upload_dir(null,false);
	if (is_wp_error($udinfo))
	{
		$response->errors[]=$udinfo->get_error_messages();
		echo json_encode($response);
		tapwarp_die();
	}
	$turdir=trailingslashit($udinfo['basedir']).'tapwarp-tmp';
	@mkdir($turdir,0775,true);
	@chmod($turdir,0775);
	if (!is_writable($turdir))
	{
		$response->errors[]=__('Please make directory writable: ').$turdir;
		$response->errors[]=__('Possibly by executing: chmod 775 ').$turdir;
		echo json_encode($response);
		tapwarp_die();
	}

	$quedir=$turdir.'/que'; // Temporarily save image when queued for registering into DB.
	$savdir=$turdir.'/sav'; // Temporarily save image when downloading.
	@mkdir($quedir,0775,true);
	@chmod($quedir,0775);
	@mkdir($savdir,0775,true);
	@chmod($savdir,0775);

	$tstfile=$quedir.'/WRITE-TEST.'.time();
	if (!@file_put_contents($tstfile,$tstfile))
		$response->errors[]=__('Temporary directory is not writable: ').dirname($tstfile);
	if (!@unlink($tstfile))
		$response->errors[]=__('Failed deletion test in: ').dirname($tstfile);

	$tstfile=$savdir.'/WRITE-TEST.'.time();
	if (!@file_put_contents($tstfile,$tstfile))
		$response->errors[]=__('Temporary directory is not writable: ').dirname($tstfile);
	if (!@unlink($tstfile))
		$response->errors[]=__('Failed deletion test in: ').dirname($tstfile);

	if ($response->errors)
	{
		$response->errors=array_unique($response->errors);
		echo json_encode($response);
		tapwarp_die();
	}

	try {
		if (function_exists('curl_multi_exec'))
			tapwarp__parallel_download($server_name,$receiverid,$picnames,$savdir,$quedir);
		else
			tapwarp__sequential_download($server_name,$receiverid,$picnames,$savdir,$quedir);
	} catch (Exception $e) {
		$response->error[]=__('Failed to download files: ').print_r($e,true);
		echo json_encode($response);
		tapwarp_die();
	}

	$localpaths=glob($quedir.'/*');
	foreach ($localpaths as $localpath)
	{
		if (!is_file($localpath))
			continue;

		$ext=pathinfo($localpath,PATHINFO_EXTENSION);
		$_FILES=array(
			array(
				'name'=>'Img-'.time().'.'.$ext,
				'type'=>"image/$ext",
				'tmp_name'=>$localpath,
				'error'=>0,
				'size'=>filesize($localpath)
			)
		);

		$post_id=media_handle_upload(0, 0, array(), array('test_form' => false, 'test_upload'=>false, 'action' => 'tapwarp-upload'));
		if (!is_integer($post_id))
		{
			$response->errors[]=__("Failed to handle upload.")."\n".print_r($post_id,true);
			break;
		}

		$ret=new stdClass();
		if ( ($meta=get_post_meta($post_id)) && 
			 ($attachmeta=@unserialize($meta['_wp_attachment_metadata'][0])) )
		{
			$ret->attachment_id=$post_id;
		}
		if ($meta && $attachmeta)
		{
			$udinfo=wp_upload_dir();
			$ret->url=$udinfo['baseurl'].'/'.dirname($attachmeta['file']).'/'.$attachmeta['sizes']['thumbnail']['file'];
			$ret->name=pathinfo($attachmeta['file'],PATHINFO_FILENAME);
			$ret->random_id='';
			$ret->siteroot=get_site_url().'/';
			for ($i=0;$i<32;$i++)
				$ret->random_id.=base_convert(mt_rand(0,31),10,36);
			$response->images[]=$ret;
		}
	}
	echo json_encode($response);

	tapwarp_die();
}

