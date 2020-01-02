<?php
/*------------------------------------------------------------------------
# com_zhbaidumap - Zh BaiduMap
# ------------------------------------------------------------------------
# author:    Dmitry Zhuk
# copyright: Copyright (C) 2011 zhuk.cc. All Rights Reserved.
# license:   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
# website:   http://zhuk.cc
# Technical Support Forum: http://forum.zhuk.cc/
-------------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Map Types
$maptypes = $this->mapTypeList;
$apikey = $this->mapapikey;
$urlProtocol = "http";
if ($this->httpsprotocol != "")
{
	if ((int)$this->httpsprotocol == 0)
	{
		$urlProtocol = 'https';
	}
}	

if ($this->map_height != "")
{
    if ((int)$this->map_height == 0)
    {
        $map_height = "400px";
        $map_height_wrap = "450px";
    }
    else
    {
        $map_height = ((int)$this->map_height - 50) . "px";
        $map_height_wrap = (int)$this->map_height . "px";
    }
}
else 
{
    $map_height = "400px";
    $map_height_wrap = "450px";
}

$mainScriptMiddle = "";

if ($this->mapapiversion != "")
{
	if ($mainScriptMiddle == "")
	{
		$mainScriptMiddle = 'v='.$this->mapapiversion;
	}
	else
	{
		$mainScriptMiddle .= '&v='.$this->mapapiversion;
	}

}
// Load for contact translation
$currentLanguage = JFactory::getLanguage();
$currentLangTag = $currentLanguage->getTag();

$currentLanguage->load('com_contact', JPATH_ADMINISTRATOR, $currentLangTag, true);	

?>
<form action="<?php echo JRoute::_('index.php?option=com_zhbaidumap&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

<div class="span12 form-horizontal">

<div class="tabbable">
    <ul class="nav nav-pills">
                <li class="active"><a href="#tab0" data-toggle="tab"><?php echo JText::_( 'COM_ZHBAIDUMAP_MAPMARKER_MARKER' ); ?></a></li>
		<li><a href="#tab1" data-toggle="tab"><?php echo JText::_( 'COM_ZHBAIDUMAP_MAPMARKER_DETAIL' ); ?></a></li>
		<li><a href="#tab2" data-toggle="tab"><?php echo JText::_('COM_ZHBAIDUMAP_MAPMARKER_DETAIL_APPEARANCE'); ?></a></li>
                <li><a href="#tab6" data-toggle="tab"><?php echo JText::_('COM_ZHBAIDUMAP_MAPMARKER_DETAIL_WITHLABEL'); ?></a></li>
		<li><a href="#tab4" data-toggle="tab"><?php echo JText::_('COM_ZHBAIDUMAP_MAPMARKER_DETAIL_INTEGRATION'); ?></a></li>
		<li><a href="#tab5" data-toggle="tab"><?php echo JText::_('COM_ZHBAIDUMAP_MAPMARKER_DETAIL_ATTRIBUTES'); ?></a></li>
		<?php
		$fieldSets = $this->form->getFieldsets('params');
		foreach ($fieldSets as $name => $fieldSet) :
		?>
		<li><a href="#params-<?php echo $name;?>" data-toggle="tab"><?php echo JText::_($fieldSet->label);?></a></li>
		<?php endforeach; ?>
    </ul>
</div>
<div class="tab-content">
		<div class="tab-pane" id="tab0">
			<div>
				<fieldset class="adminform">
				
					<?php foreach($this->form->getFieldset('markermain') as $field): 
						
						if ($field->id == 'jform_mapid')
						{
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								array_unshift($this->mapList, JHTML::_('select.option', '', JText::_( 'COM_ZHBAIDUMAP_MAPMARKER_FILTER_MAP'), 'value', 'text')); 
								echo JHTML::_( 'select.genericlist', $this->mapList, 'jform[mapid]',  'class="inputbox  span5 required" size="1"', 'value', 'text', (int)$this->item->mapid, 'jform_mapid');
								//echo $field->label;
								//echo $field->input;
							?>
							</div>
							<?php 
						}
                                                 else
						{?>
						<div class="control-group">
						<?php 
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								echo $field->input;
							?>
							</div>
							<?php 
						?>
						</div>

						<?php }?>

					<?php endforeach; ?>
				
				</fieldset>
			
			</div>
                    
                        <div>

                                <div id="mapDivWrapper" class="row-fluid" style="margin:0;padding:0;width:100%;height:<?php echo $map_height_wrap ?>">


                                <div id="placesDivAC" class="row-fluid" style="margin:0;padding:0;width:100%;height:50px">
                                                <input id="searchTextField" type="text" class="span5" size="200">
                                                <?php  echo '  <button id="findAddressButton" onclick="Do_Find(); return false;">'.JText::_('COM_ZHBAIDUMAP_MAPMARKER_DETAIL_DOFINDBUTTON').'</button>'; ?>
                                </div>


                                <div id="BDMapsID" style="margin:0;padding:0;width:100%;height:<?php echo $map_height ?>">
                                   
                                <?php 

                                $document	= JFactory::getDocument();

                                $document->addStyleSheet(JURI::root() .'administrator/components/com_zhbaidumap/assets/css/admin.css');

                                $mapDefLat = $this->mapDefLat;
                                $mapDefLng = $this->mapDefLng;

                                $mapMapTypeBaidu = $this->mapMapTypeBaidu;
                                $mapMapTypeOSM = $this->mapMapTypeOSM;
                                $mapMapTypeCustom = $this->mapMapTypeCustom;

                                //Script begin
                                $scripttext = '<script type="text/javascript" >//<![CDATA[' ."\n";

                                        $scripttext .= 'var initialLocation;' ."\n";
                                        $scripttext .= 'var initialZoom;' ."\n";
                                        $scripttext .= 'var spblocation;' ."\n";
                                        $scripttext .= 'var browserSupportFlag =  new Boolean();' ."\n";
                                        $scripttext .= 'var map;' ."\n";
                                        $scripttext .= 'var infowindow;' ."\n";
                                        $scripttext .= 'var marker;' ."\n";
                                        $scripttext .= 'var geocoder;' ."\n";
                                        $scripttext .= 'var inputPlacesAC;' ."\n";


                                        $scripttext .= 'function initialize() {' ."\n";

                                        $scripttext .= 'infowindow = new BMap.InfoWindow();' ."\n";

                                        $scripttext .= 'geocoder = new BMap.Geocoder();'."\n";

                                        if ($mapDefLat != "" && $mapDefLng !="")
                                        {
                                                $scripttext .= 'spblocation = new BMap.Point('.$mapDefLng.', '.$mapDefLat.');' ."\n";
                                        }
                                        else
                                        {
                                                $scripttext .= 'spblocation = new BMap.Point(116.404, 39.915);' ."\n";
                                        }
                                        $scripttext .= 'initialZoom = 14;' ."\n";


                                        $curr_maptype_list = '';


                                    if ((int)$mapMapTypeBaidu != 0
                                          || ((int)$mapMapTypeCustom == 0
                                             && (int)$mapMapTypeOSM == 0))
                                        {
                                                $curr_maptype_list .= '	  BMAP_NORMAL_MAP,' ."\n";
                                                $curr_maptype_list .= '	  BMAP_PERSPECTIVE_MAP,' ."\n";
                                                $curr_maptype_list .= '	  BMAP_SATELLITE_MAP,' ."\n";
                                                $curr_maptype_list .= '	  BMAP_HYBRID_MAP' ."\n";
                                        }


                                         $scripttext .= 'var mpTypes = ['.$curr_maptype_list.'];'."\n";


                                    $scripttext .= '  map = new BMap.Map(document.getElementById("BDMapsID"));' ."\n";

                                        //$scripttext .= 'map.setMapType(mapTypeOSM);';
                                        //$scripttext .= 'map.addTileLayer(tileLayerOSM);';

                                    $scripttext .= '  map.addControl(new BMap.MapTypeControl(mpTypes));' ."\n";

                                        $scripttext .= '  inputPlacesAC = document.getElementById(\'searchTextField\');' ."\n";

                                        if (isset($this->item->latitude) && isset($this->item->longitude)
                                        && ($this->item->latitude != "") && ($this->item->longitude !="")
                                         )
                                        {
                                                $scripttext .= 'initialLocation = new BMap.Point('.$this->item->longitude.', ' .$this->item->latitude.');' ."\n";
                                            $scripttext .= '  map.centerAndZoom(initialLocation, initialZoom);' ."\n";

                                                $scripttext .= '  marker = new BMap.Marker(initialLocation, {' ."\n";
                                                $scripttext .= '      enableDragging:true, ' ."\n";
                                                // Replace to new, because all charters are shown
                                                //$scripttext .= '      title:"'.htmlspecialchars(str_replace('\\', '/', $this->item->title) , ENT_QUOTES, 'UTF-8').'"' ."\n";		
                                                $scripttext .= '      title:"'.str_replace('\\', '/', str_replace('"', '\'\'', $this->item->title)).'"' ."\n";
                                                $scripttext .= '  });'."\n";

                                                $scripttext .= '    marker.addEventListener(\'dragend\', function(event) {' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_longitude.value = event.point.lng;' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_latitude.value = event.point.lat;' ."\n";
                                                $scripttext .= '    });' ."\n";

                                                $scripttext .= '    map.addEventListener(\'click\', function(event) {' ."\n";
                                                $scripttext .= '      marker.setPosition(event.point);' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_longitude.value = event.point.lng;' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_latitude.value = event.point.lat;' ."\n";
                                                $scripttext .= '    });' ."\n";

                                            $scripttext .= '  map.addOverlay(marker);' ."\n";

                                        $scripttext .= '  map.setCenter(initialLocation);' ."\n";
                                                $scripttext .= '  marker.setPosition(initialLocation);' ."\n";

                                        }
                                        else
                                        {
                                          if (isset($this->item->addresstext) and ($this->item->addresstext!= ""))
                                          {
                                                $scripttext .= '  geocoder.getPoint( "'.$this->item->addresstext.'", function(point) {'."\n";
                                                $scripttext .= '  if (point) {'."\n";
                                                $scripttext .= '    initialLocation = new BMap.Point(point.lng, point.lat);' ."\n";
                                                //$scripttext .= '    alert("Geocode was successful");'."\n";
                                                //$scripttext .= '    alert("latlng="+latlng'. $currentmarker->id.');'."\n";
                                            $scripttext .= '    map.centerAndZoom(initialLocation, initialZoom);' ."\n";

                                                $scripttext .= '  marker = new BMap.Marker(initialLocation, {' ."\n";
                                                $scripttext .= '      enableDragging:true, ' ."\n";
                                                // Replace to new, because all charters are shown
                                                //$scripttext .= '      title:"'.htmlspecialchars(str_replace('\\', '/', $this->item->title) , ENT_QUOTES, 'UTF-8').'"' ."\n";		
                                                $scripttext .= '      title:"'.str_replace('\\', '/', str_replace('"', '\'\'', $this->item->title)).'"' ."\n";
                                                $scripttext .= '  });'."\n";

                                                $scripttext .= '    marker.addEventListener(\'dragend\', function(event) {' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_longitude.value = event.point.lng;' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_latitude.value = event.point.lat;' ."\n";
                                                $scripttext .= '    });' ."\n";

                                                $scripttext .= '    map.addEventListener(\'click\', function(event) {' ."\n";
                                                $scripttext .= '      marker.setPosition(event.point);' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_longitude.value = event.point.lng;' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_latitude.value = event.point.lat;' ."\n";
                                                $scripttext .= '    });' ."\n";

                                            $scripttext .= '  map.addOverlay(marker);' ."\n";

                                        $scripttext .= '  map.setCenter(initialLocation);' ."\n";
                                                $scripttext .= '  marker.setPosition(initialLocation);' ."\n";

                                                $scripttext .= '  }'."\n";
                                                $scripttext .= '  else'."\n";
                                                $scripttext .= '  {'."\n";
                                                $scripttext .= '    alert("'.JText::_('COM_ZHBAIDUMAP_MAPMARKER_GEOCODING_ERROR_REASON').': " + "status" + "\n" + "'.JText::_('COM_ZHBAIDUMAP_MAPMARKER_GEOCODING_ERROR_ADDRESS').': '.$this->item->addresstext.'");'."\n";
                                                $scripttext .= '    initialLocation = spblocation;' ."\n";

                                            $scripttext .= '  map.centerAndZoom(initialLocation, initialZoom);' ."\n";

                                                $scripttext .= '  marker = new BMap.Marker(initialLocation, {' ."\n";
                                                $scripttext .= '      enableDragging:true, ' ."\n";
                                                // Replace to new, because all charters are shown
                                                //$scripttext .= '      title:"'.htmlspecialchars(str_replace('\\', '/', $this->item->title) , ENT_QUOTES, 'UTF-8').'"' ."\n";		
                                                $scripttext .= '      title:"'.str_replace('\\', '/', str_replace('"', '\'\'', $this->item->title)).'"' ."\n";
                                                $scripttext .= '  });'."\n";

                                                $scripttext .= '    marker.addEventListener(\'dragend\', function(event) {' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_longitude.value = event.point.lng;' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_latitude.value = event.point.lat;' ."\n";
                                                $scripttext .= '    });' ."\n";

                                                $scripttext .= '    map.addEventListener(\'click\', function(event) {' ."\n";
                                                $scripttext .= '      marker.setPosition(event.point);' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_longitude.value = event.point.lng;' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_latitude.value = event.point.lat;' ."\n";
                                                $scripttext .= '    });' ."\n";

                                            $scripttext .= '  map.addOverlay(marker);' ."\n";

                                        $scripttext .= '  map.setCenter(initialLocation);' ."\n";
                                                $scripttext .= '  marker.setPosition(initialLocation);' ."\n";

                                                $scripttext .= '  }'."\n";
                                                $scripttext .= '});'."\n";
                                          }
                                          else
                                          {
                                                $scripttext .= 'initialLocation = spblocation;' ."\n";
                                        $scripttext .= '    map.centerAndZoom(initialLocation, initialZoom);' ."\n";
                                                $scripttext .= '    marker = new BMap.Marker(initialLocation, {' ."\n";
                                                $scripttext .= '      enableDragging:true, ' ."\n";
                                                // Replace to new, because all charters are shown
                                                //$scripttext .= '      title:"'.htmlspecialchars(str_replace('\\', '/', $this->item->title) , ENT_QUOTES, 'UTF-8').'"' ."\n";		
                                                $scripttext .= '      title:"'.str_replace('\\', '/', str_replace('"', '\'\'', $this->item->title)).'"' ."\n";
                                                $scripttext .= '    });'."\n";

                                                $scripttext .= '    marker.addEventListener(\'dragend\', function(event) {' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_longitude.value = event.point.lng;' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_latitude.value = event.point.lat;' ."\n";
                                                $scripttext .= '    });' ."\n";

                                                $scripttext .= '    map.addEventListener(\'click\', function(event) {' ."\n";
                                                $scripttext .= '      marker.setPosition(event.point);' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_longitude.value = event.point.lng;' ."\n";
                                                $scripttext .= '      document.forms.adminForm.jform_latitude.value = event.point.lat;' ."\n";
                                                $scripttext .= '    });' ."\n";

                                            $scripttext .= '  map.addOverlay(marker);' ."\n";

                                        $scripttext .= '  map.setCenter(initialLocation);' ."\n";
                                                $scripttext .= '  marker.setPosition(initialLocation);' ."\n";

                                                $scripttext .= '  var geolocation = new BMap.Geolocation();' ."\n";
                                                $scripttext .= '  geolocation.getCurrentPosition(function(r){' ."\n";
                                                $scripttext .= '    if(this.getStatus() == BMAP_STATUS_SUCCESS){' ."\n";
                                                $scripttext .= '      initialLocation = new BMap.Point(r.point.lng, r.point.lat);' ."\n";
                                        $scripttext .= '      map.setCenter(initialLocation);' ."\n";
                                                $scripttext .= '      marker.setPosition(initialLocation);' ."\n";
                                                //$scripttext .= '    alert(\'detected\'+r.point.lng+\',\'+r.point.lat);' ."\n";
                                                $scripttext .= '    }' ."\n";
                                                $scripttext .= '    else {' ."\n";
                                                $scripttext .= '      initialLocation = spblocation;' ."\n";
                                                //$scripttext .= '    alert(\'failed\'+this.getStatus());' ."\n";
                                        $scripttext .= '      map.setCenter(initialLocation);' ."\n";
                                                $scripttext .= '      marker.setPosition(initialLocation);' ."\n";
                                                $scripttext .= '    }' ."\n";
                                                $scripttext .= '  });'."\n";



                                          }
                                        }


                                        $scripttext .= '  var ac = new BMap.Autocomplete(' ."\n";
                                        $scripttext .= '  	{"input" : "searchTextField"' ."\n";
                                        $scripttext .= '  	,"location" : map' ."\n";
                                        $scripttext .= '  });' ."\n";
                                        $scripttext .= '  ac.addEventListener("onconfirm", function(e) {' ."\n";
                                        $scripttext .= '    var ac_value = e.item.value;' ."\n";
                                        $scripttext .= '    myACValue = ac_value.province +  ac_value.city +  ac_value.district +  ac_value.street +  ac_value.business;' ."\n";
                                        $scripttext .= '    setACPlace();' ."\n";
                                        $scripttext .= '  });' ."\n";

                                        $scripttext .= '  function setACPlace(){' ."\n";
                                        $scripttext .= '      function acFunction(){' ."\n";
                                        $scripttext .= '          latlngAC = local.getResults().getPoi(0).point;' ."\n";
                                        $scripttext .= '          marker.setPosition(latlngAC);' ."\n";
                                        $scripttext .= '          marker.setTitle(local.getResults().getPoi(0).address);' ."\n";
                                        $scripttext .= '          map.setCenter(latlngAC);' ."\n";
                                        $scripttext .= '          map.setZoom(17);' ."\n";
                                        // For normal render marker after move
                                        //$scripttext .= '  map.addOverlay(marker);' ."\n";
                                        $scripttext .= '          document.forms.adminForm.jform_longitude.value = latlngAC.lng;' ."\n";
                                        $scripttext .= '          document.forms.adminForm.jform_latitude.value = latlngAC.lat;' ."\n";
                                        $scripttext .= '      };' ."\n";
                                        $scripttext .= '      var local = new BMap.LocalSearch(map, {' ."\n";
                                        $scripttext .= '        onSearchComplete: acFunction' ."\n";
                                        $scripttext .= '      });' ."\n";
                                        $scripttext .= '      local.search(myACValue);' ."\n";
                                        $scripttext .= '  };' ."\n";


                                    $scripttext .= '  map.enableDoubleClickZoom();' ."\n";
                                    $scripttext .= '  map.addControl(new BMap.ScaleControl());' ."\n";
                                    $scripttext .= '  map.addControl(new BMap.OverviewMapControl());' ."\n";
                                    $scripttext .= '  map.addControl(new BMap.NavigationControl());' ."\n";

                                // end initialize	
                                $scripttext .= '};' ."\n";

                                $scripttext .= 'function loadScript() {' ."\n";
                                $scripttext .= '  var script = document.createElement("script");' ."\n";
                                $scripttext .= '  script.type = "text/javascript";' ."\n";
                                $scripttext .= '  script.src = "'.$urlProtocol.'://api.map.baidu.com/api?&ak='.$apikey.$mainScriptMiddle.'&callback=initialize";' ."\n";
                                $scripttext .= '  document.body.appendChild(script);' ."\n";
                                $scripttext .= '};' ."\n";


                                        // Find button
                                        $scripttext .= 'function Do_Find() {'."\n";
                                        $scripttext .= '  geocoder.getPoint( inputPlacesAC.value , function(point) {'."\n";
                                        $scripttext .= '  if (point) {'."\n";
                                        $scripttext .= '    var latlngFind = point;' ."\n";

                                        $scripttext .= '  marker.setPosition(latlngFind);' ."\n";
                                        $scripttext .= '  marker.setTitle(inputPlacesAC.value);' ."\n";

                                        $scripttext .= '  map.setCenter(latlngFind);' ."\n";
                                        $scripttext .= '  map.setZoom(17);' ."\n";


                                        // For normal render marker after move
                                        //$scripttext .= '  map.addOverlay(marker);' ."\n";
                                        $scripttext .= '  document.forms.adminForm.jform_longitude.value = latlngFind.lng;' ."\n";
                                        $scripttext .= '  document.forms.adminForm.jform_latitude.value = latlngFind.lat;' ."\n";

                                        $scripttext .= '  }'."\n";
                                        $scripttext .= '  else'."\n";
                                        $scripttext .= '  {'."\n";
                                        $scripttext .= '    alert("'.JText::_('COM_ZHBAIDUMAP_MAPMARKER_GEOCODING_ERROR_REASON').': " + "status" + "\n" + "'.JText::_('COM_ZHBAIDUMAP_MAPMARKER_GEOCODING_ERROR_ADDRESS').': "+inputPlacesAC.value);'."\n";
                                        $scripttext .= '  }'."\n";
                                        $scripttext .= '}, "");'."\n";

                                        $scripttext .= '};' ."\n";

                                $scripttext .= 'window.onload = loadScript;' ."\n";

                                $scripttext .= '//]]></script>' ."\n";
                                // Script end

                                echo $scripttext;


                                ?>
                                </div>
                                <?php
                                        $credits ='<div>'."\n";
                                    if ((int)$mapMapTypeOSM != 0)
                                        {
                                                $credits .= 'OSM '.JText::_('COM_ZHBAIDUMAP_MAP_POWEREDBY').': ';
                                                $credits .= '<a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a>'."\n";
                                        }
                                        $credits .='</div>'."\n";
                                echo $credits;
                                ?>


                                </div>

                        </div>
		</div>    
    
    
	<div class="tab-pane active" id="tab1">
		<fieldset class="adminform">
				<?php foreach($this->form->getFieldset('details') as $field): ?>
				<div class="control-group">
					<?php 
						if ($field->id == 'jform_markergroup')
						{
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								array_unshift($this->markerGroupList, JHTML::_('select.option', '', JText::_( 'COM_ZHBAIDUMAP_MAPMARKER_FILTER_PLACEMARK_GROUP'), 'value', 'text')); 
								echo JHTML::_( 'select.genericlist', $this->markerGroupList, 'jform[markergroup]',  'class="inputbox span5" size="1"', 'value', 'text', (int)$this->item->markergroup, 'jform_markergroup');
								//echo $field->label;
								//echo $field->input;
							?>
							</div>
							<?php 
						}
						else if ($field->id == 'jform_descriptionhtml')
						{
							?>
							<div class="control-label">
							<?php 
								echo '<div class="clr"></div>';
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								echo '<div class="clr"></div>';
								echo $field->input;
							?>
							</div>
							<?php 
						}
						else
						{
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								echo $field->input;
							?>
							</div>
							<?php 
						}
						?>
				</div>
				<?php endforeach; ?>

			
		</fieldset>
	
       
        </div>
	<div class="tab-pane" id="tab2">
		
		<fieldset class="adminform">
			
				<?php foreach($this->form->getFieldset('markeradvanced') as $field): ?>
				<div class="control-group">
					<?php 
						if ($field->id == 'jform_icontype')
						{
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 

								$imgpath = JURI::root() .'administrator/components/com_zhbaidumap/assets/icons/';
								$utilspath = JURI::root() .'administrator/components/com_zhbaidumap/assets/utils/';

								$iconTypeJS = " onchange=\"javascript:
								if (document.forms.adminForm.jform_icontype.options[selectedIndex].value!='') 
								{document.image.src='".$imgpath."' + document.forms.adminForm.jform_icontype.options[selectedIndex].value.replace(/#/g,'%23') + '.png'}
								else 
								{document.image.src=''}\"";


								$scriptPosition = ' name=';

								echo str_replace($scriptPosition, $iconTypeJS.$scriptPosition, $field->input);
								echo '<img name="image" src="'.$imgpath .str_replace("#", "%23", $this->item->icontype).'.png" alt="" />';

								echo '<div class="clr"></div>';
								echo '<a class="btn btn-primary" href="http://wiki.zhuk.cc/index.php?title=Zh_BaiduMap_Credits_Icons" target="_blank">'.JText::_( 'COM_ZHBAIDUMAP_MAP_TERMSOFUSE_ICONS' ).' <img src="'.$utilspath.'info.png" alt="'.JText::_( 'COM_ZHBAIDUMAP_MAP_TERMSOFUSE_ICONS' ).'" style="margin: 0;" /></a>';
								echo '<div class="clr"></div>';
								echo '<br />';
							?>
							</div>
							<?php 
						}
						else if ($field->id == 'jform_ordering')
						{
						}                                                
						else
						{
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								echo $field->input;
							?>
							</div>
							<?php 
						}
						?>
				</div>
				<?php endforeach; ?>
			
		</fieldset>
	</div>
	<div class="tab-pane" id="tab6">
		
		<fieldset class="adminform">
			
				<?php foreach($this->form->getFieldset('markerwithlabel') as $field): ?>
				<div class="control-group">
					<?php 
						?>
						<div class="control-label">
						<?php 
							echo $field->label;
						?>
						</div>
						<div class="controls">
						<?php 
							echo $field->input;
						?>
						</div>
						<?php 
					?>
				</div>
				<?php endforeach; ?>
			
		</fieldset>
	</div>	
	
        <div class="tab-pane" id="tab4">
		
		<fieldset class="adminform">
			
				<?php foreach($this->form->getFieldset('integration') as $field): ?>
				<div class="control-group">
					<?php 
						if ($field->id == 'jform_contactid')
						{
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								//array_unshift($this->contactList, JHTML::_('select.option', '', JText::_( 'COM_ZHBAIDUMAP_MAPMARKER_FILTER_CONTACT'), 'value', 'text')); 
								//echo JHTML::_( 'select.genericlist', $this->contactList, 'jform[contactid]',  'class="inputbox span5" size="1"', 'value', 'text', (int)$this->item->contactid, 'jform_contactid');
								echo $field->input;
							?>
							</div>
							<?php 
						}
						/*
						By default datatype now not text - user
						else if ($field->id == 'jform_createdbyuser')
						{
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								array_unshift($this->userList, JHTML::_('select.option', '', JText::_( 'COM_ZHBAIDUMAP_MAPMARKER_FILTER_USER'), 'value', 'text')); 
								echo JHTML::_( 'select.genericlist', $this->userList, 'jform[createdbyuser]',  'class="inputbox span5" size="1"', 'value', 'text', (int)$this->item->createdbyuser, 'jform_createdbyuser');
								//echo $field->label;
								//echo $field->input;
							?>
							</div>
							<?php 
						}
						*/
						else
						{
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								echo $field->input;
							?>
							</div>
							<?php 
						}
						?>
				</div>
				<?php endforeach; ?>

				<?php foreach($this->form->getFieldset('integration_article') as $field): ?>
				<div class="control-group">
					<?php 
						if ($field->id == 'jform_articleid')
						{
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								echo $field->input;
							?>
							</div>
							<?php 
						}
						else
						{
							?>
							<div class="control-label">
							<?php 
								echo $field->label;
							?>
							</div>
							<div class="controls">
							<?php 
								echo $field->input;
							?>
							</div>
							<?php 
						}
						?>
				</div>
				<?php endforeach; ?>                    
		</fieldset>
	</div>
	<div class="tab-pane" id="tab5">
		
		<fieldset class="adminform">
			
				<?php foreach($this->form->getFieldset('extraattributes') as $field): ?>
				<div class="control-group">
					<?php 
						?>
						<div class="control-label">
						<?php 
							echo $field->label;
						?>
						</div>
						<div class="controls">
						<?php 
							echo $field->input;
						?>
						</div>
						<?php 
					?>
				</div>
				<?php endforeach; ?>
			
		</fieldset>
	</div>

	<?php echo $this->loadTemplate('params'); ?>

</div>


</div>


<div class="row-fluid">
	<input type="hidden" name="task" value="mapmarker.edit" />
	<?php echo JHtml::_('form.token'); ?>
</div>


</div>

</form>


