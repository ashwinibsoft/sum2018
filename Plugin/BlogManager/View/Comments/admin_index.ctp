<div class="row">
	<div class="col-md-12">
		<form method="post" style="display:inline;">
		<div class="col-md-1 right-col">
			<button type="submit" class="btn btn-primary btn-sm">
					<i class="fa fa-search"></i>
				</button>
		</div>
		<div class="col-md-2 right-col">
				<?php 
					echo $this->Form->input('limit',array('options'=>array('20'=>20,'50'=>50,'100'=>100,'999'=>'All'),'label' => false,'div'=>false,'class'=>'simple-dropdown','style'=>'width:100%','empty'=>'Results: 10','value'=>$limit));
				?>
			</div>
			<div class="col-md-2 right-col">
				<input type="text" class="form-control" name="search" value="<?=$search?>" placeholder="Search" id="search">
			</div>
		</form>
		<div style="clear:both;"></div>
	</div>
</div>
<?php echo $this->element('admin/message'); ?>
<?=$this->Form->create('Comment', array('name' => 'comments', 'action' => 'delete/' , 'id' => 'CommentDeleteForm', 'onSubmit' => 'return validate(this)', 'class' => 'table-form')); ?>
<?=$this->Form->hidden('action', array('id' => 'action', 'value' => '')); ?>
<?=$this->Form->hidden('redirect', array('value' => $url)); ?>
<div class="row">
	<div class="col-md-12">
		<div class="grid simple horizontal green no-margin-grid">
			<div class="grid-title no-border " style="padding: 0px 15px 0px;"></div>
			<div class="grid-body no-border">
				<div style="margin-top:5px;"></div>
				<table class="table table-bordered no-more-tables table-hover table-content">
					<thead style="background-color: #d0d0d0;">
						<tr>
							<th style="width:3%">
								<div class="checkbox check-default">
									<?= $this->Form->input('check', array('type'=>'checkbox','value' => 1, 'onchange' => "CheckAll(this.value)",'label'=>'','class'=>'checkall')); ?>
								</div>
							</th>
							<th class="h-align-center" style="width:7%">S.No.</th>
							
							<th style="width:30%">Auther</th>
							<th style="width:20%">Comments</th>
							<th style="width:20%">In Response To</th>
							<th style="width:20%">Appvove</th>
							<th class="h-align-center" style="width:10%">Status</th>
							<th class="h-align-center" style="width:20%">Action</th>
						</tr>
					</thead>
					<?php if(!empty($comments)){ ?>
					<tbody>
						<?php
							$i = $this->Paginator->counter('{:start}');
							foreach ($comments as $comment) {
							$j=$i;	
						?>
						<tr id="sort_<?=$comment['Comment']['id'] ?>" style="">
							<td class="v-align-middle">
								<div class="checkbox check-success">
								<?php echo $this->Form->input('Comment.id.'.$i, array('type'=>'checkbox','value' => $comment['Comment']['id'],'class'=>'checkrow')); ?>
								</div>
							</td>
							
							<td class="v-align-middle h-align-center"><?=$i++; ?></td>
							<td class="v-align-middle"><span class="muted"> 
								<?php 
								/* Resize Image */ 
								if(isset($comment['Comment']['profile_image'])) {
									$imgArr = array('source_path'=>Configure::read('Path.Profile'),'img_name'=>$comment['Comment']['profile_image'],'width'=>Configure::read('image_list_width'),'height'=>Configure::read('image_list_height'),'noimg'=>Configure::read('Path.NoProfile'));
									$resizedImg = $this->ImageResize->ResizeImage($imgArr);
									echo $this->Html->image($resizedImg);
							}
							?>
								<?php
								 echo ' <b>'.$comment['Comment']['sub_name'].'<br></b>'; 
							     echo $comment['Comment']['sub_email'].'<br>';
							     echo $comment['Comment']['sub_ip'];  
								?>
								
								</span></td>
							<td class="v-align-middle"><span class="muted">
								
								<?php 
								echo '<b>'.$comment['Comment']['comment_title'].'<br></b>'; 
								echo $comment['Comment']['comment'].'<br>'; 
								?>
								
								</span></td>						
							<td class="v-align-middle"><span class="muted">
								
								<?php 
								echo $comment['Comment']['post_name'];  
								?>
								
								</span></td>
							<td class="v-align-middle h-align-center">
								<span class="muted">
								<?php
									if ($comment['Comment']['approve'] == '1'){
											echo"<i class='fa fa-check tip link-color' title='' data-toggle='tooltip' data-original-title='Approved'></i>";
									}
									else{
											echo "<i class='fa fa-times link-color' title='' data-toggle='tooltip' data-original-title='disapprove'></i>";
									}
								?>
								</span>
							</td>
							<td class="v-align-middle h-align-center">
								<span class="muted">
								<?php
									if ($comment['Comment']['status'] == '1'){
											echo"<i class='fa fa-check tip link-color' title='' data-toggle='tooltip' data-original-title='Publish'></i>";
									}
									else if ($comment['Comment']['status'] == '2'){
											echo "<i class='fa fa-save link-color' title='' data-toggle='tooltip' data-original-title='Unublish'></i>";
									}else{
											echo "<i class='fa fa-times link-color' title='' data-toggle='tooltip' data-original-title='Unublish'></i>";
									}
								?>
								</span>
							</td>
							<td class="v-align-middle h-align-center">
									<a data-toggle="modal" data-id="<?=$comment['Comment']['id'];?>" class="tip link-color preview" data-original-title="View"  href="#viewDetail_<?=$j;?>">
									<i class="fa fa-eye"></i></a>
							</td>
						</tr>
						<?php }?>
					</tbody>
					<?php } ?>
				</table>
				<?php if (!$comments) { ?>
				<div style="margin-top:20px;"></div>
					<div class="row">
						<div class="alert alert-block alert-info ">
							<h4 class="alert-heading"><i class="icon-warning-sign"></i> No Comments Found !</h4>
							<div class="button-set">
								<?php if(!empty($search)){ ?>
								<?php echo $this->Html->link('Show all', array('controller' => 'comments', 'action' => 'index','_blank',999), array('escape' => false, 'class' => 'btn btn-white btn-cons')); ?>
								<?php } ?>
							</div>
						</div>
					</div>
				<?php }else{ ?>
				
					<div class="row">
						<div class="col-md-12">
							<div class="dataTables_paginate paging_bootstrap pagination">
								
							<?php if($this->Paginator->hasPrev()){?>
							<span class="pagin btn btn-white">
							<?=$this->Paginator->prev('<i class="fa fa-chevron-left"></i> ',array('escape' => false, 'disabledTag' => 'a'));?>
							</span>
							<?php } ?>
							
							<?=$this->Paginator->numbers(array('modulus'=>6,'type'=>'button','separator' => '','class'=>'pagin btn btn-white ','currentClass' => 'active')); ?>

							<?php if($this->Paginator->hasNext()){?>
							<span class="pagin btn btn-white">
							<?=$this->Paginator->next('<i class="fa fa-chevron-right"></i>',array('escape' => false, 'disabledTag' => 'a'));?>
							</span>
							<?php } ?>
							</div>
							
							
					</div>
					
				<?php }?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
