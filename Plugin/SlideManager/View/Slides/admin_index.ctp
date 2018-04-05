<div class="row">
	<div class="col-md-12">
		<div class="btn-group">
			<a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-demo-space"> Select action 
				<span class="caret"></span> 
			</a>
			<ul class="dropdown-menu">
				<li>
					<a href="javascript:" <?php if(!empty($slides)){  ?> onClick="return formsubmit('Publish');" <?php } ?>>Publish</a>
				</li>
				<li class="divider"></li>
				<li>
					<a href="javascript:" <?php if(!empty($slides)) {  ?> onClick="return formsubmit('Unpublish');" <?php  } ?>>Unpublish</a>
				</li>
				<li class="divider"></li>
				<li>
					<a href="javascript:" <?php if(!empty($slides)) {  ?> onClick="return formsubmit('Delete');" <?php } ?>>Delete</a>
				</li>
			</ul>
		</div>
		<?php echo $this->Html->link('New', array('controller' => 'slides', 'action' => 'add'), array('escape' => false, 'class' => 'btn btn-primary btn-cons')); ?>
		<form method="post" style="display:inline;">
			<div class="col-md-1 right-col">
				<button type="submit" class="btn btn-primary btn-sm">
					<i class="fa fa-search"></i>
				</button>
			</div>
			<div class="col-md-2 right-col">
				<?php 
					echo $this->Form->input('limit',array('options'=>array('10'=>'Results:10','20'=>20,'50'=>50,'100'=>100,'999'=>'All'),'label' => false,'div'=>false,'class'=>'simple-dropdown','style'=>'width:100%','value'=>$limit));
				?>
			</div>
			<div class="col-md-2 right-col">
				<input type="text" class="form-control" name="search" value="<?=$search?>" placeholder="Search" id="search">
			</div>
			<?php if($this->Menu->is_super_admin()){ ?>
			<div class="col-md-3 right-col">
				<?php echo $this->Form->input('theme',array('options'=>$themes,'empty'=>'All Themes','label' => false,'div'=>false,'class'=>'simple-dropdown','style'=>'width:100%', 'multiple'=>true));
				?> 
			</div>
			<?php } ?>
		</form>
		<div style="clear:both;"></div>
	</div>
</div>
<?php echo $this->element('admin/message'); ?>
<?=$this->Form->create('Slide', array('name' => 'slide', 'action' => 'delete/' , 'id' => 'SlideDeleteForm', 'onSubmit' => 'return validate(this)', 'class' => 'table-form')); ?>
<?=$this->Form->hidden('action', array('id' => 'action', 'value' => '')); ?>
<?=$this->Form->hidden('redirect', array('value' => $url)); ?>
<div class="row">
	<div class="col-md-12">
		<div class="grid simple horizontal green no-margin-grid">
			<div class="grid-title no-border " style="padding: 0px 15px 0px;"></div>
			<div class="grid-body no-border slides_tab">
				<div style="margin-top:5px;"></div>
				<table class="table table-bordered no-more-tables">
					<thead style="background-color: #d0d0d0;">
						<tr>
							<th style="width:3%">
								<div class="checkbox">
									<?= $this->Form->input('check', array('type'=>'checkbox','value' => 1, 'onchange' => "CheckAll(this.value)",'label'=>'')); ?>
								</div>
							</th>
							<th class="h-align-center" style="width:7%">S.No.</th>
							<th style="width:10%">Image</th>
							<th style="width:30%">Slide Name</th>
							<?php if($this->Menu->is_super_admin()){ ?>
							<th style="width:20%">Theme</th>
							<?php } ?>
							<th class="h-align-center" style="width:10%">Status</th>
							<th class="h-align-center" style="width:20%">Action</th>
						</tr>
					</thead>
					<?php if(!empty($slides)){ ?>
					<tbody>
						<?php 
							$i = $this->Paginator->counter('{:start}');
							foreach ($slides as $slide) {
							$j=$i;	
						?>
						<tr id="sort_<?= $slide['Slide']['id'] ?>" style="">
							<td class="v-align-middle">
								<div class="checkbox">
								<?php echo $this->Form->input('Slide.id.'.$i, array('type'=>'checkbox','value' => $slide['Slide']['id'])); ?>
								</div>
							</td>
							<td class="v-align-middle h-align-center"><?=$i++; ?></td>
							<td class="v-align-middle"><span class="muted"> <?php 
								/* Resize Image */ 
								if(isset($slide['Slide']['image'])) {
									$imgArr = array('source_path'=>Configure::read('Path.Slide'),'img_name'=>$slide['Slide']['image'],'width'=>Configure::read('image_list_width'),'height'=>Configure::read('image_list_height'),'noimg'=>Configure::read('Path.NoImage'));
									$resizedImg = $this->ImageResize->ResizeImage($imgArr);
									echo $this->Html->image($resizedImg);
							}
							?></span></td>
							<td class="v-align-middle"><span class="muted"><?php echo $slide['Slide']['name']; ?></span></td>
							<?php if($this->Menu->is_super_admin()){ ?>
							<td class="v-align-middle"><?php echo ($slide['Slide']['theme']==NULL)?'All Themes':$slide['Slide']['theme']; ?></td>
							<?php } ?>
							<td class="v-align-middle h-align-center">
								<span class="muted">
								<?php
									if ($slide['Slide']['status'] == '1'){
											echo"<i class='fa fa-check tip link-color' title='' data-toggle='tooltip' data-original-title='Publish'></i>";
									}
									else if ($slide['Slide']['status'] == '2'){
											echo "<i class='fa fa-save link-color' title='' data-toggle='tooltip' data-original-title='Unublish'></i>";
									}else{
											echo "<i class='fa fa-times link-color' title='' data-toggle='tooltip' data-original-title='Unublish'></i>";
									}
								?>
								</span>
							</td>
							<td class="v-align-middle h-align-center">
								<a href="<?=Router::url(array('controller' => 'slides', 'action' => 'add',  $slide['Slide']['id']));?>" class="tip link-color" title="" data-toggle="tooltip" data-original-title="Edit">
								<i class="fa fa-edit"></i></a>
									<a data-toggle="modal" data-id="<?=$slide['Slide']['id'];?>" class="tip link-color preview" data-original-title="View"  href="#viewDetail_<?=$j;?>">
									<i class="fa fa-eye"></i></a>
							</td>
						</tr>
						<?php }?>
					</tbody>
					<?php } ?>
				</table>
				<?php if (!$slides) { ?>
				<div style='color:#FF0000;text-align:center;'>No Record Found</div>
				<?php }else{ ?>
				<!--
				<div style='text-align:center;'>
					<button class="btn btn-success btn-cons" id="load-more-button" type="button" style="display:none;">Load more...</button>
				</div>
				-->
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
							<?/*<div class="dataTables_paginate paging_bootstrap pagination">
								
							<?php if($this->Paginator->hasPrev()){?>
							<span class="btn btn-white ">
							<?=$this->Paginator->prev('<i class="fa fa-chevron-left"></i> ',array('escape' => false, 'tag' => 'a'),null,  array('escape' => false, 'tag' => 'a', 'class' => 'disabled', 'disabledTag' => 'a'));?>
							</span>
							<?php } ?>
							
							<?=$this->Paginator->numbers(array('modulus'=>6,'type'=>'button','separator' => '','class'=>'btn btn-white ','currentClass' => 'active')); ?>

							<?php if($this->Paginator->hasNext()){?>
							<span class="btn btn-white ">
							<?=$this->Paginator->next('<i class="fa fa-chevron-right"></i>',array('escape' => false, 'tag' => 'a'),null,array('escape' => false, 'tag' => 'a', 'class' => 'disabled', 'disabledTag' => 'a'));?>
							</span>
							<?php } ?>
							</div>*/?>
							
					</div>
					
				<?php }?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
