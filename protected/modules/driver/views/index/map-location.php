<div class="modal show-location-map-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
    
      <div class="modal-header">
         <button aria-label="Close" data-dismiss="modal" class="close" type="button">
           <span aria-hidden="true"><i class="ion-android-close"></i></span>
         </button> 
         
        <div class="row">
        <div class="col-md-2">
        <a href="javascript:backToTaskDetails();"><i class="ion-ios-arrow-thin-left"></i> <?php echo Driver::t("Back")?></a>
        </div>
        <div class="col-md-8 center">        
        <h4 id="mySmallModalLabel" class="modal-title">
        <?php echo t("Location")?> </span>
        </h4>
        </div> <!--col-->
              
        </div><!-- row-->
        
      </div>  
      
      <div class="modal-body">
      
      <?php echo CHtml::hiddenField('map_location_lat')?> 
      <?php echo CHtml::hiddenField('map_location_lng')?> 
      
      <div class="map-location-wrap">
        <div class="map-location"></div>
      </div>
      

      </div> <!--body-->
    
    </div> <!--modal-content-->
  </div> <!--modal-dialog-->
</div> <!--modal-->            