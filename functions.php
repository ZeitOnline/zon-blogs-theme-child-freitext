<?php


add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	wp_enqueue_style(
		'zb-freitext',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'zb-style' ),
		@filemtime( get_stylesheet_directory() . '/style.css' )
	);
}

function zb_posted_by() {
	$byline = sprintf(
		esc_html_x( '%s', 'post author', 'zb' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

}

function zb_entry_footer($separator = "") {
	// Hide category and tag text for pages.
	if ( 'post' == get_post_type() ) {
		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
  		echo '<span class="category-link">aus: ';
  		the_category( ', ' );
  		echo '</span>';
		  echo $separator;
		  echo '<span class="comments-link">';
		  comments_popup_link( esc_html__( '0 Comments', 'zb' ), esc_html__( '1 Comment', 'zb' ), esc_html__( '% Comments', 'zb' ) );
		  echo '</span>';
	  }
  }
}

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function zb_author_widgets_init() {
	register_sidebar( array(
		'name'          => 'home Area',
		'id'            => 'home-author',
		'before_widget' => '<div class="widget-area">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => 'Article Area',
		'id'            => 'article-author',
		'before_widget' => '<div class="widget-area">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'zb_author_widgets_init' );

if ( ! function_exists( 'zeitonline_list_authors' ) ) :
/**
 * Print a list of all site contributors who published at least one post.
 *
 * @return void
 */
function zeitonline_list_authors() {
	$contributor_ids = get_users( array(
		'fields'  => 'ID',
		'orderby' => 'display_name',
		'order'   => 'ASC',
		// 'orderby' => 'post_count',
		// 'order'   => 'DESC',
		'who'     => 'authors',
	) );

	$author_ids = get_users( array(
		'fields'  => 'ID',
		'orderby' => 'display_name',
		'order'   => 'ASC',
		'role'    => 'author',
	) );

	$user_ids = array();

	echo '<div class="widget widget-authors-list">';

	foreach ( $contributor_ids as $id ) :
		$post_count = count_user_posts( $id );

		// Move on if user has not published a post (yet).
		if ( ! $post_count ) {
			continue;
		}

		$user_ids[] = $id;
	?>

	<div class="widget-authors">
		<a href="<?php echo esc_url( get_author_posts_url( $id ) ); ?>">
			<?php echo get_avatar( $id, 60 ); ?>
			<h2 class="widget-item"><?php echo get_the_author_meta( 'display_name', $id ); ?></h2>
			<p class="widget-description">
				<?php echo get_the_author_meta( 'description', $id ); ?>
			</p>
		</a>
	</div>

	<?php
	endforeach;

	$author_ids = array_diff( $author_ids, $user_ids );

	foreach ( $author_ids as $id ) :
	?>

	<div class="widget-authors">
		<?php echo get_avatar( $id, 60 ); ?>
		<h2 class="widget-item"><?php echo get_the_author_meta( 'display_name', $id ); ?></h2>
		<p class="widget-description">
			<?php echo get_the_author_meta( 'description', $id ); ?>
		</p>
	</div>

	<?php
	endforeach;

	echo '</div>';
}
endif;


function zb_author_add_meta_box() {
	add_meta_box(
		'zb-layout',
		__( 'Autorenbox', 'zb' ),
		'zb_author_metabox_html',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'zb_author_add_meta_box' );

function zb_author_metabox_html( $post) {
	wp_nonce_field( '_zb_nonce', 'zb_nonce' ); ?>

	<p>
    	<input type="radio" name="zb_author_box" id="zb_paragraph_author_box" value="paragraph-author-box" <?php echo ( (zb_get_meta( 'zb_author_box' ) === 'paragraph-author-box') OR !zb_get_meta( 'zb_author_box' ) ) ? 'checked' : ''; ?>>
  		<label for="zb_paragraph_author_box">nach dem <input type="number" name="zb_author_box_paragraph" id="zb_author_box_paragraph" value="<?php echo ( !zb_get_meta( 'zb_author_box_paragraph' ) ? '1' : zb_get_meta( 'zb_author_box_paragraph' ) ); ?>" style="width:40px;">. Absatz anzeigen</label><br />
  		<input type="radio" name="zb_author_box" id="zb_bottom_author_box" value="bottom-author-box" <?php echo ( zb_get_meta( 'zb_author_box' ) === 'bottom-author-box' ) ? 'checked' : ''; ?>>
  		<label for="zb_paragraph_author_box">unten anzeigen</label><br />
  		<input type="radio" name="zb_author_box" id="zb_hide_author_box" value="hide-author-box" <?php echo ( (zb_get_meta( 'zb_author_box' ) === 'hide-author-box') OR (zb_get_meta( 'zb_hide_author_box' ) === 'hide-author-box') ) ? 'checked' : ''; ?>>
  		<label for="zb_hide_author_box">ausblenden</label>
    </p>
  <?php
}

function zb_author_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['zb_nonce'] ) || ! wp_verify_nonce( $_POST['zb_nonce'], '_zb_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['zb_author_box'] ) )
		update_post_meta( $post_id, 'zb_author_box', esc_attr( $_POST['zb_author_box'] ) );
	else
		update_post_meta( $post_id, 'zb_author_box', null );
		
  if ( isset( $_POST['zb_author_box_paragraph'] ) )
		update_post_meta( $post_id, 'zb_author_box_paragraph', esc_attr( $_POST['zb_author_box_paragraph'] ) );
}
add_action( 'save_post', 'zb_author_save' );
