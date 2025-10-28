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

## Class options

| parameter | description |
|--|--|
| url | The url of the update endpoint. The host must matche the `Update URI` header.|
| slug | The `folder/main_php_file.php` of a plugin or the `folder` of a theme. |
| type | `plugin` or `theme`.
| sanitize_callback | The function that is called with the update data response.  Used to validate the response.  Must return `false` if the response is not valid. |
| extra_args |   Extra args passed to $url as an associative array. Can be used to log sites using the plugin or verify license code.

## Full setup
*coming soon...*