<div style="bottom: -115px;" id="quick-access" class="admin-bar">
		<div class="admin-bar-inner">
			<div class="simple-chat-popup chat-menu-toggle hide animated fadeOut">
				<div class="simple-chat-popup-arrow"></div><div class="simple-chat-popup-inner">
					 <div style="width:100px">
					 <div class="semi-bold">David Nester</div>
					 <div class="message">Hey you there </div>
					</div>
				</div>
			</div>
			<div class="btn-group dropup">
				<a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-demo-space"> Select action 
					<span class="caret"></span> 
				</a>
				<ul class="dropdown-menu ">
					<li>
						<a href="javascript:" onClick="return formsubmit('Publish');">Publish</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="javascript:" onClick="return formsubmit('Unpublish');">Unpublish</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="javascript:" onClick="return formsubmit('Delete');">Delete</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="javascript:" onClick="return formsubmit('Approve');">Approve</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="javascript:" onClick="return formsubmit('Disapprove');">Disapprove</a>
					</li>
				</ul>
			</div>
			<button type="button" class="btn btn-white  btn-cancel">Cancel</button>
		</div>
	</div>

<?php $i = $this->Paginator->counter('{:start}'); ?>
<?php foreach ($comments as $comment) { ?>
<?php  $j = $i++; ?>	
<!-- Modal -->
<div class="modal fade" id="viewDetail_<?=$j;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<br>
			<i class="icon-credit-card icon-7x"></i>
			<h4 id="myModalLabel" class="semi-bold"><?php echo $comment['Comment']['comment_title']; ?></h4>
			<br>
		</div>
		<!--start page content here for popup-->
		<div class="modal-body">
			<div class="row form-row">
				<div class="col-md-6">
					<b>comment Title</b>	
					<?php echo $comment['Comment']['comment_title']; ?>
				</div>
				
			</div>
	
		<div class="row form-row">
			<div class="col-md-12">
				 <?php 
								/* Resize Image */ 
								if(isset($comment['Comment']['profile_image'])) {
									$imgArr = array('source_path'=>Configure::read('Path.Profile'),'img_name'=>$comment['Comment']['profile_image'],'width'=>Configure::read('image_list_width'),'height'=>Configure::read('image_list_height'),'noimg'=>Configure::read('Path.NoProfile'));
									$resizedImg = $this->ImageResize->ResizeImage($imgArr);
									echo $this->Html->image($resizedImg);
							}
							?>
							<b> Auther name </b>
				<?php
					 echo $comment['Comment']['sub_name'].'<br>'; 
					
				     echo '<b>Auther email </b>'.$comment['Comment']['sub_email'].'<br>';
				     echo '<b>Ather ip address </b>'.$comment['Comment']['sub_ip'].'<br>';  
					 ?>		
					<?php 
					echo '<b>comment Title </b>'.$comment['Comment']['comment_title'].'<br>'; 
					echo '<b>comment </b>'.$comment['Comment']['comment'].'<br>'; 
					?>
					
								
					<?php 
					echo '<b>In Response To </b>'.$comment['Comment']['post_name'].'<br>';  
					?>
			</div>
		</div>	
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
	<!--end page content here for popup-->
	</div>
	<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<?php } ?>



