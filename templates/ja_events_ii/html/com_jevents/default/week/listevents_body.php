<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();

$this->data = $data = $this->datamodel->getWeekData($this->year, $this->month, $this->day);

$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();
$hasevents = false;

echo '<fieldset><legend class="ev_fieldset">' . JText::_('JEV_EVENTSFOR') . '&nbsp;' . JText::_('JEV_WEEK')
. ' : '.$data['startdate'] . ' - ' . $data['enddate'] .'</legend><br />' . "\n";
echo '<div class="ja-events-list row equal-height equal-height-child">' . "\n";

for( $d = 0; $d < 7; $d++ ){

	$day_link = '<a class="ev_link_weekday" href="' . $data['days'][$d]['link'] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">'
	. JEV_CommonFunctions::jev_strftime("%A", JevDate::mktime(3,0,0,$data['days'][$d]['week_month'], $data['days'][$d]['week_day'], $data['days'][$d]['week_year']))."<br/>"
	. JEventsHTML::getDateFormat( $data['days'][$d]['week_year'], $data['days'][$d]['week_month'], $data['days'][$d]['week_day'], 2 ).'</a>'."\n";

	if( $data['days'][$d]['today'])	$bg = 'ev_today';
	else $bg = '';

	$num_events		= count($data['days'][$d]['rows']);
	if ($num_events>0) {
            $hasevents = true;


		for( $r = 0; $r < $num_events; $r++ ){
			$row = $data['days'][$d]['rows'][$r];
      $link = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), $Itemid);
      $listyle = 'style="background-color:'.$row->bgcolor().';"';
			echo "<div class='col-sm-6 col-md-6 col-lg-4 col ".$bg."'><div class='inner'>";
			if ($row->get('imageimg1')) {
      ?>
      <div class="item-image">
          <div class="img-intro-left">
              <a href="<?php echo $link; ?>" title="<?php echo $row->title(); ?>" class="item-link">                                     
                  <?php 
                  if ($row->customfields["event_span"]["value"] >= 2) {
                      echo $row->get("imageimg1");
                  } else {
                      echo $row->get("thumbimg1"); 
                  }?>
              </a>
          </div>
      </div>
      <?php } ?>

      <p <?php echo $listyle; ?> class="category" <?php $category_url = JRoute::_("index.php?option=com_jevents&view=range&layout=listevents&Itemid=".$Itemid."&catids=".$row->catid.""); ?>                 
          <a href="<?php echo $category_url; ?> "> <?php echo $row->catname(); ?></a>
      </p>

      <div class="item-main clearfix">
          <!-- Item header -->
          <div class="header item-header clearfix">

              <?php
                  echo '<h2><a href="' . $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), $Itemid) . '">' . $row->title() . ' </a></h2>';
                  ?>
                  <dl class="article-info">
                      <dd>
                          <span class="icon-calendar"></span>
                          <?php 
                          echo $row->startDate();
                          ?>
                      </dd>
                      <dd>
                          <span class="icon-time"></span>
                          <?php 
                          echo JEVHelper::getTime($row->getUnixStartTime(), $row->hup(), $row->minup()) . ' - ';
                          echo JEVHelper::getTime($row->getUnixEndTime(), $row->hdn(), $row->mindn());
                          ?>
                      </dd>
                      <dd style="float:none;">
                          <span class="icon-globe"></span> <?php echo $row->location(); ?>
                      </dd>

                  </dl>

                  <?php
              
              ?>
          </div>

      </div>
		<?php	echo "</div></div>\n";
		}

	} else {
            echo "<div class='col-sm-12'><p>".JText::_('JEV_NOEVENT_ON_THIS_DATE')."</p></div>";
        }

} // end for days

echo '</div>' . "\n";
echo '</fieldset>' . "\n";