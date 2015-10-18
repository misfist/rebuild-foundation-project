/*
 * Filtering ajax caching
 *
 */
jQuery(document).ready(function() {
	jQuery.post( ajaxurl, {
		plugin_id: 'cpac',
		action: 'cac_update_filtering_cache',
		storage_model: CAC_FC_Storage_Model
	});
});
