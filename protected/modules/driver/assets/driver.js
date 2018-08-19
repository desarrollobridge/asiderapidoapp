jQuery.fn.exists = function(){return this.length>0;}
var data_table;
var map;
var primary_map;
var marker;
var dashboard_task_handle;
var dashboard_agent_handle;
var map_location;
var run_agent_dashboard=1;
var map_dropoff;

$(document).ready(function(){
		
    $("ul#tabs li").click(function(e){
        if (!$(this).hasClass("active")) {
            var tabNum = $(this).index();
            var nthChild = tabNum+1;
            /*$("ul#tabs li.active").removeClass("active");
            $(this).addClass("active");*/
            
            var parent=$(this).parent().parent();
            //dump(parent);
            
            parent.find("ul#tabs li.active").removeClass("active");
            $(this).addClass("active");
            
            parent.find("ul#tab li.active").removeClass("active");
            parent.find("ul#tab li:nth-child("+nthChild+")").addClass("active");
            
            /*$("ul#tab li.active").removeClass("active");
            $("ul#tab li:nth-child("+nthChild+")").addClass("active");*/
        }
    });    
    
    $( document ).on( "click", ".menu-pop", function() {
    	$(".popup_menu.nav").toggle();
    	$(".popup_menu.notification").hide();
    });
    
    $( document ).on( "click", ".menu-notification", function() {
    	$(".popup_menu.notification").toggle();
    	$(".popup_menu.nav").hide();
    });
    
    $('body').click(function(e) {
      if ($(e.target).closest('.popup_menu,.menu-pop,.menu-notification').length === 0) {
          $(".popup_menu.nav").hide();
          $(".popup_menu.notification").hide();
      }
    });       
        
    $( document ).on( "click", ".close-modal", function() {    	
    	var id=$(this).data("id");
    	$(id).modal('hide');
    });
        
    if ( $(".mobile_inputs").exists()){
      try {	
	      $(".mobile_inputs").intlTelInput({      
	        autoPlaceholder: false,		
	        defaultCountry: default_country,            
	        autoHideDialCode:true,    
	        nationalMode:false,
	        autoFormat:false,
	        utilsScript: site_url+"/assets/intel/lib/libphonenumber/build/utils.js"
	      });
	   }
	   catch(err) {
		 dump(err.message);
	   }   
    }	     
    	       
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm").serialize();
	      var action = $("#frm #action").val();
	      var button = $('#frm button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});    
	
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm_task',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm_task").serialize();
	      var action = $("#frm_task #action").val();
	      var button = $('#frm_task button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});    	
	
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm_changes_status',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm_changes_status").serialize();
	      var action = $("#frm_changes_status #action").val();
	      var button = $('#frm_changes_status button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});    	
	
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm_notification_tpl',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm_notification_tpl").serialize();
	      var action = $("#frm_notification_tpl #action").val();
	      var button = $('#frm_notification_tpl button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});    	
	
	if( $(".chosen").exists() ) {
       /*$(".chosen").chosen({
       	  allow_single_deselect:true,
       	  width: '100%'
       });     */       
       $(".chosen").chosen({
       	  allow_single_deselect:true,
       	  no_results_text: js_lang.trans_33,
          placeholder_text_single: js_lang.trans_32,
          placeholder_text_multiple: js_lang.trans_32,
          width: '100%'
       }); 	
    } 
    
    if ( $(".frm_table").exists() ){
    	initTable();
    }
		
}); /*end docu*/

function empty(data)
{
	if (typeof data === "undefined" || data==null || data=="" ) { 
		return true;
	}
	return false;
}

function dump(data)
{
	console.debug(data);
}

var ajax_request;

/*mycall*/
function callAjax(action,params,button)
{
	dump(ajax_url+"/"+action+"?"+params);
	
	ajax_request = $.ajax({
		url: ajax_url+"/"+action, 
		data: params,
		type: 'post',                  
		//async: false,
		dataType: 'json',
		timeout: 7000,		
	 beforeSend: function() {
	 	dump("before=>");
	 	dump( ajax_request );
	 	if(ajax_request != null) {
	 	   ajax_request.abort();
	 	   dump("ajax abort");
	 	   busy(false,button);	 	   
	 	} else {
	 	   busy(true,button);	 	  
	 	}
	 },
	 complete: function(data) {					
		ajax_request= (function () { return; })();
		dump( 'Completed');
		dump(ajax_request);
		busy(false,button);	
	 },
	 success: function (data) {	  
	 	dump(data);
	 	if (data.code==1){
	 		
	 		switch (action)
	 		{
	 			case "login":	 			
	 			window.location.href = home_url;
	 			break;
	 				 			
	 			case "createTeam":
	 			case "addAgent":	 			
	 			$("."+data.details).modal('hide');
	 			nAlert(data.msg,"success");
	 			tableReload();
	 			break;
	 			
	 			case "getTeam":
	 			  $("#team_name").val( data.details.team_name );
	 			  $("#location_accuracy").val( data.details.location_accuracy );
	 			  $("#status").val( data.details.status );
	 			  dump(data.details.team_member);
	 			  $("#team_member").val( data.details.team_member );	
	 			  $('#team_member').trigger("chosen:updated"); 
	 			break;
	 			
	 			case "deleteRecords":
	 			tableReload();
	 			break;
	 			
	 			case "getDriverInfo":
	 			$("#first_name").val(  data.details.first_name );
	 			$("#last_name").val(  data.details.last_name );
	 			$("#username").val(  data.details.username );
	 			$("#email").val(  data.details.email );
	 			$("#phone").val(  data.details.phone );
	 			$(".team_id_driver_new").val(  data.details.team_id );
	 			
	 			//$("input[name='team_id'][value='"+data.details.team_id+"']").prop('selected', true);
	 			
	 			$("#transport_type_id").val(  data.details.transport_type_id );
	 			$("#transport_description").val(  data.details.transport_description );
	 			$("#licence_plate").val(  data.details.licence_plate );
	 			$("#color").val(  data.details.color );
	 			$("#status").val(  data.details.status );
	 			$("#password").removeAttr("required");
	 			
	 			if ( !empty(data.details.profile_photo)){
	 				var image='<img src="'+ upload_url+"/driver/"+ data.details.profile_photo +'" />';
		   	  	    $(".profile-photo").html(image);
	 			} else {
	 				$(".profile-photo").html("<p>"+jslang.profile_photo+"</p>");
	 			}
	 			
	 			break;
	 				 			
	 			case "addTask":
	 			nAlert(data.msg,"success");	 			
	 			$(".new-task").modal('hide');

	 			if ( $("body.dashboard").exists() ){	 				
	 			} else {
	 				window.location.href = home_url;
	 				return;
	 			}

	 			loadDashboardTask(); 			
	 			break;
	 			
	 			case "getDashboardTask":
	 			/*$(".task_"+data.msg).html( data.details.html );
	 			$(".task-total-"+data.msg).html( data.details.total );*/
	 			
	 			$.each( data.details , function( key, val ) {     	 				
	 				if ( !empty(val)){	 					
	 					$(".task_"+key).html( val.html );
	 			        $(".task-total-"+key).html( val.total );
	 				} else {	 					
	 				   $(".task_"+key).html( '');
	 			       $(".task-total-"+key).html( "0" );
	 				}
	 			});
	 			
	 			
	 			dump( "coordinatesx=>" + data.msg.length);
	 			plotMainMap( data.msg );
	 			
	 			break;
	 			
	 			case "changeStatus":
	 			case "assignTask":
	 			nAlert(data.msg,"success");	 			
	 			$("."+data.details).modal('hide');
	 			
	 			loadDashboardTask();
	 			break;
	 				 			
	 			case "getTaskDetails":
	 			
	 			//alert(data.details.user_type);
	 			
	 			if ( data.details.status_raw=="successful"){
	 				$(".action-1").hide();
	 				$(".action-2").show();
	 			} else {
	 				$(".action-1").show();
	 				$(".action-2").hide();
	 			}
	 			
	 			$(".task_status").html( "<span class=\"rounded tag "+ data.details.status_raw+"\" >"+data.details.status+"</spa>"  );
	 			$(".delivery_date").html( data.details.delivery_date);
	 			$(".customer_name").html( data.details.customer_name );
	 			$(".contact_number").html( data.details.contact_number);
	 			$(".email_address").html( data.details.email_address);
	 			$(".delivery_address").html( data.details.delivery_address);
	 			$(".team_name").html( data.details.team_name);
	 			$(".driver_name").html( data.details.driver_name);
	 			$(".task_description").html( data.details.task_description);
	 			$(".transaction_type").html(data.details.trans_type );
	 			$(".driver_phone").html( data.details.driver_phone );
	 			
	 			$(".assign-agent").attr("data-id", data.details.task_id );
	 			$(".edit-task").attr("data-id", data.details.task_id );
	 			$(".change-status").attr("data-id", data.details.task_id );	 			
	 			$(".delete-task").attr("data-id", data.details.task_id );
	 			
	 			if (data.details.history_data.length>0){
	 				history_html = tplTaskHistory( data.details.history_data );
	 				$("#task-history").html( history_html );	 				
	 			} else {
	 				dump('no history');
	 				$("#task-history").html("<p class=\"alert alert-danger\">"+jslang.no_history+"</p>");
	 			}
	 			
	 			if ( data.details.driver_id>0){
	 				dump('has driver assign');
	 				$(".re_assign_agent").html( jslang.re_assign_agent );
	 				$(".assign-task-to-all-wrap").hide();
	 			} else {
	 				$(".re_assign_agent").html( jslang.assign_agent );
	 				$(".assign-task-to-all-wrap").show();
	 			}
	 			
	 			/*show and hide order no*/
	 			if ( data.details.order_id>0){
	 				$("#order-id-wrap").show();
	 				$(".order-id").html( data.details.order_id );
	 				$(".merchant_name").html( data.details.merchant_name );
	 			} else {
	 				$("#order-id-wrap").hide();
	 			}
	 			
	 			/*show order details*/
	 			if (!empty(data.details.order_details)){
	 			   $("#order-details").html( data.details.order_details);
	 			} else {
	 				$("#order-details").html("<p class=\"alert alert-danger\">"+jslang.not_available+"</p>");
	 			}
	 			
	 			// show direction button
	 			if ( !empty(data.details.driver_lat)){
	 				$(".show-direction").show();	 			    
	 				$("#data-driver_lat").val( data.details.driver_lat );
	 				$("#data-driver_lng").val( data.details.driver_lng );
	 				
	 				$("#data-task_lat").val( data.details.task_lat );
	 				$("#data-task_lng").val( data.details.task_lng );
	 			} else {
	 				$(".show-direction").hide();
	 			}
	 				 			
	 			if ( !empty(data.details.drop_address)){
	 				$(".dropoff_details_wrap").show();
		 			if(data.details.trans_type_raw=="delivery"){
		 				$(".dropoff_details_label_1").hide();
		 				$(".dropoff_details_label_2").show();
		 			} else {
		 				$(".dropoff_details_label_1").show();
		 				$(".dropoff_details_label_2").hide();
		 			}
		 			
		 			if(!empty(data.details.dropoff_merchant_name)){
		 			   $(".details_dropoff_merchant").html( data.details.dropoff_merchant_name );
		 			}
		 			$(".details_dropoff_contact_name").html( data.details.dropoff_contact_name );
		 			$(".details_dropoff_contact_number").html( data.details.dropoff_contact_number );
		 			$(".details_drop_address").html( data.details.drop_address );
	 			} else {
	 				$(".dropoff_details_wrap").hide();
	 			}
	 			
	 			break;
	 			
	 			case "deleteTask":
	 			$(".task-details-modal").modal('hide');
	 			loadDashboardTask();
	 			break;	 			
	 			
	 			case "getTaskInfo":
	 			$("#task_description").val( data.details.task_description );
	 			
	 			//$("input[name='trans_type'][value='"+data.details.trans_type+"']").attr("checked", true);
	 			$("input[name='trans_type'][value='"+data.details.trans_type_raw+"']").prop('checked', true);
	 				 			
	 			switchTransactionType( data.details.trans_type_raw );
	 			
	 			$("#contact_number").val( data.details.contact_number );
	 			$("#email_address").val( data.details.email_address );
	 			$("#customer_name").val( data.details.customer_name );
	 			$("#delivery_date").val( data.details.delivery_date );
	 			$("#delivery_address").val( data.details.delivery_address );
	 			
	 			$("#task_lat").val( data.details.task_lat );
	 			$("#task_lng").val( data.details.task_lng );
	 			
	 			if ( data.details.team_id > 0){
	 				dump('has team id');
	 				$("#team_id").val( data.details.team_id );	 					 				
	 				swicthDriver( data.details.team_id  , data.details.driver_id);
	 			} 
	 			
	 			if ( !empty( data.details.task_lat )){
	 				dump('has lat');
	 				SetMapPlot( data.details.task_lat , data.details.task_lng );
	 			} else {
	 				dump('no lat');
	 		 	    setMarkerByAddress( $(".geocomplete").val() );
	 			}
	 			
	 			if ( !empty(data.details.dropoff_merchant) ){
	 				$("#dropoff_merchant").val( data.details.dropoff_merchant );
	 				$("#dropoff_merchant").trigger("chosen:updated");
	 			}
	 			if ( !empty(data.details.drop_address) ){
	 				$("#drop_address").val( data.details.drop_address );
	 			}
	 			if ( !empty(data.details.dropoff_contact_name) ){
	 				$("#dropoff_contact_name").val( data.details.dropoff_contact_name );
	 			}
	 			if ( !empty(data.details.dropoff_contact_number) ){
	 				$("#dropoff_contact_number").val( data.details.dropoff_contact_number );
	 			}
	 			if ( !empty(data.details.dropoff_lat) ){
	 				$("#dropoff_lat").val( data.details.dropoff_lat );
	 			} else {
	 				$("#dropoff_lat").val( '' );
	 			}
	 			if ( !empty(data.details.dropoff_lng) ){
	 				$("#dropoff_lng").val( data.details.dropoff_lng );
	 			} else {
	 				$("#dropoff_lng").val( '' );
	 			}
	 			
	 			if ( !empty(data.details.dropoff_lat) ){
	 			   setDropoffMapMarker(data.details.dropoff_lat,data.details.dropoff_lng);
	 			} else {	 			
	 				map_dropoff.removeMarkers();
	 			} 
	 			
	 			break;	 	
	 			
	 			
	 			case "getDriverDetails":
	 				 			  
	 			  $(".driver_name").html( data.details.info.name );
	 			  $(".phone").html( data.details.info.phone );
	 			  $(".email").html( data.details.info.email );
	 			  $(".team_name").html( data.details.info.team_name );
	 			  $(".transport_type_id").html( data.details.info.transport_type_id );
	 			  $(".licence_plate").html( data.details.info.licence_plate );
	 			  
	 			  $(".app_version").html( data.details.info.app_version );
	 			  $(".device_platform").html( data.details.info.device_platform );
	 			  
	 			  if ( data.details.task.length>0){
	 			  	  var html_task='';
	 			  	  $.each( data.details.task , function( tkey, tval ) {      
	 			  	  	  html_task+=formatTableRow(tval);
	 			  	  });
	 			  	  dump(html_task);
	 			  	  $(".driver-task-list tbody").html(html_task);
	 			  } else {
	 			  	  $(".driver-task-list tbody tr").remove();  
	 			  }
	 			  
	 			break;
	 				 			
	 			
	 			case "GetNotificationTPL":	 			
	 			$.each( data.details , function( key, value ) {  	 				
	 				$("#"+key).val( value );
	 			});
	 			break;

	 			case "SaveNotificationTemplate":
	 			$(".notification-pop").modal('hide');
	 			break;
	 			
	 			case "chartReports":
	 			$(".report_div").html(data.details.charts);
	 			$(".table_charts").html(data.details.table);
	 			break;
	 			
	 			case "retryAutoAssign":
	 			break;
	 			
	 			case "sendPushToDriver":
	 			$(".modal-push-form-driver").modal('hide');
	 			nAlert(data.msg,"success");
	 			break;
	 			
	 			case "sendPushBulk":
	 			$(".send-bulk-push-modal").modal('hide');
	 			nAlert(data.msg,"success");
	 			break;
	 			
	 			case "getMerchantAdddress":
	 			  $("#drop_address").val( data.details.address );	
	 			  $("#dropoff_contact_name").val( data.details.contact_name );	
	 			  $("#dropoff_contact_number").val( data.details.contact_number );	
	 			   	 			 
	 			  setDropoffMapMarker( data.details.lat , data.details.lng );	 			  
	 			break;
	 			
	 			case "assignToAllDrivers":
	 			nAlert(data.msg,"success");
	 			$(".task-details-modal").modal("hide");
	 			break;
	 			
	 			case "driverUpdateStatus":
	 			tableReload();
	 			break;
	 			
	 			case "loadAgentTrackBack":	 			   
	 			   trackBackMarker(data.details);
	 			break;
	 			 			
	 			default:
	 			nAlert(data.msg,"success");
	 			break;	 			
	 		}
	 		
	 	} else {
	 		
	 		// failed mycon
	 		switch ( action )
	 		{	 	
	 			case "getDashboardTask":	 
	 			$(".task_"+data.details).html( '' );
	 			$(".task-total-"+data.details).html( "0" );
	 			break;
	 			
	 			case "getTaskDetails":	 			
	 			//$(".task-details-modal").modal('hide');
	 			setTimeout('$(".task-details-modal").modal("hide")', 100);
	 			nAlert(data.msg,"warning");
	 			break;
	 			
	 			//silent
	 			case "loadAgentDashboard":	
	 			case "retryAutoAssign":
	 			break;
	 			
	 			case "getDriverDetails":
	 			$(".driver-details-moda").modal("hide");
	 			break;
	 			
	 			case "getMerchantAdddress":
	 			break;
	 			
	 			default :
	 			nAlert(data.msg,"warning");
	 			break;
	 		}
	 			 		
	 	}
	 },
	 error: function (request,error) {	    
	 	 	 		
	 }
    });       
}

function busy(e,button)
{
	if (e) {
        $('body').css('cursor', 'wait');	
    } else $('body').css('cursor', 'auto');        

    if (e) {    
    	dump('busy loading');
        /*NProgress.set(0.0);		
        NProgress.inc(); */
        $(".main-preloader").show();
        if (!empty(button)){           
	 	   button.css({ 'pointer-events' : 'none' });	 	   	  
	 	}	 		 	
    } else {    	
    	dump('done loading');
    	$(".main-preloader").hide();
    	//NProgress.done();    	
    	if (!empty(button)){
	 	   button.css({ 'pointer-events' : 'auto' });	 	   	  
	 	}
    }       
}

function nAlert(msg,alert_type)
{
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
	var action= $("#frm_table #action").val();	
		
	data_table = $('#table_list').dataTable({		
		   "iDisplayLength": 20,
	       "bProcessing": true, 	       
	       "bServerSide": true,	            
	       "sAjaxSource": ajax_url+"/"+ action +"/?currentController=admin&"+params,	       
	       "aaSorting": [[ 0, "DESC" ]],	       
           "sPaginationType": "full_numbers",   
           //"bFilter":false,            
           "bLengthChange": false,
	       "oLanguage":{	       	 
	       	 "sProcessing": "<p>Processing.. <i class=\"fa fa-spinner fa-spin\"></i></p>"
	       },	    
	       "oLanguage": {
	       	  "sEmptyTable":    js_lang.tablet_1,
			    "sInfo":           js_lang.tablet_2,
			    "sInfoEmpty":      js_lang.tablet_3,
			    "sInfoFiltered":   js_lang.tablet_4,
			    "sInfoPostFix":    "",
			    "sInfoThousands":  ",",
			    "sLengthMenu":     js_lang.tablet_5,
			    "sLoadingRecords": js_lang.tablet_6,
			    "sProcessing":     js_lang.tablet_7,
			    "sSearch":         js_lang.tablet_8,
			    "sZeroRecords":    js_lang.tablet_9,
			    "oPaginate": {
			        "sFirst":    js_lang.tablet_10,
			        "sLast":     js_lang.tablet_11,
			        "sNext":     js_lang.tablet_12,
			        "sPrevious": js_lang.tablet_13
			    },
			    "oAria": {
			        "sSortAscending":  js_lang.tablet_14,
			        "sSortDescending": js_lang.tablet_15
			    }
	       },   
	       "fnInitComplete": function(oSettings, json) {	       	  		      
		   }		
	});
}

function tableReload()
{	
	data_table.fnReloadAjax(); 
}

function clearFormElements(ele) {

    $(ele).find(':input').each(function() {
        switch(this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
            case 'textarea':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });

}

$(document).ready(function(){
	
	$('.create-team').on('show.bs.modal', function (e) {        
        $(".modal-title").html( jslang.create_team );	            
        if ( !empty($("#id").val())){
           $(".modal-title").html( jslang.update_team );	
           callAjax("getTeam", "id="+$("#id").val() ,  $("#frm .orange-button") ) ;
        }
    });     
    $('.create-team').on('hide.bs.modal', function (e) {
        $("#id").val('');      
        clearFormElements("#frm");
    });     	
    $( document ).on( "click", ".table-edit", function() {    	
    	var id= $(this).data("modal");
    	dump( $(this).data("id") );
    	$("#id").val(  $(this).data("id") );
    	$(id).modal('show');
    });
        
    $( document ).on( "click", ".table-delete", function() {    	
    	  dump(jslang);
    	  c=confirm(jslang.are_your_sure+"?");
    	  if(c){
    	     callAjax("deleteRecords", $(this).data("data") );
    	  }
    }); 	    
        
    $( document ).on( "click", "#transport_type_id", function() {    	
    	var selected=$(this).val();    	
    	switchTransportType( selected ); 	
    });	    
    $('.new-agent').on('hide.bs.modal', function (e) {                        
        $("#id").val('');     
        $(".profile-photo").html("<p>"+jslang.profile_photo+"</p>");
        clearFormElements("#frm");
    });     
    $('.new-agent').on('show.bs.modal', function (e) {            	
    	$("#password").attr("required",1);
    	switchTransportType( $("#transport_type_id").val()  );
        $(".modal-title").html( jslang.add_driver );	            
        if ( !empty($("#id").val())){
           $(".modal-title").html( jslang.update_driver );	
           callAjax("getDriverInfo", "id="+$("#id").val() ,  $("#frm .orange-button") ) ;
        }
    });     
        
    $( document ).on( "click", ".refresh-table", function() {    	
    	tableReload();
    });	
    
    $( document ).on( "click", ".add-new-task", function() {    
    	$(".task_id").val('');
        $(".order_id").val('');
        $(".task_lat").val('');
        $(".task_lng").val('');
        clearFormElements("#frm_task");	
        
        $(".dropoff_wrap").hide();
        $("#dropoff_lat").val('');
        $("#dropoff_lng").val('');
        $("#dropoff_merchant").val(0).trigger("chosen:updated");	
        
        $(".map2").hide();
        $(".new-task").modal('show');
    });
    
    /*task modal*/
    $('.new-task').on('show.bs.modal', function (e) {            	
    	dump('show modal');    	
    	switchTransactionType( $(".trans_type:checked").val() );
    	swicthDriver( $("#driver_id:selected").val() );    	
    	var task_id=$(".task_id").val();
    	if(!empty(task_id)){
    		dump("task_id=>"+task_id);    		
    		callAjax("getTaskInfo", "id="+task_id ) ; 
    	}
    });	
    $('.new-task').on('shown.bs.modal', function (e) {                        
        dump('modal totally loaded');     
        setDefaultMapLocation();       
    });     
    $('.new-task').on('hide.bs.modal', function (e) {                        
        dump('hide modal');        
        $(".task_id").val('');
        $(".order_id").val('');
        $(".task_lat").val('');
        $(".task_lng").val('');
        clearFormElements("#frm_task");
    });         
    
    $( document ).on( "click", ".trans_type", function() {    	
        switchTransactionType( $(".trans_type:checked").val() );
    });
        
    /*missing translation*/
    var today_date=moment().format('YYYY/MM/DD'); 
    if ( $('.datetimepicker').exists() ){
    	dump('datetimepicker exists');    	
    	dump(today_date);
	    $('.datetimepicker').datetimepicker({
	    	/*format:'Y-m-d g:i A',
	    	formatTime:'g:i A', */
	        //format:'d.m.Y H:i'      
	        formatTime:'g:i A',
	        format:'Y-m-d H:i',
	        minDate:today_date
	    });	    
    }
    
    if ( $("#calendar").exists() ){    	
	    $('#calendar').datetimepicker({	    
	    	timepicker:false,	
	        format:'d M Y',	   	        
	        //onChangeDateTime:function(dp,$input){	        		        	    
	        onSelectDate:function(dp,$input){
	        	var date_formated=dp.format("YYYY-MM-DD");
	        	dump(date_formated);
	        	$(".calendar_formated").val( date_formated );
	        	loadDashboardTask();
	        	//loadAgentDashboard();
            }      
	    });	    
    }
    
    if (!empty(calendar_language)){
    	jQuery.datetimepicker.setLocale(calendar_language);    
    }
    if (!empty(merchant_calendar_language)){
    	jQuery.datetimepicker.setLocale(merchant_calendar_language);    
    }
    
    /*geocomplete*/
    if ( $(".geocomplete").exists() ){    	    	
	   //$(".geocomplete").geocomplete().
	   $(".geocomplete")
	   .geocomplete({
	  	 country: default_country
	   })
	   .bind("geocode:result", function(event, result){	     	   	  
	      setMarkerByAddress( $(".geocomplete").val() );
	   });
    }
    
    if ( $("#search_map").exists() ){
    	$("#search_map")
	   .geocomplete({
	  	 country: default_country
	   })
	   .bind("geocode:result", function(event, result){	     	       
	       var t_lat = result.geometry.location.lat();
	       var t_lng = result.geometry.location.lng();
	       dump(t_lat); dump(t_lng);
	       if (!empty(t_lat)){
	          map.setCenter(t_lat,t_lng);
	          map.setZoom(10);
	       } else {
	       	  nAlert(jslang.undefine_result,'warning');
	       }
	   });
    }
        
    $( document ).on( "change", ".task_team_id", function() { 
    	 var team_id=$(this).val();     	 
    	 swicthDriver(team_id);
    });
    
    $( document ).on( "keyup", ".delivery_address_task", function() { 
    	 var address=$(this).val();
    	 setMarkerByAddress( $(".geocomplete").val() );
    });    
    
    if ( $(".dashboard-work-area").exists() ){    	
    	loadDashboardTask();
    	dashboard_task_handle = setInterval(function(){loadDashboardTaskSilent()}, 8000);
    }
    
    /*assign task*/    
    $( document ).on( "click", ".assign-agent", function() {    	       
        var task_id=$(this).data('id');        
        var modalclose=$(this).data("modalclose");
        if(!empty(modalclose)){
        	 $("."+modalclose).modal('hide');
        }
        $(".assign-task").modal('show');
        $(".task-id").html( task_id );
        $(".task_id").val( task_id );
    });
    $('.assign-task').on('show.bs.modal', function (e) {                        
        dump('modal totally loaded'); 
        swicthDriver(0);         
    });     
    
    var task_id_details='';
    /*task details*/    
    $( document ).on( "click", ".task-details", function() {  
    	$(".driver-details-moda").modal('hide');  	       
    	var task_id=$(this).data('id');     	
    	dump('modal show');  
    	$(".task-id").html( task_id );
        $(".task_id_details").val( task_id );
    	$(".task-details-modal").modal('show');    
    	
    	$(".map-direction").hide();
    	$("#direction_output").html('');
    	$(".task-details-tabs").show();
    });    
    $('.task-details-modal').on('show.bs.modal', function (e) {                        
        dump('modal totally loaded');  
        dump( $(".task_id_details").val() );
        callAjax("getTaskDetails", "id="+$(".task_id_details").val() ) ;        
    });     
    
    /*delete task*/
    $( document ).on( "click", ".delete-task", function() {    	       
    	var task_id= $(".task_id_details").val();
    	c=confirm(jslang.are_your_sure+"?");
    	if(c){
    	   callAjax("deleteTask", "task_id="+task_id );
    	}
    });	
    
    /*edit task*/
    $( document ).on( "click", ".edit-task", function() {   
    	var task_id= $(".task_id_details").val();
    	dump(task_id);
    	$(".task_id").val( task_id );
    	$(".task-details-modal").modal("hide");
    	$(".new-task").modal('show');
    });	
    
    /*change status*/    
    $( document ).on( "click", ".change-status", function() {   
    	var task_id= $(".task_id_details").val();
    	dump(task_id);   
    	$(".task-id").html( task_id );    	
    	$(".task_id").val( task_id );
    	$("#reason").val('');
    	$(".task-details-modal").modal("hide");
    	$(".task-change-status-modal").modal("show"); 	
    });	
    $('.task-details-modal').on('show.bs.modal', function (e) {                        
        dump('modal totally loaded');  
        dump( $(".task_id").val() );   
        $(".status").val('');     
        $(".reason_wrap").hide();
        $(".reason").val('');
    });     
    
    
    /*show reason text area*/
    $( document ).on( "change", ".status_task_change", function() {   
    	 var status=$(this).val();    	 
    	 switchReason( status );
    });
    
    /*set focus on map*/
    $( document ).on( "click", ".task-map", function() {           
        var t_lat=$(this).data("lat");
        var t_lng=$(this).data("lng");
        map.setCenter(t_lat,t_lng);
        map.setZoom(14);
        $(".task-map").removeClass("active");
        $(this).addClass("active");
        //map.setZoom(16);
    });
    
    /*load agent list*/
    if ( $(".agent-active").exists() ){    	    	
    	loadAgentDashboard();
    	dashboard_agent_handle = setInterval(function(){loadAgentDashboardSilent()}, 8500);
    }
    
    //show agent details
    $( document ).on( "click", ".view-driver-details", function() {     
    	var driver_id=$(this).data("id");    	
    	$(".driver_id_details").val( driver_id );
    	$(".driver-details-moda").modal('show');
    });	    
    $('.driver-details-moda').on('show.bs.modal', function (e) {                               
        callAjax("getDriverDetails","driver_id="+ $(".driver_id_details").val()+"&date="+$(".calendar_formated").val() );
    });     
    
    
    if ( $(".sticky").exists() ){
    	dump('sticky');
        $(".sticky").sticky({topSpacing:0});
    }
    
    $(".switch-boostrap").bootstrapSwitch({
    	size:"mini",
    	onText:jslang.on,
    	offText:jslang.off
    });
    
    $( document ).on( "click", ".notification_tpl", function() {     
    	$("#option_name").val( $(this).data("id") );
    	$(".option-name").html( $(this).data("id") );
    	$(".notification-pop").modal('show');    	
    });	
    $('.notification-pop').on('show.bs.modal', function (e) {        	  
    	callAjax("GetNotificationTPL", "option_name=" + $("#option_name").val() );
    });     
    
    if ( $("#jplayer").exists() ){
    	initJplayer();
    }
           
    $( document ).on( "change", "#team", function() {     
    	loadAgentDashboard();
    });
    
    if (empty( Cookies.get('drv_sound_on') )){
    	Cookies.set('drv_sound_on', '1', { expires: 500, path: '/' });
    } else {
    	var drv_sound_on = Cookies.get('drv_sound_on');
    	dump( "drv_sound_on->"+Cookies.get('drv_sound_on') );
    	if ( drv_sound_on==2){    		
    		$(".menu-sound i").addClass("ion-android-volume-off");
    		$(".menu-sound i").removeClass("ion-volume-high");
    	}
    }
        
    $( document ).on( "click", ".menu-sound", function() {     
    	var f=$(this).find("i");
    	if (f.hasClass("ion-android-volume-off")){
    		f.removeClass("ion-android-volume-off");
    		f.addClass("ion-volume-high");
    		dump('on');
    		Cookies.set('drv_sound_on', '1', { expires: 500, path: '/' });
    	} else {
    		f.addClass("ion-android-volume-off");
    		f.removeClass("ion-volume-high");
    		dump('off');
    		Cookies.set('drv_sound_on', '2', { expires: 500, path: '/' });
    	}
    });
    
    $( document ).on( "click", ".show-location-map", function() {  
    	var lat=$(this).data("lat");   
    	var lng=$(this).data("lng");   
    	$(".task-details-modal").modal('hide');
    	$("#map_location_lat").val( lat );
    	$("#map_location_lng").val( lng );
    	$(".show-location-map-modal").modal('show');
    });
    $('.show-location-map-modal').on('shown.bs.modal', function (e) {       	
    	var lat=$("#map_location_lat").val();
    	var lng=$("#map_location_lng").val();
    	map_location = new GMaps({
		  div: '.map-location',
		   lat: lat,
	       lng: lng,
		   zoom: 5,
		   styles: map_style 
		}); 
		 map_location.setCenter( lat , lng );
	     map_location.setZoom(10);
	     
	   var location_marker = map_location.addMarker({
         lat: lat ,
         lng: lng          
        });
    });	
             
}); /*end docu*/

function loadDashboardTask()
{
	if ( $(".dashboard-work-area").exists() ){
	    callAjax("getDashboardTask","status=unassigned&date="+ $(".calendar_formated").val() );
	}
	if ( $(".task-list-area").exists() ){
		tableReload();
	}
}

function loadDashboardTaskSilent()
{
	//dump('loadDashboardTaskSilent');
	callAjaxSilent("getDashboardTask","status=unassigned&date="+ $(".calendar_formated").val() );
}

function loadAgentDashboard()
{
	//dump('loadAgentDashboard');
	var params="date="+ $(".calendar_formated").val() + "&team_id=" + $("#team").val();
	params+="&agent_name="+ $(".search_agent").val();
	callAjax2("loadAgentDashboard", params );
}

function loadAgentDashboardSilent()
{
	//dump('loadAgentDashboardSilent');
	var params="date="+ $(".calendar_formated").val() + "&team_id=" + $("#team").val();
	params+="&agent_name="+ $(".search_agent").val();
	callAjaxSilent2("loadAgentDashboard", params );
}

function switchReason(status)
{
	dump(status);
	switch (status)
	{
		case "failed":
		case "canceled":
		case "cancelled":
		$(".reason_wrap").show();
		break;
		
		default:
		$(".reason_wrap").hide();
		break;
		
	}
}

function switchTransportType(selected)
{	
	switch (selected)
	{
		case "bike":
		$("#licence_plate").hide();
		break;
		
		case "walk":
		$("#licence_plate").hide();
		$("#transport_description").hide();
		$("#color").hide();
		$(".description").hide();
		break;
		
		default:
		$(".description").show();
		$("#licence_plate").show();
		$("#transport_description").show();
		$("#color").show();
		break;
	}
}

function switchTransactionType(transaction_type)
{
	dump(transaction_type);	
	switch (transaction_type)
	{
		case "pickup":
		$(".delivery-info").show();
		$("#delivery_date").attr("placeholder", jslang.pickup_before);
		$("#delivery_address").attr("placeholder", jslang.pickup_address);
		
		$(".dropoff_wrap").show();
		$(".map2").show();
		$(".dropoff_action_1").hide();
		$(".dropoff_action_2").show();
		
		initMapDropOff();
		break;
		
		case "delivery":
		$(".delivery-info").show();
		$("#delivery_date").attr("placeholder", jslang.delivery_before);
		$("#delivery_address").attr("placeholder", jslang.delivery_address);
		
		$(".dropoff_wrap").show();
		$(".map2").show();
		$(".dropoff_action_1").show();
		$(".dropoff_action_2").hide();
		
		initMapDropOff();
		break;
		
		default:
		$(".delivery-info").hide();
		break;
	}
}

function swicthDriver(team_id , id_selected)
{
	if ( team_id>0){    	 	
	 	$(".assign-agent-wrap").show();	 	
	 	$(".team_opion").hide();
	 	$(".option_"+team_id).show();    	 	
	 	$(".map1").css({"height":"280px"});
	 	
	 	if(!empty(id_selected)){
	 	   $("#driver_id").val( id_selected );
	 	} else {
	 	   $("#driver_id").val('');
	 	}
	 } else {
	 	$(".task_team_id").val(0);
	 	$(".driver_id").val("");
	 	$(".assign-agent-wrap").hide();
	 	$(".team_opion").hide();    	 	
	 	$(".map1").css({"height":"280px"});
	 }
}

function setDefaultMapLocation()
{
	dump('setDefaultMapLocation');		    
	primary_map = new GMaps({
	  div: '.map_task',
	  lat: default_location_lat,
	  lng: default_location_lng	,
	  zoom: 5,
	  styles: map_style 
	}); 
	
	var task_id = $(".task_id").val();	
	if ( task_id > 0 ) {
		dump('it means edit ');
		SetMapPlot( $("#task_lat").val(), $("#task_lng").val() );
		return;
	}
	
	/*GMaps.geolocate({
	  success: function(position) {
	    map.setCenter(position.coords.latitude, position.coords.longitude);
	  },
	  error: function(error) {
	    nAlert('Geolocation failed: '+error.message ,'warning');
	  },
	  not_supported: function() {
	    nAlert("Your browser does not support geolocation",'warning');
	  },
	  always: function() {	
	  }
	});*/
}

function setMarkerByAddress( address )
{
	primary_map.removeMarkers();
	
	map_marker = map_marker_delivery;
	if( $(".trans_type").exists() ){		
		if ( $(".trans_type:checked").val() =="pickup"){
			map_marker = map_pickup_icon;
		}
	}
	
	dump(map_marker);
	
	if($('.new-task-submit').is(':visible')) {
		$(".new-task-submit").css({ 'pointer-events' : 'none' });
	}
	
	dump(address);
	GMaps.geocode({
	  address: address,
	  callback: function(results, status) {
	    if (status == 'OK') {
	    	
	      if($('.new-task-submit').is(':visible')) {
		    $(".new-task-submit").css({ 'pointer-events' : 'auto' });
	      }
	    	
	      var latlng = results[0].geometry.location;
	      primary_map.setCenter(latlng.lat(), latlng.lng());
	      primary_map.setZoom(15);
	      
	      $("#task_lat").val( latlng.lat() );
	      $("#task_lng").val( latlng.lng() );
	      
	      var marker = primary_map.addMarker({
	         lat: latlng.lat(),
	         lng: latlng.lng(),
	         draggable: true,
	         icon : map_marker 
	      });
	      
	      marker.addListener('dragend',function(event) {	      	 
	         /*dump( "lat=>"+event.latLng.lat() );        
	         dump( "long=>"+event.latLng.lng() );*/
	         $("#task_lat").val( event.latLng.lat() );
	         $("#task_lng").val( event.latLng.lng() );
	         
	         if($('.new-task-submit').is(':visible')) {
	         	convertLatLongToAddress( event.latLng.lat() , event.latLng.lng() );
	         }
	         
	      });
	      
	    } else {
	    	if($('.new-task-submit').is(':visible')) {
		        $(".new-task-submit").css({ 'pointer-events' : 'auto' });
	        }	    	
	    }
	  }
	});	
}

function SetMapPlot( lat , lng )
{
	 primary_map.removeMarkers();
	
	 dump(lat); dump(lng);
	 primary_map.setCenter( lat , lng );
	 primary_map.setZoom(15);
	 
	 map_marker = map_marker_delivery;
	if( $(".trans_type").exists() ){		
		if ( $(".trans_type:checked").val() =="pickup"){
			map_marker = map_pickup_icon;
		}
	}
	 	 
     var marker = primary_map.addMarker({
         lat: lat ,
         lng: lng ,
         draggable: true,
         icon : map_marker 
      });
      
      marker.addListener('dragend',function(event) {
         dump( "lat=>"+event.latLng.lat() );        
         dump( "long=>"+event.latLng.lng() );
         $("#task_lat").val( event.latLng.lat() );
         $("#task_lng").val( event.latLng.lng() );
         
         if($('.new-task-submit').is(':visible')) {
         	convertLatLongToAddress( event.latLng.lat() , event.latLng.lng() );
         }
         
      });
}

function tplTaskHistory(data)
{
	if (data.length<=0){
		return;
	}
	var html='';
	$.each( data , function( key, val ) {     
	 	dump(val);
	 	html+='<div class="grey-box top10">';
	 	html+='<div class="row">';
	 	   html+='<div class="col-md-2">';
	       html+= '<span class="tag rounded '+val.status_raw+'">'+val.status+'</span>';	       	      
	       html+='</div>';
	       html+='<div class="col-md-6">';
	       html+= val.remarks
	       
	       if (!empty(val.reason)){
	       	  html+='<p class="small text-muted">'+jslang.reason+': '+val.reason+'</p>';
	       }
	       if (!empty(val.notes)){
	       	  html+='<p class="small text-muted">'+jslang.notes+': '+val.notes+'</p>';
	       }
	       	       
	       if (val.photo_url!=2){
	       	  if (val.photo_url!=3){
		       	  html+='<p>';
		       	  html+='<a href="'+val.photo_url+'" target="_blank">'
		       	  html+='<img class="customer-signature" src="'+val.photo_url+'">';
		       	  html+='</a>';
		       	  html+='</p>';
	       	  }
	       }
	       
	       if (val.photo_url==3){
	       	   html+='<p class="text-danger">'+val.photo_delete_msg+'</p>'; 
	       }
	       
	       if (!empty(val.customer_signature)){
	       	  html+='<p>';
	       	  html+='<a href="'+val.customer_signature_url+'" target="_blank">'
	       	  html+='<img class="customer-signature" src="'+val.customer_signature_url+'">';
	       	  html+='</a>';
	       	  html+='</p>';
	       	  
	       	  if (!empty(val.receive_by)){
	       	     html+='<p class="small text-muted">'+jslang.receive_by+': '+val.receive_by+'</p>';
	          }
	       }
	       
	       
	       html+='</div>';
	       html+='<div class="col-md-4">';
	         html+='<i class="ion-ios-clock-outline"></i> '+val.date_created+' <br/>';
	         if (!empty(val.driver_location_lat)){
	             html+='<i class="ion-ios-location"></i>';
	             html+='<a href="javascript:;" class="show-location-map" data-lat="'+val.driver_location_lat+'" data-lng="'+val.driver_location_lng+'"  >'+jslang.location_on_map+'</a>';
	         }
	       html+='</div>';
	     html+='</div> ';
	     html+='</div>';
    });	 
    return html;
}

function plotMainMap(data)
{	 	
	dump("plotMainMap");
	dump(default_location_lat);
	dump(default_location_lng);
	 map = new GMaps({ 
			div: '.primary_map',
			lat: default_location_lat ,
			lng: default_location_lng ,
			//scrollwheel: false ,
			zoom: 5,
			styles: map_style ,				
		    markerClusterer: function(map) {
                return new MarkerClusterer(map);
            }
	 }); 	 
	 plotTaskMap(data);
}

function plotTaskMap(data)
{
	
	if ( data.length >0) {
		
		 // remove all pin
		 map.removeMarkers();
  	   		 
		 var last_lat='';
    	 var last_lng='';
         var bounds = [];
    			   
		 $.each( data , function( key, val ) {  
	 	 	 
		 	 //if(val.lat>0){
		 	 if (!empty(val.lat)){
		 		
		 	 	 if ( map_hide_pickup==1){
			 	 	  if ( val.trans_type_raw=="pickup"){
			 	 	  	   return;
			 	 	  }
			 	 }
			 	 
		 	 	 if ( map_hide_delivery_task==1){
			 	 	  if ( val.trans_type_raw=="delivery"){
			 	 	  	   return;
			 	 	  }
			 	 }
			 	 
			 	 if ( map_hide_successful_task==1){
			 	 	  if ( val.status_raw=="successful"){
			 	 	  	   return;
			 	 	  }
			 	 } 
		 	 	
			 	 info_html='';
			 	 
			 	 if ( val.map_type=="restaurant"){
			 	 info_html+="<div class=\"map-info-window\">";
			 	    info_html+="<h4>"+ jslang.task_id + ": " + val.task_id+"</h4>";
			 	    info_html+="<h5>"+ jslang.name + ": " + val.customer_name+"</h5>";
			 	    info_html+="<p>"+val.address+"</p>";		 	    
			 	    info_html+="<p class=\"inline green-button small rounded\">"+val.trans_type+"</p>";
			 	    info_html+="<p class=\"inline orange-button-small rounded\">"+val.status+"</p>";		 	    
			 	    info_html+="<a href=\"javascript:;\"  class=\"top10 task-details\" data-id=\""+val.task_id+"\"  >"+jslang.details+"</a>";
			 	 info_html+="</div>";
			 	 } else {
			 	 	info_html+=val.first_name+" ";
		  	 	    info_html+=val.last_name;
		  	 	    if(val.is_online==1){
		  	 	       info_html+='<p class="text-primary">'+jslang.online+'</p>';
		  	 	    } else {
		  	 	       info_html+='<p class="text-danger">'+jslang.offline+'</p>';
		  	 	    }
		  	 	    if ( !empty(val.lat) && !empty(val.lng)){
			  	 	    dump('get lat as address');		  	 	    
		  	 	    }	  	 	    
			 	 }
			 	 
			 	 last_lat=val.lat;
			   	 last_lng=val.lng;
			   	  
			   	 var latlng = new google.maps.LatLng( last_lat , last_lng );
			   	 bounds.push(latlng);
			   	 
			   	 if ( val.map_type=="restaurant"){
				   	 if ( val.trans_type=="delivery"){			   	 	
				   	 	/*if ( val.status_raw=="successful"){
				   	 		map_marker=delivery_icon_successful;
				   	 	} else {
				   	 		map_marker=map_marker_delivery;
				   	 	}*/
				   	 	//dump(val.status_raw);
				   	 	switch (val.status_raw)
				   	 	{
				   	 		case "successful":
				   	 		map_marker=delivery_icon_successful;
				   	 		break;
				   	 		
				   	 		case "declined":
				   	 		case "failed":
				   	 		case "cancelled":
				   	 		map_marker=delivery_icon_failed;
				   	 		break;
				   	 		
				   	 		default:
				   	 		map_marker=map_marker_delivery;
				   	 		break;
				   	 	}
				   	 } else {
				   	 	/*if ( val.status_raw=="successful"){
				   	 		map_marker=pickup_icon_ok;
				   	 	} else {
				   	 		map_marker=map_pickup_icon;
				   	 	}*/
				   	 	switch (val.status_raw)
				   	 	{
				   	 		case "successful":
				   	 		map_marker=pickup_icon_ok;
				   	 		break;
				   	 		
				   	 		case "declined":
				   	 		case "failed":
				   	 		case "cancelled":
				   	 		map_marker=delivery_icon_failed;
				   	 		break;
				   	 		
				   	 		default:
				   	 		map_marker=map_pickup_icon;
				   	 		break;
				   	 	}
				   	 }
			   	 } else {
			   	 	  map_marker=driver_icon;
			   	 }
			   	    
			   	 if ( val.map_type=="restaurant"){
				   	 map.addMarker({
						lat: val.lat,
						lng: val.lng,			
						icon : map_marker ,	
						infoWindow: {
						  content: info_html
						}
					 });		     
			   	 } else {
			   	 	if ( val.is_online==2){		   	 		
			   	 		map_marker=driver_icon_offline;
			   	 	}
			   	 	plotDriverToMap( val.lat, val.lng , map_marker , info_html );
			   	 }
			 	  
		 	 }
		 	 		 	 
		 }); /*end each*/
		 
		 dump("run_agent_dashboard=>" + run_agent_dashboard);
		 if(run_agent_dashboard==1){
		    map.fitLatLngBounds(bounds);
		    //loadAgentDashboard();
		 }
	} else {
		dump('no task to map');		
		//loadAgentDashboard();
	}
}

function setDriverMarker(lat , lng , info_html )
{
	if(empty(lat)){
		return ;
	}
	if(empty(lng)){
		return ;
	}
	
	dump('setDriverMarker');
			
	var marker = map.addMarker({
         lat: lat ,
         lng: lng ,
         icon : driver_icon ,	
         draggable: false,
         infoWindow: {
		    content: info_html
		 }
    });
}

function scroll(id){
   if ( $(id) ) {
      $('.content_main').animate({scrollTop: $(id).offset().top-100},'slow');
   }
}


var notification_handle='';

$(document).ready(function(){
					
	if ( $("#layout_1").exists() ){
	   getInitialNotifications();
	   setTimeout('getNotifications()', 1100);
	}
	
}); /*end docu*/
function getInitialNotifications()
{
	 action="getInitialNotifications";
	 params='';
	 
	 var notification_handle2;
	
	 notification_handle2 = $.ajax({
		url: ajax_url+"/"+action, 
		data: params,
		type: 'post',                  		
		dataType: 'json',
		timeout: 7000,		
	 beforeSend: function() {	 	
	 	 if(notification_handle2 != null) {
	 	   notification_handle2.abort();
	 	   dump("ajax abort");	 	   	  
	 	} 
	 },
	 complete: function(data) {		
	 	notification_handle2 = (function () { return; })();
	 },
	 success: function (data) {	  		 	
	 	if (data.code==1){	 			 		
	 		$.each( data.details , function( key, val ) { 	 		 			 
	 			 fillPopUpNotification( val.message, val.title , val.task_id , val.status );
	 		});	
	 	} else {
	 		$("#notification_list").prepend('<p class="no-noti text-info">'+jslang.no_notification+'</p>');
	 	}
     },
	 error: function (request,error) {	    	 	 
	 }
    });     	 
}

function getNotifications()
{
	 action="GetNotifications";
	 params='';
	 
	 notification_handle = $.ajax({
		url: ajax_url+"/"+action, 
		data: params,
		type: 'post',                  		
		dataType: 'json',
		timeout: 7000,		
	 beforeSend: function() {	 	
	 	 window.clearInterval(notification_handle);	 	
	 },
	 complete: function(data) {		
	 	notification_handle = setInterval(function(){getNotifications()}, 10000); 	
	 },
	 success: function (data) {	  		 	
	 	if (data.code==1){	 	
	 		$(".no-noti").remove();	
	 		playNotification(); 
	 		$.each( data.details , function( key, val ) { 	 	
	 			 toastMessage( val.message, val.title );   
	 			 fillPopUpNotification( val.message, val.title , val.task_id , val.status );
	 		});	
	 	}
     },
	 error: function (request,error) {	    
	 	 window.clearInterval(notification_handle);
	 }
    });     	 
}

function fillPopUpNotification(message,title , task_id  , status )
{
	var link='<a data-id="'+task_id+'" class="task-details" href="javascript:;">'+task_id+'</a>';
	var new_title = status + " "+ jslang.task_id + ":"+ link ;
	var html='';
	html+='<li>';
	html+='<i class="ion-ios-circle-filled"></i> '+message +" <br/>"+new_title;
	html+='</li>';
	$("#notification_list").prepend(html);
}

function toastMessage(message,title)
{
	 if (empty(title)){
	 	title='';
	 }
	 if (empty(message)){
	 	return;
	 }
	 toastr.options = {	  
	  "positionClass": "toast-bottom-right",
	  "preventDuplicates": false,
	  "onclick": null,
	  "showDuration": "500",
	  "hideDuration": "1000",
	  "timeOut": "5000",
	  "extendedTimeOut": "1000",
	  "showEasing": "swing",
	  "hideEasing": "linear",
	  "showMethod": "fadeIn",
	  "hideMethod": "fadeOut"
	};
	toastr.info(message,title);
}

function initJplayer()
{	
   $("#jplayer").jPlayer({
    ready: function() {
      $(this).jPlayer("setMedia", {
        mp3: site_url+"/assets/audio/fb-alert.mp3"
      })
    },
    swfPath: site_url+ "/assets/jplayer",
    loop: false
   });
}

function playNotification()
{
	var drv_sound_on = Cookies.get('drv_sound_on');
	if (drv_sound_on==2){
		// do nothing
	} else {
	   $("#jplayer").jPlayer("play");
	}
}

/*VERSION 1.1*/
$(document).ready(function(){
	
	if ( $(".report_div").exists() ){
		loadChart();
	}
	
	$( document ).on( "change", "#team_selection", function() { 		
		team_id=$(this).val();
		$("#driver_selection").val("all");
		$("#driver_selection .team_opion").hide();
		$("#driver_selection .option_"+team_id).show();
	});
	
	$( document ).on( "click", ".view_charts", function() {
		report_type=$(this).data("id");		
		$("#chart_type").val(report_type);
		loadChart();
	});	
	
	$( document ).on( "change", "#time_selection", function() { 		
		if ( $(this).val()=="custom" ){
			$(".custom_selection").show();
			//$("#time_selection").hide();
		} else {
			$(".custom_selection").hide();
			//$("#time_selection").show();
			loadChart();
		}		
	});		
	
	$( document ).on( "change", "#team_selection,#driver_selection", function() { 		
		loadChart();
	});		
	
	$( document ).on( "click", ".change_charts", function() { 		
		$("#chart_type_option").val( $(this).data("id") );
		loadChart();
	});		
	
	function loadChart()
	{
		params="chart_type="+$("#chart_type").val();
		params+='&chart_type_option='+ $("#chart_type_option").val();
		params+='&time_selection='+ $("#time_selection").val();
		params+='&team_selection='+ $("#team_selection").val();
		params+='&driver_selection='+ $("#driver_selection").val();
		params+='&start_date='+ $("#start_date").val();
		params+='&end_date='+ $("#end_date").val();
		$(".table_charts").html('');
		callAjax("chartReports", params );
	}
	
	if ( $('.datetimepicker1').exists() ){
    	dump('datetimepicker1 exists');    	    	
	    $('.datetimepicker1').datetimepicker({	    	
	        timepicker:false,      
	        format:'Y-m-d',	 
	        onChangeDateTime:function(dp,$input){
                if ( $("#start_date").val()!="" && $("#end_date").val()!="" ){
                	loadChart();
                }
            }       
	    });	    
    }	
    
    $( document ).on( "keyup", ".numeric_only", function() {
      this.value = this.value.replace(/[^0-9\.]/g,'');
    });	 
    
    if ( $("#driver_auto_assign_type").exists() ){
    	switchAutoAssign();
    }
    $( document ).on( "click", "#driver_auto_assign_type", function() {
       switchAutoAssign();
    });	 

    
     if ( $("#upload-certificate-dev").exists()) {
	   var uploader = new ss.SimpleUpload({
	       button: 'upload-certificate-dev', // HTML element used as upload button
	       url: ajax_url+"/uploadCertificate", // URL of server-side upload handler
	       name: 'uploadfile', // Parameter name of the uploaded file
	       responseType: 'json',
	       allowedExtensions: ['pem'],
		   maxSize: 11024, // kilobytes
		   onExtError: function(filename,extension ){
			   nAlert("Invalid File extennsion","warning");
		   },
		   onSizeError: function (filename,fileSize){ 
			   nAlert("Invalid File size","warning");  
		   },       
		   onSubmit: function(filename, extension) {      	            
		   	  busy(true);
		   },	
		   onComplete: function(filename, response) {	   	  
		   	  dump(response);
		   	  busy(false);
		   	  if (response.code==1){	   	  	 	   	  	 
		   	  	 nAlert(response.msg,"success");
		   	  	 $("#driver_ios_push_dev_cer").val(filename);		   	  	 
		   	  } else {
		   	  	 nAlert(response.msg,"warning");	   	  	 
		   	  }
		   }
	    });    
   }	
   
    if ( $("#upload-certificate-prod").exists()) {
	   var uploader = new ss.SimpleUpload({
	       button: 'upload-certificate-prod', // HTML element used as upload button
	       url: ajax_url+"/uploadCertificate", // URL of server-side upload handler
	       name: 'uploadfile', // Parameter name of the uploaded file
	       responseType: 'json',
	       allowedExtensions: ['pem'],
		   maxSize: 11024, // kilobytes
		   onExtError: function(filename,extension ){
			   nAlert("Invalid File extennsion","warning");
		   },
		   onSizeError: function (filename,fileSize){ 
			   nAlert("Invalid File size","warning");  
		   },       
		   onSubmit: function(filename, extension) {      	            
		   	  busy(true);
		   },	
		   onComplete: function(filename, response) {	   	  
		   	  dump(response);
		   	  busy(false);
		   	  if (response.code==1){	   	  	 	   	  	 
		   	  	 nAlert(response.msg,"success");
		   	  	 $("#driver_ios_push_prod_cer").val(filename);		   	  	 
		   	  } else {
		   	  	 nAlert(response.msg,"warning");	   	  	 
		   	  }
		   }
	    });    
   }	
   
   $.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm-ios',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm-ios").serialize();
	      var action = $("#frm-ios #action").val();
	      var button = $('#frm-ios button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});    
		
	$( document ).on( "click", ".locate-driver-onmap", function() {
		var t_lat = $(this).data("lat");
		var t_lng = $(this).data("lng");
		if ( !empty(t_lat) && !empty(t_lng) ){		   
		   map.setCenter(t_lat,t_lng);
		   map.setZoom(14);		   
		}
	});
		
	$( document ).on( "click", ".show-direction", function() { 		
		showDirection( $("#data-driver_lat").val(), $("#data-driver_lng").val(), $("#data-task_lat").val(), $("#data-task_lng").val() );
	
	});		
	
	
	$( document ).on( "click", ".close-direction", function() { 	
		$(".map-direction").hide();
    	$("#direction_output").html('');
    	$(".task-details-tabs").show();
	});	
	
}); /*end docu*/

function convertLatLongToAddress(lat, lng)
{	
	var latlng = new google.maps.LatLng(lat, lng);
	var geocoder = geocoder = new google.maps.Geocoder();
	geocoder.geocode({ 'latLng': latlng }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                dump( results[1].formatted_address );
                $("#delivery_address").val( results[1].formatted_address );
            }
        }
    });
}

function switchAutoAssign()
{
	var selected=$("#driver_auto_assign_type:checked").val();
	dump(selected);
	switch (selected)
	{
		case "one_by_one":
			$(".section_one_by_one").show();
			$(".section_send_to_all").hide();
		break;
		
		case "send_to_all":
		   $(".section_one_by_one").hide();
		   $(".section_send_to_all").show();
		break;
	}
}

function plotDriverToMap(lat, lng , map_marker , info_html )
{
	if (empty(lat) && empty(lng)){
		return;
	}
	var latlng = new google.maps.LatLng(lat, lng);
	var geocoder = geocoder = new google.maps.Geocoder();
	geocoder.geocode({ 'latLng': latlng }, function (results, status) {
		dump(status);
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
            	dump('driver address');
                dump( results[1].formatted_address );      
                
                info_html+='<p><b>'+jslang.currentlocation+'</b>: '+results[1].formatted_address+'</p>';
                map.addMarker({
					lat: lat,
					lng: lng,			
					icon : map_marker ,	
					infoWindow: {
					  content: info_html
					}
				 });		          
            }
        } else {
        	// getting of address has failed
        	 map.addMarker({
				lat: lat,
				lng: lng,			
				icon : map_marker ,	
				infoWindow: {
				  content: info_html
				}
			 });
        }
    });
}

function showDirection(driver_lat , driver_lng, task_lat, task_lng )
{
	$("#direction_output").html('');		
	$("#map-direction").html('');
	
	$(".task-details-tabs").hide();
    $(".map-direction").show(); 
    	
	var directionsService = new google.maps.DirectionsService();
    var directionsDisplay = new google.maps.DirectionsRenderer();

     var map_direction = new google.maps.Map(document.getElementById('map-direction'), {
       zoom:7,
       mapTypeId: google.maps.MapTypeId.ROADMAP,
       scrollwheel: false
     });

     directionsDisplay.setMap(map_direction);
     directionsDisplay.setPanel(document.getElementById('direction_output'));
     
     var destination_location= task_lat+","+task_lng;
     dump("destination->"+destination_location);
     
     driver_origin = driver_lat+','+driver_lng;
     
     trave_mod="DRIVING";

     switch( trave_mod )
     {
     	case "DRIVING":
     	var request = {
	       origin: driver_origin, 
	       destination: destination_location ,
	       travelMode: google.maps.DirectionsTravelMode.DRIVING
	     };
     	break;
     	
     	case "WALKING":
     	var request = {
	       origin: driver_origin, 
	       destination:destination_location ,
	       travelMode: google.maps.DirectionsTravelMode.WALKING
         };
     	break;
     	
     	case "BICYCLING":
     	var request = {
	       origin: driver_origin, 
	       destination: destination_location ,
	       travelMode: google.maps.DirectionsTravelMode.BICYCLING
	     };	     
     	break;
     	
     	case "TRANSIT":
     	var request = {
	       origin: driver_origin, 
	       destination: destination_location ,
	       travelMode: google.maps.DirectionsTravelMode.TRANSIT
	     };
     	break;
     }          
     
     directionsService.route(request, function(response, status) {       
       if (status == google.maps.DirectionsStatus.OK) {
         directionsDisplay.setDirections(response);         
       } else {
       	  nAlert(status,"warning");
       	  $("#direction_output").css({"display":"none"});	
       }
     });
}

function retryAutoAssign(task_id)
{	
	$(".autoassign-col-1-"+task_id).html('');
	$(".autoassign-col-2-"+task_id).html('<p class="small-font text-primary">'+jslang.autoassigning+'...</p>');	
	callAjax("retryAutoAssign","task_id="+ task_id);
}

function backToTaskDetails()
{
	$(".show-location-map-modal").modal('hide');
	$(".task-details-modal").modal('show');
}

$(document).ready(function(){
	
	$( document ).on( "click", ".send-push", function() {
		var id=$(this).data("id");		
		$("#push_form_driver_id").val(id);
		$(".push_form_driver_name").html( $(this).data("fname") );
		$(".modal-push-form-driver").modal("show");
	});
	
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm_send_push_driver',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm_send_push_driver").serialize();
	      var action = $("#frm_send_push_driver #action").val();
	      var button = $('#frm_send_push_driver button[type="submit"]');	      
	      callAjax(action,params,button);
	      return false;
	    }  
	});    	
	
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm_send_push_bulk',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm_send_push_bulk").serialize();
	      var action = $("#frm_send_push_bulk #action").val();
	      var button = $('#frm_send_push_bulk button[type="submit"]');	      
	      callAjax(action,params,button);
	      return false;
	    }  
	});    
	
}); /*end docu*/


