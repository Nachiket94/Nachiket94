<h2><?php echo $title; ?></h2>
<?php $this->load->view('gdmo/_menu'); ?>
<form method="post">
    <input type="hidden" name="id" value="<?php echo isset($edit->id) ? $edit->id : ''; ?>">
    <p><label>Name</label><br><input type="text" name="name" required value="<?php echo isset($edit->name) ? html_escape($edit->name) : ''; ?>"></p>
    <p><label>Rate by Effective Date</label><br><select name="rate_id" required><option value="">Select</option><?php foreach ($rates as $rate): ?><option value="<?php echo $rate->id; ?>" <?php echo isset($edit->rate_id) && $edit->rate_id == $rate->id ? 'selected' : ''; ?>><?php echo $rate->effective_date . ' - Shift: ' . $rate->shift_rate . ', Emergency: ' . $rate->emergency_rate; ?></option><?php endforeach; ?></select></p>
    <p><label><input type="checkbox" name="is_active" value="1" <?php echo !isset($edit->is_active) || $edit->is_active ? 'checked' : ''; ?>> Active</label></p>
    <button type="submit">Save</button>
</form>
<table border="1" cellpadding="5" cellspacing="0"><tr><th>Name</th><th>Effective Date</th><th>Shift Rate</th><th>Emergency Rate</th><th>Active</th><th>Action</th></tr>
<?php foreach ($gdmos as $row): ?><tr><td><?php echo html_escape($row->name); ?></td><td><?php echo $row->effective_date; ?></td><td><?php echo $row->shift_rate; ?></td><td><?php echo $row->emergency_rate; ?></td><td><?php echo $row->is_active ? 'Yes' : 'No'; ?></td><td><a href="?edit=<?php echo $row->id; ?>">Edit</a></td></tr><?php endforeach; ?>
</table>
