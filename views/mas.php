<?php
$total_view    = count( $post_stats['has_read'] );
$ratio_percent = ceil( ( $total_view / $post_stats['total_users'] ) * 100 );
?>

<a href="#" class="mas-tooltip tooltip" data-tooltip-content="#tooltip_content">
	<?php printf( __( 'Seen by %d people (%d%%)', 'bea-mark-as-read' ), $total_view, $ratio_percent ); ?>
</a>

<div class="tooltip_templates">
    <div id="tooltip_content" class="vu">
        <ul class="vu__filter">
			<?php if ( ! empty( $post_stats['has_read'] ) ) : ?>
                <li class="li_is_col">
                    <label for="vu"><?php _e( 'Read', 'bea-mark-as-read' ); ?></label>
                    <ul class="vu__content">
						<?php foreach ( $post_stats['has_read'] as $user ) : ?>
                            <li><?php echo $user->first_name . ' ' . $user->last_name; ?></li>
						<?php endforeach; ?>
                    </ul>
                </li>
			<?php endif; ?>
			<?php if ( ! empty( $post_stats['has_no_read'] ) ) : ?>
                <li class="li_is_col">
                    <label for="pasvu"><?php _e( 'Unread', 'bea-mark-as-read' ); ?></label>
                    <ul class="vu__content">
						<?php foreach ( $post_stats['has_no_read'] as $user ) : ?>
                            <li><?php echo $user->first_name . ' ' . $user->last_name; ?></li>
						<?php endforeach; ?>
                    </ul>
                </li>
			<?php endif; ?>
            <li class="clear"></li>
        </ul>
    </div>
</div>