$(document).ready(function(){
	
	if ( $("#driver_allowed_team_to_merchant").exists() ){
		var selected=$("#driver_allowed_team_to_merchant:checked").val();
		if(selected==2){
		   $(".driver_allowed_merchant_list").attr("disabled",false).trigger("chosen:updated");	
		}
	}
	$($(".driver_allowed_team_to_merchant1")).on('switchChange.bootstrapSwitch', function(event, state) {	 		
		if (state){
		   $(".driver_allowed_merchant_list").attr("disabled",true).trigger("chosen:updated");		           
	       $($(".driver_allowed_team_to_merchant2")).bootstrapSwitch('state', false,false);	        
		}
	});
	$($(".driver_allowed_team_to_merchant2")).on('switchChange.bootstrapSwitch', function(event, state) {
		if (state){
		   $(".driver_allowed_merchant_list").removeAttr("disabled").trigger("chosen:updated");		           
	       $($(".driver_allowed_team_to_merchant1")).bootstrapSwitch('state', false, false);	
		}	    
	    //$(".driver_allowed_merchant_list").attr("disabled",false);        
	});
	
	if ( $(".drop_address").exists() ){    
		
	   $(".drop_address")
	   .geocomplete({
	  	 country: default_country
	   })
	   .bind("geocode:result", function(event, result){	     	   	  
	      setMarkerDropOff( $(".drop_address").val() );
	   });
    }
    
    $( document ).on( "change", "#dropoff_merchant", function() { 
        var merchant_id=$(this).val();
        dump(merchant_id);
        if(merchant_id>=1){
        	callAjax("getMerchantAdddress","mtid="+merchant_id);
        } else {
        	$("#drop_address").val('');
        }
    });			
	
}); /*end docu*/

