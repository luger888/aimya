(function($) 
{
$.fn.checkRadio = function (params){
	
		var defaults=
		{
			wrapperClass: 'checkboxWrapper',
			chekedClass: 'checked'
		};
		
		var options=$.extend(defaults, params);
		var wrapperClass = options.wrapperClass;
		var chekedClass = options.chekedClass;
		return this.each(function()
		{
			var div = $('<span />');
			div.addClass(wrapperClass).css('overflow', 'hidden');
			var input = $(this);
			input.css({'opacity':'0'});
			
			if(input.is(':checked')){
				div.addClass(chekedClass);
			}//if
			
			input.change(function(){
				if ($(this).is(':radio')) {
					
					curentRadio = $(this);
					
					$('.'+ wrapperClass + ' input:radio').each(function(index, element) {
           				 if($(this).attr('name') == curentRadio.attr('name')){
							$(this).parent().removeClass(chekedClass);
						}
          			});
					
					curentRadio.parent().addClass(chekedClass);
					
				}else{
					
					if ($(this).is(':checked')) {
						$(this).parent().addClass(chekedClass);
					}else{
						$(this).parent().removeClass(chekedClass);
					}//IF
					
				}//IF
			});
			
			input.wrap(div);
			input.css({'height': input.parent().height(), 'width': input.parent().width(), 'margin': 0});
			
		});
		
}

})( jQuery );