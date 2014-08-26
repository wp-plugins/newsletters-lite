var request_getlistfields = false;
var request_subscribe = false;

jQuery(document).ready(function(){
	jQuery("input[id*=checkboxall]").click(function() {
		var checked_status = this.checked;
		jQuery("input[id*=checklist]").each(function() {
			this.checked = checked_status;
		});
	});
	
	jQuery("input[id*=checkinvert]").click(function() {	
		jQuery("input[id*=checklist]").each(function() {
			var status = this.checked;
			
			if (status == true) {
				this.checked = false;
			} else {
				this.checked = true;
			}
		});
	});
});

function hsl2rgb(hsl) {
    var h = hsl[0], s = hsl[1], l = hsl[2];
    var m1, m2, hue;
    var r, g, b
    h = (Math.round( 360*h )/1);
    if (s == 0)
        r = g = b = (l * 255);
    else {
        if (l <= 0.5)
            m2 = l * (s + 1);
        else
            m2 = l + s - l * s;
        m1 = l * 2 - m2;
        hue = h / 360;
        r = Math.round(HueToRgb(m1, m2, hue + 1/3));
        g = Math.round(HueToRgb(m1, m2, hue));
        b = Math.round(HueToRgb(m1, m2, hue - 1/3));
    }
    return {r: r, g: g, b: b};
}

function HueToRgb(m1, m2, hue) {
    var v;
    if (hue < 0)
        hue += 1;
    else if (hue > 1)
        hue -= 1;

    if (6 * hue < 1)
        v = m1 + (m2 - m1) * hue * 6;
    else if (2 * hue < 1)
        v = m2;
    else if (3 * hue < 2)
        v = m1 + (m2 - m1) * (2/3 - hue) * 6;
    else
        v = m1;

    return 255 * v;
}

function wpml_submitserial(form) {
	jQuery('#wpml_submitserial_loading').show();
	var formdata = jQuery(form).serialize();

	jQuery.post(wpmlajaxurl + '?action=wpmlserialkey', formdata, function(response) {
		jQuery('#wpmlsubmitserial').html(response);
		jQuery.colorbox.resize();
	});
}

function wpml_deleteserial() {
	jQuery('#wpml_submitserial_loading').show();

	jQuery.post(wpmlajaxurl + '?action=wpmlserialkey&delete=1', false, function(response) {
		jQuery.colorbox.close(); parent.location.reload(1);
	});
}

function jqCheckAll(checker, formid, name) {					
	jQuery('input:checkbox[name="' + name + '[]"]').each(function() {
		jQuery(this).attr("checked", checker.checked);
	});
}

function wpml_scroll(selector) {
	var targetOffset = jQuery(selector).offset().top;
    jQuery('html,body').animate({scrollTop: targetOffset}, 500);
}

function newsletters_refreshfields(widgetid) {
	if (request_getlistfields) { request_getlistfields.abort(); }
	jQuery('#' + widgetid + '-loading').show();
	jQuery('#' + widgetid + '-button').button('option', 'disabled', true);
	jQuery('#' + widgetid + ' .wpmlfieldholder :input').attr('readonly', true);
	var formvalues = jQuery('#' + widgetid + '-form').serialize();
	
	request_getlistfields = jQuery.post(wpmlajaxurl + "action=wpmlgetlistfields&widget_id=" + widgetid, formvalues, function(response) {
		jQuery('#' + widgetid + '-loading').hide();
		jQuery('#' + widgetid + '-button').button('option', 'disabled', false);
		jQuery('#' + widgetid + '-fields').html(response);
	});
}

function wpml_titletoslug(title) {
	var title = title.toLowerCase();
	var slug = title.replace(/[^0-9a-z]+/g, "");
	jQuery('#Field_slug').val(slug);	
}

function wpml_tinymcetag(tag) {
	if (tinyMCE.activeEditor) {
		if (window.tinyMCE && tag != "") {
			window.tinyMCE.execCommand('mceInsertContent', false, tag);	
		}
	} else {
		jQuery('textarea#content').text(jQuery('textarea#content').val() + '\n\n' + tag);
	}
	
	wpml_scroll('#wp-content-editor-container');
}