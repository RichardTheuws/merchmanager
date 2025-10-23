<?php
/**
 * Provide a admin area view for the plugin settings
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <?php
    // Show settings saved message
    if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
        echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Settings saved.', 'merchmanager' ) . '</p></div>';
    }
    ?>
    
    <div class="msp-settings-wrapper">
        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active"><?php _e( 'General', 'merchmanager' ); ?></a>
            <a href="#users" class="nav-tab"><?php _e( 'Users', 'merchmanager' ); ?></a>
            <a href="#notifications" class="nav-tab"><?php _e( 'Notifications', 'merchmanager' ); ?></a>
            <a href="#advanced" class="nav-tab"><?php _e( 'Advanced', 'merchmanager' ); ?></a>
        </h2>
        
        <form method="post" action="options.php" class="msp-settings-form">
            <?php
            settings_fields( 'msp_settings' );
            $options = get_option( 'msp_settings', array() );
            ?>
            
            <div id="general" class="msp-settings-tab">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="msp_currency"><?php _e( 'Currency', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <select name="msp_settings[currency]" id="msp_currency">
                                <option value="USD" <?php selected( isset( $options['currency'] ) ? $options['currency'] : 'USD', 'USD' ); ?>><?php _e( 'US Dollar ($)', 'merchmanager' ); ?></option>
                                <option value="EUR" <?php selected( isset( $options['currency'] ) ? $options['currency'] : 'USD', 'EUR' ); ?>><?php _e( 'Euro (€)', 'merchmanager' ); ?></option>
                                <option value="GBP" <?php selected( isset( $options['currency'] ) ? $options['currency'] : 'USD', 'GBP' ); ?>><?php _e( 'British Pound (£)', 'merchmanager' ); ?></option>
                                <option value="CAD" <?php selected( isset( $options['currency'] ) ? $options['currency'] : 'USD', 'CAD' ); ?>><?php _e( 'Canadian Dollar ($)', 'merchmanager' ); ?></option>
                                <option value="AUD" <?php selected( isset( $options['currency'] ) ? $options['currency'] : 'USD', 'AUD' ); ?>><?php _e( 'Australian Dollar ($)', 'merchmanager' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="msp_date_format"><?php _e( 'Date Format', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <select name="msp_settings[date_format]" id="msp_date_format">
                                <option value="m/d/Y" <?php selected( isset( $options['date_format'] ) ? $options['date_format'] : 'm/d/Y', 'm/d/Y' ); ?>><?php echo date( 'm/d/Y' ); ?> (MM/DD/YYYY)</option>
                                <option value="d/m/Y" <?php selected( isset( $options['date_format'] ) ? $options['date_format'] : 'm/d/Y', 'd/m/Y' ); ?>><?php echo date( 'd/m/Y' ); ?> (DD/MM/YYYY)</option>
                                <option value="Y-m-d" <?php selected( isset( $options['date_format'] ) ? $options['date_format'] : 'm/d/Y', 'Y-m-d' ); ?>><?php echo date( 'Y-m-d' ); ?> (YYYY-MM-DD)</option>
                                <option value="F j, Y" <?php selected( isset( $options['date_format'] ) ? $options['date_format'] : 'm/d/Y', 'F j, Y' ); ?>><?php echo date( 'F j, Y' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="msp_time_format"><?php _e( 'Time Format', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <select name="msp_settings[time_format]" id="msp_time_format">
                                <option value="g:i a" <?php selected( isset( $options['time_format'] ) ? $options['time_format'] : 'g:i a', 'g:i a' ); ?>><?php echo date( 'g:i a' ); ?> (12-hour)</option>
                                <option value="H:i" <?php selected( isset( $options['time_format'] ) ? $options['time_format'] : 'g:i a', 'H:i' ); ?>><?php echo date( 'H:i' ); ?> (24-hour)</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="msp_low_stock_threshold"><?php _e( 'Default Low Stock Threshold', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <input type="number" name="msp_settings[low_stock_threshold]" id="msp_low_stock_threshold" value="<?php echo esc_attr( isset( $options['low_stock_threshold'] ) ? $options['low_stock_threshold'] : 5 ); ?>" min="1" />
                            <p class="description"><?php _e( 'Default threshold for low stock alerts.', 'merchmanager' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="msp_sales_page_expiry"><?php _e( 'Default Sales Page Expiry', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <input type="number" name="msp_settings[sales_page_expiry]" id="msp_sales_page_expiry" value="<?php echo esc_attr( isset( $options['sales_page_expiry'] ) ? $options['sales_page_expiry'] : 7 ); ?>" min="1" />
                            <p class="description"><?php _e( 'Default number of days before a sales page expires.', 'merchmanager' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="users" class="msp-settings-tab" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label><?php _e( 'MSP Management Role Permissions', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e( 'MSP Management Role Permissions', 'merchmanager' ); ?></legend>
                                <label>
                                    <input type="checkbox" name="msp_settings[management_manage_bands]" value="1" <?php checked( isset( $options['management_manage_bands'] ) ? $options['management_manage_bands'] : 1, 1 ); ?> />
                                    <?php _e( 'Manage Bands', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[management_manage_tours]" value="1" <?php checked( isset( $options['management_manage_tours'] ) ? $options['management_manage_tours'] : 1, 1 ); ?> />
                                    <?php _e( 'Manage Tours', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[management_manage_merchandise]" value="1" <?php checked( isset( $options['management_manage_merchandise'] ) ? $options['management_manage_merchandise'] : 1, 1 ); ?> />
                                    <?php _e( 'Manage Merchandise', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[management_manage_sales]" value="1" <?php checked( isset( $options['management_manage_sales'] ) ? $options['management_manage_sales'] : 1, 1 ); ?> />
                                    <?php _e( 'Manage Sales', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[management_view_reports]" value="1" <?php checked( isset( $options['management_view_reports'] ) ? $options['management_view_reports'] : 1, 1 ); ?> />
                                    <?php _e( 'View Reports', 'merchmanager' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label><?php _e( 'MSP Tour Management Role Permissions', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e( 'MSP Tour Management Role Permissions', 'merchmanager' ); ?></legend>
                                <label>
                                    <input type="checkbox" name="msp_settings[tour_management_manage_bands]" value="1" <?php checked( isset( $options['tour_management_manage_bands'] ) ? $options['tour_management_manage_bands'] : 0, 1 ); ?> />
                                    <?php _e( 'Manage Bands', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[tour_management_manage_tours]" value="1" <?php checked( isset( $options['tour_management_manage_tours'] ) ? $options['tour_management_manage_tours'] : 1, 1 ); ?> />
                                    <?php _e( 'Manage Tours', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[tour_management_manage_merchandise]" value="1" <?php checked( isset( $options['tour_management_manage_merchandise'] ) ? $options['tour_management_manage_merchandise'] : 1, 1 ); ?> />
                                    <?php _e( 'Manage Merchandise', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[tour_management_manage_sales]" value="1" <?php checked( isset( $options['tour_management_manage_sales'] ) ? $options['tour_management_manage_sales'] : 1, 1 ); ?> />
                                    <?php _e( 'Manage Sales', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[tour_management_view_reports]" value="1" <?php checked( isset( $options['tour_management_view_reports'] ) ? $options['tour_management_view_reports'] : 1, 1 ); ?> />
                                    <?php _e( 'View Reports', 'merchmanager' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label><?php _e( 'MSP Merch Sales Role Permissions', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e( 'MSP Merch Sales Role Permissions', 'merchmanager' ); ?></legend>
                                <label>
                                    <input type="checkbox" name="msp_settings[merch_sales_manage_bands]" value="1" <?php checked( isset( $options['merch_sales_manage_bands'] ) ? $options['merch_sales_manage_bands'] : 0, 1 ); ?> />
                                    <?php _e( 'Manage Bands', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[merch_sales_manage_tours]" value="1" <?php checked( isset( $options['merch_sales_manage_tours'] ) ? $options['merch_sales_manage_tours'] : 0, 1 ); ?> />
                                    <?php _e( 'Manage Tours', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[merch_sales_manage_merchandise]" value="1" <?php checked( isset( $options['merch_sales_manage_merchandise'] ) ? $options['merch_sales_manage_merchandise'] : 0, 1 ); ?> />
                                    <?php _e( 'Manage Merchandise', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[merch_sales_manage_sales]" value="1" <?php checked( isset( $options['merch_sales_manage_sales'] ) ? $options['merch_sales_manage_sales'] : 1, 1 ); ?> />
                                    <?php _e( 'Manage Sales', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[merch_sales_view_reports]" value="1" <?php checked( isset( $options['merch_sales_view_reports'] ) ? $options['merch_sales_view_reports'] : 0, 1 ); ?> />
                                    <?php _e( 'View Reports', 'merchmanager' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="notifications" class="msp-settings-tab" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="msp_enable_email_notifications"><?php _e( 'Enable Email Notifications', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="msp_settings[enable_email_notifications]" id="msp_enable_email_notifications" value="1" <?php checked( isset( $options['enable_email_notifications'] ) ? $options['enable_email_notifications'] : 0, 1 ); ?> />
                            <p class="description"><?php _e( 'Enable email notifications for various events.', 'merchmanager' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="msp_notification_email"><?php _e( 'Notification Email', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <input type="email" name="msp_settings[notification_email]" id="msp_notification_email" value="<?php echo esc_attr( isset( $options['notification_email'] ) ? $options['notification_email'] : get_option( 'admin_email' ) ); ?>" class="regular-text" />
                            <p class="description"><?php _e( 'Email address to receive notifications.', 'merchmanager' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label><?php _e( 'Notification Types', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e( 'Notification Types', 'merchmanager' ); ?></legend>
                                <label>
                                    <input type="checkbox" name="msp_settings[notify_low_stock]" value="1" <?php checked( isset( $options['notify_low_stock'] ) ? $options['notify_low_stock'] : 1, 1 ); ?> />
                                    <?php _e( 'Low Stock Alerts', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[notify_sales_summary]" value="1" <?php checked( isset( $options['notify_sales_summary'] ) ? $options['notify_sales_summary'] : 1, 1 ); ?> />
                                    <?php _e( 'Daily Sales Summary', 'merchmanager' ); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="msp_settings[notify_new_sales_page]" value="1" <?php checked( isset( $options['notify_new_sales_page'] ) ? $options['notify_new_sales_page'] : 1, 1 ); ?> />
                                    <?php _e( 'New Sales Page Generation', 'merchmanager' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="advanced" class="msp-settings-tab" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="msp_data_retention"><?php _e( 'Data Retention Period', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <select name="msp_settings[data_retention]" id="msp_data_retention">
                                <option value="0" <?php selected( isset( $options['data_retention'] ) ? $options['data_retention'] : 0, 0 ); ?>><?php _e( 'Keep all data', 'merchmanager' ); ?></option>
                                <option value="365" <?php selected( isset( $options['data_retention'] ) ? $options['data_retention'] : 0, 365 ); ?>><?php _e( '1 year', 'merchmanager' ); ?></option>
                                <option value="730" <?php selected( isset( $options['data_retention'] ) ? $options['data_retention'] : 0, 730 ); ?>><?php _e( '2 years', 'merchmanager' ); ?></option>
                                <option value="1095" <?php selected( isset( $options['data_retention'] ) ? $options['data_retention'] : 0, 1095 ); ?>><?php _e( '3 years', 'merchmanager' ); ?></option>
                            </select>
                            <p class="description"><?php _e( 'How long to keep sales data before archiving.', 'merchmanager' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="msp_debug_mode"><?php _e( 'Debug Mode', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="msp_settings[debug_mode]" id="msp_debug_mode" value="1" <?php checked( isset( $options['debug_mode'] ) ? $options['debug_mode'] : 0, 1 ); ?> />
                            <p class="description"><?php _e( 'Enable debug mode for troubleshooting.', 'merchmanager' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="msp_csv_delimiter"><?php _e( 'CSV Delimiter', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <select name="msp_settings[csv_delimiter]" id="msp_csv_delimiter">
                                <option value="," <?php selected( isset( $options['csv_delimiter'] ) ? $options['csv_delimiter'] : ',', ',' ); ?>><?php _e( 'Comma (,)', 'merchmanager' ); ?></option>
                                <option value=";" <?php selected( isset( $options['csv_delimiter'] ) ? $options['csv_delimiter'] : ',', ';' ); ?>><?php _e( 'Semicolon (;)', 'merchmanager' ); ?></option>
                                <option value="\t" <?php selected( isset( $options['csv_delimiter'] ) ? $options['csv_delimiter'] : ',', '\t' ); ?>><?php _e( 'Tab', 'merchmanager' ); ?></option>
                            </select>
                            <p class="description"><?php _e( 'Delimiter for CSV import/export.', 'merchmanager' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="msp_remove_data"><?php _e( 'Remove Data on Uninstall', 'merchmanager' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="msp_settings[remove_data]" id="msp_remove_data" value="1" <?php checked( isset( $options['remove_data'] ) ? $options['remove_data'] : 0, 1 ); ?> />
                            <p class="description"><?php _e( 'Remove all plugin data when uninstalling the plugin.', 'merchmanager' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <?php submit_button(); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            
            // Hide all tabs
            $('.msp-settings-tab').hide();
            
            // Remove active class from all tabs
            $('.nav-tab').removeClass('nav-tab-active');
            
            // Show the selected tab
            $($(this).attr('href')).show();
            
            // Add active class to the clicked tab
            $(this).addClass('nav-tab-active');
        });
    });
    </script>
</div>