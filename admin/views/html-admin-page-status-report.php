<?php
/**
 * Admin View: Page - Status Report.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

$environment      = UsersWP_Status::get_environment_info();
$database         = UsersWP_Status::get_database_info();
$active_plugins   = UsersWP_Status::get_active_plugins();
$theme            = UsersWP_Status::get_theme_info();
$security         = UsersWP_Status::get_security_info();
$pages            = UsersWP_Status::get_pages();

?>
<style type="text/css">
	table.uwp-status-table {
		margin-bottom: 1em;
	}
	table.uwp-status-table th {
		font-weight: 700;
		padding: 9px;
	}
	table.uwp-status-table td,
	table.uwp-status-table th {
		font-size: 1.1em;
		font-weight: 400;
	}
	table.uwp-status-table h2 {
		font-size: 14px;
		margin: 0;
	}
	table.uwp-status-table td:first-child {
		width: 33%;
	}
	table.uwp-status-table td mark,
	table.uwp-status-table th mark {
		background: transparent none;
	}
	table.uwp-status-table td mark.yes,
	table.uwp-status-table th mark.yes {
		color: #7ad03a;
	}
	p.submit {
		margin: .5em 0;
		padding: 2px;
	}
	#debug-report {
		display: none;
		margin: 10px 0;
		padding: 0;
		position: relative;
	}
	#debug-report textarea {
		font-family: monospace;
		width: 100%;
		margin: 0;
		height: 300px;
		padding: 20px;
		border-radius: 0;
		resize: none;
		font-size: 12px;
		line-height: 20px;
		outline: 0;
	}
</style>
<div class="notice">
	<p><?php esc_html_e( 'Please copy and paste this information in your ticket when contacting support:', 'userswp' ); ?> </p>
	<p class="submit"><a href="javascript:void" class="button-primary debug-report"><?php esc_html_e( 'Get system report', 'userswp' ); ?></a></p>
	<div id="debug-report">
		<textarea readonly="readonly"></textarea>
		<p class="submit"><button id="copy-for-support" class="button-primary" href="javascript:void"><?php esc_html_e( 'Copy for Support', 'userswp' ); ?></button></p>
		<p class="copy-error hidden"><?php esc_html_e( 'Copying to clipboard failed. Please press Ctrl/Cmd+C to copy.', 'userswp' ); ?></p>
	</div>
</div>
<table class="uwp-status-table widefat">
	<thead>
	<tr>
		<th colspan="3" data-export-label="WordPress Environment"><h2><?php esc_html_e( 'WordPress Environment', 'userswp' ); ?></h2></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td data-export-label="Home URL"><?php esc_html_e( 'Home URL', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $environment['home_url'] ) ?></td>
	</tr>
	<tr>
		<td data-export-label="Site URL"><?php esc_html_e( 'Site URL', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $environment['site_url'] ) ?></td>
	</tr>
	<tr>
		<td data-export-label="UWP Version"><?php esc_html_e( 'UWP version', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $environment['version'] ) ?></td>
	</tr>
	<tr>
		<td data-export-label="WP Version"><?php esc_html_e( 'WP version', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $environment['wp_version'] ) ?></td>
	</tr>
	<tr>
		<td data-export-label="WP Multisite"><?php esc_html_e( 'WP multisite', 'userswp' ); ?>:</td>
		<td><?php echo ( $environment['wp_multisite'] ) ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
	</tr>
	<tr>
		<td data-export-label="WP Memory Limit"><?php esc_html_e( 'WP memory limit', 'userswp' ); ?>:</td>
		<td><?php
			if ( $environment['wp_memory_limit'] < 67108864 ) {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We recommend setting memory to at least 64MB. See: %2$s', 'userswp' ), esc_html( size_format( $environment['wp_memory_limit'] ) ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . esc_html__( 'Increasing memory allocated to PHP', 'userswp' ) . '</a>' ) . '</mark>';
			} else {
				echo '<mark class="yes">' . esc_html( size_format( $environment['wp_memory_limit'] ) ) . '</mark>';
			}
			?></td>
	</tr>
	<tr>
		<td data-export-label="WP Debug Mode"><?php esc_html_e( 'WP debug mode', 'userswp' ); ?>:</td>
		<td>
			<?php if ( $environment['wp_debug_mode'] ) : ?>
				<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
			<?php else : ?>
				<mark class="no">&ndash;</mark>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="WP Cron"><?php esc_html_e( 'WP cron', 'userswp' ); ?>:</td>
		<td>
			<?php if ( $environment['wp_cron'] ) : ?>
				<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
			<?php else : ?>
				<mark class="no">&ndash;</mark>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="Language"><?php esc_html_e( 'Language', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $environment['language'] ); ?></td>
	</tr>
	<tr>
		<td data-export-label="UWP Mode"><?php esc_html_e( 'UWP Installation Mode', 'userswp' ); ?>:</td>
		<?php $install_type = uwp_get_installation_type();
		switch ($install_type) {
			case "multi_na_all":
				$mode = __('UsersWP is Network Activate for All Site', 'userswp');
				break;
			case "multi_na_site_id":
				$mode = __('UsersWP is Network Activate for Specific Site', 'userswp');
				break;
			case "multi_na_default":
				$mode = __('UsersWP is Activate for Main Site', 'userswp');
				break;
			case "multi_not_na":
			case "single":
			default:
				$mode = __('UsersWP is Activate for Single Site', 'userswp');
		}
		?>
		<td><?php echo esc_html( $mode ); ?></td>
	</tr>
	</tbody>
</table>

<table class="uwp-status-table widefat" cellspacing="0">
	<thead>
	<tr>
		<th colspan="3" data-export-label="Server Environment"><h2><?php esc_html_e( 'Server Environment', 'userswp' ); ?></h2></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td data-export-label="Server Info"><?php esc_html_e( 'Server info', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $environment['server_info'] ); ?></td>
	</tr>
	<tr>
		<td data-export-label="PHP Version"><?php esc_html_e( 'PHP version', 'userswp' ); ?>:</td>
		<td><?php
			if ( version_compare( $environment['php_version'], '5.6', '<' ) ) {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We recommend a minimum PHP version of 5.6.', 'userswp' ), esc_html( $environment['php_version'] ) ) . '</mark>';
			} else {
				echo '<mark class="yes">' . esc_html( $environment['php_version'] ) . '</mark>';
			}
			?></td>
	</tr>
	<?php if ( function_exists( 'ini_get' ) ) : ?>
		<tr>
			<td data-export-label="PHP Post Max Size"><?php esc_html_e( 'PHP post max size', 'userswp' ); ?>:</td>
			<td><?php echo esc_html( size_format( $environment['php_post_max_size'] ) ) ?></td>
		</tr>
		<tr>
			<td data-export-label="PHP Time Limit"><?php esc_html_e( 'PHP time limit', 'userswp' ); ?>:</td>
			<td><?php echo esc_html( $environment['php_max_execution_time'] ) ?></td>
		</tr>
		<tr>
			<td data-export-label="PHP Max Input Vars"><?php esc_html_e( 'PHP max input vars', 'userswp' ); ?>:</td>
			<td><?php echo esc_html( $environment['php_max_input_vars'] ) ?></td>
		</tr>
		<tr>
			<td data-export-label="cURL Version"><?php esc_html_e( 'cURL version', 'userswp' ); ?>:</td>
			<td><?php echo esc_html( $environment['curl_version'] ) ?></td>
		</tr>
		<tr>
			<td data-export-label="SUHOSIN Installed"><?php esc_html_e( 'SUHOSIN installed', 'userswp' ); ?>:</td>
			<td><?php echo $environment['suhosin_installed'] ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
		</tr>
	<?php endif;
	if ( $wpdb->use_mysqli ) {
		$ver = mysqli_get_server_info( $wpdb->dbh );
	} else {
		$ver = mysql_get_server_info();
	}
	if ( ! empty( $wpdb->is_mysql ) && ! stristr( $ver, 'MariaDB' ) ) : ?>
		<tr>
			<td data-export-label="MySQL Version"><?php esc_html_e( 'MySQL version', 'userswp' ); ?>:</td>
			<td>
				<?php
				if ( version_compare( $environment['mysql_version'], '5.6', '<' ) ) {
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We recommend a minimum MySQL version of 5.6. See: %2$s', 'userswp' ), esc_html( $environment['mysql_version'] ), '<a href="https://wordpress.org/about/requirements/" target="_blank">' . esc_html__( 'WordPress requirements', 'userswp' ) . '</a>' ) . '</mark>';
				} else {
					echo '<mark class="yes">' . esc_html( $environment['mysql_version'] ) . '</mark>';
				}
				?>
			</td>
		</tr>
	<?php endif; ?>
	<tr>
		<td data-export-label="Max Upload Size"><?php esc_html_e( 'Max upload size', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( size_format( $environment['max_upload_size'] ) ) ?></td>
	</tr>
	<tr>
		<td data-export-label="Default Timezone is UTC"><?php esc_html_e( 'Default timezone is UTC', 'userswp' ); ?>:</td>
		<td><?php
			if ( 'UTC' !== $environment['default_timezone'] ) {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( 'Default timezone is %s - it should be UTC', 'userswp' ), esc_html( $environment['default_timezone'] ) ) . '</mark>';
			} else {
				echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
			} ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="fsockopen/cURL"><?php esc_html_e( 'fsockopen/cURL', 'userswp' ); ?>:</td>
		<td><?php
			if ( $environment['fsockopen_or_curl_enabled'] ) {
				echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
			} else {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.', 'userswp' ) . '</mark>';
			} ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="SoapClient"><?php esc_html_e( 'SoapClient', 'userswp' ); ?>:</td>
		<td><?php
			if ( $environment['soapclient_enabled'] ) {
				echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
			} else {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( 'Your server does not have the %s class enabled - some gateway plugins which use SOAP may not work as expected.', 'userswp' ), '<a href="https://php.net/manual/en/class.soapclient.php">SoapClient</a>' ) . '</mark>';
			} ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="DOMDocument"><?php esc_html_e( 'DOMDocument', 'userswp' ); ?>:</td>
		<td><?php
			if ( $environment['domdocument_enabled'] ) {
				echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
			} else {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( 'Your server does not have the %s class enabled - HTML/Multipart emails, and also some extensions, will not work without DOMDocument.', 'userswp' ), '<a href="https://php.net/manual/en/class.domdocument.php">DOMDocument</a>' ) . '</mark>';
			} ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="GZip"><?php esc_html_e( 'GZip', 'userswp' ); ?>:</td>
		<td><?php
			if ( $environment['gzip_enabled'] ) {
				echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
			} else {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( 'Your server does not support the %s function - this is required to use the GeoIP database from MaxMind.', 'userswp' ), '<a href="https://php.net/manual/en/zlib.installation.php">gzopen</a>' ) . '</mark>';
			} ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="GD Library"><?php esc_html_e( 'GD Library', 'userswp' ); ?>:</td>
		<td><?php
			if ( $environment['gd_library'] ) {
				echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
			} else {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( 'Your server does not have enabled %s - this is required for image processing.', 'userswp' ), '<a href="https://secure.php.net/manual/en/image.installation.php">GD Library</a>' ) . '</mark>';
			} ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="Multibyte String"><?php esc_html_e( 'Multibyte string', 'userswp' ); ?>:</td>
		<td><?php
			if ( $environment['mbstring_enabled'] ) {
				echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
			} else {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( 'Your server does not support the %s functions - this is required for better character encoding. Some fallbacks will be used instead for it.', 'userswp' ), '<a href="https://php.net/manual/en/mbstring.installation.php">mbstring</a>' ) . '</mark>';
			} ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="Remote Post"><?php esc_html_e( 'Remote POST', 'userswp' ); ?>:</td>
		<td><?php
			if ( $environment['remote_post_successful'] ) {
				echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
			} else {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%s failed. Contact your hosting provider.', 'userswp' ), 'wp_remote_post()' ) . ' ' . esc_html( $environment['remote_post_response'] ) . '</mark>';
			} ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="Remote Get"><?php esc_html_e( 'Remote GET', 'userswp' ); ?>:</td>
		<td><?php
			if ( $environment['remote_get_successful'] ) {
				echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
			} else {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%s failed. Contact your hosting provider.', 'userswp' ), 'wp_remote_get()' ) . ' ' . esc_html( $environment['remote_get_response'] ) . '</mark>';
			} ?>
		</td>
	</tr>
	<?php
	$rows = apply_filters( 'uwp_system_status_environment_rows', array() );
	if(count($rows) > 0) {
		foreach ($rows as $row) {
			if (!empty($row['success'])) {
				$css_class = 'yes';
				$icon = '<span class="dashicons dashicons-yes"></span>';
			} else {
				$css_class = 'error';
				$icon = '<span class="dashicons dashicons-no-alt"></span>';
			}
			?>
			<tr>
			<td data-export-label="<?php echo esc_attr($row['name']); ?>"><?php echo esc_html($row['name']); ?>
				:
			</td>
			<td>
				<mark class="<?php echo esc_attr($css_class); ?>">
					<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo !empty($row['note']) ? wp_kses_data($row['note']) : ''; ?>
				</mark>
			</td>
			</tr><?php
		}
	}?>
	</tbody>
</table>
<table class="uwp-status-table widefat" cellspacing="0">
	<thead>
	<tr>
		<th colspan="3" data-export-label="User Platform"><h2><?php esc_html_e( 'User Platform', 'userswp' ); ?></h2></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td data-export-label="Platform"><?php esc_html_e( 'Platform', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $environment['platform'] ); ?></td>
	</tr>
	<tr>
		<td data-export-label="Browser name"><?php esc_html_e( 'Browser name', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $environment['browser_name'] ); ?></td>
	</tr>
	<tr>
		<td data-export-label="Browser version"><?php esc_html_e( 'Browser version', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $environment['browser_version'] ); ?></td>
	</tr>
	<tr>
		<td data-export-label="User agent"><?php esc_html_e( 'User agent', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $environment['user_agent'] ); ?></td>
	</tr>
	</tbody>
</table>
<table class="uwp-status-table widefat" cellspacing="0">
	<thead>
	<tr>
		<th colspan="3" data-export-label="Database"><h2><?php esc_html_e( 'Database', 'userswp' ); ?></h2></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td data-export-label="UWP Database Version"><?php esc_html_e( 'UWP database version', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $database['uwp_db_version'] ); ?></td>
	</tr>
	<tr>
		<td data-export-label="UWP Database Prefix"><?php esc_html_e( 'Database Prefix', 'userswp' ); ?></td>
		<td><?php
			if ( strlen( $database['database_prefix'] ) > 20 ) {
				echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We recommend using a prefix with less than 20 characters.', 'userswp' ), esc_html( $database['database_prefix'] ) ) . '</mark>';
			} else {
				echo '<mark class="yes">' . esc_html( $database['database_prefix'] ) . '</mark>';
			}
			?>
		</td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'Total Database Size', 'userswp' ); ?></td>
		<td><?php printf( '%.2fMB', esc_html( $database['database_size']['data'] + $database['database_size']['index'] ) ); ?></td>
	</tr>

	<tr>
		<td><?php esc_html_e( 'Database Data Size', 'userswp' ); ?></td>
		<td><?php printf( '%.2fMB', esc_html( $database['database_size']['data'] ) ); ?></td>
	</tr>

	<tr>
		<td><?php esc_html_e( 'Database Index Size', 'userswp' ); ?></td>
		<td><?php printf( '%.2fMB', esc_html( $database['database_size']['index'] ) ); ?></td>
	</tr>

	<?php foreach ( $database['database_tables']['userswp'] as $table => $table_data ) { ?>
		<tr>
			<td><?php echo esc_html( $table ); ?></td>
			<td>
				<?php if( ! $table_data ) {
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Table does not exist', 'userswp' ) . '</mark>';
				} else {
					printf( esc_html__( 'Data: %.2fMB + Index: %.2fMB', 'userswp' ), esc_html( uwp_format_decimal( $table_data['data'], 2 ) ), esc_html( uwp_format_decimal( $table_data['index'], 2 ) ) );
				} ?>
			</td>
		</tr>
	<?php } ?>

	<?php foreach ( $database['database_tables']['other'] as $table => $table_data ) { ?>
		<tr>
			<td><?php echo esc_html( $table ); ?></td>
			<td>
				<?php printf( esc_html__( 'Data: %.2fMB + Index: %.2fMB', 'userswp' ), esc_html( uwp_format_decimal( $table_data['data'], 2 ) ), esc_html( uwp_format_decimal( $table_data['index'], 2 ) ) ); ?>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<table class="uwp-status-table widefat" cellspacing="0">
	<thead>
	<tr>
		<th colspan="3" data-export-label="Security"><h2><?php esc_html_e( 'Security', 'userswp' ); ?></h2></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td data-export-label="Secure connection (HTTPS)"><?php esc_html_e( 'Secure connection (HTTPS)', 'userswp' ); ?>:</td>
		<td>
			<?php if ( $security['secure_connection'] ) : ?>
				<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
			<?php else : ?>
				<mark class="error"><span class="dashicons dashicons-warning"></span><?php echo esc_html__( 'Your site is not using HTTPS.', 'userswp' ); ?></mark>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td data-export-label="Hide errors from visitors"><?php esc_html_e( 'Hide errors from visitors', 'userswp' ); ?></td>
		<td>
			<?php if ( $security['hide_errors'] ) : ?>
				<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
			<?php else : ?>
				<mark class="error"><span class="dashicons dashicons-warning"></span><?php esc_html_e( 'Error messages should not be shown to visitors.', 'userswp' ); ?></mark>
			<?php endif; ?>
		</td>
	</tr>
	</tbody>
</table>
<table class="uwp-status-table widefat" cellspacing="0">
	<thead>
	<tr>
		<th colspan="3" data-export-label="Active Plugins (<?php echo count( $active_plugins ) ?>)"><h2><?php esc_html_e( 'Active Plugins', 'userswp' ); ?> (<?php echo count( $active_plugins ) ?>)</h2></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ( $active_plugins as $plugin ) {
		if ( ! empty( $plugin['name'] ) ) {
			$dirname = dirname( $plugin['plugin'] );

			$version_string = '';
			$network_string = '';
			if ( ! empty( $plugin['latest_verison'] ) && version_compare( $plugin['latest_verison'], $plugin['version'], '>' ) ) {
				/* translators: %s: plugin latest version */
				$version_string = ' &ndash; <strong style="color:red;">' . sprintf( esc_html__( '%s is available', 'userswp' ), $plugin['latest_verison'] ) . '</strong>';
			}

			if ( false != $plugin['network_activated'] ) {
				$network_string = ' &ndash; <strong style="color:black;">' . esc_html__( 'Network enabled', 'userswp' ) . '</strong>';
			}
			?>
			<tr>
				<td>
				<?php if ( ! empty( $plugin['url'] ) ) { ?>
				<a href="<?php echo esc_url( $plugin['url'] ); ?>" aria-label="<?php esc_attr_e( 'Visit plugin homepage' , 'userswp' ); ?>" target="_blank"><?php echo esc_html( $plugin['name'] ); ?></a>
				<?php } else { echo esc_html( $plugin['name'] ); } ?>
				</td>
				<td><?php
					/* translators: %s: plugin author */
					printf( esc_html__( 'by %s', 'userswp' ), esc_html( $plugin['author_name'] ) );
					echo ' &ndash; ' . esc_html( $plugin['version'] ) . $version_string . $network_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?></td>
			</tr>
			<?php
		}
	}
	?>
	</tbody>
