<div class="modal task-details-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
    
      <div class="modal-header">
         <button aria-label="Close" data-dismiss="modal" class="close" type="button">
           <span aria-hidden="true"><i class="ion-android-close"></i></span>
         </button> 
         
        <div class="row">
        
        <div class="col-md-4">
        <h4 id="mySmallModalLabel" class="modal-title">
        <?php echo Driver::t("Task ID")?> : <span class="task-id"></span>
        </h4>
        </div> <!--col-->
        
        <!--<div class="col-md-4" id="order-id-wrap">
        <h4>
        <?php echo Driver::t("Order No")?> : <span class="order-id"></span>
        </h4>
        </div>--> <!--col-->              
        
        </div><!-- row-->
        
      </div>  
      
      <div class="modal-body">
      
      <div class="map-direction">
        <a href="javascript:;" class="close-direction"><?php echo Driver::t("Close")?></a>
        <div id="map-direction" class="top20"></div>
        <div id="direction_output"></div>
      </div> <!--map-direction-->
      
      <form id="frm" class="frm" method="POST" onsubmit="return false;">       
       <?php echo CHtml::hiddenField('task_id_details','',array(
         'class'=>"task_id_details"
       ))?>       
      </form>
       
	<ul id="tabs" class="task-details-tabs"> 
	 <li class="active"><?php echo Driver::t("Task Details")?></li>
	 <li><?php echo Driver::t("Activity Timeline")?></li>
	 <li><?php echo Driver::t("Order Details")?></li>
	 <!--<li><?php echo Driver::t("Path History")?></li>-->
	</ul>
		
	<ul id="tab" class="task-details-tabs" >
	 <li class="active">    
	    <div class="row">
	      <div class="col-md-6 border">
	        <div class="grey-box top10">
	        
	           <div class="form-group left-form-group">
	            <label class="font-medium"><?php echo Driver::t("Status")?> :</label>
	            <span class="v task_status"></span>
	           </div>
	           
	           <div class="form-group left-form-group">
	            <label class="font-medium"><?php echo Driver::t("Transaction Type")?> :</label>
	            <p class="v transaction_type"></p>
	           </div>    
	         
	           <div class="form-group left-form-group">
	            <label class="font-medium"><?php echo Driver::t("Start Before")?> :</label>
	            <p class="v"></p>
	           </div>    
	           
	           <div class="form-group left-form-group">
	            <label class="font-medium"><?php echo Driver::t("Complete Before")?> :</label>
	            <p class="v delivery_date"></p>
	           </div>    	           
	           
	        </div><!-- white-box-->
	      </div> <!--col-->
	      <div class="col-md-6 border">
	        <div class="grey-box top10">
	        
	        <div class="form-group left-form-group">
	            <label class="font-medium"><?php echo Driver::t("Name")?> :</label>
	            <p class="v customer_name"></p>
	        </div>    
	        
	        <div class="form-group left-form-group">
	          <label class="font-medium"><i class="ion-ios-telephone"></i></label>
	          <span class="v contact_number"></span>
	        </div>
	        
	        <div class="form-group left-form-group">
	          <label class="font-medium"><i class="ion-ios-email"></i></label>
	          <span class="v email_address"></span>
	        </div>
	        
	        <div class="form-group left-form-group">
	          <label class="font-medium"><i class="ion-ios-location"></i></label>
	          <span class="v delivery_address"></span>
	        </div>
	        
	        </div><!-- white-box-->
	      </div> <!--col-->
	    </div> <!--row-->
	    
	    <div class="grey-box top10" id="order-id-wrap">
	      <div class="row">
	          <div class="col-md-6">
	             <div class="form-group left-form-group">
		          <label class="font-medium"><?php echo Driver::t("Order No")?>:</label>
		          <span class="v order-id"></span>
		         </div>
	          </div> <!--col-->
	          <div class="col-md-6">
	             <div class="form-group left-form-group">
		          <label class="font-medium"><?php echo Driver::t("Merchant name")?>:</label>
		          <span class="v merchant_name"></span>
		         </div>
	          </div> <!--col-->
	        </div> <!--row-->
	    </div> <!--box-->
	    
	    <div class="grey-box top10">
	        <div class="row">
	          <div class="col-md-6">
	             <div class="form-group left-form-group">
		          <label class="font-medium"><?php echo Driver::t("Team")?>:</label>
		          <span class="v team_name"></span>
		         </div>
	          </div> <!--col-->
	          <div class="col-md-6">
	             <div class="form-group left-form-group">
		          <label class="font-medium"><?php echo Driver::t("Driver")?>:</label>
		          <span class="v driver_name"></span>
		         </div>
	          </div> <!--col-->
	        </div> <!--row-->
	        
	        <div class="row">
	          <div class="col-md-6">
	             <div class="form-group left-form-group">
		          <label class="font-medium"><?php echo Driver::t("Phone")?>:</label>
		          <span class="v driver_phone"></span>
		         </div>
	          </div> <!--col-->
	         </div> 
	    </div><!-- white-box-->
	    
	    <div class="grey-box top10">
	        <div class="form-group left-form-group">
	            <label class="font-medium"><?php echo Driver::t("Task Description")?> :</label>
	            <p class="v task_description"></p>
	        </div>  
	    </div><!-- white-box-->
	    
	    
	    <div class="grey-box top10 dropoff_details_wrap">
	        <div class="form-group left-form-group">
	            <label class="font-medium">
	            <span class="dropoff_details_label_1"><?php echo Driver::t("Drop Details")?></span>
	            <span class="dropoff_details_label_2"><?php echo Driver::t("Pickup Details")?></span>
	             :</label>
	            
	           <div class="row">
	              <div class="col-md-3"><b><?php echo Driver::t("Merchant")?></b></div>
	              <div class="col-md-8">: <span class="details_dropoff_merchant"></span></div>
	           </div>    
	           
	            <div class="row top10">
	              <div class="col-md-3"><b><?php echo Driver::t("Name")?></b></div>
	              <div class="col-md-3">: <span class="details_dropoff_contact_name"></span></div>
	              
	              <div class="col-md-3"><b><?php echo Driver::t("Contact number")?></b></div>
	              <div class="col-md-3">: <span class="details_dropoff_contact_number"></span></div>
	            </div>
	            
	           <div class="row top10">
	              <div class="col-md-3"><b><?php echo Driver::t("Address")?></b></div>
	              <div class="col-md-8">: <span class="details_drop_address"></span></div>
	           </div>    
	             
	        </div>  
	    </div><!-- white-box-->
	    
	    
	 </li>
	 <li>
	     <div class="top10" id="task-history">
	     
	     <!--<div class="grey-box top10">
	     <div class="row">
	       <div class="col-md-7">
	       Status updated from Unassigned to Assigned
	       </div>
	       <div class="col-md-5">
	         <i class="ion-ios-clock-outline"></i> 5/4/2016 07:52 am <br/>
	         <i class="ion-ios-location"></i>  <a href="#">Location on Map</a>
	       </div>
	     </div>  
	     </div>--> <!--box-->
	     
	     </div> <!--task-history-->
	 </li>
	 <li>
	     <div class="grey-box top10" id="order-details">
	     
	     </div>
	 </li>
	 
	 <!--<li>
	   <div class="grey-box top10">
	     Coming soon
	   </div>
	 </li>-->
	 
	</ul>
	
	<div class="panel-footer top20 task-action-button">       
	 
	<div class="action-1">
	   <div class="row">
	     <div class="col-md-4 border">
	       <a href="javascript:;" class="orange-button assign-agent re_assign_agent" 
            data-modalclose="task-details-modal">
	        <?php echo Driver::t("Assign Agent")?>
	       </a>
	     </div> <!--col-->
	     <div class="col-md-4 border">
	       <a href="javascript:;" class="orange-button edit-task">
	       <?php echo Driver::t("Edit Task")?>
	       </a>
	     </div> <!--row-->
	     <div class="col-md-4 border">
	       <a href="javascript:;" class="orange-button change-status">
	       <?php echo Driver::t("Change Status")?>
	       </a>
	     </div> <!--row-->
	   </div> <!--row-->
	   
	   <div class="row top10">
	     <div class="col-md-4 border">
	       <a href="javascript:;" class="orange-button delete-task">
	       <?php echo Driver::t("Delete Task")?>
	       </a>
	     </div> <!--col-->
	     
	     <div class="col-md-4 assign-task-to-all-wrap">
	       <a href="javascript:;" class="orange-button assign-task-to-all">
	       <?php echo Driver::t("Assign to all drivers")?>
	       </a>
	     </div> <!--col-->
	     
	     <div class="col-md-4 border">
	       <?php 
	       echo CHtml::hiddenField('data-driver_lat');
	       echo CHtml::hiddenField('data-driver_lng');
	       echo CHtml::hiddenField('data-task_lat');
	       echo CHtml::hiddenField('data-task_lng');
	       ?>
	       <a href="javascript:;" class="orange-button show-direction">
	       <?php echo Driver::t("Get Directions")?>
	       </a>
	     </div> <!--col-->
	     
	   </div> <!--row-->  	  
	   	   	   
	</div>  <!--action 1-->
	
	<div class="action-2">
	
	 <div class="row top10">
	     <div class="col-md-4 border">
	       <a href="javascript:;" class="orange-button delete-task">
	       <?php echo Driver::t("Delete Task")?>
	       </a>
	     </div> <!--col-->	     
	   </div> <!--row-->  	  
	   
	</div> <!--action-2-->
	
	   
	</div> <!--panel-footer-->

      </div> <!--body-->
    
    </div> <!--modal-content-->
  </div> <!--modal-dialog-->
</div> <!--modal-->      