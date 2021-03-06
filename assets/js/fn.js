
var _http_query = "";

var sday	 = "";
var smonth   = "";
var syear	 = "";

var eday	= "";
var emonth  = "";
var eyear   = "";

var tzoffset	= 0;
var gmthour     = 0;
var clockelemid	= "#displayWClock";

clockTimeout = null;


var max_filesize = ( max_filesize === undefined ) ? 2048 : max_filesize;

function rawurlencode (str) {
	if (typeof str != 'undefined' && str != null ){
		str = str.toString();  
		return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A');
	}
	
	return "";
}

function confirm_delete(returnUrl){
	if ( confirm("Esti sigur?") ) return window.location.href = returnUrl;
}

function add_httpquery_term(term, value, prefix, separator){
	value = rawurlencode(value);
	
	if((typeof term != 'undefined') && (term.length > 0) && (typeof value != 'undefined') && (value.length > 0)){
		_http_query += (_http_query.length > 0) ? (separator + term + '=' + value) : (prefix + term + '=' + value);
	}
	
	return _http_query;
}

function select_eltext(el, win) {
    win = win || window;
    var doc = win.document, sel, range;
    if (win.getSelection && doc.createRange) {
        sel = win.getSelection();
        range = doc.createRange();
        range.selectNodeContents(el);
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (doc.body.createTextRange) {
        range = doc.body.createTextRange();
        range.moveToElementText(el);
        range.select();
    }
}

function submit_http_query(formId, prefix, separator, link){
	
	var iname = null;
	var ival  = null;
	
	//---- reset the query ----- //
	_http_query = "";
	
	var has_input = false;
	
	if( typeof link == 'undefined' || link.length == 0) 			
		link = $(formId).attr('action');
	
	if( typeof prefix == 'undefined' ||  prefix.length == 0) 		
		prefix = "?";
	if( typeof separator == 'undefined' || separator.length == 0) 	
		separator = "&";
	
	//inputs
	$(formId + " input").each(function(){
		iname= $(this).attr('name');
		ival = $(this).val();
		
		add_httpquery_term(iname, ival, prefix, separator);
	});
	
	//select
	$(formId + " select").each(function(){
		iname= $(this).attr('name');
		ival = $(this).val();
		
		add_httpquery_term(iname, ival, prefix, separator);
	});
	
	//textarea
	$(formId + " textarea").each(function(){
		iname= $(this).attr('name');
		ival = $(this).text();
		
		add_httpquery_term(iname, ival, prefix, separator);
	});
	
	if(_http_query.length == 0) 
		return;
	else
		window.location.href = (link + _http_query);
}

function transactions_filter_submit(){
	
	$('#sdate').val(syear + "-"+ smonth + "-" + sday); //Y-m-d
	$('#edate').val(eyear + "-"+ emonth + "-" + eday);

	submit_http_query('#filterTransactionsForm');
	
}

function fn_popup(url) {
	
	title = "fnpopup";
	
	w = screen.width - (screen.width/3);
	h = screen.height - (screen.height/3);
	
	var left = (screen.width/2)-(w/2);
	var top = (screen.height/2)-(h/2);
	
	var specs = ('toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
	
	try{
		return window.open(url, title, specs);
	}catch(e){
		alert("Navigatorul tau a blocat ferestra.\nObserva mesajul din bara de sus a navigatorului si permite deschiderea ferestrei.");
	}
		
} 


function fn_submit_apopup(formId, url){
	
	var tform = document.getElementById(formId);
	
	$('#' + formId).attr('action', url);
	
	if(tform){
		tform.onsubmit = function() {
			fn_popup('about:blank');  this.target = 'fnpopup';
		};
	}
}

function fn_form_defaults(formId){
	
	tform = document.getElementById(formId);
	
	tform.onsubmit = function(){ return true; };
	
	$('#' + formId).removeAttr('action');
	$('#' + formId).attr('target', "_self");
	$('#' + formId).unbind('onsubmit');
}

function fn_cconvert(ajaxURL, inputResult){
	var ccfromID = $('#cc_from_id').val();
	var cctoID 	 = $('#cc_to_id').val();
	var ccsum	 = parseFloat($('#cc_sum').val());
	
	if (ccsum > 0){
		$.post(ajaxURL, {from: ccfromID, to: cctoID, sum: ccsum}, function(data){
			if(typeof data != 'undefined'){
				$(inputResult).val(data);
				$(inputResult).focus();
			}
		});
	}
	else alert("Specifica suma.");
}

function fn_round_nr(number, decimals, decimal_sep){
    number = new String(number); if( decimal_sep === undefined ) decimal_sep = '.';

    //do not round the number if it is already rounded to the number of decimals
    if( number.indexOf(decimal_sep) > 0 ) { decimalscount = number.split(decimal_sep); decimalscount = decimalscount[1].length; if(decimalscount == decimals) return number; }

    var x = Math.pow(10, decimals); number = parseFloat(number); var rounded = Math.round(number*x)/x; var maxZeros = decimals;
    rounded = new String(rounded); if(rounded.indexOf(decimal_sep) < 0 ) rounded+=decimal_sep; var nrdecimals = rounded.split("."); nrdecimals = nrdecimals[1].length; while( nrdecimals < maxZeros ){
        rounded = new String(rounded); rounded+= "0"; nrdecimals++;
    }

    return rounded;
}

function fn_prepend_zeros(inputnr){
	inputnr = parseInt(inputnr);
	
	if(inputnr < 10) return "0" + inputnr;
	
	return inputnr;
}

function fn_calculate_tz(hour, offset){
	
	hour+= offset;
	
	if(hour >= 24) hour = hour - 24;
	if(hour < 0) hour = 24 + hour;

	return fn_prepend_zeros(hour);
}

function fn_clock(){

	var today	=	new Date();
	
	var h = today.getHours();
	var m = today.getMinutes();
	var s = today.getSeconds();
	
	h = fn_calculate_tz(h, tzoffset);
	
    // add a zero in front of numbers < 10
	m = fn_prepend_zeros(m);
	s = fn_prepend_zeros(s);
	
	//$(clockelemid).text(h+":"+m+":"+s);
	$(clockelemid).text(h+":"+m);
}

function fn_clock_set_tzoffset(offset){
	tzoffset = parseInt(offset);
}

function fn_check_upload_file(fld) {

    var fvalue = $(fld).val();
    var parent = $(fld).parent().get(0);

    $(parent).find('.file-alert-js').remove();

    var unsafeWarn = $('.file-alert-web-unsafe').clone();
    var sizeWarn   = $('.file-alert-upload-size-exceeded').clone();

    if(!/(\.pdf|\.gif|\.jpg|\.jpeg|\.mp3|\.png|\.mp4|\.txt)$/i.test(fvalue)) {
        $(unsafeWarn).removeClass('hidden'); $(fld).after(unsafeWarn); return false;
    }

    if( (fld.files !== undefined) && fld.files.length && (fld.files[0].size >= max_filesize ) ) {
        $(sizeWarn).removeClass('hidden'); $(fld).after(sizeWarn); return false;
    }

    $(parent).find('.file-alert-js').remove();

    return true;
}

function fn_resize_to_height(element, paddingBottom){

    var winHeight = $(window).height();
    var offsetTop  = $(element).offset().top;

    var elemHeight = $(element).height();

    paddingBottom = (paddingBottom === undefined) ? 0 : parseInt(paddingBottom);

    var maxHeight = ( winHeight - offsetTop - paddingBottom);

    $(element).css({height: maxHeight + 'px'});

    return maxHeight;
}

function fn_check_highlight(element, target){
    if( $(element).is(':checked') )
        $(target).addClass('selected');
    else
        $(target).removeClass('selected');
}

$(document).ready(function(){
	
	//--- set default transactions filters ---//
	sday 	= parseInt($('#sday').val());
	smonth 	= parseInt($('#smonth').val());
	syear 	= parseInt($('#syear').val());
	
	eday   = parseInt($('#eday').val());
	emonth = parseInt($('#emonth').val());
	eyear  = parseInt($('#eyear').val());
	
	$('#sday').change(function(){ sday = $(this).val(); });
	$('#smonth').change(function(){ smonth = $(this).val(); });
	$('#syear').change(function(){ syear = $(this).val(); });
	
	$('#eday').change(function(){ eday = $(this).val(); });
	$('#emonth').change(function(){ emonth = $(this).val(); });
	$('#eyear').change(function(){ eyear = $(this).val(); });
	
	$('#filterTransactionsBtn').click(function(){
        transactions_filter_submit();
    });
	
	$('#clock_tz').change(function(){ fn_clock_set_tzoffset($(this).val()); });

    //--- find the GMT hour ---//
    //set_gmt_hour(); //We don't need to know the gmt hour for now
    //--- find the GMT hour ---//

	clockTimeout = setInterval("fn_clock()", 999);

    //--- mup port selection ---//
    $('#port_select').change(function(){
        var portNum = parseInt( $(this).val() ); if( ( portNum > 0 ) && !isNaN( portNum ) ) $('#mup_port').val(portNum); else $('#mup_port').val("");
    });
    //--- mup port selection ---//

    //--- cronjob line auto-select on click ---//
    $('code').click(function(){ select_eltext(this); });
    //--- cronjob line auto-select on click ---//

    //--- autofill currency info ---//
    $('#addCurrencyForm #ccode').change(function(){

        var rate   = $(this).find('option:selected').data('rate');
        var symbol = $(this).find('option:selected').data('symbol');
        var cname  = $(this).find('option:selected').data('cname');

        if( symbol.length > 0 )$('#csymbol').val(symbol);
        if( cname.length > 0 )$('#cname').val(cname);
        if( rate > 0 ) $('#cexchange').val(rate);

    });

    $('#ccode').trigger('change');
    //--- autofill currency info ---//

    //--- toggle transaction repeats input ---//
    $('#add_pending').click(function(){
        if( $(this).is(':checked') ) $('.recurring-choices').slideDown('fast'); else $('.recurring-choices').slideUp('fast');
    });
    //--- toggle transaction repeats input ---//


    //--- resize file embed area to current window height ----//
    if( $('#page-file-preview .file-embed').length > 0 ){

        $(window).resize(function(){
            if( appliedH = fn_resize_to_height('.file-embed iframe, .file-embed video, .file-embed audio, .file-embed img', 20) )
                $('.embed-responsive').css('padding-bottom', appliedH + 'px');
        });

        $(window).resize();
    }
    //--- resize file embed area to current window height ----//

});