function initMapDropOff()
{
	if ( $(".map_dropoff").html()=="" ){
	    map_dropoff = new GMaps({
		  div: '.map_dropoff',
		  lat: default_location_lat,
		  lng: default_location_lng	,
		  zoom: 5,
		  styles: map_style 
	    }); 	
	}
}

function setMarkerDropOff(address)
{	
	map_dropoff.removeMarkers();
				
	GMaps.geocode({
	  address: address,
	  callback: function(results, status) {
	    if (status == 'OK') {
	    	
	      if($('.new-task-submit').is(':visible')) {
		    $(".new-task-submit").css({ 'pointer-events' : 'auto' });
	      }
	    	
	      var latlng = results[0].geometry.location;
	      map_dropoff.setCenter(latlng.lat(), latlng.lng());
	      map_dropoff.setZoom(15);
	      
	      dump(latlng.lat());
	      dump(latlng.lng());
	      
	      $("#dropoff_lat").val( latlng.lat() );
	      $("#dropoff_lng").val( latlng.lng() );
	      
	      var marker_dropoff = map_dropoff.addMarker({
	         lat: latlng.lat(),
	         lng: latlng.lng(),
	         draggable: true	         
	      });
	      
	      marker_dropoff.addListener('dragend',function(event) {	      	 
	         dump( "lat=>"+event.latLng.lat() );        
	         dump( "long=>"+event.latLng.lng() );
	         
	         $("#dropoff_lat").val( event.latLng.lat() );
	         $("#dropoff_lng").val( event.latLng.lng() );
	         
	         convertLatLongToAddress2( event.latLng.lat() , event.latLng.lng() , "#drop_address" );
	         
	      });
	      
	    } else {
	    	if($('.new-task-submit').is(':visible')) {
		        $(".new-task-submit").css({ 'pointer-events' : 'auto' });
	        }	    	
	    }
	  }
	});	
}

