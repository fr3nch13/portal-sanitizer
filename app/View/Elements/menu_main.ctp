<?php  
?>
				<?php if (AuthComponent::user('id')): ?>
				<ul class="sf-menu">
					<?php if (AuthComponent::user('id') and AuthComponent::user('role') == 'basic'): ?>
					<li><?php echo $this->Html->link(__('Add New Sanitized %s', __('Media')), array('controller' => 'media', 'action' => 'add', $this->params['prefix'] => false, 'basic' => true, 'plugin' => false), array('class' => 'top')); ?></li>
					<li><?php echo $this->Html->link(__('View Sanitized %s', __('Media')), array('controller' => 'media', 'action' => 'index', $this->params['prefix'] => false, 'basic' => true, 'plugin' => false), array('class' => 'top')); ?></li>
					<?php else: ?>
					<li>
						<?php echo $this->Html->link(__('Add New Sanitized %s', __('Media')), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('Add New Sanitized %s', __('Media')), array('controller' => 'media', 'action' => 'add', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Add New Sanitized %s and Edit', __('Media')), array('controller' => 'media', 'action' => 'add', '1', 'admin' => false, 'plugin' => false)); ?></li>
<!--							<li><?php echo $this->Html->link(__('Import New Sanitized %s (not working)', __('Media')), array('controller' => 'media', 'action' => 'import', 'admin' => false, 'plugin' => false)); ?></li> -->
						</ul>
					</li>
					<li>
						<?php echo $this->Html->link(__('Update Open Sanitized %s', __('Media')), '#', array('class' => 'top')); ?>
						<?php echo $this->element('Utilities.menu_items', array(
							'request_url' => array('controller' => 'media', 'action' => 'index', '1,2', $this->params['prefix'] => false, 'plugin' => false),
						)); ?>
					</li>
					<li>
						<?php echo $this->Html->link(__('View Sanitized %s', __('Media')), '#', array('class' => 'top')); ?>
						<?php echo $this->element('Utilities.menu_items', array(
							'request_url' => array('controller' => 'sanitize_statuses', 'action' => 'menu_main', $this->params['prefix'] => false, 'plugin' => false),
						)); ?>
					</li>
					<li>
						<?php echo $this->Html->link(__('View Repurposed %s', __('Media')), '#', array('class' => 'top')); ?>
						<?php echo $this->element('Utilities.menu_items', array(
							'request_url' => array('controller' => 'repurpose_statuses', 'action' => 'menu_main', $this->params['prefix'] => false, 'plugin' => false),
						)); ?>
					</li>
					
					<li><?php echo $this->Html->link(__('View Users'), array('controller' => 'users', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
					
					<?php echo $this->Common->loadPluginMenuItems(); ?>
					
					<?php endif; ?>
					<?php if (AuthComponent::user('id') and in_array(AuthComponent::user('role'), array('manager', 'admin'))): ?>
					<li>
						<?php echo $this->Html->link(__('Management'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('Media Types'), array('controller' => 'media_types', 'action' => 'index', 'manager' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('%s Statuses', __('Sanitize')), array('controller' => 'sanitize_statuses', 'action' => 'index', 'manager' => true, 'plugin' => false)); ?></li>
							<?php if (AuthComponent::user('id') and AuthComponent::user('role') == 'admin'): ?>
							<li><?php echo $this->Html->link(__('%s Statuses', __('Test')), array('controller' => 'test_statuses', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('%s Statuses', __('Repurpose')), array('controller' => 'repurpose_statuses', 'action' => 'index', 'manager' => true, 'plugin' => false)); ?></li>
							<?php endif; ?>
							<li><?php echo $this->Html->link(__('All %s', __('Users')), array('controller' => 'users', 'action' => 'index', 'manager' => true, 'plugin' => false)); ?></li>
						</ul>
					</li>
					<?php endif; ?>
					<?php if (AuthComponent::user('id') and AuthComponent::user('role') == 'admin'): ?>
					<li>
						<?php echo $this->Html->link(__('Admin'), '#', array('class' => 'top')); ?>
						<ul>
							<?php echo $this->Common->loadPluginMenuItems('admin'); ?>
							<li><?php echo $this->Html->link(__('Users'), '#', array('class' => 'sub')); ?>
								<ul>
									<li><?php echo $this->Html->link(__('All %s', __('Users')), array('controller' => 'users', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
									<li><?php echo $this->Html->link(__('Login History'), array('controller' => 'login_histories', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
								</ul>
							</li>
							<li><?php echo $this->Html->link(__('App Admin'), '#', array('class' => 'sub')); ?>
								<ul>
									<li><?php echo $this->Html->link(__('Config'), array('controller' => 'users', 'action' => 'config', 'admin' => true, 'plugin' => false)); ?></li>
									<li><?php echo $this->Html->link(__('Statistics'), array('controller' => 'users', 'action' => 'stats', 'admin' => true, 'plugin' => false)); ?></li>
									<li><?php echo $this->Html->link(__('Process Times'), array('controller' => 'proctimes', 'action' => 'index', 'admin' => true, 'plugin' => 'utilities')); ?></li> 
								</ul>
							</li>
						</ul>
					</li>
					<?php endif; ?>
				</ul>
				<?php endif; ?>