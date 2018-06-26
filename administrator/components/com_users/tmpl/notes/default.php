<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;

HTMLHelper::_('behavior.multiselect');

$user       = Factory::getUser();
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

?>
<form action="<?php echo Route::_('index.php?option=com_users&view=notes'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div id="j-sidebar-container" class="col-md-2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="col-md-10">
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

				<?php if (empty($this->items)) : ?>
					<joomla-alert type="warning"><?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></joomla-alert>
				<?php else : ?>
				<table class="table table-striped">
					<thead>
						<tr>
							<th style="width:1%" class="nowrap text-center">
								<?php echo HTMLHelper::_('grid.checkall'); ?>
							</th>
							<th style="width:1%" class="nowrap text-center">
								<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
							</th>
							<th class="nowrap">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_USERS_HEADING_SUBJECT', 'a.subject', $listDirn, $listOrder); ?>
							</th>
							<th style="width:20%" class="nowrap d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_USERS_HEADING_USER', 'u.name', $listDirn, $listOrder); ?>
							</th>
							<th style="width:10%" class="nowrap d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_USERS_HEADING_REVIEW', 'a.review_time', $listDirn, $listOrder); ?>
							</th>
							<th style="width:1%" class="nowrap d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="6">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php foreach ($this->items as $i => $item) :
						$canEdit    = $user->authorise('core.edit',       'com_users.category.' . $item->catid);
						$canCheckin = $user->authorise('core.admin',      'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
						$canChange  = $user->authorise('core.edit.state', 'com_users.category.' . $item->catid) && $canCheckin;
						$subject    = $item->subject ?: Text::_('COM_USERS_EMPTY_SUBJECT');
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="text-center checklist">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="text-center">
								<div class="btn-group">
									<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'notes.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								</div>
							</td>
							<td>
								<?php if ($item->checked_out) : ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'notes.', $canCheckin); ?>
								<?php endif; ?>
								<?php $subject = $item->subject ?: Text::_('COM_USERS_EMPTY_SUBJECT'); ?>
								<?php if ($canEdit) : ?>
									<?php $editIcon = $item->checked_out ? '' : '<span class="fa fa-pencil-square mr-2" aria-hidden="true"></span>'; ?>
									<a class="hasTooltip" href="<?php echo Route::_('index.php?option=com_users&task=note.edit&id=' . $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape(addslashes($subject)); ?>">
										<?php echo $editIcon; ?><?php echo $this->escape($subject); ?></a>
								<?php else : ?>
									<?php echo $this->escape($subject); ?>
								<?php endif; ?>
								<div class="small">
									<?php echo Text::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
								</div>
							</td>
							<td class="d-none d-md-table-cell">
								<?php echo $this->escape($item->user_name); ?>
							</td>
							<td class="d-none d-md-table-cell">
								<?php if ($item->review_time !== Factory::getDbo()->getNullDate()) : ?>
									<?php echo HTMLHelper::_('date', $item->review_time, Text::_('DATE_FORMAT_LC4')); ?>
								<?php else : ?>
									<?php echo Text::_('COM_USERS_EMPTY_REVIEW'); ?>
								<?php endif; ?>
							</td>
							<td class="d-none d-md-table-cell">
								<?php echo (int) $item->id; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php endif; ?>

				<div>
					<input type="hidden" name="task" value="">
					<input type="hidden" name="boxchecked" value="0">
					<?php echo HTMLHelper::_('form.token'); ?>
				</div>
			</div>
		</div>
	</div>
</form>