function convertLatLongToAddress2(lat, lng, target_div)
{	
	var latlng = new google.maps.LatLng(lat, lng);
	var geocoder = geocoder = new google.maps.Geocoder();
	geocoder.geocode({ 'latLng': latlng }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                dump( results[1].formatted_address );
                $(target_div).val( results[1].formatted_address );
            }
        }
    });
}

function setDropoffMapMarker(lat , lng )
{
		
	//alert('setDropoffMapMarker');
	map_dropoff.removeMarkers();
	
	if(empty(lat)){
		return;
	}
	if(empty(lng)){
		return;
	}
	
	/*alert(lat);
	alert(lng);*/
		
    map_dropoff.setCenter( lat , lng);
    map_dropoff.setZoom(15);
    
    $("#dropoff_lat").val( lat );
	$("#dropoff_lng").val( lng );
	
	var marker_dropoff = map_dropoff.addMarker({
	   lat: lat,
	   lng: lng,
	   draggable: true	         
	});
	
	marker_dropoff.addListener('dragend',function(event) {	      	 
	     dump( "lat=>"+event.latLng.lat() );        
	     dump( "long=>"+event.latLng.lng() );
	     
	     $("#dropoff_lat").val( event.latLng.lat() );
	     $("#dropoff_lng").val( event.latLng.lng() );
	     
	     convertLatLongToAddress2( event.latLng.lat() , event.latLng.lng() , "#drop_address" );
	     
	});
}


