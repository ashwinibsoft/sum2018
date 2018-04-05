<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<?php echo $this->Html->image('/img/post/'.$post['Post']['post_image'],array('alt'=>'')); ?>
<br/>
<?php if($post['Post']['id']!=21){?>
<p><h2><?=$post['Post']['post_name'];?></h2></p>
<?php } ?>
<p><?php echo $this->ShortLink->show($post['Post']['description']);?></p>




