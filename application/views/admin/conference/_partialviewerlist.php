<div class="downloadlabel hide" id="downloadlabel"><?php echo $this->lang->line('join') . ' ' . $this->lang->line('list'); ?></div>
<?php

if (!empty($viewerDetail)) {
    if ($type == "staff") {
        ?>

    <table class="custom-table table table-hover table-striped table-bordered viewer-list-datatable">
                        <thead>
                            <tr>
                             <th><?php echo $this->lang->line('staff'); ?></th>
                             <th class="text-right"><?php echo $this->lang->line('last') . ' ' . $this->lang->line('join'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
foreach ($viewerDetail as $viewer_key => $viewer_value) {
            ?>
<tr>
    <td> <?php

            echo $viewer_value->create_for_name . " " . $viewer_value->create_for_surname . " (" . $viewer_value->role_name . ": " . $viewer_value->employee_id . ")";
            ?></td>
    <td class="pull-right">
        <?php echo date($this->customlib->getSchoolDateFormat(true, true), strtotime($viewer_value->created_at)) ?>
    </td>
</tr>
<?php
}
        ?>
                        </tbody>
</table>

<?php
} elseif ($type == "patient") {
        ?>
  <table class="custom-table table table-hover table-striped table-bordered viewer-list-datatable">
                        <thead>
                          <tr>
                            <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('id'); ?></th>
                            <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('mobile') . " " . $this->lang->line('no'); ?></th>
                            <th class=""><?php echo $this->lang->line('last') . ' ' . $this->lang->line('join'); ?></th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
foreach ($viewerDetail as $viewer_key => $viewer_value) {
            ?>
              <tr>
                  <td><?php echo $viewer_value->patient_unique_id; ?></td>
                    <td><?php echo $viewer_value->patient_name; ?></td>
                    <td><?php echo $viewer_value->mobileno; ?></td>
                    <td class=""><?php echo date($this->customlib->getSchoolDateFormat(true, true), strtotime($viewer_value->created_at)); ?></td>

              </tr>
<?php
}
        ?>
                        </tbody>
</table>
   <?php
}

} else {
    ?>
 <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                    <?php
}

?>