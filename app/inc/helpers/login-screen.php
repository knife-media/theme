<?php
/**
* Custom login screen
*
* Styling wp-login.php page
*
* @package knife-theme
* @since 1.1
*/

new Knife_Login_Screen;

class Knife_Login_Screen {
	public function __construct() {
		add_filter('login_enqueue_scripts', [$this, 'login_styles']);
		add_action('login_headerurl', [$this, 'change_url']);
		add_action('login_headertitle', [$this, 'change_title']);
		add_action('admin_print_styles', [$this, 'admin_styles']);
	}

	/**
	 * Prints custom styles with custom logo
	 */
	public function login_styles() {
		$logo = get_template_directory_uri() . '/assets/images/logo-white.svg';
	?>
		<style type="text/css">
			.login {
				background-color: black;
			}

			.login #login h1 a {
				width: 120px;
				height: 64px;
				background-image: url(<?php echo $logo; ?>);
				background-size: contain;
				background-position: center;
				box-shadow: none;
			}

			.login #login #nav {
				margin-top: 20px;
				text-align: center;
			}

			.login #login #nav a {
				color: #eee;
				border-bottom: solid 1px #ddd;
			}

			.login #login #nav a:hover {
				border-bottom-color: transparent;
			}

 			.login #login #backtoblog {
				display: none;
			}
		</style>
	<?php
	}


	/**
	 * We have to style login layer on auth-check
	 */
	public function admin_styles() {
	?>
		<style type="text/css">
			.wp-admin #wp-auth-check-wrap #wp-auth-check {
				background-color: black;
			}
		</style>
	<?php
	}

	/**
	 * Change logo links to front page instead of wordpress.org
	 */
	public function change_url() {
		return home_url();
	}

	/**
	 * Change logo title
	 */
	public function change_title() {
		return __('На главную', 'knife-theme');
	}
}
