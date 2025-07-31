<div class="col-md-2">
                <div class="box border0">
                    <ul class="tablists">
                        <li><a href="<?php echo base_url(); ?>hospital/medicinecategory/medicine" class="<?php  if($this->router->method=='medicine') { echo 'active';}?>"> <th><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></th></a></li>
						 <li><a class="<?php if($this->router->method=='supplier') { echo 'active';} ?>" href="<?php echo base_url(); ?>hospital/medicinecategory/supplier" > <th><?php echo $this->lang->line('supplier'); ?></th></a></li>
                     
                    </ul>
                </div>
            </div>