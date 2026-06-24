<h2><?php echo $title; ?></h2>
<?php $this->load->view('gdmo/_menu'); ?>
<form method="post">
    <input type="hidden" name="id" value="<?php echo isset($edit->id) ? $edit->id : ''; ?>">
    <p><label>GDMO</label><br><select name="gdmo_id"><option value="">Global</option><?php foreach ($gdmos as $gdmo): ?><option value="<?php echo $gdmo->id; ?>" <?php echo isset($edit->gdmo_id) && $edit->gdmo_id == $gdmo->id ? 'selected' : ''; ?>><?php echo html_escape($gdmo->name); ?></option><?php endforeach; ?></select></p>
    <p><label>Effective Date</label><br><input type="date" name="effective_date" required value="<?php echo isset($edit->effective_date) ? $edit->effective_date : ''; ?>"></p>
    <p><label>TDS %</label><br><input type="number" step="0.01" name="tds_percent" required value="<?php echo isset($edit->tds_percent) ? $edit->tds_percent : ''; ?>"></p>
    <p><label>Remarks</label><br><input type="text" name="remarks" value="<?php echo isset($edit->remarks) ? html_escape($edit->remarks) : ''; ?>"></p>
    <button type="submit">Save</button>
</form>
<table border="1" cellpadding="5" cellspacing="0"><tr><th>GDMO</th><th>Effective Date</th><th>TDS %</th><th>Remarks</th><th>Action</th></tr>
<?php foreach ($tds_rates as $row): ?><tr><td><?php echo $row->gdmo_name ? html_escape($row->gdmo_name) : 'Global'; ?></td><td><?php echo $row->effective_date; ?></td><td><?php echo $row->tds_percent; ?></td><td><?php echo html_escape($row->remarks); ?></td><td><a href="?edit=<?php echo $row->id; ?>">Edit</a></td></tr><?php endforeach; ?>
</table>