$(document).ready(function(){
   $( document ).on( "click", ".assign-task-to-all", function() {    
   	  c=confirm(jslang.are_your_sure+"?");
      if(c){
    	var task_id= $(".task_id_details").val();    	
    	callAjax("assignToAllDrivers", "task_id=" + task_id );
      }   	       	
   });	
   
   if ( $("#upload-driver-photo").exists()) {
	   var uploader = new ss.SimpleUpload({
	       button: 'upload-driver-photo', // HTML element used as upload button
	       url: ajax_url+"/uploadprofilephoto", // URL of server-side upload handler
	       name: 'uploadfile', // Parameter name of the uploaded file
	       responseType: 'json',
	       allowedExtensions: ['jpeg','png','jpg','gif'],
		   maxSize: 11024, // kilobytes
		   onExtError: function(filename,extension ){
			   nAlert("Invalid File extennsion","warning");
		   },
		   onSizeError: function (filename,fileSize){ 
			   nAlert("Invalid File size","warning");  
		   },       
		   onSubmit: function(filename, extension) {      	            
		   	  busy(true);
		   },	
		   onComplete: function(filename, response) {	   	  
		   	  dump(response);
		   	  busy(false);		   	 
		   	  if (response.code==1){
		   	  	 nAlert(response.msg,"success");
		   	  	 $("#profile_photo").val(filename);
		   	  	 var image='<img src="'+ upload_url+"/driver/"+ filename +'" />';
		   	  	 $(".profile-photo").html(image);
		   	  } else {
		   	  	 nAlert(response.msg,"warning");	 
		   	  }
		   }
	    });    
   }	
   
   $( document ).on( "click", ".driver_approved", function() {    	
    	var driver_id=$(this).data("id");
    	callAjax("driverUpdateStatus", "driver_id=" + driver_id +"&status=1" );
   });
   
   $( document ).on( "click", ".driver_denied", function() {
    	var driver_id=$(this).data("id");    	
    	callAjax("driverUpdateStatus", "driver_id=" + driver_id +"&status=2" );
   });
   
}); /*end docu*/	    


