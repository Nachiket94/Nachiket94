<h2><?php echo $title; ?></h2>
<?php $this->load->view('gdmo/_menu'); ?>
<form method="get"><label>Month</label> <input type="month" name="month" value="<?php echo $month; ?>"> <button type="submit">Show</button></form>
<?php if ($locked): ?><p style="color:red;">Salary is locked for <?php echo $month; ?> and cannot be reopened.</p><?php else: ?><form method="post"><input type="hidden" name="month" value="<?php echo $month; ?>"><button type="submit" name="lock" value="1" onclick="return confirm('Lock salary permanently for this month?');">Lock Salary</button></form><?php endif; ?>
