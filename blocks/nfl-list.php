<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package acme-nfl
 */

/**
 * Helper function that generates subtable markup
 *
 * @param $table_data
 * @param $header
 * @return string
 */
function generate_subtable_markup($table_data, $header) {

    $subtable_markup = "<h2>{$header}</h2>";
    foreach ($table_data as $key => $teams) {
        $subtable_markup .= "<h3>{$key}</h3>";
        $subtable_markup .= '<table><thead><th>Team Name</th><th>Team Nickname</th></thead><tbody>';
        foreach ($teams as $team) {
            $subtable_markup .= "<tr><td>{$team['name']}</td><td>{$team['nickname']}</td></tr>";
        }
        $subtable_markup .= '</tbody></table>';
    }


    return $subtable_markup;
}

/**
 * Gutenberg block callback for dynamic NFL team information.
 *
 * @param $attributes
 * @param $content
 * @return string
 */
function nfl_list_render_callback($attributes, $content) {
    /** @var array|WP_Error $response */
    $response = wp_remote_get('http://delivery.chalk247.com/team_list/NFL.JSON?api_key=74db8efa2a6db279393b433d97c2bc843f8e32b0');

    $nfl_all_markup = '<div class="wp-block-acme-nfl-nfl-list">';
    $nfl_all_markup .= '<h2>All NFL Teams</h2>';
    $nfl_all_markup .= '<table><thead><th>Team Name</th><th>Team Nickname</th><th>Conference</th><th>Division</th></thead><tbody>';

    if (is_array($response) && !is_wp_error($response)) {
        $api_response = json_decode(wp_remote_retrieve_body($response), true);

        $conferences = [];
        $divisions   = [];
        if (isset($api_response['results']['data']['team'])) {
            foreach ($api_response['results']['data']['team'] as $team) {
                $team_conference = $team['conference'];
                $team_division   = $team['division'];

                // Assign team to conferences array
                if (!isset($conferences[$team_conference])) {
                    $conferences[$team_conference] = [];
                }
                $conferences[$team_conference][] = $team;

                // Assign team to divisions array
                if (!isset($divisions[$team_division])) {
                    $divisions[$team_division] = [];
                }
                $divisions[$team_division][] = $team;

                $nfl_all_markup .= "<tr><td>{$team['name']}</td><td>{$team['nickname']}</td><td>{$team['conference']}</td><td>{$team['division']}</td></tr>";
            }

            $nfl_all_markup .= '</tbody></table>';
        }

        $nfl_all_markup .= generate_subtable_markup($conferences, 'Conferences');
        $nfl_all_markup .= generate_subtable_markup($divisions, 'Divisions');
    }

    $nfl_all_markup .= '</div>';

    return $nfl_all_markup;
}

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function nfl_list_block_init() {
    // Skip block registration if Gutenberg is not enabled/merged.
    if (!function_exists('register_block_type')) {
        return;
    }
    $dir = dirname(__FILE__);

    $index_js = 'nfl-list/index.js';
    wp_register_script('nfl-list-block-editor', plugins_url($index_js, __FILE__), array('wp-blocks',
                                                                                        'wp-i18n',
                                                                                        'wp-element',), filemtime("$dir/$index_js"));

    $editor_css = 'nfl-list/editor.css';
    wp_register_style('nfl-list-block-editor', plugins_url($editor_css, __FILE__), array(), filemtime("$dir/$editor_css"));

    $style_css = 'nfl-list/style.css';
    wp_register_style('nfl-list-block', plugins_url($style_css, __FILE__), array(), filemtime("$dir/$style_css"));

    register_block_type('acme-nfl/nfl-list', array('editor_script'   => 'nfl-list-block-editor',
                                                   'editor_style'    => 'nfl-list-block-editor',
                                                   'style'           => 'nfl-list-block',
                                                   'render_callback' => 'nfl_list_render_callback'));
}

add_action('init', 'nfl_list_block_init');