var track_map;
var track_driver_marker;
var track_interval;
var track_interval_counter=1;

$(document).ready(function(){
	
	if ( $(".track-map").exists() ){		
		track_map = new GMaps({
		  div: '.track-map',
		  lat: default_location_lat,
		  lng: default_location_lng	,
		  zoom: 5,
		  styles: map_style 
		}); 
	}
		
	$( document ).on( "change", "#track_driver_id", function() {
		$(".track_replay").hide();
		$(".track-details-wrap").html('');
		clearInterval(track_interval);
		
		var selected=$(this).val();		
		if ( selected>=1){
			$("#track_date").removeAttr("disabled");
			$(".tr_d").hide();
			$(".track_driver_"+selected).show();
			$("#track_date").val("-1");
			$("#track_date").focus();
		} else {
			$(".tr_d").hide();
			$("#track_date").attr("disabled",true);
		}
	});
	
	$( document ).on( "change", "#track_date", function() {
		$(".track_replay").hide();
		$(".track-details-wrap").html('');
		clearInterval(track_interval);
		
		var selected=$(this).val();		
		if ( selected!="-1"){
			params="track_driver_id="+ $("#track_driver_id").val();
			params+="&track_date="+ $("#track_date").val();
			callAjax("loadAgentTrackBack",params)
		}
	});
	
	$( document ).on( "click", ".track_replay", function() {
		 params="track_driver_id="+ $("#track_driver_id").val();
	     params+="&track_date="+ $("#track_date").val();
		 callAjax("loadAgentTrackBack",params)
	});
	
	/*$( document ).on( "change", "#change_language", function() {
		  var selected =  $('#change_language :selected').text();
		  if ( selected<=0){
		  	 return ;
		  }
		  url = home_url+"/setlang/?lang="+selected+"&action=" + $("#action_name").val() ;		  
		  window.location.href =  url;
	});*/
	
	
   $( document ).on( "click", ".dashboard-tab a.tab-a", function() {   	    
   	    var cur_tab=$(this);
   	    var tab=$(this).data("id");   	    	    
   	    $("a.tab-a").removeClass("active");
   	    $(this).addClass("active");
   	    switch (tab)
   	    {
   	    	 case "tab-task":
   	    	    $(".content_1").show();   	    	    
   	    	    $(".content_2").hide();
   	    	    $(".content_3").hide();
   	    	 break;
   	    	 
   	    	 case "tab-map":
   	    	    $(".content_1").hide();
   	    	    $(".content_2").show();
   	    	    $(".content_3").hide();
   	    	 break;
   	    	 
   	    	 case "tab-agent":
   	    	    $(".content_1").hide();
   	    	    $(".content_2").hide();
   	    	    $(".content_3").show();
   	    	 break;
   	    }
   	    
   });	
      
   $( document ).on( "click", ".nav-menu", function() { 
   	   $(".parent-wrapper .content_1.white").toggle("fast", function() {            
            if ( $(this).attr("style")=="display: block;" ||  $(this).attr("style")=="display:block;" ){            	
            	$(".content_main").addClass("margin-left");
            } else {
            	$(".content_main").removeClass("margin-left");
            }
        });
   });	
      
   $( document ).on( "change", ".applanguage", function() { 
   	   var selected=$(this).val();
   	   if ( selected=="0" || selected==0){
   	   	  // do nothing
   	   } else {   	   	  
   	   	   window.location.href = home_url+'/index/setlanguage/?lang='+selected;
   	   }
   });	
   
   $( document ).on( "click", ".search_agent_btn", function() {    	    
   	   searchAgent();
   });
   $( ".search_agent" ).on( "keydown", function(event) {   	    
   	    if(event.which == 13){
   	       searchAgent();
   	    }
   });
   
   $( document ).on( "click", ".search_agent_back", function() { 
   	    $(".search_agent_wrap").hide();
   	    $(".search_agent").val('');
   	    var params="date="+ $(".calendar_formated").val() + "&team_id=" + $("#team").val();
   	    	params+="&agent_name="+ $(".search_agent").val();
   	    	callAjax2("loadAgentDashboard", params );
   });   
		
}); /*end docu*/

