(function ($) {
	"use strict";
	$(function () {

    $('#rp_funding_programs').delegate('input.add', 'click', function (e) {
      var new_input = '<input name="rp_funding_programs[]" value="" placeholder="Add funding program" class="widefat" type="text">';
      $(e.delegateTarget).find('.list').append(new_input);
    });

	});
}(jQuery));
