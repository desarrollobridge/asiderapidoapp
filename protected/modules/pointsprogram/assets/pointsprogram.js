jQuery.fn.exists = function(){return this.length>0;}
jQuery.fn.found = function(){return this.length>0;}

var data_table;

function dump(data)
{
	console.debug(data);
}

function busy(e)
{
    if (e) {
        $('body').css('cursor', 'wait');	
    } else $('body').css('cursor', 'auto');        
    
    if (e) {
    	$("body").before("<div class=\"preloader\"></div>");
    } else $(".preloader").remove();
    
}

jQuery(document).ready(function() {	
	dump('init');
	
	$( document ).on( "keyup", ".numeric_only", function() {
      this.value = this.value.replace(/[^0-9\.]/g,'');
    });	 
    
    if ( !$(".row-points").exists() ){
        $(".assign-value-wrap").append( tplPoints() );    
    }
        
    $( document ).on( "click", ".add_new_row", function() {
    	$(".assign-value-wrap").append( tplPoints() ); 
    });
    
    $( document ).on( "click", ".remove_row", function() {    
    	var parent=$(this).parent().parent();
    	var count = $(".row-points").length;
    	dump(count);
    	if (count>1){
    	    parent.remove();
    	}
    });
    
    if ( $(".chosen").found() ){
      $(".chosen").chosen({
       	  allow_single_deselect:true       	  
      });     
    } 
    
    if ( $("#table_list").exists() ) {
       initTable();
    } 
   
}); /*end doc*/

function tplPoints()
{
	var html='';
	html+='<div class="row padtop10 row-points">';
      html+='<div class="col-md-3"><input type="text" name="points[]" class="numeric_only points form-control"></div>';
      html+='<div class="col-md-3"><input type="text" name="amount_value[]" class="numeric_only amount_value form-control"></div>';
      html+='<div class="col-md-1"><a class="remove_row" href="javascript:;"><i class="fa fa-minus-circle"></i></a></div>';
    html+='</div>';
    return html;
}

function nAlert(msg,alert_type)
{
	// type = warning or success
	var n = noty({
		 text: msg,
		 type        : alert_type ,		 
		 theme       : 'relax',
		 layout      : 'topCenter',		 
		 timeout:2000,
		 animation: {
	        open: 'animated fadeInDown', // Animate.css class names
	        close: 'animated fadeOut', // Animate.css class names	        
	    }
	});
}

function initTable()
{		
	var params=$("#frm_table").serialize();	
	
	data_table = $('#table_list').dataTable({		
		   "iDisplayLength": 20,
	       "bProcessing": true, 	       
	       "bServerSide": true,	       
	       "sAjaxSource": ajaxurl+"/"+ $("#action").val()+"/?currentController=admin&"+params,	       
	       "aaSorting": [[ 0, "DESC" ]],	       
           "sPaginationType": "full_numbers",                       
           "bLengthChange": false,
	       "oLanguage":{	       	 
	       	  "sInfo": js_lang.trans_13,
		       	 "sEmptyTable": js_lang.tablet_1,
		       	 "sInfoEmpty":  js_lang.tablet_3,
		       	 "sProcessing": "<p>"+js_lang.tablet_7+" <i class=\"fa fa-spinner fa-spin\"></i></p>",
		       	 "oPaginate": {
				        "sFirst":    js_lang.tablet_10,
				        "sLast":     js_lang.tablet_11,
				        "sNext":     js_lang.tablet_12,
				        "sPrevious": js_lang.tablet_13
				  }
	       },	       
	       "fnInitComplete": function(oSettings, json) {	       	  		      
		   }		
	});
	   		
}