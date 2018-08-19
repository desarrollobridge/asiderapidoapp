<div id="chartdiv"></div>

<?php 
$new_data='';
$new_data_1='';
$total_ordered=0;

if (is_array($data) && count($data)>=1){
	foreach ($data as $val) {				
		//dump($val);			
		
		$total_ordered+=isset($val['total_order_amount'])?$val['total_order_amount']:0;
		
		if (isset($val['driver_name'])){			
			
			if ( $val['status']=="successful"){								
				$new_data[$val['driver_name']]['successful']=$val['total'];
				//$new_data[$val['driver_name']]['successful_total_order_amount']=$val['total_order_amount'];
			}
			if ( $val['status']=="failed"){								
				$new_data[$val['driver_name']]['failed']=$val['total'];
				//$new_data[$val['driver_name']]['failed_total_order_amount']=$val['total_order_amount'];
			}
			if ( $val['status']=="cancelled"){								
				$new_data[$val['driver_name']]['cancelled']=$val['total'];
				//$new_data[$val['driver_name']]['cancelled_total_order_amount']=$val['total_order_amount'];
			}			
						
		}
				
	}
	//dump($new_data);
	if(is_array($new_data) && count($new_data)>=1){
		foreach ($new_data as $driver_name=>$val) {
			$val['driver_name']=$driver_name;
			$new_data_1[]=$val;
		}
	}
}

/*dump($new_data_1);
die();*/
$datas=json_encode($new_data_1);
?>
<script type="text/javascript">
var chart = AmCharts.makeChart("chartdiv", {
    "type": "serial",
	"theme": "light",
    "legend": {    	
    	"align":"left"
        //"useGraphSettings": true
    },
    "dataProvider":<?php echo $datas;?>,
    "valueAxes": [{
        "stackType": "regular",
        "axisAlpha": 0.3,
        "gridAlpha": 0
    }],
    "graphs": [{
    	"lineColor": "#2c9f2c",
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0.3,
        "title": "<?php echo Driver::t("Successful")?>",
        "type": "column",		
        "valueField": "successful"
    }, {
    	"lineColor": "#e53935",
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0.3,
        "title": "<?php echo Driver::t("Failed")?>",
        "type": "column",
        "valueField": "failed"
    },{
    	"lineColor": "#f6bf00",
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0.3,
        "title": "<?php echo Driver::t("Cancelled")?>",
        "type": "column",		
        "valueField": "cancelled"
    }],
    "categoryField": "driver_name",
    "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha": 0,
        "gridAlpha": 0,
        "position": "left",
       "labelRotation": 45
    },
    "export": {
    	"enabled": true
     }

});

</script>