<?php
$nvsr_donate_button_src = \nvsr\DonateButton::get_donate_button_src();
?>
<form class="nvsr_donate_button" method="post"
      action="https://www.paypal.com/cgi-bin/webscr">
    <input type="hidden" value="_s-xclick" name="cmd">
    <input type="hidden" value="3018416" name="hosted_button_id">
    <input class="donate_button" 
           type="image" 
           title="Donate now to our charity and help a really great organization enhance our clients' lives." 
           name="submit" 
           src="<?php echo $nvsr_donate_button_src ?>">
</form>