<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://https://booleanbites.com
 * @since      1.0.0
 *
 * @package    Houzi_Description_Ai
 * @subpackage Houzi_Description_Ai/admin/partials
 */
?>

<div class="wrap houzi-ai-wrap">
    <header class="houzi-ai-header">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div class="houzi-ai-meta">
            <p>Enhance your real estate listings powered by Gemini to transform property data into compelling,
                high-conversion descriptions.</p>
            <p><strong><?php _e('Author:', 'houzi-description-ai'); ?></strong> BooleanBites Ltd</p>
            <p><strong><?php _e('Website:', 'houzi-description-ai'); ?></strong> <a href="https://booleanbites.com"
                    target="_blank">https://booleanbites.com</a></p>
        </div>
    </header>

    <form method="post" action="options.php">
        <?php
        settings_fields('houzi-description-ai');
        do_settings_sections('houzi-description-ai');
        ?>

        <div class="houzi-ai-section">
            <h2><?php _e('AI Setup', 'houzi-description-ai'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label
                            for="houzi_ai_api_key"><?php _e('API Key', 'houzi-description-ai'); ?></label></th>
                    <td>
                        <input type="text" id="houzi_ai_api_key" name="houzi_ai_api_key"
                            value="<?php echo esc_attr(get_option('houzi_ai_api_key')); ?>" class="regular-text">
                        <p class="description">
                            <?php _e('Acquire the API key from ', 'houzi-description-ai'); ?>
                            <a href="https://aistudio.google.com/app/apikey"
                                target="_blank"><?php _e('Google AI Studio', 'houzi-description-ai'); ?></a>
                            <?php _e(' and paste in the above field.', 'houzi-description-ai'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                            for="houzi_ai_gemini_model"><?php _e('Gemini Model', 'houzi-description-ai'); ?></label>
                    </th>
                    <td>
                        <?php $selected_model = get_option('houzi_ai_gemini_model', 'gemini-flash-latest'); ?>
                        <select id="houzi_ai_gemini_model" name="houzi_ai_gemini_model">
                            <option value="gemini-flash-latest" <?php selected($selected_model, 'gemini-flash-latest'); ?>>Gemini Flash</option>
                            <option value="gemini-flash-lite-latest" <?php selected($selected_model, 'gemini-flash-lite-latest'); ?>>Gemini Flash Lite</option>
                            <option value="gemini-3-flash-preview" <?php selected($selected_model, 'gemini-3-flash-preview'); ?>>Gemini 3 Flash</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </div>
    </form>

    <hr>

    <div class="houzi-ai-section">
        <h2><?php _e('Generate AI Description', 'houzi-description-ai'); ?></h2>

        <!-- Properties Section -->
        <div class="houzi-ai-generation-box"
            style="margin-bottom: 30px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px;">
            <h3><?php _e('Properties', 'houzi-description-ai'); ?></h3>
            <div style="margin-bottom: 15px;">
                <p><strong><?php _e('Select properties to process:', 'houzi-description-ai'); ?></strong></p>
                <label style="margin-right: 20px;">
                    <input type="radio" name="houzi_ai_property_scope" value="all" checked>
                    <?php _e('All Properties', 'houzi-description-ai'); ?>
                </label>
                <label>
                    <input type="radio" name="houzi_ai_property_scope" value="pending">
                    <?php _e('Properties without AI Description', 'houzi-description-ai'); ?>
                </label>
            </div>
            <button type="button" id="houzi-generate-property-btn" class="button button-primary houzi-bulk-generate-btn"
                data-type="property">
                <?php _e('Generate Property Descriptions', 'houzi-description-ai'); ?>
            </button>
        </div>

        <!-- Agents Section -->
        <div class="houzi-ai-generation-box"
            style="margin-bottom: 30px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px;">
            <h3><?php _e('Agents', 'houzi-description-ai'); ?></h3>
            <div style="margin-bottom: 15px;">
                <p><strong><?php _e('Select agents to process:', 'houzi-description-ai'); ?></strong></p>
                <label style="margin-right: 20px;">
                    <input type="radio" name="houzi_ai_houzez_agent_scope" value="all" checked>
                    <?php _e('All Agents', 'houzi-description-ai'); ?>
                </label>
                <label>
                    <input type="radio" name="houzi_ai_houzez_agent_scope" value="pending">
                    <?php _e('Agents without AI Description', 'houzi-description-ai'); ?>
                </label>
            </div>
            <button type="button" id="houzi-generate-agent-btn" class="button button-primary houzi-bulk-generate-btn"
                data-type="houzez_agent">
                <?php _e('Generate Agent Descriptions', 'houzi-description-ai'); ?>
            </button>
        </div>

        <!-- Agencies Section -->
        <div class="houzi-ai-generation-box"
            style="margin-bottom: 30px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px;">
            <h3><?php _e('Agencies', 'houzi-description-ai'); ?></h3>
            <div style="margin-bottom: 15px;">
                <p><strong><?php _e('Select agencies to process:', 'houzi-description-ai'); ?></strong></p>
                <label style="margin-right: 20px;">
                    <input type="radio" name="houzi_ai_houzez_agency_scope" value="all" checked>
                    <?php _e('All Agencies', 'houzi-description-ai'); ?>
                </label>
                <label>
                    <input type="radio" name="houzi_ai_houzez_agency_scope" value="pending">
                    <?php _e('Agencies without AI Description', 'houzi-description-ai'); ?>
                </label>
            </div>
            <button type="button" id="houzi-generate-agency-btn" class="button button-primary houzi-bulk-generate-btn"
                data-type="houzez_agency">
                <?php _e('Generate Agency Descriptions', 'houzi-description-ai'); ?>
            </button>
        </div>

        <!-- Shared Progress Component -->
        <div id="houzi-ai-progress-container"
            style="display: none; margin-top: 20px; padding: 20px; background: #fff; border: 2px solid #2271b1; border-radius: 4px;">
            <div class="houzi-ai-progress-stats">
                <strong><span id="houzi-ai-current-type-label">Properties</span>:</strong>
                <span id="houzi-ai-progress-text">0/0</span> processed.
            </div>
            <div class="houzi-ai-progress-bar-bg"
                style="background: #ddd; height: 12px; width: 100%; border-radius: 6px; overflow: hidden; margin: 10px 0;">
                <div id="houzi-ai-progress-bar-fill"
                    style="background: #2271b1; height: 100%; width: 0%; transition: width 0.3s;"></div>
            </div>
            <div id="houzi-ai-log"
                style="margin-top: 10px; max-height: 250px; overflow-y: auto; background: #f9f9f9; padding: 15px; border: 1px solid #ddd; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 11px; line-height: 1.5;">
            </div>
        </div>
    </div>
</div>