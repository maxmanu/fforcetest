<?php
/*
Plugin Name: Home Test
Plugin URI: https://maxmanuel.com/
Description: Plugin that shows git commit history
Version: 1.0
Author: Max Manuel
Author URI: https://maxmanuel.com/
Text domain: home-test
*/
defined('ABSPATH') or die("Por aquí no vamos a ninguna parte");
define('WPHT_DIR', plugin_dir_path(__FILE__));

/**
 * This class represents a WordPress settings page for display commits.
 * It sets up the page and its contents, including sections and fields.
 * The class uses WordPress hooks to add actions and filters to customize the settings page.
 * The class includes methods to create the settings page, render its content,
 * set up the sections and fields, and handle form submissions.
 */
class commits_Settings_Page
{

  public function __construct()
  {
    add_action('admin_menu', array($this, 'wpht_create_settings'));
  }

  public function wpht_create_settings()
  {
    $page_title = 'Commits';
    $menu_title = 'Commits';
    $capability = 'manage_options';
    $slug = 'commits';
    $callback = array($this, 'wpht_settings_content');
    $icon = 'dashicons-editor-ul';
    $position = 2;
    add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
  }

  public function wpht_settings_content()
  {
    $username = 'maxmanu';
    $repo = 'fforcetest';
    $url = "https://api.github.com/repos/{$username}/{$repo}/commits";
    $response = wp_remote_get($url);

?>
    <div class="wrap">
      <h1>Commits</h1>
      <?php
      if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        $body = wp_remote_retrieve_body($response);
        $commits = json_decode($body, true);
        foreach ($commits as $commit) {
          $commit_sha = $commit['sha'];
          $commit_url = "https://github.com/{$username}/{$repo}/commit/{$commit_sha}";

          echo "<strong>Commit:</strong> <a href='{" . esc_url($commit_url) . "}' target='_blank'>" . esc_html(substr($commit['sha'], 0, 7)) . "</a><br>";
          echo "<strong>Fecha:</strong> " . esc_html($commit['commit']['author']['date']) . "<br>";
          echo "<strong>Autor:</strong> " . esc_html($commit['commit']['author']['name']) . "<br>";
          echo "<strong>Mensaje:</strong> " . esc_html($commit['commit']['message']) . "<br><br>";
        }
      } else {
        echo "Error al obtener los commits.";
      }
      ?>
      <?php settings_errors(); ?>
      <form method="POST" action="options.php">
        <?php
        settings_fields('commits');
        do_settings_sections('commits');
        // submit_button();
        ?>
      </form>
    </div>
<?php }
}
new commits_Settings_Page();
