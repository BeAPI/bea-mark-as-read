<?php

$total_view = count( $users_has_read ); ?>

<a href="">Vu par <?php echo $total_view ?> personnes</a>


<div class="vu">
    <ul class="vu__filter">
		<?php if ( ! empty( $users_has_read ) ) : ?>
            <li>
                <label for="vu">Vu</label>
                <input type="radio" name="vu" id="vu" checked>
                <ul class="vu__content">
					<?php foreach ( $users_has_read as $u_unread ) : ?>
                        <li><?php echo $u_unread->user_nicename ?></li>
					<?php endforeach;; ?>
                </ul>
            </li>
		<?php endif; ?>
		<?php if ( ! empty( $users_has_no_read ) ) : ?>
            <li>
                <label for="pasvu">Pas vu</label>
                <input type="radio" name="vu" id="pasvu">
                <ul class="vu__content">
					<?php foreach ( $users_has_no_read as $user_n_read ) : ?>
                        <li><?php echo $user_n_read->user_nicename ?></li>
					<?php endforeach;; ?>
                </ul>
            </li>
		<?php endif; ?>
    </ul>
</div>