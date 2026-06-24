<h2><?php echo $title; ?></h2>
<?php $this->load->view('gdmo/_menu'); ?>
<form method="get"><label>From</label> <input type="month" name="from_month" value="<?php echo $from; ?>"> <label>To</label> <input type="month" name="to_month" value="<?php echo $to; ?>"> <button type="submit">Show</button> <a href="<?php echo site_url('gdmo/tds_report?from_month=' . rawurlencode($from) . '&to_month=' . rawurlencode($to) . '&export=excel'); ?>">Export Excel</a></form>
<table border="1" cellpadding="5" cellspacing="0"><tr><th>Sl.No.</th><th>GDMO Name</th><th>Salary Amount</th><th>TDS</th><th>Total</th></tr>
<?php $i = 1; foreach ($rows as $row): ?><tr><td><?php echo $i++; ?></td><td><?php echo html_escape($row->gdmo_name); ?></td><td><?php echo $row->salary_amount; ?></td><td><?php echo $row->tds_amount; ?></td><td><?php echo $row->total_amount; ?></td></tr><?php endforeach; ?>
</table>
