<?php
/**
 * A custom WordPress comment walker class to implement the Materialize Media object in wordpress comment list.
 *
 * @package     Materialize Comment Walker
 * @version     1.0.0
 * @author      Dom Noble <domnoble.com>
 * @license     http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 */

namespace App;

/** COMMENTS WALKER */
class MaterializeX_Walker_Comment extends \Walker_Comment {
  /**
	 * Output a comment in the HTML5 format.
	 *
	 * @access protected
	 * @since 1.0.0
	 *
	 * @see wp_list_comments()
	 *
	 * @param object $comment Comment to display.
	 * @param int    $depth   Depth of comment.
	 * @param array  $args    An array of arguments.
	 */
	protected function html5_comment( $comment, $depth, $args ) {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
    global $main_options;
?>
<?php var_dump($main_options); ?>
		<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent media' : 'media' ); ?>>
    <div class="col s12">
              <div class="row valign-wrapper">
			<?php if ( 0 != $args['avatar_size'] ): ?>
			<div class="col s2">
				<a href="<?php echo get_comment_author_url(); ?>" class="media-object">
					<?php echo get_avatar( $comment, 64,'','', array ('size' => '64','class' => array ('circle','responsive-img')) ); ?>
				</a>
			</div>
			<?php endif; ?>

			<div class="col s10" id="div-comment-<?php comment_ID(); ?>">
				<?php printf( '<h4 class="card-title">%s</h4>', get_comment_author_link() ); ?>

				<div class="comment-metadata">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID, $args ) ); ?>">
						<time datetime="<?php comment_time( 'c' ); ?>">
							<?php printf( _x( '%1$s at %2$s', '1: date, 2: time' ), get_comment_date(), get_comment_time() ); ?>
						</time>
					</a>
				</div><!-- .comment-metadata -->

				<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation label label-info"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
				<?php endif; ?>

				<div class="comment-content">
					<?php comment_text(); ?>
				</div><!-- .comment-content -->

				<div class="valign-wrapper">
          <?php $btnclass = "btn blue"; ?>

				  <?php echo preg_replace( '/class="comment-edit-link"/', 'class="comment-edit-link ' . $btnclass . '"', edit_comment_link( __( 'Edit' ), '', '' ), 1 ); ?>

          <?php echo preg_replace( '/class="comment-reply-link"/', 'class="comment-reply-link ' . $btnclass. '"',
          comment_reply_link( array_merge( $args, array(
            'add_below' => 'div-comment',
            'depth'     => $depth,
            'max_depth' => $args['max_depth'],
            'before'    => '',
            'after'     => ''
          ) ) ), 1 ); ?>

				</div>
    </div>
  </div>

			</div>
<?php
}
}
