<div class="row">
	<div class="col-md-12">
		
		<?php echo $this->Html->link('<i class="fa fa-plus"></i>  &nbsp;  Add New Content', array('controller' => 'pages', 'action' => 'add'), array('escape' => false, 'class' => 'btn btn-primary btn-cons')); ?>
		<form method="post" style="display:inline;">
		<div class="col-md-1 right-col">
			<button type="submit" class="btn btn-primary btn-sm">
					<i class="fa fa-search"></i>
				</button>
		</div>
		<div class="col-md-2 right-col">
			<?php 
				echo $this->Form->input('limit',array('options'=>array('10'=>'Records:10','20'=>20,'50'=>50,'100'=>100,'999'=>'All'),'label' => false,'div'=>false,'class'=>'simple-dropdown','style'=>'width:100%','value'=>$limit));
			?>
			</div>
		<div class="col-md-2 right-col">
				<input type="text" class="form-control" name="search" value="<?=$search?>" placeholder="Search" id="search">
		</div>
		<div class="col-md-3 right-col">
		<?php echo $this->Page->page_select_mutlilevel('parent_id',array('options'=>array(),'label' => false,'div'=>false,'empty'=>'Parent Page:','class'=>'simple-dropdown','style'=>'width:100%'),$page_list,$parent_id);
			?>
		</div>
		</form>
		<div style="clear:both;"></div>
	</div>