function showAgentSearch()
{
	$(".search_agent_wrap").show();
	$(".search_agent").focus();
}

function searchAgent()
{
	if ( $(".search_agent").val()==""){
    	nAlert(jslang.driver_required,"warning");
    } else {
    	var params="date="+ $(".calendar_formated").val() + "&team_id=" + $("#team").val();
    	params+="&agent_name="+ $(".search_agent").val();
    	callAjax2("loadAgentDashboard", params );
    }
}

function trackBackMarker(data)
{	
	$(".track_replay").hide();
	var total= parseInt(data.length);
	if (total<=0){
		return;
	}
	
	$(".track-details-wrap").html('');
	
	//dashboard_task_handle = setInterval(function(){loadDashboardTaskSilent()}, 9000);
	track_interval=setInterval(function(){
		dump(track_interval_counter);
		
		var current_data = data[track_interval_counter-1];
		dump(current_data);
		
		if (empty(track_driver_marker)){
			track_driver_marker = track_map.addMarker({
		         lat: current_data.latitude ,
		         lng: current_data.longitude,
		         icon : driver_icon 	         	       
		    });
		    track_map.setCenter( current_data.latitude , current_data.longitude );
		    track_map.setZoom(15);
		    
		    
		    $(".track-details-wrap").append('<p>'+ jslang.lat+":"+ current_data.latitude + " , " + jslang.lng +":"+ current_data.longitude + '</p>');
		    
		} else {
			track_driver_marker.setPosition( new google.maps.LatLng( current_data.latitude , current_data.longitude ) );
			
			$(".track-details-wrap").append('<p>'+ jslang.lat+":"+ current_data.latitude + " , " + jslang.lng +":"+ current_data.longitude + '</p>');
			
			if ( !track_map.getBounds().contains(track_driver_marker.getPosition()) ){
				track_map.setCenter( current_data.latitude , current_data.longitude );		    
			} 
		}
		
		if ( track_interval_counter >= total){
			track_interval_counter=0;
			$(".track_replay").css({
				"display":"block"
			});
			clearInterval(track_interval);	 
		}
		track_interval_counter++;
	}, 1000);	
}