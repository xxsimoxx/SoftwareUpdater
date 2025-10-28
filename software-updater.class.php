<?php

if (class_exists('SoftwareUpdater')) {
	return;
}

class SoftwareUpdater {

	private $url        = '',
			$slug       = '',
			$type       = '',
			$sanitize_callback = null,
			$extra_args = [];

	/**
	 * __construct function
	 *
	 * The only function needed to set up the client side part
	 * of a ClassicPress plugin's updater.
	 *
	 * @param string $url               The url of the update endpoint.
	 *                                  The host must matche the "Update URI" header.
	 * @param string $slug              The folder/main_php_file.php of a plugin
	 *                                  or the folder of a theme.
	 * @param string $type              plugin or theme.
	 * @param string $sanitize_callback The function that is called with the update data response.
	 *                                  Used to validate the response.
	 *                                  Must return false if the response is not valid.
	 * @param array  $extra_args        Extra args passed to $url as an associative array.
	 *                                  Can be used to log sites using the plugin
	 *                                  or verify license code.
	 *
	 * @return SoftwareUpdater
	 */
	public function __construct($url, $slug, $type = 'plugin', $sanitize_callback = null, $extra_args = []) {
		if (!in_array($type, ['plugin', 'theme'])) {
			wp_trigger_error(__CLASS__, "Wrong \$type $type for $slug.");
			return;
		}
		$uri = wp_parse_url($url, PHP_URL_HOST);
		add_filter("update_{$type}s_{$uri}", [$this, 'update_filter'], 10, 5);
		$this->url               = $url;
		$this->slug              = $slug;
		$this->type              = $type;
		$this->sanitize_callback = $sanitize_callback;
		$this->extra_args        = $extra_args;
		$this->sanitize_extra_args();
	}

	public function update_filter($update, $plugin_data, $plugin_file, $locales) {
		if (plugin_basename($plugin_file) !== $this->slug) {
			return $update;
		}
		$url  = $this->url;
		$url .= "?{$this->type}={$this->slug}";
		$url  = add_query_arg($this->extra_args, $url);
		$response = wp_remote_get($url);
		if (!is_array($response) || is_wp_error($response)) {
			return $update;
		}
		$update_from_endpoint = json_decode(wp_remote_retrieve_body($response), true);
		if ($this->sanitize_callback !== null && function_exists($this->sanitize_callback) && call_user_func($this->sanitize_callback, $update_from_endpoint) === false) {
			return $update;
		}
		return $update_from_endpoint;
	}

	private function sanitize_extra_args() {
		$exclude_list = ['plugin', 'theme'];
		foreach ($this->extra_args as $key => $value) {
			if (!in_array($key, $exclude_list)) {
				continue;
			}
			unset($this->extra_args[$key]);
		}
	}

}