</div>
<?php echo $this->element('admin/message'); ?>
<?=$this->Form->create('Page', array('name' => 'page', 'action' => 'delete/' . $parent_id, 'id' => 'PageDeleteForm', 'onSubmit' => 'return validate(this)', 'class' => 'table-form')); ?>
<?=$this->Form->hidden('action', array('id' => 'action', 'value' => '')); ?>
<?=$this->Form->hidden('redirect', array('value' => $url)); ?>
<div class="row">
	<div class="col-md-12">
		<div class="grid simple horizontal green no-margin-grid">
			<div class="grid-title no-border " style="padding: 0px 15px 0px;"></div>
			<div class="grid-body no-border">
				
				<?php if(!empty($pages)){ ?>
					<div style="margin-top:5px;"></div>
				<table class="table table-bordered no-more-tables table-hover table-content"> 
					<thead style="background-color: #d0d0d0;">
						<tr>
							<th style="width:3%">
								<div class="checkbox check-default">
									<?= $this->Form->input('check', array('type'=>'checkbox','value' => 1, 'onchange' => "CheckAll(this.value)",'label'=>'','class'=>'checkall')); ?>
								</div>
							</th>
							<th class="h-align-center" style="width:9%">S.No.</th>
							<?php
							$class = '';
							if($this->Paginator->sortKey('Page')=='name'){
								if($this->Paginator->sortDir('Page')=='asc'){
									$class = 'sorting_asc';
								}else{
									$class = 'sorting_desc';
								}
							}
							
							?>
							
							<th style="width:60%" class="tip sortable <?php echo $class; ?>" >
								<?php echo $this->Paginator->sort('name','Page Name',array('escape' => false,'class'=>'tip','title'=>'Click to arrange in ascending and descending order','data-toggle'=>'tooltip','data-placement'=>'top'));?>
							</th>
							<th class="h-align-center" style="width:8%">Status</th>
							<?php
							$class = '';
							if($this->Paginator->sortKey('Page')=='created_at'){
								if($this->Paginator->sortDir('Page')=='asc'){
									$class = 'sorting_asc';
								}else{
									$class = 'sorting_desc';
								}
							}
							
							?>
							<!--
							<th class="h-align-center sortable <?php echo $class; ?>" style="width:20%">
								<?php echo $this->Paginator->sort('created_at','Created At',array('escape' => false));?>
							</th>
							-->
							<th class="h-align-center" style="width:22%">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$i = $this->Paginator->counter('{:start}');
							foreach ($pages as $page) {
							$j=$i;	
						?>
						<tr id="sort_<?= $page['Page']['id'] ?>" class="<?php echo ($page['Page']['system_page']==1 || (int)$page['Page']['status']==2)?'disabled':''; ?>">
							<td class="v-align-middle">
								<div class="checkbox check-success ">
								<?php echo $this->Form->input('Page.id.'.$i, array('type'=>'checkbox','value' => $page['Page']['id'],'class'=>'checkrow','div'=>false,'disabled'=>((int)$page['Page']['system_page']==1 || (int)$page['Page']['status']==2)?true:false)); ?>
								</div>
							</td>
							<td class="v-align-middle h-align-center"><?=$i++; ?></td>
							<td class="clickable v-align-middle">
								<span class="tip" data-toggle="tooltip" title="<?php echo $page['Page']['name'];  ?>">
									<?php if(strlen($page['Page']['name']) > 80){ ?>
										<?php echo substr($page['Page']['name'],0,80).'....'; ?>
									<?php }else{ ?>
										<?php echo  $page['Page']['name'];?>
									<?php  } ?>
								</span>
								<?php if((int)$page['Page']['system_page']){ ?>
									&nbsp;<span class="label label-important">Protected</span>
								<?php } ?>
								<?php if((int)$page['Page']['status']==2){ ?>
									&nbsp;<span class="label label-inverse">Draft</span>
								<?php } ?>
								
							</td>
							<td class="v-align-middle h-align-center">
								<span class="muted">
								<?php
									if ($page['Page']['status'] == '1'){
											echo"<i class='fa fa-check tip link-color' title='Publish' data-toggle='tooltip' data-original-title='Publish'></i>";
									}
									else if ($page['Page']['status'] == '2'){
											echo "<i class='fa fa-save tip link-color' title='Draft' data-toggle='tooltip' data-original-title='Draft'></i>";
											
									}else{
											echo "<i class='fa fa-times tip link-color' title='Unpublish' data-toggle='tooltip' data-original-title='Unpublish'></i>";
									}
								?>
								</span>
							</td>
							<!--
							<td class="v-align-middle h-align-center">
							<?php echo date("d, F Y",strtotime($page['Page']['created_at'])); ?>
							</td>
							-->
							<td class="v-align-middle h-align-center">
								<a href="<?=Router::url(array('controller' => 'pages', 'action' => 'add',  $page['Page']['id']));?>" class="tip link-color" title="" data-toggle="tooltip" data-original-title="Edit">
								<i class="fa fa-edit"></i></a>
									
								<a  class="tip link-color" href="<?php echo Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'view', $page['Page']['id'],'admin'=>false,'?'=>array('preview'=>'true'))); ?>" data-toggle="tooltip" data-original-title='See this page "<?php echo $page['Page']['name'] ?>" on website ' target="_blank"><i class="fa fa-eye"></i></a>
									
							</td>
						</tr>
						<?php }?>
					</tbody>
				</table>
				<?php } ?>
				<?php if (!$pages) { ?>
					<div style="margin-top:20px;"></div>
					<div class="row">
						<div class="alert alert-block alert-info ">
							<h4 class="alert-heading"><i class="icon-warning-sign"></i> No Results Found!</h4>
							<div class="button-set">
								<?php echo $this->Html->link('<i class="fa fa-plus"></i>  &nbsp;  Add New Content', array('controller' => 'pages', 'action' => 'add'), array('escape' => false, 'class' => 'btn btn-primary btn-cons')); ?>
								<?php //  if(!empty($search)){ ?>
								<?php echo $this->Html->link('Show all', array('controller' => 'pages', 'action' => 'index','_blank','_blank',999), array('escape' => false, 'class' => 'btn btn-white btn-cons')); ?>

								<?php // } ?>

							</div>
						</div>
					</div>
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


<script type="text/javascript">
    function formsubmit(action)
    {
        var flag = true;
        if (flag)
        {
            document.getElementById('action').value = action;
            if (validate())
                document.getElementById('PageDeleteForm').submit();
        }
    }

    function validate() {
        var ans = "0";
        for (i = 0; i < document.page.elements.length; i++) {
            if (document.page.elements[i].type == "checkbox") {
                if (document.page.elements[i].checked) {
                    ans = "1";
                    break;
                }
            }
        }
        if (ans == "0") {
            alert("Please select page(s) to " + (document.getElementById('action').value).toLowerCase()+".");
            return false;
        } else {
            var answer = confirm('Are you sure you want to ' + document.getElementById('action').value + ' page(s)');
            if (!answer)
                return false;
        }
        return true;
    }


    function CheckAll(chk)
    {
        var fmobj = document.getElementById('PageDeleteForm');
        for (var i = 0; i < fmobj.elements.length; i++)
        {
            var e = fmobj.elements[i];
            if (e.type == 'checkbox')
                fmobj.elements[i].checked = document.getElementById('PageCheck').checked;
        }

    }
    
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
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('th.sortable').hover(function(){
		if(!jQuery(this).hasClass('sorting_asc') && !jQuery(this).hasClass('sorting_desc')){
			jQuery(this).addClass('tmp sorting_asc');
		}
		if(jQuery(this).hasClass('sorting_asc') && !jQuery(this).hasClass('tmp sorting_asc')){
			jQuery(this).addClass('tmp sorting_desc');
		}
		if(jQuery(this).hasClass('sorting_desc') && !jQuery(this).hasClass('tmp sorting_desc')){
			jQuery(this).addClass('tmp sorting_asc');
		}
		
		
	},function(){
		if(jQuery(this).hasClass('tmp sorting_asc')){
			jQuery(this).removeClass('tmp sorting_asc');
		}
		if(jQuery(this).hasClass('tmp sorting_desc')){
			jQuery(this).removeClass('tmp sorting_desc');
		}
	});
	});
</script>
