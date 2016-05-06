<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_postit.class.php
 * \ingroup postit
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class Actionspostit
 */
class Actionspostit
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	function note($fk_object, $type_object) {
		
		global $langs, $user, $db;
		
			if(!$user->rights->postit->myaction->read && !$user->rights->postit->allaction->write) return false;
		
			$langs->load('postit@postit');
		
			$form=new Form($db);
			$select_user = $form->select_dolusers($user->id, 'fk_user', 1);
		
			$a = '<a id="addNote" href="javascript:addNote()" style="position:absolute; left:-30px; top:0px; display:block;">'.img_picto('', 'post-it.png@postit',' style="width:32px; height:32px;" ').'</a>';
			
			$aDelete =' <span rel="delete">'.img_delete().'</span>';
			$aResponse =' <span rel="response">'.img_picto('','response.png@postit').'</span>';
			
			?>
			<script language="javascript">
				$(document).ready(function() {
					
					<?php
					if($user->rights->postit->allaction->write || $user->rights->postit->myaction->write) {
						
					?>
					
					$a = $('<?php echo $a ?>');
					$('div.login_block_other').append($a);	
					<?php
					}					
					?>
					
					$.ajax({
						url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
						,data: {
							get:'postit-of-object'
							,fk_object:<?php echo $fk_object ?>
							,type_object:"<?php echo $type_object ?>"
						
						}
						,dataType:"json"
					}).done(function(Tab) {
						console.log(Tab);
						for(x in Tab) {
							addNote(Tab[x]);
						}
						
					});
					
									
				});
				
				function setStatus(id,status) {
					
					var fk_user = <?php echo $user->id ?>;
					
					if(status=='')status='private';
					
					var $el = $('div#postit-'+id); 
					$el.attr('status',status);
					var author = parseInt( $el.attr('author') );
					console.log(author,fk_user);
					var $el2 = $el.find('[rel=status]');
					
					if(status == 'public') {
						$el2.html("<?php echo addslashes(img_picto('', 'public.png@postit')); ?>");
						
						if(author!=fk_user) {
							$el.removeClass('yellowPaper greenPaper');
							$el.addClass('bluePaper');
						}
					}
					else if(status == 'shared') {
						$el2.html("<?php echo addslashes(img_picto('', 'shared.png@postit')); ?>");

						if(author!=fk_user) {
							$el.removeClass('yellowPaper bluePaper');
							$el.addClass('greenPaper');
						}
					}
					else {
						$el2.html("<?php echo addslashes(img_picto('', 'private.png@postit')); ?>");
						$el.removeClass('bluePaper greenPaper');
						$el.addClass('yellowPaper');
					
					}
					
					
				}
				
				function addNote(postit) {
					var $a = $('#addNote');
					
					pos = $a.offset();
					if(!pos) {
						pos={};
						pos.top=0;
						pos.left=0;
					}
					
					$div = $('<div class="yellowPaperTemporary postit"><div rel="content"><div rel="actions"></div><div rel="postit-title"><?php echo $langs->trans('NewNote') ?></div><div rel="postit-comment"><?php echo $langs->trans('NoteComment') ?></div></div></div>');

					$div.find('[rel=actions]').append("<?php echo addslashes($aDelete); ?>");						
					$div.find('[rel=actions]').append("<span rel=\"status\"></span>");
					$div.find('[rel=actions]').append("<?php echo addslashes($aResponse); ?>");
					
					$div.css('width',  100);
					$div.css('height', 200);
					$div.css('top', pos.top + 20);
					$div.css('left', pos.left - 50);   
					
					
					$('body').append($div);
				
					if(postit) {
						$div.attr('id-post-it', postit.rowid);
						$div.attr('id','postit-'+postit.rowid);
						$div.attr('author',postit.fk_user);
						if(postit.fk_postit) $div.attr('fk-postit',postit.fk_postit);
						
						if(postit.position_top<=0)postit.position_top = 0;
						if(postit.position_left<=0)postit.position_left = 0;
						if(postit.position_width<=0)postit.position_width= 100;
						if(postit.position_height<=0)postit.position_height = 200;

						$div.find('[rel=postit-title]').html(postit.title);
						$div.find('[rel=postit-comment]').html(postit.comment);
						
						$div.css('top',  postit.position_top);
						$div.css('left',  postit.position_left);
						$div.css('width',  postit.position_width);
						$div.css('height',  postit.position_height);
						
						setStatus(postit.rowid, postit.status);
						
						if(!postit.rightToDelete) $div.find('[rel=delete]').remove();
						if(!postit.rightToSetStatus) $div.find('[rel=status]').remove();
						if(!postit.rightResponse) $div.find('[rel=response]').remove();
					}
					else{
						postit={
							rightEdit : 1
						}
					}
					
					console.log(postit);
					
					
					$div.find('[rel=response]').click(function() {
						$div = $(this).closest('div.postit');
						
						var pos = $div.offset();
						var idPostit = $div.attr('id-post-it');
						
						addNote({
							rowid:0
							,fk_postit:idPostit
							,fk_user:<?php echo $user->id ?>
							,rightToDelete:1
							,rightToSetStatus:1
							,rightResponse:0
							,rightEdit:1
							,title:"<?php echo $langs->trans('NewResponse') ?>"
							,comment:""
							,position_top:pos.top+20
							,position_left:pos.left+20
						});
					});
					  					
					$div.find('[rel=delete]').click(function() {
						if(window.confirm("Vous êtes sûr ?")) {
							var $div = $(this).closest('div.postit');
							var idPostit = $div.attr('id-post-it');
							console.log($div,idPostit);
							$.ajax({
								url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
								,data: {
									put:'delete'
									,id:idPostit
									
								}
								,method:'post'
							}).done(function(idPostit) {
								$div.remove();
							});
							
						}
					});
					
					
						  					
					$div.find('[rel=status]').click(function() {
					
						var $div = $(this).closest('div.postit');
						var idPostit = $div.attr('id-post-it');
						var status = $div.attr('status');
						console.log('current',status);
						$.ajax({
							url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
							,data: {
								put:'change-status'
								,id:idPostit
								,current:status
								
							}
							,method:'post'
						}).done(function(status) {
							setStatus(idPostit,status)
						});
						
					
					});
					
							
							
				
					//todo factorise
					if(postit.rightEdit) {
								$div.find('[rel=postit-title]').editable({
									 event:'click'
									 ,callback : function( data ) {
										  var $div = data.$el.closest('div.postit');
										  var idPostit = $div.attr('id-post-it');
	  										var fk_postit= $(this).attr('fk-postit');

									        if( data.content ) {
											    $.ajax({
													url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
													,data: {
														put:'postit'
														,id:idPostit
														,fk_postit:fk_postit
														,fk_object:<?php echo $fk_object ?>
														,type_object:"<?php echo $type_object ?>"
														,title:data.content
													}
													,method:'post'
												}).done(function(idPostit) {
													$div.attr('id-post-it', idPostit);
												});
						
											}
			      					 }
								     
							    });
								
								$div.find('[rel=postit-comment]').editable({
									event:'click'
									 ,callback : function( data ) {
										  var $div = data.$el.closest('div.postit');
										  var idPostit = $div.attr('id-post-it');
									        if( data.content ) {
											    $.ajax({
													url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
													,data: {
														put:'postit'
														,id:idPostit
														,fk_object:<?php echo $fk_object ?>
														,type_object:"<?php echo $type_object ?>"
														,comment:data.content
													}
													,method:'post'
												}).done(function(idPostit) {
													$div.attr('id-post-it', idPostit);
												});
						
											}
			      					 }
								     
							    });
									
								var containment = "window";
								if($div.attr('fk-postit')) {
								//	containment = '#postit-'+$div.attr('fk-postit');
									$div.mouseenter(function() {
										$parent = $('#postit-'+$div.attr('fk-postit'));
										$parent.effect("highlight", {color:"#00ff00"}, 1000);
									});
								}
								
								$div.draggable({
									containment : containment
									,stop:function(event, ui) {
										
										var $div = $(this);
										var idPostit = $(this).attr('id-post-it');
										var fk_postit= $(this).attr('fk-postit');
										
										$.ajax({
											url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
											,data: {
												put:'postit'
												,id:idPostit
												,fk_postit:fk_postit
												,fk_object:<?php echo $fk_object ?>
												,type_object:"<?php echo $type_object ?>"
												,top:ui.position.top
												,left:ui.position.left
											}
											,method:'post'
										}).done(function(idPostit) {
											$div.attr('id-post-it', idPostit);
										});
										
									}
								});
								
								
								$div.resizable({
									stop:function(event, ui) {
										
										var $div = $(this);
										var idPostit = $(this).attr('id-post-it');
										var fk_postit= $(this).attr('fk-postit');

										$.ajax({
											url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
											,data: {
												put:'postit'
												,id:idPostit
												,fk_postit:fk_postit
												,fk_object:<?php echo $fk_object ?>
												,type_object:"<?php echo $type_object ?>"
												,top:ui.position.top
												,left:	ui.position.left
												,width:ui.size.width
												,height:ui.size.height
											}
											,method:'post'
										}).done(function(idPostit) {
											$div.attr('id-post-it', idPostit);
										});
										
									}
								});
		
					}
					
				}
				
				
			</script>
			<?php
		
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function addStatisticLine($parameters, &$object, &$action, $hookmanager) {
		
		if (in_array('index', explode(':', $parameters['context'])))
		{
			
			$this->note(-1, 'global');
	
		}
			
	}
	
	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		$error = 0; // Error counter
		
		if (in_array('globalcard', explode(':', $parameters['context'])) && $object->id>0)
		{
					
			$this->note($object->id, $object->element);
			
		}

		if (! $error)
		{
			return 0; // or return 1 to replace standard code
		}
		else
		{
			return -1;
		}
	}
}