<?php $i = $this->Paginator->counter('{:start}'); ?>
<?php foreach ($slides as $slide) { ?>
<?php  $j = $i++; ?>	
<!-- Modal -->
<div class="modal fade" id="viewDetail_<?=$j;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<br>
			<i class="icon-credit-card icon-7x"></i>
			<h4 id="myModalLabel" class="semi-bold"><?=$slide['Slide']['name']?></h4>
			<br>
		</div>
		<!--start page content here for popup-->
		<div class="modal-body">
			<div class="row form-row">
				<div class="col-md-6">
					<b>Slide Name</b>	
					<?=$slide['Slide']['name']?>
				</div>
				<?php /*<div class="col-md-6">
					<b>Slide Title</b>
					<?=$slide['Slide']['title']?>
				  
				</div>*/?>
			</div>
		
		<?php /*<div class="row form-row">
			<div class="col-md-12">
				<b>Description</b>
				<?=$slide['Slide']['description']?>
				
			</div>
		</div>*/?>
		<div class="row form-row">
			<div class="col-md-12">
				<b>Slide Image</b>
				<?php 
				/* Resize Image */ 
				if(isset($slide['Slide']['image'])) {
					$imgArr = array('source_path'=>Configure::read('Path.Slide'),'img_name'=>$slide['Slide']['image'],'width'=>Configure::read('image_list_width'),'height'=>Configure::read('image_list_height'),'noimg'=>Configure::read('Path.NoImage'));
					$resizedImg = $this->ImageResize->ResizeImage($imgArr);
					
					echo $this->Html->image($resizedImg);
				}
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
            document.getElementById('SlideDeleteForm').submit();
        }
    }

    function validate() {
        var ans = "0";
        for (i = 0; i < document.slide.elements.length; i++) {
            if (document.slide.elements[i].type == "checkbox") {
                if (document.slide.elements[i].checked) {
                    ans = "1";
                    break;
                }
            }
        }
        if (ans == "0") {
            alert("Please select slide(s) to " + document.getElementById('action').value.toLowerCase());
            return false;
        } else {
            var answer = confirm('Are you sure you want to ' + document.getElementById('action').value.toLowerCase() + ' this slide(s)?');
            if (!answer)
                return false;
        }
        return true;
    }

    function CheckAll(chk)
    {
        var fmobj = document.getElementById('SlideDeleteForm');
        for (var i = 0; i < fmobj.elements.length; i++)
        {
            var e = fmobj.elements[i];
            if (e.type == 'checkbox')
         fmobj.elements[i].checked = document.getElementById('SlideCheck').checked;
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
                        url:'<?=Router::url(array('plugin'=>'slide_manager','controller'=>'slides','action'=>'index',$search,'page:'));?>'+page,
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
                    url: "<?php echo Router::url(array('plugin'=>'slide_manager','controller'=>'slides','action'=>'ajax_sort','admin'=>false)); ?>",
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

