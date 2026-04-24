var wbte_gc_ds = {
	getAsset:function( args ) {
		let params = wbte_gc_ds_js_params;
		let type = args.hasOwnProperty('type') ? args['type'] : '';
		let name = args.hasOwnProperty('name') ? args['name'] : '';

		if ('icon' === type ) {
			return params.icon_base_url + name + '.svg';
		} else if('image' === type) {
			return params.img_base_url + name;
		}
		return '';
	},
	getIconSvg: async function( icon ) {
		return await fetch( wbte_gc_ds_js_params.icon_base_url + icon + '.svg')
		.then(response => response.text())
		.then(svgData => {
			return this.sanitizeSVG(svgData);
		});
	},
	sanitizeSVG:function( svgData ) {
			
		/* Parse the SVG string into an XML Document. */
		const parser = new DOMParser();
		const svgDoc = parser.parseFromString( svgData, 'image/svg+xml' );
	
		/* Start sanitizing from the root <svg> element. */
		const svgRoot = svgDoc.documentElement;
		this.sanitizeNode( svgRoot );
	
		/* Return sanitized SVG as a string */
		const serializer = new XMLSerializer();
		return serializer.serializeToString( svgDoc );
	},
	sanitizeNode:function( node ) {
		let allowedTags= ['svg', 'g', 'path', 'rect', 'circle', 'line', 'polyline', 'polygon', 'text', 'use', 'defs', 'symbol', 'title'];
		let allowedAttrs= ['x', 'y', 'viewBox', 'fill', 'stroke', 'd', 'class', 'id', 'width', 'height', 'cx', 'cy', 'r', 'rx', 'ry', 'xlink:href', 'style', 'transform'];
		
		/* Remove any tag that is not allowed. */
		if ( ! allowedTags.includes( node.tagName ) ) {
			node.remove();
			return;
		}

		/* Loop through attributes and remove those not allowed. */
		for (let i = node.attributes.length - 1; i >= 0; i--) {
			const attr = node.attributes[i].name;
			if ( ! allowedAttrs.includes( attr ) ) {
				node.removeAttribute( attr );
			}
		}

		/* Recursively sanitize child nodes. */
		Array.from(node.children).forEach(wbte_gc_ds.sanitizeNode);
	}
}



var wbte_gc_notify_msg = {
	error:function( message, auto_close ) {
		var auto_close = (auto_close!== undefined ? auto_close : true);
		var er_elm=jQuery('<div class="wbte_gc_notify_msg wbte_gc_notify_msg_error">' + this.get_icon('error-icon') + message + '</div>');				
		this.set_notify(er_elm, auto_close);
	},
	success:function( message, auto_close ) {
		var auto_close = (auto_close!== undefined ? auto_close : true);
		var suss_elm = jQuery('<div class="wbte_gc_notify_msg wbte_gc_notify_msg_success">' + this.get_icon('success-icon') + message + '</div>');				
		this.set_notify(suss_elm, auto_close);
	},
	warning:function( message, auto_close ) {
		var auto_close = (auto_close!== undefined ? auto_close : true);
		var suss_elm = jQuery('<div class="wbte_gc_notify_msg wbte_gc_notify_msg_warning">' + this.get_icon('warn-icon') + message + '</div>');				
		this.set_notify(suss_elm, auto_close);
	},
	info:function( message, auto_close ) {
		var auto_close = (auto_close!== undefined ? auto_close : true);
		var info_elm = jQuery('<div class="wbte_gc_notify_msg wbte_gc_notify_msg_info">' + this.get_icon('info-icon') + message + '</div>');				
		this.set_notify(info_elm, auto_close);
	},
	progress:function( message ) {
		var prog_elm = jQuery('<div class="wbte_gc_notify_msg wbte_gc_notify_msg_progress"><span class="spinner"></span> ' + message + '</div>');				
		this.set_notify(prog_elm, false, true);
		return prog_elm;
	},
	progress_complete:function( elm, message, auto_close ) {
		var auto_close = (auto_close!== undefined ? auto_close : true);
		elm.removeClass('wbte_gc_notify_msg_progress').addClass('wbte_gc_notify_msg_success');
		elm.html( this.get_icon('success-icon') +  message );				
		this.set_notify(elm, auto_close);
	},
	progress_error:function( elm, message, auto_close ) {
		var auto_close = (auto_close!== undefined ? auto_close : true);
		elm.removeClass('wbte_gc_notify_msg_progress').addClass('wbte_gc_notify_msg_error');
		elm.html( this.get_icon('error-icon') + message );				
		this.set_notify(elm, auto_close);
	},
    get_icon:function( type ) {
		return '<img class="wbte_gc_notify_msg_icon" src="' + wbte_gc_ds.getAsset( { 'name': type, 'type': 'icon' } ) + '" />';
	},
	set_notify:function( elm, auto_close, is_static ) {
		jQuery('body').append(elm);
		elm.stop(true, true).animate({'opacity':1, 'top':'50px'}, 1000);
		if(is_static) { return; }
		
		elm.on('click',function(){
			wbte_gc_notify_msg.fade_out(elm);
		});
		
		if(auto_close) {
			setTimeout(function(){
				wbte_gc_notify_msg.fade_out(elm);
			},5000);
		}else{
			jQuery('body').on('click',function(){
				wbte_gc_notify_msg.fade_out(elm);
			});
		}
	},
	fade_out:function(elm) {
		elm.animate({'opacity':0,'top':'100px'}, 1000, function(){
			elm.remove();
		});
	}
}