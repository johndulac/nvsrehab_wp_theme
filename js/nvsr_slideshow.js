/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function nvsr_slideshow() {
    this.slides = jQuery('.nvsr_slide');
    if (this.slides.length > 1) {
        this.setup();
        this.start();
    }
}

nvsr_slideshow.prototype.setup = function() {
    this.prepare_slides();
    this.prepare_transition_data();
    //timer stuff
    this.timer = false;
    var that = this;
    jQuery(window).on('unload', function() {
        that.unload();
    });
};
/**
 * Uses jQuery.data to join slides as circular linked list.
 * Sets selected slide as slide matching '.first' or first slide found.
 * 
 */
nvsr_slideshow.prototype.prepare_slides = function() {
    var that = this;
    this.slides.each(function(index, elem) {
        var slide = jQuery(elem);
        var next_index = index + 1;
        if (next_index >= that.slides.length) {
            next_index = 0;
        }
        var next_slide = jQuery(that.slides.get(next_index));
        slide.data('next_slide', next_slide);
    });
    this.selected_slide = this.slides.find('.first');
    if (this.selected_slide.length > 0) {
        this.selected_slide = this.selected_slide.first();
    } else {
        this.selected_slide = this.slides.first();
    }
};
/*
 * Sets up transition delay and transition duration variables
 */
nvsr_slideshow.prototype.prepare_transition_data = function() {
    //transition delay
    var default_delay = 8;
    this.transition_delay = jQuery('.slides input.transition_delay').val();
    if (null === this.transition_delay || !jQuery.isNumeric(this.transition_delay)) {
        this.transition_delay = default_delay;
    }
    //transition duration
    var default_duration = 1;
    this.transition_duration = jQuery('.slides input.transition_duration').val();
    if (null === this.transition_duration || !jQuery.isNumeric(this.transition_duration)) {
        this.transition_duration = default_duration;
    }
};

nvsr_slideshow.prototype.start = function() {
    var that = this;
    this.timer = setTimeout(function() {
        that.do_transition();
    }, this.transition_delay * 1000);
};

nvsr_slideshow.prototype.stop = function() {
    if (false !== this.timer) {
        clearTimeout(this.timer);
    }
};

nvsr_slideshow.prototype.unload = function() {
    this.slides.stop();//kill any running animations
    this.stop();//kill timer
};

nvsr_slideshow.prototype.do_transition = function() {
    var previous_slide = this.selected_slide;
    this.selected_slide = previous_slide.data('next_slide');
    this.selected_slide.css({'z-index': '10'});
    var that = this;
    this.selected_slide.animate({'left': '0'}
    , this.transition_duration * 1000, function() {
        previous_slide.css({'left': '100%'});
        that.selected_slide.css({'z-index': '0'});
        that.start();
    });
};

jQuery(function() {
    new nvsr_slideshow();
}
);