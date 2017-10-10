<?php

$total_view = count( $users_has_read );

$pourcent = ceil( (count($users_has_read) / count($all_users) ) * 100 );
?>

<a href="#" class="mas-tooltip tooltip" data-tooltip-content="#tooltip_content">Vu par <?php echo $total_view ?> personnes (<?php echo $pourcent; ?>%)</a>
<div class="tooltip_templates">
    <div id="tooltip_content" class="vu">
        <ul class="vu__filter">
    		<?php if ( ! empty( $users_has_read ) ) : ?>
                <li class="li_is_col">
                    <label for="vu">Vu</label>
                    <ul class="vu__content">
    					<?php foreach ( $users_has_read as $u_unread ) : ?>
                            <li><?php echo $u_unread->first_name . ' ' . $u_unread->last_name; ?></li>
    					<?php endforeach; ?>
                    </ul>
                </li>
    		<?php endif; ?>
    		<?php if ( ! empty( $users_has_no_read ) ) : ?>
                <li class="li_is_col">
                    <label for="pasvu">Pas vu</label>
                    <ul class="vu__content">
    					<?php foreach ( $users_has_no_read as $user_n_read ) : ?>
                            <li><?php echo $user_n_read->first_name . ' ' . $user_n_read->last_name; ?></li>
    					<?php endforeach; ?>
                    </ul>
                </li>
    		<?php endif; ?>
            <li class="clear"></li>
        </ul>
    </div>
</div>