<h2><?php echo $title; ?></h2>
<?php $this->load->view('gdmo/_menu'); ?>
<form method="post">
    <input type="hidden" name="id" value="<?php echo isset($edit->id) ? $edit->id : ''; ?>">
    <p><label>Effective Date</label><br><input type="date" name="effective_date" required value="<?php echo isset($edit->effective_date) ? $edit->effective_date : ''; ?>"></p>
    <p><label>Shift Rate (In Hour)</label><br><input type="number" step="0.01" name="shift_rate" required value="<?php echo isset($edit->shift_rate) ? $edit->shift_rate : ''; ?>"></p>
    <p><label>Emergency Rate (In Hour)</label><br><input type="number" step="0.01" name="emergency_rate" required value="<?php echo isset($edit->emergency_rate) ? $edit->emergency_rate : ''; ?>"></p>
    <p><label>Remarks</label><br><input type="text" name="remarks" value="<?php echo isset($edit->remarks) ? html_escape($edit->remarks) : ''; ?>"></p>
    <button type="submit">Save</button>
</form>
<table border="1" cellpadding="5" cellspacing="0"><tr><th>Effective Date</th><th>Shift Rate</th><th>Emergency Rate</th><th>Remarks</th><th>Action</th></tr>
<?php foreach ($rates as $row): ?><tr><td><?php echo $row->effective_date; ?></td><td><?php echo $row->shift_rate; ?></td><td><?php echo $row->emergency_rate; ?></td><td><?php echo html_escape($row->remarks); ?></td><td><a href="?edit=<?php echo $row->id; ?>">Edit</a></td></tr><?php endforeach; ?>
</table>
