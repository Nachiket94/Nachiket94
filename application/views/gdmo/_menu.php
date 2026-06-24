<div style="margin-bottom:15px;">
    <strong>GDMO:</strong>
    <a href="<?php echo site_url('gdmo'); ?>">Main</a> |
    <a href="<?php echo site_url('gdmo/rate_master'); ?>">Rate Master</a> |
    <a href="<?php echo site_url('gdmo/tds_master'); ?>">TDS Master</a> |
    <a href="<?php echo site_url('gdmo/gdmo_master'); ?>">GDMO Master</a> |
    <a href="<?php echo site_url('gdmo/attendance'); ?>">Attendance</a> |
    <a href="<?php echo site_url('gdmo/create_salary'); ?>">Create Salary</a> |
    <a href="<?php echo site_url('gdmo/view_salary'); ?>">View Salary</a> |
    <a href="<?php echo site_url('gdmo/tds_report'); ?>">TDS Report</a> |
    <a href="<?php echo site_url('gdmo/lock_salary'); ?>">Lock Salary</a>
</div>
<?php if ($this->session->flashdata('success')): ?><p style="color:green;"><?php echo $this->session->flashdata('success'); ?></p><?php endif; ?>
<?php if ($this->session->flashdata('error')): ?><p style="color:red;"><?php echo $this->session->flashdata('error'); ?></p><?php endif; ?>
