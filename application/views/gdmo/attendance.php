<h2><?php echo $title; ?></h2>
<?php $this->load->view('gdmo/_menu'); ?>
<form method="get"><label>Month</label> <input type="month" name="month" value="<?php echo $month; ?>"> <button type="submit">Show</button></form>
<?php if ($locked): ?><p style="color:red;">Salary is locked for this month.</p><?php endif; ?>
<form method="post"><input type="hidden" name="month" value="<?php echo $month; ?>">
<table border="1" cellpadding="5" cellspacing="0"><tr><th>GDMO Name</th><th>Shift A (In Days)</th><th>Shift B (In Days)</th><th>Shift C (In Days)</th><th>Emergency (In Days)</th><th>Extra (In Hour)</th></tr>
<?php foreach ($gdmos as $gdmo): $row = isset($attendance[$gdmo->id]) ? $attendance[$gdmo->id] : NULL; ?><tr>
<td><?php echo html_escape($gdmo->name); ?></td>
<td><input type="number" step="0.5" name="attendance[<?php echo $gdmo->id; ?>][shift_a_days]" value="<?php echo $row ? $row->shift_a_days : 0; ?>" <?php echo $locked ? 'readonly' : ''; ?>></td>
<td><input type="number" step="0.5" name="attendance[<?php echo $gdmo->id; ?>][shift_b_days]" value="<?php echo $row ? $row->shift_b_days : 0; ?>" <?php echo $locked ? 'readonly' : ''; ?>></td>
<td><input type="number" step="0.5" name="attendance[<?php echo $gdmo->id; ?>][shift_c_days]" value="<?php echo $row ? $row->shift_c_days : 0; ?>" <?php echo $locked ? 'readonly' : ''; ?>></td>
<td><input type="number" step="0.5" name="attendance[<?php echo $gdmo->id; ?>][emergency_days]" value="<?php echo $row ? $row->emergency_days : 0; ?>" <?php echo $locked ? 'readonly' : ''; ?>></td>
<td><input type="number" step="0.5" name="attendance[<?php echo $gdmo->id; ?>][extra_hours]" value="<?php echo $row ? $row->extra_hours : 0; ?>" <?php echo $locked ? 'readonly' : ''; ?>></td>
</tr><?php endforeach; ?></table>
<?php if (!$locked): ?><button type="submit">Save Attendance</button><?php endif; ?>
</form>
