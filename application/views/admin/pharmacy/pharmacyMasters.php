 <div class="col-md-2">
                <div class="box border0">
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('medicine_category', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/medicinecategory/medicine" class="<?php  if($this->router->method=='medicine') { echo 'active';}?>"> <th><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></th></a></li>
						<?php } ?>
                         <?php  if ($this->rbac->hasPrivilege('medicine_supplier', 'can_view')) { ?>
						 <li><a class="<?php if($this->router->method=='supplier') { echo 'active';} ?>" href="<?php echo base_url(); ?>admin/medicinecategory/supplier" > <th><?php echo $this->lang->line('supplier'); ?></th></a></li>
                        <?php }  if ($this->rbac->hasPrivilege('medicine_dosage', 'can_view')) { ?>
                         <li><a class="<?php if($this->router->method=='index') { echo 'active';};?>" href="<?php echo base_url(); ?>admin/medicinedosage" href="<?php echo base_url(); ?>admin/medicinedosage" > <th><?php echo $this->lang->line('medicine')." ".$this->lang->line('dosage'); ?></th></a></li>
                         <li><a class="<?php if($this->router->method=='medicinInstruction') { echo 'active';};?>" href="<?php echo base_url(); ?>admin/medicinedosage/medicinInstruction" href="<?php echo base_url(); ?>admin/medicinedosage/medicinInstruction" > <th><?php echo $this->lang->line('medicine')." "." Instruction"; ?></th></a></li>    
                         <li><a class="<?php if($this->router->method=='precaution') { echo 'active';};?>" href="<?php echo base_url(); ?>admin/medicinedosage/precaution" href="<?php echo base_url(); ?>admin/medicinedosage/precaution" > <th><?php echo $this->lang->line('medicine')." ". $this->lang->line('precaution'); ?></th></a></li>    
                     <?php } ?>
                     
                    </ul>
                </div>
            </div>