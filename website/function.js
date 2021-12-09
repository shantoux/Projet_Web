	//Fonction remove required
	function removeRequired(form){
		$.each(form, function(key, value) {
			if ( value.hasAttribute("required")){
				value.removeAttribute("required");
			}
		});
	}