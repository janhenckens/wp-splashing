<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://studioespresso.co
 * @since      1.0.0
 *
 * @package    Wp_Splashing
 * @subpackage Wp_Splashing/admin/partials
 */
?>

<div class="wrap">
    <h1>
		<?php _e('Splashing Images', 'wp-splashing-images')?>
		<span style="filter: grayscale(100%);">&#128247; </span>
	</h1>
		<?php if($_GET['session']) {

			$data = unserialize(base64_decode($_GET['session']));
			$this->unsplash->saveTokens($data['token']);
			$user = $this->unsplash->getUser(); ?>
			<div class="notice inline notice-info notice-alt">
				<p>
					<?php
					echo sprintf( __('You\'re now connected to Unsplash as <a href="%1$s" target="_blank">%2$s</a>', 'wp-splashing-image'), $user->links['html'], $user->username);
					?>
				</p>
			</div>
<?php } ?>

	<h2 class="nav-tab-wrapper">

		<a href="/wp-admin/upload.php?page=wp-splashing&mode=popular" class="nav-tab <?php echo $_GET['mode'] == 'popular' ? 'nav-tab-active' : ''; ?>" alt="<?php _e('25 most popular photos on Unsplash', 'wp-splashing-images'); ?>"><span style="color: green;">&#8599; </span><?php _e('Popular', 'wp-splashing-images'); ?></a>
		<a href="/wp-admin/upload.php?page=wp-splashing&mode=latest" class="nav-tab <?php echo $_GET['mode'] == 'latest' ? 'nav-tab-active' : ''; ?>" alt="<?php _e('Shows the 25 last added images', 'wp-splashing-images'); ?>"><?php _e('&#128349; Last added', 'wp-splashing-images'); ?></a>
		<a href="/wp-admin/upload.php?page=wp-splashing&mode=random" class="nav-tab <?php echo $_GET['mode'] == 'random' ? 'nav-tab-active' : ''; ?>" alt="<?php _e('Shows 25 random images', 'wp-splashing-images'); ?>"><span style="color: gold;">&#9786; </span><?php _e('Surprise me :)', 'wp-splashing-images'); ?></a>
		<?php if($this->unsplash->isUnsplashUser()) { ?>
		<a href="/wp-admin/upload.php?page=wp-splashing&mode=liked" class="nav-tab <?php echo $_GET['mode'] == 'liked' ? 'nav-tab-active' : ''; ?>" alt="<?php _e('Shows 25 random images', 'wp-splashing-images'); ?>"><span style="color: red;">&hearts; </span><?php _e('Photos I like', 'wp-splashing-images'); ?></a>
		<a href="/wp-admin/upload.php?page=wp-splashing&mode=mine" class="nav-tab <?php echo $_GET['mode'] == 'mine' ? 'nav-tab-active' : ''; ?>" alt="<?php _e('Shows 25 random images', 'wp-splashing-images'); ?>"><span style="color: gold;">&#128100; </span><?php _e('My photos', 'wp-splashing-images'); ?></a>
		<?php } ?>

	</h2>
	<div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
			<div id="wp-splashing_images" style="position: relative;" class="postbox-container">
				<div id="splashing-images">
					<?php
					if(isset($_GET['search'])) {
						$data = $this->unsplash->search($_GET['search'], $_GET['paged']);
						$images = $data['results'];
					} elseif(isset($_GET['mode']) && $_GET['mode'] == 'random') {
                        $images = $this->unsplash->getRandom(25);
					} elseif(isset($_GET['mode']) && $_GET['mode'] == 'latest') {
                        $images = $this->unsplash->getLatest(25);
                    } elseif(isset($_GET['mode']) && $_GET['mode'] == 'popular') {
                        $images = $this->unsplash->getPopular(25);
                    } elseif(isset($_GET['mode']) && $_GET['mode'] == 'liked') {
						$images = $this->unsplash->getLiked(1, 100);
					} elseif(isset($_GET['mode']) && $_GET['mode'] == 'mine') {
						$images = $this->unsplash->getOwnImages(1, 100);
					} else {
						$images = $this->unsplash->getLastFeatured(24);
					}
					if($images != false) {
						echo '<div class="wrapper" id="splashing-results">';
						foreach($images as $image) {
							$thumb = $image->urls['thumb'];
							$download = $image->links['download'];
							$author = $image->user['name'];
							echo '<a href="" class="upload" data-source="' . $download . '" data-author="' . $author . '">
								<img class="splashing-thumbnail ms-item" src="' . $thumb .'">
							</a>';
						}
						echo '</div>';
					} else {


						echo '<div><p>' . sprintf( __('Looks like me couldn\'t find any images for <strong><em>%1$s</em></strong>, try searching for something else or surprise yourself.','wp-splashing-images'), $_GET['search']) . '</p></div>';
					} ?>

				</div>
				<?php
				if(isset($data['pagination'])) {
					$args = array(
						'base' 				 => preg_replace('/\?.*/', '', get_pagenum_link()) . '%_%',
						'format'             => '?paged=%#%',
						'total'              => $data['pagination']['total_pages'],
						'current'            => $_GET['paged'],
						'show_all'           => false,
						'end_size'           => 2,
						'mid_size'           => 3,
						'prev_next'          => true,
						'prev_text'          => __('« Previous', 'wp-splashing-images'),
						'next_text'          => __('Next »', 'wp-splashing-images'),
						'type'               => 'plain',
						'add_args'           => false,
					);
					echo '<div class="splashing-pagination">';
					echo paginate_links( $args );
					echo '</div>';
				}
				?>
			</div>
			<?php require(plugin_dir_path( __FILE__ ) . 'wp-splashing-admin-sidebar.php'); ?>
    	</div>
	</div>
</div>
<script type="text/javascript">

	jQuery(document).ready(function() {
		var $grid = jQuery('#splashing-images').masonry({
			itemSelector: 'a.upload'
		});

		$grid.imagesLoaded().progress( function() {
			$grid.masonry('layout');
		});

	});
</script>