</table>

<table class="uwp-status-table widefat" cellspacing="0">
	<thead>
	<tr>
		<th colspan="3" data-export-label="UWP Pages"><h2><?php esc_html_e( 'UWP Pages', 'userswp' ); ?></h2></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ( $pages as $page ) {
		$error   = false;

		echo '<tr><td data-export-label="' . esc_attr( $page['page_name'] ) . '">';
		if ( $page['page_id'] ) {
			echo '<a href="' . esc_url( get_edit_post_link( $page['page_id'] ) ) . '" aria-label="' . esc_attr( wp_sprintf( __( 'Edit %s page', 'userswp' ), $page['page_name'] ) ) . '">' . esc_html( $page['page_name'] ) . '</a>';
		} else {
			echo esc_html( $page['page_name'] );
		}
		echo ':</td><td>';

		$has_shortcode = false;
		// Page ID check.
		if ( ! $page['page_set'] ) {
			echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Page not set', 'userswp' ) . '</mark>';
			$error = true;
		} else if ( ! $page['page_exists'] ) {
			echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Page ID is set, but the page does not exist', 'userswp' ) . '</mark>';
			$error = true;
		} else if ( ! $page['page_visible'] ) {
			echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . wp_kses_post( wp_sprintf( __( 'Page visibility should be <a href="%s" target="_blank">public</a>', 'userswp' ), 'https://codex.wordpress.org/Content_Visibility' ) ) . '</mark>';
			$error = true;
		} else {
			// Shortcode check
			if ( $page['shortcode_present'] ) {
				$has_shortcode = true;
			}
		}

		if ( ! $error ) {
			if ( $has_shortcode ) {
				echo '<span class="dashicons dashicons-shortcode" title="' . esc_attr( wp_sprintf( __( 'Page contains shortcode %s', 'userswp' ), $page['shortcode'] ) ) . '"><font style="display:none">[/]</font></span> ';
			}
			echo '<mark class="yes">#' . absint( $page['page_id'] ) . ' - ' . esc_html( str_replace( home_url(), '', get_permalink( $page['page_id'] ) ) ) . '</mark>';
		}
		echo '</td></tr>';
	}
	?>
	</tbody>
