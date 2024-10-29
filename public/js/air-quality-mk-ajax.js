
(function($) {
	$(document).ready(function() {
		// when user clicks the link
		$('select.air-quality-mk-station').change( 'click', function(event) {
			
			// prevent default
			event.preventDefault();
			$('.air-quality-mk-station-response').html(air_quality_mk_ajax.loading_text);
			var station_id = $(this).val();
			
			// submit the data
			$.post(air_quality_mk_ajax.ajaxurl, {
				nonce:     air_quality_mk_ajax.nonce,
				action:    'public_hook',
				station_id: station_id
			}, function(data) {
				console.log(data);
                $('.air-quality-mk-station-response').html(data);
			});
		});
	});
})( jQuery );
