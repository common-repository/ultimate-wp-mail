(function() {
    tinymce.PluginManager.add('UWPM_Tags', function( editor, url ) {
        
        if ( typeof ewd_uwpm_php_data !== 'undefined' ) {
        	var custom_element_sections = ewd_uwpm_get_custom_element_sections();
        	var Menu_Array = 
        		[{
        	    	text: 'User Tags',
        	    	value: 'user-tags',
        	    	onclick: function() {
					    var win = editor.windowManager.open( {
					        title: 'Insert User Tag',
					        body: [{
        	    				type: 'listbox',
        	    				name: 'selected_tag',
        	    				label: 'Field Name:',
					            'values': ewd_uwpm_create_user_tag_list()
					        }],
					        onsubmit: function( e ) {
					        	if (e.data.selected_tag != '') {editor.insertContent( '['+e.data.selected_tag+']');}
					        }
					    });
					}
				},
				{
        	    	text: 'Post Tags',
        	    	value: 'post-tags',
        	    	onclick: function() {
					    var win = editor.windowManager.open( {
					        title: 'Insert Post Tag',
					        body: [{
        	    				type: 'listbox',
        	    				name: 'selected_tag',
        	    				label: 'Field Name:',
					            'values': ewd_uwpm_create_post_tag_list()
					        }],
					        onsubmit: function( e ) {
					        	if (e.data.selected_tag != '') {editor.insertContent( '['+e.data.selected_tag+']');}
					        }
					    });
					}
				},
				{
        	    	text: 'Custom Elements',
        	    	value: 'custom-elements',
        	    	onclick: function() {
					    var win = editor.windowManager.open( {
					        title: 'Insert Custom Element',
					        body: [{
        	    				type: 'listbox',
        	    				name: 'selected_tag',
        	    				label: 'Tag Name:',
					            'values': ewd_uwpm_create_custom_element_list('uncategorized')
					        }],
					        onsubmit: function( e ) { 
					            jQuery(ewd_uwpm_php_data.uwpm_custom_elements).each(function(index, el) {
					            	if (false) {}
					            	else if (e.data.selected_tag != '' && e.data.selected_tag != -1) {editor.insertContent( '['+e.data.selected_tag+']');}
					            });
					        }
					    });
					}
				}]; 
			jQuery(custom_element_sections).each(function(index, el) {
				var Menu_Element = 
					{
        	   			text: el.text,
        	   			value: el.value,
        	   			onclick: function() {
						    var win = editor.windowManager.open( {
						        title: 'Insert ' + el.text,
						        body: [{
        	   						type: 'listbox',
        	   						name: 'selected_tag',
        	   						label: 'Tag Name:',
						            'values': ewd_uwpm_create_custom_element_list(el.value)
						        }],
						        onsubmit: function( e ) {
					            jQuery(ewd_uwpm_php_data.uwpm_custom_elements).each(function(index, el) {
					            	if (el.slug == e.data.selected_tag) {
					            		var selected_tag = e.data.selected_tag;
					            		if (el.attributes.length !== 0) {
					            			var attributes = [];
					            			jQuery(el.attributes).each(function(index, el) {
					            				var attribute_values = EWD_UWPM_Parse_If_JSON(el.attribute_values);
					            				attributes.push({
					            					type: el.attribute_type,
					            					name: el.attribute_name,
					            					label: el.attribute_label,
					            					'values': attribute_values
					            				});
					            			});
					            			var win = editor.windowManager.open( {
					    					    title: 'Additional Attributes for ' + el.name,
					    					    body: attributes,
					    					    onsubmit: function( e ) {
					    					    	var attribute_string = '';
					    					    	jQuery(e.data).each(function(index, attribute) {
					    					    		jQuery.each(attribute, function(key, value) {
					    					    			attribute_string += key + "='" + value + "' ";
					    					    		});
					    					    	});
					    					    	if (selected_tag != '') {
					    					    		editor.insertContent( '['+selected_tag+' '+attribute_string+']');
					    					    	}
					    					    }
					    					});
					            		}
					            		else if (e.data.selected_tag != '' && e.data.selected_tag != -1) {editor.insertContent( '['+e.data.selected_tag+']');}
					            	}
					            });
					        }
						    });
						}
					}
				Menu_Array.push(Menu_Element);
			});

        	editor.addButton( 'UWPM_Tags', {
        	    title: 'Email Variables',
        	    text: 'Email Variables',
        	    type: 'menubutton',
        	    icon: 'wp_code',
        	    menu: Menu_Array,
        	});
        }
    });
})();


function ewd_uwpm_create_user_tag_list() {
	var result = [];

	jQuery( ewd_uwpm_php_data.uwpm_user_tags ).each( function( index, el ) {
		var d = {};
		d['text'] = el.name;
		d['value'] = el.slug;
		result.push(d);
	});

    return result;
}

function ewd_uwpm_create_post_tag_list() {
	var result = [];

	jQuery( ewd_uwpm_php_data.uwpm_post_tags ).each( function( index, el ) {
		var d = {};
		d['text'] = el.name;
		d['value'] = el.slug;
		result.push(d);
	});

    return result;
}

function ewd_uwpm_create_custom_element_list(section) {
	var result = [];

	jQuery( ewd_uwpm_php_data.uwpm_custom_elements ).each( function( index, el ) {
		
		if ( el.section == section ) {
			var d = {};
			d['text'] = el.label;
			d['value'] = el.slug;
			result.push(d);
		}
	});

    return result;
}

function ewd_uwpm_get_custom_element_sections() {
	var result = [];

	jQuery( ewd_uwpm_php_data.uwpm_custom_element_sections ).each( function( index, el ) {
		var d = {};
		d['text'] = el.name;
		d['value'] = el.slug;
		result.push(d);
	});

    return result;
}

function EWD_UWPM_Parse_If_JSON( attribute_values ) {
	var result = '';

	try {result = JSON.parse(attribute_values);}
	catch (e) {}

    return result;
}
