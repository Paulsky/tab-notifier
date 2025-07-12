# Tab Return Notifier

Tab Return Notifier is a lightweight plugin that helps bring users back to your site when they switch to another browser tab. When a visitor navigates away, the plugin dynamically updates the tab title with attention-grabbing messages and animations.

<br/>
<img src="" width="300" alt="Tab Return Notifier demo" style="max-width: 300px !important; height: auto !important;" />
<br/>
<br/>

For more WordPress plugins, check out our products at [Wijnberg Developments](https://wijnberg.dev).

## Built with

- [WordPress](https://github.com/WordPress/WordPress)
- [emoji-picker-element](https://github.com/nolanlawson/emoji-picker-element)

## Requirements

- WordPress 5.0 or higher

### Optional

- WooCommerce plugin installed and activated

## Installation

To install the plugin, follow these steps:

1. Download the `.zip` file from the [releases page](https://github.com/Paulsky/tab-notifier/releases/).
2. In your WordPress admin dashboard, go to `Plugins` > `Add New`.
3. Click `Upload Plugin` at the top of the page.
4. Click `Choose File`, select the `.zip` file you downloaded, then click `Install Now`.
5. After installation, click `Activate Plugin`.

The plugin is now ready for use.

## Getting started

These instructions will guide you through the installation and basic setup of the Tab Return Notifer plugin.

### Configuration

Configure the plugin settings below for proper functionality.

#### Plugin settings

Configure these plugin-specific settings:

1. **Main settings**
    - Go to: *Settings > Tab Return Notifier*
    - Set your preferred animation settings
    - Set your preferred messages
    - Optional: override messages per post type and/or taxonomy
    
#### Optional: WooCommerce settings

If you would like to use the `{{recently_viewed}}` variable, you need the "Recently Viewed Products" widget to be active on the frontend. This has to do with the internal WooCommerce cookies. [Please see this page](https://woocommerce.com/document/woocommerce-widgets/)*.

### Usage

After configuration, and you have enabled the notifier in the settings, the plugin should work out-of-the-box (in the frontend).


#### WordPress filters

`wdtano_is_enabled_for_current_view`
*Override the enabled status for current view*

```php
apply_filters( 'wdtano_is_enabled_for_current_view', $enabled, $options )
```

**Parameters:**
- `$enabled` *(bool)* - Current enabled status
- `$options` *(array)* - Full plugin options

**Usage Example:**
```php
add_filter('wdtano_is_enabled_for_current_view', function($enabled, $options) {
    if (is_page('special-page')) {
        return true;
    }
    return $enabled;
}, 10, 2);
```

`wdtano_get_messages_for_current_view`
*Filters messages before translation*

```php
apply_filters( 'wdtano_get_messages_for_current_view', $messages, $options, $group )
```

**Parameters:**
- `$messages` *(array)* - Original message array
- `$options` *(array)* - Full plugin options
- `$group` *(string)* - Current message group identifier

---

`wdtano_get_translated_messages_for_current_view`
*Filters messages after translation*

```php
apply_filters( 'wdtano_get_translated_messages_for_current_view', $translated_messages, $options, $group, $messages )
```

**Parameters:**
- `$translated_messages` *(array)* - Translated message array
- `$options` *(array)* - Full plugin options
- `$group` *(string)* - Current message group identifier
- `$messages` *(array)* - Untranslated source messages

- `wdtano_is_enabled_for_current_view`: This allows you to enable or disable the plugin for specific pages or posts.


### WPML

To translate the option texts via WPML:

1. Save your options first in: Settings -> Tab Return Notifier
2. Then translate the texts in: WPML -> String Translations and search for your option values in the domain 'tax-switch-for-woocommerce'


## Compatibility

This plugin is tested and compatible with the following:

### Themes

- GeneratePress

If you encounter any conflicts with other themes or plugins, please report them by opening an issue or through our website.

## Known Issues

### Incompatibility

None at the moment

## Language support

Currently supported languages:
- English
- Dutch

If you would like to add support for a new language or improve existing translations, please let us know by opening an issue or contacting us through our website.

## Contributing

Your contributions are welcome! If you'd like to contribute to the project, feel free to fork the repository, make your changes, and submit a pull request.

## Development and deployment

To prepare your development work for submission, ensure you have `npm` installed and run `npm run build`. This command compiles the React components and prepares the plugin for deployment.

### Steps:

1. Ensure `npm` is installed.
2. Navigate to the project root.
3. Run `npm run build`.

The compiled files are now ready for use. Please ensure your changes adhere to the project's coding standards.

## Security

If you discover any security related issues, please email us instead of using the issue tracker.

## License

This plugin is licensed under the GNU General Public License v2 or later.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