<script type="text/javascript">
    function formsubmit(action)
    {
        var flag = true;
 
        if (flag)
        {
            document.getElementById('action').value = action;
            if (validate())
            document.getElementById('CommentDeleteForm').submit();
        }
    }

    function validate() {
        var ans = "0";
        for (i = 0; i < document.comments.elements.length; i++) {
            if (document.comments.elements[i].type == "checkbox") {
                if (document.comments.elements[i].checked) {
					 
                    ans = "1";
                    break;
	
                }
            }
        }
        if (ans == "0") {
            alert("Please select comment(s) to " + document.getElementById('action').value.toLowerCase());
            return false;
        } else {
            var answer = confirm('Are you sure you want to ' + document.getElementById('action').value.toLowerCase() + ' this comment(s)?');
            if (!answer)
                return false;
        }
        return true;
    }

    function CheckAll(chk)
    {
        var fmobj = document.getElementById('CommentDeleteForm');
        for (var i = 0; i < fmobj.elements.length; i++)
        {
            var e = fmobj.elements[i];
            if (e.type == 'checkbox')
                fmobj.elements[i].checked = document.getElementById('CommentCheck').checked;
        }

    }
    
</script>

<!--
<script type='text/javascript'>
    $(function(){
      //Keep track of last scroll
      var lastScroll = 0;
      var loading_start = 0;
      var page = <?=$this->paginator->counter('{:page}')?>;
      var pages = <?=$this->paginator->counter('{:pages}')?>;
      $(window).bind('scroll',function(event){
           //Sets the current scroll position
			var st = $(this).scrollTop();
			var win_height = $(this).height();
			var doc_height = $(document).height();
			var scrollBottom = doc_height - win_height - st;
			var scroll_value=200;
			if((scrollBottom <= scroll_value) && (pages >= (page+1))){
                if(loading_start===0){
                    loading_start = 1;
                    page++;
                    $('#loader').show();
                    $.ajax({
                        url:'<?//=Router::url(array('plugin'=>'event_manager','controller'=>'event_comments','action'=>'index',$search,'page:'));?>'+page,
                        async:false,
                        success:function(data){
                            $('tbody').append(data);
                            loading_start = 0;
                            $('#loader').hide();
                        }
                    });
                }
            }
				
			lastScroll = st;
      });
      
    });
</script>
-->
<script type="text/javascript">
	$(document).ready(function(){
		$("a.preview").click(function(){
			//alert();
			$($(this).attr("href")).modal('show');
			return false;
		});
        $( "tbody" ).sortable({
			//placeholder: "ui-state-highlight",
			opacity: 0.6,
            update: function(event, ui) {
                var info = $(this).sortable("serialize");
                $.ajax({
                    type: "POST",
                    url: "<?php echo Router::url(array('plugin'=>'blog_manager','controller'=>'blog_comments','action'=>'ajax_sort','admin'=>false)); ?>",
                    data: info,
                    context: document.body,
                    success: function(){
                        
                       // alert("cool");
                    }
              });
            }
        });
        $( "tbody" ).disableSelection();         
    });
</script>

<script type="text/javascript">
		$(document).ready(function(){
				$('#search').focus(function(){
				$(this).data('placeholder',$(this).attr('placeholder'))
				$(this).attr('placeholder','');
			});
				$('#search').blur(function(){
					$(this).attr('placeholder',$(this).data('placeholder'));
				});
		});
</script>
