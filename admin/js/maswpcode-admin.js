(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    $(document).ready(function () {
        $('.approve-submission').on('click', function () {
            var button = $(this);
            var submissionId = button.data('id');

            button.text('Processing...').prop('disabled', true);

            $.ajax({
                url: maswpcode.ajaxurl,  // Provided via wp_localize_script in PHP
                type: 'POST',
                data: {
                    action: 'maswpcode_approve_submission',
                    submission_id: submissionId,
                    nonce: maswpcode.nonce
                },
                success: function (response) {
                    if (response.success) {
                        button.text('Approved').addClass('disabled');
                    } else {
                        button.text('Approve').prop('disabled', false);
                        alert(response.data.message);
                    }
                },
                error: function () {
                    button.text('Approve').prop('disabled', false);
                    alert('Error processing submission.');
                }
            });
        });
    });

})(jQuery);

