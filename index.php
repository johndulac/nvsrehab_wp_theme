<?php
get_header();
?><div class="layout column left_column blog_nav"><?php
get_template_part('parts/nav', 'blog');
?></div><div class="layout double_column right_column blog_content"><?php
        get_template_part('parts/loop', 'index');
        ?></div><div style="clear:both;"></div><?php
get_footer();
?>