<h2><?php echo $title; ?></h2>
<?php $this->load->view('gdmo/_menu'); ?>
<table border="1" cellpadding="6" cellspacing="0">
<tr><th>Name</th><td><?php echo html_escape($row->gdmo_name); ?></td></tr><tr><th>Month</th><td><?php echo $row->salary_month; ?></td></tr><tr><th>Shift Amount</th><td><?php echo $row->shift_amount; ?></td></tr><tr><th>Emergency Amount</th><td><?php echo $row->emergency_amount; ?></td></tr><tr><th>Extra Amount</th><td><?php echo $row->extra_amount; ?></td></tr><tr><th>Total Amount</th><td><?php echo $row->gross_amount; ?></td></tr><tr><th>TDS (<?php echo $row->tds_percent; ?>%)</th><td><?php echo $row->tds_amount; ?></td></tr><tr><th>Net Amount</th><td><?php echo $row->net_amount; ?></td></tr>
</table>
<p><button onclick="window.print();">Print</button></p>
