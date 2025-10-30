# Software Updater
This class allow plugins and theme developer to get updates from their own sources.
The class is very lightweight and designed to require very few code on the update server.
Is up to you to setup code that serves updates in the update server.

## Easy setup (single plugin)
1. In your plugin or theme, add:
```php
require_once 'classes/software-updater.class.php';
new SoftwareUpdater( 'https://your-update-server/update.txt', 'your-plugin/your-plugin.php', 'plugin' );
```
2. Add to the headers:
```php
* Update URI: https://your-update-server
```

In `https://your-update-server/update.txt` put some JSON data, like
```php
{"version":"1.0.3","slug":"your-plugin","package":"https:\/\/example.com\/update-test-1.0.3.zip"}
```
You are done. Just remember to update your JSON file when updating the plugin.

## Full setup
1. In your plugin or theme, add:
```php
require_once 'classes/software-updater.class.php';
new SoftwareUpdater(
	'https://your-update-server/update.php',
	'your-plugin/your-plugin.php',
	'plugin',
	null,
	array(
		'key'  => '5429-DJUX-5GRD-CSZO',
		'from' => get_bloginfo('url'),
	),
);
```
2. Add to the headers:
```php
* Update URI: https://your-update-server
```

3. Build your own Update Server, something like this will work. You can add code to log sites requesting updates and validating activation keys.
```php
$plugins = [
	'your-plugin/your-plugin.php' => [
		'version' => '1.0.3',
		'slug'    => 'your-plugin',
		'package' => 'https://example.com/update-test-1.0.3.zip',
	],
];

$themes = [
	'your-theme' => [
		'version' => '2.0.3',
		'slug'    => 'theme',
		'package' => 'https://example.com/update-test-1.0.3.zip',
	],
];

if (array_key_exists('plugin', $_REQUEST)) {
	echo json_encode($plugins[$_REQUEST['plugin']]);
}

if (array_key_exists('theme', $_REQUEST)) {
	echo json_encode($themes[$_REQUEST['theme']]);
}
```

## Class options

| parameter | description |
|--|--|
| url | The url of the update endpoint. The host must matche the `Update URI` header.|
| slug | The `folder/main_php_file.php` of a plugin or the `folder` of a theme. |
| type | `plugin` or `theme`.
| sanitize_callback | The function that is called with the update data response.  Used to validate the response.  Must return `false` if the response is not valid. |
| extra_args |   Extra args passed to $url as an associative array. Can be used to log sites using the plugin or verify license code.

`public function __construct( $url, $slug, $type = 'plugin', $sanitize_callback = null, $extra_args = array() )`

## Supported response parameters

- For plugins, the supported parameter are those for the `update_plugins_{$hostname}` hook, documented [here](https://docs.classicpress.net/reference/hooks/update_plugins_hostname/).
- For themes, the supported parameter are those for the `update_themes_{$hostname}` hook, documented [here](https://docs.classicpress.net/reference/hooks/update_themes_hostname/).