</table>
<table class="uwp-status-table widefat" cellspacing="0">
	<thead>
	<tr>
		<th colspan="3" data-export-label="Theme"><h2><?php esc_html_e( 'Theme', 'userswp' ); ?></h2></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td data-export-label="Name"><?php esc_html_e( 'Name', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $theme['name'] ) ?></td>
	</tr>
	<tr>
		<td data-export-label="Version"><?php esc_html_e( 'Version', 'userswp' ); ?>:</td>
		<td><?php
			echo esc_html( $theme['version'] );
			if ( version_compare( $theme['version'], $theme['latest_verison'], '<' ) ) {
				/* translators: %s: theme latest version */
				echo ' &ndash; <strong style="color:red;">' . sprintf( esc_html__( '%s is available', 'userswp' ), esc_html( $theme['latest_verison'] ) ) . '</strong>';
			}
			?></td>
	</tr>
	<tr>
		<td data-export-label="Author URL"><?php esc_html_e( 'Author URL', 'userswp' ); ?>:</td>
		<td><?php echo esc_html( $theme['author_url'] ) ?></td>
	</tr>
	<tr>
		<td data-export-label="Child Theme"><?php esc_html_e( 'Child theme', 'userswp' ); ?>:</td>
		<td><?php
			echo $theme['is_child_theme'] ? '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>' : '<span class="dashicons dashicons-no-alt"></span> &ndash; ' . sprintf( __( 'If you are modifying userswp on a parent theme that you did not build personally we recommend using a child theme. See: <a href="%s" target="_blank">How to create a child theme</a>', 'userswp' ), 'https://codex.wordpress.org/Child_Themes' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?></td>
	</tr>
	<?php
	if ( $theme['is_child_theme'] ) :
		?>
		<tr>
			<td data-export-label="Parent Theme Name"><?php esc_html_e( 'Parent theme name', 'userswp' ); ?>:</td>
			<td><?php echo esc_html( $theme['parent_name'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Parent Theme Version"><?php esc_html_e( 'Parent theme version', 'userswp' ); ?>:</td>
			<td><?php
				echo esc_html( $theme['parent_version'] );
				if ( version_compare( $theme['parent_version'], $theme['parent_latest_verison'], '<' ) ) {
					/* translators: %s: parant theme latest version */
					echo ' &ndash; <strong style="color:red;">' . sprintf( esc_html__( '%s is available', 'userswp' ), esc_html( $theme['parent_latest_verison'] ) ) . '</strong>';
				}
				?></td>
		</tr>
		<tr>
			<td data-export-label="Parent Theme Author URL"><?php esc_html_e( 'Parent theme author URL', 'userswp' ); ?>:</td>
			<td><?php echo esc_html( $theme['parent_author_url'] ) ?></td>
		</tr>
	<?php endif ?>
	</tbody>
</table>
<table class="uwp-status-table widefat" cellspacing="0">
	<thead>
	<tr>
		<th colspan="3" data-export-label="Templates"><h2><?php esc_html_e( 'Templates', 'userswp' ); ?></h2></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if ( ! empty( $theme['overrides'] ) ) { ?>
		<tr>
			<td data-export-label="Overrides"><?php esc_html_e( 'Overrides', 'userswp' ); ?></td>
			<td>
				<?php
				$total_overrides = count( $theme['overrides'] );
				for ( $i = 0; $i < $total_overrides; $i++ ) {
					$override = $theme['overrides'][ $i ];
					if ( $override['core_version'] && ( empty( $override['version'] ) || version_compare( $override['version'], $override['core_version'], '<' ) ) ) {
						$current_version = $override['version'] ? $override['version'] : '-';
						printf(
							esc_html__( '%1$s version %2$s is out of date. The core version is %3$s', 'userswp' ),
							'<code>' . esc_html( $override['file'] ) . '</code>',
							'<strong style="color:red">' . esc_html( $current_version ) . '</strong>',
							esc_html( $override['core_version'] )
						);
					} else {
						echo esc_html( $override['file'] );
					}
					if ( ( count( $theme['overrides'] ) - 1 ) !== $i ) {
						echo ', ';
					}
					echo '<br />';
				}
				?>
			</td>
		</tr>
		<?php
	} else {
		?>
		<tr>
			<td data-export-label="Overrides"><?php esc_html_e( 'Overrides', 'userswp' ); ?>:</td>
			<td>&ndash;</td>
		</tr>
		<?php
	}

	if ( true === $theme['has_outdated_templates'] ) {
		?>
		<tr>
			<td data-export-label="Outdated Templates"><?php esc_html_e( 'Outdated templates', 'userswp' ); ?>:</td>
			<td><mark class="error"><span class="dashicons dashicons-warning"></span></mark></td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>