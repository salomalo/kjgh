<div id="listing" class="reviews" itemscope itemtype="http://schema.org/<?php echo $this->config->get('schema_type'); ?>">

<h1><?php echo JText::sprintf('COM_MTREE_USER_REVIEW'); ?></h1>
<h1><?php
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), 'itemprop="name"', array("edit"=>false,"delete"=>false) ) ?></h1>

<?php
$hide_title = true;
$hide_submitreview = true;
include $this->loadTemplate( 'sub_reviews.tpl.php' );	
?>

</div>