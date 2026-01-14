=== Houzi Description AI ===
Contributors: BooleanBites
Donate link: https://booleanbites.com/
Tags: real estate, ai, gemini, property description, houzi
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A powerful WordPress plugin that leverages Google Gemini AI to automatically generate professional descriptions for your properties.

== Description ==

**Houzi Description AI** is designed for real estate websites that need high-quality property descriptions quickly. By connecting to Google Gemini AI, the plugin analyzes property details and crafts compelling, professional descriptions without the manual effort.

The plugin extracts key information from your 'property' post type, including:
*   Property Title
*   Taxonomies: Property Type, Status, and Features.
*   Post Meta: Address, Bedrooms, Bathrooms, Garages, and Price.

It then sends this data to the selected Gemini model to generate a professional description, which is automatically saved back to the property's content.

== Installation ==

1. Upload the `houzi-description-ai` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the **Houzi AI** menu in your admin dashboard.
4. Enter your Google Gemini API Key (acquire from Google AI Studio).
5. Select your preferred Gemini model.
6. Click "Generate Property Description" to start the process.

== Features ==

*   **Google Gemini AI Power**: Uses state-of-the-art AI models for natural-sounding descriptions.
*   **Multiple Model Support**: Choose between Gemini Flash, Gemini Flash Lite, or Gemini 3 Flash.
*   **Automated Data Extraction**: Automatically pulls property details to feed the AI prompt.
*   **AJAX Processing**: Efficiently processes properties one by one, avoiding server timeouts.
*   **Real-time Progress Tracker**: Watch the progress with a sleek visual progress bar and live activity log.
*   **Premium Admin UI**: A clean, modern interface integrated directly into the WordPress admin.

== Frequently Asked Questions ==

= Where do I get an API Key? =
You can get a free or paid API key from [Google AI Studio](https://aistudio.google.com/app/apikey).

= Does it overwrite existing descriptions? =
Yes, the plugin updates the `post_content` of the property with the AI-generated description.

= Which post types are supported? =
Currently, it specifically targets the `property` post type commonly used in real estate themes.

== Screenshots ==

1. Admin Dashboard with AI Setup and Progress Bar.

== Changelog ==

= 1.0.0 =
* Initial release.
* Added Gemini AI integration.
* Added AJAX processing for properties.
* Added progress bar and activity log.