 <h1 class="contentheading"><?php
	if (!empty($this->error_title))
	{
		echo $this->error_title;
	} else {
		echo JText::_( 'COM_MTREE_ERROR' );
	}
?></h1>

<p /><strong><?php echo $this->error_msg ?></strong>