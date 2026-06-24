<h2><?php echo $title; ?></h2>
<?php $this->load->view('gdmo/_menu'); ?>
<form method="post"><p><label>Select Month</label><br><input type="month" name="month" value="<?php echo $month; ?>" required></p><button type="submit" name="create" value="1">Create Salary</button></form>
