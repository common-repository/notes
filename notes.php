<?php
/**
 * Plugin Name:       Notes
 * Plugin URI:        https://seosthemes.com/notes
 * Description:       Displays notes on the WordPress dashboard. When the date of the event has occurred, the note is colored red.
 * Version:           1.1.0
 * Tags:              Notes, Note, WordPress Notes, WordPress Note, Admin Notes
 * Author:            seosbg
 * Author URI:        https://seosthemes.com/
 * Text Domain:       notes
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Do not allow direct access to the file.
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * All admin scripts and styles.
 */
	function notes_admin_scripts() {
    // Load the datepicker script (pre-registered in WordPress).
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'note-datepicker', plugin_dir_url(__FILE__) . '/js/datepicker.js');
    wp_enqueue_style( 'note-jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );		
	wp_enqueue_style( 'note-admin-css', plugin_dir_url(__FILE__) . '/css/note.css');
	}
	add_action('admin_enqueue_scripts', 'notes_admin_scripts');

/**
 * User Section
 */
	add_action('admin_menu', 'notes_menu');

	function notes_menu() {
		add_menu_page('Notes', 'Notes', 'administrator', 'notes', 'notes_settings_page', 'dashicons-calendar-alt');
	}

	add_action( 'admin_init', 'notes_plugin_settings' );

	function notes_plugin_settings() {
        for($i=1;$i<=8; $i++) {
		    register_setting( 'notes', 'note_name_'.$i );	
		    register_setting( 'notes', 'note_datepicker_'.$i );
		}
	    register_setting( 'notes', 'note_email' );
	}

	function notes_settings_page() {
	?>
	<div id="notes-plugin">	
		<div class="s-img-logo">
				<a target="_blank" href="https://seosthemes.com/">
					<div>
						 <?php echo ' <img class="ss-logo" src="' . plugins_url( 'images/logo.png' , __FILE__ ) . '" alt="logo" />'; ?>
						 <h2>SEOS THEMES</h2>
					</div>
				</a>
		</div>	
	    <h1><?php _e('Notes', 'notes'); ?></h1>
	    <br />
		<form autocomplete="off" action="options.php" method="post" role="form" name="custom-note">
			<?php settings_fields( 'notes' ); ?>
			<?php do_settings_sections( 'notes' ); ?>
			<h3>Today is: <?php echo date_i18n( 'Y-m-d' ); ?></h3>
			<div class="cont-notes">
				<?php for($i=1;$i<=8; $i++) { ?>
				<table class="notes-table" >
					<tr>
						<td <?php if((get_option('note_datepicker_'.$i) <= date_i18n( 'Y-m-d' ) and get_option('note_datepicker_'.$i) !="") or get_option('note_datepicker_'.$i) == "This date has already passed") { echo "style='background: red;'"; } ?>><b><?php _e('Note '.$i, 'notes'); ?></b>
						<br />
						<textarea class="note" name="note_name_<?php echo $i; ?>"><?php echo esc_html(get_option("note_name_".$i)); ?></textarea></td>
					</tr>
					<tr>
						<td <?php if(get_option('note_datepicker_'.$i) <= date_i18n( 'Y-m-d' ) and get_option('note_datepicker_'.$i) !="" or get_option('note_datepicker_'.$i) == "This date has already passed") { echo "style='background: red;'"; } ?>>
						<br />
						<input autocomplete="off" placeholder="<?php _e('Date', 'notes'); ?>" type="text" class="datepicker" name="note_datepicker_<?php echo $i; ?>" value="<?php echo esc_html(get_option('note_datepicker_'.$i)); ?>" /></td>
					</tr>
				</table>
				<?php } ?>
			</div>	
			<div class="note-submit"><?php submit_button(); ?></div>	
		</form>
	</div>
	<?php
	function notes_custom_cron_func() {
		for($i=1;$i<=8; $i++) {
			if(get_option('note_datepicker_'.$i) == date_i18n( 'Y-m-d' ) and get_option('note_datepicker_'.$i) !="") {
				$old_date = "note_datepicker_".$i;
				update_option( $old_date, "This date has already passed" );
			}
				
		}
	}
	notes_custom_cron_func();
}

/**
 * Add translation.
 */
		function notes_language_load() {
			load_plugin_textdomain('notes_language_load', FALSE, basename(dirname(__FILE__)) . '/languages');
		}
		add_action('init', 'notes_language_load');	
		
/**
 * Add dashboard position.
 */
	function notes_dashboard_setup_function() {
		add_meta_box( 'my_dashboard_widget', 'Note Dashboard Widget', 'notes_dashboard_widget_function', 'dashboard', 'side', 'high' );
	}
	add_action( 'wp_dashboard_setup', 'notes_dashboard_setup_function' );
 
/**
 * Output the contents of the dashboard widget
 */
	function notes_dashboard_widget_function() {
		for($i=1;$i<=8; $i++) {
			if(get_option('note_name_'.$i)) { ?>
				<p class="new-custom-note" <?php if((get_option('note_datepicker_'.$i) <= date_i18n( 'Y-m-d' ) and get_option('note_datepicker_'.$i) !="") or get_option('note_datepicker_'.$i) == "This date has already passed") { echo "style='background: red;'"; } ?>>
				<?php _e('Note: '.$i.' ', 'notes'); ?>
				<?php echo esc_html(get_option('note_name_'.$i)); ?>
				</p>
			<?php
			}
		}
	}