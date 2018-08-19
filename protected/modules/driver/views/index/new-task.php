<div class="modal new-task" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    
      <div class="modal-header">
         <button aria-label="Close" data-dismiss="modal" class="close" type="button">
           <span aria-hidden="true"><i class="ion-android-close"></i></span>
         </button> 
        <h4 id="mySmallModalLabel" class="modal-title">
        <?php echo Driver::t("New Task")?>
        </h4> 
      </div>  
      
      <div class="modal-body">

      <form id="frm_task" class="frm" method="POST" onsubmit="return false;">
      <?php echo CHtml::hiddenField('action','addTask')?>
      <?php echo CHtml::hiddenField('task_id','',array(
        'class'=>"task_id"
      ))?>
      <?php echo CHtml::hiddenField('order_id','')?>

      <?php echo CHtml::hiddenField('task_lat','')?>
      <?php echo CHtml::hiddenField('task_lng','')?>
      
      <?php echo CHtml::hiddenField('dropoff_lat','')?>
      <?php echo CHtml::hiddenField('dropoff_lng','')?>
      
      <div class="row">
         <div class="col-md-6 ">
         
          <h5><?php echo Driver::t("Task Description")?></h5>
          <div class="top10">
          <?php 
          echo CHtml::textArea('task_description','',array(
           'class'=>""
          ))
          ?>
          </div>
          
          <div class="top10 row">
            <div class="col-xs-6 ">
              <?php echo CHtml::radioButton('trans_type',false,array(
               'class'=>"trans_type",
               'value'=>'pickup',
               'required'=>true
              ));              
              ?>
              <span><?php echo Driver::t("Pickup")?></span>
            </div>
            <div class="col-xs-6 ">
              <?php echo CHtml::radioButton('trans_type',false,array(
               'class'=>"trans_type",
               'value'=>"delivery"
              ));              
              ?>
              <span><?php echo Driver::t("Delivery")?></span>
            </div> <!--col-->
          </div> <!--row-->
          
          <div class="delivery-info top20">
            <div class="row">
              <div class="col-sm-6">
                <?php echo CHtml::textField('contact_number','',array(
                  'class'=>"mobile_inputs",
                  'placeholder'=>Driver::t("Contact nunber"),
                  'maxlength'=>15
                ))?>
              </div> <!--col-->
              <div class="col-sm-6 ">
                <?php 
                echo CHtml::textField('email_address','',array(
                  'placeholder'=>Driver::t("Email address")
                ))
                ?>
              </div> <!--col-->
            </div> <!--row-->
            
            <div class="row top10">
              <div class="col-sm-6 ">
              <?php echo CHtml::textField('customer_name','',array(
                'placeholder'=>Driver::t("Name"),
                'required'=>true
              ))?>
              </div>
              <div class="col-sm-6 "><?php echo CHtml::textField('delivery_date','',array(
                'placeholder'=>Driver::t("Delivery before"),
                'required'=>true,
                'class'=>"datetimepicker"
              ))?></div>
            </div> <!--row-->
            
            <div class="row top10">
             <div class="col-sm-12 ">
             <?php 
             echo CHtml::textField('delivery_address','',array(
               'class'=>'delivery_address geocomplete delivery_address_task',
               'placeholder'=>Driver::t("Delivery Address"),
               'required'=>true
             ))
             ?>
             </div> <!--col-->
            </div>
            
          </div> <!--delivery-info-wrap-->
          
          
          <div class="dropoff_wrap">
          
          <h4>
          <span class="dropoff_action_1"><?php echo Driver::t("Pickup Details")?></span>
          <span class="dropoff_action_2"><?php echo Driver::t("Drop Details")?></span>
          </h4>
          
           <div class="row top10">
             <div class="col-md-12 ">
             <?php 
             $user_type=Driver::getUserType();
             if ($user_type=="merchant"){
             	$merchant_list= Driver::merchantListByID( Driver::getUserId());
             } else $merchant_list=Driver::merchantList();
             echo CHtml::dropDownList('dropoff_merchant','',
            //(array) Driver::merchantList(),
            (array) $merchant_list,
             array(
               'class'=>"chosen"
             ))
             ?>
             </div>
          </div>   
          
          <div class="row top10">
          <div class="col-sm-6 ">
          <?php echo CHtml::textField('dropoff_contact_name','',array(
            'placeholder'=>Driver::t("Name"),            
          ))?>
          </div>
          <div class="col-sm-6 "><?php echo CHtml::textField('dropoff_contact_number','',array(
            'class'=>"mobile_inputs",
            'placeholder'=>Driver::t("Contact nunber"),
            'maxlength'=>15
          ))?></div>
         </div> <!--row-->
          
          <div class="row top10">
             <div class="col-sm-12 ">
             <?php 
             echo CHtml::textField('drop_address','',array(
               'class'=>'drop_address',
               'placeholder'=>Driver::t("Address"),
               //'required'=>true
             ))
             ?>
             </div> <!--col-->
          </div>
          
          </div> <!--dropoff_wrap-->
          
          
          <?php 
          $team_list=Driver::teamList( Driver::getUserType(),Driver::getUserId());
          if($team_list){
          	 $team_list=Driver::toList($team_list,'team_id','team_name',
          	   Driver::t("Select a team")
          	 );
          }          
          $all_driver=Driver::getAllDriver(
            Driver::getUserType(),Driver::getUserId()
          );   
          ?>          
          <h5 class="top20"><?php echo Driver::t("Select Team")?></h5>          
          <div class="top10 row">
          <div class="col-sm-12 ">
          <?php 
          echo CHtml::dropDownList('team_id','', (array)$team_list,array(
            'class'=>"task_team_id"
          ))
          ?>
          </div>
          </div>
                    
          <div class="assign-agent-wrap">
          <h5 class="top20"><?php echo Driver::t("Assign Agent")?></h5>
              <div class="col-sm-12 ">
	          <div class="top10 row">
	          <?php 
	          //echo CHtml::dropDownList('driver_id','',array())
	          ?>
	          <select name="driver_id" id="driver_id" class="driver_id">
	          <?php if(is_array($all_driver) && count($all_driver)>=1):?>
	            <option value=""><?php echo Driver::t("Select driver")?></option>
	            <?php foreach ($all_driver as $val):?>
	            <option class="<?php echo "team_opion option_".$val['team_id']?>" value="<?php echo $val['driver_id']?>">
	              <?php echo $val['first_name']." ".$val['last_name']?>
	            </option>
	            <?php endforeach;?>
	          <?php endif;?>
	          </select>
	          </div>
	          </div>
          </div>
         
         </div> <!--col-->
         
         <div class="col-md-6">
         
          <div class="map1">
            <div class="map_task"></div>
          </div>
          
          <div class="map2">
            <div class="map_dropoff"></div>
          </div>
          
         </div> <!--col-->
         
      </div> <!--row-->
      
       <div class="panel-footer top20">
       
         <button type="submit" class="orange-button medium rounded new-task-submit">
         <?php echo Driver::t("Submit")?>
         </button>
         
         <button type="button" data-id=".new-task" 
            class="close-modal green-button medium rounded"><?php echo Driver::t("Cancel")?></button>
        </div>
        
       </div> <!--panel-footer-->
      
      </form>

      </div> <!--body-->
    
    </div> <!--modal-content-->
  </div> <!--modal-dialog-->
</div> <!--modal-->