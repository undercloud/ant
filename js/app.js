$(document).ready(function(){
	$('h1,h2').each(function(i,d){
		var section = $(d).attr('id')

		$.get(
			'pages/' + section + '.html',
			function(data){
				$(d)
					.after(data)
					.next()
					.find('code[src]')
					.each(function(i,c){
						var src = $(c).attr('src')

						$.get(
							'code/' + src + '.src?' + (new Date().getTime()),
							function(data){
								$(c)
									.text(data)
									.removeAttr('src')

								Prism.highlightElement($(c).get(0))
							}
						)
					})

			}
		)
	})

	$(document).scroll(function(){
		var ln = 1700;
		var top = $(this).scrollTop();

		if (top >= ln) {
			$('#up').show();
		} else {
			$('#up').hide();
		}
	});

	$('#up').click(function(){
		$(this).hide();
		$(document).scrollTop(0);
	});
})