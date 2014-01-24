<?php
get_header();
?><div class="layout column left_column page_nav"><?php
get_template_part('parts/nav', 'page');
?></div><div class="layout right_column double_column page_content"><?php
        get_template_part('parts/loop', 'page');
        ?></div><div style="clear:both;"></div><?php
get_footer();
?>