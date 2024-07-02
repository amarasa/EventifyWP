<?php

// Add a settings menu item under the Events menu
add_action('admin_menu', 'events_plugin_settings_menu');

function events_plugin_settings_menu()
{
    add_submenu_page(
        'edit.php?post_type=event', // Parent slug
        'Events Plugin Settings',   // Page title
        'Config',                   // Menu title
        'manage_options',           // Capability
        'events-plugin-settings',   // Menu slug
        'events_plugin_settings_page' // Callback function
    );

    add_submenu_page(
        'edit.php?post_type=event', // Parent slug
        'Events Shortcodes',        // Page title
        'Shortcodes',               // Menu title
        'manage_options',           // Capability
        'events-plugin-shortcodes', // Menu slug
        'events_plugin_shortcodes_page' // Callback function
    );
}

// Render the settings page
function events_plugin_settings_page()
{
?>
    <div class="wrap">
        <h1><?php _e('Events Plugin Settings', 'basic-events'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('events_plugin_settings_group');
            do_settings_sections('events-plugin-settings');
            submit_button();
            ?>
        </form>
    </div>
<?php
}

// Register settings
add_action('admin_init', 'events_plugin_register_settings');

function events_plugin_register_settings()
{
    // Register API key setting
    register_setting('events_plugin_settings_group', 'events_plugin_google_api_key');

    // Register editor preference setting
    register_setting('events_plugin_settings_group', 'events_plugin_editor_preference');

    // Add API Settings section
    add_settings_section(
        'events_plugin_settings_section',
        __('EventifyWP Settings', 'basic-events'),
        null,
        'events-plugin-settings'
    );

    // Add API Key field
    add_settings_field(
        'events_plugin_google_api_key',
        __('EventifyWP Settings', 'basic-events'),
        'events_plugin_google_api_key_field',
        'events-plugin-settings',
        'events_plugin_settings_section'
    );
}

function events_plugin_google_api_key_field()
{
    $api_key = get_option('events_plugin_google_api_key');
    $editor_preference = get_option('events_plugin_editor_preference', 'classic'); // Default to 'classic' if option doesn't exist

?>
    <tr valign="top">
        <th scope="row"><?php _e('Google API Key', 'basic-events'); ?></th>
        <td>
            <input type="text" id="events_plugin_google_api_key" name="events_plugin_google_api_key" value="<?php echo esc_attr($api_key); ?>" size="50" />
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Editor Preference', 'basic-events'); ?></th>
        <td>
            <select id="events_plugin_editor_preference" name="events_plugin_editor_preference">
                <option value="classic" <?php selected('classic', $editor_preference); ?>><?php _e('Classic Editor', 'basic-events'); ?></option>
                <option value="gutenberg" <?php selected('gutenberg', $editor_preference); ?>><?php _e('Gutenberg Editor', 'basic-events'); ?></option>
            </select>
        </td>
    </tr>
<?php
}


// Render the shortcodes page
function events_plugin_shortcodes_page()
{
    $event_categories = get_terms(array(
        'taxonomy' => 'event_category',
        'hide_empty' => false,
    ));
?>
    <div class="wrap">
        <h1><?php _e('Events Shortcodes', 'basic-events'); ?></h1>
        <h2><?php _e('Available Shortcodes', 'basic-events'); ?></h2>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e('Shortcode', 'basic-events'); ?></th>
                    <th><?php _e('Configuration', 'basic-events'); ?></th>
                    <th><?php _e('Description', 'basic-events'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="shortcode-wrapper">
                            <input type="text" id="upcoming_events_by_category_shortcode" value="[upcoming_events_by_category category='']" readonly onclick="this.select();" />
                            <span class="copy-button" data-clipboard-target="#upcoming_events_by_category_shortcode" title="Copy to clipboard">
                                <span class="dashicons dashicons-clipboard"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <select id="event_category_select">
                            <option value=""><?php _e('Select Category', 'basic-events'); ?></option>
                            <?php foreach ($event_categories as $category) : ?>
                                <option value="<?php echo esc_attr($category->slug); ?>"><?php echo esc_html($category->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><?php _e('Displays a list of upcoming events filtered by a specific category. Optionally specify the number of events to display with the "count" attribute.', 'basic-events'); ?></td>
                </tr>
                <tr>
                    <td>
                        <div class="shortcode-wrapper">
                            <input type="text" id="upcoming_events_shortcode" value="[upcoming_events count='5']" readonly onclick="this.select();" />
                            <span class="copy-button" data-clipboard-target="#upcoming_events_shortcode" title="Copy to clipboard">
                                <span class="dashicons dashicons-clipboard"></span>
                            </span>
                        </div>
                    </td>
                    <td>-</td>
                    <td><?php _e('Displays a list of upcoming events. Optionally specify the number of events to display with the "count" attribute.', 'basic-events'); ?></td>
                </tr>
                <tr>
                    <td>
                        <div class="shortcode-wrapper">
                            <input type="text" id="single_event_shortcode" value="[single_event id='123']" readonly onclick="this.select();" />
                            <span class="copy-button" data-clipboard-target="#single_event_shortcode" title="Copy to clipboard">
                                <span class="dashicons dashicons-clipboard"></span>
                            </span>
                        </div>
                    </td>
                    <td>-</td>
                    <td><?php _e('Displays a single event based on the event ID. Each event will show you its specific shortcode in the right side panel.', 'basic-events'); ?></td>
                </tr>
                <tr>
                    <td>
                        <div class="shortcode-wrapper">
                            <input type="text" id="past_events_shortcode" value="[past_events start='' end='']" readonly onclick="this.select();" />
                            <span class="copy-button" data-clipboard-target="#past_events_shortcode" title="Copy to clipboard">
                                <span class="dashicons dashicons-clipboard"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="date-fields-wrapper">
                            <div class="date-field">
                                <label for="past_events_start"><?php _e('Start Date:', 'basic-events'); ?></label>
                                <input type="date" id="past_events_start" placeholder="Start Date" />
                            </div>
                            <div class="date-field">
                                <label for="past_events_end"><?php _e('End Date:', 'basic-events'); ?></label>
                                <input type="date" id="past_events_end" placeholder="End Date" />
                            </div>
                        </div>
                        <br>
                        <label>
                            <input type="checkbox" id="past_events_all" />
                            <?php _e('All past events', 'basic-events'); ?>
                        </label>
                    </td>
                    <td><?php _e('Displays a list of past events. Optionally specify a start date and/or end date, or select "All past events" to display all past events.', 'basic-events'); ?></td>
                </tr>
                <tr>
                    <td>
                        <div class="shortcode-wrapper">
                            <input type="text" id="events_by_location_shortcode" value="[events_by_location type='' value='' exact='false' radius='']" readonly onclick="this.select();" />
                            <span class="copy-button" data-clipboard-target="#events_by_location_shortcode" title="Copy to clipboard">
                                <span class="dashicons dashicons-clipboard"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div>
                            <label for="location_type"><?php _e('Location Type:', 'basic-events'); ?></label>
                            <select id="location_type">
                                <option value=""><?php _e('Select Type', 'basic-events'); ?></option>
                                <option value="city"><?php _e('City', 'basic-events'); ?></option>
                                <option value="state"><?php _e('State', 'basic-events'); ?></option>
                            </select>
                        </div>
                        <div id="events_by_location_city" style="display: none;">
                            <label for="location_value_city"><?php _e('City:', 'basic-events'); ?></label>
                            <input type="text" id="location_value_city">
                        </div>
                        <div id="events_by_location_state" style="display: none;">
                            <label for="location_value_state"><?php _e('State:', 'basic-events'); ?></label>
                            <select id="location_value_state">
                                <option value="AL">Alabama</option>
                                <option value="AK">Alaska</option>
                                <option value="AZ">Arizona</option>
                                <option value="AR">Arkansas</option>
                                <option value="CA">California</option>
                                <option value="CO">Colorado</option>
                                <option value="CT">Connecticut</option>
                                <option value="DE">Delaware</option>
                                <option value="FL">Florida</option>
                                <option value="GA">Georgia</option>
                                <option value="HI">Hawaii</option>
                                <option value="ID">Idaho</option>
                                <option value="IL">Illinois</option>
                                <option value="IN">Indiana</option>
                                <option value="IA">Iowa</option>
                                <option value="KS">Kansas</option>
                                <option value="KY">Kentucky</option>
                                <option value="LA">Louisiana</option>
                                <option value="ME">Maine</option>
                                <option value="MD">Maryland</option>
                                <option value="MA">Massachusetts</option>
                                <option value="MI">Michigan</option>
                                <option value="MN">Minnesota</option>
                                <option value="MS">Mississippi</option>
                                <option value="MO">Missouri</option>
                                <option value="MT">Montana</option>
                                <option value="NE">Nebraska</option>
                                <option value="NV">Nevada</option>
                                <option value="NH">New Hampshire</option>
                                <option value="NJ">New Jersey</option>
                                <option value="NM">New Mexico</option>
                                <option value="NY">New York</option>
                                <option value="NC">North Carolina</option>
                                <option value="ND">North Dakota</option>
                                <option value="OH">Ohio</option>
                                <option value="OK">Oklahoma</option>
                                <option value="OR">Oregon</option>
                                <option value="PA">Pennsylvania</option>
                                <option value="RI">Rhode Island</option>
                                <option value="SC">South Carolina</option>
                                <option value="SD">South Dakota</option>
                                <option value="TN">Tennessee</option>
                                <option value="TX">Texas</option>
                                <option value="UT">Utah</option>
                                <option value="VT">Vermont</option>
                                <option value="VA">Virginia</option>
                                <option value="WA">Washington</option>
                                <option value="WV">West Virginia</option>
                                <option value="WI">Wisconsin</option>
                                <option value="WY">Wyoming</option>
                            </select>
                        </div>
                        <div id="exact_match_wrapper">
                            <label>
                                <input type="radio" name="match_type" value="exact" checked>
                                <?php _e('Exact Match', 'basic-events'); ?>
                            </label>
                            <label>
                                <input type="radio" name="match_type" value="radius">
                                <?php _e('Radius Match', 'basic-events'); ?>
                            </label>
                        </div>
                        <div id="radius_wrapper" style="display: none;">
                            <label for="radius_value"><?php _e('Radius (miles):', 'basic-events'); ?></label>
                            <input type="number" id="radius_value" min="1" placeholder="Enter radius" />
                        </div>
                    </td>
                    <td><?php _e('Displays a list of events that match the specified location type and value. Supports exact and radius matches for cities.', 'basic-events'); ?></td>
                </tr>

                <tr>
                    <td>
                        <div class="shortcode-wrapper">
                            <input type="text" id="event_countdown_shortcode" value="[event_countdown id='123' style='plain']" readonly onclick="this.select();" />
                            <span class="copy-button" data-clipboard-target="#event_countdown_shortcode" title="Copy to clipboard">
                                <span class="dashicons dashicons-clipboard"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <label for="countdown_style"><?php _e('Style:', 'basic-events'); ?></label>
                        <select id="countdown_style">
                            <option value="plain"><?php _e('Plain Text', 'basic-events'); ?></option>
                            <option value="box"><?php _e('Box', 'basic-events'); ?></option>
                            <option value="flip"><?php _e('Flip Card', 'basic-events'); ?></option>
                        </select>
                    </td>
                    <td><?php _e('Displays a countdown timer for a specific event. Use the Event ID shown in the right side panel of the event.', 'basic-events'); ?></td>
                </tr>

                <tr>
                    <td>
                        <div class="shortcode-wrapper">
                            <input type="text" id="featured_events_shortcode" value='[featured_events count="12"]' readonly onclick="this.select();" />
                            <span class="copy-button" data-clipboard-target="#featured_events_shortcode" title="Copy to clipboard">
                                <span class="dashicons dashicons-clipboard"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <label for="featured_events_count"><?php _e('Count:', 'basic-events'); ?></label>
                        <input type="number" id="featured_events_count" min="1" placeholder="Enter number of events" />
                        <label for="featured_events_all"><?php _e('Show all:', 'basic-events'); ?></label>
                        <input type="checkbox" id="featured_events_all" />
                    </td>
                    <td><?php _e('Displays a list of featured events. Specify the number of events to display with the "count" attribute, or select "Show all" to display all featured events.', 'basic-events'); ?></td>
                </tr>

                <tr>
                    <td>
                        <div class="shortcode-wrapper">
                            <input type="text" id="events_calendar_shortcode" value="[events_calendar]" readonly onclick="this.select();" />
                            <span class="copy-button" data-clipboard-target="#events_calendar_shortcode" title="Copy to clipboard">
                                <span class="dashicons dashicons-clipboard"></span>
                            </span>
                        </div>
                    </td>
                    <td>-</td>
                    <td><?php _e('Displays a calendar with events. Allows users to switch between monthly, weekly, and daily views, as well as list view.', 'basic-events'); ?></td>
                </tr>


                <!-- Add more shortcodes here as they are implemented -->
            </tbody>
        </table>
    </div>
    <script type="text/javascript">
        document.getElementById('event_category_select').addEventListener('change', function() {
            var selectedCategory = this.value;
            var shortcodeInput = document.getElementById('upcoming_events_by_category_shortcode');
            shortcodeInput.value = '[upcoming_events_by_category category="' + selectedCategory + '"]';
        });

        document.querySelectorAll('.copy-button').forEach(button => {
            button.addEventListener('click', function() {
                var target = document.querySelector(this.getAttribute('data-clipboard-target'));
                target.select();
                document.execCommand('copy');
                this.setAttribute('title', 'Copied!');
                this.innerHTML = '<span class="dashicons dashicons-yes"></span>';
                setTimeout(() => {
                    this.setAttribute('title', 'Copy to clipboard');
                    this.innerHTML = '<span class="dashicons dashicons-clipboard"></span>';
                }, 2000);
            });
        });

        document.getElementById('past_events_all').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('past_events_start').disabled = true;
                document.getElementById('past_events_end').disabled = true;
                document.getElementById('past_events_shortcode').value = '[past_events]';
            } else {
                document.getElementById('past_events_start').disabled = false;
                document.getElementById('past_events_end').disabled = false;
                updatePastEventsShortcode();
            }
        });

        document.getElementById('past_events_start').addEventListener('change', function() {
            var startDateInput = document.getElementById('past_events_start');
            var endDateInput = document.getElementById('past_events_end');
            endDateInput.max = startDateInput.value;
            if (endDateInput.value && endDateInput.value > startDateInput.value) {
                endDateInput.value = '';
            }
            updatePastEventsShortcode();
        });

        document.getElementById('past_events_end').addEventListener('change', updatePastEventsShortcode);

        function updatePastEventsShortcode() {
            var startDate = document.getElementById('past_events_start').value;
            var endDate = document.getElementById('past_events_end').value;
            var shortcode = '[past_events';
            if (startDate) {
                shortcode += ' start="' + startDate + '"';
            }
            if (endDate) {
                shortcode += ' end="' + endDate + '"';
            }
            shortcode += ']';
            document.getElementById('past_events_shortcode').value = shortcode;
        }

        document.getElementById('location_type').addEventListener('change', function() {
            var locationType = this.value;
            var cityField = document.getElementById('events_by_location_city');
            var stateField = document.getElementById('events_by_location_state');

            if (locationType === 'city') {
                cityField.style.display = 'block';
                stateField.style.display = 'none';
                document.getElementById('exact_match_wrapper').style.display = 'block';
                if (document.querySelector('input[name="match_type"]:checked').value === 'radius') {
                    document.getElementById('radius_wrapper').style.display = 'block';
                }
            } else if (locationType === 'state') {
                cityField.style.display = 'none';
                stateField.style.display = 'block';
                document.getElementById('exact_match_wrapper').style.display = 'none';
                document.getElementById('radius_wrapper').style.display = 'none';
            }
            updateEventsByLocationShortcode();
        });

        document.getElementById('location_value_city').addEventListener('input', updateEventsByLocationShortcode);
        document.getElementById('location_value_state').addEventListener('change', updateEventsByLocationShortcode);
        document.querySelectorAll('input[name="match_type"]').forEach(function(elem) {
            elem.addEventListener('change', function() {
                if (this.value === 'radius') {
                    document.getElementById('radius_wrapper').style.display = 'block';
                } else {
                    document.getElementById('radius_wrapper').style.display = 'none';
                }
                updateEventsByLocationShortcode();
            });
        });
        document.getElementById('radius_value').addEventListener('input', updateEventsByLocationShortcode);

        function updateEventsByLocationShortcode() {
            var locationType = document.getElementById('location_type').value;
            var locationValue = locationType === 'city' ? document.getElementById('location_value_city').value : document.getElementById('location_value_state').value;
            var exactMatch = document.querySelector('input[name="match_type"]:checked').value === 'exact';
            var radius = document.getElementById('radius_value').value;

            var shortcode = '[events_by_location type="' + locationType + '" value="' + locationValue + '" exact="' + exactMatch + '"';
            if (locationType === 'city' && !exactMatch) {
                shortcode += ' radius="' + radius + '"';
            }
            shortcode += ']';
            document.getElementById('events_by_location_shortcode').value = shortcode;
        }

        document.getElementById('countdown_style').addEventListener('change', updateEventCountdownShortcode);

        function updateEventCountdownShortcode() {
            var style = document.getElementById('countdown_style').value || 'plain';

            var shortcode = '[event_countdown id="123" style="' + style + '"]';
            document.getElementById('event_countdown_shortcode').value = shortcode;
        }

        document.getElementById('featured_events_count').addEventListener('input', updateFeaturedEventsShortcode);
        document.getElementById('featured_events_all').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('featured_events_count').disabled = true;
            } else {
                document.getElementById('featured_events_count').disabled = false;
            }
            updateFeaturedEventsShortcode();
        });

        document.getElementById('featured_events_count').addEventListener('input', updateFeaturedEventsShortcode);
        document.getElementById('featured_events_all').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('featured_events_count').disabled = true;
            } else {
                document.getElementById('featured_events_count').disabled = false;
            }
            updateFeaturedEventsShortcode();
        });

        function updateFeaturedEventsShortcode() {
            var count = document.getElementById('featured_events_all').checked ? 'all' : document.getElementById('featured_events_count').value;
            var shortcode = '[featured_events count="' + count + '"]';
            document.getElementById('featured_events_shortcode').value = shortcode;
        }

        document.querySelectorAll('.copy-button').forEach(button => {
            button.addEventListener('click', function() {
                var target = document.querySelector(this.getAttribute('data-clipboard-target'));
                target.select();
                document.execCommand('copy');
                this.setAttribute('title', 'Copied!');
                this.innerHTML = '<span class="dashicons dashicons-yes"></span>';
                setTimeout(() => {
                    this.setAttribute('title', 'Copy to clipboard');
                    this.innerHTML = '<span class="dashicons dashicons-clipboard"></span>';
                }, 2000);
            });
        });
    </script>

    <style>
        .shortcode-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .shortcode-wrapper input {
            width: 100%;
            background: white;
            border: 1px solid #ccc;
            padding: 5px 30px 5px 5px;
            /* Adjust padding to make room for the icon */
            border-radius: 3px;
        }

        .copy-button {
            position: absolute;
            right: 5px;
            cursor: pointer;
            padding: 5px;
        }

        .copy-button .dashicons {
            font-size: 18px;
        }

        .date-fields-wrapper {
            display: flex;
            gap: 10px;
        }

        .date-field {
            display: flex;
            flex-direction: column;
        }

        .date-field label {
            margin-bottom: 5px;
        }
    </style>
<?php
}
