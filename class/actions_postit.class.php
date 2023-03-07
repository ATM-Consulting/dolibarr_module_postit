<?php
/* Copyright (C) 2022 SuperAdmin
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
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    postit/class/actions_postit.class.php
 * \ingroup postit
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

/**
 * Class ActionsPostIt
 */
class ActionsPostIt
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	public function note($fk_object, $type_object) {

		global $langs, $user, $db, $conf;

		if(!$user->rights->postit->myaction->read && !$user->rights->postit->allaction->write) return false;

		$langs->load('postit@postit');

		// FIXME $select_user is not used and create a conflict with hook of others modules
		//$form=new Form($db);
		//$select_user = $form->select_dolusers($user->id, 'fk_user', 1);

		$aStyle = 'position:absolute; top:0px; display:block; ';
		$imgStyle = 'width:32px; height:32px;';
		if($conf->theme == 'eldy' && intval(DOL_VERSION) >= 12)
		{
			$aStyle = 'position:relative; display:inline-block;';
			$imgStyle = 'height:25px; vertical-align:middle;';
		}
		elseif($conf->theme == 'eldy')
		{
			$aStyle .= " left:-30px; ";
		}


		$a = '<a id="addNote" href="javascript:createNote(0)" title="'.dol_escape_htmltag($langs->trans('NewStickyNote')).'" data-theme="'.dol_escape_htmltag($conf->theme).'" style="'.$aStyle.'">'.img_picto('', 'menu-icon.svg@postit',' style="'.$imgStyle.'" ').'</a>';

		// TODO: Appli this to Dolibarr v10 if Evolution design is merge in Eldy
		if($conf->theme == 'eldy' && intval(DOL_VERSION) >= 12){
			$a = '<div class="inline-block" ><div class="login_block_elem" >'.$a.'</div></div>';
		}

		$aDelete =' <span rel="delete"><span class="fa fa-trash-o postit-icon"></span></span>';
		$aResponse =' <span rel="response">'.img_picto('','response.png@postit').'</span>';
		?>
		<script language="javascript">
            $(document).ready(function() {

				<?php
				if($user->rights->postit->allaction->write || $user->rights->postit->myaction->write) {

				?>

                $a = $('<?php echo $a ?>');
                $('div.login_block_other').prepend($a);
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

                    for(x in Tab) {
                        addNote(Tab[x]);
                    }

                });


            });

            function setStatus(id,status) {
                //status = status.trim();
                var fk_user = <?php echo $user->id ?>;

                if (status === '') status = 'private';

                var $el = $('div#postit-'+id);
                $el.attr('status', status);
                var author = parseInt($el.attr('author'));
                var $el2 = $el.find('[rel=status]');

                if (status === 'public') {
					$el2.html('<span class="fa fa-users postit-icon --' + status + '"></span>');

                    //if(author!=fk_user) {
                    $el.removeClass('yellowPaper greenPaper');
                    $el.addClass('bluePaper');
                    //}
                }
                else if(status === 'shared') {
                    $el2.html('<span class="fa fa-users postit-icon  --' + status + '"></span> <span class="fa fa-share-alt postit-icon --' + status + '"></span>');

                    //if(author!=fk_user) {
                    $el.removeClass('yellowPaper bluePaper');
                    $el.addClass('greenPaper');
                    //}
                }
                else {
                    $el2.html('<span class="fa fa-user-secret postit-icon  --' + status + '"></span>');
                    $el.removeClass('bluePaper greenPaper');
                    $el.addClass('yellowPaper');

                }

                var txtPublic = '<?php echo $langs->transnoentitiesnoconv('shortPublicNote'); ?>';
                var txtShared = '<?php echo $langs->transnoentitiesnoconv('shortSharedNote'); ?>';
                var txtPrivate = '<?php echo $langs->transnoentitiesnoconv('shortPrivateNote'); ?>';

                if (status == 'public') $el.find('span.statusText').text(txtPublic);
                else if (status == 'shared') $el.find('span.statusText').text(txtShared);
                else $el.find('span.statusText').text(txtPrivate);
            }

            function saveNote($div, data) {

                var idPostit = $div.attr('id-post-it');
                var fk_postit= $div.attr('fk-postit');

                data.put = 'postit';
                data.id = idPostit;
                data.fk_postit=fk_postit;
                data.fk_object=<?php echo $fk_object ?>;
                data.type_object="<?php echo $type_object ?>";

                $.ajax({
                    url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
                    ,data: data
                    ,method:'post'
                    ,dataType:"json"
                }).done(function(postit) {
                    let postitOriginal = $div.data('original');
                    console.log(postit, postitOriginal);
                    if (postitOriginal.comment === postit.comment && postitOriginal.title === postit.title) {
                        // si le contenu n'a pas changé, on ne recharge pas tout
                        return;
                    }
                    $div.remove();
                    addNote(postit);
                });


            }

            function createNote(fk_postit) {

                $.ajax({
                    url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
                    ,data: {
                        put:'postit'
                        ,height:200
                        ,width:200
                        ,fk_postit:fk_postit
                        ,fk_object:<?php echo $fk_object ?>
                        ,type_object:"<?php echo $type_object ?>"
                    }
                    ,method:'post'
                    ,dataType:'json'
                }).done(function(postit) {
                    addNote(postit);
                });
            }

            function addNote(postit) {
                var $a = $('#addNote');

                let pos = $a.offset();
                if (!pos) {
                    pos={};
                    pos.top=0;
                    pos.left=0;
                }

                let dateFormatPHP = '<?php echo $langs->trans('FormatDateHourShort') ?>';
                // PHP (Dolibarr): "%d.%m.%Y %H:%M", JS (Dolibarr): "yyyy-MM-dd HH:mm"
                // Note: some date placeholders may be missing for the php-to-js translation
                let dateFormatEquiv = {d: "dd", m: "MM", Y: "yyyy", y: 'yy', H: "HH", M: "mm", S: "ss"};
                let dateFormatJS = dateFormatPHP.replace(/(%[dmyYHMS])/g, (m) => dateFormatEquiv[m[1]]);
                let TMS = new Date(postit.tms * 1000);
                TMS = formatDate(TMS, dateFormatJS);
                let $div = $(
                    '<div class="yellowPaperTemporary postit">'
                    +'  <div rel="content">'
                    +'    <div rel="actions"></div>'
                    +'    <div rel="postit-title" class="ifempty"></div>'
                    +'    <div rel="postit-comment" class="ifempty"></div>'
                    +'    <div rel="postit-author"></div>'
                    +'    <div rel="postit-tms">' + TMS + '</div>'
                    +'  </div>'
                    +'</div>'
                );
                $div.data('original', postit);

                $div.find('[rel=actions]').append("<?php echo addslashes($aDelete); ?>");
                $div.find('[rel=actions]').append("<span rel=\"status\"></span>");
                $div.find('[rel=actions]').append("<?php echo addslashes($aResponse); ?>");
                $div.find('[rel=actions]').append("<span class='statusText' style='font-size:11px;'></span>");

                $div.css('width',  100);
				<?php if($conf->theme !== 'eldy') print '$div.css("z-index", 100);' ?>
                $div.css('height', 200);
                $div.css('top', pos.top + 20);
                $div.css('left', pos.left <?php $conf->theme !== 'eldy' ? print '' : '-50'; ?>);
                $div.hide();

                $('body').append($div);
                $div.fadeIn(100);

                if(postit) {
                    $div.attr('id-post-it', postit.id);
                    $div.attr('id','postit-'+postit.id);
                    $div.attr('author',postit.fk_user);
                    if(postit.fk_postit) $div.attr('fk-postit',postit.fk_postit);

                    if(postit.position_width<=0)postit.position_width= 200;
                    if(postit.position_height<=0)postit.position_height = 200;
                    if(postit.position_top<=0)postit.position_top = pos.top + $('#id-top').height() + 10;
                    if(postit.position_left<=0)postit.position_left = pos.left - postit.position_width;

                    $div.find('[rel=postit-title]').html(postit.title);
                    $div.find('[rel=postit-comment]').html(postit.comment.replace(/\n/g, '<br>'));

                    $div.css('top',  postit.position_top);
                    $div.css('left',  postit.position_left);
                    $div.css('width',  postit.position_width);
                    $div.css('height',  postit.position_height);
                    $div.find('[rel=postit-author]').html(postit.author);

                    setStatus(postit.id, postit.status);

                    if(postit.fk_user==<?php echo $user->id ?>) {
                        $div.find('[rel=postit-author]').remove();
                    }

                    if(!postit.rightToDelete) $div.find('[rel=delete]').remove();
                    if(!postit.rightToSetStatus) $div.find('[rel=status]').remove();
                    if(!postit.rightResponse) $div.find('[rel=response]').remove();
                }
                else{
                    postit={
                        rightEdit : 1
                    };

                }


                $div.find('[rel=response]').click(function() {
                    $div = $(this).closest('div.postit');

                    var idPostit = $div.attr('id-post-it');

                    createNote(idPostit);

                });

                $div.find('[rel=delete]').click(function() {
                    if(window.confirm("<?php echo str_replace('"', '\\"', $langs->transnoentities('AreYouSureYouWantToDeleteThisNote')); ?>")) {
                        var $div = $(this).closest('div.postit');
                        var idPostit = $div.attr('id-post-it');
                        $.ajax({
                            url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
                            ,data: {
                                put:'delete'
                                ,id:idPostit

                            }
                            ,method:'post'
                        }).done(function(postit) {
                            $div.remove();
                        });

                    }
                });



                $div.find('[rel=status]').click(function() {

                    var $div = $(this).closest('div.postit');
                    var idPostit = $div.attr('id-post-it');
                    var status = $div.attr('status');

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



                $div.draggable({
                    containment : containment
                    ,stop:function(event, ui) {

                        var $div = $(this);
                        if(postit.rightEdit) {
                            saveNote($div, {top: ui.position.top, left:ui.position.left});
                        }

                    }
                });


                //todo factorise
                if(postit.rightEdit) {
                    $div.find('[rel=postit-title]').editable({
                        event:'click'
                        ,callback : function( data ) {
                            var $div = data.$el.closest('div.postit');
                            if( data.content ) {
                                saveNote($div,{title:data.content});

                            }
                        }

                    });

                    $div.find('[rel=postit-comment]').editable({
                        event:'click'
                        ,callback : function( data ) {
                            var $div = data.$el.closest('div.postit');
                            if(data.content) {
                                saveNote($div, {
                                    comment:data.content
                                });

                            }

                        }

                    });

                    var containment = "window";
                    if($div.attr('fk-postit')) {
                        //	containment = '#postit-'+$div.attr('fk-postit');
                        $div.mouseenter(function() {
                            $parent = $('#postit-'+$div.attr('fk-postit'));
                            $parent.css("box-shadow","5px 5px 5px 0px #ff0000;");
                        }).mouseleave(function() {
                            $parent = $('#postit-'+$div.attr('fk-postit'));
                            $parent.css("box-shadow","5px 5px 5px 0px #666;");
                        });
                    }


                    $div.resizable({
                        stop:function(event, ui) {

                            var $div = $(this);
                            saveNote($div,{top: ui.position.top, left:ui.position.left, width:ui.size.width, height:ui.size.height});

                        }
                    });

                }

            }


		</script>
		<?php

	}

	/**
	 * Overloading the addStatisticLine function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function addStatisticLine($parameters, &$object, &$action, $hookmanager) {
		// the hook call for `addStatisticLine` was removed from Dolibarr index in 14.0 (maybe it will be added back
		// because this looks more like an omission than deliberate deletion)
		if (version_compare(DOL_VERSION, '14.0', '<')) {
			$this->_injectPostitScriptInIndexPage($parameters);
		};
	}

	/**
	 * Overloading the beforeBodyClose function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function printTopRightMenu($parameters, &$object, &$action, $hookmanager) {
		// we use this hook starting from 14.0 instead of addStatisticLine
		if (version_compare(DOL_VERSION, '14.0', '>=')) {
			$this->_injectPostitScriptInIndexPage($parameters);
		};

		return 0;
	}


	/**
	 * Overloading the formObjectOptions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
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


	/**
	 * Check that we are in the `index` context and call `$this->note()`.
	 * @param $parameters
	 */
	protected function _injectPostitScriptInIndexPage($parameters) {
		if (in_array('index', explode(':', $parameters['context'])))
		{
			$this->note(-1, 'global');
		}
	}

	/**
	 * @param $parameters
	 * @param $object
	 * @param $action
	 * @param $hookmanager
	 * @return void
	 */
	public function addHtmlHeader ($parameters, &$object, &$action, $hookmanager){
		global $langs;
		$langs->load('postit@postit');


	}
}
