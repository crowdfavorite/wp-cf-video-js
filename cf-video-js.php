<?php
/*
Plugin Name: CF Video JS
Description: Adds cross-browser video tag support with VideoJS for easy video embedding.
Version: 0.1
Author: Crowd Favorite
Author URI: http://crowdfavorite.com
*/

/**
 * Who am I?
 */
define('CFVJ_VER', '.01');

/**
 * Where am I?
 */
if (!defined('PLUGINDIR')) {
	define('PLUGINDIR','wp-content/plugins/');
}
define('CFVJ_DIR', apply_filters('cfvj_dir', PLUGINDIR.'/cf-video-js/', PLUGINDIR));
define('CFVJ_URL', apply_filters('cfvj_url', plugins_url().'/cf-video-js/', plugins_url()));

function cfvj_init() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('video-js', CFVJ_URL.'video-js/video.js', array(), CFVJ_VER);
	wp_enqueue_style('video-js', CFVJ_URL.'video-js/video-js.css', array(), CFVJ_VER);
	add_action('wp_head', 'cfvj_render_js_setup', 9);
}
add_action('init', 'cfvj_init');

function cfvj_render_js_setup() {
	echo '<script type="text/javascript">
	jQuery(function($){
	  VideoJS.setup();
	});
</script>';
}

/**
 * Turn an array of attributes into an HTML attribute string for use on a tag.
 * @param array
 * @return string
 */
function cfvj_htmlify_attrs($attrs = array()) {
	$html = array();

	if (!empty($attrs)) {
		foreach ($attrs as $key => $value) {
			$value = esc_attr($value);
			if ($value) {
				$html[] = $key . '="' . $value . '"';
			}
		}
	}
	
	return implode(' ', $html);
}

/**
 * Take a space-separated string of urls and turn them into keyed array of valid sources for video tag
 * Invalid sources are dropped.
 * @param string || array
 * @return array
 */
function cfvj_sanitize_sources($str) {
	$sources = explode(' ', $str);
	$output = array();
	
	foreach ($sources as $source) {
		if (stripos($source, '.mp4') !== false) {
			$output['mp4'] = $source;
		} else if (stripos($source, '.webm') !== false) {
			$output['webm'] = $source;
		} else if (stripos($source, '.ogg') !== false) {
			$output['ogg'] = $source;
		}
	}
	return $output;
}

/**
 * Output video source tags for valid array of sources
 * Does not do sanitization relies on input from cfvj_sanitize_sources
 * @param array of sources
 * @return string
 */
function cfvj_get_video_sources($sources) {
	$tags = array();
	
	if (isset($sources['mp4'])) {
		$tags[] = '<source src="'.$sources['mp4'].'" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\' />';
	}
	if (isset($sources['webm'])) {
		$tags[] = '<source src="'.$sources['webm'].'" type=\'video/webm; codecs="vp8, vorbis"\' />';
	}
	if (isset($sources['ogg'])) {
		$tags[] = '<source src="'.$sources['ogg'].'" type=\'video/webm; codecs="vp8, vorbis"\' />';
	}
	$output = implode("\n", $tags);
	return $output;
}

function cfvj_get_video_links($sources) {
	$tags = array();
	$output = '';
	
	if (isset($sources['mp4'])) {
		$tags[] = '<a href="'.$sources['mp4'].'">MP4</a>';
	}
	if (isset($sources['webm'])) {
		$tags[] = '<a href="'.$sources['webm'].'">WebM</a>';
	}
	if (isset($sources['ogg'])) {
		$tags[] = '<a href="'.$sources['ogg'].'">Ogg</a>';
	}
	if (!empty($tags)) {
		$output = '<p class="vjs-no-video"><strong>Download Video:</strong> ' . implode(", ", $tags) . '</p>';
	}
	return $output;
}

function cfvj_video($atts) {
	$attrs = shortcode_atts(array(
		'src' => null,
		'poster' => null,
		'width' => 640,
		'height' => 360
	), $atts);
	
	extract($attrs);
	$output = '';
	
	// If we have a valid video URL
	if ($src) {
		
		// Set up conditional attributes, etc
		$video_attrs = $attrs;
		// Remove src, since that will be a tag inside of <video>
		unset($video_attrs['src']);
		$html_attrs = cfvj_htmlify_attrs($video_attrs);
		
		$sources = cfvj_sanitize_sources($src);
		
		// Get multiple sources
		$html_sources = cfvj_get_video_sources($sources);
		$fallback_links = cfvj_get_video_links($sources);
		
		// Output flash if mp4 present
		if (isset($sources['mp4'])) {
			// Add poster image if available
			$poster_img = $poster ? '<img src="'.$poster.'" width="'.$width.'" height="'.$height.'" alt="Poster Image" title="No video playback capabilities." />' : '';
			
			$flash_vid .= '
				<object class="vjs-flash-fallback" width="'.$width.'" height="'.$height.'" type="application/x-shockwave-flash"
					data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">
					<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />
					<param name="allowfullscreen" value="true" />
					<param name="flashvars" value=\'config={"clip":{"url":"'.$sources['mp4'].'","autoPlay":false,"autoBuffering":true}}\' />
					'.$poster_img.'
				</object>
			';
		}
		
		$output = '
		<div class="video-js-box">
			<video class="video-js" controls preload '.$html_attrs.'>
				'.$html_sources.'
				'.$flash_vid.'
			</video>
			'.$fallback_links.'
		</div>';
	}
	
	return $output;
}
add_shortcode('video', 'cfvj_video');

/**
 * Enqueue the readme function
 */
function cfvj_add_readme() {
	if(function_exists('cfreadme_enqueue')) {
		cfreadme_enqueue('cf-video-js','cfvj_readme');
	}
}
add_action('admin_init','cfvj_add_readme');

/**
 * return the contents of the links readme file
 * replace the image urls with full paths to this plugin install
 *
 * @return string
 */
function cfvj_readme() {
	$file = CFVJ_DIR.'README.txt';
	if(is_file($file) && is_readable($file)) {
		$markdown = file_get_contents($file);
		return $markdown;
	}
	return null;
}
?>