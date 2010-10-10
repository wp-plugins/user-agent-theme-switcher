<?php
    if($isDebug == 'true') {
	$debug = 'checked="checked"';
    } else {
	$debug = '';
    }
?>
<div class="wrap">
    <h2>Debug mode</h2>
    <div class="postbox">
	<div style="margin: 7px;">
	    <form method="get" action="<?php echo $this->blogUrl; ?>/wp-admin/admin.php">
		<input type="hidden" name="page" value="<?php echo UserAgentThemeSwitcher::PAGE_DEBUG; ?>" />
		<input type="hidden" name="action" value="<?php echo UserAgentThemeSwitcher::ACTION_DEBUG; ?>" />
		<p><label>Debug mode: <input type="checkbox" name="debug" <?php echo $debug; ?> /></label></p>
		<p><input type="submit" value="update" class="button bold" /></p>
	    </form>
	</div>
    </div>
    <?php
	if(get_option(UserAgentThemeSwitcherData::DEBUG_KEY) == 'true') {
    ?>
    <table class="widefat page fixed" width="100%" cellpadding="3" cellspacing="3">
	<thead>
	    <tr>
		<th class="manage-column" scope="col">UserAgent</th>
		<th class="manage-column" scope="col" width="75"></th>
		<th class="manage-column" scope="col" width="75"></th>
	    </tr>
	</thead>
	<tbody>
	    <?php
		$countUseragents = count($useragents);

		for($i = 0; $i < $countUseragents; $i++) {
		    echo '<tr>';
		    echo '<td>'.$useragents[$i]->useragent.'</td>';
		    echo '<td><a href="'.$this->blogUrl.'/wp-admin/admin.php?page='.UserAgentThemeSwitcher::PAGE_DEBUG.'&action='.UserAgentThemeSwitcher::ACTION_REPORTUSERAGENT.'&useragent='.$useragents[$i]->useragent.'">report</a></td>';
		    echo '<td><a href="'.$this->blogUrl.'/wp-admin/admin.php?page='.UserAgentThemeSwitcher::PAGE_DEBUG.'&action='.UserAgentThemeSwitcher::ACTION_DELETEUSERAGENT.'&useragent='.$useragents[$i]->id.'">delete</a></td>';
		    echo '</tr>';
		}
	    ?>
	</tbody>
    </table>
     <form method="get" action="<?php echo $this->blogUrl; ?>/wp-admin/admin.php">
	<input type="hidden" name="page" value="<?php echo UserAgentThemeSwitcher::PAGE_DEBUG; ?>" />
	<input type="hidden" name="action" value="<?php echo UserAgentThemeSwitcher::ACTION_TRUNCATEDEBUGUSERAGENT; ?>" />
	<input type="submit" name="save" class="button bold" value="Delete all">
    </form>
    <?php
	}
    ?>
    <?php include('useragent-donation.php'); ?>
</div>
<br/>
<br/>
<br/>