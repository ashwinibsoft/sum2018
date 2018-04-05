<div class="row">
	<div class="col-md-12">
		
		<?php echo $this->Html->link('<i class="fa fa-plus"></i>  &nbsp;  Add New Category', array('controller' => 'blog_categories', 'action' => 'add'), array('escape' => false, 'class' => 'btn btn-primary btn-cons')); ?>
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
<?=$this->Form->create('BlogCategorie', array('name' => 'categories', 'action' => 'delete/' , 'id' => 'CategoryDeleteForm', 'onSubmit' => 'return validate(this)', 'class' => 'table-form')); ?>
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
							
							<th style="width:14%">Category Image</th>
							<th style="width:30%">Category Name</th>
							<th class="h-align-center" style="width:10%">Status</th>
							<th class="h-align-center" style="width:20%">Action</th>
						</tr>
					</thead>
					<?php if(!empty($categories)){ ?>
					<tbody>
						<?php
							$i = $this->Paginator->counter('{:start}');
							foreach ($categories as $categorie) {
							$j=$i;	
						?>
						<tr id="sort_<?=$categorie['BlogCategorie']['id'] ?>" style="">
							<td class="v-align-middle">
								<div class="checkbox check-success">
								<?php echo $this->Form->input('BlogCategorie.id.'.$i, array('type'=>'checkbox','value' => $categorie['BlogCategorie']['id'],'class'=>'checkrow')); ?>
								</div>
							</td>
							
							<td class="v-align-middle h-align-center"><?=$i++; ?></td>
							<td class="v-align-middle"><span class="muted"> <?php 
								/* Resize Image */ 
								if(isset($categorie['BlogCategorie']['cat_image'])) {
									$imgArr = array('source_path'=>Configure::read('Path.PostCategory'),'img_name'=>$categorie['BlogCategorie']['cat_image'],'width'=>Configure::read('image_list_width'),'height'=>Configure::read('image_list_height'),'noimg'=>Configure::read('Path.NoImage'));
									$resizedImg = $this->ImageResize->ResizeImage($imgArr);
									echo $this->Html->image($resizedImg);
							}
							?></span></td>
							<td class="v-align-middle"><span class="muted"><?php echo $categorie['BlogCategorie']['cat_name']; ?></span></td>						
							
							
							<td class="v-align-middle h-align-center">
								<span class="muted">
								<?php
									if ($categorie['BlogCategorie']['status'] == '1'){
											echo"<i class='fa fa-check tip link-color' title='' data-toggle='tooltip' data-original-title='Publish'></i>";
									}
									else if ($categorie['BlogCategorie']['status'] == '2'){
											echo "<i class='fa fa-save link-color' title='' data-toggle='tooltip' data-original-title='Unublish'></i>";
									}else{
											echo "<i class='fa fa-times link-color' title='' data-toggle='tooltip' data-original-title='Unublish'></i>";
									}
								?>
								</span>
							</td>
							<td class="v-align-middle h-align-center">
								<a href="<?=Router::url(array('controller' => 'blog_categories', 'action' => 'add',  $categorie['BlogCategorie']['id']));?>" class="tip link-color" title="" data-toggle="tooltip" data-original-title="Edit">
								<i class="fa fa-edit"></i></a>
									<a data-toggle="modal" data-id="<?=$categorie['BlogCategorie']['id'];?>" class="tip link-color preview" data-original-title="View"  href="#viewDetail_<?=$j;?>">
									<i class="fa fa-eye"></i></a>
							</td>
						</tr>
						<?php }?>
					</tbody>
					<?php } ?>
				</table>
				<?php if (!$categories) { ?>
				<div style="margin-top:20px;"></div>
					<div class="row">
						<div class="alert alert-block alert-info ">
							<h4 class="alert-heading"><i class="icon-warning-sign"></i> No Results Found!</h4>
							<div class="button-set">
								<?php echo $this->Html->link('<i class="fa fa-plus"></i>  &nbsp;  Add New Category', array('controller' => 'blog_categories', 'action' => 'add'), array('escape' => false, 'class' => 'btn btn-primary btn-cons')); ?>
								<?php if(!empty($search)){ ?>
								<?php echo $this->Html->link('Show all', array('controller' => 'blog_categories', 'action' => 'index','_blank',999), array('escape' => false, 'class' => 'btn btn-white btn-cons')); ?>
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
				</ul>
			</div>
			<button type="button" class="btn btn-white  btn-cancel">Cancel</button>
		</div>
	</div>

<?php $i = $this->Paginator->counter('{:start}'); ?>
<?php foreach ($categories as $categorie) { ?>
<?php  $j = $i++; ?>	
<!-- Modal -->
<div class="modal fade" id="viewDetail_<?=$j;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<br>
			<i class="icon-credit-card icon-7x"></i>
			<h4 id="myModalLabel" class="semi-bold"><?=$categorie['BlogCategorie']['cat_name']?></h4>
			<br>
		</div>
		<!--start page content here for popup-->
		<div class="modal-body">
			<div class="row form-row">
				<div class="col-md-6">
					<b>category Name</b>	
					<?=$categorie['BlogCategorie']['cat_name']?>
				</div>
				
			</div>
	
		<div class="row form-row">
			<div class="col-md-12">
				<b>Category image</b>
				<?php 
				/* Resize Image */ 
								if(isset($categorie['BlogCategorie']['cat_image'])) {
									$imgArr = array('source_path'=>Configure::read('Path.PostCategory'),'img_name'=>$categorie['BlogCategorie']['cat_image'],'width'=>Configure::read('image_list_width'),'height'=>Configure::read('image_list_height'),'noimg'=>Configure::read('Path.NoImage'));
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
            document.getElementById('CategoryDeleteForm').submit();
        }
    }

    function validate() {
        var ans = "0";
        for (i = 0; i < document.categories.elements.length; i++) {
            if (document.categories.elements[i].type == "checkbox") {
                if (document.categories.elements[i].checked) {
					 if (document.categories.elements[i].value!=1) {
                    ans = "1";
                    break;
				}
                }
            }
        }
        if (ans == "0") {
            alert("Please select category(s) to " + document.getElementById('action').value.toLowerCase());
            return false;
        } else {
            var answer = confirm('Are you sure you want to ' + document.getElementById('action').value.toLowerCase() + ' this category(s)?');
            if (!answer)
                return false;
        }
        return true;
    }

    function CheckAll(chk)
    {
        var fmobj = document.getElementById('CategoryDeleteForm');
        for (var i = 0; i < fmobj.elements.length; i++)
        {
            var e = fmobj.elements[i];
            if (e.type == 'checkbox')
                fmobj.elements[i].checked = document.getElementById('BlogCategorieCheck').checked;
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
                        url:'<?//=Router::url(array('plugin'=>'event_manager','controller'=>'event_categories','action'=>'index',$search,'page:'));?>'+page,
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
                    url: "<?php echo Router::url(array('plugin'=>'blog_manager','controller'=>'blog_categories','action'=>'ajax_sort','admin'=>false)); ?>",
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
