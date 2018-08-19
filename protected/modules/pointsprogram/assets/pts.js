var pts_table;
jQuery.fn.found = function(){return this.length>0;}

$(document).ready(function(){
    $("ul#simpletabs li").click(function(e){
        if (!$(this).hasClass("active")) {
            var tabNum = $(this).index();
            var nthChild = tabNum+1;
            $("ul#simpletabs li.active").removeClass("active");
            $(this).addClass("active");
            $("ul#tab li.active").removeClass("active");
            $("ul#tab li:nth-child("+nthChild+")").addClass("active");
        }
    });
}); /*end docu*/


$(document).ready(function(){
	
	if ( $("#pts-income-tbl").found() ){		
		pts_table = $('#pts-income-tbl').dataTable({
			   "aaSorting": [[ 0, "desc" ]],
		       "bProcessing": true, 
		       "bServerSide": false,	    
		       "bFilter":false,
		       "bLengthChange":false,
		       "sAjaxSource": pts_ajaxurl+"/incomepoints",
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
		       "fnInitComplete": function (oSettings, json) { 	              
	           }
	    });		
	}
	
	if ( $("#pts-expenses-tbl").found() ){		
		pts_table = $('#pts-expenses-tbl').dataTable({
			   "aaSorting": [[ 0, "desc" ]],
		       "bProcessing": true, 
		       "bServerSide": false,	    
		       "bFilter":false,
		       "bLengthChange":false,
		       "sAjaxSource": pts_ajaxurl+"/expensespoints",
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
		       "fnInitComplete": function (oSettings, json) { 	              
	           }
	    });		
	}
	
	if ( $("#pts-expired-tbl").found() ){		
		pts_table = $('#pts-expired-tbl').dataTable({
		       "bProcessing": true, 
		       "bServerSide": false,	    
		       "bFilter":false,
		       "bLengthChange":false,
		       "sAjaxSource": pts_ajaxurl+"/expiredpoints",
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
		       "fnInitComplete": function (oSettings, json) { 	              
	           }
	    });		
	}
	
	if ( $("#pts-merchant-tbl").found() ){		
		pts_table = $('#pts-merchant-tbl').dataTable({
		       "bProcessing": true, 
		       "bServerSide": false,	    
		       "bFilter":false,
		       "bLengthChange":false,
		       "sAjaxSource": pts_ajaxurl+"/pointsbymerchant",
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
		       "fnInitComplete": function (oSettings, json) { 	              
	           }
	    });		
	}
	
	$( document ).on( "click", ".apply_redeem_pts", function() {
			
		if ( $("#redeem_points").val()=="" ){			
			uk_msg(pts_lang.please_enter_points);
			$("#redeem_points").focus();
		} else {			
			applyRedeemPoints()
		}
	});
	
	/*show redeem points*/
	if ( $("#pts_redeem_flag").found() ){
		if ( $("#pts_redeem_flag").val()>0 ){
			$(".pts_redeem_wrap").css({
				"display":"table"
			});
		}
	}
	
	$( document ).on( "click", ".pts_cancel_redeem", function() {
		busy(true);
		$.ajax({    
	    type: "POST",
	    url: pts_ajaxurl+"/removeRedeemPoints",
	    data: '' ,
	    dataType: 'json',       
	    success: function(data){
	    	busy(false);
	    	$(".pts_redeem_wrap").hide();
	    	$("#redeem_points").val('');
	    	load_item_cart();
	    	
	    	$("#redeem_points").show();
	    	$(".apply_redeem_pts").show();
	    	
	    }, 
	    error: function(){	        	    	
	       busy(false);
	    }		
	    });   	     	 
	});
	
}); /*end docu*/

function applyRedeemPoints()
{
	var label=$(".apply_redeem_pts").html();	
	$(".apply_redeem_pts").css({ 'pointer-events' : 'none' });
	$(".apply_redeem_pts").html('<i class="fa fa-refresh fa-spin"></i>');

	var params='redeem_points=' + $("#redeem_points").val();
	params+="&subtotal_order="+ $("#subtotal_order2").val();
	params+="&merchant_id="+ $("#merchant_id").val();
	
    $.ajax({    
    type: "POST",
    url: pts_ajaxurl+"/applyRedeemPoints",
    data: params ,
    dataType: 'json',       
    success: function(data){
    	$(".apply_redeem_pts").css({ 'pointer-events' : 'auto' });
    	$(".apply_redeem_pts").html(label);    	  	
    	if (data.code==1){
    		$(".pts_points").html( data.details.pts_points );
    		$(".pts_amount").html( data.details.pts_amount );
    		
    		$(".pts_redeem_wrap").css({
				"display":"table"
			});
    		
			$("#redeem_points").hide();
			$(".apply_redeem_pts").hide();
			
    		load_item_cart();
    	} else {    		
    		//$(".pts_redeem_wrap").hide();
    		uk_msg(data.msg)
    	}
    }, 
    error: function(){	        	    	
    	$(".apply_redeem_pts").css({ 'pointer-events' : 'auto' });
    	$(".apply_redeem_pts").html(label);    	
    }		
    });   	     	  
}
